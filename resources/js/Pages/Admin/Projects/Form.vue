<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
const props = defineProps<{ item?: any }>()
const form = useForm({
  title: props.item?.title ?? '',
  slug: props.item?.slug ?? '',
  category: props.item?.category ?? '',
  city: props.item?.city ?? '',
  client: props.item?.client ?? '',
  year: props.item?.year ?? '',
  status: props.item?.status ?? 'brouillon',
  excerpt: props.item?.excerpt ?? '',
  body: props.item?.body ?? '',
  cover: null as File | null,
  gallery: [] as File[],
})
function submit () {
  const url = props.item ? route('admin.projects.update', props.item.id) : route('admin.projects.store')
  form.post(url, { forceFormData: true })
}
</script>

<template>
  <div class="max-w-4xl mx-auto p-6 text-white">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">{{ props.item ? 'Modifier' : 'Nouveau' }} projet</h1>
      <Link :href="route('admin.projects.index')" class="px-3 py-2 rounded-lg bg-white/10">Retour</Link>
    </div>

    <form @submit.prevent="submit" class="grid gap-4">
      <div class="grid md:grid-cols-2 gap-4">
        <label class="grid gap-1">
          <span class="text-white/70 text-sm">Titre</span>
          <input v-model="form.title" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2" required />
        </label>
        <label class="grid gap-1">
          <span class="text-white/70 text-sm">Slug</span>
          <input v-model="form.slug" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2" />
        </label>
      </div>

      <div class="grid md:grid-cols-4 gap-4">
        <label class="grid gap-1"><span class="text-white/70 text-sm">Catégorie</span><input v-model="form.category" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2" /></label>
        <label class="grid gap-1"><span class="text-white/70 text-sm">Ville</span><input v-model="form.city" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2" /></label>
        <label class="grid gap-1"><span class="text-white/70 text-sm">Client</span><input v-model="form.client" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2" /></label>
        <label class="grid gap-1"><span class="text-white/70 text-sm">Année</span><input v-model="form.year" type="number" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2" /></label>
      </div>

      <label class="grid gap-1"><span class="text-white/70 text-sm">Résumé</span><textarea v-model="form.excerpt" rows="2" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2"></textarea></label>
      <label class="grid gap-1"><span class="text-white/70 text-sm">Contenu</span><textarea v-model="form.body" rows="6" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2"></textarea></label>

      <div class="grid md:grid-cols-2 gap-4">
        <label class="grid gap-1"><span class="text-white/70 text-sm">Image de couverture</span><input type="file" @change="(e:any)=>form.cover=e.target.files?.[0] ?? null" /></label>
        <label class="grid gap-1"><span class="text-white/70 text-sm">Galerie (plusieurs)</span><input type="file" multiple @change="(e:any)=>form.gallery=[...e.target.files]" /></label>
      </div>

      <label class="grid gap-1">
        <span class="text-white/70 text-sm">Statut</span>
        <select v-model="form.status" class="bg-white/5 ring-1 ring-white/10 rounded-lg px-3 py-2">
          <option value="brouillon">Brouillon</option>
          <option value="publié">Publié</option>
        </select>
      </label>

      <div><button class="px-4 py-2 rounded-lg bg-bk-gold text-bk-night font-bold">Enregistrer</button></div>
    </form>
  </div>
</template>
