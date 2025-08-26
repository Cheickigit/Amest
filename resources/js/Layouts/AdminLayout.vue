<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

type User = { name?:string; email?:string; roles?:string[]; permissions?:string[]; last_login_at?:string; two_factor_enabled?:boolean }
type Counts = { leads?:number; rfps?:number; drafts?:number }
type PageProps = { auth?:{user?:User|null}, adminCounts?:Counts, csrf_token?:string }

const page   = usePage<PageProps>()
const user   = computed(() => page.props.auth?.user ?? null)
const counts = computed<Counts>(() => page.props.adminCounts ?? { leads:0, rfps:0, drafts:0 })

const has = (role:string)=>!!user.value?.roles?.includes(role)
const can = (perm:string)=>!!user.value?.permissions?.includes(perm)

/* Ziggy helpers */
function hasRoute(name:string){ try{ return (ziggyRoute as any)().has(name) }catch{ return false } }
function href(name:string, params?:any, absolute?:boolean){
  try{ return ziggyRoute(name, params ?? {}, absolute, (window as any).Ziggy) }catch{ return '#' }
}

/* Sidebar state */
const sidebarOpen    = ref(true)   // mobile visible
const sidebarCompact = ref(false)  // desktop compact
onMounted(()=>{
  const a = localStorage.getItem('bko.sb'); if(a) sidebarOpen.value    = a==='1'
  const b = localStorage.getItem('bko.sb.c'); if(b) sidebarCompact.value = b==='1'
})
watch(sidebarOpen,    v=>localStorage.setItem('bko.sb',   v?'1':'0'))
watch(sidebarCompact, v=>localStorage.setItem('bko.sb.c', v?'1':'0'))

/* Online indicator */
const online = ref(navigator.onLine)
const onOnline=()=>online.value=true, onOffline=()=>online.value=false
onMounted(()=>{ addEventListener('online',onOnline); addEventListener('offline',onOffline) })
onBeforeUnmount(()=>{ removeEventListener('online',onOnline); removeEventListener('offline',onOffline) })

/* Active route */
const currentUrl = computed(()=>page.url)
const isActive = (activeOn:string[]) => activeOn.some(p=>currentUrl.value.startsWith('/'+p))

/* Menu */
type IconKey='dash'|'projects'|'quotes'|'rfps'|'contacts'|'posts'|'users'|'settings'|'security'
type Item = { label:string; icon:IconKey; route:string; activeOn:string[]; enabled:boolean; badge?:()=>number }
const items = computed<Item[]>(()=>[
  { label:'Tableau de bord', icon:'dash',     route:'admin.dashboard',      activeOn:['admin.dashboard'],      enabled:true },
  { label:'Réalisations',    icon:'projects', route:'admin.projects.index', activeOn:['admin.projects.'],      enabled: can('projects.view')||has('admin') },
  { label:'Devis & leads',   icon:'quotes',   route:'admin.quotes.index',   activeOn:['admin.quotes.'],  badge:()=>counts.value.leads??0,  enabled: can('quotes.view')||has('admin') },
  { label:'Appels d’offres', icon:'rfps',     route:'admin.rfps.index',     activeOn:['admin.rfps.'],    badge:()=>counts.value.rfps ??0,  enabled: can('rfps.view')  ||has('admin') },
  { label:'Contacts',        icon:'contacts', route:'admin.contacts.index', activeOn:['admin.contacts.'],      enabled: can('contacts.view')||has('admin') },
  { label:'Actualités',      icon:'posts',    route:'admin.posts.index',    activeOn:['admin.posts.'],   badge:()=>counts.value.drafts??0, enabled: can('cms.manage') ||has('admin') },
  { label:'Utilisateurs',    icon:'users',    route:'admin.users.index',    activeOn:['admin.users.'],         enabled: can('users.manage')||has('admin') },
  { label:'Compte & sécurité',icon:'security',route:'admin.account.security',activeOn:['admin.account.security'],enabled:true },
  { label:'Paramètres',      icon:'settings', route:'admin.settings.index', activeOn:['admin.settings.'],      enabled: has('admin') },
])

/* Palette */
const paletteOpen=ref(false), paletteQ=ref('')
const paletteList = computed(()=>items.value.filter(i=>i.enabled&&hasRoute(i.route))
                                           .filter(i=>i.label.toLowerCase().includes(paletteQ.value.toLowerCase())))
function openPalette(){ paletteOpen.value=true; requestAnimationFrame(()=> (document.getElementById('cmdk') as HTMLInputElement)?.focus()) }
function go(h:string){ router.visit(h); paletteOpen.value=false; paletteQ.value='' }
</script>

<template>
  <!-- h-screen + overflow-hidden => la page ne scrolle pas; seul le contenu scrolle -->
  <div :class="['h-screen bg-bk-night text-bk-off overflow-hidden', sidebarCompact ? 'md:pl-20' : 'md:pl-72']">
    <!-- SIDEBAR FIXE -->
    <aside
      :class="[
        'fixed z-40 inset-y-0 left-0 p-4 border-r border-white/10 overflow-y-auto',
        'backdrop-blur bg-[radial-gradient(1200px_600px_at_-10%_-20%,rgba(220,193,118,.08),transparent),linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.02))]',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
        sidebarCompact ? 'w-20' : 'w-72',
        'transition-transform'
      ]">
      <!-- Brand + actions -->
      <div class="flex items-center justify-between h-12 px-2">
        <div class="flex items-center gap-2">
          <img src="/assets/logo-bk.jpeg" class="h-8 w-auto object-contain" alt="BKO Construction Admin">
          <div v-if="!sidebarCompact" class="font-display text-xl">BKO <span class="text-bk-gold">Construction</span></div>
        </div>
        <button type="button"
                class="hidden md:inline-flex items-center gap-1 text-xs rounded-md px-2 py-1 bg-white/10 ring-1 ring-white/15 hover:ring-bk-gold/50"
                :title="sidebarCompact ? 'Étendre' : 'Réduire'"
                @click="sidebarCompact=!sidebarCompact">
          <svg v-if="sidebarCompact" viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M9 6l6 6-6 6"/></svg>
          <svg v-else            viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M15 6l-6 6 6 6"/></svg>
          <span v-if="!sidebarCompact">Réduire</span>
        </button>
      </div>

      <div class="px-2 text-[11px] text-white/60 flex items-center gap-2">
        <span :class="['inline-block w-2 h-2 rounded-full', online ? 'bg-emerald-400' : 'bg-red-400']"></span>
        <span v-if="!sidebarCompact">{{ online ? 'En ligne' : 'Hors ligne' }}</span>
      </div>

      <nav class="mt-4 space-y-1 text-sm">
        <div v-for="it in items" :key="it.label" class="group relative">
          <Link :href="href(it.route)"
                :class="[
                  'flex items-center gap-3 px-3 py-2 rounded-lg transition',
                  isActive(it.activeOn) ? 'bg-bk-gold text-bk-night shadow-[0_12px_26px_-12px_rgba(220,193,118,.55)]' : 'hover:bg-white/10',
                  !it.enabled ? 'opacity-50 pointer-events-none' : ''
                ]">
            <span class="inline-flex w-6 justify-center">
              <svg v-if="it.icon==='dash'" viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
              <svg v-else-if="it.icon==='projects'" viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M3 5h18v2H3V5zm0 6h18v2H3v-2zm0 6h12v2H3v-2z"/></svg>
              <svg v-else-if="it.icon==='quotes'" viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M21 6h-8l-2-2H3v16h18V6z"/></svg>
              <svg v-else-if="it.icon==='rfps'" viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M19 3H5a2 2 0 00-2 2v14l4-4h12a2 2 0 002-2V5a2 2 0 00-2-2z"/></svg>
              <svg v-else-if="it.icon==='contacts'" viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M12 12a5 5 0 100-10 5 5 0 000 10zm-9 9a9 9 0 1118 0H3z"/></svg>
              <svg v-else-if="it.icon==='posts'" viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M4 4h16v2H4V4zm0 5h16v2H4V9zm0 5h10v2H4v-2z"/></svg>
              <svg v-else-if="it.icon==='users'" viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M16 11a4 4 0 10-8 0 4 4 0 008 0zM4 20a8 8 0 1116 0H4z"/></svg>
              <svg v-else-if="it.icon==='settings'" viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M19.14 12.94a7.07 7.07 0 000-1.88l2.03-1.58a.5.5 0 00.12-.64l-1.92-3.32a.5.5 0 00-.6-.22l-2.39.96a7.1 7.1 0 00-1.62-.94l-.36-2.54a.5.5 0 00-.5-.42h-3.84a.5.5 0 00-.5-.42l-.36 2.54c-.58-.23 1.12-.54 1.62-.94l-2.39-.96a.5.5 0 00-.6-.22L2.7 8.84a.5.5 0 00.12.64l2.03 1.58a7.07 7.07 0 000 1.88L2.82 14.6a.5.5 0 00-.12.64l1.92 3.32a.5.5 0 00.6.22l2.39-.96c.5.4 1.04.71 1.62.94l.36 2.54a.5.5 0 00.5.42h3.84a.5.5 0 00.5-.42l.36-2.54c.58-.23 1.12-.54 1.62-.94l2.39.96a.5.5 0 00.6-.22l1.92-3.32a.5.5 0 00-.12-.64l-2.03-1.66zM12 15a3 3 0 110-6 3 3 0 010 6z"/></svg>
              <svg v-else-if="it.icon==='security'" viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M12 1l9 4v6c0 5-3.8 9.7-9 11-5.2-1.3-9-6-9-11V5l9-4zM7 11h10v2H7v-2z"/></svg>
            </span>
            <span v-if="!sidebarCompact" class="flex-1 truncate">{{ it.label }}</span>
            <span v-if="it.badge" class="ml-auto inline-flex items-center justify-center px-2 h-5 rounded bg-bk-gold/20 text-bk-gold text-xs font-semibold">{{ it.badge() }}</span>
          </Link>
          <div v-if="sidebarCompact" class="pointer-events-none absolute left-[calc(100%+8px)] top-1/2 -translate-y-1/2
                                           opacity-0 group-hover:opacity-100 transition bg-[#0f141a]/95 ring-1 ring-white/15
                                           rounded-md px-2 py-1 text-xs whitespace-nowrap">
            {{ it.label }}
          </div>
        </div>
      </nav>

      <div v-if="!sidebarCompact" class="mt-6 text-[11px] text-white/45 px-2">© {{ new Date().getFullYear() }} BKO Construction · Back-office</div>
    </aside>

    <!-- CONTENU: header sticky + zone scrollable -->
    <div class="h-full flex flex-col">
      <header class="sticky top-0 z-30 h-14 border-b border-white/10 bg-bk-night/80 backdrop-blur flex items-center gap-2">
        <button type="button" class="p-3 md:hidden" @click="sidebarOpen=!sidebarOpen" aria-label="Menu">
          <svg viewBox="0 0 24 24" class="w-6 h-6"><path fill="currentColor" d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
        <div class="px-1 font-semibold">BKO Construction · Admin</div>
        <div class="ml-auto px-4 relative">
          <details class="group">
            <summary class="list-none flex items-center gap-3 cursor-pointer select-none">
              <div class="text-right hidden sm:block">
                <div class="text-sm font-semibold truncate max-w-[160px]">{{ user?.name ?? 'Compte' }}</div>
                <div class="text-xs text-white/60 truncate max-w-[200px]">{{ user?.email }}</div>
              </div>
              <div class="w-9 h-9 rounded-full bg-white/10 ring-1 ring-white/10 grid place-items-center font-bold">
                {{ (user?.name || 'U').substring(0,1).toUpperCase() }}
              </div>
            </summary>
            <div class="absolute right-4 mt-2 w-72 rounded-xl bg-[#0f141a]/95 ring-1 ring-white/15 backdrop-blur shadow-2xl p-2">
              <Link :href="href('admin.account.security')" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10">
                <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M12 1l9 4v6c0 5-3.8 9.7-9 11-5.2-1.3-9-6-9-11V5l9-4zM7 11h10v2H7v-2z"/></svg>
                Compte & sécurité
              </Link>
              <Link :href="href('home')" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10">
                <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                Voir le site
              </Link>
              <button type="button" @click="openPalette" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10">
                <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M3 5h18v2H3v-2h18V7H3zm0 6h18v2H3v-2zm0 6h12v2H3v-2z"/></svg>
                Palette de commandes <span class="ml-auto text-[11px] text-white/50">⌘/Ctrl K</span>
              </button>
              <div class="px-2 pt-2 border-t border-white/10 mt-2">
                <button type="button" class="w-full text-left flex items-center gap-2 px-1.5 py-2 rounded-lg hover:bg-white/10"
                        @click="router.post(href('logout'))">
                  <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M14 7v-2H4v14h10v-2H6V7h8zm7 5l-4-4v3h-5v2h5v3l4-4z"/></svg>
                  Déconnexion
                </button>
              </div>
            </div>
          </details>
        </div>
      </header>

      <!-- zone scrollable -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>
    </div>

    <!-- Palette -->
    <div v-if="paletteOpen" class="fixed inset-0 z-[60] bg-black/60 backdrop-blur-sm" @click="paletteOpen=false">
      <div class="max-w-xl mx-auto mt-28 px-4" @click.stop>
        <div class="rounded-2xl overflow-hidden ring-1 ring-white/15 bg-[#0f141a]/95">
          <div class="relative">
            <input id="cmdk" v-model="paletteQ" placeholder="Aller à… — ESC pour fermer" class="w-full h-12 bg-transparent px-4 pr-12 outline-none text-sm placeholder:text-white/50">
            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white" @click="paletteOpen=false" aria-label="Fermer">✕</button>
          </div>
          <div class="max-h-80 overflow-y-auto divide-y divide-white/10">
            <button v-for="i in paletteList" :key="i.route" type="button"
                    class="w-full px-4 py-2.5 text-left hover:bg-white/5 flex items-center gap-3"
                    @click="go(href(i.route))">
              <span class="inline-flex w-5 justify-center">📄</span>
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
