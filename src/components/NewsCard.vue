<template>
  <div class="cards-wrapper">
    <div class="card-grid">
      <b-row>
        <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
          <b-card no-body>
            <b-card-header>
              <div class="header-img-block">
                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[0]+')'}"></b-link>
              </div>
              <div class="group-category">
                <span class="category-text">Customer Experience</span>
              </div>
              <b-link class="btn btn-borderwhite icon-btn" title="View Details" to="/newsdetail">
                <span>View Details</span>
                <i>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 16"
                    width="19"
                    height="15"
                  >
                    <g id="Main Content">
                      <g id="1">
                        <g id="Button">
                          <path
                            id="Forma 1 copy 12"
                            class="shp0"
                            d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z"
                          />
                        </g>
                      </g>
                    </g>
                  </svg>
                </i>
              </b-link>
            </b-card-header>
            <b-card-body>
              <div class="content-block">
                <div class="top-block">
                  <b-link
                    target="_blank"
                    title="Grow Trees"
                    class="card-title mb-2"
                  >10 User and Customer Engagement Strategies for 2019</b-link>
                  <b-card-text>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore...
                  </b-card-text>
                </div>
                <div class="bottom-block">
                  <b-card-text class="publish-date">Published on 23/07/2019</b-card-text>
                  <div class="author-block">
                    <i :style="{backgroundImage: 'url('+profileImages[0]+')'}"></i>
                    <span>Andrew Johnson</span>
                  </div>
                </div>
              </div>
            </b-card-body>
          </b-card>
        </b-col>
        <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
          <b-card no-body>
            <b-card-header>
              <div class="header-img-block">
                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[1]+')'}"></b-link>
              </div>
              <div class="group-category">
                <span class="category-text">Updates</span>
              </div>
              <b-button class="btn-borderwhite icon-btn" title="View Details" to="/newsdetail">
                <span>View Details</span>
                <i>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 16"
                    width="19"
                    height="15"
                  >
                    <g id="Main Content">
                      <g id="1">
                        <g id="Button">
                          <path
                            id="Forma 1 copy 12"
                            class="shp0"
                            d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z"
                          />
                        </g>
                      </g>
                    </g>
                  </svg>
                </i>
              </b-button>
            </b-card-header>
            <b-card-body>
              <div class="content-block">
                <div class="top-block">
                  <b-link
                    target="_blank"
                    title="Grow Trees"
                    class="card-title mb-2"
                  >Overheard in Product: Good Products, the End of Failure.</b-link>
                  <b-card-text>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore...
                  </b-card-text>
                </div>
                <div class="bottom-block">
                  <b-card-text class="publish-date">Published on 23/07/2019</b-card-text>
                  <div class="author-block">
                    <i :style="{backgroundImage: 'url('+profileImages[1]+')'}"></i>
                    <span>Charles Vigue</span>
                  </div>
                </div>
              </div>
            </b-card-body>
          </b-card>
        </b-col>
        <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
          <b-card no-body>
            <b-card-header>
              <div class="header-img-block">
                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[2]+')'}"></b-link>
              </div>
              <div class="group-category">
                <span class="category-text">Customer Experience</span>
              </div>
              <b-button class="btn-borderwhite icon-btn" title="View Details" to="/newsdetail">
                <span>View Details</span>
                <i>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 16"
                    width="19"
                    height="15"
                  >
                    <g id="Main Content">
                      <g id="1">
                        <g id="Button">
                          <path
                            id="Forma 1 copy 12"
                            class="shp0"
                            d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z"
                          />
                        </g>
                      </g>
                    </g>
                  </svg>
                </i>
              </b-button>
            </b-card-header>
            <b-card-body>
              <div class="content-block">
                <div class="top-block">
                  <b-link
                    target="_blank"
                    title="Grow Trees"
                    class="card-title mb-2"
                  >10 User and Customer Engagement Strategies for 2019</b-link>
                  <b-card-text>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore...
                  </b-card-text>
                </div>
                <div class="bottom-block">
                  <b-card-text class="publish-date">Published on 23/07/2019</b-card-text>
                  <div class="author-block">
                    <i :style="{backgroundImage: 'url('+profileImages[2]+')'}"></i>
                    <span>Travis Steen</span>
                  </div>
                </div>
              </div>
            </b-card-body>
          </b-card>
        </b-col>
        <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
          <b-card no-body>
            <b-card-header>
              <div class="header-img-block">
                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[3]+')'}"></b-link>
              </div>
              <div class="group-category">
                <span class="category-text">Customer Experience</span>
              </div>
              <b-button class="btn-borderwhite icon-btn" title="View Details" to="/newsdetail">
                <span>View Details</span>
                <i>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 16"
                    width="19"
                    height="15"
                  >
                    <g id="Main Content">
                      <g id="1">
                        <g id="Button">
                          <path
                            id="Forma 1 copy 12"
                            class="shp0"
                            d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z"
                          />
                        </g>
                      </g>
                    </g>
                  </svg>
                </i>
              </b-button>
            </b-card-header>
            <b-card-body>
              <div class="content-block">
                <div class="top-block">
                  <b-link
                    target="_blank"
                    title="Grow Trees"
                    class="card-title mb-2"
                  >10 User and Customer Engagement Strategies for 2019</b-link>
                  <b-card-text>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore...
                  </b-card-text>
                </div>
                <div class="bottom-block">
                  <b-card-text class="publish-date">Published on 23/07/2019</b-card-text>
                  <div class="author-block">
                    <i :style="{backgroundImage: 'url('+profileImages[0]+')'}"></i>
                    <span>Andrew Johnson</span>
                  </div>
                </div>
              </div>
            </b-card-body>
          </b-card>
        </b-col>
        <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
          <b-card no-body>
            <b-card-header>
              <div class="header-img-block">
                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[4]+')'}"></b-link>
              </div>
              <div class="group-category">
                <span class="category-text">Updates</span>
              </div>
              <b-button class="btn-borderwhite icon-btn" title="View Details">
                <span>View Details</span>
                <i>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 16"
                    width="19"
                    height="15"
                  >
                    <g id="Main Content">
                      <g id="1">
                        <g id="Button">
                          <path
                            id="Forma 1 copy 12"
                            class="shp0"
                            d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z"
                          />
                        </g>
                      </g>
                    </g>
                  </svg>
                </i>
              </b-button>
            </b-card-header>
            <b-card-body>
              <div class="content-block">
                <div class="top-block">
                  <b-link
                    target="_blank"
                    title="Grow Trees"
                    class="card-title mb-2"
                  >Overheard in Product: Good Products, the End of Failure.</b-link>
                  <b-card-text>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore...
                  </b-card-text>
                </div>
                <div class="bottom-block">
                  <b-card-text class="publish-date">Published on 23/07/2019</b-card-text>
                  <div class="author-block">
                    <i :style="{backgroundImage: 'url('+profileImages[1]+')'}"></i>
                    <span>Charles Vigue</span>
                  </div>
                </div>
              </div>
            </b-card-body>
          </b-card>
        </b-col>
        <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
          <b-card no-body>
            <b-card-header>
              <div class="header-img-block">
                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[5]+')'}"></b-link>
              </div>
              <div class="group-category">
                <span class="category-text">Customer Experience</span>
              </div>
              <b-button class="btn-borderwhite icon-btn" title="View Details" to="/newsdetail">
                <span>View Details</span>
                <i>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 16"
                    width="19"
                    height="15"
                  >
                    <g id="Main Content">
                      <g id="1">
                        <g id="Button">
                          <path
                            id="Forma 1 copy 12"
                            class="shp0"
                            d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z"
                          />
                        </g>
                      </g>
                    </g>
                  </svg>
                </i>
              </b-button>
            </b-card-header>
            <b-card-body>
              <div class="content-block">
                <div class="top-block">
                  <b-link
                    target="_blank"
                    title="Grow Trees"
                    class="card-title mb-2"
                  >10 User and Customer Engagement Strategies for 2019</b-link>
                  <b-card-text>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore...
                  </b-card-text>
                </div>
                <div class="bottom-block">
                  <b-card-text class="publish-date">Published on 23/07/2019</b-card-text>
                  <div class="author-block">
                    <i :style="{backgroundImage: 'url('+profileImages[2]+')'}"></i>
                    <span>Travis Steen</span>
                  </div>
                </div>
              </div>
            </b-card-body>
          </b-card>
        </b-col>
        <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
          <b-card no-body>
            <b-card-header>
              <div class="header-img-block">
                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[6]+')'}"></b-link>
              </div>
              <div class="group-category">
                <span class="category-text">Customer Experience</span>
              </div>
              <b-button class="btn-borderwhite icon-btn" title="View Details" to="/newsdetail">
                <span>View Details</span>
                <i>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 16"
                    width="19"
                    height="15"
                  >
                    <g id="Main Content">
                      <g id="1">
                        <g id="Button">
                          <path
                            id="Forma 1 copy 12"
                            class="shp0"
                            d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z"
                          />
                        </g>
                      </g>
                    </g>
                  </svg>
                </i>
              </b-button>
            </b-card-header>
            <b-card-body>
              <div class="content-block">
                <div class="top-block">
                  <b-link
                    target="_blank"
                    title="Grow Trees"
                    class="card-title mb-2"
                  >10 User and Customer Engagement Strategies for 2019</b-link>
                  <b-card-text>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore...
                  </b-card-text>
                </div>
                <div class="bottom-block">
                  <b-card-text class="publish-date">Published on 23/07/2019</b-card-text>
                  <div class="author-block">
                    <i :style="{backgroundImage: 'url('+profileImages[0]+')'}"></i>
                    <span>Andrew Johnson</span>
                  </div>
                </div>
              </div>
            </b-card-body>
          </b-card>
        </b-col>
        <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
          <b-card no-body>
            <b-card-header>
              <div class="header-img-block">
                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[7]+')'}"></b-link>
              </div>
              <div class="group-category">
                <span class="category-text">Updates</span>
              </div>
              <b-button class="btn-borderwhite icon-btn" title="View Details">
                <span>View Details</span>
                <i>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 16"
                    width="19"
                    height="15"
                  >
                    <g id="Main Content">
                      <g id="1">
                        <g id="Button">
                          <path
                            id="Forma 1 copy 12"
                            class="shp0"
                            d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z"
                          />
                        </g>
                      </g>
                    </g>
                  </svg>
                </i>
              </b-button>
            </b-card-header>
            <b-card-body>
              <div class="content-block">
                <div class="top-block">
                  <b-link
                    target="_blank"
                    title="Grow Trees"
                    class="card-title mb-2"
                  >Overheard in Product: Good Products, the End of Failure.</b-link>
                  <b-card-text>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore...
                  </b-card-text>
                </div>
                <div class="bottom-block">
                  <b-card-text class="publish-date">Published on 23/07/2019</b-card-text>
                  <div class="author-block">
                    <i :style="{backgroundImage: 'url('+profileImages[1]+')'}"></i>
                    <span>Charles Vigue</span>
                  </div>
                </div>
              </div>
            </b-card-body>
          </b-card>
        </b-col>
        <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up">
          <b-card no-body>
            <b-card-header>
              <div class="header-img-block">
                <b-link class="group-img" :style="{backgroundImage: 'url('+grpImages[8]+')'}"></b-link>
              </div>
              <div class="group-category">
                <span class="category-text">Customer Experience</span>
              </div>
              <b-button class="btn-borderwhite icon-btn" title="View Details" to="/newsdetail">
                <span>View Details</span>
                <i>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 16"
                    width="19"
                    height="15"
                  >
                    <g id="Main Content">
                      <g id="1">
                        <g id="Button">
                          <path
                            id="Forma 1 copy 12"
                            class="shp0"
                            d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z"
                          />
                        </g>
                      </g>
                    </g>
                  </svg>
                </i>
              </b-button>
            </b-card-header>
            <b-card-body>
              <div class="content-block">
                <div class="top-block">
                  <b-link
                    target="_blank"
                    title="Grow Trees"
                    class="card-title mb-2"
                  >10 User and Customer Engagement Strategies for 2019</b-link>
                  <b-card-text>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore...
                  </b-card-text>
                </div>
                <div class="bottom-block">
                  <b-card-text class="publish-date">Published on 23/07/2019</b-card-text>
                  <div class="author-block">
                    <i :style="{backgroundImage: 'url('+profileImages[2]+')'}"></i>
                    <span>Travis Steen</span>
                  </div>
                </div>
              </div>
            </b-card-body>
          </b-card>
        </b-col>
      </b-row>
    </div>
  </div>
</template>
<script>
export default {
  name: "NewsCard",
  components: {},
  data() {
    return {
      showBlock: false,
      max: 100,
      value: 80,
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
      ],
      profileImages: [
        require("@/assets/images/volunteer1.png"),
        require("@/assets/images/volunteer2.png"),
        require("@/assets/images/volunteer7.png")
      ]
    };
  },
  methods: {},
  mounted() {}
};
</script>
