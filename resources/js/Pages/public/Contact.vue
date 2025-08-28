<!-- resources/js/Pages/Public/Contact.vue -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
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

/* Formulaire Contact */
const form = useForm({
  name: '', email: '', phone: '', subject: '', message: '', hp: ''
})

function submit(){
  form.post(r('forms.contact.store', {}, false, '/contact'), {
    preserveScroll: true,
    onSuccess: () => {
      const keepEmail = form.email
      form.reset('name','phone','subject','message','hp')
      form.email = keepEmail
    }
  })
}
</script>

<template>
  <PublicLayout>
    <!-- HERO -->
    <section class="relative overflow-hidden pt-14 md:pt-20 pb-10 md:pb-14">
      <div class="hero-ribbon" aria-hidden="true"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-6" v-reveal>
          <span class="tag-ghost">Contact</span>
          <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
            Entrons en <span class="text-bk-gold">contact</span>
          </h1>
          <p class="mt-4 text-white/80 max-w-2xl">
            Implantés à Tanger, nous répondons sous 24h ouvrées. Dites-nous votre besoin (VRD, bâtiments, GC),
            on vous propose un phasage réaliste et un chiffrage clair.
          </p>
          <div class="mt-6 flex flex-wrap gap-2">
            <span class="chip">Qualité</span><span class="chip">Sécurité</span><span class="chip">Délais</span><span class="chip">Transparence</span>
          </div>
        </div>

        <!-- Scène 3D / visuel -->
        <div class="lg:col-span-6 relative" v-reveal>
          <div class="hero-halo" aria-hidden="true"></div>
          <div class="persp">
            <div class="platform"></div>
            <div class="platform-cards">
              <div class="iso-card" style="left:6%;  bottom:10%; --h:130px; --w:170px; --z:18;"></div>
              <div class="iso-card" style="left:36%; bottom:18%; --h:105px; --w:140px; --z:24;"></div>
              <div class="iso-card" style="left:60%; bottom:32%; --h:82px;  --w:120px; --z:36;"></div>
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

    <!-- CARTES CONTACT + FORM -->
    <section class="py-8 md:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-6">
        <!-- Coordonnées -->
        <div class="lg:col-span-4">
          <div class="card ring-gold p-6 md:p-7" v-reveal>
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

          <!-- Carte / Map -->
          <div class="card ring-gold overflow-hidden mt-6" v-reveal>
            <div class="relative w-full h-[280px]">
              <iframe
                class="absolute inset-0 w-full h-full"
                title="Localisation BKOCONSTRUCTION"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps?q=P5H4%2B2J3%20Tanger%2C%20Maroc&output=embed">
              </iframe>
            </div>
          </div>
        </div>

        <!-- Formulaire -->
        <div class="lg:col-span-8">
          <form class="card ring-gold p-6 md:p-8 grid gap-4" @submit.prevent="submit" aria-describedby="contact-status" v-reveal>
            <h2 class="section-title text-left">Écrivez-nous</h2>

            <div v-if="Object.keys(form.errors).length" id="contact-status" aria-live="assertive"
                 class="rounded-lg px-3 py-2 text-sm text-red-300 bg-red-500/10 ring-1 ring-red-500/20">
              Merci de vérifier les champs saisis.
            </div>

            <div class="grid md:grid-cols-2 gap-3">
              <input v-model="form.name" type="text" placeholder="Nom complet" class="field" required />
              <input v-model="form.email" type="email" placeholder="Email (ex. contact@domaine.ma)" class="field" required />
            </div>
            <div class="grid md:grid-cols-2 gap-3">
              <input v-model="form.phone" type="tel" placeholder="Téléphone" class="field" />
              <input v-model="form.subject" type="text" placeholder="Objet (ex. Demande d’information)" class="field" />
            </div>
            <textarea v-model="form.message" rows="5" placeholder="Votre message" class="field" required></textarea>

            <input v-model="form.hp" type="text" class="sr-only" tabindex="-1" autocomplete="off" />

            <div class="flex items-center gap-3 pt-1">
              <button type="submit" class="btn-outline-gold" :disabled="form.processing">{{ form.processing ? 'Envoi…' : 'Envoyer' }}</button>
              <div v-if="form.progress" class="flex-1 h-1 rounded bg-white/10 overflow-hidden">
                <div class="h-1 bg-bk-gold transition-all" :style="{ width: `${form.progress.percentage}%` }"></div>
              </div>
            </div>
          </form>
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
.tag-ghost{ display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .6rem; border-radius:.55rem; font-weight:800; font-size:.75rem; color:#fdfdfe; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.20); box-shadow: inset 0 1px 0 rgba(255,255,255,.25) }
.section-title{ position:relative; display:block; width:100%; font-weight:800; letter-spacing:-.01em; font-size:clamp(1.5rem,2vw + 1rem,2rem) }
.section-title::after{ content:""; display:block; height:2px; width:110px; margin:.7rem 0 0; background:linear-gradient(90deg,#dcc176,rgba(253,253,254,.6)); opacity:.85; border-radius:999px }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.05rem; border-radius:1rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe; transition:border-color .2s, transform .2s }
.btn-ghost:hover{ border-color: rgba(220,193,118,.6); transform: translateY(-1px) }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.8rem 1.15rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent; transition: transform .2s }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.btn-outline-gold:hover{ transform: translateY(-2px) }
.chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .7rem; border-radius:.7rem; color:#fdfdfe; font-weight:600; font-size:.9rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.14) }
.field{ width:100%; border-radius:.75rem; padding:.75rem .9rem; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.14); color:#fdfdfe; outline:none; position:relative; z-index:1 }
.field:focus{ outline:2px solid rgba(220,193,118,.6); outline-offset:2px }

/* Icons */
.i{ width:18px; height:18px; display:inline-block; border-radius:5px; background: rgba(255,255,255,.12) }
.i-phone{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-mail{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M20 8l-8 5-8-5V6l8 5 8-5v2zM4 18h16v-2H4v2z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M20 8l-8 5-8-5V6l8 5 8-5v2zM4 18h16v-2H4v2z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-pin{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-clock{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; background:#dcc176 }

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

/* Utils */
.text-bk-gold{ color:#dcc176 }
</style>
