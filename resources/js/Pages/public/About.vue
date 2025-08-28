<!-- resources/js/Pages/public/About.vue -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

/* Helper route robuste (Ziggy + fallback) */
function r(name: string, params?: any, absolute = false, fallback: string = '#') {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (!Z) return fallback
    return ziggyRoute(name, params ?? {}, absolute, Z)
  } catch { return fallback }
}

/* Props optionnelles */
type Stats = { roads_km:number; buildings:number; years:number }
type Founder = { name:string; role?:string; photo?:string; quote?:string; bio?:string }
const props = defineProps<{ stats?: Partial<Stats>, founder?: Partial<Founder> }>()

const stats = computed<Stats>(() => ({
  roads_km: 120,
  buildings: 40,
  years: 1, // 1 an d’expérience
  ...(props.stats ?? {})
}))

const founder = computed<Founder>(() => ({
  name: 'Rachid Ag Ahmed Mohamed',
  role: 'Fondateur & Directeur',
  photo: '/assets/founder-rachid.jpg', // ← remplace par ton image réelle
  quote: 'Construire durablement, avec exigence et clarté.',
  bio: `Rachid Ag Ahmed Mohamed est le fondateur de BKOCONSTRUCTION et en assure la direction. 
Il impulse une culture d’excellence centrée sur la qualité, la sécurité et le respect des délais. 
Sa vision : des chantiers maîtrisés, transparents et créateurs de valeur pour les partenaires publics comme privés.`,
  ...(props.founder ?? {})
}))

/* v-reveal minimal */
let io:IntersectionObserver|null = null
const vReveal = {
  mounted(el:HTMLElement){
    el.classList.add('reveal')
    if(!io){
      io = new IntersectionObserver((entries)=>{
        entries.forEach(e=>{
          if(e.isIntersecting){ e.target.classList.add('is-visible'); io?.unobserve(e.target) }
        })
      },{threshold:.14})
    }
    io.observe(el)
  }
}

/* v-countup minimal */
const vCountup = {
  mounted(el:HTMLElement, binding:{value:number}){
    const target = Number(binding.value ?? 0)
    el.textContent = '0'
    const obs = new IntersectionObserver((ents)=>{
      if(!ents[0].isIntersecting) return
      obs.disconnect()
      const dur=1200, t0=performance.now()
      const tick=(t:number)=>{
        const p=Math.min(1,(t-t0)/dur), e=1-Math.pow(1-p,3)
        el.textContent = Math.round(target*e).toLocaleString('fr-FR')
        if(p<1) requestAnimationFrame(tick)
      }
      requestAnimationFrame(tick)
    },{threshold:.35})
    obs.observe(el)
  }
}
</script>

<template>
  <PublicLayout>
    <!-- HERO À PROPOS -->
    <section class="relative overflow-hidden pt-14 md:pt-20 pb-10 md:pb-14">
      <div class="hero-ribbon" aria-hidden="true"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <!-- Texte -->
        <div class="lg:col-span-6" v-reveal>
          <span class="tag-ghost">À propos</span>
          <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
            BKOCONSTRUCTION — <span class="text-bk-gold">Construire l’avenir</span> à Tanger
          </h1>
          <p class="mt-4 text-white/80 max-w-2xl">
            Basée à Tanger, BKOCONSTRUCTION est spécialisée dans la construction, l’aménagement routier et la rénovation.
            Notre mission : des solutions solides, durables et adaptées, du résidentiel au tertiaire, jusqu’aux infrastructures publiques.
          </p>
          <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Appels d’offres & devis</Link>
            <Link :href="r('public.services', {}, false, '/services')" class="btn-ghost">Nos services</Link>
          </div>
          <div class="mt-5 text-white/75 space-y-2">
            <p>
              Avec une équipe expérimentée et engagée, nous plaçons la <b>qualité</b>, la <b>sécurité</b> et le
              <b>respect des délais</b> au cœur de chaque chantier.
            </p>
            <p>
              Chaque projet bâtit plus qu’un ouvrage : c’est la construction de <b>confiance</b> et la création de
              <b>valeur</b> pour nos partenaires. <br>
              <span class="text-white/85 font-semibold">BKOCONSTRUCTION, votre partenaire de confiance pour des projets qui durent.</span>
            </p>
          </div>
        </div>

        <!-- Scène 3D — NOUVELLE VERSION (orbite blueprint premium) -->
        <div class="lg:col-span-6 relative" v-reveal>
          <div class="hero-halo" aria-hidden="true"></div>

          <div class="about3d">
            <!-- sol blueprint -->
            <div class="grid-floor"></div>

            <!-- orbite + anneaux -->
            <div class="orbital">
              <div class="ring r1"></div>
              <div class="ring r2"></div>
              <div class="ring r3"></div>
              <div class="core"></div>
              <div class="scan"></div>
            </div>

            <!-- “skyline” extrudé isométrique -->
            <div class="spikes">
              <span style="--i:0"></span>
              <span style="--i:1"></span>
              <span style="--i:2"></span>
              <span style="--i:3"></span>
              <span style="--i:4"></span>
              <span style="--i:5"></span>
              <span style="--i:6"></span>
              <span style="--i:7"></span>
              <span style="--i:8"></span>
              <span style="--i:9"></span>
              <span style="--i:10"></span>
              <span style="--i:11"></span>
            </div>

            <!-- pin Tanger -->
            <div class="pin">
              <span class="dot"></span>
              <span class="label">Tanger</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FONDATEUR -->
    <section id="fondateur" class="py-8 md:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <!-- Texte fondateur -->
        <div class="lg:col-span-7" v-reveal>
          <h2 class="section-title text-left">Le fondateur</h2>
          <div class="mt-4 card ring-gold p-6 md:p-7">
            <div class="flex items-start gap-4">
              <div class="i i-star mt-1 shrink-0"></div>
              <div class="space-y-3">
                <div class="text-2xl md:text-3xl font-extrabold leading-tight">
                  {{ founder.name }}
                </div>
                <div class="text-white/75 font-semibold">{{ founder.role }}</div>
                <p class="text-white/85">{{ founder.bio }}</p>
                <blockquote class="text-white/80 text-sm pl-3 border-l-2 border-white/20 italic">“{{ founder.quote }}”</blockquote>
                <div class="pt-2 flex flex-wrap gap-2">
                  <span class="badge-ghost">Qualité</span>
                  <span class="badge-ghost">Sécurité</span>
                  <span class="badge-ghost">Délais</span>
                  <span class="badge-ghost">Transparence</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Photo fondateur -->
        <div class="lg:col-span-5 relative" v-reveal>
          <div class="portrait-wrap">
            <div class="portrait-halo" aria-hidden="true"></div>
            <div class="portrait-frame gradient-ring">
              <img :src="founder.photo" :alt="`Portrait de ${founder.name}`" class="portrait-img" loading="lazy">
              <div class="portrait-shine" aria-hidden="true"></div>
            </div>
            <div class="portrait-chip">Direction</div>
          </div>
        </div>
      </div>
    </section>

    <!-- VALEURS & MÉTHODE -->
    <section class="py-8 md:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-6">
        <!-- Valeurs -->
        <div class="lg:col-span-5 space-y-4" v-reveal>
          <h2 class="section-title text-left">Nos valeurs</h2>
          <div class="grid gap-3">
            <div class="card ring-gold p-4 flex items-start gap-3">
              <div class="i i-shield shrink-0"></div>
              <div>
                <div class="font-extrabold">Qualité & Durabilité</div>
                <p class="text-white/75 text-sm mt-1">Matériaux performants, finitions soignées, contrôles qualité continus.</p>
              </div>
            </div>
            <div class="card ring-gold p-4 flex items-start gap-3">
              <div class="i i-hse shrink-0"></div>
              <div>
                <div class="font-extrabold">Sécurité & HSE</div>
                <p class="text-white/75 text-sm mt-1">Prévention, EPI, balisage, briefings quotidiens — zéro incident comme objectif.</p>
              </div>
            </div>
            <div class="card ring-gold p-4 flex items-start gap-3">
              <div class="i i-time shrink-0"></div>
              <div>
                <div class="font-extrabold">Respect des délais</div>
                <p class="text-white/75 text-sm mt-1">Phasage réaliste, coordination fluide, suivi d’avancement transparent.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Méthode -->
        <div class="lg:col-span-7" v-reveal>
          <h2 class="section-title text-left">Notre méthode</h2>
          <div class="mt-4 grid md:grid-cols-2 gap-4">
            <div class="card-iso ring-gold p-5">
              <div class="badge-ghost mb-2">Étape 1</div>
              <div class="font-extrabold text-lg">Audit & cadrage</div>
              <p class="text-white/75 text-sm mt-1">Recueil du besoin, visite site, risques, objectifs coûts/délais.</p>
            </div>
            <div class="card-iso ring-gold p-5">
              <div class="badge-ghost mb-2">Étape 2</div>
              <div class="font-extrabold text-lg">Études & variantes</div>
              <p class="text-white/75 text-sm mt-1">Métrés, AVP/EXE, optimisations techniques, planning & budget.</p>
            </div>
            <div class="card-iso ring-gold p-5">
              <div class="badge-ghost mb-2">Étape 3</div>
              <div class="font-extrabold text-lg">Exécution & HSE</div>
              <p class="text-white/75 text-sm mt-1">Pilotage terrain, contrôles qualité, sécurité renforcée, reporting.</p>
            </div>
            <div class="card-iso ring-gold p-5">
              <div class="badge-ghost mb-2">Étape 4</div>
              <div class="font-extrabold text-lg">Réception & DOE</div>
              <p class="text-white/75 text-sm mt-1">Levé de réserves, DOE, garantie, accompagnement post-livraison.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CHIFFRES CLÉS -->
    <section class="py-8 md:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" v-reveal>
        <div class="card ring-gold p-6 md:p-8">
          <div class="grid sm:grid-cols-3 gap-4">
            <div class="kpi">
              <div class="radial" style="--val:95"></div>
              <div>
                <div class="text-3xl font-extrabold leading-none"><span v-countup="stats.roads_km">0</span>+</div>
                <div class="text-white/70 text-sm">km de voiries</div>
              </div>
            </div>
            <div class="kpi">
              <div class="radial" style="--val:88"></div>
              <div>
                <div class="text-3xl font-extrabold leading-none"><span v-countup="stats.buildings">0</span>+</div>
                <div class="text-white/70 text-sm">bâtiments livrés</div>
              </div>
            </div>
            <div class="kpi">
              <div class="radial" style="--val:100"></div>
              <div>
                <div class="text-3xl font-extrabold leading-none"><span v-countup="stats.years">0</span>+</div>
                <div class="text-white/70 text-sm">an d’expérience</div>
              </div>
            </div>
          </div>
          <div class="mt-6 text-center">
            <Link :href="r('public.projects', {}, false, '/realisations')" class="btn-ghost">Voir nos réalisations</Link>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" v-reveal>
        <div class="footer-card gradient-ring p-6 md:p-8 rounded-2xl relative overflow-hidden">
          <div class="glow g1"></div><div class="glow g2"></div><div class="glow g3"></div>
          <div class="grid md:grid-cols-3 gap-5 items-center">
            <div class="md:col-span-2">
              <h3 class="text-2xl md:text-3xl font-extrabold">Parlons de votre projet</h3>
              <p class="text-white/80 mt-2">Devis express, variantes, planning réaliste. Réponse sous 24h ouvrées.</p>
            </div>
            <div class="flex gap-3 md:justify-end">
              <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Demander un devis</Link>
              <Link :href="r('public.contact', {}, false, '/contact')" class="btn-ghost">Contact</Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<style scoped>
/* ===== Reveal */
.reveal{ opacity:0; transform: translateY(12px) scale(.985); filter: blur(2px); transition: all .6s cubic-bezier(.2,.8,.2,1) }
.reveal.is-visible{ opacity:1; transform:none; filter:none }

/* ===== Tokens (cohérents avec tes autres pages) */
.card{ position:relative; border-radius:1.1rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); backdrop-filter:blur(10px) }
.ring-gold{ position:relative }
.ring-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; z-index:0; pointer-events:none; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.tag-ghost{ display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .6rem; border-radius:.55rem; font-weight:800; font-size:.75rem; color:#fdfdfe; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.20); box-shadow: inset 0 1px 0 rgba(255,255,255,.25) }
.section-title{ position:relative; display:block; width:100%; font-weight:800; letter-spacing:-.01em; font-size:clamp(1.5rem,2vw + 1rem,2rem) }
.section-title::after{ content:""; display:block; height:2px; width:110px; margin:.7rem 0 0; background:linear-gradient(90deg,#dcc176,rgba(253,253,254,.6)); opacity:.85; border-radius:999px }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.05rem; border-radius:1rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe; transition:border-color .2s, transform .2s }
.btn-ghost:hover{ border-color: rgba(220,193,118,.6); transform: translateY(-1px) }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.8rem 1.15rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent; transition: transform .2s }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.btn-outline-gold:hover{ transform: translateY(-2px) }
.badge-ghost{ display:inline-flex; align-items:center; justify-content:center; border-radius:.4rem; padding:.25rem .4rem; font-size:.7rem; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:#fdfdfe }
.badge-iso{ display:inline-flex; align-items:center; justify-content:center; width:34px; height:24px; border-radius:.45rem; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.22); font-weight:900 }

/* ===== Hero 3D (About) — Orbite blueprint premium */
.hero-ribbon{ position:absolute; left:0; right:0; top:-10px; height:36px; opacity:.45; transform: rotate(-2deg); background: repeating-linear-gradient(135deg,#2a3644 0 18px,#2a3644 18px 28px,#dcc176 28px 46px,#dcc176 46px 56px) }
.hero-halo{ position:absolute; inset:-40px; border-radius:2rem; filter:blur(36px); opacity:.55; background: radial-gradient(60% 60% at 25% 25%, #dcc176 0%, transparent 60%) }

.about3d{
  position:relative;
  height:min(26rem, 60vw);
  display:grid; place-items:center;
  perspective:1200px;
  transform-style:preserve-3d;
}

/* Sol grillagé isométrique */
.grid-floor{
  position:absolute; inset:auto;
  width:95%; height:12rem; bottom:-1rem;
  border-radius:1.2rem;
  transform: rotateX(62deg) rotateZ(-10deg);
  background:
    radial-gradient(60% 60% at 20% 30%, rgba(220,193,118,.25) 0%, transparent 60%),
    repeating-linear-gradient(0deg, rgba(255,255,255,.08) 0 2px, transparent 2px 12px),
    repeating-linear-gradient(90deg, rgba(255,255,255,.08) 0 2px, transparent 2px 12px),
    linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.04));
  border:1px solid rgba(255,255,255,.12);
  box-shadow: 0 60px 140px -50px rgba(0,0,0,.65);
}

/* Orbital stack */
.orbital{
  position:relative;
  width:min(26rem, 70%);
  aspect-ratio:1/1;
  transform: translateY(-6%) rotateX(14deg);
  transform-style:preserve-3d;
  filter: drop-shadow(0 20px 60px rgba(0,0,0,.45));
}

.orbital .ring{
  position:absolute; inset:0; border-radius:999px;
  border:1px solid rgba(255,255,255,.18);
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
}
.orbital .ring::before{
  content:""; position:absolute; inset:-1px; border-radius:inherit; padding:1px;
  background:linear-gradient(120deg,rgba(220,193,118,.85),rgba(255,255,255,.15),rgba(220,193,118,.85));
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude; opacity:.75;
}
.r1{ --z:20px; transform: translateZ(var(--z)) rotateZ(0deg);   animation: spin 18s linear infinite; }
.r2{ --z:40px; transform: translateZ(var(--z)) rotateZ(12deg);  animation: spinR 26s linear infinite; }
.r3{ --z:60px; transform: translateZ(var(--z)) rotateZ(-18deg); animation: spin 34s linear infinite; }

/* Coeur lumineux */
.core{
  position:absolute; inset:18% 18%;
  border-radius:999px;
  background:
    radial-gradient(closest-side, rgba(220,193,118,.8), rgba(220,193,118,.15) 60%, transparent 70%),
    radial-gradient(closest-side, rgba(255,255,255,.6), transparent 70%);
  filter: blur(0.2px);
  box-shadow:
    0 0 60px rgba(220,193,118,.30),
    0 0 120px rgba(220,193,118,.18) inset;
  transform: translateZ(70px);
}

/* Balayage radar */
.scan{
  position:absolute; inset:0; border-radius:999px;
  background:
    conic-gradient(from 0deg, rgba(220,193,118,.0) 0 88%, rgba(220,193,118,.6) 94%, rgba(220,193,118,.0) 100%);
  mix-blend-mode: screen;
  transform: translateZ(80px);
  animation: sweep 6s cubic-bezier(.2,.8,.2,1) infinite;
}

/* Piliers extrudés (skyline) sur l’orbite */
.spikes{
  position:absolute; inset:0; display:grid; place-items:center;
  transform: translateY(-6%) rotateX(14deg);
  pointer-events:none;
}
.spikes span{
  --N:12; /* nb d’éléments */
  --ang: calc(360deg/var(--N) * var(--i));
  position:absolute; width:12px; height:72px; bottom:14%;
  transform:
    rotateZ(var(--ang))
    translateY(-40%)
    rotateZ(calc(-1 * var(--ang)))
    rotateX(82deg);
  transform-origin: center bottom;
  border-radius:8px;
  background: linear-gradient(180deg, rgba(255,255,255,.16), rgba(255,255,255,.06));
  border:1px solid rgba(255,255,255,.14);
  box-shadow:
    0 14px 40px -10px rgba(0,0,0,.45),
    inset 0 0 0 1px rgba(255,255,255,.06);
}
.spikes span::after{
  content:""; position:absolute; inset:-1px; border-radius:inherit; padding:1px;
  background:linear-gradient(120deg,rgba(220,193,118,.75),rgba(255,255,255,.12),rgba(220,193,118,.75));
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude; opacity:.65;
}
.spikes span:nth-child(3n){ height:94px }
.spikes span:nth-child(4n){ height:82px }

/* Pin Tanger */
.pin{
  position:absolute; right:10%; top:16%;
  transform: translateZ(90px);
  display:flex; align-items:center; gap:.5rem;
  filter: drop-shadow(0 6px 18px rgba(0,0,0,.35));
}
.pin .dot{
  width:10px; height:10px; border-radius:999px; background:#dcc176;
  box-shadow: 0 0 0 3px rgba(220,193,118,.25), 0 0 18px rgba(220,193,118,.55);
  animation: pulse 2.2s ease-in-out infinite;
}
.pin .label{
  font-weight:800; font-size:.85rem; color:#fdfdfe;
  padding:.25rem .5rem; border-radius:.5rem;
  background:rgba(255,255,255,.08);
  border:1px solid rgba(255,255,255,.18);
}

/* Animations */
@keyframes spin   { to{ transform: translateZ(var(--z,20px)) rotateZ(360deg) } }
@keyframes spinR  { to{ transform: translateZ(var(--z,40px)) rotateZ(-360deg) } }
@keyframes sweep  { to{ transform: translateZ(80px) rotateZ(360deg) } }
@keyframes pulse  {
  0%,100%{ box-shadow: 0 0 0 2px rgba(220,193,118,.18), 0 0 14px rgba(220,193,118,.45) }
  50%     { box-shadow: 0 0 0 5px rgba(220,193,118,.35), 0 0 24px rgba(220,193,118,.75) }
}

/* ===== KPI */
.kpi{ display:flex; align-items:center; gap:1rem; justify-content:center }
.radial{ --val:92; width:72px; height:72px; border-radius:999px; display:grid; place-items:center; background:conic-gradient(#dcc176 calc(var(--val)*1%), rgba(255,255,255,.2) 0), radial-gradient(farthest-side, rgba(21,30,39,1) 60%, transparent 61%); box-shadow: inset 0 0 0 1px rgba(255,255,255,.12); color:#fdfdfe; font-weight:800 }

/* ===== Portrait fondateur */
.portrait-wrap{ position:relative; height:100%; display:grid; place-items:center; padding:1rem 0 }
.portrait-halo{ position:absolute; inset:-10%; filter:blur(60px); opacity:.35; background: radial-gradient(50% 50% at 35% 30%, #dcc176 0%, transparent 60%) }
.portrait-frame{ position:relative; width:min(420px,88%); aspect-ratio:3/4; border-radius:1.2rem; overflow:hidden; transform: rotate(-2.5deg); box-shadow:0 40px 120px -40px rgba(0,0,0,.55); background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12) }
.portrait-img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; filter:saturate(1.03) contrast(1.02) }
.portrait-shine{ position:absolute; inset:-30%; background: radial-gradient(40% 40% at 20% 20%, rgba(220,193,118,.35), transparent 60%); filter: blur(36px); opacity:.55; transform: rotate(8deg) }
.portrait-chip{ position:absolute; right:6%; bottom:-10px; transform:rotate(-2.5deg); display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .6rem; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.16); color:#fdfdfe; border-radius:.55rem; font-weight:800; font-size:.8rem }

/* ===== Icons (masks) */
.i{ width:22px; height:22px; display:inline-block; border-radius:6px; background: rgba(255,255,255,.14) }
.i-star{ -webkit-mask:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-shield{ -webkit-mask:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M12 2l7 4v6c0 5-3.8 9.3-7 10-3.2-.7-7-5-7-10V6l7-4z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M12 2l7 4v6c0 5-3.8 9.3-7 10-3.2-.7-7-5-7-10V6l7-4z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-hse{ -webkit-mask:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M12 1l9 5v6c0 6.1-4.1 11.4-9 12-4.9-.6-9-5.9-9-12V6l9-5zm0 5a3 3 0 100 6 3 3 0 000-6z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M12 1l9 5v6c0 6.1-4.1 11.4-9 12-4.9-.6-9-5.9-9-12V6l9-5zm0 5a3 3 0 100 6 3 3 0 000-6z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-time{ -webkit-mask:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; background:#dcc176 }

/* ===== Footer-like CTA halo */
.footer-card{ position:relative; border-radius:1.25rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12) }
.gradient-ring{ position:relative }
.gradient-ring::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; z-index:0; pointer-events:none; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.glow{ position:absolute; inset:auto; filter:blur(70px); opacity:.25; pointer-events:none }
.glow.g1{ left:5%; bottom:-20%; width:40%; height:120%; background: radial-gradient(50% 50% at 30% 40%, #dcc176 0%, transparent 60%) }
.glow.g2{ right:0; top:-20%; width:30%; height:110%; background: radial-gradient(50% 50% at 70% 30%, #ffffff 0%, transparent 60%) }
.glow.g3{ left:25%; top:10%; width:20%; height:60%; background: radial-gradient(50% 50% at 50% 50%, #dcc176 0%, transparent 60%) }

/* ===== Utilities */
.text-bk-gold{ color:#dcc176 }
</style>
