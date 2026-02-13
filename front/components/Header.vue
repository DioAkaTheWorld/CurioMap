<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const emit = defineEmits(['recentrer'])
const authStore = useAuthStore()

const router = useRouter()
const route = useRoute()

function goProfil() {
  router.push('/profil')
}

function goAgenda() {
  router.push('/agenda')
}

function goMap() {
  router.push('/map')
}

function recentrer() {
  emit('recentrer')
}

function goConnexion() {
  router.push('/connexion')
}

function logout() {
  if (confirm('Êtes-vous sûr de vouloir vous déconnecter?')) {
    authStore.logout()
    router.push('/connexion')
  }
}
</script>

<template>
    <header class="top-bar">
        <img src="../public/curiomap.ico" alt="logo" class="logo" />
        <h1 class="title">CurioMap</h1>

      <button v-if="route.path==='/map'" @click="recentrer" class="recenter-btn" title="Recentrer">📍</button>
      <button v-if="route.path!=='/map'" @click="goMap" class="map-btn" title="Retourner à la map">🗺️</button>

      <template v-if="authStore.isLoggedIn">
        <button v-if="route.path!== '/agenda'" @click="goAgenda" class="agenda-btn" title="Accéder à votre agenda">📅</button>
        <button v-if="route.path!=='/profil'" @click="goProfil" class="profile-btn" title="Profil">👤</button>
        <button @click="logout" class="logout-btn" title="Se déconnecter">🚪</button>
      </template>

      <button v-if="!authStore.isLoggedIn" @click="goConnexion" class="login-btn" title="Se connecter">🔐</button>
    </header>
</template>

<style scoped>
.top-bar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 5em;
  background: white;
  display: flex;
  align-items: center;
  padding: 0 10px;
  z-index: 100;
  gap: 16px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo {
  width: 4em;
  height: auto;
  border-radius: 20%;
}

.title {
  font-size: 2em;
  font-weight: bold;
  color: #111;
  margin-right: auto;
}

.map-btn,
.agenda-btn,
.profile-btn,
.recenter-btn,
.login-btn,
.logout-btn {
  width: 2em;
  height: 2em;
  padding: 0;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 1.5em;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right:1em;
}

.recenter-btn {
  background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
}

.map-btn {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
}

.agenda-btn {
  background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
}

.profile-btn {
  background: linear-gradient(135deg, #dd9900 0%, #cc8800 100%);
}

.login-btn {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.logout-btn {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.map-btn:hover,
.agenda-btn:hover,
.profile-btn:hover,
.recenter-btn:hover,
.login-btn:hover,
.logout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.map-btn:active,
.agenda-btn:active,
.profile-btn:active,
.recenter-btn:active,
.login-btn:active,
.logout-btn:active {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
  .top-bar {
    height: 4em;
    padding: 0 1%;
    gap: 12px;
  }

  .logo {
    width: 3em;
  }

  .title {
    font-size: 1.5em;
  }

  .map-btn,
  .agenda-btn,
  .profile-btn,
  .recenter-btn,
  .login-btn,
  .logout-btn {
    width: 1.9em;
    height: 1.9em;
    font-size: 1.3em;
    margin-right: 0.7em;
  }
}

@media (max-width: 480px) {
  .top-bar {
    height: 3.5em;
    gap: 8px;
  }

  .logo {
    width: 2.5em;
  }

  .title {
    font-size: 1.2em;
  }

  .map-btn,
  .agenda-btn,
  .profile-btn,
  .recenter-btn,
  .login-btn,
  .logout-btn {
    width: 1.6em;
    height: 1.6em;
    font-size: 1.1em;
    margin-right: 0.5em;
    border-radius: 10px;
  }
}
</style>

