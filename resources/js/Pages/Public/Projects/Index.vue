<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { route as ziggyRoute } from 'ziggy-js'
defineOptions({ layout: PublicLayout })
const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(globalThis as any).Ziggy)

type P = { id:number; slug?:string|null; title:string; category?:string|null; city?:string|null; cover_image?:string|null }
const props = defineProps<{ items: { data:P[], links?:any } | P[] }>()
const list = Array.isArray(props.items) ? props.items : (props.items.data ?? [])
</script>

<template>
  <div class="theme-sand max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 md:pb-16">
    <header class="mb-7 md:mb-10 flex items-end justify-between gap-4">
      <div class="titlebar">
        <h1 class="headline">Réalisations</h1>
        <p class="subtitle">Ouvrages livrés et chantiers emblématiques — sélection récente.</p>
      </div>
      <Link href="/#rfp" class="btn-outline-gold shrink-0">Demander un devis</Link>
    </header>

    <div v-if="!list.length" class="glass ring-gold p-10 text-center dim">Aucune réalisation pour l’instant.</div>

    <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <Link v-for="p in list" :key="p.id" :href="route('public.projects.show', p.slug ?? p.id)" class="ref-pro group">
        <div class="grain" aria-hidden="true"></div>
        <img v-if="p.cover_image" :src="`/storage/${p.cover_image}`" class="img" alt="">
        <div v-else class="noimg">Aucune image</div>

        <div class="cap">
          <div class="chips">
            <span v-if="p.category" class="chip">{{ p.category }}</span>
            <span v-if="p.city" class="chip mute">{{ p.city }}</span>
          </div>
          <div class="t line-clamp-2">{{ p.title }}</div>
        </div>
        <div class="overlay"><span class="btn-outline-gold">Voir</span></div>
      </Link>
    </div>
  </div>
</template>

<style scoped>
.theme-sand{ --tx:#E9E2CC; --tx-strong:#F5EED5; --tx-dim:rgba(233,226,204,.8); --tx-mute:rgba(233,226,204,.6); color:var(--tx) }
.headline{ color:var(--tx-strong); font-size:clamp(1.8rem,1.2rem+2vw,2.4rem); font-weight:800; letter-spacing:-.02em }
.subtitle{ color:var(--tx-dim); margin-top:.35rem }
.titlebar{ border:1px solid rgba(255,255,255,.12); background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.03)); border-radius:1.1rem; padding:1.05rem 1.15rem }
.glass{ background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:1rem }

/* ref card */
.ref-pro{ position:relative; height:260px; border-radius:1rem; overflow:hidden; border:1px solid rgba(255,255,255,.12);
  background:rgba(255,255,255,.04); transition:.2s }
.ref-pro:hover{ transform:translateY(-2px); border-color:rgba(220,193,118,.55); box-shadow:0 36px 120px -46px rgba(0,0,0,.65) }
.img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.88; transition:.25s }
.ref-pro:hover .img{ opacity:.95; transform:scale(1.02) }
.noimg{ position:absolute; inset:0; display:grid; place-items:center; color:var(--tx-mute) }
.cap{ position:absolute; inset-inline:0; bottom:0; padding:1rem; background:linear-gradient(180deg,transparent,rgba(15,20,26,.86)) }
.t{ font-weight:800; text-shadow:0 1px 0 rgba(0,0,0,.25) }
.chips{ display:flex; gap:.35rem; margin-bottom:.25rem }
.chip{ font-size:.7rem; padding:.25rem .45rem; border-radius:.5rem; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.18) }
.mute{ color:var(--tx-mute) }
.overlay{ position:absolute; inset:0; display:grid; place-items:center; background:rgba(0,0,0,.34); opacity:0; transition:.2s }
.ref-pro:hover .overlay{ opacity:1 }
.grain{ position:absolute; inset:0; opacity:.12; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'%3E%3Cfilter id='a'%3E%3CfeTurbulence baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23a)' opacity='.25'/%3E%3C/svg%3E") }
</style>
