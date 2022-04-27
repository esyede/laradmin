<template>
  <div class="file-picker">
    <div class="file-wrapper">
      <file-preview
        class="preview file-item"
        v-for="(item, i) of arrayValue"
        :key="i"
        :file="item"
      >
        <a-icon
          class="replace"
          type="sync"
          title="Replace"
          @click.stop="onReplace(i)"
        />
        <lz-popconfirm title="Delete this data?" :confirm="() => remove(i)">
          <a-icon
            class="remove"
            type="delete"
            title="Delete"
          />
        </lz-popconfirm>
      </file-preview>

      <div
        v-show="!isMax"
        class="picker file-item flex-box"
        @click="onPick"
      >
        <svg-icon class="file-icon" :icon-class="pickerIcon"/>
      </div>
    </div>

    <a-modal
      :title="title"
      v-model="dialog"
      width="90%"
      :footer="null"
      wrap-class-name="system-media-dialog"
    >
      <system-media
        ref="media"
        :default-multiple="mediaMultiple"
        :default-ext="ext"
      >
        <template #actions="media">
          <a-button
            type="primary"
            :disabled="!media.anySelected"
            @click="onPickConfirm(media.selected)"
          >
            Selected
          </a-button>
        </template>
      </system-media>
    </a-modal>
  </div>
</template>

<script>
import SystemMedia from '@c/SystemMedia/index'
import FilePreview from '@c/FilePreview'
import _pick from 'lodash/pick'
import LzPopconfirm from '@c/LzPopconfirm'

export default {
  name: 'FilePicker',
  components: {
    LzPopconfirm,
    FilePreview,
    SystemMedia,
  },
  inject: {
    lzFormItem: {
      default: null,
    },
  },
  data() {
    return {
      dialog: false,
      formattedValue: null,
      pickIndex: -1,
    }
  },
  props: {
    multiple: Boolean,
    max: {
      type: [String, Number],
      default: 8,
    },
    title: {
      type: String,
      default() {
        return this.lzFormItem?.label || 'Choose file'
      },
    },
    value: null,
    ext: String,
    valueFields: {
      type: String,
      default: 'path',
    },
    flatValue: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    pickerIcon() {
      return this.multiple ? 'multi-file' : 'single-file'
    },
    canPick() {
      return !this.isMax || this.isReplace
    },
    isMax() {
      return (this.multiple && this.value.length >= this.max) ||
        (!this.multiple && this.value)
    },
    isReplace() {
      return this.pickIndex !== -1
    },
    miniWidth() {
      return this.$store.state.miniWidth
    },
    arrayValue() {
      if (!this.value) {
        return []
      }

      return Array.isArray(this.value) ? this.value : [this.value]
    },
    mediaMultiple() {
      return this.isReplace ? false : this.multiple
    },
  },
  methods: {
    onPick() {
      if (!this.canPick) {
        return
      }

      this.dialog = true
    },
    onPickConfirm(selected) {
      selected = selected.map(this.formatReturn)
      let value
      if (this.multiple) {
        if (this.isReplace) {
          value = this.value
          value[this.pickIndex] = selected[0]
        } else {
          value = this
            .value
            .concat(selected.slice(0, this.max - this.value.length))
        }
      } else {
        value = selected[0]
      }

      this.$emit('input', value)
      this.$refs.media.clearSelected()
      this.dialog = false
    },
    formatReturn(value) {
      let fields = this.valueFields ? this.valueFields.split(',') : []
      if (fields.length === 0) {
        value = { ...value }
      } else {
        fields.push('path')
        value = _pick(value, fields)
      }

      fields = [...new Set(fields)]
      if (this.flatValue && fields.length === 1) {
        return value[fields[0]]
      } else {
        return value
      }
    },
    remove(index) {
      if (this.multiple) {
        this.value.splice(index, 1)
      } else {
        this.$emit('input', null)
      }
    },
    onReplace(index) {
      this.pickIndex = index
      this.onPick()
    },
  },
  watch: {
    dialog(newVal) {
      if (!newVal) {
        this.pickIndex = -1
      }
    },
  },
}
</script>

<style scoped lang="less">
@import "~@/styles/vars";

.file-wrapper {
  display: flex;
  flex-wrap: wrap;
}

.picker {
  cursor: pointer;
}

.file-item {
  width: 100px;
  height: 100px;
  min-width: 100px;
  min-height: 100px;
  border: @border-base;
  border-radius: @border-radius-base;
  color: @border-color-base;
  margin: 0 5px 5px 0;
  transition: all .3s;

  &:hover {
    border-color: @input-hover-border-color;
    color: @info-color;
  }

  .file-icon {
    width: 50px;
    height: 50px;
  }
}

.remove {
  color: @error-color;
}

.replace {
  color: @primary-color;
}
</style>

<style lang="less">
.system-media-dialog {
  .ant-modal {
    max-width: 848px;
  }
}
</style>
