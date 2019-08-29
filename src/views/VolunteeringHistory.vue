<template>
  <div class="dashboard-history inner-pages">
    <header>
      <TopHeader></TopHeader>
    </header>
    <main>
      <DashboardBreadcrumb />
      <div class="dashboard-tab-content">
        <b-container>
          <div class="heading-section">
            <h1>Volunteering History</h1>
          </div>
          <div class="inner-content-wrap">
            <b-row class="chart-block">
              <b-col lg="6" class="chart-col">
                <div class="inner-chart-col">
                  <div class="chart-title">
                    <h5>Hours per theme</h5>
                    <CustomDropdown
                      :optionList="themeYearList"
                      @updateCall="updateThemeYear"
                      :default_text="ThemeYearText"
                    />
                  </div>
                  <div class="line-chart">
                    <canvas ref="themeChartRefs"></canvas>
                  </div>
                </div>
              </b-col>
              <b-col lg="6" class="chart-col">
                <div class="inner-chart-col">
                  <div class="chart-title">
                    <h5>Hours per skill</h5>
                    <CustomDropdown
                      :optionList="skillYearList"
                      @updateCall="updateSkillYear"
                      :default_text="skillYearText"
                    />
                  </div>
                  <div class="line-chart">
                    <canvas ref="skillChartRefs"></canvas>
                  </div>
                </div>
              </b-col>
            </b-row>
            <b-row class="dashboard-table">
              <b-col lg="6" class="table-col">
                <div class="table-outer">
                  <div class="table-inner">
                    <h3>Volunteering Hours</h3>
                    <b-table
                      :items="hoursItems"
                      responsive
                      :fields="hoursFields"
                      class="volunteery-table"
                    ></b-table>
                  </div>
                  <div class="btn-row">
                    <b-button class="btn-bordersecondary ml-auto" title="Export">Export</b-button>
                  </div>
                </div>
              </b-col>
              <b-col lg="6" class="table-col">
                <div class="table-outer">
                  <div class="table-inner">
                    <h3>Volunteering Goals</h3>
                    <b-table
                      :items="goalsItems"
                      responsive
                      :fields="goalsFields"
                      class="volunteery-table"
                    ></b-table>
                  </div>
                  <div class="btn-row">
                    <b-button class="btn-bordersecondary ml-auto" title="Export">Export</b-button>
                  </div>
                </div>
              </b-col>
            </b-row>
            <!-- <div class="no-history-data">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
				<div class="btn-row">
					<b-button title="Start Volunteering" class="btn-borderprimary">Start Volunteering</b-button>
				</div>
            </div>-->
          </div>
        </b-container>
      </div>
    </main>
    <footer>
      <PrimaryFooter></PrimaryFooter>
    </footer>
  </div>
</template>

<script>
import TopHeader from "../components/Layouts/ThePrimaryHeader";
import PrimaryFooter from "../components/Layouts/TheSecondaryFooter";
import CustomDropdown from "../components/AppCustomDropdown";
import DashboardBreadcrumb from "../components/DashboardBreadcrumb";

import Chart from "chart.js";
export default {
  components: {
    TopHeader,
    PrimaryFooter,
    CustomDropdown,
    Chart,
    DashboardBreadcrumb
  },

  name: "dashboardhistory",

  data() {
    return {
      hoursFields: [
        {
          key: "Mission",
          class: "mission-col"
        },
        {
          key: "Time",
          class: "time-col"
        },
        {
          key: "Hours",
          class: "hours-col"
        },
        {
          key: "Organisation",
          class: "organisation-col"
        }
      ],
      hoursItems: [
        {
          Mission: "Help old people",
          Time: "1h30",
          Hours: 5.5,
          Organisation: "Red Cross"
        },
        {
          Mission: "Help young kids",
          Time: "0h20",
          Hours: 0.33,
          Organisation: "Red Cross"
        },
        {
          Mission: "Plant house",
          Time: "2h50",
          Hours: 2.83,
          Organisation: "Green House"
        },
        {
          Mission: "The place",
          Time: "0h15",
          Hours: 0.25,
          Organisation: "Blue Cross"
        }
      ],
      goalsFields: [
        {
          key: "Mission",
          class: "mission-col"
        },
        {
          key: "Goal",
          class: "goal-col"
        },
        {
          key: "Result",
          class: "result-col"
        },
        {
          key: "Result%",
          class: "result-col"
        },
        {
          key: "Organisation",
          class: "organisation-col"
        }
      ],
      goalsItems: [
        {
          Mission: "Plant trees",
          Goal: "Plant 1000 tree",
          Result: 900,
          "Result%": "90%",
          Organisation: "Red Cross"
        },
        {
          Mission: "Feed Kids",
          Goal: "Provide 5000 meals",
          Result: 4400,
          "Result%": "88%",
          Organisation: "Red Cross"
        },
        {
          Mission: "Feed the homeless",
          Goal: "Provide 2500 meals",
          Result: 400,
          "Result%": "20%",
          Organisation: "Green House"
        }
      ],
      ThemeYearText: "Year",
      themeYearList: [
        "1996",
        "1997",
        "1998",
        "1999",
        "2000",
        "2001",
        "2002",
        "2003",
        "2004",
        "2005",
        "2006",
        "2007",
        "2008",
        "2009",
        "2010",
        "2011",
        "2012",
        "2013",
        "2014",
        "2015",
        "2016",
        "2017",
        "2018",
        "2019"
      ],
      skillYearText: "Year",
      skillYearList: [
        "1996",
        "1997",
        "1998",
        "1999",
        "2000",
        "2001",
        "2002",
        "2003",
        "2004",
        "2005",
        "2006",
        "2007",
        "2008",
        "2009",
        "2010",
        "2011",
        "2012",
        "2013",
        "2014",
        "2015",
        "2016",
        "2017",
        "2018",
        "2019"
      ]
    };
  },
  mounted() {
    var themeChartRefs = this.$refs.themeChartRefs;
    var themeContent = themeChartRefs.getContext("2d");
    var themeChart = new Chart(themeContent, {
      type: "horizontalBar",
      data: {
        labels: ["Belgium", "Netheriands", "Spain", "France", "Germany"],
        datasets: [
          {
            data: [330000, 210000, 130000, 70000, 40000],
            backgroundColor: "#dc3545",
            borderColor: "#dc3545",
            borderWidth: 1
          }
        ]
      },
      options: {
        legend: {
          display: false
        },
        scales: {
          yAxes: [
            {
              ticks: {
                beginAtZero: true
              }
            }
          ],
          xAxes: [
            {
              ticks: {
                fontColor: "#414141",
                fontSize: 14,
                max: 350000,
                min: 0,
                stepSize: 50000
              }
            }
          ]
        }
      }
    });
    var skillChartRefs = this.$refs.skillChartRefs;
    var skillContent = skillChartRefs.getContext("2d");
    var skillChart = new Chart(skillContent, {
      type: "horizontalBar",
      data: {
        labels: ["Belgium", "Netheriands", "Spain", "France", "Germany"],
        datasets: [
          {
            data: [330000, 210000, 130000, 70000, 40000],
            backgroundColor: "#dc3545",
            borderColor: "#dc3545",
            borderWidth: 1
          }
        ]
      },
      options: {
        legend: {
          display: false
        },
        scales: {
          yAxes: [
            {
              display: false,
              ticks: {
                beginAtZero: true
              }
            }
          ],
          xAxes: [
            {
              ticks: {
                fontColor: "#414141",
                fontSize: 14,
                max: 350000,
                min: 0,
                stepSize: 50000
              }
            }
          ]
        }
      }
    });
  },
  methods: {
    updateThemeYear(value) {
      this.ThemeYearText = value;
    },
    updateSkillYear(value) {
      this.skillYearText = value;
    }
  }
};
</script>