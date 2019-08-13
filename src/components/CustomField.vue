<template>
    <div class="row" v-if="customFieldList != null && customFieldList.length > 0">
        <!-- {{$v.report.readings}} -->
                
                    <b-col md="6"  v-for="(item,key) in optionList">
                            <b-form-group v-if="item.type == 'drop-down'">
                                <label>{{item.translations.name}}</label>
                                <AppCustomDropdown
                                    v-model="$v.report.readings[item.field_id].$model"  
                                    :defaultText="defaultText"
                                    :optionList="getArrayValue(item.translations.values)"
                                    :errorClass = "getErrorClass(item.field_id)"
                                    translationEnable= "false"
                                    @updateCall="updateCustomDropDown"
                                />  
                            </b-form-group>

                            <b-form-group :label="item.translations.name" v-if="item.type == 'radio'">
                                <b-form-radio-group
                                :id='`radio-${item.field_id}`'
                                :class="{ 'is-invalid':true }" 
                                :options="getRadioArrayValue(item.translations.values)"
                                :name="item.translations.name">   
                                </b-form-radio-group>
                                <!-- <b-form-radio-group
                                :id='`radio-${item.field_id}`'
                                v-model="selected"
                                :options="getRadioArrayValue(item.translations.values)"
                                :name="item.translations.name">   
                                </b-form-radio-group> -->
                            </b-form-group>

                            <b-form-group :label="item.translations.name"  v-if="item.type == 'checkbox'">
                                <b-form-checkbox-group
                                :id='`checkbox-${item.field_id}`'
                               
                                :options="getRadioArrayValue(item.translations.values)"
                                :name="item.translations.name"
                                ></b-form-checkbox-group>
                            </b-form-group>

                            <b-form-group v-if="item.type == 'multiselect'">
                                <label>{{item.translations.name}}</label>
                                <AppCustomCheckboxDropdown 
                                    :filterTitle="defaultText" 
                                   
                                    :checkList="getRadioArrayValue(item.translations.values)"
                                  
                                />
                                <!--  <AppCheckboxDropdown 
                                    :filterTitle="defaultText" 
                                    :selectedItem="selectedTheme"
                                    :checkList="themeList"
                                    @changeParmas="changeThemeParmas"
                                    @updateCall="changeTheme"
                                /> -->
                            </b-form-group>

                            <b-form-group v-if="item.type == 'textarea'">
                                 <label>{{item.translations.name}}</label>
                                <b-form-textarea
                                :id='`textarea-${item.field_id}`'
                                :placeholder='`Enter ${item.translations.name}`'
                                rows="3"
                                max-rows="6"
                                >
                            </b-form-textarea>

                            </b-form-group>

                            <b-form-group v-if="item.type == 'text'">
                                <label>{{item.translations.name}}</label>
                                <b-form-input   :placeholder='`Enter ${item.translations.name}`'></b-form-input>  
                            </b-form-group>

                          

                    </b-col>

                    <div class="btn-wrapper">
                    <b-button class="btn-bordersecondary" @click="handleSubmit2">save</b-button>
                </div>
    </div>
    <div v-else>
        No data
    </div>
</template>
<script>
import store from "../store";
import AppCustomDropdown from "../components/AppCustomDropdown";
import MultiSelect from "../components/MultiSelect";
import AppCustomCheckboxDropdown from "../components/AppCustomCheckboxDropdown";
import { required,maxLength, email,sameAs, minLength, between,helpers,decimal} from 'vuelidate/lib/validators';
export default {
    components: {  
        AppCustomDropdown,
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
            defaultText : "Please select",
            report: {
                readings: {}
            },
            submit : false
        };
    },
    validations() {
        const validations = {
                report: {
                    readings: {
                    }
                }
            };
            var _this = this;
            
                _.each( this.customFieldList, wrr => {
                    // if(this.submit == true) {
                        validations.report.readings[wrr.field_id] = {
                            required
                        };
                    // }
                    this.$set(this.report.readings, wrr.field_id, '')
                });
            
            // console.log( 'water report ready', validations);
            return validations;
    },
    mounted() {
           
    },
    methods: {
        updateCustomDropDown(value){
            console.log(value)
            this.report.readings[1] = value.selectedId
            //  this.countryDefault = value.selectedVal;
            // this.profile.country =  value.selectedId;
        },
        getArrayValue(data) {
            let listData = Object.keys(data).map(function(key) {
                        return [Number(key), data[key]];
            });
            return listData;
        },
        handleSubmit2() {
            this.$v.$touch();
          
            if (this.$v.$invalid) {
                return;
            }
        },
        getErrorClass(id) {
            if(this.$v.report.readings[id]) {
                return this.$v.report.readings[id].$dirty
            } else {
                return false
            }
        },
        getRadioArrayValue(data) {
            let radioData = [];
            let listData = Object.keys(data).map(function(key) {
                let newData = data[key]
                Object.keys(newData).map(function(key) {
                
                    radioData.push({
                        text: newData[key],
                        value : key
                    });
                });
            });
              console.log(radioData)
            return radioData;
        },
        getMultiSelectArrayValue(data) {
            let multiselectData = [];
            let listData = Object.keys(data).map(function(key) {
                        return [Number(key), data[key]];
            });
            return listData;
        }
    },

    updated() {
    },
    created() {
       
    }
};
</script>

