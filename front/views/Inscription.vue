<template>
  <div class="inscription-container">
    <div class="form">
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
        <button type="submit">Se connecter</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'

const form = reactive({
  email: '',
  password: '',
  confirmPassword:''
})
const sinscrire = () => {
  if(form.confirmPassword===form.password)
  console.log(form);
  else
    console.log("confirmez votre mot de passe")
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

</style>