<template>
  <div class="user-dashboard inner-pages">
    <header>
      <TopHeader></TopHeader>
    </header>
    <main>
      <DashboardBreadcrumb />
      <div class="dashboard-tab-content">
        <b-container>
          <div class="heading-section">
            <h1>Dashboard</h1>
            <div class="date-filter">
              <CustomDropdown
                :optionList="yearList"
                @updateCall="updateYear"
                :default_text="yearText"
              />
              <CustomDropdown
                :optionList="monthList"
                @updateCall="updateMonth"
                :default_text="monthText"
                class="month-dropdown"
              />
            </div>
          </div>
          <div class="inner-content-wrap">
            <b-list-group class="status-bar">
              <b-list-group-item>
                <div class="list-item">
                  <i>
                    <img src="../assets/images/clock-ic.svg" alt />
                  </i>
                  <p>
                    <span>1h50</span>Hours
                  </p>
                </div>
              </b-list-group-item>
              <b-list-group-item>
                <div class="list-item">
                  <i>
                    <img src="../assets/images/certified-ic.svg" alt />
                  </i>
                  <p>
                    <span>Top 2%</span>Volunteering Rank
                  </p>
                </div>
              </b-list-group-item>
              <b-list-group-item>
                <div class="list-item">
                  <i>
                    <img src="../assets/images/request-ic.svg" alt />
                  </i>
                  <p>
                    <span>25</span>Open Volunteering Requests
                  </p>
                </div>
              </b-list-group-item>
              <b-list-group-item>
                <div class="list-item">
                  <i>
                    <img src="../assets/images/target-ic.svg" alt />
                  </i>
                  <p>
                    <span>10</span>Mission
                  </p>
                </div>
              </b-list-group-item>
              <b-list-group-item>
                <div class="list-item">
                  <i>
                    <img src="../assets/images/vote-ic.svg" alt />
                  </i>
                  <p>
                    <span>55</span>Voted Missions
                  </p>
                </div>
              </b-list-group-item>
              <b-list-group-item>
                <div class="list-item">
                  <i>
                    <img src="../assets/images/group-ic.svg" alt />
                  </i>
                  <p>
                    <span>101</span>Organization
                  </p>
                </div>
              </b-list-group-item>
            </b-list-group>
            <b-row class="chart-block">
              <b-col lg="6" class="chart-col">
                <div class="inner-chart-col">
                  <div class="chart-title">
                    <h5>Hours tracked this year</h5>
                  </div>
                  <div class="progress-chart">
                    <!-- <img src="../assets/images/progress-ch.png" alt /> -->
                    <b-progress :max="max">
                      <b-progress-bar :value="value">
                        Completed:
                        <span>{{ value }} hours</span>
                      </b-progress-bar>
                    </b-progress>
                    <ul class="progress-axis">
                      <li v-for="xvalue in xvalues" :key="xvalue">{{xvalue}}</li>
                    </ul>
                    <p class="progress-label">Goal: 500 Hours</p>
                  </div>
                </div>
              </b-col>
              <b-col lg="6" class="chart-col">
                <div class="inner-chart-col">
                  <div class="chart-title">
                    <h5>Hours per month</h5>
                    <CustomDropdown
                      :optionList="missionTitle"
                      @updateCall="updateMissionTitle"
                      :default_text="missionTitleText"
                      class="month-dropdown"
                    />
                  </div>
                  <div class="line-chart">
                    <!-- <img src="../assets/images/line-ch.png" alt /> -->
                    <canvas ref="lineChartRefs"></canvas>
                  </div>
                </div>
              </b-col>
            </b-row>
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
    CustomDropdown,
    PrimaryFooter,
    Chart,
    DashboardBreadcrumb
  },

  name: "dashboard",

  data() {
    return {
      value: 210,
      max: 500,
      xvalues: [0, 50, 100, 150, 200, 250, 300, 350, 400, 450, 500],
      yearText: "Year",
      monthText: "Month",
      missionTitleText: "Mission title",
      yearList: [
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
      missionTitle: [
        "Mission title1",
        "Mission title2",
        "Mission title3",
        "Mission title4"
      ],
      monthList: [
        "January",
        "february",
        "march",
        "april",
        "may",
        "june",
        "july",
        "august",
        "september",
        "october",
        "november",
        "december"
      ],
      chartdata: {
        labels: [
          "Jan 18",
          "Feb 18",
          "Apr 18",
          "Mar 18",
          "May 18",
          "Jun 18",
          "Jul 18",
          "Aug 18"
        ]
      },
      currentpage: "dashboard"
    };
  },
  mounted() {
    var lineChartRefs = this.$refs.lineChartRefs;
    var lineContent = lineChartRefs.getContext("2d");
    lineChartRefs.height = 350;
    var lineChart = new Chart(lineContent, {
      type: "line",
      data: {
        labels: [
          "Jan 18",
          "Feb 18",
          "Apr 18",
          "Mar 18",
          "May 18",
          "Jun 18",
          "Jul 18",
          "Aug 18"
        ],
        datasets: [
          {
            data: [25, 10, 0, 20, 15, 10, 48, 0],
            backgroundColor: "#b2cc9d",
            borderColor: "#4b6d30",
            pointBackgroundColor: "#4b6d30"
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: false
        },
        scales: {
          xAxes: [
            {
              gridLines: {
                display: false
              },
              ticks: {
                fontColor: "#414141",
                fontSize: 14
              },
              scaleLabel: {
                display: true,
                labelString: "Month",
                fontSize: 16,
                fontColor: "#414141"
              }
            }
          ],
          yAxes: [
            {
              stacked: true,
              ticks: {
                max: 75,
                min: 0,
                stepSize: 25,
                fontColor: "#414141",
                fontSize: 14
              },
              scaleLabel: {
                display: true,
                labelString: "Hours",
                fontSize: 16,
                fontColor: "#414141"
              }
            }
          ]
        },
        elements: {
          line: {
            tension: 0
          }
        }
      }
    });
  },
  methods: {
    updateYear(value) {
      this.yearText = value;
    },
    updateMonth(value) {
      this.monthText = value;
    },
    updateMissionTitle(value) {
      this.missionTitleText = value;
    },
    handleActive() {}
  }
};
</script>
