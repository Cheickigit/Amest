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
type Event = { id: number|string; title: string; date: string; location: string; status: string }
type Member = { id: number|string; name: string; role: string; status: string; joined: string }

type Props = {
  filters: { range?: '7d' | '30d' | 'q' | 'ytd' }
  metrics: Partial<Record<'members'|'activities'|'events'|'announcements', Metric>>
  trends: { members: Trend; activities: Trend }
  bySegment: Array<{ label: string; value: number }>
  activity: Activity[]
  upcomingEvents: Event[]
  recentMembers: Member[]
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
const refreshEveryMs = 30000
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
  members: 'M16 11a4 4 0 1 0-8 0a4 4 0 0 0 8 0zM4 20a8 8 0 1 1 16 0H4z',
  activities: 'M4 4h16v2H4V4zm0 5h16v2H4V9zm0 5h10v2H4v-2z',
  events: 'M8 2v2H5v18h14V4h-3V2h-2v2h-4V2H8zM5 8h14v12H5V8zm2 2v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2zm-8 4v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2zm-8 4v2h2v-2H7zm4 0v2h2v-2h-2z',
  announcements: 'M21 6h-8l-2-2H3v16h18V6z',
  stats: 'M4 2v20h16V2H4zm2 2h12v4H6V4zm0 6h12v4H6v-4zm0 6h12v4H6v-4z',
  education: 'M12 3l9 4v6c0 1.1-.9 2-2 2h-1v-6h1V7l-7 3l-7-3v2h1v6H5v6h2v-6h8v6h2v-6h1c1.1 0 2-.9 2-2V7l-9-4z',
  solidarity: 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41c0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z',
  projects: 'M3 5h18v2H3V5zm0 6h18v2H3v-2zm0 6h12v2H3v-2z',
  search: 'M10 2a8 8 0 1 0 5.29 14.29l4.21 4.21l1.41-1.41l-4.21-4.21A8 8 0 0 0 10 2z',
  plus: 'M12 5v14M5 12h14',
  chart: 'M4 19h16M7 15v-6M12 19V5M17 19v-9',
}

/* ---------- KPI data sécurisée ---------- */
const m = computed(() => ({
  members: props.value.metrics?.members ?? { label:'Membres', value:150, delta:12, hint:'Actifs' },
  activities: props.value.metrics?.activities ?? { label:'Activités', value:25, delta:8, hint:'Cette année' },
  events: props.value.metrics?.events ?? { label:'Événements', value:8, delta:3, hint:'À venir' },
  announcements: props.value.metrics?.announcements ?? { label:'Annonces', value:12, delta:-2, hint:'En attente' },
}))

/* ---------- Icône de ligne pour l'activité (mapping simple) ---------- */
function activityPath(type: string): string {
  const t = (type || '').toLowerCase()
  if (t.includes('membre') || t.includes('inscription')) return paths.members
  if (t.includes('activité') || t.includes('événement')) return paths.activities
  if (t.includes('annonce') || t.includes('publication')) return paths.announcements
  if (t.includes('projet')) return paths.projects
  if (t.includes('éducation') || t.includes('formation')) return paths.education
  if (t.includes('solidarité') || t.includes('aide')) return paths.solidarity
  return paths.search
}

/* ---------- Couleurs AMEST optimisées pour contraste ---------- */
const colors = {
  // Couleurs primaires AMEST (Mali)
  primary: {
    darkGreen: '#047857',     // Vert foncé - excellent contraste
    green: '#059669',         // Vert principal
    lightGreen: '#10b981',    // Vert clair
    darkYellow: '#d97706',    // Jaune foncé
    yellow: '#f59e0b',        // Jaune principal
    lightYellow: '#fbbf24',   // Jaune clair
    red: '#dc2626',           // Rouge (3ème bande drapeau)
  },

  // Couleurs neutres pour arrière-plans et textes
  neutral: {
    black: '#1f2937',         // Noir pour titres et textes importants
    darkGray: '#374151',      // Gris foncé pour textes secondaires
    gray: '#6b7280',          // Gris pour textes tertiaires
    lightGray: '#9ca3af',     // Gris clair pour bordures
    white: '#ffffff',         // Blanc
  },

  // Couleurs d'accent
  accent: {
    emerald: '#10b981',
    amber: '#f59e0b',
    rose: '#f43f5e',
    blue: '#3b82f6',
    indigo: '#6366f1',
  },

  // Arrière-plans
  background: {
    light: '#f9fafb',         // Fond très clair
    card: '#ffffff',          // Fond des cartes
    sidebar: '#f0fdf4',       // Fond sidebar
  }
}

/* ---------- Gradients harmonieux ---------- */
const gradients = {
  members: `linear-gradient(135deg, ${colors.primary.darkGreen}, ${colors.primary.green})`,
  activities: `linear-gradient(135deg, ${colors.primary.darkYellow}, ${colors.primary.yellow})`,
  events: `linear-gradient(135deg, ${colors.accent.rose}, ${colors.accent.blue})`,
  announcements: `linear-gradient(135deg, ${colors.accent.indigo}, ${colors.accent.blue})`,
  success: `linear-gradient(135deg, ${colors.primary.green}, ${colors.accent.emerald})`,
  warning: `linear-gradient(135deg, ${colors.primary.yellow}, ${colors.accent.amber})`,
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4">
      <div>
        <h1 class="text-2xl font-bold" :style="{ color: colors.neutral.black }">
          Tableau de bord AMEST-Sahel
        </h1>
        <p class="mt-1" :style="{ color: colors.neutral.darkGray }">
          Bienvenue dans l'administration de l'association
        </p>
      </div>

      <!-- Toolbar -->
      <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-3">
          <div class="hidden md:flex items-center gap-2 text-xs" :style="{ color: colors.neutral.gray }">
            <span class="inline-block w-2 h-2 rounded-full" :style="{ backgroundColor: colors.primary.green }"></span>
            Dernière mise à jour : {{ lastRefreshed }}
          </div>
          <label class="inline-flex items-center gap-2 text-xs select-none" :style="{ color: colors.neutral.gray }">
            <input type="checkbox" v-model="autoRefresh" class="accent-green-600">
            Actualisation automatique
          </label>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
          <!-- Recherche -->
          <div class="relative">
            <input
              id="global-search"
              v-model="q"
              type="search"
              inputmode="search"
              placeholder="Rechercher ( / )"
              class="h-10 w-[260px] rounded-lg pl-9 pr-8 text-sm outline-none focus:ring-2 transition"
              :style="{
                backgroundColor: colors.background.light,
                border: `1px solid ${colors.neutral.lightGray}`,
                color: colors.neutral.black,
              }"
              :class="{ 'focus:ring-green-500': true }"
            />
            <svg viewBox="0 0 24 24" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" :style="{ color: colors.neutral.gray }">
              <path :d="paths.search" fill="currentColor"/>
            </svg>
            <button
              v-if="q"
              class="absolute right-2 top-1/2 -translate-y-1/2 hover:opacity-70 transition"
              :style="{ color: colors.neutral.gray }"
              @click="q=''"
            >
              ✕
            </button>
          </div>

          <!-- Filtres période -->
          <div class="grid grid-cols-4 gap-1 rounded-lg p-1 h-10" :style="{ backgroundColor: colors.background.light, border: `1px solid ${colors.neutral.lightGray}` }">
            <button
              v-for="period in [{label: '7j', value: '7d'}, {label: '30j', value: '30d'}, {label: 'Trim.', value: 'q'}, {label: 'Année', value: 'ytd'}]"
              :key="period.value"
              @click="range=period.value; applyFilters()"
              class="text-xs rounded-md px-2.5 h-8 grid place-items-center transition"
              :style="{
                color: range === period.value ? colors.neutral.white : colors.neutral.darkGray,
                backgroundColor: range === period.value ? colors.primary.green : 'transparent',
              }"
            >
              {{ period.label }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- KPI Cards AMEST -->
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <!-- Carte Membres -->
      <div class="relative rounded-xl overflow-hidden border" :style="{ borderColor: colors.neutral.lightGray }">
        <div class="absolute inset-0" :style="{ background: gradients.members }"></div>
        <div class="relative bg-white/95 backdrop-blur-sm p-4">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-sm font-medium" :style="{ color: colors.neutral.darkGray }">Membres</div>
              <div class="mt-1 text-3xl font-bold" :style="{ color: colors.neutral.black }">{{ m.members.value }}</div>
            </div>
            <div class="shrink-0">
              <div class="w-11 h-11 rounded-xl grid place-items-center" :style="{ backgroundColor: colors.primary.green + '20' }">
                <svg viewBox="0 0 24 24" class="w-6 h-6" :style="{ color: colors.primary.darkGreen }">
                  <path :d="paths.members" fill="currentColor"/>
                </svg>
              </div>
            </div>
          </div>
          <div class="mt-1 text-xs" :style="{ color: colors.neutral.gray }">{{ m.members.hint }}</div>
          <div v-if="m.members.delta !== undefined" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold px-1.5 py-0.5 rounded"
               :style="{
                 color: m.members.delta > 0 ? colors.primary.darkGreen : colors.primary.red,
                 backgroundColor: m.members.delta > 0 ? colors.primary.green + '20' : colors.primary.red + '20',
               }">
            <span>{{ m.members.delta > 0 ? '↑' : '↓' }}</span>
            <span>{{ Math.abs(m.members.delta) }}%</span>
          </div>
        </div>
      </div>

      <!-- Carte Activités -->
      <div class="relative rounded-xl overflow-hidden border" :style="{ borderColor: colors.neutral.lightGray }">
        <div class="absolute inset-0" :style="{ background: gradients.activities }"></div>
        <div class="relative bg-white/95 backdrop-blur-sm p-4">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-sm font-medium" :style="{ color: colors.neutral.darkGray }">Activités</div>
              <div class="mt-1 text-3xl font-bold" :style="{ color: colors.neutral.black }">{{ m.activities.value }}</div>
            </div>
            <div class="shrink-0">
              <div class="w-11 h-11 rounded-xl grid place-items-center" :style="{ backgroundColor: colors.primary.yellow + '20' }">
                <svg viewBox="0 0 24 24" class="w-6 h-6" :style="{ color: colors.primary.darkYellow }">
                  <path :d="paths.activities" fill="currentColor"/>
                </svg>
              </div>
            </div>
          </div>
          <div class="mt-1 text-xs" :style="{ color: colors.neutral.gray }">{{ m.activities.hint }}</div>
          <div v-if="m.activities.delta !== undefined" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold px-1.5 py-0.5 rounded"
               :style="{
                 color: m.activities.delta > 0 ? colors.primary.darkYellow : colors.primary.red,
                 backgroundColor: m.activities.delta > 0 ? colors.primary.yellow + '20' : colors.primary.red + '20',
               }">
            <span>{{ m.activities.delta > 0 ? '↑' : '↓' }}</span>
            <span>{{ Math.abs(m.activities.delta) }}%</span>
          </div>
        </div>
      </div>

      <!-- Carte Événements -->
      <div class="relative rounded-xl overflow-hidden border" :style="{ borderColor: colors.neutral.lightGray }">
        <div class="absolute inset-0" :style="{ background: gradients.events }"></div>
        <div class="relative bg-white/95 backdrop-blur-sm p-4">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-sm font-medium" :style="{ color: colors.neutral.darkGray }">Événements</div>
              <div class="mt-1 text-3xl font-bold" :style="{ color: colors.neutral.black }">{{ m.events.value }}</div>
            </div>
            <div class="shrink-0">
              <div class="w-11 h-11 rounded-xl grid place-items-center" :style="{ backgroundColor: colors.accent.blue + '20' }">
                <svg viewBox="0 0 24 24" class="w-6 h-6" :style="{ color: colors.accent.blue }">
                  <path :d="paths.events" fill="currentColor"/>
                </svg>
              </div>
            </div>
          </div>
          <div class="mt-1 text-xs" :style="{ color: colors.neutral.gray }">{{ m.events.hint }}</div>
          <div v-if="m.events.delta !== undefined" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold px-1.5 py-0.5 rounded"
               :style="{
                 color: m.events.delta > 0 ? colors.accent.blue : colors.primary.red,
                 backgroundColor: m.events.delta > 0 ? colors.accent.blue + '20' : colors.primary.red + '20',
               }">
            <span>{{ m.events.delta > 0 ? '↑' : '↓' }}</span>
            <span>{{ Math.abs(m.events.delta) }}%</span>
          </div>
        </div>
      </div>

      <!-- Carte Annonces -->
      <div class="relative rounded-xl overflow-hidden border" :style="{ borderColor: colors.neutral.lightGray }">
        <div class="absolute inset-0" :style="{ background: gradients.announcements }"></div>
        <div class="relative bg-white/95 backdrop-blur-sm p-4">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-sm font-medium" :style="{ color: colors.neutral.darkGray }">Annonces</div>
              <div class="mt-1 text-3xl font-bold" :style="{ color: colors.neutral.black }">{{ m.announcements.value }}</div>
            </div>
            <div class="shrink-0">
              <div class="w-11 h-11 rounded-xl grid place-items-center" :style="{ backgroundColor: colors.accent.indigo + '20' }">
                <svg viewBox="0 0 24 24" class="w-6 h-6" :style="{ color: colors.accent.indigo }">
                  <path :d="paths.announcements" fill="currentColor"/>
                </svg>
              </div>
            </div>
          </div>
          <div class="mt-1 text-xs" :style="{ color: colors.neutral.gray }">{{ m.announcements.hint }}</div>
          <div v-if="m.announcements.delta !== undefined" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold px-1.5 py-0.5 rounded"
               :style="{
                 color: m.announcements.delta > 0 ? colors.accent.indigo : colors.primary.red,
                 backgroundColor: m.announcements.delta > 0 ? colors.accent.indigo + '20' : colors.primary.red + '20',
               }">
            <span>{{ m.announcements.delta > 0 ? '↑' : '↓' }}</span>
            <span>{{ Math.abs(m.announcements.delta) }}%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Graphiques & Statistiques -->
    <div class="grid gap-6 xl:grid-cols-3">
      <!-- Graphique évolution -->
      <div class="rounded-xl border p-5" :style="{ borderColor: colors.neutral.lightGray, backgroundColor: colors.background.card }">
        <div class="flex items-center gap-2">
          <svg viewBox="0 0 24 24" class="w-5 h-5" :style="{ color: colors.primary.green }">
            <path :d="paths.chart" fill="currentColor"/>
          </svg>
          <h3 class="text-lg font-semibold" :style="{ color: colors.neutral.black }">Évolution des membres</h3>
        </div>
        <div class="mt-1 text-sm" :style="{ color: colors.neutral.gray }">
          Période : {{ range === '7d' ? '7 derniers jours' : range === '30d' ? '30 derniers jours' : range === 'q' ? 'Trimestre' : 'Année en cours' }}
        </div>

        <div v-if="!(props.trends?.members?.data?.length)" class="mt-6 text-sm" :style="{ color: colors.neutral.gray }">
          Pas encore de données.
        </div>
        <template v-else>
          <svg :viewBox="'0 0 420 120'" class="mt-4 w-full h-36">
            <defs>
              <linearGradient id="gArea" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" :stop-color="colors.primary.green" stop-opacity="0.35" />
                <stop offset="100%" :stop-color="colors.primary.green" stop-opacity="0.02" />
              </linearGradient>
            </defs>
            <path :d="toPath(props.trends.members.data, 420, 120) + ' L 420 120 L 0 120 Z'" fill="url(#gArea)"/>
            <path :d="toPath(props.trends.members.data, 420, 120)" fill="none" :stroke="colors.primary.green" stroke-width="2.5" vector-effect="non-scaling-stroke" />
          </svg>
        </template>
      </div>

      <!-- Statistiques engagement -->
      <div class="rounded-xl border p-5" :style="{ borderColor: colors.neutral.lightGray, backgroundColor: colors.background.card }">
        <div class="flex items-center gap-2">
          <svg viewBox="0 0 24 24" class="w-5 h-5" :style="{ color: colors.primary.green }">
            <path :d="paths.stats" fill="currentColor"/>
          </svg>
          <h3 class="text-lg font-semibold" :style="{ color: colors.neutral.black }">Engagement & Participation</h3>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-4">
          <div v-for="stat in [{label: 'Participation', value: 75}, {label: 'Engagement', value: 82}, {label: 'Satisfaction', value: 88}]"
               :key="stat.label"
               class="grid place-items-center gap-2">
            <div class="relative w-20 h-20 rounded-full"
                 :style="{ background: `conic-gradient(${colors.primary.green} ${stat.value}%, ${colors.neutral.lightGray} 0)` }">
              <div class="absolute inset-3 rounded-full bg-white grid place-items-center text-sm font-bold"
                   :style="{ color: colors.primary.darkGreen }">
                {{ stat.value }}%
              </div>
            </div>
            <div class="text-xs" :style="{ color: colors.neutral.darkGray }">{{ stat.label }}</div>
          </div>
        </div>
      </div>

      <!-- Actions rapides -->
      <div class="rounded-xl border p-5" :style="{ borderColor: colors.neutral.lightGray, backgroundColor: colors.background.card }">
        <h3 class="text-lg font-semibold" :style="{ color: colors.neutral.black }">Actions rapides</h3>
        <div class="mt-4 space-y-3">
          <Link :href="r('admin.members.create')" class="flex items-center gap-3 p-3 rounded-lg transition hover:scale-[1.02]"
                :style="{ backgroundColor: colors.primary.green + '10', border: `1px solid ${colors.primary.green + '30'}` }">
            <div class="w-10 h-10 rounded-lg grid place-items-center" :style="{ backgroundColor: colors.primary.green + '20' }">
              <svg viewBox="0 0 24 24" class="w-5 h-5" :style="{ color: colors.primary.darkGreen }">
                <path :d="paths.plus" fill="currentColor"/>
              </svg>
            </div>
            <span class="font-medium" :style="{ color: colors.neutral.black }">Ajouter un membre</span>
          </Link>

          <Link :href="r('admin.activities.create')" class="flex items-center gap-3 p-3 rounded-lg transition hover:scale-[1.02]"
                :style="{ backgroundColor: colors.primary.yellow + '10', border: `1px solid ${colors.primary.yellow + '30'}` }">
            <div class="w-10 h-10 rounded-lg grid place-items-center" :style="{ backgroundColor: colors.primary.yellow + '20' }">
              <svg viewBox="0 0 24 24" class="w-5 h-5" :style="{ color: colors.primary.darkYellow }">
                <path :d="paths.activities" fill="currentColor"/>
              </svg>
            </div>
            <span class="font-medium" :style="{ color: colors.neutral.black }">Créer une activité</span>
          </Link>

          <Link :href="r('admin.announcements.create')" class="flex items-center gap-3 p-3 rounded-lg transition hover:scale-[1.02]"
                :style="{ backgroundColor: colors.accent.blue + '10', border: `1px solid ${colors.accent.blue + '30'}` }">
            <div class="w-10 h-10 rounded-lg grid place-items-center" :style="{ backgroundColor: colors.accent.blue + '20' }">
              <svg viewBox="0 0 24 24" class="w-5 h-5" :style="{ color: colors.accent.blue }">
                <path :d="paths.announcements" fill="currentColor"/>
              </svg>
            </div>
            <span class="font-medium" :style="{ color: colors.neutral.black }">Publier une annonce</span>
          </Link>
        </div>
      </div>
    </div>

    <!-- Membres récents & Événements à venir -->
    <div class="grid gap-6 lg:grid-cols-2">
      <!-- Membres récents -->
      <div class="rounded-xl border p-5" :style="{ borderColor: colors.neutral.lightGray, backgroundColor: colors.background.card }">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <svg viewBox="0 0 24 24" class="w-5 h-5" :style="{ color: colors.primary.green }">
              <path :d="paths.members" fill="currentColor"/>
            </svg>
            <h3 class="text-lg font-semibold" :style="{ color: colors.neutral.black }">Membres récents</h3>
          </div>
          <span class="text-sm" :style="{ color: colors.neutral.gray }">5 derniers</span>
        </div>

        <div class="mt-4 space-y-3">
          <div v-for="member in [
            {name: 'Moussa Traoré', role: 'Étudiant en Médecine', status: 'actif'},
            {name: 'Aminata Diallo', role: 'Stagiaire Informatique', status: 'actif'},
            {name: 'Boubacar Keita', role: 'Étudiant en Droit', status: 'actif'},
            {name: 'Fatoumata Coulibaly', role: 'Étudiante en Commerce', status: 'nouveau'},
            {name: 'Sékou Camara', role: 'Stagiaire Génie Civil', status: 'actif'}
          ]" :key="member.name"
               class="flex items-center gap-3 p-3 rounded-lg transition hover:scale-[1.01]"
               :style="{ backgroundColor: colors.background.light }">
            <div class="w-10 h-10 rounded-full grid place-items-center font-bold"
                 :style="{
                   background: gradients.members,
                   color: colors.neutral.white
                 }">
              {{ member.name.substring(0,1).toUpperCase() }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium truncate" :style="{ color: colors.neutral.black }">{{ member.name }}</div>
              <div class="text-xs truncate" :style="{ color: colors.neutral.gray }">{{ member.role }}</div>
            </div>
            <div class="px-2 py-1 rounded text-xs font-medium"
                 :style="{
                   backgroundColor: member.status === 'actif' ? colors.primary.green + '20' : colors.accent.amber + '20',
                   color: member.status === 'actif' ? colors.primary.darkGreen : colors.primary.darkYellow
                 }">
              {{ member.status }}
            </div>
          </div>
        </div>
      </div>

      <!-- Événements à venir -->
      <div class="rounded-xl border p-5" :style="{ borderColor: colors.neutral.lightGray, backgroundColor: colors.background.card }">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <svg viewBox="0 0 24 24" class="w-5 h-5" :style="{ color: colors.primary.green }">
              <path :d="paths.events" fill="currentColor"/>
            </svg>
            <h3 class="text-lg font-semibold" :style="{ color: colors.neutral.black }">Événements à venir</h3>
          </div>
          <span class="text-sm" :style="{ color: colors.neutral.gray }">Prochainement</span>
        </div>

        <div class="mt-4 space-y-3">
          <div v-for="event in [
            {title: 'Réunion mensuelle', date: '15 Oct 2024', location: 'Campus Sousse', status: 'confirmé'},
            {title: 'Atelier d\'intégration', date: '22 Oct 2024', location: 'Mahdia', status: 'planifié'},
            {title: 'Sortie culturelle', date: '30 Oct 2024', location: 'Zoo de Friguia', status: 'confirmé'},
            {title: 'Formation Power BI', date: '05 Nov 2024', location: 'Sousse', status: 'planifié'}
          ]" :key="event.title"
               class="p-3 rounded-lg border transition hover:scale-[1.01]"
               :style="{ borderColor: colors.neutral.lightGray }">
            <div class="flex justify-between items-start">
              <div class="flex-1 min-w-0">
                <h4 class="font-medium" :style="{ color: colors.neutral.black }">{{ event.title }}</h4>
                <div class="flex items-center gap-2 mt-1 text-xs" :style="{ color: colors.neutral.gray }">
                  <svg viewBox="0 0 24 24" class="w-3 h-3"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8s8 3.6 8 8s-3.6 8-8 8zm.5-13H11v6l5.2 3.2l.8-1.3l-4.5-2.7V7z"/></svg>
                  {{ event.date }}
                </div>
                <div class="flex items-center gap-2 mt-1 text-xs" :style="{ color: colors.neutral.gray }">
                  <svg viewBox="0 0 24 24" class="w-3 h-3"><path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                  {{ event.location }}
                </div>
              </div>
              <div class="px-2 py-1 rounded text-xs font-medium"
                   :style="{
                     backgroundColor: event.status === 'confirmé' ? colors.primary.green + '20' : colors.primary.yellow + '20',
                     color: event.status === 'confirmé' ? colors.primary.darkGreen : colors.primary.darkYellow
                   }">
                {{ event.status }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Transitions douces */
.transition {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Effets de survol */
.hover\:scale-\[1\.01\]:hover {
  transform: scale(1.01);
}

.hover\:scale-\[1\.02\]:hover {
  transform: scale(1.02);
}

.hover\:opacity-70:hover {
  opacity: 0.7;
}

/* Focus visible */
.focus\:ring-2:focus {
  outline: 2px solid transparent;
  outline-offset: 2px;
}

.focus\:ring-green-500:focus {
  --tw-ring-color: #10b981;
}
</style>
