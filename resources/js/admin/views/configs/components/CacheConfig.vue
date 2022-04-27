<template>
  <loading-action :action="cacheConfig" title="Empty and re-cache config">Re-cache</loading-action>
</template>

<script>
import LoadingAction from '@c/LoadingAction'
import { cacheConfig } from '@/api/configs'

export default {
  name: 'CacheConfig',
  components: {
    LoadingAction,
  },
  methods: {
    cacheConfig,
  },
}
</script>
