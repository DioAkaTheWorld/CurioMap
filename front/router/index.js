import { createRouter, createWebHistory } from 'vue-router'
import Map from '../views/Map.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path:'/',
            name:'Map',
            component: Map
        },
    ],
})

export default router
