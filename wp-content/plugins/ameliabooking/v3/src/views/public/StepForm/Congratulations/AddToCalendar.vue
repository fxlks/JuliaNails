<template>
  <div v-if="settings.general.addToCalendar && booked && booked.data.length" class="am-fs-sb-cs" :style="cssVars">
    <p>{{amLabels.add_to_calendar}}</p>
    <div class="am-fs-sb-cs-cals">
      <div v-for="(cal, index) in calendars" :key="index" class="am-fs-sb-cs-cals-cards">
        <a
          v-for="calPair in cal"
          :key="calPair.type"
          :href="calPair.links[0]"
          :target="(calPair.type === 'apple' || calPair.type === 'outlook') ? '_self' : '_blank'"
          :style="{borderColor : 'var(--am-c-atc-sb-text-op10)'}"
          class="am-fs-sb-cs-cals-card"
          @click="executeIfMultipleLinks(calPair)"
        >
          <div>
            <span :class="`am-icon-${calPair.type}`"></span>
          </div>
          <p :style="{color : 'var(--am-c-atc-sb-text)'}">{{ calPair.label }}</p>
        </a>
      </div>

    </div>
  </div>
</template>

<script setup>
import { computed, inject } from 'vue'
import { useStore } from 'vuex'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation'

import {
  settings,
  ajaxUrl
} from '../../../../plugins/settings.js'

const store = useStore()

// * Labels
const amLabels = inject('amLabels')

let ready = computed(() => store.getters['entities/getReady'])

let booked = computed(() => store.getters['booking/getBooked'])

let calendars = computed(() => {
  return ready.value && booked.value ?
    [
      [
        getCalendarLinkData(booked.value.data, 'google'),
        {
          type: 'outlook',
          label: 'Outlook',
          links: [
            ajaxUrl + '/bookings/ics/' + (booked.value.data.length ? booked.value.data[0].bookingId : 0) +
            '&type=' + (booked.value.type === 'package' ? 'appointment' : booked.value.type) + getRecurringIdsParams(booked.value) +
            '&token=' + booked.value.token
          ]
        }
      ],
      [
        getCalendarLinkData(booked.value.data, 'yahoo'),
        {
          type: 'apple',
          label: 'Apple',
          links: [
            ajaxUrl + '/bookings/ics/' + (booked.value.data.length ? booked.value.data[0].bookingId : 0) +
            '&type=' + (booked.value.type === 'package' ? 'appointment' : booked.value.type) + getRecurringIdsParams(booked.value) +
            '&token=' + booked.value.token
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
              alert(amLabels.value.disable_popup_blocker)
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

  booked.data.forEach((item, index) => {
    if (index > 0) {
      recurringIdsParams += '&recurring[]=' + item.bookingId
    }
  })

  return recurringIdsParams
}

function getCalendarLinkData(eventData, type) {
  let links = []

  switch (type) {
    case ('yahoo'):
      eventData.forEach(function (data) {
        let location = data.locationId?
            store.getters['entities/getLocation'](
              data.locationId
            ) : ''

        let address = data.cfAddress ? data.cfAddress : (location ? (location.address ? location.address : location.name) : '')

        let duration = (data.utcEnd.getTime() - data.utcStart.getTime()) / (60 * 1000)

        duration = (duration < 600 ? '0' + Math.floor((duration / 60)) : Math.floor((duration / 60)) + '') +
            (duration % 60 < 10 ? '0' + duration % 60 : duration % 60 + '')

        let st = formatTime(new Date(data.utcStart - (data.utcStart.getTimezoneOffset() * (60 * 1000))))

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
        let location = data.locationId?
          store.getters['entities/getLocation'](
            data.locationId
          ) : ''

        let address = data.cfAddress ? data.cfAddress : (location ? (location.address ? location.address : location.name) : '')

        let startTime = formatTime(data.utcStart)
        let endTime = formatTime(data.utcEnd)

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

let amColors = inject('amColors')

const cssVars = computed(() => {
  return {
    '--am-c-atc-sb-text-op10': useColorTransparency(amColors.value.colorSbText, 0.1),
    '--am-c-atc-sb-text-op5': useColorTransparency(amColors.value.colorSbText, 0.05)
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
  .am-fs-sb-cs {
    --am-c-atc-sb-text: var(--am-c-sb-text);
    & > p {
      text-align: center;
      font-size: 14px;
      line-height: 20px;
      color: var(--am-c-atc-sb-text);
      font-weight: 400;
      margin-bottom: 16px;
    }
    &-cals{
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      // gap: 8px;
      &-cards {
        display: flex;
        flex-direction: row;
        // gap: 8px;
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
        color: var(--am-c-atc-sb-text);
        border: 1px solid;
        border-radius: 4px;
        background-color: var(--am-c-atc-sb-text-op5);
        padding: 16px 0 8px;
        margin-right: 8px;
        box-sizing: border-box;
        box-shadow: 0 1px 1px rgba(115, 134, 169, 0.06);

        p {
          font-style: normal;
          font-weight: 500;
          font-size: 15px;
          line-height: 24px;
        }
        div {
          display: flex;
          height: 24px;
          align-items: center;
          span {
            font-size: 24px;
            color: var(--am-c-atc-sb-text);
          }
          .am-icon-yahoo {
            font-size: 17px
          }
        }
        &:hover {
          background-color: var(--am-c-atc-sb-text-op10);
        }
      }
    }
  }
}
</style>
