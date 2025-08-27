<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import { route as ziggyRoute } from 'ziggy-js'

defineOptions({ layout: AdminLayout })

type FileItem = { name?:string|null; path?:string|null; url?:string|null; size?:number|null; mime?:string|null }
type Quote = {
  id:number; name:string; company?:string|null; email?:string|null; phone?:string|null; city?:string|null;
  project_type?:string|null; budget?:string|null; desired_date?:string|null;
  message?:string|null; files?:FileItem[]; status:string; created_at:string; read_at?:string|null
}
const props = defineProps<{ item: Quote }>()
const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(globalThis as any).Ziggy)
const fmt = (d?:string|null)=> d ? new Date(d).toLocaleString('fr-FR',{ dateStyle:'medium', timeStyle:'short' }) : '—'

function prettySize(b?:number|null){
  if (!b) return '—'
  const u = ['o','Ko','Mo','Go','To']; let i=0; let v=b
  while(v>=1024 && i<u.length-1){ v/=1024; i++ }
  return `${v.toFixed(1)} ${u[i]}`
}

function confirmDelete(e: Event){
  if (!confirm('Supprimer définitivement cette demande ?')) e.preventDefault()
}
</script>

<template>
  <div class="space-y-6">
    <!-- HEADER -->
    <div class="flex flex-wrap items-end justify-between gap-3">
      <div>
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Demande #{{ props.item.id }}</h1>
        <div class="text-white/70 text-sm">Reçue le {{ fmt(props.item.created_at) }}</div>
      </div>
      <Link :href="route('admin.quotes.index')" class="inline-flex items-center h-10 px-3 rounded-lg border border-white/15 hover:border-bk-gold/60">← Retour à la liste</Link>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
      <!-- DETAILS -->
      <div class="lg:col-span-2 space-y-4">
        <div class="card ring-gold p-5">
          <div class="font-bold text-lg mb-3">Informations</div>
          <div class="grid md:grid-cols-2 gap-3">
            <div><span class="text-white/60">Nom :</span> <span class="font-semibold">{{ props.item.name }}</span></div>
            <div><span class="text-white/60">Société :</span> {{ props.item.company ?? '—' }}</div>
            <div><span class="text-white/60">Email :</span> <span class="text-white/85">{{ props.item.email ?? '—' }}</span></div>
            <div><span class="text-white/60">Tél :</span> {{ props.item.phone ?? '—' }}</div>
            <div><span class="text-white/60">Ville :</span> {{ props.item.city ?? '—' }}</div>
            <div><span class="text-white/60">Type :</span> {{ props.item.project_type ?? '—' }}</div>
            <div><span class="text-white/60">Budget :</span> {{ props.item.budget ?? '—' }}</div>
            <div><span class="text-white/60">Délai souhaité :</span> {{ props.item.desired_date ?? '—' }}</div>
          </div>
        </div>

        <div class="card ring-gold p-5">
          <div class="font-bold text-lg mb-3">Message</div>
          <div class="whitespace-pre-wrap text-white/90 leading-relaxed">
            {{ props.item.message || '—' }}
          </div>
        </div>
      </div>

      <!-- ASIDE -->
      <aside class="space-y-4">
        <div class="card ring-gold p-5">
          <div class="font-bold mb-3">Pièces jointes</div>
          <ul class="space-y-2">
            <li v-for="(f,i) in (props.item.files || [])" :key="i"
                class="flex items-center gap-3 justify-between">
              <div class="min-w-0">
                <div class="truncate font-medium">{{ f.name || ('Fichier ' + (i+1)) }}</div>
                <div class="text-xs text-white/60">{{ f.mime || '—' }} · {{ prettySize(f.size) }}</div>
              </div>
              <a :href="route('admin.quotes.file', { quote: props.item.id, index: i })"
                 class="inline-flex items-center h-9 px-3 rounded-lg border border-white/15 hover:border-bk-gold/60">Télécharger</a>
            </li>
            <li v-if="!(props.item.files||[]).length" class="text-white/70">Aucun fichier joint.</li>
          </ul>
        </div>

        <div class="card ring-gold p-5">
          <div class="font-bold mb-2">Actions</div>
          <form :action="route('admin.quotes.destroy', props.item.id)" method="post" @submit="confirmDelete">
            <input type="hidden" name="_method" value="DELETE" />
            <button class="inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-red-400/40 text-red-300 hover:bg-red-400/10">
              Supprimer la demande
            </button>
          </form>
          <div class="mt-3 text-xs text-white/60">
            Statut : <span class="font-semibold" :class="props.item.status==='new' ? 'text-bk-gold' : 'text-white/80'">{{ props.item.status }}</span><br>
            Lu le : {{ fmt(props.item.read_at) }}
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<style scoped>
.card{ border-radius: 1rem; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); backdrop-filter: blur(10px) }
.ring-gold{ position:relative }
.ring-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; z-index:0; pointer-events:none;
  background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9));
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
</style>
