<template>
  <div class="dashboard-stories inner-pages">
    <header>
      <ThePrimaryHeader></ThePrimaryHeader>
    </header>
    <main>
      <DashboardBreadcrumb />
      <div class="dashboard-tab-content">
        <b-container>
          <div class="heading-section">
            <h1>My Stories</h1>
            <b-button
              title="Publish a new story"
              type="button"
              class="btn-bordersecondary"
            >Publish a new story</b-button>
          </div>
          <div class="dashboard-story-content">
            <p>
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
              labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
              laboris nisi ut aliquip.
            </p>
          </div>
          <b-list-group class="status-bar inner-statusbar">
            <b-list-group-item>
              <div class="list-item">
                <i>
                  <img src="../assets/images/published-ic.svg" alt />
                </i>
                <p>
                  <span>1</span>Published
                </p>
              </div>
            </b-list-group-item>
            <b-list-group-item>
              <div class="list-item">
                <i>
                  <img src="../assets/images/pending-ic.svg" alt="Pending" />
                </i>
                <p>
                  <span>2</span>Pending
                </p>
              </div>
            </b-list-group-item>
            <b-list-group-item>
              <div class="list-item">
                <i>
                  <img src="../assets/images/decline.svg" alt="Decline" />
                </i>
                <p>
                  <span>25</span>Declined
                </p>
              </div>
            </b-list-group-item>
            <b-list-group-item>
              <div class="list-item">
                <i>
                  <img src="../assets/images/draft.svg" alt="Draft" />
                </i>
                <p>
                  <span>25</span>Draft
                </p>
              </div>
            </b-list-group-item>
          </b-list-group>
          <div class="story-card-wrap">
            <h2>Story History</h2>
            <b-row class="story-card-row">
              <b-col class="story-card-block" md="6" lg="4">
                <div class="story-img" :style="{backgroundImage: 'url('+grpImages[1]+')'}"></div>
                <div class="story-card">
                  <h4 class="story-card-title">Help Old People</h4>
                  <div class="story-card-body">
                    <span>20/07/2019</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore...</p>
                  </div>
                  <div class="story-card-footer">
                    <span class="status-label">Published</span>
                    <div class="action-block">
                      <b-button class="btn-action" v-b-tooltip.hover title="Delete">
                        <img src="../assets/images/gray-delete-ic.svg" alt="Delete" />
                      </b-button>
                      <b-link class="btn-action" v-b-tooltip.hover title="Redirect" to="/stories">
                        <img src="../assets/images/external-link.svg" alt="Redirect" />
                      </b-link>
                    </div>
                  </div>
                </div>
              </b-col>
              <b-col class="story-card-block" md="6" lg="4">
                <div class="story-img" :style="{backgroundImage: 'url('+grpImages[2]+')'}"></div>
                <div class="story-card">
                  <h4 class="story-card-title">Help Young Kids</h4>
                  <div class="story-card-body">
                    <span>15/06/2019</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore...</p>
                  </div>
                  <div class="story-card-footer">
                    <span class="status-label">Pending</span>
                    <div class="action-block">
                      <b-button
                        class="btn-action"
                        @click="$refs.storyEditModal.show()"
                        v-b-tooltip.hover
                        title="Edit"
                      >
                        <img src="../assets/images/edit-ic.svg" alt="Edit" />
                      </b-button>
                      <b-button class="btn-action" v-b-tooltip.hover title="Delete">
                        <img src="../assets/images/gray-delete-ic.svg" alt="Delete" />
                      </b-button>
                      <b-link class="btn-action" v-b-tooltip.hover title="Redirect" to="/stories">
                        <img src="../assets/images/external-link.svg" alt="Redirect" />
                      </b-link>
                    </div>
                  </div>
                </div>
              </b-col>
              <b-col class="story-card-block" md="6" lg="4">
                <div class="story-img" :style="{backgroundImage: 'url('+grpImages[3]+')'}"></div>
                <div class="story-card">
                  <h4 class="story-card-title">Plant House</h4>
                  <div class="story-card-body">
                    <span>25/03/2019</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore...</p>
                  </div>
                  <div class="story-card-footer">
                    <span class="status-label">Declined</span>
                    <div class="action-block">
                      <b-button class="btn-action" v-b-tooltip.hover title="Delete">
                        <img src="../assets/images/gray-delete-ic.svg" alt="Delete" />
                      </b-button>
                      <b-button class="btn-action" v-b-tooltip.hover title="Copy">
                        <img src="../assets/images/copy.svg" alt="Copy" />
                      </b-button>
                    </div>
                  </div>
                </div>
              </b-col>
              <b-col class="story-card-block" md="6" lg="4">
                <div class="story-img" :style="{backgroundImage: 'url('+grpImages[4]+')'}"></div>
                <div class="story-card">
                  <h4 class="story-card-title">Plant House</h4>
                  <div class="story-card-body">
                    <span>25/03/2019</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore...</p>
                  </div>
                  <div class="story-card-footer">
                    <span class="status-label">Draft</span>
                    <div class="action-block">
                      <b-button
                        class="btn-action"
                        @click="$refs.storyEditModal.show()"
                        v-b-tooltip.hover
                        title="Edit"
                      >
                        <img src="../assets/images/edit-ic.svg" alt="Edit" />
                      </b-button>
                      <b-button class="btn-action" v-b-tooltip.hover title="Delete">
                        <img src="../assets/images/gray-delete-ic.svg" alt="Delete" />
                      </b-button>
                    </div>
                  </div>
                </div>
              </b-col>
            </b-row>
            <div class="btn-row">
              <b-button class="btn-bordersecondary ml-auto" title="Export">Export</b-button>
            </div>
          </div>
          <b-modal ref="storyEditModal" :modal-class="'table-edit-modal table-modal'" hide-footer>
            <template slot="modal-title">
              Edit Story &nbsp;&nbsp;
              <b-badge class="status-label">Published</b-badge>
            </template>
            <form action class="form-wrap">
              <b-form-group>
                <label for>Mission Title</label>
                <b-form-input id type="text" placeholder="Help old people"></b-form-input>
              </b-form-group>
              <b-form-group>
                <label for>Date</label>
                <b-form-input id type="date" placeholder="4/28/2018"></b-form-input>
              </b-form-group>
              <b-form-group>
                <label for>Story Description</label>
                <b-form-textarea
                  id
                  placeholder="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip."
                  size="lg"
                  no-resize
                  rows="5"
                ></b-form-textarea>
              </b-form-group>
            </form>
            <div class="btn-wrap">
              <b-button
                class="btn-borderprimary"
                @click="$refs.storyEditModal.hide()"
                title="Cancel"
              >Cancel</b-button>
              <b-button class="btn-bordersecondary" @click="save()" title="Save">Save</b-button>
            </div>
          </b-modal>
        </b-container>
      </div>
    </main>
    <footer>
      <TheSecondaryFooter></TheSecondaryFooter>
    </footer>
  </div>
</template>

<script>
import ThePrimaryHeader from "../components/Layouts/ThePrimaryHeader";
import TheSecondaryFooter from "../components/Layouts/TheSecondaryFooter";
import DashboardBreadcrumb from "../components/DashboardBreadcrumb";
export default {
  components: {
    ThePrimaryHeader,
    TheSecondaryFooter,
    DashboardBreadcrumb
  },

  name: "dashboardstories",

  data() {
    return {
      grpImages: [
        require("@/assets/images/storie-img01.png"),
        require("@/assets/images/storie-img02.png"),
        require("@/assets/images/storie-img03.png"),
        require("@/assets/images/storie-img04.png"),
        require("@/assets/images/storie-img05.png"),
        require("@/assets/images/storie-img06.png"),
        require("@/assets/images/storie-img01.png"),
        require("@/assets/images/storie-img02.png"),
        require("@/assets/images/storie-img03.png")
      ]
    };
  },
  methods: {}
};
</script>