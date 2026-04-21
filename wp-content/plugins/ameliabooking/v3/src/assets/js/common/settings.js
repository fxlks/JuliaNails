import httpClient from "../../../plugins/axios";
import { settings } from '../../../plugins/settings.js'
import { useAuthorizationHeaderObject } from "../public/panel";

function usePopulateSettings (globalSettings, defaultSettings, savedSettings = {}, resultSettings = {}) {
  for (let key in defaultSettings) {
    if (key in globalSettings && defaultSettings[key] && typeof defaultSettings[key] === 'object') {
      if (!(key in resultSettings)) {
        resultSettings[key] = {}
      }

      usePopulateSettings(
        globalSettings[key],
        defaultSettings[key],
        key in savedSettings ? savedSettings[key] : globalSettings[key],
        resultSettings[key]
      )

      if (Object.keys(resultSettings[key]).length === 0) {
        delete resultSettings[key]
      }
    } else if (key in savedSettings) {
      resultSettings[key] = savedSettings[key]
    } else if (key in globalSettings) {
      resultSettings[key] = globalSettings[key]
    } else {
      resultSettings[key] = defaultSettings[key]
    }
  }

  return resultSettings
}

function useUpdateStashEntities (store) {
  if (settings.activation.stash) {
    httpClient.post(
      '/stash',
      {},
      Object.assign(
        useAuthorizationHeaderObject(store),
        {
          params: {
            source: 'cabinet-provider',
          },
        }
      )
    ).then(() => {
    }).catch(() => {
    })
  }
}

export {
  usePopulateSettings,
  useUpdateStashEntities,
}
