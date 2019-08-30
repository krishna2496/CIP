<template>
  <div class="breadcrumb-wrap">
    <b-container>
      <div class="breadcrumb-dropdown-wrap">
        <span class="breadcrumb-current" @touchend.stop></span>
        <div class="breadcrumb-dropdown">
          <!-- <b-breadcrumb>
            <b-breadcrumb-item to="/dashboard" class="active">Dashboard</b-breadcrumb-item>
            <b-breadcrumb-item to="/dashboardhistory">Volunteering History</b-breadcrumb-item>
            <b-breadcrumb-item to="/dashboardtimesheet">Volunteering Timesheet</b-breadcrumb-item>
            <b-breadcrumb-item to="/dashboardmessage">Messages</b-breadcrumb-item>
            <b-breadcrumb-item to="/dashboardcomments">Comments Histroy</b-breadcrumb-item>
            <b-breadcrumb-item to="/dashboardstories">My Stories</b-breadcrumb-item>
          </b-breadcrumb>-->
          <b-breadcrumb>
            <b-breadcrumb-item
              v-for="(item, idx) in items"
              :key="idx"
              :to="item.link"
              @touchend.stop
            >{{item.name}}</b-breadcrumb-item>
          </b-breadcrumb>
        </div>
      </div>
    </b-container>
  </div>
</template>
<script>
import { setTimeout } from "timers";
export default {
  name: "Breadcrumb",
  props: {
    breadcrumbActive: String
  },
  data() {
    return {
      items: [
        { id: 1, name: "Dashboard", link: "dashboard" },
        { id: 2, name: "Volunteering History", link: "volunteering-history" },
        { id: 3, name: "Volunteering Timesheet", link: "volunteering-timesheet" },
        // { id: 4, name: "Messages", link: "dashboardmessage" },
        // { id: 5, name: "Comments Histroy", link: "dashboardcomments" },
        // { id: 6, name: "My Stories", link: "dashboardstories" }
      ]
    };
  },
  methods: {
    handleBreadcrumb() {
      if (screen.width < 768) {
        var breadcrumbDropdown = document.querySelector(
          ".breadcrumb-dropdown-wrap"
        );
        breadcrumbDropdown.classList.toggle("open");
      }
    }
  },
  created() {
    setTimeout(() => {
      if (document.querySelector(".breadcrumb") != null) {
        var currentDashboard = document.querySelector(
          ".breadcrumb .router-link-active"
        ).innerHTML;
        this.currentDashboardPage = currentDashboard;
        var currentLink = document.querySelector(".breadcrumb-current");
        currentLink.innerHTML = this.currentDashboardPage;
        var breadcrumbItem = document.querySelectorAll(".breadcrumb-item");
        currentLink.addEventListener("click", this.handleBreadcrumb);
      }
    });
  }
};
</script>