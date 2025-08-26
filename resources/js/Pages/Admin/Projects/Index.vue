<script setup lang="ts">
import { usePage, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

type MediaItem = { type:'image'|'video', kind?:'upload'|'url', path?:string, url?:string, mime?:string, size?:number }
type Project   = { id:number; title:string; city?:string; category?:string; status?:'brouillon'|'publié'; cover_image?:string|null; media?:MediaItem[] }
const props = defineProps<{ items: { data: Project[], links?: any[] } | Project[] }>()

const Z = computed(() => (window as any).Ziggy)
const route = (name:string, params?:any, absolute=false) => ziggyRoute(name, params ?? {}, absolute, Z.value)

const q = ref('')
const status = ref<'all'|'publié'|'brouillon'>('all')

const raw = computed<Project[]>(() => Array.isArray(props.items) ? props.items : props.items.data ?? [])
const list = computed<Project[]>(() =>
  raw.value
    .filter(p => !q.value || p.title.toLowerCase().includes(q.value.toLowerCase()))
    .filter(p => status.value === 'all' ? true : p.status === status.value)
)

function del(id:number) {
  if (!confirm('Supprimer cette réalisation ?')) return
  router.delete(route('admin.projects.destroy', id), { preserveScroll: true })
}

function firstVideo(p: Project){ return (p.media||[]).find(m=>m.type==='video') }
</script>

<template>
  <div class="max-w-7xl mx-auto text-white space-y-5">
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
      <h1 class="text-2xl font-extrabold tracking-tight">Réalisations</h1>

      <div class="flex-1"></div>

      <!-- search -->
      <div class="relative">
        <input v-model="q" placeholder="Rechercher un titre…"
               class="h-10 w-64 rounded-lg bg-white/[.06] ring-1 ring-white/10 pl-9 pr-3 text-sm outline-none focus:ring-bk-gold/50" />
        <svg viewBox="0 0 24 24" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-white/60">
          <path fill="currentColor" d="M10 2a8 8 0 105.29 14.29l4.21 4.21 1.41-1.41-4.21-4.21A8 8 0 0010 2m0 2a6 6 0 110 12A6 6 0 0110 4z"/>
        </svg>
      </div>

      <!-- status chips -->
      <div class="inline-flex rounded-lg bg-white/5 ring-1 ring-white/10 p-1 h-10">
        <button type="button" @click="status='all'"       :class="['chip', status==='all'?'chip--active':'']">Tous</button>
        <button type="button" @click="status='publié'"     :class="['chip', status==='publié'?'chip--active':'']">Publiés</button>
        <button type="button" @click="status='brouillon'"  :class="['chip', status==='brouillon'?'chip--active':'']">Brouillons</button>
      </div>

      <Link :href="route('admin.projects.create')"
            class="h-10 inline-flex items-center gap-2 rounded-lg px-3 bg-bk-gold text-bk-night font-semibold shadow-[0_16px_40px_-16px_rgba(220,193,118,.45)] hover:-translate-y-0.5 transition">
        ➕ Nouveau
      </Link>
    </div>

    <!-- Empty state -->
    <div v-if="!list.length" class="rounded-2xl ring-1 ring-white/10 bg-white/[.04] p-10 grid place-items-center text-center">
      <svg width="120" height="120" viewBox="0 0 24 24" class="opacity-60 mb-3"><path fill="currentColor" d="M19 3H5a2 2 0 0 0-2 2v14l4-4h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Z"/></svg>
      <div class="text-lg font-semibold">Aucune réalisation ne correspond.</div>
      <div class="text-sm text-white/60">Essaye un autre mot-clé ou change le filtre.</div>
    </div>

    <!-- Grid -->
    <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <article v-for="p in list" :key="p.id" class="group rounded-2xl overflow-hidden ring-1 ring-white/10 bg-white/[.04] hover:ring-bk-gold/40 transition">
        <div class="relative aspect-video bg-white/5 overflow-hidden">
          <img v-if="p.cover_image" :src="`/storage/${p.cover_image}`" class="w-full h-full object-cover" :alt="p.title" />
          <div v-else class="h-full w-full grid place-items-center text-white/40 text-sm bg-gradient-to-br from-white/10 to-transparent">Aucune couverture</div>

          <!-- video badge -->
          <div v-if="firstVideo(p)" class="absolute top-2 left-2 rounded-full bg-black/60 px-2 h-6 grid place-items-center text-xs">🎬 Vidéo</div>

          <!-- gradient overlay on hover -->
          <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition
                      bg-gradient-to-t from-black/50 via-black/20 to-transparent"></div>
        </div>

        <div class="p-3">
          <div class="flex items-start gap-2">
            <div class="flex-1 min-w-0">
              <div class="font-semibold truncate" :title="p.title">{{ p.title }}</div>
              <div class="text-xs text-white/60 mt-0.5 flex flex-wrap items-center gap-1">
                <span class="px-2 h-5 rounded bg-white/10">{{ p.category || '—' }}</span>
                <span class="px-2 h-5 rounded bg-white/5">{{ p.city || '—' }}</span>
                <span :class="['px-2 h-5 rounded text-[11px] uppercase',
                               p.status==='publié' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-yellow-500/15 text-yellow-300']">
                  {{ p.status }}
                </span>
              </div>
            </div>

            <div class="flex gap-1">
              <Link :href="route('admin.projects.edit', p.id)" title="Éditer"
                    class="icon-btn">✏️</Link>
              <button type="button" @click="del(p.id)" title="Supprimer"
                      class="icon-btn text-red-300/90 hover:text-red-200">🗑️</button>
            </div>
          </div>

          <div v-if="(p.media||[]).length" class="mt-3 flex gap-1 overflow-x-auto">
            <template v-for="(m, i) in (p.media || []).slice(0, 6)" :key="i">
              <img v-if="m.type==='image' && m.path" :src="`/storage/${m.path}`" class="h-10 w-14 object-cover rounded ring-1 ring-white/10" />
              <div v-else-if="m.type==='video'" class="h-10 w-14 grid place-items-center rounded ring-1 ring-white/10 text-[10px] text-white/70">🎬</div>
            </template>
          </div>
        </div>
      </article>
    </div>
  </div>
</template>

<style scoped>
.chip { @apply text-xs rounded-md px-2.5 text-white/80 hover:text-white; }
.chip--active { @apply bg-bk-gold text-bk-night font-semibold; }
.icon-btn { @apply px-2 py-1 rounded bg-white/10 text-xs hover:bg-white/15; }
</style>
