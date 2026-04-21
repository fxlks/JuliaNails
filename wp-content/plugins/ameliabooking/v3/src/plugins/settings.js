const settings = window.wpAmeliaSettings
const locale = window.localeLanguage[0]
const longLocale = window.localeLanguage[0]
const shortLocale = window.localeLanguage[0].split('_')[0]
const ajaxUrl = window.wpAmeliaUrls.wpAmeliaPluginAjaxURL
const labels = window.wpAmeliaLabels

export { settings, locale, shortLocale, longLocale, ajaxUrl, labels }
