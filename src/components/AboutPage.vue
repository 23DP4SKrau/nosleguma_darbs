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
          <button class="login-btn" @click="openLogin">Pieslēgties</button>
          <button class="signup-btn" @click="openSignup">Reģistrēties</button>
        </div>
      </div>
    </header>

    <!-- About Hero Section -->
    <section class="about-hero">
      <div class="about-hero-content">
        <p class="collection-label">par mums</p>
        <h1>Par HobiSpace</h1>
        <p class="hero-subtitle">Mēs esam radoši cilvēki, kurus vieno intereses par vairākiem hobijiem. Šī mājaslapa apkopo informāciju, idejas un pieredzi gan iesācējiem, gan pieredzējušiem entuziastiem.</p>
      </div>
    </section>

    <!-- Our Mission -->
    <section class="mission-section">
      <div class="container">
        <h2>Mūsu misija</h2>
        <p>
          HobiSpace tic, ka hobiji ir gan laika pavadīšana interesantā veidā, gan ceļs uz pašatklāšanu, 
          radošumu un savienojumu ar citiem. Mūsu misija ir ļaut HobiSpace lietotājiem atcerēties savus piedzīvojumus. Šeit cilvēki var izpētīt dažādus hobijus, 
          dalīties savās aizrautībās un iedvesmot citus sekot savām interesēm.
        </p>
      </div>
    </section>

    <!-- What We Offer -->
    <section class="offers-section">
      <div class="container">
        <h2>Ko mēs piedāvājam</h2>
        <div class="offers-grid">
          <div class="offer-card">
            <div class="offer-icon"></div>
            <h3>Izpēti hobijus</h3>
            <p>Pārlūkojiet plašu hobiju klāstu un atklājiet jaunas intereses, kas atbilst jums.</p>
          </div>
          <div class="offer-card">
            <div class="offer-icon"></div>
            <h3>Dalies ar savu hobiju</h3>
            <p>Demonstrējiet savus hobiju projektus, sasniegumus un radošos darbus ar mums.</p>
          </div>
          <div class="offer-card">
            <div class="offer-icon"></div>
            <h3>Atzīmē savus panākumus</h3>
            <p>Pieraksti ko tu esi darījies un ieguvis kamēr nodarbojies ar savu hobiju.</p>
          </div>
        </div>
      </div>
    </section>
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
        <p>© {{ new Date().getFullYear() }} HobiSpace. All rights reserved.</p>
      </div>
    </footer>
</template>

<script>
export default {
  name: 'AboutPage',
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

.about-hero-content h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.about-hero-content p {
  font-size: 1.5rem;
  opacity: 0.9;
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
  background-color: #FDF8F0;
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
  }

  .reasons {
    grid-template-columns: 1fr;
  }
}
</style>