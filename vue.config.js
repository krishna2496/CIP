module.exports = {
  css: {
    loaderOptions: {
      sass: {
        data: newFunction()
      }
    }
  },
  chainWebpack: config => {
    config.module
      .rule("vue")
      .use("vue-loader")
      .loader("vue-loader")
      .tap(options => {
        options["transformAssetUrls"] = {
          img: "src",
          image: "xlink:href",
          "b-img": "src",
          "b-img-lazy": ["src", "blank-src"],
          "b-card": "img-src",
          "b-card-img": "img-src",
          "b-card-img-lazy": ["src", "blank-src"],
          "b-carousel-slide": "img-src",
          "b-embed": "src"
        };

        return options;
      });
  }
};

function newFunction() {
  return `
	@import "@/../src/assets/scss/variables.scss"; 
	@import "@/../src/assets/scss/mixin.scss";
	@import "@/../src/assets/scss/theme/main-theme.scss";
	`;
}
