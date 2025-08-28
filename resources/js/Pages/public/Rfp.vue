<!-- resources/js/Pages/Public/Rfp.vue -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { useForm } from '@inertiajs/vue3'
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

/* Upload config */
const ALLOWED = [
  'application/pdf','application/x-zip-compressed','application/zip',
  'application/x-rar-compressed','application/x-7z-compressed',
  'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'application/octet-stream'
]
const EXT_ALLOWED = ['.pdf','.zip','.rar','.7z','.doc','.docx','.xls','.xlsx','.dwg','.dxf']
const MAX_FILE_MB = 20
const MAX_FILES   = 10

function hasAllowedExt(n:string){ return EXT_ALLOWED.some(ext=>n.toLowerCase().endsWith(ext)) }
function formatSize(bytes?:number|null){
  if(bytes==null) return ''
  const u=['o','Ko','Mo','Go']; let i=0, v=bytes
  while(v>=1024 && i<u.length-1){ v/=1024; i++ }
  return `${v.toFixed(v>=10||i===0?0:1)} ${u[i]}`
}

/* Form RFP */
const form = useForm({
  org:'', contact:'', email:'', phone:'',
  title:'', reference:'', location:'', deadline:'',
  message:'', files:[] as File[], hp:''
})
let filesClientError = ''

function pickFiles(e:Event){
  try{
    const input = e.target as HTMLInputElement; if(!input.files) return
    const picked = Array.from(input.files)
    const out    = [...form.files]
    let error = ''

    for(const f of picked){
      if(out.length>=MAX_FILES){ error = `Maximum ${MAX_FILES} fichiers.`; break }
      const okType = ALLOWED.includes(f.type) || hasAllowedExt(f.name)
      const okSize = f.size <= MAX_FILE_MB*1024*1024
      if(!okType){ error = `Format non autorisé: ${f.name}`; break }
      if(!okSize){ error = `Taille > ${MAX_FILE_MB} Mo: ${f.name}`; break }
      out.push(f)
    }
    if(error){ filesClientError = error } else { filesClientError=''; form.files = out }
    input.value=''
  }catch{ filesClientError='Impossible de lire le fichier. Réessayez.' }
}
function removeFile(i:number){ form.files.splice(i,1) }

function submitRfp(){
  filesClientError=''
  form.post(r('forms.tender.store', {}, false, '/rfp'), {
    preserveScroll:true, forceFormData:true,
    onSuccess:()=>{
      const keep = { email: form.email, contact: form.contact }
      form.reset('org','phone','title','reference','location','deadline','message','files','hp')
      form.email   = keep.email
      form.contact = keep.contact
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
          <span class="tag-ghost">Appels d’offres</span>
          <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
            Déposez votre <span class="text-bk-gold">DCE</span> — on chiffre vite & juste
          </h1>
          <p class="mt-4 text-white/80 max-w-2xl">
            VRD, Bâtiments, Génie civil : nous répondons avec variantes optimisées, phasage clair et planning réaliste.
            Transmission sécurisée des documents (PDF, DWG, ZIP…).
          </p>
          <div class="mt-6 flex flex-wrap gap-2">
            <span class="chip">Méthodes & phasage</span><span class="chip">Traçabilité</span><span class="chip">Qualité & Sécurité</span>
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

    <!-- CONTENU -->
    <section class="py-8 md:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-6">
        <!-- Aide / Checklist -->
        <aside class="lg:col-span-4">
          <div class="card ring-gold p-6 md:p-7" v-reveal>
            <h2 class="section-title text-left">Checklist DCE</h2>
            <ul class="mt-4 space-y-2 text-white/85">
              <li>• Cahier des charges / CCTP</li>
              <li>• Plans (PDF/DWG)</li>
              <li>• Estimatif / BPU</li>
              <li>• Délai souhaité & phasage particulier</li>
              <li>• Contraintes d’accès / site occupé</li>
            </ul>
            <div class="mt-5 grid gap-2 text-sm text-white/70">
              <div><span class="font-semibold text-white">Formats :</span> PDF, ZIP/RAR/7Z, DOC(X), XLS(X), DWG, DXF</div>
              <div><span class="font-semibold text-white">Limites :</span> {{ MAX_FILES }} fichiers · {{ MAX_FILE_MB }} Mo/fichier</div>
            </div>
          </div>
        </aside>

        <!-- Formulaire RFP -->
        <div class="lg:col-span-8">
          <form class="card ring-gold p-6 md:p-8 grid gap-4" @submit.prevent="submitRfp" aria-describedby="rfp-status" v-reveal>
            <h2 class="section-title text-left">Transmettre un appel d’offres</h2>

            <div v-if="Object.keys(form.errors).length" id="rfp-status" aria-live="assertive"
                 class="rounded-lg px-3 py-2 text-sm text-red-300 bg-red-500/10 ring-1 ring-red-500/20">
              Merci de vérifier les champs saisis.
            </div>

            <div class="grid md:grid-cols-2 gap-3">
              <input v-model="form.org"     type="text" class="field" placeholder="Organisation / Maîtrise d’ouvrage" required />
              <input v-model="form.contact" type="text" class="field" placeholder="Nom du contact" required />
            </div>
            <div class="grid md:grid-cols-2 gap-3">
              <input v-model="form.email" type="email" class="field" placeholder="Email" required />
              <input v-model="form.phone" type="tel"   class="field" placeholder="Téléphone" />
            </div>
            <div class="grid md:grid-cols-2 gap-3">
              <input v-model="form.title"     type="text" class="field" placeholder="Intitulé de l’AO" />
              <input v-model="form.reference" type="text" class="field" placeholder="Référence / N° dossier" />
            </div>
            <div class="grid md:grid-cols-2 gap-3">
              <input v-model="form.location" type="text" class="field" placeholder="Localisation (ex. Tanger Med)" />
              <input v-model="form.deadline" type="date" class="field" placeholder="Date limite" />
            </div>
            <textarea v-model="form.message" rows="4" class="field" placeholder="Précisions utiles (contraintes, variantes souhaitées, planning…)"></textarea>

            <div class="space-y-2">
              <label class="upload">
                <input type="file" multiple class="sr-only"
                       accept=".pdf,.zip,.rar,.7z,.doc,.docx,.xls,.xlsx,.dwg,.dxf"
                       @change="pickFiles" />
                <span class="upload-ui">Joindre DCE / plans — <span class="opacity-80">{{ form.files.length }} fichier(s)</span></span>
              </label>
              <div class="text-xs text-white/50">Formats supportés · Max {{ MAX_FILE_MB }} Mo/fichier · {{ MAX_FILES }} fichiers.</div>
              <div v-if="filesClientError" class="text-sm text-red-300">{{ filesClientError }}</div>
              <div v-if="form.files.length" class="flex flex-wrap gap-2 pt-1">
                <div v-for="(f,i) in form.files" :key="i" class="inline-flex items-center gap-2 px-2 h-8 rounded-lg bg-white/10 ring-1 ring-white/15">
                  <span class="truncate max-w-[18ch]">{{ f.name }}</span>
                  <span class="text-[11px] text-white/60">{{ formatSize(f.size) }}</span>
                  <button type="button" class="text-white/60 hover:text-red-300 text-xs" @click="removeFile(i)">✕</button>
                </div>
              </div>
            </div>

            <input v-model="form.hp" type="text" class="sr-only" tabindex="-1" autocomplete="off" />

            <div class="flex items-center gap-3 pt-1">
              <button type="submit" class="btn-outline-gold" :disabled="form.processing">
                {{ form.processing ? 'Envoi…' : 'Envoyer' }}
              </button>
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
.section-title{ position:relative; display:block; width:100%; font-weight:800; letter-spacing:-.01em; font-size:clamp(1.5rem,2vw + 1rem,2rem) }
.section-title::after{ content:""; display:block; height:2px; width:110px; margin:.7rem 0 0; background:linear-gradient(90deg,#dcc176,rgba(253,253,254,.6)); opacity:.85; border-radius:999px }
.tag-ghost{ display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .6rem; border-radius:.55rem; font-weight:800; font-size:.75rem; color:#fdfdfe; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.20); box-shadow: inset 0 1px 0 rgba(255,255,255,.25) }
.chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .7rem; border-radius:.7rem; color:#fdfdfe; font-weight:600; font-size:.9rem; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.14) }
.btn-outline-gold{ position:relative; display:inline-flex; align-items:center; justify-content:center; padding:.8rem 1.15rem; border-radius:1rem; font-weight:800; color:#fdfdfe; border:1px solid transparent; transition: transform .2s }
.btn-outline-gold::before{ content:""; position:absolute; inset:0; border-radius:inherit; padding:1px; background:linear-gradient(120deg,rgba(220,193,118,.9),rgba(255,255,255,.18),rgba(220,193,118,.9)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite:exclude; border:1px solid transparent }
.btn-outline-gold:hover{ transform: translateY(-2px) }
.field{ width:100%; border-radius:.75rem; padding:.75rem .9rem; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.14); color:#fdfdfe; outline:none; position:relative; z-index:1 }
.field:focus{ outline:2px solid rgba(220,193,118,.6); outline-offset:2px }
.upload{ display:inline-flex; border-radius:.8rem; border:1px dashed rgba(220,193,118,.5); color:#fdfdfe; position:relative; z-index:1 }
.upload .upload-ui{ width:100%; padding:.85rem .95rem; border-radius:.8rem; background:rgba(220,193,118,.1) }

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
