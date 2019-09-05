<template>
    <div>
        <b-modal ref="timeHoursModal" @hidden ="hideModal" :modal-class="'time-hours-modal table-modal'" hide-footer>
            <template slot="modal-header" slot-scope="{ close }">
                <i class="close" @click="close()" v-b-tooltip.hover :title="langauageData.label.close"></i>
                    <h5 class="modal-title">{{langauageData.label.hour_entry_modal_title}}</h5>
                </template>
                <b-alert show :variant="classVariant" dismissible v-model="showErrorDiv">
                    {{ message }}
                </b-alert>
            {{$v.timeEntryDefaultData.minutes}}
            <form action class="form-wrap">
                <b-form-group>
                    <b-row>
                        <b-col sm="6">
                            <!-- <b-form-group> -->
                             <!--    <label for>{{langauageData.label.hours}}</label>
                                <b-form-input id 
                                type="text"
                                v-model.trim="timeEntryDefaultData.hours"
                                :class="{ 'is-invalid': submitted && $v.timeEntryDefaultData.hours.$error }" 
                                :placeholder="langauageData.placeholder.spent_hours"></b-form-input>
                            </b-form-group> -->

                            <b-form-group>
                                <label for>{{langauageData.label.hours}}</label>
                                <AppCustomDropdown
                                    v-model="timeEntryDefaultData.hours"
                                    :optionList="hourList"
                                    :defaultText="defaultHours"
                                    :errorClass="submitted && $v.timeEntryDefaultData.hours.$error" 
                                    @updateCall="updateHours"
                                    translationEnable= "false"
                                />
                                
                            </b-form-group>

                        </b-col>
                        <b-col sm="6">
                            <!-- minuteList -->
                          <!--   <b-form-group>
                                <label for>{{langauageData.label.minutes}}</label>
                                <b-form-input id type="text"
                                v-model.trim="timeEntryDefaultData.minutes"
                                :class="{ 'is-invalid': submitted && $v.timeEntryDefaultData.minutes.$error }"
                                :placeholder="langauageData.placeholder.spent_minutes"></b-form-input>
                            </b-form-group> -->

                              <b-form-group>
                                <label for>{{langauageData.label.minutes}}</label>
                                <AppCustomDropdown
                                    v-model="timeEntryDefaultData.hours"
                                    :optionList="minuteList"
                                    :errorClass="submitted && $v.timeEntryDefaultData.minutes.$error" 
                                    :defaultText="defaultMinutes"
                                    @updateCall="updateMinutes"
                                    translationEnable= "false"
                                />
                                <div v-if="submitted && !$v.timeEntryDefaultData.minutes.required" class="invalid-feedback">
                                    {{ langauageData.errors.minute_or_hours_is_required }}</div>
                            </b-form-group>

                        </b-col>
                    </b-row>
                </b-form-group>
                <b-form-group>
                    <b-row>
                        <b-col sm="6" class="date-col">
                            <b-form-group>
                                <label for>{{langauageData.label.date_volunteered}}</label>
                                <date-picker
                                    v-model="timeEntryDefaultData.dateVolunteered"
                                    :notAfter="disabledFutureDates"
                                    :disabledDays="disableDates"
                                    :class="{ 'is-invalid': submitted && $v.timeEntryDefaultData.dateVolunteered.$error }"
                                    :lang="lang"
                                ></date-picker>
                                <div v-if="submitted && !$v.timeEntryDefaultData.dateVolunteered.required" class="invalid-feedback">
                                    {{ langauageData.errors.date_volunteer_is_required }}</div>
                            </b-form-group>
                        </b-col>
                    <b-col sm="6" class="date-col">
                        <b-form-group>
                            <label for>{{langauageData.label.workday}}</label>
                                    <AppCustomDropdown
                                        v-model="timeEntryDefaultData.workDay"
                                        :optionList="workDayList"
                                        :errorClass="submitted && $v.timeEntryDefaultData.workDay.$error" 
                                        :defaultText="defaultWorkday"
                                        @updateCall="updateWorkday"
                                        translationEnable= "true"
                                    />
                        </b-form-group>
                        <div v-if="submitted && !$v.timeEntryDefaultData.workDay.required" class="invalid-feedback">
                                    {{ langauageData.errors.work_day }}</div>
                    </b-col>
                    </b-row>
                </b-form-group>
                <b-form-group>
                    <b-row>
                        <b-col sm="12">
                            <b-form-group>
                                <label for>{{langauageData.label.notes}}:</label>
                                <b-form-textarea id
                                v-model="timeEntryDefaultData.notes"
                                :class="{ 'is-invalid': submitted && $v.timeEntryDefaultData.notes.$error }"
                                :placeholder="langauageData.placeholder.notes"
                                size="lg" no-resize rows="5"></b-form-textarea>
                            </b-form-group>
                             <div v-if="submitted && !$v.timeEntryDefaultData.notes.required" class="invalid-feedback">
                                    {{ langauageData.errors.notes }}</div>
                        </b-col>
                    </b-row>
                </b-form-group>
                <b-form-group>
                    <b-row>
                        <b-col sm="12" class="date-col">
                        <label for>{{langauageData.label.file_upload}}</label>
                        <div class="file-upload-wrap">
                            <div class="btn-wrapper">
                                <file-upload
                                    class="btn"
                                    extensions="gif,jpg,jpeg,png,webp"
                                    accept="image/png,image/gif,image/jpeg,image/webp"
                                    :multiple="true"
                                    :drop="true"
                                    :drop-directory="true"
                                    :size="1024 * 1024 * 10"
                                    v-model="files"
                                    ref="upload">
                                Browse
                                </file-upload>
                                <span>Or drop files here</span>
                            </div>
                            <div class="uploaded-file-details" v-for="(file, index) in files" :key="file.id">
                                <p class="filename">{{file.name}}</p>
                                <a 
                                class="remove-item" 
                                href="#" 
                                @click.prevent="$refs.upload.remove(file)" 
                                v-b-tooltip.hover 
                                title="Delete"
                                >
                                    <img :src="$store.state.imagePath+'/assets/images/delete-ic.svg'" alt="delete-ic"/>
                                </a>
                            </div>
                        </div>
                        </b-col>
                    </b-row>
                </b-form-group>
            </form>
        <div class="btn-wrap">
            <b-button
                class="btn-borderprimary"
                @click="$refs.timeHoursModal.hide()"
                title="Cancel"
            >{{langauageData.label.cancel}}</b-button>
            <b-button 
                class="btn-bordersecondary" 
                @click="saveTimeHours()" 
                title="Submit">{{langauageData.label.submit}}
            </b-button>
        </div>
        </b-modal>
    </div>
</template>

<script>
import store from '../store';
import moment from 'moment'
import DatePicker from "vue2-datepicker";
import AppCustomDropdown from "../components/CustomFieldDropdown";
import { required,maxLength, email,sameAs, minLength, between,helpers,numeric,requiredIf} from 'vuelidate/lib/validators';
import FileUpload from 'vue-upload-component';
import {addVolunteerHours} from '../services/service';

export default {
    name: "VolunteeringHours",
    components: {
        DatePicker,
        AppCustomDropdown,
        FileUpload
    },
    props: {
        defaultWorkday : String,
        files: Array,
        timeEntryDefaultData : Object,
        workDayList : Array,
        disableDates : Array,
        defaultHours : String,
        defaultMinutes : String
    },
    data: function() {
        return {
            lang : '',
            langauageData : [],
            submitted : false,
            disabledFutureDates : new Date(),
            showErrorDiv : false,
            message : null,
            classVariant :"success",
            hourList:[
                    ["0","00"],
                    ["1","01"],
                    ["2","02"],
                    ["3","03"],
                    ["4","04"],
                    ["5","05"],
                    ["6","06"],
                    ["7","07"],
                    ["8","08"],
                    ["9","09"],
                    ["10","10"],
                    ["11","11"],
                    ["12","12"],
                    ["13","13"],
                    ["14","14"],
                    ["15","15"],
                    ["16","16"],
                    ["17","17"],
                    ["18","18"],
                    ["19","19"],
                    ["20","20"],
                    ["21","21"],
                    ["22","22"],
                    ["23","23"]
            ],
            minuteList:[
                    ["0","00"],
                    ["1","01"],
                    ["2","02"],
                    ["3","03"],
                    ["4","04"],
                    ["5","05"],
                    ["6","06"],
                    ["7","07"],
                    ["8","08"],
                    ["9","09"],
                    ["10","10"],
                    ["11","11"],
                    ["12","12"],
                    ["13","13"],
                    ["14","14"],
                    ["15","15"],
                    ["16","16"],
                    ["17","17"],
                    ["18","18"],
                    ["19","19"],
                    ["20","20"],
                    ["21","21"],
                    ["22","22"],
                    ["23","23"],
                    ["24","20"],
                    ["25","2"],
                    ["26","22"],  
                    ["27","27"],
                    ["28","28"],
                    ["29","29"],
                    ["30","30"],
                    ["31","31"],
                    ["32","32"],
                    ["33","33"],
                    ["34","34"],
                    ["35","35"],
                    ["36","36"],  
                    ["37","37"],
                    ["38","38"],
                    ["39","39"],
                    ["40","40"],
                    ["41","41"],
                    ["42","42"],
                    ["43","43"],
                    ["44","44"],
                    ["45","45"],
                    ["46","46"],  
                    ["47","47"],
                    ["48","48"],
                    ["49","49"],
                    ["50","50"],
                    ["51","51"],
                    ["52","52"],
                    ["53","53"],
                    ["54","50"],
                    ["55","51"],
                    ["56","52"],  
                    ["57","57"],
                    ["58","58"],
                    ["59","59"]

            ],
            saveVolunteerHours : {
                mission_id : "",
                date_volunteered: "",
                day_volunteered: "",
                notes: "",
                hours: "",
                minutes: "",
                documents: []
            }
        }
    },
    validations() {
        
                     
        return {
            timeEntryDefaultData : {      
                minutes: {
                    requiredIf: requiredIf(() => {
                        return this.timeEntryDefaultData.hours == ''|| this.timeEntryDefaultData.hours == '0'
                    }),
                },
                hours: {
                    requiredIf: requiredIf(() => {
                        return this.timeEntryDefaultData.minutes == ''|| this.timeEntryDefaultData.minutes == '0'
                    }),
                },
                workDay : {required},
                notes : {required},
                dateVolunteered : {required}
            }
        }
    },
    methods: {
        updateWorkday(value) {
            var selectedData = {
                'selectedVal' : '',
                'fieldId' : ''
            }
            selectedData['selectedVal']  = value.selectedVal
            selectedData['fieldId'] = 'workday';  
            this.timeEntryDefaultData.workDay = value.selectedId
            this.$emit("updateCall",selectedData)
        },
        updateHours(value) {
            var selectedData = {
                'selectedVal' : '',
                'fieldId' : ''
            }
            selectedData['selectedVal']  = value.selectedVal
            selectedData['fieldId'] = 'hours';  
            this.timeEntryDefaultData.hours = value.selectedId
            this.$emit("updateCall",selectedData)
        },
        updateMinutes(value) {
            var selectedData = {
                'selectedVal' : '',
                'fieldId' : ''
            }
            selectedData['selectedVal']  = value.selectedVal
            selectedData['fieldId'] = 'minutes'; 
            this.timeEntryDefaultData.minutes = value.selectedId
            this.$emit("updateCall",selectedData)
        },
        saveTimeHours() {
            var _this = this;
            this.submitted = true;
            this.$v.$touch();
            if (this.$v.$invalid) {
                return;
            }

            const formData = new FormData();
            let fileData = []
            let file = this.files;
            if(file) {
                file.filter(function (fileItem, fileIndex) {
                    fileData.push(fileItem.file);
                    formData.append('documents[]', fileItem.file);
                })
            }  
            let volunteeredDate = moment(String(this.timeEntryDefaultData.dateVolunteered)).format('DD-MM-YYYY');
            formData.append('mission_id',this.timeEntryDefaultData.missionId);
            formData.append('date_volunteered',volunteeredDate);
            formData.append('day_volunteered',this.timeEntryDefaultData.workDay);
            formData.append('notes',this.timeEntryDefaultData.notes);
            formData.append('hours',this.timeEntryDefaultData.hours);
            formData.append('minutes',this.timeEntryDefaultData.minutes);
         
            addVolunteerHours(formData).then( response => {
                if (response.error === true) { 
                    this.message = null;
                    this.showErrorDiv = true
                    this.classVariant = 'danger'
                    //set error msg
                    this.message = response.message
                } else {
                    this.message = null;
                    this.showErrorDiv = true
                    this.classVariant = 'success'
                    //set error msg
                    this.message = response.message
                    this.submitted = false;   
                }
               
            })
            
        },
        hideModal() {
            this.submitted = false;
            this.showErrorDiv = false
            this.$emit("resetModal");
            document.querySelector('html').classList.remove('modal-open');
        }
    },
    created() {
        this.langauageData = JSON.parse(store.state.languageLabel) 
        this.lang = (store.state.defaultLanguage).toLowerCase();
    }
};
</script>