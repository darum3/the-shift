<template>
    <div id="easygantt-vue">
        <easygantt-vue :open-hhmm="open" :close-hhmm="close"
            :rest-api="restApi"
            :user-list-api="userListApi"
            :date="date"
            :edit="edit"
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
        edit: {
            type: Boolean,
            default: false,
        },
        userListApi: {
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
        this.open = this.openHhmm
        this.close = this.closeHhmm
    },
}
</script>
