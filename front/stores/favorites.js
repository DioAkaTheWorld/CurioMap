import { defineStore } from 'pinia'
import { useAuthStore } from './auth'

export const useFavoritesStore = defineStore('favorites', {
  state: () => ({
    favorites: [],
  }),

  getters: {
    isFavorite: (state) => (pointId) => {
      return state.favorites.includes(pointId)
    }
  },

  actions: {
    async fetchFavorites() {
      const authStore = useAuthStore()
      if (!authStore.isLoggedIn) {
        this.favorites = []
        return
      }

      try {
        const response = await fetch(
          `${import.meta.env.VITE_API_URL}/users/${authStore.user.id}/favorites`,
          {
            headers: authStore.getAuthHeaders()
          }
        )

        if (response.ok) {
          const data = await response.json()
          this.favorites = data.map(point => point.id)
        } else {
          const errorData = await response.json()
          console.error('Erreur chargement favoris:', errorData)
          this.favorites = []
        }
      } catch (error) {
        console.error('Erreur lors du chargement des favoris:', error)
        this.favorites = []
      }
    },

    async addFavorite(pointId) {
      const authStore = useAuthStore()
      if (!authStore.isLoggedIn) {
        alert('Vous devez être connecté pour ajouter des favoris')
        return false
      }

      try {
        const response = await fetch(
          `${import.meta.env.VITE_API_URL}/users/${authStore.user.id}/favorites/${pointId}`,
          {
            method: 'POST',
            headers: authStore.getAuthHeaders()
          }
        )

        if (response.ok) {
          if (!this.favorites.includes(pointId)) {
            this.favorites.push(pointId)
          }
          return true
        } else {
          const errorData = await response.json()
          console.error('Erreur ajout favori:', errorData)
          return false
        }
      } catch (error) {
        console.error('Erreur lors de l\'ajout du favori:', error)
        return false
      }
    },

    async removeFavorite(pointId) {
      const authStore = useAuthStore()
      if (!authStore.isLoggedIn) return false

      try {
        const response = await fetch(
          `${import.meta.env.VITE_API_URL}/users/${authStore.user.id}/favorites/${pointId}`,
          {
            method: 'DELETE',
            headers: authStore.getAuthHeaders()
          }
        )

        if (response.ok) {
          this.favorites = this.favorites.filter(id => id !== pointId)
          return true
        } else {
          const errorData = await response.json()
          console.error('Erreur suppression favori:', errorData)
          return false
        }
      } catch (error) {
        console.error('Erreur lors de la suppression du favori:', error)
        return false
      }
    },

    async toggleFavorite(pointId) {
      if (this.isFavorite(pointId)) {
        return await this.removeFavorite(pointId)
      } else {
        return await this.addFavorite(pointId)
      }
    },

    clearFavorites() {
      this.favorites = []
    }
  }
})

