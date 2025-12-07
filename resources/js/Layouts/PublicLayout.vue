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
  'public.posts': '/actualites',
  'public.announcements': '/annonces-archives',
  'announcements-archive': '/annonces-archives',
  'public.team': '/equipe',
  'public.contact': '/contact',
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

/* Route courante */
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

/* Contact AMEST-Sahel */
const phoneDisplay = ref('+216 24 282 332')
const phoneHref    = ref('tel:+21624282332')
const whatsappHref = ref('https://wa.me/21624282332')
const email        = ref('amestsahel04@gmail.com')
const city         = ref('Tunisie - Région Sahel')

// Gestion robuste des logos avec fallback
const logoError = (event: Event, size: 'small' | 'medium' | 'large' = 'medium') => {
  const target = event.target as HTMLImageElement
  target.style.display = 'none'
  const parent = target.parentElement
  if (parent) {
    const sizes = {
      small: 'w-8 h-8 text-sm',
      medium: 'w-10 h-10 text-base',
      large: 'w-12 h-12 text-lg'
    }

    parent.className = `${sizes[size]} bg-gradient-to-br from-green-600 via-yellow-500 to-red-600 rounded-2xl flex items-center justify-center text-white font-bold shadow-lg`
    parent.innerHTML = 'AM'
  }
}

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
  <div class="min-h-screen bg-gradient-to-br from-green-50 to-yellow-50 text-gray-900 relative overflow-x-hidden">

    <!-- Barre de progression lecture -->
    <div class="progress-line bg-green-600" :style="`transform:scaleX(${progress})`"></div>

    <!-- ======= HEADER ======= -->
    <header
      class="fixed top-0 inset-x-0 z-50 border-b transition-all duration-300 ease-out
             backdrop-blur supports-[backdrop-filter]:bg-white/90 bg-white/95
             h-[76px] lg:h-[108px]"
      :class="scrolled ? 'border-green-200 shadow-lg' : 'border-transparent'"
      @mouseenter="onHeaderEnter" @mouseleave="onHeaderLeave"
    >
      <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-rows-[0px_76px] lg:grid-rows-[40px_68px]">
        <!-- TOPBAR - Couleurs Mali -->
<div
  class="row-start-1 row-end-2 hidden lg:flex h-10 items-center justify-between text-[13px] text-gray-700 border-b border-green-200
         transition-all duration-200 ease-out bg-gradient-to-b from-green-50 to-transparent will-change-[transform,opacity]"
  :class="topbarPinned ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-full'"
>
  <div class="flex items-center gap-6">
    <!-- Drapeau Mali miniature -->
    <div class="flex h-6 w-12 rounded overflow-hidden shadow-sm">
      <div class="flex-1 bg-green-600"></div>
      <div class="flex-1 bg-yellow-500"></div>
      <div class="flex-1 bg-red-600"></div>
    </div>

    <!-- Texte sur deux lignes -->
    <div class="flex flex-col leading-tight">
      <span class="text-gray-700 block">Association Malienne des Étudiants et</span>
      <span class="text-gray-700 block">Stagiaires en Tunisie — Section Sahel</span>
    </div>

    <a :href="phoneHref" class="inline-flex items-center gap-2 hover:text-green-700">
      <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>
      <span>{{ phoneDisplay }}</span>
    </a>

    <a :href="'mailto:' + email" class="inline-flex items-center gap-2 hover:text-green-700">
      <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M20 8l-8 5-8-5V6l8 5 8-5v2z"/><path fill="currentColor" d="M20 18H4v-2h16v2z"/></svg>
      <span>{{ email }}</span>
    </a>

    <span class="inline-flex items-center gap-2 text-gray-600">
      <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>
      <span>{{ city }}</span>
    </span>
  </div>

  <div class="flex items-center gap-3">
    <a :href="whatsappHref" target="_blank" rel="noopener" class="hover:text-green-700 inline-flex items-center gap-1">
      <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M20 3.5A10 10 0 0012 1a10 10 0 00-8.66 15.07L2 23l7.12-1.87A10 10 0 0022 11a10 10 0 00-2-7.5zM12 19a8 8 0 118-8 8 8 0 01-8 8z"/></svg>
      WhatsApp
    </a>
    <span class="text-gray-400">|</span>
    <button class="hover:text-green-700">FR</button>
    <span class="text-gray-400">/</span>
    <button class="hover:text-green-700">AR</button>
  </div>
</div>

        <!-- NAVBAR -->
        <div class="row-start-2 row-end-3 flex items-center justify-between">
          <!-- Logo AMEST Sahel AVEC VOTRE LOGO -->
          <Link :href="r('home')" class="flex items-center space-x-3 group">
            <!-- Votre logo - Version robuste avec fallback -->
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center overflow-hidden bg-white p-1 shadow-lg">
              <img
                src="/assets/logo-amest.jpg"
                alt="AMEST Sahel Logo"
                class="w-full h-full object-contain"
                @error="(e) => logoError(e, 'large')"
              >
            </div>
            <div class="flex flex-col">
              <span class="text-xl font-bold text-gray-900 group-hover:text-green-700 transition-colors duration-300 leading-tight">
                AMEST
              </span>
              <span class="text-xs text-green-600 font-semibold tracking-wide -mt-1">
                Section Sahel
              </span>
            </div>
          </Link>

          <nav class="hidden md:flex items-center gap-1 rounded-2xl px-1 py-1 bg-white/80 ring-1 ring-green-200 backdrop-blur-sm">
            <Link :href="r('home')"            :class="['nav-pill', isActive('home') ? 'text-green-700 bg-green-50 border border-green-200' : 'text-gray-700 hover:text-green-700 hover:bg-green-50']" :aria-current="isActive('home') ? 'page' : undefined">Accueil</Link>
            <Link :href="r('public.about')"    :class="['nav-pill', isActive('public.about') ? 'text-green-700 bg-green-50 border border-green-200' : 'text-gray-700 hover:text-green-700 hover:bg-green-50']" :aria-current="isActive('public.about') ? 'page' : undefined">À propos</Link>
            <Link :href="r('public.posts')" :class="['nav-pill', isActive('public.posts', true) ? 'text-green-700 bg-green-50 border border-green-200' : 'text-gray-700 hover:text-green-700 hover:bg-green-50']" :aria-current="isActive('public.posts', true) ? 'page' : undefined">Activités</Link>
            <Link :href="r('announcements-archive')"
                :class="['nav-pill', isActive('announcements-archive', true) ? 'text-green-700 bg-green-50 border border-green-200' : 'text-gray-700 hover:text-green-700 hover:bg-green-50']"
                :aria-current="isActive('announcements-archive', true) ? 'page' : undefined">
                Annonces & Archives
             </Link>

            <Link :href="r('public.contact')"  :class="['nav-pill', isActive('public.contact') ? 'text-green-700 bg-green-50 border border-green-200' : 'text-gray-700 hover:text-green-700 hover:bg-green-50']" :aria-current="isActive('public.contact') ? 'page' : undefined">Contact</Link>
          </nav>

          <div class="hidden md:flex items-center gap-3">
            <Link :href="r('public.contact')" class="btn-mali" :aria-current="isActive('public.contact') ? 'page' : undefined">
              Nous Rejoindre
            </Link>
            <Link v-if="!user" :href="r('login')"
                  class="inline-flex items-center justify-center rounded-xl px-4 py-2 border border-green-300 text-gray-700 hover:border-green-500 hover:text-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500/50">Se connecter</Link>
            <Link v-else :href="r('dashboard')"
                  class="inline-flex items-center justify-center rounded-xl px-4 py-2 border border-green-300 text-gray-700 hover:border-green-500 hover:text-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500/50">Espace</Link>
          </div>

          <!-- Burger -->
          <button class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl bg-green-100 text-green-700 border border-green-300 hover:border-green-500 hover:text-green-800 shadow-sm transition"
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
    <div v-show="open" class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm" @click="open=false"></div>
    <div v-show="open" id="mobile-drawer" role="dialog" aria-modal="true" class="fixed top-2 inset-x-2 z-[61]">
      <div class="relative overflow-hidden rounded-2xl border border-green-200 bg-white shadow-2xl" @click.stop>
        <div class="flex items-center justify-between px-4 pt-3 pb-2">
          <div class="flex items-center space-x-3">
            <!-- Logo dans le menu mobile -->
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center overflow-hidden bg-white p-1">
              <img
                src="/assets/logo-amest.jpg"
                alt="AMEST Sahel Logo"
                class="w-full h-full object-contain"
                @error="(e) => logoError(e, 'medium')"
              >
            </div>
            <div class="flex flex-col">
              <span class="text-lg font-bold text-gray-900">AMEST</span>
              <span class="text-xs text-green-600 font-semibold -mt-1">Section Sahel</span>
            </div>
          </div>
          <button class="p-2 rounded-lg bg-green-100 border border-green-300 hover:border-green-500 transition" @click="open=false" aria-label="Fermer">
            <svg viewBox="0 0 24 24" class="w-6 h-6"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2"/></svg>
          </button>
        </div>

        <div class="grid grid-cols-2 gap-2 px-3 pb-3">
          <Link :href="r('home')"            class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-semibold bg-green-50 border border-green-200 hover:border-green-500 hover:text-green-700 transition" @click="open=false">Accueil</Link>
          <Link :href="r('public.about')"    class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-semibold bg-green-50 border border-green-200 hover:border-green-500 hover:text-green-700 transition" @click="open=false">À propos</Link>
          <Link :href="r('public.posts')" class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-semibold bg-green-50 border border-green-200 hover:border-green-500 hover:text-green-700 transition" @click="open=false">Activités</Link>
          <Link :href="r('public.announcements')"     class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-semibold bg-green-50 border border-green-200 hover:border-green-500 hover:text-green-700 transition" @click="open=false">Annonces</Link>
          <Link :href="r('public.contact')"  class="flex items-center justify-center min-h-[3.2rem] rounded-xl font-semibold bg-green-50 border border-green-200 hover:border-green-500 hover:text-green-700 transition" @click="open=false">Contact</Link>
        </div>

        <div class="px-3 pb-3 grid gap-2">
          <Link :href="r('public.contact')" class="btn-mali text-center" @click="open=false">
            Nous Rejoindre
          </Link>
          <div class="flex gap-2">
            <Link v-if="!user" :href="r('login')"     class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2 border border-green-300 text-gray-700 hover:border-green-500 hover:text-green-700 transition" @click="open=false">Se connecter</Link>
            <Link v-else        :href="r('dashboard')" class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2 border border-green-300 text-gray-700 hover:border-green-500 hover:text-green-700 transition" @click="open=false">Espace</Link>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENU PAGE -->
    <main id="page-content" class="bg-gradient-to-br from-green-50 to-yellow-50"><slot /></main>

    <!-- ======= FOOTER AMEST-SAHEL - Couleurs Mali ======= -->
    <footer class="bg-gradient-to-br from-green-600 to-green-700 text-white mt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Newsletter -->
        <div class="bg-gradient-to-r from-yellow-500 to-red-600 rounded-3xl p-8 text-white mb-12 text-center">
          <h3 class="text-2xl font-bold mb-3">📧 Restez informé</h3>
          <p class="text-white/90 mb-6 max-w-md mx-auto">
            Recevez nos actualités, événements et opportunités pour la communauté étudiante malienne.
          </p>
          <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
            <input
              type="email"
              placeholder="Votre email"
              class="flex-1 px-4 py-3 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500"
            >
            <button class="bg-white text-green-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors">
              S'abonner
            </button>
          </div>
        </div>

        <div class="grid md:grid-cols-4 gap-8">
          <!-- Logo et Description -->
          <div class="md:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
              <!-- Logo dans le footer -->
              <div class="w-12 h-12 rounded-2xl flex items-center justify-center overflow-hidden bg-white p-1 shadow-lg">
                <img
                  src="/assets/logo-amest.jpg"
                  alt="AMEST Sahel Logo"
                  class="w-full h-full object-contain"
                  @error="(e) => logoError(e, 'large')"
                >
              </div>
              <div class="flex flex-col">
                <span class="text-2xl font-bold text-white">AMEST</span>
                <span class="text-sm text-yellow-300 font-semibold -mt-1">Section Sahel</span>
              </div>
            </div>
            <p class="text-green-100 mb-6 max-w-md leading-relaxed">
              Association Malienne des Étudiants et Stagiaires en Tunisie - Section Sahel.
              Unis pour la réussite académique et l'épanouissement de la communauté étudiante malienne en Tunisie.
            </p>
            <div class="flex space-x-3">
              <a href="#" class="w-10 h-10 bg-green-700 rounded-2xl flex items-center justify-center hover:bg-yellow-500 transition-all duration-300 transform hover:-translate-y-1">
                <span class="text-sm">📘</span>
              </a>
              <a href="#" class="w-10 h-10 bg-green-700 rounded-2xl flex items-center justify-center hover:bg-yellow-500 transition-all duration-300 transform hover:-translate-y-1">
                <span class="text-sm">📷</span>
              </a>
              <a href="#" class="w-10 h-10 bg-green-700 rounded-2xl flex items-center justify-center hover:bg-yellow-500 transition-all duration-300 transform hover:-translate-y-1">
                <span class="text-sm">📺</span>
              </a>
            </div>
          </div>

          <!-- Liens Rapides -->
          <div>
            <h3 class="text-lg font-semibold mb-4 text-white">Navigation</h3>
            <ul class="space-y-3">
              <li><Link :href="r('home')" class="text-green-100 hover:text-yellow-300 transition-colors duration-200">Accueil</Link></li>
              <li><Link :href="r('public.about')" class="text-green-100 hover:text-yellow-300 transition-colors duration-200">À propos</Link></li>
              <li><Link :href="r('public.posts')" class="text-green-100 hover:text-yellow-300 transition-colors duration-200">Activités</Link></li>
              <li><Link :href="r('public.announcements')" class="text-green-100 hover:text-yellow-300 transition-colors duration-200">Annonces</Link></li>
              <li><Link :href="r('public.contact')" class="text-green-100 hover:text-yellow-300 transition-colors duration-200">Contact</Link></li>
            </ul>
          </div>

          <!-- Contact -->
          <div>
            <h3 class="text-lg font-semibold mb-4 text-white">Contact</h3>
            <div class="space-y-4">
              <div class="flex items-center space-x-3 group">
                <div class="w-10 h-10 bg-yellow-500/30 rounded-2xl flex items-center justify-center group-hover:bg-yellow-500 transition-colors">
                  <span class="text-sm">📧</span>
                </div>
                <div>
                  <a href="mailto:amestsahel04@gmail.com" class="text-green-100 hover:text-yellow-300 transition-colors block">
                    amestsahel04@gmail.com
                  </a>
                </div>
              </div>
              <div class="flex items-center space-x-3 group">
                <div class="w-10 h-10 bg-yellow-500/30 rounded-2xl flex items-center justify-center group-hover:bg-yellow-500 transition-colors">
                  <span class="text-sm">📱</span>
                </div>
                <div>
                  <a href="tel:24282332" class="text-green-100 hover:text-yellow-300 transition-colors block">
                    24 282 332
                  </a>
                </div>
              </div>
              <div class="flex items-center space-x-3 group">
                <div class="w-10 h-10 bg-yellow-500/30 rounded-2xl flex items-center justify-center group-hover:bg-yellow-500 transition-colors">
                  <span class="text-sm">📍</span>
                </div>
                <span class="text-green-100">Tunisie - Région Sahel</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-green-500 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
          <p class="text-green-200 text-sm">
            © 2025 AMEST-Sahel. Tous droits réservés.
          </p>
          <div class="flex space-x-6 mt-4 md:mt-0">
            <a href="#" class="text-green-200 hover:text-yellow-300 text-sm transition-colors">
              Confidentialité
            </a>
            <a href="#" class="text-green-200 hover:text-yellow-300 text-sm transition-colors">
              Conditions
            </a>
            <a href="#" class="text-green-200 hover:text-yellow-300 text-sm transition-colors">
              FAQ
            </a>
          </div>
        </div>
      </div>
    </footer>

    <!-- Actions flottantes -->
    <a :href="whatsappHref" target="_blank" rel="noopener"
       class="fixed bottom-5 right-5 inline-flex items-center justify-center w-12 h-12 rounded-full
              bg-green-600 text-white border border-green-600 hover:bg-green-700 hover:border-green-700 shadow-lg z-40 transition-all duration-300 transform hover:scale-110">
      <svg viewBox="0 0 24 24" class="w-6 h-6"><path fill="currentColor" d="M20 3.5A10 10 0 0012 1a10 10 0 00-8.66 15.07L2 23l7.12-1.87A10 10 0 0022 11a10 10 0 00-2-7.5zM12 19a8 8 0 118-8 8 8 0 01-8 8z"/></svg>
    </a>

    <button v-show="showUp" @click="scrollToTop"
      class="fixed bottom-5 right-20 md:right-24 inline-flex items-center justify-center w-10 h-10 rounded-full
             bg-yellow-500 text-white border border-yellow-500 hover:bg-yellow-600 hover:border-yellow-600 shadow-lg z-40 transition-all duration-300 transform hover:scale-110"
      aria-label="Retour en haut">
      <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M12 4l6 6-1.4 1.4L13 7.8V20h-2V7.8L7.4 11.4 6 10l6-6z"/></svg>
    </button>
  </div>
</template>

<style scoped>
.progress-line {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: linear-gradient(90deg, #059669, #d97706, #dc2626);
  transform-origin: left;
  z-index: 100;
  transition: transform 0.3s ease;
}

.btn-mali {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  border-radius: 0.75rem;
  font-weight: 600;
  color: white;
  background: linear-gradient(135deg, #059669, #d97706, #dc2626);
  border: 1px solid rgba(5, 150, 105, 0.3);
  transition: all 0.3s ease;
  overflow: hidden;
}

.btn-mali:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -10px rgba(5, 150, 105, 0.5);
}

.nav-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  border-radius: 0.75rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.brand-logo {
  height: 3rem;
  width: auto;
  object-fit: contain;
  transition: height 0.18s ease;
}

@media (min-width: 768px) {
  .brand-logo {
    height: 3.25rem;
  }
}
</style>
