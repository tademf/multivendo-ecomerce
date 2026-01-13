<template>
  <div class="min-h-screen overflow-hidden" :class="currentThemeClass">
    <!-- Abstract Background -->
    <div class="abstract-background">
      <!-- Minimal shapes -->
      <div class="abstract-shape shape-1"></div>
      <div class="abstract-shape shape-2"></div>
      <div class="abstract-shape shape-3"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 flex flex-col items-center justify-center min-h-screen px-4">
      <!-- Center Content -->
      <div class="max-w-lg w-full mx-auto text-center">
        <!-- Error Number - Minimal -->
        <div class="mb-8">
          <div class="inline-block relative">
            <div class="text-[120px] md:text-[180px] font-black tracking-tighter leading-none opacity-20">
              404
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="text-[160px] md:text-[240px] font-black leading-none opacity-10">
                404
              </div>
            </div>
          </div>
        </div>
        
        <!-- Main Message -->
        <div class="mb-8">
          <h1 class="text-3xl md:text-4xl font-light mb-4 theme-text-primary">
            Page not found
          </h1>
          <p class="text-lg theme-text-secondary opacity-80">
            The page you're looking for doesn't exist or has been moved.
          </p>
        </div>

        <!-- Visual Separator -->
        <div class="relative my-8">
          <div class="h-px w-24 mx-auto bg-gradient-to-r from-transparent via-current to-transparent opacity-30"></div>
          <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <div class="w-3 h-3 rounded-full theme-border opacity-50"></div>
          </div>
        </div>

        <!-- Single Action -->
        <div class="mt-12">
          <button 
            @click="goHome"
            class="single-action-btn group"
          >
            <div class="relative overflow-hidden">
              <div class="absolute inset-0 theme-action-bg"></div>
              <div class="relative flex items-center justify-center gap-3 px-8 py-4">
                <div class="w-6 h-6 flex items-center justify-center">
                  <i class="fas fa-home text-lg transition-all duration-300 group-hover:scale-110"></i>
                </div>
                <span class="text-lg font-medium">Return to safety</span>
              </div>
            </div>
          </button>
        </div>

        <!-- Footer Note -->
        <div class="mt-20">
          <div class="text-sm theme-text-muted opacity-60">
            <div class="flex items-center justify-center gap-2">
              <span>Error code: 404</span>
              <div class="w-1 h-1 rounded-full theme-bg-muted opacity-40"></div>
              <span>HTTP Status</span>
              <div class="w-1 h-1 rounded-full theme-bg-muted opacity-40"></div>
              <span>Page not found</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Right Corner -->
      <div class="absolute bottom-8 right-8">
        <div class="flex items-center gap-3">
          <div class="text-xs theme-text-muted opacity-50">
            {{ formattedTime }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

// Theme Management
const theme = ref(localStorage.getItem('theme') || 'light');
const currentThemeClass = computed(() => `${theme.value}-theme`);

// Current time for display
const currentTime = ref('');
const formattedTime = computed(() => {
  return currentTime.value;
});

// Listen to theme changes from navbar
const handleThemeChange = (event) => {
  const newTheme = event.detail.theme;
  theme.value = newTheme;
  localStorage.setItem('theme', newTheme);
  document.documentElement.setAttribute('data-theme', newTheme);
  
  document.body.className = document.body.className
    .replace('light-theme', '')
    .replace('dark-theme', '')
    .trim();
  document.body.classList.add(`${newTheme}-theme`);
};

const goHome = () => {
  router.visit('/');
};

// Update current time
const updateTime = () => {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  });
};

// Initialize theme on mount
onMounted(() => {
  // Get theme from localStorage or default to light
  const savedTheme = localStorage.getItem('theme') || 'light';
  theme.value = savedTheme;
  
  // Apply theme to HTML and body
  document.documentElement.setAttribute('data-theme', savedTheme);
  document.body.classList.add(`${savedTheme}-theme`);
  
  // Listen for theme changes from navbar
  window.addEventListener('theme-changed', handleThemeChange);
  
  // Start time update
  updateTime();
  const timeInterval = setInterval(updateTime, 30000);
  
  // Cleanup interval on unmount
  onUnmounted(() => {
    clearInterval(timeInterval);
  });
});

// Cleanup on unmount
onUnmounted(() => {
  window.removeEventListener('theme-changed', handleThemeChange);
});
</script>

<style scoped>
/* Theme Variables */
:root {
  --page-bg-light: #ffffff;
  --page-bg-dark: #0f172a;
  --text-primary-light: #1e293b;
  --text-primary-dark: #f1f5f9;
  --text-secondary-light: #64748b;
  --text-secondary-dark: #cbd5e1;
  --text-muted-light: #94a3b8;
  --text-muted-dark: #94a3b8;
  --border-color-light: #e2e8f0;
  --border-color-dark: #334155;
  --action-bg-light: rgba(15, 23, 42, 0.05);
  --action-bg-dark: rgba(255, 255, 255, 0.05);
  --action-hover-light: rgba(15, 23, 42, 0.1);
  --action-hover-dark: rgba(255, 255, 255, 0.1);
  --bg-muted-light: #e2e8f0;
  --bg-muted-dark: #334155;
}

[data-theme="dark"] {
  --page-bg: var(--page-bg-dark);
  --text-primary: var(--text-primary-dark);
  --text-secondary: var(--text-secondary-dark);
  --text-muted: var(--text-muted-dark);
  --border-color: var(--border-color-dark);
  --action-bg: var(--action-bg-dark);
  --action-hover: var(--action-hover-dark);
  --bg-muted: var(--bg-muted-dark);
}

[data-theme="light"] {
  --page-bg: var(--page-bg-light);
  --text-primary: var(--text-primary-light);
  --text-secondary: var(--text-secondary-light);
  --text-muted: var(--text-muted-light);
  --border-color: var(--border-color-light);
  --action-bg: var(--action-bg-light);
  --action-hover: var(--action-hover-light);
  --bg-muted: var(--bg-muted-light);
}

/* Base Styles */
.min-h-screen {
  background-color: var(--page-bg);
  transition: background-color 0.5s ease;
}

/* Abstract Background */
.abstract-background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  overflow: hidden;
  pointer-events: none;
}

.abstract-shape {
  position: absolute;
  border: 1px solid var(--border-color);
  opacity: 0.1;
  animation: floatAbstract 30s infinite ease-in-out;
}

.shape-1 {
  width: 400px;
  height: 400px;
  top: -200px;
  left: -200px;
  border-radius: 50%;
  animation-delay: 0s;
}

.shape-2 {
  width: 300px;
  height: 300px;
  bottom: -150px;
  right: -150px;
  border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
  animation-delay: 10s;
}

.shape-3 {
  width: 200px;
  height: 200px;
  top: 50%;
  left: 80%;
  border-radius: 63% 37% 54% 46% / 55% 48% 52% 45%;
  animation-delay: 20s;
}

@keyframes floatAbstract {
  0%, 100% {
    transform: translate(0, 0) rotate(0deg);
  }
  33% {
    transform: translate(100px, 50px) rotate(120deg);
  }
  66% {
    transform: translate(-50px, 100px) rotate(240deg);
  }
}

/* Theme Text Classes */
.theme-text-primary {
  color: var(--text-primary);
  transition: color 0.5s ease;
}

.theme-text-secondary {
  color: var(--text-secondary);
  transition: color 0.5s ease;
}

.theme-text-muted {
  color: var(--text-muted);
  transition: color 0.5s ease;
}

.theme-border {
  border-color: var(--border-color);
  transition: border-color 0.5s ease;
}

.theme-bg-muted {
  background-color: var(--bg-muted);
  transition: background-color 0.5s ease;
}

/* Action Button */
.single-action-btn {
  display: inline-block;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid var(--border-color);
  background: transparent;
  transition: all 0.3s ease;
  cursor: pointer;
}

.single-action-btn:hover {
  border-color: var(--text-primary);
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.theme-action-bg {
  background: var(--action-bg);
  transition: background 0.3s ease;
}

.single-action-btn:hover .theme-action-bg {
  background: var(--action-hover);
}

/* Dark theme specific hover */
[data-theme="dark"] .single-action-btn:hover {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

/* Smooth transitions for theme changes */
* {
  transition: color 0.5s ease,
              background-color 0.5s ease,
              border-color 0.5s ease,
              transform 0.3s ease,
              opacity 0.5s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
  .text-\[120px\] {
    font-size: 80px;
  }
  
  .text-\[160px\] {
    font-size: 100px;
  }
  
  .text-3xl {
    font-size: 1.875rem;
  }
  
  .text-lg {
    font-size: 1.125rem;
  }
  
  .single-action-btn {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .text-\[120px\] {
    font-size: 60px;
  }
  
  .text-\[160px\] {
    font-size: 80px;
  }
  
  .text-3xl {
    font-size: 1.5rem;
  }
  
  .absolute.bottom-8.right-8 {
    bottom: 4rem;
    right: 1rem;
  }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>