<template>
<div id="easygantt-area">
    <div class='chart' v-for="(dailyTasks, i) in tasks" :key="dailyTasks.date">
        <span :id="'date' + i" class="chart_header"><input type='date' :value="dailyTasks.date" @change="changeDate" /></span>
        <span v-if="isEdit" class="chart_header">
            <span v-if="dailyTasks.fixed">確定済み</span>
            <span v-else style="margin-left: 1em;">
                入力モード
                <select v-model="current_work_type">
                    <option
                        v-for="work_type in work_types" :key="work_type.code"
                        :selected="current_work_type === work_type.code"
                        :style="'backgroud-color: ' + work_type.work_color"
                        :value="work_type.code">
                        {{work_type.name}}
                    </option>
                </select>
                <button
                    class='btn btn-sm btn-light' style='margin-left: 1em;'
                    @click="updateData"
                    :disabled="inProgress || Object.keys(dailyTasks.shifts).length == 0">
                    保存
                </button>
                <button class='btn btn-sm btn-success' style='margin-left: 1em;'
                    @click="fixShift(dailyTasks.date)">
                    シフト確定
                </button>
            </span>
        </span>
        <span v-if="inProgress" class='text-success blinking' style='font-size: 1.2em;'>更新中</span>
        <div class='daily-area'>
            <easygantt-time-scale
                :name-width="nameWidth" :single-time-scale-width="singleTimeScaleWidth"
                :open="openHhmm" :close="closeHhmm"
                @delete="deleteConfirm()"
                :delEnable="isEdit && !dailyTasks.fixed"
            ></easygantt-time-scale>
            <ul :id="'task'+i" class="data">
                <li v-for="(userShift) in dailyTasks.shifts" :key="userShift.task.task_id"
                    :class="{'user-select' : userShift.in_select}">
                    <div :style="'width: '+nameWidth+'px;'" class='person_name'
                        @click="onClickName(userShift)">{{userShift.name}}</div>
                    <div v-if="Object.keys(userShift.task).length != 0" class="person_shift"
                        @mouseup="endShiftCreate(userShift, $event)" @mousemove="shiftMouseMove(userShift, $event)">
                        <span class="bubble" id='shift_bubble'
                            :style="
                                'margin-left:'+(userShift.task.startTaskMin*widthAboutMin+nameWidth+1)+'px;' +
                                'width:' + userShift.task.duration*widthAboutMin+'px;' +
                                'background-color: ' + work_types[userShift.task.work_type].work_color + ';'"
                            :class="{'ew-resizable' : userShift.resize_startTime === true || userShift.resize_endTime === true, movable : isEdit, moving : userShift.in_drag=='time_change'}"
                            @mousedown="onMousedownBubble(userShift, $event)" @mouseup="endShiftCreate(userShift, $event)">
                        </span>
                        <span class='time'>{{getDisplayString(userShift.task.startTime)}}-{{getDisplayString(userShift.task.endTime)}}</span>
                        <!-- <span class="bubble-span">{{task.name}}</span> -->
                    </div>
                    <div v-else-if="Object.keys(userShift.task).length == 0 && isEdit" class="person_shift" :class="{create_shift : userShift.in_drag === 'create'}"
                        @mousedown="startShiftCreate(userShift, $event)">
                        &nbsp;
                    </div>
                </li>
                <li>
                    <!-- 最終の空行 -->
                    <div :style="'width: ' +nameWidth+'px;'" class="person_add">
                        <div v-if="isEdit && !dailyTasks.fixed"><button class="btn btn-light btn-sm" @click="addLine(dailyTasks.date)">＋</button></div>
                        <div v-else>&nbsp;</div>
                    </div>
                    <div class="person_shift">
                        &nbsp;
                    </div>
                </li>
                <div class='hr'></div>
            </ul>
        </div>
    </div>
    <user-select-dialog
        :list-api="userListApi"
        v-if="showUserSelect"
        @close="showUserSelect = false"
        @select="userSelect"
    ></user-select-dialog>
    <ok-cancel-dialog width="80%"
        v-if="showDeleteDialog"
        :message="deleteMsg"
        @ok="deleteExec()"
        @cancel="showDeleteDialog = false"
    ></ok-cancel-dialog>
    <ok-cancel-dialog width="80%"
        v-if="showFixDialog"
        :message="fixMessage"
        @ok="fixExec()"
        @cancel="showFixDialog = false"
    ></ok-cancel-dialog>
</div>
</template>

<script>
import axios from 'axios'

export default {
    props: {
        openHhmm: {
            required: true,
            default: '0900',
        },
        closeHhmm: {
            required: true,
            default: '1800',
        },
        restApi: {
            required: true,
            type: String,
        },
        date: {
            required: true,
            type: String, // yyyy-mm-dd
        },
        edit: {
            default: false,
            type: Boolean,
        },
        userListApi: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            tasks: [],
            work_types: [],
            delete_ids: [],

            open: '', // hhmm
            close: '',

            scaleDiv: 0,
            timeScale: [],
            singleTimeScaleWidth: 0,
            timeScaleWidth: 0,
            widthAboutMin: 0, //1分毎の幅px

            nameWidth: 50, //px
            current_work_type: null,

            isEdit: false,

            showUserSelect: false,
            userAddDate: null,
            divPageX: null,
            showDeleteDialog: false,
            deleteMsg: '削除しますか？',
            // 確定処理
            showFixDialog: false,
            fixedDate: null,
            fixMessage: 'シフトを確定します. グループのメンバーに通知されます(予定).',

            moveStart: null,

            inProgress: false,
        }
    },
    created() {
        this.loadTasks()

        // 仮の初期化
        this.open=this.openHhmm
        this.close=this.closeHhmm

        var openMin = this.convertTimesToMins(this.open);
        var closeMin = this.convertTimesToMins(this.close);
        var workingMin = closeMin - openMin;

        this.scaleDiv =  workingMin / 30;
        this.isEdit = this.edit
    },
    beforeUpdate() {
        this.resizeWindow()
    },
    mounted() {
        window.addEventListener('resize', this.resizeWindow)
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.resizeWindow)
    },
    methods: {
        async loadTasks() {
            var resp = await axios.get(this.restApi+'/'+this.date, {
                headers: {'Content-Type': 'application/json'}
            })
            this.work_types = resp.data.work_types
            this.current_work_type = Object.keys(this.work_types)[0]
            this.tasks = resp.data.tasks
            this.calcTaskTimes()
        },
        calcTaskTimes() {
            // 開始位置とタスクの長さ（px）を計算し、this.tasks を更新
            this.tasks.forEach(daily => {
                daily.shifts.forEach(person => {
                    this.$set(person, 'in_drag', false)
                    this.$set(person, 'resize_startTime', false)
                    this.$set(person, 'in_select', false)
                    this.$set(person, 'resize_endTime', false)
                    if(person.task != null) {
                        this.updateTaskMinute(person.task)
                    }
                });
            });
        },
        updateTaskMinute(task) {
            var startTimeMin = this.convertTimesToMins(task.startTime)
            task.startTaskMin = startTimeMin - this.convertTimesToMins(this.open)
            task.duration = this.convertTimesToMins(task.endTime) - startTimeMin
        },
        resizeWindow() {
            var chartWholeWidth = document.getElementById('easygantt-vue').clientWidth - this.nameWidth
            this.singleTimeScaleWidth = chartWholeWidth / (this.scaleDiv + 1) - 1
            this.widthAboutMin = (this.singleTimeScaleWidth)/30
        },
        addLine(date) {
            // '+'ボタンの制御
            this.showUserSelect = true
            this.userAddDate = date
        },
        userSelect(user) {
            if (this.userAddDate) {
                // メンバー追加
                this.tasks.forEach(daily => {
                    if (daily.date == this.userAddDate) {
                        daily.shifts.push({
                            "user_id": user.id,
                            "name": user.name,
                            "in_drag": false,
                            "resize_startTime": false,
                            "resize_endTime": false,
                            "in_select": false,
                            "task": {},
                        })
                        daily.shifts.splice(daily.shifts.length)
                    }
                })
            }
            this.showUserSelect = false
            this.userAddDate = null
        },
        /**
         * シフトのbubbleをマウスダウン（D&D開始）
         */
        onMousedownBubble(shift, event) {
            this.moveStart = event.pageX
            if (shift.resize_startTime === true) {
                shift.in_drag = 'start_change'
            } else if (shift.resize_endTime === true) {
                shift.in_drag = 'end_change'
            } else {
                shift.in_drag = 'time_change'
            }
        },
        // クリックした
        startShiftCreate(shift, event) {
            let startTaskMin = Math.round((event.offsetX - this.nameWidth - 1)/this.widthAboutMin) // 15分刻み
            startTaskMin = Math.ceil(startTaskMin/15)*15
            let startTimeMin = startTaskMin + this.convertTimesToMins(this.open)
            let startTime = this.convertMinsToTimes(startTimeMin)

            this.$set(shift.task, 'startTime', startTime)
            this.$set(shift.task, 'endTime', startTime) // 初期は同じ数字
            this.$set(shift.task, 'startTaskMin', startTaskMin)
            this.$set(shift.task, 'duration', 0)
            this.$set(shift.task, 'work_type', this.current_work_type)

            this.divPageX = window.pageXOffset + event.target.getBoundingClientRect().left
            shift.in_drag = 'create'
        },
        /**
         * シフト上でマウスを動かした(D&D)
         */
        shiftMouseMove(shift, event) {
            if (event.target.id == 'shift_bubble') {
                let inResizePix = this.widthAboutMin * 15 // カーソルを変えるpix
                // スケールのためのcursor変更
                let clientRect = event.target.getBoundingClientRect()
                if (Math.abs(event.x - clientRect.left) < inResizePix) {
                    // 15分以内ならカーソル変更
                    this.$set(shift, 'resize_startTime', true)
                    return
                } else if (Math.abs(event.x - clientRect.right) < inResizePix) {
                    this.$set(shift, 'resize_endTime', true)
                    return
                }
            }
            if (shift.resize_startTime == true && shift.in_drag !== 'start_change') {
                // 開始中でなければフラグを戻す
                this.$set(shift, 'resize_startTime', false)
            } else if (shift.resize_endTime === true && shift.in_drag !== 'end_change') {
                this.$set(shift, 'resize_endTime', false)
            }
            if (shift.in_drag === false) return; // D&D中でなければ何もしない

            if (shift.in_drag == 'create') {
                this.calcShiftCreate(shift, event) // 新規描画（作成）中
            } else if(shift.in_drag == 'time_change') {
                this.calcShiftChange(shift, event) // 時間変更（D&D)
            } else if(shift.in_drag == 'start_change') {
                this.calcStartChange(shift, event)
            } else if (shift.in_drag == 'end_change') {
                this.calcEndChange(shift, event)
            }
        },
        // 描画中
        calcShiftCreate(shift, event) {
            let x = event.pageX - this.divPageX
            let endTaskMin = Math.round((x - this.nameWidth - 1)/this.widthAboutMin) // 15分刻み
            endTaskMin = Math.ceil(endTaskMin/15)*15
            let duration = endTaskMin - shift.task.startTaskMin
            if (duration < 0) {
                // マイナスは設定できないように
                endTaskMin = shift.task.startTaskMin
                duration = 0
            }
            let endTimeMin = endTaskMin + this.convertTimesToMins(this.open)
            let endTime = this.convertMinsToTimes(endTimeMin)

            this.$set(shift.task, 'endTime', endTime)
            this.$set(shift.task, 'duration', endTaskMin - shift.task.startTaskMin)
        },
        // 時間の変更
        calcShiftChange(shift, event) {
            let moveTimeMin = this.calcMoveTime(event)
            let newStartMin = shift.task.startTaskMin + moveTimeMin
            if (moveTimeMin != 0 && newStartMin >= 0) {
                let startTimeMin = newStartMin + this.convertTimesToMins(this.open)
                let startTime = this.convertMinsToTimes(startTimeMin)
                let endTimeMin = startTimeMin + shift.task.duration
                let endTime = this.convertMinsToTimes(endTimeMin)

                if (endTime <= this.closeHhmm) {
                    this.$set(shift.task, 'startTime', startTime)
                    this.$set(shift.task, 'endTime', endTime)
                    this.$set(shift.task, 'startTaskMin', newStartMin)

                    this.moveStart = event.pageX
                }
            }
        },
        // 開始時間の変更
        calcStartChange(shift, event) {
            let moveTimeMin = this.calcMoveTime(event)
            let newStartMin = shift.task.startTaskMin + moveTimeMin
            if (moveTimeMin != 0 && newStartMin >= 0) {
                let startTimeMin = newStartMin + this.convertTimesToMins(this.open)
                let startTime = this.convertMinsToTimes(startTimeMin)
                this.$set(shift.task, 'startTime', startTime)
                this.updateTaskMinute(shift.task)

                this.moveStart = event.pageX
            }
        },
        // 終了時間の変更
        calcEndChange(shift, event) {
            let moveTimeMin = this.calcMoveTime(event)
            let newEndMin = (shift.task.startTaskMin + shift.task.duration) + moveTimeMin
            let endTime = this.convertMinsToTimes(newEndMin + this.convertTimesToMins(this.open))
            if (moveTimeMin != 0 && endTime <= this.closeHhmm) {
                this.$set(shift.task, 'endTime', endTime)
                this.updateTaskMinute(shift.task)

                this.moveStart = event.pageX
            }
        },
        // 移動時間の計算
        calcMoveTime(event) {
            let delta = event.pageX - this.moveStart
            return Math.round(Math.round(delta/this.widthAboutMin)/15) * 15
        },
        // 確定
        endShiftCreate(shift, event) {
            if (shift.in_drag == 'create') {
                if (shift.task.duration == 0) {
                    this.$set(shift, 'task', {}) // 確定しない
                }
                this.divPageX = null
            }
            shift.in_drag = false
        },
        onClickName(shift) {
            shift.in_select = !shift.in_select
            this.$upda
        },
        // 削除
        deleteConfirm() {
            this.showDeleteDialog = true
        },
        // 削除実行
        deleteExec() {
            this.tasks.forEach(daily => {
                Array.prototype.push.apply(this.delete_ids, daily.shifts.filter(s => s.in_select && s.task.task_id != null).map(s => s.task.task_id))
                daily.shifts = daily.shifts.filter(s => s.in_select == false)
                daily.shifts.splice(daily.shifts.length)
            })
            this.showDeleteDialog = false
        },
        async updateData() {
            // 処理中へ
            this.isEdit = false
            this.inProgress = true

            try {
                if (this.delete_ids.length !== 0) {
                    await axios.delete(this.restApi,
                        {data: {'task_id': this.delete_ids}})
                    this.delete_ids = []
                }

                var res = await axios.post(this.restApi,
                [{
                    'date': this.tasks[0].date,
                    'shift': this.tasks[0].shifts.map(function(item) {
                        return {
                            'user_id': item.user_id,
                            'work_type_code': item.task.work_type,
                            'startTime': item.task.startTime,
                            'endTime': item.task.endTime,
                        }
                    })
                }])

                location.reload()
            }catch(e) {
                console.error(e)
            }

            // this.inProgress = false
            // this.isEdit = true
        },
        fixShift(date) {
            this.fixedDate = date
            this.showFixDialog = true
        },
        async fixExec() {
            this.isEdit = false
            this.inProgress = true

            try {
                await axios.post(this.restApi + '/fix', {'date': this.fixedDate})

                location.reload()
            } catch(e) {
                // TODO ハンドリング
                console.error(e)
            }
        },
        changeDate(e) {
            let date = e.target.value
            location.href = location.origin + location.pathname + '?date=' + date
        },
    },
}
</script>

<style lang="scss">
.chart {
    background-color: #333;
    border-radius: 10px;
    padding: 10px;

    // span[id *= 'date'] {
    // }
    span.chart_header {
        font-size: 14px;
        font-weight: 600;
        color: azure;
    }

    .daily-area {
        border-top: 0.1px solid #333;
        position: relative;
    }

    .hr {
        border-top: 1px solid rgba(250, 250, 250, 0.8);
    }

    .scale {
        height: 100%;
        width: 100%;
        position: absolute;
        border-top: rgb(250, 250, 250) 1px solid;
        border-bottom: rgb(250, 250, 250) 1px solid;

        section {
            float: left;
            /* border-leftの線の太さを考慮して60 - 1する */
            text-align: center;
            background-color: #333;
            color: #979796;
            font-size: 14px;
            font-weight: lighter;
            border-left: 1px dashed rgba(250,250,250,0.2);
            height: 100%;
            white-space: nowrap;
        }
        section.person_name {
            border-left:1px solid #333; // 線出さない
            border-right:1px solid rgb(250, 250, 250);
        }
        .bubble {
            float: left;
            display: block;
        }
    }
    .data {
        font-size: 14px;
        padding: 0;
        margin: 30px 0 0 0;
        text-align: left;
        list-style-type: none;
        color: rgba(250,250,250,0.8);
        // border-top: 1px solid rgba(250, 250, 250, 0.8);

        li{
            line-height: 30px;
            height: 40px;
            display: block;
            clear: both;
            position: relative;
            white-space: nowrap;
            // border-bottom: 1px solid;
            border-top: 1px solid rgba(250, 250, 250, 0.8);

            &.user-select {
                background-color: rgba(255, 182, 193, 0.5);
            }
        }

        div:not(.milestone){
            .bubble {
                height: 20px;
                display: block;
                float: left;
                // position: relative;
                top: 7px;
                border-radius: 4px;
                margin: 0 3px 0 0;
                opacity: 0.7;
            }
        }

        div.person_name {
            padding-left: 3px;
            cursor: pointer;
        }
        div.person_shift {
            // display: inline-block;
            margin-top: -22px;
        }
        div.person_add {
            margin: 2px 0;
            text-align: center;

            button {
                padding: 0.2em 0.8em;
                border-radius: 0.7em;
            }
        }

        // 編集モード
        .create_shift {
            cursor: text;
        }
    }
    input[type="date"] {
        color: white;
        background-color: black;
        cursor: pointer;
    }
}
.person_delete {
    background-image: url('../../../img/delete.png');
    background-size: 25px;
    background-repeat: no-repeat;
    background-position: center;
}
</style>
