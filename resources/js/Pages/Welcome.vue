<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

/* -------------------- Helper routes : robuste, sans crash -------------------- */
function r(name: string, params?: any, absolute = false, fallback: string = '#') {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (!Z) return fallback
    return ziggyRoute(name, params ?? {}, absolute, Z)
  } catch {
    return fallback
  }
}

/* -------------------- Types & props dynamiques -------------------- */
type MediaItem = { type:'image'|'video', kind?:'upload'|'url', path?:string|null, url?:string|null }
type Project   = { id:number; title:string; slug?:string|null; city?:string|null; category?:string|null; status:'brouillon'|'publié'; cover_image?:string|null; media?:MediaItem[] }
type Post      = { id:number; title:string; slug?:string|null; excerpt?:string|null; cover_image?:string|null; published_at?:string|null }

const props = defineProps<{
  heroProjects?: Project[] | null
  projects?: Project[] | null
  posts?: Post[] | null
}>()

/* sources sûres pour le template */
const heroList = computed<Project[]>(() => props.heroProjects ?? [])
const gridList = computed<Project[]>(() => props.projects ?? [])
const postList = computed<Post[]>(() => props.posts ?? [])

/* ---------- Tilt 3D ---------- */
function handleTilt(e: MouseEvent) {
  const el = e.currentTarget as HTMLElement | null; if (!el) return
  const rct = el.getBoundingClientRect()
  const cx = (e.clientX - rct.left) / rct.width - 0.5
  const cy = (e.clientY - rct.top) / rct.height - 0.5
  el.style.setProperty('--rx', (-cy * 10).toFixed(2) + 'deg')
  el.style.setProperty('--ry', (cx * 10).toFixed(2) + 'deg')
}
function resetTilt(e: MouseEvent) {
  const el = e.currentTarget as HTMLElement | null; if (!el) return
  el.style.setProperty('--rx', '0deg'); el.style.setProperty('--ry', '0deg')
}

/* ---------- v-reveal ---------- */
let io: IntersectionObserver | null = null
const vReveal = {
  mounted(el: HTMLElement) {
    el.classList.add('reveal')
    if (!io) {
      io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible')
            io?.unobserve(entry.target as Element)
          }
        })
      }, { threshold: 0.14 })
    }
    io.observe(el)
  }
}

/* ---------- v-countup ---------- */
const vCountup = {
  mounted(el: HTMLElement, binding: { value: number }) {
    const target = Number(binding.value ?? 0)
    el.textContent = '0'
    const obs = new IntersectionObserver((ents) => {
      if (!ents[0].isIntersecting) return
      obs.disconnect()
      const dur = 1200, t0 = performance.now()
      const tick = (t: number) => {
        const p = Math.min(1, (t - t0) / dur)
        const e = 1 - Math.pow(1 - p, 3)
        el.textContent = Math.round(target * e).toLocaleString('fr-FR')
        if (p < 1) requestAnimationFrame(tick)
      }
      requestAnimationFrame(tick)
    }, { threshold: 0.35 })
    obs.observe(el)
  }
}

/* ---------- état ---------- */
const showAllRefs = ref(false)
function focusDevis() {
  (document.getElementById('devis-name') as HTMLInputElement | null)?.focus()
}

/* ---------- Form “Devis express” (public) ---------- */
const qForm = useForm({
  name: '', company: '', email: '', phone: '',
  city: '', project_type: '', budget: '',
  desired_date: '', message: '',
  files: [] as File[],
  hp: ''
})
function pickFiles(e: Event){ const i = e.target as HTMLInputElement; if (i.files) qForm.files = Array.from(i.files) }
function submitQuote() {
  qForm.post(r('forms.quote.store', {}, false, '/devis'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => qForm.reset('message', 'files'),
  })
}

/* ---------- Utils ---------- */
const fmt = (d?: string) => d ? new Date(d).toLocaleDateString('fr-FR', { year:'numeric', month:'short', day:'2-digit' }) : '—'
</script>

<template>
  <PublicLayout>
    <!-- ================= HERO ================= -->
    <section class="relative overflow-hidden pt-12 md:pt-16 pb-10 md:pb-12">
      <div class="hero-ribbon" aria-hidden="true"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <!-- G : texte -->
        <div class="lg:col-span-4" v-reveal>
          <span class="tag-ghost">Entreprise de BTP · Tanger</span>
          <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
            Infrastructures & ouvrages — <span class="text-bk-gold">BK Construction</span>
          </h1>
          <p class="mt-4 text-white/80">
            VRD, routes, bâtiments & génie civil. Méthodes lean, qualité, sécurité — un chantier sans surprises.
          </p>

          <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="#devis" class="btn-outline-gold" @click="focusDevis">Devis express</a>
            <a href="#projects" class="btn-ghost">Réalisations</a>
          </div>

          <div class="mt-5 flex flex-wrap gap-2">
            <span class="chip">Routes</span><span class="chip">VRD</span><span class="chip">Bâtiments</span><span class="chip">Génie civil</span><span class="chip">Immobilier</span>
          </div>
        </div>

        <!-- C : scène 3D -->
        <div class="lg:col-span-4 relative" v-reveal>
          <div class="hero-halo" aria-hidden="true"></div>
          <div class="persp">
            <div class="platform"></div>
            <div class="platform-cards">
              <div class="iso-card" style="left:6%;  bottom:10%; --h:120px; --w:150px; --z:18;"></div>
              <div class="iso-card" style="left:36%; bottom:16%; --h:95px;  --w:125px; --z:24;"></div>
              <div class="iso-card" style="left:58%; bottom:28%; --h:74px;  --w:105px; --z:36;"></div>
            </div>
            <!-- grue SVG (inchangé) -->
            <svg class="crane" viewBox="0 0 520 260" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
              <g stroke-linecap="round" stroke-linejoin="round">
                <path class="text-white/70" d="M60 240V70M60 70L40 40M60 70L80 40M60 120L40 90M60 120L80 90M60 170L40 140M60 170L80 140" />
                <path class="text-white/60" d="M20 240H100" />
                <path class="text-white/80" d="M60 70H330" />
                <rect class="text-bk-gold/80" x="300" y="58" width="30" height="20" fill="currentColor" stroke="transparent" />
                <g class="trolley" transform="translate(180 68)">
                  <circle r="6" class="text-white" fill="currentColor" stroke="transparent"/>
                  <path d="M0 6V88" class="text-white/70"/>
                  <g class="hook">
                    <rect x="-8" y="88" width="16" height="10" rx="2" class="text-bk-gold" fill="currentColor" stroke="transparent"/>
                    <path d="M0 98v12" class="text-white/80"/>
                    <rect x="-16" y="110" width="32" height="18" rx="2" class="text-white/80" fill="currentColor" stroke="transparent"/>
                  </g>
                </g>
              </g>
            </svg>
          </div>
        </div>

        <!-- D : KPI -->
        <div class="lg:col-span-4 grid gap-4" v-reveal>
          <div class="card ring-gold p-5">
            <div class="text-xs text-white/70 text-center">Satisfaction clients</div>
            <div class="mt-2 flex items-center justify-center gap-5">
              <div class="radial" style="--val:98"></div>
              <div>
                <div class="text-3xl font-extrabold text-center"><span v-countup="98">98</span>%</div>
                <div class="text-white/70 text-sm text-center">2024–2025</div>
              </div>
            </div>
          </div>

          <div class="card ring-gold p-5">
            <div class="flex items-center justify-between">
              <div class="text-xs text-white/70">Chantiers livrés</div>
              <div class="text-xs text-white/60">12 derniers mois</div>
            </div>
            <svg viewBox="0 0 140 42" class="mt-2 w-full h-14">
              <polyline points="0,30 12,24 24,28 36,20 48,18 60,24 72,16 84,14 96,12 108,10 120,12 132,8"
                        fill="none" stroke="url(#g)" stroke-width="2.5" />
              <defs>
                <linearGradient id="g" x1="0" x2="1" y1="0" y2="0">
                  <stop offset="0" stop-color="rgba(220,193,118,.25)"/>
                  <stop offset="1" stop-color="rgba(255,255,255,.35)"/>
                </linearGradient>
              </defs>
            </svg>
            <div class="mt-1 text-2xl font-extrabold"><span v-countup="56">56</span> projets</div>
          </div>

          <div class="card ring-gold p-5">
            <div class="flex items-center gap-3">
              <div class="badge-iso">ISO</div>
              <div>
                <div class="font-bold">Qualité & Sécurité</div>
                <div class="text-white/70 text-sm">Référentiels ISO 9001/45001.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= PARTENAIRES ================= -->
    <section class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Ils nous font confiance</h2>
        <div class="marquee mt-5" v-reveal>
          <div class="marquee-track">
            <span class="logo-pill">ONCF</span><span class="logo-pill">ADM</span><span class="logo-pill">INDUSTRIA</span><span class="logo-pill">LOGIPARK</span><span class="logo-pill">URBA</span><span class="logo-pill">IMMOPRO</span><span class="logo-pill">CITYDEV</span><span class="logo-pill">PORT AUTH</span><span class="logo-pill">AGROTECH</span>
            <span class="logo-pill">ONCF</span><span class="logo-pill">ADM</span><span class="logo-pill">INDUSTRIA</span><span class="logo-pill">LOGIPARK</span><span class="logo-pill">URBA</span><span class="logo-pill">IMMOPRO</span><span class="logo-pill">CITYDEV</span><span class="logo-pill">PORT AUTH</span><span class="logo-pill">AGROTECH</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= SERVICES ================= -->
    <section id="services" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Nos services</h2>

        <div class="mt-8 grid gap-5 md:grid-cols-3">
          <article class="card-iso ring-gold" @mousemove="handleTilt" @mouseleave="resetTilt" v-reveal>
            <div class="illus iso-vrd" aria-hidden="true"><span class="b1"></span><span class="b2"></span><span class="b3"></span></div>
            <header class="flex items-center gap-3 mt-2">
              <span class="badge-ghost">VRD</span>
              <h3 class="text-xl font-bold">Voirie & Routes</h3>
            </header>
            <p class="text-white/80 mt-2">Terrassement, assainissement, chaussées, ouvrages d’art.</p>
            <ul class="mt-3 text-sm text-white/70 list-disc pl-5 space-y-1">
              <li>Chaussées souples & rigides</li><li>Ouvrages hydrauliques</li><li>Signalisation & sécurité</li>
            </ul>
          </article>

          <article class="card-iso ring-gold" @mousemove="handleTilt" @mouseleave="resetTilt" v-reveal>
            <div class="illus iso-bat" aria-hidden="true"><span class="core"></span><span class="slab"></span><span class="beam"></span></div>
            <header class="flex items-center gap-3 mt-2">
              <span class="badge-ghost">BAT</span>
              <h3 class="text-xl font-bold">Bâtiments</h3>
            </header>
            <p class="text-white/80 mt-2">Logements, bureaux, structures béton, finitions premium.</p>
            <ul class="mt-3 text-sm text-white/70 list-disc pl-5 space-y-1">
              <li>Structure & second œuvre</li><li>BIM exé & DOE</li><li>Contrôle qualité</li>
            </ul>
          </article>

          <article class="card-iso ring-gold" @mousemove="handleTilt" @mouseleave="resetTilt" v-reveal>
            <div class="illus iso-gc" aria-hidden="true"><span class="dam"></span><span class="pipe"></span></div>
            <header class="flex items-center gap-3 mt-2">
              <span class="badge-ghost">GC</span>
              <h3 class="text-xl font-bold">Génie civil</h3>
            </header>
            <p class="text-white/80 mt-2">Hydraulique, soutènements, bassins & réseaux.</p>
            <ul class="mt-3 text-sm text-white/70 list-disc pl-5 space-y-1">
              <li>Études d’exécution</li><li>Phasage & logistique</li><li>Réceptions & DOE</li>
            </ul>
          </article>
        </div>
      </div>
    </section>

    <!-- ================= RÉALISATIONS ================= -->
    <section id="projects" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Réalisations</h2>

        <!-- carrousel -->
        <div class="mt-8 overflow-x-auto no-scrollbar" v-reveal>
          <div class="flex gap-6 snap-x snap-mandatory scroll-pl-4">
            <div v-for="p in heroList" :key="p.id" class="ref-card snap-start group">
              <div class="grain" aria-hidden="true"></div>
              <img v-if="p.cover_image" :src="`/storage/${p.cover_image}`" class="absolute inset-0 w-full h-full object-cover opacity-[.88]" alt="" loading="lazy" />
              <div class="ref-caption">
                <div class="font-bold">{{ p.title }}</div>
                <div class="text-white/70 text-sm">{{ p.category || '—' }}<span v-if="p.city"> — {{ p.city }}</span></div>
              </div>
              <div class="ref-overlay">
                <Link :href="r('public.projects.show', p.slug ?? p.id, false, `/realisations/${p.slug ?? p.id}`)" class="btn-outline-gold">Détails du projet</Link>
              </div>
            </div>
          </div>
        </div>

        <!-- CTA voir toutes -->
        <div class="mt-6 flex justify-center">
          <Link :href="r('public.projects.index', {}, false, '/realisations')" class="btn-ghost">Voir toutes les réalisations</Link>
        </div>

        <!-- grille aperçue -->
        <transition name="fade">
          <div v-if="showAllRefs" class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="p in gridList" :key="p.id" class="ref-card group h-[260px] md:h-[300px]">
              <div class="grain" aria-hidden="true"></div>
              <img v-if="p.cover_image" :src="`/storage/${p.cover_image}`" class="absolute inset-0 w-full h-full object-cover opacity-[.88]" alt="" loading="lazy" />
              <div class="ref-caption">
                <div class="font-bold">{{ p.title }}</div>
                <div class="text-white/70 text-sm">{{ p.category || '—' }}<span v-if="p.city"> — {{ p.city }}</span></div>
              </div>
              <div class="ref-overlay">
                <Link :href="r('public.projects.show', p.slug ?? p.id, false, `/realisations/${p.slug ?? p.id}`)" class="btn-outline-gold">Fiche projet</Link>
              </div>
            </div>
          </div>
        </transition>

        <div class="mt-4 flex justify-center">
          <button class="btn-ghost" @click="showAllRefs = !showAllRefs">
            {{ showAllRefs ? 'Masquer l’aperçu' : 'Voir un aperçu grille' }}
          </button>
        </div>
      </div>
    </section>

    <!-- ================= RFP / DEVIS ================= -->
    <section id="rfp" class="py-10 md:py-14">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Appels d’offres & Devis</h2>

        <div class="mt-8 card ring-gold p-6 md:p-8" v-reveal>
          <div class="grid md:grid-cols-2 gap-8 items-start">
            <div>
              <h3 class="text-2xl md:text-3xl font-extrabold">Envoyez votre DCE ou faites une demande express</h3>
              <p class="text-white/80 mt-3">
                Réponse rapide avec chiffrage fiable, variantes et phasage réaliste.
              </p>
              <div class="mt-6 flex gap-3">
                <a href="#contact" class="btn-outline-gold">Nous contacter</a>
                <a href="#devis" class="btn-ghost" @click="focusDevis">Devis express</a>
              </div>
              <div class="mt-6 mini-bullets">
                <span>✔️ Métrés & variantes optimisées</span>
                <span>✔️ Délais & phasage détaillés</span>
                <span>✔️ Proposition claire et traçable</span>
              </div>
            </div>

            <!-- FORM devis express -->
            <form id="devis" class="grid gap-3" @submit.prevent="submitQuote">
              <div class="grid md:grid-cols-2 gap-3">
                <input id="devis-name" v-model="qForm.name" type="text" placeholder="Nom / Société" class="field" required />
                <input v-model="qForm.email" type="email" placeholder="Email" class="field" />
              </div>
              <div class="grid md:grid-cols-2 gap-3">
                <input v-model="qForm.phone" type="tel" placeholder="Téléphone" class="field" />
                <input v-model="qForm.city" type="text" placeholder="Ville / Site (ex. Tanger Med)" class="field" />
              </div>
              <textarea v-model="qForm.message" rows="3" placeholder="Votre besoin (lieu, délais, budget estimatif…)" class="field"></textarea>

              <label class="upload">
                <input type="file" multiple accept=".pdf,.zip,.rar,.7z,.doc,.docx,.xls,.xlsx,.dwg,.dxf" class="sr-only" @change="pickFiles" />
                <span class="upload-ui">Joindre DCE / plans (PDF, ZIP) — <span class="opacity-80">{{ qForm.files.length }} fichier(s)</span></span>
              </label>

              <input v-model="qForm.hp" type="text" class="sr-only" tabindex="-1" autocomplete="off" />

              <button type="submit" class="btn-outline-gold w-fit" :disabled="qForm.processing">
                {{ qForm.processing ? 'Envoi…' : 'Envoyer' }}
              </button>

              <div v-if="Object.keys(qForm.errors).length" class="text-sm text-red-300 mt-1">Merci de vérifier les champs saisis.</div>
              <div v-if="qForm.progress" class="h-1 rounded bg-white/10 overflow-hidden"><div class="h-1 bg-bk-gold" :style="{ width: `${qForm.progress.percentage}%` }"></div></div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= ACTUALITÉS ================= -->
    <section id="news" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Actualités</h2>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <article v-for="a in postList" :key="a.id" class="news-card ring-gold" v-reveal>
            <div class="news-thumb">
              <img v-if="a.cover_image" :src="`/storage/${a.cover_image}`" class="w-full h-full object-cover" alt="" loading="lazy" />
            </div>
            <div class="news-body">
              <h3 class="news-title">{{ a.title }}</h3>
              <p class="news-excerpt">{{ a.excerpt || '—' }}</p>
              <div class="text-xs text-white/60 mt-1">{{ fmt(a.published_at || undefined) }}</div>
              <Link :href="r('public.posts.show', a.slug ?? a.id, false, `/actualites/${a.slug ?? a.id}`)" class="btn-ghost mt-3 inline-flex">Lire</Link>
            </div>
          </article>
        </div>

        <div class="mt-6 flex justify-center">
          <Link :href="r('public.posts.index', {}, false, '/actualites')" class="btn-outline-gold">Toutes les actualités</Link>
        </div>
      </div>
    </section>

    <!-- ================= CONTACT (infos + carte) ================= -->
    <section id="contact" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-6 md:gap-8 items-stretch">
        <div class="card ring-gold p-6 md:p-7 flex flex-col gap-5" v-reveal>
          <div class="text-center">
            <h2 class="section-title mb-1">Contact</h2>
            <p class="text-white/80">Implantés à Tanger — réponse sous 24h ouvrées.</p>
          </div>

          <ul class="space-y-3 text-white/85">
            <li class="flex items-center gap-3"><span class="i i-phone"></span><a href="tel:+212770556021" class="hover:text-bk-gold font-semibold">+212 7 70 55 60 21</a></li>
            <li class="flex items-center gap-3"><span class="i i-mail"></span><a href="mailto:contact@bkconstruction.ma" class="hover:text-bk-gold">contact@bkconstruction.ma</a></li>
            <li class="flex items-center gap-3"><span class="i i-pin"></span><span>Tanger, Maroc</span></li>
            <li class="flex items-center gap-3"><span class="i i-clock"></span><span>Lun–Sam : 8h30 – 18h</span></li>
          </ul>

          <div class="grid grid-cols-3 gap-2 pt-2">
            <a href="tel:+212770556021" class="btn-outline-gold sm w-full justify-center gap-2"><span class="i i-phone"></span> Appeler</a>
            <a href="https://wa.me/212770556021" target="_blank" rel="noopener" class="btn-ghost sm w-full justify-center gap-2"><span class="i i-wa"></span> WhatsApp</a>
            <a href="mailto:contact@bkconstruction.ma" class="btn-ghost sm w-full justify-center gap-2"><span class="i i-mail"></span> Email</a>
          </div>
        </div>

        <div class="card ring-gold overflow-hidden" v-reveal>
          <div class="relative w-full h-[340px] md:h-full">
            <iframe class="absolute inset-0 w-full h-full" title="BK Construction — Tanger" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=Tanger%2C%20Maroc&output=embed"></iframe>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<style scoped>
/* helpers */
.no-scrollbar::-webkit-scrollbar{ display:none } .no-scrollbar{ -ms-overflow-style:none; scrollbar-width:none }
@keyframes shine { from{ transform: translateX(-120%);} to{ transform: translateX(120%);} }
.reveal{ opacity:0; transform: translateY(12px) scale(.985); filter: blur(2px); transition: all .6s cubic-bezier(.2,.8,.2,1) }
.reveal.is-visible{ opacity:1; transform:none; filter:none }

/* titres */
.section-title{ position:relative; display:block; width:100%; font-weight:800; text-align:center; letter-spacing:-.01em; font-size:clamp(1.65rem,2vw + 1rem,2.25rem) }
.section-title::after{ content:""; display:block; height:2px; width:110px; margin:.7rem auto 0; background:linear-gradient(90deg,#dcc176,rgba(253,253,254,.6)); opacity:.85; border-radius:999px }

/* tokens UI */
.card{ position:relative; border-radius:1.1rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); backdrop-filter:blur(10px) }
.ring-gold{ position:relative }
.ring-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; z-index:0; pointer-events:none;
  background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9));
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.tag-ghost{ display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .6rem; border-radius:.55rem; font-weight:800; font-size:.75rem; color:#fdfdfe; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.20); box-shadow: inset 0 1px 0 rgba(255,255,255,.25) }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.05rem; border-radius:1rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe; transition:border-color .2s, transform .2s }
.btn-ghost:hover{ border-color: rgba(220,193,118,.6); transform: translateY(-1px) }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.8rem 1.15rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent; transition: transform .2s }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9));
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.btn-outline-gold:hover{ transform: translateY(-2px) }
.chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .7rem; border-radius:.7rem; color:#fdfdfe; font-weight:600; font-size:.9rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.14) }
.badge-ghost{ display:inline-flex; align-items:center; justify-content:center; border-radius:.4rem; padding:.25rem .4rem; font-size:.7rem; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:#fdfdfe }
.field{ width:100%; border-radius:.75rem; padding:.75rem .9rem; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.14); color:#fdfdfe; outline:none; position:relative; z-index:1 }
.field:focus{ outline:2px solid rgba(220,193,118,.6); outline-offset:2px }
.upload{ display:inline-flex; border-radius:.8rem; border:1px dashed rgba(220,193,118,.5); color:#fdfdfe; position:relative; z-index:1 }
.upload .upload-ui{ width:100%; padding:.85rem .95rem; border-radius:.8rem; background:rgba(220,193,118,.1) }

/* Héros 3D */
.hero-ribbon{ position:absolute; left:0; right:0; top:-10px; height:36px; opacity:.45; transform: rotate(-2deg);
  background: repeating-linear-gradient(135deg,#2a3644 0 18px,#2a3644 18px 28px,#dcc176 28px 46px,#dcc176 46px 56px) }
.hero-halo{ position:absolute; inset:-40px; border-radius:2rem; filter:blur(36px); opacity:.55; background: radial-gradient(60% 60% at 25% 25%, #dcc176 0%, transparent 60%) }
.persp{ perspective:1200px; position:relative }
.platform{ height:11rem; border-radius:1.2rem; background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.05)); border:1px solid rgba(255,255,255,.12);
  transform: rotateX(55deg) rotateZ(-12deg); box-shadow: 0 50px 120px -40px rgba(0,0,0,.6) }
.platform-cards{ position:absolute; inset:0; display:grid; place-items:center; pointer-events:none }
.platform-cards>div{ position:absolute }
.iso-card{ height:var(--h); width:var(--w); transform: translateZ(0) rotateX(55deg) rotateZ(-12deg); background: linear-gradient(160deg, rgba(255,255,255,.12), rgba(21,30,39,.55)); border: 1px solid rgba(255,255,255,.14); border-radius: 12px; box-shadow: 0 40px 90px -40px rgba(0,0,0,.55); z-index: var(--z) }
.iso-card::after{ content:""; position:absolute; inset:-1px; border-radius:12px; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.8),rgba(255,255,255,.2),rgba(220,193,118,.8));
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude; opacity:.75 }
.crane{ position:absolute; right:0; top:-16px; width:70%; filter: drop-shadow(0 6px 30px rgba(0,0,0,.35)) }
.trolley{ animation: trolley 6s ease-in-out infinite alternate } .hook{ animation: hook 3s ease-in-out infinite alternate }
@keyframes trolley{ from{ transform: translateX(40px) } to{ transform: translateX(160px) } }
@keyframes hook{ from{ transform: translateY(0) } to{ transform: translateY(8px) } }

/* services */
.card-iso{ transform:perspective(900px) rotateX(var(--rx,0deg)) rotateY(var(--ry,0deg)); transition: transform .15s ease-out, box-shadow .2s ease; box-shadow:0 40px 120px -40px rgba(0,0,0,.55); border-radius:1rem; padding:1.1rem 1.2rem 1.2rem; background:linear-gradient(160deg,rgba(255,255,255,.08),rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.12) }
.illus{ height:120px; position:relative; margin-top:.1rem }
.iso-vrd .b1,.iso-vrd .b2,.iso-vrd .b3{ position:absolute; border-radius:10px; transform: rotateX(55deg) rotateZ(-12deg); background:linear-gradient(160deg,rgba(255,255,255,.12),rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.14) }
.iso-vrd .b1{ width:120px; height:26px; left:6%; bottom:14% } .iso-vrd .b2{ width:100px; height:22px; left:34%; bottom:26% } .iso-vrd .b3{ width:78px; height:18px; left:60%; bottom:38% }
.iso-bat .core,.iso-bat .slab,.iso-bat .beam{ position:absolute; transform: rotateX(55deg) rotateZ(-12deg); border:1px solid rgba(255,255,255,.14); background:linear-gradient(160deg,rgba(255,255,255,.12),rgba(21,30,39,.55)); border-radius:8px }
.iso-bat .core{ width:50px; height:70px; left:18%; bottom:16% } .iso-bat .slab{ width:110px; height:12px; left:26%; bottom:28% } .iso-bat .beam{ width:60px; height:8px; left:52%; bottom:38% }
.iso-gc .dam,.iso-gc .pipe{ position:absolute; transform: rotateX(55deg) rotateZ(-12deg); background:linear-gradient(160deg,rgba(255,255,255,.12),rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.14); border-radius:10px }
.iso-gc .dam{ width:120px; height:28px; left:10%; bottom:18% } .iso-gc .pipe{ width:26px; height:26px; left:66%; bottom:34%; border-radius:999px }

/* expertise etc. (inchangé) */
.expert{ padding:1rem; border-radius:1rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); text-align:center }
.expert-title{ font-weight:800; margin-top:.35rem } .expert-text{ color:rgba(253,253,254,.78); margin-top:.2rem; font-size:.95rem }
.icon-iso{ width:58px; height:58px; margin:0 auto; position:relative; filter: drop-shadow(0 12px 30px rgba(0,0,0,.35)) }

/* refs */
.ref-card{ position:relative; width:360px; height:230px; border-radius:1rem; overflow:hidden; border:1px solid rgba(255,255,255,.12); background: radial-gradient(60% 60% at 30% 30%, rgba(220,193,118,.14), transparent 60%) }
@media (min-width:768px){ .ref-card{ width:520px; height:320px } }
.ref-caption{ position:absolute; left:0; right:0; bottom:0; padding:1rem; background: linear-gradient(180deg, rgba(15,20,26,.85), transparent 80%) }
.ref-overlay{ position:absolute; inset:0; display:grid; place-items:center; background: rgba(0,0,0,.35); opacity:0; transition: opacity .25s ease }
.ref-card:hover .ref-overlay{ opacity:1 }
.grain{ position:absolute; inset:0; opacity:.12; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'%3E%3Cfilter id='a'%3E%3CfeTurbulence baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23a)' opacity='.25'/%3E%3C/svg%3E") }

/* jauges */
.radial{ --val:92; width:72px; height:72px; border-radius:999px; display:grid; place-items:center; background:conic-gradient(#dcc176 calc(var(--val)*1%), rgba(255,255,255,.2) 0), radial-gradient(farthest-side, rgba(21,30,39,1) 60%, transparent 61%); box-shadow: inset 0 0 0 1px rgba(255,255,255,.12); color:#fdfdfe; font-weight:800 }
.radial.small{ width:54px; height:54px } .radial.ok{ background: conic-gradient(#62d47b calc(var(--val)*1%), rgba(255,255,255,.2) 0), radial-gradient(farthest-side, rgba(21,30,39,1) 60%, transparent 61%) }

/* marquee */
.marquee{ position:relative; overflow:hidden; mask-image: linear-gradient(90deg, transparent, black 10%, black 90%, transparent) }
.marquee-track{ display:flex; align-items:center; gap:2.25rem; padding:.25rem 0; animation: marquee 22s linear infinite; opacity:.9 }
.marquee:hover .marquee-track{ animation-play-state: paused }
@keyframes marquee{ from{ transform: translateX(0) } to{ transform: translateX(-50%) } }
@media (prefers-reduced-motion: reduce){ .marquee-track{ animation: none } }
.logo-pill{ display:inline-flex; align-items:center; justify-content:center; padding:.6rem 1.1rem; border-radius:.8rem; font-weight:800; letter-spacing:.3px; color:#fdfdfe; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12) }

/* news */
.news-card{ overflow:hidden; border-radius:1rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12) }
.news-thumb{ height:140px; background: radial-gradient(60% 60% at 30% 30%, rgba(220,193,118,.2), transparent 60%) }
.news-body{ padding:1rem } .news-title{ font-weight:800 } .news-excerpt{ color:rgba(253,253,254,.78); margin-top:.15rem }

/* icônes contact */
.i{ width:18px; height:18px; display:inline-block; border-radius:5px; background: rgba(255,255,255,.12) }
.i-phone{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-mail{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M20 8l-8 5-8-5V6l8 5 8-5v2zM4 18h16v-2H4v2z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M20 8l-8 5-8-5V6l8 5 8-5v2zM4 18h16v-2H4v2z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-pin{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-wa{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M20 12a8 8 0 1 0-14.1 5l-1.1 3.9 4-1A8 8 0 1 0 20 12zM7 18l.5-1.7a6 6 0 1 1 2.2 2.2L8 19l-1 .3L7 18z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M20 12a8 8 0 1 0-14.1 5l-1.1 3.9 4-1A8 8 0 1 0 20 12zM7 18l.5-1.7a6 6 0 1 1 2.2 2.2L8 19l-1 .3L7 18z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-clock{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; background:#dcc176 }
</style>
