<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

/* Helper route robuste (Ziggy + fallback) */
function r(name: string, params?: any, absolute = false, fallback: string = '#') {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (!Z) return fallback
    return ziggyRoute(name, params ?? {}, absolute, Z)
  } catch { return fallback }
}

/* Types & props */
type MediaItem = { type:'image'|'video', kind?:'upload'|'url', path?:string|null, url?:string|null }
type Project   = { id:number; title:string; slug?:string|null; city?:string|null; category?:string|null; status:'brouillon'|'publié'; cover_image?:string|null; media?:MediaItem[]; client?:string|null; company?:string|null }
type Post      = { id:number; title:string; slug?:string|null; excerpt?:string|null; cover_image?:string|null; published_at?:string|null }
type FaqItem   = { question:string; answer:string }

const props = defineProps<{ heroProjects?:Project[]|null; projects?:Project[]|null; posts?:Post[]|null; faqs?:FaqItem[]|null }>()

const heroList = computed<Project[]>(() => props.heroProjects ?? [])
const gridList = computed<Project[]>(() => props.projects ?? [])
const postList = computed<Post[]>(() => props.posts ?? [])

/* Partenaires */
const partnerList = computed(() => {
  const base = ['Avanza','Biocarb Bamako']
  const baseLower = base.map(s=>s.toLowerCase())
  const seen = new Set<string>()
  ;[...heroList.value,...gridList.value].forEach(p=>{
    const n = (p?.client || p?.company || '').toString().trim()
    if (n && !baseLower.includes(n.toLowerCase())) seen.add(n)
  })
  return [...base, ...Array.from(seen)]
})

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

/* v-countup */
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

/* Tilt 3D (services) */
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

/* Devis express */
const ALLOWED = [
  'application/pdf','application/x-zip-compressed','application/zip',
  'application/x-rar-compressed','application/x-7z-compressed',
  'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'application/octet-stream'
]
const EXT_ALLOWED = ['.pdf','.zip','.rar','.7z','.doc','.docx','.xls','.xlsx','.dwg','.dxf']
const MAX_FILE_MB=20, MAX_FILES=10

const qForm = useForm({ name:'',company:'',email:'',phone:'', city:'', project_type:'', budget:'', desired_date:'', message:'', files:[] as File[], hp:'' })
const filesClientError = ref(''); const successMsg = ref('')

const hasAllowedExt = (n:string)=>EXT_ALLOWED.some(ext=>n.toLowerCase().endsWith(ext))
function formatSize(bytes?:number|null){ if(bytes==null) return ''; const u=['o','Ko','Mo','Go'];let i=0,v=bytes; while(v>=1024&&i<u.length-1){v/=1024;i++} return `${v.toFixed(v>=10||i===0?0:1)} ${u[i]}` }

function pickFiles(e:Event){
  try{
    const input=e.target as HTMLInputElement; if(!input.files) return
    const picked=Array.from(input.files); const out:[File,...File[]]|File[]=[...qForm.files]; let error=''
    for(const f of picked){
      if(out.length>=MAX_FILES){ error=`Maximum ${MAX_FILES} fichiers.`; break }
      const okType = ALLOWED.includes(f.type)||hasAllowedExt(f.name)
      const okSize = f.size <= MAX_FILE_MB*1024*1024
      if(!okType){ error=`Format non autorisé: ${f.name}`; break }
      if(!okSize){ error=`Taille > ${MAX_FILE_MB} Mo: ${f.name}`; break }
      out.push(f)
    }
    if(error) filesClientError.value=error; else { filesClientError.value=''; qForm.files=out }
    input.value=''
  }catch{ filesClientError.value='Impossible de lire le fichier. Réessaie.'}
}
const removeFile=(i:number)=>qForm.files.splice(i,1)

function submitQuote(){
  successMsg.value=''; filesClientError.value=''
  qForm.post(r('forms.quote.store', {}, false, '/devis'), {
    forceFormData:true, preserveScroll:true,
    onSuccess:()=>{
      successMsg.value='Merci, votre demande a été envoyée. Nous revenons vers vous très vite.'
      const keepEmail=qForm.email
      qForm.reset('name','company','phone','city','project_type','budget','desired_date','message','files','hp')
      qForm.email=keepEmail
    }
  })
}
function focusDevis(){
  document.getElementById('devis')?.scrollIntoView({behavior:'smooth',block:'start'})
  setTimeout(()=> (document.getElementById('devis-name') as HTMLInputElement|undefined)?.focus(),240)
}

/* FAQ (mise à jour) */
const faqList = computed<FaqItem[]>(() => props.faqs && props.faqs.length ? props.faqs : [
  { question:'Que fait BKO Construction ?', answer:'Études, VRD/voirie, bâtiments, génie civil, ouvrages hydrauliques et immobilier. De la conception au DOE, avec contrôle qualité continu.' },
  { question:'Où intervenez-vous ?', answer:'Basés à Tanger, nous intervenons dans tout le Maroc et au-delà selon l’envergure du projet.' },
  { question:'Comment obtenir un chiffrage fiable ?', answer:'Utilisez “Devis express” (joignez votre DCE). Réponse sous 24h ouvrées avec variantes et phasage réaliste.' },
  { question:'Quelles garanties qualité/sécurité ?', answer:'Process alignés sur ISO 9001/45001, plan de prévention, EPI, contrôles réguliers et traçabilité.' },
  { question:'Travaillez-vous en site occupé ?', answer:'Oui. Phasage et logistique anticipés pour limiter l’impact sur l’activité, y compris en milieux sensibles.' },
])

/* util */
function fmt(s?:string){ if(!s) return ''; const d=new Date(s); return isNaN(+d)?(s||''):d.toLocaleDateString('fr-FR',{day:'2-digit',month:'long',year:'numeric'}) }
</script>

<template>
  <PublicLayout>
    <!-- HERO -->
    <section class="relative overflow-hidden pt-12 md:pt-16 pb-10 md:pb-12">
      <div class="hero-ribbon" aria-hidden="true"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-4" v-reveal>
          <span class="tag-ghost">Entreprise de BTP · Tanger</span>
          <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
            Infrastructures & ouvrages — <span class="text-bk-gold">BKO Construction</span>
          </h1>
          <p class="mt-4 text-white/80">
            Depuis Tanger, nous accompagnons collectivités, promoteurs et industriels dans leurs projets structurants partout au Maroc.
          </p>
          <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="#devis" class="btn-outline-gold" @click="focusDevis">Devis express</a>
            <a href="#projects" class="btn-ghost">Réalisations</a>
          </div>
          <div class="mt-5 flex flex-wrap gap-2">
            <span class="chip">Routes</span><span class="chip">VRD</span><span class="chip">Bâtiments</span><span class="chip">Génie civil</span><span class="chip">Immobilier</span>
          </div>
        </div>

        <!-- Scène 3D -->
        <div class="lg:col-span-4 relative" v-reveal>
          <div class="hero-halo" aria-hidden="true"></div>
          <div class="persp">
            <div class="platform"></div>
            <div class="platform-cards">
              <div class="iso-card" style="left:6%;  bottom:10%; --h:120px; --w:150px; --z:18;"></div>
              <div class="iso-card" style="left:36%; bottom:16%; --h:95px;  --w:125px; --z:24;"></div>
              <div class="iso-card" style="left:58%; bottom:28%; --h:74px;  --w:105px; --z:36;"></div>
            </div>
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

        <!-- KPI -->
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
              <defs><linearGradient id="g" x1="0" x2="1" y1="0" y2="0"><stop offset="0" stop-color="rgba(220,193,118,.25)"/><stop offset="1" stop-color="rgba(255,255,255,.35)"/></linearGradient></defs>
            </svg>
            <div class="mt-1 text-2xl font-extrabold"><span v-countup="56">56</span> projets</div>
          </div>
          <div class="card ring-gold p-5">
            <div class="flex items-center gap-3"><div class="badge-iso">ISO</div><div><div class="font-bold">Qualité & Sécurité</div><div class="text-white/70 text-sm">Référentiels ISO 9001/45001.</div></div></div>
          </div>
        </div>
      </div>
    </section>

   <!-- APERÇU À PROPOS — ULTRA PREMIUM (Full Tailwind) -->
<section class="relative py-12 md:py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" v-reveal>
    <div class="relative overflow-hidden rounded-2xl border border-white/15 bg-white/5 backdrop-blur-xl shadow-[0_30px_120px_-40px_rgba(0,0,0,.65)]">
      <!-- halos & décor -->
      <div class="pointer-events-none absolute -inset-6 opacity-30 blur-3xl"
           aria-hidden="true"
           style="background:radial-gradient(60% 60% at 18% 20%, #dcc176 0%, transparent 60%);"></div>

      <div class="pointer-events-none absolute inset-0 opacity-20 mix-blend-screen"
           aria-hidden="true"
           style="background:
             linear-gradient(transparent 31px, rgba(255,255,255,.12) 32px, transparent 33px) 0 0 / 32px 32px,
             linear-gradient(90deg, transparent 31px, rgba(255,255,255,.12) 32px, transparent 33px) 0 0 / 32px 32px;"></div>

      <div class="pointer-events-none absolute -top-12 -right-12 h-80 w-80 rounded-full blur-md opacity-60 animate-pulse"
           aria-hidden="true"
           style="background:radial-gradient(circle at 30% 30%, rgba(220,193,118,.45), rgba(220,193,118,.08) 60%, transparent 70%);"></div>

      <!-- body -->
      <div class="relative grid gap-6 p-6 md:p-8 lg:p-10 lg:grid-cols-12">
        <!-- LEFT -->
        <div class="lg:col-span-7">
          <h2 class="text-left text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight">
            <span class="bg-gradient-to-r from-white to-white/80 bg-clip-text text-transparent">À propos</span>
            <span class="mx-2 text-white/50">—</span>
            <span class="bg-gradient-to-r from-[#dcc176] to-[#f3e8c1] bg-clip-text text-transparent">aperçu</span>
          </h2>

          <p class="mt-4 text-white/85 max-w-prose">
            BKO Construction pilote des projets d’infrastructures et de bâtiments avec une exigence constante :
            délais tenus, qualité mesurée et sécurité prioritaire. Méthodes lean, phasage précis et traçabilité complète.
          </p>

          <!-- chips -->
          <div class="mt-4 flex flex-wrap gap-2">
            <span class="inline-flex items-center rounded-lg border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold text-white/90">Lean construction</span>
            <span class="inline-flex items-center rounded-lg border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold text-white/90">BIM &amp; DOE</span>
            <span class="inline-flex items-center rounded-lg border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold text-white/90">HSE prioritaire</span>
          </div>

          <!-- meters -->
          <div class="mt-6 grid gap-3">
            <!-- meter 1 -->
            <div>
              <div class="mb-1 flex items-center justify-between text-sm text-white/90">
                <span>Respect des délais</span>
                <span class="font-extrabold text-[#dcc176]">96%</span>
              </div>
              <div class="relative h-2 w-full overflow-hidden rounded-full border border-white/15 bg-white/10">
                <span class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#dcc176] to-[#f3e8c1]"
                      style="width:96%"></span>
              </div>
            </div>
            <!-- meter 2 -->
            <div>
              <div class="mb-1 flex items-center justify-between text-sm text-white/90">
                <span>Satisfaction clients</span>
                <span class="font-extrabold text-[#dcc176]">98%</span>
              </div>
              <div class="relative h-2 w-full overflow-hidden rounded-full border border-white/15 bg-white/10">
                <span class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#dcc176] to-[#f3e8c1]"
                      style="width:98%"></span>
              </div>
            </div>
            <!-- meter 3 -->
            <div>
              <div class="mb-1 flex items-center justify-between text-sm text-white/90">
                <span>Incidents majeurs</span>
                <span class="font-extrabold text-[#dcc176]">0</span>
              </div>
              <div class="relative h-2 w-full overflow-hidden rounded-full border border-white/15 bg-white/10">
                <span class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#dcc176] to-[#f3e8c1]"
                      style="width:0%"></span>
              </div>
            </div>
          </div>

          <div class="mt-6">
            <Link :href="r('public.about', {}, false, '/apropos')"
                  class="inline-flex items-center justify-center rounded-xl border border-white/15 px-4 py-2 text-white transition
                         hover:border-[#dcc176]/60 hover:-translate-y-0.5">
              Découvrir l’entreprise
            </Link>
          </div>
        </div>

        <!-- RIGHT -->
        <div class="lg:col-span-5 relative">
          <!-- ruban -->
          <div class="pointer-events-none absolute right-[-52px] top-2 w-[220px] rotate-[35deg] transform-gpu
                      bg-gradient-to-r from-white/20 to-[#dcc176]/70 text-[#0f141a] font-black tracking-wider text-center
                      border border-white/25 shadow-[0_20px_80px_-40px_rgba(0,0,0,.6)]">
            <span class="block py-1.5">BKO</span>
          </div>

          <div class="grid grid-cols-3 gap-3">
            <!-- stat 1 -->
            <div
              class="relative rounded-xl border border-white/15 bg-gradient-to-b from-white/10 to-white/5 p-4 text-center
                     shadow-[0_40px_120px_-40px_rgba(0,0,0,.55)] transform-gpu transition
                     will-change-transform hover:-translate-y-1 hover:scale-[1.02]
                     hover:border-[#dcc176]/40 hover:shadow-[0_50px_140px_-50px_rgba(0,0,0,.7)]
                     hover:[transform:perspective(900px)_rotateX(3deg)]">
              <div class="text-2xl font-black bg-gradient-to-r from-[#dcc176] to-[#f3e8c1] bg-clip-text text-transparent">
                <span v-countup="120">120</span>+
              </div>
              <div class="mt-1 text-sm text-white/80">km de voiries</div>
            </div>

            <!-- stat 2 -->
            <div
              class="relative rounded-xl border border-white/15 bg-gradient-to-b from-white/10 to-white/5 p-4 text-center
                     shadow-[0_40px_120px_-40px_rgba(0,0,0,.55)] transform-gpu transition
                     will-change-transform hover:-translate-y-1 hover:scale-[1.02]
                     hover:border-[#dcc176]/40 hover:shadow-[0_50px_140px_-50px_rgba(0,0,0,.7)]
                     hover:[transform:perspective(900px)_rotateX(3deg)]">
              <div class="text-2xl font-black bg-gradient-to-r from-[#dcc176] to-[#f3e8c1] bg-clip-text text-transparent">
                <span v-countup="40">40</span>+
              </div>
              <div class="mt-1 text-sm text-white/80">bâtiments livrés</div>
            </div>

            <!-- stat 3 -->
            <div
              class="relative rounded-xl border border-white/15 bg-gradient-to-b from-white/10 to-white/5 p-4 text-center
                     shadow-[0_40px_120px_-40px_rgba(0,0,0,.55)] transform-gpu transition
                     will-change-transform hover:-translate-y-1 hover:scale-[1.02]
                     hover:border-[#dcc176]/40 hover:shadow-[0_50px_140px_-50px_rgba(0,0,0,.7)]
                     hover:[transform:perspective(900px)_rotateX(3deg)]">
              <div class="text-2xl font-black bg-gradient-to-r from-[#dcc176] to-[#f3e8c1] bg-clip-text text-transparent">
                <span v-countup="1">1</span>
              </div>
              <div class="mt-1 text-sm text-white/80">an d’expérience</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


    <!-- PARTENAIRES -->
    <section class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Ils nous font confiance</h2>
        <div class="marquee2" v-reveal>
          <div class="marquee2__track" :style="{ '--d':'26s' }">
            <span v-for="(n,i) in partnerList" :key="'m1-'+i" class="logo-pill2">{{ n }}</span>
            <span v-for="(n,i) in partnerList" :key="'m2-'+i" class="logo-pill2">{{ n }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- SERVICES -->
    <section id="services" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Nos services</h2>
        <div class="mt-8 grid gap-5 md:grid-cols-3">
          <article class="card-iso ring-gold" @mousemove="handleTilt" @mouseleave="resetTilt" v-reveal>
            <div class="illus iso-vrd" aria-hidden="true"><span class="b1"></span><span class="b2"></span><span class="b3"></span></div>
            <header class="flex items-center gap-3 mt-2"><span class="badge-ghost">VRD</span><h3 class="text-xl font-bold">Voirie & Routes</h3></header>
            <p class="text-white/80 mt-2">Terrassement, assainissement, chaussées, ouvrages d’art.</p>
            <ul class="mt-3 text-sm text-white/70 list-disc pl-5 space-y-1"><li>Chaussées souples & rigides</li><li>Ouvrages hydrauliques</li><li>Signalisation & sécurité</li></ul>
          </article>
          <article class="card-iso ring-gold" @mousemove="handleTilt" @mouseleave="resetTilt" v-reveal>
            <div class="illus iso-bat" aria-hidden="true"><span class="core"></span><span class="slab"></span><span class="beam"></span></div>
            <header class="flex items-center gap-3 mt-2"><span class="badge-ghost">BAT</span><h3 class="text-xl font-bold">Bâtiments</h3></header>
            <p class="text-white/80 mt-2">Logements, bureaux, structures béton, finitions premium.</p>
            <ul class="mt-3 text-sm text-white/70 list-disc pl-5 space-y-1"><li>Structure & second œuvre</li><li>BIM exé & DOE</li><li>Contrôle qualité</li></ul>
          </article>
          <article class="card-iso ring-gold" @mousemove="handleTilt" @mouseleave="resetTilt" v-reveal>
            <div class="illus iso-gc" aria-hidden="true"><span class="dam"></span><span class="pipe"></span></div>
            <header class="flex items-center gap-3 mt-2"><span class="badge-ghost">GC</span><h3 class="text-xl font-bold">Génie civil</h3></header>
            <p class="text-white/80 mt-2">Hydraulique, soutènements, bassins & réseaux.</p>
            <ul class="mt-3 text-sm text-white/70 list-disc pl-5 space-y-1"><li>Études d’exécution</li><li>Phasage & logistique</li><li>Réceptions & DOE</li></ul>
          </article>
        </div>
      </div>
    </section>

    <!-- RÉALISATIONS (nouvelles cartes) -->
    <section id="projects" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Réalisations</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <Link v-for="p in (heroList.length ? heroList : gridList)" :key="p.id"
                :href="r('public.projects.show', p.slug ?? p.id, false, `/realisations/${p.slug ?? p.id}`)"
                class="proj group" v-reveal>
            <div class="proj__media">
              <img v-if="p.cover_image" :src="`/storage/${p.cover_image}`" :alt="`BKO — ${p.title}`" loading="lazy">
              <div v-else class="skeleton"></div>
              <div class="proj__shine"></div>
            </div>
            <div class="proj__body">
              <div class="proj__tags">
                <span class="pill">{{ p.category || 'Projet' }}</span>
                <span v-if="p.city" class="pill pill--ghost">{{ p.city }}</span>
              </div>
              <h3 class="proj__title">{{ p.title }}</h3>
              <div class="proj__cta">Voir le projet</div>
            </div>
          </Link>
        </div>
        <div class="mt-8 flex justify-center">
          <Link :href="r('public.projects', {}, false, '/realisations')" class="btn-ghost">Toutes les réalisations</Link>
        </div>
      </div>
    </section>

    <!-- RFP / DEVIS -->
    <section id="rfp" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Appels d’offres & Devis</h2>
        <div class="mt-8 grid lg:grid-cols-2 gap-8 items-stretch">
          <div class="card ring-gold p-6 md:p-8 flex flex-col justify-between min-h-[360px]" v-reveal>
            <div>
              <h3 class="text-2xl md:text-3xl font-extrabold">Envoyez votre DCE ou faites une demande express</h3>
              <p class="text-white/80 mt-3">Réponse rapide avec chiffrage fiable, variantes et phasage réaliste. Conseils techniques pour optimiser délais & budget.</p>
              <div class="mt-6 mini-bullets"><span>✔️ Métrés & variantes optimisées</span><span>✔️ Délais & phasage détaillés</span><span>✔️ Proposition claire et traçable</span><span>✔️ Interventions partout au Maroc</span></div>
            </div>
            <div class="mt-6 flex gap-3"><a href="#contact" class="btn-outline-gold">Nous contacter</a><a href="#devis" class="btn-ghost" @click.prevent="focusDevis">Devis express</a></div>
          </div>

          <form id="devis" class="card ring-gold p-6 md:p-8 grid gap-4" @submit.prevent="submitQuote" aria-describedby="quote-status" v-reveal>
            <div v-if="successMsg" id="quote-status" aria-live="polite" class="rounded-lg px-3 py-2 text-sm text-emerald-300 bg-emerald-500/10 ring-1 ring-emerald-500/20">{{ successMsg }}</div>
            <div v-else-if="Object.keys(qForm.errors).length" id="quote-status" aria-live="assertive" class="rounded-lg px-3 py-2 text-sm text-red-300 bg-red-500/10 ring-1 ring-red-500/20">Merci de vérifier les champs saisis.</div>

            <div class="grid md:grid-cols-2 gap-3">
              <input id="devis-name" v-model="qForm.name" type="text" placeholder="Nom / Société" class="field" required />
              <input v-model="qForm.email" type="email" placeholder="Email (ex. contact@domaine.ma)" class="field" />
            </div>
            <div class="grid md:grid-cols-2 gap-3">
              <input v-model="qForm.phone" type="tel" placeholder="Téléphone" class="field" />
              <input v-model="qForm.city" type="text" placeholder="Ville / Site (ex. Tanger Med)" class="field" />
            </div>
            <div class="grid md:grid-cols-2 gap-3">
              <select v-model="qForm.project_type" class="field">
                <option value="" disabled>Type de projet</option><option>VRD / Voirie</option><option>Génie civil</option><option>Bâtiment</option><option>Ouvrage d’art</option><option>Autre</option>
              </select>
              <select v-model="qForm.budget" class="field">
                <option value="" disabled>Budget estimatif</option><option>&lt; 1 M MAD</option><option>1–5 M MAD</option><option>5–20 M MAD</option><option>&gt; 20 M MAD</option>
              </select>
            </div>
            <div class="grid md:grid-cols-2 gap-3">
              <input v-model="qForm.desired_date" type="date" class="field" placeholder="Échéance souhaitée" />
              <input v-model="qForm.company" type="text" class="field" placeholder="Société (optionnel)" />
            </div>
            <textarea v-model="qForm.message" rows="3" placeholder="Votre besoin (lieu, délais, budget…)" class="field"></textarea>

            <div class="space-y-2">
              <label class="upload">
                <input type="file" multiple accept=".pdf,.zip,.rar,.7z,.doc,.docx,.xls,.xlsx,.dwg,.dxf" class="sr-only" @change="pickFiles" />
                <span class="upload-ui">Joindre DCE / plans — <span class="opacity-80">{{ qForm.files.length }} fichier(s)</span></span>
              </label>
              <div class="text-xs text-white/50">Formats: PDF, ZIP, RAR, 7Z, DOC(X), XLS(X), DWG, DXF — Max 20 Mo / fichier, 10 fichiers.</div>
              <div v-if="filesClientError" class="text-sm text-red-300">{{ filesClientError }}</div>
              <div v-if="qForm.files.length" class="flex flex-wrap gap-2 pt-1">
                <div v-for="(f,i) in qForm.files" :key="i" class="inline-flex items-center gap-2 px-2 h-8 rounded-lg bg-white/10 ring-1 ring-white/15">
                  <span class="truncate max-w-[18ch]">{{ f.name }}</span>
                  <span class="text-[11px] text-white/60">{{ formatSize(f.size) }}</span>
                  <button type="button" class="text-white/60 hover:text-red-300 text-xs" @click="removeFile(i)">✕</button>
                </div>
              </div>
            </div>

            <input v-model="qForm.hp" type="text" class="sr-only" tabindex="-1" autocomplete="off" />
            <div class="flex items-center gap-3 pt-1">
              <button type="submit" class="btn-outline-gold" :disabled="qForm.processing">{{ qForm.processing ? 'Envoi…' : 'Envoyer' }}</button>
              <div v-if="qForm.progress" class="flex-1 h-1 rounded bg-white/10 overflow-hidden"><div class="h-1 bg-bk-gold transition-all" :style="{ width: `${qForm.progress.percentage}%` }"></div></div>
            </div>
          </form>
        </div>
      </div>
    </section>

    <!-- ACTUALITÉS -->
    <section id="news" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Actualités</h2>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <Link v-for="a in postList" :key="a.id" :href="r('public.posts.show', a.slug ?? a.id, false, `/actualites/${a.slug ?? a.id}`)" class="news group" v-reveal>
            <div class="news__media"><img v-if="a.cover_image" :src="`/storage/${a.cover_image}`" :alt="`BKO — ${a.title}`" loading="lazy"><div v-else class="skeleton"></div><div class="news__shine"></div></div>
            <div class="news__body"><h3 class="news__title line-clamp-2">{{ a.title }}</h3><p v-if="a.excerpt" class="news__excerpt line-clamp-2">{{ a.excerpt }}</p><div class="news__meta">{{ fmt(a.published_at || undefined) }}</div></div>
          </Link>
        </div>
        <div class="mt-7 flex justify-center"><Link :href="r('public.posts', {}, false, '/actualites')" class="btn-ghost">Toutes les actualités</Link></div>
      </div>
    </section>

    <!-- TÉMOIGNAGES -->
    <section id="temoignages" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Témoignages</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
          <article class="tcard ring-gold" v-reveal><p class="tcard__txt">“Planning tenu et qualité au rendez-vous. Coordination avec nos équipes exemplaire.”</p><div class="tcard__who"><div class="tcard__avatar"></div><div><div class="tcard__name">Directeur de zone</div><div class="tcard__org">Collectivité locale</div></div></div></article>
          <article class="tcard ring-gold" v-reveal><p class="tcard__txt">“Des variantes intelligentes ont réduit budget et délais. Bon niveau de reporting.”</p><div class="tcard__who"><div class="tcard__avatar"></div><div><div class="tcard__name">Chef de projet</div><div class="tcard__org">Promoteur privé</div></div></div></article>
          <article class="tcard ring-gold" v-reveal><p class="tcard__txt">“Sécurité et phasage maîtrisés en site occupé. Zéro incident.”</p><div class="tcard__who"><div class="tcard__avatar"></div><div><div class="tcard__name">Responsable HSE</div><div class="tcard__org">Site industriel</div></div></div></article>
        </div>
      </div>
    </section>

    <!-- CONTACT -->
    <section id="contact" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-6 md:gap-8 items-stretch">
        <div class="card ring-gold p-6 md:p-7 flex flex-col gap-5" v-reveal>
          <div class="text-center">
            <h2 class="section-title mb-1">Contact</h2>
            <p class="text-white/80">Implantés à Tanger — réponse sous 24h ouvrées.</p>
          </div>
          <ul class="space-y-3 text-white/85">
            <li class="flex items-center gap-3"><span class="i i-phone"></span><a href="tel:+212770556021" class="hover:text-bk-gold font-semibold">+212 7 70 55 60 21</a></li>
            <li class="flex items-center gap-3"><span class="i i-mail"></span><a href="mailto:contact@bkoconstruction.com" class="hover:text-bk-gold">contact@bkoconstruction.com</a></li>
            <li class="flex items-center gap-3"><span class="i i-pin"></span><span>P5H4+2J3 Tanger, Maroc</span></li>
            <li class="flex items-center gap-3"><span class="i i-clock"></span><span>Lun–Sam : 8h30 – 18h</span></li>
          </ul>
          <div class="grid grid-cols-3 gap-2 pt-2">
            <a href="tel:+212770556021" class="btn-outline-gold sm w-full justify-center">Appeler</a>
            <a href="https://wa.me/212770556021" target="_blank" rel="noopener" class="btn-ghost sm w-full justify-center">WhatsApp</a>
            <a href="mailto:contact@bkoconstruction.com" class="btn-ghost sm w-full justify-center">Email</a>
          </div>
        </div>

        <!-- >>> IFRAME EMBED CORRIGÉ (Plus Code) <<< -->
        <div class="card ring-gold overflow-hidden" v-reveal>
          <div class="relative w-full h-[340px] md:h-full">
            <iframe
              class="absolute inset-0 w-full h-full"
              title="BKO Construction — Localisation"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              src="https://www.google.com/maps?q=P5H4%2B2J3%20Tanger%2C%20Maroc&output=embed">
            </iframe>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<style scoped>
/* helpers */
.no-scrollbar::-webkit-scrollbar{ display:none } .no-scrollbar{ -ms-overflow-style:none; scrollbar-width:none }
@keyframes shine{ from{transform:translateX(-120%)} to{transform:translateX(120%)} }
.reveal{ opacity:0; transform: translateY(12px) scale(.985); filter: blur(2px); transition: all .6s cubic-bezier(.2,.8,.2,1) }
.reveal.is-visible{ opacity:1; transform:none; filter:none }

/* titres */
.section-title{ position:relative; display:block; width:100%; font-weight:800; text-align:center; letter-spacing:-.01em; font-size:clamp(1.65rem,2vw + 1rem,2.25rem) }
.section-title::after{ content:""; display:block; height:2px; width:110px; margin:.7rem auto 0; background:linear-gradient(90deg,#dcc176,rgba(253,253,254,.6)); opacity:.85; border-radius:999px }

/* tokens UI */
.card{ position:relative; border-radius:1.1rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); backdrop-filter:blur(10px) }
.ring-gold{ position:relative }
.ring-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; z-index:0; pointer-events:none; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.tag-ghost{ display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .6rem; border-radius:.55rem; font-weight:800; font-size:.75rem; color:#fdfdfe; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.20); box-shadow: inset 0 1px 0 rgba(255,255,255,.25) }
.btn-ghost{ display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.05rem; border-radius:1rem; border:1px solid rgba(255,255,255,.15); color:#fdfdfe; transition:border-color .2s, transform .2s }
.btn-ghost:hover{ border-color: rgba(220,193,118,.6); transform: translateY(-1px) }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.8rem 1.15rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent; transition: transform .2s }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.btn-outline-gold:hover{ transform: translateY(-2px) }
.chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .7rem; border-radius:.7rem; color:#fdfdfe; font-weight:600; font-size:.9rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.14) }
.badge-ghost{ display:inline-flex; align-items:center; justify-content:center; border-radius:.4rem; padding:.25rem .4rem; font-size:.7rem; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:#fdfdfe }
.field{ width:100%; border-radius:.75rem; padding:.75rem .9rem; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.14); color:#fdfdfe; outline:none; position:relative; z-index:1 }
.field:focus{ outline:2px solid rgba(220,193,118,.6); outline-offset:2px }
.upload{ display:inline-flex; border-radius:.8rem; border:1px dashed rgba(220,193,118,.5); color:#fdfdfe; position:relative; z-index:1 }
.upload .upload-ui{ width:100%; padding:.85rem .95rem; border-radius:.8rem; background:rgba(220,193,118,.1) }

/* bullets */
.mini-bullets{ display:grid; gap:.5rem; color:#fdfdfe }

/* Héros 3D */
.hero-ribbon{ position:absolute; left:0; right:0; top:-10px; height:36px; opacity:.45; transform: rotate(-2deg); background: repeating-linear-gradient(135deg,#2a3644 0 18px,#2a3644 18px 28px,#dcc176 28px 46px,#dcc176 46px 56px) }
.hero-halo{ position:absolute; inset:-40px; border-radius:2rem; filter:blur(36px); opacity:.55; background: radial-gradient(60% 60% at 25% 25%, #dcc176 0%, transparent 60%) }
.persp{ perspective:1200px; position:relative }
.platform{ height:11rem; border-radius:1.2rem; background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.05)); border:1px solid rgba(255,255,255,.12); transform: rotateX(55deg) rotateZ(-12deg); box-shadow: 0 50px 120px -40px rgba(0,0,0,.6) }
.platform-cards{ position:absolute; inset:0; display:grid; place-items:center; pointer-events:none }
.platform-cards>div{ position:absolute }
.iso-card{ height:var(--h); width:var(--w); transform: translateZ(0) rotateX(55deg) rotateZ(-12deg); background: linear-gradient(160deg, rgba(255,255,255,.12), rgba(21,30,39,.55)); border: 1px solid rgba(255,255,255,.14); border-radius: 12px; box-shadow: 0 40px 90px -40px rgba(0,0,0,.55); z-index: var(--z) }
.iso-card::after{ content:""; position:absolute; inset:-1px; border-radius:12px; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.8),rgba(255,255,255,.2),rgba(220,193,118,.8)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; opacity:.75 }
.crane{ position:absolute; right:0; top:-16px; width:70%; filter: drop-shadow(0 6px 30px rgba(0,0,0,.35)) }
.trolley{ animation: trolley 6s ease-in-out infinite alternate } .hook{ animation: hook 3s ease-in-out infinite alternate }
@keyframes trolley{ from{ transform: translateX(40px) } to{ transform: translateX(160px) } }
@keyframes hook{ from{ transform: translateY(0) } to{ transform: translateY(8px) } }

/* services 3D */
.card-iso{ transform:perspective(900px) rotateX(var(--rx,0deg)) rotateY(var(--ry,0deg)); transition: transform .15s ease-out, box-shadow .2s ease; box-shadow:0 40px 120px -40px rgba(0,0,0,.55); border-radius:1rem; padding:1.1rem 1.2rem 1.2rem; background:linear-gradient(160deg,rgba(255,255,255,.08),rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.12) }
.illus{ height:120px; position:relative; margin-top:.1rem }
.iso-vrd .b1,.iso-vrd .b2,.iso-vrd .b3{ position:absolute; border-radius:10px; transform: rotateX(55deg) rotateZ(-12deg); background:linear-gradient(160deg,rgba(255,255,255,.12),rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.14) }
.iso-vrd .b1{ width:120px; height:26px; left:6%; bottom:14% } .iso-vrd .b2{ width:100px; height:22px; left:34%; bottom:26% } .iso-vrd .b3{ width:78px; height:18px; left:60%; bottom:38% }
.iso-bat .core,.iso-bat .slab,.iso-bat .beam{ position:absolute; transform: rotateX(55deg) rotateZ(-12deg); border:1px solid rgba(255,255,255,.14); background:linear-gradient(160deg,rgba(255,255,255,.12),rgba(21,30,39,.55)); border-radius:8px }
.iso-bat .core{ width:50px; height:70px; left:18%; bottom:16% } .iso-bat .slab{ width:110px; height:12px; left:26%; bottom:28% } .iso-bat .beam{ width:60px; height:8px; left:52%; bottom:38% }
.iso-gc .dam,.iso-gc .pipe{ position:absolute; transform: rotateX(55deg) rotateZ(-12deg); background:linear-gradient(160deg,rgba(255,255,255,.12),rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.14); border-radius:10px }
.iso-gc .dam{ width:120px; height:28px; left:10%; bottom:18% } .iso-gc .pipe{ width:26px; height:26px; left:66%; bottom:34%; border-radius:999px }

/* Projets */
.proj{ position:relative; border-radius:1.1rem; overflow:hidden; display:flex; flex-direction:column; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.12); box-shadow:0 40px 120px -40px rgba(0,0,0,.55); transform:translateZ(0) }
.proj:hover{ border-color:rgba(220,193,118,.35); box-shadow:0 50px 140px -50px rgba(0,0,0,.7) }
.proj__media{ position:relative; aspect-ratio: 16/10; overflow:hidden }
.proj__media img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transition: transform .4s ease }
.proj:hover .proj__media img{ transform: scale(1.05) }
.proj__shine{ position:absolute; inset:-30%; background: radial-gradient(40% 40% at 25% 20%, rgba(220,193,118,.35), transparent 60%); filter: blur(36px); opacity:.6; transform: rotate(-8deg) }
.proj__body{ position:relative; display:flex; flex-direction:column; gap:.45rem; padding:1rem 1.1rem 1.2rem; background:linear-gradient(180deg, rgba(15,20,26,.05) 25%, rgba(15,20,26,.88)) }
.proj__tags{ display:flex; gap:.4rem; flex-wrap:wrap }
.pill{ display:inline-flex; align-items:center; gap:.35rem; padding:.28rem .55rem; margin-bottom:.4rem; border-radius:.6rem; font-weight:700; font-size:.72rem; color:#0f141a; background:linear-gradient(90deg,#dcc176,#f3e8c1); box-shadow:0 10px 24px -14px rgba(220,193,118,.8) }
.pill--ghost{ color:#fdfdfe; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.2) }
.proj__title{ font-weight:800; letter-spacing:.2px; font-size:1.05rem }
.proj__cta{ opacity:.9; font-weight:700; color:#dcc176 }

/* Actualités */
.news{ position:relative; border-radius:1.1rem; overflow:hidden; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.12); display:flex; flex-direction:column; transform:translateZ(0); box-shadow:0 40px 120px -40px rgba(0,0,0,.55) }
.news:hover{ border-color:rgba(220,193,118,.35); box-shadow:0 50px 140px -50px rgba(0,0,0,.7) }
.news__media{ position:relative; aspect-ratio: 16/9; overflow:hidden }
.news__media img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transition: transform .4s ease }
.news:hover .news__media img{ transform: scale(1.06) }
.news__shine{ position:absolute; inset:-30%; background: radial-gradient(40% 40% at 25% 20%, rgba(220,193,118,.35), transparent 60%); filter: blur(36px); opacity:.5 }
.news__body{ padding:1rem 1.1rem 1.2rem }
.news__title{ font-weight:800; font-size:1.05rem }
.news__excerpt{ color:rgba(253,253,254,.78); margin-top:.35rem; font-size:.95rem }
.news__meta{ color:rgba(253,253,254,.65); font-size:.85rem; margin-top:.35rem }

/* Skeleton */
.skeleton{ position:absolute; inset:0; background: linear-gradient(90deg, rgba(255,255,255,.06), rgba(255,255,255,.12), rgba(255,255,255,.06)); background-size:200% 100%; animation: sk 1.6s linear infinite; border-radius:inherit }
@keyframes sk { from{ background-position: 200% 0 } to{ background-position: -200% 0 } }

/* jauges & badges */
.radial{ --val:92; width:72px; height:72px; border-radius:999px; display:grid; place-items:center; background:conic-gradient(#dcc176 calc(var(--val)*1%), rgba(255,255,255,.2) 0), radial-gradient(farthest-side, rgba(21,30,39,1) 60%, transparent 61%); box-shadow: inset 0 0 0 1px rgba(255,255,255,.12); color:#fdfdfe; font-weight:800 }
.badge-iso{ display:inline-flex; align-items:center; justify-content:center; width:34px; height:24px; border-radius:.45rem; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.22); font-weight:900 }

/* Marquee */
.marquee2{ position:relative; overflow:hidden }
.marquee2__track{ --d:26s; display:flex; width:max-content; animation:marquee2 var(--d) linear infinite; will-change:transform }
.marquee2:hover .marquee2__track{ animation-play-state:paused }
@keyframes marquee2 { to{ transform: translateX(-50%) } }
.logo-pill2{ margin-right:2rem; display:inline-flex; align-items:center; justify-content:center; padding:.6rem 1.1rem; border-radius:.8rem; font-weight:800; letter-spacing:.3px; color:#fdfdfe; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12) }

/* FAQ */
.faq{ position:relative; border-radius:1rem; overflow:hidden; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.12) }
.faq + .faq{ margin-top:.5rem }
.faq[open] .faq__q{ background:rgba(255,255,255,.06) }
.faq__q{ display:flex; align-items:center; gap:.75rem; padding:1rem 1.1rem; cursor:pointer; list-style:none; user-select:none }
.faq__icon{ width:18px; height:18px; border-radius:6px; background: rgba(255,255,255,.12); -webkit-mask:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M12 5v14m-7-7h14"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="white" d="M12 5v14m-7-7h14"/></svg>') center/contain no-repeat; transition: transform .2s ease }
.faq[open] .faq__icon{ transform: rotate(45deg) }
.faq__txt{ font-weight:700 }
.faq__a{ padding:0 1.1rem 1.1rem; color:#fdfdfe; opacity:.9 }

/* À propos */
.about{ position:relative; overflow:hidden }
.about__halo{ position:absolute; inset:0; filter:blur(70px); opacity:.25; background:radial-gradient(60% 60% at 20% 20%, #dcc176 0%, transparent 60%) }
.about__body{ position:relative; display:grid; gap:1.2rem; padding:1.2rem }
@media (min-width:1024px){ .about__body{ grid-template-columns:1.2fr .8fr; padding:1.6rem } }
.about__p{ color:rgba(253,253,254,.85); margin-top:.6rem }
.about__bullets{ display:grid; gap:.4rem; margin-top:.8rem; color:rgba(253,253,254,.85) }
.about__right{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.6rem; align-items:stretch }
.about__stat{ border-radius:.9rem; padding:.9rem .8rem; text-align:center; background:linear-gradient(160deg,rgba(255,255,255,.08),rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.12) }
.about__n{ font-weight:900; font-size:1.35rem; color:#dcc176 }
.about__k{ color:rgba(253,253,254,.78) }

/* Témoignages */
.tcard{ position:relative; border-radius:1rem; padding:1rem 1.1rem 1.1rem; background:linear-gradient(140deg, rgba(255,255,255,.06), rgba(21,30,39,.55)); border:1px solid rgba(255,255,255,.12); box-shadow:0 40px 120px -40px rgba(0,0,0,.55) }
.tcard__txt{ color:rgba(253,253,254,.9); font-size:1rem }
.tcard__who{ display:flex; align-items:center; gap:.7rem; margin-top:.9rem }
.tcard__avatar{ width:38px; height:38px; border-radius:999px; background:linear-gradient(120deg,#dcc176,#f3e8c1) }
.tcard__name{ font-weight:800 }
.tcard__org{ color:rgba(253,253,254,.7); font-size:.9rem }

/* icônes contact */
.i{ width:18px; height:18px; display:inline-block; border-radius:5px; background: rgba(255,255,255,.12) }
.i-phone{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M6.6 10.8a15.6 15.6 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11.5 11.5 0 003.6.6 1 1 0 011 1V20a1 1 0 01-1 1A16 16 0 013 5a1 1 0 011-1h3.5a1 1 0 011 1 11.5 11.5 0 00.6 3.6 1 1 0 01-.24 1z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-mail{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M20 8l-8 5-8-5V6l8 5 8-5v2zM4 18h16v-2H4v2z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M20 8l-8 5-8-5V6l8 5 8-5v2zM4 18h16v-2H4v2z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-pin{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a7 7 0 017 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 017-7zm0 9.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>') center/contain no-repeat; background:#dcc176 }
.i-clock{ -webkit-mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; mask:url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 11H7v-2h4V6h2v7z"/></svg>') center/contain no-repeat; background:#dcc176 }

/* util */
.text-bk-gold{ color:#dcc176 } .bg-bk-gold{ background:#dcc176 } .text-bk-night{ color:#0f141a }
</style>
