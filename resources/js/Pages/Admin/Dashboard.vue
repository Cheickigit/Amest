<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, onMounted, onBeforeUnmount, ref, watch } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

type Metric = { label: string; value: number | string; delta?: number; hint?: string }
type TrendPoint = { x: string; y: number }
type Trend = { label: string; data: TrendPoint[] }
type Activity = { id: number|string; type: string; title: string; when: string; url?: string }
type Props = {
  filters: { range?: '7d' | '30d' | 'q' | 'ytd' }
  metrics: Record<string, Metric>
  trends: { leads: Trend; projects: Trend }
  bySegment: Array<{ label: string; value: number }>
  slas: { ontime: number; quality: number; satisfaction: number }
  activity: Activity[]
}

const page = usePage<Props>()
const props = computed(() => page.props)
const route = (name: string, params?: any, absolute?: boolean) =>
  ziggyRoute(name, params, absolute, (window as any).Ziggy)

const q = ref('')
const range = ref<Props['filters']['range']>(props.value.filters?.range ?? '30d')
const autoRefresh = ref(true); const refreshEveryMs = 20_000; const lastRefreshed = ref(new Date().toLocaleTimeString())
let timer:any=null

onMounted(() => {
  const onKey = (e: KeyboardEvent) => { if (e.key === '/') { e.preventDefault(); (document.getElementById('global-search') as HTMLInputElement)?.focus() } }
  window.addEventListener('keydown', onKey, { passive: true } as any)
})
onMounted(() => startAutoRefresh())
onBeforeUnmount(() => stopAutoRefresh())
function startAutoRefresh(){ stopAutoRefresh(); if(!autoRefresh.value) return; timer=setInterval(()=>applyFilters(true),refreshEveryMs)}
function stopAutoRefresh(){ if(timer){clearInterval(timer); timer=null} }
watch(autoRefresh, v => v ? startAutoRefresh() : stopAutoRefresh())

function applyFilters(replace=false) {
  router.get(route('admin.dashboard'), { range: range.value, q: q.value }, { preserveState: true, preserveScroll: true, replace })
  lastRefreshed.value = new Date().toLocaleTimeString()
}
let d:any=null; watch(q,()=>{ clearTimeout(d); d=setTimeout(()=>applyFilters(true),450)})

const fmt = { num: (n: number) => Intl.NumberFormat().format(n), pct: (n: number) => `${n}%` }

function toPath(points: TrendPoint[], w = 320, h = 96): string {
  if (!points.length) return ''
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

// Sub-components (template-only)
const KpiCard = {
  props: { label: String, value: [String, Number], delta: Number, hint: String, icon: String },
  template: `
  <div class="relative rounded-2xl p-[1px]
              bg-[conic-gradient(at_22%_20%,rgba(220,193,118,.85),rgba(255,255,255,.12),rgba(220,193,118,.85))]">
    <div class="rounded-2xl h-full bg-white/[.06] backdrop-blur ring-1 ring-white/10 p-4">
      <div class="flex items-start justify-between">
        <div class="text-sm text-white/70">{{ label }}</div>
        <div class="text-xl leading-none">{{ icon }}</div>
      </div>
      <div class="mt-1 text-3xl font-extrabold tracking-tight">{{ String(value) }}</div>
      <div class="mt-1 text-[12px] text-white/60">{{ hint }}</div>
      <div v-if="delta !== undefined"
           :class="['mt-2 inline-flex items-center gap-1 text-xs font-semibold px-1.5 py-0.5 rounded',
                    delta>0 ? 'text-emerald-300 bg-emerald-500/10 ring-1 ring-emerald-500/30'
                            : delta<0 ? 'text-red-300 bg-red-500/10 ring-1 ring-red-500/30'
                                      : 'text-white/70 bg-white/10 ring-1 ring-white/15']">
        <span>{{ delta>0 ? '▲' : delta<0 ? '▼' : '•' }}</span>
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
  props: { to: String, icon: String, label: String },
  methods: { href(name: string){ return route(name) } },
  template: `
  <a :href="href(to)"
     class="inline-flex items-center gap-2 rounded-lg px-3 py-2 bg-white/[.06] ring-1 ring-white/10
            hover:ring-bk-gold/60 hover:-translate-y-0.5 transition will-change-transform">
    <span>{{ icon }}</span>
    <span class="text-sm font-medium">{{ label }}</span>
  </a>`
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
          <input type="checkbox" v-model="autoRefresh" class="accent-bk-gold/80">
          Auto-refresh
        </label>
      </div>

      <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
        <!-- Recherche -->
        <div class="relative">
          <input id="global-search" v-model="q" type="search" inputmode="search" placeholder="Rechercher ( / )"
                 class="h-10 w-[260px] rounded-lg bg-white/[.06] ring-1 ring-white/10 px-9 text-sm placeholder:text-white/50 outline-none focus:ring-bk-gold/50"/>
          <svg viewBox="0 0 24 24" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-white/60">
            <path fill="currentColor" d="M10 2a8 8 0 105.29 14.29l4.21 4.21 1.41-1.41-4.21-4.21A8 8 0 0010 2m0 2a6 6 0 110 12A6 6 0 0110 4z"/>
          </svg>
          <button v-if="q" class="absolute right-2 top-1/2 -translate-y-1/2 text-white/50 hover:text-white" @click="q=''">✕</button>
        </div>

        <!-- Filtres période -->
        <div class="grid grid-cols-4 gap-1 rounded-lg bg-white/[.06] ring-1 ring-white/10 p-1 h-10">
          <button @click="range='7d';  applyFilters()" :class="['chip', range==='7d'  ? 'chip--active' : '']">7j</button>
          <button @click="range='30d'; applyFilters()" :class="['chip', range==='30d' ? 'chip--active' : '']">30j</button>
          <button @click="range='q';   applyFilters()" :class="['chip', range==='q'   ? 'chip--active' : '']">Trim.</button>
          <button @click="range='ytd'; applyFilters()" :class="['chip', range==='ytd' ? 'chip--active' : '']">YTD</button>
        </div>

        <Link :href="route('admin.reports')"
              class="h-10 inline-flex items-center gap-2 rounded-lg px-3 bg-bk-gold text-bk-night font-semibold shadow-[0_16px_40px_-16px_rgba(220,193,118,.45)] hover:-translate-y-0.5 transition">
          <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M3 5h18v2H3V5m0 6h18v2H3v-2m0 6h12v2H3v-2z"/></svg>
          Exporter
        </Link>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <KpiCard :label="props.metrics.projects.label" :value="props.metrics.projects.value" :delta="props.metrics.projects.delta" icon="📁" hint="Projets en portefeuille" />
      <KpiCard :label="props.metrics.leads.label"    :value="props.metrics.leads.value"    :delta="props.metrics.leads.delta"    icon="👥" hint="Leads actifs" />
      <KpiCard :label="props.metrics.rfps.label"     :value="props.metrics.rfps.value"     :delta="props.metrics.rfps.delta"     icon="📑" hint="Appels d’offres ouverts" />
      <KpiCard :label="props.metrics.posts.label"    :value="props.metrics.posts.value"    :delta="props.metrics.posts.delta"    icon="✍️" hint="Articles en brouillon" />
    </div>

    <!-- Graphiques & Statuts -->
    <div class="grid gap-6 xl:grid-cols-3">
      <div class="panel xl:col-span-2">
        <div class="panel-title">Flux de leads</div>
        <div class="mt-2 text-sm text-white/60">Période : {{ range?.toUpperCase() }}</div>

        <svg :viewBox="'0 0 320 96'" class="mt-4 w-full h-28">
          <defs>
            <linearGradient id="gArea" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%"   stop-color="#dcc176" stop-opacity="0.35" />
              <stop offset="100%" stop-color="#dcc176" stop-opacity="0.02" />
            </linearGradient>
          </defs>
          <path :d="toPath(props.trends.leads.data, 320, 96) + ' L 320 96 L 0 96 Z'" fill="url(#gArea)"/>
          <path :d="toPath(props.trends.leads.data, 320, 96)" fill="none" stroke="#dcc176" stroke-width="2.5" vector-effect="non-scaling-stroke" />
        </svg>

        <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
          <div class="stat-chip"><span class="dot bg-bk-gold"></span>Leads</div>
          <div class="stat-chip"><span class="dot bg-white/25"></span>Référence</div>
          <div class="stat-chip">Max: <span class="text-white/90 font-semibold">{{ fmt.num(Math.max(...props.trends.leads.data.map(p=>p.y))) }}</span></div>
          <div class="stat-chip">Jours: <span class="text-white/90 font-semibold">{{ props.trends.leads.data.length }}</span></div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-title">SLA & Satisfaction</div>
        <div class="mt-4 grid grid-cols-3 gap-4">
          <Radial label="Délais"        :value="props.slas.ontime" />
          <Radial label="Qualité"       :value="props.slas.quality" />
          <Radial label="Satisfaction"  :value="props.slas.satisfaction" />
        </div>

        <div class="mt-5 border-t border-white/10 pt-4 grid sm:grid-cols-2 gap-3">
          <QuickAction to="admin.quotes.index"    icon="💬" label="Voir les leads" />
          <QuickAction to="admin.projects.create" icon="➕" label="Créer un projet" />
        </div>
      </div>
    </div>

    <!-- Segments + Activité -->
    <div class="grid gap-6 lg:grid-cols-2">
      <div class="panel">
        <div class="panel-title">Projets par segment</div>
        <div class="mt-4 grid gap-3">
          <div v-for="s in props.bySegment" :key="s.label" class="flex items-center gap-3">
            <div class="min-w-[120px] text-sm text-white/80">{{ s.label }}</div>
            <div class="flex-1 h-2 rounded-full bg-white/10 overflow-hidden">
              <div class="h-full rounded-full bg-bk-gold" :style="{ width: Math.min(100, s.value) + '%' }"></div>
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
            <span class="inline-flex w-8 h-8 items-center justify-center rounded-lg bg-white/10 ring-1 ring-white/10">{{ a.type }}</span>
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
         bg-[conic-gradient(at_30%_30%,rgba(220,193,118,.85),rgba(255,255,255,.12),rgba(220,193,118,.85))];
}
.panel > * { @apply rounded-2xl bg-white/[.06] backdrop-blur ring-1 ring-white/10 p-4 sm:p-5; }
.panel-title { @apply text-base font-semibold; }
.chip { @apply text-xs rounded-md px-2.5 text-white/80 hover:text-white; }
.chip--active { @apply bg-bk-gold text-bk-night font-semibold; }
.stat-chip { @apply inline-flex items-center gap-2 text-white/70; }
.dot { @apply inline-block w-2.5 h-2.5 rounded-full; }
</style>
