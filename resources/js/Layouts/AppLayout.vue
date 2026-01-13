<template>
  <div class="app-layout">
    <AppNavbar />
    <main class="main-content">
      <slot />
    </main>
    <AppFooter />
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppNavbar from '@/Components/AppNavbar.vue'
import AppFooter from '@/Components/AppFooter.vue'

const page = usePage()

/**
 * በ OTP ሎጊን ሲደረግ ወይም ገጽ ሲቀየር የሚቀሩ 
 * የሞዳል ጥላዎችን (Backdrops) ለማጽዳት ይረዳል
 */
const cleanUpModals = () => {
  // የ body ስታይልን ወደ መደበኛ መመለስ
  document.body.classList.remove('modal-open');
  document.body.style.overflow = '';
  document.body.style.paddingRight = '';
  
  // ማንኛውንም የተረፈ .modal-backdrop ማስወገድ
  const backdrops = document.querySelectorAll('.modal-backdrop');
  backdrops.forEach(backdrop => {
    backdrop.remove();
  });
}

onMounted(() => {
  cleanUpModals();
});

// በ Inertia ገጾች መካከል ስትቀያየር ሁሌም ቼክ ያደርጋል
watch(() => page.url, () => {
  cleanUpModals();
});
</script>

<style scoped>
.app-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding: 0rem 0;
}

@media (max-width: 768px) {
  .main-content {
    padding: 1rem 0;
  }
}
</style>