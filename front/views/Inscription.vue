<template>
  <div class="inscription-container">
    <div class="form">
      <h2>Inscription</h2>

      <div v-if="errorMessage" class="alert alert-error">
        {{ errorMessage }}
      </div>

      <div v-if="successMessage" class="alert alert-success">
        {{ successMessage }}
      </div>

      <form @submit.prevent="sinscrire">
        <div class="form-group">
          <label for="email">Email</label>
          <input
              type="email"
              id="email"
              v-model="form.email"
              placeholder="exemple@mail.com"
              required
          >
        </div>
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input
              type="password"
              id="password"
              v-model="form.password"
              placeholder="Entrez votre mot de passe"
              required
          >
        </div>
        <div class="form-group">
          <label for="confirm-password">Confirmez votre mot de passe</label>
          <input
              type="password"
              id="confirm-password"
              v-model="form.confirmPassword"
              placeholder="Confirmez votre mot de passe"
              required
          >
        </div>
        <button type="submit">S'inscrire</button>
      </form>

      <div class="form-footer">
        <p>Déjà un compte ? <router-link to="/connexion">Se connecter</router-link></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const errorMessage = ref('')
const successMessage = ref('')

const form = reactive({
  email: '',
  password: '',
  confirmPassword: ''
})

const sinscrire = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  if (!form.email || !form.password || !form.confirmPassword) {
    errorMessage.value = 'Veuillez remplir tous les champs'
    return
  }

  if (form.password.length < 6) {
    errorMessage.value = 'Votre mot de passe doit contenir au moins 6 caractères'
    return
  }

  if (form.password !== form.confirmPassword) {
    errorMessage.value = 'Les mots de passe ne correspondent pas'
    return
  }

  const result = await authStore.register(form.email, form.password)

  if (result.success) {
    successMessage.value = 'Inscription réussie!'
    setTimeout(() => {
      router.push('/map')
    }, 1000)
  } else {
    errorMessage.value = result.error || 'Erreur lors de l\'inscription'
  }
}
</script>

<style scoped>
.inscription-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f0f0f0;
}

.form {
  background: white;
  padding: 30px;
  border-radius: 8px;
  width: 100%;
  max-width: 400px;
}

h2 {
  margin-bottom: 20px;
  text-align: center;
  color: #333;
}

.alert {
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 15px;
  font-size: 14px;
}

.alert-error {
  background: #fee;
  color: #c33;
  border: 1px solid #fcc;
}

.alert-success {
  background: #efe;
  color: #3c3;
  border: 1px solid #cfc;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  box-sizing: border-box;
}

input:focus {
  outline: none;
  border-color: #4CAF50;
}

button {
  width: 100%;
  padding: 12px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
}

button:hover {
  background: #45a049;
}

.form-footer {
  margin-top: 20px;
  text-align: center;
}

.form-footer p {
  color: #666;
  font-size: 14px;
}

.form-footer a {
  color: #4CAF50;
  text-decoration: none;
  font-weight: 500;
}

.form-footer a:hover {
  text-decoration: underline;
}

</style>