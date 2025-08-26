<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

const user = usePage().props?.auth?.user
const can = (perm) => user?.permissions?.includes(perm)
const has = (role) => user?.roles?.includes(role)
</script>

<template>
  <Head title="Admin — Tableau de bord" />

  <!-- Bandeau d’info si accès limité -->
  <div v-if="!has('admin')" class="mb-6 rounded-xl border border-white/10 bg-white/5 p-4 text-sm">
    Accès <strong>Chef de projet</strong> : certaines fonctions (Utilisateurs, CMS, Paramètres) sont masquées.
  </div>

  <div class="grid gap-6 md:grid-cols-3">
    <div v-if="can('projects.view')" class="rounded-2xl border border-white/10 p-6 bg-white/5">
      <div class="text-bk-gold font-display text-3xl">120</div>
      <div class="text-sm text-bk-off/70">Projets en portefeuille</div>
    </div>

    <div v-if="can('quotes.view')" class="rounded-2xl border border-white/10 p-6 bg-white/5">
      <div class="text-bk-gold font-display text-3xl">34</div>
      <div class="text-sm text-bk-off/70">Leads actifs</div>
    </div>

    <div v-if="can('rfps.view')" class="rounded-2xl border border-white/10 p-6 bg-white/5">
      <div class="text-bk-gold font-display text-3xl">7</div>
      <div class="text-sm text-bk-off/70">Appels d’offres ouverts</div>
    </div>

    <!-- Widgets réservés ADMIN -->
    <div v-if="can('users.manage')" class="rounded-2xl border border-white/10 p-6 bg-white/5">
      <div class="text-bk-gold font-display text-3xl">5</div>
      <div class="text-sm text-bk-off/70">Nouveaux utilisateurs</div>
    </div>

    <div v-if="can('cms.manage')" class="rounded-2xl border border-white/10 p-6 bg-white/5">
      <div class="text-bk-gold font-display text-3xl">12</div>
      <div class="text-sm text-bk-off/70">Articles en brouillon</div>
    </div>

    <div class="md:col-span-3 rounded-2xl border border-white/10 p-6 bg-white/5">
      <h2 class="font-semibold mb-2">Activité récente</h2>
      <p class="text-sm text-bk-off/70">Flux à venir…</p>
    </div>
  </div>
</template>
