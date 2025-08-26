<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

/* ---- types pour usePage() ---- */
type PageAuth = { user?: Record<string, unknown> | null }
type PageProps = { auth?: PageAuth }

const page = usePage<PageProps>()
const user = computed(() => page.props.auth?.user ?? null)

/* ---- helper route() ---- */
const route = (name: string, params?: any, absolute?: boolean) =>
  ziggyRoute(name, params, absolute, (window as any).Ziggy)

/* ---- état UI ---- */
const open = ref(false)
const scrolled = ref(false)
const progress = ref(0)

/* smart hide du topbar */
const SMART_HIDE = true
const topbarPinned = ref(true)
const lastY = ref(0)
const lastDeltaDown = ref(false)
const headerHover = ref(false)

/* ---- INFOS réelles BK Construction (Tanger) ---- */
const phoneDisplay = ref('+212 7 70 55 60 21')
const phoneHref    = ref('tel:+212770556021')
const whatsappHref = ref('https://wa.me/212770556021') // même numéro
const email        = ref('contact@bkconstruction.ma')
const city         = ref('Tanger, Maroc')

/* ui misc */
const showUp = ref(false)
function scrollToTop(){ window.scrollTo({ top: 0, behavior: 'smooth' }) }

function onScroll(){
  const y = window.scrollY
  scrolled.value = y > 8
  showUp.value = y > 320

  const h = document.documentElement.scrollHeight - window.innerHeight
  progress.value = h > 0 ? Math.min(1, y / h) : 0

  if (SMART_HIDE) {
    const delta = y - lastY.value
    if (Math.abs(delta) > 2) {
      if (delta > 0 && y > 64) { topbarPinned.value = false; lastDeltaDown.value = true }
      else if (delta < 0)      { topbarPinned.value = true;  lastDeltaDown.value = false }
    }
  } else {
    topbarPinned.value = true
  }
  lastY.value = y
}

function onResize(){ if (window.innerWidth >= 1024) open.value = false }
function onMouse(e: MouseEvent){
  const x = e.clientX / window.innerWidth
  const y = e.clientY / window.innerHeight
  document.documentElement.style.setProperty('--mx', String(x))
  document.documentElement.style.setProperty('--my', String(y))
}

function onHeaderEnter(){ headerHover.value = true; topbarPinned.value = true }
function onHeaderLeave(){
  headerHover.value = false
  if (SMART_HIDE && lastDeltaDown.value && window.scrollY > 64) topbarPinned.value = false
}

onMounted(() => {
  lastY.value = window.scrollY
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
  <div :class="['min-h-screen bg-bk-night text-bk-off relative overflow-x-hidden', open ? 'overflow-hidden' : '']">
    <!-- BACKDROP -->
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10">
      <div class="absolute inset-0 opacity-[.06] mix-blend-overlay
                  bg-[length:36px_36px,36px_36px]
                  bg-[linear-gradient(to_right,rgba(255,255,255,.2)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,.2)_1px,transparent_1px)]"></div>
      <div class="absolute -top-[12%] -left-[15%] w-[50vw] h-[45vh] blur-[70px] mix-blend-screen
                  bg-[radial-gradient(60%_60%_at_30%_30%,#dcc176_0%,transparent_60%)] opacity-[.55]"></div>
      <div class="absolute -bottom-[20%] -right-[10%] w-[55vw] h-[55vh] blur-[70px] mix-blend-screen
                  bg-[radial-gradient(60%_60%_at_70%_70%,#fdfdfe_0%,transparent_60%)] opacity-20"></div>
      <div class="absolute top-[25%] right-[35%] w-[34vw] h-[34vh] blur-[70px] mix-blend-screen
                  bg-[conic-gradient(from_90deg_at_50%_50%,rgba(220,193,118,.55),rgba(253,253,254,.12),rgba(220,193,118,.55))] opacity-20"></div>
      <div class="absolute inset-0 pointer-events-none
                  [background:radial-gradient(350px_350px_at_calc(var(--mx)*100%)_calc(var(--my)*100%),rgba(255,255,255,.06),transparent_60%)]"></div>
    </div>

    <!-- barre de progression -->
    <div class="fixed top-0 left-0 h-[2.5px] w-full origin-left transform-gpu
                bg-gradient-to-r from-bk-gold to-white/70 z-[60]"
         :style="`transform:scaleX(${progress})`"></div>

    <!-- HEADER FIXE -->
    <div
      class="fixed top-0 inset-x-0 z-50 border-b transition-all duration-300 ease-out
             backdrop-blur supports-[backdrop-filter]:bg-[#151e27]/80 bg-[#151e27]/92
             h-[76px] lg:h-[108px]"
      :class="scrolled ? 'border-white/15 shadow-[0_10px_40px_-30px_rgba(0,0,0,.8)]' : 'border-white/10'"
      @mouseenter="onHeaderEnter" @mouseleave="onHeaderLeave"
    >
      <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- TOPBAR -->
        <div
          class="absolute top-0 left-0 right-0 hidden lg:flex h-10 items-center justify-between
                 text-[13px] text-white/85 border-b border-white/10
                 transition-all duration-300 ease-out bg-gradient-to-b from-white/5 to-transparent"
          :class="topbarPinned ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-[calc(100%+1px)]'">
          <div class="flex items-center gap-6">
            <span class="inline-flex items-center gap-2 rounded-md px-2 py-1 font-extrabold text-[12px]
                         text-white bg-white/10 ring-1 ring-white/20 shadow-inner">MA</span>
            <span class="text-white/85">Entreprise de BTP à Tanger — Qualité • Sécurité • Délais</span>

            <a :href="phoneHref" class="inline-flex items-center gap-2 hover:text-bk-gold">
              <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>
              <span>{{ phoneDisplay }}</span>
            </a>

            <a :href="'mailto:' + email" class="inline-flex items-center gap-2 hover:text-bk-gold">
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

        <!-- petite barre "peek" quand le topbar est caché -->
        <div class="absolute top-0 left-0 right-0 hidden lg:block h-1.5
                    bg-gradient-to-r from-bk-gold/40 via-white/20 to-bk-gold/40
                    rounded-b-full transition-all duration-300"
             :class="topbarPinned ? 'opacity-0 -translate-y-3' : 'opacity-100 translate-y-0'"></div>

        <!-- NAVBAR -->
        <div class="absolute inset-x-0 bottom-0 flex items-center justify-between
                    h-[76px] lg:transition-[height] lg:duration-300"
             :class="topbarPinned ? 'lg:h-[66px]' : 'lg:h-[108px]'">
          <Link :href="route('home')" class="flex items-center">
            <img :class="['object-contain transition-all duration-300', topbarPinned ? 'h-10 md:h-12' : 'h-12 md:h-14']"
                 src="/assets/logo-bk.jpeg" alt="BK Construction logo">
          </Link>

          <nav class="hidden md:flex items-center gap-1 rounded-2xl px-1 py-1 bg-white/5 ring-1 ring-white/10">
            <a href="#services" class="group relative rounded-full px-4 py-2 text-white/90 transition hover:text-white hover:-translate-y-0.5 ring-1 ring-transparent hover:ring-bk-gold/40">Services</a>
            <a href="#projects" class="group relative rounded-full px-4 py-2 text-white/90 transition hover:text-white hover:-translate-y-0.5 ring-1 ring-transparent hover:ring-bk-gold/40">Réalisations</a>
            <a href="#process"  class="group relative rounded-full px-4 py-2 text-white/90 transition hover:text-white hover:-translate-y-0.5 ring-1 ring-transparent hover:ring-bk-gold/40">Méthode</a>
            <a href="#rfp"      class="group relative rounded-full px-4 py-2 text-white/90 transition hover:text-white hover:-translate-y-0.5 ring-1 ring-transparent hover:ring-bk-gold/40">Appels d’offres</a>
            <a href="#contact"  class="group relative rounded-full px-4 py-2 text-white/90 transition hover:text-white hover:-translate-y-0.5 ring-1 ring-transparent hover:ring-bk-gold/40">Contact</a>
          </nav>

          <div class="hidden md:flex items-center gap-3">
            <a href="#devis"
               class="relative inline-flex items-center justify-center font-extrabold tracking-wide
                      rounded-xl px-4 py-2 bg-bk-gold text-bk-night shadow-[0_16px_40px_-16px_rgba(220,193,118,.45)]
                      transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-bk-gold/60">
              <span class="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/30 to-transparent
                           [mask-image:linear-gradient(90deg,transparent,black,transparent)] animate-[shine_2.8s_ease-in-out_infinite]"></span>
              Demander un devis
            </a>
            <Link v-if="!user" :href="route('login')"
                  class="inline-flex items-center justify-center rounded-xl px-4 py-2
                         border border-white/15 text-white hover:border-bk-gold/60 transition focus:outline-none focus:ring-2 focus:ring-bk-gold/50">Se connecter</Link>
            <Link v-else :href="route('dashboard')"
                  class="inline-flex items-center justify-center rounded-xl px-4 py-2
                         border border-white/15 text-white hover:border-bk-gold/60 transition focus:outline-none focus:ring-2 focus:ring-bk-gold/50">Espace</Link>
          </div>

          <button class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl
                         bg-white/10 text-white/95 border border-white/25
                         hover:border-bk-gold/70 shadow-[0_12px_30px_-18px_rgba(0,0,0,.6)] transition"
                  @click="open = !open" :aria-expanded="open" aria-controls="mobile-drawer">
            <span class="sr-only">Ouvrir le menu</span>
            <svg v-if="!open" viewBox="0 0 24 24" class="w-6 h-6"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
            <svg v-else viewBox="0 0 24 24" class="w-6 h-6"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- spacer sous header -->
    <div class="h-[76px] lg:h-[108px]" aria-hidden="true"></div>

    <!-- Overlay + Drawer mobile -->
    <div v-show="open" class="fixed inset-0 z-[60] bg-black/70 backdrop-blur-sm" @click="open=false"></div>
    <div v-show="open" id="mobile-drawer" role="dialog" aria-modal="true" class="fixed top-2 inset-x-2 z-[61]">
      <div class="relative overflow-hidden rounded-2xl border border-white/15 text-bk-off
                  shadow-[0_30px_80px_-20px_rgba(0,0,0,.7)]
                  bg-gradient-to-b from-[#0f141a]/95 to-[#0f141a]/80"
           @click.stop>
        <div class="flex items-center justify-between px-4 pt-3 pb-2">
          <img src="/assets/logo-bk.jpeg" class="h-10 w-auto object-contain" alt="BK Construction">
          <button class="p-2 rounded-lg bg-white/10 border border-white/15 hover:border-bk-gold/60 transition" @click="open=false" aria-label="Fermer">
            <svg viewBox="0 0 24 24" class="w-6 h-6"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </button>
        </div>

        <div class="grid grid-cols-2 gap-2 px-3 pb-3">
          <a href="#services" class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold
                                     bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Services</a>
          <a href="#projects" class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold
                                     bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Réalisations</a>
          <a href="#process"  class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold
                                     bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Méthode</a>
          <a href="#rfp"      class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold
                                     bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Appels d’offres</a>
          <a href="#contact"  class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold
                                     bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Contact</a>
        </div>

        <div class="px-3 pb-3 grid gap-2">
          <a href="#devis"
             class="relative inline-flex items-center justify-center font-extrabold tracking-wide
                    rounded-xl px-4 py-2 bg-bk-gold text-bk-night shadow-[0_16px_40px_-16px_rgba(220,193,118,.45)]
                    transition hover:-translate-y-0.5 text-center"
             @click="open=false">
            <span class="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/30 to-transparent
                         [mask-image:linear-gradient(90deg,transparent,black,transparent)] animate-[shine_2.8s_ease-in-out_infinite]"></span>
            Devis express
          </a>
          <div class="flex gap-2">
            <Link v-if="!user" :href="route('login')" class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2
                                       border border-white/15 text-white hover:border-bk-gold/60 transition" @click="open=false">Se connecter</Link>
            <Link v-else :href="route('dashboard')" class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2
                                       border border-white/15 text-white hover:border-bk-gold/60 transition" @click="open=false">Espace</Link>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENU -->
    <main id="page-content"><slot /></main>

    <!-- FOOTER (cohérent or subtil + verre) -->
    <footer class="mt-16 text-sm relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-14">
        <div class="relative rounded-2xl">
          <div class="absolute inset-0 -z-10 blur-[70px] opacity-30 mix-blend-screen
                      bg-[radial-gradient(60%_60%_at_20%_20%,#dcc176_0%,transparent_60%)]"></div>

          <div class="relative rounded-2xl p-[1px]
                      bg-[conic-gradient(at_30%_30%,rgba(220,193,118,.8),rgba(255,255,255,.15),rgba(220,193,118,.8))]">
            <div class="relative rounded-2xl bg-white/[.06] backdrop-blur border border-white/10
                        shadow-[0_40px_120px_-40px_rgba(0,0,0,.65)]">

              <div class="pointer-events-none absolute inset-0 rounded-2xl opacity-10
                          [background-image:radial-gradient(rgba(255,255,255,.28)_0.6px,transparent_0.6px)]
                          [background-size:6px_6px]"></div>

              <div class="relative grid gap-6 p-5 lg:grid-cols-5 lg:gap-8 lg:p-8">
                <div>
                  <img src="/assets/logo-bk.jpeg" class="h-12 w-auto object-contain mb-2" alt="BK Construction">
                  <p class="text-bk-off/80">Entreprise marocaine de BTP : routes, bâtiments, VRD, génie civil & immobilier.</p>
                  <div class="flex gap-2 mt-2">
                    <a :href="whatsappHref" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" aria-label="WhatsApp">
                      <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M20 3.5A10 10 0 0012 1a10 10 0 00-8.66 15.07L2 23l7.12-1.87A10 10 0 0022 11a10 10 0 00-2-7.5zM12 19a8 8 0 118-8 8 8 0 01-8 8z"/></svg>
                    </a>
                    <a :href="phoneHref" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" aria-label="Appeler">
                      <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>
                    </a>
                    <a :href="'mailto:' + email" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" aria-label="Email">
                      <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M20 8l-8 5-8-5V6l8 5 8-5v2z"/><path fill="currentColor" d="M20 18H4v-2h16v2z"/></svg>
                    </a>
                  </div>
                </div>

                <div>
                  <div class="font-bold text-bk-gold mb-3">Services</div>
                  <ul class="grid gap-2 text-bk-off/90">
                    <li><a href="#services" class="hover:text-bk-gold">Routes & VRD</a></li>
                    <li><a href="#services" class="hover:text-bk-gold">Bâtiments</a></li>
                    <li><a href="#services" class="hover:text-bk-gold">Immobilier</a></li>
                    <li><a href="#services" class="hover:text-bk-gold">Rénovation</a></li>
                    <li><a href="#services" class="hover:text-bk-gold">Génie civil</a></li>
                  </ul>
                </div>

                <div>
                  <div class="font-bold text-bk-gold mb-3">Secteurs</div>
                  <ul class="grid gap-2 text-bk-off/90">
                    <li><a href="#" class="hover:text-bk-gold">Public & Collectivités</a></li>
                    <li><a href="#" class="hover:text-bk-gold">Immobilier privé</a></li>
                    <li><a href="#" class="hover:text-bk-gold">Industrie & Logistique</a></li>
                    <li><a href="#" class="hover:text-bk-gold">Infrastructures urbaines</a></li>
                  </ul>
                </div>

                <div>
                  <div class="font-bold text-bk-gold mb-3">Ressources</div>
                  <ul class="grid gap-2 text-bk-off/90">
                    <li><a href="#projects" class="hover:text-bk-gold">Réalisations</a></li>
                    <li><a href="#rfp" class="hover:text-bk-gold">Appels d’offres</a></li>
                    <li><a href="/terms" class="hover:text-bk-gold">Conditions générales</a></li>
                    <li><a href="/privacy" class="hover:text-bk-gold">Politique de confidentialité</a></li>
                  </ul>
                </div>

                <div>
                  <div class="font-bold text-bk-gold mb-3">Contact</div>
                  <ul class="grid gap-1.5 mb-3 text-bk-off/90">
                    <li><a :href="phoneHref">Tél : <span class="text-bk-off">{{ phoneDisplay }}</span></a></li>
                    <li><a :href="'mailto:' + email">{{ email }}</a></li>
                    <li class="text-bk-off/70">{{ city }}</li>
                  </ul>

                  <form class="flex gap-2 w-full bg-white/5 ring-1 ring-white/15 px-2 py-1.5 rounded-lg backdrop-blur" @submit.prevent>
                    <input type="email" placeholder="Votre email" aria-label="Votre email"
                           class="flex-1 bg-transparent outline-none border-0 text-white placeholder:text-white/50 px-2" />
                    <button type="button" class="shrink-0 rounded-md px-3 py-1.5 bg-bk-gold text-bk-night font-extrabold hover:-translate-y-0.5 transition">S’abonner</button>
                  </form>
                </div>
              </div>

              <div class="border-t border-dashed border-white/15 px-5 py-4 lg:px-8 flex flex-col md:flex-row gap-3 md:items-center md:justify-between text-bk-off/80">
                <div>
                  © {{ new Date().getFullYear() }}
                  <a href="https://1clickmali.com" target="_blank" rel="noopener" class="font-semibold hover:text-bk-gold">1Click Mali</a>.
                  Designed by
                  <a href="https://zehawitech.com" target="_blank" rel="noopener" class="font-semibold hover:text-bk-gold">ZEHAWI TECHNOLOGIES SARL</a>.
                </div>
                <div class="text-bk-off/70">
                  RC : <span class="text-bk-off/90">xxxxx</span> · ICE : <span class="text-bk-off/90">xxxxxxxxxxxx</span> · IF : <span class="text-bk-off/90">xxxxxxxx</span> · Patente : <span class="text-bk-off/90">xxxxxxx</span>
                </div>
                <div class="flex gap-4">
                  <a :href="whatsappHref" target="_blank" rel="noopener" class="hover:text-bk-gold">WhatsApp</a>
                  <a :href="phoneHref" class="hover:text-bk-gold">Appel</a>
                  <a :href="'mailto:' + email" class="hover:text-bk-gold">Email</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>      
    </footer>

    <!-- Actions flottantes -->
    <a :href="whatsappHref" target="_blank" rel="noopener"
       class="fixed bottom-5 right-5 inline-flex items-center justify-center w-12 h-12 rounded-full
              bg-white/10 border border-white/10 hover:border-bk-gold/60 backdrop-blur z-40">
      <svg viewBox="0 0 24 24" class="w-6 h-6"><path fill="currentColor" d="M20 3.5A10 10 0 0012 1a10 10 0 00-8.66 15.07L2 23l7.12-1.87A10 10 0 0022 11a10 10 0 00-2-7.5zM12 19a8 8 0 118-8 8 8 0 01-8 8z"/></svg>
    </a>

    <button v-show="showUp" @click="scrollToTop"
      class="fixed bottom-5 right-20 md:right-24 inline-flex items-center justify-center w-10 h-10 rounded-full
             bg-white/10 border border-white/15 hover:border-bk-gold/60 backdrop-blur z-40 transition"
      aria-label="Retour en haut">
      <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M12 4l6 6-1.4 1.4L13 7.8V20h-2V7.8L7.4 11.4 6 10l6-6z"/></svg> 
    </button>
  </div>
</template>

<style scoped>
@keyframes shine { from { transform: translateX(-120%); } to { transform: translateX(120%); } }
</style>
