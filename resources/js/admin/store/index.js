import Vue from 'vue'
import Vuex from 'vuex'

import users from './modules/users'
import vueRouters from './modules/vue-routers'
import sideMenu from './modules/side-menu'
import config from './modules/config'

import _last from 'lodash/last'

Vue.use(Vuex)

const cachedKeys = {
  ParentView: true,
}

export default new Vuex.Store({
  modules: {
    users,
    vueRouters,
    sideMenu,
    config,
  },
  state: {
    miniWidth: window.innerWidth <= 768,
    tinyWidth: window.innerWidth <= 575,
    include: [],
    matchedMenusChain: [],
  },
  mutations: {
    SET_MINI_WIDTH(state, payload) {
      state.miniWidth = payload
    },
    SET_TINY_WIDTH(state, payload) {
      state.tinyWidth = payload
    },
    ADD_INCLUDE(state, name) {
      if (cachedKeys[name]) {
        return
      }

      cachedKeys[name] = true
      state.include.push(name)
    },
    SET_MATCHED_MENUS_CHAIN(state, menus) {
      state.matchedMenusChain = menus
    },
  },
  getters: {
    matchedMenu(state) {
      return _last(state.matchedMenusChain) || null
    },
  },
})
