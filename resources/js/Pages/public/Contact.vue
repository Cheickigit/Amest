<!-- resources/js/Pages/Public/Contact.vue -->
<template>
  <PublicLayout>
    <!-- HERO SECTION AVEC DRAPEAU MALI -->
    <section class="relative overflow-hidden pt-20 md:pt-28 pb-16 bg-gradient-to-r from-red-600 via-yellow-400 to-green-600">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center" v-reveal>
          <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 border border-white/30 text-white text-sm font-semibold mb-6">
            Contactez-nous
          </span>
          <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
            <span class="drop-shadow-lg">Contact</span>
          </h1>
          <p class="text-xl md:text-2xl text-white/95 max-w-4xl mx-auto leading-relaxed font-medium">
            Une question ? Une suggestion ? Nous sommes là pour vous écouter !
          </p>
          <p class="text-lg text-white/90 max-w-3xl mx-auto mt-6 leading-relaxed">
            L'AMEST-Sahel reste à votre écoute pour toute question concernant l'association,
            les activités ou l'intégration des étudiants maliens en Tunisie.
          </p>
        </div>
      </div>
    </section>

    <!-- CONTENU PRINCIPAL -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12">
          <!-- Formulaire de contact -->
          <div v-reveal>
            <div class="mb-8">
              <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Envoyez-nous un <span class="text-green-600">message</span>
              </h2>
              <p class="text-gray-600">
                Remplissez ce formulaire et nous vous répondrons dans les plus brefs délais.
              </p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Nom complet -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                  Nom complet *
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  id="name"
                  required
                  placeholder="Votre nom complet"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                >
                <p v-if="errors.name" class="text-red-600 text-sm mt-2">{{ errors.name }}</p>
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                  Adresse email *
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  id="email"
                  required
                  placeholder="votre@email.com"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                >
                <p v-if="errors.email" class="text-red-600 text-sm mt-2">{{ errors.email }}</p>
              </div>

              <!-- Téléphone -->
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                  Téléphone
                </label>
                <input
                  v-model="form.phone"
                  type="tel"
                  id="phone"
                  placeholder="+216 XX XXX XXX"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                >
              </div>

              <!-- Sujet -->
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                  Sujet *
                </label>
                <select
                  v-model="form.subject"
                  id="subject"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition bg-white"
                >
                  <option value="">Sélectionnez un sujet</option>
                  <option value="adhesion">Adhésion à l'association</option>
                  <option value="evenement">Information sur un événement</option>
                  <option value="aide">Demande d'aide ou de soutien</option>
                  <option value="partenariat">Proposition de partenariat</option>
                  <option value="autre">Autre demande</option>
                </select>
              </div>

              <!-- Message -->
              <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                  Message *
                </label>
                <textarea
                  v-model="form.message"
                  id="message"
                  rows="6"
                  required
                  placeholder="Votre message..."
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none"
                ></textarea>
                <p v-if="errors.message" class="text-red-600 text-sm mt-2">{{ errors.message }}</p>
              </div>

              <!-- Bouton d'envoi -->
              <div>
                <button
                  type="submit"
                  :disabled="loading"
                  class="w-full bg-gradient-to-r from-red-600 to-yellow-500 text-white font-medium py-4 px-6 rounded-lg hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span v-if="loading">Envoi en cours...</span>
                  <span v-else class="flex items-center justify-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Envoyer le message
                  </span>
                </button>
              </div>

              <!-- Message de succès -->
              <div v-if="successMessage" class="p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                    <span class="text-green-600">✓</span>
                  </div>
                  <div>
                    <p class="text-green-800 font-medium">{{ successMessage }}</p>
                    <p class="text-green-600 text-sm mt-1">Nous vous répondrons dans les 48h.</p>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <!-- Informations de contact -->
          <div class="space-y-8" v-reveal>
            <!-- Coordonnées -->
            <div class="bg-gradient-to-br from-red-50 to-yellow-50 rounded-2xl p-8 border border-red-200">
              <h3 class="text-2xl font-bold text-gray-900 mb-6">
                Nos <span class="text-green-600">coordonnées</span>
              </h3>

              <div class="space-y-6">
                <!-- Email -->
                <div class="flex items-start gap-4">
                  <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-red-600 text-xl">📧</span>
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Email</h4>
                    <a href="mailto:amestsahel04@gmail.com" class="text-gray-700 hover:text-red-600 transition">
                      amestsahel04@gmail.com
                    </a>
                    <p class="text-gray-500 text-sm mt-1">Réponse sous 48h</p>
                  </div>
                </div>

                <!-- Téléphone -->
                <div class="flex items-start gap-4">
                  <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-yellow-600 text-xl">📱</span>
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Téléphone</h4>
                    <a href="tel:+21624282332" class="text-gray-700 hover:text-red-600 transition">
                      +216 24 28 23 32
                    </a>
                    <p class="text-gray-500 text-sm mt-1">Lun-Ven: 9h-17h</p>
                  </div>
                </div>

                <!-- Adresse -->
                <div class="flex items-start gap-4">
                  <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-green-600 text-xl">📍</span>
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Localisation</h4>
                    <p class="text-gray-700">Région du Sahel, Tunisie</p>
                    <p class="text-gray-500 text-sm mt-1">Sousse • Mahdia • Monastir</p>
                  </div>
                </div>

                <!-- Réseaux sociaux -->
                <div class="flex items-start gap-4">
                  <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 text-xl">🌐</span>
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Réseaux sociaux</h4>
                    <div class="flex gap-4">
                      <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-red-100 transition">
                        <span class="text-gray-700 hover:text-red-600">📘</span>
                      </a>
                      <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-blue-100 transition">
                        <span class="text-gray-700 hover:text-blue-400">🐦</span>
                      </a>
                      <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-pink-100 transition">
                        <span class="text-gray-700 hover:text-pink-600">📸</span>
                      </a>
                      <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-blue-200 transition">
                        <span class="text-gray-700 hover:text-blue-700">💼</span>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- FAQ rapide -->
            <div class="bg-gradient-to-br from-green-50 to-yellow-50 rounded-2xl p-8 border border-green-200">
              <h3 class="text-2xl font-bold text-gray-900 mb-6">
                Questions <span class="text-red-600">fréquentes</span>
              </h3>

              <div class="space-y-4">
                <details class="group">
                  <summary class="flex items-center justify-between cursor-pointer p-3 bg-white rounded-lg hover:bg-gray-50 transition">
                    <span class="font-medium text-gray-900">Comment adhérer à l'association ?</span>
                    <span class="text-gray-500 group-open:rotate-180 transition">▼</span>
                  </summary>
                  <div class="p-3 bg-white/50 rounded-lg mt-2">
                    <p class="text-gray-700 text-sm">
                      Remplissez le formulaire d'adhésion en ligne ou contactez-nous directement.
                      Les frais d'adhésion annuels sont de 30 DT.
                    </p>
                  </div>
                </details>

                <details class="group">
                  <summary class="flex items-center justify-between cursor-pointer p-3 bg-white rounded-lg hover:bg-gray-50 transition">
                    <span class="font-medium text-gray-900">Qui peut participer aux activités ?</span>
                    <span class="text-gray-500 group-open:rotate-180 transition">▼</span>
                  </summary>
                  <div class="p-3 bg-white/50 rounded-lg mt-2">
                    <p class="text-gray-700 text-sm">
                      Tous les étudiants et stagiaires maliens résidant dans la région du Sahel
                      peuvent participer à nos activités. Certains événements sont ouverts au public.
                    </p>
                  </div>
                </details>

                <details class="group">
                  <summary class="flex items-center justify-between cursor-pointer p-3 bg-white rounded-lg hover:bg-gray-50 transition">
                    <span class="font-medium text-gray-900">Quels sont les horaires du bureau ?</span>
                    <span class="text-gray-500 group-open:rotate-180 transition">▼</span>
                  </summary>
                  <div class="p-3 bg-white/50 rounded-lg mt-2">
                    <p class="text-gray-700 text-sm">
                      Notre bureau est ouvert du lundi au vendredi de 9h à 17h.
                      Pour les réunions, veuillez prendre rendez-vous à l'avance.
                    </p>
                  </div>
                </details>
              </div>
            </div>

            <!-- Carte de localisation -->
            <div class="bg-gradient-to-br from-blue-50 to-yellow-50 rounded-2xl p-8 border border-blue-200">
              <h3 class="text-2xl font-bold text-gray-900 mb-6">
                Notre <span class="text-green-600">zone d'action</span>
              </h3>

              <div class="aspect-video rounded-lg overflow-hidden bg-gradient-to-r from-red-500 via-yellow-400 to-green-500 flex items-center justify-center">
                <div class="text-center text-white p-6">
                  <div class="text-4xl mb-4">🗺️</div>
                  <p class="font-semibold">Région du Sahel Tunisien</p>
                  <p class="text-white/80 text-sm mt-2">Sousse • Mahdia • Monastir</p>
                </div>
              </div>

              <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="text-center">
                  <div class="text-2xl mb-2">🎓</div>
                  <p class="font-medium text-gray-900">500+</p>
                  <p class="text-gray-600 text-sm">Étudiants</p>
                </div>
                <div class="text-center">
                  <div class="text-2xl mb-2">🏙️</div>
                  <p class="font-medium text-gray-900">3</p>
                  <p class="text-gray-600 text-sm">Villes</p>
                </div>
                <div class="text-center">
                  <div class="text-2xl mb-2">📅</div>
                  <p class="font-medium text-gray-900">20+</p>
                  <p class="text-gray-600 text-sm">Événements/an</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- BANDEAU URGENCE -->
    <section class="bg-gradient-to-r from-red-600 to-yellow-500 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6" v-reveal>
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
              <span class="text-2xl">🚨</span>
            </div>
            <div>
              <h3 class="font-bold text-lg">Urgence étudiante ?</h3>
              <p class="text-white/90 text-sm">Nous sommes là pour vous aider 24h/24</p>
            </div>
          </div>
          <div class="flex items-center gap-4">
            <a href="tel:+21624282332" class="bg-white text-red-600 font-bold px-6 py-3 rounded-lg hover:bg-gray-100 transition">
              📞 Appeler l'urgence
            </a>
            <a href="#" class="border border-white/30 text-white px-6 py-3 rounded-lg hover:bg-white/10 transition">
              Voir les procédures
            </a>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { ref, reactive } from 'vue'

// Formulaire
const form = reactive({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
})

// États
const loading = ref(false)
const successMessage = ref('')
const errors = reactive({
  name: '',
  email: '',
  message: ''
})

// Validation
const validateForm = (): boolean => {
  let valid = true

  // Reset errors
  Object.keys(errors).forEach(key => {
    errors[key as keyof typeof errors] = ''
  })

  // Validation du nom
  if (!form.name.trim()) {
    errors.name = 'Le nom est requis'
    valid = false
  } else if (form.name.trim().length < 2) {
    errors.name = 'Le nom doit contenir au moins 2 caractères'
    valid = false
  }

  // Validation de l'email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!form.email) {
    errors.email = 'L\'email est requis'
    valid = false
  } else if (!emailRegex.test(form.email)) {
    errors.email = 'Format d\'email invalide'
    valid = false
  }

  // Validation du message
  if (!form.message.trim()) {
    errors.message = 'Le message est requis'
    valid = false
  } else if (form.message.trim().length < 10) {
    errors.message = 'Le message doit contenir au moins 10 caractères'
    valid = false
  }

  return valid
}

// Soumission du formulaire
const submitForm = async () => {
  if (!validateForm()) return

  loading.value = true

  try {
    // Simuler l'envoi
    await new Promise(resolve => setTimeout(resolve, 1500))

    // Réinitialiser le formulaire
    Object.keys(form).forEach(key => {
      form[key as keyof typeof form] = ''
    })

    // Afficher le message de succès
    successMessage.value = 'Votre message a été envoyé avec succès !'

    // Cacher le message après 5 secondes
    setTimeout(() => {
      successMessage.value = ''
    }, 5000)

  } catch (error) {
    alert('Une erreur est survenue. Veuillez réessayer.')
  } finally {
    loading.value = false
  }
}

// Animations (comme About.vue)
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

/* Style pour les détails/summary */
details summary::-webkit-details-marker {
  display: none;
}

details summary {
  list-style: none;
}

/* Transition pour les flèches */
details summary svg {
  transition: transform 0.3s ease;
}

details[open] summary svg {
  transform: rotate(180deg);
}

/* Effets pour les inputs */
input:focus,
textarea:focus,
select:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

/* Effets de survol */
.hover-lift {
  transition: transform 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-2px);
}
</style>
