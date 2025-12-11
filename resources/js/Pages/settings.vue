<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4">
      <div class="max-w-6xl mx-auto">
        <!-- Header with Back Button -->
        <div class="mb-10">
          <div class="flex items-center justify-between mb-6">
            <Link 
              :href="route('home')" 
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all transform hover:-translate-y-0.5 hover:shadow-md"
            >
              <i class="fas fa-arrow-left mr-2"></i>
              Back to Home
            </Link>
          </div>
          
          <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Settings</h1>
          <p class="text-gray-600">Manage your account preferences and settings</p>
        </div>

        <!-- Coming Soon Content -->
        <div class="flex flex-col items-center justify-center py-12">
          <!-- Animated Icon -->
          <div class="relative mb-8">
            <div class="w-32 h-32 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center animate-pulse">
              <i class="fas fa-cogs text-white text-5xl"></i>
            </div>
            
            <!-- Rotating gear icons -->
            <div class="absolute -top-2 -right-2 w-16 h-16 bg-blue-400 rounded-full flex items-center justify-center animate-spin-slow">
              <i class="fas fa-cog text-white text-xl"></i>
            </div>
            <div class="absolute -bottom-2 -left-2 w-16 h-16 bg-purple-400 rounded-full flex items-center justify-center animate-spin-slow-reverse">
              <i class="fas fa-cog text-white text-xl"></i>
            </div>
          </div>

          <!-- Coming Soon Text -->
          <div class="text-center max-w-2xl">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
              Coming <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-600">Soon</span>
            </h2>
            
            <p class="text-xl text-gray-600 mb-8">
              We're working hard to bring you a comprehensive settings page. 
              Stay tuned for updates!
            </p>

            <!-- Progress Bar -->
            <div class="mb-8">
              <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Development Progress</span>
                <span>65%</span>
              </div>
              <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-500 to-purple-600 rounded-full w-2/3 animate-progress"></div>
              </div>
            </div>

            <!-- Features Coming -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
              <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                  <i class="fas fa-user-cog text-blue-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Account Settings</h3>
                <p class="text-gray-600 text-sm">Manage your profile, password, and preferences</p>
              </div>
              
              <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                  <i class="fas fa-bell text-purple-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Notifications</h3>
                <p class="text-gray-600 text-sm">Customize email and push notifications</p>
              </div>
              
              <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                  <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Security</h3>
                <p class="text-gray-600 text-sm">Two-factor authentication and security settings</p>
              </div>
            </div>

            <!-- Countdown Timer -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8">
              <h3 class="font-semibold text-gray-800 mb-4 text-center">Expected Launch</h3>
              <div class="flex justify-center space-x-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-blue-600">15</div>
                  <div class="text-sm text-gray-600">Days</div>
                </div>
                <div class="text-2xl font-bold text-gray-300">:</div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-purple-600">08</div>
                  <div class="text-sm text-gray-600">Hours</div>
                </div>
                <div class="text-2xl font-bold text-gray-300">:</div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-blue-600">30</div>
                  <div class="text-sm text-gray-600">Minutes</div>
                </div>
              </div>
            </div>

            <!-- Contact Support -->
            <div class="mt-8 text-sm text-gray-500">
              <p>Need immediate assistance? 
                <a href="mailto:temuclassic986@gmail.com" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                  Contact our support team
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>

<style scoped>
/* Custom animations */
@keyframes spin-slow {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@keyframes spin-slow-reverse {
  from {
    transform: rotate(360deg);
  }
  to {
    transform: rotate(0deg);
  }
}

@keyframes progress {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.animate-spin-slow {
  animation: spin-slow 8s linear infinite;
}

.animate-spin-slow-reverse {
  animation: spin-slow-reverse 10s linear infinite;
}

.animate-progress {
  background-size: 200% 200%;
  animation: progress 2s ease-in-out infinite;
}

/* Ensure proper stacking context */
.relative {
  position: relative;
}

.absolute {
  position: absolute;
}

/* Ensure proper transition timing */
.transition-shadow {
  transition: box-shadow 0.3s ease;
}

.transition-all {
  transition: all 0.3s ease;
}
</style>