<template>
  <a-form>
    <template v-if="type === this.CONFIG_TYPES.FILE">
      <a-form-item label="Max uploads">
        <a-input-number v-model="value.max" :min="1" :max="99"/>
      </a-form-item>
      <a-form-item label="File type">
        <a-input v-model="value.ext" placeholder="Separated with commas"/>
      </a-form-item>
    </template>
    <template v-else-if="type === this.CONFIG_TYPES.SINGLE_SELECT || type === this.CONFIG_TYPES.MULTIPLE_SELECT">
      <a-form-item label="Option">
        <a-input
          type="textarea"
          rows="4"
          v-model="value.options"
          :placeholder="'Example:\nvalue1=>text1\nvalue2=>text2'"
        />
      </a-form-item>
      <a-form-item label="Type">
        <a-radio-group v-model="value.type">
          <a-radio value="input" label="input">Input Box</a-radio>
          <a-radio value="select" label="select">Select Box</a-radio>
        </a-radio-group>
      </a-form-item>
    </template>
    <template v-else>
      <a-form-item class="ma-0">None</a-form-item>
    </template>
  </a-form>
</template>

<script>
import { mapConstants } from '@/libs/constants'
import _pick from 'lodash/pick'

export default {
  name: 'TypeOptions',
  model: {
    prop: 'value',
    event: 'change.value',
  },
  props: {
    type: String,
    value: Object,
  },
  computed: {
    ...mapConstants(['CONFIG_TYPES']),
  },
  created() {
    this.optionsBak = {}
    this.$on('field-reset', this.onReset)
  },
  beforeDestroy() {
    this.$off('field-reset', this.onReset)
  },
  methods: {
    onReset() {
      this.optionsBak = {}
      this.initOptions()
    },
    initOptions() {
      const type = this.type
      if (!type) {
        return
      }

      let defaultValue
      switch (type) {
        case this.CONFIG_TYPES.FILE:
          defaultValue = {
            max: 1,
            ext: '',
          }
          break
        case this.CONFIG_TYPES.SINGLE_SELECT:
        case this.CONFIG_TYPES.MULTIPLE_SELECT:
          defaultValue = {
            options: '',
            type: 'input',
          }
          break
        default:
          defaultValue = null
      }

      const v = defaultValue
        ? _pick(Object.assign({}, this.optionsBak[type] || defaultValue, this.value), Object.keys(defaultValue))
        : null

      this.optionsBak[type] = v
      this.$emit('change.value', v)
    },
  },
  watch: {
    type: {
      handler() {
        this.initOptions()
      },
      immediate: true,
    },
    $route() {
      this.optionsBak = {}
    },
  },
}
</script>

<style scoped lang="less">
.ant-form-item {
  margin-bottom: 0;
}
</style>
