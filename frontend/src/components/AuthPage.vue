<template>
  <v-container class="fill-height" max-width="500">
    <v-card class="pa-6" width="100%">
      <h1 class="text-h4 mb-4 text-center">
        {{ isLogin ? 'Pieslēgties' : 'Reģistrēties' }}
      </h1>

      <v-form @submit.prevent="handleSubmit">
        <v-text-field
          v-if="!isLogin"
          v-model="form.name"
          label="Vārds"
          required
        />

        <v-text-field
          v-model="form.email"
          label="E-pasts"
          type="email"
          required
        />

        <v-text-field
          v-model="form.password"
          label="Parole"
          type="password"
          required
        />

        <v-btn type="submit" color="primary" block class="mt-4">
          {{ isLogin ? 'Pieslēgties' : 'Izveidot kontu' }}
        </v-btn>
      </v-form>

      <p class="text-center mt-4">
        <span v-if="isLogin">Nav konta?</span>
        <span v-else>Jau ir konts?</span>

        <v-btn variant="text" color="primary" @click="isLogin = !isLogin">
          {{ isLogin ? 'Reģistrēties' : 'Pieslēgties' }}
        </v-btn>
      </p>
    </v-card>
  </v-container>
</template>

<script setup>
import { reactive, ref } from 'vue'

const isLogin = ref(true)

const form = reactive({
  name: '',
  email: '',
  password: '',
})

async function handleSubmit() {
  const url = isLogin.value
    ? 'http://127.0.0.1:8000/api/login'
    : 'http://127.0.0.1:8000/api/register'

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(form),
    })

    const data = await response.json()

    if (!response.ok) {
      alert(data.message || 'Kļūda! Pārbaudi ievadītos datus.')
      return
    }

    alert(data.message)
  } catch (error) {
    console.error(error)
    alert('Nevar pieslēgties Laravel serverim.')
  }
}
</script>