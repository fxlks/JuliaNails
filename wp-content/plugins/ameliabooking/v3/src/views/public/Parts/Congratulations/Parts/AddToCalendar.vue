<template>
  <div
    v-if="amSettings.general.addToCalendar && booked && booked.data.length"
    :class="`am-atc-${generatedClass}`"
    :style="cssVars"
  >
    <p>
      {{ props.labels.add_to_calendar }}
    </p>
    <div :class="`am-atc-${generatedClass}-cals`">
      <div
        v-for="(cal, index) in calendars"
        :key="index"
        :class="`am-atc-${generatedClass}-cals-cards`"
      >
        <a
          v-for="calPair in cal"
          :key="calPair.type"
          :href="calPair.links[0]"
          :target="(calPair.type === 'apple' || calPair.type === 'outlook') ? '_self' : '_blank'"
          :style="{borderColor : 'var(--am-c-atc-text-op10)'}"
          :class="`am-atc-${generatedClass}-cals-card`"
          @click="executeIfMultipleLinks(calPair)"
        >
          <div>
            <span :class="`am-icon-${calPair.type}`"></span>
          </div>
          <p :style="{color : 'var(--am-c-atc-text)'}">
            {{ calPair.label }}
          </p>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  inject
} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Composables
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'

let props = defineProps({
  labels: {
    type: Object,
    required: true
  },
  booked: {
    type: Object,
    required: true
  },
  ready: {
    type: Boolean,
    required: true
  }
})


let generatedClass = computed(() => {
  if (props.booked.type !== 'event') {
    return 'sbs'
  }

  return 'event'
})

const store = useStore()

let amSettings = computed(() => store.getters['getSettings'])
let ajaxUrl = computed(() => store.getters['getBaseUrls'].wpAmeliaPluginAjaxURL)

let calendars = computed(() => {
  return props.ready && props.booked ?
    [
      [
        getCalendarLinkData(props.booked.data, 'google'),
        {
          type: 'outlook',
          label: 'Outlook',
          links: [
            ajaxUrl.value + '/bookings/ics/' + (props.booked.data.length ? props.booked.data[0].bookingId : 0) +
            '&type=' + (props.booked.type === 'package' ? 'appointment' : props.booked.type) + getRecurringIdsParams(props.booked) +
            '&token=' + props.booked.token
          ]
        }
      ],
      [
        getCalendarLinkData(props.booked.data, 'yahoo'),
        {
          type: 'apple',
          label: 'Apple',
          links: [
            ajaxUrl.value + '/bookings/ics/' + (props.booked.data.length ? props.booked.data[0].bookingId : 0) +
            '&type=' + (props.booked.type === 'package' ? 'appointment' : props.booked.type) + getRecurringIdsParams(props.booked) +
            '&token=' + props.booked.token
          ]
        }
      ]
    ] : []
})

function executeIfMultipleLinks (calendar) {
  if (calendar.links.length > 1) {
    let popupBlocked = false
    setTimeout(function () {
      calendar.links.forEach(function (link, index) {
        if (index !== 0) {
          if (!popupBlocked) {
            let newWin = window.open(link, '_blank')
            try {
              newWin.addEventListener('load', function () {})
            }
            catch (e) {
              popupBlocked = true
              alert(props.labels.disable_popup_blocker)
            }
          } else {
            window.open(link, '_blank')
          }
        }
      })
    }, 1000)
  }

  return true
}

function getRecurringIdsParams (booked) {
  let recurringIdsParams = ''

  if (props.booked.type !== 'event') {
    booked.data.forEach((item, index) => {
      if (index > 0) {
        recurringIdsParams += '&recurring[]=' + item.bookingId
      }
    })
  }

  return recurringIdsParams
}

function getCalendarLinkData(eventData, type) {
  let links = []

  switch (type) {
    case ('yahoo'):
      eventData.forEach(function (data) {
        let location = data.locationId ?
            store.getters['eventEntities/getLocation'](
              data.locationId
            ) : ''

        let address = data.cfAddress ? data.cfAddress : (location ? (location.address ? location.address : location.name) : '')

        let duration = (data.utcEnd.getTime() - data.utcStart.getTime()) / (60 * 1000)

        duration = (duration < 600 ? '0' + Math.floor((duration / 60)) : Math.floor((duration / 60)) + '') +
            (duration % 60 < 10 ? '0' + duration % 60 : duration % 60 + '')

        let startInClientTZ = new Date(data.start)

        let st = formatTime(new Date(startInClientTZ - (startInClientTZ.getTimezoneOffset() * (60 * 1000))))

        links.push(
            encodeURI([
              'http://calendar.yahoo.com/?v=60&view=d&type=20',
              '&title=' + (data.title || ''),
              '&st=' + st,
              '&dur=' + (duration || ''),
              '&desc=' + (data.description || ''),
              '&in_loc=' + address
            ].join(''))
        )
      })

      return {
        type: 'yahoo',
        label: 'Yahoo',
        links: links
      }

    case ('google'):
      eventData.forEach(function (data) {
        let location = data.locationId ?
          store.getters['eventEntities/getLocation'](
            data.locationId
          ) : ''

        let address = data.cfAddress ? data.cfAddress : (location ? (location.address ? location.address : location.name) : '')

        let startTime = formatTime(new Date(data.start))
        let endTime = formatTime(new Date(data.end))

        links.push(
            encodeURI([
              'https://www.google.com/calendar/render',
              '?action=TEMPLATE',
              '&text=' + (data.title || ''),
              '&dates=' + (startTime || ''),
              '/' + (endTime || ''),
              '&details=' + (data.description || ''),
              '&location=' + address,
              '&sprop=&sprop=name:'
            ].join(''))
        )
      })

      return {
        type: 'google',
        label: 'Google',
        links: links
      }
  }
}

function formatTime (date) {
  return date.toISOString().replace(/-|:|\.\d+/g, '')
}

// * Colors
let amColors = inject('amColors')
const cssVars = computed(() => {
  if (props.booked.type !== 'event') {
    return {
      '--am-c-atc-text': amColors.value.colorSbText,
      '--am-c-atc-text-op10': useColorTransparency(amColors.value.colorSbText, 0.1),
      '--am-c-atc-text-op50': useColorTransparency(amColors.value.colorSbText, 0.05)
    }
  }

  return {
    '--am-c-atc-text': amColors.value.colorMainText,
    '--am-c-atc-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-atc-text-op50': useColorTransparency(amColors.value.colorMainText, 0.05)
  }
})
</script>

<script>
export default {
  name: 'AddToCalendar'
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  // atc - add to calendar
  .am-atc {
    // sbs - step by step
    &-sbs {
      //--am-c-atc-text: var(--am-c-sb-text);

      & > p {
        text-align: center;
        font-size: 18px;
        line-height: 20px;
        color: var(--am-c-atc-text);
        font-weight: 400;
        margin-bottom: 16px;
      }

      &-cals {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;

        &-cards {
          display: flex;
          flex-direction: row;
          align-items: center;
          justify-content: center;
          margin-bottom: 8px;
        }

        &-card {
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          width: 86px;
          text-decoration: none;
          color: var(--am-c-atc-text);
          border: 1px solid;
          border-radius: 4px;
          background-color: var(--am-c-atc-text-op50);
          padding: 16px 0 8px;
          margin-right: 8px;
          box-sizing: border-box;
          box-shadow: 0 1px 1px rgba(115, 134, 169, 0.06);

          p {
            font-style: normal;
            font-weight: 500;
            font-size: 15px;
            line-height: 24px;
            margin: 0;
            padding: 0;
          }

          div {
            display: flex;
            height: 24px;
            align-items: center;

            span {
              font-size: 24px;
              color: var(--am-c-atc-text);
            }

            .am-icon-yahoo {
              font-size: 17px
            }
          }

          &:hover {
            background-color: var(--am-c-atc-text-op10);
          }
        }
      }
    }

    // - event
    &-event {
      --am-c-atc-text: var(--am-c-sb-text);

      & > p {
        text-align: center;
        font-size: 18px;
        line-height: 1.55555;
        font-weight: 500;
        color: var(--am-c-atc-text);
        margin-bottom: 16px;
      }

      &-cals {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;

        &-cards {
          display: flex;
          flex-direction: row;
          align-items: center;
          justify-content: center;
          margin-bottom: 8px;
        }

        &-card {
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          width: 86px;
          text-decoration: none;
          color: var(--am-c-atc-text);
          border: 1px solid;
          border-radius: 4px;
          background-color: var(--am-c-atc-text-op50);
          padding: 16px 0 8px;
          margin-right: 8px;
          box-sizing: border-box;
          box-shadow: 0 1px 1px rgba(115, 134, 169, 0.06);

          p {
            font-style: normal;
            font-weight: 500;
            font-size: 15px;
            line-height: 24px;
            margin: 0;
            padding: 0;
          }

          div {
            display: flex;
            height: 24px;
            align-items: center;

            span {
              font-size: 24px;
              color: var(--am-c-atc-text);
            }

            .am-icon-yahoo {
              font-size: 17px
            }
          }

          &:hover {
            background-color: var(--am-c-atc-text-op10);
          }
        }
      }
    }
  }
}
</style>
