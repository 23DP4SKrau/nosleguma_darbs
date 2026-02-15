import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import './assets/main.css'


createApp(App).mount('#app')

export default {
  data() {
    return {
      showAuth: false,
      isLogin: true,
      showPassword: false,   // 👈 add this
      form: {
        name: '',
        email: '',
        password: ''
      }
    }
  },
  methods: {
    openLogin() {
      this.isLogin = true
      this.showAuth = true
    },
    openSignup() {
      this.isLogin = false
      this.showAuth = true
    },
    closeAuth() {
      this.showAuth = false
      this.showPassword = false  // reset
    },
    toggleAuth() {
      this.isLogin = !this.isLogin
    },
    togglePassword() {          // 👈 add this
      this.showPassword = !this.showPassword
    },
    handleSubmit() {
      alert(this.isLogin ? "Logged in!" : "Account created!")
      this.closeAuth()
    }
  }
}
