import Axios from 'axios'
import { message } from 'ant-design-vue'
import _trimStart from 'lodash/trimStart'
import {
  debounceMsg,
  getFirstError,
  handleValidateErrors,
  showLoginModal,
} from '@/libs/utils'
import _forIn from 'lodash/forIn'

const config = {
  baseURL: '/admin-api',
  timeout: 30 * 1000,
}

const axios = Axios.create(config)
const CancelToken = Axios.CancelToken

export const requestQueue = {}
const destroyUrlFromQueue = (path) => {
  if (!path) {
    return
  }
  path = path.slice('/admin-api/'.length)
  delete requestQueue[path]
}

const showError = res => {
  const { message: msg } = res.data
  msg && message.error(msg)
}

export const cancelAllRequest = (msg = '') => {
  Object.values(requestQueue).forEach(i => i.source.cancel(msg))
  _forIn(requestQueue, (value, url) => {
    value.source.cancel(msg)
    delete requestQueue[url]
  })
}

axios.interceptors.request.use(
  (config) => {
    const source = CancelToken.source()
    config.cancelToken = source.token

    requestQueue[_trimStart(config.url, '/')] = {
      source,
    }

    if (config.validationForm) {
      config.validationForm[config.validationErrorKey] = {}
    }

    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)

axios.interceptors.response.use(
  (res) => {
    destroyUrlFromQueue(res.config.url)
    return res
  },
  (err) => {
    const { response: res } = err
    const { config } = err

    if (res && !config[`disableHandle${res.status}`]) {
      switch (res.status) {
        case 404:
          message.error('The requested URL does not exist')
          break
        case 401:
          cancelAllRequest('Login failed: ' + config.url)
          message.error('Login expired, please login again')
          if (!config.disableLoginModal) {
            showLoginModal()
          }

          break
        case 400:
          showError(res)
          break
        case 403:
          showError(res)
          cancelAllRequest('Not authorized to access: ' + config.url)
          break
        case 422:
          if (config.showValidationMsg) {
            message.error(getFirstError(res))
          } else if (config.validationForm) {
            config.validationForm[config.validationErrorKey] = handleValidateErrors(res)
          }
          break
        case 429:
          message.error('Too many request, please try again later')
          cancelAllRequest('Too many request')
          break
        default:
          message.error(`Whoops! server error (code: ${res.status})`)
          break
      }
    } else if (!res) {
      if (err instanceof Axios.Cancel) {
        console.log(err.toString())
      } else {
        debounceMsg('Request failed')
      }
    }

    config && destroyUrlFromQueue(config.url)

    return Promise.reject(err)
  },
)

export default class Request {
  method
  args = []
  config = {}
  defaultConfig = {
    showValidationMsg: false,
    validationForm: null,
    validationErrorKey: 'errors',
    disableLoginModal: false,
  }

  methodsWithData = [
    'put', 'patch', 'post',
  ]

  constructor(method, args) {
    this.method = method
    this.args = Array.from(args)
  }

  then(resolve, reject) {
    const configPos = (this.methodsWithData.indexOf(this.method) !== -1) ? 2 : 1

    const args = this.args
    args[configPos] = Object.assign(
      {},
      args[configPos],
      this.defaultConfig,
      this.config,
    )

    axios[this.method](...args).then(resolve, reject)
  }

  setConfig(config) {
    this.config = config
    return this
  }

  static get(url, config) {
    return new Request('get', arguments)
  }

  static delete(url, config) {
    return new Request('delete', arguments)
  }

  static post(url, data, config) {
    return new Request('post', arguments)
  }

  static put(url, data, config) {
    return new Request('put', arguments)
  }

  static patch(url, data, config) {
    return new Request('patch', arguments)
  }

  static request(config) {
    return new Request('request', arguments)
  }
}
