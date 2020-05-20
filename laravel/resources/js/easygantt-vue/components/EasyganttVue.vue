<template>
<div id="easygantt-area">
    <div class='chart' v-for="(dailyTasks, i) in tasks" :key="dailyTasks.date">
        <span :id="'date' + i">{{dailyTasks.date}}</span>
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
                    <div :class="userShift.task.work_type" class="person_shift">
                        <span class="bubble"
                            :style="'margin-left:'+(userShift.task.startTaskMin*widthAboutMin+nameWidth+1)+'px; width:' + userShift.task.duration*widthAboutMin+'px;'">
                        </span>
                        <span class='time'>{{getDisplayString(userShift.task.startTime)}}-{{getDisplayString(userShift.task.endTime)}}</span>
                        <!-- <span class="bubble-span">{{task.name}}</span> -->
                    </div>
                </li>
            </ul>
        </div>
    </div>
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
    },
    data() {
        return {
            tasks: [],

            open: '', // hhmm
            close: '',

            scaleDiv: 0,
            timeScale: [],
            singleTimeScaleWidth: 0,
            timeScaleWidth: 0,
            widthAboutMin: 0, //1分毎の幅px

            nameWidth: 50, //px
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
            this.tasks = resp.data
            this.calcTaskTimes()
        },
        calcTaskTimes() {
            // 開始位置とタスクの長さ（px）を計算し、this.tasks を更新
            this.tasks.forEach(daily => {
                daily.shifts.forEach(person => {
                    var startTimeMin = this.convertTimesToMins(person.task.startTime)
                    person.task.startTaskMin = startTimeMin - this.convertTimesToMins(this.open)
                    person.task.duration = this.convertTimesToMins(person.task.endTime) - startTimeMin
                });
            });
        },
        resizeWindow() {
            var chartWholeWidth = document.getElementById('easygantt-vue').clientWidth - this.nameWidth
            this.singleTimeScaleWidth = chartWholeWidth / (this.scaleDiv + 1) - 1
            this.widthAboutMin = (this.singleTimeScaleWidth)/30
        }
    },
}
</script>

<style lang="scss">
.chart {
    background-color: #333;
    border-radius: 10px;
    padding: 10px;

    span[id *= 'date'] {
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
            line-height: 25px;
            height: 30px;
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
            margin-top: -20px;
        }
    }
}
</style>
