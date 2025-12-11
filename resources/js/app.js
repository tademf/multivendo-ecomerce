import './bootstrap'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

createInertiaApp({
  title: (title) => `${title} - ${import.meta.env.VITE_APP_NAME || 'E-Shop'}`,
  
  resolve: async (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    
    // Check if page exists
    const pagePath = `./Pages/${name}.vue`
    if (!pages[pagePath]) {
      console.error(`Page not found: ${pagePath}`)
      // Return a fallback component or 404 page
      return pages['./Pages/NotFound.vue'] || {
        default: {
          render: () => h('div', 'Page not found')
        }
      }
    }
    
    return pages[pagePath]
  },
  
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) })
    
    // Use Inertia plugin
    vueApp.use(plugin)
    
    // Use Ziggy for Laravel routes
    vueApp.use(ZiggyVue)
    
    vueApp.mount(el)
    
    return vueApp
  },
})