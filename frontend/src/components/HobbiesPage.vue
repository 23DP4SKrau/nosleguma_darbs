<template>
  <div class="hobbies-page">
    <h1>Hobiji</h1>

    <div class="hobbies-grid">
      <div class="hobby-card" v-for="hobby in hobbies" :key="hobby.name">
        <img :src="hobby.image" :alt="hobby.name" class="hobby-photo" />
        <h2>{{ hobby.name }}</h2>
        <p>{{ hobby.description }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const hobbies = [
  { name: 'Fotografēšana', image: new URL('../assets/photo.png', import.meta.url).href, description: 'Mirkļu iemūžināšana fotogrāfijās.' },
  { name: 'Skriešana', image: new URL('../assets/run.png', import.meta.url).href, description: 'Aktīvs hobijs veselībai un izturībai.' },
  { name: 'Dejošana', image: new URL('../assets/dance.png', import.meta.url).href, description: 'Kustība mūzikas ritmā.' },
  { name: 'Dziedāšana', image: new URL('../assets/sing.png', import.meta.url).href, description: 'Balss un mūzikas izpausme.' },
  { name: 'Šūšana', image: new URL('../assets/sew.png', import.meta.url).href, description: 'Apģērbu un auduma darbu veidošana.' },
  { name: 'Lasīšana', image: new URL('../assets/read.png', import.meta.url).href, description: 'Grāmatu un stāstu pasaules iepazīšana.' },
  { name: 'Fitness', image: new URL('../assets/fitness.png', import.meta.url).href, description: 'Treniņi spēkam un labsajūtai.' },
  { name: 'Gleznošana', image: new URL('../assets/paint.png', import.meta.url).href, description: 'Radoša izpausme ar krāsām.' },
  { name: 'Rakstīšana', image: new URL('../assets/write.png', import.meta.url).href, description: 'Domu un stāstu pierakstīšana.' },
  { name: 'Riteņbraukšana', image: new URL('../assets/cycle.png', import.meta.url).href, description: 'Braukšana ar riteni dabā vai pilsētā.' },
  { name: 'Peldēšana', image: new URL('../assets/swim.png', import.meta.url).href, description: 'Sports un atpūta ūdenī.' },
  { name: 'Mūzika', image: new URL('../assets/music.png', import.meta.url).href, description: 'Mūzikas klausīšanās vai spēlēšana.' },
  { name: 'Makšķerēšana', image: new URL('../assets/fish.png', import.meta.url).href, description: 'Mierīga atpūta pie ūdens.' },
  { name: 'Videospēles', image: new URL('../assets/game.png', import.meta.url).href, description: 'Spēļu spēlēšana izklaidei.' },
  { name: 'Ēdiena gatavošana', image: new URL('../assets/cook.png', import.meta.url).href, description: 'Jaunu recepšu izmēģināšana.' },
  { name: 'Cepšana', image: new URL('../assets/bake.png', import.meta.url).href, description: 'Kūku, cepumu un citu gardumu gatavošana.' },
  { name: 'Rokdarbi', image: new URL('../assets/diy.png', import.meta.url).href, description: 'Radošu lietu veidošana ar rokām.' }
]
</script>

<style scoped>
.hobbies-page {
  padding: 3rem 2rem;
  background-color: #fdf8f0;
  min-height: 100vh;
  text-align: center;
}

.hobbies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.5rem;
}

.hobby-card {
  background: white;
  border-radius: 12px;
  padding: 1rem;
  text-align: left;
}

.hobby-photo {
  width: 100%;
  height: 160px;
  object-fit: cover;
  border-radius: 10px;
  display: block;
  margin-bottom: 1rem;
}

.hobby-card h2 {
  font-size: 1.25rem;
  font-weight: bold;
}

.hobby-card p {
  color: #333;
}
</style>