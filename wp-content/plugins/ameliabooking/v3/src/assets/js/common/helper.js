import { settings } from "../../../plugins/settings.js"

function useCurrentTimeZone () {
  return Intl.DateTimeFormat().resolvedOptions().timeZone
}

function useRemoveUrlParameter (url, parameter) {
  let urlParts = url.split('?')

  if (urlParts.length >= 2) {
    let prefix = encodeURIComponent(parameter) + '='
    let pars = urlParts[1].split(/[&;]/g)

    for (let i = pars.length; i-- > 0;) {
      if (pars[i].lastIndexOf(prefix, 0) !== -1) {
        pars.splice(i, 1)
      }
    }

    url = urlParts[0] + (pars.length > 0 ? '?' + pars.join('&') : '')
  }

  return url
}

function useUrlParams (params) {
  if (!settings.activation.disableUrlParams) {
    return params
  }

  let names = [
    'categories',
    'services',
    'packages',
    'employees',
    'providers',
    'providerIds',
    'extras',
    'locations',
    'events',
    'types',
    'dates',
    'customers',
    'providers',
    'services',
    'locations',
    'status',
  ]

  let convertedParams = JSON.parse(JSON.stringify(params))

  names.forEach((name) => {
    if (name === 'extras' && name in convertedParams && convertedParams['extras']) {
      convertedParams['extras'] = JSON.parse(convertedParams['extras'])

      let extras = []

      convertedParams['extras'].forEach((item) => {
        extras.push(item.id + '-' + item.quantity)
      })

      convertedParams['extras'] = extras.length ? extras : null
    }

    if (name in convertedParams && Array.isArray(convertedParams[name]) && convertedParams[name].length) {
      convertedParams[name] = convertedParams[name].join(',')
    }
  })

  return convertedParams
}

function useSortedDateStrings (dates) {
  return dates.sort((a,b) =>  new Date(a) - new Date(b))
}

function useSortedTimeStrings (times) {
  return times.sort((a,b) =>  new Date(`2000-01-01T${a}`) - new Date(`2000-01-01T${b}`))
}

function useUrlQueryParams (url) {
  let queryString = url.indexOf('#') > 0
    ? url.substring(0, url.indexOf('#')).split('?')[1]
    : url.split('?')[1]

  if (queryString) {
    let keyValuePairs = queryString.split('&')
    let keyValue = []
    let queryParams = {}

    keyValuePairs.forEach(function (pair) {
      keyValue = pair.split('=')
      queryParams[keyValue[0]] = decodeURIComponent(keyValue[1]).replace(/\+/g, ' ')
    })

    return queryParams
  }

  // ! return null
  return {}
}

function useUrlQueryParam (param) {
  let queryParams = useUrlQueryParams(window.location.href)

  return param in queryParams ? queryParams[param] : null
}

function useDescriptionVisibility (text) {
  if (text && text.length) {
    return !text.includes('<!-- Content -->') || (text.includes('<!-- Content -->') && text.length > 16)
  }

  return false
}
function mapAddressComponentsForXML(components) {
  const result = {};

  components.forEach(component => {
    const types = component.types;

    if (types.includes('street_number')) {
      result.BuildingNumber = component.long_name;
    }

    if (types.includes('route')) {
      result.StreetName = component.long_name;
    }

    if (types.includes('postal_code')) {
      result.PostalZone = component.long_name;
    }

    if (types.includes('locality')) {
      result.CityName = component.long_name;
    }

    if (types.includes('administrative_area_level_1')) {
      result.CountrySubentity = component.long_name;
    }

    if (types.includes('administrative_area_level_2') && !result.CountrySubentity) {
      result.CountrySubentity = component.long_name;
    }

    if (types.includes('country')) {
      result.CountryCode = component.short_name;
    }

    if (types.includes('premise')) {
      result.Premise = component.long_name;
    }

    if (types.includes('sublocality') || types.includes('neighborhood')) {
      result.AdditionalStreetName = component.long_name;
    }
  });

  return result;
}

function createFileUrlFromResponse (response, format = 'pdf') {
  const byteCharacters = atob(response.data)
  const byteNumbers = new Array(byteCharacters.length)
  for (let i = 0; i < byteCharacters.length; i++) {
    byteNumbers[i] = byteCharacters.charCodeAt(i)
  }
  const byteArray = new Uint8Array(byteNumbers)
  const file = new Blob([byteArray], { type: `application/${format};base64` })
  return URL.createObjectURL(file)
}

export {
  useRemoveUrlParameter,
  useSortedDateStrings,
  useUrlParams,
  useUrlQueryParams,
  useUrlQueryParam,
  useCurrentTimeZone,
  useSortedTimeStrings,
  useDescriptionVisibility,
  createFileUrlFromResponse,
  mapAddressComponentsForXML
}
