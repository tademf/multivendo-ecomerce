<template>
  <div class="app-layout">
    <AppNavbar />
    <main class="main-content">
      <slot />
    </main>
    <AppFooter />
  </div>
</template>

<script>
import AppNavbar from '@/Components/AppNavbar.vue'
import AppFooter from '@/Components/AppFooter.vue'

export default {
  name: 'AppLayout',
  components: {
    AppNavbar,
    AppFooter,
  }
}
</script>

<style scoped>
.app-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding: 2rem 0;
}

@media (max-width: 768px) {
  .main-content {
    padding: 1rem 0;
  }
}
</style>