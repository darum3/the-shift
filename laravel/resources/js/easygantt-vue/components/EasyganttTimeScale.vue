<template>
    <div class="scale">
        <section :style="'width:'+nameWidth+'px;'" class="person_name">
            <div v-if="delEnable" class='person_delete' :style="'width:'+nameWidth+'px; height:30px;'" @click="$emit('delete')"></div>
            <div v-else :style="'width:'+nameWidth+'px;'">&nbsp;</div>
        </section>
        <section v-for="time in timeScale" :style="'width:'+singleTimeScaleWidth+'px;'" :key="time">
            {{time}}
        </section>
    </div>
</template>

<script>
export default {
    props: {
        nameWidth: {
            required: true,
            type: Number,
        },
        singleTimeScaleWidth: {
            required: true,
            type: Number,
        },
        open: {
            required: true,
        },
        close: {
            required: true,
        },
        delEnable: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            timeScale: [],
        }
    },
    created() {
        let openMin = this.convertTimesToMins(this.open)
        let closeMin = this.convertTimesToMins(this.close)
        let scaleDiv = (closeMin - openMin) / 30
        for(let i=0; i <= scaleDiv; i++) {
            this.timeScale[i] = String((openMin + (i * 30))/60);
            if(this.timeScale[i].slice(-2) === ".5") {
                this.timeScale[i] = this.timeScale[i].replace(".5", ":30");
            } else {
                this.timeScale[i] = this.timeScale[i] + ":00";
            }
        }
    }
}
</script>

<style lang="scss" scoped>
    div.person_delete {
        cursor: pointer;
    }
</style>
