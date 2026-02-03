<script setup>
import Header from './components/Header.vue'
import { ref } from 'vue'
const mapRef = ref(null)

function recentrer(){
  if(mapRef.value.recentrer){
    mapRef.value.recentrer()
  }
}
</script>
<template>
  <div id="app">
    <Header @recentrer="recentrer" />
    <router-view v-slot="{ Component }">
      <component :is="Component" ref="mapRef" />
    </router-view>
  </div>
</template>
