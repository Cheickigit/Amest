<template>
  <PublicLayout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-yellow-50 py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
            Annonces & <span class="text-green-600">Archives</span>
          </h1>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Retrouvez toutes les annonces importantes et archives de l'association
          </p>
        </div>

        <!-- Contenu temporaire -->
        <div class="bg-white rounded-2xl p-8 shadow-lg">
          <h2 class="text-2xl font-bold mb-4 text-gray-900">Page en construction</h2>
          <p class="text-gray-700 mb-6">
            Cette section sera bientôt disponible avec toutes les annonces et archives.
          </p>

          <!-- Statistiques (optionnel) -->
          <div v-if="$page.props.stats" class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="bg-green-50 p-6 rounded-xl text-center">
              <div class="text-3xl font-bold text-green-700">{{ $page.props.stats.total }}</div>
              <div class="text-gray-600">Annonces publiées</div>
            </div>
            <div class="bg-yellow-50 p-6 rounded-xl text-center">
              <div class="text-3xl font-bold text-yellow-700">{{ $page.props.stats.important_count }}</div>
              <div class="text-gray-600">Annonces importantes</div>
            </div>
            <div class="bg-red-50 p-6 rounded-xl text-center">
              <div class="text-3xl font-bold text-red-700">{{ $page.props.archives?.length || 0 }}</div>
              <div class="text-gray-600">Dans les archives</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
</script>
