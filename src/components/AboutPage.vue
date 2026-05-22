<template>
  <div class="about-page">
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

    <!-- About Hero Section -->
    <section class="about-hero">
      <div class="about-hero-layout">
        <img src="@/assets/zenn.png" alt="Radošs hobiju attēls" class="about-side-image about-side-image-left" />

        <div class="about-hero-content">
          <p class="collection-label">par mums</p>
          <h1>Par HobiSpace</h1>
          <p class="hero-subtitle">Mēs, radoši cilvēki ar vēlmi brīvo laiku pavadīt nevis telefonos, bet jēgpilni un interesanti, izdomājām izveidot mājaslapu, kas apkopo informāciju par lietotāju hobijiem, idejām, pieredzi un rekordiem. Šī mājaslapa ir paredzēta jebkura vecuma cilvēkiem, galvenais, lai esi mērķtiecīgs un tev patīk pavadīt laiku dažādos un aizraujošos veidos.</p>
        </div>

        <img src="@/assets/zen.png" alt="Mierīgs hobiju attēls" class="about-side-image about-side-image-right" />
      </div>
    </section>

    <!-- Our Mission -->
    <section class="mission-section">
      <div class="container">
        <h2>Mūsu misija</h2>
        <p>
          Mēs ticam, ka hobiji ir ceļš uz pašatklāšanu, kā arī laika pavadīšanu interesantā veidā. Atrodot savus īstos hobijus, tev ir iespēja ar citiem savienoties un izpaust savu radošumu. Tieši mūsu misija ir ļaut HobiSpace lietotājiem atcerēties savus piedzīvojumus.
          Mēs vēlamies, lai HobiSpace kļūtu par vietu, kur cilvēki var dalīties ar saviem hobijiem, sasniegumiem un iedvesmot citus. Mēs esam šeit, lai atbalstītu un iedrošinātu ikvienu atrast savu aizraušanos un izbaudīt to pilnībā.
        </p>
      </div>
    </section>

    <!-- What We Offer -->
    <section class="offers-section">
      <div class="container">
        <h2>Mūsu piedāvājumi</h2>
        <div class="offers-grid">
          <div class="offer-card">
            <div class="offer-icon"></div>
            <h3>Izpēti hobijus</h3>
            <p>Pārlūkojiet plašu hobiju klāstu un atklājiet jaunas intereses, kas atbilst jums.</p>
          </div>
          <div class="offer-card">
            <div class="offer-icon"></div>
            <h3>Pievieno hobijus</h3>
            <p>Pievienojiet jaunus hobijus, kuri vēl nav minēti, lai paplašinātu mūsu hobiju klāstu.</p>
          </div>
          <div class="offer-card">
            <div class="offer-icon"></div>
            <h3>Atzīmē savus panākumus</h3>
            <p>Pieraksti, ko tu esi darījis un ieguvis, kamēr nodarbojies ar savu hobiju.</p>
          </div>
        </div>
      </div>
    </section>
  </div>

    <!-- Auth Modal -->
    <div v-if="showAuth" class="auth-overlay" @click.self="closeAuth">
      <div class="auth-modal">
        <button class="close-btn" @click="closeAuth">x</button>

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
        <p>&copy; {{ new Date().getFullYear() }} HobiSpace. Visas tiesības aizsargātas.</p>
      </div>
    </footer>
</template>

<script>
import { API_URL } from '@/config/api'

export default {
  name: 'AboutPage',
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
};
</script>

<style scoped>
.about-page {
  width: 100%;
}

/* About Hero */
.about-hero {
  background-color: #F6DFCE;
  color: #000000;
  padding: 80px 20px;
  text-align: center;
  min-height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.about-hero-layout {
  width: min(1180px, 100%);
  display: grid;
  grid-template-columns: minmax(160px, 0.7fr) minmax(320px, 1.25fr) minmax(160px, 0.7fr);
  align-items: center;
  gap: 2rem;
}

.about-side-image {
  width: 100%;
  max-width: 260px;
  max-height: 260px;
  object-fit: contain;
  border-radius: 0;
  box-shadow: none;
  mix-blend-mode: multiply;
  animation: heroFloat 5s ease-in-out infinite;
}

.about-side-image-left {
  justify-self: end;
}

.about-side-image-right {
  justify-self: start;
  max-width: 255px;
  max-height: 255px;
  animation-delay: 1.2s;
}

.about-hero-content h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.about-hero-content .collection-label {
  margin: 0 0 0.75rem;
  color: #705949;
  font-size: 1rem !important;
  font-weight: 700;
  text-transform: uppercase;
  opacity: 1 !important;
  line-height: 1.2 !important;
}

.about-hero-content p {
  font-size: 1.5rem;
  opacity: 0.9;
  line-height: 1.55;
}

.hero-subtitle {
  font-size: 1rem !important;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
  color: #555555;
  opacity: 0.95;
}

/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Mission Section */
.mission-section {
  padding: 60px 20px;
  background-color: #ffffff;
}

.mission-section h2 {
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
  color: #000000;
  text-align: center;
}

.mission-section p {
  font-size: 1.1rem;
  color: #333333;
  line-height: 1.8;
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
}

/* Offers Section */
.offers-section {
  padding: 60px 20px;
  background-color: #F6DFCE;
}

.offers-section h2 {
  font-size: 2.5rem;
  margin-bottom: 3rem;
  color: #000000;
  text-align: center;
}

.offers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.offer-card {
  background-color: #ffffff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
  transition: transform 0.3s, box-shadow 0.3s;
}

.offer-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

.offer-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.offer-card h3 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: #000000;
}

.offer-card p {
  color: #666666;
  line-height: 1.6;
}

/* Why Us Section */
.why-us-section {
  padding: 60px 20px;
  background-color: #ffffff;
}

.why-us-section h2 {
  font-size: 2.5rem;
  margin-bottom: 3rem;
  color: #000000;
  text-align: center;
}

.reasons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.reason {
  padding: 2rem;
  background-color: #F6DFCE;
  border-radius: 10px;
  border-left: 4px solid #000000;
}

.reason h3 {
  font-size: 1.3rem;
  margin-bottom: 1rem;
  color: #000000;
}

.reason p {
  color: #333333;
  line-height: 1.6;
}

/* Responsive */
@media (max-width: 768px) {
  .about-hero {
    padding: 3.5rem 1rem;
    min-height: auto;
  }

  .about-hero-layout {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .about-side-image {
    max-width: 190px;
    max-height: 190px;
  }

  .about-side-image-left,
  .about-side-image-right {
    justify-self: center;
  }

  .about-hero-content h1 {
    font-size: 2rem;
  }

  .about-hero-content p {
    font-size: 1.2rem;
  }

  .mission-section h2,
  .offers-section h2,
  .why-us-section h2 {
    font-size: 2rem;
  }

  .offers-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .reasons {
    grid-template-columns: 1fr;
  }

  .mission-section,
  .offers-section,
  .why-us-section {
    padding: 2.5rem 1rem;
  }

  .offer-card,
  .reason {
    padding: 1.25rem;
    border-radius: 8px;
  }
}

@media (max-width: 420px) {
  .about-hero-content h1 {
    font-size: 1.75rem;
  }

  .about-hero-content p,
  .mission-section p {
    font-size: 1rem;
  }

  .mission-section h2,
  .offers-section h2,
  .why-us-section h2 {
    font-size: 1.65rem;
  }
}
</style>
