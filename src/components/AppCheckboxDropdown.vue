<template>
    <div class="checkbox-select select-dropdown dropdown-with-counter">
        <span class="select-text">{{filterTitle}}</span>
    <div class="chk-select-wrap dropdown-option-wrap" data-simplebar @click.stop @touchend.stop>
    <ul class="chk-select-options dropdown-option-list" v-if="checkList.length > 0">
        <li 
            v-for="(item , i) in checkList" 
            v-bind:data-id="item[1].id"
            :key="i"           
            >
            <b-form-checkbox name  v-model="items" @click.native ="filterTable" v-bind:value="item[1].id">{{item[1].title}}<span class="counter">{{item[1].mission_count}}</span></b-form-checkbox>
        </li>
    </ul>
    <ul class="chk-select-options dropdown-option-list" v-else>
        <li>
            <label class="no-checkbox">{{ $t("label.no_record_found")}}</label>
        </li>
    </ul>
    </div>
    </div>
</template>

<script>
import Vue from "vue";
export default {
    name: "AppCheckboxDropdown",
    components: {},
    props: {
        filterTitle: String,
        checkList: {
        type: Array,
            default: () => []
        },
        selectedItem: Array,
    },

    data() {
        return {
            items: this.selectedItem,
        };
    },
    mounted() {},
    methods: {
        filterTable() {
            this.$emit("changeParmas");
        }
    },
    watch: {
        items: function(val){            
            this.$emit("updateCall",val.join(','));
        },
        selectedItem:function(val){
            this.items = this.selectedItem;
        },
    },
    created() {
    },
};
</script>