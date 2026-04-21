import axios from 'axios'

const baseURL = window.wpAmeliaUrls.wpAmeliaPluginAjaxURL

const params = {
  wpAmeliaNonce: window.wpAmeliaNonce
}

const httpClient = axios.create({
  baseURL,
  params
})

export default httpClient
