<!-- resources/js/Pages/Public/Projects.vue (Liste des réalisations) -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
defineOptions({ layout: PublicLayout })
const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(globalThis as any).Ziggy)

/* v-reveal minimal */
let io:IntersectionObserver|null = null
const vReveal = {
  mounted(el:HTMLElement){
    el.classList.add('reveal')
    if(!io){
      io = new IntersectionObserver((entries)=>{
        entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('is-visible'); io?.unobserve(e.target) } })
      },{threshold:.14})
    }
    io.observe(el)
  }
}

type P = { id:number; slug?:string|null; title:string; category?:string|null; city?:string|null; cover_image?:string|null }
const props = defineProps<{ items: { data:P[], links?:any } | P[] }>()
const raw = Array.isArray(props.items) ? props.items : (props.items.data ?? [])
const list = computed(()=> raw)

/* Filtres côté client */
const allCats = computed(()=> {
  const s = new Set<string>()
  for(const p of list.value){ if(p.category) s.add(p.category) }
  return ['Tous', ...Array.from(s)]
})
const selCat = ref('Tous')
const filtered = computed(()=> selCat.value==='Tous' ? list.value : list.value.filter(p=>p.category===selCat.value))
</script>

<template>
  <div class="theme-sand max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 md:pb-16">
    <!-- HERO — 3D centré avec textes latéraux -->
    <section class="relative overflow-hidden pt-14 md:pt-18 pb-8 md:pb-10">
      <div class="hero-ribbon" aria-hidden="true"></div>
      <div class="grid lg:grid-cols-12 gap-6 items-center">
        <!-- Texte gauche -->
        <div class="lg:col-span-3 order-2 lg:order-1" v-reveal>
          <div class="titlebar">
            <h1 class="headline">Réalisations</h1>
            <p class="subtitle">Ouvrages livrés, chantiers phares & références récentes.</p>
          </div>
          <div class="mt-4 flex flex-wrap gap-2">
            <span class="chip">VRD</span><span class="chip">Bâtiments</span><span class="chip">Génie civil</span>
          </div>
          <div class="mt-5">
            <Link :href="route('home') + '#rfp'" class="btn-outline-gold">Demander un devis</Link>
          </div>
        </div>

        <!-- 3D au centre -->
        <div class="lg:col-span-6 order-1 lg:order-2 relative" v-reveal>
          <div class="hero-halo" aria-hidden="true"></div>
          <div class="proj3d">
            <div class="deck">
              <span class="slab s1"></span>
              <span class="slab s2"></span>
              <span class="slab s3"></span>
              <span class="slab s4"></span>
            </div>
            <div class="tower">
              <span class="b b1"></span>
              <span class="b b2"></span>
              <span class="b b3"></span>
              <span class="b b4"></span>
            </div>
            <div class="medal">
              <span class="ring"></span><span class="coin">BK</span>
            </div>
          </div>
        </div>

        <!-- Texte droite -->
        <div class="lg:col-span-3 order-3" v-reveal>
          <div class="card ring-gold p-4 md:p-5">
            <div class="dim text-sm">Points forts</div>
            <ul class="mt-2 space-y-2 text-white/85">
              <li>• Qualité & traçabilité</li>
              <li>• Délais maîtrisés</li>
              <li>• HSE au cœur</li>
            </ul>
            <div class="mt-4 grid grid-cols-2 gap-2">
              <Link :href="route('public.services')" class="btn-ghost">Services</Link>
              <Link :href="route('public.contact')" class="btn-ghost">Contact</Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Filtres -->
    <section class="mb-6" v-reveal>
      <div class="filterbar">
        <button
          v-for="c in allCats" :key="c"
          class="pill"
          :class="selCat===c ? 'is-active' : ''"
          @click="selCat=c">{{ c }}</button>
      </div>
    </section>

    <!-- Aucune -->
    <div v-if="!filtered.length" class="glass ring-gold p-10 text-center dim" v-reveal>
      Aucune réalisation pour l’instant.
    </div>

    <!-- Grille -->
    <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <Link v-for="p in filtered" :key="p.id" :href="route('public.projects.show', p.slug ?? p.id)" class="ref-pro group" v-reveal>
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

        <div class="overlay">
          <span class="btn-outline-gold">Voir le projet</span>
        </div>
      </Link>
    </div>
  </div>
</template>

<style scoped>
/* Reveal */
.reveal{opacity:0;transform:translateY(12px) scale(.985);filter:blur(2px);transition:all .6s cubic-bezier(.2,.8,.2,1)}
.reveal.is-visible{opacity:1;transform:none;filter:none}

/* Thème */
.theme-sand{ --tx:#E9E2CC; --tx-strong:#F5EED5; --tx-dim:rgba(233,226,204,.8); --tx-mute:rgba(233,226,204,.6); color:var(--tx) }
.headline{ color:var(--tx-strong); font-size:clamp(1.8rem,1.2rem+2vw,2.4rem); font-weight:800; letter-spacing:-.02em }
.subtitle{ color:var(--tx-dim); margin-top:.35rem }
.dim{ color:var(--tx-dim) } .mute{ color:var(--tx-mute) }
.titlebar{ border:1px solid rgba(255,255,255,.12); background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.03)); border-radius:1.1rem; padding:1.05rem 1.15rem }
.glass{ background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:1rem }
.chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.35rem .6rem; border-radius:.6rem; color:var(--tx); font-weight:600; font-size:.85rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.14) }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.7rem 1rem; border-radius:1rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.1rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude }
.card{ position:relative; border-radius:1.1rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); backdrop-filter:blur(10px) }
.ring-gold{ position:relative }
.ring-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude }

/* Filterbar */
.filterbar{ display:flex; flex-wrap:wrap; gap:.45rem }
.pill{ padding:.45rem .7rem; border-radius:.7rem; border:1px solid rgba(255,255,255,.14); background:rgba(255,255,255,.06); color:var(--tx); font-weight:700; font-size:.85rem }
.pill.is-active{ border-color:rgba(220,193,118,.5); box-shadow:0 0 0 1px rgba(220,193,118,.3) inset }

/* HERO 3D */
.hero-ribbon{ position:absolute; left:0; right:0; top:-10px; height:36px; opacity:.45; transform: rotate(-2deg); background: repeating-linear-gradient(135deg,#2a3644 0 18px,#2a3644 18px 28px,#dcc176 28px 46px,#dcc176 46px 56px) }
.hero-halo{ position:absolute; inset:-40px; border-radius:2rem; filter:blur(36px); opacity:.55; background: radial-gradient(60% 60% at 25% 25%, #dcc176 0%, transparent 60%) }

.proj3d{ position:relative; height:min(28rem,64vw); display:grid; place-items:center; perspective:1100px; transform-style:preserve-3d }
.deck{ position:absolute; inset:auto; bottom:-6px; width:96%; height:12rem; transform: rotateX(58deg) rotateZ(-10deg); border-radius:1.2rem;
  background:linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.04)); border:1px solid rgba(255,255,255,.12) }
.slab{ position:absolute; display:block; border-radius:12px; border:1px solid rgba(255,255,255,.14);
  background:linear-gradient(160deg, rgba(255,255,255,.12), rgba(21,30,39,.55)); transform: rotateX(58deg) rotateZ(-10deg) translateZ(0) }
.s1{ width:170px; height:20px; left:8%;  bottom:14% } .s2{ width:140px; height:16px; left:34%; bottom:26% }
.s3{ width:110px; height:14px; left:58%; bottom:38% } .s4{ width:90px; height:12px; left:76%; bottom:48% }

.tower .b{ position:absolute; border-radius:10px; border:1px solid rgba(255,255,255,.14);
  background:linear-gradient(160deg, rgba(255,255,255,.12), rgba(21,30,39,.55)); transform: rotateX(58deg) rotateZ(-10deg) }
.b1{ width:60px; height:120px; left:20%; bottom:20% } .b2{ width:46px; height:95px; left:38%; bottom:30% }
.b3{ width:38px; height:75px; left:58%; bottom:40% } .b4{ width:30px; height:58px; left:74%; bottom:50% }

.medal{ position:absolute; right:8%; top:18%; transform: translateZ(120px) rotateX(10deg) rotateZ(-8deg) }
.medal .ring{ position:absolute; inset:-10px; border:2px solid rgba(220,193,118,.55); border-radius:999px; animation:spin 8s linear infinite }
.medal .coin{ width:86px; height:86px; display:grid; place-items:center; font-weight:900; letter-spacing:.02em;
  border-radius:999px; background: radial-gradient(60% 60% at 50% 45%, #dcc176, #b89b55); color:#0f141a; box-shadow:0 20px 60px -20px rgba(0,0,0,.6) }
@keyframes spin{to{transform:rotate(360deg)}}

/* Cards */
.ref-pro{ position:relative; height:260px; border-radius:1rem; overflow:hidden; border:1px solid rgba(255,255,255,.12);
  background:rgba(255,255,255,.04); transition:.2s }
.ref-pro:hover{ transform:translateY(-2px); border-color:rgba(220,193,118,.55); box-shadow:0 36px 120px -46px rgba(0,0,0,.65) }
.img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.9; transition:.25s }
.ref-pro:hover .img{ opacity:.98; transform:scale(1.02) }
.noimg{ position:absolute; inset:0; display:grid; place-items:center; color:var(--tx-mute) }
.cap{ position:absolute; inset-inline:0; bottom:0; padding:1rem; background:linear-gradient(180deg,transparent,rgba(15,20,26,.86)) }
.t{ font-weight:800; text-shadow:0 1px 0 rgba(0,0,0,.25) }
.chips{ display:flex; gap:.35rem; margin-bottom:.25rem }
.overlay{ position:absolute; inset:0; display:grid; place-items:center; background:rgba(0,0,0,.34); opacity:0; transition:.2s }
.ref-pro:hover .overlay{ opacity:1 }
.grain{ position:absolute; inset:0; opacity:.12; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'%3E%3Cfilter id='a'%3E%3CfeTurbulence baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23a)' opacity='.25'/%3E%3C/svg%3E") }
</style>
