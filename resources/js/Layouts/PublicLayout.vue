<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

type PageAuth = { user?: Record<string, unknown> | null }
type PageProps = { auth?: PageAuth }

const page = usePage<PageProps>()
const user = computed(() => page.props.auth?.user ?? null)

/* Fallbacks -> noms EXACTS des routes Laravel */
const FALLBACKS: Record<string, string> = {
  home: '/',
  'public.about': '/a-propos',
  'public.services': '/services',
  'public.projects': '/realisations',
  'public.projects.show': '/realisations',
  'public.posts': '/actualites',
  'public.posts.show': '/actualites',
  'public.rfp': '/appels-d-offres',
  'public.contact': '/contact',
  'public.quote': '/devis',
  login: '/login',
  dashboard: '/dashboard',
}

function r(name: string, params?: any, absolute = false, fallback?: string) {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (Z) return String(ziggyRoute(name, params ?? {}, absolute, Z))
  } catch {}
  return fallback ?? FALLBACKS[name] ?? '#'
}

/* Route courante (avec fallback par URL si Ziggy indispo) */
function getCurrentRoute(): string | null {
  try {
    const fn = ziggyRoute as unknown as (() => any)
    const inst = fn()
    if (inst && typeof inst.current === 'function') return inst.current() as string
  } catch {}
  try {
    const g = (globalThis as any).route
    if (typeof g === 'function') {
      const inst2 = g()
      if (inst2 && typeof inst2.current === 'function') return inst2.current()
    }
  } catch {}
  return null
}
const currentRouteName = computed<string | null>(() => getCurrentRoute())
const currentUrl = computed(() => (usePage() as any).url || window.location.pathname)

function isActive(name: string, startsWith = false) {
  const n = currentRouteName.value
  if (n) return startsWith ? n.startsWith(name + '.') || n === name : n === name
  const target = r(name)
  return startsWith ? String(currentUrl.value).startsWith(String(target)) : String(currentUrl.value) === String(target)
}

/* ====== Header state / décor ====== */
const open = ref(false)
const scrolled = ref(false)
const progress = ref(0)

const SMART_HIDE = true
const topbarPinned = ref(true)
const lastY = ref(0)
const lastDeltaDown = ref(false)
const headerHover = ref(false)

/* Contact */
const phoneDisplay = ref('+212 7 70 55 60 21')
const phoneHref    = ref('tel:+212770556021')
const whatsappHref = ref('https://wa.me/212770556021')
const email        = ref('contact@bkoconstruction.com')
const city         = ref('Tanger, Maroc')

/* Scroll / FX */
const showUp = ref(false)
function scrollToTop(){ window.scrollTo({ top: 0, behavior: 'smooth' }) }
function onScroll(){
  const y = window.scrollY
  scrolled.value = y > 8
  showUp.value   = y > 320
  const h = document.documentElement.scrollHeight - window.innerHeight
  progress.value = h > 0 ? Math.min(1, y / h) : 0
  if (SMART_HIDE) {
    const delta = y - lastY.value
    if (delta > 1 && y > 64) { topbarPinned.value = false; lastDeltaDown.value = true }
    else if (delta < 0)      { topbarPinned.value = true;  lastDeltaDown.value = false }
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

    <!-- BACKDROP décoratif -->
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10">
      <div class="bk-grid"></div>
      <div class="atlas-aurora a"></div>
      <div class="atlas-aurora b"></div>
      <div class="atlas-aurora c"></div>
      <div class="spotlight"></div>
    </div>

    <!-- Barre de progression lecture -->
    <div class="progress-line" :style="`transform:scaleX(${progress})`"></div>

    <!-- ======= HEADER ======= -->
    <header
      class="fixed top-0 inset-x-0 z-50 border-b transition-all duration-300 ease-out
             backdrop-blur supports-[backdrop-filter]:bg-[#151e27]/80 bg-[#151e27]/92
             h-[76px] lg:h-[108px]"
      :class="scrolled ? 'border-white/15 shadow-[0_10px_40px_-30px_rgba(0,0,0,.8)]' : 'border-white/10'"
      @mouseenter="onHeaderEnter" @mouseleave="onHeaderLeave"
    >
      <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-rows-[0px_76px] lg:grid-rows-[40px_68px]">
        <!-- TOPBAR -->
        <div
          class="row-start-1 row-end-2 hidden lg:flex h-10 items-center justify-between text-[13px] text-white/85 border-b border-white/10
                 transition-all duration-200 ease-out bg-gradient-to-b from-white/5 to-transparent will-change-[transform,opacity]"
          :class="topbarPinned ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-full'"
        >
          <div class="flex items-center gap-6">
            <span class="chip-ma">MA</span>
            <span class="text-white/85">Entreprise marocaine de BTP — Qualité • Sécurité • Délais</span>

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

        <!-- NAVBAR -->
        <div class="row-start-2 row-end-3 flex items-center justify-between">
          <Link :href="r('home')" class="flex items-center">
            <img class="brand-logo" src="/assets/logo-bk.jpeg" alt="BKOCONSTRUCTION logo">
          </Link>

          <nav class="hidden md:flex items-center gap-1 rounded-2xl px-1 py-1 bg-white/5 ring-1 ring-white/10">
            <Link :href="r('home')"            :class="['nav-pill', isActive('home') ? 'text-bk-gold' : '']"                        :aria-current="isActive('home') ? 'page' : undefined">Accueil</Link>
            <Link :href="r('public.about')"    :class="['nav-pill', isActive('public.about') ? 'text-bk-gold' : '']"                :aria-current="isActive('public.about') ? 'page' : undefined">À propos</Link>
            <Link :href="r('public.services')" :class="['nav-pill', isActive('public.services') ? 'text-bk-gold' : '']"             :aria-current="isActive('public.services') ? 'page' : undefined">Services</Link>
            <Link :href="r('public.projects')" :class="['nav-pill', isActive('public.projects', true) ? 'text-bk-gold' : '']"        :aria-current="isActive('public.projects', true) ? 'page' : undefined">Réalisations</Link>
            <Link :href="r('public.posts')"    :class="['nav-pill', isActive('public.posts', true) ? 'text-bk-gold' : '']"           :aria-current="isActive('public.posts', true) ? 'page' : undefined">Actualités</Link>
            <Link :href="r('public.rfp')"      :class="['nav-pill', isActive('public.rfp') ? 'text-bk-gold' : '']"                  :aria-current="isActive('public.rfp') ? 'page' : undefined">Appels d’offres</Link>
            <Link :href="r('public.contact')"  :class="['nav-pill', isActive('public.contact') ? 'text-bk-gold' : '']"              :aria-current="isActive('public.contact') ? 'page' : undefined">Contact</Link>
          </nav>

          <div class="hidden md:flex items-center gap-3">
            <Link :href="r('public.quote')" class="btn-gold" :aria-current="isActive('public.quote') ? 'page' : undefined">
              <span class="btn-glow"></span><span class="btn-shine"></span>Demander un devis
            </Link>
            <Link v-if="!user" :href="r('login')"
                  class="inline-flex items-center justify-center rounded-xl px-4 py-2 border border-white/15 text-white hover:border-bk-gold/60 transition focus:outline-none focus:ring-2 focus:ring-bk-gold/50">Se connecter</Link>
            <Link v-else :href="r('dashboard')"
                  class="inline-flex items-center justify-center rounded-xl px-4 py-2 border border-white/15 text-white hover:border-bk-gold/60 transition focus:outline-none focus:ring-2 focus:ring-bk-gold/50">Espace</Link>
          </div>

          <!-- Burger -->
          <button class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/10 text-white/95 border border-white/25 hover:border-bk-gold/70 shadow-[0_12px_30px_-18px_rgba(0,0,0,.6)] transition"
                  @click="open = !open" :aria-expanded="open" aria-controls="mobile-drawer">
            <span class="sr-only">Ouvrir le menu</span>
            <svg v-if="!open" viewBox="0 0 24 24" class="w-6 h-6"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
            <svg v-else viewBox="0 0 24 24" class="w-6 h-6"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
          </button>
        </div>
      </div>
    </header>

    <!-- Spacer -->
    <div class="h-[76px] lg:h-[108px]" aria-hidden="true"></div>

    <!-- Overlay + Drawer Mobile -->
    <div v-show="open" class="fixed inset-0 z-[60] bg-black/70 backdrop-blur-sm" @click="open=false"></div>
    <div v-show="open" id="mobile-drawer" role="dialog" aria-modal="true" class="fixed top-2 inset-x-2 z-[61]">
      <div class="relative overflow-hidden rounded-2xl border border-white/15 text-bk-off shadow-[0_30px_80px_-20px_rgba(0,0,0,.7)] bg-gradient-to-b from-[#0f141a]/95 to-[#0f141a]/80" @click.stop>
        <div class="flex items-center justify-between px-4 pt-3 pb-2">
          <img src="/assets/logo-bk.jpeg" class="h-10 w-auto object-contain" alt="BKOCONSTRUCTION">
          <button class="p-2 rounded-lg bg-white/10 border border-white/15 hover:border-bk-gold/60 transition" @click="open=false" aria-label="Fermer">
            <svg viewBox="0 0 24 24" class="w-6 h-6"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2"/></svg>
          </button>
        </div>

        <div class="grid grid-cols-2 gap-2 px-3 pb-3">
          <Link :href="r('home')"            class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Accueil</Link>
          <Link :href="r('public.about')"    class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">À propos</Link>
          <Link :href="r('public.services')" class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Services</Link>
          <Link :href="r('public.projects')" class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Réalisations</Link>
          <Link :href="r('public.posts')"    class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Actualités</Link>
          <Link :href="r('public.rfp')"      class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Appels d’offres</Link>
          <Link :href="r('public.contact')"  class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Contact</Link>
          <Link :href="r('public.quote')"    class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-bold bg-white/5 ring-1 ring-white/15 hover:ring-bk-gold/60 transition" @click="open=false">Devis express</Link>
        </div>

        <div class="px-3 pb-3 grid gap-2">
          <Link :href="r('public.quote')" class="btn-gold text-center" @click="open=false">
            <span class="btn-glow"></span><span class="btn-shine"></span> Demander un devis
          </Link>
          <div class="flex gap-2">
            <Link v-if="!user" :href="r('login')"     class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2 border border-white/15 text-white hover:border-bk-gold/60 transition" @click="open=false">Se connecter</Link>
            <Link v-else        :href="r('dashboard')" class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2 border border-white/15 text-white hover:border-bk-gold/60 transition" @click="open=false">Espace</Link>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENU PAGE -->
    <main id="page-content"><slot /></main>

    <!-- ======= FOOTER (réintégré) ======= -->
    <footer class="mt-16 text-sm relative overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-14">
        <div class="absolute inset-0 -z-10 blur-[70px] opacity-30 mix-blend-screen
                    bg-[radial-gradient(60%_60%_at_20%_20%,#dcc176_0%,transparent_60%)]"></div>

        <div class="footer-card gradient-ring">
          <div class="glow g1"></div><div class="glow g2"></div><div class="glow g3"></div>

          <div class="footer-content">
            <div class="min-w-0">
              <img src="/assets/logo-bk.jpeg" class="footer-logo" alt="BKOCONSTRUCTION">
              <p class="text-bk-off/85">Entreprise marocaine de BTP : routes, bâtiments, VRD, génie civil & immobilier.</p>
              <div class="actions">
                <a :href="whatsappHref" target="_blank" rel="noopener" class="icon-btn" aria-label="WhatsApp">
                  <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M20 3.5A10 10 0 0012 1a10 10 0 00-8.66 15.07L2 23l7.12-1.87A10 10 0 0022 11a10 10 0 00-2-7.5zM12 19a8 8 0 118-8 8 8 0 01-8 8z"/></svg>
                </a>
                <a :href="phoneHref" class="icon-btn" aria-label="Appeler">
                  <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>
                </a>
                <a :href="'mailto:' + email" class="icon-btn" aria-label="Email">
                  <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M20 8l-8 5-8-5V6l8 5 8-5v2z"/><path fill="currentColor" d="M20 18H4v-2h16v2z"/></svg>
                </a>
              </div>
            </div>

            <div>
              <div class="footer-title">Services</div>
              <ul class="list">
                <li><Link :href="r('public.services')">Routes & VRD</Link></li>
                <li><Link :href="r('public.services')">Bâtiments</Link></li>
                <li><Link :href="r('public.services')">Immobilier</Link></li>
                <li><Link :href="r('public.services')">Rénovation</Link></li>
                <li><Link :href="r('public.services')">Génie civil</Link></li>
              </ul>
            </div>

            <div>
              <div class="footer-title">Secteurs</div>
              <ul class="list">
                <li><Link :href="r('public.services')">Public & Collectivités</Link></li>
                <li><Link :href="r('public.services')">Immobilier privé</Link></li>
                <li><Link :href="r('public.services')">Industrie & Logistique</Link></li>
                <li><Link :href="r('public.services')">Infrastructures urbaines</Link></li>
              </ul>
            </div>

            <div>
              <div class="footer-title">Ressources</div>
              <ul class="list">
                <li><Link :href="r('public.projects')">Réalisations</Link></li>
                <li><Link :href="r('public.posts')">Actualités</Link></li>
                <li><Link :href="r('public.about')">À propos</Link></li>
              </ul>
            </div>

            <div class="min-w-0">
              <div class="footer-title">Contact</div>
              <ul class="list mb-3">
                <li><a :href="phoneHref">Tél : <span class="text-bk-off">{{ phoneDisplay }}</span></a></li>
                <li><a :href="'mailto:' + email">{{ email }}</a></li>
                <li class="text-bk-off/75">{{ city }}</li>
              </ul>

              <Link :href="r('public.quote')" class="btn-gold w-full justify-center">
                <span class="btn-glow"></span><span class="btn-shine"></span>
                Demander un devis
              </Link>

              <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                <div class="rounded-lg bg-white/5 ring-1 ring-white/10 p-2">
                  <div class="text-xs text-white/70">ISO</div>
                  <div class="font-bold">9001</div>
                </div>
                <div class="rounded-lg bg-white/5 ring-1 ring-white/10 p-2">
                  <div class="text-xs text-white/70">ISO</div>
                  <div class="font-bold">45001</div>
                </div>
                <div class="rounded-lg bg-white/5 ring-1 ring-white/10 p-2">
                  <div class="text-xs text-white/70">Clients</div>
                  <div class="font-bold">98% sats.</div>
                </div>
              </div>
            </div>
          </div>

          <div class="footer-bottom">
            <div class="min-w-0">
              © {{ new Date().getFullYear() }} BKO Construction. Tous droits réservés.
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
.brand-logo{ height:3rem; width:auto; object-fit:contain; transition:height .18s ease }
@media (min-width:768px){ .brand-logo{ height:3.25rem } }
@keyframes shine { from { transform: translateX(-120%); } to { transform: translateX(120%); } }
</style>
