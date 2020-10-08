import store from './store';
import sanitizeHtml from 'sanitize-html';

export function setSiteTitle() {
  if (!store.state.defaultLanguage) {
    return;
  }

  const defaultLang = store.state.defaultLanguage.toLowerCase();
  const translations = JSON.parse(store.state.languageLabel);
  let siteTitle = translations.label.site_title || 'Home';
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
};

export function cleanHtml(html) {
  return sanitizeHtml(html, {
    allowedTags: sanitizeHtml.defaults.allowedTags.concat(['img']),
    allowedAttributes: {
      '*': [
        'style',
        'border',
        'cellpadding',
        'cellspacing',
        'title',
        'href',
        'src',
        'name',
        'alt'
      ]
    }
  });
}
