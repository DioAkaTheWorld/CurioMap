<script setup>
</script>

<template>

    <div class="titre-profil">
        <h1>Profil de l'utilisateur</h1>
        <p>Modifier vos informations personnelles</p>
    </div>

    <div class="profil-container">
        <div class="modif-profil">
            <h1>Profil</h1>
            <div class="photo-modif">
            <img class="photo-profil" src="../assets/exemple.png" alt="photo-profil">
                <button class="btn">Modifier la photo</button>
            </div>

            <div class="input-user">
                <input 
                    type="text"
                    placeholder="Quentin54"
                >
                <input 
                    type="text"
                    placeholder="quentin54@gmail.com"
                >
            </div>

            <div>
                <p><strong>Nouveau mot de passe</strong></p>
                <input 
                    type="text"
                    placeholder="mot de passe"
                >
                <p><strong>Confirmer le nouveau mot de passe</strong></p>
                <input 
                    type="text"
                    placeholder="confirmer le mot de passe"
                >
            </div>
            <button class="btn enregistrer">Enregistrer les modifications</button>
        </div>

        <div class="votre-profil">
            <h1>Votre Profil</h1>
            <div class="photo">
                <img class="photo-profil" src="../assets/exemple.png" alt="photo-profil">
            </div>
            <p>Quentin54</p>
            <p>Membre depuis avril 2024</p>
            <div class="ptn-crees">
                <img class="icon-point" src="../assets/icon-point.png" alt="icon-point">
                <p>Points céés</p> 
                <p><strong>3</strong></p>
            </div>

            <div class="fav">
                <img class="icon-fav" src="../assets/icon-fav.png" alt="icon-fav">
                <p>Favoris enregistrés</p>
                <p><strong>10</strong></p>
            </div>
        </div>
    </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
}
.titre-profil {
    max-width: 1200px;
    margin: 5% auto;
    padding: 0 20px;
}
.profil-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.profil-container > h1 {
  font-size: 32px;
  margin-bottom: 5px;
}

.profil-container > p {
  color: #555;
  margin-bottom: 30px;
}

/* ===== LAYOUT PRINCIPAL ===== */
.profil-container {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 40px;
}


/* ===== CARTE MODIF PROFIL ===== */
.modif-profil {
  border: 2px solid #333;
  padding: 30px;
  border-radius: 6px;
}

.modif-profil h1 {
  font-size: 26px;
  margin-bottom: 25px;
}

/* Photo + bouton */
.photo-modif {
  display: flex;
  align-items: center;
  gap: 30px;
  margin-bottom: 25px;
}

.photo-profil {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #333;
}

.btn {
  padding: 10px 18px;
  border: 2px solid #333;
  background: #fff;
  cursor: pointer;
  border-radius: 8px;
  font-weight: bold;
}

.btn:hover {
  background: #f2f2f2;
}

/* Inputs */
.input-user {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 25px;
}

input {
  padding: 12px 15px;
  border-radius: 8px;
  border: 2px solid #ddd;
  font-size: 16px;
}

input:focus {
  outline: none;
  border-color: #333;
}

.modif-profil p {
  margin: 15px 0 5px;
}

.enregistrer {
  margin-top: 25px;
  align-self: flex-end;
}

/* ===== CARTE VOTRE PROFIL ===== */
.votre-profil {
  border: 2px solid #333;
  padding: 30px;
  border-radius: 6px;
  text-align: center;
}

.votre-profil h1 {
  font-size: 26px;
  margin-bottom: 20px;
}

.votre-profil .photo {
  display: flex;
  justify-content: center;
  margin-bottom: 15px;
}

.votre-profil p {
  margin: 6px 0;
}

/* Stats */
.ptn-crees,
.fav {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 10px;
  margin-top: 20px;
}

.icon-point,
.icon-fav {
  width: 24px;
  height: 24px;
}

/* ===== RESPONSIVE TELEPHONE ===== */
@media (max-width: 768px) {
  .profil-container {
    grid-template-columns: 1fr;
    gap: 25px;
  }

  .photo-modif {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .enregistrer {
    width: 100%;
    text-align: center;
  }

  .ptn-crees,
  .fav {
    grid-template-columns: auto 1fr auto;
  }
}
</style>