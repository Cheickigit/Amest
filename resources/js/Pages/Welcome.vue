<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

/* Helper route robuste */
function r(name: string, params?: any, absolute = false, fallback: string = '#') {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (!Z) return fallback
    return ziggyRoute(name, params ?? {}, absolute, Z)
  } catch { return fallback }
}

/* Types & props */
type ActivityItem = { type:'image'|'video', kind?:'upload'|'url', path?:string|null, url?:string|null }
type Activity   = { id:number; title:string; slug?:string|null; location?:string|null; category?:string|null; status:'brouillon'|'publié'; cover_image?:string|null; media?:ActivityItem[]; partner?:string|null; organization?:string|null }
type Post      = { id:number; title:string; slug?:string|null; excerpt?:string|null; cover_image?:string|null; published_at?:string|null }
type FaqItem   = { question:string; answer:string }

const props = defineProps<{ heroActivities?:Activity[]|null; activities?:Activity[]|null; posts?:Post[]|null; faqs?:FaqItem[]|null }>()

const heroList = computed<Activity[]>(() => props.heroActivities ?? [])
const gridList = computed<Activity[]>(() => props.activities ?? [])
const postList = computed<Post[]>(() => props.posts ?? [])

/* Partenaires */
const partnerList = computed(() => {
  const base = ['Université de Sousse','Université de Monastir','Campus Mahdia']
  const baseLower = base.map(s=>s.toLowerCase())
  const seen = new Set<string>()
  ;[...heroList.value,...gridList.value].forEach(p=>{
    const n = (p?.partner || p?.organization || '').toString().trim()
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

/* Tilt 3D (domaines) */
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

/* Formulaire de contact */
const cForm = useForm({ name:'',email:'',phone:'', subject:'', message:'', hp:'' })
const successMsg = ref('')

function submitContact(){
  successMsg.value = ''
  cForm.post(r('forms.contact.store', {}, false, '/contact'), {
    preserveScroll:true,
    onSuccess:()=>{
      successMsg.value = 'Merci, votre message a été envoyé. Nous vous recontacterons très vite.'
      const keepEmail = cForm.email
      cForm.reset('name','phone','subject','message','hp')
      cForm.email = keepEmail
    }
  })
}

/* FAQ */
const faqList = computed<FaqItem[]>(() => props.faqs && props.faqs.length ? props.faqs : [
  { question:'Quelle est la mission de AMEST-Sahel ?', answer:'Rassembler et soutenir les étudiants et stagiaires maliens en Tunisie à travers des activités sociales, culturelles et éducatives.' },
  { question:'Comment puis-je devenir membre ?', answer:'Contactez-nous via le formulaire ou rejoignez-nous lors de nos événements. Tous les étudiants maliens sont les bienvenus !' },
  { question:'Quelles activités organisez-vous ?', answer:'Sorties culturelles, soutien scolaire, événements sportifs, ruptures de jeûne, et bien plus encore.' },
  { question:'Où se situent vos locaux ?', answer:'Nous intervenons principalement dans la région du Sahel tunisien : Sousse, Monastir, Mahdia et leurs environs.' },
])

/* util */
function fmt(s?:string){ if(!s) return ''; const d=new Date(s); return isNaN(+d)?(s||''):d.toLocaleDateString('fr-FR',{day:'2-digit',month:'long',year:'numeric'}) }
</script>

<template>
  <PublicLayout>
    <!-- HERO -->
    <section class="relative overflow-hidden pt-12 md:pt-16 pb-10 md:pb-12 bg-gradient-to-br from-green-600 to-green-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-6" v-reveal>
          <span class="tag-ghost">Association étudiante malienne · Tunisie</span>
          <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight tracking-tight text-white">
            Solidarité & Réussite — <span class="text-yellow-300">AMEST Sahel</span>
          </h1>
          <p class="mt-4 text-green-100">
            Rassembler, soutenir et accompagner les étudiants et stagiaires maliens
            dans la région du Sahel tunisien pour une expérience académique épanouissante.
          </p>
          <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="#activites" class="btn-outline-yellow">Nos Activités</a>
            <a href="#contact" class="btn-ghost-light">Nous Rejoindre</a>
          </div>
          <div class="mt-5 flex flex-wrap gap-2">
            <span class="chip-light">Étudiants</span><span class="chip-light">Stagiaires</span><span class="chip-light">Solidarité</span><span class="chip-light">Culture</span><span class="chip-light">Éducation</span>
          </div>
        </div>

        <!-- Illustration communauté -->
        <div class="lg:col-span-6 relative" v-reveal>
          <div class="relative z-10">
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20 shadow-2xl">
              <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-yellow-500/20 rounded-2xl p-4 text-center border border-yellow-500/30">
                  <div class="text-2xl mb-2">🎓</div>
                  <div class="text-white font-semibold text-sm">Étudiants</div>
                </div>
                <div class="bg-red-600/20 rounded-2xl p-4 text-center border border-red-600/30">
                  <div class="text-2xl mb-2">💼</div>
                  <div class="text-white font-semibold text-sm">Stagiaires</div>
                </div>
                <div class="bg-green-500/20 rounded-2xl p-4 text-center border border-green-500/30">
                  <div class="text-2xl mb-2">🤝</div>
                  <div class="text-white font-semibold text-sm">Solidarité</div>
                </div>
                <div class="bg-yellow-500/20 rounded-2xl p-4 text-center border border-yellow-500/30">
                  <div class="text-2xl mb-2">🌍</div>
                  <div class="text-white font-semibold text-sm">Intégration</div>
                </div>
              </div>

              <div class="text-center">
                <div class="text-white font-bold text-lg mb-2">Communauté Unie</div>
                <div class="text-green-100 text-sm">Étudiants maliens en Tunisie</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- NOTRE MISSION -->
    <section id="mission" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" v-reveal>
        <div class="relative overflow-hidden rounded-2xl border border-green-200 bg-gradient-to-br from-green-50 to-yellow-50 shadow-xl">
          <div class="relative grid gap-6 p-6 md:p-8 lg:p-10 lg:grid-cols-12">
            <div class="lg:col-span-7">
              <h2 class="text-left text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900">
                Notre <span class="text-green-600">Mission</span>
                <span class="mx-2 text-gray-400">—</span>
                Notre <span class="text-yellow-600">Vision</span>
              </h2>

              <p class="mt-4 text-gray-700 max-w-prose leading-relaxed">
                L'AMEST-Sahel œuvre pour créer un environnement favorable à l'épanouissement académique,
                social et culturel des étudiants et stagiaires maliens en Tunisie. Nous croyons en une
                communauté unie où l'entraide et le partage sont les clés de la réussite.
              </p>

              <div class="mt-4 flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-lg border border-green-200 bg-white px-3 py-1 text-xs font-semibold text-green-700">Solidarité active</span>
                <span class="inline-flex items-center rounded-lg border border-yellow-200 bg-white px-3 py-1 text-xs font-semibold text-yellow-700">Développement durable</span>
                <span class="inline-flex items-center rounded-lg border border-red-200 bg-white px-3 py-1 text-xs font-semibold text-red-700">Éducation populaire</span>
              </div>

              <div class="mt-6 grid gap-3">
                <div>
                  <div class="mb-1 flex items-center justify-between text-sm text-gray-700">
                    <span>Étudiants accompagnés</span>
                    <span class="font-extrabold text-green-600">98%</span>
                  </div>
                  <div class="relative h-2 w-full overflow-hidden rounded-full border border-green-200 bg-green-100">
                    <span class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-green-500 to-green-600"
                          style="width:98%"></span>
                  </div>
                </div>
                <div>
                  <div class="mb-1 flex items-center justify-between text-sm text-gray-700">
                    <span>Satisfaction des membres</span>
                    <span class="font-extrabold text-green-600">95%</span>
                  </div>
                  <div class="relative h-2 w-full overflow-hidden rounded-full border border-green-200 bg-green-100">
                    <span class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-green-500 to-green-600"
                          style="width:95%"></span>
                  </div>
                </div>
              </div>

              <div class="mt-6">
                <Link :href="r('public.about', {}, false, '/a-propos')"
                      class="inline-flex items-center justify-center rounded-xl border border-green-600 px-4 py-2 text-green-700 transition hover:bg-green-600 hover:text-white">
                  Découvrir notre histoire
                </Link>
              </div>
            </div>

            <div class="lg:col-span-5 relative">
              <div class="pointer-events-none absolute right-[-52px] top-2 w-[220px] rotate-[35deg] transform-gpu bg-gradient-to-r from-green-600 to-yellow-500 text-white font-black tracking-wider text-center border border-white/25 shadow-lg">
                <span class="block py-1.5">SOLIDARITÉ</span>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div class="relative rounded-xl border border-green-200 bg-white p-4 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                  <div class="text-2xl font-black bg-gradient-to-r from-green-600 to-green-700 bg-clip-text text-transparent">
                    <span v-countup="150">150</span>+
                  </div>
                  <div class="mt-1 text-sm text-gray-600">Membres actifs</div>
                </div>

                <div class="relative rounded-xl border border-yellow-200 bg-white p-4 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                  <div class="text-2xl font-black bg-gradient-to-r from-yellow-600 to-yellow-700 bg-clip-text text-transparent">
                    <span v-countup="25">25</span>+
                  </div>
                  <div class="mt-1 text-sm text-gray-600">Activités annuelles</div>
                </div>

                <div class="relative rounded-xl border border-red-200 bg-white p-4 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                  <div class="text-2xl font-black bg-gradient-to-r from-red-600 to-red-700 bg-clip-text text-transparent">
                    <span v-countup="6">6</span>+
                  </div>
                  <div class="mt-1 text-sm text-gray-600">Ans d'engagement</div>
                </div>

                <div class="relative rounded-xl border border-green-200 bg-white p-4 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                  <div class="text-2xl font-black bg-gradient-to-r from-green-600 to-yellow-600 bg-clip-text text-transparent">
                    <span v-countup="15">15</span>+
                  </div>
                  <div class="mt-1 text-sm text-gray-600">Partenariats</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ACTIVITÉS -->
    <section id="activites" class="py-20 bg-gradient-to-br from-green-50 to-yellow-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title text-gray-900" v-reveal>Nos <span class="text-green-600">Activités</span></h2>
        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <div v-for="activity in [
            { title: 'Journée d\'intégration', date: '01 Février 2025', location: 'Mahdia', category: 'social' },
            { title: 'Sortie au Zoo de Friguia', date: '22 Février 2025', location: 'Friguia', category: 'culture' },
            { title: 'Rupture de jeûne Sousse', date: '15 Mars 2025', location: 'Sousse', category: 'social' },
            { title: 'Rupture de jeûne Mahdia', date: '22 Mars 2025', location: 'Mahdia', category: 'social' },
            { title: 'La nuit des communautés', date: '12 Avril 2025', location: 'Sousse', category: 'culture' },
            { title: 'Formation Power BI', date: '26 Avril 2025', location: 'Sousse', category: 'education' }
          ]" :key="activity.title"
               class="bg-white rounded-2xl p-6 shadow-lg border border-green-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
               v-reveal>
            <div class="flex items-center justify-between mb-4">
              <span class="text-2xl">
                {{ activity.category === 'social' ? '🤝' : activity.category === 'culture' ? '🎭' : '📚' }}
              </span>
              <span class="px-3 py-1 rounded-full text-xs font-semibold"
                    :class="activity.category === 'social' ? 'bg-green-100 text-green-700' : activity.category === 'culture' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'">
                {{ activity.category === 'social' ? 'Social' : activity.category === 'culture' ? 'Culture' : 'Éducation' }}
              </span>
            </div>
            <h4 class="font-bold text-gray-900 mb-2">{{ activity.title }}</h4>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
              <span>📅</span>
              <span>{{ activity.date }}</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <span>📍</span>
              <span>{{ activity.location }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- NOUS CONTACTER -->
    <section id="contact" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title text-gray-900" v-reveal>Nous <span class="text-green-600">Contacter</span></h2>

        <div class="mt-8 grid lg:grid-cols-2 gap-8">
          <!-- Informations de contact -->
          <div class="card-contact" v-reveal>
            <h3 class="text-2xl font-bold text-gray-900 mb-6">📞 Restons en contact</h3>

            <div class="space-y-4 mb-8">
              <div class="flex items-center gap-4 p-4 bg-green-50 rounded-2xl border border-green-200">
                <div class="w-12 h-12 bg-green-600 rounded-2xl flex items-center justify-center text-white">
                  <span class="text-xl">📧</span>
                </div>
                <div>
                  <div class="font-semibold text-gray-900">Email</div>
                  <a href="mailto:amestsahel04@gmail.com" class="text-green-700 hover:text-green-800">
                    amestsahel04@gmail.com
                  </a>
                </div>
              </div>

              <div class="flex items-center gap-4 p-4 bg-yellow-50 rounded-2xl border border-yellow-200">
                <div class="w-12 h-12 bg-yellow-500 rounded-2xl flex items-center justify-center text-white">
                  <span class="text-xl">📱</span>
                </div>
                <div>
                  <div class="font-semibold text-gray-900">Téléphone</div>
                  <a href="tel:24282332" class="text-yellow-700 hover:text-yellow-800">
                    24 282 332
                  </a>
                </div>
              </div>

              <div class="flex items-center gap-4 p-4 bg-red-50 rounded-2xl border border-red-200">
                <div class="w-12 h-12 bg-red-600 rounded-2xl flex items-center justify-center text-white">
                  <span class="text-xl">📍</span>
                </div>
                <div>
                  <div class="font-semibold text-gray-900">Localisation</div>
                  <span class="text-red-700">Tunisie - Région Sahel</span>
                </div>
              </div>
            </div>

            <h4 class="text-xl font-bold mb-4 text-gray-900">🌐 Nos Réseaux</h4>
            <div class="grid grid-cols-3 gap-4">
              <a href="#" class="bg-green-100 rounded-2xl p-4 text-center border border-green-200 hover:bg-green-200 transition-colors">
                <div class="text-2xl mb-2">📘</div>
                <div class="text-sm text-green-700">Facebook</div>
              </a>
              <a href="#" class="bg-yellow-100 rounded-2xl p-4 text-center border border-yellow-200 hover:bg-yellow-200 transition-colors">
                <div class="text-2xl mb-2">📷</div>
                <div class="text-sm text-yellow-700">Instagram</div>
              </a>
              <a href="#" class="bg-red-100 rounded-2xl p-4 text-center border border-red-200 hover:bg-red-200 transition-colors">
                <div class="text-2xl mb-2">📺</div>
                <div class="text-sm text-red-700">YouTube</div>
              </a>
            </div>
          </div>

          <!-- Formulaire de contact -->
          <div class="card-contact" v-reveal>
            <h3 class="text-2xl font-bold text-gray-900 mb-6">💬 Envoyez-nous un message</h3>

            <form @submit.prevent="submitContact" class="space-y-6">
              <div v-if="successMsg" class="bg-green-50 border border-green-200 rounded-2xl p-4 text-green-700">
                {{ successMsg }}
              </div>

              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Votre nom</label>
                  <input v-model="cForm.name" type="text" required
                         class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all">
                </div>
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Votre email</label>
                  <input v-model="cForm.email" type="email" required
                         class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all">
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sujet</label>
                <select v-model="cForm.subject" required
                        class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all">
                  <option value="">Choisir un sujet</option>
                  <option>Devenir membre</option>
                  <option>Participer aux activités</option>
                  <option>Demande d'information</option>
                  <option>Partenariat</option>
                  <option>Autre</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Votre message</label>
                <textarea v-model="cForm.message" rows="5" required
                          class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all"></textarea>
              </div>

              <input v-model="cForm.hp" type="text" class="sr-only" tabindex="-1" autocomplete="off">

              <button type="submit" :disabled="cForm.processing"
                      class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-2xl font-semibold hover:shadow-lg transition-all duration-300 disabled:opacity-50">
                {{ cForm.processing ? 'Envoi en cours...' : 'Envoyer le message' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<style scoped>
/* Styles pour la page AMEST-Sahel */
.section-title {
  position: relative;
  display: block;
  width: 100%;
  font-weight: 800;
  text-align: center;
  letter-spacing: -.01em;
  font-size: clamp(1.65rem, 2vw + 1rem, 2.25rem);
  margin-bottom: 3rem;
}

.section-title::after {
  content: "";
  display: block;
  height: 2px;
  width: 110px;
  margin: .7rem auto 0;
  background: linear-gradient(90deg, #059669, #d97706, #dc2626);
  opacity: .85;
  border-radius: 999px;
}

.tag-ghost {
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  padding: .3rem .6rem;
  border-radius: .55rem;
  font-weight: 800;
  font-size: .75rem;
  color: #ffffff;
  background: rgba(255, 255, 255, .10);
  border: 1px solid rgba(255, 255, 255, .20);
}

.btn-outline-yellow {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: .8rem 1.15rem;
  border-radius: 1rem;
  font-weight: 800;
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, .30);
  transition: all .2s;
}

.btn-outline-yellow:hover {
  background: rgba(255, 255, 255, .10);
  transform: translateY(-1px);
}

.btn-ghost-light {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: .75rem 1.05rem;
  border-radius: 1rem;
  border: 1px solid rgba(255, 255, 255, .30);
  color: #ffffff;
  transition: border-color .2s, transform .2s;
}

.btn-ghost-light:hover {
  border-color: rgba(255, 255, 255, .60);
  transform: translateY(-1px);
}

.chip-light {
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  padding: .45rem .7rem;
  border-radius: .7rem;
  color: #ffffff;
  font-weight: 600;
  font-size: .9rem;
  background: rgba(255, 255, 255, .10);
  border: 1px solid rgba(255, 255, 255, .20);
}

.card-contact {
  background: white;
  border-radius: 1.5rem;
  padding: 2rem;
  box-shadow: 0 10px 40px -20px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.reveal {
  opacity: 0;
  transform: translateY(12px) scale(.985);
  filter: blur(2px);
  transition: all .6s cubic-bezier(.2,.8,.2,1);
}

.reveal.is-visible {
  opacity: 1;
  transform: none;
  filter: none;
}
</style>
