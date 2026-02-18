<template>
  <div>
    <button
      v-if="!isOpen"
      @click="togglePanel"
      class="favoris-toggle-btn"
      title="Voir mes favoris"
    >
      ❤️
      <span v-if="favorisCount > 0" class="badge">{{ favorisCount }}</span>
    </button>

    <transition name="slide">
      <div v-if="isOpen" class="favoris-panel">
        <div class="panel-header">
          <h2>❤️ Mes Favoris</h2>
          <button @click="togglePanel" class="close-top-right" title="Fermer">✕</button>
        </div>

        <div class="panel-content">
          <p v-if="!authStore.isLoggedIn" class="not-logged">
            Connectez-vous pour voir vos favoris
          </p>

          <p v-else-if="favoris.length === 0" class="empty-message">
            Vous n'avez pas encore de favoris.<br>
          </p>

          <div v-else class="favoris-list">
            <div
              v-for="point in favoris"
              :key="point.id"
              class="favori-item"
              @click="naviguerVersPoint(point)"
            >
              <div class="item-header">
                <h3>{{ point.titre }}</h3>
                <button
                  @click.stop="retirerFavori(point.id)"
                  class="btn-remove-small"
                  title="Retirer des favoris"
                >
                  ❌
                </button>
              </div>

              <span
                class="category-tag"
                :style="{ backgroundColor: getCategoryColor(point.categorie) }"
              >
                {{ getCategoryLabel(point.categorie) }}
              </span>

              <p v-if="point.description" class="item-description">
                {{ truncateText(point.description, 50) }}
              </p>

              <p v-if="point.adresse" class="item-address">📍 {{ point.adresse }}</p>

              <div class="item-footer">
                <span class="view-hint">Voir sur la carte</span>
              </div>
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
import { useFavoritesStore } from '../stores/favorites'

export default {
  name: 'PanelFavoris',
  emits: ['navigate-to-point'],
  data() {
    return {
      isOpen: false,
      favoris: [],
      categories: []
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    },
    favoritesStore() {
      return useFavoritesStore()
    },
    favorisCount() {
      return this.favoris.length
    }
  },
  watch: {
    'authStore.isLoggedIn'(newVal) {
      if (newVal) {
        this.loadFavoris()
      } else {
        this.favoris = []
        this.isOpen = false
      }
    }
  },
  async mounted() {
    await this.loadCategories()
    if (this.authStore.isLoggedIn) {
      await this.loadFavoris()
    }
  },
  methods: {
    togglePanel() {
      this.isOpen = !this.isOpen
      if (this.isOpen && this.authStore.isLoggedIn) {
        this.loadFavoris()
      }
    },

    async loadCategories() {
      try {
        let url = `${import.meta.env.VITE_API_URL}/categories`
        if (this.authStore.isLoggedIn) {
          url += `?user_id=${this.authStore.user.id}`
        }

        const response = await fetch(url, {
          headers: this.authStore.getAuthHeaders()
        })

        if (response.ok) {
          const data = await response.json()
          this.categories = data.map(cat => ({
            id: cat.id,
            label: cat.libelle,
            iduser: cat.iduser
          }))
        }
      } catch (error) {
        console.error('Erreur chargement catégories:', error)
      }
    },

    async loadFavoris() {
      try {
        const response = await fetch(
          `${import.meta.env.VITE_API_URL}/favorites`,
          {
            headers: this.authStore.getAuthHeaders()
          }
        )

        if (response.ok) {
          this.favoris = await response.json()
        }
      } catch (error) {
        console.error('Erreur chargement favoris:', error)
      }
    },

    async retirerFavori(pointId) {
      const success = await this.favoritesStore.removeFavorite(pointId)
      if (success) {
        this.favoris = this.favoris.filter(p => p.id !== pointId)
        this.$emit('favorites-updated')
      } else {
        alert('Erreur lors de la suppression du favori')
      }
    },

    naviguerVersPoint(point) {
      this.$emit('navigate-to-point', point)
      this.isOpen = false
    },

    getCategoryLabel(id) {
      const cat = this.categories.find(c => c.id === parseInt(id))
      return cat ? cat.label : 'Autre'
    },

    getCategoryColor(id) {
      if (!id) return '#3388ff'
      const intId = parseInt(id)

      switch (intId) {
        case 1: return '#ff9800' //Restaurant
        case 2: return '#774d0e' //Monument
        case 3: return '#ea5a90' //Concert
        case 4: return '#4caf50' //Parc
        case 5: return '#9c27b0' //Musée
        default:
          const c = (intId * 137.508) % 360
          return `hsl(${c}, 70%, 50%)`
      }
    },

    truncateText(text, maxLength) {
      if (!text) return ''
      return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
    }
  }
}
</script>

<style scoped>
.favoris-toggle-btn {
  position: fixed;
  top: 120px;
  right: 20px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: none;
  color: white;
  font-size: 28px;
  cursor: pointer;
  z-index: 1000;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.favoris-toggle-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 20px rgba(230, 57, 70, 0.6);
}

.favoris-toggle-btn .badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #fff;
  color: #e63946;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
  border: 2px solid #e63946;
}

.panel-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  z-index: 999;
}

.favoris-panel {
  position: fixed;
  top: 0;
  right: 0;
  width: 400px;
  max-width: 90vw;
  height: 100vh;
  background: white;
  box-shadow: -4px 0 12px rgba(0, 0, 0, 0.15);
  z-index: 1001;
  display: flex;
  flex-direction: column;
}

.panel-header {
  padding: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: relative;
}

.panel-header h2 {
  margin: 0;
  font-size: 1.5rem;
}

.close-top-right {
  position: absolute;
  top: 15px;
  right: 15px;
  background: rgba(255,255,255,0.2);
  border: none;
  color: white;
  font-size: 24px;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 1010;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}
.close-top-right:hover {
  background: rgba(255,255,255,0.3);
  transform: rotate(90deg);
}

.panel-content {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}

.not-logged, .empty-message {
  text-align: center;
  color: #666;
  padding: 40px 20px;
  line-height: 1.6;
}

.favoris-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.favori-item {
  background: #f9f9f9;
  border-radius: 12px;
  padding: 15px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.favori-item:hover {
  background: white;
  border-color: #667eea;
  transform: translateX(-5px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
  gap: 10px;
}

.item-header h3 {
  margin: 0;
  font-size: 1.1rem;
  color: #333;
  flex: 1;
}

.btn-remove-small {
  background: none;
  border: none;
  font-size: 16px;
  cursor: pointer;
  padding: 4px;
  opacity: 0.6;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.btn-remove-small:hover {
  opacity: 1;
  transform: scale(1.2);
}

.category-tag {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  color: white;
  margin-bottom: 10px;
}

.item-description {
  color: #666;
  font-size: 0.9rem;
  line-height: 1.4;
  margin: 10px 0;
}

.item-address {
  color: #888;
  font-size: 0.85rem;
  margin: 5px 0;
}

.item-footer {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #eee;
}

.view-hint {
  color: #667eea;
  font-size: 0.85rem;
  font-weight: 600;
}


@media (max-width: 768px) {
  .favoris-panel {
    width: 100%;
    max-width: 100%;
  }

  .favoris-toggle-btn {
    width: 50px;
    height: 50px;
    font-size: 24px;
    top: 100px;
    right: 15px;
  }

  .favoris-toggle-btn .badge {
    width: 20px;
    height: 20px;
    font-size: 11px;
  }
}
</style>
