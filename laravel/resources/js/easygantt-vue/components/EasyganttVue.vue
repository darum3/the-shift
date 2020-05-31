<template>
<div id="easygantt-area">
    <div class='chart' v-for="(dailyTasks, i) in tasks" :key="dailyTasks.date">
        <span :id="'date' + i" class="chart_header">{{dailyTasks.date}}</span>
        <span v-if="isEdit" class="chart_header">：入力モード
            <button class='btn btn-sm btn-light' style='margin-left: 1em;' @click="updateData" :disabled="inProgress || Object.keys(dailyTasks.shifts).length == 0">保存</button>
        </span>
        <span v-if="inProgress" class='text-success blinking' style='font-size: 1.2em;'>更新中</span>
        <div class='daily-area'>
            <div class='scale'>
                <div class='hr'></div>
                <section :style="'width:'+nameWidth+'px; border-left:1px solid; border-right:1px solid;'">&nbsp;</section>
                <section v-for="time in timeScale" :style="'width:' + singleTimeScaleWidth + 'px;'" :key="time">
                    {{time}}
                </section>
            </div>
            <ul :id="'task'+i" class="data">
                <li v-for="(userShift) in dailyTasks.shifts" :key="userShift.user_id">
                    <div :style="'width: '+nameWidth+'px;'" class='person_name'>{{userShift.name}}</div>
                    <div v-if="Object.keys(userShift.task).length != 0" :class="work_types[userShift.task.work_type].category" class="person_shift"
                        @mouseup="endShiftCreate(userShift, $event)" @mousemove="shiftMouseMove(userShift, $event)">
                        <span class="bubble" id='shift_bubble'
                            :style="'margin-left:'+(userShift.task.startTaskMin*widthAboutMin+nameWidth+1)+'px; width:' + userShift.task.duration*widthAboutMin+'px;'"
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
                        <div v-if="isEdit"><button class="btn btn-light btn-sm" @click="addLine(dailyTasks.date)">＋</button></div>
                        <div v-else>&nbsp;</div>
                    </div>
                    <div class="person_shift">
                        &nbsp;
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <user-select-dialog
        :list-api="userListApi"
        v-if="showUserSelect"
        @close="showUserSelect = false"
        @select="userSelect"
    ></user-select-dialog>
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

            open: '', // hhmm
            close: '',

            scaleDiv: 0,
            timeScale: [],
            singleTimeScaleWidth: 0,
            timeScaleWidth: 0,
            widthAboutMin: 0, //1分毎の幅px

            nameWidth: 50, //px

            isEdit: false,

            showUserSelect: false,
            userAddDate: null,
            divPageX: null,

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
        for(let i=0; i <= this.scaleDiv; i++) {
            this.timeScale[i] = String((openMin + (i * 30))/60);
            if(this.timeScale[i].slice(-2) === ".5") {
                this.timeScale[i] = this.timeScale[i].replace(".5", ":30");
            } else {
                this.timeScale[i] = this.timeScale[i] + ":00";
            }
        }
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
            this.tasks = resp.data.tasks
            this.calcTaskTimes()
        },
        calcTaskTimes() {
            // 開始位置とタスクの長さ（px）を計算し、this.tasks を更新
            this.tasks.forEach(daily => {
                daily.shifts.forEach(person => {
                    person.in_drag = false
                    person.resize_startTime = false
                    person.resize_endTime = false
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
                            "task": {},
                        })
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
            this.$set(shift.task, 'work_type', 'MAKE') // TODO 選択する

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
        async updateData() {
            // 処理中へ
            this.isEdit = false
            this.inProgress = true

            try {
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
            }catch(e) {
                console.error(e)
            }

            this.inProgress = false
            this.isEdit = true
        }
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
        border-top: 1px solid rgba(250, 250, 250, 0.5);
    }

    .scale {
        height: 100%;
        width: 100%;
        position: absolute;

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

        li{
            line-height: 30px;
            height: 40px;
            display: block;
            clear: both;
            position: relative;
            white-space: nowrap;
            border-bottom: 1px solid;
        }

        // バブルの色：TODO
        .work_type_01 {
            span.bubble {
                background-color: #2b8fef;
            }
        }
        .work_type_02 {
            span.bubble {
                background-color: #13d604;
            }
        }
        .work_type_03 {
            span.bubble {
                background-color: #ffe74d;
            }
        }
        .work_type_04 {
            span.bubble {
                background-color: #8470ff;
            }
        }
        .work_type_05 {
            span.bubble {
                background-color: #ffc0cb;
            }
        }
        .work_type_06 {
            span.bubble {
                background-color: #a9a9a9;
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
            border-left: 1px solid;
            border-right: 1px solid;
        }
        div.person_shift {
            // display: inline-block;
            margin-top: -22px;
        }
        div.person_add {
            border-left: 1px solid;
            border-right: 1px solid;
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
}
</style>
