<template>
    <div class="homepage">
      <!-- Navigation Bar -->
      <header class="navbar">
  <div class="navbar-left">
    <div class="logo-container">
      <img src="@/assets/logo.jpg" alt="HobiSpace Logo" class="logo" />
      <span class="project-name">HobiSpace</span>
    </div>

    <nav>
      <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#hobbies">Hobbies</a></li>
        <li><a href="#contact">Contacts</a></li>
      </ul>
    </nav>
  </div>

  <div class="navbar-right">
    <input type="text" placeholder="Search hobbies..." class="search-bar" />

    <div class="auth-buttons">
      <button class="login-btn" @click="openLogin">Log in</button>
<button class="signup-btn" @click="openSignup">Sign Up</button>
    </div>
  </div>
</header>
  
      <!-- Hero Section -->
      <main class="hero">
        <div class="hero-content">
          <h1>Welcome to HobiSpace</h1>
          <p>Your ultimate destination for exploring hobbies and finding inspiration.</p>
          <a href="#hobbies" class="cta-button">Explore Hobbies</a>
        </div>
      </main>
    </div>

      <!-- Footer -->
    <footer class="footer">
  <div class="footer-container">

    <div class="footer-section">
      <h3>HobiSpace</h3>
      <p>Explore hobbies. Discover passion. Connect with creativity.</p>
    </div>

    <div class="footer-section">
      <h4>Legal</h4>
      <ul>
        <li><a href="#">Terms of Service</a></li>
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Cookies Policy</a></li>
      </ul>
    </div>

    <!-- Auth Modal -->
<div v-if="showAuth" class="auth-overlay" @click.self="closeAuth">
  <div class="auth-modal">
    <button class="close-btn" @click="closeAuth">×</button>

    <h2>{{ isLogin ? 'Log In to HobiSpace' : 'Create an Account' }}</h2>

    <form @submit.prevent="handleSubmit">
      <div v-if="!isLogin" class="form-group">
        <label>Full Name</label>
        <input type="text" v-model="form.name" required />
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" v-model="form.email" required />
      </div>

      <div class="form-group password-group">
  <label>Password</label>

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
        {{ isLogin ? 'Log In' : 'Sign Up' }}
      </button>
    </form>

    <p class="switch-text">
      {{ isLogin ? "Don't have an account?" : "Already have an account?" }}
      <span @click="toggleAuth">
        {{ isLogin ? "Sign Up" : "Log In" }}
      </span>
    </p>
  </div>
</div>

    <div class="footer-section">
      <h4>Follow Us</h4>
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
  data() {
    return {
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
}
</script>
  