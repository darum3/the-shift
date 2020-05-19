<template>
    <div id="easygantt-vue">
        <easygantt-vue :open-hhmm="open" :close-hhmm="close"
            :rest-api="restApi"
            :date="date"
        ></easygantt-vue>
    </div>
</template>

<script>
export default {
    props: {
        openHhmm: {
            type: String,
            default: "0900",
        },
        closeHhmm: {
            type: String,
            default: "2200",
        },
        restApi: {
            required: true,
            type: String,
        },
        date: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            open: null,
            close: null,
        }
    },
    created() {
        this.open = this.open_hhmm
        this.close = this.close_hhmm
    },
}
</script>
