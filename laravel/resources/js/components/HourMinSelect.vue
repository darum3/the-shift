<template>
    <div>
        <select v-model="hour">
            <option v-for="h in hours" :key="h" :value="h">{{h}}時</option>
        </select>
        <select v-model="minute">
            <option v-for="m in minutes" :key="m" :value="m">{{m}}分</option>
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
        this.hours = [...Array(this.hourEnd-this.hourStart+1).keys()].map(i => i+this.hourEnd)
        this.minutes = [...Array(60/this.minunteInterval).keys()].map(i => i * this.minunteInterval)
    },
}
</script>
