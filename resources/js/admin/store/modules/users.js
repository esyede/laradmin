import { removeLoggedIn, setLoggedIn } from '@/libs/token'
import { login, logout } from '@/api/auth'
import { getUser } from '@/api/admin-users'
import router from '@/router'

export default {
  state: {
    user: null,
  },
  getters: {
    loggedIn(state) {
      return !!state.user
    },
    userInfo: state => (field, defaultValue = null) => {
      return state.user ? state.user[field] : defaultValue
    },
  },
  mutations: {
    SET_USER(state, user) {
      state.user = user
    },
  },
  actions: {
    async login({ commit }, vm) {
      await login(vm.form).setConfig({ validationForm: vm })
      setLoggedIn()
    },
    async logout({ dispatch }) {
      try {
        await logout().setConfig({ disableHandle401: true })
        dispatch('frontendLogout')
      } catch (e) {
        const { response: res } = e
        if (res && res.status === 401) {
          dispatch('frontendLogout')
        } else {
          throw e
        }
      }
    },
    async getUser({ commit }) {
      const { data } = await getUser().setConfig({ disableLoginModal: true })
      commit('SET_USER', data)
    },
    frontendLogout() {
      removeLoggedIn()
      window.location.href = router.resolve({ name: 'login' }).href
    },
  },
}
