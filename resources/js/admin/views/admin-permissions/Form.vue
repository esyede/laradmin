<template>
  <page-content center>
    <lz-form
      :get-data="getData"
      :submit="onSubmit"
      :form.sync="form"
      :errors.sync="errors"
    >
      <lz-form-item label="Slug" required prop="slug">
        <a-input v-model="form.slug"/>
      </lz-form-item>
      <lz-form-item label="Name" required prop="name">
        <a-input v-model="form.name"/>
      </lz-form-item>
      <lz-form-item label="Method" prop="http_method">
        <a-select v-model="form.http_method" mode="multiple">
          <a-select-option v-for="i of methods" :key="i">{{ i }}</a-select-option>
        </a-select>
      </lz-form-item>
      <lz-form-item label="Path" prop="http_path">
        <a-input :auto-size="{ minRows: 5, maxRows: 5 }" type="textarea" v-model="form.http_path"/>
      </lz-form-item>
    </lz-form>
  </page-content>
</template>

<script>
import {
  editAdminPerm,
  storeAdminPerm,
  updateAdminPerm,
} from '@/api/admin-perms'
import LzForm from '@c/LzForm'
import LzFormItem from '@c/LzForm/LzFormItem'
import PageContent from '@c/PageContent'

export default {
  name: 'Form',
  components: {
    PageContent,
    LzForm,
    LzFormItem,
  },
  data() {
    return {
      form: {
        slug: '',
        name: '',
        http_method: [],
        http_path: '',
      },
      errors: {},
      methods: ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD'],
    }
  },
  methods: {
    async getData($form) {
      if ($form.realEditMode) {
        const { data } = await editAdminPerm($form.resourceId)
        data.http_path = data.http_path.join('\n')
        return data
      }
    },
    async onSubmit($form) {
      if ($form.realEditMode) {
        await updateAdminPerm($form.resourceId, this.form)
      } else {
        await storeAdminPerm(this.form)
      }
    },
  },
}
</script>
