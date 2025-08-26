<script setup>
import { Head } from '@inertiajs/vue3'
import ClientLayout from '@/Layouts/ClientLayout.vue'
defineOptions({ layout: ClientLayout })
</script>

<template>
  <Head title="Portail client — BK Construction" />
  <div class="grid gap-6 md:grid-cols-2">
    <section class="rounded-2xl border border-white/10 p-6 bg-white/5">
      <h2 class="font-semibold mb-2">Mes projets</h2>
      <p class="text-sm text-bk-off/70">Liste à venir…</p>
    </section>
    <section id="docs" class="rounded-2xl border border-white/10 p-6 bg-white/5">
      <h2 class="font-semibold mb-2">Documents</h2>
      <p class="text-sm text-bk-off/70">Plans, rapports, photos…</p>
    </section>
    <section id="invoices" class="rounded-2xl border border-white/10 p-6 bg-white/5">
      <h2 class="font-semibold mb-2">Factures</h2>
      <p class="text-sm text-bk-off/70">Factures et échéances…</p>
    </section>
    <section id="support" class="rounded-2xl border border-white/10 p-6 bg-white/5">
      <h2 class="font-semibold mb-2">Support</h2>
      <p class="text-sm text-bk-off/70">Messagerie et suivi.</p>
    </section>
  </div>
</template>
