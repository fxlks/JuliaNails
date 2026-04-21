import {
  createApp,
  defineAsyncComponent
} from 'vue/dist/vue.esm-bundler'
import { createStore } from "vuex";
import entities from "./../../../store/modules/entities";
import booking from "./../../../store/modules/booking";
import event from "./../../../store/modules/event";
import attendee from "./../../../store/modules/attendee";
import appointment from "./../../../store/modules/appointment";
import employee from "./../../../store/modules/employee";
import eventEntities from "../../../store/modules/eventEntities";
import eventBooking from "../../../store/modules/eventBooking";
import shortcodeParams from "../../../store/modules/shortcodeParams";
import params from "../../../store/modules/params";
import pagination from "../../../store/modules/pagination";
import customerInfo from "../../../store/modules/customerInfo";
import customFields from "../../../store/modules/customFields";
import recurring from "../../../store/modules/recurring";
import persons from "../../../store/modules/persons.js";
import tickets from "../../../store/modules/tickets.js";
import payment from "../../../store/modules/payment.js";
import bookableType from "../../../store/modules/bookableType.js";
import coupon from "../../../store/modules/coupon.js";
import auth from "../../../store/modules/auth.js";
import cabinet from "../../../store/modules/cabinet.js";
import cabinetFilters from "../../../store/modules/cabinetFilters.js"
import eventWaitingListOptions from "../../../store/modules/eventWaitingListOptions"
import stepByStepFilters from "../../../store/modules/stepByStepFilters.js"
import appointmentWaitingListOptions from "../../../store/modules/appointmentWaitingListOptions"

import {
  provide,
  ref,
  reactive,
  readonly
} from "vue";
import VueGtag from "vue-gtag";
import {
  install,
  init
} from "./facebookPixel.js";

import {useLicence} from "../common/licence";

import dayjs from "dayjs";
import updateLocale from "dayjs/plugin/updateLocale";

dayjs.extend(updateLocale);

dayjs.updateLocale(dayjs.locale(), {
  weekStart: window.wpAmeliaSettings.wordpress.startOfWeek,
})

// It is necessary to investigate what is the best practice
// import axios from './plugins/axios'

const StepFormWrapper = defineAsyncComponent({
  loader: () => import('../../../views/public/StepForm/BookingStepForm.vue'),
})

const CatalogFormWrapper = defineAsyncComponent({
  loader: () => import('../../../views/public/CatalogForm/CatalogForm.vue'),
})

const EventsListFormWrapper = defineAsyncComponent({
  loader: () => import('../../../views/public/EventForm/EventListForm/EventsListForm.vue'),
})

const EventsCalendarFormWrapper = defineAsyncComponent({
  loader: () => import('../../../views/public/EventForm/EventCalendarForm/EvensCalendarForm.vue'),
})

const DialogForms = defineAsyncComponent({
  loader: () => import('../../../views/public/Dialog/DialogForms.vue'),
})

const CustomerPanelWrapper = defineAsyncComponent({
  loader: () => import('../../../views/public/Cabinet/CustomerPanel/CustomerPanel.vue'),
})

if (typeof window.ameliaShortcodeData === 'undefined') {
  window.ameliaShortcodeData = [{counter: null}]
}

const dynamicCdn = window.wpAmeliaUrls.wpAmeliaPluginURL + 'v3/public/';

window.__dynamic_handler__ = function(importer) {
  return dynamicCdn + 'assets/' + importer;
}
// @ts-ignore
window.__dynamic_preload__ = function(preloads) {
  return preloads.map(preload => dynamicCdn + preload);
}

let isMounted = ref(false)

if (window.ameliaShortcodeDataTriggered !== undefined) {
  window.ameliaShortcodeDataTriggered.forEach((shortCodeData) => {

    // * Shortcodes that are rendered in Amelia Popup
    if (shortCodeData.in_dialog) {
      // * Collection of all external buttons that are connected to "Amelia popup"
      let externalButtonsLoading = setInterval(() => {
        let externalButtons = shortCodeData.trigger_type && shortCodeData.trigger_type === 'class' ? [...document.getElementsByClassName(shortCodeData.trigger)]
          : [document.getElementById(shortCodeData.trigger)]

        if (externalButtons.length > 0 && externalButtons[0] !== null && typeof externalButtons[0] !== 'undefined') {
          clearInterval(externalButtonsLoading)

          // * Detect if form loaded from redirection
          if ('ameliaCache' in window && window.ameliaCache.length && window.ameliaCache[0]) {
            let cacheData = JSON.parse(window.ameliaCache[0])
            if (cacheData &&
              'request' in cacheData &&
              'form' in cacheData.request &&
              'shortcode' in cacheData.request.form &&
              'trigger' in cacheData.request.form.shortcode &&
              cacheData.request.form.shortcode.trigger &&
              parseInt(cacheData.request.form.shortcode.counter) === parseInt(shortCodeData.counter)
            ) {
              shortCodeData.isRestored = true
            }
          }

          // * vue creation
          createAmelia(shortCodeData)

          // * Made the buttons invisible because amelia components are not fully loaded
          externalButtons.forEach(btn => {
            btn.style.pointerEvents = 'none'
          })

          // Create a timeout to prevent buttons from being permanently disabled
          let componentLoadTimeout

          let componentsLoaded = setInterval(() => {
            if (isMounted.value) {
              clearInterval(componentsLoaded)
              clearTimeout(componentLoadTimeout)
              // * Made the buttons visible because amelia components are fully loaded
              externalButtons.forEach(btn => {
                btn.style.removeProperty('pointer-events')
              })
            }
          }, 250)

          // Safety timeout - if components don't load within 12 seconds, enable buttons anyway
          componentLoadTimeout = setTimeout(() => {
            clearInterval(componentsLoaded)
            console.warn('Amelia components loading timeout')
            externalButtons.forEach(btn => {
              btn.style.removeProperty('pointer-events')
            })
          }, 12000)
        }
      }, 250)
    } else {
      let ameliaPopUpLoaded = false

      let ameliaBookingButtonLoadInterval = setInterval(
        function () {
          let ameliaPopUpButtons = shortCodeData.trigger_type && shortCodeData.trigger_type === 'class' ? [...document.getElementsByClassName(shortCodeData.trigger)]
            : [document.getElementById(shortCodeData.trigger)]

          if (!ameliaPopUpLoaded && ameliaPopUpButtons.length > 0 && ameliaPopUpButtons[0] !== null && typeof ameliaPopUpButtons[0] !== 'undefined') {
            ameliaPopUpLoaded = true

            clearInterval(ameliaBookingButtonLoadInterval)
            ameliaPopUpButtons.forEach(ameliaPopUpButton => {
              ameliaPopUpButton.onclick = function () {
                let ameliaBookingFormLoadInterval = setInterval(
                  function () {
                    let ameliaPopUpForms = document.getElementsByClassName('amelia-skip-load-' + shortCodeData.counter)

                    if (ameliaPopUpForms.length) {
                      clearInterval(ameliaBookingFormLoadInterval)
                      for (let i = 0; i < ameliaPopUpForms.length; i++) {
                        if (!ameliaPopUpForms[i].classList.contains('amelia-v2-booking-' + shortCodeData.counter + '-loaded')) {
                          createAmelia(shortCodeData)
                        }
                      }
                    }
                  }, 1000
                )
              }

              if ('ameliaCache' in window && window.ameliaCache.length && window.ameliaCache[0]) {
                let cacheData = JSON.parse(window.ameliaCache[0])

                if (cacheData &&
                  'request' in cacheData &&
                  'form' in cacheData.request &&
                  'shortcode' in cacheData.request.form &&
                  'trigger' in cacheData.request.form.shortcode &&
                  cacheData.request.form.shortcode.trigger
                ) {
                  if (!('trigger_type' in cacheData.request.form.shortcode) ||
                    !cacheData.request.form.shortcode.trigger_type ||
                    cacheData.request.form.shortcode.trigger_type === 'id'
                  ) {
                    let el = document.getElementById(cacheData.request.form.shortcode.trigger)

                    if (typeof el !== 'undefined') {
                      el.click()
                    }
                  } else if ('trigger_type' in cacheData.request.form.shortcode &&
                    cacheData.request.form.shortcode.trigger_type === 'class'
                  ) {
                    let el = document.getElementsByClassName(cacheData.request.form.shortcode.trigger)

                    if (typeof el !== 'undefined' && el.length) {
                      el[0].click()
                    }
                  }
                }
              }
            })

          }
        }, 1000
      )
    }
  })
}

window.ameliaShortcodeData.forEach((item) => {
  createAmelia(item)
})

function createAmelia(shortcodeData) {
  let wpAmeliaSettings = JSON.parse(JSON.stringify(window.wpAmeliaSettings))

  if (window?.ameliaActions?.init) {
    window.ameliaActions.init(shortcodeData, wpAmeliaSettings)
  }

  const settings = reactive(wpAmeliaSettings)

  let wpAmeliaTimeZones = 'wpAmeliaTimeZones' in window ? window.wpAmeliaTimeZones : []

  let app = createApp({
    setup() {
      const baseURLs = reactive(window.wpAmeliaUrls)
      const labels = reactive(window.wpAmeliaLabels)
      const timeZones = reactive(wpAmeliaTimeZones)
      const timeZone = ref('wpAmeliaTimeZone' in window ? window.wpAmeliaTimeZone[0] : '')
      const localLanguage = ref(window.localeLanguage[0])
      const licence = reactive(useLicence())
      provide('settings', readonly(settings))
      provide('baseUrls', readonly(baseURLs))
      provide('labels', readonly(labels))
      provide('timeZones', readonly(timeZones))
      provide('timeZone', readonly(timeZone))
      provide('localLanguage', readonly(localLanguage))
      provide('shortcodeData', readonly(ref(shortcodeData)))
      provide('licence', licence)
      provide('isMounted', isMounted)
    }
  })

  if (settings.featuresIntegrations.googleAnalytics.enabled && settings.googleTag.id) {
    app.use(VueGtag, {
      config: {id: wpAmeliaSettings.googleTag.id}
    })
  }

  if (settings.featuresIntegrations.googleAnalytics.enabled && settings.googleAnalytics.id) {
    app.use(VueGtag, {
      config: {id: wpAmeliaSettings.googleAnalytics.id}
    })
  }

  if (settings.facebookPixel.id) {
    install()

    init(wpAmeliaSettings.facebookPixel.id)
  }

  let data = 'ameliaCache' in window && window.ameliaCache.length && window.ameliaCache[0]
    ? JSON.parse(window.ameliaCache[0])
    : null

  app
    .component('StepFormWrapper', StepFormWrapper)
    .component('CatalogFormWrapper', CatalogFormWrapper)
    .component('EventsListFormWrapper', EventsListFormWrapper)
    .component('EventsCalendarFormWrapper', EventsCalendarFormWrapper)
    .component('DialogForms', DialogForms)
    .component('CustomerPanelWrapper', CustomerPanelWrapper)
    .use(
      createStore({
        namespaced: true,

        state: () => ({
          settings: reactive(wpAmeliaSettings),
          labels: reactive(window.wpAmeliaLabels),
          localLanguage: ref(window.localeLanguage[0]),
          baseUrls: reactive(window.wpAmeliaUrls),
          timeZones: reactive(wpAmeliaTimeZones),
          timeZone: ref('wpAmeliaTimeZone' in window ? window.wpAmeliaTimeZone[0] : ''),
          isRtl: ref(document.documentElement.dir === 'rtl'),
          ready: false,
          loading: true,
          formKey: '',
          restoring: data &&
            data?.request?.form?.shortcode?.counter &&
            parseInt(data.request.form.shortcode.counter) === parseInt(shortcodeData.counter),
          restored: false,
        }),

        getters: {
          getSettings (state) {
            return state.settings
          },

          getLabels (state) {
            return state.labels
          },

          getLocalLanguage (state) {
            return state.localLanguage
          },

          getBaseUrls (state) {
            return state.baseUrls
          },

          getRestoring (state) {
            return state.restoring
          },

          getRestored (state) {
            return state.restored
          },

          getReady (state) {
            return state.ready
          },

          getLoading (state) {
            return state.loading
          },

          getFormKey (state) {
            return state.formKey
          },

          getIsRtl (state) {
            return state.isRtl
          }
        },

        mutations: {
          setRestoring (state, payload) {
            state.restoring = payload
          },

          setRestored (state, payload) {
            state.restored = payload
          },

          setReady (state, payload) {
            state.ready = payload
          },

          setLoading (state, payload) {
            state.loading = payload
          },

          setFormKey (state, payload) {
            state.formKey = payload
          }
        },

        modules: {
          entities,
          booking,
          event,
          attendee,
          appointment,
          employee,
          eventEntities,
          eventBooking,
          shortcodeParams,
          params,
          pagination,
          customerInfo,
          customFields,
          recurring,
          persons,
          tickets,
          payment,
          bookableType,
          coupon,
          auth,
          cabinet,
          cabinetFilters,
          eventWaitingListOptions,
          stepByStepFilters,
          appointmentWaitingListOptions
        },
      })
    )
    .mount(`#amelia-v2-booking${shortcodeData.counter !== null ? '-' + shortcodeData.counter : ''}`)
}

window.amelia = {load: createAmelia}
