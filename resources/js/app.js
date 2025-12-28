import './bootstrap'
import '../css/app.css';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Import Bootstrap
import * as bootstrap from 'bootstrap';

// Make Bootstrap available globally
window.bootstrap = bootstrap;

createInertiaApp({
  title: (title) => `${title} - ${import.meta.env.VITE_APP_NAME || 'E-Shop'}`,
  
  resolve: async (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    
    // Check if page exists
    const pagePath = `./Pages/${name}.vue`
    if (!pages[pagePath]) {
      console.error(`Page not found: ${pagePath}`)
      
      // Try to find a 404 component
      const notFoundPage = Object.keys(pages).find(path => 
        path.includes('NotFound') || path.includes('404')
      )
      
      if (notFoundPage) {
        return pages[notFoundPage]
      }
      
      // Return a simple fallback component
      return {
        default: {
          setup() {
            return () => h('div', { class: 'container py-5' }, [
              h('h1', { class: 'text-danger' }, '404 - Page Not Found'),
              h('p', `The page "${name}" could not be found.`),
              h('a', { href: '/', class: 'btn btn-primary mt-3' }, 'Go Home')
            ])
          }
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
    
    // Initialize Bootstrap tooltips and popovers globally
    vueApp.mixin({
      mounted() {
        // Initialize tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        tooltips.forEach(tooltip => {
          new bootstrap.Tooltip(tooltip)
        })
        
        // Initialize popovers
        const popovers = document.querySelectorAll('[data-bs-toggle="popover"]')
        popovers.forEach(popover => {
          new bootstrap.Popover(popover)
        })
      }
    })
    
    vueApp.mount(el)
    
    return vueApp
  },
  
  progress: {
    // Inertia progress bar configuration
    color: '#3b82f6',
    showSpinner: true,
  },
})