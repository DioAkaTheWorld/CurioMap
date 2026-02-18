<template>
  <div v-if="show" class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <button class="close-btn" @click="$emit('close')">&times;</button>

      <div v-if="point" class="point-header">
        <h2>{{ point.titre }}</h2>
        <span v-if="point.categorieLibelle" class="category-badge" :style="{ backgroundColor: getCategoryColor(point.categorie) }">
          {{ point.categorieLibelle }}
        </span>
      </div>

      <div v-if="point" class="point-info">
        <p v-if="point.description"><strong>📝 Description:</strong> {{ point.description }}</p>
        <p v-if="point.dateDebut"><strong>📅 Début:</strong> {{ point.dateDebut }}</p>
        <p v-if="point.dateFin"><strong>📅 Fin:</strong> {{ point.dateFin }}</p>
        <p v-if="point.adresse"><strong>📍 Adresse:</strong> {{ point.adresse }}</p>
      </div>

      <div class="add-comment">
        <h4>Ajouter un commentaire</h4>
        <div class="stars">
          <button v-for="n in 5" :key="n" @click="newComment.note = n" :class="{active: newComment.note>=n}" type="button">⭐</button>
          <button v-if="newComment.note" @click="newComment.note = null" type="button" class="clear">✖</button>
        </div>
        <textarea v-model="newComment.texte" placeholder="Votre avis..." rows="3"></textarea>
        <button @click="ajouterCommentaire" :disabled="!newComment.texte.trim()">Publier</button>
      </div>

      <div class="commentaires-section">
        <h3>💬 Commentaires ({{ commentaires.length }})</h3>

        <div v-if="loading" class="loading">Chargement des commentaires...</div>

        <div v-else-if="commentaires.length === 0" class="no-comments">
          <p>Aucun commentaire pour ce point.</p>
        </div>

        <div v-else class="comments-list">
          <div v-for="comment in commentaires" :key="comment.id" class="comment-item">
            <div class="comment-header">
              <div>
                <strong>{{ comment.nom_utilisateur || 'Utilisateur' }}</strong>
                <span class="comment-date">{{ comment.date_creation }}</span>
              </div>
              <div class="comment-actions">
                <div v-if="comment.note" class="rating">
                  {{ '⭐'.repeat(comment.note) }}
                </div>
                <button
                  v-if="authStore.user && authStore.user.id === comment.iduser"
                  @click="supprimerCommentaire(comment.id)"
                  class="delete-btn"
                  title="Supprimer"
                >
                  ✖
                </button>
              </div>
            </div>
            <p class="comment-text">{{ comment.commentaire }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useAuthStore } from '../stores/auth'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  point: {
    type: Object,
    default: null
  }
})

defineEmits(['close'])

const authStore = useAuthStore()
const commentaires = ref([])
const loading = ref(false)
const newComment = ref({ texte: '', note: null })

const getCategoryColor = (categoryId) => {
  if (!categoryId) return '#3388ff'
  const intId = parseInt(categoryId)

  switch (intId) {
    case 1: return '#ff9800'
    case 2: return '#774d0e'
    case 3: return '#ea5a90'
    case 4: return '#4caf50'
    case 5: return '#9c27b0'
    default:
      const c = (intId * 137.508) % 360
      return `hsl(${c}, 70%, 50%)`
  }
}

const chargerCommentaires = async () => {
  if (!props.point?.id) return

  loading.value = true
  try {
    const response = await fetch(`${import.meta.env.VITE_API_URL}/points/${props.point.id}/commentaires`)
    if (response.ok) {
      commentaires.value = await response.json()
    } else {
      console.error('Erreur chargement commentaires:', response.status)
    }
  } catch (error) {
    console.error('Erreur chargement commentaires:', error)
  } finally {
    loading.value = false
  }
}

const ajouterCommentaire = async () => {
  if (!authStore.isLoggedIn) {
    alert('Connectez-vous pour commenter ce point')
    return
  }

  try {
    const response = await fetch(`${import.meta.env.VITE_API_URL}/points/${props.point.id}/commentaires`, {
      method: 'POST',
      headers: {
        ...authStore.getAuthHeaders(),
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        idpoint: props.point.id,
        commentaire: newComment.value.texte.trim(),
        note: newComment.value.note
      })
    })

    if (response.ok) {
      newComment.value = { texte: '', note: null }
      await chargerCommentaires()
    } else {
      alert('Erreur lors de l\'ajout')
    }
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur réseau')
  }
}

const supprimerCommentaire = async (idComm) => {
  if (!confirm('Supprimer ce commentaire ?')) return

  try {
    const response = await fetch(`${import.meta.env.VITE_API_URL}/commentaires/${idComm}`, {
      method: 'DELETE',
      headers: authStore.getAuthHeaders()
    })

    if (response.ok) {
      await chargerCommentaires()
    } else {
      alert('Erreur lors de la suppression')
    }
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur réseau')
  }
}

watch(() => props.show, (newVal) => {
  if (newVal && props.point) {
    chargerCommentaires()
  }
  if (!newVal) {
    newComment.value = { texte: '', note: null }
  }
})
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
  padding-top:2%;
}

.modal-content {
  background: white;
  padding: 25px;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow-y: auto;
  position: relative;
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: none;
  border: none;
  font-size: 28px;
  cursor: pointer;
  color: #999;
  width: 30px;
  height: 30px;
  padding: 0;
  line-height: 1;
}

.close-btn:hover {
  color: #333;
}

.point-header {
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid #eee;
}

.point-header h2 {
  margin: 0 0 10px 0;
  color: #333;
}

.category-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  color: white;
  font-size: 13px;
  font-weight: bold;
}

.point-info {
  margin-bottom: 20px;
  border-bottom: 2px solid #eee;
  padding-bottom: 10px;
}

.point-info p {
  margin: 8px 0;
  color: #555;
}

.commentaires-section {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 2px solid #eee;
}

.commentaires-section h3 {
  margin-bottom: 15px;
  color: #333;
}

.loading,
.no-comments {
  text-align: center;
  padding: 20px;
  color: #999;
  font-style: italic;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.comment-item {
  background: #f9f9f9;
  padding: 15px;
  border-radius: 8px;
}

.comment-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}

.comment-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.delete-btn {
  background: none;
  border: none;
  font-size: 16px;
  cursor: pointer;
  opacity: 0.6;
  padding: 4px;
  transition: opacity 0.2s;
}

.delete-btn:hover {
  opacity: 1;
}

.comment-header strong {
  display: block;
  color: #333;
  font-size: 14px;
}

.comment-date {
  display: block;
  font-size: 12px;
  color: #999;
  margin-top: 2px;
  font-weight: normal;
}

.rating {
  font-size: 14px;
}

.comment-text {
  color: #555;
  line-height: 1.6;
  margin: 0;
}

.add-comment {
  margin-top: 20px;
  padding: 15px;
  background: #f5f5f5;
  border-radius: 8px;
}

.add-comment h4 {
  margin: 0 0 10px 0;
  font-size: 15px;
  color: #333;
}

.add-comment .stars {
  margin-bottom: 10px;
  display: flex;
  gap: 5px;
  align-items: center;
}

.add-comment .stars button {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  opacity: 0.3;
  padding: 0;
}

.add-comment .stars button.active {
  opacity: 1;
}

.add-comment .stars .clear {
  background: #e74c3c;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 11px;
  opacity: 1;
  margin-left: 5px;
}

.add-comment textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-family: inherit;
  resize: vertical;
  box-sizing: border-box;
  margin-bottom: 10px;
}

.add-comment > button {
  background: #3388ff;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 500;
}

.add-comment > button:disabled {
  background: #ccc;
  cursor: not-allowed;
}
</style>

