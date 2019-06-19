<template>
    <div class="checkbox-select">
        <span class="select-text">{{filterTitle}}</span>
    <div class="chk-select-wrap" data-simplebar @click.stop @touchend.stop>
    <ul class="chk-select-options">
        <li v-for="(item , i) in checkList" :key="i">
            <b-form-checkbox name>{{item.value}}</b-form-checkbox>
        </li>
    </ul>
    </div>
    </div>
</template>

<script>
export default {
    name: "CheckboxDropdown",
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