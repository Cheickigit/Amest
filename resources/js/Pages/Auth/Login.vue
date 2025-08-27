<!-- resources/js/Pages/Auth/Login.vue -->
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

/* Icônes (paths) */
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
  <Head title="Connexion — BKO Construction" />
  <div class="min-h-screen bg-bk-night text-bk-off relative overflow-hidden">
    <!-- fond premium -->
    <div class="pointer-events-none absolute inset-0 -z-10">
      <div class="absolute inset-0
                  bg-[radial-gradient(1200px_600px_at_-10%_-20%,rgba(220,193,118,.08),transparent),linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.02))]" />
      <div class="absolute -top-24 -right-24 w-[520px] h-[520px] rounded-full blur-3xl opacity-20"
           style="background: radial-gradient(closest-side, #dcc176 20%, transparent)"></div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
      <div class="grid lg:grid-cols-2 gap-8 items-stretch">
        <!-- panneau gauche (brand) -->
        <div class="hidden lg:flex">
          <div class="relative rounded-3xl p-[1px] w-full
                      bg-[conic-gradient(at_18%_22%,rgba(220,193,118,.85),rgba(255,255,255,.12),rgba(220,193,118,.85))] shadow-[0_40px_120px_-40px_rgba(220,193,118,.35)]">
            <div class="rounded-3xl h-full p-8 bg-white/[.06] backdrop-blur ring-1 ring-white/10 flex flex-col">
              <div class="flex items-center gap-3">
                <img src="/assets/logo-bk.jpeg" alt="BKO" class="h-11 w-auto rounded-sm ring-1 ring-white/10" />
                <div class="font-display text-2xl tracking-tight">
                  BKO <span class="text-bk-gold">Construction</span>
                </div>
              </div>

              <div class="mt-6 text-white/85">
                <div class="text-4xl font-extrabold leading-tight">Back-office</div>
                <p class="mt-3 text-white/70">Pilotez réalisations, leads, appels d’offres et contenus.</p>
              </div>

              <div class="mt-8 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-2xl p-4 bg-white/[.04] ring-1 ring-white/10">
                  <div class="text-white/70">Sécurité</div>
                  <div class="text-white font-semibold">2FA, sessions contrôlées</div>
                </div>
                <div class="rounded-2xl p-4 bg-white/[.04] ring-1 ring-white/10">
                  <div class="text-white/70">Traçabilité</div>
                  <div class="text-white font-semibold">Historique & pièces</div>
                </div>
              </div>

              <div class="mt-auto pt-10 text-xs text-white/50">© {{ new Date().getFullYear() }} BKO Construction</div>
            </div>
          </div>
        </div>

        <!-- panneau droit (form) -->
        <div class="flex">
          <div class="relative rounded-3xl p-[1px] w-full
                      bg-[conic-gradient(at_22%_20%,rgba(220,193,118,.85),rgba(255,255,255,.12),rgba(220,193,118,.85))]">
            <div class="rounded-3xl h-full p-6 sm:p-8 bg-white/[.06] backdrop-blur ring-1 ring-white/10">
              <div class="flex items-center gap-3 lg:hidden">
                <img src="/assets/logo-bk.jpeg" alt="BKO" class="h-9 w-auto rounded-sm ring-1 ring-white/10" />
                <div class="font-display text-xl">BKO <span class="text-bk-gold">Construction</span></div>
              </div>

              <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight">Connexion</h1>
              <p class="text-white/70 mt-1 text-sm">Accédez à l’administration.</p>

              <div v-if="props.status"
                   class="mt-4 text-sm font-medium text-emerald-300 bg-emerald-500/10 ring-1 ring-emerald-500/20 rounded-lg px-3 py-2">
                {{ props.status }}
              </div>

              <form class="mt-6 grid gap-4" @submit.prevent="submit">
                <!-- email -->
                <div>
                  <label for="email" class="text-sm text-white/70">Email</label>
                  <div class="mt-1 relative">
                    <input id="email" v-model="form.email" type="email" required autocomplete="username"
                           class="peer w-full h-12 rounded-xl pl-10 pr-3 bg-white/[.06] ring-1 ring-white/15 outline-none
                                  focus:ring-bk-gold/60 placeholder:text-white/40"
                           placeholder="prenom@exemple.com" />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-white/60" viewBox="0 0 24 24">
                      <path :d="icons.mail" fill="currentColor" />
                    </svg>
                  </div>
                  <div v-if="form.errors.email" class="mt-1 text-sm text-red-300">{{ form.errors.email }}</div>
                </div>

                <!-- password -->
                <div>
                  <div class="flex items-center justify-between">
                    <label for="password" class="text-sm text-white/70">Mot de passe</label>
                    <Link v-if="canResetPassword" :href="r('password.request')" class="text-xs text-white/60 hover:text-bk-gold">
                      Mot de passe oublié ?
                    </Link>
                  </div>
                  <div class="mt-1 relative">
                    <input :type="showPassword ? 'text' : 'password'" id="password" v-model="form.password"
                           required autocomplete="current-password"
                           class="w-full h-12 rounded-xl pl-10 pr-10 bg-white/[.06] ring-1 ring-white/15 outline-none
                                  focus:ring-bk-gold/60 placeholder:text-white/40" placeholder="••••••••" />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-white/60" viewBox="0 0 24 24">
                      <path :d="icons.lock" fill="currentColor" />
                    </svg>
                    <button type="button"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-white/60 hover:text-white p-1 rounded-md"
                            @click="showPassword = !showPassword" :aria-label="showPassword ? 'Masquer' : 'Afficher'">
                      <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path :d="eyePath" fill="currentColor" />
                      </svg>
                    </button>
                  </div>
                  <div v-if="form.errors.password" class="mt-1 text-sm text-red-300">{{ form.errors.password }}</div>
                </div>

                <!-- remember -->
                <label class="inline-flex items-center gap-2 select-none">
                  <input type="checkbox" v-model="form.remember"
                         class="w-4 h-4 rounded border-white/20 bg-white/10 text-bk-gold focus:ring-bk-gold/30" />
                  <span class="text-sm text-white/70">Se souvenir de moi</span>
                </label>

                <!-- submit -->
                <div class="pt-2">
                  <button type="submit" :disabled="form.processing"
                          :class="['inline-flex items-center justify-center w-full h-12 rounded-xl font-semibold transition',
                                   'bg-bk-gold text-bk-night shadow-[0_20px_48px_-16px_rgba(220,193,118,.45)] hover:-translate-y-0.5',
                                   form.processing ? 'opacity-60 cursor-not-allowed' : '']">
                    <span>Se connecter</span>
                    <svg class="ml-2 w-4 h-4" viewBox="0 0 24 24"><path :d="icons.arrowR" fill="currentColor"/></svg>
                  </button>
                </div>

                <div v-if="form.hasErrors" class="text-sm text-red-300/90">
                  <ul class="list-disc pl-5 space-y-1">
                    <li v-for="(msg, key) in form.errors" :key="key">{{ msg }}</li>
                  </ul>
                </div>
              </form>

              <!-- pas de lien vers register ✅ -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
