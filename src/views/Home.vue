<template>
  <div class="home-page inner-pages filter-header">
    <header @scroll="handleScroll">
      <TopHeader></TopHeader>
      <BottomHeader></BottomHeader>
    </header>
    <main>
      <b-container class="home-content-wrapper">
        <div class="chip-container">
          <CustomChip :textVal="'Tree Plantation'"/>
          <CustomChip :textVal="'Canada'"/>
          <CustomChip :textVal="'Toronto'"/>
          <CustomChip :textVal="'Montreal'"/>
          <CustomChip :textVal="'Environment'"/>
          <CustomChip :textVal="'Nutrition'"/>
          <CustomChip :textVal="'Anthropology'"/>
          <CustomChip :textVal="'Environmental Science'"/>
          <b-button class="clear-btn">Clear All</b-button>
        </div>
        <div class="heading-section">
          <h2>
            Explore
            <strong>72 missions</strong>
          </h2>
          <div class="right-section">
            <CustomDropdown
              :optionList="sortByOptions"
              :default_text="sortByDefault"
              @updateCall="updateSortTitle"
            />
          </div>
        </div>
        <b-tabs class="view-tab">
          <b-tab class="grid-tab-content">
            <template slot="title">
              <i class="grid">
                <svg
                  version="1.1"
                  id="Capa_1"
                  xmlns="http://www.w3.org/2000/svg"
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                  x="0px"
                  y="0px"
                  viewBox="0 0 174.239 174.239"
                  style="enable-background:new 0 0 174.239 174.239;"
                  xml:space="preserve"
                >
                  <g>
                    <g>
                      <path
                        d="M174.239,174.239H96.945V96.945h77.294V174.239z M111.88,159.305h47.425V111.88H111.88V159.305z"
                      ></path>
                    </g>
                    <g>
                      <path
                        d="M77.294,174.239H0V96.945h77.294V174.239z M14.935,159.305H62.36V111.88H14.935V159.305z"
                      ></path>
                    </g>
                    <g>
                      <path
                        d="M174.239,77.294H96.945V0h77.294V77.294z M111.88,62.36h47.425V14.935H111.88V62.36z"
                      ></path>
                    </g>
                    <g>
                      <path
                        d="M77.294,77.294H0V0h77.294V77.294z M14.935,62.36H62.36V14.935H14.935V62.36z"
                      ></path>
                    </g>
                  </g>
                </svg>
              </i>
            </template>
            <LandingCard id="my-cardlist"/>
          </b-tab>
          <b-tab class="list-tab-content">
            <template slot="title">
              <i class="list">
                <svg
                  id="Layer_1"
                  data-name="Layer 1"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 22 22"
                >
                  <path id="List" class="cls-1" d="M0,0H22V2H0ZM0,10H22v2H0ZM0,20H22v2H0Z"></path>
                </svg>
              </i>
            </template>
            <CardListBlock/>
          </b-tab>
        </b-tabs>
        <div class="pagination-block">
          <b-pagination
            v-model="currentPage"
            :total-rows="rows"
            :per-page="perPage"
            align="center"
            aria-controls="my-cardlist"
          ></b-pagination>
        </div>
      </b-container>
    </main>
    <footer>
      <PrimaryFooter></PrimaryFooter>
    </footer>
    <back-to-top bottom="68px" right="40px" title="back to top">
      <i>
        <svg
          version="1.1"
          id="Capa_1"
          xmlns="http://www.w3.org/2000/svg"
          xmlns:xlink="http://www.w3.org/1999/xlink"
          x="0px"
          y="0px"
          width="451.847px"
          height="451.847px"
          viewBox="0 0 451.847 451.847"
          style="enable-background:new 0 0 451.847 451.847;"
          xml:space="preserve"
        >
          <g>
            <path
              d="M225.923,354.706c-8.098,0-16.195-3.092-22.369-9.263L9.27,151.157c-12.359-12.359-12.359-32.397,0-44.751
		c12.354-12.354,32.388-12.354,44.748,0l171.905,171.915l171.906-171.909c12.359-12.354,32.391-12.354,44.744,0
		c12.365,12.354,12.365,32.392,0,44.751L248.292,345.449C242.115,351.621,234.018,354.706,225.923,354.706z"
            ></path>
          </g>
        </svg>
      </i>
    </back-to-top>
  </div>
</template>

<script>
import TopHeader from "../components/Header/TopHeader";
import BottomHeader from "../components/Header/BottomHeader";
import PrimaryFooter from "../components/Footer/PrimaryFooter";
import LandingCard from "../components/LandingCard";
import CardListBlock from "../components/CardListBlock";
import CustomDropdown from "../components/CustomDropdown";
import CustomChip from "../components/CustomChip";

export default {
  components: {
    TopHeader,
    BottomHeader,
    PrimaryFooter,
    LandingCard,
    CardListBlock,
    CustomDropdown,
    CustomChip
  },

  name: "home",

  data() {
    return {
      rows: 25,
      perPage: 5,
      currentPage: 1,
      sortByOptions: [
        "Newest",
        "Oldest",
        "Lowest available seats",
        "Highest available seats",
        "My favourite",
        "Deadline"
      ],
      sortByDefault: "Sort By"
    };
  },
  methods: {
    handleScroll() {
      var body = document.querySelector("body");
      var bheader = document.querySelector("header");
      var bheader_top = bheader.offsetHeight;
      if (window.scrollY > bheader_top) {
        body.classList.add("small-header");
      } else {
        body.classList.remove("small-header");
      }
    },
    updateSortTitle(value) {
      this.sortByDefault = value;
    }
  },
  created() {
    window.addEventListener("scroll", this.handleScroll);
  },
  destroyed() {
    window.removeEventListener("scroll", this.handleScroll);
  }
};
</script>