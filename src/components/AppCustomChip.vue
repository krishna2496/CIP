<template>
    <div class="custom-chip">
        <span class="chip-content">
            <i class="document-icon">
                <img :src="url"  alt="document"/>
            </i>
            {{textVal}}
            <i class="chip-close" 
                v-bind:data-id="tagId"
                v-bind:data-type="type"
                @click="handleSelect"
            >
                <img v-bind:data-id="tagId"
                v-bind:data-type="type" src="../assets/images/cross-ic.svg" alt="close"/>
            </i>
        </span>
    </div>
</template>
<script>
    export default {
        name: "AppCustomChip",
        props: {
            textVal : String,
            tagId: String,
            type: String,
            url: String,
        },
        data() {
            return {};
        },
        methods: {
            handleSelect(e) {
                var selectedData = []
                selectedData['selectedId']  = e.target.dataset.id;
                selectedData['selectedType']  = e.target.dataset.type;
                this.$emit("updateCall", selectedData);
            }
        },
    };
</script>