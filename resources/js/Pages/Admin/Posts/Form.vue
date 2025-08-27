<script setup lang="ts">
import { useForm, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch, onBeforeUnmount } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { route as ziggyRoute } from 'ziggy-js'
defineOptions({ layout: AdminLayout })

const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(window as any).Ziggy)

type Post = {
  id?: number
  title: string; slug?: string
  status: 'brouillon'|'publié'
  published_at?: string|null
  excerpt?: string; body?: string
  cover_image?: string|null
  tags?: string[]
}

const props = defineProps<{ item?: Post }>()
const isEdit = computed(()=> !!props.item?.id)

/* ---- Form ---- */
const form = useForm({
  title:        props.item?.title ?? '',
  slug:         props.item?.slug ?? '',
  status:       (props.item?.status as 'brouillon'|'publié') ?? 'brouillon',
  published_at: props.item?.published_at ?? '',
  excerpt:      props.item?.excerpt ?? '',
  body:         props.item?.body ?? '',
  cover:        null as File|null,
  tags:         props.item?.tags ?? [],
  tagInput:     '' as string,
})

/* Auto-slug (sans écraser si l’utilisateur tape le slug) */
const slugTouched = ref(false)
const slugify = (s:string)=>s.normalize('NFD').replace(/[\u0300-\u036f]/g,'')
  .toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'').slice(0,200)
watch(()=>form.title, t=>{ if(!slugTouched.value) form.slug = slugify(String(t||'')) })

/* Cover preview */
const coverPreview = ref<string|null>(null)
function onCover(e:Event){
  const f=(e.target as HTMLInputElement).files?.[0]||null
  form.cover=f
  if(coverPreview.value) URL.revokeObjectURL(coverPreview.value)
  coverPreview.value = f ? URL.createObjectURL(f) : null
}
onBeforeUnmount(()=>{ if(coverPreview.value) URL.revokeObjectURL(coverPreview.value) })

/* Tags */
function addTag(){
  const t=form.tagInput.trim()
  if(!t) return
  if(t.length>40) return alert('Tag trop long')
  form.tags.push(t); form.tagInput=''
}
function removeTag(i:number){ form.tags.splice(i,1) }

/* Submit : POST + _method=PUT si édition + redirection fiable */
function submit(){
  const url = isEdit.value
    ? route('admin.posts.update', props.item!.id)
    : route('admin.posts.store')

  form.transform(d=>({ ...d, ...(isEdit.value ? { _method:'PUT' as const } : {}) }))

  form.post(url, {
    forceFormData:true,
    preserveScroll:true,
    onSuccess:()=> router.visit(route('admin.posts.index')),
    onFinish: ()=> form.transform(d=>({ ...d, _method: undefined as any })),
  })
}
</script>

<template>
  <div class="max-w-5xl mx-auto text-bk-off">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <div class="text-[11px] text-bk-off/60">BKO Construction / Back-office</div>
        <h1 class="text-2xl font-extrabold">{{ isEdit ? 'Modifier un article' : 'Nouvel article' }}</h1>
      </div>
      <div class="flex items-center gap-2">
        <Link :href="route('admin.posts.index')" class="px-3 h-10 inline-flex items-center rounded-lg bg-white/10 hover:bg-white/15 text-bk-off">
          Retour
        </Link>
        <button type="button" :disabled="form.processing" @click="submit"
                class="px-4 h-10 inline-flex items-center gap-2 rounded-lg bg-bk-gold text-bk-night font-bold hover:brightness-95 disabled:opacity-60">
          Enregistrer
        </button>
      </div>
    </div>

    <!-- Upload progress -->
    <div v-if="form.progress" class="h-1 mb-4 rounded bg-white/10 overflow-hidden">
      <div class="h-1 bg-bk-gold" :style="{ width: `${form.progress.percentage}%` }"></div>
    </div>

    <div v-if="Object.keys(form.errors).length"
         class="mb-4 rounded-xl bg-red-500/10 ring-1 ring-red-500/30 text-red-200 px-4 py-3">
      Merci de corriger les champs indiqués ci-dessous.
    </div>

    <form @submit.prevent="submit" class="grid gap-5">
      <div class="grid md:grid-cols-2 gap-4">
        <label class="grid gap-1">
          <span class="text-bk-off/70 text-sm">Titre *</span>
          <input v-model="form.title" required
                 class="bg-white/[.06] ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none text-bk-off placeholder:text-bk-off/50 focus:ring-bk-gold/50" />
        </label>
        <label class="grid gap-1">
          <span class="text-bk-off/70 text-sm">Slug</span>
          <input v-model="form.slug" @input="slugTouched=true"
                 class="bg-white/[.06] ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none text-bk-off placeholder:text-bk-off/50 focus:ring-bk-gold/50" />
        </label>
      </div>

      <div class="grid md:grid-cols-3 gap-4">
        <label class="grid gap-1">
          <span class="text-bk-off/70 text-sm">Statut</span>
          <div class="relative">
            <select v-model="form.status"
                    class="h-10 w-full appearance-none rounded-lg bg-white/[.06] ring-1 ring-white/10 px-3 pr-8 outline-none text-bk-off [color-scheme:dark] focus:ring-bk-gold/50">
              <option value="brouillon">Brouillon</option>
              <option value="publié">Publié</option>
            </select>
            <span class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 opacity-70">▾</span>
          </div>
        </label>
        <label class="grid gap-1 md:col-span-2">
          <span class="text-bk-off/70 text-sm">Publication (optionnel)</span>
          <input type="datetime-local" v-model="form.published_at"
                 class="h-10 rounded-lg bg-white/[.06] ring-1 ring-white/10 px-3 outline-none text-bk-off placeholder:text-bk-off/50 [color-scheme:dark] focus:ring-bk-gold/50" />
        </label>
      </div>

      <label class="grid gap-1">
        <span class="text-bk-off/70 text-sm">Résumé</span>
        <textarea v-model="form.excerpt" rows="2"
                  class="bg-white/[.06] ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none text-bk-off placeholder:text-bk-off/50 focus:ring-bk-gold/50"></textarea>
      </label>

      <label class="grid gap-1">
        <span class="text-bk-off/70 text-sm">Contenu</span>
        <textarea v-model="form.body" rows="8"
                  class="bg-white/[.06] ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none text-bk-off placeholder:text-bk-off/50 focus:ring-bk-gold/50"></textarea>
      </label>

      <div class="grid md:grid-cols-2 gap-4">
        <label class="grid gap-1">
          <span class="text-bk-off/70 text-sm">Image de couverture</span>
          <input type="file" accept="image/*" @change="onCover" class="text-bk-off [color-scheme:dark]" />
          <div v-if="props.item?.cover_image && !coverPreview" class="text-xs text-bk-off/60">
            Actuelle : <span class="underline">/storage/{{ props.item.cover_image }}</span>
          </div>
        </label>

        <div class="grid gap-1">
          <span class="text-bk-off/70 text-sm">Tags</span>
          <div class="flex gap-2">
            <input v-model="form.tagInput"
                   class="flex-1 bg-white/[.06] ring-1 ring-white/10 rounded-lg px-3 py-2 outline-none text-bk-off placeholder:text-bk-off/50 focus:ring-bk-gold/50"
                   @keyup.enter.prevent="addTag" />
            <button type="button" @click="addTag" class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-bk-off">
              Ajouter
            </button>
          </div>
          <div class="flex flex-wrap gap-2 mt-2">
            <span v-for="(t,i) in form.tags" :key="i" class="px-2 h-7 inline-flex items-center rounded-md bg-white/10 text-sm text-bk-off">
              {{ t }}
              <button type="button" class="ml-2 text-bk-off/60 hover:text-bk-off" @click="removeTag(i)">✕</button>
            </span>
          </div>
        </div>
      </div>

      <div>
        <button class="px-4 h-10 inline-flex items-center rounded-lg bg-bk-gold text-bk-night font-bold hover:brightness-95">
          Enregistrer
        </button>
      </div>
    </form>

    <div class="mt-6 rounded-2xl overflow-hidden ring-1 ring-white/10 bg-white/[.04]"
         v-if="coverPreview || props.item?.cover_image">
      <div class="p-3 text-sm text-bk-off/70">Aperçu — image de couverture</div>
      <div class="aspect-video bg-white/5">
        <img v-if="coverPreview" :src="coverPreview" class="w-full h-full object-cover" />
        <img v-else :src="`/storage/${props.item?.cover_image}`" class="w-full h-full object-cover" />
      </div>
    </div>
  </div>
</template>

<style scoped>
/* teinte uniforme, y compris le menu d’options natif */
:global(select), :global(input), :global(textarea) { color: #e3e6ec; }
:global(select option){ color:#e3e6ec; background:#0f141a; }
:global(input::placeholder), :global(textarea::placeholder){ color:#e3e6ec99; }
/* améliore le contraste quand l’OS force le thème clair */
:global([color-scheme="dark"]){ color-scheme: dark; }
</style>
