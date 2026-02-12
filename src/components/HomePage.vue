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
      <button class="login-btn">Log in</button>
      <button class="signup-btn">Sign Up</button>
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
  
  
  