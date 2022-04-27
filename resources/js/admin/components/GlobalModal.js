import Vue from 'vue'
import store from '@/store'
import router from '@/router'
import { Modal } from 'ant-design-vue'
import _pick from 'lodash/pick'

const GlobalModal = Vue.extend({
  name: 'GlobalModal',
  data() {
    return {
      show: false,
    }
  },
  props: {
    content: [String, Function],
    ...Modal.props,
    lzFooter: null,
  },
  mounted() {
    this.show = true
  },
  methods: {
    clean() {
      this.$destroy()
      const p = this.$el.parentNode
      p && p.removeChild(this.$el)
    },
    close() {
      this.show = false
    },
  },
  watch: {
    $route() {
      this.show = false
    },
    show(newVal) {
      if (!newVal) {
        this.clean()
      }
    },
  },
  render(h) {
    let { content, lzFooter } = this
    if (content) {
      content = (typeof this.content === 'string')
        ? this.content
        : this.content.bind(this)(h)
    }

    const attrs = _pick(this, Object.keys(Modal.props))
    if (!lzFooter) {
      attrs.footer = null
    }

    const footer = lzFooter &&
      <template slot="footer">{
        typeof lzFooter === 'function'
          ? lzFooter.bind(this)(h)
          : lzFooter
      }</template>

    return (
      <a-modal
        {...{
          attrs,
          listeners: this.$listeners,
        }}
        v-model={this.show}
      >
        {[content, footer]}
      </a-modal>
    )
  },
})

GlobalModal.new = (options) => {
  const vm = new GlobalModal({
    store,
    router,
    ...options,
  })

  document.body.appendChild(vm.$mount().$el)

  return vm
}

export default GlobalModal
