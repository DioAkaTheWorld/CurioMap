<script setup>
import {useRoute, useRouter} from 'vue-router'
const emit = defineEmits(['recentrer'])

    const router = useRouter();
    const route = useRoute();

    function goProfile() {
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
      <button v-if="route.path!=='/map'" @click="goMap" class="map-btn">Retourner à la map</button>
      <button v-if="route.path!== '/agenda'" @click="goAgenda" class="agenda-btn">Accéder à votre agenda</button>
      <button v-if="route.path!=='/profil'" @click="goProfile" class="profile-btn">Profil</button>
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
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  white-space: nowrap;
}

.recenter-btn {
  background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
  color: white;
  box-shadow: 0 2px 6px rgba(255, 107, 107, 0.3);
  font-size: 20px;
  padding: 8px 16px;
}

.recenter-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
}

.map-btn {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  color: white;
  box-shadow: 0 2px 6px rgba(76, 175, 80, 0.3);
}

.map-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
}

.agenda-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
}

.agenda-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.profile-btn {
  background: linear-gradient(135deg, #111 0%, #333 100%);
  color: white;
  box-shadow: 0 2px 6px rgba(17, 17, 17, 0.3);
}

.profile-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(17, 17, 17, 0.4);
}

.map-btn:active,
.agenda-btn:active,
.profile-btn:active,
.recenter-btn:active {
  transform: translateY(0);
}

</style>

