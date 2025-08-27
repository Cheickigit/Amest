<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { route as ziggyRoute } from 'ziggy-js'
defineOptions({ layout: PublicLayout })

type Post = { id:number; slug?:string|null; title:string; excerpt?:string|null; body?:string|null; cover_image?:string|null; published_at?:string|null; tags?:string[]|null }
type More = { id:number; slug?:string|null; title:string; cover_image?:string|null; published_at?:string|null }
const props = defineProps<{ item: Post, more: More[] }>()
const route = (n:string,p?:any,a=false)=>ziggyRoute(n,p??{},a,(window as any).Ziggy)
</script>

<template>
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-white">
    <div class="flex items-center gap-3">
      <h1 class="text-3xl font-extrabold">{{ props.item.title }}</h1>
      <div class="flex-1"></div>
      <Link :href="route('public.posts')" class="btn-ghost">Toutes les actualités</Link>
    </div>

    <div class="mt-4 text-white/70 text-sm" v-if="props.item.published_at">
      Publié le {{ new Date(props.item.published_at).toLocaleDateString('fr-FR') }}
    </div>

    <div class="mt-5 rounded-2xl overflow-hidden ring-1 ring-white/10 bg-white/[.04]">
      <div class="aspect-[16/9] bg-white/5">
        <img v-if="props.item.cover_image" :src="`/storage/${props.item.cover_image}`" class="w-full h-full object-cover" alt="">
      </div>
      <div class="p-4 md:p-6 space-y-4">
        <p v-if="props.item.excerpt" class="text-white/80">{{ props.item.excerpt }}</p>
        <div v-if="props.item.body" class="prose prose-invert max-w-none" v-html="props.item.body"></div>
        <div v-if="props.item.tags?.length" class="flex flex-wrap gap-2 pt-2">
          <span v-for="t in props.item.tags" :key="t" class="chip text-xs">{{ t }}</span>
        </div>
      </div>
    </div>

    <div v-if="props.more?.length" class="mt-8">
      <div class="text-white/70 text-sm mb-3">À lire aussi</div>
      <div class="grid gap-4 sm:grid-cols-2">
        <Link v-for="m in props.more" :key="m.id"
              :href="route('public.posts.show', m.slug || m.id)"
              class="news-card ring-gold block">
          <div class="news-thumb">
            <img v-if="m.cover_image" :src="`/storage/${m.cover_image}`" class="w-full h-full object-cover opacity-90" alt="">
          </div>
          <div class="news-body">
            <h3 class="news-title">{{ m.title }}</h3>
            <div class="text-xs text-white/60 mt-1" v-if="m.published_at">{{ new Date(m.published_at).toLocaleDateString('fr-FR') }}</div>
          </div>
        </Link>
      </div>
    </div>
  </div>
</template>
