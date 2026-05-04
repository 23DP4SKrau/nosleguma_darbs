<template>
  <div class="contact-page">
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

    <!-- Contact Hero Section -->
    <section class="contact-hero">
      <div class="contact-container">
        <!-- Left Side -->
        <div class="contact-left">
          <p class="collection-label">sazināšanās</p>
          <h1>Runājam par hobijiem</h1>
          <div class="contact-options">
            <div class="option">
              <span class="option-icon"></span>
              <p>Ziņo mums par neērtībām</p>
            </div>
            <div class="option">
              <span class="option-icon"></span>
              <p>Dalies ar savu darbu</p>
            </div>
          </div>
        </div>

        <!-- Right Side - Contact Form -->
        <div class="contact-right">
          <form @submit.prevent="handleSubmit" class="contact-form">
            <div class="form-group">
              <input 
                type="text" 
                v-model="form.name" 
                placeholder="Jūsu vārds" 
                required 
                class="form-input"
              />
            </div>

            <div class="form-group">
              <input 
                type="email" 
                v-model="form.email" 
                placeholder="Jūsu e-pasts" 
                required 
                class="form-input"
              />
            </div>

            <div class="form-group">
              <textarea 
                v-model="form.message" 
                placeholder="Jūsu ziņa" 
                required 
                rows="5"
                class="form-input form-textarea"
              ></textarea>
            </div>

            <button type="submit" class="send-btn">Nosūtīt ziņu</button>
          </form>
        </div>
      </div>
    </section>

    <!-- Auth Modal -->
    <div v-if="showAuth" class="auth-overlay" @click.self="closeAuth">
      <div class="auth-modal">
        <button class="close-btn" @click="closeAuth">×</button>

        <h2>{{ isLogin ? 'Pieslēgties HobiSpace' : 'Izveidot kontu' }}</h2>

        <form @submit.prevent="handleAuthSubmit">
          <div v-if="!isLogin" class="form-group">
            <label>Pilns vārds</label>
            <input type="text" v-model="authForm.name" required />
          </div>

          <div class="form-group">
            <label>E-pasts</label>
            <input type="email" v-model="authForm.email" required />
          </div>

          <div class="form-group password-group">
            <label>Parole</label>

            <div class="password-wrapper">
              <input 
                :type="showPassword ? 'text' : 'password'" 
                v-model="authForm.password" 
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
        <p>© {{ new Date().getFullYear() }} HobiSpace. Visas tiesības aizsargātas.</p>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'ContactPage',
  data() {
    return {
      searchQuery: '',
      form: {
        name: '',
        email: '',
        message: ''
      },
      authForm: {
        name: '',
        email: '',
        password: ''
      },
      showAuth: false,
      isLogin: true,
      showPassword: false
    }
  },
  methods: {
    handleSubmit() {
      alert(`Thank you for your message, ${this.form.name}! We'll get back to you soon.`)
      this.form = { name: '', email: '', message: '' }
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
    handleAuthSubmit() {
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
.contact-page {
  width: 100%;
}

/* Contact Hero Section */
.contact-hero {
  background-color: #F6DFCE;
  color: #000000;
  padding: 80px 20px;
  min-height: 500px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.contact-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  max-width: 1200px;
  width: 100%;
  align-items: center;
}

/* Left Side */
.contact-left h1 {
  font-size: 3rem;
  margin-bottom: 2rem;
  font-weight: 700;
  line-height: 1.2;
}

.contact-options {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.option {
  background-color: #ffffff;
  padding: 1.5rem;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.option:hover {
  transform: translateX(10px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.option-icon {
  font-size: 2rem;
}

.option p {
  font-size: 1.1rem;
  font-weight: 500;
  margin: 0;
}

/* Right Side - Form */
.contact-right {
  background-color: #ffffff;
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-input {
  padding: 1rem;
  border: 2px solid #f0f0f0;
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
  transition: border-color 0.3s;
  background-color: #f9f9f9;
}

.form-input:focus {
  outline: none;
  border-color: #F6DFCE;
  background-color: #ffffff;
}

.form-textarea {
  resize: vertical;
  font-family: inherit;
}

.send-btn {
  padding: 1rem 2rem;
  background-color: #BC4527;
  color: #FDF8F0;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 0.5rem;
}

.send-btn:hover {
  background-color: #9b3a20;
  color: #FDF8F0;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.send-btn:active {
  transform: translateY(0);
}

/* Responsive */
@media (max-width: 768px) {
  .contact-container {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .contact-left h1 {
    font-size: 2rem;
  }

  .contact-options {
    gap: 1rem;
  }

  .option {
    padding: 1rem;
  }

  .option-icon {
    font-size: 1.5rem;
  }

  .option p {
    font-size: 1rem;
  }

  .contact-right {
    padding: 1.5rem;
  }

  .contact-form {
    gap: 1rem;
  }

  .form-input {
    padding: 0.75rem;
  }

  .send-btn {
    padding: 0.75rem 1.5rem;
  }
}
</style>