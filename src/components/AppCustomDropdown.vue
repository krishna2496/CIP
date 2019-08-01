<template>
    <div v-if="optionList != null && optionList.length > 0" class="custom-dropdown select-dropdown">
        <span class="select-text">{{defaultText}}</span>
        <div class="option-list-wrap dropdown-option-wrap" data-simplebar>
            <ul class="option-list dropdown-option-list" v-if="translationEnable == 'false'">
                <li
                    v-for="item in optionList"
                    v-bind:data-id="item[0]"
                    @click="handleSelect"
                    @touchend="handleSelect"
                >{{item[1]}}</li>
            </ul>
            <ul class="option-list dropdown-option-list" v-else>
                <li
                    v-for="item in optionList"
                    v-bind:data-id="item[0]"
                    @click="handleSelect"
                    @touchend="handleSelect">{{langauageData.label[item[1]]}}</li>
            </ul>
        </div>
    </div>
</template>

<script>
import store from '../store';
export default {
    name: "AppCustomDropdown",
    components: {},
    props: {
        optionList: Array,
        defaultText: String,
        translationEnable : String,
    },
    data() {
        return {
            defaultTextVal: this.defaultText,
            langauageData : [],
        };
    },
    mounted() {
    },
    methods: {
        handleSelect(e) {
            var selectedData = []
            selectedData['selectedVal']  = e.target.innerHTML;
            selectedData['selectedId']  = e.target.dataset.id;
            this.$emit("updateCall", selectedData);
        }
    },
    beforeDestroy() {
        document.removeEventListener("click", this.onClick);
    },
    created() {
        this.langauageData = JSON.parse(store.state.languageLabel);
    }
};
</script>

