<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { computed, ref, onMounted } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

defineOptions({ layout: AdminLayout })

type Item = {
  id:number; name:string; email?:string|null; phone?:string|null; city?:string|null;
  status:string; files:number; created_at:string; read_at?:string|null
}
const props = defineProps<{ items:{ data:Item[], links:any[] }, stats:{ total:number, new:number } }>()

const list = computed(()=> props.items.data ?? [])
const q = ref('')

/* init q depuis l’URL */
onMounted(()=> {
  try { const s = new URLSearchParams(location.search); q.value = s.get('q') ?? '' } catch {}
})

const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(globalThis as any).Ziggy)
const fmt = (d:string)=> new Date(d).toLocaleString('fr-FR', { dateStyle:'medium', timeStyle:'short' })
</script>

<template>
  <div class="space-y-6">
    <!-- HEADER -->
    <div class="flex flex-wrap items-end gap-4 justify-between">
      <div>
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Devis & leads</h1>
        <div class="text-white/70 text-sm">Sources : formulaire public « Devis express »</div>
      </div>
      <div class="flex gap-3">
        <div class="rounded-xl bg-white/[.06] ring-1 ring-white/12 px-4 py-2 text-center">
          <div class="text-xs text-white/60">Total</div>
          <div class="text-xl font-extrabold">{{ props.stats.total }}</div>
        </div>
        <div class="rounded-xl bg-bk-gold/15 ring-1 ring-bk-gold/40 px-4 py-2 text-center">
          <div class="text-xs text-white/60">Nouveaux</div>
          <div class="text-xl font-extrabold text-bk-gold">{{ props.stats.new }}</div>
        </div>
      </div>
    </div>

    <!-- TOOLBAR -->
    <div class="sticky top-14 z-10">
      <div class="rounded-xl bg-white/[.06] ring-1 ring-white/12 backdrop-blur px-3 py-3 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
        <form method="get" class="flex-1">
          <div class="relative">
            <input v-model="q" name="q" placeholder="Rechercher nom / email / ville…"
                   class="w-full h-11 rounded-lg bg-transparent ring-1 ring-white/15 focus:ring-bk-gold/60 outline-none pl-10 pr-3"/>
            <span class="absolute left-3 top-1/2 -translate-y-1/2 opacity-70">
              <svg viewBox="0 0 24 24" width="18" height="18"><path d="M10 2a8 8 0 1 0 5.29 14.29l4.21 4.21l1.41-1.41l-4.21-4.21A8 8 0 0 0 10 2z" fill="currentColor"/></svg>
            </span>
          </div>
        </form>
        <div class="flex gap-2">
          <Link :href="route('admin.quotes.index', { q })" class="inline-flex items-center h-11 px-4 rounded-lg border border-white/15 hover:border-bk-gold/60">Filtrer</Link>
          <Link :href="route('admin.quotes.index')" class="inline-flex items-center h-11 px-4 rounded-lg border border-white/15 hover:border-bk-gold/60">Réinitialiser</Link>
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto rounded-xl border border-white/10 bg-white/[.04]">
      <table class="min-w-full text-[13.5px]">
        <thead class="bg-white/[.06] text-white/70">
          <tr>
            <th class="px-3 py-2 text-left w-14">Statut</th>
            <th class="px-3 py-2 text-left min-w-[200px]">Contact</th>
            <th class="px-3 py-2 text-left min-w-[160px]">Coordonnées</th>
            <th class="px-3 py-2 text-left min-w-[120px]">Ville</th>
            <th class="px-3 py-2 text-left w-24">Pièces</th>
            <th class="px-3 py-2 text-left min-w-[180px]">Reçu</th>
            <th class="px-3 py-2 w-28"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="it in list" :key="it.id"
              class="border-t border-white/10 hover:bg-white/[.04] transition">
            <td class="px-3 py-2">
              <span v-if="it.status==='new'" class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 bg-bk-gold/15 text-bk-gold ring-1 ring-bk-gold/30 font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-bk-gold"></span>Nouveau
              </span>
              <span v-else class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 bg-white/10 text-white/80 ring-1 ring-white/15">Lu</span>
            </td>
            <td class="px-3 py-2">
              <div class="font-semibold">{{ it.name }}</div>
              <div class="text-white/55">{{ it.id }}</div>
            </td>
            <td class="px-3 py-2 text-white/85">
              <div v-if="it.email" class="truncate"><span class="opacity-70">✉︎</span> {{ it.email }}</div>
              <div v-if="it.phone" class="truncate text-white/65"><span class="opacity-70">☎︎</span> {{ it.phone }}</div>
            </td>
            <td class="px-3 py-2">{{ it.city ?? '—' }}</td>
            <td class="px-3 py-2">
              <span class="inline-flex items-center justify-center min-w-[2ch] rounded-md bg-white/10 ring-1 ring-white/15 px-2 py-0.5">{{ it.files }}</span>
            </td>
            <td class="px-3 py-2 whitespace-nowrap">{{ fmt(it.created_at) }}</td>
            <td class="px-3 py-2">
              <Link :href="route('admin.quotes.show', it.id)"
                    class="inline-flex items-center gap-1 px-3 h-9 rounded-lg border border-white/15 hover:border-bk-gold/60">
                Ouvrir
              </Link>
            </td>
          </tr>

          <tr v-if="!list.length">
            <td colspan="7" class="px-3 py-10 text-center text-white/70">Aucune demande pour le moment.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <nav v-if="(props.items.links || []).length" class="flex flex-wrap gap-2 justify-center">
      <Link v-for="(l,i) in props.items.links" :key="i" :href="l.url || '#'"
            :class="[
              'px-3 py-1.5 rounded-lg border transition',
              l.active ? 'bg-bk-gold text-bk-night border-transparent'
                       : 'border-white/15 hover:border-bk-gold/60'
            ]" v-html="l.label" preserve-state />
    </nav>
  </div>
</template>
