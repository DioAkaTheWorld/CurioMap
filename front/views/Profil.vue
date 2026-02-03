<script setup>
    import { useRouter } from 'vue-router'

    const router = useRouter();

    function goMap(){
        router.push('/map')
    }

</script>

<template>
    <button class="btn" @click="goMap">Retour</button>
    <h1>Vue Profil</h1>
</template>

<style scoped>
 .btn{
    margin-top: 70px;
 }
</style>