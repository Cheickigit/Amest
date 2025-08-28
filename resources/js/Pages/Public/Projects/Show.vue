<!-- resources/js/Pages/Public/Projects/Show.vue (Fiche projet) -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
defineOptions({ layout: PublicLayout })
const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(globalThis as any).Ziggy)

/* v-reveal minimal */
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

type MediaItem = { type:'image'|'video', kind?:'upload'|'url', path?:string|null, url?:string|null }
type Project = {
  id:number; slug?:string|null; title:string;
  category?:string|null; city?:string|null; client?:string|null; year?:number|string|null;
  cover_image?:string|null; excerpt?:string|null; body?:string|null; media?:MediaItem[]|null
}
const props = defineProps<{ item: Project, related: Array<Pick<Project,'id'|'slug'|'title'|'cover_image'|'category'|'city'>> }>()
const coverUrl = computed(()=> props.item.cover_image ? `/storage/${props.item.cover_image}` : null)
const images   = computed(()=> (props.item.media ?? []).filter(m=>m.type==='image' && m.path))
const videos   = computed(()=> (props.item.media ?? []).filter(m=>m.type==='video' && (m.url || m.path)))
</script>

<template>
  <div class="theme-sand max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 md:pb-16">
    <!-- HERO -->
    <section class="hero" v-reveal>
      <div class="frame">
        <img v-if="coverUrl" :src="coverUrl" alt="" class="img">
        <div v-else class="noimg">Aucune image</div>
      </div>
      <div class="veil"></div>

      <div class="meta">
        <nav class="crumb">
          <Link :href="route('home')" class="hover:text-bk-gold">Accueil</Link>
          <span>›</span>
          <Link :href="route('public.projects')" class="hover:text-bk-gold">Réalisations</Link>
        </nav>
        <h1 class="headline">{{ props.item.title }}</h1>
        <div class="pillrow">
          <span v-if="props.item.category" class="pill">{{ props.item.category }}</span>
          <span v-if="props.item.city" class="pill">{{ props.item.city }}</span>
          <span v-if="props.item.client" class="pill">Client : {{ props.item.client }}</span>
          <span v-if="props.item.year" class="pill">Année : {{ props.item.year }}</span>
        </div>
      </div>
    </section>

    <!-- STRIP KPIs -->
    <section class="mt-6" v-reveal>
      <div class="strip">
        <div class="kpi"><div class="radial" style="--val:95"></div><div><div class="k">Qualité</div><div class="d">Contrôles & essais</div></div></div>
        <div class="kpi"><div class="radial" style="--val:90"></div><div><div class="k">Délais</div><div class="d">Phasage maîtrisé</div></div></div>
        <div class="kpi"><div class="radial" style="--val:100"></div><div><div class="k">HSE</div><div class="d">Zéro incident</div></div></div>
      </div>
    </section>

    <!-- CONTENT -->
    <section class="mt-8 grid lg:grid-cols-12 gap-6">
      <!-- article -->
      <article class="lg:col-span-8 glass ring-gold p-5 md:p-7" v-reveal>
        <p v-if="props.item.excerpt" class="lead">{{ props.item.excerpt }}</p>

        <div v-if="props.item.body" class="prose prose-invert max-w-none mt-4">
          <div v-html="props.item.body"></div>
        </div>

        <!-- galerie -->
        <div v-if="images.length" class="mt-6">
          <h2 class="headline sm mb-3">Galerie</h2>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <a v-for="(m,i) in images" :key="i" :href="`/storage/${m.path}`" target="_blank" rel="noopener"
               class="block rounded-lg overflow-hidden ring-1 ring-white/10 bg-white/5 hover:ring-bk-gold/50">
              <img :src="`/storage/${m.path}`" alt="" class="w-full h-40 object-cover">
            </a>
          </div>
        </div>

        <!-- vidéos -->
        <div v-if="videos.length" class="mt-6">
          <h2 class="headline sm mb-3">Vidéos</h2>
          <div class="grid sm:grid-cols-2 gap-4">
            <template v-for="(v,i) in videos" :key="i">
              <iframe v-if="v.url && /(youtube|vimeo)/i.test(v.url)"
                      class="w-full aspect-video rounded-xl ring-1 ring-white/10"
                      :src="v.url.replace('watch?v=','embed/')" allowfullscreen></iframe>
              <a v-else :href="v.url ? v.url : (`/storage/${v.path}`)" target="_blank" rel="noopener"
                 class="block rounded-xl ring-1 ring-white/10 bg-white/5 p-4 hover:ring-bk-gold/50">
                Ouvrir la vidéo
              </a>
            </template>
          </div>
        </div>
      </article>

      <!-- aside -->
      <aside class="lg:col-span-4 space-y-4" v-reveal>
        <div class="glass ring-gold p-5 md:p-6">
          <div class="dim text-sm">Informations</div>
          <dl class="specs">
            <div v-if="props.item.category"><dt>Catégorie</dt><dd>{{ props.item.category }}</dd></div>
            <div v-if="props.item.city"><dt>Ville</dt><dd>{{ props.item.city }}</dd></div>
            <div v-if="props.item.client"><dt>Client</dt><dd>{{ props.item.client }}</dd></div>
            <div v-if="props.item.year"><dt>Année</dt><dd>{{ props.item.year }}</dd></div>
          </dl>
          <Link :href="route('home') + '#rfp'" class="mt-4 btn-outline-gold w-full justify-center">Demander un devis</Link>
        </div>

        <div class="glass ring-gold p-5 md:p-6">
          <h3 class="headline sm">Portée du projet</h3>
          <ul class="mt-2 space-y-2 text-white/85 text-sm">
            <li>• Études & variantes</li>
            <li>• Exécution & contrôle qualité</li>
            <li>• Sécurité & environnement</li>
            <li>• Réception & DOE</li>
          </ul>
        </div>
      </aside>
    </section>

    <!-- RELATED -->
    <section v-if="related.length" class="mt-10" v-reveal>
      <div class="grid-head">
        <h2 class="headline sm">Réalisations similaires</h2>
        <Link :href="route('public.projects')" class="btn-ghost">Toutes les réalisations</Link>
      </div>
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <Link v-for="r in related" :key="r.id" :href="route('public.projects.show', r.slug ?? r.id)" class="ref-pro group">
          <div class="grain" aria-hidden="true"></div>
          <img v-if="r.cover_image" :src="`/storage/${r.cover_image}`" class="img" alt="">
          <div v-else class="noimg">Aucune image</div>
          <div class="cap">
            <div class="chips">
              <span v-if="r.category" class="chip">{{ r.category }}</span>
              <span v-if="r.city" class="chip mute">{{ r.city }}</span>
            </div>
            <div class="t line-clamp-2">{{ r.title }}</div>
          </div>
          <div class="overlay"><span class="btn-outline-gold">Voir</span></div>
        </Link>
      </div>
    </section>
  </div>
</template>

<style scoped>
/* Reveal */
.reveal{opacity:0;transform:translateY(12px) scale(.985);filter:blur(2px);transition:all .6s cubic-bezier(.2,.8,.2,1)}
.reveal.is-visible{opacity:1;transform:none;filter:none}

/* theme */
.theme-sand{ --tx:#E9E2CC; --tx-strong:#F5EED5; --tx-dim:rgba(233,226,204,.8); --tx-mute:rgba(233,226,204,.6); color:var(--tx) }
.headline{ color:var(--tx-strong); font-weight:800; letter-spacing:-.02em; font-size:clamp(1.8rem,1.2rem+1.6vw,2.3rem) }
.headline.sm{ font-size:1.25rem } .dim{ color:var(--tx-dim) } .mute{ color:var(--tx-mute) }
.glass{ background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:1.1rem; box-shadow:0 30px 80px -40px rgba(0,0,0,.55) }

/* hero */
.hero{ position:relative; overflow:hidden; border-radius:1.3rem; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.04) }
.frame{ aspect-ratio:16/4; background:rgba(255,255,255,.05) }
.img{ width:100%; height:100%; object-fit:cover } .noimg{ width:100%; height:100%; display:grid; place-items:center; color:var(--tx-mute) }
.veil{ position:absolute; inset:0; background:linear-gradient(180deg, rgba(15,20,26,.08), rgba(15,20,26,.72) 70%) }
.meta{ position:absolute; left:0; right:0; bottom:0; padding:1.1rem 1.2rem; backdrop-filter:blur(6px);
  background:linear-gradient(180deg, rgba(21,30,39,.25), rgba(21,30,39,.55)); border-top:1px solid rgba(255,255,255,.1) }
.crumb{ display:flex; gap:.45rem; font-size:.85rem; color:var(--tx-dim); margin-bottom:.35rem }
.pillrow{ display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.35rem }
.pill{ padding:.3rem .55rem; border-radius:.55rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.14) }

/* Strip KPI */
.strip{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.6rem }
.kpi{ display:flex; align-items:center; gap:.7rem; padding:.7rem .8rem; border-radius:.9rem; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.12) }
.radial{ --val:92; width:52px; height:52px; border-radius:999px; display:grid; place-items:center;
  background:conic-gradient(#dcc176 calc(var(--val)*1%), rgba(255,255,255,.2) 0), radial-gradient(farthest-side, rgba(21,30,39,1) 60%, transparent 61%);
  box-shadow:inset 0 0 0 1px rgba(255,255,255,.12) }
.k{ font-weight:900 } .d{ color:var(--tx-dim); font-size:.85rem }

/* specs */
.specs{ margin-top:.6rem; display:grid; gap:.35rem }
.specs>div{ display:flex; justify-content:space-between; gap:.6rem; padding:.45rem .55rem; border-radius:.6rem; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.12) }
.specs dt{ color:var(--tx-dim) } .specs dd{ color:var(--tx) }

/* ref card */
.ref-pro{ position:relative; height:240px; border-radius:1rem; overflow:hidden; border:1px solid rgba(255,255,255,.12);
  background:rgba(255,255,255,.04); transition:.2s }
.ref-pro:hover{ transform:translateY(-2px); border-color:rgba(220,193,118,.55); box-shadow:0 36px 120px -46px rgba(0,0,0,.65) }
.cap{ position:absolute; inset-inline:0; bottom:0; padding:1rem; background:linear-gradient(180deg,transparent,rgba(15,20,26,.86)) }
.t{ font-weight:800; text-shadow:0 1px 0 rgba(0,0,0,.25) }
.chips{ display:flex; gap:.35rem; margin-bottom:.25rem }
.chip{ font-size:.7rem; padding:.25rem .45rem; border-radius:.5rem; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.18) }
.overlay{ position:absolute; inset:0; display:grid; place-items:center; background:rgba(0,0,0,.34); opacity:0; transition:.2s }
.ref-pro:hover .overlay{ opacity:1 }
.grain{ position:absolute; inset:0; opacity:.12; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'%3E%3Cfilter id='a'%3E%3CfeTurbulence baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23a)' opacity='.25'/%3E%3C/svg%3E") }
.grid-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:.6rem }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.7rem 1rem; border-radius:1rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.1rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude }
.ring-gold{ position:relative } .ring-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude }
</style>
