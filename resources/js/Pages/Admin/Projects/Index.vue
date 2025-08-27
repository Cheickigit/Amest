<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

type MediaItem = { type:'image'|'video', kind?:'upload'|'url', path?:string, url?:string }
type Project   = { id:number; title:string; city?:string; category?:string; status?:'brouillon'|'publié'; cover_image?:string|null; media?:MediaItem[] }
const props = defineProps<{ items: { data: Project[], links?: any[] } | Project[] }>()

const Z = computed(() => (window as any).Ziggy)
const route = (name:string, params?:any, absolute=false) => ziggyRoute(name, params ?? {}, absolute, Z.value)

/* Icônes 3D (paths) — mêmes couleurs que le layout */
const icons: Record<string,string> = {
  search:'M10 2a8 8 0 1 0 5.29 14.29l4.21 4.21l1.41-1.41l-4.21-4.21A8 8 0 0 0 10 2z',
  video:'M8 5v14l11-7L8 5z',
  edit:'M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z',
  trash:'M6 7h12v2H6zm2 3h8l-1 10H9L8 10zm3-5h2l1 2H10l1-2z',
}

/* filtres */
const q      = ref('')
const status = ref<'all'|'publié'|'brouillon'>('all')
const sort   = ref<'recent'|'title'>('recent')

const raw  = computed<Project[]>(() => Array.isArray(props.items) ? props.items : props.items.data ?? [])
const list = computed<Project[]>(() => {
  let arr = raw.value
    .filter(p => !q.value || p.title.toLowerCase().includes(q.value.toLowerCase()))
    .filter(p => status.value==='all' ? true : p.status===status.value)
  if (sort.value==='title') arr = [...arr].sort((a,b)=> a.title.localeCompare(b.title))
  return arr
})

function del(id:number){
  if(!confirm('Supprimer cette réalisation ?')) return
  router.delete(route('admin.projects.destroy', id), { preserveScroll:true })
}
const hasVideo = (p:Project)=> (p.media||[]).some(m=>m.type==='video')
</script>

<template>
  <div class="max-w-7xl mx-auto text-white space-y-5">
    <!-- Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-center gap-3">
      <h1 class="text-2xl font-extrabold tracking-tight">Réalisations</h1>
      <div class="flex-1"></div>

      <div class="relative">
        <input v-model="q" placeholder="Rechercher un titre…" class="h-10 w-64 rounded-lg bg-white/[.06] ring-1 ring-white/10 pl-8 pr-3 text-sm outline-none focus:ring-bk-gold/50" />
        <span class="absolute left-2 top-1/2 -translate-y-1/2 opacity-70">
          <svg viewBox="0 0 24 24" width="16" height="16"><defs><linearGradient id="g-sea" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="50%" stop-color="#DCC176"/><stop offset="100%" stop-color="#8C7635"/></linearGradient></defs><path fill="url(#g-sea)" :d="icons.search"/></svg>
        </span>
      </div>

      <div class="inline-flex rounded-lg bg-white/5 ring-1 ring-white/10 p-1 h-10">
        <button type="button" @click="status='all'"      :class="['px-2.5 text-xs rounded-md', status==='all'?'bg-bk-gold text-bk-night font-semibold':'text-white/80 hover:text-white']">Tous</button>
        <button type="button" @click="status='publié'"    :class="['px-2.5 text-xs rounded-md', status==='publié'?'bg-bk-gold text-bk-night font-semibold':'text-white/80 hover:text-white']">Publiés</button>
        <button type="button" @click="status='brouillon'" :class="['px-2.5 text-xs rounded-md', status==='brouillon'?'bg-bk-gold text-bk-night font-semibold':'text-white/80 hover:text-white']">Brouillons</button>
      </div>

      <select v-model="sort" class="h-10 rounded-lg bg-white/[.06] ring-1 ring-white/10 px-3 text-sm outline-none">
        <option value="recent">Plus récents</option>
        <option value="title">Titre (A→Z)</option>
      </select>

      <Link :href="route('admin.projects.create')"
            class="h-10 inline-flex items-center gap-2 rounded-lg px-3 bg-bk-gold text-bk-night font-semibold shadow-[0_16px_40px_-16px_rgba(220,193,118,.45)] hover:-translate-y-0.5 transition">
        Nouveau
      </Link>
    </div>

    <!-- Empty -->
    <div v-if="!list.length" class="rounded-2xl ring-1 ring-white/10 bg-white/[.04] p-12 grid place-items-center text-center">
      <svg width="120" height="120" viewBox="0 0 24 24" class="opacity-60 mb-3">
        <defs><linearGradient id="grad-empty" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs>
        <path fill="url(#grad-empty)" d="M19 3H5a2 2 0 0 0-2 2v14l4-4h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Z"/>
      </svg>
      <div class="text-lg font-semibold">Aucune réalisation trouvée</div>
      <div class="text-sm text-white/60">Ajoutez votre première réalisation pour commencer.</div>
    </div>

    <!-- Grid -->
    <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <article v-for="p in list" :key="p.id" class="group rounded-2xl overflow-hidden ring-1 ring-white/10 bg-white/[.04] hover:ring-bk-gold/40 transition">
        <div class="relative aspect-video bg-white/5 overflow-hidden">
          <img v-if="p.cover_image" :src="`/storage/${p.cover_image}`" class="w-full h-full object-cover" :alt="p.title" />
          <div v-else class="h-full w-full grid place-items-center text-white/40 text-sm bg-gradient-to-br from-white/10 to-transparent">Aucune couverture</div>

          <div v-if="hasVideo(p)" class="absolute top-2 left-2 rounded-full bg-black/60 px-2 h-6 grid place-items-center text-xs">
            <svg viewBox="0 0 24 24" width="14" height="14"><defs><linearGradient id="g-vid" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs><path fill="url(#g-vid)" :d="icons.video"/></svg>
            &nbsp;Vidéo
          </div>
          <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-gradient-to-t from-black/50 via-black/20 to-transparent"></div>
        </div>

        <div class="p-3">
          <div class="flex items-start gap-2">
            <div class="flex-1 min-w-0">
              <div class="font-semibold truncate" :title="p.title">{{ p.title }}</div>
              <div class="text-xs text-white/60 mt-1 flex flex-wrap gap-1 items-center">
                <span class="px-2 h-5 rounded bg-white/10">{{ p.category || '—' }}</span>
                <span class="px-2 h-5 rounded bg-white/5">{{ p.city || '—' }}</span>
                <span :class="['px-2 h-5 rounded text-[11px] uppercase',
                               p.status==='publié' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-yellow-500/15 text-yellow-300']">
                  {{ p.status }}
                </span>
              </div>
            </div>
            <div class="flex gap-1">
              <Link :href="route('admin.projects.edit', p.id)" title="Éditer" class="px-2 py-1 rounded bg-white/10 text-xs hover:bg-white/15">
                <svg viewBox="0 0 24 24" width="14" height="14"><defs><linearGradient id="g-ed" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs><path fill="url(#g-ed)" :d="icons.edit"/></svg>
              </Link>
              <button type="button" @click="del(p.id)" title="Supprimer" class="px-2 py-1 rounded bg-red-500/15 text-red-200 text-xs hover:bg-red-500/25">
                <svg viewBox="0 0 24 24" width="14" height="14"><defs><linearGradient id="g-tr" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs><path fill="url(#g-tr)" :d="icons.trash"/></svg>
              </button>
            </div>
          </div>

          <div v-if="(p.media||[]).length" class="mt-3 flex gap-1 overflow-x-auto">
            <template v-for="(m, i) in (p.media || []).slice(0,6)" :key="i">
              <img v-if="m.type==='image' && m.path" :src="`/storage/${m.path}`" class="h-10 w-14 object-cover rounded ring-1 ring-white/10" />
              <div v-else-if="m.type==='video'" class="h-10 w-14 grid place-items-center rounded ring-1 ring-white/10 text-[10px] text-white/70">
                <svg viewBox="0 0 24 24" width="12" height="12"><defs><linearGradient id="g-vs" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs><path fill="url(#g-vs)" :d="icons.video"/></svg>
              </div>
            </template>
          </div>
        </div>
      </article>
    </div>
  </div>
</template>
