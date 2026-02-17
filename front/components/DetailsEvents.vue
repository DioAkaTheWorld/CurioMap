<template>
  <div v-if="show" class="modal-overlay" @click.self="fermer">
    <div class="modal-content">
      <button class="close-btn" @click="fermer">&times;</button>

      <h2 class="event-title">{{ event.title }}</h2>

      <div class="event-details">
        <div class="detail-row">
          <span class="icon">📅</span>
          <div class="detail-info">
            <strong>Date de début :</strong>
            <p>{{ formatDate(event.start) }} {{ formatTime(event.start) }}</p>
          </div>
        </div>

        <div class="detail-row">
          <span class="icon">📅</span>
          <div class="detail-info">
            <strong>Date de fin :</strong>
            <p>{{ formatDate(event.end) }} {{ formatTime(event.end) }}</p>
          </div>
        </div>

        <div class="detail-row notes">
          <span class="icon">📝</span>
          <div class="detail-info">
            <strong>Notes :</strong>
            <textarea
                v-if="editingNotes"
                v-model="notesValue"
                class="notes-textarea"
                rows="4"
                maxlength="150"
            ></textarea>
            <p v-else>{{ event.description || '' }}</p>
          </div>
          <button class="edit-btn" @click="toggleEditNotes" :title="editingNotes ? 'Enregistrer' : 'Modifier les notes'">
            {{ editingNotes ? '💾' : '✏️' }}
          </button>
        </div>
      </div>

      <button class="delete-btn" @click="supprimerEvenement" title="Supprimer l'événement">
        Supprimer l'événement
      </button>
    </div>
  </div>
</template>

<script setup>
import {defineProps, defineEmits, ref, watch, onMounted} from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  event: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'update:event', 'delete:event']);
const editingNotes = ref(false);
const notesValue = ref(null);

watch(() => props.event, (newEvent) => {
  notesValue.value = newEvent.description || ''
}, {immediate: true})

const toggleEditNotes = async () => {
  if (editingNotes.value) {
    let nouvellesNotes = notesValue.value;
    const payload = {
      id: props.event.id,
      notes: nouvellesNotes
    };
    try {
      console.log(props.event.id)
      const response = await fetch(`${import.meta.env.VITE_API_URL}/notes`, {
        method: 'POST',
        headers: authStore.getAuthHeaders(),
        body: JSON.stringify(payload)
    })
      if(response.ok){
        console.log(response);
        notesValue.value=nouvellesNotes;
        props.event.description=nouvellesNotes;
        emit('update:event');
      }
    }
    catch(error){
      console.log(error);
    }
  }
  editingNotes.value = !editingNotes.value
}

const supprimerEvenement = async () => {
    if (!confirm("Voulez-vous vraiment supprimer cet événement ?")) return;

    try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/agenda/${props.event.id}`, {
            method: 'DELETE',
            headers: authStore.getAuthHeaders()
        });

        if (response.ok) {
            emit('delete:event', props.event.id);
            emit('close');
        } else {
            const err = await response.json();
            alert("Erreur lors de la suppression : " + (err.error || "Erreur inconnue"));
        }
    } catch (e) {
        console.error(e);
        alert("Impossible de contacter le serveur.");
    }
}

const fermer = () => {
  editingNotes.value=false;
  emit('close')
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'

  try {
    if (dateString.toPlainDate) {
      const plainDate = dateString.toPlainDate()
      return new Date(plainDate.year, plainDate.month - 1, plainDate.day)
        .toLocaleDateString('fr-FR', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        })
    }
    const date = new Date(dateString)
    return date.toLocaleDateString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (e) {
    return dateString.toString()
  }
}

const formatTime = (dateString) => {
  if (!dateString) return 'N/A'

  try {
    if (dateString.toPlainTime) {
      const plainTime = dateString.toPlainTime()
      return `${String(plainTime.hour).padStart(2, '0')}:${String(plainTime.minute).padStart(2, '0')}`
    }

    const date = new Date(dateString)
    return date.toLocaleTimeString('fr-FR', {
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch (e) {
    return dateString.toString()
  }
}

</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
  backdrop-filter: blur(3px);
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
  position: relative;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: none;
  border: none;
  font-size: 32px;
  cursor: pointer;
  color: #999;
  line-height: 1;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #f0f0f0;
  color: #333;
  transform: rotate(90deg);
}

.event-title {
  margin: 0 0 25px 0;
  font-size: 24px;
  color: #333;
  padding-right: 30px;
  border-bottom: 3px solid #4CAF50;
  padding-bottom: 10px;
}

.event-details {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.detail-row {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  padding: 12px;
  border-radius: 8px;
  background: #f9f9f9;
  transition: background 0.2s;
}

.detail-row:hover {
  background: #f0f0f0;
}

.detail-row.notes {
  align-items: flex-start;
}

.icon {
  font-size: 24px;
  flex-shrink: 0;
}

.detail-info {
  flex: 1;
}

.detail-info strong {
  display: block;
  color: #555;
  font-size: 14px;
  margin-bottom: 5px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.detail-info p {
  margin: 0;
  color: #222;
  font-size: 16px;
  word-wrap: break-word;
}

.notes .detail-info p {
  white-space: pre-wrap;
  line-height: 1.5;
}
.notes-textarea {
  resize: none;
  width:100%;
}

.delete-btn {
  background-color: #f44336;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 10px 15px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.3s;
  width: 100%;
  margin-top: 15px;
}

.delete-btn:hover {
  background-color: #d32f2f;
}

@media (max-width: 768px) {
  .modal-content {
    width: 95%;
    padding: 20px;
    max-height: 90vh;
  }

  .event-title {
    font-size: 20px;
  }

  .icon {
    font-size: 20px;
  }

  .detail-info strong {
    font-size: 12px;
  }

  .detail-info p {
    font-size: 14px;
  }
}

@media (max-width: 480px) {
  .modal-content {
    padding: 15px;
  }

  .event-title {
    font-size: 18px;
    margin-bottom: 20px;
  }

  .detail-row {
    gap: 10px;
    padding: 10px;
  }

  .icon {
    font-size: 18px;
  }
}
</style>
