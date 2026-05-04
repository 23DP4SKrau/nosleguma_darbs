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
      <button class="login-btn" @click="openLogin">Pieslēgties</button>
<button class="signup-btn" @click="openSignup">Reģistrēties</button>
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

    <h2>{{ isLogin ? 'Pieslēgties HobiSpace' : 'Izveidot kontu' }}</h2>

    <form @submit.prevent="handleSubmit">
      <div v-if="!isLogin" class="form-group">
        <label>Pilns vārds</label>
        <input type="text" v-model="form.name" required />
      </div>

      <div class="form-group">
        <label>E-pasts</label>
        <input type="email" v-model="form.email" required />
      </div>

      <div class="form-group password-group">
  <label>Parole</label>

  <div class="password-wrapper">
    <input 
      :type="showPassword ? 'text' : 'password'" 
      v-model="form.password" 
      required 
    />
    <span class="eye-icon" @click="togglePassword">
      {{ showPassword ? '⌣' : '👁' }}
    </span>
  </div>
</div>

      <button type="submit" class="auth-submit">
        {{ isLogin ? 'Pieslēgties' : 'Reģistrēties' }}
      </button>
    </form>

    <p class="switch-text">
      {{ isLogin ? "Nav konta?" : "Jau ir konts?" }}
      <span @click="toggleAuth">
        {{ isLogin ? "Reģistrēties" : "Pieslēgties" }}
      </span>
    </p>
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
export default {
  data() {
    return {
      searchQuery: '',
      showAuth: false,
      isLogin: true,
      showPassword: false,  
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
      this.showPassword = false  
    },
    toggleAuth() {
      this.isLogin = !this.isLogin
    },
    togglePassword() {        
      this.showPassword = !this.showPassword
    },
    handleSubmit() {
      if (this.isLogin) {
        alert("Pieslēgšanās veiksmīga!")
      } else {
        alert("Konts izveidots veiksmīgi!")
      }
      this.closeAuth()
    }
  }
}
</script>
  