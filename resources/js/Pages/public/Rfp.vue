<!-- resources/js/Pages/Public/Rfp.vue -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
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

/* Upload config (mêmes règles) */
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

/* ----- FORMULAIRE “DEVIS EXPRESS” ----- */
const qForm = useForm({
  name:'', email:'', phone:'', city:'',
  project_type:'', budget:'', desired_date:'', company:'',
  message:'', files:[] as File[], hp:''
})
let successMsg = ''
let filesClientError = ''

function processFilesList(list: FileList | File[]){
  const picked = Array.from(list)
  const out    = [...qForm.files]
  let error = ''

  for(const f of picked){
    if(out.length>=MAX_FILES){ error = `Maximum ${MAX_FILES} fichiers.`; break }
    const okType = ALLOWED.includes(f.type) || hasAllowedExt(f.name)
    const okSize = f.size <= MAX_FILE_MB*1024*1024
    if(!okType){ error = `Format non autorisé: ${f.name}`; break }
    if(!okSize){ error = `Taille > ${MAX_FILE_MB} Mo: ${f.name}`; break }
    out.push(f)
  }
  if(error){ filesClientError = error } else { filesClientError=''; qForm.files = out }
}

function pickFiles(e:Event){
  try{
    const input = e.target as HTMLInputElement; if(!input.files) return
    processFilesList(input.files)
    input.value=''
  }catch{ filesClientError='Impossible de lire le fichier. Réessayez.' }
}
function removeFile(i:number){ qForm.files.splice(i,1) }

/* Envoi (route OK + fallback) */
function submitQuote(){
  filesClientError=''
  successMsg=''
  qForm.post(r('forms.quote.store', {}, false, '/devis'), {
    preserveScroll:true, forceFormData:true,
    onSuccess:()=>{
      successMsg = 'Merci, votre demande a bien été envoyée. Nous revenons vers vous rapidement.'
      const keep = { email:qForm.email, name:qForm.name, phone:qForm.phone }
      qForm.reset('city','project_type','budget','desired_date','company','message','files','hp')
      qForm.email = keep.email; qForm.name = keep.name; qForm.phone = keep.phone
    }
  })
}

/* Focus ancré “Devis express” */
function focusDevis(){
  const formEl = document.getElementById('devis')
  formEl?.scrollIntoView({ behavior:'smooth', block:'start' })
  setTimeout(()=>{ (document.getElementById('devis-name') as HTMLInputElement|undefined)?.focus() }, 420)
}
</script>

<template>
  <PublicLayout>
    <!-- HERO : texte gauche / 3D centre / texte droite -->
    <section class="relative overflow-hidden pt-14 md:pt-20 pb-10 md:pb-14">
      <div class="hero-ribbon" aria-hidden="true"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <!-- Colonne gauche -->
        <div class="lg:col-span-4" v-reveal>
          <span class="tag-ghost">Appels d’offres</span>
          <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
            Déposez votre <span class="text-bk-gold">DCE</span> — chiffrage clair & rapide
          </h1>
          <p class="mt-4 text-white/80">
            VRD, Bâtiments, Génie civil : variantes optimisées, phasage net, planning réaliste. Transmission sécurisée des documents.
          </p>
          <div class="mt-6 flex flex-wrap gap-2">
            <span class="chip">Méthodes & phasage</span>
            <span class="chip">Traçabilité</span>
            <span class="chip">Qualité & Sécurité</span>
          </div>
        </div>

        <!-- 3D CENTRALE -->
        <div class="lg:col-span-4 relative order-first lg:order-none" v-reveal>
          <div class="hero-halo" aria-hidden="true"></div>
          <div class="rfp3d" aria-hidden="true">
            <div class="grid-floor"></div>

            <div class="vault">
              <div class="ring r1"></div>
              <div class="ring r2"></div>
              <div class="ring r3"></div>
              <div class="shield"></div>
              <div class="shackle"></div>
              <div class="scan"></div>
              <div class="cloud"></div>
              <div class="arrow"></div>
            </div>

            <div class="bits">
              <span style="--i:0"></span><span style="--i:1"></span><span style="--i:2"></span>
              <span style="--i:3"></span><span style="--i:4"></span><span style="--i:5"></span>
              <span style="--i:6"></span><span style="--i:7"></span><span style="--i:8"></span>
            </div>
          </div>
        </div>

        <!-- Colonne droite -->
        <div class="lg:col-span-4" v-reveal>
          <div class="card ring-gold p-6 md:p-7">
            <h3 class="text-2xl font-extrabold">Pourquoi nous ?</h3>
            <div class="mini-bullets mt-3">
              <span>✔️ Métrés & variantes optimisées</span>
              <span>✔️ Délais & phasage détaillés</span>
              <span>✔️ Proposition claire & traçable</span>
              <span>✔️ Interventions partout au Maroc</span>
            </div>
            <div class="mt-6 flex gap-3">
              <a href="#rfp" class="btn-outline-gold">Transmettre un AO</a>
              <a href="#devis" class="btn-ghost" @click.prevent="focusDevis">Devis express</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION RFP -->
    <section id="rfp" class="py-10 md:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title" v-reveal>Appels d’offres & Devis</h2>

        <div class="mt-8 grid lg:grid-cols-2 gap-8 items-stretch">
          <!-- Carte info (boutons corrigés) -->
          <div class="card ring-gold p-6 md:p-8 flex flex-col justify-between min-h-[360px]" v-reveal>
            <div>
              <h3 class="text-2xl md:text-3xl font-extrabold">Envoyez votre DCE ou faites une demande express</h3>
              <p class="text-white/80 mt-3">
                Réponse rapide avec chiffrage fiable, variantes et phasage réaliste. Conseils techniques pour optimiser délais & budget.
              </p>
              <div class="mt-6 mini-bullets">
                <span>✔️ Métrés & variantes optimisées</span>
                <span>✔️ Délais & phasage détaillés</span>
                <span>✔️ Proposition claire et traçable</span>
                <span>✔️ Interventions partout au Maroc</span>
              </div>
            </div>
            <div class="mt-6 flex gap-3">
              <!-- route CONTACT corrigée -->
              <Link :href="r('public.contact', {}, false, '/contact')" class="btn-outline-gold">Nous contacter</Link>
              <!-- ancre + focus pour #devis -->
              <a href="#devis" class="btn-ghost" @click.prevent="focusDevis">Devis express</a>
            </div>
          </div>

          <!-- Formulaire -->
          <form
            id="devis"
            class="card ring-gold p-6 md:p-8 grid gap-4"
            @submit.prevent="submitQuote"
            aria-describedby="quote-status"
            v-reveal
          >
            <div
              v-if="successMsg"
              id="quote-status"
              aria-live="polite"
              class="rounded-lg px-3 py-2 text-sm text-emerald-300 bg-emerald-500/10 ring-1 ring-emerald-500/20"
            >{{ successMsg }}</div>

            <div
              v-else-if="Object.keys(qForm.errors).length"
              id="quote-status"
              aria-live="assertive"
              class="rounded-lg px-3 py-2 text-sm text-red-300 bg-red-500/10 ring-1 ring-red-500/20"
            >Merci de vérifier les champs saisis.</div>

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
                <option value="" disabled>Type de projet</option>
                <option>VRD / Voirie</option><option>Génie civil</option><option>Bâtiment</option><option>Ouvrage d’art</option><option>Autre</option>
              </select>
              <select v-model="qForm.budget" class="field">
                <option value="" disabled>Budget estimatif</option>
                <option>&lt; 1 M MAD</option><option>1–5 M MAD</option><option>5–20 M MAD</option><option>&gt; 20 M MAD</option>
              </select>
            </div>
            <div class="grid md:grid-cols-2 gap-3">
              <input v-model="qForm.desired_date" type="date" class="field" placeholder="Échéance souhaitée" />
              <input v-model="qForm.company" type="text" class="field" placeholder="Société (optionnel)" />
            </div>
            <textarea v-model="qForm.message" rows="3" placeholder="Votre besoin (lieu, délais, budget…)" class="field"></textarea>

            <div class="space-y-2">
              <label class="upload">
                <input
                  type="file"
                  multiple
                  accept=".pdf,.zip,.rar,.7z,.doc,.docx,.xls,.xlsx,.dwg,.dxf"
                  class="sr-only"
                  @change="pickFiles"
                />
                <span class="upload-ui">
                  Joindre DCE / plans — <span class="opacity-80">{{ qForm.files.length }} fichier(s)</span>
                </span>
              </label>
              <div class="text-xs text-white/50">
                Formats: PDF, ZIP, RAR, 7Z, DOC(X), XLS(X), DWG, DXF — Max 20 Mo / fichier, 10 fichiers.
              </div>

              <div v-if="filesClientError" class="text-sm text-red-300">{{ filesClientError }}</div>

              <div v-if="qForm.files.length" class="flex flex-wrap gap-2 pt-1">
                <div
                  v-for="(f,i) in qForm.files"
                  :key="i"
                  class="inline-flex items-center gap-2 px-2 h-8 rounded-lg bg-white/10 ring-1 ring-white/15"
                >
                  <span class="truncate max-w-[18ch]">{{ f.name }}</span>
                  <span class="text-[11px] text-white/60">{{ formatSize(f.size) }}</span>
                  <button type="button" class="text-white/60 hover:text-red-300 text-xs" @click="removeFile(i)">✕</button>
                </div>
              </div>
            </div>

            <!-- Honeypot anti-bot -->
            <input v-model="qForm.hp" type="text" class="sr-only" tabindex="-1" autocomplete="off" />

            <div class="flex items-center gap-3 pt-1">
              <button type="submit" class="btn-outline-gold" :disabled="qForm.processing">
                {{ qForm.processing ? 'Envoi…' : 'Envoyer' }}
              </button>
              <div v-if="qForm.progress" class="flex-1 h-1 rounded bg-white/10 overflow-hidden">
                <div class="h-1 bg-bk-gold transition-all" :style="{ width: `${qForm.progress.percentage}%` }"></div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<style scoped>
/* ===== Reveal */
.reveal{ opacity:0; transform: translateY(12px) scale(.985); filter: blur(2px); transition: all .6s cubic-bezier(.2,.8,.2,1) }
.reveal.is-visible{ opacity:1; transform:none; filter:none }

/* ===== Tokens */
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
.mini-bullets{ display:grid; gap:.35rem }
.mini-bullets span{ display:inline-flex; align-items:center; gap:.4rem; font-size:.95rem; color:#fdfdfe; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:.6rem; padding:.4rem .55rem }

/* Champs / Upload (contrastes améliorés) */
.field{ width:100%; border-radius:.75rem; padding:.75rem .9rem; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.14); color:#f5f7fb; outline:none; position:relative; z-index:1 }
.field::placeholder{ color:rgba(255,255,255,.70) }
.field:focus{ outline:2px solid rgba(220,193,118,.6); outline-offset:2px }
/* Lisibilité des <select> natifs : options foncées sur fond clair du système */
select.field{ color:#f5f7fb }
select.field option{ color:#0f141a; background:#ffffff }
select.field option[disabled]{ color:#6b7280 }
.upload{ display:inline-flex; border-radius:.8rem; border:1px dashed rgba(220,193,118,.5); color:#fdfdfe; position:relative; z-index:1 }
.upload .upload-ui{ width:100%; padding:.85rem .95rem; border-radius:.8rem; background:rgba(220,193,118,.1) }

/* ===== Hero 3D CENTRALE – “Vault Upload” */
.hero-ribbon{ position:absolute; left:0; right:0; top:-10px; height:36px; opacity:.45; transform: rotate(-2deg); background: repeating-linear-gradient(135deg,#2a3644 0 18px,#2a3644 18px 28px,#dcc176 28px 46px,#dcc176 46px 56px) }
.hero-halo{ position:absolute; inset:-40px; border-radius:2rem; filter:blur(36px); opacity:.55; background: radial-gradient(60% 60% at 25% 25%, #dcc176 0%, transparent 60%) }

.rfp3d{ position:relative; height:min(30rem,64vw); display:grid; place-items:center; perspective:1100px; transform-style:preserve-3d; filter: drop-shadow(0 24px 70px rgba(0,0,0,.45)) }
.rfp3d .grid-floor{
  position:absolute; inset:auto; bottom:-8px; width:94%; height:12rem; border-radius:1.2rem;
  transform: rotateX(62deg) rotateZ(-8deg);
  background:
    repeating-linear-gradient(0deg, rgba(255,255,255,.09) 0 2px, transparent 2px 12px),
    repeating-linear-gradient(90deg, rgba(255,255,255,.09) 0 2px, transparent 2px 12px),
    linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.04));
  border:1px solid rgba(255,255,255,.12);
}

.vault{
  position:relative; width:min(22rem,70%); aspect-ratio:1/1; border-radius:999px;
  transform: translateZ(90px) rotateX(14deg);
  background: radial-gradient(60% 60% at 50% 45%, rgba(255,255,255,.06), rgba(255,255,255,.02));
  border:1px solid rgba(255,255,255,.18);
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
}
.vault .ring{ position:absolute; inset:10%; border-radius:999px; border:1px solid rgba(255,255,255,.16); }
.vault .ring.r1{ inset:12%; animation: pulse 4.8s ease-in-out infinite }
.vault .ring.r2{ inset:18%; animation: pulse 5.2s ease-in-out infinite .2s }
.vault .ring.r3{ inset:24%; animation: pulse 5.6s ease-in-out infinite .4s }

.vault .shield{
  position:absolute; inset:32% 32% 34% 32%; border-radius:20% 20% 40% 40%;
  background: linear-gradient(160deg, rgba(255,255,255,.22), rgba(21,30,39,.55));
  border:1px solid rgba(255,255,255,.18);
  box-shadow: 0 20px 60px -20px rgba(0,0,0,.6), inset 0 0 0 1px rgba(255,255,255,.06);
}
.vault .shackle{
  position:absolute; width:42%; height:22%; left:29%; top:22%; border:3px solid #dcc176; border-bottom:none; border-radius:60px 60px 0 0;
  filter: drop-shadow(0 0 14px rgba(220,193,118,.5));
}
.vault .scan{
  position:absolute; inset:14%; border-radius:999px;
  background: conic-gradient(from 0deg, rgba(220,193,118,.0) 0 80%, rgba(220,193,118,.6) 90%, rgba(220,193,118,.0) 100%);
  animation: sweep 6s cubic-bezier(.2,.8,.2,1) infinite; mix-blend-mode: screen;
}
.vault .cloud{
  position:absolute; left:50%; bottom:18%; width:42%; height:26%; transform:translateX(-50%);
  background:
    radial-gradient(40% 40% at 35% 50%, rgba(255,255,255,.85), transparent 60%),
    radial-gradient(45% 45% at 60% 55%, rgba(255,255,255,.75), transparent 60%);
  filter: blur(4px); opacity:.65; border-radius:999px;
}
.vault .arrow{
  position:absolute; left:50%; top:52%; width:0; height:0; transform:translateX(-50%);
  border-left:10px solid transparent; border-right:10px solid transparent; border-top:14px solid #dcc176;
  filter: drop-shadow(0 0 10px rgba(220,193,118,.55));
  animation: bounce 1.8s ease-in-out infinite;
}

.bits{ position:absolute; inset:0; transform: translateZ(120px) rotateX(14deg); pointer-events:none }
.bits span{
  --i:0; position:absolute; left:calc(20% + var(--i)*8%); bottom:24%;
  width:6px; height:10px; background:#dcc176; border-radius:3px;
  filter: drop-shadow(0 0 6px rgba(220,193,118,.6));
  animation: rise 3s ease-in-out infinite; animation-delay: calc(var(--i)*.18s);
}

/* Animations */
@keyframes pulse { 0%,100%{ transform: scale(1) } 50%{ transform: scale(1.03) } }
@keyframes sweep { to{ transform: rotateZ(360deg) } }
@keyframes bounce { 0%,100%{ transform: translate(-50%,0) } 50%{ transform: translate(-50%,-6px) } }
@keyframes rise {
  0%{ transform: translateY(0); opacity:.8 }
  60%{ opacity:.3 }
  100%{ transform: translateY(-30px); opacity:0 }
}

/* Utils */
.text-bk-gold{ color:#dcc176 }
.bg-bk-gold{ background:#dcc176 }
</style>
