<!-- resources/js/Pages/Public/Contact.vue -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { route as ziggyRoute } from 'ziggy-js'

/* Helper route robuste (Ziggy + fallback) */
function r(name: string, params?: any, absolute = false, fallback: string = '#') {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (!Z) return fallback
    return ziggyRoute(name, params ?? {}, absolute, Z)
  } catch { return fallback }
}

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
</script>

<template>
  <PublicLayout>
    <!-- HERO (3D à gauche, texte à droite) -->
    <section class="relative overflow-hidden pt-14 md:pt-20 pb-10 md:pb-14">
      <div class="hero-ribbon" aria-hidden="true"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <!-- 3D -->
        <div class="lg:col-span-6 relative order-first" v-reveal>
          <div class="hero-halo" aria-hidden="true"></div>
          <div class="contact3d" aria-hidden="true">
            <!-- sol -->
            <div class="grid-floor"></div>

            <!-- balise / beacon -->
            <div class="beacon">
              <div class="stem"></div>
              <div class="head"></div>
              <div class="ring r1"></div>
              <div class="ring r2"></div>
              <div class="ring r3"></div>
              <div class="ping"></div>
            </div>

            <!-- enveloppe flottante -->
            <div class="mail">
              <div class="env"></div>
              <div class="flap"></div>
              <div class="shadow"></div>
            </div>

            <!-- bulles icônes -->
            <div class="badge b1"><span class="i i-phone"></span></div>
            <div class="badge b2"><span class="i i-mail"></span></div>
            <div class="badge b3"><span class="i i-pin"></span></div>
          </div>
        </div>

        <!-- Texte -->
        <div class="lg:col-span-6" v-reveal>
          <span class="tag-ghost">Contact</span>
          <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
            Entrons en <span class="text-bk-gold">contact</span>
          </h1>
          <p class="mt-4 text-white/80 max-w-2xl">
            Basés à Tanger, nous répondons sous 24h ouvrées. Dites-nous votre besoin (VRD, bâtiments, génie civil) — on vous propose un phasage réaliste et un chiffrage clair.
          </p>

          <div class="mt-6 flex flex-wrap gap-2">
            <span class="chip">Qualité</span><span class="chip">Sécurité</span><span class="chip">Délais</span><span class="chip">Transparence</span>
          </div>

          <div class="mt-7 flex flex-col sm:flex-row gap-3">
            <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Appels d’offres / devis</Link>
            <a href="https://wa.me/212770556021" target="_blank" rel="noopener" class="btn-ghost">WhatsApp</a>
          </div>
        </div>
      </div>
    </section>

    <!-- CARTES PRATIQUES -->
    <section class="py-8 md:py-12" v-reveal>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-6 lg:grid-cols-12">
        <!-- Coordonnées -->
        <div class="lg:col-span-4">
          <div class="card ring-gold p-6 md:p-7">
            <h2 class="section-title text-left">Coordonnées</h2>
            <ul class="mt-4 space-y-3 text-white/85">
              <li class="flex items-center gap-3"><span class="i i-phone"></span><a href="tel:+212770556021" class="hover:text-bk-gold font-semibold">+212 7 70 55 60 21</a></li>
              <li class="flex items-center gap-3"><span class="i i-mail"></span><a href="mailto:contact@bkoconstruction.com" class="hover:text-bk-gold">contact@bkoconstruction.com</a></li>
              <li class="flex items-center gap-3"><span class="i i-pin"></span><span>P5H4+2J3 Tanger, Maroc</span></li>
              <li class="flex items-center gap-3"><span class="i i-clock"></span><span>Lun–Sam : 8h30 – 18h</span></li>
            </ul>
            <div class="grid grid-cols-3 gap-2 pt-4">
              <a href="tel:+212770556021" class="btn-outline-gold sm w-full justify-center">Appeler</a>
              <a href="https://wa.me/212770556021" target="_blank" rel="noopener" class="btn-ghost sm w-full justify-center">WhatsApp</a>
              <a href="mailto:contact@bkoconstruction.com" class="btn-ghost sm w-full justify-center">Email</a>
            </div>
          </div>
        </div>

        <!-- Atouts + Zones -->
        <div class="lg:col-span-8 grid gap-6 md:grid-cols-2">
          <div class="card ring-gold p-6 md:p-7">
            <h3 class="text-xl font-extrabold">Pourquoi BKOCONSTRUCTION ?</h3>
            <div class="mini-bullets mt-3">
              <span>✔️ Métrés & variantes optimisées</span>
              <span>✔️ Délais & phasage détaillés</span>
              <span>✔️ Proposition claire & traçable</span>
              <span>✔️ HSE & qualité au cœur</span>
            </div>
            <div class="mt-5">
              <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Demander un devis</Link>
            </div>
          </div>

          <div class="card ring-gold p-6 md:p-7">
            <h3 class="text-xl font-extrabold">Zones d’intervention</h3>
            <p class="text-white/80 mt-2">Nord & national : Tanger, Tétouan, Kénitra, Rabat, Casablanca… Interventions possibles partout au Maroc.</p>
            <div class="mt-4 flex flex-wrap gap-2">
              <span class="badge-ghost">VRD</span>
              <span class="badge-ghost">Bâtiments</span>
              <span class="badge-ghost">Génie civil</span>
              <span class="badge-ghost">Site occupé</span>
              <span class="badge-ghost">HSE</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- GOOGLE MAPS (embed “vrai”) -->
    <section class="py-8 md:py-12" v-reveal>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title">Nous trouver</h2>
        <div class="mt-6 card ring-gold overflow-hidden relative">
          <div class="map-wrap">
            <iframe
              class="absolute inset-0 w-full h-full"
              title="Localisation BKOCONSTRUCTION"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              src="https://www.google.com/maps?q=P5H4%2B2J3%20Tanger%2C%20Maroc&output=embed">
            </iframe>
          </div>
          <div class="p-4 flex items-center justify-between gap-3">
            <div class="text-white/80 text-sm">P5H4+2J3 Tanger, Maroc — Accès facile, stationnement à proximité.</div>
            <a
              class="btn-ghost"
              target="_blank"
              rel="noopener"
              href="https://www.google.com/maps/search/?api=1&query=P5H4%2B2J3%20Tanger%2C%20Maroc"
            >Ouvrir dans Google Maps</a>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA BAS DE PAGE -->
    <section class="py-10 md:py-14" v-reveal>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="footer-card gradient-ring p-6 md:p-8 rounded-2xl relative overflow-hidden">
          <div class="glow g1"></div><div class="glow g2"></div><div class="glow g3"></div>
          <div class="grid md:grid-cols-3 gap-5 items-center">
            <div class="md:col-span-2">
              <h3 class="text-2xl md:text-3xl font-extrabold">Besoin d’un chiffrage clair ?</h3>
              <p class="text-white/80 mt-2">Envoyez votre DCE ou demandez un devis express. Réponse sous 24h ouvrées.</p>
            </div>
            <div class="flex gap-3 md:justify-end">
              <Link :href="r('public.rfp', {}, false, '/appels-d-offres')" class="btn-outline-gold">Appels d’offres / Devis</Link>
              <a href="https://wa.me/212770556021" target="_blank" rel="noopener" class="btn-ghost">WhatsApp</a>
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

/* ===== Tokens (cohérents) */
.card{ position:relative; border-radius:1.1rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); backdrop-filter:blur(10px) }
.ring-gold{ position:relative }
.ring-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; z-index:0; pointer-events:none; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.tag-ghost{ display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .6rem; border-radius:.55rem; font-weight:800; font-size:.75rem; color:#fdfdfe; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.20); box-shadow: inset 0 1px 0 rgba(255,255,255,.25) }
.section-title{ position:relative; display:block; width:100%; font-weight:800; letter-spacing:-.01em; font-size:clamp(1.5rem,2vw + 1rem,2rem) }
.section-title::after{ content:""; display:block; height:2px; width:110px; margin:.7rem 0 0; background:linear-gradient(90deg,#dcc176,rgba(253,253,254,.6)); opacity:.85; border-radius:999px }
.chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .7rem; border-radius:.7rem; color:#fdfdfe; font-weight:600; font-size:.9rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.14) }
.badge-ghost{ display:inline-flex; align-items:center; justify-content:center; border-radius:.4rem; padding:.25rem .4rem; font-size:.7rem; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:#fdfdfe }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.05rem; border-radius:1rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe; transition:border-color .2s, transform .2s }
.btn-ghost:hover{ border-color: rgba(220,193,118,.6); transform: translateY(-1px) }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.8rem 1.15rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent; transition: transform .2s }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.btn-outline-gold:hover{ transform: translateY(-2px) }

/* ===== Icons (mask) */
.i{ width:18px; height:18px; display:inline-block; border-radius:5px; background: rgba(255,255,255,.12) }
.i-phone{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-mail{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M20 8l-8 5-8-5V6l8 5 8-5v2zM4 18h16v-2H4v2z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M20 8l-8 5-8-5V6l8 5 8-5v2zM4 18h16v-2H4v2z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-pin{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-clock{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; background:#dcc176 }

/* ===== HERO 3D — “Beacon + Mail” */
.hero-ribbon{ position:absolute; left:0; right:0; top:-10px; height:36px; opacity:.45; transform: rotate(-2deg); background: repeating-linear-gradient(135deg,#2a3644 0 18px,#2a3644 18px 28px,#dcc176 28px 46px,#dcc176 46px 56px) }
.hero-halo{ position:absolute; inset:-40px; border-radius:2rem; filter:blur(36px); opacity:.55; background: radial-gradient(60% 60% at 25% 25%, #dcc176 0%, transparent 60%) }

.contact3d{ position:relative; height:min(30rem,64vw); display:grid; place-items:center; perspective:1100px; transform-style:preserve-3d; filter: drop-shadow(0 24px 70px rgba(0,0,0,.45)) }
.contact3d .grid-floor{
  position:absolute; inset:auto; bottom:-8px; width:96%; height:12rem; border-radius:1.2rem;
  transform: rotateX(62deg) rotateZ(-8deg);
  background:
    repeating-linear-gradient(0deg, rgba(255,255,255,.09) 0 2px, transparent 2px 12px),
    repeating-linear-gradient(90deg, rgba(255,255,255,.09) 0 2px, transparent 2px 12px),
    linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.04));
  border:1px solid rgba(255,255,255,.12);
}

/* Beacon */
.beacon{ position:relative; transform: translateZ(90px) rotateX(12deg) }
.beacon .stem{
  width:10px; height:110px; margin:0 auto; border-radius:8px;
  background: linear-gradient(180deg, rgba(255,255,255,.2), rgba(255,255,255,.05));
  border:1px solid rgba(255,255,255,.14);
}
.beacon .head{
  width:52px; height:52px; margin:-8px auto 0; border-radius:999px;
  background: radial-gradient(60% 60% at 50% 45%, rgba(255,255,255,.2), rgba(21,30,39,.6));
  border:1px solid rgba(255,255,255,.18);
  box-shadow: 0 20px 60px -20px rgba(0,0,0,.6), inset 0 0 0 1px rgba(255,255,255,.06);
}
.beacon .ring{ position:absolute; left:50%; top:34%; transform: translate(-50%,-50%); border-radius:999px; border:1px solid rgba(255,255,255,.16) }
.beacon .r1{ width:120px; height:120px; animation: ringPulse 4.5s ease-in-out infinite }
.beacon .r2{ width:180px; height:180px; animation: ringPulse 5s ease-in-out infinite .2s }
.beacon .r3{ width:240px; height:240px; animation: ringPulse 5.5s ease-in-out infinite .4s }
.beacon .ping{
  position:absolute; left:50%; top:34%; width:14px; height:14px; transform: translate(-50%,-50%);
  background:#dcc176; border-radius:999px; filter: drop-shadow(0 0 10px rgba(220,193,118,.6));
  animation: ping 1.8s ease-in-out infinite;
}

/* Envelope */
.mail{ position:absolute; right:6%; top:22%; transform: translateZ(120px) rotateX(10deg) rotateZ(-8deg) }
.mail .env{
  width:140px; height:90px; border-radius:10px; background:linear-gradient(160deg, rgba(255,255,255,.16), rgba(21,30,39,.55));
  border:1px solid rgba(255,255,255,.14); position:relative; z-index:2;
}
.mail .flap{
  position:absolute; width:0; height:0; left:0; top:0; z-index:3;
  border-left:70px solid transparent; border-right:70px solid transparent; border-top:48px solid rgba(255,255,255,.22);
  transform-origin: top; animation: flap 4s ease-in-out infinite;
}
.mail .shadow{
  position:absolute; inset:auto; top:100%; left:10px; width:120px; height:12px; filter: blur(8px); opacity:.5;
  background: radial-gradient(60% 60% at 50% 50%, rgba(0,0,0,.4), transparent 60%);
}

/* Badges tourbillonnants */
.badge{ position:absolute; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); display:grid; place-items:center }
.badge.b1{ left:8%; top:22%; transform: translateZ(110px) rotateX(12deg); animation: float 4s ease-in-out infinite }
.badge.b2{ right:16%; bottom:18%; transform: translateZ(110px) rotateX(12deg); animation: float 4.6s ease-in-out infinite .2s }
.badge.b3{ left:22%; bottom:12%; transform: translateZ(110px) rotateX(12deg); animation: float 5s ease-in-out infinite .4s }

/* Animations 3D */
@keyframes ringPulse { 0%,100%{ transform: translate(-50%,-50%) scale(1) } 50%{ transform: translate(-50%,-50%) scale(1.04) } }
@keyframes ping { 0%,100%{ transform: translate(-50%,-50%) scale(1) } 50%{ transform: translate(-50%,-50%) scale(1.2) } }
@keyframes flap { 0%,100%{ transform: rotateX(0deg) } 50%{ transform: rotateX(-22deg) } }
@keyframes float { 0%,100%{ transform: translateZ(110px) translateY(0) rotateX(12deg) } 50%{ transform: translateZ(110px) translateY(-8px) rotateX(12deg) } }

/* ===== Map */
.map-wrap{ position:relative; width:100%; height:420px }
@media (max-width: 640px){ .map-wrap{ height:300px } }

/* ===== Footer-like CTA halo (déjà utilisé) */
.footer-card{ position:relative; border-radius:1.25rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12) }
.gradient-ring{ position:relative }
.gradient-ring::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; z-index:0; pointer-events:none; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.glow{ position:absolute; inset:auto; filter:blur(70px); opacity:.25; pointer-events:none }
.glow.g1{ left:5%; bottom:-20%; width:40%; height:120%; background: radial-gradient(50% 50% at 30% 40%, #dcc176 0%, transparent 60%) }
.glow.g2{ right:0; top:-20%; width:30%; height:110%; background: radial-gradient(50% 50% at 70% 30%, #ffffff 0%, transparent 60%) }
.glow.g3{ left:25%; top:10%; width:20%; height:60%; background: radial-gradient(50% 50% at 50% 50%, #dcc176 0%, transparent 60%) }

/* Utils */
.text-bk-gold{ color:#dcc176 }
</style>
