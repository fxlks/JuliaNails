import {longLocale, shortLocale} from "../../../plugins/settings.js";

function checkTranslations (entity, type) {
  let nameTranslations = type !== '' ? JSON.parse(entity.translations)[type] : JSON.parse(entity.translations)

  if (shortLocale in nameTranslations) {
    return nameTranslations[shortLocale]
  }

  let availableTranslations = Object.keys(nameTranslations)
  let availableTranslationsShort = Object.keys(nameTranslations).map(key => key.length > 2 ? key.slice(0, 2) : key)

  let name = ''

  for (let i = 0; i < availableTranslationsShort.length; i++) {
    if (longLocale === availableTranslations[i] && nameTranslations[availableTranslations[i]]) {
      name = nameTranslations[availableTranslations[i]]

      break
    }
    if (availableTranslationsShort[i] === shortLocale && nameTranslations[availableTranslations[i]]) {
      name = nameTranslations[availableTranslations[i]]
    }
  }

  return name
}

function getTranslated (entity, type) {
  if (entity.translations && JSON.parse(entity.translations)[type] && checkTranslations(entity, type)) {
    return checkTranslations(entity, type)
  }

  return entity[type]
}

function getNameTranslated (entity) {
  if (entity.translations && JSON.parse(entity.translations)['name'] && checkTranslations(entity, 'name')) {
    return checkTranslations(entity, 'name')
  }

  return entity.name
}

function getTicketTranslated (entity) {
  if (entity.translations && checkTranslations(entity, '')) {
    return checkTranslations(entity, '')
  }

  return entity.name
}

function getBadgeTranslated (entity) {
  if (entity.translations && checkTranslations(entity, '')) {
    return checkTranslations(entity, '')
  }

  return entity.content
}

function getCfLabelTranslated (entity) {
  if (entity.translations && checkTranslations(entity, 'name')) {
    return checkTranslations(entity, 'name')
  }

  return entity.label
}

function getCfOptionTranslated (entity) {
  if (entity.translations && checkTranslations(entity, '')) {
    return checkTranslations(entity, '')
  }

  return entity.label
}

function getDescriptionTranslated (entity) {
  if (entity.translations && JSON.parse(entity.translations)['description'] && checkTranslations(entity, 'description')) {
    return checkTranslations(entity, 'description')
  }

  return entity.description
}

function useTranslateEntities (entities) {
  if (entities.services && entities.services.length) {
    entities.services.forEach(service => {
      service.name = getNameTranslated(service)
      service.description = getDescriptionTranslated(service)
      service.extras.forEach(extra => {
        extra.name = getNameTranslated(extra)
        extra.description = getDescriptionTranslated(extra)
      })
    })
  }

  if (entities.locations && entities.locations.length) {
    entities.locations.forEach(location => {
      location.name = getNameTranslated(location)
      location.description = getDescriptionTranslated(location)
    })
  }

  if (entities.packages && entities.packages.length) {
    entities.packages.forEach(pack => {
      pack.name = getNameTranslated(pack)
      pack.description = getDescriptionTranslated(pack)
      pack.bookable.forEach(bookable => {
        bookable.service.name = getNameTranslated(bookable.service)
        bookable.service.description = getDescriptionTranslated(bookable.service)
        if ('extras' in bookable.service) {
          bookable.service.extras.forEach(extra => {
            extra.name = getNameTranslated(extra)
            extra.description = getDescriptionTranslated(extra)
          })
        }
      })
    })
  }

  if (entities.categories && entities.categories.length) {
    entities.categories.forEach(category => {
      category.name = getNameTranslated(category)

      category.serviceList.forEach(service => {
        service.name = getNameTranslated(service)
        service.description = getDescriptionTranslated(service)
        service.extras.forEach(extra => {
          extra.name = getNameTranslated(extra)
          extra.description = getDescriptionTranslated(extra)
        })
      })
    })
  }

  if (entities.events && entities.events.length) {
    entities.events.forEach(event => {
      event.name = getNameTranslated(event)
      event.description = getDescriptionTranslated(event)
      event.extras.forEach(extra => {
        extra.name = getNameTranslated(extra)
        extra.description = getDescriptionTranslated(extra)
      })
    })
  }

  if (entities.customFields && entities.customFields.length) {
    entities.customFields.forEach(customField => {
      customField.label = getCfLabelTranslated(customField)
      customField.options.forEach(option => {
        option.label = getCfOptionTranslated(option)
      })
    })
  }

  if (entities.employees && entities.employees.length) {
    entities.employees.forEach(employee => {
      employee.firstName = getTranslated(employee, 'firstName')
      employee.lastName = getTranslated(employee, 'lastName')
      employee.description = getTranslated(employee, 'description')
    })
  }
}

export {
  getNameTranslated,
  getDescriptionTranslated,
  getTicketTranslated,
  getBadgeTranslated,
  useTranslateEntities
}
