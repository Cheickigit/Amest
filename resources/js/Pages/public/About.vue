<!-- resources/js/Pages/Public/About.vue -->
<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

/* Helper route */
function r(name: string, params?: any, absolute = false, fallback: string = '#') {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (!Z) return fallback
    return ziggyRoute(name, params ?? {}, absolute, Z)
  } catch { return fallback }
}

/* Données AMEST-Sahel - ORGANISÉES PAR HIÉRARCHIE AVEC PHOTOS CORRECTES */
const teamMembers = ref([
  // BUREAU EXÉCUTIF
  {
    name: 'Adama Pangolo DAOU',
    role: 'Président',
    studies: 'Étudiant en Génie civil, Pont et chaussée',
    email: 'adamadaou650@gmail.com',
    photo: '/assets/team/president-adama.jpg',
    hierarchy: 1,
    category: 'Bureau Exécutif'
  },
  {
    name: 'Aly TOGO',
    role: 'Secrétaire général',
    studies: 'Étudiant en génie électrique',
    email: '',
    photo: '/assets/team/secretaire-aly.jpg',
    hierarchy: 2,
    category: 'Bureau Exécutif'
  },
  {
    name: 'Seybou DABO',
    role: 'Trésorier',
    studies: 'Étudiant en génie électrique',
    email: 'seyboudabo7136@gmail.com',
    photo: '/assets/team/tresorier-seybou.jpg',
    hierarchy: 3,
    category: 'Bureau Exécutif'
  },

  // SECRÉTARIAT GÉNÉRAL
  {
    name: 'Seydou Fily MALLE',
    role: 'Adjoint Secrétaire général',
    studies: 'Étudiant en IA et Data Science',
    email: 'seydoufilymalle@gmail.com',
    photo: '/assets/team/adj-secretaire-seydou.jpg',
    hierarchy: 4,
    category: 'Secrétariat Général'
  },

  // TRÉSORERIE
  {
    name: 'Abdrahamane SANGARE',
    role: 'Adjoint Trésorier',
    studies: 'Étudiant en finance',
    email: 'abdrahamanesangare006@gmail.com',
    photo: '/assets/team/adj-tresorier-abdrahamane.jpg',
    hierarchy: 5,
    category: 'Trésorerie'
  },

  // COMMISSION ORGANISATION
  {
    name: 'Lassine COULIBALY',
    role: 'Chargé de l\'organisation',
    studies: 'Étudiant en comptabilité contrôle audit',
    email: 'lassine.coulibaly001@gmail.com',
    photo: '/assets/team/organisation-lassine.jpg',
    hierarchy: 6,
    category: 'Commission Organisation'
  },
  {
    name: 'Abdoul Kassim TOURE',
    role: 'Adjoint Chargé de l\'organisation',
    studies: 'Étudiant en génie mécanique',
    email: '',
    photo: '/assets/team/adj-organisation-kassim.jpg',
    hierarchy: 7,
    category: 'Commission Organisation'
  },
  {
    name: 'Kadidia BAH',
    role: 'Adjoint Chargé de l\'organisation',
    studies: 'Étudiante en génie électrique',
    email: 'kadidiabah70@gmail.com',
    photo: '/assets/team/adj-organisation-kadidia.jpg',
    hierarchy: 8,
    category: 'Commission Organisation'
  },

  // COMMISSION RELATIONS EXTÉRIEURES
  {
    name: 'Aoua H DIALLO',
    role: 'Chargé des relations extérieures',
    studies: 'Étudiante en Génie civil',
    email: 'dialloaoua66@gmail.com',
    photo: '/assets/team/relations-aoua.jpg',
    hierarchy: 9,
    category: 'Commission Relations Extérieures'
  },
  {
    name: 'Hawa DIARRA',
    role: 'Adjoint Chargé des relations extérieures',
    studies: 'Étudiante en Marketing Digital',
    email: 'hsoufianadiarra@gmail.com',
    photo: '/assets/team/adj-relations-hawa.jpg',
    hierarchy: 10,
    category: 'Commission Relations Extérieures'
  },

  // COMMISSION COMMUNICATION
  {
    name: 'Massaouda CAMARA',
    role: 'Chargé de la communication',
    studies: 'Étudiante en comptabilité contrôle audit',
    email: 'massaoudacamara2003@gmail.com',
    photo: '/assets/team/communication-massaouda.jpg',
    hierarchy: 11,
    category: 'Commission Communication'
  },

  // COMMISSION AFFAIRES SOCIOCULTURELLES
  {
    name: 'Alhoussouna TOURE',
    role: 'Chargé des affaires socioculturelles',
    studies: 'Étudiant en Marketing et Communication',
    email: 'talhoussouna73@gmail.com',
    photo: '/assets/team/socioculturel-alhoussouna.jpg',
    hierarchy: 12,
    category: 'Commission Affaires Socioculturelles'
  }
])

// Grouper les membres par catégorie
const groupedTeam = computed(() => {
  const groups = {}
  teamMembers.value.forEach(member => {
    if (!groups[member.category]) {
      groups[member.category] = []
    }
    groups[member.category].push(member)
  })
  return groups
})

const activities = ref([
  {
    title: 'Journée d\'intégration',
    date: '01 Février 2025',
    location: 'Mahdia',
    description: 'Accueil des nouveaux membres et présentation de l\'association',
    image: '/assets/activities/integration-2025.jpg'
  },
  {
    title: 'Sortie au Zoo de Friguia',
    date: '22 Février 2025',
    location: 'Friguia',
    description: 'Excursion éducative et détente au parc animalier',
    image: '/assets/activities/zoo-friguia-2025.jpg'
  },
  {
    title: 'Rupture de jeûne Sousse',
    date: '15 Mars 2025',
    location: 'Sousse',
    description: 'Célébration du Ramadan en communauté',
    image: '/assets/activities/iftar-sousse-2025.jpg'
  },
  {
    title: 'Rupture de jeûne Mahdia',
    date: '22 Mars 2025',
    location: 'Mahdia',
    description: 'Partage et convivialité pendant le mois sacré',
    image: '/assets/activities/iftar-mahdia-2025.jpg'
  },
  {
    title: 'La nuit des communautés',
    date: '12 Avril 2025',
    location: 'Sousse',
    description: 'Événement interculturel mettant en valeur la diversité',
    image: '/assets/activities/nuit-communautes-2025.jpg'
  },
  {
    title: 'Formation Power BI',
    date: '26 Avril 2025',
    location: 'Sousse',
    description: 'Atelier de développement des compétences en analyse de données',
    image: '/assets/activities/formation-powerbi-2025.jpg'
  },
  {
    title: 'Excursion',
    date: '01 Juin 2025',
    location: 'Zaghouan-Sidi Bou Said',
    description: 'Découverte des sites historiques et touristiques',
    image: '/assets/activities/excursion-2025.jpg'
  }
])

const formerPresidents = ref([
  { name: 'Yacouba DIAKITE', mandate: '2022 – 2023', photo: '/assets/former-presidents/yacouba.jpg' },
  { name: 'Ibrahima DEMBELE', mandate: '2021 – 2022', photo: '/assets/former-presidents/ibrahima.jpg' },
  { name: 'Boubacar KIMBIRI', mandate: '2020-2021', photo: '/assets/former-presidents/boubacar.jpg' },
  { name: 'Seydou GUINDO', mandate: '2019-2020', photo: '/assets/former-presidents/seydou.jpg' },
  { name: 'Andoulé Abdoulaye SAGARA', mandate: '2018-2019', photo: '/assets/former-presidents/andoule.jpg' }
])

/* Animations */
let io: IntersectionObserver | null = null
const vReveal = {
  mounted(el: HTMLElement) {
    el.classList.add('reveal')
    if (!io) {
      io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
          if (e.isIntersecting) {
            e.target.classList.add('is-visible');
            io?.unobserve(e.target)
          }
        })
      }, { threshold: .14 })
    }
    io.observe(el)
  }
}

// Gestion des images manquantes
const handleImageError = (event: Event, fallbackText: string) => {
  const target = event.target as HTMLImageElement
  const parent = target.parentElement
  if (parent) {
    // Remplacer l'image par un avatar avec les initiales
    target.style.display = 'none'
    const avatar = document.createElement('div')
    avatar.className = 'w-full h-full bg-gradient-to-br from-red-500 to-yellow-400 rounded-full flex items-center justify-center text-white font-bold text-lg'
    avatar.textContent = fallbackText.split(' ').map(n => n[0]).join('')
    parent.appendChild(avatar)
  }
}
</script>

<template>
  <PublicLayout>
    <!-- HERO SECTION AVEC DRAPEAU MALI -->
    <section class="relative overflow-hidden pt-20 md:pt-28 pb-16 md:py-24 bg-gradient-to-r from-red-600 via-yellow-400 to-green-600">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center" v-reveal>
          <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 border border-white/30 text-white text-sm font-semibold mb-6">
            À propos de nous
          </span>
          <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
            AMEST-<span class="text-white drop-shadow-lg">Sahel</span>
          </h1>
          <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed font-medium">
            Association Malienne des Étudiants et Stagiaires en Tunisie - Section Sahel
          </p>
          <p class="text-lg text-white/90 max-w-3xl mx-auto mt-6 leading-relaxed">
            Rassembler la communauté étudiante malienne en Tunisie autour des valeurs de solidarité,
            d'entraide et d'excellence depuis 2018.
          </p>
        </div>
      </div>
    </section>

    <!-- PRÉSENTATION -->
    <section class="py-16 bg-white">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <div v-reveal>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
              Notre <span class="text-green-600">Histoire</span>
            </h2>
            <div class="space-y-4 text-gray-700 text-lg leading-relaxed">
              <p>
                <strong class="text-red-600">Obtenant son autonomie du bureau central de l'AMEST en 2018</strong>,
                l'AMEST-Sahel a commencé comme un petit groupe d'étudiants maliens désireux de créer
                un espace d'entraide et de partage dans la région du Sahel tunisien.
              </p>
              <p>
                Aujourd'hui, nous sommes une association dynamique qui œuvre pour le bien-être
                académique, social et culturel de la communauté étudiante malienne en Tunisie.
              </p>
            </div>
          </div>
          <div v-reveal class="relative">
            <div class="bg-gradient-to-br from-red-600 to-yellow-500 rounded-2xl p-8 text-white">
              <h3 class="text-2xl font-bold mb-4">Notre Vision</h3>
              <p class="text-white/95 leading-relaxed">
                Être la référence pour les étudiants et stagiaires maliens en Tunisie,
                en créant un environnement favorable à leur épanouissement académique,
                professionnel et personnel.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- MISSION, VALEURS, OBJECTIFS -->
    <section class="py-16 bg-gradient-to-br from-red-50 to-yellow-50">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" v-reveal>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Notre <span class="text-green-600">Engagement</span>
          </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <!-- Mission -->
          <div v-reveal class="bg-white rounded-2xl p-8 shadow-lg border border-red-200">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
              <span class="text-2xl text-red-600">🎯</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Notre Mission</h3>
            <p class="text-gray-700 leading-relaxed">
              Favoriser l'entraide et l'intégration des étudiants et stagiaires maliens
              au sahel de la Tunisie tout en créant un environnement propice à leur épanouissement.
            </p>
          </div>

          <!-- Valeurs -->
          <div v-reveal class="bg-white rounded-2xl p-8 shadow-lg border border-yellow-200">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
              <span class="text-2xl text-yellow-600">💚</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Nos Valeurs</h3>
            <ul class="text-gray-700 space-y-2">
              <li class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                Solidarité
              </li>
              <li class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                Engagement
              </li>
              <li class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                Respect
              </li>
              <li class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                Excellence
              </li>
            </ul>
          </div>

          <!-- Objectifs -->
          <div v-reveal class="bg-white rounded-2xl p-8 shadow-lg border border-green-200">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
              <span class="text-2xl text-green-600">🚀</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Nos Objectifs</h3>
            <p class="text-gray-700 leading-relaxed">
              Développer des initiatives éducatives, culturelles et professionnelles
              pour soutenir la communauté étudiante malienne.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ÉQUIPE ORGANISÉE PAR HIÉRARCHIE -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" v-reveal>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Notre <span class="text-green-600">Équipe</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            L'organisation hiérarchique de notre bureau exécutif
          </p>
        </div>

        <!-- Bureau Exécutif -->
        <div v-for="(members, category) in groupedTeam" :key="category" class="mb-12">
          <div class="text-center mb-8" v-reveal>
            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
              {{ category }}
            </h3>
            <div class="w-24 h-1 bg-gradient-to-r from-red-500 to-green-500 mx-auto rounded-full"></div>
          </div>

          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="(member, index) in members"
              :key="index"
              v-reveal
              class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-6 text-center hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-red-300 group member-card"
            >
              <!-- Photo du membre -->
              <div class="w-32 h-32 mx-auto mb-4 rounded-full bg-gradient-to-br from-red-500 to-yellow-400 p-1 group-hover:from-red-600 group-hover:to-yellow-500 transition-all duration-300">
                <div class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                  <img
                    :src="member.photo"
                    :alt="`Photo de ${member.name}`"
                    class="w-full h-full object-cover rounded-full"
                    @error="(e) => handleImageError(e, member.name)"
                  >
                </div>
              </div>

              <!-- Informations du membre -->
              <h3 class="font-bold text-gray-900 text-lg mb-1">{{ member.name }}</h3>
              <p class="text-red-600 font-semibold text-sm mb-2">{{ member.role }}</p>
              <p class="text-gray-600 text-sm mb-3">{{ member.studies }}</p>

              <!-- Email -->
              <a
                v-if="member.email"
                :href="`mailto:${member.email}`"
                class="inline-flex items-center text-green-600 hover:text-green-700 text-sm font-medium transition-colors"
              >
                <span class="mr-1">📧</span>
                {{ member.email }}
              </a>
              <div v-else class="h-5"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ACTIVITÉS AVEC PHOTOS -->
    <section class="py-16 bg-gradient-to-br from-green-50 to-yellow-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" v-reveal>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Nos <span class="text-red-600">Activités</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Découvrez les événements qui rythment la vie de notre association
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(activity, index) in activities"
            :key="index"
            v-reveal
            class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 border border-green-200 activity-card"
          >
            <!-- Bannière couleur Mali -->
            <div class="h-3 bg-gradient-to-r from-red-500 via-yellow-400 to-green-500"></div>

            <!-- Photo de l'activité -->
            <div class="h-48 bg-gray-200 relative overflow-hidden">
              <img
                :src="activity.image"
                :alt="`Photo de ${activity.title}`"
                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                @error="(e) => {
                  const target = e.target as HTMLImageElement;
                  target.style.display = 'none';
                  const parent = target.parentElement;
                  if (parent) {
                    parent.className = 'h-48 bg-gradient-to-br from-red-500 to-yellow-400 flex items-center justify-center';
                    parent.innerHTML = '<span class=\"text-white text-4xl\">📅</span>';
                  }
                }"
              >
            </div>

            <div class="p-6">
              <h3 class="font-bold text-gray-900 text-lg mb-2">{{ activity.title }}</h3>
              <div class="flex items-center text-gray-600 text-sm mb-2">
                <span class="mr-3">📅 {{ activity.date }}</span>
                <span>📍 {{ activity.location }}</span>
              </div>
              <p class="text-gray-700 text-sm">{{ activity.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ANCIENS PRÉSIDENTS AVEC PHOTOS -->
    <section class="py-16 bg-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" v-reveal>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Anciens <span class="text-yellow-600">Présidents</span>
          </h2>
          <p class="text-xl text-gray-600">
            Hommage à ceux qui ont contribué à bâtir l'association
          </p>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-yellow-50 rounded-2xl p-8 border border-red-200" v-reveal>
          <div class="grid gap-6">
            <div
              v-for="(president, index) in formerPresidents"
              :key="index"
              class="flex items-center gap-6 py-4 px-6 bg-white rounded-lg shadow-sm border border-gray-200 hover:border-red-300 transition-colors"
            >
              <!-- Photo ancien président -->
              <div class="w-16 h-16 rounded-full bg-gradient-to-br from-red-500 to-yellow-400 p-0.5 flex-shrink-0">
                <div class="w-full h-full bg-gray-200 rounded-full overflow-hidden">
                  <img
                    :src="president.photo"
                    :alt="`Photo de ${president.name}`"
                    class="w-full h-full object-cover"
                    @error="(e) => {
                      const target = e.target as HTMLImageElement;
                      target.style.display = 'none';
                      const parent = target.parentElement;
                      if (parent) {
                        parent.className = 'w-full h-full bg-gradient-to-br from-red-500 to-yellow-400 rounded-full flex items-center justify-center text-white font-bold';
                        parent.textContent = president.name.split(' ').map(n => n[0]).join('');
                      }
                    }"
                  >
                </div>
              </div>

              <div class="flex-1">
                <span class="font-semibold text-gray-900 block">{{ president.name }}</span>
              </div>

              <span class="bg-gradient-to-r from-red-500 to-yellow-400 text-white px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap">
                {{ president.mandate }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CONTACT -->
    <section class="py-16 bg-gradient-to-r from-red-600 via-yellow-400 to-green-600 text-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div v-reveal>
          <h2 class="text-3xl md:text-4xl font-bold mb-6">Restons en Contact</h2>
          <div class="grid md:grid-cols-3 gap-8 mt-8">
            <div>
              <div class="text-3xl mb-2">📧</div>
              <h3 class="font-bold mb-2">Email</h3>
              <a href="mailto:amestsahel04@gmail.com" class="text-white/90 hover:text-white">
                amestsahel04@gmail.com
              </a>
            </div>
            <div>
              <div class="text-3xl mb-2">📱</div>
              <h3 class="font-bold mb-2">Téléphone</h3>
              <p class="text-white/90">24282332</p>
            </div>
            <div>
              <div class="text-3xl mb-2">🌐</div>
              <h3 class="font-bold mb-2">Réseaux sociaux</h3>
              <div class="space-y-1">
                <p class="text-white/90">Facebook: amest sahel</p>
                <p class="text-white/90">Instagram: Amest_sahel</p>
                <p class="text-white/90">YouTube: Amest-talk</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<style scoped>
.reveal {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.6s ease-out;
}

.reveal.is-visible {
  opacity: 1;
  transform: translateY(0);
}

/* Effet de brillance sur les cartes membres */
.member-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Effet sur les cartes d'activités */
.activity-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}
</style>
