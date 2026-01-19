<script setup>
import HelloWorld from './components/HelloWorld.vue'
import TheWelcome from './components/TheWelcome.vue'
</script>

<template>
  <div class="page">
    <!-- Navigation -->
    <nav class="navbar">
      <ul>
        <li>Home</li>
        <li>About Us</li>
        <li>Contacts</li>
      </ul>
    </nav>

    <!-- Hero section -->
    <main class="hero">
      <h1>HobiSpace</h1>
      <p>Your space for hobbies & inspiration</p>
    </main>
  </div>
</template>

<style scoped>
.page {
  height: 100vh;
  background: linear-gradient(135deg, #1d2671, #c33764);
  color: white;
  font-family: 'Segoe UI', sans-serif;
}

/* Navigation */
.navbar {
  position: absolute;
  top: 20px;
  left: 30px;
}

.navbar ul {
  list-style: none;
  display: flex;
  gap: 25px;
  padding: 0;
  margin: 0;
}

.navbar li {
  cursor: pointer;
  font-size: 16px;
  opacity: 0.85;
  transition: opacity 0.3s;
}

.navbar li:hover {
  opacity: 1;
  text-decoration: underline;
}

/* Hero section */
.hero {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.hero h1 {
  font-size: 80px;
  margin: 0;
  letter-spacing: 2px;
}

.hero p {
  font-size: 22px;
  margin-top: 10px;
  opacity: 0.9;
}
</style>