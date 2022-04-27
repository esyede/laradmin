<template>
  <a-menu
    :selected-keys="activeNames"
    :open-keys.sync="openedMenus"
    mode="inline"
    theme="dark"
  >
    <template v-for="menu of menus">
      <side-menu-item
        :key="menu.id"
        :menu="menu"
        :q="q"
        v-if="menu.menu"
      />
    </template>
  </a-menu>
</template>

<script>
import { hasChildren, makeRouteName } from '@/libs/utils'
import { mapState } from 'vuex'
import _trimEnd from 'lodash/trimEnd'
import _forIn from 'lodash/forIn'
import SideMenuItem from './SideMenuItem'

export default {
  name: 'SideMenu',
  components: {
    SideMenuItem,
  },
  data() {
    return {
      openedMenusBak: [],
      openedMenus: [],
      menus: this.$store.state.vueRouters.vueRouters,
    }
  },
  props: {
    q: String,
  },
  computed: {
    ...mapState({
      collapsed: (state) => !state.sideMenu.opened,
    }),
    activeNames() {
      return this.matchedMenu
        ? this.matchedMenusChain.map((i) => makeRouteName(i.id))
        : this.$route.matched
          .filter((i) => i.meta && i.meta.id)
          .map((i) => makeRouteName(i.meta.id))
    },
    ...mapState({
      miniWidth: (state) => state.miniWidth,
    }),
    matchedMenu() {
      const current = this.$route
      const curPath = _trimEnd(current.path, '/')
      const curQuery = current.query
      const notMatched = '-99999'
      const weightMatchedMap = {}

      this.pathsWithPreSlash.forEach((i) => {
        const path = _trimEnd(i.route.path, '/')
        const query = i.route.query

        if (path === curPath) {
          let weight = 0
          _forIn(query, (value, key) => {
            if (curQuery[key] === value) {
              weight++
            } else {
              weight = notMatched
              return false
            }
          })

          if (weightMatchedMap[weight] === undefined) {
            weightMatchedMap[weight] = i
          }
        }
      })

      const maxWeight = Object.keys(weightMatchedMap).sort((a, b) => b - a)[0]
      if (maxWeight === notMatched) {
        return null
      } else {
        return weightMatchedMap[maxWeight] || null
      }
    },
    matchedMenusChain() {
      const t = []
      let menu = this.matchedMenu
      while (menu) {
        t.unshift(menu)
        menu = menu.parent
      }
      return t
    },
    pathsWithPreSlash() {
      return this.getPathsWithPreSlash()
    },
  },
  methods: {
    onCollapse() {
      this.miniWidth && this.$store.commit('SET_OPENED', false)
    },
    getPathsWithPreSlash(menus = this.menus, parent = null) {
      let t = []

      menus.forEach((i) => {
        const route = (i.path && i.path.indexOf('/') === 0) ? this.$router.resolve(i.path).route : null
        const data = {
          ...i,
          route,
          parent,
        }
        route && t.push(data)

        if (hasChildren(i)) {
          t = t.concat(this.getPathsWithPreSlash(i.children, data))
        }
      })

      return t
    },
  },
  watch: {
    matchedMenusChain: {
      handler(newVal) {
        this.$store.commit('SET_MATCHED_MENUS_CHAIN', newVal)
      },
      immediate: true,
    },
    activeNames: {
      handler(newVal) {
        if (this.collapsed) {
          return
        }
        this.openedMenus = Array.from(new Set(this.openedMenus.concat(...newVal)))
      },
      immediate: true,
    },
    collapsed: {
      async handler(newVal) {
        await this.$nextTick()
        if (newVal) {
          [this.openedMenusBak, this.openedMenus] = [this.openedMenus, []]
        } else {
          this.openedMenus = [...this.activeNames]
        }
      },
      immediate: true,
    },
  },
}
</script>
