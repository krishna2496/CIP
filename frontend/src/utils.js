import store from './store';

export function setSiteTitle() {
  const languageData = JSON.parse(store.state.languageLabel);
  if (store.state.defaultLanguage && languageData) {
      let defaultLang = store.state.defaultLanguage.toLowerCase();
      let siteTitle = '';
      if (store.state.siteTitle && store.state.siteTitle.translations != "") {
          let siteTranslationArray = store.state.siteTitle.translations;
          let data = siteTranslationArray.filter((item) => {
              if (item.lang == defaultLang) {
                  return item;
              }
          });
          if (data[0] && data[0].title) {
              siteTitle = data[0].title;
          } else {
              let data = siteTranslationArray.filter((item) => {
                  if (item.lang == store.state.defaultTenantLanguage.toLowerCase()) {
                      return item;
                  }
              });

              if (data[0] && data[0].title) {
                  siteTitle = data[0].title;
              } else {
                  if (typeof(languageData.label.site_title) != "undefined") {
                      siteTitle = languageData.label.site_title;
                  }
              }
          }
      } else {
          if (typeof(languageData.label.site_title) != "undefined") {
              siteTitle = languageData.label.site_title;
          }
      }
      document.title = siteTitle;
  }
}
