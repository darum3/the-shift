<template>
    <tr>
        <td>
            <select class="custom-select custom-select-sm" v-model="inWorkType">
                <option v-for="workType in workTypes" :key="workType.code"
                :value="workType.code">{{workType.name}}</option>
            </select>
        </td>
        <td>
            <hour-min-select
                v-model="start"
            ></hour-min-select>
        </td>
        <td>
            〜
        </td>
        <td :class="{invalid: isUnmatch}">
            <hour-min-select
                v-model="end"
            ></hour-min-select>
            <span v-if="isUnmatch" class="invalid">終了時刻が不正です</span>
        </td>
        <td>
            <button v-if="value.number > 0" class='btn btn-sm btn-dark' @click="onClickDelete">ー</button>
        </td>
    </tr>
</template>

<script>
export default {
    props: {
        workTypes: {
            type: Array,
            required: true,
        },
        value: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            start: null,
            end: null,
            inWorkType: null,

            isUnmatch: false,
        }
    },
    created() {
        if (!this.value.start) {
            this.start = '0900'
        } else {
            this.start = this.value.start
        }
        if (!this.value.end) {
            this.end = '2300'
        } else {
            this.end = this.value.end
        }
        if (!this.value.work_type) {
            this.inWorkType = this.workTypes[0].code
        } else {
            this.inWorkType = this.value.work_type
        }
    },
    watch: {
        start() {
            this.validate()
            this.value.start = this.start
            this.$emit('input', this.value)
        },
        end() {
            this.validate()
            this.value.end = this.isUnmatch===true ? undefined : this.end
            this.$emit('input', this.value)
        },
        inWorkType() {
            this.value.work_type = this.inWorkType
            this.$emit('input', this.value)
        }
    },
    methods: {
        validate() {
            let isUnmatch = false
            if (this.start >= this.end) {
                isUnmatch = true
            }
            if (this.isUnmatch !== isUnmatch) {
                this.$emit('error', this.isUnmatch)
            }
            this.isUnmatch = isUnmatch
        },
        onClickDelete() {
            this.$emit('delete')
        },
    },
}
</script>

<style lang="scss" scoped>
    span.invalid {
        color:red;
        font-size: smaller;
    }
    td.invalid {
        border: 1px solid red;
    }
</style>
