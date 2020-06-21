<template>
    <tr>
        <td>
            <select class="custom-select custom-select-sm">
                <option v-for="workType in workTypes" :key="workType.code"
                :value="workType.code">{{workType.name}}</option>
            </select>
        </td>
        <td>
            <!-- 時 -->
    </tr>
</template>

<script>
export default {
    props: {
        row: {
            type: Object,
            required: true,
        },
        workTypes: {
            type: Array,
            required: true,
        },
    },
}
</script>
