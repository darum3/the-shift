<template>
<div>
    <div class='chart' v-for="(daylyTasks, i) in tasks" :key="i">
        <!-- {{task}} -->
        <span :id="'date' + i">日付</span>
        <div class='daily-area'>
            <div class='scale'>
                <div class='hr'></div>
                <section v-for="time in timeScale" :style="'width:' + singleTimeScaleWidth + 'px;'" :key="time">
                    {{time}}
                </section>
            </div>
            <ul :id="'task'+i" class="data">
                <li v-for="(task, j) in daylyTasks" :key="j">
                    <div :class="task.category">
                        <span class="bubble"
                            :style="'margin-left:'+task.startTaskMin*widthAboutMin+'px; width:' + task.duration*widthAboutMin+'px;'">
                        </span>
                        <span class='time'>{{getDisplayString(task.startTime)}}-{{getDisplayString(task.endTime)}}</span>
                        <span class="bubble-span">{{task.name}}</span>
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
    data() {
        return {
            tasks: [],

            open: '', // hhmm
            close: '',

            scaleDiv: 0,
            timeScale: [],
            singleTimeScaleWidth: 0,
            timeScaleWidth: 0,
        }
    },
    created() {
        this.loadTasks()

        // 仮の初期化
        this.open='0900'
        this.close='1800'

        var openMin = this.convertTimesToMins(this.open);
        var closeMin = this.convertTimesToMins(this.close);
        var workingMin = closeMin - openMin;
        // timeScale = [];
        this.scaleDiv =  workingMin / 30;
        for(let i=0; i <= this.scaleDiv; i++) {
            this.timeScale[i] = String((openMin + (i * 30))/60);
            if(this.timeScale[i].slice(-2) === ".5") {
                this.timeScale[i] = this.timeScale[i].replace(".5", ":30");
            } else {
                this.timeScale[i] = this.timeScale[i] + ":00";
            }
        }

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
            var resp = await axios.get('./test/task.json')
            this.tasks = resp.data
            this.clacTaskTimes()
            console.log(this.tasks)
        },
        clacTaskTimes() {
            // 開始位置とタスクの長さ（px）を計算し、this.tasks を更新
            this.tasks.forEach(daily => {
                daily.forEach(task => {
                    var startTimeMin = this.convertTimesToMins(task.startTime)
                    task.startTaskMin = startTimeMin - this.convertTimesToMins(this.open)
                    task.duration = this.convertTimesToMins(task.endTime) - startTimeMin
                });
            });
        },
        resizeWindow() {
            var clientWidth = document.getElementById('easygantt').clientWidth
            this.singleTimeScaleWidth = clientWidth / (this.scaleDiv + 1) - 9
            this.widthAboutMin = (this.singleTimeScaleWidth+1)/30
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
        // color: azure;
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
            line-height: 20px;
            height: 25px;
            display: block;
            clear: both;
            position: relative;
            white-space: nowrap;
        }

        // バブルの色：TODO
        .dev {
            span.bubble {
                background-color: #2b8fef;
            }
        }
        .lecture {
            span.bubble {
                background-color: #13d604;
            }
        }
        .meeting {
            span.bubble {
                background-color: #ffe74d;
            }
        }
        .event {
            span.bubble {
                background-color: #8470ff;
            }
        }
        .absence {
            span.bubble {
                background-color: #ffc0cb;
            }
        }
        .other {
            span.bubble {
                background-color: #a9a9a9;
            }
        }

        div:not(.milestone){
            .bubble {
                height: 10px;
                display: block;
                float: left;
                position: relative;
                top: 7px;
                border-radius: 4px;
                margin: 0 3px 0 0;
                opacity: 0.7;
            }
        }
    }
}
</style>
