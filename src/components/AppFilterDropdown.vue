<template>
    <div class="custom-dropdown">
        <span class="select-text">{{defaultText}}</span>
        <div class="option-list-wrap" data-simplebar v-if="optionList != null && optionList.length > 0" >
            <ul class="option-list" v-if="translationEnable == 'false'">
                <li
                    v-for="item in optionList"
                    v-bind:data-id="item[1].id"
                    @click="handleSelect"
                    @touchend="handleSelect"
                    >{{item[1].title}}
                </li>
            </ul>
            <ul class="option-list" v-else>
                <li
                    v-for="item in optionList"
                    v-bind:data-id="item[0]"
                    @click="handleSelect"
                    @touchend="handleSelect">{{$t(`label.${item[1]}`)}}</li>
            </ul>
        </div>
        <div class="option-list-wrap" data-simplebar v-else >
            <ul class="option-list" v-if="translationEnable == 'false'">
                <li> {{ $t("label.no_record_found")}} </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    name: "AppFilterDropdown",
    components: {},
    props: {
        optionList: Array,
        defaultText: String,
        translationEnable : String,
    },
    data() {
        return {
            defaultTextVal: this.defaultText
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
    }
};
</script>

