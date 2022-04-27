import Page404 from '@v/errors/Page404'
import { randomChars } from '@/libs/utils'
import Layout from '@c/Layout'

const randomPath = '/' + randomChars()

export default [
  {
    path: '/login',
    name: 'login',
    meta: {
      title: 'Login',
    },
    component: () => import('@v/Login'),
  },
  {
    path: randomPath,
    component: Layout,
    children: [
      {
        path: '/user/edit',
        name: 'editMyProfile',
        meta: {
          title: 'Edit Profile',
        },
        component: () => import('@v/admin-users/EditProfile'),
      },
    ],
  },
]

export const pageNotFoundRoute = {
  path: '*',
  meta: {
    title: 'Page Not Found',
  },
  component: Page404,
}

export const appendRoutes = [
  {
    path: randomPath,
    component: Layout,
    children: [
      {
        path: '/configs/:categorySlug',
        name: 'updateConfigForm',
        component: () => import('@v/configs/ConfigValuesForm'),
      },
    ],
  },
  pageNotFoundRoute,
]
