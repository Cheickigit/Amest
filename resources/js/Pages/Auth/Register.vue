<!-- resources/js/Pages/Auth/Register.vue -->
<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

const r = (name: string, params?: any, absolute = false) =>
  ziggyRoute(name, params ?? {}, absolute, (window as any).Ziggy)

const showPass = ref(false)
const showConf = ref(false)

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
})

const jetstream = computed(() => (usePage().props as any)?.jetstream ?? {})

function submit() {
  form.post(r('register'), { onFinish: () => form.reset('password', 'password_confirmation') })
}
</script>

<template>
  <Head title="Créer un compte — BKO Construction" />
  <div class="min-h-screen bg-bk-night text-bk-off">
    <div class="fixed inset-0 -z-10 pointer-events-none
                bg-[radial-gradient(1000px_520px_at_110%_-10%,rgba(220,193,118,.08),transparent),linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02))]" />
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
      <div class="grid lg:grid-cols-2 gap-8 items-stretch">
        <div class="hidden lg:flex">
          <div class="relative rounded-3xl p-[1px] w-full
                      bg-[conic-gradient(at_30%_30%,rgba(220,193,118,.85),rgba(255,255,255,.12),rgba(220,193,118,.85))]">
            <div class="rounded-3xl h-full p-8 bg-white/[.06] backdrop-blur ring-1 ring-white/10 flex flex-col">
              <div class="flex items-center gap-3">
                <img src="/assets/logo-bk.jpeg" alt="BKO" class="h-10 w-auto rounded-sm" />
                <div class="font-display text-2xl tracking-tight">
                  BKO <span class="text-bk-gold">Construction</span>
                </div>
              </div>
              <div class="mt-5">
                <div class="text-3xl font-extrabold leading-tight">Créer votre accès</div>
                <p class="mt-2 text-white/70">Gérez contenus et demandes en toute simplicité.</p>
              </div>
              <div class="mt-auto pt-8 text-xs text-white/50">
                Déjà inscrit ?
                <Link :href="r('login')" class="text-bk-gold hover:underline">Se connecter</Link>
              </div>
            </div>
          </div>
        </div>

        <div class="flex">
          <div class="relative rounded-3xl p-[1px] w-full
                      bg-[conic-gradient(at_22%_20%,rgba(220,193,118,.85),rgba(255,255,255,.12),rgba(220,193,118,.85))]">
            <div class="rounded-3xl h-full p-6 sm:p-8 bg-white/[.06] backdrop-blur ring-1 ring-white/10">
              <div class="flex items-center gap-3 lg:hidden">
                <img src="/assets/logo-bk.jpeg" alt="BKO" class="h-9 w-auto rounded-sm" />
                <div class="font-display text-xl">BKO <span class="text-bk-gold">Construction</span></div>
              </div>

              <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold">Créer un compte</h1>
              <p class="text-white/70 mt-1 text-sm">Renseignez vos informations ci-dessous.</p>

              <form class="mt-6 grid gap-4" @submit.prevent="submit">
                <div>
                  <label for="name" class="text-sm text-white/70">Nom</label>
                  <input id="name" v-model="form.name" type="text" required autocomplete="name"
                         class="mt-1 w-full h-11 rounded-xl px-3 bg-white/[.06] ring-1 ring-white/15 outline-none
                                focus:ring-bk-gold/60 placeholder:text-white/40" placeholder="Votre nom" />
                  <div v-if="form.errors.name" class="mt-1 text-sm text-red-300">{{ form.errors.name }}</div>
                </div>

                <div>
                  <label for="email" class="text-sm text-white/70">Email</label>
                  <input id="email" v-model="form.email" type="email" required autocomplete="username"
                         class="mt-1 w-full h-11 rounded-xl px-3 bg-white/[.06] ring-1 ring-white/15 outline-none
                                focus:ring-bk-gold/60 placeholder:text-white/40" placeholder="prenom@exemple.com" />
                  <div v-if="form.errors.email" class="mt-1 text-sm text-red-300">{{ form.errors.email }}</div>
                </div>

                <div>
                  <label for="password" class="text-sm text-white/70">Mot de passe</label>
                  <div class="mt-1 relative">
                    <input :type="showPass ? 'text' : 'password'" id="password" v-model="form.password" required autocomplete="new-password"
                           class="w-full h-11 rounded-xl px-3 pr-10 bg-white/[.06] ring-1 ring-white/15 outline-none
                                  focus:ring-bk-gold/60 placeholder:text-white/40" placeholder="••••••••" />
                    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-white/60 hover:text-white"
                            @click="showPass = !showPass">{{ showPass ? '🙈' : '👁️' }}</button>
                  </div>
                  <div v-if="form.errors.password" class="mt-1 text-sm text-red-300">{{ form.errors.password }}</div>
                </div>

                <div>
                  <label for="password_confirmation" class="text-sm text-white/70">Confirmer le mot de passe</label>
                  <div class="mt-1 relative">
                    <input :type="showConf ? 'text' : 'password'" id="password_confirmation" v-model="form.password_confirmation"
                           required autocomplete="new-password"
                           class="w-full h-11 rounded-xl px-3 pr-10 bg-white/[.06] ring-1 ring-white/15 outline-none
                                  focus:ring-bk-gold/60 placeholder:text-white/40" placeholder="••••••••" />
                    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-white/60 hover:text-white"
                            @click="showConf = !showConf">{{ showConf ? '🙈' : '👁️' }}</button>
                  </div>
                  <div v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-300">
                    {{ form.errors.password_confirmation }}
                  </div>
                </div>

                <div v-if="jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-1">
                  <label class="inline-flex items-center gap-2 text-sm text-white/70 select-none">
                    <input id="terms" type="checkbox" v-model="form.terms" required
                           class="w-4 h-4 rounded border-white/20 bg-white/10 text-bk-gold focus:ring-bk-gold/30" />
                    <span>
                      J’accepte les
                      <a target="_blank" :href="r('terms.show')" class="text-bk-gold hover:underline">Conditions</a>
                      et la
                      <a target="_blank" :href="r('policy.show')" class="text-bk-gold hover:underline">Politique de confidentialité</a>
                    </span>
                  </label>
                  <div v-if="form.errors.terms" class="mt-1 text-sm text-red-300">{{ form.errors.terms }}</div>
                </div>

                <div class="pt-2">
                  <button type="submit" :disabled="form.processing"
                          :class="['inline-flex items-center justify-center w-full h-11 rounded-xl font-semibold transition',
                                   'bg-bk-gold text-bk-night shadow-[0_16px_40px_-16px_rgba(220,193,118,.45)] hover:-translate-y-0.5',
                                   form.processing ? 'opacity-60 cursor-not-allowed' : '']">
                    <span v-if="!form.processing">Créer le compte</span>
                    <span v-else>Création…</span>
                  </button>

                  <div class="mt-3 text-center text-sm text-white/60">
                    Déjà un compte ?
                    <Link :href="r('login')" class="font-semibold text-bk-gold hover:underline">Se connecter</Link>
                  </div>
                </div>

                <div v-if="form.hasErrors" class="text-sm text-red-300/90">
                  <ul class="list-disc pl-5 space-y-1">
                    <li v-for="(msg, key) in form.errors" :key="key">{{ msg }}</li>
                  </ul>
                </div>
              </form>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>
