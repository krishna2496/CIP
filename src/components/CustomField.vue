<template>
    <div class="row custom-field" v-if="CustomFieldList != null && CustomFieldList.length > 0">
        <b-col :md="getColumn(item.type)" v-for="(item,key) in optionList" :key=key>
            <b-form-group v-if="item.type == 'drop-down'">
                <label>{{item.translations.name}}
                    <span v-if="item.is_mandatory == 1">*</span>
                </label>
                <AppCustomFieldDropdown v-model="customFeildData[item.field_id]"
                    :defaultText="defaultValue[item.field_id]" :optionList="getArrayValue(item.translations.values)"
                    :errorClass="getErrorClass(item.field_id)" :validstate="getErrorState(item.field_id)"
                    :fieldId="item.field_id" translationEnable="false" @updateCall="updateCustomDropDown" />
                <div v-if="getErrorClass(item.field_id)" class="invalid-feedback">
                    {{item.translations.name}} {{ languageData.errors.field_required }}
                </div>
            </b-form-group>
            <b-form-group v-if="item.type == 'radio'">
                <label>{{item.translations.name}}
                    <span v-if="item.is_mandatory == 1">*</span>
                </label>
                <b-form-radio-group v-model="customFeildData[item.field_id]" :id='`radio-${item.field_id}`'
                    :options="getRadioArrayValue(item.translations.values)"
                    :class="{ 'is-invalid': getErrorClass(item.field_id) }" :validstate="getErrorState(item.field_id)"
                    @change="updateChanges" :name="item.translations.name">
                </b-form-radio-group>
            </b-form-group>
            <b-form-group v-if="item.type == 'checkbox'">
                <label>{{item.translations.name}}
                    <span v-if="item.is_mandatory == 1">*</span>
                </label>
                <b-form-checkbox-group id="checkbox-1" v-model="customFeildData[item.field_id]"
                    :options="getRadioArrayValue(item.translations.values)" name="checkbox-custom"
                    :class="{ 'is-invalid': getErrorClass(item.field_id) }" :validstate="getErrorState(item.field_id)"
                    @input="updateChanges">
                </b-form-checkbox-group>
            </b-form-group>
            <b-form-group v-if="item.type == 'multiselect'">
                <label>{{item.translations.name}} <span v-if="item.is_mandatory == 1">*</span></label>
                <AppCustomCheckboxDropdown :filterTitle="defaultText" v-model="customFeildData[item.field_id]"
                    :selectedItem="getSelectedItem(item.field_id)"
                    :checkList="getRadioArrayValue(item.translations.values)" :errorClass="getErrorClass(item.field_id)"
                    :validstate="getErrorState(item.field_id)" :fieldId="item.field_id"
                    @updateCall="changeMultiSelect" />
                <div v-if="getErrorClass(item.field_id)" class="invalid-feedback">
                    {{item.translations.name}} {{ languageData.errors.field_required }}
                </div>
            </b-form-group>
            <b-form-group v-if="item.type == 'textarea'">
                <label>{{item.translations.name}}<span v-if="item.is_mandatory == 1">*</span></label>
                <b-form-textarea v-model.trim="customFeildData[item.field_id]" :id='`textarea-${item.field_id}`'
                    :placeholder='`Enter ${item.translations.name}`' rows="3"
                    :class="{ 'is-invalid': getErrorClass(item.field_id) }" :validstate="getErrorState(item.field_id)"
                    @change="updateChanges" max-rows="6">
                </b-form-textarea>
                <div v-if="getErrorClass(item.field_id)" class="invalid-feedback">
                    {{item.translations.name}} {{ languageData.errors.field_required }}
                </div>
            </b-form-group>
            <b-form-group v-if="item.type == 'text'">
                <label>{{item.translations.name}}<span v-if="item.is_mandatory == 1">*</span></label>

                <b-form-input v-model.trim="customFeildData[item.field_id]" @input="updateChanges"
                    :class="{ 'is-invalid': getErrorClass(item.field_id) }" :validstate="getErrorState(item.field_id)"
                    :placeholder='`Enter ${item.translations.name}`'></b-form-input>
                <div v-if="getErrorClass(item.field_id)" class="invalid-feedback">
                    {{item.translations.name}} {{ languageData.errors.field_required }}
                </div>
            </b-form-group>

            <b-form-group v-if="item.type == 'email'">
                <label>{{item.translations.name}} <span v-if="item.is_mandatory == 1">*</span></label>

                <b-form-input type="email" v-model.trim="customFeildData[item.field_id]" @input="updateChanges"
                    :class="{ 'is-invalid': getErrorClass(item.field_id) }" :validstate="getErrorState(item.field_id)"
                    :placeholder='`Enter ${item.translations.name}`'></b-form-input>
                <div v-if="getErrorClass(item.field_id)" class="invalid-feedback">
                    <span v-if="!$v.customFeildData[item.field_id].required">{{item.translations.name}}
                        {{ languageData.errors.field_required }}</span>
                    <span
                        v-if="!$v.customFeildData[item.field_id].email">{{ languageData.errors.invalid_email }}</span>
                </div>
            </b-form-group>
        </b-col>
    </div>
    <div v-else>
    </div>
</template>
<script>
    import store from "../store";
    import AppCustomFieldDropdown from "../components/AppCustomFieldDropdown";
    import AppCustomCheckboxDropdown from "../components/AppCustomCheckboxDropdown";
    import {
        required,
        email
    } from 'vuelidate/lib/validators';
    export default {
        components: {
            AppCustomFieldDropdown,
            AppCustomCheckboxDropdown
        },
        name: "CustomField",
        props: {
            optionList: Array,
            optionListValue: Array,
            isSubmit: Boolean
        },
        data() {
            return {
                CustomFieldList: this.optionList,
                CustomField: [],
                list: [],
                CustomFieldValidation: {},
                defaultText: "",
                customFeildData: {},
                submit: false,
                defaultValue: {},
                languageData: []
            };
        },
        validations() {
            const validations = {
                customFeildData: {}
            };

            _.each(this.CustomFieldList, wrr => {
                if (wrr.is_mandatory == 1) {
                    validations.customFeildData[wrr.field_id] = {
                        required
                    };
                } else {
                    validations.customFeildData[wrr.field_id] = {};
                }

                if (wrr.type == "email") {
                    if (wrr.is_mandatory == 1) {
                        validations.customFeildData[wrr.field_id] = {
                            required,
                            email
                        };
                    } else {
                        validations.customFeildData[wrr.field_id] = {
                            email
                        };
                    }
                }

                switch (wrr.type) {
                    case 'drop-down':
                        if (wrr.translations.values[0] && wrr.translations.values[0][wrr
                                .user_custom_field_value]) {
                            this.$set(this.defaultValue, wrr.field_id, wrr.translations.values[0][wrr
                                .user_custom_field_value
                            ])
                        } else {
                            this.$set(this.defaultValue, wrr.field_id, this.defaultText)
                        }
                        this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                        break;
                    case 'text':
                        this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                        this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                        break;
                    case 'email':
                        this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                        this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                        break;
                    case 'textarea':
                        this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                        this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                        break;
                    case 'multiselect':
                        if (wrr.translations.values[0] && wrr.translations.values[0][wrr
                                .user_custom_field_value]) {
                            this.$set(this.defaultValue, wrr.field_id, wrr.translations.values[0][wrr
                                .user_custom_field_value
                            ])

                        } else {
                            this.$set(this.defaultValue, wrr.field_id, "")
                        }
                        this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                        break;
                    case 'radio':
                        this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                        this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                        break;
                    case 'checkbox':
                        this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                        if (wrr.user_custom_field_value.toString().indexOf(",") !== -1) {
                            this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value.split(
                                ","))
                        } else {
                            if (wrr.user_custom_field_value.toString() != '') {
                                this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value
                                    .toString().split(","))
                            } else {
                                this.$set(this.customFeildData, wrr.field_id, [])
                            }
                        }
                        break;
                    default:
                        this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                        this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                }
            });
            return validations;
        },
        mounted() {},
        methods: {
            getColumn(type) {
                if (type == "radio" || type == "checkbox") {
                    return 6
                } else {
                    return 12
                }
            },
            updateCustomDropDown(value) {
                this.customFeildData[value.fieldId] = value.selectedId
                this.defaultValue[value.fieldId] = value.selectedVal
                this.updateChanges();
            },
            changeMultiSelect(value) {
                this.customFeildData[value.fieldId] = value.selectedVal
                this.updateChanges();
            },
            getArrayValue(data) {
                let returnData = [];
                if (data) {
                    Object.keys(data).map(function (key) {
                        let newData = data[key]
                        Object.keys(newData).map(function (key) {
                            returnData.push({
                                text: newData[key],
                                value: key
                            });
                        });
                    });
                }
                return returnData;
            },
            getErrorClass(id) {
                if (this.$v.customFeildData[id] && this.isSubmit == true) {
                    return this.$v.customFeildData[id].$invalid
                } else {
                    return false
                }
            },
            getErrorState(id) {
                if (this.$v.customFeildData[id]) {
                    return this.$v.customFeildData[id].$invalid
                } else {
                    return false
                }
            },
            getRadioArrayValue(data) {
                let radioData = [];
                if (data) {
                    Object.keys(data).map(function (key) {
                        let newData = data[key]
                        Object.keys(newData).map(function (key) {
                            radioData.push({
                                text: newData[key],
                                value: key
                            });
                        });
                    });
                }
                return radioData;
            },
            getSelectedItem(id) {
                let selectedDataArray = [];
                let selectedData = this.$v.customFeildData[id].$model;
                if (selectedData != '') {
                    let selectedString = selectedData.toString()
                    selectedDataArray = selectedString.split(",");
                }
                return selectedDataArray
            },
            updateChanges() {
                this.$emit("detectChangeInCustomFeild", this.customFeildData);
            }
        },
        updated() {},
        created() {
            this.languageData = JSON.parse(store.state.languageLabel);
            this.defaultText = this.languageData.label.please_select
        }
    };
</script>