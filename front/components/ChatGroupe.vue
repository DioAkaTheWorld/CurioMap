<template>
  <div class="chat-container">
    <div class="chat-header">
      <h3>💬 {{ groupeName }}</h3>
      <button @click="$emit('close')" class="close-chat-btn" title="Fermer">✕</button>
    </div>

    <div class="messages-container" ref="messagesContainer">
      <div v-if="loading" class="loading">Chargement des messages...</div>

      <div v-else-if="messages.length === 0" class="no-messages">
        <p>Aucun message pour l'instant</p>
      </div>

      <div v-else class="messages-list">
        <div
          v-for="msg in messages"
          :key="msg.id"
          :class="['message-item', { 'my-message': msg.idUser === authStore.user?.id }]"
        >
          <div class="message-header">
            <strong>{{ msg.nomUtilisateur }}</strong>
            <span class="message-time">{{ msg.dateCreation }}</span>
          </div>
          <div class="message-content">
            <p>{{ msg.message }}</p>
            <button
              v-if="msg.idUser === authStore.user?.id"
              @click="supprimerMessage(msg.id)"
              class="delete-msg-btn"
              title="Supprimer"
            >
              ✖
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="chat-input">
      <textarea
        v-model="nouveauMessage"
        @keydown.enter.prevent="envoyerMessage"
        placeholder="Écrivez un message"
        rows="2"
      ></textarea>
      <button @click="envoyerMessage" :disabled="!nouveauMessage.trim()" class="send-btn">
        ➤
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import { useAuthStore } from '../stores/auth'

const props = defineProps({
  groupeId: {
    type: Number,
    required: true
  },
  groupeName: {
    type: String,
    required: true
  }
})

defineEmits(['close'])

const authStore = useAuthStore()
const messages = ref([])
const loading = ref(false)
const isInitialLoad = ref(true)
const nouveauMessage = ref('')
const messagesContainer = ref(null)



const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const chargerMessages = async () => {
  //Affiche le message seulement au premier chargement(sinon pas tres pratique)
  if (isInitialLoad.value) {
    loading.value = true
  }

  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/groupes/${props.groupeId}/messages`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })

    if (!response.ok) throw new Error('Erreur chargement messages')

    const newMessages = await response.json()

    const lastMessageId = messages.value.length > 0 ? messages.value[messages.value.length - 1].id : null

    messages.value = newMessages

    if (newMessages.length > 0) {
      const hasNewMessage = !lastMessageId ||
        newMessages[newMessages.length - 1].id !== lastMessageId

      if (hasNewMessage) {
        scrollToBottom()
      }
    }

    if (isInitialLoad.value) {
      isInitialLoad.value = false
      loading.value = false
    }
  } catch (error) {
    console.error('Erreur:', error)
    if (isInitialLoad.value) {
      alert('Impossible de charger les messages')
      loading.value = false
    }
  }
}

const envoyerMessage = async () => {
  if (!nouveauMessage.value.trim()) return

  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/groupes/${props.groupeId}/messages`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        message: nouveauMessage.value
      })
    })

    if (!response.ok) throw new Error('Erreur envoi message')

    const newMsg = await response.json()
    messages.value.push(newMsg)
    nouveauMessage.value = ''
    scrollToBottom()
  } catch (error) {
    console.error('Erreur:', error)
    alert('Impossible d\'envoyer le message')
  }
}

const supprimerMessage = async (idMessage) => {
  if (!confirm('Supprimer ce message ?')) return

  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_URL}/groupes/${idMessage}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })

    if (!response.ok) throw new Error('Erreur suppression message')

    messages.value = messages.value.filter(m => m.id !== idMessage)
  } catch (error) {
    console.error('Erreur:', error)
    alert('Impossible de supprimer le message')
  }
}

//refresh des messages toutes les 5 secondes
let refreshInterval = null

onMounted(() => {
  chargerMessages()
  refreshInterval = setInterval(chargerMessages, 5000)
})

//changement de chat si on change de groupe
watch(() => props.groupeId, () => {
  if (refreshInterval) clearInterval(refreshInterval)
  isInitialLoad.value = true
  chargerMessages()
  refreshInterval = setInterval(chargerMessages, 5000)
})

import { onUnmounted } from 'vue'
onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval)
})
</script>

<style scoped>
.chat-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  background: #4a5568;
  color: white;
  border-bottom: 1px solid #e2e8f0;
}

.chat-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 500;
}

.close-chat-btn {
  background: none;
  color: white;
  border: none;
  width: 30px;
  height: 30px;
  cursor: pointer;
  font-size: 24px;
  opacity: 0.7;
  transition: opacity 0.2s;
}

.close-chat-btn:hover {
  opacity: 1;
}

.messages-container {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  background: #f7fafc;
}

.loading, .no-messages {
  text-align: center;
  color: #a0aec0;
  padding: 40px 20px;
  font-size: 14px;
}

.messages-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.message-item {
  display: flex;
  flex-direction: column;
  max-width: 70%;
}

.message-item.my-message {
  align-self: flex-end;
}

.message-header {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  margin-bottom: 4px;
  color: #718096;
}

.message-time {
  font-size: 11px;
  color: #a0aec0;
}

.message-content {
  position: relative;
  background: white;
  padding: 10px 14px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.my-message .message-content {
  background: #4a5568;
  color: white;
}

.message-content p {
  margin: 0;
  word-wrap: break-word;
  line-height: 1.5;
}

.delete-msg-btn {
  position: absolute;
  top: -6px;
  right: -6px;
  background: #e53e3e;
  color: white;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  cursor: pointer;
  font-size: 10px;
  opacity: 0;
  transition: opacity 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.message-item:hover .delete-msg-btn {
  opacity: 1;
}

.chat-input {
  display: flex;
  gap: 10px;
  padding: 15px 20px;
  background: white;
  border-top: 1px solid #e2e8f0;
}

.chat-input textarea {
  flex: 1;
  padding: 10px 15px;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  resize: none;
  font-family: inherit;
  font-size: 14px;
  transition: border-color 0.2s;
}

.chat-input textarea:focus {
  outline: none;
  border-color: #4a5568;
}

.send-btn {
  background: #4a5568;
  color: white;
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  cursor: pointer;
  font-size: 18px;
  transition: background 0.2s, transform 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.send-btn:hover:not(:disabled) {
  background: #2d3748;
  transform: scale(1.05);
}

.send-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
</style>

