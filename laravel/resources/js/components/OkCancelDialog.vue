<template>
    <modal>
        <h3 slot='header'>確認</h3>
        <div slot="body">{{message}}</div>
        <div slot='footer'>
            <button class="dialog-button btn-sm btn-warning" @click="$emit('cancel')">キャンセル</button>
            <button class="dialog-button btn-sm btn-primary" @click="$emit('ok')">OK</button>
        </div>
    </modal>
</template>

<script>
export default {
    props: {
        message: String,
    },
}
</script>

<style lang="scss" scoped>
    button.dialog-button {
        float: right;
    }
</style>
