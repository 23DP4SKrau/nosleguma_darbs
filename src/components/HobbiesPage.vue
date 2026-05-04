<template>
  <div class="hobbies-page">
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

    <!-- Hobbies Hero Section -->
    <section class="hobbies-hero">
      <div class="hobbies-hero-content">
        <p class="collection-label">kolekcija</p>
        <h1>Hobiji, kurus vērts saglabāt</h1>
        <p>Uzklikšķiniet uz jebkura hobija, lai atvērtu tā pierakstus, nospiediet sirsniņu, lai saglabātu savus mīļākos hobijus un pievieno jaunu hobiju kuru vēl neesi šeit ieraudzījis.</p>
      </div>
    </section>

    <!-- Hobbies Grid -->
    <section class="hobbies-section">
      <div class="hobbies-grid">
        <div v-for="hobby in filteredHobbies" :key="hobby.id" class="hobby-card" @click="openLogModal(hobby)">
          <div class="hobby-image">
            <span class="hobby-emoji">{{ hobby.emoji }}</span>
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

    <!-- Log Modal -->
    <div v-if="showLogModal" class="log-overlay" @click.self="showLogModal = false">
      <div class="log-modal">
        <button class="close-btn" @click="showLogModal = false">×</button>
        <h2>Log {{ selectedHobby.name }}</h2>
        <form @submit.prevent="saveLog">
          <div class="form-group">
            <label>Datums</label>
            <input type="date" v-model="logForm.date" required />
          </div>
          <div v-if="getSpecificLabel(selectedHobby.id)" class="form-group">
            <label>{{ getSpecificLabel(selectedHobby.id) }}</label>
            <input type="text" v-model="logForm.specific" />
          </div>
          <div class="form-group">
            <label>Ziņa</label>
            <textarea v-model="logForm.message" rows="3" placeholder="Pievienojiet savu ziņu..."></textarea>
          </div>
          <button type="submit" class="save-btn">Saglabāt ierakstu</button>
        </form>
      </div>
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
export default {
  name: 'HobbiesPage',
  data() {
    return {
      searchQuery: '',
      showAuth: false,
      isLogin: true,
      showPassword: false,
      showLogModal: false,
      selectedHobby: null,
      logForm: {
        date: '',
        message: '',
        specific: ''
      },
      logs: [],
      form: {
        name: '',
        email: '',
        password: ''
      },
      hobbies: [
        { id: 1, name: 'Fotogrāfēšana', isFavorite: false, description: 'Fiksējiet mirkļus un veidojiet vizuālus stāstus ar katru kadru.' },
        { id: 3, name: 'Skriešana', isFavorite: false, description: 'Veidojiet izturību un attīriet prātu ar katru kilometru.' },
        { id: 4, name: 'Dejošana',isFavorite: false, description: 'Kustieties pēc mūzikas un palaid savu enerģiju caur ritmu.' },
        { id: 5, name: 'Dziedāšana', isFavorite: false, description: 'Trenējiet melodijas, palieliniet savu pārliecību un atrodiet savu balsi.' },
        { id: 6, name: 'Šūšana', isFavorite: false, description: 'Dizainējiet un labojiet auduma projektus ar radošumu un prasmi.' },
        { id: 7, name: 'Lasīšana', isFavorite: false, description: 'Atklājiet stāstus, idejas un iedvesmu caur katru lapu.' },
        { id: 8, name: 'Fitness', isFavorite: false, description: 'Trenējiet savu ķermeni ar spēku, elastību un pozitīvām rutīnām.' },
        { id: 9, name: 'Gleznošana', isFavorite: false, description: 'Atvediet krāsu un iztēli dzīvē uz audekla vai papīra.' },
        { id: 10, name: 'Rakstīšana', isFavorite: false, description: 'Veidojiet esejas, stāstus un ekspresīvu rakstīšanu ar nolūku.' },
        { id: 11, name: 'Riteņbraukšana', isFavorite: false, description: 'Izpētiet jaunus maršrutus un baudiet brīvību braukt ārā.' },
        { id: 12, name: 'Peldēšana', isFavorite: false, description: 'Atpūtieties ūdenī, vienlaikus uzlabojot spēku un izturību.' },
        { id: 13, name: 'Mūzika', isFavorite: false, description: 'Mācīties instrumentus, sitienus un ritmus, kas kustina jūsu garu.' },
        { id: 14, name: 'Makšķerēšana', isFavorite: false, description: 'Baudiet mierīgu ezeru laiku un svaiga loma uztraukumu.' },
        { id: 15, name: 'Videospēļu spēlēšana', isFavorite: false, description: 'Spēlējiet stratēģijas, piedzīvojumu un sociālās spēles ar draugiem.' },
        { id: 16, name: 'Ēdiena gatavošana', isFavorite: false, description: 'Veidojiet garšīgus ēdienus, eksperimentējiet ar receptēm un barojiet savu prieku.' },
        { id: 17, name: 'Cepšana', isFavorite: false, description: 'Samaisiet, cepiet un pasniedziet saldumus mājīgiem mirkļiem.' },
        { id: 18, name: 'Rokdarbi', isFavorite: false, description: 'Veidojiet paštaisītus rokdarbus, izmantojot savu iztēli.' }
      ]
    }
  },
  computed: {
    filteredHobbies() {
      if (!this.searchQuery) {
        return this.hobbies
      }
      return this.hobbies.filter(hobby =>
        hobby.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      )
    }
  },
  methods: {
    toggleFavorite(hobbyId) {
      const hobby = this.hobbies.find(h => h.id === hobbyId)
      if (hobby) {
        hobby.isFavorite = !hobby.isFavorite
      }
    },
    openLogModal(hobby) {
      this.selectedHobby = hobby;
      this.showLogModal = true;
      this.logForm = { date: new Date().toISOString().split('T')[0], message: '', specific: '' };
    },
    saveLog() {
      const log = {
        hobby: this.selectedHobby.name,
        date: this.logForm.date,
        message: this.logForm.message,
        specific: this.logForm.specific
      };
      this.logs.push(log);
      alert('Ieraksts saglabāts!');
      this.showLogModal = false;
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
      };
      return labels[id] || '';
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
    handleSubmit() {
      if (this.isLogin) {
        alert("Logged in successfully!")
      } else {
        alert("Account created successfully!")
      }
      this.closeAuth()
    }
  }
};
</script>

<style scoped>
.hobbies-page {
  width: 100%;
}

/* Hobbies Hero Section */
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
}

.collection-label {
  font-size: 1rem;
  font-weight: 500;
  color: #666666;
  margin-bottom: 0.5rem;
  letter-spacing: 1px;
}

/* Hobbies Section */
.hobbies-section {
  padding: 60px 20px;
  background-color: #ffffff;
}

.hobbies-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.hobby-card {
  background-color: #FDF8F0;
  border-radius: 12px;
  padding: 2rem;
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
  font-size: 3.5rem;
  margin-bottom: 1rem;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hobby-emoji {
  display: inline-block;
}

.hobby-card h3 {
  font-size: 1.3rem;
  color: #000000;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.hobby-description {
  font-size: 0.95rem;
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

/* Responsive */
@media (max-width: 1024px) {
  .hobbies-grid {
    grid-template-columns: repeat(2, 1fr);
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
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .hobby-card {
    padding: 1.5rem;
  }

  .hobby-image {
    font-size: 2.5rem;
    height: 60px;
  }

  .hobby-card h3 {
    font-size: 1.1rem;
  }

  .heart-btn {
    font-size: 1.5rem;
  }
}

/* Log Modal Styles */
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
.log-modal textarea {
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
</style>