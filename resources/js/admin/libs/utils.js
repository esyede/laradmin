import _trim from 'lodash/trim'
import ParentView from '@c/ParentView'
import Layout from '@c/Layout'
import pages from '@v/pages'
import Page404 from '@v/errors/Page404'
import _debounce from 'lodash/debounce'
import { message } from 'ant-design-vue'
import store from '@/store'
import _get from 'lodash/get'
import { PERMISSION_PASS_ALL, ROLE_ADMIN, SYSTEM_BASIC } from '@/libs/constants'
import _intersection from 'lodash/intersection'
import { isExternal, isInt } from '@/libs/validates'
import _trimStart from 'lodash/trimStart'
import _trimEnd from 'lodash/trimEnd'
import GlobalModal from '@c/GlobalModal'
import LoadingAction from '@c/LoadingAction'
import LoginForm from '@c/LoginForm'
import Space from '@c/Space'


export const handleValidateErrors = (res) => {
  let errors = {}
  if (res && (res.status === 422 || res.status === 429)) {
    ({ errors } = res.data)
    if (!errors) {
      return {}
    }
    Object.keys(errors).forEach((k) => {
      errors[k] = errors[k][0]
    })
  }

  return errors
}

export const hasChildren =
  (item, childrenKey = 'children') =>
    Array.isArray(item[childrenKey]) && item[childrenKey].length > 0

export const buildRoutes = (routers, homeName, level = 0) => {
  let homeRoute = null
  const handle = (routers, homeName, level = 0) => {
    const routes = []
    routers.forEach((routerData) => {
      routerData.path = routerData.path || ''

      if ((routerData.path.startsWith('/') && !hasChildren(routerData)) || isExternal(routerData.path)) {
        return
      }

      let path = routerData.path
      if (path) {
        path = path.startsWith('/') ? path : `/${routerData.path}`
      }
      let route = {
        path,
        name: makeRouteName(routerData.id),
        component: pages[routerData.path] || Page404,
        meta: {
          title: routerData.title,
          cache: !!routerData.cache,
          isMenu: !!routerData.menu,
          id: routerData.id,
        },
      }

      if (hasChildren(routerData)) {
        route.children = handle(routerData.children || [], homeName, level + 1)
      }

      if (hasChildren(routerData)) {
        route.component = ParentView
        if (route.children.length) {
          route.path = route.children[0].path
        }
      }

      if (route.name === homeName) {
        homeRoute = route
      }

      if (level === 0) {
        route = {
          path: '/',
          component: Layout,
          children: [route],
        }
        if (homeName) {
          route.redirect = { name: homeName }
        }
        routes.push(route)
      } else {
        routes.push(route)
      }
    })
    return routes
  }
  const routes = handle(routers, homeName, level = 0)
  return {
    routes,
    homeRoute,
  }
}

export const makeRouteName = unique => 'routes-' + unique

export const startSlash = path => '/' + _trim(path, '/')

export const randomChars = () => Math.random().toString(36).substring(7)

export const nestedToSelectOptions = (items, props = {}) => {
  const defaultProps = {
    id: 'id',
    title: 'title',
    children: 'children',
    firstLevel: {
      id: 0,
      title: 'None',
      text: 'None',
    },
  }
  props = Object.assign({}, defaultProps, props)

  const _build = (items, indent) => {
    const options = []
    items.forEach(i => {
      options.push({
        id: i[props.id],
        text: '　'.repeat(indent) + i[props.title],
        title: i[props.title],
      })
      if (hasChildren(i)) {
        options.push(..._build(i[props.children], indent + 2))
      }
    })
    return options
  }

  const res = _build(items, props.firstLevel ? 2 : 0)
  props.firstLevel && res.unshift(props.firstLevel)
  return res
}

export const assignExists = (target, source, force = false) => {
  const res = {}
  for (const k of Object.keys(target)) {
    if (hasOwnProperty(source, k) || force) {
      res[k] = source[k]
    } else {
      res[k] = target[k]
    }
  }

  return res
}

export const getFirstError = (res) => {
  if (res.status === 422) {
    return Object.values(res.data.errors)[0][0]
  } else {
    return ''
  }
}

export const getMessage = key => {
  return messages[key] || messages.default
}

export const removeFromNested = (items, identify, identifyKey = 'id', childrenKey = 'children') => {
  for (const i in items) {
    const item = items[i]
    if (item[identifyKey] === identify) {
      items.splice(i, 1)
      return true
    }

    if (
      hasChildren(item, childrenKey) &&
      removeFromNested(item[childrenKey], identify, identifyKey, childrenKey)
    ) {
      return true
    }
  }
  return false
}


export const getExt = (filename) => {
  const t = filename.split('.')
  return t.length <= 1
    ? ''
    : t[t.length - 1]
}

const _debounceMsg = () => {
  const t = _debounceMsg.type || 'error'
  _debounceMsg.msg && (message[t])(_debounceMsg.msg)
}

const debouncedMsg = _debounce(_debounceMsg, 10)


export const debounceMsg = (msg, type = 'error') => {
  _debounceMsg.msg = msg
  _debounceMsg.type = type
  debouncedMsg()
}


export const can = (permissions) => {
  const userPerms = _get(store, 'state.users.user.permissions', [])

  if (userPerms.indexOf(PERMISSION_PASS_ALL) !== -1) {
    return true
  }

  if (typeof permissions === 'string') {
    permissions = permissions.split(',')
  } else if (!Array.isArray(permissions)) {
    throw new Error('Must be an array of permissions, or a comma-separated string of permissions')
  }

  return _intersection(userPerms, permissions).length === permissions.length
}

export const roleIn = (roles) => {
  const userRoles = _get(store, 'state.users.user.roles', [])

  if (userRoles.indexOf(ROLE_ADMIN) !== -1) {
    return true
  }

  if (typeof roles === 'string') {
    roles = roles.split(',')
  } else if (!Array.isArray(roles)) {
    throw new Error('Must be an array of roles, or a comma-separated string of roles')
  }

  return _intersection(userRoles, roles).length > 0
}


export function toInt(val, defaultVal = 0) {
  if (isInt(val)) {
    return Number(val)
  } else {
    return defaultVal
  }
}


export function getUrl(path) {
  if (!path) {
    return ''
  }
  const { SLUG, CDN_DOMAIN_SLUG, DEFAULT_CDN_DOMAIN } = SYSTEM_BASIC
  const CDNDomain = store.getters.getConfig(SLUG + '.' + CDN_DOMAIN_SLUG, DEFAULT_CDN_DOMAIN)
  return _trimEnd(CDNDomain, '/') + '/' + _trimStart(path, '/')
}


export function showLoginModal() {
  GlobalModal.new({
    components: {
      LoadingAction,
      LoginForm,
      Space,
    },
    propsData: {
      title: 'Login',
      width: '350px',
      bodyStyle: {
        paddingBottom: '8px',
      },
      content(h) {
        return <login-form ref="form"/>
      },
      lzFooter(h) {
        return (
          <div>
            <a-button v-on:click={this.close}>Close</a-button>
            <a-button v-on:click={() => {
              this.$store.dispatch('frontendLogout')
              this.close()
            }}>Logout
            </a-button>
            <loading-action type="primary" action={async () => {
              await this.$refs.form.onSubmit()
              this.close()
            }}>
              登录
            </loading-action>
          </div>
        )
      },
    },
  })
}


export function arrayWrap(val) {
  if (!val) {
    return []
  } else if (Array.isArray(val)) {
    return val
  } else {
    return [val]
  }
}


export function jsonParse(str, defaultValue = undefined) {
  try {
    return JSON.parse(str)
  } catch (e) {
    return defaultValue
  }
}


export function removeWhile(array, callback) {
  const res = []
  array.forEach((i) => {
    !callback(i) && res.push(i)
  })

  return res
}


export function hasOwnProperty(obj, key) {
  return obj === undefined ? false : Object.prototype.hasOwnProperty.call(obj, key)
}


export function requireAll(requireContext) {
  return requireContext.keys().map(requireContext)
}


export function stringBool(val) {
  const map = {
    0: false,
    1: true,
    false: false,
    true: true,
  }

  if (hasOwnProperty(map, val)) {
    return map[val]
  }

  return Boolean(val)
}
