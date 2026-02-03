<template>
  <div class="calendar-wrapper">
    <ScheduleXCalendar :calendar-app="calendarApp" />
  </div>
</template>

<script setup>
import { ScheduleXCalendar } from "@schedule-x/vue"
import {
  createCalendar,
  createViewMonthGrid,
  createViewWeek,
  createViewDay
} from "@schedule-x/calendar"
import '@schedule-x/theme-default/dist/index.css'

const calendarApp = createCalendar({
  views: [
    createViewMonthGrid(),
    createViewWeek(),
    createViewDay(),
  ],
  selectedDate: Temporal.PlainDate.from('2026-02-02'),
  locale: 'fr-FR',
  events: [
    {
      id: 1,
      title: 'Test Evenement',
      start: Temporal.PlainDateTime.from('2026-02-02T10:00').toZonedDateTime('Europe/Paris'),
      end: Temporal.PlainDateTime.from('2026-02-02T12:00').toZonedDateTime('Europe/Paris'),
    }
  ]
})
</script>

<style scoped>
.calendar-wrapper {
  height: 800px;
  width: 100%;
}
</style>