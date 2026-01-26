<template>
    <div class="homepage">
      <!-- Navigation Bar -->
      <header class="navbar">
        <div class="logo-container">
          <img src="@/assets/logo.svg" alt="HobiSpace Logo" class="logo" />
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
  </template>
  
  <script setup>
    // No additional logic needed for this static homepage
  </script>
  
  <style scoped>
  /* General Reset */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f9f9f9;
    color: #333;
  }
  
  /* Navbar */
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background-color: #1d2671;
    color: white;
    position: sticky;
    top: 0;
    z-index: 1000;
  }
  
  .logo-container {
    display: flex;
    align-items: center;
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
    color: white;
    font-size: 1rem;
    transition: color 0.3s;
  }
  
  .nav-links a:hover {
    color: #ffcc00;
  }
  
  /* Hero Section */
  .hero {
    height: 100vh;
    background: linear-gradient(135deg, #1d2671, #c33764);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 2rem;
  }
  
  .hero-content h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
  }
  
  .hero-content p {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    opacity: 0.9;
  }
  
  .cta-button {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background-color: #ffcc00;
    color: #1d2671;
    font-size: 1rem;
    font-weight: bold;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
  }
  
  .cta-button:hover {
    background-color: #1d2671;
    color: #ffcc00;
  }
  
  /* Responsive Design */
  @media (max-width: 768px) {
    .hero-content h1 {
      font-size: 2.5rem;
    }
  
    .hero-content p {
      font-size: 1rem;
    }
  
    .cta-button {
      font-size: 0.9rem;
    }
  
    .nav-links {
      gap: 1rem;
    }
  }
  </style>