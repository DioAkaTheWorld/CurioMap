import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    plugins: [vue()],
    base: '/',
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'src')
        }
    },
    server: {
        allowedHosts: ['docketu.iutnc.univ-lorraine.fr'],
        host: true,
        port: 5173,
        watch: {
            usePolling: true
        }
    }
})
