<script>
export default {
  name: 'LoadingAction',
  data: () => ({
    actionLoading: false,
    disabled: false,
  }),
  props: {
    action: {
      type: Function,
      required: true,
    },
    comp: {
      type: String,
      default: 'a-button',
    },
    type: String,
    loading: Boolean,
    disableOnSuccess: {
      type: [String, Number],
      default: 500,
    },
    disableTextWhenLoading: Boolean,
  },
  computed: {
    readLoading() {
      return this.loading || this.actionLoading
    },
  },
  beforeDestroy() {
    this.clearRecoverDisabledTimeout()
  },
  methods: {
    async onAction() {
      if (this.actionLoading || this.disabled) {
        return false
      }
      this.actionLoading = true
      try {
        await this.action()
        this.handleDisableOnSuccess()
      } finally {
        this.actionLoading = false
      }
    },
    handleDisableOnSuccess() {
      if (this.disableOnSuccess > 0) {
        this.disabled = true
        this.clearRecoverDisabledTimeout()
        this.recoverDisabledTimeout = setTimeout(() => {
          this.disabled = false
        }, this.disableOnSuccess)
      }
    },
    clearRecoverDisabledTimeout() {
      this.recoverDisabledTimeout && clearTimeout(this.recoverDisabledTimeout)
    },
  },
  watch: {
    actionLoading(newVal) {
      this.$emit('action-loading', newVal)
    },
  },
  render(h) {
    return (
      <this.comp
        type={this.type}
        {...{
          attrs: this.$attrs,
          listeners: this.$listeners,
        }}
        v-on:click={this.onAction}
        loading={this.readLoading}
        disabled={this.disabled}
      >
        {(!this.disableTextWhenLoading || !this.readLoading)
          ? this.$slots.default
          : null}
      </this.comp>
    )
  },
}
</script>
