<template>
  <div class="profile-page inner-pages">
    <header>
      <ThePrimaryHeader></ThePrimaryHeader>
    </header>
    <main>
      <b-container>
        <b-row class="profile-content">
          <b-col xl="3" lg="4" md="12" class="profile-left-col">
            <div class="profile-details">
              <i class="user-icon-wrap" :style="{backgroundImage: 'url('+userIcon+')'}"></i>
              <h4>Evan Donohue</h4>
              <b-list-group class="social-nav">
                <b-list-group-item>
                  <b-link title="facebook" class="facebook-link">
                    <img
                      src="../assets/images/facebook-ic-blue.svg"
                      class="normal-img"
                      alt="facebook img"
                    />
                    <img
                      src="../assets/images/facebook-ic.svg"
                      class="hover-img"
                      alt="facebook img"
                    />
                  </b-link>
                </b-list-group-item>
                <b-list-group-item>
                  <b-link title="twitter" class="twitter-link">
                    <img
                      src="../assets/images/twitter-ic-blue.svg"
                      class="normal-img"
                      alt="twitter img"
                    />
                    <img src="../assets/images/twitter-ic.svg" class="hover-img" alt="twitter img" />
                  </b-link>
                </b-list-group-item>
                <b-list-group-item>
                  <b-link title="linked in" class="linkedin-link">
                    <img
                      src="../assets/images/linkedin-ic-blue.svg"
                      class="normal-img"
                      alt="linkedin img"
                    />
                    <img
                      src="../assets/images/linkedin-ic.svg"
                      class="hover-img"
                      alt="linkedin img"
                    />
                  </b-link>
                </b-list-group-item>
              </b-list-group>
              <div class="link-wrap">
                <b-link title="change password" class="border-hover-link">Change Password</b-link>
              </div>
              <b-form-group>
                <label>Language</label>
                <AppCustomDropdown
                  :default_text="langDefault"
                  :optionList="langList"
                  @updateCall="updateLang"
                />
              </b-form-group>
              <b-form-group>
                <label>Timezone</label>
                <AppCustomDropdown
                  :default_text="timeDefault"
                  :optionList="timeList"
                  @updateCall="updateTime"
                />
              </b-form-group>
            </div>
          </b-col>
          <b-col xl="9" lg="8" md="12" class="profile-form-wrap">
            <b-form class="profile-form">
              <b-row class="row-form">
                <b-col cols="12">
                  <h2 class="title-with-border">
                    <span>Basic Information</span>
                  </h2>
                </b-col>
                <b-col md="6">
                  <b-form-group>
                    <label for>Name</label>
                    <b-form-input id type="text" placeholder="Enter your name"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col md="6">
                  <b-form-group>
                    <label for>Surname</label>
                    <b-form-input id type="text" placeholder="Enter your surname"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col md="6">
                  <b-form-group>
                    <label for>Employee ID</label>
                    <b-form-input id type="text" placeholder="Enter your employee id"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col md="6">
                  <b-form-group>
                    <label for>Manager</label>
                    <b-form-input id type="text" placeholder="Enter your manager details"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col md="6">
                  <b-form-group>
                    <label for>Title</label>
                    <b-form-input id type="text" placeholder="Enter your employee id"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col md="6">
                  <b-form-group>
                    <label for>Department</label>
                    <b-form-input id type="text" placeholder="Enter your manager details"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col md="12">
                  <b-form-group>
                    <label>My Profile</label>
                    <b-form-textarea
                      id
                      placeholder="Enter your comments..."
                      size="lg"
                      no-resize
                      rows="5"
                    ></b-form-textarea>
                  </b-form-group>
                </b-col>
                <b-col md="12">
                  <b-form-group>
                    <label>Why I Volunteer?</label>
                    <b-form-textarea
                      id
                      placeholder="Enter your comments..."
                      size="lg"
                      no-resize
                      rows="5"
                    ></b-form-textarea>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="row-form">
                <b-col cols="12">
                  <h2 class="title-with-border">
                    <span>Address Information</span>
                  </h2>
                </b-col>
                <b-col md="6">
                  <b-form-group>
                    <label>Country</label>
                    <AppCustomDropdown
                      :default_text="countryDefault"
                      :optionList="countryList"
                      @updateCall="updateCountry"
                    />
                  </b-form-group>
                </b-col>
                <b-col md="6">
                  <b-form-group>
                    <label>City</label>
                    <b-form-input id type="email" placeholder="Enter your city"></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="row-form">
                <b-col cols="12">
                  <h2 class="title-with-border">
                    <span>Professional Information</span>
                  </h2>
                </b-col>
                <b-col md="6">
                  <b-form-group>
                    <label>Availablity</label>
                    <AppCustomDropdown
                      :default_text="availabilityDefault"
                      :optionList="availabilityList"
                      @updateCall="updateAvailability"
                    />
                  </b-form-group>
                </b-col>
                <b-col md="6">
                  <b-form-group>
                    <label>LinkedIn</label>
                    <b-form-input id type="email" required placeholder="Enter linkedIn URL"></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="row-form">
                <b-col cols="12">
                  <h2 class="title-with-border">
                    <span>My Skills</span>
                  </h2>
                </b-col>
                <b-col cols="12">
                  <MultiSelect />
                </b-col>
              </b-row>
            </b-form>
          </b-col>
        </b-row>
      </b-container>
    </main>
    <footer>
      <TheSecondaryFooter></TheSecondaryFooter>
    </footer>
  </div>
</template>
<script>
import AppCustomDropdown from "../components/AppCustomDropdown";
import MultiSelect from "../components/MultiSelect";

export default {
  components: {
    ThePrimaryHeader : () => import("../components/Layouts/ThePrimaryHeader"),
    TheSecondaryFooter: () => import("../components/Layouts/TheSecondaryFooter"),
    AppCustomDropdown,
    MultiSelect
  },
  data() {
    return {
      langList: ["English: USA", "English: UK", "French"],
      langDefault: "English: USA",
      userIcon: require("@/assets/images/user-img-large.png"),
      timeList: ["GMT-4"],
      timeDefault: "GMT-4",
      countryList: [
        "India",
        "Japan",
        "China",
        "Africa",
        "Iran",
        "Shrilanka",
        "Bangladesh"
      ],
      countryDefault: "Select your country",
      availabilityList: ["part time", "full time", "free lancher"],
      availabilityDefault: "Select your availablity",
      file: "null"
    };
  },
  mounted() {},

  methods: {
    updateLang(value) {
      this.langDefault = value;
    },
    updateTime(value) {
      this.timeDefault = value;
    },
    updateCountry(value) {
      this.countryDefault = value;
    },
    updateAvailability(value) {
      this.availabilityDefault = value;
    }
  }
};
</script>