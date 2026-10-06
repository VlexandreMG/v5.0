// Configuration de Vite, l'outil qui transforme nos fichiers .vue
// en JavaScript compréhensible par le navigateur.
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],

  server: {
    port: 5173
    // Proxy : le navigateur appelle /api/... sur localhost:5173,
    // et Vite transmet la requête à l'API PHP sur localhost:8000.
    // Pour le navigateur, front et API ont la même origine : plus de CORS.
    // proxy: {
    //   '/api': 'http://localhost:8000'
    // }
  }
})
