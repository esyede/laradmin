<template>
  <transition-group class="ant-breadcrumb" tag="div" name="breadcrumb">
    <a-breadcrumb-item v-for="i of breadCrumb" :key="i.id">
      <router-link v-if="i.path" :to="i.path">{{ i.title }}</router-link>
      <span v-else>{{ i.title }}</span>
    </a-breadcrumb-item>
  </transition-group>
</template>

<script>
import { mapState } from 'vuex'
import { randomChars } from '@/libs/utils'

export default {
  name: 'Breadcrumb',
  computed: {
    ...mapState({
      homeRoute: (state) => state.vueRouters.homeRoute,
      matchedMenusChain: (state) => state.matchedMenusChain,
    }),
    homeName() {
      return this.$store.getters.homeName
    },
    breadCrumb() {
      const m = this.matchedMenusChain.length
        ? [...this.matchedMenusChain]
        : this.$route.matched
          .filter((i) => i.name)
          .map((i) => ({
            id: i.meta.id || randomChars(),
            title: i.meta.title,
            path: i.path,
          }))

      if (m.length === 0 || m[m.length - 1].id !== this.homeRoute.meta.id) {
        m.unshift({
          ...this.homeRoute.meta,
          path: this.homeRoute.path,
        })
      }

      m[m.length - 1].path = null

      return m
    },
  },
}
</script>

<style lang="less">
/* breadcrumb transition */
.breadcrumb-enter-active,
.breadcrumb-leave-active {
  transition: all .5s;
}

.breadcrumb-enter,
.breadcrumb-leave-active {
  opacity: 0;
  transform: translateX(20px);
}

.breadcrumb-move {
  transition: all .5s;
}

.breadcrumb-leave-active {
  position: absolute;
}
</style>
