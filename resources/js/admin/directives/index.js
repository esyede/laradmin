import Vue from 'vue'
import * as vClickOutside from 'v-click-outside-x'

Vue.use(vClickOutside)

Vue.directive('resize', require('./resize').default)
Vue.directive('can', require('./can').default)
Vue.directive('role-in', require('./role-in').default)
