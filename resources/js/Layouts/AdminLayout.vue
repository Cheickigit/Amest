<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

/* ---------- Types ---------- */
type User = {
  name?: string; email?: string; roles?: string[]; permissions?: string[];
  last_login_at?: string; two_factor_enabled?: boolean
}
type Counts = { leads?: number; drafts?: number }
type PageProps = {
  auth?: { user?: User | null },
  adminCounts?: Counts,
  csrf_token?: string,
  url?: string
}

/* ---------- Page / user ---------- */
const page   = usePage<PageProps>()
const user   = computed<User | null>(() => page.props.auth?.user ?? null)
const counts = computed<Counts>(() => page.props.adminCounts ?? { leads:0, drafts:0 })

const userName  = computed(() => user.value?.name  || 'Compte')
const userEmail = computed(() => user.value?.email || '')

const has = (role:string) => !!user.value?.roles?.includes(role)
const can = (perm:string) => !!user.value?.permissions?.includes(perm)

/* ---------- Ziggy helper robuste ---------- */
const Zig = computed<any|undefined>(() => (window as any)?.Ziggy)
function hrefSafe(name: string, params?: any, absolute = false): string {
  try { return ziggyRoute(name, params ?? {}, absolute, Zig.value) } catch { return '#' }
}

/* ---------- UI state ---------- */
const sidebarOpen    = ref(true)   // mobile visible
const sidebarCompact = ref(false)  // desktop compact
onMounted(() => {
  sidebarOpen.value    = (localStorage.getItem('amest.sb')   ?? '1') === '1'
  sidebarCompact.value = (localStorage.getItem('amest.sb.c') ?? '0') === '1'
})
watch(sidebarOpen,    v => localStorage.setItem('amest.sb',   v ? '1' : '0'))
watch(sidebarCompact, v => localStorage.setItem('amest.sb.c', v ? '1' : '0'))

function onNav() { if (window.innerWidth < 768) sidebarOpen.value = false }

/* ---------- Online indicator ---------- */
const online = ref(typeof navigator !== 'undefined' ? navigator.onLine : true)
const onOnline  = ()=> online.value = true
const onOffline = ()=> online.value = false
onMounted(()=>{ addEventListener('online', onOnline); addEventListener('offline', onOffline) })
onBeforeUnmount(()=>{ removeEventListener('online', onOnline); removeEventListener('offline', onOffline) })

/* ---------- Active link helpers ---------- */
const currentUrl = computed(() =>
  (page as any).url || page.props.url || (typeof window !== 'undefined' ? window.location.pathname + window.location.search : '')
)
function isActiveExact(path: string) {
  return currentUrl.value === path || currentUrl.value === `${path}/`
}
function isActivePrefix(prefixes: string[]) {
  return prefixes.some(seg => currentUrl.value.startsWith(seg.startsWith('/') ? seg : `/${seg}`))
}

/* ---------- Icônes (paths) ---------- */
const icons = {
  dash:'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z',
  members:'M16 11a4 4 0 1 0-8 0a4 4 0 0 0 8 0zM4 20a8 8 0 1 1 16 0H4z',
  activities:'M4 4h16v2H4V4zm0 5h16v2H4V9zm0 5h10v2H4v-2z',
  announcements:'M21 6h-8l-2-2H3v16h18V6z',
  projects:'M3 5h18v2H3V5zm0 6h18v2H3v-2zm0 6h12v2H3v-2z',
  contacts:'M16 11a4 4 0 1 0-8 0a4 4 0 0 0 8 0zM4 20a8 8 0 1 1 16 0H4z',
  settings:'M19.14 12.94a7.07 7.07 0 0 0 0-1.88l2.03-1.58l-1.92-3.32l-2.39.96a7.1 7.1 0 0 0-1.62-.94l-.36-2.54h-3.84l-.36 2.54c-.58.23-1.12.54-1.62.94l-2.39-.96L2.7 8.84l2.03 1.58a7.07 7.07 0 0 0 0 1.88L2.82 14.6l1.92 3.32l2.39-.96c.5.4 1.04.71 1.62.94l.36 2.54h3.84l.36-2.54c.58-.23 1.12-.54 1.62-.94l2.39.96l1.92-3.32l-2.03-1.66zM12 15a3 3 0 1 1 0-6 3 3 0 0 1 0 6z',
  security:'M12 1l9 4v6c0 5-3.8 9.7-9 11c-5.2-1.3-9-6-9-11V5l9-4zM7 11h10v2H7z',
  search:'M10 2a8 8 0 1 0 5.29 14.29l4.21 4.21l1.41-1.41l-4.21-4.21A8 8 0 0 0 10 2z',
  stats:'M4 2v20h16V2H4zm2 2h12v4H6V4zm0 6h12v4H6v-4zm0 6h12v4H6v-4z',
  calendar:'M8 2v2H5v18h14V4h-3V2h-2v2h-4V2H8zM5 8h14v12H5V8zm2 2v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2zm-8 4v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2zm-8 4v2h2v-2H7zm4 0v2h2v-2h-2z',
  education:'M12 3l9 4v6c0 1.1-.9 2-2 2h-1v-6h1V7l-7 3l-7-3v2h1v6H5v6h2v-6h8v6h2v-6h1c1.1 0 2-.9 2-2V7l-9-4z',
  solidarity:'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41c0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z',
} as const
type IconKey = keyof typeof icons
function iconPath(k: IconKey): string { return icons[k] ?? icons.dash }

/* ---------- Menu AMEST adapté ---------- */
type Item = {
  label:string; icon:IconKey; route:string;
  activeOn:string[]; enabled:boolean; exact?:boolean; badge?:()=>number
}
const items = computed<Item[]>(() => [
  { label:'Tableau de bord',  icon:'dash',     route:'admin.dashboard',      activeOn:['/admin'],          enabled:true, exact:true },
  { label:'Membres',          icon:'members',  route:'admin.members.index',  activeOn:['/admin/members'],  enabled: can('contacts.view') || has('admin') },
  { label:'Activités',        icon:'activities', route:'admin.activities.index', activeOn:['/admin/activities'], enabled: can('projects.view') || has('admin') },
  { label:'Annonces',         icon:'announcements', route:'admin.announcements.index', activeOn:['/admin/announcements'], enabled: can('posts.view') || has('admin') },
  { label:'Projets',          icon:'projects', route:'admin.projects.index', activeOn:['/admin/projects'], enabled: can('projects.view') || has('admin') },
  { label:'Contacts',         icon:'contacts', route:'admin.contacts.index', activeOn:['/admin/contacts'], enabled: can('contacts.view') || has('admin') },
  { label:'Statistiques',     icon:'stats',    route:'admin.stats',          activeOn:['/admin/stats'],    enabled: can('reports.view') || has('admin') },
  { label:'Événements',       icon:'calendar', route:'admin.events.index',   activeOn:['/admin/events'],   enabled: can('projects.view') || has('admin') },
  { label:'Éducation',        icon:'education', route:'admin.education.index', activeOn:['/admin/education'], enabled: true },
  { label:'Solidarité',       icon:'solidarity', route:'admin.solidarity.index', activeOn:['/admin/solidarity'], enabled: true },
  { label:'Compte & sécurité',icon:'security', route:'admin.account.security',activeOn:['/admin/account'], enabled:true },
])

function itemIsActive(it: Item): boolean {
  return it.exact ? isActiveExact('/admin') : isActivePrefix(it.activeOn)
}

/* ---------- Palette (⌘/Ctrl K) ---------- */
const paletteOpen = ref(false)
const paletteQ    = ref('')
const paletteList = computed(() =>
  items.value
    .filter(i => i.enabled)
    .filter(i => i.label.toLowerCase().includes(paletteQ.value.toLowerCase()))
)

function openPalette(){
  paletteOpen.value = true
  requestAnimationFrame(()=> (document.getElementById('cmdk') as HTMLInputElement)?.focus())
}
function goRoute(name:string){
  router.visit(hrefSafe(name))
  paletteOpen.value = false
  paletteQ.value = ''
}
function onKey(e: KeyboardEvent){
  const meta = e.metaKey || e.ctrlKey
  if (meta && e.key.toLowerCase() === 'k') { e.preventDefault(); openPalette() }
  if (e.key === 'Escape') {
    if (paletteOpen.value) paletteOpen.value = false
    closeAccount()
  }
}

/* ---------- Compte: fermer (clic extérieur + navigation + Esc) ---------- */
const accountRef = ref<HTMLDetailsElement|null>(null)
function closeAccount(){ if (accountRef.value) accountRef.value.open = false }
function onDocClick(e: MouseEvent) {
  if (accountRef.value && !accountRef.value.contains(e.target as Node)) closeAccount()
}
onMounted(() => {
  document.addEventListener('click', onDocClick, { passive: true })
  document.addEventListener('keydown', onKey)
})
onBeforeUnmount(() => {
  document.removeEventListener('click', onDocClick)
  document.removeEventListener('keydown', onKey)
})
watch(() => (page as any).url, () => closeAccount())

/* ---------- Gestion d'erreur d'image ---------- */
const handleLogoError = (event: Event) => {
  const target = event.target as HTMLImageElement
  target.style.display = 'none'
  const parent = target.parentElement
  if (parent) {
    parent.className = 'h-10 w-10 rounded-xl flex items-center justify-center bg-gradient-to-br from-green-600 via-yellow-500 to-red-600'
    parent.innerHTML = '<span class="text-white font-bold text-sm">AM</span>'
  }
}

const year = new Date().getFullYear()
</script>

<template>
  <div :class="['h-screen bg-gradient-to-br from-green-50 to-yellow-50 text-gray-800 overflow-hidden', sidebarCompact ? 'md:pl-20' : 'md:pl-72']">
    <!-- SIDEBAR AMEST-SAHEL -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-40 p-4 border-r border-green-200 overflow-y-auto transition-transform',
        'backdrop-blur bg-gradient-to-b from-green-100/80 to-yellow-50/80',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
        sidebarCompact ? 'w-20' : 'w-72'
      ]"
    >
      <!-- Brand / toggle -->
      <div class="flex items-center justify-between h-12 px-2">
        <div class="flex items-center gap-2">
          <!-- Logo AMEST -->
          <div class="h-10 w-10 rounded-xl flex items-center justify-center overflow-hidden bg-white p-1 shadow-md">
            <img
              src="/assets/logo-amest.jpg"
              class="h-full w-full object-contain"
              alt="AMEST Sahel Logo"
              @error="handleLogoError"
            />
          </div>
          <div v-if="!sidebarCompact" class="font-display font-bold tracking-tight">
            <div class="text-green-800 text-lg">AMEST</div>
            <div class="text-yellow-600 text-xs -mt-1">Section Sahel</div>
          </div>
        </div>
        <button
          type="button"
          class="hidden md:inline-flex items-center gap-1 text-xs rounded-md px-2 py-1 bg-green-100 ring-1 ring-green-300 hover:ring-green-500 text-green-700"
          :title="sidebarCompact ? 'Étendre' : 'Réduire'"
          @click="sidebarCompact=!sidebarCompact"
        >
          <svg viewBox="0 0 24 24" width="14" height="14"><path :d="icons.dash" fill="currentColor"/></svg>
          <span v-if="!sidebarCompact">Réduire</span>
        </button>
      </div>

      <!-- Online -->
      <div class="px-2 text-[11px] text-green-700 flex items-center gap-2 mt-4">
        <span :class="['inline-block w-2 h-2 rounded-full', online ? 'bg-emerald-400' : 'bg-red-400']"></span>
        <span v-if="!sidebarCompact">{{ online ? 'En ligne' : 'Hors ligne' }}</span>
      </div>

      <!-- Slogan AMEST -->
      <div v-if="!sidebarCompact" class="px-2 mt-3 mb-4">
        <div class="text-xs font-semibold text-green-700 italic">Solidarité & Réussite</div>
        <div class="text-[10px] text-gray-600 mt-1">Association Malienne des Étudiants et Stagiaires</div>
      </div>

      <!-- Menu -->
      <nav class="mt-4 space-y-1 text-sm">
        <div v-for="it in items" :key="it.route" v-show="it.enabled" class="group relative">
          <Link
            :href="hrefSafe(it.route)"
            prefetch
            @click="onNav"
            :aria-current="itemIsActive(it) ? 'page' : undefined"
            :class="[
              'flex items-center gap-3 px-3 py-2 rounded-lg transition',
              itemIsActive(it)
                ? 'bg-gradient-to-r from-green-500 to-yellow-500 text-white shadow-lg'
                : 'hover:bg-green-100/50 text-gray-700 hover:text-green-800'
            ]"
          >
            <span class="inline-flex w-6 justify-center">
              <svg viewBox="0 0 24 24" width="18" height="18">
                <path :d="iconPath(it.icon)" :fill="itemIsActive(it) ? 'white' : 'currentColor'"/>
              </svg>
            </span>
            <span v-if="!sidebarCompact" class="flex-1 truncate">{{ it.label }}</span>
            <span
              v-if="it.badge && !sidebarCompact"
              class="ml-auto inline-flex items-center justify-center px-2 h-5 rounded bg-green-500/20 text-green-700 text-xs font-semibold"
            >
              {{ it.badge() }}
            </span>
          </Link>

          <!-- Tooltip en mode compact -->
          <div
            v-if="sidebarCompact"
            class="pointer-events-none absolute left-[calc(100%+8px)] top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition
                   bg-white/95 ring-1 ring-green-200 rounded-md px-2 py-1 text-xs whitespace-nowrap shadow-lg z-50"
          >
            {{ it.label }}
          </div>
        </div>
      </nav>

      <!-- Footer sidebar -->
      <div v-if="!sidebarCompact" class="mt-6 text-[11px] text-gray-500 px-2">
        <div class="border-t border-green-200 pt-3">
          <div class="font-semibold text-green-700 mb-1">AMEST-Sahel</div>
          <div class="text-gray-600">Back-office administration</div>
          <div class="mt-2">© {{ year }} - Tous droits réservés</div>
        </div>
      </div>
    </aside>

    <!-- HEADER + CONTENU -->
    <div class="h-full flex flex-col">
      <header class="sticky top-0 z-30 h-14 border-b border-green-200 bg-white/90 backdrop-blur flex items-center gap-2">
        <button type="button" class="p-3 md:hidden text-green-700" @click="sidebarOpen=!sidebarOpen" aria-label="Menu">
          <svg viewBox="0 0 24 24" width="20" height="20"><path :d="icons.dash" fill="currentColor"/></svg>
        </button>

        <!-- Barre recherche (ouvre palette) -->
        <div class="relative hidden sm:block ml-1" @click="openPalette">
          <input
            id="hdr-search"
            readonly
            placeholder="Rechercher (⌘/Ctrl K)"
            class="h-9 w-[280px] rounded-lg bg-green-50 ring-1 ring-green-300 pl-8 pr-3 text-sm outline-none cursor-text text-gray-700 placeholder:text-green-600/60"
          >
          <span class="absolute left-2 top-1/2 -translate-y-1/2 opacity-70 pointer-events-none text-green-600">
            <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.search" fill="currentColor"/></svg>
          </span>
        </div>

        <!-- Compte (droite) -->
        <div class="ml-auto px-4 relative">
          <details class="group" ref="accountRef">
            <summary class="list-none flex items-center gap-3 cursor-pointer select-none">
              <div class="text-right hidden sm:block">
                <div class="text-sm font-semibold text-gray-800 truncate max-w-[180px]">{{ userName }}</div>
                <div class="text-xs text-green-600 truncate max-w-[220px]">{{ userEmail }}</div>
              </div>
              <div class="w-9 h-9 rounded-full bg-gradient-to-r from-green-100 to-yellow-100 ring-2 ring-green-300 grid place-items-center font-bold text-green-800">
                {{ userName.substring(0,1).toUpperCase() }}
              </div>
            </summary>

            <!-- Menu compte -->
            <div class="absolute right-4 mt-2 w-[320px] rounded-xl bg-white/95 ring-1 ring-green-200 backdrop-blur shadow-2xl p-2">
              <!-- Header compte -->
              <div class="px-3 py-2">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-r from-green-100 to-yellow-100 flex items-center justify-center font-bold text-green-800">
                    {{ userName.substring(0,1).toUpperCase() }}
                  </div>
                  <div>
                    <div class="text-sm font-semibold text-gray-800">{{ userName }}</div>
                    <div class="text-xs text-green-600">{{ userEmail }}</div>
                  </div>
                </div>
                <div class="mt-3 flex flex-wrap gap-1">
                  <span
                    v-for="r in (user?.roles || [])"
                    :key="r"
                    class="px-1.5 py-0.5 rounded bg-green-100 text-green-700 text-[10px] uppercase tracking-wide"
                  >{{ r }}</span>
                  <span v-if="user?.two_factor_enabled" class="px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-700 text-[10px]">
                    2FA ON
                  </span>
                </div>
                <div v-if="user?.last_login_at" class="mt-2 text-[11px] text-gray-500 bg-green-50 rounded px-2 py-1">
                  Dernière connexion : {{ user?.last_login_at }}
                </div>
              </div>

              <div class="my-2 h-px bg-green-200"></div>

              <!-- Liens rapides -->
              <Link :href="hrefSafe('admin.account.security')" @click="closeAccount"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-green-50 text-gray-700 hover:text-green-700">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.security" fill="currentColor"/></svg>
                Compte & sécurité
              </Link>

              <Link v-if="can('contacts.view') || has('admin')" :href="hrefSafe('admin.members.index')" @click="closeAccount"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-green-50 text-gray-700 hover:text-green-700">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.members" fill="currentColor"/></svg>
                Gérer les membres
              </Link>

              <Link v-if="can('projects.view') || has('admin')" :href="hrefSafe('admin.activities.index')" @click="closeAccount"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-green-50 text-gray-700 hover:text-green-700">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.activities" fill="currentColor"/></svg>
                Activités & événements
              </Link>

              <Link v-if="can('posts.view') || has('admin')" :href="hrefSafe('admin.announcements.index')" @click="closeAccount"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-green-50 text-gray-700 hover:text-green-700">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.announcements" fill="currentColor"/></svg>
                Annonces ({{ counts.drafts ?? 0 }})
              </Link>

              <Link :href="hrefSafe('home')" @click="closeAccount"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-green-50 text-gray-700 hover:text-green-700">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.dash" fill="currentColor"/></svg>
                Voir le site public
              </Link>

              <button type="button" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-green-50 text-gray-700 hover:text-green-700"
                      @click="openPalette">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.search" fill="currentColor"/></svg>
                Palette de commandes <span class="ml-auto text-[11px] text-green-600">⌘/Ctrl K</span>
              </button>

              <div class="my-2 h-px bg-green-200"></div>

              <!-- Déconnexion -->
              <button type="button" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-red-50 text-red-600 hover:text-red-700"
                      @click="router.post(hrefSafe('logout'))">
                <svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8a8 8 0 0 1-8 8zm4-9H8v2h8z"/></svg>
                Déconnexion
              </button>
            </div>
          </details>
        </div>
      </header>

      <!-- Contenu principal -->
      <main class="flex-1 overflow-y-auto p-6 bg-gradient-to-b from-green-50/30 to-transparent">
        <slot />
      </main>
    </div>

    <!-- PALETTE DE COMMANDES -->
    <div v-if="paletteOpen" class="fixed inset-0 z-[60] bg-black/40 backdrop-blur-sm" @click="paletteOpen=false">
      <div class="max-w-xl mx-auto mt-28 px-4" @click.stop>
        <div class="rounded-2xl overflow-hidden ring-1 ring-green-300 bg-white/95 shadow-2xl">
          <div class="relative">
            <input id="cmdk" v-model="paletteQ" placeholder="Rechercher une section... — ESC pour fermer"
                   class="w-full h-12 bg-transparent px-4 pr-12 outline-none text-sm text-gray-700 placeholder:text-green-600/60">
            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-green-600 hover:text-green-800"
                    @click="paletteOpen=false">✕</button>
          </div>
          <div class="max-h-80 overflow-y-auto divide-y divide-green-200">
            <button v-for="i in paletteList" :key="i.route" type="button"
                    class="w-full px-4 py-2.5 text-left hover:bg-green-50 flex items-center gap-3 text-gray-700"
                    @click="goRoute(i.route)">
              <svg viewBox="0 0 24 24" width="16" height="16"><path :d="iconPath(i.icon)" fill="currentColor" class="text-green-600"/></svg>
              <span class="text-sm">{{ i.label }}</span>
              <span class="ml-auto text-[11px] text-green-600">{{ i.route }}</span>
            </button>
            <div v-if="!paletteList.length" class="px-4 py-6 text-sm text-gray-500 text-center">
              Aucune correspondance trouvée.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Scrollbars personnalisés pour AMEST */
::-webkit-scrollbar{ width:10px; height:10px }
::-webkit-scrollbar-track{ background:rgba(220, 193, 118, 0.1); border-radius:10px }
::-webkit-scrollbar-thumb{ background:rgba(5, 150, 105, 0.3); border-radius:10px; border:2px solid transparent; background-clip:content-box }
::-webkit-scrollbar-thumb:hover{ background:rgba(5, 150, 105, 0.5); border:2px solid transparent; background-clip:content-box }
</style>
