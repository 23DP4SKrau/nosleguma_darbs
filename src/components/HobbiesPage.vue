<template>
  <div class="hobbies-page">
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

    <section class="hobbies-hero">
      <div class="hobbies-hero-content">
        <p class="collection-label">kolekcija</p>
        <h1>Hobiji, kurus vērts saglabāt</h1>
        <p>Uzklikšķiniet uz jebkura hobija, lai atvērtu tā pierakstus, nospiediet sirsniņu, lai saglabātu savus mīļākos hobijus, un pievienojiet jaunu hobiju, kuru vēl neesi šeit ieraudzījis.</p>
      </div>
    </section>

    <section class="hobbies-section">
      <div class="hobbies-actions">
        <button class="add-hobby-btn" type="button" @click="openAddModal">
          Pievienot hobiju
        </button>
      </div>

      <div class="hobbies-grid">
        <div v-for="hobby in filteredHobbies" :key="hobby.id" class="hobby-card" @click="openLogModal(hobby)">
          <div class="hobby-image">
            <img :src="getHobbyImage(hobby)" :alt="hobby.name" class="hobby-photo" />
          </div>
          <h3>{{ hobby.name }}</h3>
          <div class="hobby-meta">
            <span>{{ hobby.category?.name || 'Hobijs' }}</span>
            <span>{{ formatCost(hobby.estimated_cost) }}</span>
          </div>
          <p class="hobby-description">{{ hobby.description }}</p>
          <div class="hobby-card-actions">
          <button
            class="heart-btn"
            :class="{ active: hobby.isFavorite }"
            title="Pievienot favorītiem"
            @click.stop="toggleFavorite(hobby.id)"
          >
            ♥
          </button>
            <button class="card-action-btn" type="button" @click.stop="openLogModal(hobby)">
              Ieraksti
            </button>
            <button v-if="canManageHobby(hobby)" class="card-action-btn" type="button" @click.stop="openEditHobbyModal(hobby)">
              Labot
            </button>
            <button v-if="canManageHobby(hobby)" class="card-action-btn danger" type="button" @click.stop="deleteHobby(hobby)">Dzēst</button>
          </div>
        </div>
      </div>
    </section>

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

    <div v-if="showLogModal" class="log-overlay" @click.self="closeLogModal">
      <div class="log-modal">
        <button class="close-btn" @click="closeLogModal">×</button>
        <h2>{{ selectedHobby.name }} ieraksti</h2>

        <div class="log-list-panel">
          <div class="log-list-heading">
            <h3>Saglabātie ieraksti</h3>
            <span>{{ selectedHobbyLogs.length }}</span>
          </div>

          <div v-if="isLoadingLogs" class="log-empty">Ielādē ierakstus...</div>
          <div v-else-if="selectedHobbyLogs.length === 0" class="log-empty">
            Šim hobijam vēl nav ierakstu.
          </div>

          <article v-for="log in selectedHobbyLogs" :key="log.id" class="log-item">
            <div>
              <strong>{{ log.title }}</strong>
              <span>{{ formatDate(log.log_date) }}</span>
              <p v-if="log.notes">{{ log.notes }}</p>
              <small v-if="log.duration_minutes || log.mood_rating">
                <template v-if="log.duration_minutes">{{ log.duration_minutes }} min.</template>
                <template v-if="log.duration_minutes && log.mood_rating"> / </template>
                <template v-if="log.mood_rating">Noskaņojums: {{ log.mood_rating }}/5</template>
              </small>
            </div>
            <div class="log-item-actions">
              <button type="button" @click="startEditLog(log)">Labot</button>
              <button type="button" class="danger" @click="deleteLog(log)">Dzēst</button>
            </div>
          </article>
        </div>
        <form @submit.prevent="saveLog">
          <h3 class="form-title">{{ isEditingLog ? 'Labot ierakstu' : 'Pievienot jaunu ierakstu' }}</h3>
          <div class="form-group">
            <label>Datums</label>
            <input type="date" v-model="logForm.date" required />
          </div>

          <div class="form-group">
            <label>Nosaukums</label>
            <input type="text" v-model="logForm.title" required maxlength="255" />
          </div>

          <div v-if="getSpecificLabel(selectedHobby)" class="form-group">
            <label>{{ getSpecificLabel(selectedHobby) }}</label>
            <input type="text" v-model="logForm.specific" />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Pavadītais laiks min</label>
              <input type="number" v-model.number="logForm.duration_minutes" min="1" max="1440" />
            </div>

            <div class="form-group">
              <label>Noskaņojums</label>
              <select v-model.number="logForm.mood_rating">
                <option value="">Nav vērtējuma</option>
                <option v-for="rating in 5" :key="rating" :value="rating">{{ rating }}</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Ziņa</label>
            <textarea v-model="logForm.message" rows="3" placeholder="Pievienojiet savu ziņu..."></textarea>
          </div>
          <button v-if="isEditingLog" type="button" class="secondary-btn edit-cancel-btn" @click="resetLogForm">
            Atcelt labošanu
          </button>
          <button type="submit" class="save-btn">
            {{ isEditingLog ? 'Saglabāt izmaiņas' : 'Saglabāt ierakstu' }}
          </button>
        </form>
      </div>
    </div>

    <div v-if="showAddModal" class="log-overlay" @click.self="closeAddModal">
      <div class="log-modal add-hobby-modal">
        <button class="close-btn" type="button" @click="closeAddModal">×</button>
        <h2>{{ isEditingHobby ? 'Labot hobiju' : 'Pievienot hobiju' }}</h2>

        <form @submit.prevent="saveHobby">
          <div class="form-group">
            <label>Nosaukums</label>
            <input v-model="addForm.name" type="text" required maxlength="255" />
          </div>

          <div class="form-group">
            <label>Tips</label>
            <select v-model="addForm.category_id" required>
              <option value="" disabled>Izvēlies kategoriju</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Izmaksas</label>
            <select v-model="addForm.estimated_cost" required>
              <option value="bezmaksas">Bezmaksas</option>
              <option value="zemas">Zemas</option>
              <option value="videjas">Vidējas</option>
              <option value="augstas">Augstas</option>
            </select>
          </div>

          <div v-if="!isEditingHobby" class="form-group">
            <label>Ikona</label>
            <input type="file" accept="image/*" required @change="handleImageUpload" />
          </div>

          <div v-if="imagePreview" class="add-preview">
            <img :src="imagePreview" alt="Izvēlētā hobija bilde" />
          </div>

          <div class="form-group">
            <label>Hobija apraksts</label>
            <textarea v-model="addForm.description" rows="4" required minlength="10"></textarea>
          </div>

          <div class="form-group">
            <label>Pieraksta papildlauka nosaukums</label>
            <input
              v-model="addForm.custom_log_label"
              type="text"
              maxlength="255"
              placeholder="Piemēram, dziesmas nosaukums"
            />
          </div>

          <button type="submit" class="save-btn">
            {{ isEditingHobby ? 'Saglabāt izmaiņas' : 'Pievienot kartīti' }}
          </button>
        </form>
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
  name: 'HobbiesPage',
  data() {
    return {
      searchQuery: '',
      mobileMenuOpen: false,
      showAuth: false,
      isLogin: true,
      showPassword: false,
      isAdminLogin: false,
      showLogModal: false,
      showAddModal: false,
      isEditingHobby: false,
      isEditingLog: false,
      editingHobbyId: null,
      editingLogId: null,
      selectedHobbyLogs: [],
      isLoadingLogs: false,
      selectedHobby: null,
      logForm: {
        date: '',
        title: '',
        message: '',
        specific: '',
        duration_minutes: '',
        mood_rating: '',
      },
      logs: [],
      currentUser: null,
      categories: [
        { id: 1, name: 'Sports' },
        { id: 2, name: 'Radošie hobiji' },
        { id: 3, name: 'Mūzika' },
        { id: 4, name: 'Daba' },
        { id: 5, name: 'Mājai' },
        { id: 6, name: 'Digitālie hobiji' },
      ],
      addForm: {
        name: '',
        category_id: '',
        estimated_cost: 'bezmaksas',
        difficulty: 'viegls',
        imageFile: null,
        description: '',
        custom_log_label: '',
      },
      imagePreview: '',
      form: {
        name: '',
        email: '',
        password: ''
      },
      hobbies: [
        { id: 1, name: 'Fotografēšana', isFavorite: false, description: 'Saglabā atmiņu par kādu skaistu bildi.' },
        { id: 2, name: 'Skriešana', isFavorite: false, description: 'Saglabā katru noskrietu kilometru.' },
        { id: 3, name: 'Dejošana', isFavorite: false, description: 'Kustieties pēc mūzikas sajūtot ritmu.' },
        { id: 4, name: 'Dziedāšana', isFavorite: false, description: 'Izvēlies mīļāko dziesmu un dziedi to ar pārliecību.' },
        { id: 5, name: 'Šūšana', isFavorite: false, description: 'Uztaisiet skaistu apģērba gabalu.' },
        { id: 6, name: 'Lasīšana', isFavorite: false, description: 'Atklājiet stāstus, idejas un iedvesmu caur katru lapu.' },
        { id: 7, name: 'Fitness', isFavorite: false, description: 'Trenējiet savu ķermeni ar spēku, elastību un pozitīvām domām.' },
        { id: 8, name: 'Gleznošana', isFavorite: false, description: 'Atvediet krāsu un iztēli dzīvē uz papīra.' },
        { id: 9, name: 'Rakstīšana', isFavorite: false, description: 'Veidojiet esejas, stāstus vai kādu interesantu rakstu.' },
        { id: 10, name: 'Riteņbraukšana', isFavorite: false, description: 'Izpētiet jaunus maršrutus un baudiet brīvīvo laiku ārā.' },
        { id: 11, name: 'Peldēšana', isFavorite: false, description: 'Relaksējies ūdenī, vienlaikus uzlabojot spēku un izturību.' },
        { id: 12, name: 'Mūzika', isFavorite: false, description: 'Mācieties instrumentus, sitienus un ritmus, kas kustina jūsu dzirdi.' },
        { id: 13, name: 'Makšķerēšana', isFavorite: false, description: 'Baudiet mierīgu ezeru laiku makšķerējot.' },
        { id: 14, name: 'Videospēļu spēlēšana', isFavorite: false, description: 'Spēlējiet stratēģijas, piedzīvojumu un sociālās spēles ar draugiem.' },
        { id: 15, name: 'Ēdiena gatavošana', isFavorite: false, description: 'Veidojiet garšīgus ēdienus un eksperimentējiet ar receptēm.' },
        { id: 16, name: 'Cepšana', isFavorite: false, description: 'Samaisiet, cepiet un pasniedziet saldumus mājīgiem un siltiem laikiem.' },
        { id: 17, name: 'Rokdarbi', isFavorite: false, description: 'Veidojiet paštaisītus rokdarbus, izmantojot savu radošumu.' }
      ]
    }
  },
  async mounted() {
    this.currentUser = JSON.parse(localStorage.getItem('hobispace_user') || 'null')

    await this.loadCategories()
    await this.loadAddedHobbies()

    if (this.currentUser) {
      await this.loadFavorites()
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

    filteredHobbies() {
      if (!this.searchQuery) {
        return this.hobbies
      }
      return this.hobbies.filter(hobby =>
        hobby.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      )
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
    }
  },
  methods: {
    toggleMobileMenu() {
      this.mobileMenuOpen = !this.mobileMenuOpen
    },
    closeMobileMenu() {
      this.mobileMenuOpen = false
    },
    normalizeHobby(hobby) {
      const existing = this.hobbies.find((item) => item.id === hobby.id)
      const defaultText = this.getDefaultHobbyText(hobby.id)

      return {
        ...hobby,
        ...defaultText,
        image: hobby.image || existing?.image || this.getDefaultHobbyImageKey(hobby),
        isFavorite: existing?.isFavorite || false,
      }
    },
    normalizeCategory(category) {
      const namesById = {
        1: 'Sports',
        2: 'Radošie hobiji',
        3: 'Mūzika',
        4: 'Daba',
        5: 'Mājai',
        6: 'Digitālie hobiji',
      }

      return {
        ...category,
        name: namesById[category.id] || category.name,
      }
    },
    canManageHobby(hobby) {
      if (!this.currentUser) {
        return false
      }

      return true
    },
    formatCost(value) {
      const labels = {
        bezmaksas: 'Bezmaksas',
        zemas: 'Zemas izmaksas',
        videjas: 'Vidējas izmaksas',
        augstas: 'Augstas izmaksas',
      }

      return labels[value] || 'Nav norādīts'
    },
    formatDate(value) {
      return new Intl.DateTimeFormat('lv-LV').format(new Date(value))
    },
    async loadCategories() {
      try {
        const response = await fetch(`${API_URL}/categories`, {
          headers: { Accept: 'application/json' },
        })

        if (response.ok) {
          const categories = await response.json()
          this.categories = categories.map((category) => this.normalizeCategory(category))
        }
      } catch (error) {
        // Forma var strādāt arī ar noklusējuma tipiem, ja serveris nav palaists.
      }
    },
    async loadAddedHobbies() {
      try {
        const response = await fetch(`${API_URL}/hobbies`, {
          headers: { Accept: 'application/json' },
        })

        if (!response.ok) {
          return
        }

        const databaseHobbies = await response.json()
        this.hobbies = databaseHobbies.map((hobby) => this.normalizeHobby(hobby))
      } catch (error) {
        // Pamata hobiju kartītes paliek redzamas arī bez servera.
      }
    },
    async loadFavorites() {
      try {
        const response = await fetch(`${API_URL}/favorites/${this.currentUser.id}`, {
          headers: { Accept: 'application/json' },
        })
        const favorites = await response.json()
        const favoriteIds = favorites.map((hobby) => hobby.id)

        this.hobbies = this.hobbies.map((hobby) => ({
          ...hobby,
          isFavorite: favoriteIds.includes(hobby.id),
        }))
      } catch (error) {
        alert('Neizdevās ielādēt favorītus.')
      }
    },
    async toggleFavorite(hobbyId) {
      if (!this.currentUser) {
        this.openLogin()
        return
      }

      const hobby = this.hobbies.find(h => h.id === hobbyId)
      if (hobby) {
        try {
          const response = await fetch(`${API_URL}/favorites/toggle`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Accept: 'application/json',
            },
            body: JSON.stringify({
              user_id: this.currentUser.id,
              hobby_id: hobby.id,
            }),
          })
          const data = await response.json()

          if (!response.ok) {
            alert(data.message || 'Neizdevās saglabāt favorītu.')
            return
          }

          hobby.isFavorite = data.is_favorite
        } catch (error) {
          alert('Nevar pieslēgties Laravel serverim.')
        }
      }
    },
    openAddModal() {
      if (!this.currentUser) {
        this.openLogin()
        return
      }

      this.isEditingHobby = false
      this.editingHobbyId = null
      this.showAddModal = true
    },
    closeAddModal() {
      this.showAddModal = false
      this.isEditingHobby = false
      this.editingHobbyId = null
      this.addForm = {
        name: '',
        category_id: '',
        estimated_cost: 'bezmaksas',
        difficulty: 'viegls',
        imageFile: null,
        description: '',
        custom_log_label: '',
      }
      this.imagePreview = ''
    },
    handleImageUpload(event) {
      const file = event.target.files?.[0]
      this.addForm.imageFile = file || null
      this.imagePreview = file ? URL.createObjectURL(file) : ''
    },
    openEditHobbyModal(hobby) {
      if (!this.canManageHobby(hobby)) {
        return
      }

      this.isEditingHobby = true
      this.editingHobbyId = hobby.id
      this.addForm = {
        name: hobby.name,
        category_id: hobby.category_id || hobby.category?.id || '',
        estimated_cost: hobby.estimated_cost || 'bezmaksas',
        difficulty: hobby.difficulty || 'viegls',
        imageFile: null,
        description: hobby.description,
        custom_log_label: hobby.custom_log_label || '',
      }
      this.imagePreview = hobby.image ? this.getHobbyImage(hobby) : ''
      this.showAddModal = true
    },
    async deleteHobby(hobby) {
      if (!this.canManageHobby(hobby)) {
        return
      }

      if (!confirm(`Vai tiešām vēlies dzēst hobiju "${hobby.name}"?`)) {
        return
      }

      try {
        const response = await fetch(`${API_URL}/hobbies/${hobby.id}`, {
          method: 'DELETE',
          headers: { Accept: 'application/json' },
        })
        const data = await response.json()

        if (!response.ok) {
          alert(data.message || 'Neizdevās dzēst hobiju.')
          return
        }

        this.hobbies = this.hobbies.filter((item) => item.id !== hobby.id)
      } catch (error) {
        alert('Nevar pieslēgties Laravel serverim.')
      }
    },
    async saveHobby() {
      try {
        const isEditing = this.isEditingHobby && this.editingHobbyId
        let response

        if (isEditing) {
          response = await fetch(`${API_URL}/hobbies/${this.editingHobbyId}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              Accept: 'application/json',
            },
            body: JSON.stringify({
              name: this.addForm.name,
              category_id: this.addForm.category_id,
              estimated_cost: this.addForm.estimated_cost,
              difficulty: this.addForm.difficulty,
              description: this.addForm.description,
              custom_log_label: this.addForm.custom_log_label || null,
              image: this.hobbies.find((hobby) => hobby.id === this.editingHobbyId)?.image || null,
              is_public: true,
            }),
          })
        } else {
        const formData = new FormData()
        formData.append('name', this.addForm.name)
        formData.append('category_id', this.addForm.category_id)
        formData.append('estimated_cost', this.addForm.estimated_cost)
        formData.append('difficulty', this.addForm.difficulty)
        formData.append('description', this.addForm.description)
        formData.append('custom_log_label', this.addForm.custom_log_label || '')
        formData.append('user_id', this.currentUser.id)

        if (this.addForm.imageFile) {
          formData.append('image_file', this.addForm.imageFile)
        }

          response = await fetch(`${API_URL}/hobbies`, {
            method: 'POST',
          headers: {
            Accept: 'application/json',
          },
          body: formData,
        })
        }
        const data = await response.json()

        if (!response.ok) {
          alert(data.message || 'Neizdevās pievienot hobiju.')
          return
        }

        const savedHobby = this.normalizeHobby(data.hobby)

        if (isEditing) {
          this.hobbies = this.hobbies.map((hobby) => hobby.id === savedHobby.id ? savedHobby : hobby)
        } else {
          this.hobbies.push(savedHobby)
        }
        this.closeAddModal()
      } catch (error) {
        alert('Nevar pieslēgties Laravel serverim.')
      }
    },
    async openLogModal(hobby) {
      if (!this.currentUser) {
        this.openLogin()
        return
      }

      this.selectedHobby = hobby
      this.showLogModal = true
      this.resetLogForm()
      await this.loadSelectedHobbyLogs()
    },
    closeLogModal() {
      this.showLogModal = false
      this.selectedHobby = null
      this.selectedHobbyLogs = []
      this.resetLogForm()
    },
    resetLogForm() {
      this.isEditingLog = false
      this.editingLogId = null
      this.logForm = {
        date: new Date().toISOString().split('T')[0],
        title: this.selectedHobby ? `${this.selectedHobby.name} ieraksts` : '',
        message: '',
        specific: '',
        duration_minutes: '',
        mood_rating: '',
      }
    },
    async loadSelectedHobbyLogs() {
      if (!this.selectedHobby || !this.currentUser) {
        return
      }

      this.isLoadingLogs = true

      try {
        const response = await fetch(`${API_URL}/hobby-logs?user_id=${this.currentUser.id}&hobby_id=${this.selectedHobby.id}`, {
          headers: { Accept: 'application/json' },
        })

        if (response.ok) {
          this.selectedHobbyLogs = await response.json()
        }
      } catch (error) {
        alert('Neizdevās ielādēt ierakstus.')
      } finally {
        this.isLoadingLogs = false
      }
    },
    startEditLog(log) {
      this.isEditingLog = true
      this.editingLogId = log.id
      this.logForm = {
        date: String(log.log_date).slice(0, 10),
        title: log.title,
        message: log.notes || '',
        specific: log.specific_value || '',
        duration_minutes: log.duration_minutes || '',
        mood_rating: log.mood_rating || '',
      }
    },
    async deleteLog(log) {
      if (!confirm(`Vai tiešām dzēst ierakstu "${log.title}"?`)) {
        return
      }

      try {
        const response = await fetch(`${API_URL}/hobby-logs/${log.id}`, {
          method: 'DELETE',
          headers: { Accept: 'application/json' },
        })
        const data = await response.json()

        if (!response.ok) {
          alert(data.message || 'Neizdevās dzēst ierakstu.')
          return
        }

        this.selectedHobbyLogs = this.selectedHobbyLogs.filter((item) => item.id !== log.id)
        this.logs = this.logs.filter((item) => item.id !== log.id)
        if (this.editingLogId === log.id) {
          this.resetLogForm()
        }
      } catch (error) {
        alert('Nevar pieslēgties Laravel serverim.')
      }
    },
    async saveLog() {
      try {
        const isEditing = this.isEditingLog && this.editingLogId
        const response = await fetch(`${API_URL}/hobby-logs${isEditing ? `/${this.editingLogId}` : ''}`, {
          method: isEditing ? 'PUT' : 'POST',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
          },
          body: JSON.stringify({
            user_id: this.currentUser.id,
            hobby_id: this.selectedHobby.id,
            log_date: this.logForm.date,
            title: this.logForm.title,
            notes: this.logForm.message || null,
            specific_value: this.logForm.specific || null,
            duration_minutes: this.logForm.duration_minutes || null,
            mood_rating: this.logForm.mood_rating || null,
          }),
        })
        const data = await response.json()

        if (!response.ok) {
          alert(data.message || 'Neizdevās saglabāt ierakstu.')
          return
        }

        if (isEditing) {
          this.selectedHobbyLogs = this.selectedHobbyLogs.map((log) => log.id === data.log.id ? data.log : log)
          this.logs = this.logs.map((log) => log.id === data.log.id ? data.log : log)
        } else {
          this.logs.push(data.log)
          this.selectedHobbyLogs = [data.log, ...this.selectedHobbyLogs]
        }
        alert('Ieraksts saglabāts!')
        this.resetLogForm()
      } catch (error) {
        alert('Nevar pieslēgties Laravel serverim.')
      }
    },
    getSpecificLabel(hobby) {
      if (hobby?.custom_log_label) {
        return hobby.custom_log_label
      }

      const labels = {
        1: 'Fotografēšanas vieta',
        2: 'Distance (km)',
        3: 'Dejas veids',
        4: 'Dziesmas nosaukums',
        5: 'Šūšanas projekts',
        6: 'Grāmatas nosaukums',
        7: 'Vingrinājuma veids',
        8: 'Mākslas darba nosaukums',
        9: 'Raksta nosaukums',
        10: 'Distance (km)',
        11: 'Distance (m)',
        12: 'Instruments vai dziesma',
        13: 'Noķertā zivs',
        14: 'Spēles nosaukums',
        15: 'Ēdiena nosaukums',
        16: 'Receptes nosaukums',
        17: 'Rokdarbu projekts',
        18: 'Projekts'
      }
      return labels[hobby?.id] || ''
    },
    getDefaultHobbyText(id) {
      const texts = {
        1: { name: 'Fotografēšana', description: 'Fiksējiet mirkļus un veidojiet vizuālus stāstus ar katru kadru.' },
        2: { name: 'Skriešana', description: 'Veidojiet izturību un attīriet prātu ar katru kilometru.' },
        3: { name: 'Dejošana', description: 'Kustieties pēc mūzikas un palaidiet savu enerģiju caur ritmu.' },
        4: { name: 'Dziedāšana', description: 'Trenējiet melodijas, palieliniet savu pārliecību un atrodiet savu balsi.' },
        5: { name: 'Šūšana', description: 'Dizainējiet un labojiet auduma projektus ar radošumu un prasmi.' },
        6: { name: 'Lasīšana', description: 'Atklājiet stāstus, idejas un iedvesmu caur katru lapu.' },
        7: { name: 'Fitness', description: 'Trenējiet savu ķermeni ar spēku, elastību un pozitīvām rutīnām.' },
        8: { name: 'Gleznošana', description: 'Atvediet krāsu un iztēli dzīvē uz audekla vai papīra.' },
        9: { name: 'Rakstīšana', description: 'Veidojiet esejas, stāstus un ekspresīvu rakstīšanu ar nolūku.' },
        10: { name: 'Riteņbraukšana', description: 'Izpētiet jaunus maršrutus un baudiet brīvību braukt ārā.' },
        11: { name: 'Peldēšana', description: 'Atpūtieties ūdenī, vienlaikus uzlabojot spēku un izturību.' },
        12: { name: 'Mūzika', description: 'Mācieties instrumentus, sitienus un ritmus, kas kustina jūsu garu.' },
        13: { name: 'Makšķerēšana', description: 'Baudiet mierīgu ezeru laiku un svaiga loma uztraukumu.' },
        14: { name: 'Videospēļu spēlēšana', description: 'Spēlējiet stratēģijas, piedzīvojumu un sociālās spēles ar draugiem.' },
        15: { name: 'Ēdiena gatavošana', description: 'Veidojiet garšīgus ēdienus, eksperimentējiet ar receptēm un barojiet savu prieku.' },
        16: { name: 'Cepšana', description: 'Samaisiet, cepiet un pasniedziet saldumus mājīgiem mirkļiem.' },
        17: { name: 'Rokdarbi', description: 'Veidojiet paštaisītus rokdarbus, izmantojot savu iztēli.' },
      }

      return texts[id] || {}
    },
    getImageByKey(key) {
      const images = {
        photo: new URL('../assets/photo.png', import.meta.url).href,
        run: new URL('../assets/run.png', import.meta.url).href,
        dance: new URL('../assets/dance.png', import.meta.url).href,
        sing: new URL('../assets/sing.png', import.meta.url).href,
        sew: new URL('../assets/sew.png', import.meta.url).href,
        read: new URL('../assets/read.png', import.meta.url).href,
        fitness: new URL('../assets/fitness.png', import.meta.url).href,
        paint: new URL('../assets/paint.png', import.meta.url).href,
        write: new URL('../assets/write.png', import.meta.url).href,
        cycle: new URL('../assets/cycle.png', import.meta.url).href,
        swim: new URL('../assets/swim.png', import.meta.url).href,
        music: new URL('../assets/music.png', import.meta.url).href,
        fish: new URL('../assets/fish.png', import.meta.url).href,
        game: new URL('../assets/game.png', import.meta.url).href,
        cook: new URL('../assets/cook.png', import.meta.url).href,
        bake: new URL('../assets/bake.png', import.meta.url).href,
        diy: new URL('../assets/diy.png', import.meta.url).href,
      }

      return images[key] || images.photo
    },
    getDefaultHobbyImageKey(hobby) {
      const imageKeysById = {
        1: 'photo',
        2: 'run',
        3: 'dance',
        4: 'sing',
        5: 'sew',
        6: 'read',
        7: 'fitness',
        8: 'paint',
        9: 'write',
        10: 'cycle',
        11: 'swim',
        12: 'music',
        13: 'fish',
        14: 'game',
        15: 'cook',
        16: 'bake',
        17: 'diy',
      }

      if (imageKeysById[hobby.id]) {
        return imageKeysById[hobby.id]
      }

      const normalizedName = String(hobby.name)
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()

      const imageKeysByName = {
        fotografesana: 'photo',
        skriesana: 'run',
        dejosana: 'dance',
        dziedasana: 'sing',
        susana: 'sew',
        lasisana: 'read',
        fitness: 'fitness',
        gleznosana: 'paint',
        rakstisana: 'write',
        ritenbrauksana: 'cycle',
        peldesana: 'swim',
        muzika: 'music',
        makskeresana: 'fish',
        videospele: 'game',
        'videospelu spelesana': 'game',
        'ediena gatavosana': 'cook',
        cepsana: 'bake',
        rokdarbi: 'diy',
      }

      return imageKeysByName[normalizedName] || 'photo'
    },
    getHobbyImage(hobby) {
      if (hobby.image) {
        if (hobby.image.startsWith('/uploads/')) {
          return `${API_URL.replace('/api', '')}${hobby.image}`
        }

        return this.getImageByKey(hobby.image)
      }

      return this.getImageByKey(this.getDefaultHobbyImageKey(hobby))
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
          alert(data.message || 'Pārbaudi, vai ievadītie dati ir pareizi.')
          return
        }

        localStorage.setItem('hobispace_user', JSON.stringify(data.user))
        this.currentUser = data.user
        this.closeAuth()
        await this.loadFavorites()
        this.$router.push('/profile')
      } catch (error) {
        alert('Nevar pieslēgties Laravel serverim.')
      }
    },
    logout() {
      localStorage.removeItem('hobispace_user')
      this.currentUser = null
      this.hobbies = this.hobbies.map((hobby) => ({ ...hobby, isFavorite: false }))
    }
  }
}
</script>

<style scoped>
.hobbies-page {
  width: 100%;
}

.hobbies-hero {
  background-color: #F6DFCE;
  color: #000000;
  padding: 80px 20px;
  text-align: center;
  min-height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hobbies-hero-content {
  max-width: 1000px;
}

.hobbies-hero-content h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.hobbies-hero-content p {
  font-size: 1.5rem;
  opacity: 0.9;
  line-height: 1.55;
}

.collection-label {
  margin: 0 0 0.75rem;
  color: #705949;
  font-size: 1rem !important;
  font-weight: 700;
  text-transform: uppercase;
  opacity: 1 !important;
  line-height: 1.2 !important;
}

.hobbies-section {
  padding: 60px 20px;
  background-color: #ffffff;
}

.hobbies-actions {
  max-width: 1180px;
  margin: 0 auto 1.25rem;
  display: flex;
  justify-content: flex-end;
}

.add-hobby-btn {
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 8px;
  background-color: #BC4527;
  color: #FDF8F0;
  cursor: pointer;
  font-weight: 700;
}

.add-hobby-btn:hover {
  background-color: #9b3a20;
}

.hobbies-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  max-width: 1180px;
  margin: 0 auto;
}

.hobby-card {
  background-color: #FDF8F0;
  border-radius: 12px;
  padding: 1.15rem;
  text-align: center;
  transition: transform 0.3s, box-shadow 0.3s;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  position: relative;
}

.hobby-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.hobby-image {
  margin-bottom: 0.85rem;
  width: 100%;
  height: 120px;
  overflow: hidden;
  border-radius: 10px;
  background-color: #F6DFCE;
}

.hobby-photo {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: contain;
  padding: 0.35rem;
}

.hobby-card h3 {
  font-size: 1.1rem;
  color: #000000;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.hobby-meta {
  display: flex;
  justify-content: center;
  gap: 0.45rem;
  flex-wrap: wrap;
  margin-bottom: 0.65rem;
}

.hobby-meta span {
  padding: 0.25rem 0.45rem;
  border-radius: 6px;
  background-color: #ffffff;
  color: #705949;
  font-size: 0.74rem;
  font-weight: 700;
}

.hobby-description {
  font-size: 0.88rem;
  color: #555555;
  line-height: 1.6;
  margin-bottom: 1rem;
  min-height: 3rem;
}

.hobby-card-actions {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.45rem;
  flex-wrap: wrap;
}

.card-action-btn,
.log-item-actions button,
.secondary-btn {
  border: 1px solid #d8c8bc;
  border-radius: 7px;
  background-color: #ffffff;
  color: #1f1f1f;
  cursor: pointer;
  font: inherit;
  font-size: 0.82rem;
  font-weight: 700;
  padding: 0.45rem 0.65rem;
}

.card-action-btn:hover,
.log-item-actions button:hover,
.secondary-btn:hover {
  border-color: #BC4527;
  color: #BC4527;
}

.card-action-btn.danger,
.log-item-actions .danger {
  color: #9b1c1c;
}

.heart-btn {
  background: none;
  border: none;
  font-size: 1.8rem;
  cursor: pointer;
  color: #cccccc;
  transition: all 0.3s;
  padding: 0;
  line-height: 1;
}

.heart-btn:hover {
  transform: scale(1.2);
  color: #F6DFCE;
}

.heart-btn.active {
  color: #ff6b6b;
  transform: scale(1.2);
}

@media (max-width: 1024px) {
  .hobbies-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2rem;
  }

  .hero-description {
    font-size: 1rem;
  }

  .hobbies-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }

  .hobby-card {
    padding: 1rem;
  }

  .hobby-image {
    height: 110px;
  }

  .hobby-card h3 {
    font-size: 1.1rem;
  }

  .heart-btn {
    font-size: 1.5rem;
  }
}

@media (max-width: 520px) {
  .hobbies-hero {
    padding: 3.5rem 1rem;
    min-height: auto;
  }

  .hobbies-hero-content h1 {
    font-size: 1.85rem;
  }

  .hobbies-hero-content p {
    font-size: 1rem;
  }

  .hobbies-section {
    padding: 2rem 1rem;
  }

  .hobbies-actions {
    justify-content: stretch;
  }

  .add-hobby-btn {
    width: 100%;
  }

  .hobbies-grid {
    grid-template-columns: 1fr;
  }

  .hobby-card {
    border-radius: 8px;
  }

  .hobby-image {
    height: 140px;
  }
}

.log-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 1rem;
}

.log-modal {
  background-color: #FDF8F0;
  border-radius: 12px;
  padding: 2rem;
  max-width: 720px;
  width: 90%;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  position: relative;
}

.log-modal .close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #555;
}

.log-modal h2 {
  margin-bottom: 1.5rem;
  color: #000000;
  font-size: 1.5rem;
  text-align: center;
}

.form-title {
  margin: 1.25rem 0 1rem;
  color: #000000;
  font-size: 1.15rem;
  font-weight: 800;
}

.log-list-panel {
  display: grid;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  padding: 1rem;
  border: 1px solid #eadfd5;
  border-radius: 8px;
  background: #ffffff;
}

.log-list-heading {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.log-list-heading h3 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 800;
}

.log-list-heading span {
  display: inline-grid;
  place-items: center;
  min-width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #F6DFCE;
  color: #BC4527;
  font-weight: 900;
}

.log-empty {
  color: #62554e;
  font-weight: 700;
}

.log-item {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  gap: 1rem;
  align-items: start;
  padding-top: 0.75rem;
  border-top: 1px solid #eadfd5;
}

.log-item strong,
.log-item span,
.log-item small {
  display: block;
}

.log-item strong {
  color: #000000;
}

.log-item span,
.log-item small,
.log-item p {
  color: #62554e;
}

.log-item-actions {
  display: flex;
  gap: 0.45rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.log-modal .form-group {
  margin-bottom: 1.5rem;
}

.log-modal label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
}

.log-modal input,
.log-modal textarea,
.log-modal select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  box-sizing: border-box;
}

.log-modal textarea {
  resize: vertical;
  min-height: 80px;
}

.log-modal .save-btn {
  background-color: #BC4527;
  color: #FDF8F0;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  width: 100%;
  transition: background-color 0.3s;
}

.log-modal .save-btn:hover {
  background-color: #9b3a20;
}

.edit-cancel-btn {
  width: 100%;
  margin-bottom: 0.75rem;
}

.add-hobby-modal {
  max-width: 620px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.add-preview {
  width: 100%;
  height: 130px;
  margin-bottom: 1.5rem;
  border-radius: 10px;
  background-color: #F6DFCE;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.add-preview img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 0.5rem;
}

@media (max-width: 520px) {
  .log-overlay {
    align-items: flex-start;
    overflow-y: auto;
  }

  .log-modal {
    width: 100%;
    max-height: none;
    padding: 1.4rem;
    margin: 1rem 0;
  }

  .log-modal h2 {
    font-size: 1.25rem;
    padding-right: 2rem;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 0;
  }
}
</style>
