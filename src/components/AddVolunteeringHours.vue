<template>
    <div>
        <b-modal ref="timeHoursModal" :modal-class="'time-hours-modal table-modal'" hide-footer>
        <!-- {{timeEntryDefaultData}} -->
            <template slot="modal-header" slot-scope="{ close }">
                <i class="close" @click="close()" v-b-tooltip.hover :title="langauageData.label.close"></i>
                    <h5 class="modal-title">{{langauageData.label.hour_entry_modal_title}}</h5>
                </template>
            <form action class="form-wrap">
                <b-form-group>
                    <b-row>
                        <b-col sm="6">
                            <b-form-group>
                                <label for>{{langauageData.label.hours}}</label>
                                <b-form-input id 
                                type="text"
                                v-model="volunteeringHours.hours"
                                :placeholder="langauageData.placeholder.spent_hours"></b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col sm="6">
                            <b-form-group>
                                <label for>{{langauageData.label.minutes}}</label>
                                <b-form-input id type="text"
                                volunteeringHours.hours
                                :placeholder="langauageData.placeholder.spent_minutes"></b-form-input>
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
                                    v-model="volunteeringHours.date_volunteered"
                                    valuetype="format"
                                    :first-day-of-week="1"
                                    :lang="lang"
                                ></date-picker>
                            </b-form-group>
                        </b-col>
                    <b-col sm="6" class="date-col">
                        <b-form-group>
                            <label for>{{langauageData.label.workday}}</label>
                            <b-row>
                                <b-col sm="6">
                                    <AppCustomDropdown
                                        v-model="volunteeringHours.day_volunteered"
                                        :optionList="workDayList"
                                        :defaultText="defaultWorkday"
                                        @updateCall="updateWorkday"
                                        translationEnable= "false"
                                    />
                                </b-col>
                            </b-row>
                        </b-form-group>
                    </b-col>
                    </b-row>
                </b-form-group>
                <b-form-group>
                    <b-row>
                        <b-col sm="12">
                            <b-form-group>
                                <label for>{{langauageData.label.notes}}:</label>
                                <b-form-textarea id
                                v-model="volunteeringHours.notes"
                                :placeholder="langauageData.placeholder.notes"
                                size="lg" no-resize rows="5"></b-form-textarea>
                            </b-form-group>
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
                                    post-action="/upload/post"
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
                                <span class="file-size">{{file.size}}</span>
                                <div class="status">
                                    <span class="success">File uploaded successfully</span>
                                </div>
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
import { required,maxLength, email,sameAs, minLength, between,helpers} from 'vuelidate/lib/validators';
import FileUpload from 'vue-upload-component';

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
        timeEntryDefaultData : Object
    },
    data: function() {
        return {
            lang : 'en',
            langauageData : [],
            workDayList: [
                ["workday","workday"],
                ["weekend","weekend"],
                ["holiday","holiday"],
            ],
            volunteeringHours :
                {
                    missionId : '',
                    hours : '',
                    minutes : '',
                    dateVolunteered : '',
                    workDay : '',
                    notes : '',
                    day: ''
                }
            ,
        }
    },
    methods: {
        updateWorkday(value) {
            this.defaultWorkday = value.selectedVal;
        },
        saveTimeHours(){

        }
    },
    created() {
        this.langauageData = JSON.parse(store.state.languageLabel);
    }
};
</script>