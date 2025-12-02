import { ref, computed } from 'vue';

// Language files (you'll need to create these)
import en from '../lang/en.json';
import am from '../lang/am.json';

// Available languages
const languages = {
  en: { name: 'English', data: en },
  am: { name: 'Amharic', data: am }
};

// Reactive state
const currentLanguage = ref('en');
const translations = ref(languages.en.data);

export function useLanguage() {
  // Change language
  const setLanguage = (lang) => {
    if (languages[lang]) {
      currentLanguage.value = lang;
      translations.value = languages[lang].data;
      localStorage.setItem('preferred-language', lang);
    }
  };

  // Translate function
  const t = (key) => {
    return translations.value[key] || key;
  };

  // Get current language
  const getCurrentLanguage = computed(() => currentLanguage.value);

  // Get available languages
  const getAvailableLanguages = computed(() => Object.keys(languages));

  // Initialize from localStorage or browser language
  const init = () => {
    const saved = localStorage.getItem('preferred-language');
    const browserLang = navigator.language.split('-')[0];

    if (saved && languages[saved]) {
      setLanguage(saved);
    } else if (languages[browserLang]) {
      setLanguage(browserLang);
    }
  };

  // Auto-initialize
  init();

  return {
    t,
    setLanguage,
    currentLanguage: getCurrentLanguage,
    availableLanguages: getAvailableLanguages
  };
}
