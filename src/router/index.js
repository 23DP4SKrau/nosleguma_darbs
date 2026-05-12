import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '@/components/HomePage.vue';
import AboutPage from '@/components/AboutPage.vue';
import HobbiesPage from '@/components/HobbiesPage.vue';
import ContactPage from '@/components/ContactPage.vue';
import ProfilePage from '@/components/ProfilePage.vue';

const routes = [
  { path: '/', component: HomePage },
  { path: '/about', component: AboutPage },
  { path: '/hobbies', component: HobbiesPage },
  { path: '/contact', component: ContactPage },
  { path: '/profile', component: ProfilePage },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
