<template>
  <page-content>
    <space class="my-1">
      <search-form :fields="search"/>
      <cache-config/>
    </space>

    <a-table
      row-key="id"
      :data-source="configs"
      bordered
      :scroll="{ x: 1600 }"
      :pagination="false"
      table-layout="fixed"
    >
      <a-table-column title="ID" data-index="id" :width="60"/>
      <a-table-column title="Name" data-index="category.name" :width="150"/>

      <a-table-column title="Name" :width="150">
        <template #default="record">
          <quick-edit
            :id="record.id"
            field="name"
            :update="updateConfig"
            v-model="record.name"
          />
        </template>
      </a-table-column>
      <a-table-column title="Slug" :width="150">
        <template #default="record">
          <quick-edit
            :id="record.id"
            field="slug"
            :update="updateConfig"
            v-model="record.slug"
          />
        </template>
      </a-table-column>
      <a-table-column title="Type" data-index="type_text" :width="100"/>
      <a-table-column title="Data">
        <template #default="record">
          <div v-if="record.type === CONFIG_TYPES.FILE" style="display: flex; overflow-x: auto">
            <file-preview
              v-for="(item, index) of arrayWrap(record.value)"
              :key="index"
              :file="item"
            />
          </div>
          <span v-else>{{ record.value }}</span>
        </template>
      </a-table-column>
      <a-table-column title="Created At" data-index="created_at" :width="180"/>
      <a-table-column title="Updated At" data-index="updated_at" :width="180"/>
      <a-table-column title="Actions" :width="100">
        <template #default="record">
          <space>
            <router-link :to="`/configs/${record.id}/edit`">Edit</router-link>
            <lz-popconfirm :confirm="destroyConfig(record.id)">
              <a class="error-color" href="javascript:void(0);">Delete</a>
            </lz-popconfirm>
          </space>
        </template>
      </a-table-column>
    </a-table>
    <lz-pagination :page="page"/>
  </page-content>
</template>

<script>
import {
  getConfigCategories,
  getConfigs,
  updateConfig,
  destroyConfig,
} from '@/api/configs'
import Space from '@c/Space'
import LzPagination from '@c/LzPagination'
import PageContent from '@c/PageContent'
import SearchForm from '@c/SearchForm'
import LzPopconfirm from '@c/LzPopconfirm'
import { arrayWrap, removeWhile } from '@/libs/utils'
import QuickEdit from '@c/QuickEdit'
import FilePreview from '@c/FilePreview'
import { mapConstants } from '@/libs/constants'
import CacheConfig from '@v/configs/components/CacheConfig'

export default {
  name: 'Index',
  scroll: true,
  components: {
    CacheConfig,
    QuickEdit,
    LzPopconfirm,
    PageContent,
    LzPagination,
    Space,
    SearchForm,
    FilePreview,
  },
  data() {
    return {
      configs: [],
      page: null,

      search: [
        {
          field: 'category_id',
          label: 'Category',
          type: 'select',
          options: [],
          labelField: 'name',
          valueField: 'id',
        },
        {
          field: 'name',
          label: 'Name',
        },
        {
          field: 'slug',
          label: 'Slug',
        },
      ],
    }
  },
  computed: {
    ...mapConstants('CONFIG_TYPES'),
  },
  created() {
    this.getConfigCategories()
  },
  methods: {
    arrayWrap,
    destroyConfig(id) {
      return async () => {
        await destroyConfig(id)
        this.configs = removeWhile(this.configs, (i) => i.id === id)
      }
    },
    updateConfig,
    async getConfigCategories() {
      const { data } = await getConfigCategories({ all: 1 })
      this.search[0].options = data
    },
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getConfigs(newVal.query)
        this.configs = data
        this.page = meta

        this.$scrollResolve()
      },
      immediate: true,
    },
  },
}
</script>
