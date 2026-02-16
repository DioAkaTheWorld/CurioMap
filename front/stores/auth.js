import { defineStore } from 'pinia'

const API_URL = import.meta.env.VITE_API_URL;
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false,
    loading: false,
    error: null
  }),

  getters: {
    isLoggedIn: (state) => state.isAuthenticated,
    currentUser: (state) => state.user,
    isLoading: (state) => state.loading
  },

  actions: {
    async login(email, password) {
      this.loading = true
      this.error = null

      try {
        const response = await fetch(`${API_URL}/auth/login`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ email, password })
        })

        const data = await response.json()

        if (!response.ok || !data.success) {
          this.error = data.error || 'Erreur de connexion'
          this.loading = false
          return {
            success: false,
            error: this.error
          }
        }

        this.user = data.user
        this.token = data.token
        this.isAuthenticated = true
        localStorage.setItem('token', data.token)
        this.loading = false

        return { success: true, user: data.user }

      } catch (error) {
        console.error('Erreur lors de la connexion:', error)
        this.error = 'Impossible de se connecter au serveur'
        this.loading = false
        return {
          success: false,
          error: this.error
        }
      }
    },

    async register(email, password) {
      this.loading = true
      this.error = null

      try {
        const response = await fetch(`${API_URL}/auth/register`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ email, password })
        })

        const data = await response.json()

        if (!response.ok || !data.success) {
          this.error = data.error || 'Erreur lors de l\'inscription'
          this.loading = false
          return {
            success: false,
            error: this.error
          }
        }

        this.user = data.user
        this.token = data.token
        this.isAuthenticated = true
        localStorage.setItem('token', data.token)
        this.loading = false

        return { success: true, user: data.user }

      } catch (error) {
        console.error('Erreur lors de l\'inscription:', error)
        this.error = 'Impossible de se connecter au serveur'
        this.loading = false
        return {
          success: false,
          error: this.error
        }
      }
    },

    logout() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      this.error = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    },

    loadUser() {
      const savedToken = localStorage.getItem('token')
      if (savedToken) {
        try {
          const payload = JSON.parse(atob(savedToken.split('.')[1]))

          if (payload.exp && payload.exp * 1000 < Date.now()) {
            console.warn('Token expiré')
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            return
          }

          this.user = {
            id: payload.user_id,
            email: payload.email
          }
          this.token = savedToken
          this.isAuthenticated = true
        } catch (e) {
          console.error('Erreur lors du chargement du token:', e)
          localStorage.removeItem('token')
          localStorage.removeItem('user')
        }
      }
    },

    getAuthHeaders() {
      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }

      if (this.token) {
        headers['Authorization'] = `Bearer ${this.token}`
      }

      return headers
    }
  }
})

