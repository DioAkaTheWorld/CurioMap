<template>
  <div>
    <button
      v-if="!isOpen"
      @click="togglePanel"
      class="groupes-toggle-btn"
      title="Mes Groupes"
    >
      👥
    </button>

    <transition name="slide">
      <div v-if="isOpen" class="groupes-panel">
        <div class="panel-header">
          <h2>👥 Mes Groupes</h2>
          <button @click="togglePanel" class="close-panel-btn" title="Fermer">✕</button>
        </div>

        <div class="panel-content">
          <p v-if="!authStore.isLoggedIn" class="not-logged">
            Connectez-vous pour gérer vos groupes
          </p>

          <div v-else>
            <!-- Mode Liste -->
            <div v-if="!isCreating">
              <button @click="startCreating" class="btn-create">
                ➕ Créer un groupe
              </button>

              <div v-if="groupes.length === 0" class="empty-message">
                Vous n'avez pas encore de groupe.
              </div>

              <div v-else class="groupes-list">
                 <div v-for="groupe in groupes" :key="groupe.id" class="groupe-item">
                    <h3>{{ groupe.nom }}</h3>
                    <p>{{ groupe.description }}</p>
                 </div>
              </div>
            </div>

            <!-- Mode Création -->
            <div v-else class="creation-form">
              <h3>Nouveau Groupe</h3>
              <form @submit.prevent="creerGroupe">
                <div class="form-group">
                  <label>Nom du groupe</label>
                  <input
                    v-model="nouveauGroupe.nom"
                    type="text"
                    required
                    class="form-control"
                    placeholder="Ex: Voyage Japon 2026"
                  />
                </div>

                <div class="form-group">
                  <label>Description (optionnel)</label>
                  <textarea
                    v-model="nouveauGroupe.description"
                    class="form-control"
                    rows="3"
                    placeholder="Description du groupe..."
                  ></textarea>
                </div>

                <div class="form-actions">
                  <button type="button" @click="cancelCreating" class="btn-secondary">Annuler</button>
                  <button type="submit" class="btn-primary">Créer</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <transition name="fade">
      <div v-if="isOpen" class="panel-overlay" @click="togglePanel"></div>
    </transition>
  </div>
</template>

<script>
import { useAuthStore } from '../stores/auth'

export default {
  name: 'PanelGroupes',
  data() {
    return {
      isOpen: false,
      isCreating: false,
      groupes: [],
      nouveauGroupe: {
        nom: '',
        description: ''
      }
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    }
  },
  methods: {
    togglePanel() {
      this.isOpen = !this.isOpen
      if (this.isOpen && this.authStore.isLoggedIn) {
          //on pourrait charger les groupes existants ici, à faire après
          //this.fetchGroupes();
      }
    },

    startCreating() {
      this.isCreating = true;
      this.nouveauGroupe = { nom: '', description: '' };
    },

    cancelCreating() {
      this.isCreating = false;
    },

    async creerGroupe() {
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/groupes`, {
          method: 'POST',
          headers: this.authStore.getAuthHeaders(),
          body: JSON.stringify(this.nouveauGroupe)
        });

        if (response.ok) {
          const createdGroupe = await response.json();
          this.groupes.push(createdGroupe);
          alert('Groupe créé avec succès !');
          this.isCreating = false;
        } else {
          const err = await response.json();
          alert('Erreur: ' + (err.error || 'Problème serveur'));
        }
      } catch (error) {
        console.error(error);
        alert('Impossible de contacter le serveur');
      }
    }
  }
}
</script>

<style scoped>
.groupes-toggle-btn {
  position: fixed;
  top: 200px;
  right: 20px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: none;
  background: white;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  color: #333;
  font-size: 28px;
  cursor: pointer;
  z-index: 1000;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.groupes-toggle-btn:hover {
  transform: scale(1.1);
}

.groupes-panel {
  position: fixed;
  top: 0;
  right: 0;
  height: 100vh;
  width: 400px;
  background: white;
  z-index: 2000;
  box-shadow: -5px 0 25px rgba(0,0,0,0.15);
  display: flex;
  flex-direction: column;
}

.panel-header {
  padding: 20px;
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.panel-header h2 {
  margin: 0;
  font-size: 1.5rem;
}

.close-panel-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.close-panel-btn:hover {
  background: rgba(255, 255, 255, 0.4);
  transform: rotate(90deg);
}

.panel-content {
  padding: 20px;
  flex: 1;
  overflow-y: auto;
}

.not-logged {
  text-align: center;
  color: #666;
  margin-top: 50px;
}

.btn-create {
  width: 100%;
  padding: 12px;
  background: #3388ff;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  margin-bottom: 20px;
  transition: background 0.3s;
}

.btn-create:hover {
  background: #2266dd;
}

.creation-form {
  background: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #eee;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.btn-primary {
  background: #3388ff;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-secondary {
  background: #ccc;
  color: black;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.groupes-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.groupe-item {
  background: #f0f4f8;
  padding: 10px;
  border-radius: 6px;
  border-left: 4px solid #3388ff;
}

.groupe-item h3 {
  margin: 0 0 5px 0;
  font-size: 16px;
}

.groupe-item p {
  margin: 0;
  color: #666;
  font-size: 14px;
}

.panel-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  z-index: 1500;
}
</style>
