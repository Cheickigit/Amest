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
type Activity = {
  id: number;
  title: string;
  date: string;
  location: string;
  summary: string;
  cover_image?: string|null;
  category: 'culture'|'education'|'sport'|'social';
}

type TeamMember = {
  name: string;
  role: string;
  formation: string;
  email: string;
  photo?: string;
}

type FormerPresident = {
  name: string;
  mandate: string;
}

const props = defineProps<{
  heroActivities?: Activity[]|null;
  activities?: Activity[]|null;
  team?: TeamMember[]|null;
  formerPresidents?: FormerPresident[]|null;
}>()

/* Données de l'association */
const associationInfo = {
  name: "AMEST-Sahel",
  fullName: "Association Malienne des Étudiants et Stagiaires en Tunisie section Sahel",
  description: "Organisation qui vise à rassembler les étudiants et stagiaires maliens vivant en Tunisie, en mettant en avant la solidarité, l'intégration et le développement académique et professionnel.",
  mission: "Favoriser l'entraide et l'intégration des étudiants et stagiaires maliens au sahel de la Tunisie tout en créant un environnement propice à leur épanouissement.",
  values: ["Solidarité", "Engagement", "Respect", "Excellence"],
  contact: {
    email: "amestsahel04@gmail.com",
    phone: "24282332",
    facebook: "amest sahel",
    instagram: "Amest_sahel",
    youtube: "Amest-talk"
  }
}

/* Listes calculées */
const heroList = computed<Activity[]>(() => props.heroActivities ?? [])
const activityList = computed<Activity[]>(() => props.activities ?? [])
const teamList = computed<TeamMember[]>(() => props.team ?? [])
const presidentsList = computed<FormerPresident[]>(() => props.formerPresidents ?? [])

/* Animation reveal */
let io: IntersectionObserver|null = null
const vReveal = {
  mounted(el: HTMLElement) {
    el.classList.add('reveal')
    if (!io) {
      io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
          if (e.isIntersecting) {
            e.target.classList.add('is-visible')
            io?.unobserve(e.target)
          }
        })
      }, { threshold: .14 })
    }
    io.observe(el)
  }
}

/* Compteurs animés */
const vCountup = {
  mounted(el: HTMLElement, binding: { value: number }) {
    const target = Number(binding.value ?? 0)
    el.textContent = '0'
    const obs = new IntersectionObserver((ents) => {
      if (!ents[0].isIntersecting) return
      obs.disconnect()
      const dur = 1200, t0 = performance.now()
      const tick = (t: number) => {
        const p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3)
        el.textContent = Math.round(target * e).toLocaleString('fr-FR')
        if (p < 1) requestAnimationFrame(tick)
      }
      requestAnimationFrame(tick)
    }, { threshold: .35 })
    obs.observe(el)
  }
}

/* Effet tilt sur les cartes */
function handleTilt(e: MouseEvent) {
  const el = e.currentTarget as HTMLElement|undefined
  if (!el) return
  const r = el.getBoundingClientRect()
  const cx = (e.clientX - r.left) / r.width - .5
  const cy = (e.clientY - r.top) / r.height - .5
  el.style.setProperty('--rx', (-cy * 8).toFixed(2) + 'deg')
  el.style.setProperty('--ry', (cx * 8).toFixed(2) + 'deg')
}

function resetTilt(e: MouseEvent) {
  const el = e.currentTarget as HTMLElement|undefined
  if (!el) return
  el.style.setProperty('--rx', '0deg')
  el.style.setProperty('--ry', '0deg')
}

/* Formulaire de contact */
const cForm = useForm({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: '',
  hp: ''
})

const successMsg = ref('')

function submitContact() {
  successMsg.value = ''
  cForm.post(r('contact.store', {}, false, '/contact'), {
    preserveScroll: true,
    onSuccess: () => {
      successMsg.value = 'Merci pour votre message ! Nous vous répondrons très rapidement.'
      const keepEmail = cForm.email
      cForm.reset('name', 'phone', 'subject', 'message', 'hp')
      cForm.email = keepEmail
    }
  })
}

/* Utilitaires */
function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

function getCategoryColor(category: string) {
  const colors = {
    culture: 'from-purple-500 to-pink-500',
    education: 'from-blue-500 to-cyan-500',
    sport: 'from-green-500 to-emerald-500',
    social: 'from-orange-500 to-red-500'
  }
  return colors[category as keyof typeof colors] || 'from-gray-500 to-gray-700'
}

function getCategoryIcon(category: string) {
  const icons = {
    culture: '🎭',
    education: '📚',
    sport: '⚽',
    social: '🤝'
  }
  return icons[category as keyof typeof icons] || '🌟'
}
</script>

<template>
  <PublicLayout>
    <!-- HERO SECTION -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-mali-blue via-mali-green to-mali-gold">
      <!-- Arrière-plan décoratif -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-current rounded-full mix-blend-overlay filter blur-xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-current rounded-full mix-blend-overlay filter blur-xl animate-pulse delay-1000"></div>
      </div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-center py-20">
        <!-- Contenu principal -->
        <div class="text-center lg:text-left" v-reveal>
          <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-6">
            <span class="w-2 h-2 bg-mali-gold rounded-full animate-pulse"></span>
            <span class="text-sm font-semibold text-white">Association étudiante malienne</span>
          </div>

          <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
            <span class="block">AMEST</span>
            <span class="block text-mali-gold">Sahel</span>
          </h1>

          <p class="text-xl md:text-2xl text-white/90 mb-8 leading-relaxed">
            Association Malienne des Étudiants et Stagiaires en Tunisie<br>
            <span class="text-mali-gold font-semibold">Section Sahel</span>
          </p>

          <p class="text-lg text-white/80 mb-8 max-w-2xl">
            Rassembler, soutenir et faire rayonner la communauté étudiante malienne
            en Tunisie à travers la solidarité, l'intégration et le développement.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
            <a href="#activites" class="btn-primary group">
              <span>Découvrir nos activités</span>
              <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
            <a href="#contact" class="btn-secondary">
              Nous rejoindre
            </a>
          </div>

          <!-- Statistiques -->
          <div class="flex flex-wrap gap-6 mt-12 justify-center lg:justify-start">
            <div class="text-center">
              <div class="text-2xl md:text-3xl font-bold text-white mb-1">
                <span v-countup="150">150</span>+
              </div>
              <div class="text-sm text-white/70">Membres actifs</div>
            </div>
            <div class="text-center">
              <div class="text-2xl md:text-3xl font-bold text-white mb-1">
                <span v-countup="25">25</span>+
              </div>
              <div class="text-sm text-white/70">Activités annuelles</div>
            </div>
            <div class="text-center">
              <div class="text-2xl md:text-3xl font-bold text-white mb-1">
                <span v-countup="6">6</span>+
              </div>
              <div class="text-sm text-white/70">Ans d'engagement</div>
            </div>
          </div>
        </div>

        <!-- Illustration -->
        <div class="relative" v-reveal>
          <div class="relative z-10">
            <!-- Carte principale -->
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20 shadow-2xl">
              <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-mali-green/20 rounded-2xl p-4 text-center border border-mali-green/30">
                  <div class="text-2xl mb-2">🎓</div>
                  <div class="text-white font-semibold text-sm">Étudiants</div>
                </div>
                <div class="bg-mali-gold/20 rounded-2xl p-4 text-center border border-mali-gold/30">
                  <div class="text-2xl mb-2">💼</div>
                  <div class="text-white font-semibold text-sm">Stagiaires</div>
                </div>
                <div class="bg-mali-blue/20 rounded-2xl p-4 text-center border border-mali-blue/30">
                  <div class="text-2xl mb-2">🤝</div>
                  <div class="text-white font-semibold text-sm">Solidarité</div>
                </div>
                <div class="bg-purple-500/20 rounded-2xl p-4 text-center border border-purple-500/30">
                  <div class="text-2xl mb-2">🌍</div>
                  <div class="text-white font-semibold text-sm">Intégration</div>
                </div>
              </div>

              <div class="text-center">
                <div class="text-white font-bold text-lg mb-2">Communauté Unie</div>
                <div class="text-white/70 text-sm">Étudiants maliens en Tunisie</div>
              </div>
            </div>

            <!-- Éléments flottants -->
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-mali-gold/20 rounded-2xl rotate-12 border border-mali-gold/30 backdrop-blur-sm"></div>
            <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-mali-green/20 rounded-2xl -rotate-12 border border-mali-green/30 backdrop-blur-sm"></div>
          </div>
        </div>
      </div>

      <!-- Vague décorative -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="w-full h-12">
          <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" class="fill-current text-white/10"></path>
          <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" class="fill-current text-white/10"></path>
          <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" class="fill-current text-white/10"></path>
        </svg>
      </div>
    </section>

    <!-- À PROPOS -->
    <section id="apropos" class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" v-reveal>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
            Notre <span class="text-mali-blue">Histoire</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Découvrez le parcours de l'AMEST-Sahel, de sa création à son rôle actuel
            dans la communauté étudiante malienne en Tunisie.
          </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12 items-center mb-16">
          <!-- Histoire -->
          <div v-reveal>
            <div class="bg-white rounded-3xl p-8 shadow-2xl border border-gray-100">
              <h3 class="text-2xl font-bold text-gray-900 mb-6">Notre Parcours</h3>
              <p class="text-gray-600 mb-6 leading-relaxed">
                Fondée en 2018 après avoir obtenu son autonomie du bureau central de l'AMEST,
                l'AMEST-Sahel a commencé comme un petit groupe d'étudiants maliens désireux
                de créer un espace d'entraide et de partage.
              </p>
              <p class="text-gray-600 leading-relaxed">
                Aujourd'hui, nous sommes une association dynamique qui œuvre pour le bien-être
                et la réussite de la communauté étudiante malienne dans la région du Sahel tunisien.
              </p>
            </div>
          </div>

          <!-- Mission et Valeurs -->
          <div v-reveal>
            <div class="space-y-6">
              <!-- Mission -->
              <div class="bg-gradient-to-r from-mali-blue to-mali-green rounded-2xl p-6 text-white">
                <h4 class="text-xl font-bold mb-3">🎯 Notre Mission</h4>
                <p class="text-white/90">
                  {{ associationInfo.mission }}
                </p>
              </div>

              <!-- Valeurs -->
              <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100">
                <h4 class="text-xl font-bold text-gray-900 mb-4">💎 Nos Valeurs</h4>
                <div class="grid grid-cols-2 gap-3">
                  <div v-for="(value, index) in associationInfo.values" :key="index"
                       class="flex items-center gap-3 p-3 rounded-lg bg-gray-50">
                    <div class="w-8 h-8 rounded-full bg-mali-gold/20 flex items-center justify-center">
                      <span class="text-mali-gold text-sm font-bold">{{ index + 1 }}</span>
                    </div>
                    <span class="font-semibold text-gray-700">{{ value }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Objectifs -->
        <div class="bg-white rounded-3xl p-8 shadow-2xl border border-gray-100" v-reveal>
          <h3 class="text-2xl font-bold text-center text-gray-900 mb-12">🎯 Nos Objectifs</h3>
          <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center" v-for="(goal, index) in [
              'Développer des initiatives éducatives pour soutenir la réussite académique',
              'Organiser des activités culturelles pour promouvoir le patrimoine malien',
              'Créer des opportunités professionnelles et de networking'
            ]" :key="index">
              <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-mali-blue to-mali-green flex items-center justify-center text-white text-2xl">
                {{ ['📚', '🎭', '💼'][index] }}
              </div>
              <h4 class="font-bold text-gray-900 mb-2">Objectif {{ index + 1 }}</h4>
              <p class="text-gray-600 text-sm">{{ goal }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ACTIVITÉS -->
    <section id="activites" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" v-reveal>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
            Nos <span class="text-mali-green">Activités</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Découvrez les événements et activités qui rythment la vie de notre association
            et renforcent les liens entre nos membres.
          </p>
        </div>

        <!-- Activités à venir -->
        <div class="mb-16" v-reveal>
          <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Prochaines Activités</h3>
          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="activity in activityList.slice(0, 6)" :key="activity.id"
                 class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                 @mousemove="handleTilt" @mouseleave="resetTilt">
              <div class="flex items-center justify-between mb-4">
                <span class="text-2xl">{{ getCategoryIcon(activity.category) }}</span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                      :class="`bg-gradient-to-r ${getCategoryColor(activity.category)} text-white`">
                  {{ activity.category }}
                </span>
              </div>
              <h4 class="font-bold text-gray-900 mb-2">{{ activity.title }}</h4>
              <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                <span>📅</span>
                <span>{{ formatDate(activity.date) }}</span>
              </div>
              <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <span>📍</span>
                <span>{{ activity.location }}</span>
              </div>
              <p class="text-gray-600 text-sm">{{ activity.summary }}</p>
            </div>
          </div>
        </div>

        <!-- Galerie photos -->
        <div v-reveal>
          <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Galerie Photos</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="i in 8" :key="i"
                 class="aspect-square bg-gray-200 rounded-2xl flex items-center justify-center text-gray-400">
              <span class="text-sm">Photo {{ i }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ÉQUIPE -->
    <section id="equipe" class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" v-reveal>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
            Notre <span class="text-mali-gold">Équipe</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Découvrez les membres dévoués qui font vivre l'association au quotidien
            et travaillent pour le bien-être de la communauté.
          </p>
        </div>

        <!-- Bureau exécutif -->
        <div class="mb-16" v-reveal>
          <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Bureau Exécutif</h3>
          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="member in teamList.slice(0, 6)" :key="member.email"
                 class="bg-white rounded-2xl p-6 text-center shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
              <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-mali-blue to-mali-green rounded-2xl flex items-center justify-center text-white text-xl font-bold">
                {{ member.name.split(' ').map(n => n[0]).join('') }}
              </div>
              <h4 class="font-bold text-gray-900 mb-1">{{ member.name }}</h4>
              <p class="text-mali-blue font-semibold text-sm mb-2">{{ member.role }}</p>
              <p class="text-gray-600 text-xs mb-3">{{ member.formation }}</p>
              <a :href="`mailto:${member.email}`" class="text-mali-green text-sm hover:underline">
                {{ member.email }}
              </a>
            </div>
          </div>
        </div>

        <!-- Anciens présidents -->
        <div v-reveal>
          <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Anciens Présidents</h3>
          <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="president in presidentsList" :key="president.name"
                   class="text-center p-4 rounded-2xl bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-mali-gold to-orange-500 rounded-2xl flex items-center justify-center text-white">
                  <span class="text-xl">👑</span>
                </div>
                <h4 class="font-bold text-gray-900 mb-1">{{ president.name }}</h4>
                <p class="text-mali-blue text-sm">{{ president.mandate }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CONTACT -->
    <section id="contact" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" v-reveal>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
            Restons <span class="text-mali-blue">Connectés</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Rejoignez notre communauté ou contactez-nous pour plus d'informations.
            Nous sommes toujours ravis d'accueillir de nouveaux membres !
          </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12">
          <!-- Informations de contact -->
          <div v-reveal>
            <div class="bg-gradient-to-br from-mali-blue to-mali-green rounded-3xl p-8 text-white">
              <h3 class="text-2xl font-bold mb-6">📞 Nous Contacter</h3>

              <div class="space-y-4 mb-8">
                <div class="flex items-center gap-4 p-4 bg-white/10 rounded-2xl backdrop-blur-sm">
                  <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                    <span class="text-xl">📧</span>
                  </div>
                  <div>
                    <div class="font-semibold">Email</div>
                    <a :href="`mailto:${associationInfo.contact.email}`" class="text-white/90 hover:text-white">
                      {{ associationInfo.contact.email }}
                    </a>
                  </div>
                </div>

                <div class="flex items-center gap-4 p-4 bg-white/10 rounded-2xl backdrop-blur-sm">
                  <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                    <span class="text-xl">📱</span>
                  </div>
                  <div>
                    <div class="font-semibold">Téléphone</div>
                    <a :href="`tel:${associationInfo.contact.phone}`" class="text-white/90 hover:text-white">
                      {{ associationInfo.contact.phone }}
                    </a>
                  </div>
                </div>
              </div>

              <h4 class="text-xl font-bold mb-4">🌐 Nos Réseaux</h4>
              <div class="grid grid-cols-3 gap-4">
                <a href="#" class="bg-white/10 rounded-2xl p-4 text-center backdrop-blur-sm hover:bg-white/20 transition-colors">
                  <div class="text-2xl mb-2">📘</div>
                  <div class="text-sm">Facebook</div>
                </a>
                <a href="#" class="bg-white/10 rounded-2xl p-4 text-center backdrop-blur-sm hover:bg-white/20 transition-colors">
                  <div class="text-2xl mb-2">📷</div>
                  <div class="text-sm">Instagram</div>
                </a>
                <a href="#" class="bg-white/10 rounded-2xl p-4 text-center backdrop-blur-sm hover:bg-white/20 transition-colors">
                  <div class="text-2xl mb-2">📺</div>
                  <div class="text-sm">YouTube</div>
                </a>
              </div>
            </div>
          </div>

          <!-- Formulaire de contact -->
          <div v-reveal>
            <div class="bg-white rounded-3xl p-8 shadow-2xl border border-gray-100">
              <h3 class="text-2xl font-bold text-gray-900 mb-6">💬 Envoyer un message</h3>

              <form @submit.prevent="submitContact" class="space-y-6">
                <div v-if="successMsg" class="bg-green-50 border border-green-200 rounded-2xl p-4 text-green-700">
                  {{ successMsg }}
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Votre nom</label>
                    <input v-model="cForm.name" type="text" required
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:border-mali-blue focus:ring-2 focus:ring-mali-blue/20 transition-all">
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Votre email</label>
                    <input v-model="cForm.email" type="email" required
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:border-mali-blue focus:ring-2 focus:ring-mali-blue/20 transition-all">
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Sujet</label>
                  <select v-model="cForm.subject" required
                          class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:border-mali-blue focus:ring-2 focus:ring-mali-blue/20 transition-all">
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
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:border-mali-blue focus:ring-2 focus:ring-mali-blue/20 transition-all"></textarea>
                </div>

                <input v-model="cForm.hp" type="text" class="sr-only" tabindex="-1" autocomplete="off">

                <button type="submit" :disabled="cForm.processing"
                        class="w-full bg-gradient-to-r from-mali-blue to-mali-green text-white py-4 rounded-2xl font-semibold hover:shadow-lg transition-all duration-300 disabled:opacity-50">
                  {{ cForm.processing ? 'Envoi en cours...' : 'Envoyer le message' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
          <div>
            <h3 class="text-2xl font-bold text-mali-gold mb-4">AMEST-Sahel</h3>
            <p class="text-gray-400 text-sm">
              Association Malienne des Étudiants et Stagiaires en Tunisie section Sahel
            </p>
          </div>

          <div>
            <h4 class="font-semibold mb-4">Liens Rapides</h4>
            <div class="space-y-2 text-sm text-gray-400">
              <a href="#apropos" class="block hover:text-mali-gold transition-colors">À propos</a>
              <a href="#activites" class="block hover:text-mali-gold transition-colors">Activités</a>
              <a href="#equipe" class="block hover:text-mali-gold transition-colors">Équipe</a>
              <a href="#contact" class="block hover:text-mali-gold transition-colors">Contact</a>
            </div>
          </div>

          <div>
            <h4 class="font-semibold mb-4">Contact</h4>
            <div class="space-y-2 text-sm text-gray-400">
              <div>{{ associationInfo.contact.email }}</div>
              <div>{{ associationInfo.contact.phone }}</div>
            </div>
          </div>

          <div>
            <h4 class="font-semibold mb-4">Suivez-nous</h4>
            <div class="flex gap-4">
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-2xl flex items-center justify-center hover:bg-mali-blue transition-colors">
                <span class="text-lg">📘</span>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-2xl flex items-center justify-center hover:bg-mali-green transition-colors">
                <span class="text-lg">📷</span>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-2xl flex items-center justify-center hover:bg-mali-gold transition-colors">
                <span class="text-lg">📺</span>
              </a>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
          <p>&copy; 2024 AMEST-Sahel. Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </PublicLayout>
</template>

<style scoped>
/* Variables CSS pour les couleurs du Mali */
:root {
  --mali-blue: #3b82f6;
  --mali-green: #10b981;
  --mali-gold: #f59e0b;
  --mali-red: #ef4444;
}

.text-mali-blue { color: var(--mali-blue); }
.text-mali-green { color: var(--mali-green); }
.text-mali-gold { color: var(--mali-gold); }
.text-mali-red { color: var(--mali-red); }

.bg-mali-blue { background-color: var(--mali-blue); }
.bg-mali-green { background-color: var(--mali-green); }
.bg-mali-gold { background-color: var(--mali-gold); }
.bg-mali-red { background-color: var(--mali-red); }

/* Boutons */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, var(--mali-blue), var(--mali-green));
  color: white;
  padding: 1rem 2rem;
  border-radius: 1rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.1);
  color: white;
  padding: 1rem 2rem;
  border-radius: 1rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
}

/* Animations reveal */
.reveal {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
}

.reveal.is-visible {
  opacity: 1;
  transform: translateY(0);
}

/* Effets de tilt pour les cartes */
.card-iso {
  transform: perspective(1000px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
  transition: transform 0.2s ease-out;
}
</style>
