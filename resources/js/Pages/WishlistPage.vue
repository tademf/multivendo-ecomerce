<template>
  <AppLayout>
    <div class="wishlist-page py-5" :class="themeClasses">
      <div class="container">
        <!-- Page Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h1 class="h2 fw-bold mb-2">My Wishlist</h1>
                <p class="text-muted mb-0">
                  {{ activeItems.length }} {{ activeItems.length === 1 ? 'item' : 'items' }} in your wishlist
                  <span v-if="expiredItems.length > 0" class="ms-2 text-danger">
                    ({{ expiredItems.length }} expired)
                  </span>
                </p>
              </div>
              <div class="d-flex gap-2">
                <button
                  v-if="activeItems.length > 0"
                  @click="clearWishlist"
                  class="btn btn-outline-danger border"
                  type="button"
                  :disabled="loading"
                >
                  <i class="fas fa-trash me-2"></i>Clear All
                  <span v-if="loading" class="spinner-border spinner-border-sm ms-2"></span>
                </button>
                
                <!-- Clean Expired Button -->
                <button
                  v-if="expiredItems.length > 0"
                  @click="cleanExpiredItems"
                  class="btn btn-warning"
                  type="button"
                  :disabled="loading"
                >
                  <i class="fas fa-broom me-2"></i>Clean Expired
                  <span v-if="loading" class="spinner-border spinner-border-sm ms-2"></span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Expired Items Warning -->
        <div v-if="expiredItems.length > 0" class="alert alert-warning mb-4">
          <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div>
              <strong>Note:</strong> You have {{ expiredItems.length }} expired item(s) in your wishlist. 
              Expired items will be automatically removed.
              <button @click="cleanExpiredItems" class="btn btn-sm btn-outline-warning ms-2">
                Remove All Expired
              </button>
            </div>
          </div>
        </div>

        <!-- Wishlist Items Grid -->
        <div v-if="props.wishlistItems.length > 0" class="row g-4">
          <div
            v-for="item in props.wishlistItems"
            :key="item.id"
            class="col-6 col-md-4 col-lg-3"
          >
            <div class="card product-card h-100 border shadow-sm" :class="{ 'expired-card': item.is_expired }">
              <!-- Product Image -->
              <div class="product-image-container position-relative overflow-hidden bg-light">
                <img
                  :src="getProductImage(item.product.image)"
                  :alt="item.product.name"
                  class="product-img"
                  @error="handleImageError"
                  @click="goToProduct(item.product)"
                />

                <!-- Expired Badge -->
                <div v-if="item.is_expired" class="badge bg-danger position-absolute top-0 start-0 m-2">
                  <i class="fas fa-clock me-1"></i> Expired
                </div>
                <!-- Expiration Badge -->
                <div v-else-if="item.expired_date" class="badge bg-success position-absolute top-0 start-0 m-2">
                  <i class="fas fa-clock me-1"></i> {{ item.expires_in }}
                </div>

                <!-- Remove from Wishlist Button -->
                <button
                  @click="removeFromWishlist(item.id)"
                  class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 border-0"
                  type="button"
                  title="Remove from wishlist"
                  :disabled="loading"
                >
                  <i class="fas fa-times"></i>
                </button>

                <!-- Stock Badge -->
                <div v-if="item.product.stock <= 0" class="badge bg-danger position-absolute bottom-0 start-0 m-2">
                  <i class="fas fa-times-circle me-1"></i> Out of Stock
                </div>
                <div v-else-if="item.product.stock < 10" class="badge bg-warning text-dark position-absolute bottom-0 start-0 m-2">
                  <i class="fas fa-exclamation-triangle me-1"></i> {{ item.product.stock }} left
                </div>

                <!-- Discount Badge -->
                <div v-if="item.product.discount && item.product.discount.status === 'active'" 
                     class="badge bg-danger position-absolute bottom-0 end-0 m-2">
                  <i class="fas fa-fire me-1"></i>{{ item.product.discount.discount_amount }}% OFF
                </div>
              </div>

              <!-- Product Info -->
              <div class="card-body d-flex flex-column p-3">
                <!-- Product Name -->
                <h6 class="card-title fw-bold mb-2 text-truncate cursor-pointer" @click="goToProduct(item.product)">
                  {{ item.product.name }}
                </h6>

                <!-- Category -->
                <div class="mb-2">
                  <span class="badge bg-light text-dark border small">
                    {{ getCategoryName(item.product.category_id) }}
                  </span>
                </div>

                <!-- Price -->
                <div class="mb-3">
                  <div v-if="item.product.discount && item.product.discount.status === 'active'" 
                       class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Original Price (Strikethrough) -->
                    <span class="text-muted text-decoration-line-through small">
                      {{ formatPrice(item.product.price) }} Birr
                    </span>

                    <!-- Discounted Price -->
                    <span class="h5 fw-bold text-danger mb-0">
                      {{ formatPrice(calculateDiscountedPrice(item.product)) }} Birr
                    </span>
                  </div>
                  <div v-else>
                    <span class="h5 fw-bold text-primary mb-0">
                      {{ formatPrice(item.product.price) }} Birr
                    </span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-auto d-grid gap-2">
                  <!-- Add to Cart Button -->
                  <button
                    v-if="item.product.stock > 0 && !item.is_expired"
                    @click="addToCart(item)"
                    class="btn btn-primary"
                    type="button"
                    :disabled="loading"
                  >
                    <i class="fas fa-shopping-cart me-2"></i>
                    {{ loading ? 'Adding...' : 'Add to Cart' }}
                  </button>

                  <!-- Out of Stock -->
                  <button
                    v-else-if="item.product.stock <= 0"
                    class="btn btn-outline-danger"
                    type="button"
                    disabled
                  >
                    <i class="fas fa-times me-2"></i>Out of Stock
                  </button>

                  <!-- Expired -->
                  <button
                    v-else
                    class="btn btn-outline-secondary"
                    type="button"
                    disabled
                  >
                    <i class="fas fa-clock me-2"></i>Expired
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty Wishlist State -->
        <div v-else class="text-center py-5">
          <div class="empty-state">
            <div class="empty-icon mb-4">
              <i class="fas fa-heart fa-4x text-muted"></i>
            </div>
            <h3 class="h4 fw-bold mb-3">Your Wishlist is Empty</h3>
            <p class="text-muted mb-4">
              Save items you like to your wishlist. 
              Review them anytime and easily move them to your cart.
            </p>
            <button
              @click="continueShopping"
              class="btn btn-primary px-4 py-2"
              type="button"
            >
              <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </button>
          </div>
        </div>
      </div>

      <!-- Bootstrap Modal for Confirmations -->
      <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Confirmation</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              {{ modalMessage }}
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger" @click="confirmAction">Confirm</button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Toast Container -->
      <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055">
        <div
          class="toast align-items-center"
          :class="[`text-bg-${toast.type}`, { show: toast.show }]"
          role="alert"
        >
          <div class="d-flex">
            <div class="toast-body d-flex align-items-center">
              <i :class="toast.icon" class="me-2"></i>
              {{ toast.message }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="hideToast"></button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watchEffect } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// FIXED: Use props directly from Inertia
const { props } = usePage()
const loading = ref(false)
const modalMessage = ref('')
const pendingAction = ref(null)
const pendingItemId = ref(null)

// Dark mode
const currentTheme = ref(localStorage.getItem('theme') || 'light')

// Toast notification
const toast = ref({
  show: false,
  message: '',
  type: 'success',
  icon: 'fas fa-check-circle'
})

let confirmModal = null

// Computed
const activeItems = computed(() => {
  return props.wishlistItems.filter(item => !item.is_expired)
})

const expiredItems = computed(() => {
  return props.wishlistItems.filter(item => item.is_expired)
})

const hasItems = computed(() => {
  return props.wishlistItems.length > 0
})

const inStockItems = computed(() => {
  return activeItems.value.filter(item => item.product.stock > 0)
})

// Theme classes
const themeClasses = computed(() => {
  return {
    'bg-white': currentTheme.value === 'light',
    'bg-dark text-light': currentTheme.value === 'dark',
    'light-theme': currentTheme.value === 'light',
    'dark-theme': currentTheme.value === 'dark'
  }
})

// Watch for theme changes
watchEffect(() => {
  const html = document.documentElement
  html.setAttribute('data-theme', currentTheme.value)
  
  // Listen for theme changes from navbar
  window.addEventListener('theme-changed', (event) => {
    currentTheme.value = event.detail.theme
  })
})

// Methods
const formatPrice = (price) => {
  const num = parseFloat(price) || 0
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(num)
}

const calculateDiscountedPrice = (product) => {
  if (!product.discount || product.discount.status !== 'active') {
    return product.price
  }
  
  const originalPrice = parseFloat(product.price) || 0
  const discountPercent = parseFloat(product.discount.discount_amount) || 0
  return originalPrice * (1 - discountPercent / 100)
}

const getProductImage = (imagePath) => {
  if (!imagePath) return 'https://placehold.co/400x300/e0e7ff/667eea?text=E-SHOP'

  if (imagePath.startsWith('http') || imagePath.startsWith('/')) {
    return imagePath
  }

  return `/storage/${imagePath}`
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/400x300/e0e7ff/667eea?text=E-SHOP'
}

const getCategoryName = (categoryId) => {
  const category = props.categories?.find(cat => cat.id === categoryId)
  return category ? category.name : 'Category'
}

const removeFromWishlist = (wishlistId) => {
  modalMessage.value = 'Are you sure you want to remove this item from your wishlist?'
  pendingAction.value = 'remove'
  pendingItemId.value = wishlistId
  confirmModal.show()
}

const cleanExpiredItems = () => {
  modalMessage.value = `Are you sure you want to remove ${expiredItems.value.length} expired item(s) from your wishlist?`
  pendingAction.value = 'cleanExpired'
  confirmModal.show()
}

const clearWishlist = () => {
  modalMessage.value = 'Are you sure you want to clear your entire wishlist?'
  pendingAction.value = 'clear'
  confirmModal.show()
}

const confirmAction = async () => {
  confirmModal.hide()
  
  if (pendingAction.value === 'remove') {
    await performRemove(pendingItemId.value)
  } else if (pendingAction.value === 'cleanExpired') {
    await performCleanExpired()
  } else if (pendingAction.value === 'clear') {
    await performClear()
  }
  
  // Reset pending values
  pendingAction.value = null
  pendingItemId.value = null
}

const performRemove = async (wishlistId) => {
  loading.value = true
  
  try {
    await router.delete(route('wishlist.destroy', { id: wishlistId }), {
      preserveState: false,
      preserveScroll: true,
      onSuccess: () => {
        showToast('Item removed from wishlist', 'success')
        window.dispatchEvent(new CustomEvent('wishlist-updated'))
      },
      onError: (errors) => {
        showToast(errors.message || 'Error removing item', 'error')
      },
      onFinish: () => {
        loading.value = false
      }
    })
    
  } catch (error) {
    console.error('Error removing item:', error)
    showToast('Error removing item', 'error')
    loading.value = false
  }
}

const performCleanExpired = async () => {
  loading.value = true
  
  try {
    // Remove each expired item
    for (const item of expiredItems.value) {
      await router.delete(route('wishlist.destroy', { id: item.id }), {
        preserveState: false,
        preserveScroll: true,
        onError: (errors) => {
          console.error('Error removing expired item:', errors)
        }
      })
    }
    
    showToast(`${expiredItems.value.length} expired item(s) removed`, 'success')
    window.dispatchEvent(new CustomEvent('wishlist-updated'))
    
  } catch (error) {
    console.error('Error cleaning expired items:', error)
    showToast('Error cleaning expired items', 'error')
  } finally {
    loading.value = false
  }
}

const performClear = async () => {
  loading.value = true
  
  try {
    await router.delete(route('wishlist.clear'), {
      preserveState: false,
      preserveScroll: true,
      onSuccess: (page) => {
        showToast('Wishlist cleared successfully', 'success')
        window.dispatchEvent(new CustomEvent('wishlist-updated'))
      },
      onError: (errors) => {
        showToast(errors.message || 'Error clearing wishlist', 'error')
      },
      onFinish: () => {
        loading.value = false
      }
    })
    
  } catch (error) {
    console.error('Error clearing wishlist:', error)
    showToast('Error clearing wishlist', 'error')
    loading.value = false
  }
}

const addToCart = async (wishlistItem) => {
  if (wishlistItem.is_expired) {
    showToast('Cannot add expired items to cart', 'error')
    return
  }
  
  loading.value = true
  
  try {
    await router.post(route('cart.store'), {
      product_id: wishlistItem.product.product_id,
      quantity: 1
    }, {
      preserveState: false,
      preserveScroll: true,
      onSuccess: () => {
        performRemove(wishlistItem.id)
        showToast('Product added to cart successfully', 'success')
        window.dispatchEvent(new CustomEvent('cart-updated'))
      },
      onError: (errors) => {
        showToast(errors.message || 'Error adding to cart', 'error')
        loading.value = false
      }
    })
    
  } catch (error) {
    console.error('Error adding to cart:', error)
    showToast('Error adding to cart', 'error')
    loading.value = false
  }
}

const continueShopping = () => {
  router.visit('/')
}

const goToProduct = (product) => {
  if (!product || !product.product_id) return
  router.visit(route('product.show', product.product_id))
}

const showToast = (message, type = 'success', icon = null) => {
  const icons = {
    success: 'fas fa-check-circle',
    warning: 'fas fa-exclamation-triangle',
    error: 'fas fa-times-circle',
    info: 'fas fa-info-circle'
  }

  toast.value = {
    show: true,
    message,
    type,
    icon: icon || icons[type] || icons.success
  }

  setTimeout(() => {
    toast.value.show = false
  }, 3000)
}

const hideToast = () => {
  toast.value.show = false
}

// Initialize modal on mount
onMounted(() => {
  if (window.bootstrap) {
    const modalElement = document.getElementById('confirmModal')
    confirmModal = new window.bootstrap.Modal(modalElement)
  }
  
  // Get current theme
  currentTheme.value = localStorage.getItem('theme') || 'light'
})
</script>
<style scoped>
/* Light theme styles */
.wishlist-page.light-theme {
  background-color: #ffffff;
  color: #1e293b;
}

.wishlist-page.light-theme .card {
  background-color: #ffffff;
  border-color: #e2e8f0;
  color: #1e293b;
}

.wishlist-page.light-theme .product-image-container {
  background-color: #f8f9fa;
}

.wishlist-page.light-theme .badge.bg-light {
  background-color: #f8f9fa !important;
  color: #1e293b !important;
}

/* Dark theme styles */
.wishlist-page.dark-theme {
  background-color: #0f172a;
  color: #f1f5f9;
}

.wishlist-page.dark-theme .card {
  background-color: #1e293b;
  border-color: #334155;
  color: #f1f5f9;
}

.wishlist-page.dark-theme .product-image-container {
  background-color: #2d3748;
}

.wishlist-page.dark-theme .badge.bg-light {
  background-color: #334155 !important;
  color: #f1f5f9 !important;
  border-color: #475569 !important;
}

.wishlist-page.dark-theme .text-muted {
  color: #94a3b8 !important;
}

.wishlist-page.dark-theme .modal-content {
  background-color: #1e293b;
  color: #f1f5f9;
}

.wishlist-page.dark-theme .modal-header {
  border-bottom-color: #334155;
}

.wishlist-page.dark-theme .modal-footer {
  border-top-color: #334155;
}

.wishlist-page.dark-theme .btn-close {
  filter: invert(1) grayscale(100%) brightness(200%);
}

.wishlist-page.dark-theme .empty-state .text-muted {
  color: #94a3b8 !important;
}

/* Common styles */
.wishlist-page {
  min-height: 70vh;
}

.product-card {
  transition: all 0.3s ease;
  border-radius: 12px;
  overflow: hidden;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px var(--page-shadow-color, rgba(0, 0, 0, 0.1)) !important;
}

.product-image-container {
  height: 200px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 20px;
  transition: transform 0.3s ease;
  cursor: pointer;
}

.product-card:hover .product-img {
  transform: scale(1.05);
}

.cursor-pointer {
  cursor: pointer;
}

.cursor-pointer:hover {
  color: #667eea;
  text-decoration: underline;
}

.empty-state {
  padding: 3rem 0;
}

.empty-icon {
  opacity: 0.5;
}

.text-decoration-line-through {
  text-decoration-thickness: 2px;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Expired card styling */
.expired-card {
  opacity: 0.7;
  filter: grayscale(30%);
}

.expired-card:hover {
  transform: none !important;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
}

.wishlist-page.dark-theme .expired-card {
  opacity: 0.5;
}

/* Toast styles */
.toast {
  border-radius: 10px;
  border: none;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .product-image-container {
    height: 180px;
  }
  
  .product-img {
    padding: 15px;
  }
}

@media (max-width: 576px) {
  .product-image-container {
    height: 150px;
  }
  
  .product-img {
    padding: 10px;
  }
  
  .card-body {
    padding: 1rem !important;
  }
  
  .btn {
    padding: 0.5rem;
    font-size: 0.9rem;
  }
}

/* Wishlist grid adjustments */
@media (max-width: 1200px) {
  .col-lg-3 {
    width: 25%;
  }
}

@media (max-width: 992px) {
  .col-md-4 {
    width: 33.333%;
  }
}

@media (max-width: 768px) {
  .col-6 {
    width: 50%;
  }
}

@media (max-width: 480px) {
  .col-6 {
    width: 100%;
  }
}

/* Animation for wishlist actions */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.product-card {
  animation: fadeIn 0.3s ease-out;
}
</style>