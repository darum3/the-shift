<template>
    <div>
        <h5>シフト入力：{{target}}</h5>
        <table class='table table-sm'>
            <thead class='table-success'>
                <tr>
                    <th scope='col'>職種</th>
                    <th scope='col'>開始時間</th>
                    <th scope='col'>&nbsp;</th>
                    <th scope='col'>終了時間</th>
                    <th scope='col'>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <desired-input-tr v-for="(row, index) in inputs" :key="row.number"
                    :work-types="workTypes"
                    v-model="inputs[index]"
                    @delete="deleteRow(row.number)"
                ></desired-input-tr>
            </tbody>
        </table>
        <div><button class='btn btn-secondary btn-sm' @click="addRow()">追加</button></div>
        <hr />
        <h5>同じシフト希望を提出</h5>
        <button class='btn btn-sm btn-dark' @click="weekSelect">全て選択</button>
        <fieldset class='form-inline'>
            <!-- <legend>同じ週</legend> -->
            <div class='custom-control custom-checkbox date-select' v-for="date in dates" :key="date.getDay()">
                <input :disabled="isSameAsTarget(date)"
                    type='checkbox'
                    class='custom-control-input'
                    :id="'check'+date.getDay()"
                    v-model="checked[date.getDay()]"
                    :name="date.getFullYear()+date.getMonth()+date.getDate()"
                    />
                <label class='custom-control-label' :for="'check'+date.getDay()">
                    {{ (date.getMonth()+1)+'/'+date.getDate() + getWeekDay(date.getDay()) }}
                </label>
            </div>
        </fieldset>
        <hr />
        <button class='btn btn-primary' @click="clickSend()">提出</button>
    </div>
</template>

<script>
const WEEKDAYS = ['(日)', '(月)', '(火)', '(水)', '(木)', '(金)', '(土)']
import axios from 'axios'
export default {
    props: {
        originalDataJson: {
            type: String,
            required: true,
        },
        workTypesJson: {
            type: String,
            required: true,
        },
        target: {
            type: String,
            required: true,
        },
        sunday: {
            type: String,
            required: true,
        },
        url: {
            type: String,
            required: true,
        },
        listUrl: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            inputs: [],
            workTypes: null,
            dates: [],
            checked: [],
        }
    },
    created() {
        this.workTypes = JSON.parse(this.workTypesJson)
        this.inputs = JSON.parse(this.originalDataJson).map((element, index) => ({
            number: index,
            start: element.time_start.substr(0, 2) + element.time_start.substr(3, 2),
            end: element.time_end.substr(0, 2) + element.time_end.substr(3, 2),
            work_type: this.workTypes.find(work => work.id === element.work_type_id).code
        }))
        // console.log(this.inputs)
        if (this.inputs.length === 0) {
            this.inputs.push({number: 0})
        }

        let today = new Date(this.target)
        let date = new Date(this.sunday)
        for(let i = 0; i < 7; ++i) {
            let day = new Date(date)
            day.setDate(day.getDate() + i)

            this.dates.push(day)
            this.checked.push(today.getDay() === i)
        }
    },
    methods: {
        deleteRow(number) {
            let pos = this.inputs.findIndex((element) => element.number === number)
            this.inputs.splice(pos, 1)
        },
        addRow() {
            this.inputs.push({'number': this.inputs[this.inputs.length-1].number + 1})
        },
        isSameAsTarget(date) {
            let dateStr = date.getFullYear() + '-' + ('00' + (date.getMonth() + 1)).slice(-2) + '-' + ('00' + (date.getDate())).slice(-2)
            return dateStr === this.target
        },
        weekSelect() {
            for(let i = 0; i < 7; ++i) {
                this.$set(this.checked, i, true)
            }
        },
        getWeekDay(value) {
            return WEEKDAYS[value]
        },
        async clickSend() {
            let desired = this.inputs.map(element => ({
                start: element.start,
                end: element.end,
                work_type: element.work_type,
            }))

            let param = this.dates
                .filter((date, index) => this.checked[index])
                .map(date => ({
                    target_date: date.getFullYear() + '-' + ('00' + (date.getMonth()+1)).slice(-2) + '-' + ('00' + (date.getDate())).slice(-2),
                    desired: desired
                }))

            try {
                await axios.post(this.url, param)
                location.href = this.listUrl
            } catch(e) {
                if (!e.response) {
                    console.error(e.response)
                    return;
                } else if (e.response.status === 422) {
                    console.error(e.response.data.errors)
                } else {
                    console.error(e.response)
                }
            }
        },
    },
}
</script>

<style lang="scss" scoped>
div.date-select {
    margin-right: 1em;
}
</style>
