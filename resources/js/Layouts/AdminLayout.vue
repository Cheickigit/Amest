<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

type User = { name?:string; email?:string; roles?:string[]; permissions?:string[]; last_login_at?:string; two_factor_enabled?:boolean }
type Counts = { leads?:number; rfps?:number; drafts?:number }
type PageProps = { auth?:{ user?:User|null }, adminCounts?:Counts, csrf_token?:string }

const page   = usePage<PageProps>()
const user   = computed(()=> page.props.auth?.user ?? null)
const counts = computed<Counts>(()=> page.props.adminCounts ?? { leads:0, rfps:0, drafts:0 })

const userName  = computed(()=> user.value?.name  || 'Compte')
const userEmail = computed(()=> user.value?.email || '')
const has = (role:string)=> !!user.value?.roles?.includes(role)
const can = (perm:string)=> !!user.value?.permissions?.includes(perm)

/* Ziggy */
const Zig = computed<any|undefined>(() => (window as any)?.Ziggy)
/* >>> PLUS DE FILTRE: on calcule toujours l'URL, et on fallback sur '#' si erreur */
function hrefSafe(name:string, params?:any, absolute=false): string {
  try { return ziggyRoute(name, params ?? {}, absolute, Zig.value) } catch { return '#' }
}

/* UI */
const sidebarOpen    = ref(true)
const sidebarCompact = ref(false)
onMounted(()=> {
  sidebarOpen.value    = (localStorage.getItem('bko.sb')   ?? '1') === '1'
  sidebarCompact.value = (localStorage.getItem('bko.sb.c') ?? '0') === '1'
})
watch(sidebarOpen,    v=> localStorage.setItem('bko.sb',   v?'1':'0'))
watch(sidebarCompact, v=> localStorage.setItem('bko.sb.c', v?'1':'0'))

/* Online */
const online = ref(typeof navigator !== 'undefined' ? navigator.onLine : true)
const onOnline=()=> online.value=true, onOffline=()=> online.value=false
onMounted(()=>{ addEventListener('online', onOnline); addEventListener('offline', onOffline) })
onBeforeUnmount(()=>{ removeEventListener('online', onOnline); removeEventListener('offline', onOffline) })

/* Actif */
const currentUrl = computed(()=> page.url || '')
const isActive = (prefixes:string[]) => prefixes.some(p => currentUrl.value.startsWith(p.startsWith('/')?p:`/${p}`))

/* Icônes */
const icons = {
  dash:'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z',
  projects:'M3 5h18v2H3V5zm0 6h18v2H3v-2zm0 6h12v2H3v-2z',
  quotes:'M21 6h-8l-2-2H3v16h18V6z',
  rfps:'M19 3H5a2 2 0 0 0-2 2v14l4-4h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z',
  contacts:'M12 12a5 5 0 1 0-5-5a5 5 0 0 0 5 5Zm-9 9a9 9 0 1 1 18 0H3z',
  posts:'M4 4h16v2H4V4zm0 5h16v2H4V9zm0 5h10v2H4v-2z',
  users:'M16 11a4 4 0 1 0-8 0a4 4 0 0 0 8 0zM4 20a8 8 0 1 1 16 0H4z',
  settings:'M19.14 12.94a7.07 7.07 0 0 0 0-1.88l2.03-1.58l-1.92-3.32l-2.39.96a7.1 7.1 0 0 0-1.62-.94l-.36-2.54h-3.84l-.36 2.54c-.58.23-1.12.54-1.62.94l-2.39-.96L2.7 8.84l2.03 1.58a7.07 7.07 0 0 0 0 1.88L2.82 14.6l1.92 3.32l2.39-.96c.5.4 1.04.71 1.62.94l.36 2.54h3.84l.36-2.54c.58-.23 1.12-.54 1.62-.94l2.39.96l1.92-3.32l-2.03-1.66zM12 15a3 3 0 1 1 0-6 3 3 0 0 1 0 6z',
  security:'M12 1l9 4v6c0 5-3.8 9.7-9 11c-5.2-1.3-9-6-9-11V5l9-4zM7 11h10v2H7z',
  search:'M10 2a8 8 0 1 0 5.29 14.29l4.21 4.21l1.41-1.41l-4.21-4.21A8 8 0 0 0 10 2z',
} as const
type IconKey = keyof typeof icons
const iconPath = (k:IconKey) => icons[k] ?? icons.dash

/* Menu (sans filtre Ziggy) */
type Item = { label:string; icon:IconKey; route:string; activeOn:string[]; enabled:boolean; badge?:()=>number }
const items = computed<Item[]>(() => [
  { label:'Tableau de bord',  icon:'dash',     route:'admin.dashboard',        activeOn:['/admin'],           enabled:true },
  { label:'Réalisations',     icon:'projects', route:'admin.projects.index',   activeOn:['/admin/projects'],  enabled: can('projects.view') || has('admin') },
  { label:'Devis & leads',    icon:'quotes',   route:'admin.quotes.index',     activeOn:['/admin/quotes'],    enabled: can('quotes.view')  || has('admin'), badge:()=>counts.value.leads  ?? 0 },
  { label:'Appels d’offres',  icon:'rfps',     route:'admin.rfps.index',       activeOn:['/admin/rfps'],      enabled: can('rfps.view')    || has('admin'), badge:()=>counts.value.rfps   ?? 0 },
  { label:'Contacts',         icon:'contacts', route:'admin.contacts.index',   activeOn:['/admin/contacts'],  enabled: can('contacts.view')|| has('admin') },
  { label:'Actualités',       icon:'posts',    route:'admin.posts.index',      activeOn:['/admin/posts'],     enabled: can('cms.manage')   || has('admin'), badge:()=>counts.value.drafts ?? 0 },
  { label:'Utilisateurs',     icon:'users',    route:'admin.users.index',      activeOn:['/admin/users'],     enabled: can('users.manage') || has('admin') },
  { label:'Compte & sécurité',icon:'security', route:'admin.account.security', activeOn:['/admin/account'],   enabled:true },
  { label:'Paramètres',       icon:'settings', route:'admin.settings.index',   activeOn:['/admin/settings'],  enabled: has('admin') },
])

/* Palette */
const paletteOpen = ref(false)
const paletteQ    = ref('')
const paletteList = computed(() =>
  items.value
    .filter(i => i.enabled)
    .filter(i => i.label.toLowerCase().includes(paletteQ.value.toLowerCase()))
)
function openPalette(){ paletteOpen.value = true; requestAnimationFrame(()=> (document.getElementById('cmdk') as HTMLInputElement)?.focus()) }
function goRoute(name:string){ router.visit(hrefSafe(name)); paletteOpen.value=false; paletteQ.value='' }

const year = new Date().getFullYear()
</script>

<template>
  <div :class="['h-screen bg-bk-night text-bk-off overflow-hidden', sidebarCompact ? 'md:pl-20' : 'md:pl-72']">
    <!-- SIDEBAR -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-40 p-4 border-r border-white/10 overflow-y-auto transition-transform',
        'backdrop-blur bg-[radial-gradient(1200px_600px_at_-10%_-20%,rgba(220,193,118,.10),transparent),linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.02))]',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
        sidebarCompact ? 'w-20' : 'w-72'
      ]"
    >
      <div class="flex items-center justify-between h-12 px-2">
        <div class="flex items-center gap-2">
          <img src="/assets/logo-bk.jpeg" class="h-8 w-auto object-contain" alt="BKO Construction" />
          <div v-if="!sidebarCompact" class="font-display text-xl">BKO <span class="text-bk-gold">Construction</span></div>
        </div>
        <button type="button" class="hidden md:inline-flex items-center gap-1 text-xs rounded-md px-2 py-1 bg-white/10 ring-1 ring-white/15 hover:ring-bk-gold/50"
                :title="sidebarCompact ? 'Étendre' : 'Réduire'" @click="sidebarCompact=!sidebarCompact">
          <svg viewBox="0 0 24 24" width="14" height="14"><path :d="icons.dash" fill="currentColor"/></svg>
          <span v-if="!sidebarCompact">Réduire</span>
        </button>
      </div>

      <div class="px-2 text-[11px] text-white/60 flex items-center gap-2">
        <span :class="['inline-block w-2 h-2 rounded-full', online ? 'bg-emerald-400' : 'bg-red-400']"></span>
        <span v-if="!sidebarCompact">{{ online ? 'En ligne' : 'Hors ligne' }}</span>
      </div>

      <nav class="mt-4 space-y-1 text-sm">
        <div v-for="it in items" :key="it.route" v-show="it.enabled" class="group relative">
          <Link :href="hrefSafe(it.route)" prefetch
                :aria-current="isActive(it.activeOn) ? 'page' : undefined"
                :class="[
                  'flex items-center gap-3 px-3 py-2 rounded-lg transition',
                  isActive(it.activeOn) ? 'bg-bk-gold text-bk-night shadow-[0_18px_38px_-18px_rgba(220,193,118,.60)]'
                                        : 'hover:bg-white/10'
                ]">
            <span class="inline-flex w-6 justify-center">
              <svg viewBox="0 0 24 24" width="18" height="18"><path :d="iconPath(it.icon)" fill="currentColor"/></svg>
            </span>
            <span v-if="!sidebarCompact" class="flex-1 truncate">{{ it.label }}</span>
            <span v-if="it.badge && !sidebarCompact" class="ml-auto inline-flex items-center justify-center px-2 h-5 rounded bg-bk-gold/20 text-bk-gold text-xs font-semibold">
              {{ it.badge() }}
            </span>
          </Link>
          <div v-if="sidebarCompact"
               class="pointer-events-none absolute left-[calc(100%+8px)] top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition
                      bg-[#0f141a]/95 ring-1 ring-white/15 rounded-md px-2 py-1 text-xs whitespace-nowrap">{{ it.label }}</div>
        </div>
      </nav>

      <div v-if="!sidebarCompact" class="mt-6 text-[11px] text-white/45 px-2">© {{ year }} BKO Construction · Back-office</div>
    </aside>

    <!-- HEADER + CONTENU -->
    <div class="h-full flex flex-col">
      <header class="sticky top-0 z-30 h-14 border-b border-white/10 bg-bk-night/80 backdrop-blur flex items-center gap-2">
        <button type="button" class="p-3 md:hidden" @click="sidebarOpen=!sidebarOpen" aria-label="Menu">
          <svg viewBox="0 0 24 24" width="20" height="20"><path :d="icons.dash" fill="currentColor"/></svg>
        </button>

        <div class="relative hidden sm:block ml-1" @click="openPalette">
          <input id="hdr-search" readonly placeholder="Rechercher (⌘/Ctrl K)"
                 class="h-9 w-[280px] rounded-lg bg-white/[.06] ring-1 ring-white/10 pl-8 pr-3 text-sm outline-none cursor-text">
          <span class="absolute left-2 top-1/2 -translate-y-1/2 opacity-70 pointer-events-none">
            <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.search" fill="currentColor"/></svg>
          </span>
        </div>

        <div class="ml-auto px-4 relative">
          <details class="group">
            <summary class="list-none flex items-center gap-3 cursor-pointer select-none">
              <div class="text-right hidden sm:block">
                <div class="text-sm font-semibold truncate max-w-[180px]">{{ userName }}</div>
                <div class="text-xs text-white/60 truncate max-w-[220px]">{{ userEmail }}</div>
              </div>
              <div class="w-9 h-9 rounded-full bg-white/10 ring-1 ring-white/10 grid place-items-center font-bold">
                {{ userName.substring(0,1).toUpperCase() }}
              </div>
            </summary>

            <div class="absolute right-4 mt-2 w-[320px] rounded-xl bg-[#0f141a]/95 ring-1 ring-white/15 backdrop-blur shadow-2xl p-2">
              <div class="px-3 py-2">
                <div class="text-sm font-semibold">{{ userName }}</div>
                <div class="text-xs text-white/60">{{ userEmail }}</div>
                <div class="mt-2 flex flex-wrap gap-1">
                  <span v-for="r in (user?.roles || [])" :key="r" class="px-1.5 py-0.5 rounded bg-white/10 text-[10px] uppercase tracking-wide">{{ r }}</span>
                  <span v-if="user?.two_factor_enabled" class="px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 text-[10px]">2FA ON</span>
                </div>
                <div v-if="user?.last_login_at" class="mt-1 text-[11px] text-white/45">Dernière connexion : {{ user?.last_login_at }}</div>
              </div>

              <div class="my-1 h-px bg-white/10"></div>

              <Link :href="hrefSafe('admin.account.security')" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.security" fill="currentColor"/></svg>
                Compte & sécurité
              </Link>
              <Link v-if="can('cms.manage') || has('admin')" :href="hrefSafe('admin.posts.index')" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.posts" fill="currentColor"/></svg>
                Actualités ({{ counts.drafts ?? 0 }})
              </Link>
              <Link v-if="can('projects.create') || has('admin')" :href="hrefSafe('admin.projects.create')" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.projects" fill="currentColor"/></svg>
                Nouveau projet
              </Link>
              <Link :href="hrefSafe('home')" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.dash" fill="currentColor"/></svg>
                Voir le site
              </Link>

              <button type="button" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10" @click="openPalette">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.search" fill="currentColor"/></svg>
                Palette de commandes <span class="ml-auto text-[11px] text-white/50">⌘/Ctrl K</span>
              </button>

              <div class="my-1 h-px bg-white/10"></div>

              <button type="button" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10" @click="router.post(hrefSafe('logout'))">
                <svg viewBox="0 0 24 24" width="16" height="16"><path :d="icons.settings" fill="currentColor"/></svg>
                Déconnexion
              </button>
            </div>
          </details>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>
    </div>

    <!-- PALETTE -->
    <div v-if="paletteOpen" class="fixed inset-0 z-[60] bg-black/60 backdrop-blur-sm" @click="paletteOpen=false">
      <div class="max-w-xl mx-auto mt-28 px-4" @click.stop>
        <div class="rounded-2xl overflow-hidden ring-1 ring-white/15 bg-[#0f141a]/95">
          <div class="relative">
            <input id="cmdk" v-model="paletteQ" placeholder="Aller à… — ESC pour fermer"
                   class="w-full h-12 bg-transparent px-4 pr-12 outline-none text-sm placeholder:text-white/50">
            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white" @click="paletteOpen=false">✕</button>
          </div>
          <div class="max-h-80 overflow-y-auto divide-y divide-white/10">
            <button v-for="i in paletteList" :key="i.route" type="button" class="w-full px-4 py-2.5 text-left hover:bg-white/5 flex items-center gap-3"
                    @click="goRoute(i.route)">
              <svg viewBox="0 0 24 24" width="16" height="16"><path :d="iconPath(i.icon)" fill="currentColor"/></svg>
              <span class="text-sm">{{ i.label }}</span>
              <span class="ml-auto text-[11px] text-white/40">{{ i.route }}</span>
            </button>
            <div v-if="!paletteList.length" class="px-4 py-6 text-sm text-white/60">Aucune correspondance.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
