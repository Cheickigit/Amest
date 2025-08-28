<!-- resources/js/Pages/Public/Posts.vue (Index actualités avec sidebar) -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { route as ziggyRoute } from 'ziggy-js'
defineOptions({ layout: PublicLayout })
const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(globalThis as any).Ziggy)

/* v-reveal */
let io:IntersectionObserver|null = null
const vReveal = {
  mounted(el:HTMLElement){
    el.classList.add('reveal')
    if(!io){
      io = new IntersectionObserver((entries)=>{ entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('is-visible'); io?.unobserve(e.target) } })},{threshold:.14})
    }
    io.observe(el)
  }
}

type Post = { id:number; slug?:string|null; title:string; excerpt?:string|null; published_at?:string|null; cover_image?:string|null }
const props = defineProps<{ items: { data:Post[], links?:any } | Post[] }>()
const list = Array.isArray(props.items) ? props.items : (props.items.data ?? [])
const fmt = (d?:string|null)=> d ? new Date(d).toLocaleDateString('fr-FR',{year:'numeric',month:'long',day:'2-digit'}) : ''

/* Archives (yyyy-mm) */
const archives = (() => {
  const map = new Map<string,string>()
  for(const p of list){
    if(!p.published_at) continue
    const dt = new Date(p.published_at)
    const k = `${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}`
    const lbl = dt.toLocaleDateString('fr-FR',{year:'numeric',month:'long'})
    map.set(k,lbl)
  }
  return Array.from(map.entries()).slice(0,8)
})()

/* Derniers */
const latest = list.slice(0,5)
</script>

<template>
  <div class="theme-sand max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 md:pb-16">
    <!-- Header -->
    <header class="mb-7 md:mb-10" v-reveal>
      <div class="titlebar">
        <h1 class="headline">Actualités</h1>
        <p class="subtitle">Projets, chantiers, QSE et vie de l’entreprise — nos dernières publications.</p>
      </div>
    </header>

    <div v-if="!list.length" class="card glass ring-gold p-10 text-center dim" v-reveal>
      Aucune actualité pour l’instant.
    </div>

    <!-- Layout blog: contenu + sidebar -->
    <div v-else class="grid lg:grid-cols-12 gap-6">
      <!-- Main -->
      <div class="lg:col-span-8 grid gap-6 sm:grid-cols-2">
        <Link v-for="p in list" :key="p.id" :href="route('public.posts.show', p.slug ?? p.id)" class="card-pro group" v-reveal>
          <div class="thumb">
            <img v-if="p.cover_image" :src="`/storage/${p.cover_image}`" alt="" class="img">
            <div v-else class="noimg">Aucune image</div>
            <span class="shine" />
            <div class="grad" />
            <div class="chips">
              <span class="chip">Actu</span>
              <span class="chip mute">{{ fmt(p.published_at) }}</span>
            </div>
          </div>
          <div class="body">
            <h3 class="title line-clamp-2">{{ p.title }}</h3>
            <p v-if="p.excerpt" class="excerpt line-clamp-2">{{ p.excerpt }}</p>
            <span class="cta">Lire</span>
          </div>
        </Link>
      </div>

      <!-- Sidebar -->
      <aside class="lg:col-span-4 space-y-4">
        <div class="glass ring-gold p-4 md:p-5" v-reveal>
          <h3 class="h3">À la une</h3>
          <ul class="mt-3 space-y-3">
            <li v-for="p in latest" :key="p.id">
              <Link :href="route('public.posts.show', p.slug ?? p.id)" class="side-item">
                <div class="thumb-s">
                  <img v-if="p.cover_image" :src="`/storage/${p.cover_image}`" class="img-s" alt="">
                  <div v-else class="noimg-s">—</div>
                </div>
                <div>
                  <div class="title-s line-clamp-2">{{ p.title }}</div>
                  <div class="date-s">{{ fmt(p.published_at) }}</div>
                </div>
              </Link>
            </li>
          </ul>
        </div>

        <div class="glass ring-gold p-4 md:p-5" v-reveal>
          <h3 class="h3">Archives</h3>
          <div class="mt-3 flex flex-wrap gap-2">
            <span v-for="[k,lab] in archives" :key="k" class="badge-ghost">{{ lab }}</span>
          </div>
        </div>

        <div class="glass ring-gold p-4 md:p-5" v-reveal>
          <h3 class="h3">Services liés</h3>
          <div class="mini-bullets mt-3">
            <span>✔️ VRD / Voirie</span><span>✔️ Bâtiments</span><span>✔️ Génie civil</span>
          </div>
          <div class="mt-4">
            <Link :href="route('public.services')" class="btn-ghost">Voir nos services</Link>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<style scoped>
/* Reveal */
.reveal{opacity:0;transform:translateY(12px) scale(.985);filter:blur(2px);transition:all .6s cubic-bezier(.2,.8,.2,1)}
.reveal.is-visible{opacity:1;transform:none;filter:none}

/* Thème */
.theme-sand{ --tx:#E9E2CC; --tx-strong:#F5EED5; --tx-dim:rgba(233,226,204,.8); --tx-mute:rgba(233,226,204,.62); color:var(--tx) }
.headline{ font-size:clamp(1.8rem,1.2rem+2vw,2.4rem); font-weight:800; color:var(--tx-strong); letter-spacing:-.02em }
.subtitle{ color:var(--tx-dim); margin-top:.35rem }
.dim{ color:var(--tx-dim) } .mute{ color:var(--tx-mute) }
.titlebar{ position:relative; padding:1.1rem 1.2rem; border-radius:1.1rem; background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.03));
  border:1px solid rgba(255,255,255,.12); box-shadow:0 30px 80px -40px rgba(0,0,0,.5) }
.titlebar:after{ content:""; position:absolute; inset:0; border-radius:inherit; pointer-events:none; padding:1px;
  background:linear-gradient(120deg, rgba(220,193,118,.9), rgba(255,255,255,.16), rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude }

/* Carte actu */
.card-pro{ display:block; overflow:hidden; border-radius:1rem; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.04);
  transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease; position:relative }
.card-pro:hover{ transform:translateY(-2px); border-color:rgba(220,193,118,.55); box-shadow:0 36px 120px -46px rgba(0,0,0,.65) }
.thumb{ position:relative; aspect-ratio:16/9; background:rgba(255,255,255,.05); overflow:hidden }
.img{ width:100%; height:100%; object-fit:cover; transform:scale(1.03); transition:transform .25s ease }
.card-pro:hover .img{ transform:scale(1.05) }
.noimg{ width:100%; height:100%; display:grid; place-items:center; color:var(--tx-mute) }
.grad{ position:absolute; inset:0; background:linear-gradient(180deg,transparent,rgba(15,20,26,.78)); opacity:.9 }
.shine{ position:absolute; inset:-40%; transform:rotate(18deg); background:linear-gradient( to right, transparent, rgba(255,255,255,.10), transparent );
  filter:blur(6px); opacity:0; transition:opacity .3s; }
.card-pro:hover .shine{ opacity:1; animation:shine 1.2s ease }
@keyframes shine{ from{ transform:translateX(-60%) rotate(18deg) } to{ transform:translateX(60%) rotate(18deg) } }
.chips{ position:absolute; left:.65rem; bottom:.6rem; display:flex; gap:.35rem; }
.chip{ font-size:.7rem; padding:.25rem .45rem; border-radius:.5rem; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.18) }
.body{ padding:.85rem .95rem 1rem }
.title{ font-weight:800; color:var(--tx); text-shadow:0 1px 0 rgba(0,0,0,.2) }
.excerpt{ margin-top:.25rem; font-size:.95rem; color:var(--tx-mute) }
.cta{ margin-top:.55rem; display:inline-flex; align-items:center; gap:.45rem; color:var(--tx); border:1px solid rgba(255,255,255,.14);
  padding:.45rem .7rem; border-radius:.7rem; background:rgba(255,255,255,.06) }

/* Sidebar */
.h3{ font-weight:900; letter-spacing:-.01em }
.glass{ background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:1rem }
.side-item{ display:flex; gap:.6rem; align-items:center }
.thumb-s{ width:66px; height:46px; border-radius:.5rem; overflow:hidden; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12) }
.img-s{ width:100%; height:100%; object-fit:cover } .noimg-s{ width:100%; height:100%; display:grid; place-items:center; color:var(--tx-mute) }
.title-s{ font-weight:700 } .date-s{ font-size:.8rem; color:var(--tx-mute) }
.badge-ghost{ display:inline-flex; align-items:center; justify-content:center; border-radius:.45rem; padding:.25rem .4rem; font-size:.7rem; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:#fdfdfe }

.card{ border-radius:1rem }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.7rem 1rem; border-radius:1rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe }
.ring-gold{ position:relative } .ring-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude }
</style>
