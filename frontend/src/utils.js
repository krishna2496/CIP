import constants from './constant';
import store from './store';

export function setSiteTitle() {
  const defaultLang = store.state.defaultLanguage.toLowerCase();
  let siteTitle = constants.DEFAULT_SITE_TITLE;
  if (store.state.siteTitle
    && store.state.siteTitle.translations
    && store.state.siteTitle.translations.length
  ) {
    const siteTranslationArray = store.state.siteTitle.translations;
    const data = siteTranslationArray.find((item) => item.lang === defaultLang);
    if (data && data.title) {
      siteTitle = data.title;
    }
  }
  document.title = siteTitle;
}
