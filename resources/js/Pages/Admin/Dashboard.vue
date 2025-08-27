<!-- resources/js/Pages/Admin/Dashboard.vue -->
<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, onMounted, onBeforeUnmount, ref, watch } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

defineOptions({ layout: AdminLayout })

/* ---------- Types ---------- */
type Metric = { label: string; value: number | string; delta?: number; hint?: string }
type TrendPoint = { x: string; y: number }
type Trend = { label: string; data: TrendPoint[] }
type Activity = { id: number|string; type: string; title: string; when: string; url?: string }
type Props = {
  filters: { range?: '7d' | '30d' | 'q' | 'ytd' }
  metrics: Partial<Record<'projects'|'leads'|'rfps'|'posts', Metric>>
  trends: { leads: Trend; projects?: Trend }
  bySegment: Array<{ label: string; value: number }>
  slas: { ontime: number; quality: number; satisfaction: number }
  activity: Activity[]
}

const page  = usePage<Props>()
const props = computed(()=> page.props)

/* Ziggy helper */
function r(name: string, params?: any, absolute = false, fallback = '#') {
  try { return ziggyRoute(name, params ?? {}, absolute, (globalThis as any).Ziggy) } catch { return fallback }
}

/* ---------- UI state ---------- */
const q            = ref('')
const range        = ref<Props['filters']['range']>(props.value.filters?.range ?? '30d')
const autoRefresh  = ref(true)
const lastRefreshed = ref(new Date().toLocaleTimeString())
const refreshEveryMs = 20000
let timer: any = null

onMounted(() => {
  const onKey = (e: KeyboardEvent) => {
    if (e.key === '/') { e.preventDefault(); (document.getElementById('global-search') as HTMLInputElement)?.focus() }
  }
  window.addEventListener('keydown', onKey, { passive: true } as any)
  startAutoRefresh()
})
onBeforeUnmount(() => stopAutoRefresh())

function startAutoRefresh(){ stopAutoRefresh(); if(!autoRefresh.value) return; timer=setInterval(()=>applyFilters(true),refreshEveryMs) }
function stopAutoRefresh(){ if(timer){ clearInterval(timer); timer=null } }
watch(autoRefresh, v => v ? startAutoRefresh() : stopAutoRefresh())

function applyFilters(replace=false) {
  router.get(r('admin.dashboard'), { range: range.value, q: q.value }, { preserveState: true, preserveScroll: true, replace })
  lastRefreshed.value = new Date().toLocaleTimeString()
}
let d:any=null; watch(q,()=>{ clearTimeout(d); d=setTimeout(()=>applyFilters(true),450) })

/* ---------- Utils ---------- */
const fmt = { num: (n: number) => Intl.NumberFormat('fr-FR').format(n), pct: (n: number) => `${n}%` }
function toPath(points: TrendPoint[], w = 420, h = 120): string {
  if (!points?.length) return ''
  const xs = points.map(p => Date.parse(p.x)), ys = points.map(p => p.y)
  const minX = Math.min(...xs), maxX = Math.max(...xs), minY = Math.min(...ys), maxY = Math.max(...ys)
  const dx = (maxX - minX) || 1, dy = (maxY - minY) || 1
  const mapX = (t: number) => ((t - minX) / dx) * (w - 12) + 6
  const mapY = (v: number) => h - (((v - minY) / dy) * (h - 12) + 6)
  const P = points.map(p => [mapX(Date.parse(p.x)), mapY(p.y)])
  if (P.length < 2) return `M ${P[0][0]} ${P[0][1]}`
  const path: string[] = [`M ${P[0][0]} ${P[0][1]}`]
  for (let i = 0; i < P.length - 1; i++) {
    const p0 = P[i - 1] || P[i], p1 = P[i], p2 = P[i + 1], p3 = P[i + 2] || p2
    const c1x = p1[0] + (p2[0] - p0[0]) / 6, c1y = p1[1] + (p2[1] - p0[1]) / 6
    const c2x = p2[0] - (p3[0] - p1[0]) / 6, c2y = p2[1] - (p3[1] - p1[1]) / 6
    path.push(`C ${c1x.toFixed(2)} ${c1y.toFixed(2)}, ${c2x.toFixed(2)} ${c2y.toFixed(2)}, ${p2[0].toFixed(2)} ${p2[1].toFixed(2)}`)
  }
  return path.join(' ')
}

/* ---------- Icônes SVG (paths) ---------- */
const paths = {
  folder: 'M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z',
  users: 'M16 11a4 4 0 1 0-8 0a4 4 0 0 0 8 0zM4 20a8 8 0 1 1 16 0H4z',
  fileText: 'M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6zM14 2v6h6',
  pen: 'M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41L18.37 3.29a1 1 0 0 0-1.41 0l-1.34 1.34l3.75 3.75l1.34-1.34z',
  message: 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10z',
  plus: 'M12 5v14M5 12h14',
  search: 'M10 2a8 8 0 1 0 5.29 14.29l4.21 4.21l1.41-1.41l-4.21-4.21A8 8 0 0 0 10 2z',
  chart: 'M4 19h16M7 15v-6M12 19V5M17 19v-9',
}

/* ---------- KPI data sécurisée ---------- */
const m = computed(() => ({
  projects: props.value.metrics?.projects ?? { label:'Projets', value:0, delta:0, hint:'En portefeuille' },
  leads:    props.value.metrics?.leads    ?? { label:'Leads',   value:0, delta:0, hint:'Derniers 30 jours' },
  rfps:     props.value.metrics?.rfps     ?? { label:'AO',      value:0, delta:0, hint:'Ouverts' },
  posts:    props.value.metrics?.posts    ?? { label:'Articles', value:0, delta:0, hint:'Brouillons' },
}))

/* ---------- Sub-components ---------- */
const KpiCard = {
  props: { label: String, value: [String, Number], delta: Number, hint: String, iconPath: String },
  template: `
  <div class="relative rounded-2xl p-[1px]
              bg-[conic-gradient(at_22%_20%,rgba(220,193,118,.9),rgba(255,255,255,.14),rgba(220,193,118,.9))]">
    <div class="rounded-2xl h-full bg-white/[.06] backdrop-blur ring-1 ring-white/10 p-4">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-sm text-white/70">{{ label }}</div>
          <div class="mt-1 text-3xl font-extrabold tracking-tight">{{ String(value) }}</div>
        </div>
        <div class="shrink-0">
          <div class="w-11 h-11 rounded-xl grid place-items-center
                      bg-[radial-gradient(circle_at_30%_30%,#fff8,transparent_60%)] ring-1 ring-white/10">
            <svg viewBox="0 0 24 24" class="w-6 h-6 text-bk-gold"><path :d="iconPath" fill="currentColor"/></svg>
          </div>
        </div>
      </div>
      <div v-if="hint" class="mt-1 text-[12px] text-white/60">{{ hint }}</div>

      <div v-if="delta !== undefined"
           :class="['mt-3 inline-flex items-center gap-1 text-xs font-semibold px-1.5 py-0.5 rounded',
                    delta>0 ? 'text-emerald-300 bg-emerald-500/10 ring-1 ring-emerald-500/30'
                            : delta<0 ? 'text-red-300 bg-red-500/10 ring-1 ring-red-500/30'
                                      : 'text-white/80 bg-white/10 ring-1 ring-white/15']">
        <span>{{ delta>0 ? '↑' : delta<0 ? '↓' : '•' }}</span>
        <span>{{ Math.abs(delta) }}%</span>
      </div>
    </div>
  </div>`
}

const Radial = {
  props: { label: String, value: { type: Number, required: true } },
  template: `
  <div class="grid place-items-center gap-2">
    <div class="relative w-24 h-24 rounded-full ring-1 ring-white/10"
         :style="{ background: 'conic-gradient(#dcc176 ' + value + '%, rgba(255,255,255,.12) 0)' }">
      <div class="absolute inset-3 rounded-full bg-[#151e27] ring-1 ring-white/10 grid place-items-center text-sm font-bold">
        {{ value }}%
      </div>
    </div>
    <div class="text-xs text-white/70">{{ label }}</div>
  </div>`
}

const QuickAction = {
  props: { to: String, iconPath: String, label: String },
  methods: { r },
  template: `
  <a :href="r(to)" class="inline-flex items-center gap-2 rounded-lg px-3 py-2
                          bg-white/[.06] ring-1 ring-white/10 hover:ring-bk-gold/60
                          hover:-translate-y-0.5 transition will-change-transform">
    <svg viewBox="0 0 24 24" class="w-4 h-4 text-bk-gold"><path :d="iconPath" fill="currentColor"/></svg>
    <span class="text-sm font-medium">{{ label }}</span>
  </a>`
}

/* ---------- Icône de ligne pour l’activité (mapping simple) ---------- */
function activityPath(type: string): string {
  const t = (type || '').toLowerCase()
  if (t.includes('devis') || t.includes('lead')) return paths.message
  if (t.includes('projet') || t.includes('vrd') || t.includes('chantier')) return paths.folder
  if (t.includes('article') || t.includes('post')) return paths.pen
  if (t.includes('ao') || t.includes('appel')) return paths.fileText
  return paths.search
}
</script>

<template>
  <div class="space-y-6">
    <!-- Toolbar -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
      <div class="flex items-center gap-3">
        <div class="text-lg font-semibold">BKO <span class="text-bk-gold">Construction</span> · Admin</div>
        <div class="hidden md:flex items-center gap-2 text-xs text-white/50">
          <span class="inline-block w-2 h-2 rounded-full bg-bk-gold/70 animate-pulse"></span>
          Live · maj {{ lastRefreshed }}
        </div>
        <label class="ml-2 inline-flex items-center gap-2 text-xs text-white/70 select-none">
          <input type="checkbox" v-model="autoRefresh" class="accent-bk-gold">
          Auto-refresh
        </label>
      </div>

      <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
        <!-- Recherche -->
        <div class="relative">
          <input id="global-search" v-model="q" type="search" inputmode="search" placeholder="Rechercher ( / )"
                 class="h-10 w-[260px] rounded-lg bg-white/[.06] ring-1 ring-white/10 pl-9 pr-8 text-sm
                        placeholder:text-white/50 outline-none focus:ring-bk-gold/50"/>
          <svg viewBox="0 0 24 24" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-white/60">
            <path :d="paths.search" fill="currentColor"/>
          </svg>
          <button v-if="q" class="absolute right-2 top-1/2 -translate-y-1/2 text-white/50 hover:text-white"
                  @click="q=''">✕</button>
        </div>

        <!-- Filtres période -->
        <div class="grid grid-cols-4 gap-1 rounded-lg bg-white/[.06] ring-1 ring-white/10 p-1 h-10">
          <button @click="range='7d';  applyFilters()" :class="['chip', range==='7d'  ? 'chip--active' : '']">7j</button>
          <button @click="range='30d'; applyFilters()" :class="['chip', range==='30d' ? 'chip--active' : '']">30j</button>
          <button @click="range='q';   applyFilters()" :class="['chip', range==='q'   ? 'chip--active' : '']">Trim.</button>
          <button @click="range='ytd'; applyFilters()" :class="['chip', range==='ytd' ? 'chip--active' : '']">YTD</button>
        </div>

        <!-- Actions rapides -->
        <div class="hidden lg:flex gap-2">
          <Link :href="r('admin.quotes.index')"
                class="h-10 inline-flex items-center gap-2 rounded-lg px-3 bg-bk-gold text-bk-night font-semibold
                       shadow-[0_16px_40px_-16px_rgba(220,193,118,.45)] hover:-translate-y-0.5 transition">
            <svg viewBox="0 0 24 24" class="w-4 h-4"><path :d="paths.message" fill="currentColor"/></svg>
            Leads
          </Link>
          <Link :href="r('admin.projects.index')"
                class="h-10 inline-flex items-center gap-2 rounded-lg px-3 bg-white/[.06] ring-1 ring-white/10 hover:ring-bk-gold/60">
            <svg viewBox="0 0 24 24" class="w-4 h-4"><path :d="paths.folder" fill="currentColor"/></svg>
            Projets
          </Link>
        </div>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <KpiCard :label="m.projects.label" :value="m.projects.value" :delta="m.projects.delta" :iconPath="paths.folder"   :hint="m.projects.hint" />
      <KpiCard :label="m.leads.label"    :value="m.leads.value"    :delta="m.leads.delta"    :iconPath="paths.users"    :hint="m.leads.hint" />
      <KpiCard :label="m.rfps.label"     :value="m.rfps.value"     :delta="m.rfps.delta"     :iconPath="paths.fileText" :hint="m.rfps.hint" />
      <KpiCard :label="m.posts.label"    :value="m.posts.value"    :delta="m.posts.delta"    :iconPath="paths.pen"      :hint="m.posts.hint" />
    </div>

    <!-- Graphiques & Statuts -->
    <div class="grid gap-6 xl:grid-cols-3">
      <div class="panel xl:col-span-2">
        <div class="panel-title flex items-center gap-2">
          Flux de leads
          <svg viewBox="0 0 24 24" class="w-4 h-4 opacity-80"><path :d="paths.chart" fill="currentColor"/></svg>
        </div>
        <div class="mt-1 text-sm text-white/60">Période : {{ range?.toUpperCase() }}</div>

        <div v-if="!(props.trends.leads?.data?.length)" class="mt-6 text-sm text-white/60">Pas encore de données de leads.</div>
        <template v-else>
          <svg :viewBox="'0 0 420 120'" class="mt-4 w-full h-36">
            <defs>
              <linearGradient id="gArea" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%"   stop-color="#dcc176" stop-opacity="0.35" />
                <stop offset="100%" stop-color="#dcc176" stop-opacity="0.02" />
              </linearGradient>
            </defs>
            <path :d="toPath(props.trends.leads.data, 420, 120) + ' L 420 120 L 0 120 Z'" fill="url(#gArea)"/>
            <path :d="toPath(props.trends.leads.data, 420, 120)" fill="none" stroke="#dcc176" stroke-width="2.5" vector-effect="non-scaling-stroke" />
          </svg>

          <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div class="stat-chip"><span class="dot bg-bk-gold"></span>Leads</div>
            <div class="stat-chip"><span class="dot bg-white/25"></span>Référence</div>
            <div class="stat-chip">Max: <span class="text-white/90 font-semibold">{{ fmt.num(Math.max(...props.trends.leads.data.map(p=>p.y))) }}</span></div>
            <div class="stat-chip">Points: <span class="text-white/90 font-semibold">{{ props.trends.leads.data.length }}</span></div>
          </div>
        </template>
      </div>

      <div class="panel">
        <div class="panel-title">SLA & Satisfaction</div>
        <div class="mt-4 grid grid-cols-3 gap-4">
          <Radial label="Délais"        :value="props.slas.ontime" />
          <Radial label="Qualité"       :value="props.slas.quality" />
          <Radial label="Satisfaction"  :value="props.slas.satisfaction" />
        </div>

        <div class="mt-5 border-t border-white/10 pt-4 grid sm:grid-cols-2 gap-3">
          <QuickAction to="admin.quotes.index"    :iconPath="paths.message" label="Voir les leads" />
          <QuickAction to="admin.projects.create" :iconPath="paths.plus"    label="Créer un projet" />
        </div>
      </div>
    </div>

    <!-- Segments + Activité -->
    <div class="grid gap-6 lg:grid-cols-2">
      <div class="panel">
        <div class="panel-title">Projets par segment</div>
        <div v-if="!props.bySegment.length" class="mt-3 text-sm text-white/60">Aucun segment.</div>
        <div v-else class="mt-4 grid gap-3">
          <div v-for="s in props.bySegment" :key="s.label" class="flex items-center gap-3">
            <div class="min-w-[140px] text-sm text-white/80">{{ s.label }}</div>
            <div class="flex-1 h-2 rounded-full bg-white/10 overflow-hidden">
              <div class="h-full rounded-full bg-bk-gold" :style="{ width: Math.min(100, Math.max(0, s.value)) + '%' }"></div>
            </div>
            <div class="w-14 text-right text-sm font-semibold">{{ s.value }}%</div>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-title">Activité récente</div>
        <div v-if="!props.activity.length" class="mt-3 text-sm text-white/60">Flux à venir…</div>
        <ul v-else class="mt-3 divide-y divide-white/10">
          <li v-for="a in props.activity" :key="a.id" class="py-3 flex items-center gap-3">
            <span class="inline-flex w-9 h-9 items-center justify-center rounded-lg bg-white/10 ring-1 ring-white/10">
              <svg viewBox="0 0 24 24" class="w-5 h-5 text-bk-gold">
                <path :d="activityPath(a.type)" fill="currentColor"/>
              </svg>
            </span>
            <div class="min-w-0 flex-1">
              <div class="text-sm font-medium truncate">
                <Link v-if="a.url" :href="a.url" class="hover:text-bk-gold">{{ a.title }}</Link>
                <span v-else>{{ a.title }}</span>
              </div>
              <div class="text-xs text-white/60">{{ a.when }}</div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped lang="postcss">
.panel {
  @apply relative rounded-2xl p-[1px]
         bg-[conic-gradient(at_30%_30%,rgba(220,193,118,.9),rgba(255,255,255,.14),rgba(220,193,118,.9))]
         shadow-[0_40px_120px_-40px_rgba(220,193,118,.3)];
}
.panel > * { @apply rounded-2xl bg-white/[.06] backdrop-blur ring-1 ring-white/10 p-4 sm:p-5; }
.panel-title { @apply text-base font-semibold; }
.chip { @apply text-xs rounded-md px-2.5 h-8 grid place-items-center text-white/80 hover:text-white; }
.chip--active { @apply bg-bk-gold text-bk-night font-semibold; }
.stat-chip { @apply inline-flex items-center gap-2 text-white/70; }
.dot { @apply inline-block w-2.5 h-2.5 rounded-full; }
</style>
