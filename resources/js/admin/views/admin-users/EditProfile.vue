<template>
  <page-content center>
    <lz-form
      ref="form"
      :get-data="getData"
      :submit="onSubmit"
      :errors.sync="errors"
      :form.sync="form"
      disable-stay
      disable-redirect
      edit-mode
    >
      <lz-form-item label="Username">
        <a-input :value="profile.username" read-only/>
      </lz-form-item>
      <lz-form-item label="Fullname" required prop="name">
        <a-input v-model="form.name"/>
      </lz-form-item>
      <lz-form-item label="Avatar" prop="avatar">
        <file-picker
          v-model="form.avatar"
          ext="jpg,gif,png,jpeg"
        />
      </lz-form-item>
      <lz-form-item label="Password" prop="password">
        <a-input type="password" v-model="form.password"/>
      </lz-form-item>
      <lz-form-item label="Re-type Password" prop="password_confirmation">
        <a-input type="password" v-model="form.password_confirmation"/>
      </lz-form-item>
      <lz-form-item label="Role">
        <a-tag v-for="i of profile.roles" color="blue" :key="i">{{ i }}</a-tag>
      </lz-form-item>
      <lz-form-item label="Permission">
        <a-tag v-for="i of profile.permissions" color="blue" :key="i">{{ i }}</a-tag>
      </lz-form-item>
    </lz-form>
  </page-content>
</template>

<script>
import { editUser, updateUser } from '@/api/admin-users'
import LzForm from '@c/LzForm'
import LzFormItem from '@c/LzForm/LzFormItem'
import PageContent from '@c/PageContent'
import FilePicker from '@c/LzForm/FilePicker'

export default {
  components: {
    FilePicker,
    PageContent,
    LzForm,
    LzFormItem,
  },
  data() {
    return {
      form: {
        name: '',
        avatar: '',
        password: '',
        password_confirmation: '',
      },
      profile: {},
      errors: {},
    }
  },
  methods: {
    async getData() {
      const { data } = await editUser()
      this.profile = data
      return data
    },
    async onSubmit() {
      const { data } = await updateUser(this.form)
      this.$store.commit('SET_USER', data)
      this.form = Object.assign({}, this.form, {
        password: '',
        password_confirmation: '',
      })
    },
  },
}
</script>
