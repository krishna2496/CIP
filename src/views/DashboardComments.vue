<template>
  <div class="dashboard-comments inner-pages">
    <header>
      <ThePrimaryHeader></ThePrimaryHeader>
    </header>
    <main>
      <DashboardBreadcrumb />
      <div class="dashboard-tab-content">
        <b-container>
          <div class="heading-section">
            <h1>Comments</h1>
          </div>
          <div class="inner-content-wrap">
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
                    <img src="../assets/images/decline-ic.svg" alt="Decline" />
                  </i>
                  <p>
                    <span>25</span>Declined
                  </p>
                </div>
              </b-list-group-item>
            </b-list-group>
            <div class="dashboard-table">
              <div class="table-outer">
                <div class="table-inner">
                  <h3>Comment History</h3>
                  <b-table
                    :items="commentItems"
                    responsive
                    :fields="commentfields"
                    class="history-table"
                  >
                    <template slot="mission" slot-scope="data">
                      <b-link to="/home" class="table-link">{{ data.item.Mission }}</b-link>
                    </template>
                    <template slot="actions">
                      <b-button class="btn-action btn-expand">
                        <img src="../assets/images/expand-ic.svg" alt="Expand" />
                      </b-button>
                      <b-button class="btn-action">
                        <img src="../assets/images/gray-delete-ic.svg" alt="Delete" />
                      </b-button>
                    </template>
                  </b-table>
                </div>
                <div class="btn-row">
                  <b-button class="btn-bordersecondary ml-auto" title="Export">Export</b-button>
                </div>
              </div>
            </div>
          </div>
          <b-modal ref="my-modal" :modal-class="'table-expand-modal table-modal'">
            <template slot="modal-title">Help old people</template>
            <h4>
              Date:
              <span>4/28/2018</span>
            </h4>
            <h4>
              Status:
              <span>Published</span>
            </h4>
            <div class="mission-row">
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
            <template slot="modal-footer" slot-scope="{ cancel}">
              <b-button class="btn-bordersecondary" @click="cancel()">Cancel</b-button>
            </template>
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

  name: "dashboardcomments",

  data() {
    return {
      selectedMonth: false,
      commentfields: [
        {
          key: "mission",
          class: "mission-col",
          label: "Mission"
        },
        {
          key: "Date",
          class: "date-col"
        },
        {
          key: "Comment",
          class: "expand-col"
        },
        {
          key: "Status",
          class: "status-col"
        },
        {
          key: "actions",
          class: "action-col",
          label: "Actions"
        }
      ],
      commentItems: [
        {
          Mission: "Help old people",
          Date: "4/28/2018",
          Comment:
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum",
          Status: "Published"
        },
        {
          Mission: "Help young kids",
          Date: "4/9/2018",
          Comment:
            "Mission was great. we did so much Mission was great. we did so muchMission was great. we did so much Mission was great. we did so much Mission was great. we did so muchMission was great. we did so much Mission was great. we did so much Mission was great. we did so muchMission was great. we did so much Mission was great. we did so much Mission was great. we did so muchMission was great. we did so much Mission was great. we did so much Mission was great. we did so muchMission was great. we did so much Mission was great. we did so much Mission was great. we did so muchMission was great. we did so much",
          Status: "Pending"
        },
        {
          Mission: "Plant house",
          Date: "2/14/2018",
          Comment: "I hated it",
          Status: "Declined"
        }
      ]
    };
  },
  methods: {},
  created() {
    setTimeout(() => {
      var buttonExpand = document.querySelectorAll(".btn-expand");
      buttonExpand.forEach(function(event) {
        event.addEventListener("click", function() {
          var getcommentCell = this.parentNode.parentNode.childNodes[2];
          var getcommenthtml = getcommentCell.innerHTML;
          //   var ellipsestext = "...";
          var strlenght = getcommenthtml.length;
          var rowParent = this.parentNode.parentNode.parentNode;
          var rowSibling = rowParent.childNodes;
          for (var i = 0; i < rowSibling.length; i++) {
            var siblingChild = rowSibling[i].childNodes;
            for (var j = 0; j < siblingChild.length; j++) {
              siblingChild[j].classList.remove("remove-truncate");
            }
          }
          if (strlenght > 30) {
            getcommentCell.classList.toggle("remove-truncate");
          }
        });
      });
    });
  }
};
</script>