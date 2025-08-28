<!-- resources/js/Pages/Public/Services.vue -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { route as ziggyRoute } from 'ziggy-js'

/* Helper Ziggy + fallback */
function r(name: string, params?: any, absolute = false, fallback: string = '#') {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (!Z) return fallback
    return ziggyRoute(name, params ?? {}, absolute, Z)
  } catch { return fallback }
}

/* v-reveal léger */
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
</script>

<template>
  <PublicLayout>
    <!-- HERO (3D à GAUCHE) -->
    <section class="relative overflow-hidden pt-14 md:pt-20 pb-10 md:pb-14">
      <div class="hero-ribbon" aria-hidden="true"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <!-- 3D -->
        <div class="lg:col-span-6 order-2 lg:order-1 relative" v-reveal>
          <div class="hero-halo" aria-hidden="true"></div>
          <div class="services3d" aria-hidden="true">
            <div class="grid-floor"></div>
            <div class="compass">
              <span class="n">N</span><span class="e">E</span><span class="s">S</span><span class="w">W</span>
              <div class="needle"></div>
            </div>
            <div class="wave">
              <span style="--i:0"></span><span style="--i:1"></span><span style="--i:2"></span>
              <span style="--i:3"></span><span style="--i:4"></span><span style="--i:5"></span>
              <span style="--i:6"></span><span style="--i:7"></span><span style="--i:8"></span>
            </div>
            <div class="scan"></div>
          </div>
        </div>

        <!-- Texte -->
        <div class="lg:col-span-6 order-1 lg:order-2" v-reveal>
          <span class="tag-ghost">Expertises — VRD, Bâtiments, Génie civil</span>
          <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
            Nos <span class="text-bk-gold">services</span> pour des chantiers impeccables
          </h1>
          <p class="mt-4 text-white/80 max-w-2xl">
            De la conception à la réception, BKOCONSTRUCTION délivre des ouvrages performants :
            méthodes robustes, traçabilité, qualité mesurée et sécurité prioritaire.
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
      </div>

      <!-- ancres rapides -->
      <div class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" v-reveal>
        <nav class="inline-flex rounded-xl overflow-hidden border border-white/10 bg-white/5 backdrop-blur">
          <a href="#vrd" class="px-4 py-2 text-sm font-semibold text-white/85 hover:text-white">Voirie & Réseaux</a>
          <span class="border-l border-white/10"></span>
          <a href="#bat" class="px-4 py-2 text-sm font-semibold text-white/85 hover:text-white">Bâtiments</a>
          <span class="border-l border-white/10"></span>
          <a href="#gc"  class="px-4 py-2 text-sm font-semibold text-white/85 hover:text-white">Génie civil</a>
        </nav>
      </div>
    </section>

    <!-- VRD (3D à DROITE) -->
    <section id="vrd" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-6" v-reveal>
          <h2 class="section-title text-left">Voirie & Réseaux (VRD)</h2>
          <div class="mt-4 card ring-gold p-6 md:p-7">
            <p class="text-white/80">
              Terrassements, assainissement, chaussées et réseaux secs/humides, avec phasage maîtrisé pour limiter les impacts d’exploitation.
            </p>
            <ul class="mt-4 grid sm:grid-cols-2 gap-2 text-white/80 text-sm">
              <li class="li-check">Chaussées souples/rigides & signalisation</li>
              <li class="li-check">Pluvial, AEP, EU, éclairage public</li>
              <li class="li-check">Fourreaux, chambres & tirage</li>
              <li class="li-check">Ouvrages hydrauliques & bassins</li>
            </ul>
            <div class="mt-5 flex flex-wrap gap-2">
              <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Chiffrer un VRD</Link>
              <Link :href="r('public.projects', {}, false, '/realisations')" class="btn-ghost">Réalisations</Link>
            </div>
          </div>
        </div>
        <div class="lg:col-span-6 relative" v-reveal>
          <div class="split3d vrd">
            <div class="floor"></div>
            <div class="road a"></div>
            <div class="road b"></div>
            <div class="manhole"></div>
            <div class="pipe"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- BAT (3D à GAUCHE) -->
    <section id="bat" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-6 order-2 lg:order-1 relative" v-reveal>
          <div class="split3d bat">
            <div class="slab"></div>
            <div class="core"></div>
            <div class="beam"></div>
            <div class="frame"></div>
          </div>
        </div>
        <div class="lg:col-span-6 order-1 lg:order-2" v-reveal>
          <h2 class="section-title text-left">Bâtiments</h2>
          <div class="mt-4 card ring-gold p-6 md:p-7">
            <p class="text-white/80">
              Logements, tertiaire, industriel : structures béton, second œuvre soigné et coordination technique (BIM/EXE) pour des finitions nettes.
            </p>
            <ul class="mt-4 grid sm:grid-cols-2 gap-2 text-white/80 text-sm">
              <li class="li-check">Gros œuvre & second œuvre</li>
              <li class="li-check">Études d’exécution, synthèse & BIM</li>
              <li class="li-check">Contrôles qualité & essais</li>
              <li class="li-check">DOE structuré & garantie</li>
            </ul>
            <div class="mt-5 flex flex-wrap gap-2">
              <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Chiffrer un bâtiment</Link>
              <Link :href="r('public.projects', {}, false, '/realisations')" class="btn-ghost">Réalisations</Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- GC (3D à DROITE) -->
    <section id="gc" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-6" v-reveal>
          <h2 class="section-title text-left">Génie civil</h2>
          <div class="mt-4 card ring-gold p-6 md:p-7">
            <p class="text-white/80">
              Infrastructures hydrauliques, soutènements, bassins et ouvrages spéciaux, avec notes de calcul et suivi d’essais.
            </p>
            <ul class="mt-4 grid sm:grid-cols-2 gap-2 text-white/80 text-sm">
              <li class="li-check">Études, notes de calcul & plans</li>
              <li class="li-check">Phasage précis & logistique de chantier</li>
              <li class="li-check">Réceptions, essais & PV</li>
              <li class="li-check">Maintenance & durabilité</li>
            </ul>
            <div class="mt-5 flex flex-wrap gap-2">
              <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Chiffrer un GC</Link>
              <Link :href="r('public.projects', {}, false, '/realisations')" class="btn-ghost">Réalisations</Link>
            </div>
          </div>
        </div>
        <div class="lg:col-span-6 relative" v-reveal>
          <div class="split3d gc">
            <div class="dam"></div>
            <div class="spill"></div>
            <div class="tube"></div>
            <div class="gauge"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
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
/* ========= Reveal */
.reveal{ opacity:0; transform: translateY(12px) scale(.985); filter: blur(2px); transition: all .6s cubic-bezier(.2,.8,.2,1) }
.reveal.is-visible{ opacity:1; transform:none; filter:none }

/* ========= Tokens (cohérents) */
.card{ position:relative; border-radius:1.1rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); backdrop-filter:blur(10px) }
.ring-gold{ position:relative }
.ring-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; z-index:0; pointer-events:none; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.section-title{ position:relative; display:block; width:100%; font-weight:800; letter-spacing:-.01em; font-size:clamp(1.5rem,2vw + 1rem,2rem) }
.section-title::after{ content:""; display:block; height:2px; width:110px; margin:.7rem 0 0; background:linear-gradient(90deg,#dcc176,rgba(253,253,254,.6)); opacity:.85; border-radius:999px }
.tag-ghost{ display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .6rem; border-radius:.55rem; font-weight:800; font-size:.75rem; color:#fdfdfe; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.20); box-shadow: inset 0 1px 0 rgba(255,255,255,.25) }
.chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .7rem; border-radius:.7rem; color:#fdfdfe; font-weight:600; font-size:.9rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.14) }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.05rem; border-radius:1rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe; transition:border-color .2s, transform .2s }
.btn-ghost:hover{ border-color: rgba(220,193,118,.6); transform: translateY(-1px) }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.8rem 1.15rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent; transition: transform .2s }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.btn-outline-gold:hover{ transform: translateY(-2px) }

/* ========= HERO 3D (grid-wave + compas) */
.hero-ribbon{ position:absolute; left:0; right:0; top:-10px; height:36px; opacity:.45; transform: rotate(-2deg); background: repeating-linear-gradient(135deg,#2a3644 0 18px,#2a3644 18px 28px,#dcc176 28px 46px,#dcc176 46px 56px) }
.hero-halo{ position:absolute; inset:-40px; border-radius:2rem; filter:blur(36px); opacity:.55; background: radial-gradient(60% 60% at 25% 25%, #dcc176 0%, transparent 60%) }

.services3d{
  position:relative; height:min(28rem, 60vw);
  display:grid; place-items:center; perspective:1000px; transform-style:preserve-3d;
  filter: drop-shadow(0 20px 60px rgba(0,0,0,.45));
}
.services3d .grid-floor{
  position:absolute; inset:auto; bottom:-8px; width:94%; height:12rem; border-radius:1.2rem;
  transform: rotateX(62deg) rotateZ(8deg);
  background:
    repeating-linear-gradient(0deg, rgba(255,255,255,.09) 0 2px, transparent 2px 12px),
    repeating-linear-gradient(90deg, rgba(255,255,255,.09) 0 2px, transparent 2px 12px),
    linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.04));
  border:1px solid rgba(255,255,255,.12);
}
.services3d .compass{
  position:absolute; width:min(22rem, 64%); aspect-ratio:1/1; border-radius:999px;
  transform: translateZ(80px) rotateX(14deg);
  border:1px solid rgba(255,255,255,.18);
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
}
.compass .needle{
  position:absolute; inset:20% 49% 20% 49%; border-radius:8px; background:#dcc176;
  transform-origin:center; animation: needle 6s linear infinite;
  box-shadow: 0 0 16px rgba(220,193,118,.55);
}
.compass .n,.compass .e,.compass .s,.compass .w{
  position:absolute; color:#fdfdfe; font-weight:900; font-size:.75rem; opacity:.8;
}
.compass .n{ top:6%; left:50%; transform:translateX(-50%) }
.compass .s{ bottom:6%; left:50%; transform:translateX(-50%) }
.compass .e{ right:6%; top:50%; transform:translateY(-50%) }
.compass .w{ left:6%;  top:50%; transform:translateY(-50%) }

.services3d .wave{
  position:absolute; inset:0; transform: translateZ(110px) rotateX(14deg);
  display:grid; place-items:center; pointer-events:none;
}
.services3d .wave span{
  --i:0; position:absolute; width:70%; height:70%; border-radius:999px;
  border:1px solid rgba(255,255,255,.16);
  animation: ring 5s ease-in-out infinite;
  animation-delay: calc(var(--i) * .35s);
}
.services3d .scan{
  position:absolute; inset:10%; border-radius:999px;
  background: conic-gradient(from 0deg, rgba(220,193,118,.0) 0 85%, rgba(220,193,118,.6) 92%, rgba(220,193,118,.0) 100%);
  transform: translateZ(130px) rotateX(14deg);
  animation: sweep 6s cubic-bezier(.2,.8,.2,1) infinite;
  mix-blend-mode: screen;
}

/* ========= SPLIT 3D (alternance) */
.split3d{ position:relative; height:min(22rem, 55vw); perspective:900px; transform-style:preserve-3d }
.split3d .floor{
  position:absolute; inset:auto; bottom:-10px; width:92%; height:10rem; border-radius:1rem;
  transform: rotateX(60deg) rotateZ(-10deg);
  background:
    repeating-linear-gradient(0deg, rgba(255,255,255,.08) 0 2px, transparent 2px 12px),
    repeating-linear-gradient(90deg, rgba(255,255,255,.08) 0 2px, transparent 2px 12px),
    linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.04));
  border:1px solid rgba(255,255,255,.12);
}

/* VRD scene */
.split3d.vrd .road{
  position:absolute; border-radius:10px; transform: rotateX(58deg) rotateZ(-12deg);
  background:linear-gradient(160deg, rgba(255,255,255,.12), rgba(21,30,39,.55));
  border:1px solid rgba(255,255,255,.14); box-shadow: 0 40px 90px -40px rgba(0,0,0,.55);
}
.split3d.vrd .road.a{ width:220px; height:36px; left:6%;  bottom:14% }
.split3d.vrd .road.b{ width:180px; height:28px; left:44%; bottom:28% }
.split3d.vrd .manhole{
  position:absolute; width:28px; height:28px; left:34%; bottom:34%; border-radius:999px;
  transform: rotateX(58deg) rotateZ(-12deg); background:rgba(255,255,255,.12);
  border:1px solid rgba(255,255,255,.18);
}
.split3d.vrd .pipe{
  position:absolute; width:18px; height:18px; left:66%; bottom:40%; border-radius:999px;
  transform: rotateX(58deg) rotateZ(-12deg); background:#dcc176;
  box-shadow:0 0 0 3px rgba(220,193,118,.25), 0 0 18px rgba(220,193,118,.55);
}

/* BAT scene */
.split3d.bat .slab,.split3d.bat .core,.split3d.bat .beam,.split3d.bat .frame{
  position:absolute; transform: rotateX(58deg) rotateZ(10deg);
  background:linear-gradient(160deg, rgba(255,255,255,.12), rgba(21,30,39,.55));
  border:1px solid rgba(255,255,255,.14); border-radius:10px;
}
.split3d.bat .slab{ width:250px; height:16px; left:16%; bottom:18% }
.split3d.bat .core{ width:68px;  height:120px; left:22%; bottom:28% }
.split3d.bat .beam{ width:150px; height:10px; left:40%; bottom:42% }
.split3d.bat .frame{
  width:90px; height:90px; left:56%; bottom:32%; border-radius:12px;
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
}

/* GC scene */
.split3d.gc .dam,.split3d.gc .spill,.split3d.gc .tube,.split3d.gc .gauge{
  position:absolute; transform: rotateX(58deg) rotateZ(-10deg);
  background:linear-gradient(160deg, rgba(255,255,255,.12), rgba(21,30,39,.55));
  border:1px solid rgba(255,255,255,.14); border-radius:12px;
}
.split3d.gc .dam{ width:240px; height:34px; left:10%; bottom:20% }
.split3d.gc .spill{ width:120px; height:22px; left:46%; bottom:32%; border-radius:10px }
.split3d.gc .tube{ width:30px; height:30px; left:74%; bottom:40%; border-radius:999px; background:#dcc176; box-shadow:0 0 0 3px rgba(220,193,118,.25), 0 0 18px rgba(220,193,118,.55) }
.split3d.gc .gauge{
  width:70px; height:70px; left:60%; bottom:22%; border-radius:999px;
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
}

/* ========= Lists */
.li-check{ position:relative; padding-left:1.2rem }
.li-check::before{
  content:""; position:absolute; left:0; top:.25rem; width:.85rem; height:.85rem; border-radius:.3rem;
  background:#dcc176; -webkit-mask: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>') center/contain no-repeat; mask: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>') center/contain no-repeat;
}

/* ========= Animations */
@keyframes needle { to{ transform: rotate(360deg) } }
@keyframes ring   { 0%,100%{ transform: scale(1) } 50%{ transform: scale(1.06) } }
@keyframes sweep  { to{ transform: translateZ(130px) rotateX(14deg) rotateZ(360deg) } }

/* ========= Utils */
.text-bk-gold{ color:#dcc176 }
</style>
