<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
const open = ref(false)
const page = usePage()
const user = page.props?.auth?.user
</script>

<template>
  <div class="min-h-screen bg-bk-night text-bk-off">
    <header class="sticky top-0 z-40 border-b border-white/10 bg-bk-night/80 backdrop-blur">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <Link :href="route('home')" class="flex items-center gap-2">
          <img src="/assets/logo-bk.png" class="h-8" alt="">
          <span class="font-display text-xl tracking-wide">BK <span class="text-bk-gold">Client</span></span>
        </Link>
        <nav class="hidden md:flex items-center gap-6 text-sm">
          <Link href="/client" class="hover:text-bk-gold">Mes projets</Link>
          <Link href="/client#docs" class="hover:text-bk-gold">Documents</Link>
          <Link href="/client#invoices" class="hover:text-bk-gold">Factures</Link>
          <Link href="/client#support" class="hover:text-bk-gold">Support</Link>
        </nav>
        <div class="hidden md:flex items-center gap-3">
          <span class="text-sm text-bk-off/80">{{ user?.name }}</span>
          <form method="POST" :action="route('logout')">
            <input type="hidden" name="_token" :value="$page.props.csrf_token">
            <button class="text-sm hover:text-bk-gold">Déconnexion</button>
          </form>
        </div>
        <button class="md:hidden p-2" @click="open = !open">
          <svg viewBox="0 0 24 24" class="w-6 h-6"><path fill="currentColor" d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
      </div>
      <div v-show="open" class="md:hidden border-t border-white/10 px-4 py-3 space-y-2 text-sm">
        <Link href="/client" class="block">Mes projets</Link>
        <Link href="/client#docs" class="block">Documents</Link>
        <Link href="/client#invoices" class="block">Factures</Link>
        <Link href="/client#support" class="block">Support</Link>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>
  </div>
</template>
