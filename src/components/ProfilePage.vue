<template>
  <div class="profile-page">
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

    <main v-if="!currentUser" class="empty-profile">
      <div>
        <p class="eyebrow">profils</p>
        <h1>Vispirms pieslēdzies</h1>
        <p>Lai redzētu favorītus un labotu profila datus, pieslēdzies vai izveido kontu hobiju sadaļā.</p>
        <router-link class="primary-link" to="/hobbies">Doties uz hobijiem</router-link>
      </div>
    </main>

    <main v-else class="profile-shell">
      <section class="profile-hero">
        <div class="avatar" aria-hidden="true">{{ initials }}</div>
        <div>
          <p class="eyebrow">mans profils</p>
          <h1>{{ currentUser.name }}</h1>
          <p>{{ currentUser.email }}</p>
        </div>
        <button type="button" class="logout-btn" @click="logout">Iziet</button>
      </section>

      <section class="stats-grid">
        <article>
          <span>{{ stats.favorites_count }}</span>
          <p>Favorīti</p>
        </article>
        <article>
          <span>{{ stats.logs_count }}</span>
          <p>Ieraksti</p>
        </article>
        <article>
          <span>{{ stats.questions_count }}</span>
          <p>Jautājumi</p>
        </article>
      </section>

      <section class="content-grid">
        <article class="profile-panel">
          <div class="section-heading">
            <p class="eyebrow">iestatījumi</p>
            <h2>Profila dati</h2>
          </div>

          <form class="profile-form" @submit.prevent="updateProfile">
            <label>
              Vārds
              <input v-model="profileForm.name" type="text" required maxlength="255" />
            </label>

            <label>
              E-pasts
              <input v-model="profileForm.email" type="email" required maxlength="255" />
            </label>

            <button type="submit" :disabled="isSaving">
              {{ isSaving ? 'Saglabā...' : 'Saglabāt izmaiņas' }}
            </button>
          </form>

          <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
          <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
        </article>

        <article class="profile-panel">
          <div class="section-heading">
            <p class="eyebrow">aktivitāte</p>
            <h2>Pēdējie ieraksti</h2>
          </div>

          <div v-if="recentLogs.length === 0" class="soft-empty">
            Te vēl nav hobiju ierakstu.
          </div>
          <ul v-else class="log-list">
            <li v-for="log in recentLogs" :key="log.id">
              <strong>{{ log.title }}</strong>
              <span>{{ log.hobby?.name }} · {{ formatDate(log.log_date) }}</span>
            </li>
          </ul>
        </article>
      </section>

      <section class="favorites-section">
        <div class="section-heading">
          <p class="eyebrow">kolekcija</p>
          <h2>Mani favorīti</h2>
        </div>

        <div v-if="favorites.length === 0" class="soft-empty">
          Te vēl nav favorītu. Atver hobiju sadaļu un nospied sirsniņu pie hobijiem, kas patīk.
        </div>

        <div v-else class="favorites-grid">
          <article v-for="hobby in favorites" :key="hobby.id" class="favorite-card">
            <div class="favorite-icon">{{ getHobbyIcon(hobby.name) }}</div>
            <div class="favorite-content">
              <span>{{ hobby.category?.name }}</span>
              <h3>{{ hobby.name }}</h3>
              <p>{{ hobby.description }}</p>
              <button type="button" @click="removeFavorite(hobby)">Noņemt no favorītiem</button>
            </div>
          </article>
        </div>
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
  name: 'ProfilePage',
  data() {
    return {
      searchQuery: '',
      currentUser: null,
      favorites: [],
      recentLogs: [],
      stats: {
        favorites_count: 0,
        logs_count: 0,
        questions_count: 0,
      },
      profileForm: {
        name: '',
        email: '',
      },
      isSaving: false,
      successMessage: '',
      errorMessage: '',
    }
  },
  computed: {
    initials() {
      return this.currentUser?.name
        ?.split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase() || 'HS'
    },
  },
  mounted() {
    this.currentUser = JSON.parse(localStorage.getItem('hobispace_user') || 'null')

    if (this.currentUser) {
      this.profileForm = {
        name: this.currentUser.name,
        email: this.currentUser.email,
      }
      this.loadProfile()
      this.loadFavorites()
    }
  },
  methods: {
    openLogin() {
      this.$router.push('/hobbies')
    },
    openSignup() {
      this.$router.push('/hobbies')
    },
    async request(path, options = {}) {
      const response = await fetch(`${API_URL}${path}`, {
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          ...(options.headers || {}),
        },
        ...options,
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.message || 'Darbību neizdevās izpildīt.')
      }

      return data
    },
    async loadProfile() {
      const data = await this.request(`/users/${this.currentUser.id}/profile`)
      this.currentUser = data.user
      this.stats = data.stats
      this.recentLogs = data.recent_logs
      localStorage.setItem('hobispace_user', JSON.stringify(data.user))
    },
    async loadFavorites() {
      this.favorites = await this.request(`/favorites/${this.currentUser.id}`)
    },
    async updateProfile() {
      this.isSaving = true
      this.successMessage = ''
      this.errorMessage = ''

      try {
        const data = await this.request(`/users/${this.currentUser.id}`, {
          method: 'PUT',
          body: JSON.stringify(this.profileForm),
        })

        this.currentUser = data.user
        localStorage.setItem('hobispace_user', JSON.stringify(data.user))
        this.successMessage = data.message
      } catch (error) {
        this.errorMessage = error.message
      } finally {
        this.isSaving = false
      }
    },
    async removeFavorite(hobby) {
      await this.request('/favorites/toggle', {
        method: 'POST',
        body: JSON.stringify({
          user_id: this.currentUser.id,
          hobby_id: hobby.id,
        }),
      })

      this.favorites = this.favorites.filter((favorite) => favorite.id !== hobby.id)
      this.stats.favorites_count = Math.max(0, this.stats.favorites_count - 1)
    },
    logout() {
      localStorage.removeItem('hobispace_user')
      this.$router.push('/hobbies')
    },
    formatDate(value) {
      return new Intl.DateTimeFormat('lv-LV').format(new Date(value))
    },
    getHobbyIcon(name) {
      const icons = {
        Fotografesana: '📷',
        Skriesana: '🏃',
        Dejosana: '💃',
        Dziedasana: '🎤',
        Susana: '🧵',
        Lasisana: '📚',
        Fitness: '💪',
        Gleznosana: '🎨',
        Rakstisana: '✍',
        Ritenbrauksana: '🚲',
        Peldesana: '🏊',
        Muzika: '🎵',
        Makskeresana: '🎣',
        Videospele: '🎮',
        'Ediena gatavosana': '🍳',
        Cepsana: '🧁',
        Rokdarbi: '🛠',
      }

      return icons[name] || '✨'
    },
  },
}
</script>

<style scoped>
.profile-page {
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

.profile-shell,
.empty-profile {
  max-width: 1180px;
  margin: 0 auto;
  padding: 2rem 1.25rem 4rem;
}

.empty-profile {
  display: grid;
  min-height: 70vh;
  place-items: center;
  text-align: center;
}

.empty-profile h1 {
  margin: 0 0 0.75rem;
  font-size: 2.7rem;
}

.primary-link {
  display: inline-flex;
  margin-top: 1rem;
  padding: 0.9rem 1.2rem;
  border-radius: 8px;
  background: #BC4527;
  color: #FDF8F0;
  text-decoration: none;
  font-weight: 800;
}

.profile-hero {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 1.25rem;
  padding: 2rem;
  border-radius: 8px;
  background: #f6dfce;
}

.avatar {
  width: 92px;
  height: 92px;
  display: grid;
  place-items: center;
  border-radius: 8px;
  background: #BC4527;
  color: #FDF8F0;
  font-size: 2rem;
  font-weight: 900;
}

.eyebrow {
  margin: 0 0 0.35rem;
  color: #705949;
  font-size: 0.8rem;
  font-weight: 900;
  text-transform: uppercase;
}

.profile-hero h1 {
  margin: 0;
  font-size: 2.6rem;
}

.profile-hero p:not(.eyebrow) {
  margin: 0.35rem 0 0;
  color: #5c4d43;
  font-weight: 700;
}

.logout-btn,
.profile-form button,
.favorite-card button {
  border: 0;
  border-radius: 8px;
  padding: 0.8rem 1rem;
  font: inherit;
  font-weight: 800;
  cursor: pointer;
}

.logout-btn {
  background: #ffffff;
  color: #9b1c1c;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
  margin: 1rem 0;
}

.stats-grid article,
.profile-panel,
.favorites-section {
  border: 1px solid #eadfd5;
  border-radius: 8px;
  background: #ffffff;
}

.stats-grid article {
  padding: 1.25rem;
}

.stats-grid span {
  display: block;
  font-size: 2rem;
  font-weight: 900;
  color: #BC4527;
}

.stats-grid p {
  margin: 0.2rem 0 0;
  color: #62554e;
  font-weight: 800;
}

.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(320px, 0.8fr);
  gap: 1rem;
}

.profile-panel,
.favorites-section {
  padding: 1.25rem;
}

.section-heading h2 {
  margin: 0 0 1rem;
  font-size: 1.6rem;
}

.profile-form {
  display: grid;
  gap: 1rem;
}

.profile-form label {
  display: grid;
  gap: 0.4rem;
  font-weight: 800;
}

.profile-form input {
  width: 100%;
  border: 1px solid #d8c8bc;
  border-radius: 8px;
  padding: 0.85rem 0.95rem;
  font: inherit;
  background: #fffaf5;
}

.profile-form button,
.favorite-card button {
  background: #BC4527;
  color: #FDF8F0;
}

.profile-form button:disabled {
  opacity: 0.7;
  cursor: wait;
}

.success-message,
.error-message {
  margin: 1rem 0 0;
  font-weight: 800;
}

.success-message {
  color: #BC4527;
}

.error-message {
  color: #9b1c1c;
}

.log-list {
  display: grid;
  gap: 0.75rem;
  padding: 0;
  margin: 0;
  list-style: none;
}

.log-list li {
  display: grid;
  gap: 0.25rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #eadfd5;
}

.log-list span,
.soft-empty {
  color: #62554e;
}

.favorites-section {
  margin-top: 1rem;
}

.favorites-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.favorite-card {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid #eadfd5;
  border-radius: 8px;
  background: #fffaf5;
}

.favorite-icon {
  width: 60px;
  height: 60px;
  display: grid;
  place-items: center;
  border-radius: 8px;
  background: #F6DFCE;
  font-size: 1.8rem;
}

.favorite-content span {
  color: #BC4527;
  font-weight: 900;
}

.favorite-content h3 {
  margin: 0.2rem 0 0.5rem;
}

.favorite-content p {
  margin: 0 0 1rem;
  color: #62554e;
  line-height: 1.5;
}

@media (max-width: 780px) {
  .navbar,
  .navbar-left,
  .navbar-right,
  .auth-buttons,
  .nav-links,
  .profile-hero {
    align-items: flex-start;
    grid-template-columns: 1fr;
    flex-direction: column;
  }

  .stats-grid,
  .content-grid,
  .favorites-grid {
    grid-template-columns: 1fr;
  }

  .profile-hero h1 {
    font-size: 2rem;
  }
}
</style>
