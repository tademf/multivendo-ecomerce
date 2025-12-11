<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
      <!-- Icon -->
      <div class="mb-8">
        <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-r from-red-100 to-pink-100 flex items-center justify-center">
          <i class="fas fa-exclamation-triangle text-5xl text-red-500"></i>
        </div>
      </div>
      
      <!-- Title -->
      <h1 class="text-4xl font-bold text-gray-800 mb-4">404</h1>
      <h2 class="text-2xl font-semibold text-gray-700 mb-6">Page Not Found</h2>
      
      <!-- Message -->
      <p class="text-gray-600 mb-8">
        Oops! The page you're looking for doesn't exist or has been moved.
      </p>
      
      <!-- Actions -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button 
          @click="goBack"
          class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition duration-200 flex items-center justify-center gap-2"
        >
          <i class="fas fa-arrow-left"></i>
          Go Back
        </button>
        
        <button 
          @click="goHome"
          class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 flex items-center justify-center gap-2"
        >
          <i class="fas fa-home"></i>
          Go to Homepage
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { usePage, router } from '@inertiajs/vue3'

export default {
  name: 'NotFound',
  
  setup() {
    const goBack = () => {
      window.history.back()
    }
    
    const goHome = () => {
      router.visit('/')
    }
    
    return {
      goBack,
      goHome
    }
  }
}
</script>