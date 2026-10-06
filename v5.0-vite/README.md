# V5.0 — Un vrai projet Vue : l'outil de build Vite

Première étape de la série V5 « De la page Vue à l'application Vue ».
Même page, mêmes données, même API que la V4.

## Ce qui change

En V4, Vue était chargé par une balise `<script>` depuis un CDN, et le template
était écrit dans le HTML. C'est pratique pour découvrir, mais aucun projet réel
n'est organisé ainsi.

Ici, le front devient un **projet Node.js** piloté par **Vite** :
<!--  -->
- le code est écrit dans des fichiers `.vue` (template + script + style), que le
  navigateur ne sait pas lire : Vite les **transforme** en JavaScript ;
- les dépendances (Vue, Vite) sont déclarées dans `package.json` et installées
  par `npm install` ;
- `npm run dev` lance un serveur de développement avec rechargement à chaud ;
- `npm run build` produit une version optimisée dans `dist/`.

Le code de la V4 a été déplacé **tel quel** dans un seul fichier `App.vue`.

## Contenu

```
v5.0-vite/
├── api/                 l'API REST de la V4, inchangée
└── front/
    ├── package.json     dépendances et scripts (dev, build, preview)
    ├── vite.config.js   configuration de Vite, dont le proxy /api
    ├── index.html       une page presque vide : <div id="app"> + main.js
    └── src/
        ├── main.js      point d'entrée : createApp(App).mount('#app')
        ├── App.vue      toute l'application (le code de la V4)
        └── assets/style.css
```

## Lancer : deux terminaux

Prérequis : PHP 8 avec l'extension `pdo_sqlite`, et **Node.js 20.19+ ou 22.12+** (`node -v`).

```bash
# Terminal 1 — l'API (la base data.sqlite est créée au premier lancement)
cd api
php -S localhost:8000 router.php

# Terminal 2 — le front
cd front
npm install        # une seule fois : télécharge Vue, Vite… dans node_modules/
npm run dev
```

Ouvrir http://localhost:5173

## À observer

1. **Le proxy.** Dans `App.vue`, l'URL de l'API est `/api/formations`, sans
   `http://localhost:8000`. Ouvrir l'onglet Réseau (F12) : la requête part vers
   `localhost:5173`, et c'est Vite qui la transmet à PHP (voir `vite.config.js`).
   Pour le navigateur, page et API ont la même origine : plus de problème CORS.
2. **Le rechargement à chaud.** Laisser la page ouverte, modifier le texte de
   l'en-tête dans `App.vue` et enregistrer : la page se met à jour sans
   rechargement, et le filtre choisi est conservé.
3. **Le code source de la page** (Ctrl+U) : un `<div id="app">` vide. Comme en V4,
   c'est le navigateur qui construit l'interface.
4. **Le build.** Lancer `npm run build`, puis ouvrir `dist/` : un `index.html` et
   des fichiers `assets/index-xxxx.js` et `.css` minifiés. Le nom contient une
   empreinte (hash) qui change à chaque modification : le navigateur ne garde
   jamais une ancienne version en cache.
5. `node_modules/` pèse plusieurs dizaines de Mo : on ne le copie jamais, on ne le
   versionne jamais (voir `.gitignore`). `npm install` le reconstruit.

## Exercices

1. Comparer `App.vue` avec `index.html` + `app.js` de la V4. Où sont passés
   `createApp`, `setup()` et le `return { ... }` ?
2. Supprimer le bloc `proxy` de `vite.config.js`, relancer `npm run dev` :
   que se passe-t-il ? Comment corriger sans remettre le proxy ?
3. Lancer `npm run build` puis `npm run preview` (port 4173). Les données
   s'affichent-elles ? Pourquoi ? (indice : le proxy ne concerne que `npm run dev`)

## Limite → V5.1

Tout est encore dans un seul fichier. La card est écrite dans la boucle de
`App.vue` : impossible de la réutiliser ailleurs (page détail, résultats de
recherche…).
