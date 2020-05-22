<template>
    <modal height="85vh">
        <h3 slot=header>ユーザ選択</h3>

        <div slot="body">
            <table class='table table-striped table-hover'>
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="width: 10%">管理者</th>
                        <th scope="col" style="width: 20%">名前</th>
                        <th scope="col" style="width: 50%">メールアドレス</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in dispData" :key="user.id" class="clickable" @click="selectUser(user)">
                        <td style="width: 10%; text-align: center;"><span v-if="user.pivot.flg_admin==1">●</span><span v-else>&nbsp;</span></td>
                        <td style="width: 20%;">{{user.name}}</td>
                        <td style="width: 50%;">{{user.email}}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div slot='footer'>
            <button class="cancel-button btn-sm btn-warning" @click="$emit('close')">
            キャンセル
            </button>
        </div>
    </modal>
</template>

<script>
import axios from 'axios'

export default {
    props: {
        listApi: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            allData: null,
            dispData: [],
        }
    },
    created() {
        this.selectList()
    },
    methods: {
        async selectList() {
            var resp = await axios.get(this.listApi)
            this.allData = resp.data.users
            this.buildDispData();
        },
        buildDispData() {
            // 表示データ組み立て
            this.dispData = this.allData.filter(function(el) {
                return el  // TODO フィルタ実装
            })
        },
        selectUser(id) {
            this.$emit('select', id)
        }
    },
}
</script>

<style lang="scss">
.cancel-button {
    float: right;
}
</style>
