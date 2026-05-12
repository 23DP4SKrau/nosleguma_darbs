<template>
  <div class="contact-page">
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
            <li><router-link to="/profile">Profils</router-link></li>
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
            <router-link class="login-btn" to="/profile">Mans profils</router-link>
            <button class="signup-btn" @click="logout">Iziet</button>
          </template>
          <template v-else>
            <button class="login-btn" @click="openLogin">Pieslēgties</button>
            <button class="signup-btn" @click="openSignup">Reģistrēties</button>
          </template>
        </div>
      </div>
    </header>

    <main class="contact-hero">
      <section class="contact-left">
        <p class="collection-label">sazināšanās</p>
        <h1>Runājam par hobijiem</h1>
        <p>
          Šeit lietotājs var nosūtīt jautājumu, ieteikumu vai ziņu administratoram.
        </p>
      </section>

      <section class="contact-right">
        <form class="contact-form" @submit.prevent="handleSubmit">
          <label>
            Vārds
            <input v-model="form.name" type="text" required maxlength="255" />
          </label>

          <label>
            E-pasts
            <input v-model="form.email" type="email" required maxlength="255" />
          </label>

          <label>
            Temats
            <input v-model="form.subject" type="text" required maxlength="255" />
          </label>

          <label>
            Ziņa
            <textarea v-model="form.message" required minlength="10" rows="6"></textarea>
          </label>

          <button type="submit" :disabled="isSending">
            {{ isSending ? 'Nosūta...' : 'Nosūtīt ziņu' }}
          </button>

          <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
          <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
        </form>
      </section>
    </main>

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
  </div>
</template>

<script>
const API_URL = 'http://127.0.0.1:8000/api'

export default {
  name: 'ContactPage',
  data() {
    return {
      searchQuery: '',
      currentUser: null,
      isSending: false,
      successMessage: '',
      errorMessage: '',
      form: {
        name: '',
        email: '',
        subject: '',
        message: '',
      },
    }
  },
  mounted() {
    this.currentUser = JSON.parse(localStorage.getItem('hobispace_user') || 'null')
  },
  methods: {
    openLogin() {
      this.$router.push('/hobbies')
    },
    openSignup() {
      this.$router.push('/hobbies')
    },
    logout() {
      localStorage.removeItem('hobispace_user')
      this.currentUser = null
    },
    async handleSubmit() {
      this.isSending = true
      this.successMessage = ''
      this.errorMessage = ''

      try {
        const currentUser = JSON.parse(localStorage.getItem('hobispace_user') || 'null')
        const response = await fetch(`${API_URL}/user-questions`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
          },
          body: JSON.stringify({
            ...this.form,
            user_id: currentUser?.id || null,
          }),
        })

        const data = await response.json()

        if (!response.ok) {
          throw new Error(data.message || 'Ziņu neizdevās nosūtīt.')
        }

        this.successMessage = data.message
        this.form = { name: '', email: '', subject: '', message: '' }
      } catch (error) {
        this.errorMessage = error.message
      } finally {
        this.isSending = false
      }
    },
  },
}
</script>

<style scoped>
.contact-page {
  min-height: 100vh;
  background: #fffaf5;
  color: #1f1f1f;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  background-color: #FDF8F0;
  color: #000000;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.navbar-left,
.logo-container,
.nav-links {
  display: flex;
  align-items: center;
}

.navbar-left {
  gap: 2rem;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search-bar {
  padding: 0.5rem 0.75rem;
  border: none;
  border-radius: 5px;
  outline: none;
  font-size: 0.9rem;
}

.auth-buttons {
  display: flex;
  gap: 0.5rem;
}

.login-btn,
.signup-btn {
  padding: 0.5rem 1rem;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  font: inherit;
  font-weight: 500;
  transition: 0.3s;
}

.login-btn {
  background-color: transparent;
  color: #000000;
  border: 1px solid #000000;
}

.login-btn:hover {
  background-color: #F6DFCE;
}

.signup-btn {
  background-color: #BC4527;
  color: #FDF8F0;
}

.signup-btn:hover {
  background-color: #9b3a20;
}

.logo-container {
  gap: 0.5rem;
}

.logo {
  height: 40px;
}

.project-name {
  font-size: 1.5rem;
  font-weight: bold;
}

.nav-links {
  list-style: none;
  display: flex;
  gap: 1.5rem;
}

.nav-links a {
  text-decoration: none;
  color: #000000;
  font-size: 1rem;
  transition: color 0.3s;
}

.nav-links a:hover {
  color: #F6DFCE;
}

.contact-hero {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(320px, 520px);
  gap: 3rem;
  max-width: 1180px;
  margin: 0 auto;
  padding: 5rem 1.25rem;
}

.collection-label {
  margin: 0 0 0.75rem;
  color: #705949;
  font-weight: 700;
  text-transform: uppercase;
}

.contact-left h1 {
  margin: 0 0 1rem;
  font-size: 3rem;
}

.contact-left p {
  max-width: 620px;
  font-size: 1.15rem;
  line-height: 1.7;
}

.contact-left span {
  font-weight: 700;
}

.contact-right {
  padding: 1.5rem;
  border: 1px solid #eadfd5;
  border-radius: 8px;
  background: #ffffff;
}

.contact-form {
  display: grid;
  gap: 1rem;
}

.contact-form label {
  display: grid;
  gap: 0.4rem;
  font-weight: 700;
}

.contact-form input,
.contact-form textarea {
  width: 100%;
  border: 1px solid #d8c8bc;
  border-radius: 8px;
  padding: 0.85rem 0.95rem;
  font: inherit;
  background: #fffaf5;
}

.contact-form button {
  border: 0;
  border-radius: 8px;
  padding: 0.9rem 1rem;
  background-color: #BC4527;
  color: #FDF8F0;
  font: inherit;
  font-weight: 700;
  cursor: pointer;
}

.contact-form button:hover {
  background-color: #9b3a20;
  color: #FDF8F0;
}

.contact-form button:disabled {
  opacity: 0.7;
  cursor: wait;
}

.success-message,
.error-message {
  margin: 0;
  font-weight: 700;
}

.success-message {
  color: #256548;
}

.error-message {
  color: #9b1c1c;
}

@media (max-width: 780px) {
  .navbar,
  .navbar-left,
  .navbar-right,
  .auth-buttons,
  .nav-links {
    align-items: flex-start;
    flex-direction: column;
  }

  .contact-hero {
    grid-template-columns: 1fr;
    padding-top: 3rem;
  }

  .contact-left h1 {
    font-size: 2.2rem;
  }
}
</style>
