<template>
  <div class="calendar-wrapper">
    <ScheduleXCalendar :calendar-app="calendarApp" />
    <DetailsEvents
      :show="showDetailsModal"
      :event="selectedEvent"
      @close="closeDetails"
      @update:event="updateEvent"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { ScheduleXCalendar } from "@schedule-x/vue"
import {
  createCalendar,
  createViewMonthGrid,
  createViewWeek,
  createViewDay
} from "@schedule-x/calendar"
import '@schedule-x/theme-default/dist/index.css'
import DetailsEvents from '../components/DetailsEvents.vue'
import router from "../router";
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const showDetailsModal = ref(false)
const selectedEvent = ref({
  title: '',
  start: null,
  end: null,
  description: ''
})

const calendarApp = createCalendar({
  views: [
    createViewMonthGrid(),
    createViewWeek(),
    createViewDay(),
  ],
  selectedDate: Temporal.Now.plainDateISO(),
  locale: 'fr-FR',
  events: [],
  callbacks: {
    onEventClick(calendarEvent) {
      selectedEvent.value = {
        id: calendarEvent.id,
        title: calendarEvent.title,
        start: calendarEvent.start,
        end: calendarEvent.end,
        description: calendarEvent.description || ''
      }
      showDetailsModal.value = true
    }
  }
})

const closeDetails = () => {
  showDetailsModal.value = false
}

const fetchEvenements = async () => {
  try {
    const userId = authStore.user?.id

    if (!userId) {
      console.error('Utilisateur non connecté')
      await router.push('/connexion')
      return
    }

    const response = await fetch(`${import.meta.env.VITE_API_URL}/agenda?user_id=${userId}`, {
      headers: authStore.getAuthHeaders()
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const evenements = await response.json()

    const formattedEvents = evenements.map(event => ({
      id: event.id,
      title: event.titre_evenement || 'Événement sans titre',
      start: Temporal.PlainDateTime.from(event.date_debut.replace(' ', 'T')).toZonedDateTime('Europe/Paris'),
      end: Temporal.PlainDateTime.from(event.date_fin.replace(' ', 'T')).toZonedDateTime('Europe/Paris'),
      description: event.notes || ''
    }))
    //refresh le calendrier avec les events quon a recup
    calendarApp.events.set(formattedEvents)

  } catch (error) {
    console.error('Erreur lors du chargement des événements:', error)
  }
}

const updateEvent = () => {
  fetchEvenements()
}

onMounted(() => {
  fetchEvenements()
})
</script>

<style scoped>
.calendar-wrapper {
  width: 100%;
  padding-top: 5em;
}
:deep(.sx__event) {
  cursor: pointer;
  transition: all 0.2s ease;
}
</style>