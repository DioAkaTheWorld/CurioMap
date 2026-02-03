import { createRouter, createWebHistory } from 'vue-router'
import Map from '../views/Map.vue'
import ProfilView from '../views/ProfilView.vue'

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
            path:'/profil',
            name:'profil',
            component: ProfilView
        }
    ],
})

export default router
