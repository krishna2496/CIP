<template>
    <div>
        
        <b-modal ref="goalActionModal" :modal-class="'goal-modal table-modal'" hide-footer  @hidden ="hideModal">
            <template slot="modal-header" slot-scope="{ close }">
                <i class="close" @click="close()" v-b-tooltip.hover :title="langauageData.label.close"></i>
                    <h5 class="modal-title">{{langauageData.label.goal_entry_modal_title}}</h5>
            </template>
            <b-alert show :variant="classVariant" dismissible v-model="showErrorDiv">
                {{ message }}
            </b-alert>
            <div class="table-wrapper-outer">
                <div v-bind:class="{ 'content-loader-wrap': true, 'loader-active': isAjaxCall}">
                    <div class="content-loader"></div>
                </div>
                <form action class="form-wrap">
                    <b-form-group>
                        <b-row>
                            <b-col sm="12">
                                <b-form-group>
                                    <label for>{{langauageData.label.mission}}</label>
                                    <b-form-input id 
                                    type="text"
                                    v-model.trim="timeEntryDefaultData.missionName"
                                    class="disabled"           
                                ></b-form-input>
                                </b-form-group>
                            </b-col>
                            <b-col>
                            <b-form-group>
                            <b-row>
                            <b-col sm="12">
                                <b-form-group>
                                    <label for>{{langauageData.label.actions}}*</label>
                                    <b-form-input 
                                    v-model.trim="timeEntryDefaultData.action"
                                    :class="{ 'is-invalid': submitted && $v.timeEntryDefaultData.action.$error }"
                                    type="text" :placeholder="langauageData.placeholder.action"></b-form-input>
                                    <div 
                                        v-if="submitted && !$v.timeEntryDefaultData.dateVolunteered.required" 
                                        class="invalid-feedback">
                                        {{ langauageData.errors.action_required }}
                                    </div>
                                
                                    <div 
                                        v-if="submitted && !$v.timeEntryDefaultData.dateVolunteered.minValue" 
                                        class="invalid-feedback">
                                        {{ langauageData.errors.minimum_action }}
                                    </div>
                                </b-form-group>
                            </b-col>
                            </b-row>
                        </b-form-group>

                            </b-col>
                        </b-row>
                    </b-form-group>
                    <b-form-group>
                        <b-row>
                            <b-col sm="6" class="date-col">
                                <b-form-group>
                                    <label for>{{langauageData.label.date_volunteered}}*</label>
                                    <date-picker
                                        v-model="timeEntryDefaultData.dateVolunteered"
                                        :notAfter="timeEntryDefaultData.disabledFutureDates"
                                        :notBefore="timeEntryDefaultData.disabledPastDates"
                                        :disabledDays="disableDates"
                                        @change="dateChange()"
                                        :class="{ 'is-invalid': submitted && $v.timeEntryDefaultData.dateVolunteered.$error }"
                                        :lang="lang"
                                    >
                                        
                                    </date-picker>
                                <div v-if="submitted && !$v.timeEntryDefaultData.dateVolunteered.required" class="invalid-feedback">
                                        {{ langauageData.errors.date_volunteer_is_required }}</div>
                                </b-form-group>
                            </b-col>
                        <b-col sm="6" class="date-col">
                            <b-form-group>
                                <label for>{{langauageData.label.day_volunteered}}*</label>
                                        <AppCustomDropdown
                                            v-model="timeEntryDefaultData.workDay"
                                            :optionList="workDayList"
                                            :errorClass="submitted && $v.timeEntryDefaultData.workDay.$error" 
                                            :defaultText="defaultWorkday"
                                            @updateCall="updateWorkday"
                                            translationEnable= "true"
                                        />
                                    <div v-if="submitted && !$v.timeEntryDefaultData.workDay.required" class="invalid-feedback">
                                        {{ langauageData.errors.work_day }}</div>
                            </b-form-group>
                        
                        </b-col>
                        </b-row>
                    </b-form-group>
                    <b-form-group>
                        <b-row>
                            <b-col sm="12">
                                <b-form-group>
                                    <label for>{{langauageData.label.notes}}*</label>
                                    <b-form-textarea id
                                    v-model="timeEntryDefaultData.notes"
                                    :class="{ 'is-invalid': submitted && $v.timeEntryDefaultData.notes.$error }"
                                    :placeholder="langauageData.placeholder.notes"
                                    size="lg" rows="5"></b-form-textarea>
                                    <div v-if="submitted && !$v.timeEntryDefaultData.notes.required" class="invalid-feedback">
                                        {{ langauageData.errors.notes }}</div>
                                </b-form-group>
                                
                            </b-col>
                        </b-row>
                    </b-form-group>
                    <b-form-group v-if="isFileUploadDisplay">
                        <b-row>
                            
                            <b-col sm="6" class="date-col">
                            <span class="error-message"  v-if="fileError">{{fileError}}</span>
                            <label for>{{langauageData.label.file_upload}}</label>
                            <div class="file-upload-wrap">
                                <div class="btn-wrapper"
                                v-bind:class="{'has-error' : fileError != '' ? true : false}">
                                    <file-upload
                                        class="btn"
                                        accept="image/png,image/jpeg,application/doc,
                                        application/docx,application/xls,application/xlsx,application/csv,application/pdf"
                                        :multiple="true"
                                        :drop="true"
                                        :drop-directory="true"
                                        @input="inputUpdate"
                                        :size="1024 * 1024 *10"
                                        v-model="fileArray"
                                        ref="upload">
                                    {{langauageData.label.browse}}
                                    </file-upload>
                                    <span>{{langauageData.label.drop_files}}</span>
                                </div>
								<div class="uploaded-file-wrap">
									<div class="uploaded-file-details" v-for="(file, index) in timeEntryDefaultData.documents">
										
										<a class="filename" :href="file.document_path" target="_blank">{{file.document_name}}</a>
										<b-button 
										class="remove-item" 
										@click.prevent="deleteFile(file.timesheet_id,file.timesheet_document_id)" 
										:title="langauageData.label.delete"
										>
											<img :src="$store.state.imagePath+'/assets/images/delete-ic.svg'" alt="delete-ic"/>
										</b-button>
									
									</div>
									<div class="uploaded-file-details" v-for="(file, index) in fileArray" :key="file.id">
										<p class="filename">{{file.name}}</p>
										<b-button
										class="remove-item" 
										@click.prevent="$refs.upload.remove(file)" 
										:title="langauageData.label.delete"
										>
											<img :src="$store.state.imagePath+'/assets/images/delete-ic.svg'" alt="delete-ic"/>
										</b-button>
									</div>
								</div>
                            </div>
                            </b-col>
                        </b-row>
                    </b-form-group>
                </form>
                <div class="btn-wrap">
                    <b-button
                        class="btn-borderprimary"
                        @click="$refs.goalActionModal.hide()"
                        
                    >{{langauageData.label.cancel}}</b-button>
                    <b-button 
                        class="btn-bordersecondary"
                        v-bind:class="{
                            disabled:isAjaxCall
                        }" 
                        @click="saveAction()" 
                        >{{langauageData.label.submit}}
                    </b-button>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import store from '../store';
import moment from 'moment'
import DatePicker from "vue2-datepicker";
import AppCustomDropdown from "../components/CustomFieldDropdown";
import { required,maxLength, email,sameAs, minLength, between,helpers,numeric,requiredIf,minValue} from 'vuelidate/lib/validators';
import FileUpload from 'vue-upload-component';
import {addVolunteerEntry,removeDocument} from '../services/service';
import constants from '../constant';

export default {
    name: "VolunteeringAction",
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
            fileArray : this.files,
            showErrorDiv : false,
            isAjaxCall : false,
            message : null,
            classVariant :"success",
            fileError : "",
            hourList:[
                    ["00","00"],
                    ["01","01"],
                    ["02","02"],
                    ["03","03"],
                    ["04","04"],
                    ["05","05"],
                    ["06","06"],
                    ["07","07"],
                    ["08","08"],
                    ["09","09"],
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
                    ["00","00"],
                    ["01","01"],
                    ["02","02"],
                    ["03","03"],
                    ["04","04"],
                    ["05","05"],
                    ["06","06"],
                    ["07","07"],
                    ["08","08"],
                    ["09","09"],
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
                action : "",
                documents: []
            }
        }
    },
    validations() {
        return {
            timeEntryDefaultData : {      
                action : {required,numeric,minValue:minValue(1)},
                workDay : {required},
                notes : {required},
                dateVolunteered : {required}
            }
        }
    },
    methods: {
        dateChange() {
            this.$emit('changeDocument',this.timeEntryDefaultData.dateVolunteered)
        },
        inputUpdate(files) {
            var _this = this
            let allowedFileTypes = ['doc','xls','xlsx','csv','pdf','png','jpg','jpeg']
            files.filter(function(data,index){
                if(data.size > 4000000) {
                    _this.fileError = _this.langauageData.errors.file_max_size
                   files.splice(index,1)
                } else {
                    let fileName = data.name.split('.');
                    _this.fileError = '';
                    if(!allowedFileTypes.includes(fileName[fileName.length-1])) {
                        _this.fileError = _this.langauageData.errors.invalid_file_type
                        files.splice(index,1)
                    }
                }
            });
        },
        
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
        saveAction() {
            var _this = this;
            this.submitted = true;
            this.$v.$touch();
         
            if (this.$v.$invalid) {
                return;
            }
            this.isAjaxCall = true;
            const formData = new FormData();
            let fileData = []
            let file = this.fileArray;
            if(file) {
                file.filter(function (fileItem, fileIndex) {
                    fileData.push(fileItem.file);
                    formData.append('documents[]', fileItem.file);
                })
            }  
            let volunteeredDate = moment(String(this.timeEntryDefaultData.dateVolunteered)).format('YYYY-MM-DD');
            let hours = this.timeEntryDefaultData.hours == '' ? 0 : this.timeEntryDefaultData.hours
            let minutes = this.timeEntryDefaultData.minutes == '' ? 0 : this.timeEntryDefaultData.minutes
            formData.append('mission_id',this.timeEntryDefaultData.missionId);
            formData.append('date_volunteered',volunteeredDate);
            formData.append('day_volunteered',this.timeEntryDefaultData.workDay);
            formData.append('notes',this.timeEntryDefaultData.notes);
            formData.append('action',this.timeEntryDefaultData.action);
         
            addVolunteerEntry(formData).then( response => {
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
                    this.$emit("getTimeSheetData");
                    setTimeout(function() {
                        _this.$refs.goalActionModal.hide();
                        _this.hideModal();
                    },700) 
                   
                }
                 this.isAjaxCall = false;
            })
            
        },
        deleteFile(timeSheetId,documentId) {
            var _this = this
            let deletFile = {
                'timesheet_id' : timeSheetId,
                'document_id' : documentId
            }
             
            removeDocument(deletFile).then(response => {
                if(response) {

                    this.message = null;
                    this.showErrorDiv = true
                    this.classVariant = 'success'
                    this.message = response
                    this.timeEntryDefaultData.documents.filter(function (document, index) {
                        if(document.timesheet_document_id == documentId && document.timesheet_id == timeSheetId) {
                            _this.timeEntryDefaultData.documents.splice(index, 1);
                        }
                    });
                } else {
                    this.message = null;
                    this.showErrorDiv = true
                    this.classVariant = 'danger'
                    this.message = response
                }
            })
        },
        hideModal() {
            this.submitted = false;
            this.showErrorDiv = false
            this.fileError = ''
            this.fileArray = [];
            this.$emit("resetModal");
            document.querySelector('html').classList.remove('modal-open');
        }
        
    },
    created() {
        this.langauageData = JSON.parse(store.state.languageLabel) 
        this.isFileUploadDisplay = this.settingEnabled(constants.TIMESHEET_DOCUMENT_UPLOAD)
        this.lang = (store.state.defaultLanguage).toLowerCase();
    }
};
</script>