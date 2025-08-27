<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

type Item = {
  id:number; name:string; email?:string|null; phone?:string|null; city?:string|null;
  status:string; files:number; created_at:string; read_at?:string|null
}
const props = defineProps<{ items:{ data:Item[], links:any }, stats:{ total:number, new:number } }>()
const items = computed(()=> props.items.data ?? [])
const q = ref('')

const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(globalThis as any).Ziggy)
</script>

<template>
  <div class="p-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-extrabold">Devis & leads</h1>
      <div class="text-sm text-white/70">Total: <b>{{ props.stats.total }}</b> · Nouveaux: <b class="text-bk-gold">{{ props.stats.new }}</b></div>
    </div>

    <div class="mt-4 flex gap-2">
      <form class="flex-1" method="get">
        <input v-model="q" name="q" placeholder="Rechercher nom / email / ville…" class="w-full rounded-lg px-3 py-2 bg-white/5 border border-white/15 outline-none" />
      </form>
      <Link :href="route('admin.quotes.index', { q })" class="inline-flex items-center px-3 rounded-lg border border-white/15 hover:border-bk-gold/60">Filtrer</Link>
    </div>

    <div class="mt-4 overflow-x-auto rounded-xl border border-white/10">
      <table class="min-w-full text-sm">
        <thead class="bg-white/5 text-white/70">
          <tr>
            <th class="px-3 py-2 text-left">Status</th>
            <th class="px-3 py-2 text-left">Nom</th>
            <th class="px-3 py-2 text-left">Coordonnées</th>
            <th class="px-3 py-2 text-left">Ville</th>
            <th class="px-3 py-2 text-left">Pièces</th>
            <th class="px-3 py-2 text-left">Reçu</th>
            <th class="px-3 py-2"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="it in items" :key="it.id" class="border-t border-white/10">
            <td class="px-3 py-2">
              <span :class="['inline-flex w-2.5 h-2.5 rounded-full', it.status==='new' ? 'bg-bk-gold' : 'bg-white/40']"></span>
            </td>
            <td class="px-3 py-2 font-semibold">{{ it.name }}</td>
            <td class="px-3 py-2 text-white/80">
              <div v-if="it.email">{{ it.email }}</div>
              <div v-if="it.phone" class="text-white/60">{{ it.phone }}</div>
            </td>
            <td class="px-3 py-2">{{ it.city ?? '—' }}</td>
            <td class="px-3 py-2">{{ it.files }}</td>
            <td class="px-3 py-2">{{ new Date(it.created_at).toLocaleString('fr-FR') }}</td>
            <td class="px-3 py-2">
              <Link :href="route('admin.quotes.show', it.id)" class="inline-flex items-center px-2 py-1 rounded-lg border border-white/15 hover:border-bk-gold/60">Ouvrir</Link>
            </td>
          </tr>
          <tr v-if="!items.length">
            <td colspan="7" class="px-3 py-6 text-center text-white/70">Aucune demande.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
