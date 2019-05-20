<template>
    <div v-if="field.config.exists">
        <a :href="field.value" v-bind="fieldAttributes" class="btn btn-primary">
            <template v-if="field.label">
                {{ field.label }}
            </template>
            <template v-else>
                <i class="fa fa-download"></i> Download
            </template>
        </a>

        <template v-if="field.config.with_edit">
            <edit-document :field="field.edit_field" :inline="true" />
        </template>
    </div>
</template>

<script>
    import EditDocument from './EditDocument.vue';

    export default {
        props: ['field'],

        components: {
            EditDocument
        }
    }
</script>
