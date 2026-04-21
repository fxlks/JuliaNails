import moment from "moment";
import {DateTime, Settings, Info} from "luxon";
import {settings, shortLocale} from "../../../plugins/settings.js";
import {useCookies} from "vue3-cookies";
import {useUrlQueryParams} from "./helper";

Settings.defaultLocale = shortLocale

const months = []
const weekDaysLocale = []
const weekDaysShortLocale = []

for (let i = 1; i <= 12; i++) {
  months.push(DateTime.local(2022, i, 1).monthLong)
  if (i <= 7) {
    weekDaysLocale.push(Info.weekdays('long')[i-1])
    weekDaysShortLocale.push(Info.weekdays('short')[i-1])
  }
}

const formatRegex = {
  formatPHPtoJsMap: {
    d: 'dd',
    D: 'ccc',
    j: 'd',
    l: 'cccc',
    N: 'c',
    w: 'c',
    W: 'W',
    F: 'MMMM',
    m: 'MM',
    M: 'MMM',
    n: 'M',
    o: 'kkkk',
    Y: 'yyyy',
    y: 'yy',
    a: 'a',
    A: 'a',
    g: 'h',
    G: 'H',
    h: 'hh',
    H: 'HH',
    i: 'mm',
    s: 'ss',
    O: 'ZZZ',
    P: 'ZZ',
    c: 'yyyy-MM-dd[T]HH:mm:ssZZ',
    r: 'ccc, dd MMM yyyy HH:mm:ss ZZZ',
    U: 'X',
    T: '',
    S: 'o'
  },

  formatPHPtoMomentMap: {
    d: 'DD',
    D: 'ddd',
    j: 'D',
    l: 'dddd',
    N: 'E',
    w: 'd',
    W: 'W',
    F: 'MMMM',
    m: 'MM',
    M: 'MMM',
    n: 'M',
    o: 'GGGG',
    Y: 'YYYY',
    y: 'YY',
    a: 'a',
    A: 'A',
    g: 'h',
    G: 'H',
    h: 'hh',
    H: 'HH',
    i: 'mm',
    s: 'ss',
    O: 'ZZ',
    P: 'Z',
    c: 'YYYY-MM-DD[T]HH:mm:ssZ',
    r: 'ddd, DD MMM YYYY HH:mm:ss ZZ',
    U: 'X',
    T: '',
    S: 'o'
  },

  formatEx: /[dDjlNwWFmMntoYyaAgGhHisOPcrUTS]/g
}

function useLocalValue (value) {
  return moment.utc(value, 'YYYY-MM-DD HH:mm').local().format('YYYY-MM-DD HH:mm')
}

function useUtcValue (value) {
  return moment(value, 'YYYY-MM-DD HH:mm').utc().format('YYYY-MM-DD HH:mm')
}

function useUtcValueOffset (value) {
  return value ? moment(value, 'YYYY-MM-DD HH:mm:ss').utcOffset() : moment().utcOffset()
}

function useStringFromDate (d) {
  return d.getFullYear() + '-' + ('0'.concat(d.getMonth() + 1)).slice(-2) + '-' + ('0'.concat(d.getDate())).slice(-2)
}

function useFormatTime (t) {
  return t.split(':')[0] + ':' + t.split(':')[1]
}

function useTimeInSeconds (time) {
  return moment(time, 'HH:mm').diff(moment().startOf('day'), 'seconds')
}

function useSecondsInTime (seconds) {
  return moment.utc(seconds * 1000).format('HH:mm')
}

function addSeconds (time, seconds) {
  return moment(time, 'HH:mm').add(seconds, 'seconds').format('HH:mm')
}

// * Luxon time format
function jsTimeFormat () {
  // Fix for French "G \h i \m\i\n" and "G\hi" format
  if (settings.wordpress.timeFormat === 'G \\h i \\m\\i\\n' || settings.wordpress.timeFormat === 'G\\hi') {
    return 'HH:mm'
  }

  return settings.wordpress.timeFormat.replace(formatRegex.formatEx, function (phpStr) {
    return formatRegex.formatPHPtoJsMap[phpStr]
  })
}

// * Moment time format
function momentTimeFormat () {
  // Fix for French "G \h i \m\i\n" and "G\hi" format
  if (settings.wordpress.timeFormat === 'G \\h i \\m\\i\\n' || settings.wordpress.timeFormat === 'G\\hi') {
    return 'HH:mm'
  }

  return settings.wordpress.timeFormat.replace(formatRegex.formatEx, function (phpStr) {
    return formatRegex.formatPHPtoMomentMap[phpStr]
  })
}

function jsDateFormat () {
  // Fix for Portuguese "j \d\e F, Y" format
  if (settings.wordpress.dateFormat === 'j \\d\\e F, Y') {
    return 'd MMMM, yyyy'
  }

  // Fix for Spanish/Catalan "j \d\e F \d\e Y" format
  if (settings.wordpress.dateFormat === 'j \\d\\e F \\d\\e Y') {
    return 'd MMMM yyyy'
  }

  // Remove ordinal suffix (S) as it's not supported in all languages
  let format = settings.wordpress.dateFormat.replace(/S/g, '')

  return format.replace(formatRegex.formatEx, function (phpStr) {
    return formatRegex.formatPHPtoJsMap[phpStr]
  })
}

function momentDateFormat () {
  // Fix for Portuguese "j \d\e F, Y" format
  if (settings.wordpress.dateFormat === 'j \\d\\e F, Y') {
    return 'D MMMM, YYYY'
  }

  // Fix for Spanish/Catalan "j \d\e F \d\e Y" format
  if (settings.wordpress.dateFormat === 'j \\d\\e F \\d\\e Y') {
    return 'D MMMM YYYY'
  }

  return settings.wordpress.dateFormat.replace(formatRegex.formatEx, function (phpStr) {
    return formatRegex.formatPHPtoMomentMap[phpStr]
  })
}

function getFrontedFormattedDateTime (dateTime) {
  let data = dateTime.split(' ')

  return getFrontedFormattedDate(data[0]) + ' ' + getFrontedFormattedTime(data[1].substring(0, 5))
}

function getFrontedFormattedTime (time, zone = '') {
  return zone
    ? DateTime.fromFormat(time, 'HH:mm', { zone: zone }).toFormat(jsTimeFormat())
    : DateTime.fromFormat(time, 'HH:mm').toFormat(jsTimeFormat())
}

function getFrontedFormattedDate (date) {
  let dateString = DateTime.fromFormat(date, 'yyyy-MM-dd').toFormat(jsDateFormat())

  // Fix for Portuguese "j \d\e F, Y" format
  if (settings.wordpress.dateFormat === 'j \\d\\e F, Y') {
    let result = ''

    dateString.split(' ').forEach(function (value, index) {
      if (index === 1) {
        value = value.charAt(0).toUpperCase() + value.slice(1)
      }

      result = result + value + ' '

      if (index === 0) {
        result += 'de '
      }
    })

    return result
  }

  // Fix for Spanish/Catalan "j \d\e F \d\e Y" format
  if (settings.wordpress.dateFormat === 'j \\d\\e F \\d\\e Y') {
    let result = ''

    let format = !dateString.includes(' de ') ? dateString : dateString.replace('de ', '')

    format.split(' ').forEach(function (value, index) {
      if (index === 1 || index === 3) {
        value = value.charAt(0).toUpperCase() + value.slice(1)
      }

      result = result + value + ' '

      if (index === 0 || index === 1) {
        result += 'de '
      }
    })

    return result
  }

  return dateString
}

function getFirstDayOfWeek () {
  return settings.wordpress.startOfWeek
}

function useSecondsToDuration (seconds, hourLabel, minuteLabel) {
  let hours = Math.floor(seconds / 3600)
  let minutes = seconds / 60 % 60

  return (hours ? (hours + hourLabel + ' ') : '') + ' ' + (minutes ? (minutes + minuteLabel) : '')
}

function useConvertedUtcToLocalDateTime (period) {
  let utcOffset = moment(period, 'YYYY-MM-DD HH:mm:ss').toDate().getTimezoneOffset()

  if (utcOffset > 0) {
    return moment.utc(period, 'YYYY-MM-DD HH:mm:ss').subtract(utcOffset, 'minutes').format('YYYY-MM-DD HH:mm:ss')
  } else {
    return moment.utc(period, 'YYYY-MM-DD HH:mm:ss').add(-1 * utcOffset, 'minutes').format('YYYY-MM-DD HH:mm:ss')
  }
}

function getEventFrontedFormattedDateDay (date) {
  return DateTime.fromFormat(date, 'yyyy-MM-dd').toFormat('dd')
}

function getEventFrontedFormattedDateMonth (date) {
  return DateTime.fromFormat(date, 'yyyy-MM-dd').toFormat('LLL')
}

function getEventFrontedFormattedTime (time) {
  return moment(time, 'HH:mm:ss').format(momentTimeFormat())
}

function getDatePickerInitRange () {
  const vueCookies = useCookies()['cookies']

  let ameliaRangePast   = vueCookies.get('ameliaRangePast')
  let ameliaRangeFuture = vueCookies.get('ameliaRangeFuture')

  if (ameliaRangePast !== null && ameliaRangeFuture !== null) {
    return [
      moment().subtract(ameliaRangePast, 'days').toDate(),
      moment().add(ameliaRangeFuture, 'days').toDate()
    ]
  }

  return [
    moment().toDate(),
    moment().add(6, 'days').toDate()
  ]
}

function setDatePickerSelectedDaysCount (start, end) {
  const vueCookies = useCookies()['cookies']
  let currentDate = moment().format('YYYY-MM-DD')

  vueCookies.set('ameliaRangePast', moment(currentDate, 'YYYY-MM-DD').diff(moment(start, 'YYYY-MM-DD'), 'days'))
  vueCookies.set('ameliaRangeFuture', moment(end, 'YYYY-MM-DD').diff(moment(currentDate, 'YYYY-MM-DD'), 'days'))
}

function getDateRange (cabinetType) {
  let queryParams = useUrlQueryParams(window.location.href)

  let start = 'start' in queryParams ? queryParams['start'] : null
  let end = 'end' in queryParams ? queryParams['end'] : null

  if (start && end) {
    return [
      moment(start).toDate(),
      moment(end).toDate()
    ]
  }

  if ('ameliaBooking' in window && 'cabinet' in window['ameliaBooking'] && 'pastDays' in window['ameliaBooking']['cabinet'] && 'futureDays' in window['ameliaBooking']['cabinet']) {
    return [
      moment().subtract(window['ameliaBooking']['cabinet']['pastDays'], 'days').toDate(),
      moment().add(window['ameliaBooking']['cabinet']['futureDays'], 'days').toDate()
    ]
  }
  // if it's customer set date range to numberOfDaysAvailableForBooking
  if (cabinetType === 'customer') {
    return [moment().toDate(), moment().add(settings.general.numberOfDaysAvailableForBooking, 'days').toDate()]
  }

  return getDatePickerInitRange()
}

export {
  weekDaysLocale,
  weekDaysShortLocale,
  months,
  useUtcValue,
  useLocalValue,
  useUtcValueOffset,
  useStringFromDate,
  useFormatTime,
  useTimeInSeconds,
  useSecondsInTime,
  getFrontedFormattedTime,
  getFrontedFormattedDate,
  getFrontedFormattedDateTime,
  addSeconds,
  getFirstDayOfWeek,
  useSecondsToDuration,
  useConvertedUtcToLocalDateTime,
  getEventFrontedFormattedDateDay,
  getEventFrontedFormattedDateMonth,
  getEventFrontedFormattedTime,
  getDatePickerInitRange,
  jsDateFormat,
  momentDateFormat,
  setDatePickerSelectedDaysCount,
  getDateRange
}
