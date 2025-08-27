<script setup lang="ts">
import { useForm, Link, router } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { route as ziggyRoute } from 'ziggy-js'
defineOptions({ layout: AdminLayout })

/* ---- Helpers ---- */
const route = (name: string, params?: any, absolute = false) =>
  ziggyRoute(name, params ?? {}, absolute, (window as any).Ziggy)

/* ---- Icônes ---- */
const icons: Record<string,string> = {
  upload:'M5 17h14v2H5v-2zm7-12l5 5h-3v4h-4v-4H7l5-5z',
  image:'M21 19V5H3v14h18zM8.5 11A2.5 2.5 0 1 0 8.5 6a2.5 2.5 0 0 0 0 5zm10.5 6H5l4-5l3 4l2-2l5 3z',
  link:'M3.9 12a5 5 0 0 1 5-5h3v2h-3a3 3 0 1 0 0 6h3v2h-3a5 5 0 0 1-5-5Zm7-1h2v2h-2v-2Zm3.1-4h3a5 5 0 1 1 0 10h-3v-2h3a3 3 0 1 0 0-6h-3V7Z',
  trash:'M6 7h12v2H6zm2 3h8l-1 10H9L8 10zm3-5h2l1 2H10l1-2z',
  save:'M17 3H5a2 2 0 0 0-2 2v14h18V7l-4-4zM12 19a4 4 0 1 1 0-8a4 4 0 0 1 0 8z',
}

/* ---- Types ---- */
type MediaItem = { type:'image'|'video', kind?:'upload'|'url', path?:string, url?:string, mime?:string, size?:number }
type Project = {
  id?:number
  title:string; slug?:string; category?:string; city?:string; client?:string; year?:number|string|null
  status:'brouillon'|'publié'; excerpt?:string; body?:string; cover_image?:string|null; media?:MediaItem[]
}

const props = defineProps<{ item?: Project }>()
const isEdit = computed(() => Boolean(props.item?.id))

/* ---- Contraintes (alignées au backend) ---- */
const minYear = 1980
const maxYear = 2100

/* ---- Form ---- */
const form = useForm({
  title:    props.item?.title ?? '',
  slug:     props.item?.slug ?? '',
  category: props.item?.category ?? '',
  city:     props.item?.city ?? '',
  client:   props.item?.client ?? '',
  year:     props.item?.year ?? null,
  status:   (props.item?.status as 'brouillon'|'publié') ?? 'brouillon',
  excerpt:  props.item?.excerpt ?? '',
  body:     props.item?.body ?? '',

  cover: null as File | null,
  media_uploads: [] as File[],
  video_urls: [] as string[],
})

/* Auto-slug (respecte le slug si tu le modifies à la main) */
const slugTouched = ref(false)
function slugify(s: string){
  return s
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g,'-')
    .replace(/(^-|-$)/g,'')
    .slice(0, 200)
}
watch(() => form.title, (t) => {
  if (!slugTouched.value) form.slug = slugify(String(t || ''))
})
function onSlugInput(){ slugTouched.value = true }

/* Préremplir liens vidéo en édition */
onMounted(()=>{
  const urls = (props.item?.media ?? [])
    .filter(m => m.type==='video' && m.kind==='url' && m.url)
    .map(m => m.url!) as string[]
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

/* Uploads */
function pick(e:Event){ const i = e.target as HTMLInputElement; form.media_uploads = i.files ? Array.from(i.files) : [] }
function allowDrop(e:DragEvent){ e.preventDefault() }
function onDrop(e:DragEvent){ e.preventDefault(); const f = Array.from(e.dataTransfer?.files || []); if (f.length) form.media_uploads = [...form.media_uploads, ...f] }

/* Vidéos (URL) */
const tempVideo = ref('')
function addVideoUrl(){
  const u = tempVideo.value.trim()
  if(!u) return
  if(!/^https?:\/\//i.test(u)){ alert('URL invalide'); return }
  form.video_urls.push(u); tempVideo.value=''
}
function removeVideoUrl(i:number){ form.video_urls.splice(i,1) }

/* Submit : POST partout + _method=PUT en édition + redirection fiable */
function submit() {
  const url = isEdit.value
    ? route('admin.projects.update', props.item!.id)
    : route('admin.projects.store')

  form.clearErrors()
  form.transform((data) => ({
    ...data,
    title: String(data.title ?? ''),
    slug: String(data.slug ?? ''),
    year: data.year ? Number(data.year) : null,
    video_urls: Array.isArray(data.video_urls) ? data.video_urls : [],
    ...(isEdit.value ? { _method: 'PUT' as const } : {}),
  }))

  const common = {
    forceFormData: true,
    preserveScroll: true,

    // Redirection côté client : plus robuste après upload
    onSuccess: () => {
      form.media_uploads = []
      form.cover = null
      router.visit(route('admin.projects.index'))
      // 👉 Si tu préfères rester sur l’édition après update :
      // if (isEdit.value) router.visit(route('admin.projects.edit', props.item!.id))
      // else router.visit(route('admin.projects.index'))
    },

    // Nettoie le _method ajouté par transform()
    onFinish: () => { form.transform((d)=>({ ...d, _method: undefined as any })) },
  } as const

  form.post(url, common)
}

/* Delete */
function destroyItem() {
  if (!props.item?.id) return
  if (!confirm('Supprimer définitivement cette réalisation ?')) return
  form.delete(route('admin.projects.destroy', props.item.id), {
    preserveScroll: true,
    onSuccess: () => router.visit(route('admin.projects.index')),
  })
}
</script>

<template>
  <div class="max-w-7xl mx-auto text-white">
    <!-- ENTÊTE -->
    <div class="sticky top-0 z-20 -mx-2 px-2 bg-bk-night/85 backdrop-blur border-b border-white/10">
      <div class="h-14 flex items-center gap-2">
        <div class="flex-1">
          <div class="text-[11px] text-white/50 leading-none">BKO Construction / Back-office</div>
          <div class="font-semibold">
            {{ isEdit ? 'Modifier une réalisation' : 'Nouvelle réalisation' }}
          </div>
        </div>

        <Link :href="route('admin.projects.index')"
              class="px-3 h-9 inline-flex items-center rounded-lg bg-white/10 hover:bg-white/15">
          Retour
        </Link>

        <button v-if="isEdit" type="button" @click="destroyItem"
                class="px-3 h-9 inline-flex items-center gap-2 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-200">
          <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.trash" fill="currentColor"/></svg>
          Supprimer
        </button>

        <button type="button" :disabled="form.processing" @click="submit"
                class="px-4 h-9 inline-flex items-center gap-2 rounded-lg bg-bk-gold text-bk-night font-bold hover:brightness-95 disabled:opacity-60">
          <svg viewBox="0 0 24 24" width="16" height="16">
            <defs><linearGradient id="g-sav" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs>
            <path fill="url(#g-sav)" :d="icons.save"/>
          </svg>
          {{ isEdit ? 'Mettre à jour' : 'Enregistrer' }}
        </button>
      </div>

      <!-- Barre de progression d’upload -->
      <div v-if="form.progress" class="h-1 mb-2 rounded bg-white/10 overflow-hidden">
        <div class="h-1 bg-bk-gold" :style="{ width: `${form.progress.percentage}%` }"></div>
      </div>
    </div>

    <!-- ALERTE ERREURS -->
    <div v-if="Object.keys(form.errors).length"
         class="mt-4 mb-4 rounded-xl bg-red-500/10 ring-1 ring-red-500/30 text-red-200 px-4 py-3">
      Merci de corriger les champs indiqués ci-dessous.
    </div>

    <!-- 2 COLONNES -->
    <div class="grid lg:grid-cols-[1fr_360px] gap-6">
      <!-- FORM -->
      <form @submit.prevent="submit" class="grid gap-5">
        <div class="grid md:grid-cols-2 gap-4">
          <label class="grid gap-1">
            <span class="text-white/70 text-sm">Titre *</span>
            <input v-model="form.title" required
                   class="bg-white/5 ring-1 ring-white/10 focus:ring-bk-gold/50 rounded-lg px-3 py-2 outline-none" />
            <span v-if="form.errors.title" class="text-xs text-red-300">{{ form.errors.title }}</span>
          </label>

          <label class="grid gap-1">
            <span class="text-white/70 text-sm">Slug</span>
            <input v-model="form.slug" @input="onSlugInput"
                   class="bg-white/5 ring-1 ring-white/10 focus:ring-bk-gold/50 rounded-lg px-3 py-2 outline-none" />
            <span v-if="form.errors.slug" class="text-xs text-red-300">{{ form.errors.slug }}</span>
          </label>
        </div>

        <div class="grid md:grid-cols-4 gap-4">
          <label class="grid gap-1">
            <span class="text-white/70 text-sm">Catégorie</span>
            <input v-model="form.category"
                   class="bg-white/5 ring-1 ring-white/10 focus:ring-bk-gold/50 rounded-lg px-3 py-2 outline-none" />
          </label>

          <label class="grid gap-1">
            <span class="text-white/70 text-sm">Ville</span>
            <input v-model="form.city"
                   class="bg-white/5 ring-1 ring-white/10 focus:ring-bk-gold/50 rounded-lg px-3 py-2 outline-none" />
          </label>

          <label class="grid gap-1">
            <span class="text-white/70 text-sm">Client</span>
            <input v-model="form.client"
                   class="bg-white/5 ring-1 ring-white/10 focus:ring-bk-gold/50 rounded-lg px-3 py-2 outline-none" />
          </label>

          <label class="grid gap-1">
            <span class="text-white/70 text-sm">Année <span class="text-white/40">(1980–2100)</span></span>
            <input v-model="form.year" type="number" :min="minYear" :max="maxYear"
                   class="bg-white/5 ring-1 ring-white/10 focus:ring-bk-gold/50 rounded-lg px-3 py-2 outline-none" />
            <span v-if="form.errors.year" class="text-xs text-red-300">{{ form.errors.year }}</span>
          </label>
        </div>

        <label class="grid gap-1">
          <span class="text-white/70 text-sm">Résumé</span>
          <textarea v-model="form.excerpt" rows="2"
                    class="bg-white/5 ring-1 ring-white/10 focus:ring-bk-gold/50 rounded-lg px-3 py-2 outline-none"></textarea>
        </label>

        <label class="grid gap-1">
          <span class="text-white/70 text-sm">Contenu</span>
          <textarea v-model="form.body" rows="6"
                    class="bg-white/5 ring-1 ring-white/10 focus:ring-bk-gold/50 rounded-lg px-3 py-2 outline-none"></textarea>
        </label>

        <!-- Cover + uploads -->
        <div class="grid md:grid-cols-2 gap-4">
          <label class="grid gap-1">
            <span class="text-white/70 text-sm">Image de couverture</span>
            <input type="file" accept="image/*" @change="onCoverChange" />
            <div v-if="props.item?.cover_image && !coverPreview" class="text-xs text-white/60">
              Actuelle : <span class="underline">/storage/{{ props.item.cover_image }}</span>
            </div>
            <span v-if="form.errors.cover" class="text-xs text-red-300">{{ form.errors.cover }}</span>
          </label>

          <div class="grid gap-2">
            <span class="text-white/70 text-sm">Médias (images / vidéos)</span>
            <div class="rounded-xl ring-1 ring-white/10 bg-white/[.04] p-4">
              <div class="grid gap-3">
                <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                  <svg viewBox="0 0 24 24" width="16" height="16">
                    <defs><linearGradient id="g-up" x1="0" y1="0" x2="1" y2="1">
                      <stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs>
                    <path fill="url(#g-up)" :d="icons.upload"/>
                  </svg>
                  <span>Téléverser des fichiers</span>
                  <input type="file" multiple accept="image/*,video/*" class="hidden" @change="pick" />
                </label>

                <div class="rounded-lg border border-dashed border-white/15 bg-white/[.03] p-6 text-center"
                     @dragover="allowDrop" @drop="onDrop">
                  <div class="text-white/70 text-sm">Glissez-déposez vos fichiers ici</div>
                  <div class="text-xs text-white/50 mt-1">8 Mo max / fichier</div>
                </div>

                <div v-if="form.media_uploads.length" class="text-xs text-white/70">
                  {{ form.media_uploads.length }} fichier(s) prêt(s) à l’envoi
                </div>
                <span v-if="form.errors['media_uploads.0']" class="text-xs text-red-300">
                  {{ form.errors['media_uploads.0'] }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Liens vidéo -->
        <div class="grid gap-2">
          <span class="text-white/70 text-sm">Liens vidéo (YouTube, Vimeo…)</span>
          <div class="flex gap-2">
            <input v-model="tempVideo" placeholder="https://…"
                   class="flex-1 bg-white/5 ring-1 ring-white/10 focus:ring-bk-gold/50 rounded-lg px-3 py-2 outline-none"
                   @keyup.enter.prevent="addVideoUrl" />
            <button type="button"
                    class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/15 inline-flex items-center gap-2"
                    @click="addVideoUrl">
              <svg viewBox="0 0 24 24" width="14" height="14">
                <defs><linearGradient id="g-li" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs>
                <path fill="url(#g-li)" :d="icons.link"/>
              </svg>
              Ajouter
            </button>
          </div>
          <ul v-if="form.video_urls.length" class="space-y-1">
            <li v-for="(u,i) in form.video_urls" :key="u" class="flex items-center gap-2 text-sm">
              <a :href="u" target="_blank" class="flex-1 truncate hover:text-bk-gold">{{ u }}</a>
              <button type="button"
                      class="text-red-300/90 hover:text-red-200 text-xs inline-flex items-center gap-1"
                      @click="removeVideoUrl(i)">
                <svg viewBox="0 0 24 24" width="12" height="12"><path :d="icons.trash" fill="currentColor"/></svg>
                Suppr.
              </button>
            </li>
          </ul>
        </div>

        <!-- Statut -->
        <div class="inline-flex rounded-lg bg-white/5 ring-1 ring-white/10 p-1 h-10">
          <button type="button" @click="form.status='brouillon'"
                  :class="['px-2.5 text-xs rounded-md', form.status==='brouillon' ? 'bg-bk-gold text-bk-night font-semibold' : 'text-white/80 hover:text-white']">
            Brouillon
          </button>
          <button type="button" @click="form.status='publié'"
                  :class="['px-2.5 text-xs rounded-md', form.status==='publié' ? 'bg-bk-gold text-bk-night font-semibold' : 'text-white/80 hover:text-white']">
            Publié
          </button>
        </div>

        <!-- Bouton bas de page -->
        <div class="pt-1">
          <button :disabled="form.processing"
                  class="px-4 h-10 inline-flex items-center gap-2 rounded-lg bg-bk-gold text-bk-night font-bold hover:brightness-95 disabled:opacity-60">
            <svg viewBox="0 0 24 24" width="16" height="16">
              <defs><linearGradient id="g-sav2" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#F5E7B7"/><stop offset="100%" stop-color="#DCC176"/></linearGradient></defs>
              <path fill="url(#g-sav2)" :d="icons.save"/>
            </svg>
            {{ isEdit ? 'Mettre à jour' : 'Enregistrer' }}
          </button>
        </div>
      </form>

      <!-- APERÇU -->
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
