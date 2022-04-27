import Axios from 'axios'
import _get from 'lodash/get'
import Vue from 'vue'

const isNetworkError = err => {
  return _get(err, 'response.status') ||
    (err.message === 'Network Error') ||
    (err instanceof Axios.Cancel)
}

window.addEventListener('unhandledrejection', function (event) {
  if (isNetworkError(event.reason)) {
    event.preventDefault()
  }
})

Vue.config.errorHandler = (err, vm, info) => {
  if (!isNetworkError(err)) {
    throw err
  }
}
