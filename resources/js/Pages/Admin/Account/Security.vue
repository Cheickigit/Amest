<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

defineOptions({ layout: AdminLayout })

type SessionItem = { id: string; ip: string|null; agent: string; last_activity: string; is_current: boolean }
type LogItem = { event: string; ip_address: string|null; user_agent: string|null; created_at: string }
type PageProps = {
  user: { name: string; email: string; roles: string[]; permissions: string[]; last_login_at?: string|null; last_login_ip?: string|null }
  sessions: SessionItem[]
  logs: LogItem[]
  flash?: { success?: string; error?: string }
}

const page = usePage<PageProps>()
const props = computed(() => page.props)
const route = (name: string, params?: any) => ziggyRoute(name, params, false, (window as any).Ziggy)

// -------- change password form
const pwd = useForm({ current_password: '', password: '', password_confirmation: '' })
const changePassword = () => {
  pwd.post(route('admin.account.password.update'), { preserveScroll: true, onSuccess: () => pwd.reset() })
}

// -------- logout others
const sec = useForm({ password: '' })
const logoutOthers = () => sec.post(route('admin.account.sessions.logout_others'), { preserveScroll: true, onSuccess: () => sec.reset() })

// -------- revoke one session
const revoking = ref<string|null>(null)
const revoke = (id: string) => {
  revoking.value = id
  window.setTimeout(() => {
    // delete
    const form = useForm({})
    form.delete(route('admin.account.sessions.revoke', { id }), { preserveScroll: true, onFinish: () => revoking.value = null })
  }, 0)
}

// Helpers (UA résumé)
const shortUA = (ua?: string|null) => {
  if (!ua) return 'N/A'
  const m = ua.match(/(Chrome|Firefox|Safari|Edg|Opera)\/[\d.]+/i)?.[0] ?? ua.split(' ').slice(0,2).join(' ')
  const os = ua.match(/\(([^)]+)\)/)?.[1]?.split(';')?.[0] ?? ''
  return `${m}${os ? ' — ' + os : ''}`
}
</script>

<template>
  <div class="space-y-6">
    <div v-if="$page.props.flash?.success" class="rounded-lg bg-emerald-500/10 text-emerald-300 ring-1 ring-emerald-500/30 px-3 py-2">
      {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash?.error" class="rounded-lg bg-red-500/10 text-red-300 ring-1 ring-red-500/30 px-3 py-2">
      {{ $page.props.flash.error }}
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
      <!-- Profil -->
      <div class="panel">
        <div class="panel-title">Profil & accès</div>
        <div class="mt-4 flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-white/10 ring-1 ring-white/10 grid place-items-center text-lg font-bold">
            {{ props.user.name?.substring(0,1)?.toUpperCase() }}
          </div>
          <div class="min-w-0">
            <div class="font-semibold truncate">{{ props.user.name }}</div>
            <div class="text-sm text-white/70 truncate">{{ props.user.email }}</div>
          </div>
        </div>

        <div class="mt-4 grid gap-2 text-sm">
          <div class="flex items-center justify-between">
            <span class="text-white/60">Dernière connexion</span>
            <span class="font-medium">{{ props.user.last_login_at ?? '—' }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-white/60">IP</span>
            <span class="font-medium">{{ props.user.last_login_ip ?? '—' }}</span>
          </div>
        </div>

        <div class="mt-4">
          <div class="text-xs text-white/60 mb-1">Rôles</div>
          <div class="flex flex-wrap gap-2">
            <span v-for="r in props.user.roles" :key="r" class="px-2 py-1 rounded-md text-xs bg-white/10 ring-1 ring-white/10">{{ r }}</span>
            <span v-if="!props.user.roles?.length" class="text-white/50 text-xs">Aucun</span>
          </div>
        </div>
      </div>

      <!-- Sessions -->
      <div class="panel lg:col-span-2">
        <div class="panel-title">Sessions actives</div>
        <div class="mt-3 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="text-white/60">
              <tr>
                <th class="text-left font-medium pb-2">Appareil / navigateur</th>
                <th class="text-left font-medium pb-2">IP</th>
                <th class="text-left font-medium pb-2">Dernière activité</th>
                <th class="text-left font-medium pb-2"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              <tr v-for="s in props.sessions" :key="s.id">
                <td class="py-2">{{ shortUA(s.agent) }} <span v-if="s.is_current" class="ml-2 text-[11px] px-1.5 py-0.5 rounded bg-bk-gold/20 text-bk-gold">courante</span></td>
                <td class="py-2">{{ s.ip ?? '—' }}</td>
                <td class="py-2">{{ s.last_activity }}</td>
                <td class="py-2">
                  <button
                    v-if="!s.is_current"
                    class="px-2 py-1 rounded-md text-xs bg-white/10 hover:bg-white/15 ring-1 ring-white/15"
                    :disabled="revoking===s.id"
                    @click="revoke(s.id)">
                    {{ revoking===s.id ? '...' : 'Révoquer' }}
                  </button>
                </td>
              </tr>
              <tr v-if="!props.sessions.length">
                <td class="py-3 text-white/60" colspan="4">Aucune autre session.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <form class="mt-4 grid gap-2 sm:flex sm:items-end" @submit.prevent="logoutOthers">
          <div class="sm:flex-1">
            <label class="text-xs text-white/60">Mot de passe (confirmation)</label>
            <input v-model="sec.password" type="password" required
                   class="mt-1 w-full h-10 rounded-lg bg-white/[.06] ring-1 ring-white/10 px-3 text-sm outline-none focus:ring-bk-gold/50"
                   placeholder="Votre mot de passe" />
          </div>
          <button class="h-10 px-3 rounded-lg bg-bk-gold text-bk-night font-semibold">Déconnecter les autres sessions</button>
        </form>
      </div>
    </div>

    <!-- Mot de passe + Logs -->
    <div class="grid gap-6 lg:grid-cols-3">
      <!-- Changer de mot de passe -->
      <div class="panel">
        <div class="panel-title">Changer le mot de passe</div>
        <form class="mt-3 grid gap-3" @submit.prevent="changePassword">
          <div>
            <label class="text-xs text-white/60">Mot de passe actuel</label>
            <input v-model="pwd.current_password" type="password" required
                   class="mt-1 w-full h-10 rounded-lg bg-white/[.06] ring-1 ring-white/10 px-3 text-sm outline-none focus:ring-bk-gold/50" />
            <div v-if="pwd.errors.current_password" class="text-xs text-red-300 mt-1">{{ pwd.errors.current_password }}</div>
          </div>
          <div>
            <label class="text-xs text-white/60">Nouveau mot de passe</label>
            <input v-model="pwd.password" type="password" required minlength="8"
                   class="mt-1 w-full h-10 rounded-lg bg-white/[.06] ring-1 ring-white/10 px-3 text-sm outline-none focus:ring-bk-gold/50" />
            <div class="text-[11px] text-white/50 mt-1">8+ caractères, robuste recommandé.</div>
            <div v-if="pwd.errors.password" class="text-xs text-red-300 mt-1">{{ pwd.errors.password }}</div>
          </div>
          <div>
            <label class="text-xs text-white/60">Confirmation</label>
            <input v-model="pwd.password_confirmation" type="password" required
                   class="mt-1 w-full h-10 rounded-lg bg-white/[.06] ring-1 ring-white/10 px-3 text-sm outline-none focus:ring-bk-gold/50" />
          </div>
          <button class="h-10 px-3 rounded-lg bg-bk-gold text-bk-night font-semibold"
                  :disabled="pwd.processing">
            {{ pwd.processing ? 'En cours…' : 'Mettre à jour' }}
          </button>
        </form>
      </div>

      <!-- Journal des connexions -->
      <div class="panel lg:col-span-2">
        <div class="panel-title">Historique de connexion</div>
        <div class="mt-3 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="text-white/60">
              <tr>
                <th class="text-left font-medium pb-2">Événement</th>
                <th class="text-left font-medium pb-2">IP</th>
                <th class="text-left font-medium pb-2">Navigateur</th>
                <th class="text-left font-medium pb-2">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              <tr v-for="(l, i) in props.logs" :key="i">
                <td class="py-2">
                  <span :class="[
                    'px-2 py-0.5 rounded text-xs',
                    l.event==='login_success' ? 'bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-500/30' :
                    l.event==='login_failed'  ? 'bg-red-500/15 text-red-300 ring-1 ring-red-500/30' :
                                                'bg-white/10 text-white/80 ring-1 ring-white/15'
                  ]">
                    {{ l.event }}
                  </span>
                </td>
                <td class="py-2">{{ l.ip_address ?? '—' }}</td>
                <td class="py-2">{{ shortUA(l.user_agent) }}</td>
                <td class="py-2">{{ l.created_at }}</td>
              </tr>
              <tr v-if="!props.logs.length">
                <td class="py-3 text-white/60" colspan="4">Aucun événement pour le moment.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="postcss">
.panel {
  @apply relative rounded-2xl p-[1px]
         bg-[conic-gradient(at_30%_30%,rgba(220,193,118,.8),rgba(255,255,255,.12),rgba(220,193,118,.8))];
}
.panel > * { @apply rounded-2xl bg-white/[.06] backdrop-blur ring-1 ring-white/10 p-4 sm:p-5; }
.panel-title { @apply text-base font-semibold; }
</style>
