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
        <div class="avatar-wrap">
          <div class="avatar" aria-hidden="true">
            <img v-if="avatarUrl" :src="avatarUrl" alt="" />
            <span v-else>{{ initials }}</span>
          </div>
          <label class="avatar-upload">
            Mainīt bildi
            <input type="file" accept="image/*" @change="uploadAvatar" />
          </label>
          <button
            v-if="avatarUrl"
            type="button"
            class="avatar-remove"
            :disabled="isUploadingAvatar"
            @click="removeAvatar"
          >
            Noņemt bildi
          </button>
        </div>
        <div>
          <p class="eyebrow">mans profils</p>
          <h1>{{ currentUser.name }}</h1>
          <p>{{ currentUser.email }}</p>
        </div>
        <div class="profile-actions">
          <button type="button" class="logout-btn" @click="logout">Iziet</button>
          <button type="button" class="delete-profile-btn" @click="showDeleteProfile = true">
            Dzēst profilu
          </button>
        </div>
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

      <section v-if="currentUser.role === 'admin'" class="admin-section">
        <div class="section-heading">
          <p class="eyebrow">admin</p>
          <h2>Visi lietotāju profili</h2>
        </div>

        <div v-if="adminUsers.length === 0" class="soft-empty">
          Vēl nav citu lietotāju.
        </div>

        <div v-else class="admin-users-list">
          <article v-for="user in adminUsers" :key="user.id" class="admin-user-card">
            <div>
              <span class="admin-role">{{ user.role }}</span>
              <h3>{{ user.name }}</h3>
              <p>{{ user.email }}</p>
              <small>
                Favorīti: {{ user.favorites_count }} · Ieraksti: {{ user.logs_count }} · Jautājumi: {{ user.questions_count }}
              </small>
            </div>

            <form class="admin-user-form" @submit.prevent="updateAdminUser(user)">
              <input v-model="adminForms[user.id].name" type="text" required maxlength="255" />
              <input v-model="adminForms[user.id].email" type="email" required maxlength="255" />
              <select v-model="adminForms[user.id].role" required>
                <option value="user">user</option>
                <option value="admin">admin</option>
              </select>
              <button type="submit" :disabled="adminSavingUserId === user.id">
                {{ adminSavingUserId === user.id ? 'Saglabā...' : 'Saglabāt' }}
              </button>
              <button
                type="button"
                class="admin-delete-user-btn"
                :disabled="adminDeletingUserId === user.id || user.id === currentUser.id"
                @click="deleteAdminUser(user)"
              >
                {{ adminDeletingUserId === user.id ? 'Dzēš...' : 'Dzēst profilu' }}
              </button>
            </form>
          </article>
        </div>

        <p v-if="adminMessage" class="success-message">{{ adminMessage }}</p>
        <p v-if="adminError" class="error-message">{{ adminError }}</p>
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
            <div class="favorite-content">
              <span>{{ hobby.category?.name }}</span>
              <h3>{{ hobby.name }}</h3>
              <p>{{ hobby.description }}</p>
              <button type="button" @click="removeFavorite(hobby)">Noņemt no favorītiem</button>
            </div>
          </article>
        </div>
      </section>

      <section class="theme-section">
        <div class="theme-toggle-row">
          <div>
            <h3>Tumšais režīms</h3>
            <p>Pārslēdz visas mājaslapas krāsu uz tumšo noformējumu.</p>
          </div>
          <button
            type="button"
            class="theme-toggle"
            :class="{ active: isDarkMode }"
            :aria-pressed="isDarkMode"
            @click="toggleDarkMode"
          >
            <span></span>
            {{ isDarkMode ? 'Ieslēgts' : 'Izslēgts' }}
          </button>
        </div>
      </section>
    </main>

    <div v-if="showDeleteProfile" class="delete-overlay" @click.self="closeDeleteProfile">
      <div class="delete-modal">
        <button type="button" class="close-btn" @click="closeDeleteProfile">x</button>
        <p class="eyebrow">drošības pārbaude</p>
        <h2>Dzēst profilu?</h2>
        <p class="delete-warning">
          Šī darbība neatgriezeniski izdzēsīs tavu kontu no datubāzes. Lai turpinātu, ievadi paroli un tekstu IZDZESTPROFILU.
        </p>

        <form class="delete-form" @submit.prevent="deleteProfile">
          <label>
            Parole
            <input v-model="deleteProfileForm.password" type="password" required />
          </label>

          <label>
            Apstiprinājuma teksts
            <input
              v-model="deleteProfileForm.confirmation"
              type="text"
              required
              placeholder="IZDZESTPROFILU"
            />
          </label>

          <button type="submit" :disabled="isDeletingProfile">
            {{ isDeletingProfile ? 'Dzēš...' : 'Dzēst profilu pavisam' }}
          </button>
        </form>

        <p v-if="deleteProfileError" class="error-message">{{ deleteProfileError }}</p>
      </div>
    </div>

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
import { API_URL } from '@/config/api'

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
      adminUsers: [],
      adminForms: {},
      adminMessage: '',
      adminError: '',
      adminSavingUserId: null,
      adminDeletingUserId: null,
      isSaving: false,
      isUploadingAvatar: false,
      isDeletingProfile: false,
      showDeleteProfile: false,
      deleteProfileError: '',
      deleteProfileForm: {
        password: '',
        confirmation: '',
      },
      successMessage: '',
      errorMessage: '',
      isDarkMode: false,
    }
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

    initials() {
      return this.currentUser?.name
        ?.split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase() || 'HS'
    },
    avatarUrl() {
      if (!this.currentUser?.avatar_path) {
        return ''
      }

      if (this.currentUser.avatar_path.startsWith('http')) {
        return this.currentUser.avatar_path
      }

      return `${API_URL.replace('/api', '')}${this.currentUser.avatar_path}`
    },
  },
  mounted() {
    this.currentUser = JSON.parse(localStorage.getItem('hobispace_user') || 'null')
    this.isDarkMode = localStorage.getItem('hobispace_dark_mode') === 'true'
    this.applyDarkMode()

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
    applyDarkMode() {
      document.body.classList.toggle('dark-mode', this.isDarkMode)
    },
    toggleDarkMode() {
      this.isDarkMode = !this.isDarkMode
      localStorage.setItem('hobispace_dark_mode', String(this.isDarkMode))
      this.applyDarkMode()
    },
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

      if (this.currentUser.role === 'admin') {
        this.loadAdminUsers()
      }
    },
    async loadFavorites() {
      this.favorites = await this.request(`/favorites/${this.currentUser.id}`)
    },
    async loadAdminUsers() {
      this.adminUsers = await this.request(`/admin/users?admin_id=${this.currentUser.id}`)
      this.adminForms = this.adminUsers.reduce((forms, user) => {
        forms[user.id] = {
          name: user.name,
          email: user.email,
          role: user.role,
        }
        return forms
      }, {})
    },
    async updateAdminUser(user) {
      this.adminSavingUserId = user.id
      this.adminMessage = ''
      this.adminError = ''

      try {
        const data = await this.request(`/admin/users/${user.id}`, {
          method: 'PUT',
          body: JSON.stringify({
            ...this.adminForms[user.id],
            admin_id: this.currentUser.id,
          }),
        })

        this.adminMessage = data.message
        await this.loadAdminUsers()

        if (user.id === this.currentUser.id) {
          this.currentUser = data.user
          localStorage.setItem('hobispace_user', JSON.stringify(data.user))
        }
      } catch (error) {
        this.adminError = error.message
      } finally {
        this.adminSavingUserId = null
      }
    },
    async deleteAdminUser(user) {
      if (user.id === this.currentUser.id) {
        this.adminError = 'Tu nevari izdzēst pats savu admin profilu no šī saraksta.'
        return
      }

      if (!confirm(`Vai tiešām dzēst lietotāja "${user.name}" profilu?`)) {
        return
      }

      this.adminDeletingUserId = user.id
      this.adminMessage = ''
      this.adminError = ''

      try {
        const data = await this.request(`/admin/users/${user.id}`, {
          method: 'DELETE',
          body: JSON.stringify({
            admin_id: this.currentUser.id,
          }),
        })

        this.adminMessage = data.message
        await this.loadAdminUsers()
        await this.loadProfile()
      } catch (error) {
        this.adminError = error.message
      } finally {
        this.adminDeletingUserId = null
      }
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
    async uploadAvatar(event) {
      const file = event.target.files?.[0]

      if (!file) {
        return
      }

      this.isUploadingAvatar = true
      this.successMessage = ''
      this.errorMessage = ''

      const formData = new FormData()
      formData.append('avatar', file)

      try {
        const response = await fetch(`${API_URL}/users/${this.currentUser.id}/avatar`, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
          },
          body: formData,
        })

        const data = await response.json()

        if (!response.ok) {
          throw new Error(data.message || 'Profila bildi neizdevās saglabāt.')
        }

        this.currentUser = data.user
        localStorage.setItem('hobispace_user', JSON.stringify(data.user))
        this.successMessage = data.message
      } catch (error) {
        this.errorMessage = error.message
      } finally {
        this.isUploadingAvatar = false
        event.target.value = ''
      }
    },
    async removeAvatar() {
      this.isUploadingAvatar = true
      this.successMessage = ''
      this.errorMessage = ''

      try {
        const response = await fetch(`${API_URL}/users/${this.currentUser.id}/avatar`, {
          method: 'DELETE',
          headers: {
            Accept: 'application/json',
          },
        })

        const data = await response.json()

        if (!response.ok) {
          throw new Error(data.message || 'Profila bildi neizdevās noņemt.')
        }

        this.currentUser = data.user
        localStorage.setItem('hobispace_user', JSON.stringify(data.user))
        this.successMessage = data.message
      } catch (error) {
        this.errorMessage = error.message
      } finally {
        this.isUploadingAvatar = false
      }
    },
    closeDeleteProfile() {
      this.showDeleteProfile = false
      this.deleteProfileError = ''
      this.deleteProfileForm = {
        password: '',
        confirmation: '',
      }
    },
    async deleteProfile() {
      this.isDeletingProfile = true
      this.deleteProfileError = ''

      try {
        const response = await fetch(`${API_URL}/users/${this.currentUser.id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
          },
          body: JSON.stringify(this.deleteProfileForm),
        })

        const data = await response.json()

        if (!response.ok) {
          throw new Error(data.message || 'Profilu neizdevās izdzēst.')
        }

        localStorage.removeItem('hobispace_user')
        this.currentUser = null
        this.closeDeleteProfile()
        this.$router.push('/hobbies')
      } catch (error) {
        this.deleteProfileError = error.message
      } finally {
        this.isDeletingProfile = false
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
    }
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
  gap: 1rem;
  flex-wrap: wrap;
}

.navbar-left,
.logo-container,
.nav-links {
  display: flex;
  align-items: center;
}

.navbar-left {
  gap: 2rem;
  min-width: 0;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: 1rem;
  min-width: 0;
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
  flex-wrap: wrap;
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
  flex-wrap: wrap;
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

.avatar-wrap {
  display: grid;
  gap: 0.55rem;
  justify-items: center;
}

.avatar {
  width: 92px;
  height: 92px;
  display: grid;
  place-items: center;
  border-radius: 50%;
  background: #BC4527;
  color: #FDF8F0;
  font-size: 2rem;
  font-weight: 900;
  overflow: hidden;
}

.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
}

.avatar-upload {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 34px;
  padding: 0.45rem 0.75rem;
  border-radius: 8px;
  background: #ffffff;
  color: #BC4527;
  border: 1px solid #d8c8bc;
  cursor: pointer;
  font-size: 0.82rem;
  font-weight: 900;
}

.avatar-upload input {
  display: none;
}

.avatar-remove {
  min-height: 32px;
  padding: 0.4rem 0.7rem;
  border: 0;
  border-radius: 8px;
  background: transparent;
  color: #9b1c1c;
  cursor: pointer;
  font: inherit;
  font-size: 0.78rem;
  font-weight: 900;
}

.avatar-remove:disabled {
  opacity: 0.6;
  cursor: wait;
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

.profile-hero .eyebrow {
  font-size: 1.35rem;
  letter-spacing: 0;
  margin-bottom: 0.55rem;
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

.profile-actions {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.logout-btn {
  background: #ffffff;
  color: #9b1c1c;
}

.delete-profile-btn {
  border: 0;
  border-radius: 8px;
  padding: 0.8rem 1rem;
  background: #9b1c1c;
  color: #ffffff;
  cursor: pointer;
  font: inherit;
  font-weight: 800;
}

.delete-overlay {
  position: fixed;
  inset: 0;
  z-index: 1500;
  display: grid;
  place-items: center;
  padding: 1rem;
  background: rgba(0, 0, 0, 0.55);
}

.delete-modal {
  position: relative;
  width: min(100%, 460px);
  padding: 1.5rem;
  border-radius: 8px;
  background: #ffffff;
  color: #1f1f1f;
  box-shadow: 0 18px 50px rgba(0, 0, 0, 0.2);
}

.delete-modal .close-btn {
  position: absolute;
  top: 0.8rem;
  right: 0.8rem;
  border: 0;
  background: transparent;
  color: #1f1f1f;
  cursor: pointer;
  font-size: 1.4rem;
  font-weight: 900;
}

.delete-modal h2 {
  margin: 0 0 0.75rem;
}

.delete-warning {
  margin: 0 0 1rem;
  color: #62554e;
  line-height: 1.5;
  font-weight: 700;
}

.delete-form {
  display: grid;
  gap: 1rem;
}

.delete-form label {
  display: grid;
  gap: 0.4rem;
  font-weight: 800;
}

.delete-form input {
  width: 100%;
  border: 1px solid #d8c8bc;
  border-radius: 8px;
  padding: 0.85rem 0.95rem;
  font: inherit;
  background: #fffaf5;
}

.delete-form button {
  border: 0;
  border-radius: 8px;
  padding: 0.85rem 1rem;
  background: #9b1c1c;
  color: #ffffff;
  cursor: pointer;
  font: inherit;
  font-weight: 900;
}

.delete-form button:disabled {
  opacity: 0.7;
  cursor: wait;
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

.admin-section {
  margin-top: 1rem;
  padding: 1.25rem;
  border: 1px solid #eadfd5;
  border-radius: 8px;
  background: #ffffff;
}

.admin-users-list {
  display: grid;
  gap: 1rem;
}

.admin-user-card {
  display: grid;
  grid-template-columns: minmax(0, 0.9fr) minmax(320px, 1.1fr);
  gap: 1rem;
  align-items: start;
  padding: 1rem;
  border: 1px solid #eadfd5;
  border-radius: 8px;
  background: #fffaf5;
}

.admin-user-card h3 {
  margin: 0.25rem 0;
}

.admin-user-card p,
.admin-user-card small {
  color: #62554e;
  font-weight: 700;
}

.admin-role {
  display: inline-flex;
  padding: 0.25rem 0.5rem;
  border-radius: 999px;
  background: #f6dfce;
  color: #BC4527;
  font-size: 0.78rem;
  font-weight: 900;
  text-transform: uppercase;
}

.admin-user-form {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.65rem;
}

.admin-user-form input,
.admin-user-form select {
  width: 100%;
  border: 1px solid #d8c8bc;
  border-radius: 8px;
  padding: 0.75rem 0.85rem;
  font: inherit;
  background: #ffffff;
}

.admin-user-form button {
  border: 0;
  border-radius: 8px;
  padding: 0.8rem 1rem;
  background: #BC4527;
  color: #FDF8F0;
  cursor: pointer;
  font: inherit;
  font-weight: 900;
}

.admin-user-form .admin-delete-user-btn {
  background: #9b1c1c;
}

.admin-user-form button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
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

.favorites-section,
.theme-section {
  margin-top: 1rem;
}

.theme-toggle-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
  padding: 1rem;
  border: 1px solid #eadfd5;
  border-radius: 8px;
  background: #fffaf5;
}

.theme-toggle-row h3 {
  margin: 0 0 0.25rem;
  font-size: 1.05rem;
}

.theme-toggle-row p {
  margin: 0;
  color: #62554e;
  font-weight: 700;
}

.theme-toggle {
  min-width: 116px;
  border: 1px solid #d8c8bc;
  border-radius: 999px;
  padding: 0.35rem 0.55rem;
  background: #ffffff;
  color: #1f1f1f;
  cursor: pointer;
  font: inherit;
  font-weight: 800;
}

.theme-toggle span {
  display: inline-block;
  width: 14px;
  height: 14px;
  margin-right: 0.35rem;
  border-radius: 999px;
  background: #d8c8bc;
  vertical-align: -2px;
}

.theme-toggle.active {
  background: #BC4527;
  color: #FDF8F0;
  border-color: #BC4527;
}

.theme-toggle.active span {
  background: #FDF8F0;
}

.favorites-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.favorite-card {
  padding: 1rem;
  border: 1px solid #eadfd5;
  border-radius: 8px;
  background: #fffaf5;
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
  .navbar {
    padding: 0.85rem 1rem;
    justify-content: center;
  }

  .navbar-left,
  .navbar-right {
    width: 100%;
    justify-content: center;
    gap: 0.85rem;
  }

  .navbar-left,
  .navbar-right {
    flex-direction: column;
  }

  .logo-container {
    justify-content: center;
  }

  .logo {
    height: 34px;
  }

  .project-name {
    font-size: 1.25rem;
  }

  .nav-links {
    width: 100%;
    justify-content: center;
    gap: 0.45rem;
  }

  .nav-links a {
    display: inline-flex;
    padding: 0.45rem 0.65rem;
    border-radius: 8px;
    background-color: #ffffff;
    font-size: 0.92rem;
    line-height: 1;
  }

  .search-bar {
    width: 100%;
    max-width: 360px;
    border: 1px solid #eadfd5;
    background-color: #ffffff;
  }

  .auth-buttons {
    width: 100%;
    max-width: 360px;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .login-btn,
  .signup-btn {
    width: 100%;
    text-align: center;
    padding: 0.65rem 0.75rem;
    font-size: 0.92rem;
  }

  .profile-shell,
  .empty-profile {
    padding: 1.5rem 1rem 3rem;
  }

  .profile-hero {
    grid-template-columns: 1fr;
    text-align: center;
    justify-items: center;
    padding: 1.5rem;
  }

  .profile-actions {
    justify-content: center;
  }

  .stats-grid,
  .content-grid,
  .favorites-grid,
  .admin-user-card,
  .admin-user-form {
    grid-template-columns: 1fr;
  }

  .profile-hero h1 {
    font-size: 2rem;
  }
  .favorite-card {
    text-align: center;
  }
}

@media (max-width: 420px) {
  .navbar {
    padding: 0.75rem;
  }

  .nav-links {
    gap: 0.35rem;
  }

  .nav-links a {
    font-size: 0.86rem;
    padding: 0.42rem 0.5rem;
  }

  .auth-buttons {
    grid-template-columns: 1fr;
  }

  .empty-profile h1,
  .profile-hero h1 {
    font-size: 1.75rem;
  }

  .avatar {
    width: 76px;
    height: 76px;
    font-size: 1.6rem;
  }

  .profile-panel,
  .favorites-section,
  .stats-grid article {
    padding: 1rem;
  }
}
</style>
