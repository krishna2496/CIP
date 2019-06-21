<template>
    <div class="custom-chip">
        <span class="chip-content">
            <i class="document-icon">
                <img :src="url"  alt="document"/>
            </i>
            {{textVal}}
            <i class="chip-close">
                <img src="../assets/images/cross-ic.svg" alt="close"/>
            </i>
        </span>
    </div>
</template>
<script>
    export default {
        name: "AppCustomChip",
        props: {
            textVal : String,
            url: String,
        },
        data() {
            return {};
        },
        methods: {},
    };
</script>