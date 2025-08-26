<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
const props = defineProps<{ item?: any }>()
const form = useForm({
  title: props.item?.title ?? '',
  slug: props.item?.slug ?? '',
  status: props.item?.status ?? 'brouillon',
  published_at: props.item?.published_at ?? '',
  excerpt: props.item?.excerpt ?? '',
  body: props.item?.body ?? '',
  cover: null as File | null,
  tags: props.item?.tags ?? [],
  tagInput: '' as string,
})
function addTag(){ if(form.tagInput){ form.tags.push(form.tagInput); form.tagInput=''; } }
function submit(){
  const url = props.item ? route('admin.posts.update', props.item.id) : route('admin.posts.store')
  form.post(url, { forceFormData:true })
}
</script>

<template>
  <div class="max-w-4xl mx-auto p-6 text-white">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">{{ props.item ? 'Modifier' : 'Nouvel' }} article</h1>
      <Link :href="route('admin.posts.index')" class="px-3 py-2 rounded-lg bg-white/10">Retour</Link>
    </div>

    <form @submit.prevent="submit" class="grid gap-4">
      <div class="grid md:grid-cols-2 gap-4">
        <label class="grid gap-1"><span class="text-white/70 text-sm">Titre</span><input v-model="form.title" required class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2" /></label>
        <label class="grid gap-1"><span class="text-white/70 text-sm">Slug</span><input v-model="form.slug" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2" /></label>
      </div>

      <div class="grid md:grid-cols-3 gap-4">
        <label class="grid gap-1"><span class="text-white/70 text-sm">Statut</span>
          <select v-model="form.status" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2"><option value="brouillon">Brouillon</option><option value="publié">Publié</option></select>
        </label>
        <label class="grid gap-1 md:col-span-2"><span class="text-white/70 text-sm">Publication (optionnel)</span>
          <input type="datetime-local" v-model="form.published_at" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2" />
        </label>
      </div>

      <label class="grid gap-1"><span class="text-white/70 text-sm">Résumé</span><textarea v-model="form.excerpt" rows="2" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2"></textarea></label>
      <label class="grid gap-1"><span class="text-white/70 text-sm">Contenu</span><textarea v-model="form.body" rows="8" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2"></textarea></label>

      <div class="grid md:grid-cols-2 gap-4">
        <label class="grid gap-1"><span class="text-white/70 text-sm">Image de couverture</span><input type="file" @change="(e:any)=>form.cover=e.target.files?.[0] ?? null" /></label>
        <div class="grid gap-1">
          <span class="text-white/70 text-sm">Tags</span>
          <div class="flex gap-2">
            <input v-model="form.tagInput" class="flex-1 bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2" />
            <button type="button" @click="addTag" class="px-3 py-2 rounded-lg bg-white/10">Ajouter</button>
          </div>
          <div class="flex flex-wrap gap-2 mt-2">
            <span v-for="(t,i) in form.tags" :key="i" class="px-2 py-1 rounded-md bg-white/10 text-sm">{{ t }}</span>
          </div>
        </div>
      </div>

      <div><button class="px-4 py-2 rounded-lg bg-bk-gold text-bk-night font-bold">Enregistrer</button></div>
    </form>
  </div>
</template>
