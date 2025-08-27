<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { route as ziggyRoute } from 'ziggy-js'
const props = defineProps<{ item: any }>()
const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(globalThis as any).Ziggy)
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-extrabold">Demande #{{ props.item.id }}</h1>
      <div class="text-sm text-white/70">Reçue le {{ new Date(props.item.created_at).toLocaleString('fr-FR') }}</div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-4">
        <div class="rounded-xl border border-white/10 p-4 bg-white/5">
          <div class="font-bold mb-2">Informations</div>
          <div class="grid md:grid-cols-2 gap-2">
            <div><span class="text-white/60">Nom :</span> {{ props.item.name }}</div>
            <div><span class="text-white/60">Société :</span> {{ props.item.company ?? '—' }}</div>
            <div><span class="text-white/60">Email :</span> {{ props.item.email ?? '—' }}</div>
            <div><span class="text-white/60">Tél :</span> {{ props.item.phone ?? '—' }}</div>
            <div><span class="text-white/60">Ville :</span> {{ props.item.city ?? '—' }}</div>
            <div><span class="text-white/60">Type :</span> {{ props.item.project_type ?? '—' }}</div>
            <div><span class="text-white/60">Budget :</span> {{ props.item.budget ?? '—' }}</div>
            <div><span class="text-white/60">Désiré le :</span> {{ props.item.desired_date ?? '—' }}</div>
          </div>
        </div>

        <div class="rounded-xl border border-white/10 p-4 bg-white/5">
          <div class="font-bold mb-2">Message</div>
          <div class="whitespace-pre-wrap text-white/85">{{ props.item.message || '—' }}</div>
        </div>
      </div>

      <div class="space-y-4">
        <div class="rounded-xl border border-white/10 p-4 bg-white/5">
          <div class="font-bold mb-2">Pièces jointes</div>
          <ul class="space-y-2">
            <li v-for="(f,i) in (props.item.files || [])" :key="i" class="flex items-center justify-between gap-3">
              <div class="truncate">{{ f.name }}</div>
              <a :href="route('admin.quotes.file', { quote: props.item.id, index: i })"
                 class="inline-flex px-2 py-1 rounded-lg border border-white/15 hover:border-bk-gold/60">Télécharger</a>
            </li>
            <li v-if="!(props.item.files||[]).length" class="text-white/70">Aucun fichier.</li>
          </ul>
        </div>

        <div class="rounded-xl border border-white/10 p-4 bg-white/5">
          <form :action="route('admin.quotes.destroy', props.item.id)" method="post" @submit.prevent="$el.submit()">
            <input type="hidden" name="_method" value="DELETE" />
            <button class="inline-flex items-center px-3 py-2 rounded-lg border border-white/15 hover:border-red-400 text-red-300">Supprimer</button>
          </form>
          <div class="mt-2">
            <Link :href="route('admin.quotes.index')" class="text-white/80 hover:text-bk-gold">← Retour à la liste</Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
