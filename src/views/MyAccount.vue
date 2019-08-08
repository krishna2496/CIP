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
                    :src="$store.state.imagePath+'/assets/images/facebook-ic-blue.svg'"
                    class="normal-img"
                    alt="facebook img"
                />
                <img
                    :src="$store.state.imagePath+'/assets/images/facebook-ic.svg'"
                    class="hover-img"
                    alt="facebook img"
                />
                </b-link>
            </b-list-group-item>
            <b-list-group-item>
              <b-link title="twitter" class="twitter-link">
                <img
                  :src="$store.state.imagePath+'/assets/images/twitter-ic-blue.svg'"
                  class="normal-img"
                  alt="twitter img"
                />
                <img :src="$store.state.imagePath+'/assets/images/twitter-ic.svg'" class="hover-img" alt="twitter img" />
              </b-link>
            </b-list-group-item>
            <b-list-group-item>
                <b-link title="linked in" class="linkedin-link">
                    <img
                        :src="$store.state.imagePath+'/assets/images/linkedin-ic-blue.svg'"
                        class="normal-img"
                        alt="linkedin img"
                    />
                    <img
                        :src="$store.state.imagePath+'/assets/images/linkedin-ic.svg'"
                        class="hover-img"
                        alt="linkedin img"
                    />
                </b-link>
            </b-list-group-item>
            </b-list-group>
            <div class="link-wrap">
                <b-link title="change password" class="border-hover-link">{{langauageData.label.change_password}}</b-link>
            </div>
            <b-form-group>
                <label>{{langauageData.label.language}}</label>
                <AppCustomDropdown
                  :default_text="langDefault"
                  :optionList="langList"
                  @updateCall="updateLang"
                />
            </b-form-group>
                <b-form-group>
                    <label>{{langauageData.label.timezone}}</label>
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
                            <span>{{langauageData.label.basic_information}}</span>
                        </h2>
                    </b-col>
                    <b-col md="6">
                    <b-form-group>
                        <label for>{{langauageData.label.name}}</label>
                        <b-form-input id type="text" :placeholder="langauageData.placeholder.name"></b-form-input>
                    </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.surname}}</label>
                            <b-form-input id type="text" :placeholder="langauageData.placeholder.surname"></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.employee_id}}</label>
                            <b-form-input id type="text" :placeholder="langauageData.placeholder.employee_id"></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.manager}}</label>
                            <b-form-input id type="text" :placeholder="langauageData.placeholder.manager"></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.title}}</label>
                            <b-form-input id type="text" :placeholder="langauageData.placeholder.title"></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.department}}</label>
                            <b-form-input id type="text" :placeholder="langauageData.placeholder.department"></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="12">
                        <b-form-group>
                            <label>{{langauageData.label.my_profile}}</label>
                            <b-form-textarea
                            id
                            :placeholder="langauageData.placeholder.comment"
                            size="lg"
                            no-resize
                            rows="5"
                            ></b-form-textarea>
                        </b-form-group>
                    </b-col>
                    <b-col md="12">
                    <b-form-group>
                        <label>{{langauageData.label.why_i_volunteer}}</label>
                        <b-form-textarea
                        id
                        :placeholder="langauageData.placeholder.why_i_volunteer"
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
                    <span>{{langauageData.label.address_information}}</span>
                </h2>
            </b-col>
            <b-col md="6">
                <b-form-group>
                    <label>{{langauageData.label.country}}</label>
                    <AppCustomDropdown
                    :default_text="countryDefault"
                    :optionList="countryList"
                    @updateCall="updateCountry"
                    />
                </b-form-group>
            </b-col>
            <b-col md="6">
                <b-form-group>
                    <label>{{langauageData.label.city}}</label>
                    <b-form-input id type="email" :placeholder="langauageData.placeholder.city"></b-form-input>
                </b-form-group>
            </b-col>
            </b-row>
            <b-row class="row-form">
                <b-col cols="12">
                    <h2 class="title-with-border">
                        <span>{{langauageData.label.professional_information}}</span>
                    </h2>
                </b-col>
                <b-col md="6">
                    <b-form-group>
                        <label>{{langauageData.label.availablity}}</label>
                        <AppCustomDropdown
                        :default_text="availabilityDefault"
                        :optionList="availabilityList"
                        @updateCall="updateAvailability"
                        />
                    </b-form-group>
                </b-col>
                <b-col md="6">
                    <b-form-group>
                        <label>{{langauageData.label.linked_in}}</label>
                        <b-form-input id  required 
                        :placeholder="langauageData.placeholder.linked_in"
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row class="row-form">
            <b-col cols="12">
              <h2 class="title-with-border">
                <span>{{langauageData.label.my_skills}}</span>
              </h2>
            </b-col>
            <b-col cols="12">
              <MultiSelect/>
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
import store from "../store";

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
file: "null",
langauageData : []
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
},
created() {
this.langauageData = JSON.parse(store.state.languageLabel);
}

};
</script>