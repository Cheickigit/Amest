<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'

function handleTilt(e: MouseEvent) {
  const card = e.currentTarget as HTMLElement | null
  if (!card) return
  const c = card.getBoundingClientRect()
  const cx = e.clientX - c.left
  const cy = e.clientY - c.top
  card.style.setProperty('--rx', (((cy / c.height) - .5) * -10).toFixed(2) + 'deg')
  card.style.setProperty('--ry', (((cx / c.width) - .5) * 10).toFixed(2) + 'deg')
}
function resetTilt(e: MouseEvent) {
  const card = e.currentTarget as HTMLElement | null
  if (!card) return
  card.style.setProperty('--rx', '0deg')
  card.style.setProperty('--ry', '0deg')
}
</script>

<template>
  <PublicLayout>
    <section class="relative py-16 md:py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="chip-ma">Entreprise de BTP</span>
        <h1 class="mt-4 text-4xl md:text-6xl font-extrabold tracking-tight">
          Routes, bâtiments, ouvrages — <span class="text-bk-gold">BK Construction</span>
        </h1>
        <p class="mt-5 max-w-2xl text-white/80">
          De l’étude à la réalisation : routes, maisons, immeubles, VRD, génie civil. Délais tenus, qualité premium.
        </p>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
          <div class="tilt-3d rounded-2xl border border-white/10 p-6 bg-white/5 backdrop-blur"
               @mousemove="handleTilt" @mouseleave="resetTilt">
            <h3 class="text-xl font-bold text-bk-gold mb-2">Voirie & Routes</h3>
            <p class="text-white/80">Terrassement, assainissement, chaussées, ponts, ouvrages d’art.</p>
          </div>

          <div class="tilt-3d rounded-2xl border border-white/10 p-6 bg-white/5 backdrop-blur"
               @mousemove="handleTilt" @mouseleave="resetTilt">
            <h3 class="text-xl font-bold text-bk-gold mb-2">Bâtiments</h3>
            <p class="text-white/80">Maisons, immeubles, structures béton, finitions premium.</p>
          </div>

          <div class="tilt-3d rounded-2xl border border-white/10 p-6 bg-white/5 backdrop-blur"
               @mousemove="handleTilt" @mouseleave="resetTilt">
            <h3 class="text-xl font-bold text-bk-gold mb-2">VRD & Génie civil</h3>
            <p class="text-white/80">Réseaux, voiries, drainage, ouvrages hydrauliques.</p>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>
