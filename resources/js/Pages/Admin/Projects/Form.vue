<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { route as ziggyRoute } from 'ziggy-js'
defineOptions({ layout: AdminLayout })

/* Icônes 3D (paths) */
const icons: Record<string,string> = {
  upload:'M5 17h14v2H5v-2zm7-12l5 5h-3v4h-4v-4H7l5-5z',
  image:'M21 19V5H3v14h18zM8.5 11A2.5 2.5 0 1 0 8.5 6a2.5 2.5 0 0 0 0 5zm10.5 6H5l4-5l3 4l2-2l5 3z',
  link:'M3.9 12a5 5 0 0 1 5-5h3v2h-3a3 3 0 1 0 0 6h3v2h-3a5 5 0 0 1-5-5Zm7-1h2v2h-2v-2Zm3.1-4h3a5 5 0 1 1 0 10h-3v-2h3a3 3 0 1 0 0-6h-3V7Z',
  trash:'M6 7h12v2H6zm2 3h8l-1 10H9L8 10zm3-5h2l1 2H10l1-2z',
  save:'M17 3H5a2 2 0 0 0-2 2v14h18V7l-4-4zM12 19a4 4 0 1 1 0-8a4 4 0 0 1 0 8z',
}

type MediaItem = { type:'image'|'video', kind?:'upload'|'url', path?:string, url?:string, mime?:string, size?:number }
type Project = {
  id?:number
  title:string; slug?:string; category?:string; city?:string; client?:string; year?:number|string
  status:'brouillon'|'publié'; excerpt?:string; body?:string; cover_image?:string|null; media?:MediaItem[]
}

const props = defineProps<{ item?: Project }>()
const route = (name:string, params?:any, absolute=false)=> ziggyRoute(name, params ?? {}, absolute, (window as any).Ziggy)

/* --- Form --- */
const form = useForm({
  title:    props.item?.title ?? '',
  slug:     props.item?.slug ?? '',
  category: props.item?.category ?? '',
  city:     props.item?.city ?? '',
  client:   props.item?.client ?? '',
  year:     props.item?.year ?? '',
  status:   (props.item?.status as 'brouillon'|'publié') ?? 'brouillon',
  excerpt:  props.item?.excerpt ?? '',
  body:     props.item?.body ?? '',

  cover: null as File | null,
  media_uploads: [] as File[],
  video_urls: [] as string[],
})

/* Préremplir URLs vidéo en édition */
onMounted(()=>{
  const urls = (props.item?.media ?? [])
    .filter(m=> m.type==='video' && m.kind==='url' && !!m.url)
    .map(m=> m.url!) as string[]
  if (urls.length) form.video_urls = urls
})

/* Cover preview */
const coverPreview = ref<string|null>(null)
function onCoverChange(e:Event){
  const input = e.target as HTMLInputElement
  form.cover = input.files?.[0] ?? null
  if (coverPreview.value) URL.revokeObjectURL(coverPreview.value)
  coverPreview.value = form.cover ? URL.createObjectURL(form.cover) : null
}
onBeforeUnmount(()=>{ if (coverPreview.value) URL.revokeObjectURL(coverPreview.value) })

/* Drag & drop uploads */
function pick(e:Event){ const i = e.target as HTMLInputElement; form.media_uploads = i.files ? Array.from(i.files) : [] }
function allowDrop(e:DragEvent){ e.preventDefault() }
function onDrop(e:DragEvent){
  e.preventDefault()
  const files = Array.from(e.dataTransfer?.files || [])
  if (!files.length) return
  form.media_uploads = [...form.media_uploads, ...files]
}

/* URLs vidéo */
const tempVideo = ref('')
function addVideoUrl(){
  const u = tempVideo.value.trim()
  if(!u) return
  if(!/^https?:\/\//i.test(u)){ alert('URL invalide'); return }
  form.video_urls.push(u); tempVideo.value=''
}
function removeVideoUrl(i:number){ form.video_urls.splice(i,1) }

/* Submit */
function submit(){
  const url = props.item ? route('admin.projects.update', props.item.id) : route('admin.projects.store')
  form.post(url, {
    forceFormData: true,
    preserveScroll: true,
    onFinish: ()=> { form.media_uploads = []; form.cover = null }
  })
}
</script>

<template>
  <div class="max-w-7xl mx-auto text-white">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <div class="text-xs text-white/50">BKO Construction / Back-office</div>
        <h1 class="text-2xl font-extrabold">{{ props.item ? 'Modifier une réalisation' : 'Nouvelle réalisation' }}</h1>
      </div>
      <div class="flex items-center gap-2">
        <Link :href="route('admin.projects.index')" class="px-3 h-10 inline-flex items-center rounded-lg bg-white/10 hover:bg-white/15">Retour</Link>
        <button type="button" @click="submit" class="px-4 h-10 inline-flex items-center gap-2 rounded-lg bg-bk-gold text-bk-night font-bold hover:brightness-95">
          <svg viewBox="0 0 24 24" width="16" height="16"><defs><linearGradient id="g-sav" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs><path fill="url(#g-sav)" :d="icons.save"/></svg>
          Enregistrer
        </button>
      </div>
    </div>

    <!-- 2 colonnes : formulaire + aperçu sticky -->
    <div class="grid lg:grid-cols-[1fr_360px] gap-6">
      <!-- FORM -->
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

        <!-- Cover -->
        <div class="grid md:grid-cols-2 gap-4">
          <label class="grid gap-1">
            <span class="text-white/70 text-sm">Image de couverture</span>
            <input type="file" accept="image/*" @change="onCoverChange" />
            <div v-if="props.item?.cover_image && !coverPreview" class="text-xs text-white/60">
              Actuelle : <span class="underline">/storage/{{ props.item.cover_image }}</span>
            </div>
          </label>

          <!-- Media uploads : drag & drop -->
          <div class="grid gap-2">
            <span class="text-white/70 text-sm">Médias (images/vidéos)</span>
            <div class="rounded-xl ring-1 ring-white/10 bg-white/[.04] p-4">
              <div class="grid gap-3">
                <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                  <svg viewBox="0 0 24 24" width="16" height="16"><defs><linearGradient id="g-up" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs><path fill="url(#g-up)" :d="icons.upload"/></svg>
                  <span>Téléverser des fichiers</span>
                  <input type="file" multiple accept="image/*,video/*" class="hidden" @change="pick" />
                </label>
                <div class="rounded-lg border border-dashed border-white/15 bg-white/[.03] p-6 text-center"
                     @dragover="allowDrop" @drop="onDrop">
                  <div class="text-white/70 text-sm">Glissez-déposez vos fichiers ici</div>
                  <div class="text-xs text-white/50 mt-1">Images et vidéos (jusqu’à 8 Mo par fichier)</div>
                </div>
                <div v-if="form.media_uploads.length" class="text-xs text-white/70">
                  {{ form.media_uploads.length }} fichier(s) prêt(s) à l’envoi
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Liens vidéo -->
        <div class="grid gap-2">
          <span class="text-white/70 text-sm">Liens vidéo (YouTube, Vimeo…)</span>
          <div class="flex gap-2">
            <input v-model="tempVideo" placeholder="https://…" class="flex-1 bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none" @keyup.enter.prevent="addVideoUrl" />
            <button type="button" class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/15 inline-flex items-center gap-2" @click="addVideoUrl">
              <svg viewBox="0 0 24 24" width="14" height="14"><defs><linearGradient id="g-li" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs><path fill="url(#g-li)" :d="icons.link"/></svg>
              Ajouter
            </button>
          </div>
          <ul v-if="form.video_urls.length" class="space-y-1">
            <li v-for="(u,i) in form.video_urls" :key="u" class="flex items-center gap-2 text-sm">
              <a :href="u" target="_blank" class="flex-1 truncate hover:text-bk-gold">{{ u }}</a>
              <button type="button" class="text-red-300/90 hover:text-red-200 text-xs inline-flex items-center gap-1" @click="removeVideoUrl(i)">
                <svg viewBox="0 0 24 24" width="12" height="12"><defs><linearGradient id="g-tr2" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs><path fill="url(#g-tr2)" :d="icons.trash"/></svg>
                Suppr.
              </button>
            </li>
          </ul>
        </div>

        <!-- Statut chips -->
        <div class="inline-flex rounded-lg bg-white/5 ring-1 ring-white/10 p-1 h-10">
          <button type="button" @click="form.status='brouillon'" :class="['px-2.5 text-xs rounded-md', form.status==='brouillon'?'bg-bk-gold text-bk-night font-semibold':'text-white/80 hover:text-white']">Brouillon</button>
          <button type="button" @click="form.status='publié'"    :class="['px-2.5 text-xs rounded-md', form.status==='publié'?'bg-bk-gold text-bk-night font-semibold':'text-white/80 hover:text-white']">Publié</button>
        </div>

        <div class="pt-1">
          <button class="px-4 h-10 inline-flex items-center gap-2 rounded-lg bg-bk-gold text-bk-night font-bold hover:brightness-95">
            <svg viewBox="0 0 24 24" width="16" height="16"><defs><linearGradient id="g-sav2" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs><path fill="url(#g-sav2)" :d="icons.save"/></svg>
            Enregistrer
          </button>
        </div>
      </form>

      <!-- APERÇU STICKY -->
      <aside class="lg:sticky lg:top-16 h-max space-y-3">
        <div class="rounded-2xl overflow-hidden ring-1 ring-white/10 bg-white/[.04]">
          <div class="p-3 text-sm text-white/70">Aperçu — image de couverture</div>
          <div class="aspect-video bg-white/5 grid place-items-center overflow-hidden">
            <img v-if="coverPreview" :src="coverPreview" class="w-full h-full object-cover" alt="">
            <img v-else-if="props.item?.cover_image" :src="`/storage/${props.item.cover_image}`" class="w-full h-full object-cover" alt="">
            <div v-else class="text-white/40 text-sm">Aucune couverture</div>
          </div>
        </div>

        <div v-if="props.item?.media?.length" class="rounded-2xl ring-1 ring-white/10 bg-white/[.04] p-3">
          <div class="text-sm text-white/70 mb-2">Médias existants</div>
          <div class="flex gap-1 flex-wrap">
            <template v-for="(m,i) in props.item.media!.slice(0,12)" :key="i">
              <img v-if="m.type==='image' && m.path" :src="`/storage/${m.path}`" class="h-12 w-16 object-cover rounded ring-1 ring-white/10" />
              <div v-else-if="m.type==='video'" class="h-12 w-16 grid place-items-center rounded ring-1 ring-white/10 text-[10px] text-white/70 bg-white/5">
                Vidéo
              </div>
            </template>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>
