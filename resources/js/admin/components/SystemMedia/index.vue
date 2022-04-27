<template>
  <div
    class="system-media"
    :class="{
      'mini-width': miniWidth,
      'tiny-width': tinyWidth,
    }"
  >
    <div class="sider" v-if="!miniWidth">
      <category
        class="h-100"
        ref="category"
        @select="onCategorySelect"
        @categories-change="onCategoriesChange"
      />
    </div>
    <div class="content">
      <div class="header">
        <space>
          <a-button v-if="miniWidth" @click="categoriesDialog = true">Choose category</a-button>
          <loading-action :action="onReloadMedia">Refresh</loading-action>
          <a-button :disabled="!anySelected" @click="movingDialog = true">Move</a-button>
          <a-button
            :type="multiple ? 'primary' : ''"
            v-if="defaultMultiple === undefined"
            @click="multiple = !multiple"
          >
            Multiple
          </a-button>
          <lz-popconfirm
            title="Physical files may also be deleted! confirm deletion?"
            type="danger"
            :disabled="!anySelected"
            :confirm="onDestroyMedia"
          >
            <a-button type="danger" :disabled="!anySelected">Delete</a-button>
          </lz-popconfirm>
        </space>
      </div>
      <a-spin
        class="files"
        :class="{ 'files-empty': media.length === 0 }"
        :spinning="mediaLoading || uploading"
        :tip="uploadingText"
      >
        <files
          ref="media"
          :media="media"
          :multiple="multiple"
          :selected.sync="selected"
          :ext="defaultExt"
        />
        <a-empty v-show="media.length === 0 && !mediaLoading"/>
      </a-spin>
      <div class="footer">
        <space>
          <a-upload
            ref="upload"
            :custom-request="storeMedia"
            :before-upload="beforeUpload"
            :show-upload-list="false"
            :disabled="currentCategoryId <= 0"
            multiple
            :accept="'.' + (defaultExt ? defaultExt : '').replace(/,/g, ',.')"
            @change="onUploadChange"
          >
            <a-button
              :disabled="currentCategoryId <= 0"
              :title="currentCategoryId <= 0 ? 'Please select a category first' : ''"
            >
              Upload
            </a-button>
          </a-upload>
          <a-button
            :disabled="!anySelected"
            @click="clearSelected"
          >
            Selected {{ this.selectedCount ? `(${this.selectedCount})` : '' }}
          </a-button>
          <a-button
            :disabled="!!defaultExt"
            @click="onOpenExtDialog"
            :title="ext"
            :type="ext ? 'primary' : null"
          >
            {{ ext ? 'Filtered' : 'Filter' }}
          </a-button>
          <slot name="actions" v-bind="getThis"/>
        </space>
        <div class="flex-spacer"/>
        <lz-pagination
          style="height: auto;"
          :page="page"
          :auto-push="false"
          :show-quick-jumper="false"
          :show-size-changer="false"
          hide-on-single-page
          simple
          @current-change="onPageChange"
        />
      </div>
    </div>

    <a-modal
      v-if="!defaultExt"
      title="File type"
      v-model="extDialog"
      width="400px"
      @ok="onExtFilter"
    >
      <a-input
        v-model="extTemp"
        @keydown.enter="onExtFilter"
        focus
        allow-clear
      />
    </a-modal>

    <a-modal
      title="Move file"
      v-model="movingDialog"
      width="400px"
    >
      <a-select
        class="w-100"
        v-model="movingTarget"
        show-search
        option-filter-prop="title"
        option-label-prop="title"
      >
        <a-select-option
          v-for="i of categoriesSelectOptions"
          :key="i.id"
          :title="i.title"
        >
          {{ i.text }}
        </a-select-option>
      </a-select>

      <template #footer>
        <a-button @click="movingDialog = false">Cancel</a-button>
        <loading-action
          type="primary"
          :action="onMove"
          :disabled="!movingTarget"
        >
          Move
        </loading-action>
      </template>
    </a-modal>

    <a-modal
      v-if="miniWidth"
      title="Choose category"
      v-model="categoriesDialog"
      :footer="null"
    >
      <category
        class="h-100"
        ref="category"
        @select="onCategorySelect"
        @categories-change="onCategoriesChange"
      />
    </a-modal>
  </div>
</template>

<script>
import LzPopconfirm from '@c/LzPopconfirm'
import {
  batchDestroyMedia,
  batchUpdateMedia,
  getCategoryMedia,
  getMedia,
  storeMedia,
} from '@/api/system-media'
import _get from 'lodash/get'
import LzPagination from '@c/LzPagination'
import {
  debounceMsg,
  getExt,
  getMessage,
  nestedToSelectOptions,
} from '@/libs/utils'
import _differenceBy from 'lodash/differenceBy'
import Category from './Category'
import Files from '@c/SystemMedia/Files'
import Space from '@c/Space'
import LoadingAction from '@c/LoadingAction'

export default {
  name: 'SystemMedia',
  components: {
    Space,
    Files,
    Category,
    LzPagination,
    LzPopconfirm,
    LoadingAction,
  },
  data() {
    return {
      categories: [],
      currentCategory: null,

      media: [],
      mediaLoading: false,
      page: null,

      ext: this.defaultExt || '',
      extTemp: '',
      extDialog: false,

      selected: [],

      movingDialog: false,
      moving: false,
      movingTarget: '',

      uploading: false,
      uploadCount: 0,
      uploadFail: 0,
      uploadSuccess: 0,
      uploadInvalid: 0,

      multiple: this.defaultMultiple === undefined
        ? false
        : this.defaultMultiple,

      categoriesDialog: false,
    }
  },
  props: {
    defaultExt: {
      type: String,
      default: '',
    },
    defaultMultiple: {
      type: Boolean,
      default: undefined,
    },
  },
  computed: {
    currentCategoryId() {
      return _get(this.currentCategory, 'id', -1)
    },
    miniWidth() {
      return this.$store.state.miniWidth
    },
    tinyWidth() {
      return this.$store.state.tinyWidth
    },
    anySelected() {
      return this.selectedCount > 0
    },
    selectedCount() {
      return this.selected.length
    },
    categoriesSelectOptions() {
      return nestedToSelectOptions(this.categories, {
        title: 'name',
        firstLevel: null,
      })
    },
    uploadingText() {
      return this.uploading
        ? `Uploading (${this.uploadSuccess} / ${this.uploadCount})`
        : ''
    },
    getThis() {
      return this
    },
    extArray() {
      return this.ext.split(',')
    },
  },
  async created() {
    await this.getMedia()
  },
  methods: {
    async getMedia(categoryId = -1, page) {
      this.mediaLoading = true
      let data
      const params = {
        page,
        ext: this.ext || undefined,
      }
      try {
        if (categoryId > 0) {
          ({ data } = await getCategoryMedia(categoryId, params))
        } else {
          params.category_id = (categoryId === -1) ? undefined : 0;
          ({ data } = await getMedia(params))
        }

        if (this.currentCategoryId !== categoryId) {
          return
        }

        this.media = data.data
        this.page = data.meta
      } finally {
        this.mediaLoading = false
      }
    },
    onPageChange(page) {
      this.getMedia(this.currentCategoryId, page)
    },
    async onReloadMedia() {
      await this.getMedia(this.currentCategoryId)
    },
    onExtFilter() {
      if (this.defaultExt) {
        return
      }

      this.ext = this.extTemp
      this.extDialog = false
    },
    clearSelected() {
      this.selected = []
    },
    async onMove() {
      if (!this.movingTarget || !this.selectedCount) {
        return
      }
      if (this.movingTarget === this.currentCategoryId) {
        this.$message.info('Failed to move to other categories')
        this.movingDialog = false
        return
      }

      await this.batchUpdateMedia({
        id: this.selected.map((i) => i.id),
        category_id: this.movingTarget,
      })
    },
    async batchUpdateMedia(data) {
      await batchUpdateMedia(data).setConfig({
        showValidationMsg: true,
      })
      this.movingDialog = false
      this.$message.success(getMessage('updated'))

      if (this.currentCategoryId === -1) {
        this.clearSelected()
      } else {
        this.moveSelected()
      }
    },
    moveSelected() {
      this.media = _differenceBy(this.media, this.selected, 'id')
      this.clearSelected()
      if (this.media.length === 0) {
        this.onReloadMedia()
      }
    },
    async onDestroyMedia() {
      if (!this.selectedCount) {
        return
      }

      await batchDestroyMedia(this.selected.map((i) => i.id))
      this.$message.success(getMessage('destroyed'))
      this.moveSelected()
    },
    onCategorySelect(category) {
      this.currentCategory = category
    },
    onCategoriesChange(categories) {
      this.categories = categories
    },
    async storeMedia({ file, onSuccess, onError }) {
      const id = this.currentCategoryId

      if (id <= 0) {
        return
      }
      let res
      try {
        res = await storeMedia(id, file).setConfig({ showValidationMsg: true })
        onSuccess(res, null)
        if (id === this.currentCategoryId || this.currentCategoryId === -1) {
          this.media.unshift(res.data)
        }
      } catch (e) {
        onError(e, res)
      }
    },
    onUploadChange({ file }) {
      if (file.status === 'done') {
        this.uploadSuccess++
      } else if (file.status === 'error') {
        this.uploadFail++
      }

      if (this.uploadCount && (this.uploadSuccess + this.uploadFail === this.uploadCount)) {
        this.$info({
          title: 'Upload Complete',
          content: `Success (${this.uploadSuccess})，Fails (${this.uploadFail})，Invalid (${this.uploadInvalid})`,
        })

        this.$refs.upload.sFileList = []
        this.uploading = false
        this.uploadCount = 0
        this.uploadFail = 0
        this.uploadSuccess = 0
      }
    },
    beforeUpload(file) {
      const lt10M = file.size / 1024 / 1024 <= 10
      if (!lt10M) {
        debounceMsg('File cannot be larger than 10 MB')
      }

      const validExt = !this.defaultExt || (this.extArray.indexOf(getExt(file.name)) !== -1)
      if (!validExt) {
        debounceMsg('File types can only be: ' + this.defaultExt)
      }

      const res = lt10M && validExt

      if (res) {
        this.uploadCount++
        this.uploading = true
      } else {
        this.uploadInvalid++
      }

      return res
    },
    onOpenExtDialog() {
      if (this.defaultExt) {
        return
      }

      this.extDialog = true
    },
  },
  watch: {
    currentCategoryId(newVal) {
      this.clearSelected()
      this.getMedia(newVal)
    },
    extDialog(newVal) {
      if (newVal) {
        this.extTemp = this.ext
      }
    },
    ext(newVal) {
      this.clearSelected()
      this.getMedia(this.currentCategoryId)
    },
    miniWidth(newVal) {
      if (!newVal) {
        this.categoriesDialog = false
      }
    },
    defaultMultiple(newVal) {
      this.multiple = newVal
    },
  },
}
</script>

<style scoped lang="less">
@import "~@/styles/vars";

@padding: 16px;

.system-media {
  min-width: 0;
  height: 550px;
  display: flex;
  border: @border-base;
  border-radius: @border-radius-base;
}

.sider {
  width: 220px;
  padding: @padding;
  border-right: @border-base;
}

.content {
  display: flex;
  flex: 1;
  flex-direction: column;
  min-width: 0;
}

.header, .footer {
  padding: @padding;
  overflow-x: auto;
  width: 100%;
  min-height: 65px;
}

.header {
  border-bottom: @border-base;
}

.footer {
  border-top: @border-base;
  display: flex;
  align-items: center;
}

.files {
  flex: 1;
  padding: @padding;
  overflow: auto;
}

.files-empty {
  ::v-deep {
    .ant-spin-container {
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  }
}

.tiny-width {
  .footer {
    flex-direction: column-reverse;
    align-items: flex-start;
  }

  ::v-deep {
    .pagination {
      margin-bottom: 8px !important;
    }
  }
}
</style>
