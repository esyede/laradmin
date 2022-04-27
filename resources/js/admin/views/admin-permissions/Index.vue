<template>
  <page-content>
    <space class="my-1">
      <search-form :fields="search"/>
    </space>

    <a-table
      row-key="id"
      :data-source="perms"
      bordered
      :scroll="{ x: 1200 }"
      :pagination="false"
    >
      <a-table-column title="ID" data-index="id" :width="60"/>
      <a-table-column title="Name" data-index="name" :width="150"/>
      <a-table-column title="Slug" data-index="slug" :width="150"/>
      <a-table-column title="Data">
        <template #default="record">
          <route-show :data="record"/>
        </template>
      </a-table-column>
      <a-table-column title="Created At" data-index="created_at" :width="180"/>
      <a-table-column title="Updated At" data-index="updated_at" :width="180"/>
      <a-table-column title="Actions" :width="100">
        <template #default="record">
          <space>
            <router-link :to="`/admin-permissions/${record.id}/edit`">Edit</router-link>
            <lz-popconfirm :confirm="destroyAdminPerm(record.id)">
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
import { getAdminPerms, destroyAdminPerm } from '@/api/admin-perms'
import RouteShow from './components/RouteShow'
import Space from '@c/Space'
import LzPagination from '@c/LzPagination'
import PageContent from '@c/PageContent'
import SearchForm from '@c/SearchForm'
import LzPopconfirm from '@c/LzPopconfirm'
import { removeWhile } from '@/libs/utils'

export default {
  name: 'Index',
  scroll: true,
  components: {
    LzPopconfirm,
    PageContent,
    LzPagination,
    Space,
    SearchForm,
    RouteShow,
  },
  data() {
    return {
      perms: [],
      page: null,

      search: [
        {
          field: 'id',
          label: 'ID',
        },
        {
          field: 'name',
          label: 'Name',
        },
        {
          field: 'slug',
          label: 'Slug',
        },
        {
          field: 'http_path',
          label: 'Path',
        },
      ],
    }
  },
  methods: {
    destroyAdminPerm(id) {
      return async () => {
        await destroyAdminPerm(id)
        this.perms = removeWhile(this.perms, (i) => i.id === id)
      }
    },
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getAdminPerms(newVal.query)
        this.perms = data
        this.page = meta

        this.$scrollResolve()
      },
      immediate: true,
    },
  },
}
</script>
