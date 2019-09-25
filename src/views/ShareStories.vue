<template>
  <div class="share-stories-page inner-pages">
    <header>
      <TopHeader></TopHeader>
    </header>
    <main>
      <b-container>
        <h1>Share your story</h1>
        <b-row class="story-form-wrap">
          <b-col xl="8" lg="7" class="left-col">
            <div class="story-form">
              <b-form>
                <b-form-group>
                  <label for>My Story Title</label>
                  <b-form-input id type="text" placeholder="Enter story title"></b-form-input>
                </b-form-group>
                <b-row>
                  <b-col md="6">
                    <b-form-group>
                      <label for>Select Mission</label>
                      <CustomDropdown
                        :optionList="missionTitle"
                        @updateCall="updateMissionTitle"
                        :default_text="missionTitleText"
                      />
                    </b-form-group>
                  </b-col>
                  <b-col md="6">
                    <b-form-group>
                      <label for>Date</label>
                      <date-picker
                        v-model="time1"
                        valuetype="format"
                        :first-day-of-week="1"
                        :lang="lang"
                      ></date-picker>
                      <!-- <b-form-input id type="text" placeholder="Select Date"></b-form-input> -->
                    </b-form-group>
                  </b-col>
                </b-row>
                <b-form-group>
                  <label for>My Story</label>
                  <b-form-textarea
                    id
                    placeholder="Enter your stories"
                    size="lg"
                    rows="25"
                    class="text-editor"
                  ></b-form-textarea>
                </b-form-group>
              </b-form>
            </div>
            <div class="btn-row">
              <b-button class="btn-borderprimary" title="Cancel">Cancel</b-button>
            </div>
          </b-col>
          <b-col xl="4" lg="5" class="right-col">
            <div class="story-form">
              <b-form-group>
                <label for>Enter Video URL</label>
                <b-form-textarea id placeholder="Enter your stories" size="lg" rows="5"></b-form-textarea>
              </b-form-group>
              <b-form-group>
                <label for>Upload your Photos</label>
                <file-upload
                  class="btn"
                  post-action="/upload/post"
                  extensions="gif,jpg,jpeg,png,webp"
                  accept="image/png, image/gif, image/jpeg, image/webp"
                  :multiple="true"
                  :drop="true"
                  :drop-directory="true"
                  :size="1024 * 1024 * 10"
                  v-model="files"
                  @input-filter="inputFilter"
                  ref="upload"
                >Drag and Drop Pictures</file-upload>
              </b-form-group>
              <div class="uploaded-block">
                <div class="uploaded-file-details" v-for="(file, index) in files" :key="index">
                  <span v-if="file.thumb" class="image-thumb">
                    <img :src="file.thumb" width="40" height="auto" />
                    <b-button type="button" @click.prevent="remove(file)" class="remove-btn">
                      <img src="../assets/images/cross-ic-white.svg" alt />
                    </b-button>
                  </span>
                  <span v-else>No Image</span>
                </div>
              </div>
            </div>
            <div class="btn-row">
              <b-button class="btn-borderprimary" title="Preview">Preview</b-button>
              <b-button class="btn-bordersecondary" title="Save">Save</b-button>
              <b-button class="btn-bordersecondary btn-submit" title="Submit">Submit</b-button>
            </div>
          </b-col>
        </b-row>
      </b-container>
    </main>
    <footer>
      <PrimaryFooter></PrimaryFooter>
    </footer>
  </div>
</template>
<script>
import PrimaryFooter from "../components/Footer/PrimaryFooter";
import TopHeader from "../components/Header/TopHeader";
import CustomDropdown from "../components/CustomDropdown";
import FileUpload from "vue-upload-component";
import DatePicker from "vue2-datepicker";

export default {
  components: {
    PrimaryFooter,
    TopHeader,
    CustomDropdown,
    FileUpload,
    DatePicker
  },
  data() {
    return {
      missionTitleText: "Mission title",
      missionTitle: [
        "Mission title1",
        "Mission title2",
        "Mission title3",
        "Mission title4"
      ],
      files: [],
      time1: "",
      lang: {
        days: [" Sun ", " Mon ", " Tue ", " Wed ", " You ", " Fri ", " Sat "],
        months: [
          "Jan",
          "Feb",
          "Mar",
          "Apr",
          "May",
          "Jun",
          "Jul",
          "Aug",
          "Sep",
          "Oct",
          "Nov",
          "Dec"
        ],
        pickers: [
          "next 7 days",
          "next 30 days",
          "previous 7 days",
          "previous 30 days"
        ],
        placeholder: {
          date: "Select date",
          dateRange: "Select Date Range"
        }
      }
    };
  },
  mounted() {},
  computed: {},
  methods: {
    updateMissionTitle(value) {
      this.missionTitleText = value;
    },
    inputFilter(newFile, prevent) {
      if (newFile) {
        if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
          return prevent();
        }
        // Filter php html js file
        if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
          return prevent();
        }
      }
      if (newFile) {
        // Create a blob field
        newFile.blob = "";
        let URL = window.URL || window.webkitURL;
        if (URL && URL.createObjectURL) {
          newFile.blob = URL.createObjectURL(newFile.file);
        }
        // Thumbnails
        newFile.thumb = "";
        if (newFile.blob && newFile.type.substr(0, 6) === "image/") {
          newFile.thumb = newFile.blob;
        }
      }
    },
    remove(file) {
      this.$refs.upload.remove(file);
    }
  },
  created() {},
  updated() {}
};
</script>


