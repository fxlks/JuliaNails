import {settings} from '../../../plugins/settings.js'

const thousandSeparatorMap = {
      1: ',',
      2: '.',
      3: ' ',
      4: ' '
}

const decimalSeparatorMap = {
      1: '.',
      2: ',',
      3: '.',
      4: ','
}


function getCurrencySymbol () {
  return settings.payments.currency
}

function getPriceThousandSeparator () {
  return thousandSeparatorMap[settings.payments.priceSeparator]
}

function getPriceDecimalSeparator () {
  return decimalSeparatorMap[settings.payments.priceSeparator]
}

function getPriceNumberOfDecimalPlaces () {
  return settings.payments.priceNumberOfDecimals
}

function getPricePrefix () {
  if (settings.payments.priceSymbolPosition === 'after' || settings.payments.priceSymbolPosition === 'afterWithSpace') {
    return ''
  } else if (settings.payments.priceSymbolPosition === 'before') {
    return getCurrencySymbol()
  } else {
    return getCurrencySymbol() + ' '
  }
}

function getPriceSuffix () {
  if (settings.payments.priceSymbolPosition === 'before' || settings.payments.priceSymbolPosition === 'beforeWithSpace') {
    return ''
  } else if (settings.payments.priceSymbolPosition === 'after') {
    return getCurrencySymbol()
  } else {
    return ' ' + getCurrencySymbol()
  }
}

function useFormattedPrice (price) {
  let showCurrency = !settings.payments.hideCurrencySymbolFrontend
  let decimalPlaces = getPriceNumberOfDecimalPlaces()
  let thousandSeparator = getPriceThousandSeparator()
  let decimalSeparator = getPriceDecimalSeparator()
  let pricePrefix = getPricePrefix()
  let priceSuffix = getPriceSuffix()

  let i = parseInt(price = Math.abs(+price || 0).toFixed(decimalPlaces)) + ''
  let j = i.length > 3 ? i.length % 3 : 0

  return (showCurrency ? pricePrefix : '') + (j ? i.substr(0, j) + thousandSeparator : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousandSeparator) + (decimalPlaces ? decimalSeparator + Math.abs(price - i).toFixed(decimalPlaces).slice(2) : '') + (showCurrency ? priceSuffix : '')
}

function capitalizeFirstLetter (string) {
  return string.charAt(0).toUpperCase() + string.slice(1)
}

function useRandomIntFromInterval (min, max) {
  // min and max included
  return Math.floor(Math.random() * (max - min + 1) + min)
}

function useCurrencyOptions () {
  return  {
    thousands: getPriceThousandSeparator(),
    decimal: getPriceDecimalSeparator(),
    precision: getPriceNumberOfDecimalPlaces(),
    suffix: getPriceSuffix(),
    prefix: getPricePrefix(),
  }
}

export {
  useCurrencyOptions,
  useFormattedPrice,
  useRandomIntFromInterval,
  capitalizeFirstLetter,
  getCurrencySymbol,
}
