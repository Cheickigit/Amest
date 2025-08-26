<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
const props = defineProps<{ items: any }>()
</script>

<template>
  <div class="max-w-6xl mx-auto p-6 text-white">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Réalisations</h1>
      <Link :href="route('admin.projects.create')" class="px-3 py-2 rounded-lg bg-bk-gold text-bk-night font-bold">Nouveau</Link>
    </div>

    <div class="overflow-x-auto rounded-xl ring-1 ring-white/10">
      <table class="min-w-full text-sm">
        <thead class="bg-white/5 text-white/70">
          <tr>
            <th class="px-4 py-2 text-left">Titre</th>
            <th class="px-4 py-2">Ville</th>
            <th class="px-4 py-2">Catégorie</th>
            <th class="px-4 py-2">Statut</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in (props.items.data ?? props.items)" :key="p.id" class="border-t border-white/10">
            <td class="px-4 py-2">{{ p.title }}</td>
            <td class="px-4 py-2 text-center">{{ p.city }}</td>
            <td class="px-4 py-2 text-center">{{ p.category }}</td>
            <td class="px-4 py-2 text-center">{{ p.status }}</td>
            <td class="px-4 py-2 text-right">
              <Link :href="route('admin.projects.edit', p.id)" class="px-2 py-1 rounded-md bg-white/10">Éditer</Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
