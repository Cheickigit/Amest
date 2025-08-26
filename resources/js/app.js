import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js' // route() dans les templates

const appName = 'BK Construction'

createInertiaApp({
  title: (title) => `${title ? title + ' · ' : ''}${appName}`,
  resolve: (name) =>
    resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const vue = createApp({ render: () => h(App, props) })
    vue.use(plugin)
    vue.use(ZiggyVue, window.Ziggy) // fourni par @routes
    vue.mount(el)
  },
})
