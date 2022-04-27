<script>
import { mapConstants } from '@/libs/constants'
import FilePicker from '@c/LzForm/FilePicker'
import _debounce from 'lodash/debounce'

export default {
  name: 'TypeInput',
  components: {
    FilePicker,
  },
  data() {
    return {
      filePickerKey: 1,
    }
  },
  props: {
    type: String,
    value: null,
    options: Object,
  },
  computed: {
    ...mapConstants(['CONFIG_TYPES']),
    selectOptions() {
      if (
        (this.type !== this.CONFIG_TYPES.SINGLE_SELECT) &&
        (this.type !== this.CONFIG_TYPES.MULTIPLE_SELECT)
      ) {
        return []
      } else {
        const pairs = this.options.options.split('\n')
        const options = []
        pairs.forEach((pair) => {
          const [value, label] = pair.split('=>').map((i) => i.trim())
          if (label) {
            options.push({ value, label })
          }
        })
        return options
      }
    },
  },
  methods: {
    onInput(val) {
      this.$emit('input', val)
    },
    initValue() {
      if (this.type === this.CONFIG_TYPES.FILE) {
        if (this.options.max > 1 && !Array.isArray(this.value)) {
          this.onInput(this.value ? [this.value] : [])
        } else if (this.options.max <= 1 && Array.isArray(this.value)) {
          this.onInput(this.value[0] || null)
        }
      } else if (this.type === this.CONFIG_TYPES.MULTIPLE_SELECT) {
        !Array.isArray(this.value) && this.onInput([])
      } else {
        if (['string', 'boolean', 'number'].indexOf(typeof this.value) === -1) {
          this.onInput(null)
        }
      }
    },
    updateFilePicker: _debounce(function () {
      this.filePickerKey++
    }, 500),
  },
  watch: {
    type: {
      handler() {
        this.initValue()
      },
      immediate: true,
    },
    'options.max'(newVal, oldVal) {
      if (!((newVal > 1) && (oldVal > 1))) {
        this.updateFilePicker()
      }

      this.initValue()

      if ((newVal > 1) && (newVal < oldVal)) {
        this.onInput(this.value.slice(0, newVal))
      }
    },
    'options.ext'() {
      this.updateFilePicker()
    },
  },
  render(h) {
    const renderData = {
      attrs: {
        value: this.value,
      },
      on: {},
    }

    let vModelEventKey
    let component = null
    let slots = null

    const TYPES = this.CONFIG_TYPES

    switch (this.type) {
      case TYPES.INPUT:
      case TYPES.OTHER:
        component = 'a-input'
        vModelEventKey = 'change.value'
        break
      case TYPES.TEXTAREA:
        component = 'a-input'
        vModelEventKey = 'change.value'
        renderData.attrs.type = 'textarea'
        break
      case TYPES.FILE:
        component = 'file-picker'
        vModelEventKey = 'input'
        Object.assign(renderData.attrs, {
          max: this.options.max,
          ext: this.options.ext,
          multiple: this.options.max > 1,
        })
        renderData.key = this.filePickerKey
        break
      case TYPES.SINGLE_SELECT:
      case TYPES.MULTIPLE_SELECT: {
        const isMultiple = this.type === TYPES.MULTIPLE_SELECT
        if (this.options.type === 'input') {
          component = isMultiple
            ? 'a-checkbox-group'
            : 'a-radio-group'
          vModelEventKey = 'input'
          const optionComponent = isMultiple
            ? 'a-checkbox'
            : 'a-radio'
          slots = this.selectOptions.map((i) => (
            <optionComponent key={i.value} value={i.value}>{i.label}</optionComponent>
          ))
        } else if (this.options.type === 'select') {
          component = 'a-select'
          vModelEventKey = 'change'
          Object.assign(renderData.attrs, {
            mode: isMultiple ? 'multiple' : 'default',
            allowClear: isMultiple,
          })
          slots = this.selectOptions.map((i) => (
            <a-select-option key={i.value} value={i.value}>{i.label}</a-select-option>
          ))
        }
        break
      }
      default:
      // do nothing
    }

    renderData.on[vModelEventKey] = this.onInput

    return <component {...renderData}>{slots}</component>
  },
}
</script>
