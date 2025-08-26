<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { route as ziggyRoute } from 'ziggy-js'
defineOptions({ layout: AdminLayout })

type MediaItem = { type:'image'|'video', kind?:'upload'|'url', path?:string, url?:string, mime?:string, size?:number }
type Project = {
  id?:number
  title:string; slug?:string; category?:string; city?:string; client?:string; year?:number|string
  status:'brouillon'|'publié'; excerpt?:string; body?:string; cover_image?:string|null; media?:MediaItem[]
}

const props = defineProps<{ item?: Project }>()
const Z = computed(() => (window as any).Ziggy)
const route = (name:string, params?:any, absolute=false) => ziggyRoute(name, params ?? {}, absolute, Z.value)

/* ---- Form ---- */
const form = useForm({
  title: props.item?.title ?? '',
  slug: props.item?.slug ?? '',
  category: props.item?.category ?? '',
  city: props.item?.city ?? '',
  client: props.item?.client ?? '',
  year: props.item?.year ?? '',
  status: (props.item?.status as 'brouillon'|'publié') ?? 'brouillon',
  excerpt: props.item?.excerpt ?? '',
  body: props.item?.body ?? '',

  cover: null as File | null,
  media_uploads: [] as File[],
  video_urls: [] as string[],
})

/* pré-remplissage des URLs vidéo si édition */
onMounted(()=>{
  const urls = (props.item?.media ?? [])
    .filter(m => m.type==='video' && m.kind==='url' && !!m.url)
    .map(m => m.url!) as string[]
  if (urls.length) form.video_urls = urls
})

/* ---- Cover preview ---- */
const coverPreview = ref<string | null>(null)
function onCoverChange(e: Event){
  const input = e.target as HTMLInputElement
  form.cover = input.files?.[0] ?? null
  if (coverPreview.value) URL.revokeObjectURL(coverPreview.value)
  coverPreview.value = form.cover ? URL.createObjectURL(form.cover) : null
}
onBeforeUnmount(()=>{ if (coverPreview.value) URL.revokeObjectURL(coverPreview.value) })

/* ---- Media uploads ---- */
function onMediaChange(e: Event){
  const input = e.target as HTMLInputElement
  form.media_uploads = input.files ? Array.from(input.files) : []
}

/* ---- Video URLs ---- */
const tempVideo = ref('')
function addVideoUrl(){
  const u = tempVideo.value.trim()
  if (!u) return
  if (!/^https?:\/\//i.test(u)) { alert('URL invalide'); return }
  form.video_urls.push(u); tempVideo.value = ''
}
function removeVideoUrl(i:number){ form.video_urls.splice(i,1) }

/* ---- Submit ---- */
function submit(){
  const url = props.item ? route('admin.projects.update', props.item.id) : route('admin.projects.store')
  form.post(url, {
    forceFormData: true,
    preserveScroll: true,
    onFinish: () => { form.media_uploads = []; form.cover = null }
  })
}
</script>

<template>
  <div class="max-w-6xl mx-auto text-white space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-extrabold">{{ props.item ? 'Modifier une réalisation' : 'Nouvelle réalisation' }}</h1>
      <div class="flex items-center gap-2">
        <Link :href="route('admin.projects.index')" class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/15">Retour</Link>
        <button type="button" @click="submit" class="px-4 py-2 rounded-lg bg-bk-gold text-bk-night font-bold hover:brightness-95">Enregistrer</button>
      </div>
    </div>

    <!-- Form -->
    <form @submit.prevent="submit" class="grid gap-5">
      <div class="grid md:grid-cols-2 gap-4">
        <label class="grid gap-1">
          <span class="text-white/70 text-sm">Titre *</span>
          <input v-model="form.title" required class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none" />
        </label>
        <label class="grid gap-1">
          <span class="text-white/70 text-sm">Slug</span>
          <input v-model="form.slug" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none" />
        </label>
      </div>

      <div class="grid md:grid-cols-4 gap-4">
        <label class="grid gap-1"><span class="text-white/70 text-sm">Catégorie</span><input v-model="form.category" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none" /></label>
        <label class="grid gap-1"><span class="text-white/70 text-sm">Ville</span><input v-model="form.city" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none" /></label>
        <label class="grid gap-1"><span class="text-white/70 text-sm">Client</span><input v-model="form.client" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none" /></label>
        <label class="grid gap-1"><span class="text-white/70 text-sm">Année</span><input v-model="form.year" type="number" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none" /></label>
      </div>

      <label class="grid gap-1"><span class="text-white/70 text-sm">Résumé</span><textarea v-model="form.excerpt" rows="2" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none"></textarea></label>
      <label class="grid gap-1"><span class="text-white/70 text-sm">Contenu</span><textarea v-model="form.body" rows="6" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none"></textarea></label>

      <!-- Cover + preview -->
      <div class="grid md:grid-cols-2 gap-4">
        <div class="grid gap-2">
          <span class="text-white/70 text-sm">Image de couverture</span>
          <label class="relative rounded-xl overflow-hidden ring-1 ring-white/10 bg-white/[.04] aspect-video grid place-items-center cursor-pointer">
            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" @change="onCoverChange" />
            <template v-if="coverPreview || props.item?.cover_image">
              <img :src="coverPreview || `/storage/${props.item?.cover_image}`" class="w-full h-full object-cover" alt="">
              <div class="absolute bottom-2 right-2 px-2 h-6 rounded bg-black/60 text-xs grid place-items-center">Changer</div>
            </template>
            <template v-else>
              <div class="text-white/50 text-sm">Clique pour ajouter une image</div>
            </template>
          </label>
        </div>

        <!-- Media uploads -->
        <div class="grid gap-2">
          <span class="text-white/70 text-sm">Médias (images/vidéos)</span>
          <label class="rounded-xl ring-1 ring-white/10 bg-white/[.04] p-4 grid gap-2">
            <input type="file" multiple accept="image/*,video/*" @change="onMediaChange" />
            <div class="text-xs text-white/60">{{ form.media_uploads.length }} fichier(s) sélectionné(s)</div>
          </label>

          <!-- Status chips -->
          <div class="inline-flex rounded-lg bg-white/5 ring-1 ring-white/10 p-1 h-10">
            <button type="button" @click="form.status='brouillon'" :class="['chip', form.status==='brouillon' ? 'chip--active' : '']">Brouillon</button>
            <button type="button" @click="form.status='publié'"    :class="['chip', form.status==='publié' ? 'chip--active' : '']">Publié</button>
          </div>
        </div>
      </div>

      <!-- URLs vidéo -->
      <div class="grid gap-2">
        <span class="text-white/70 text-sm">Liens vidéo (YouTube, Vimeo…)</span>
        <div class="flex gap-2">
          <input v-model="tempVideo" placeholder="https://…" class="flex-1 bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none" @keyup.enter.prevent="addVideoUrl" />
          <button type="button" class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/15" @click="addVideoUrl">Ajouter</button>
        </div>
        <ul v-if="form.video_urls.length" class="space-y-1">
          <li v-for="(u,i) in form.video_urls" :key="u" class="flex items-center gap-2 text-sm">
            <a :href="u" target="_blank" class="flex-1 truncate hover:text-bk-gold">{{ u }}</a>
            <button type="button" class="text-red-300/90 hover:text-red-200 text-xs" @click="removeVideoUrl(i)">Suppr.</button>
          </li>
        </ul>
      </div>

      <div class="pt-2">
        <button class="px-4 py-2 rounded-lg bg-bk-gold text-bk-night font-bold hover:brightness-95">Enregistrer</button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.chip { @apply text-xs rounded-md px-2.5 text-white/80 hover:text-white; }
.chip--active { @apply bg-bk-gold text-bk-night font-semibold; }
</style>
