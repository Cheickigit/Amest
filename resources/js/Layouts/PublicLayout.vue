<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { route as ziggyRoute } from 'ziggy-js' // <- export nommé v2

// ---- types pour usePage() ----
type PageAuth = { user?: Record<string, unknown> | null }
type PageProps = { auth?: PageAuth }

const page = usePage<PageProps>()
const user = computed(() => page.props.auth?.user ?? null)

// Helper route() pour le template
const route = (name: string, params?: any, absolute?: boolean) =>
  ziggyRoute(name, params, absolute, (window as any).Ziggy)

// ---- état UI ----
const open = ref(false)
const scrolled = ref(false)
const progress = ref(0)
const phoneDisplay = ref('+212 5 22 00 00 00')
const phoneHref    = ref('tel:+212522000000')
const whatsappHref = ref('https://wa.me/212600000000')
const email        = ref('contact@bkconstruction.ma')
const city         = ref('Casablanca, Maroc')

function onScroll(){
  scrolled.value = window.scrollY > 8
  const h = document.documentElement.scrollHeight - window.innerHeight
  progress.value = h > 0 ? Math.min(1, window.scrollY / h) : 0
}
function onResize(){ if (window.innerWidth >= 1024) open.value = false }
function onMouse(e: MouseEvent){
  const x = e.clientX / window.innerWidth
  const y = e.clientY / window.innerHeight
  document.documentElement.style.setProperty('--mx', String(x))
  document.documentElement.style.setProperty('--my', String(y))
}

onMounted(() => {
  onScroll()
  window.addEventListener('scroll', onScroll, { passive:true })
  window.addEventListener('resize', onResize)
  window.addEventListener('mousemove', onMouse)
})
onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll)
  window.removeEventListener('resize', onResize)
  window.removeEventListener('mousemove', onMouse)
})
</script>

<template>
  <div :class="['min-h-screen bg-bk-night text-bk-off relative overflow-x-hidden', scrolled ? 'shrink' : '']">
    <div aria-hidden="true" class="fixed inset-0 -z-10 pointer-events-none">
      <div class="bk-grid"></div>
      <div class="atlas-aurora a"></div>
      <div class="atlas-aurora b"></div>
      <div class="atlas-aurora c"></div>
      <div class="atlas-grain"></div>
      <div class="spotlight"></div>
    </div>

    <div class="progress-line" :style="`transform:scaleX(${progress})`"></div>

    <div class="hidden lg:block bg-[rgba(15,20,26,0.75)] backdrop-blur-md border-b border-white/10 text-white/85">
      <div class="max-w-7xl mx-auto h-10 px-4 sm:px-6 lg:px-8 flex items-center justify-between text-[13px]">
        <div class="flex items-center gap-6">
          <span class="chip-ma">MA</span>
          <span class="text-white/85">Entreprise marocaine de BTP — Qualité • Sécurité • Délais</span>

          <a class="hover:text-bk-gold inline-flex items-center gap-2" :href="phoneHref">
            <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>
            <span>{{ phoneDisplay }}</span>
          </a>

          <a class="hover:text-bk-gold inline-flex items-center gap-2" :href="'mailto:' + email">
            <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M20 8l-8 5-8-5V6l8 5 8-5v2z"/><path fill="currentColor" d="M20 18H4v-2h16v2z"/></svg>
            <span>{{ email }}</span>
          </a>

          <span class="inline-flex items-center gap-2 text-white/70">
            <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>
            <span>{{ city }}</span>
          </span>
        </div>

        <div class="flex items-center gap-3">
          <a :href="whatsappHref" target="_blank" rel="noopener" class="hover:text-bk-gold inline-flex items-center gap-1">
            <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M20 3.5A10 10 0 0012 1a10 10 0 00-8.66 15.07L2 23l7.12-1.87A10 10 0 0022 11a10 10 0 00-2-7.5zM12 19a8 8 0 118-8 8 8 0 01-8 8z"/></svg>
            WhatsApp
          </a>
          <span class="text-white/40">|</span>
          <button class="hover:text-bk-gold">FR</button>
          <span class="text-white/40">/</span>
          <button class="hover:text-bk-gold">AR</button>
        </div>
      </div>
    </div>

    <header
      class="sticky top-0 z-50 transition-all border-b mobile-header"
      :class="scrolled ? 'border-white/15 py-1 shrink' : 'border-white/10 py-2'"
    >
      <div class="max-w-7xl mx-auto h-18 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <Link :href="route('home')" class="flex items-center">
          <img src="/assets/logo-bk.jpeg" class="brand-logo" alt="BK Construction logo">
        </Link>

        <nav class="hidden md:flex items-center gap-1 rounded-2xl px-1 py-1 bg-white/5 border border-white/10">
          <a href="#services" class="nav-pill">Services</a>
          <a href="#projects" class="nav-pill">Réalisations</a>
          <a href="#process"  class="nav-pill">Méthode</a>
          <a href="#rfp"      class="nav-pill">Appels d’offres</a>
          <a href="#contact"  class="nav-pill">Contact</a>
        </nav>

        <div class="hidden md:flex items-center gap-3">
          <a href="#devis" class="btn-gold"><span class="btn-glow"></span><span class="btn-shine"></span>Demander un devis</a>
          <Link v-if="!user" :href="route('login')" class="btn-ghost">Se connecter</Link>
          <Link v-else :href="route('dashboard')" class="btn-ghost">Espace</Link>
        </div>

        <button
          class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl
                 bg-white/12 text-white/95 border border-white/25
                 hover:border-bk-gold/70 shadow-[0_12px_30px_-18px_rgba(0,0,0,.6)]"
          @click="open = !open" :aria-expanded="open" aria-controls="mobile-drawer"
        >
          <span class="sr-only">Ouvrir le menu</span>
          <svg v-if="!open" viewBox="0 0 24 24" class="w-6 h-6"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
          <svg v-else viewBox="0 0 24 24" class="w-6 h-6"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
        </button>
      </div>

      <div v-show="open" class="fixed inset-0 z-[60] bg-black/55 backdrop-blur-sm" @click="open=false"></div>

      <div v-show="open" id="mobile-drawer" role="dialog" aria-modal="true" class="fixed top-2 inset-x-2 z-[61]">
        <div class="relative rounded-2xl border border-white/15 bg-gradient-to-b from-white/10 to-white/[0.03] shadow-[0_30px_80px_-20px_rgba(0,0,0,.6)] overflow-hidden" @click.stop>
          <div class="pointer-events-none absolute -top-20 -left-14 w-80 h-80 rounded-full blur-3xl opacity-40 bg-bk-gold/40"></div>

          <div class="flex items-center justify-between px-4 pt-3 pb-2">
            <img src="/assets/logo-bk.jpeg" class="h-10 w-auto object-contain" alt="BK Construction">
            <button class="p-2 rounded-lg bg-white/10 border border-white/15 hover:border-bk-gold/60" @click="open=false" aria-label="Fermer">
              <svg viewBox="0 0 24 24" class="w-6 h-6"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
          </div>

          <div class="grid grid-cols-2 gap-2 px-3 pb-3">
            <a href="#services" class="group tile" @click="open=false"><span>Services</span></a>
            <a href="#projects" class="group tile" @click="open=false"><span>Réalisations</span></a>
            <a href="#process"  class="group tile" @click="open=false"><span>Méthode</span></a>
            <a href="#rfp"      class="group tile" @click="open=false"><span>Appels d’offres</span></a>
            <a href="#contact"  class="group tile" @click="open=false"><span>Contact</span></a>
          </div>

          <div class="px-3 pb-3 grid gap-2">
            <a href="#devis" class="btn-gold w-full text-center" @click="open=false">
              <span class="btn-glow"></span><span class="btn-shine"></span>Devis express
            </a>
            <div class="flex gap-2">
              <Link v-if="!user" :href="route('login')" class="btn-ghost w-full text-center" @click="open=false">Se connecter</Link>
              <Link v-else :href="route('dashboard')" class="btn-ghost w-full text-center" @click="open=false">Espace</Link>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main id="page-content"><slot /></main>

    <footer class="mt-16 text-sm relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-14">
        <div class="footer-card gradient-ring">
          <div class="glow g1"></div><div class="glow g2"></div><div class="glow g3"></div>

          <div class="footer-content">
            <div class="col brand">
              <img src="/assets/logo-bk.jpeg" class="footer-logo" alt="BK Construction">
              <p class="text-bk-off/75">Entreprise marocaine de BTP : routes, bâtiments, VRD, génie civil & immobilier.</p>
              <div class="actions">
                <a :href="whatsappHref" target="_blank" rel="noopener" class="icon-btn" aria-label="WhatsApp"><svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M20 3.5A10 10 0 0012 1a10 10 0 00-8.66 15.07L2 23l7.12-1.87A10 10 0 0022 11a10 10 0 00-2-7.5zM12 19a8 8 0 118-8 8 8 0 01-8 8z"/></svg></a>
                <a :href="phoneHref" class="icon-btn" aria-label="Appeler"><svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg></a>
                <a :href="'mailto:' + email" class="icon-btn" aria-label="Email"><svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M20 8l-8 5-8-5V6l8 5 8-5v2z"/><path fill="currentColor" d="M20 18H4v-2h16v2z"/></svg></a>
              </div>
            </div>

            <div class="col">
              <div class="footer-title">Services</div>
              <ul class="list">
                <li><a href="#services">Routes & VRD</a></li>
                <li><a href="#services">Bâtiments</a></li>
                <li><a href="#services">Immobilier</a></li>
                <li><a href="#services">Rénovation</a></li>
                <li><a href="#services">Génie civil</a></li>
              </ul>
            </div>

            <div class="col">
              <div class="footer-title">Secteurs</div>
              <ul class="list">
                <li><a href="#">Public & Collectivités</a></li>
                <li><a href="#">Immobilier privé</a></li>
                <li><a href="#">Industrie & Logistique</a></li>
                <li><a href="#">Infrastructures urbaines</a></li>
              </ul>
            </div>

            <div class="col">
              <div class="footer-title">Ressources</div>
              <ul class="list">
                <li><a href="#projects">Réalisations</a></li>
                <li><a href="#rfp">Appels d’offres</a></li>
                <li><a href="/terms">Conditions générales</a></li>
                <li><a href="/privacy">Politique de confidentialité</a></li>
              </ul>
            </div>

            <div class="col">
              <div class="footer-title">Contact</div>
              <ul class="list mb-3">
                <li><a :href="phoneHref">Tél : <span>{{ phoneDisplay }}</span></a></li>
                <li><a :href="'mailto:' + email">{{ email }}</a></li>
                <li class="text-bk-off/70">{{ city }}</li>
              </ul>

              <form class="newsletter w-full" @submit.prevent>
                <input type="email" placeholder="Votre email" aria-label="Votre email" />
                <button type="button">S’abonner</button>
              </form>
              <p class="mt-2 text-xs text-bk-off/60">
                En vous abonnant, vous acceptez nos <a href="/privacy" class="underline hover:text-bk-gold">conditions</a>.
              </p>
            </div>
          </div>

          <div class="footer-bottom">
            <div>
              © {{ new Date().getFullYear() }}
              <a href="https://1clickmali.com" target="_blank" rel="noopener" class="font-semibold hover:text-bk-gold">1Click Mali</a>.
              Designed by
              <a href="https://zehawitech.com" target="_blank" rel="noopener" class="font-semibold hover:text-bk-gold">ZEHAWI TECHNOLOGIES SARL</a>.
            </div>
            <div class="text-bk-off/70">
              RC : <span class="text-bk-off/90">xxxxx</span> · ICE : <span class="text-bk-off/90">xxxxxxxxxxxx</span> · IF : <span class="text-bk-off/90">xxxxxxxx</span> · Patente : <span class="text-bk-off/90">xxxxxxx</span>
            </div>
            <div class="links">
              <a :href="whatsappHref" target="_blank" rel="noopener">WhatsApp</a>
              <a :href="phoneHref">Appel</a>
              <a :href="'mailto:' + email">Email</a>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <a :href="whatsappHref" target="_blank" rel="noopener"
       class="fixed bottom-5 right-5 inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/10 border border-white/10 hover:border-bk-gold/60 backdrop-blur">
      <svg viewBox="0 0 24 24" class="w-6 h-6"><path fill="currentColor" d="M20 3.5A10 10 0 0012 1a10 10 0 00-8.66 15.07L2 23l7.12-1.87A10 10 0 0022 11a10 10 0 00-2-7.5zM12 19a8 8 0 118-8 8 8 0 01-8 8z"/></svg>
    </a>
  </div>
</template>

<style scoped>
.tile{
  display:flex; align-items:center; justify-content:center; min-height:3.2rem;
  border-radius:.9rem; font-weight:700; color:#f6f7fb; letter-spacing:.2px;
  background:linear-gradient(160deg, rgba(255,255,255,.08), rgba(255,255,255,.03));
  border:1px solid rgba(255,255,255,.14);
  transition: border-color .2s ease;
}
.tile:hover{ border-color:rgba(220,193,118,.6) }
</style>
