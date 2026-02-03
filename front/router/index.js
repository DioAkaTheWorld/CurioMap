import { createRouter, createWebHistory } from 'vue-router'
import Map from '../views/Map.vue'
import Agenda from '../views/Agenda.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path:'/',
            name:'Map',
            component: Map
        },
        {
            path:'/agenda',
            name:'Agenda',
            component: Agenda
        }
    ],
})

export default router
