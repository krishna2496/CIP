<template>
  <div class="cms-page inner-pages">
    <header @scroll="handleScroll">
      <TopHeader></TopHeader>
    </header>
    <main>
      <b-container>
        <h1>Terms of Use</h1>
        <b-row>
          <b-col lg="3" md="4" class="cms-nav">
            <b-nav>
              <b-nav-item active v-scroll-to="{ el: '#block-1' , offset :getOffset}">Introduction</b-nav-item>
              <b-nav-item
                v-scroll-to="{ el: '#block-2',  offset :getOffset}"
              >Changes to the Agreements?</b-nav-item>
              <b-nav-item v-scroll-to="{ el: '#block-3', offset :getOffset}">Using our Service?</b-nav-item>
            </b-nav>
          </b-col>
          <b-col lg="9" md="8">
            <div class="cms-content cms-accordian" id="cms-content">
              <div class="cms-content-block" id="block-1">
                <h2 v-b-toggle.content-1 class="accordian-title">Introduction</h2>
                <b-collapse
                  id="content-1"
                  class="accordian-content"
                  accordion="my-accordion"
                  visible
                >
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                    ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
                    ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in
                    reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur
                    sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id
                    est laborum.
                  </p>
                  <p>
                    Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                    laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi
                    architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas
                    sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione
                    voluptatem sequi nesciunt.
                  </p>
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                    ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
                    ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in
                    reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur
                    sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id
                    est laborum.
                  </p>
                </b-collapse>
              </div>
              <div class="cms-content-block" id="block-2">
                <h2 v-b-toggle.content-2 class="accordian-title">Changes to the Agreements?</h2>
                <b-collapse id="content-2" class="accordian-content" accordion="my-accordion">
                  <p>
                    Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                    laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi
                    architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas
                    sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione
                    voluptatem sequi nesciunt.
                  </p>
                  <h3>Why Optimy Uses These Technologies?</h3>
                  <p class="list-title">We use these technologies for a number of purposes, such as:</p>
                  <b-list-group>
                    <b-list-group-item>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain.</b-list-group-item>
                    <b-list-group-item>
                      At vero eos et accusamus et iusto odio dignissimos ducimus qui
                      blanditiis praesentium voluptatum deleniti atque excepturi sint occaecati cupiditate
                      non provident, similique sunt in culpa qui officia deserunt mollitia animi.
                    </b-list-group-item>
                    <b-list-group-item>
                      On the other hand, we denounce with righteous indignation and dislike
                      men who are so beguiled and demoralized
                    </b-list-group-item>
                    <b-list-group-item>
                      But I must explain to you how all this mistaken idea of denouncing
                      pleasure and praising pain.
                    </b-list-group-item>
                    <b-list-group-item>
                      Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                      eiusmod tempor incididunt ut labore et dolore
                    </b-list-group-item>
                  </b-list-group>
                  <p>
                    Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                    laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi
                    architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas
                    sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione
                    voluptatem sequi nesciunt.
                  </p>
                </b-collapse>
              </div>
              <div class="cms-content-block" id="block-3">
                <h2 v-b-toggle.content-3 class="accordian-title">Using our Service?</h2>
                <b-collapse id="content-3" class="accordian-content" accordion="my-accordion">
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                    ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
                    ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in
                    reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur
                    sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id
                    est laborum.
                  </p>
                  <p>
                    Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                    laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi
                    architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas
                    sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione
                    voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit
                    amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut
                    labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis
                    nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi
                    consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam
                    nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla
                    pariatur.
                  </p>
                  <p>
                    But I must explain to you how all this mistaken idea of denouncing pleasure and praising
                    pain was born and I will give you a complete account of the system, and expound the
                    actual teachings of the great explorer of the truth, the master-builder of human
                    happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure,
                    but because those who do not know how to pursue pleasure rationally encounter
                    consequences that are extremely painful. Nor again is there anyone who loves or pursues
                    or desires to obtain pain of itself, because it is pain, but because occasionally
                    circumstances occur in which toil and pain can procure him some great pleasure. To take
                    a trivial example, which of us ever undertakes laborious physical exercise, except to
                    obtain some advantage from it? But who has any right to find fault with a man who
                    chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain
                    that produces no resultant pleasure.
                  </p>
                  <p>
                    At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium
                    voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint
                    occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt
                    mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et
                    expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque
                    nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda
                    est, omnis dolor repellendus.
                  </p>
                  <p>
                    Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet
                    ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic
                    tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur
                    aut perferendis doloribus asperiores repellat.
                  </p>
                </b-collapse>
              </div>
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
import TopHeader from "../components/Header/TopHeader";
import PrimaryFooter from "../components/Footer/PrimaryFooter";

export default {
  components: {
    TopHeader,
    PrimaryFooter
  },
  data() {
    return {};
  },

  methods: {
    // left menu sticky function
    handleScroll() {
      var nav_ = document.querySelector(".cms-nav");
      var header_height = document.querySelector("header").offsetHeight;
      var nav_top = nav_.offsetTop;
      var window_top = window.pageYOffset + (header_height + 1);
      var nav_height = document.querySelector(".cms-nav .nav").offsetHeight;
      var nav_bottom = nav_height + nav_top;
      var footer_top = document.querySelector("footer").getBoundingClientRect()
        .top;
      if (screen.width > 767) {
        if (window_top > nav_top) {
          if (nav_bottom >= footer_top) {
            nav_.classList.add("absolute");
            nav_.classList.remove("fixed");
          } else {
            nav_.classList.add("fixed");
            nav_.classList.remove("absolute");
          }
        } else {
          nav_.classList.remove("fixed");
        }
      }

      var link_list = document.querySelectorAll(".cms-nav .nav-item");
      var block_list = document.querySelectorAll(".cms-content-block");
      for (var i = 0; i < block_list.length; ++i) {
        if (block_list[i].getBoundingClientRect().top < header_height + 42) {
          for (var j = 0; j < link_list.length; j++) {
            var link_siblings = link_list[j].parentNode.childNodes;
            for (var k = 0; k < link_siblings.length; ++k) {
              link_siblings[k].childNodes[0].classList.remove("active");
            }
            link_siblings[i].childNodes[0].classList.add("active");
          }
        }
      }
    },
    getOffset() {
      var header_height = document.querySelector("header").offsetHeight;
      return -header_height;
    }
  },
  created() {
    window.addEventListener("scroll", this.handleScroll);
    window.addEventListener("resize", this.handleScroll);
    window.addEventListener("resize", this.getOffset);
  },
  destroyed() {
    window.removeEventListener("scroll", this.handleScroll);
    window.removeEventListener("resize", this.handleScroll);
    window.removeEventListener("resize", this.getOffset);
  }
};
</script>