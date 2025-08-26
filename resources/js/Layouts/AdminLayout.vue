<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const sidebar = ref(false)
const page = usePage()
const url = computed(() => page.url)
const user = page.props?.auth?.user
const can = (perm) => user?.permissions?.includes(perm)
const has = (role) => user?.roles?.includes(role)
const isActive = (p) => url.value === p || url.value.startsWith(p + '/')
</script>

<template>
  <div class="min-h-screen bg-bk-night text-bk-off flex">
    <aside :class="['fixed md:static z-40 inset-y-0 left-0 w-72 bg-white/5 border-r border-white/10 backdrop-blur p-4 transform transition-transform',
                    sidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0']">
      <div class="flex items-center gap-2 px-2 h-12">
        <img src="/assets/logo-bk.png" class="h-8" alt="">
        <div class="font-display text-xl">BK <span class="text-bk-gold">Admin</span></div>
      </div>

      <nav class="mt-4 space-y-1 text-sm">
        <Link href="/admin" :class="['block px-3 py-2 rounded-lg', isActive('/admin') ? 'bg-bk-gold text-bk-night' : 'hover:bg-white/10']">
          Tableau de bord
        </Link>

        <Link v-if="can('projects.view')" href="/admin/projects"
              :class="['block px-3 py-2 rounded-lg', isActive('/admin/projects') ? 'bg-bk-gold text-bk-night' : 'hover:bg-white/10']">
          Projets
        </Link>

        <Link v-if="can('quotes.view')" href="/admin/quotes"
              :class="['block px-3 py-2 rounded-lg', isActive('/admin/quotes') ? 'bg-bk-gold text-bk-night' : 'hover:bg-white/10']">
          Devis & leads
        </Link>

        <Link v-if="can('rfps.view')" href="/admin/rfps"
              :class="['block px-3 py-2 rounded-lg', isActive('/admin/rfps') ? 'bg-bk-gold text-bk-night' : 'hover:bg-white/10']">
          Appels d’offres
        </Link>

        <Link v-if="can('reports.view')" href="/admin/reports"
              :class="['block px-3 py-2 rounded-lg', isActive('/admin/reports') ? 'bg-bk-gold text-bk-night' : 'hover:bg-white/10']">
          Rapports de chantier
        </Link>

        <Link v-if="can('clients.view')" href="/admin/clients"
              :class="['block px-3 py-2 rounded-lg', isActive('/admin/clients') ? 'bg-bk-gold text-bk-night' : 'hover:bg-white/10']">
          Clients & contrats
        </Link>

        <!-- Réservé admin -->
        <Link v-if="can('cms.manage')" href="/admin/blog"
              :class="['block px-3 py-2 rounded-lg', isActive('/admin/blog') ? 'bg-bk-gold text-bk-night' : 'hover:bg-white/10']">
          Blog / CMS
        </Link>

        <Link v-if="can('users.manage')" href="/admin/users"
              :class="['block px-3 py-2 rounded-lg', isActive('/admin/users') ? 'bg-bk-gold text-bk-night' : 'hover:bg-white/10']">
          Utilisateurs & rôles
        </Link>

        <Link v-if="has('admin')" href="/admin/settings"
              :class="['block px-3 py-2 rounded-lg', isActive('/admin/settings') ? 'bg-bk-gold text-bk-night' : 'hover:bg-white/10']">
          Paramètres
        </Link>
      </nav>
    </aside>

    <div class="flex-1 min-w-0 md:ml-0">
      <header class="sticky top-0 z-30 h-14 border-b border-white/10 bg-bk-night/80 backdrop-blur flex items-center">
        <button class="p-3 md:hidden" @click="sidebar=!sidebar">
          <svg viewBox="0 0 24 24" class="w-6 h-6"><path fill="currentColor" d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
        <div class="px-4 font-semibold">Back-office BK</div>
        <div class="ml-auto px-4 flex items-center gap-3">
          <Link :href="route('home')" class="text-sm hover:text-bk-gold">Site</Link>
          <form method="POST" :action="route('logout')">
            <input type="hidden" name="_token" :value="$page.props.csrf_token">
            <button class="text-sm hover:text-bk-gold">Déconnexion</button>
          </form>
        </div>
      </header>

      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
