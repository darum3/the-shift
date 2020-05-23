Vue.mixin({
    methods: {
        // hhmmのフォーマットの時間を分数にして返す
        convertTimesToMins(time) {
            let hour = parseInt(String(time).slice(0, -2));
            let min = parseInt(String(time).slice(-2));
            let sumMins = hour * 60 + min;
            return sumMins;
        },
        getDisplayString(time) {
            return String(time).slice(0, -2) + ':' + String(time).slice(-2)
        },
        // 分数をhhmmフォーマットに戻す
        convertMinsToTimes(timeMin) {
            let hour = Math.floor(timeMin / 60)
            let min = timeMin % 60
            let time = ("00"+String(hour)).substr(-2) + ("00"+String(min)).substr(-2)
            return time
        }
    }
})
