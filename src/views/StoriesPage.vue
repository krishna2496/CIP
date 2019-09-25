<template>
  <div class="inner-pages storie-page lists-page">
    <header>
      <TopHeader></TopHeader>
    </header>
    <main>
      <b-container>
        <BannerSection />
        <StoriesCard />
        <div class="pagination-block" data-aos="fade-up">
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
      <i class="icon-wrap">
        <img class="img-normal" src="../assets/images/down-arrow.svg" alt="Down Arrow" />
        <img class="img-rollover" src="../assets/images/down-arrow-black.svg" alt="Down Arrow" />
      </i>
    </back-to-top>
  </div>
</template>
<script>
import TopHeader from "../components/Header/TopHeader";
import BottomHeader from "../components/Header/BottomHeader";
import PrimaryFooter from "../components/Footer/PrimaryFooter";
import BannerSection from "../components/BannerSection";
import StoriesCard from "../components/StoriesCard";

export default {
  components: {
    TopHeader,
    BottomHeader,
    PrimaryFooter,
    BannerSection,
    StoriesCard
  },
  data() {
    return {
      rows: 25,
      perPage: 5,
      currentPage: 1
    };
  },

  methods: {},
  created() {},
  destroyed() {}
};
</script>