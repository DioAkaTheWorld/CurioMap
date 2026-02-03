import { createRouter, createWebHistory } from 'vue-router'
import Map from '../views/Map.vue'
import Agenda from '../views/Agenda.vue'
import Profil from '../views/Profil.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path:'/',
            redirect:'/map'
        },
        {
            path:'/map',
            name:'map',
            component: Map
        },
        {
            path: '/agenda',
            name: 'Agenda',
            component: Agenda
        },
        {
            path:'/profil',
            name:'profil',
            component: Profil
        }
    ],
})

export default router
