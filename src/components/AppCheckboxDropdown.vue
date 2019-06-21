<template>
    <div class="checkbox-select">
        <span class="select-text">{{filterTitle}}</span>
    <div class="chk-select-wrap" data-simplebar @click.stop @touchend.stop>
    <ul class="chk-select-options">
        <li 
            v-for="(item , i) in checkList" 
            v-bind:data-id="item[0]"
            :key="i">
            <b-form-checkbox name>{{item[1]}}</b-form-checkbox>
        </li>
    </ul>
    </div>
    </div>
</template>

<script>
export default {
    name: "AppCheckboxDropdown",
    components: {},
    props: {
        filterTitle: String,
        checkList: {
        type: Array,
            default: () => []
        }
    },
    data() {
        return {};
    },
    mounted() {},
    methods: {}
};
</script>