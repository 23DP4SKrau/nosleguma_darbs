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

    <section class="hobbies-hero">
      <div class="hobbies-hero-content">
        <p class="collection-label">kolekcija</p>
        <h1>Hobiji, kurus vērts saglabāt</h1>
        <p>Uzklikšķiniet uz jebkura hobija, lai atvērtu tā pierakstus, nospiediet sirsniņu, lai saglabātu savus mīļākos hobijus un pievieno jaunu hobiju kuru vēl neesi šeit ieraudzījis.</p>
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
          <p class="hobby-description">{{ hobby.description }}</p>
          <button
            class="heart-btn"
            :class="{ active: hobby.isFavorite }"
            @click.stop="toggleFavorite(hobby.id)"
          >
            ♥
          </button>
        </div>
      </div>
    </section>

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
            <ul v-if="!isLogin && form.password" class="password-rules">
              <li :class="{ valid: passwordChecks.length }">Vismaz 8 simboli</li>
              <li :class="{ valid: passwordChecks.uppercase }">Viens lielais burts</li>
              <li :class="{ valid: passwordChecks.lowercase }">Viens mazais burts</li>
              <li :class="{ valid: passwordChecks.number }">Viens cipars</li>
              <li :class="{ valid: passwordChecks.symbol }">Viens speciālais simbols</li>
            </ul>
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

    <div v-if="showLogModal" class="log-overlay" @click.self="showLogModal = false">
      <div class="log-modal">
        <button class="close-btn" @click="showLogModal = false">×</button>
        <h2>Log {{ selectedHobby.name }}</h2>
        <form @submit.prevent="saveLog">
          <div class="form-group">
            <label>Datums</label>
            <input type="date" v-model="logForm.date" required />
          </div>

          <div class="form-group">
            <label>Virsraksts</label>
            <input type="text" v-model="logForm.title" required maxlength="255" />
          </div>

          <div v-if="getSpecificLabel(selectedHobby.id)" class="form-group">
            <label>{{ getSpecificLabel(selectedHobby.id) }}</label>
            <input type="text" v-model="logForm.specific" />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Ilgums minūtēs</label>
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
          <button type="submit" class="save-btn">Saglabāt ierakstu</button>
        </form>
      </div>
    </div>

    <div v-if="showAddModal" class="log-overlay" @click.self="closeAddModal">
      <div class="log-modal add-hobby-modal">
        <button class="close-btn" type="button" @click="closeAddModal">×</button>
        <h2>Pievienot hobiju</h2>

        <form @submit.prevent="saveHobby">
          <div class="form-group">
            <label>Nosaukums</label>
            <input v-model="addForm.name" type="text" required maxlength="255" />
          </div>

          <div class="form-group">
            <label>Tips</label>
            <select v-model="addForm.category_id" required>
              <option value="" disabled>Izvēlies tipu</option>
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

          <div class="form-group">
            <label>Bilde no datora</label>
            <input type="file" accept="image/*" required @change="handleImageUpload" />
          </div>

          <div v-if="imagePreview" class="add-preview">
            <img :src="imagePreview" alt="Izvēlētā hobija bilde" />
          </div>

          <div class="form-group">
            <label>Apraksts</label>
            <textarea v-model="addForm.description" rows="4" required minlength="10"></textarea>
          </div>

          <button type="submit" class="save-btn">Pievienot kartīti</button>
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
const API_URL = 'http://127.0.0.1:8000/api'

export default {
  name: 'HobbiesPage',
  data() {
    return {
      searchQuery: '',
      showAuth: false,
      isLogin: true,
      showPassword: false,
      showLogModal: false,
      showAddModal: false,
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
        imageFile: null,
        description: '',
      },
      imagePreview: '',
      form: {
        name: '',
        email: '',
        password: ''
      },
      hobbies: [
        { id: 1, name: 'Fotogrāfēšana', isFavorite: false, description: 'Fiksējiet mirkļus un veidojiet vizuālus stāstus ar katru kadru.' },
        { id: 2, name: 'Skriešana', isFavorite: false, description: 'Veidojiet izturību un attīriet prātu ar katru kilometru.' },
        { id: 3, name: 'Dejošana', isFavorite: false, description: 'Kustieties pēc mūzikas un palaid savu enerģiju caur ritmu.' },
        { id: 4, name: 'Dziedāšana', isFavorite: false, description: 'Trenējiet melodijas, palieliniet savu pārliecību un atrodiet savu balsi.' },
        { id: 5, name: 'Šūšana', isFavorite: false, description: 'Dizainējiet un labojiet auduma projektus ar radošumu un prasmi.' },
        { id: 6, name: 'Lasīšana', isFavorite: false, description: 'Atklājiet stāstus, idejas un iedvesmu caur katru lapu.' },
        { id: 7, name: 'Fitness', isFavorite: false, description: 'Trenējiet savu ķermeni ar spēku, elastību un pozitīvām rutīnām.' },
        { id: 8, name: 'Gleznošana', isFavorite: false, description: 'Atvediet krāsu un iztēli dzīvē uz audekla vai papīra.' },
        { id: 9, name: 'Rakstīšana', isFavorite: false, description: 'Veidojiet esejas, stāstus un ekspresīvu rakstīšanu ar nolūku.' },
        { id: 10, name: 'Riteņbraukšana', isFavorite: false, description: 'Izpētiet jaunus maršrutus un baudiet brīvību braukt ārā.' },
        { id: 11, name: 'Peldēšana', isFavorite: false, description: 'Atpūtieties ūdenī, vienlaikus uzlabojot spēku un izturību.' },
        { id: 12, name: 'Mūzika', isFavorite: false, description: 'Mācīties instrumentus, sitienus un ritmus, kas kustina jūsu garu.' },
        { id: 13, name: 'Makšķerēšana', isFavorite: false, description: 'Baudiet mierīgu ezeru laiku un svaiga loma uztraukumu.' },
        { id: 14, name: 'Videospēļu spēlēšana', isFavorite: false, description: 'Spēlējiet stratēģijas, piedzīvojumu un sociālās spēles ar draugiem.' },
        { id: 15, name: 'Ēdiena gatavošana', isFavorite: false, description: 'Veidojiet garšīgus ēdienus, eksperimentējiet ar receptēm un barojiet savu prieku.' },
        { id: 16, name: 'Cepšana', isFavorite: false, description: 'Samaisiet, cepiet un pasniedziet saldumus mājīgiem mirkļiem.' },
        { id: 17, name: 'Rokdarbi', isFavorite: false, description: 'Veidojiet paštaisītus rokdarbus, izmantojot savu iztēli.' }
      ]
    }
  },
  mounted() {
    this.currentUser = JSON.parse(localStorage.getItem('hobispace_user') || 'null')

    if (this.currentUser) {
      this.loadFavorites()
    }

    this.loadCategories()
    this.loadAddedHobbies()
  },
  computed: {
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
        uppercase: /[A-ZĀČĒĢĪĶĻŅŠŪŽ]/.test(password),
        lowercase: /[a-zāčēģīķļņšūž]/.test(password),
        number: /\d/.test(password),
        symbol: /[^A-Za-zĀČĒĢĪĶĻŅŠŪŽāčēģīķļņšūž0-9]/.test(password),
      }
    },
    isPasswordStrong() {
      return Object.values(this.passwordChecks).every(Boolean)
    }
  },
  methods: {
    async loadCategories() {
      try {
        const response = await fetch(`${API_URL}/categories`, {
          headers: { Accept: 'application/json' },
        })

        if (response.ok) {
          this.categories = await response.json()
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
        const existingIds = this.hobbies.map((hobby) => hobby.id)
        const addedHobbies = databaseHobbies
          .filter((hobby) => !existingIds.includes(hobby.id))
          .map((hobby) => ({
            id: hobby.id,
            name: hobby.name,
            description: hobby.description,
            image: hobby.image || 'photo',
            isFavorite: false,
          }))

        this.hobbies = [...this.hobbies, ...addedHobbies]
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
        if (hobby.isFavorite) {
          return
        }

        try {
          const response = await fetch(`${API_URL}/favorites`, {
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

      this.showAddModal = true
    },
    closeAddModal() {
      this.showAddModal = false
      this.addForm = {
        name: '',
        category_id: '',
        estimated_cost: 'bezmaksas',
        imageFile: null,
        description: '',
      }
      this.imagePreview = ''
    },
    handleImageUpload(event) {
      const file = event.target.files?.[0]
      this.addForm.imageFile = file || null
      this.imagePreview = file ? URL.createObjectURL(file) : ''
    },
    async saveHobby() {
      try {
        const formData = new FormData()
        formData.append('name', this.addForm.name)
        formData.append('category_id', this.addForm.category_id)
        formData.append('estimated_cost', this.addForm.estimated_cost)
        formData.append('description', this.addForm.description)
        formData.append('user_id', this.currentUser.id)

        if (this.addForm.imageFile) {
          formData.append('image_file', this.addForm.imageFile)
        }

        const response = await fetch(`${API_URL}/hobbies`, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
          },
          body: formData,
        })
        const data = await response.json()

        if (!response.ok) {
          alert(data.message || 'Neizdevās pievienot hobiju.')
          return
        }

        this.hobbies.push({
          id: data.hobby.id,
          name: data.hobby.name,
          description: data.hobby.description,
          image: data.hobby.image,
          isFavorite: false,
        })
        this.closeAddModal()
      } catch (error) {
        alert('Nevar pieslēgties Laravel serverim.')
      }
    },
    openLogModal(hobby) {
      if (!this.currentUser) {
        this.openLogin()
        return
      }

      this.selectedHobby = hobby
      this.showLogModal = true
      this.logForm = {
        date: new Date().toISOString().split('T')[0],
        title: `${hobby.name} ieraksts`,
        message: '',
        specific: '',
        duration_minutes: '',
        mood_rating: '',
      }
    },
    async saveLog() {
      try {
        const response = await fetch(`${API_URL}/hobby-logs`, {
          method: 'POST',
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

        this.logs.push(data.log)
        alert('Ieraksts saglabāts datubāzē!')
        this.showLogModal = false
      } catch (error) {
        alert('Nevar pieslēgties Laravel serverim.')
      }
    },
    getSpecificLabel(id) {
      const labels = {
        1: 'Bildes atrašanās vieta',
        3: 'Distance (km)',
        4: 'Dejas veids',
        5: 'Dziesmas nosaukums',
        6: 'Projekta nosaukums',
        7: 'Grāmatas nosaukums',
        8: 'Vingrinājuma veids',
        9: 'Mākslas darba nosaukums',
        10: 'Raksta nosaukums',
        11: 'Distance (km)',
        12: 'Distance (m)',
        13: 'Instruments',
        14: 'Zivs lielums un zivs nosaukums',
        15: 'Spēles nosaukums',
        16: 'Recepte',
        17: 'Recepte',
        18: 'Projekts'
      }
      return labels[id] || ''
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
    getHobbyImage(hobby) {
      if (hobby.image) {
        if (hobby.image.startsWith('/uploads/')) {
          return `${API_URL.replace('/api', '')}${hobby.image}`
        }

        return this.getImageByKey(hobby.image)
      }

      const images = {
        'Fotogrāfēšana': new URL('../assets/photo.png', import.meta.url).href,
        'Skriešana': new URL('../assets/run.png', import.meta.url).href,
        'Dejošana': new URL('../assets/dance.png', import.meta.url).href,
        'Dziedāšana': new URL('../assets/sing.png', import.meta.url).href,
        'Šūšana': new URL('../assets/sew.png', import.meta.url).href,
        'Lasīšana': new URL('../assets/read.png', import.meta.url).href,
        Fitness: new URL('../assets/fitness.png', import.meta.url).href,
        'Gleznošana': new URL('../assets/paint.png', import.meta.url).href,
        'Rakstīšana': new URL('../assets/write.png', import.meta.url).href,
        'Riteņbraukšana': new URL('../assets/cycle.png', import.meta.url).href,
        'Peldēšana': new URL('../assets/swim.png', import.meta.url).href,
        'Mūzika': new URL('../assets/music.png', import.meta.url).href,
        'Makšķerēšana': new URL('../assets/fish.png', import.meta.url).href,
        'Videospēļu spēlēšana': new URL('../assets/game.png', import.meta.url).href,
        'Ēdiena gatavošana': new URL('../assets/cook.png', import.meta.url).href,
        'Cepšana': new URL('../assets/bake.png', import.meta.url).href,
        'Rokdarbi': new URL('../assets/diy.png', import.meta.url).href,
      }

      return images[hobby.name] || new URL('../assets/logo.jpg', import.meta.url).href
    },
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
    async handleSubmit() {
      const path = this.isLogin ? '/login' : '/register'

      if (!this.isLogin && !this.isPasswordStrong) {
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
  font-size: 1rem;
  font-weight: 500;
  color: #666666;
  margin-bottom: 0.5rem;
  letter-spacing: 1px;
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

.hobby-description {
  font-size: 0.88rem;
  color: #555555;
  line-height: 1.6;
  margin-bottom: 1rem;
  min-height: 3rem;
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
  max-width: 500px;
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
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  width: 100%;
  transition: background-color 0.3s;
}

.log-modal .save-btn:hover {
  background-color: #45a049;
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
