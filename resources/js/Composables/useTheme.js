import { ref, onMounted } from 'vue'

export function useTheme() {
    const theme = ref('light')
    
    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme')
        if (savedTheme) {
            theme.value = savedTheme
        } else {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                theme.value = 'dark'
            }
        }
        applyTheme()
    }
    
    const toggleTheme = () => {
        theme.value = theme.value === 'light' ? 'dark' : 'light'
        applyTheme()
        localStorage.setItem('theme', theme.value)
    }
    
    const applyTheme = () => {
        const html = document.documentElement
        const body = document.body
        
        if (theme.value === 'dark') {
            html.setAttribute('data-bs-theme', 'dark')
            body.classList.add('dark-theme')
            body.classList.remove('light-theme')
        } else {
            html.setAttribute('data-bs-theme', 'light')
            body.classList.add('light-theme')
            body.classList.remove('dark-theme')
        }
    }
    
    onMounted(() => {
        initTheme()
        
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                theme.value = e.matches ? 'dark' : 'light'
                applyTheme()
            }
        })
    })
    
    return {
        theme,
        toggleTheme,
        initTheme
    }
}