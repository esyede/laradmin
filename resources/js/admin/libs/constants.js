export const mapConstants = (names) => {
  if (typeof names === 'string') {
    names = [names]
  }
  const mapped = {}
  names.forEach((n) => {
    mapped[n] = () => {
      return eval(n)
    }
  })
  return mapped
}

export const IMAGE_EXTS = [
  'jpg', 'jpeg', 'gif', 'png', 'bmp', 'ico', 'webp', 'svg', 'tiff',
]

export const PERMISSION_PASS_ALL = 'pass-all'

export const ROLE_ADMIN = 'administrator'

export const CONFIG_TYPES = {
  INPUT: 'input',
  TEXTAREA: 'textarea',
  FILE: 'file',
  SINGLE_SELECT: 'single_select',
  MULTIPLE_SELECT: 'multiple_select',
  OTHER: 'other',
}

export const SYSTEM_BASIC = {
  SLUG: 'system_basic',
  APP_NAME_SLUG: 'app_name',
  APP_LOGO_SLUG: 'app_logo',
  HOME_ROUTE_SLUG: 'home_route',
  CDN_DOMAIN_SLUG: 'cdn_domain',
  ADMIN_LOGIN_CAPTCHA_SLUG: 'admin_login_captcha',

  DEFAULT_APP_NAME: 'Admin Backend',
  DEFAULT_HOME_ROUTE: '1',
  DEFAULT_CDN_DOMAIN: '/',
  DEFAULT_ADMIN_LOGIN_CAPTCHA: '1',
}

export const CACHE_AFTER_UPDATE_CONFIG = 'cache-after-update-config'
