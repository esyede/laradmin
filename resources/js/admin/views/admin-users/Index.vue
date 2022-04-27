<template>
  <page-content>
    <space class="my-1">
      <search-form :fields="search"/>
    </space>

    <a-table
      row-key="id"
      :data-source="users"
      bordered
      :scroll="{ x: 1500 }"
      :pagination="false"
    >
      <a-table-column title="ID" data-index="id" :width="60"/>
      <a-table-column title="Fullname" data-index="name" :width="150"/>
      <a-table-column title="Username" data-index="username" :width="200"/>
      <a-table-column title="Roles">
        <template #default="record">
          <a-tag v-for="i of record.roles" color="blue" :key="i">{{ i }}</a-tag>
        </template>
      </a-table-column>
      <a-table-column title="Permission">
        <template #default="record">
          <a-tag v-for="i of record.permissions" color="blue" :key="i">{{ i }}</a-tag>
        </template>
      </a-table-column>
      <a-table-column title="Created At" data-index="created_at" :width="180"/>
      <a-table-column title="Updated At" data-index="updated_at" :width="180"/>
      <a-table-column title="Actions" :width="100">
        <template #default="record">
          <space>
            <router-link :to="`/admin-users/${record.id}/edit`">Edit</router-link>
            <lz-popconfirm :confirm="destroyAdminRole(record.id)">
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
import { getAdminUsers, destroyAdminUser } from '@/api/admin-users'
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
  },
  data() {
    return {
      users: [],
      page: null,

      search: [
        {
          field: 'id',
          label: 'ID',
        },
        {
          field: 'name',
          label: 'Fullname',
        },
        {
          field: 'username',
          label: 'Username',
        },
        {
          field: 'role_name',
          label: 'Role',
        },
        {
          field: 'permission_name',
          label: 'Permission',
        },
      ],
    }
  },
  methods: {
    destroyAdminRole(id) {
      return async () => {
        await destroyAdminUser(id)
        this.users = removeWhile(this.users, (i) => i.id === id)
      }
    },
  },
  watch: {
    $route: {
      async handler(newVal) {
        const { data: { data, meta } } = await getAdminUsers(newVal.query)
        this.users = data
        this.page = meta

        this.$scrollResolve()
      },
      immediate: true,
    },
  },
}
</script>
