<!--
  V5.0 — Le code de la V4, déplacé tel quel dans un fichier .vue.
  Un fichier .vue regroupe trois blocs : <script setup>, <template>, <style>.
-->
<script setup>
import { ref, computed, onMounted } from 'vue'

// Plus d'URL absolue : grâce au proxy de vite.config.js,
// '/api/...' part vers le même serveur que la page (localhost:5173).
const API = '/api/formations'

const formations = ref([])
const chargement = ref(true)
const erreur     = ref('')
const filtre     = ref('Tous')
const niveaux    = ['Tous', 'L1', 'L2', 'L3']

const formationsFiltrees = computed(() =>
  filtre.value === 'Tous'
    ? formations.value
    : formations.value.filter(f => f.niveau === filtre.value)
)

onMounted(async () => {
  try {
    const reponse = await fetch(API)
    if (!reponse.ok) throw new Error('HTTP ' + reponse.status)
    formations.value = await reponse.json()
  } catch (e) {
    erreur.value = 'Erreur de chargement : ' + e.message
  } finally {
    chargement.value = false
  }
})
// Avec <script setup>, tout ce qui est déclaré ici est utilisable
// dans le template : plus besoin de « return { ... } ».
</script>

<template>
  <header>
    <h1>Nos formations</h1>
    <p>V5.0 — projet Vite, un seul composant (App.vue)</p>
  </header>

  <nav class="filtres">
    <button v-for="n in niveaux" :key="n"
            :class="{ actif: n === filtre }"
            @click="filtre = n">{{ n }}</button>
  </nav>

  <p v-if="erreur" class="message">{{ erreur }}</p>
  <p v-else-if="chargement" class="message">Chargement…</p>

  <main v-else class="liste">
    <!-- La card est encore écrite ici : on la découpera en composant en V5.1 -->
    <article v-for="f in formationsFiltrees" :key="f.id" class="card">
      <h2>{{ f.titre }}</h2>
      <p>{{ f.description }}</p>
      <span class="badge">{{ f.niveau }}</span>
    </article>
  </main>
</template>
