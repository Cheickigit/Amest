<script setup lang="ts">
import { usePage, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

type MediaItem = { type:'image'|'video', kind?:'upload'|'url', path?:string, url?:string, mime?:string, size?:number }
type Project   = { id:number; title:string; city?:string; category?:string; status?:string; cover_image?:string|null; media?:MediaItem[] }
const props = defineProps<{ items: { data: Project[], links?: any[] } | Project[] }>()

const Z = computed(() => (window as any).Ziggy)
const route = (name:string, params?:any, absolute=false) => ziggyRoute(name, params ?? {}, absolute, Z.value)

const q = ref('')
const list = computed<Project[]>(() => {
  const arr = Array.isArray(props.items) ? props.items : props.items.data
  return (arr ?? []).filter(p => !q.value || p.title.toLowerCase().includes(q.value.toLowerCase()))
})

function del(id:number) {
  if (!confirm('Supprimer ce projet ?')) return
  router.delete(route('admin.projects.destroy', id), { preserveScroll: true })
}
</script>

<template>
  <div class="max-w-6xl mx-auto p-6 text-white space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Réalisations</h1>
      <div class="flex items-center gap-2">
        <input v-model="q" placeholder="Rechercher…" class="h-10 w-60 rounded-lg bg-white/[.06] ring-1 ring-white/10 px-3 text-sm outline-none" />
        <Link :href="route('admin.projects.create')" class="px-3 py-2 rounded-lg bg-bk-gold text-bk-night font-bold">Nouveau</Link>
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="p in list" :key="p.id" class="rounded-xl overflow-hidden ring-1 ring-white/10 bg-white/[.04]">
        <div class="aspect-video bg-white/5 grid place-items-center overflow-hidden">
          <img v-if="p.cover_image" :src="`/storage/${p.cover_image}`" class="w-full h-full object-cover" :alt="p.title" />
          <div v-else class="text-white/40 text-sm">Aucune couverture</div>
        </div>
        <div class="p-3">
          <div class="flex items-start gap-2">
            <div class="flex-1 min-w-0">
              <div class="font-semibold truncate" :title="p.title">{{ p.title }}</div>
              <div class="text-xs text-white/60 mt-0.5">
                {{ p.category || '—' }} · {{ p.city || '—' }} · <span class="uppercase">{{ p.status }}</span>
              </div>
            </div>
            <div class="flex gap-1">
              <Link :href="route('admin.projects.edit', p.id)" class="px-2 py-1 rounded bg-white/10 text-xs hover:bg-white/15">Éditer</Link>
              <button type="button"
                      @click="del(p.id)"
                      class="px-2 py-1 rounded bg-red-500/15 text-red-200 text-xs hover:bg-red-500/25">
                Suppr.
              </button>
            </div>
          </div>

          <div class="mt-3 flex gap-1 overflow-x-auto">
            <template v-for="(m, i) in (p.media || []).slice(0, 6)" :key="i">
              <img v-if="m.type==='image' && m.path" :src="`/storage/${m.path}`" class="h-10 w-14 object-cover rounded ring-1 ring-white/10" />
              <div v-else-if="m.type==='video'" class="h-10 w-14 grid place-items-center rounded ring-1 ring-white/10 text-[10px] text-white/70">
                🎬
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
