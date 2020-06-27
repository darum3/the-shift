<template>
    <div>
        <select v-model="hour" @change="onChangeValue()">
            <option v-for="h in hours" :key="h" :value="h">{{('00'+h).substr(-2)}}時</option>
        </select>
        <select v-model="minute" @change="onChangeValue()">
            <option v-for="m in minutes" :key="m" :value="m">{{('00'+m).substr(-2)}}分</option>
        </select>
    </div>
</template>

<script>
export default {
    props: {
        hourStart: {
            type: Number,
            default: 0,
        },
        hourEnd: {
            type: Number,
            default: 23,
        },
        minunteInterval: {
            type: Number,
            default: 1,
        },
        value: {
            type: String,
            default: '0000',
        },
    },
    data() {
        return {
            hour: 0,
            minute: 0,

            hours: [],
            minutes: [],
        }
    },
    created() {
        this.hours = [...Array(this.hourEnd-this.hourStart+1).keys()].map(i => i+this.hourStart)
        this.minutes = [...Array(60/this.minunteInterval).keys()].map(i => i * this.minunteInterval)

        // 初期値
        this.hour = Number(this.value.substr(0, 2))
        this.minute = Number(this.value.substr(2))
    },
    methods: {
        onChangeValue() {
            let value = ('00' + this.hour).slice(-2) + ('00' + this.minute).slice(-2)
            this.$emit('input', value)
        },
    },
}
</script>
