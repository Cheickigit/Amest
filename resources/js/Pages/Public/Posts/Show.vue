<!-- resources/js/Pages/Public/Posts/Show.vue -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed, ref, onMounted } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
defineOptions({ layout: PublicLayout })
const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(globalThis as any).Ziggy)

/* Helper route robuste (Ziggy + fallback) */
function r(name: string, params?: any, absolute = false, fallback: string = '#') {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (!Z) return fallback
    return ziggyRoute(name, params ?? {}, absolute, Z)
  } catch { return fallback }
}

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

type Post = { id:number; slug?:string|null; title:string; excerpt?:string|null; body?:string|null; cover_image?:string|null; published_at?:string|null; tags?:string[]|null }
const props = defineProps<{ item: Post, more: Array<Pick<Post,'id'|'slug'|'title'|'cover_image'|'published_at'>> }>()
const cover = computed(()=> props.item.cover_image ? `/storage/${props.item.cover_image}` : null)
const fmt = (d?:string|null)=> d ? new Date(d).toLocaleDateString('fr-FR',{year:'numeric',month:'long',day:'2-digit'}) : ''

/* Partage (SSR-safe) */
const shareUrl = ref<string>('')               // évite window dans le template
onMounted(()=>{ if (typeof window !== 'undefined') shareUrl.value = window.location.href })
const mailHref = computed(()=> `mailto:?subject=${encodeURIComponent(props.item.title)}&body=${encodeURIComponent(shareUrl.value)}`)
const fbHref   = computed(()=> `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl.value)}`)
const liHref   = computed(()=> `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl.value)}`)
</script>

<template>
  <div class="theme-sand max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 md:pb-16">
    <!-- HERO -->
    <section class="hero" v-reveal>
      <div class="frame">
        <img v-if="cover" :src="cover" alt="" class="img">
        <div v-else class="noimg">Aucune image</div>
      </div>
      <div class="veil"></div>

      <div class="meta">
        <nav class="crumb">
          <Link :href="route('home')" class="hover:text-bk-gold">Accueil</Link>
          <span>›</span>
          <Link :href="route('public.posts')" class="hover:text-bk-gold">Actualités</Link>
        </nav>
        <h1 class="headline">{{ props.item.title }}</h1>
        <div class="dim">{{ fmt(props.item.published_at) }}</div>
      </div>
    </section>

    <section class="mt-8 grid lg:grid-cols-12 gap-6">
      <!-- Article -->
      <article class="lg:col-span-8 glass ring-gold content" v-reveal>
        <p v-if="props.item.excerpt" class="lead">{{ props.item.excerpt }}</p>

        <div v-if="props.item.body" class="prose prose-invert max-w-none mt-4">
          <div v-html="props.item.body"></div>
        </div>

        <div v-if="(props.item.tags ?? []).length" class="tags">
          <span v-for="(t,i) in props.item.tags" :key="i" class="tag">#{{ t }}</span>
        </div>
      </article>

      <!-- Sidebar -->
      <aside class="lg:col-span-4 space-y-4">
        <div class="glass ring-gold p-4 md:p-5" v-reveal>
          <h3 class="h3">Partager</h3>
          <div class="mt-3 grid grid-cols-3 gap-2">
            <a :href="mailHref" class="btn-ghost sm">Email</a>
            <a :href="fbHref" target="_blank" rel="noopener" class="btn-ghost sm">Facebook</a>
            <a :href="liHref" target="_blank" rel="noopener" class="btn-ghost sm">LinkedIn</a>
          </div>
        </div>

        <div class="glass ring-gold p-4 md:p-5" v-reveal>
          <h3 class="h3">Derniers articles</h3>
          <ul class="mt-3 space-y-3">
            <li v-for="p in more.slice(0,5)" :key="p.id">
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
          <h3 class="h3">Besoin d’un chiffrage ?</h3>
          <p class="text-white/80 text-sm mt-2">VRD, Bâtiments, Génie civil — devis express avec variantes & phasage.</p>
          <div class="mt-3">
            <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Appels d’offres / Devis</Link>
          </div>
        </div>
      </aside>
    </section>

    <!-- MORE -->
    <section v-if="more.length" class="mt-10" v-reveal>
      <div class="grid-head">
        <h2 class="headline sm">À lire aussi</h2>
        <Link :href="route('public.posts')" class="btn-ghost">Toutes les actualités</Link>
      </div>

      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <Link v-for="p in more" :key="p.id" :href="route('public.posts.show', p.slug ?? p.id)" class="card-pro">
          <div class="thumb">
            <img v-if="p.cover_image" :src="`/storage/${p.cover_image}`" class="img" alt="">
            <div v-else class="noimg">Aucune image</div>
            <div class="grad" />
          </div>
          <div class="body">
            <div class="dim text-[12px]">{{ fmt(p.published_at) }}</div>
            <h3 class="title line-clamp-2">{{ p.title }}</h3>
            <span class="cta">Lire</span>
          </div>
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
.headline{ color:var(--tx-strong); font-weight:800; letter-spacing:-.02em; font-size:clamp(1.8rem,1.3rem+1.5vw,2.2rem) }
.headline.sm{ font-size:1.25rem }
.dim{ color:var(--tx-dim) } .mute{ color:var(--tx-mute) }

/* hero */
.hero{ position:relative; overflow:hidden; border-radius:1.3rem; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.04) }
.frame{ aspect-ratio:16/4; background:rgba(255,255,255,.05) }
.img{ width:100%; height:100%; object-fit:cover }
.noimg{ width:100%; height:100%; display:grid; place-items:center; color:var(--tx-mute) }
.veil{ position:absolute; inset:0; background:linear-gradient(180deg, rgba(15,20,26,.1), rgba(15,20,26,.72) 70%) }
.meta{ position:absolute; left:0; right:0; bottom:0; padding:1.1rem 1.2rem; backdrop-filter: blur(6px);
  background:linear-gradient(180deg, rgba(21,30,39,.25), rgba(21,30,39,.55)); border-top:1px solid rgba(255,255,255,.1) }
.crumb{ font-size:.85rem; color:var(--tx-dim); margin-bottom:.3rem; display:flex; gap:.45rem; align-items:center }

/* content */
.glass{ background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:1.1rem; box-shadow:0 30px 80px -40px rgba(0,0,0,.55) }
.content{ padding:1.1rem 1.25rem 1.25rem; }
.lead{ font-size:1.05rem; color:var(--tx); line-height:1.6 }
.tags{ margin-top:1rem; display:flex; gap:.5rem; flex-wrap:wrap }
.tag{ padding:.35rem .6rem; border-radius:.6rem; font-size:.85rem; color:var(--tx);
  background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.14) }

/* reused cards (more) */
.card-pro{ display:block; overflow:hidden; border-radius:1rem; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.04);
  transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease }
.card-pro:hover{ transform:translateY(-2px); border-color:rgba(220,193,118,.55); box-shadow:0 36px 120px -46px rgba(0,0,0,.65) }
.thumb{ position:relative; aspect-ratio:16/9; background:rgba(255,255,255,.05); overflow:hidden }
.grad{ position:absolute; inset:0; background:linear-gradient(180deg,transparent,rgba(15,20,26,.78)) }
.body{ padding:.85rem .95rem 1rem } .title{ font-weight:800; color:var(--tx) } .cta{ margin-top:.4rem; display:inline-block; color:var(--tx) }

/* sidebar */
.h3{ font-weight:900 }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.65rem .9rem; border-radius:.9rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.1rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude }
.side-item{ display:flex; gap:.6rem; align-items:center }
.thumb-s{ width:66px; height:46px; border-radius:.5rem; overflow:hidden; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12) }
.img-s{ width:100%; height:100%; object-fit:cover } .noimg-s{ width:100%; height:100%; display:grid; place-items:center; color:var(--tx-mute) }
.title-s{ font-weight:700 } .date-s{ font-size:.8rem; color:var(--tx-mute) }

.grid-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:.6rem }
</style>
