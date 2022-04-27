import Vue from 'vue'
import _remove from 'lodash/remove'

const cache = {}
const keys = []


export const removeCacheByName = (name) => {
  if (!name) {
    return
  }

  const key = findKeyByName(name)
  if (!key) {
    return
  }

  cache[key].componentInstance.$destroy()

  delete cache[key]
  _remove(keys, (i) => i === key)
}


const findKeyByName = (name) => {
  for (const key of Object.keys(cache)) {
    const v = cache[key]
    if (v.componentOptions?.Ctor?.options?.name === name) {
      return key
    }
  }
}


export default Object.assign({}, Vue.options.components.KeepAlive, {
  name: 'LzKeepAlive',
  created() {
    this.cache = cache
    this.keys = keys
  },
  destroyed() {},
})
