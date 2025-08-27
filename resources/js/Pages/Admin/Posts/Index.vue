<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { route as ziggyRoute } from 'ziggy-js'
import { ref, computed } from 'vue'
defineOptions({ layout: AdminLayout })

const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(window as any).Ziggy)

type Post = { id:number; title:string; status:'brouillon'|'publié'; published_at?:string|null }
const props = defineProps<{ items: { data: Post[], links?: any[] } | Post[] }>()

/* filtres + tri */
const q = ref('')
const status = ref<'all'|'publié'|'brouillon'>('all')
const sort   = ref<'recent'|'title'>('recent')

const raw = computed<Post[]>(() => Array.isArray(props.items) ? props.items : props.items.data ?? [])
const list = computed<Post[]>(() => {
  let arr = raw.value
    .filter(p => p.title.toLowerCase().includes(q.value.toLowerCase()))
    .filter(p => status.value==='all' ? true : p.status===status.value)
  if (sort.value==='title') arr = [...arr].sort((a,b)=> a.title.localeCompare(b.title))
  return arr
})

function del(id:number){
  if(!confirm('Supprimer cet article ?')) return
  router.delete(route('admin.posts.destroy', id), { preserveScroll:true })
}
</script>

<template>
  <div class="max-w-7xl mx-auto text-bk-off space-y-5">
    <!-- Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-center gap-3">
      <h1 class="text-2xl font-extrabold">Actualités</h1>
      <div class="flex-1"></div>

      <div class="relative">
        <input v-model="q" placeholder="Rechercher…"
               class="h-10 w-[280px] rounded-lg bg-white/[.06] ring-1 ring-white/10 pl-9 pr-3 text-sm outline-none text-bk-off placeholder:text-bk-off/55">
        <span class="absolute left-2 top-1/2 -translate-y-1/2 opacity-70">🔎</span>
      </div>

      <div class="inline-flex rounded-lg bg-white/5 ring-1 ring-white/10 p-1 h-10">
        <button type="button" @click="status='all'"
                :class="['px-2.5 text-xs rounded-md', status==='all' ? 'bg-bk-gold text-bk-night font-semibold' : 'text-bk-off/85 hover:text-bk-off']">
          Tous
        </button>
        <button type="button" @click="status='publié'"
                :class="['px-2.5 text-xs rounded-md', status==='publié' ? 'bg-bk-gold text-bk-night font-semibold' : 'text-bk-off/85 hover:text-bk-off']">
          Publiés
        </button>
        <button type="button" @click="status='brouillon'"
                :class="['px-2.5 text-xs rounded-md', status==='brouillon' ? 'bg-bk-gold text-bk-night font-semibold' : 'text-bk-off/85 hover:text-bk-off']">
          Brouillons
        </button>
      </div>

      <div class="relative">
        <select v-model="sort"
                class="h-10 rounded-lg bg-white/[.06] ring-1 ring-white/10 px-3 pr-8 text-sm outline-none text-bk-off [color-scheme:dark]">
          <option value="recent">Plus récents</option>
          <option value="title">Titre (A→Z)</option>
        </select>
        <span class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 opacity-70">▾</span>
      </div>

      <Link :href="route('admin.posts.create')"
            class="h-10 inline-flex items-center gap-2 rounded-lg px-3 bg-bk-gold text-bk-night font-semibold shadow-[0_16px_40px_-16px_rgba(220,193,118,.45)] hover:-translate-y-0.5 transition">
        Nouvel article
      </Link>
    </div>

    <!-- Empty -->
    <div v-if="!list.length" class="rounded-2xl ring-1 ring-white/10 bg-white/[.04] p-10 text-center text-bk-off/70">
      Aucun article.
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto rounded-xl ring-1 ring-white/10">
      <table class="min-w-full text-sm">
        <thead class="bg-white/5 text-bk-off/70">
          <tr>
            <th class="px-4 py-2 text-left">Titre</th>
            <th class="px-4 py-2 text-center">Statut</th>
            <th class="px-4 py-2 text-center">Publication</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="text-bk-off">
          <tr v-for="p in list" :key="p.id" class="border-t border-white/10">
            <td class="px-4 py-2">{{ p.title }}</td>
            <td class="px-4 py-2 text-center">
              <span :class="['px-2 h-5 rounded text-[11px] uppercase inline-flex items-center justify-center',
                             p.status==='publié' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-yellow-500/15 text-yellow-300']">
                {{ p.status }}
              </span>
            </td>
            <td class="px-4 py-2 text-center">{{ p.published_at ?? '—' }}</td>
            <td class="px-4 py-2 text-right">
              <Link :href="route('admin.posts.edit', p.id)"
                    class="px-2 py-1 rounded-md bg-white/10 hover:bg-white/15 text-bk-off">Éditer</Link>
              <button type="button" @click="del(p.id)"
                      class="ml-2 px-2 py-1 rounded-md bg-red-500/15 text-red-200 hover:bg-red-500/25">
                Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
/* teinte sombre native pour les contrôles */
:global(select), :global(option) { color:#e3e6ec; }
:global(select option){ background:#0f141a; }
:global([color-scheme="dark"]){ color-scheme: dark; }
:global(input::placeholder){ color:#e3e6ec99; }
</style>
