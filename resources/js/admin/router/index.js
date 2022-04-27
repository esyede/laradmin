import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from './routes'
import _get from 'lodash/get'
import _last from 'lodash/last'
import _trim from 'lodash/trim'
import store from '@/store'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import { SYSTEM_BASIC } from '@/libs/constants'
import { loggedIn, removeLoggedIn } from '@/libs/token'
import { cancelAllRequest } from '@/axios/request'

Vue.use(VueRouter)
Vue.prototype.$scrollResolve = () => {}
NProgress.configure({ showSpinner: false })

const router = new VueRouter({
  mode: 'history',
  base: process.env.NODE_ENV === 'development' ? 'admin-dev' : 'admin',
  routes,
  scrollBehavior(to, from, savedPosition) {
    const delayScroll = _last(to.matched)?.components?.default?.scroll

    const pos = savedPosition || { x: 0, y: 0 }

    return delayScroll
      ? new Promise((resolve) => {
        Vue.prototype.$scrollResolve = () => {
          Vue.nextTick(() => {
            resolve(pos)
          })
        }
      })
      : pos
  },
})

const loginRoute = to => {
  const query = {}
  if (_trim(to.path, '/')) {
    query.redirect = to.path
  }
  return {
    name: 'login',
    query,
  }
}

const renameComponent = async (to) => {
  if (!_get(to, 'meta.cache')) {
    return
  }

  let c = router.getMatchedComponents(to)
  c = c[c.length - 1]

  if (c && (c instanceof Function)) {
    c = (await c()).default
    c.name += to.meta.id

    store.commit('ADD_INCLUDE', c.name)
  }
}

router.beforeEach(async (to, from, next) => {
  cancelAllRequest('Page switched, request canceled')

  await renameComponent(to)

  if (to.query._refresh !== undefined) {
    const query = {
      ...to.query,
    }
    delete query._refresh
    next()
    router.replace({
      path: to.path,
      query,
    })
    return
  }

  NProgress.start()

  try {
    if (!store.getters.getConfig(SYSTEM_BASIC.SLUG)) {
      await store.dispatch('getSystemBasicConfigs')
    }
  } catch (e) {
    next(false)
    NProgress.done()
    throw e
  }

  if (loggedIn()) {
    if (to.name === 'login') {
      next('/')
    } else {
      const requests = []
      try {
        const loggedIn = store.getters.loggedIn
        const vueRoutersLoaded = store.state.vueRouters.loaded

        !loggedIn && requests.push(store.dispatch('getUser'))
        !vueRoutersLoaded && requests.push(store.dispatch('getVueRouters'))

        await Promise.all(requests)

        !vueRoutersLoaded ? next(to) : next()
      } catch ({ response: res }) {
        if (res && res.status === 401) {
          removeLoggedIn()
          location.href = router.resolve(loginRoute(to)).href
        } else {
          NProgress.done()
          next(false)
        }
      }
    }
  } else if (to.name !== 'login') {
    next(loginRoute(to))
  } else {
    next()
  }
})

router.afterEach(() => {
  NProgress.done()
  Vue.nextTick(() => {
    const { matchedMenu, appName } = store.getters
    const title = matchedMenu ? matchedMenu.title : _get(router, 'currentRoute.meta.title')
    document.title = `${title ? title + ' - ' : ''} ${appName}`

    const main = document.querySelector('#main')
    main && main.scrollTo({ left: 0, top: 0 })
  })
})

router.onError((e) => {
  NProgress.done()
  throw e
})

export default router
