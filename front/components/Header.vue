<script setup>
    import { useRouter } from 'vue-router'

    const router = useRouter();

    function goProfile() {
        router.push('/profil')
    }
</script>

<template>
    <header class="top-bar">
        <img src="../../curiomap.ico" alt="logo" class="logo"></img>
        <h1 class="title">CurioMap</h1>

        <button @click="goProfile" class="profile-btn">Profil</button>
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
        padding-left: 80px;  
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;           
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
        margin-left: 10px;
    }

    .profile-btn {
        margin-left: auto;
        margin-right: 140px; 
        padding: 8px 16px;
        background-color: #111;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }


</style>