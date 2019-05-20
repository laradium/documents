<template>
    <div :class="{'d-inline-block': this.inline}">
        <input type="hidden" :value="field.value" :name="field.name">

        <a href="#edit-document" data-toggle="modal" v-bind="fieldAttributes" class="btn btn-primary">
            <template v-if="field.label">
                {{ field.label }}
            </template>
            <template v-else>
                <i class="fa fa-pencil"></i> Edit
            </template>
        </a>

        <div id="edit-document" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit document</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <wysiwyg-field :field="content"></wysiwyg-field>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="saveContent" data-dismiss="modal">
                            Save
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'inline'],

        data() {
            return {
                content: {
                    label: null,
                    name: 'custom-content',
                    config: {
                        is_translatable: false,
                    },
                    value: this.field.value
                }
            }
        },

        methods: {
            saveContent() {
                this.field.value = this.content.value;
            }
        }
    }
</script>
