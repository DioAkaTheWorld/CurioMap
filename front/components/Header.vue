<script setup>
import {useRoute, useRouter} from 'vue-router'
const emit = defineEmits(['recentrer'])

    const router = useRouter();
    const route = useRoute();

    function goProfil() {
        router.push('/profil')
    }
    function goAgenda() {
      router.push('/agenda')
    }
    function goMap(){
      router.push('/map')
    }
    function recentrer(){
      emit('recentrer')
    }
</script>

<template>
    <header class="top-bar">
        <img src="../public/curiomap.ico" alt="logo" class="logo"></img>
        <h1 class="title">CurioMap</h1>

      <button v-if="route.path==='/map'" @click="recentrer" class="recenter-btn" title="Recentrer">📍</button>
      <button v-if="route.path!=='/map'" @click="goMap" class="map-btn" title="Retourner à la map">🗺️</button>
      <button v-if="route.path!== '/agenda'" @click="goAgenda" class="agenda-btn" title="Accéder à votre agenda">📅</button>
      <button v-if="route.path!=='/profil'" @click="goProfil" class="profile-btn" title="Profil">👤</button>
    </header>
</template>

<style scoped>
.top-bar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 70px;
  background: white;
  display: flex;
  align-items: center;
  padding: 0 10px;
  z-index: 1000;
  gap: 16px;
}

.logo {
  width: 60px;
  height: auto;
  border-radius: 20%;
}

.title {
  font-size: 32px;
  font-weight: bold;
  color: #111;
  margin-right: auto;
}

.map-btn,
.agenda-btn,
.profile-btn,
.recenter-btn {
  padding: 10px 20px;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  height:70%;
  white-space: nowrap;
  margin-right:30px;
}

.recenter-btn {
  background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
  color: white;
  font-size: 20px;
  padding: 8px 16px;
}

.recenter-btn:hover {
  transform: translateY(-2px);
}

.map-btn {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  color: white;
}

.map-btn:hover {
  transform: translateY(-2px);
}

.agenda-btn {
  background: #6366f1;
  color: white;
}

.agenda-btn:hover {
  transform: translateY(-2px);
}

.profile-btn {
  background:#d97706;
}

.profile-btn:hover {
  transform: translateY(-2px);
}

.map-btn:active,
.agenda-btn:active,
.profile-btn:active,
.recenter-btn:active {
  transform: translateY(0);
}

</style>

