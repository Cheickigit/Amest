<!-- resources/js/Pages/Public/Services.vue -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

/* Helper Ziggy + fallback */
function r(name: string, params?: any, absolute = false, fallback: string = '#') {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (!Z) return fallback
    return ziggyRoute(name, params ?? {}, absolute, Z)
  } catch { return fallback }
}

/* v-reveal */
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

/* Tilt 3D (cards) */
function handleTilt(e:MouseEvent){
  const el = e.currentTarget as HTMLElement|undefined; if(!el) return
  const r = el.getBoundingClientRect()
  const cx = (e.clientX - r.left)/r.width - .5
  const cy = (e.clientY - r.top)/r.height - .5
  el.style.setProperty('--rx', (-cy*10).toFixed(2)+'deg')
  el.style.setProperty('--ry', (cx*10).toFixed(2)+'deg')
}
function resetTilt(e:MouseEvent){
  const el = e.currentTarget as HTMLElement|undefined; if(!el) return
  el.style.setProperty('--rx','0deg'); el.style.setProperty('--ry','0deg')
}

/* Onglets légers (vue filtrée) */
type Tab = 'vrd'|'bat'|'gc'
const tab = ref<Tab>('vrd')
</script>

<template>
  <PublicLayout>
    <!-- HERO -->
    <section class="relative overflow-hidden pt-14 md:pt-20 pb-10 md:pb-14">
      <div class="hero-ribbon" aria-hidden="true"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-6" v-reveal>
          <span class="tag-ghost">Expertises — VRD, Bâtiments, Génie civil</span>
          <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
            Nos <span class="text-bk-gold">services</span> pour des chantiers impeccables
          </h1>
          <p class="mt-4 text-white/80 max-w-2xl">
            De la conception à la réception, BKOCONSTRUCTION assure des projets
            performants : méthodes, traçabilité, qualité mesurée et sécurité prioritaire.
          </p>

          <div class="mt-6 flex flex-wrap gap-2">
            <span class="chip">Métrés & variantes</span>
            <span class="chip">Phasage & délais</span>
            <span class="chip">Qualité & HSE</span>
          </div>

          <div class="mt-7 flex flex-col sm:flex-row gap-3">
            <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Déposer un DCE</Link>
            <Link :href="r('public.contact', {}, false, '/contact')" class="btn-ghost">Nous contacter</Link>
          </div>
        </div>

        <!-- Scène 3D -->
        <div class="lg:col-span-6 relative" v-reveal>
          <div class="hero-halo" aria-hidden="true"></div>
          <div class="persp">
            <div class="platform"></div>
            <div class="platform-cards">
              <div class="iso-card" style="left:6%;  bottom:10%; --h:130px; --w:170px; --z:18;"></div>
              <div class="iso-card" style="left:36%; bottom:18%; --h:106px; --w:140px; --z:24;"></div>
              <div class="iso-card" style="left:60%; bottom:32%; --h:84px;  --w:120px; --z:36;"></div>
            </div>
            <svg class="crane" viewBox="0 0 520 260" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
              <g stroke-linecap="round" stroke-linejoin="round">
                <path class="text-white/70" d="M60 240V70M60 70L40 40M60 70L80 40M60 120L40 90M60 120L80 90M60 170L40 140M60 170L80 140"/>
                <path class="text-white/60" d="M20 240H100"/>
                <path class="text-white/80" d="M60 70H330"/>
                <rect class="text-bk-gold/80" x="300" y="58" width="30" height="20" fill="currentColor" stroke="transparent"/>
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
      </div>
    </section>

    <!-- TABS -->
    <section class="pt-4" v-reveal>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="inline-flex rounded-xl overflow-hidden border border-white/10 bg-white/5 backdrop-blur">
          <button
            class="px-4 py-2 text-sm font-semibold transition"
            :class="tab==='vrd' ? 'bg-bk-gold text-bk-night' : 'text-white/85 hover:text-white'"
            @click="tab='vrd'">Voirie & Réseaux</button>
          <button
            class="px-4 py-2 text-sm font-semibold transition border-l border-white/10"
            :class="tab==='bat' ? 'bg-bk-gold text-bk-night' : 'text-white/85 hover:text-white'"
            @click="tab='bat'">Bâtiments</button>
          <button
            class="px-4 py-2 text-sm font-semibold transition border-l border-white/10"
            :class="tab==='gc' ? 'bg-bk-gold text-bk-night' : 'text-white/85 hover:text-white'"
            @click="tab='gc'">Génie civil</button>
        </div>
      </div>
    </section>

    <!-- SERVICES — Grille 3D -->
    <section class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-6 md:grid-cols-3">
        <!-- VRD -->
        <article class="card-iso ring-gold" v-show="tab==='vrd'" @mousemove="handleTilt" @mouseleave="resetTilt" v-reveal>
          <div class="illus iso-vrd" aria-hidden="true"><span class="b1"></span><span class="b2"></span><span class="b3"></span></div>
          <header class="flex items-center gap-3 mt-2">
            <span class="badge-ghost">VRD</span>
            <h3 class="text-xl font-bold">Voirie & Réseaux</h3>
          </header>
          <p class="text-white/80 mt-2">Terrassement, assainissement, chaussées, ouvrages hydrauliques.</p>
          <ul class="mt-3 text-sm text-white/70 list-disc pl-5 space-y-1">
            <li>Chaussées souples/rigides & signalisation</li>
            <li>Pluvial, AEP, EU, fourreaux & éclairage</li>
            <li>Ouvrages d’art & bassins de rétention</li>
          </ul>
          <div class="mt-4 flex gap-2">
            <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Chiffrer un VRD</Link>
            <Link :href="r('public.projects', {}, false, '/realisations')" class="btn-ghost">Réalisations</Link>
          </div>
        </article>

        <!-- BAT -->
        <article class="card-iso ring-gold" v-show="tab==='bat'" @mousemove="handleTilt" @mouseleave="resetTilt" v-reveal>
          <div class="illus iso-bat" aria-hidden="true"><span class="core"></span><span class="slab"></span><span class="beam"></span></div>
          <header class="flex items-center gap-3 mt-2">
            <span class="badge-ghost">BAT</span>
            <h3 class="text-xl font-bold">Bâtiments</h3>
          </header>
          <p class="text-white/80 mt-2">Logements, tertiaire, industriel — structures béton & finitions.</p>
          <ul class="mt-3 text-sm text-white/70 list-disc pl-5 space-y-1">
            <li>Gros œuvre & second œuvre</li>
            <li>Études d’exé, BIM & DOE</li>
            <li>Contrôles qualité & essais</li>
          </ul>
          <div class="mt-4 flex gap-2">
            <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Chiffrer un bâtiment</Link>
            <Link :href="r('public.projects', {}, false, '/realisations')" class="btn-ghost">Réalisations</Link>
          </div>
        </article>

        <!-- GC -->
        <article class="card-iso ring-gold" v-show="tab==='gc'" @mousemove="handleTilt" @mouseleave="resetTilt" v-reveal>
          <div class="illus iso-gc" aria-hidden="true"><span class="dam"></span><span class="pipe"></span></div>
          <header class="flex items-center gap-3 mt-2">
            <span class="badge-ghost">GC</span>
            <h3 class="text-xl font-bold">Génie civil</h3>
          </header>
          <p class="text-white/80 mt-2">Hydraulique, soutènements, bassins, ouvrages spéciaux.</p>
          <ul class="mt-3 text-sm text-white/70 list-disc pl-5 space-y-1">
            <li>Études, notes de calcul & plans</li>
            <li>Phasage précis & logistique de chantier</li>
            <li>Réceptions, essais & DOE</li>
          </ul>
          <div class="mt-4 flex gap-2">
            <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Chiffrer un GC</Link>
            <Link :href="r('public.projects', {}, false, '/realisations')" class="btn-ghost">Réalisations</Link>
          </div>
        </article>
      </div>
    </section>

    <!-- CAPACITÉS / AVANTAGES -->
    <section class="py-8 md:py-12" v-reveal>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title text-center">Capacités clés</h2>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <div class="card ring-gold p-5">
            <div class="flex items-center gap-3">
              <span class="i i-check"></span><div class="font-bold">Méthodes & métrés</div>
            </div>
            <p class="text-white/75 mt-2 text-sm">Quantités fiabilisées, variantes optimisées et BPU clair.</p>
          </div>
          <div class="card ring-gold p-5">
            <div class="flex items-center gap-3">
              <span class="i i-plan"></span><div class="font-bold">Études & BIM</div>
            </div>
            <p class="text-white/75 mt-2 text-sm">Plans EXE, synthèse, coordination, DOE structuré.</p>
          </div>
          <div class="card ring-gold p-5">
            <div class="flex items-center gap-3">
              <span class="i i-helmet"></span><div class="font-bold">Qualité & Sécurité</div>
            </div>
            <p class="text-white/75 mt-2 text-sm">Traçabilité, contrôles réguliers, plan de prévention.</p>
          </div>
          <div class="card ring-gold p-5">
            <div class="flex items-center gap-3">
              <span class="i i-time"></span><div class="font-bold">Délais tenus</div>
            </div>
            <p class="text-white/75 mt-2 text-sm">Phasage réaliste, jalons et suivi visuel simple.</p>
          </div>
          <div class="card ring-gold p-5">
            <div class="flex items-center gap-3">
              <span class="i i-site"></span><div class="font-bold">Site occupé</div>
            </div>
            <p class="text-white/75 mt-2 text-sm">Logistique anticipée pour limiter l’impact opérationnel.</p>
          </div>
          <div class="card ring-gold p-5">
            <div class="flex items-center gap-3">
              <span class="i i-shield"></span><div class="font-bold">Conformité</div>
            </div>
            <p class="text-white/75 mt-2 text-sm">Alignement ISO 9001/45001 et exigences contractuelles.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- PROCESS -->
    <section class="py-8 md:py-12" v-reveal>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title text-center">Process projet</h2>

        <div class="mt-8 grid gap-4 lg:grid-cols-6">
          <div class="step card ring-gold p-5">
            <div class="step__n">01</div>
            <div class="font-bold">Analyse</div>
            <div class="text-white/70 text-sm mt-1">Lecture DCE, risques & variantes.</div>
          </div>
          <div class="step card ring-gold p-5">
            <div class="step__n">02</div>
            <div class="font-bold">Métrés</div>
            <div class="text-white/70 text-sm mt-1">Quantités & chiffrage fiable.</div>
          </div>
          <div class="step card ring-gold p-5">
            <div class="step__n">03</div>
            <div class="font-bold">Planning</div>
            <div class="text-white/70 text-sm mt-1">Phasage & jalons réalistes.</div>
          </div>
          <div class="step card ring-gold p-5">
            <div class="step__n">04</div>
            <div class="font-bold">Exécution</div>
            <div class="text-white/70 text-sm mt-1">Contrôle qualité & HSE.</div>
          </div>
          <div class="step card ring-gold p-5">
            <div class="step__n">05</div>
            <div class="font-bold">Réception</div>
            <div class="text-white/70 text-sm mt-1">Essais, PV & DOE.</div>
          </div>
          <div class="step card ring-gold p-5">
            <div class="step__n">06</div>
            <div class="font-bold">Suivi</div>
            <div class="text-white/70 text-sm mt-1">Levée de réserves & garantie.</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA 3D -->
    <section class="py-10 md:py-14" v-reveal>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card ring-gold p-6 md:p-8 grid lg:grid-cols-3 gap-6 items-center relative overflow-hidden">
          <div class="absolute -inset-6 opacity-30 blur-[70px] bg-[radial-gradient(60%_60%_at_20%_20%,#dcc176_0%,transparent_60%)]" aria-hidden="true"></div>
          <div class="lg:col-span-2 relative">
            <h3 class="text-2xl md:text-3xl font-extrabold">Vous avez un projet ?</h3>
            <p class="text-white/80 mt-2">Envoyez votre DCE ou demandez un chiffrage express. Réponse rapide et structurée.</p>
          </div>
          <div class="relative flex gap-3 justify-start lg:justify-end">
            <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Déposer un DCE</Link>
            <Link :href="r('public.contact', {}, false, '/contact')" class="btn-ghost">Contact</Link>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<style scoped>
/* Reveal */
.reveal{ opacity:0; transform: translateY(12px) scale(.985); filter: blur(2px); transition: all .6s cubic-bezier(.2,.8,.2,1) }
.reveal.is-visible{ opacity:1; transform:none; filter:none }

/* Tokens */
.card{ position:relative; border-radius:1.1rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); backdrop-filter:blur(10px) }
.ring-gold{ position:relative }
.ring-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; z-index:0; pointer-events:none; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.section-title{ position:relative; display:block; width:100%; font-weight:800; letter-spacing:-.01em; font-size:clamp(1.5rem,2vw + 1rem,2rem) }
.section-title::after{ content:""; display:block; height:2px; width:110px; margin:.7rem auto 0; background:linear-gradient(90deg,#dcc176,rgba(253,253,254,.6)); opacity:.85; border-radius:999px }
.tag-ghost{ display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .6rem; border-radius:.55rem; font-weight:800; font-size:.75rem; color:#fdfdfe; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.20); box-shadow: inset 0 1px 0 rgba(255,255,255,.25) }
.chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .7rem; border-radius:.7rem; color:#fdfdfe; font-weight:600; font-size:.9rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.14) }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.05rem; border-radius:1rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe; transition:border-color .2s, transform .2s }
.btn-ghost:hover{ border-color: rgba(220,193,118,.6); transform: translateY(-1px) }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.8rem 1.15rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent; transition: transform .2s }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.btn-outline-gold:hover{ transform: translateY(-2px) }

/* Hero 3D */
.hero-ribbon{ position:absolute; left:0; right:0; top:-10px; height:36px; opacity:.45; transform: rotate(-2deg); background: repeating-linear-gradient(135deg,#2a3644 0 18px,#2a3644 18px 28px,#dcc176 28px 46px,#dcc176 46px 56px) }
.hero-halo{ position:absolute; inset:-40px; border-radius:2rem; filter:blur(36px); opacity:.55; background: radial-gradient(60% 60% at 25% 25%, #dcc176 0%, transparent 60%) }
.persp{ perspective:1200px; position:relative }
.platform{ height:12rem; border-radius:1.2rem; background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.05)); border:1px solid rgba(255,255,255,.12); transform: rotateX(55deg) rotateZ(-12deg); box-shadow: 0 50px 120px -40px rgba(0,0,0,.6) }
.platform-cards{ position:absolute; inset:0; display:grid; place-items:center; pointer-events:none }
.platform-cards>div{ position:absolute }
.iso-card{ height:var(--h); width:var(--w); transform: translateZ(0) rotateX(55deg) rotateZ(-12deg); background: linear-gradient(160deg, rgba(255,255,255,.12), rgba(21,30,39,.55)); border: 1px solid rgba(255,255,255,.14); border-radius: 12px; box-shadow: 0 40px 90px -40px rgba(0,0,0,.55); z-index: var(--z) }
.iso-card::after{ content:""; position:absolute; inset:-1px; border-radius:12px; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.8),rgba(255,255,255,.2),rgba(220,193,118,.8)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; opacity:.75 }
.crane{ position:absolute; right:0; top:-16px; width:72%; filter: drop-shadow(0 6px 30px rgba(0,0,0,.35)) }
.trolley{ animation: trolley 6s ease-in-out infinite alternate } .hook{ animation: hook 3s ease-in-out infinite alternate }
@keyframes trolley{ from{ transform: translateX(40px) } to{ transform: translateX(160px) } }
@keyframes hook{ from{ transform: translateY(0) } to{ transform: translateY(8px) } }

/* Services 3D */
.card-iso{ transform:perspective(900px) rotateX(var(--rx,0deg)) rotateY(var(--ry,0deg)); transition: transform .15s ease-out, box-shadow .2s ease; box-shadow:0 40px 120px -40px rgba(0,0,0,.55); border-radius:1rem; padding:1.1rem 1.2rem 1.2rem; background:linear-gradient(160deg,rgba(255,255,255,.08),rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.12) }
.illus{ height:120px; position:relative; margin-top:.1rem }
.iso-vrd .b1,.iso-vrd .b2,.iso-vrd .b3{ position:absolute; border-radius:10px; transform: rotateX(55deg) rotateZ(-12deg); background:linear-gradient(160deg,rgba(255,255,255,.12),rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.14) }
.iso-vrd .b1{ width:120px; height:26px; left:6%; bottom:14% } .iso-vrd .b2{ width:100px; height:22px; left:34%; bottom:26% } .iso-vrd .b3{ width:78px; height:18px; left:60%; bottom:38% }
.iso-bat .core,.iso-bat .slab,.iso-bat .beam{ position:absolute; transform: rotateX(55deg) rotateZ(-12deg); border:1px solid rgba(255,255,255,.14); background:linear-gradient(160deg,rgba(255,255,255,.12),rgba(21,30,39,.55)); border-radius:8px }
.iso-bat .core{ width:50px; height:70px; left:18%; bottom:16% } .iso-bat .slab{ width:110px; height:12px; left:26%; bottom:28% } .iso-bat .beam{ width:60px; height:8px; left:52%; bottom:38% }
.iso-gc .dam,.iso-gc .pipe{ position:absolute; transform: rotateX(55deg) rotateZ(-12deg); background:linear-gradient(160deg,rgba(255,255,255,.12),rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.14); border-radius:10px }
.iso-gc .dam{ width:120px; height:28px; left:10%; bottom:18% } .iso-gc .pipe{ width:26px; height:26px; left:66%; bottom:34%; border-radius:999px }

/* Process */
.step{ position:relative; overflow:hidden }
.step::after{ content:""; position:absolute; inset:-1px; pointer-events:none; border-radius:inherit; background: radial-gradient(60% 60% at 20% 20%, rgba(220,193,118,.15), transparent 60%) }
.step__n{ font-weight:900; color:#dcc176; margin-bottom:.35rem }

/* Icônes (mask) */
.i{ width:18px; height:18px; display:inline-block; border-radius:5px; background: rgba(255,255,255,.12) }
.i-check{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-plan{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M3 3h13v18H3zM16 7h5v14H16zM6 6h7v2H6zM6 10h7v2H6zM6 14h7v2H6z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M3 3h13v18H3zM16 7h5v14H16zM6 6h7v2H6zM6 10h7v2H6zM6 14h7v2H6z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-helmet{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 3a9 9 0 00-9 9v3h18v-3a9 9 0 00-9-9zM3 18h18v2H3z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 3a9 9 0 00-9 9v3h18v-3a9 9 0 00-9-9zM3 18h18v2H3z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-time{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-site{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-shield{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2l8 4v6c0 5-3.5 9.74-8 10-4.5-.26-8-5-8-10V6l8-4z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2l8 4v6c0 5-3.5 9.74-8 10-4.5-.26-8-5-8-10V6l8-4z"/></svg>') center/contain no-repeat; background:#dcc176 }

/* Utils */
.text-bk-gold{ color:#dcc176 }
.text-bk-night{ color:#0f141a }
.bg-bk-gold{ background:#dcc176 }
</style>
