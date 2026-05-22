<template>
    <div class="homepage">
      <!-- Navigation Bar -->
      <header class="navbar">
  <div class="navbar-left">
    <div class="logo-container">
      <img src="@/assets/logo.jpg" alt="HobiSpace logotips" class="logo" />
      <span class="project-name">HobiSpace</span>
    </div>

    <nav>
  <ul class="nav-links">
    <li><router-link to="/">Sākums</router-link></li>
    <li><router-link to="/about">Par mums</router-link></li>
    <li><router-link to="/hobbies">Hobiji</router-link></li>
    <li><router-link to="/contact">Kontakti</router-link></li>
  </ul>
</nav>
  </div>

  <div class="navbar-right">
        <input 
          type="text" 
          v-model="searchQuery"
          placeholder="Meklēt hobijus..." 
          class="search-bar" 
        />
    <div class="auth-buttons">
      <template v-if="currentUser">
        <router-link class="profile-nav-link" to="/profile" aria-label="Mans profils">
          <img v-if="navAvatarUrl" :src="navAvatarUrl" alt="" />
          <span v-else>{{ navInitials }}</span>
        </router-link>
      </template>
      <template v-else>
        <button class="login-btn" @click="openLogin">Pieslēgties</button>
        <button class="signup-btn" @click="openSignup">Reģistrēties</button>
      </template>
    </div>
  </div>

  <button
    type="button"
    class="mobile-menu-btn"
    :class="{ active: mobileMenuOpen }"
    :aria-expanded="mobileMenuOpen"
    aria-label="Atvērt navigāciju"
    @click="toggleMobileMenu"
  >
    <span></span>
    <span></span>
    <span></span>
  </button>

  <div v-if="mobileMenuOpen" class="mobile-menu">
    <router-link to="/" @click="closeMobileMenu">Sākums</router-link>
    <router-link to="/about" @click="closeMobileMenu">Par mums</router-link>
    <router-link to="/hobbies" @click="closeMobileMenu">Hobiji</router-link>
    <router-link to="/contact" @click="closeMobileMenu">Kontakti</router-link>

    <div class="mobile-auth-buttons">
      <template v-if="currentUser">
        <router-link class="profile-nav-link" to="/profile" aria-label="Mans profils" @click="closeMobileMenu">
          <img v-if="navAvatarUrl" :src="navAvatarUrl" alt="" />
          <span v-else>{{ navInitials }}</span>
        </router-link>
      </template>
      <template v-else>
        <button class="login-btn" @click="openLogin(); closeMobileMenu()">Pieslēgties</button>
        <button class="signup-btn" @click="openSignup(); closeMobileMenu()">Reģistrēties</button>
      </template>
    </div>
  </div>
</header>
  
      <!-- Hero Section -->
      <main class="hero">
        <div class="hero-content">
          <p class="collection-label">ceļvedis tavam brīvajam laikam</p>
          <h1>Laipni lūdzam HobiSpace</h1>
          <p>Tava galvenā vieta hobiju izpētei un iedvesmas rašanai.</p>
          <div class="cta-buttons-group">
            <router-link to="/hobbies" class="cta-button primary-button">Izpētīt hobijus</router-link>
            <router-link to="/about" class="cta-button secondary-button">Par mums</router-link>
          </div>
        </div>
      </main>
    </div>

      <!-- Footer -->
    <footer class="footer">
  <div class="footer-container">

    <div class="footer-section">
      <h3>HobiSpace</h3>
      <p>Izpēti hobijus. Atklāj aizrautību. Savienojies ar radošumu.</p>
    </div>

    <div class="footer-section">
      <h4>Resursi</h4>
      <ul>
        <li><a href="#">Pakalpojuma noteikumi</a></li>
        <li><a href="#">Privātuma politika</a></li>
        <li><a href="#">Sīkdatņu politika</a></li>
      </ul>
    </div>

    <!-- Auth Modal -->
<div v-if="showAuth" class="auth-overlay" @click.self="closeAuth">
  <div class="auth-modal">
    <button class="close-btn" @click="closeAuth">×</button>

    <h2>{{ isAdminLogin ? 'Admin pieslēgšanās' : (isLogin ? 'Pieslēgties HobiSpace' : 'Izveidot kontu') }}</h2>

    <form @submit.prevent="handleSubmit">
      <div v-if="!isLogin && !isAdminLogin" class="form-group">
        <label>Pilns vārds</label>
        <input type="text" v-model="form.name" required />
      </div>

      <div class="form-group">
        <label>{{ isAdminLogin ? 'Admin lietotājs' : 'E-pasts' }}</label>
        <input :type="isAdminLogin ? 'text' : 'email'" v-model="form.email" required />
      </div>

      <div class="form-group password-group">
  <label>Parole</label>

  <div class="password-wrapper">
    <input 
      :type="showPassword ? 'text' : 'password'" 
      v-model="form.password" 
      required 
    />
    <button
                type="button"
                class="eye-icon"
                :aria-label="showPassword ? 'Paslēpt paroli' : 'Parādīt paroli'"
                @click="togglePassword"
              >
                <span v-if="showPassword" aria-hidden="true">&#x2323;</span>
                <span v-else aria-hidden="true">&#x1F441;</span>
              </button>
  </div>
  <ul v-if="!isLogin && !isAdminLogin && form.password" class="password-rules">
    <li :class="{ valid: passwordChecks.length }">Vismaz 8 simboli</li>
    <li :class="{ valid: passwordChecks.uppercase }">Viens lielais burts</li>
    <li :class="{ valid: passwordChecks.lowercase }">Viens mazais burts</li>
    <li :class="{ valid: passwordChecks.number }">Viens cipars</li>
    <li :class="{ valid: passwordChecks.symbol }">Viens speciālais simbols</li>
  </ul>
</div>

      <button type="submit" class="auth-submit">
        {{ isAdminLogin ? 'Pieslēgties kā admin' : (isLogin ? 'Pieslēgties' : 'Reģistrēties') }}
      </button>
    </form>

    <p class="switch-text">
      {{ isLogin ? "Nav konta?" : "Jau ir konts?" }}
      <span @click="toggleAuth">
        {{ isLogin ? "Reģistrēties" : "Pieslēgties" }}
      </span>
    </p>

    <button v-if="isLogin" type="button" class="admin-login-link" @click="openAdminLogin">Admin</button>
  </div>
</div>

    <div class="footer-section">
      <h4>Sekojiet mums</h4>
      <div class="social-icons">
        <a href="#">Instagram</a>
        <a href="#">YouTube</a>
        <a href="#">X</a>
        <a href="#">TikTok</a>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <p>© {{ new Date().getFullYear() }} HobiSpace. Visas tiesības aizsargātas.</p>
  </div>
</footer>
  </template>
  
<script>
import { API_URL } from '@/config/api'

export default {
  data() {
    return {
      searchQuery: '',
      mobileMenuOpen: false,
      showAuth: false,
      isLogin: true,
      showPassword: false,
      isAdminLogin: false,
      currentUser: null,
      form: {
        name: '',
        email: '',
        password: ''
      }
    }
  },
  mounted() {
    this.currentUser = JSON.parse(localStorage.getItem('hobispace_user') || 'null')
  },
  computed: {
    navInitials() {
      return this.currentUser?.name
        ?.split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase() || 'HS'
    },
    navAvatarUrl() {
      if (!this.currentUser?.avatar_path) {
        return ''
      }

      if (this.currentUser.avatar_path.startsWith('http')) {
        return this.currentUser.avatar_path
      }

      return API_URL.replace('/api', '') + this.currentUser.avatar_path
    },

    passwordChecks() {
      const password = this.form.password

      return {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        symbol: /[^A-Za-z0-9]/.test(password),
      }
    },
    isPasswordStrong() {
      return Object.values(this.passwordChecks).every(Boolean)
    },
  },
  methods: {
    toggleMobileMenu() {
      this.mobileMenuOpen = !this.mobileMenuOpen
    },
    closeMobileMenu() {
      this.mobileMenuOpen = false
    },
    openLogin() {
      this.isLogin = true
      this.isAdminLogin = false
      this.showAuth = true
    },
    openSignup() {
      this.isLogin = false
      this.isAdminLogin = false
      this.showAuth = true
    },
    closeAuth() {
      this.showAuth = false
      this.showPassword = false
      this.isAdminLogin = false
    },
    toggleAuth() {
      this.isLogin = !this.isLogin
      this.isAdminLogin = false
    },
    openAdminLogin() {
      this.isLogin = true
      this.isAdminLogin = true
      this.form = { name: '', email: 'admin', password: '' }
    },
    togglePassword() {        
      this.showPassword = !this.showPassword
    },
    async handleSubmit() {
      const path = this.isLogin ? '/login' : '/register'

      if (!this.isLogin && !this.isAdminLogin && !this.isPasswordStrong) {
        alert('Parole nav pietiekami stipra.')
        return
      }

      try {
        const response = await fetch(`${API_URL}${path}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
          },
          body: JSON.stringify(this.form),
        })

        const data = await response.json()

        if (!response.ok) {
          alert(data.message || 'Pārbaudi ievadītos datus.')
          return
        }

        localStorage.setItem('hobispace_user', JSON.stringify(data.user))
        this.currentUser = data.user
        this.closeAuth()
        this.$router.push('/profile')
      } catch (error) {
        alert('Nevar pieslēgties Laravel serverim.')
      }
    },
    logout() {
      localStorage.removeItem('hobispace_user')
      this.currentUser = null
    }
  }
}
</script>
  
