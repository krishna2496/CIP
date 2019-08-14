<template>
    <div class="row custom-field" v-if="customFieldList != null && customFieldList.length > 0">
                     <b-col :md="getColumn(item.type)"  v-for="(item,key) in optionList">
                            
                            <b-form-group v-if="item.type == 'drop-down'">
                                <label>{{item.translations.name}}</label>
                                <AppCustomFeildDropdown
                                    v-model="customFeildData[item.field_id]"  
                                    :defaultText="defaultValue[item.field_id]"
                                    :optionList="getArrayValue(item.translations.values)"
                                    :errorClass = "getErrorClass(item.field_id)"
                                    :fieldId = "item.field_id"
                                    translationEnable= "false"
                                    @updateCall="updateCustomDropDown"
                                />  
                            </b-form-group>

                            <b-form-group :label="item.translations.name" v-if="item.type == 'radio'">
                                <b-form-radio-group
                                    v-model="customFeildData[item.field_id]"  
                                    :id='`radio-${item.field_id}`'
                                    :class="{ 'is-invalid':false }" 
                                    :options="getRadioArrayValue(item.translations.values)"

                                     @change="updateChanges"
                                    :name="item.translations.name">   
                                </b-form-radio-group>
                            </b-form-group>

                            <b-form-group :label="item.translations.name"  v-if="item.type == 'checkbox'">
                                <b-form-checkbox-group
                                :id='`checkbox-${item.field_id}`'
                                v-model="customFeildData[item.field_id]"  
                                :options="getRadioArrayValue(item.translations.values)"
                                :name="item.translations.name"
                                 :class="{ 'is-invalid':false }" 
                                 @change="updateChanges"
                                >
                                </b-form-checkbox-group>
                            </b-form-group>

                            <b-form-group v-if="item.type == 'multiselect'">
                                <label>{{item.translations.name}}</label>
                                <AppCustomCheckboxDropdown 
                                    :filterTitle="defaultText" 
                                    v-model="customFeildData[item.field_id]"  
                                    :selectedItem="getSelectedItem(item.field_id)"
                                    :checkList="getRadioArrayValue(item.translations.values)"
                                    :errorClass = "getErrorClass(item.field_id)"
                                    :fieldId = "item.field_id"
                                    @updateCall="changeMultiSelect"
                                />
                            </b-form-group>

                            <b-form-group v-if="item.type == 'textarea'">
                                <label>{{item.translations.name}}</label>
                                <b-form-textarea
                                v-model="customFeildData[item.field_id]"  
                                :id='`textarea-${item.field_id}`'
                                :placeholder='`Enter ${item.translations.name}`'
                                rows="3"
                                :class="{ 'is-invalid': false }" 
                                @keypress="updateChanges"
                                max-rows="6"
                                >
                            </b-form-textarea>

                            </b-form-group>

                            <b-form-group v-if="item.type == 'text'">
                                <label>{{item.translations.name}}</label>
                                <b-form-input   
                                v-model="customFeildData[item.field_id]"  
                                @keypress="updateChanges"
                                :placeholder='`Enter ${item.translations.name}`'></b-form-input>  
                            </b-form-group>

                            <b-form-group v-if="item.type == 'email'">
                                <label>{{item.translations.name}}</label>
                                <b-form-input   
                                type="email"
                                v-model="customFeildData[item.field_id]"  
                                @keypress="updateChanges"
                                :placeholder='`Enter ${item.translations.name}`'></b-form-input>  
                            </b-form-group>
                    </b-col>
    </div>
    <div v-else>
        No data
    </div>
</template>
<script>
import store from "../store";
import AppCustomFeildDropdown from "../components/AppCustomFeildDropdown";
import MultiSelect from "../components/MultiSelect";
import AppCustomCheckboxDropdown from "../components/AppCustomCheckboxDropdown";
import { required,maxLength, email,sameAs, minLength, between,helpers,decimal} from 'vuelidate/lib/validators';
export default {
    components: {  
        AppCustomFeildDropdown,
        MultiSelect,
        AppCustomCheckboxDropdown
    },
    name: "customField",
    props: {
        optionList : Array,
        optionListValue : Array
    },
    data() {
        return {
            customFieldList : this.optionList,
            customField : [],
            list:[],
            customFieldValidation : {},
            defaultText : "",
            customFeildData :{},
            submit : false,
            defaultValue : {},
            langauageData : []
        };
    },
    validations() {
        const validations = {
                customFeildData : {}
            };
            var _this = this;
            
                _.each( this.customFieldList, wrr => {
                    // if(this.submit == true) {
                        validations.customFeildData[wrr.field_id] = {
                            required
                        };
                    // }
                    
                    
                    switch (wrr.type) {
                        case 'drop-down' :
                            if(wrr.translations.values[0] && wrr.translations.values[0][wrr.user_custom_field_value]) {
                                this.$set(this.defaultValue, wrr.field_id, wrr.translations.values[0][wrr.user_custom_field_value])
                            } else {
                                this.$set(this.defaultValue, wrr.field_id,this.defaultText)
                            }
                            this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                            break;
                        case 'text' :   
                                this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                                this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                            break;
                        case 'email' :
                                this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                                this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                            break;    
                        case 'textarea' :
                            this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                            this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                            break;
                        case 'multiselect' :
                            if(wrr.translations.values[0] &&  wrr.translations.values[0][wrr.user_custom_field_value]) {
                                this.$set(this.defaultValue, wrr.field_id, wrr.translations.values[0][wrr.user_custom_field_value])

                            } else {
                                this.$set(this.defaultValue, wrr.field_id,"")
                            }
                            this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                            break;
                        case 'radio' :
                            this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                            this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                            break;
                        case 'checkbox' :
                            this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                            this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                            break;
                        default:
                            this.$set(this.defaultValue, wrr.field_id, wrr.user_custom_field_value)
                            this.$set(this.customFeildData, wrr.field_id, wrr.user_custom_field_value)
                    }
                    
                    
                });
        
            return validations;
    },
    mounted() {
           
    },
    methods: {
        getColumn(type) {
            if(type == "radio" || type == "checkbox") {
                return 6
            } else {
                return 12
            }
        },
        updateCustomDropDown(value){
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
            if(data) {
                let listData = Object.keys(data).map(function(key) {
                    let newData = data[key]
                    Object.keys(newData).map(function(key) {
                        returnData.push({
                            text: newData[key],
                            value : key
                        });
                    });
                });
            }
            return returnData;
        },
        handleSubmit2() {
            this.$v.$touch();
          
            if (this.$v.$invalid) {
                return;
            }
            
            // console.log(this.customFeildData);
        },
        getErrorClass(id) {
            if(this.$v.customFeildData[id]) {
                return this.$v.customFeildData[id].$invalid
            } else {
                return false
            }
        },
        getRadioArrayValue(data) {
            let radioData = [];
            if(data) {
                let listData = Object.keys(data).map(function(key) {
                    let newData = data[key]
                    Object.keys(newData).map(function(key) {
                        radioData.push({
                            text: newData[key],
                            value : key
                        });
                    });
                });
            }
            return radioData;
        },
        getSelectedItem(id) {
            let selectedDataArray = [];
            let selectedData = this.$v.customFeildData[id].$model;
            if(selectedData != '') {
                let selectedString = selectedData.toString()
                selectedDataArray = selectedString.split(",");
            }
            return selectedDataArray
    
        },
        updateChanges() {
            this.$emit("detectChangeInCustomFeild",this.customFeildData);
        }
    },
   
    updated() {


    },
    created() {
       this.langauageData = JSON.parse(store.state.languageLabel);
       this.defaultText = this.langauageData.label.please_select
    }
};
</script>

