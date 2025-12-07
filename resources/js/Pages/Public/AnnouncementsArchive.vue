<!-- resources/js/Pages/Public/AnnouncementsArchive.vue -->
<template>
  <PublicLayout>
    <!-- HERO SECTION AVEC DRAPEAU MALI -->
    <section class="relative overflow-hidden pt-20 md:pt-28 pb-16 bg-gradient-to-r from-red-600 via-yellow-400 to-green-600">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center" v-reveal>
          <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 border border-white/30 text-white text-sm font-semibold mb-6">
            Centre d'information
          </span>
          <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
            Annonces & <span class="text-white drop-shadow-lg">Archives</span>
          </h1>
          <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed font-medium">
            Toutes les actualités et l'historique de l'AMEST-Sahel réunis
          </p>
          <p class="text-lg text-white/90 max-w-3xl mx-auto mt-6 leading-relaxed">
            Restez informé des dernières nouvelles et explorez notre mémoire associative depuis 2018.
          </p>
        </div>
      </div>
    </section>

    <!-- INTRODUCTION -->
    <section class="py-16 bg-white">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <div v-reveal>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
              Un <span class="text-green-600">centre</span> d'information complet
            </h2>
            <div class="space-y-4 text-gray-700 text-lg leading-relaxed">
              <p>
                Cette page centralise <strong class="text-red-600">toutes les informations</strong>
                de l'AMEST-Sahel. Des annonces les plus récentes aux archives historiques,
                trouvez ici tout ce qui fait la vie de notre association.
              </p>
              <p>
                Naviguez entre les <strong class="text-yellow-600">actualités récentes</strong>
                et plongez dans notre <strong class="text-green-600">mémoire associative</strong>
                pour découvrir l'évolution et les réalisations de l'AMEST-Sahel.
              </p>
            </div>
          </div>
          <div v-reveal class="relative">
            <div class="bg-gradient-to-br from-red-600 to-yellow-500 rounded-2xl p-8 text-white">
              <h3 class="text-2xl font-bold mb-4">Pourquoi consulter cette page ?</h3>
              <ul class="space-y-3 text-white/95">
                <li class="flex items-start gap-3">
                  <span class="mt-1">📢</span>
                  <span>Ne ratez aucune annonce importante</span>
                </li>
                <li class="flex items-start gap-3">
                  <span class="mt-1">📚</span>
                  <span>Comprenez l'évolution de l'association</span>
                </li>
                <li class="flex items-start gap-3">
                  <span class="mt-1">🎯</span>
                  <span>Accédez à toutes les informations en un clic</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- DEUX COLONNES PRINCIPALES -->
    <section class="py-16 bg-gradient-to-br from-green-50 to-yellow-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" v-reveal>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Explorez notre <span class="text-red-600">contenu</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Deux univers complémentaires pour tout savoir sur l'AMEST-Sahel
          </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
          <!-- ANNONCES -->
          <div v-reveal class="bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 border border-red-200">
            <!-- En-tête avec gradient Mali -->
            <div class="h-4 bg-gradient-to-r from-red-500 via-yellow-400 to-green-500"></div>

            <div class="p-8">
              <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-red-500 to-yellow-400 flex items-center justify-center">
                  <span class="text-3xl text-white">📢</span>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-gray-900">Annonces Récentes</h3>
                  <p class="text-gray-600">Actualités et informations importantes</p>
                </div>
              </div>

              <!-- Liste des annonces -->
              <div class="space-y-6 mb-8">
                <div v-for="announcement in recentAnnouncements" :key="announcement.id"
                     class="group cursor-pointer">
                  <div class="flex justify-between items-start mb-2">
                    <span :class="[
                      'px-3 py-1 rounded-full text-xs font-semibold',
                      announcement.type === 'urgent' ? 'bg-red-100 text-red-800' :
                      announcement.type === 'event' ? 'bg-green-100 text-green-800' :
                      'bg-blue-100 text-blue-800'
                    ]">
                      {{ announcement.category }}
                    </span>
                    <span class="text-sm text-gray-500">{{ announcement.date }}</span>
                  </div>
                  <h4 class="font-bold text-gray-900 mb-2 group-hover:text-red-600 transition-colors">
                    {{ announcement.title }}
                  </h4>
                  <p class="text-gray-600 text-sm line-clamp-2">{{ announcement.description }}</p>

                  <!-- Indicateur important -->
                  <div v-if="announcement.important" class="flex items-center gap-2 mt-3">
                    <span class="text-red-500">⚠️</span>
                    <span class="text-sm text-red-600 font-medium">Important</span>
                  </div>
                </div>
              </div>

              <!-- Bouton Voir plus -->
              <div class="text-center">
                <a :href="r('announcements')"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-yellow-400 text-white rounded-full font-medium hover:shadow-lg transition-all">
                  <span>Voir toutes les annonces</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>

          <!-- ARCHIVES -->
          <div v-reveal class="bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 border border-green-200">
            <!-- En-tête avec gradient Mali -->
            <div class="h-4 bg-gradient-to-r from-green-500 via-yellow-400 to-red-500"></div>

            <div class="p-8">
              <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-500 to-yellow-400 flex items-center justify-center">
                  <span class="text-3xl text-white">📚</span>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-gray-900">Archives Historiques</h3>
                  <p class="text-gray-600">Mémoire et réalisations passées</p>
                </div>
              </div>

              <!-- Liste des archives par année -->
              <div class="space-y-6 mb-8">
                <div v-for="year in archiveYears" :key="year.year" class="group">
                  <div class="flex items-center justify-between mb-3">
                    <h4 class="text-lg font-bold text-gray-900">{{ year.year }}</h4>
                    <span class="text-sm text-gray-500">{{ year.count }} événements</span>
                  </div>

                  <div class="space-y-3">
                    <div v-for="event in year.events" :key="event.id"
                         class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                      <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-yellow-400 flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-sm">{{ getEventIcon(event.type) }}</span>
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-gray-900 text-sm">{{ event.title }}</p>
                        <p class="text-gray-500 text-xs">{{ event.date }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Bouton Explorer -->
              <div class="text-center">
                <a :href="r('archive')"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-yellow-400 text-white rounded-full font-medium hover:shadow-lg transition-all">
                  <span>Explorer les archives</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- STATISTIQUES -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" v-reveal>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            En <span class="text-green-600">chiffres</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            La richesse de notre contenu informationnel
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6" v-reveal>
          <div class="bg-gradient-to-br from-red-50 to-yellow-50 rounded-2xl p-6 text-center border border-red-200">
            <div class="text-4xl font-bold text-red-600 mb-2">{{ totalAnnouncements }}</div>
            <div class="text-gray-700 font-medium">Annonces publiées</div>
            <p class="text-gray-500 text-sm mt-2">Depuis 2018</p>
          </div>

          <div class="bg-gradient-to-br from-green-50 to-yellow-50 rounded-2xl p-6 text-center border border-green-200">
            <div class="text-4xl font-bold text-green-600 mb-2">{{ totalEvents }}</div>
            <div class="text-gray-700 font-medium">Événements archivés</div>
            <p class="text-gray-500 text-sm mt-2">Activités et réalisations</p>
          </div>

          <div class="bg-gradient-to-br from-blue-50 to-yellow-50 rounded-2xl p-6 text-center border border-blue-200">
            <div class="text-4xl font-bold text-blue-600 mb-2">{{ archiveYears.length }}</div>
            <div class="text-gray-700 font-medium">Années d'archives</div>
            <p class="text-gray-500 text-sm mt-2">De 2018 à aujourd'hui</p>
          </div>

          <div class="bg-gradient-to-br from-purple-50 to-yellow-50 rounded-2xl p-6 text-center border border-purple-200">
            <div class="text-4xl font-bold text-purple-600 mb-2">100%</div>
            <div class="text-gray-700 font-medium">Transparence</div>
            <p class="text-gray-500 text-sm mt-2">Information accessible à tous</p>
          </div>
        </div>
      </div>
    </section>

    <!-- GUIDE D'UTILISATION -->
    <section class="py-16 bg-gradient-to-br from-green-50 to-yellow-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl p-8 border border-green-200 shadow-lg" v-reveal>
          <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">
            Comment <span class="text-red-600">utiliser</span> cette page ?
          </h2>

          <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-red-500 to-yellow-400 flex items-center justify-center">
                <span class="text-2xl text-white">📢</span>
              </div>
              <h3 class="font-bold text-gray-900 mb-3">Pour les annonces</h3>
              <p class="text-gray-600 text-sm">
                Consultez les annonces récentes pour ne rien manquer des actualités importantes de l'association.
              </p>
            </div>

            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-green-500 to-yellow-400 flex items-center justify-center">
                <span class="text-2xl text-white">📚</span>
              </div>
              <h3 class="font-bold text-gray-900 mb-3">Pour les archives</h3>
              <p class="text-gray-600 text-sm">
                Explorez l'historique pour comprendre l'évolution et les réalisations de l'AMEST-Sahel.
              </p>
            </div>

            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-blue-500 to-yellow-400 flex items-center justify-center">
                <span class="text-2xl text-white">🔍</span>
              </div>
              <h3 class="font-bold text-gray-900 mb-3">Pour la recherche</h3>
              <p class="text-gray-600 text-sm">
                Utilisez cette page comme point de départ pour trouver toute information sur l'association.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="py-16 bg-gradient-to-r from-red-600 via-yellow-400 to-green-600 text-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div v-reveal>
          <h2 class="text-3xl md:text-4xl font-bold mb-6">Restez connecté !</h2>
          <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            Abonnez-vous à notre newsletter pour recevoir directement les annonces importantes dans votre boîte mail.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
            <input
              type="email"
              placeholder="Votre email"
              class="flex-1 px-6 py-3 rounded-full text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500"
            >
            <button class="bg-white text-green-700 px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition-colors whitespace-nowrap">
              S'abonner
            </button>
          </div>

          <p class="text-white/70 text-sm mt-4">
            Nous respectons votre vie privée. Désabonnez-vous à tout moment.
          </p>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { ref, computed } from 'vue'

/* Helper route (identique à About.vue) */
function r(name: string, params?: any, absolute = false, fallback: string = '#') {
  try {
    const Z = (globalThis as any)?.Ziggy
    if (!Z) return fallback
    return ziggyRoute(name, params ?? {}, absolute, Z)
  } catch { return fallback }
}

/* Données des annonces récentes */
const recentAnnouncements = ref([
  {
    id: 1,
    title: 'Assemblée Générale Annuelle 2025',
    description: 'L\'AMEST-Sahel organise son assemblée générale annuelle le 15 mars 2025. Tous les membres sont invités à participer.',
    type: 'event',
    category: 'Événement',
    date: '28 Fév 2025',
    important: true
  },
  {
    id: 2,
    title: 'Inscriptions aux cours de soutien',
    description: 'Les inscriptions pour les cours de soutien en mathématiques et physique sont ouvertes jusqu\'au 10 mars.',
    type: 'academic',
    category: 'Académique',
    date: '25 Fév 2025',
    important: false
  },
  {
    id: 3,
    title: 'Changement de local',
    description: 'À partir du 1er mars, nos réunions auront lieu au nouveau local situé à la Faculté des Sciences de Sousse.',
    type: 'info',
    category: 'Information',
    date: '20 Fév 2025',
    important: false
  },
  {
    id: 4,
    title: 'Urgent : Récupération de documents',
    description: 'Les étudiants qui n\'ont pas encore récupéré leur carte de membre sont priés de se présenter au bureau.',
    type: 'urgent',
    category: 'Urgent',
    date: '18 Fév 2025',
    important: true
  }
])

/* Données des archives par année */
const archiveYears = ref([
  {
    year: '2024',
    count: 8,
    events: [
      { id: 1, title: 'Assemblée Générale', date: '15 Déc 2024', type: 'meeting' },
      { id: 2, title: 'Gala de fin d\'année', date: '20 Juin 2024', type: 'party' },
      { id: 3, title: 'Forum d\'orientation', date: '10 Mars 2024', type: 'academic' }
    ]
  },
  {
    year: '2023',
    count: 6,
    events: [
      { id: 4, title: 'Atelier développement personnel', date: '18 Nov 2023', type: 'workshop' },
      { id: 5, title: 'Journée de solidarité', date: '05 Oct 2023', type: 'social' },
      { id: 6, title: 'Nuit des cultures africaines', date: '25 Mai 2023', type: 'cultural' }
    ]
  },
  {
    year: '2022',
    count: 5,
    events: [
      { id: 7, title: 'Séminaire entrepreneuriat', date: '12 Avr 2022', type: 'academic' },
      { id: 8, title: 'Tournoi sportif', date: '25 Fév 2022', type: 'sport' }
    ]
  }
])

/* Statistiques */
const totalAnnouncements = computed(() => recentAnnouncements.value.length + 20) // 20 annonces passées
const totalEvents = computed(() => archiveYears.value.reduce((sum, year) => sum + year.count, 0))

/* Helper functions */
const getEventIcon = (type: string): string => {
  const icons: Record<string, string> = {
    meeting: '📅',
    party: '🎉',
    academic: '🎓',
    workshop: '💼',
    social: '❤️',
    cultural: '🌍',
    sport: '⚽',
    default: '📌'
  }
  return icons[type] || icons.default
}

/* Animations (identique à About.vue) */
let io: IntersectionObserver | null = null
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
</script>

<style scoped>
/* Styles identiques à About.vue pour la cohérence */
.reveal {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.6s ease-out;
}

.reveal.is-visible {
  opacity: 1;
  transform: translateY(0);
}

/* Effets de survol */
.hover-lift {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* Limite de lignes pour les descriptions */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Styles pour les cartes */
.card-hover {
  transition: all 0.3s ease;
}

.card-hover:hover {
  border-color: #dc2626; /* red-600 */
  box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
  .stat-card {
    padding: 1.5rem;
  }
}
</style>
