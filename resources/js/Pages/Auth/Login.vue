<!-- resources/js/Pages/Auth/Login.vue - VERSION AMEST-Sahel CORRIGÉE -->
<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

const r = (name: string, params?: any, absolute = false) =>
  ziggyRoute(name, params ?? {}, absolute, (window as any).Ziggy)

const props = defineProps<{ canResetPassword?: boolean; status?: string }>()

const showPassword = ref(false)

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

function submit() {
  form
    .transform(d => ({ ...d, remember: form.remember ? 'on' : '' }))
    .post(r('login'), { onFinish: () => form.reset('password') })
}

/* Icônes */
const icons = {
  eye: 'M1 12s4-7 11-7s11 7 11 7s-4 7-11 7S1 12 1 12Zm11 4a4 4 0 1 0 0-8a4 4 0 0 0 0 8z',
  eyeOff: 'M3 3l18 18M10.73 5.08A9.77 9.77 0 0 1 12 5c7 0 11 7 11 7a17.86 17.86 0 0 1-5.09 5.64M6.11 6.11A17.94 17.94 0 0 0 1 12s4 7 11 7a9.8 9.8 0 0 0 3.54-.64M9.88 9.88A4 4 0 0 0 12 16a4 4 0 0 0 2.12-.62',
  lock: 'M6 10V7a6 6 0 1 1 12 0v3h1a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h1Zm2 0h8V7a4 4 0 1 0-8 0v3Z',
  mail: 'M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm18 0l-8 6L2 6',
  arrowR: 'M5 12h14M13 5l7 7l-7 7',
}
const eyePath = computed(() => (showPassword.value ? icons.eyeOff : icons.eye))
</script>

<template>
  <Head title="Connexion — AMEST-Sahel" />
  <div class="min-h-screen bg-gradient-to-br from-green-50 to-yellow-50 text-gray-900 relative overflow-hidden">
    <!-- Drapeau Mali en arrière-plan subtil -->
    <div class="pointer-events-none absolute inset-0 -z-10 opacity-5">
      <div class="absolute inset-0 bg-gradient-to-r from-green-600 via-yellow-500 to-red-600"></div>
    </div>

    <!-- Effets décoratifs -->
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-green-200 opacity-20 blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-yellow-200 opacity-20 blur-3xl"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
      <div class="grid lg:grid-cols-2 gap-8 items-stretch">
        <!-- Panneau gauche (présentation AMEST) -->
        <div class="hidden lg:flex">
          <div class="relative rounded-3xl p-[1px] w-full overflow-hidden
                      bg-gradient-to-r from-green-600 via-yellow-500 to-red-600 shadow-xl">
            <div class="rounded-3xl h-full p-8 bg-white flex flex-col">
              <!-- LOGO ET NOM (CORRIGÉ - pas de duplication) -->
              <div class="flex items-center gap-3">
                <img
                  src="/assets/logo-amest.jpg"
                  alt="AMEST Sahel"
                  class="w-12 h-12 rounded-2xl object-cover ring-2 ring-white shadow-md"
                />
                <div class="font-display text-2xl tracking-tight">
                  <span class="font-bold text-gray-900">AMEST</span>
                  <span class="text-green-600 font-semibold">-Sahel</span>
                </div>
              </div>

              <div class="mt-8">
                <div class="text-4xl font-extrabold leading-tight text-gray-900">
                  Espace <span class="text-green-600">Membre</span>
                </div>
                <p class="mt-3 text-gray-600">
                  Connectez-vous pour accéder aux ressources, événements et opportunités
                  de la communauté étudiante malienne en Tunisie.
                </p>
              </div>

              <div class="mt-8 grid grid-cols-2 gap-3">
                <div class="rounded-2xl p-4 bg-gradient-to-br from-green-50 to-yellow-50 border border-green-200">
                  <div class="text-green-600 font-semibold">✓ Solidarité</div>
                  <div class="text-gray-700 text-sm mt-1">Entraide communautaire</div>
                </div>
                <div class="rounded-2xl p-4 bg-gradient-to-br from-green-50 to-yellow-50 border border-green-200">
                  <div class="text-green-600 font-semibold">✓ Événements</div>
                  <div class="text-gray-700 text-sm mt-1">Activités exclusives</div>
                </div>
              </div>

              <div class="mt-auto pt-10 text-sm text-gray-500">
                Association Malienne des Étudiants et Stagiaires en Tunisie — Section Sahel
              </div>
            </div>
          </div>
        </div>

        <!-- Panneau droit (formulaire) -->
        <div class="flex">
          <div class="relative rounded-3xl p-[1px] w-full overflow-hidden
                      bg-gradient-to-r from-green-600 via-yellow-500 to-red-600 shadow-xl">
            <div class="rounded-3xl h-full p-6 sm:p-8 bg-white">
              <!-- Logo mobile (CORRIGÉ - avec votre logo) -->
              <div class="flex items-center gap-3 lg:hidden mb-6">
                <img
                  src="/assets/logo-amest.jpg"
                  alt="AMEST Sahel"
                  class="w-10 h-10 rounded-2xl object-cover ring-1 ring-gray-200"
                />
                <div class="font-display text-xl">
                  <span class="font-bold">AMEST</span><span class="text-green-600">-Sahel</span>
                </div>
              </div>

              <h1 class="text-3xl font-bold text-gray-900">Connexion</h1>
              <p class="text-gray-600 mt-2">Accédez à votre espace membre</p>

              <div v-if="props.status"
                   class="mt-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700">
                {{ props.status }}
              </div>

              <form class="mt-6 space-y-5" @submit.prevent="submit">
                <!-- Email -->
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <div class="relative">
                    <input
                      id="email"
                      v-model="form.email"
                      type="email"
                      required
                      autocomplete="username"
                      class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                      placeholder="votre@email.com"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                    </div>
                  </div>
                  <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <!-- Mot de passe -->
                <div>
                  <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <Link
                      v-if="canResetPassword"
                      :href="r('password.request')"
                      class="text-sm text-green-600 hover:text-green-700"
                    >
                      Mot de passe oublié ?
                    </Link>
                  </div>
                  <div class="relative">
                    <input
                      :type="showPassword ? 'text' : 'password'"
                      id="password"
                      v-model="form.password"
                      required
                      autocomplete="current-password"
                      class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                      placeholder="••••••••"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                      </svg>
                    </div>
                    <button
                      type="button"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                      @click="showPassword = !showPassword"
                    >
                      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path v-if="!showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path v-if="!showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        <path v-if="showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                      </svg>
                    </button>
                  </div>
                  <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <!-- Se souvenir de moi -->
                <div class="flex items-center">
                  <input
                    id="remember"
                    v-model="form.remember"
                    type="checkbox"
                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                  />
                  <label for="remember" class="ml-2 block text-sm text-gray-900">Se souvenir de moi</label>
                </div>

                <!-- Bouton de connexion -->
                <div>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-yellow-500 hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-300 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {{ form.processing ? 'Connexion en cours...' : 'Se connecter' }}
                  </button>
                </div>

                <!-- Lien vers inscription -->
                <div class="text-center pt-4 border-t border-gray-200">
                  <p class="text-sm text-gray-600">
                    Pas encore membre ?
                    <Link :href="r('register')" class="font-medium text-green-600 hover:text-green-700">
                      Créer un compte
                    </Link>
                  </p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
