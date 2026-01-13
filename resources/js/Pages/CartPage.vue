<template>
  <AppLayout>
    <div class="cart-page py-5" :class="themeClasses">
      <div class="container">
        <!-- Page Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h1 class="h2 fw-bold mb-2">Shopping Cart</h1>
                <p class="text-muted mb-0">
                  {{ activeItems.length }} {{ activeItems.length === 1 ? 'item' : 'items' }} in your cart
                  <span v-if="expiredItems.length > 0" class="ms-2 text-danger">
                    ({{ expiredItems.length }} expired)
                  </span>
                </p>
              </div>
              <div class="d-flex gap-2">
                <button
                  v-if="activeItems.length > 0"
                  @click="clearCart"
                  class="btn btn-outline-danger"
                  type="button"
                  :disabled="loading"
                >
                  <i class="fas fa-trash me-2"></i>Clear Cart
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
              <strong>Note:</strong> You have {{ expiredItems.length }} expired item(s) in your cart. 
              Expired items will be automatically removed and cannot be checked out.
              <button @click="cleanExpiredItems" class="btn btn-sm btn-outline-warning ms-2">
                Remove All Expired
              </button>
            </div>
          </div>
        </div>

        <!-- Cart Content -->
        <div v-if="props.cartItems.length > 0" class="row">
          <!-- Cart Items List -->
          <div class="col-lg-12">
            <div class="card border shadow-sm">
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover mb-0">
                    <thead class="table-light">
                      <tr>
                        <th class="border-0" style="width: 120px;">Product</th>
                        <th class="border-0">Details</th>
                        <th class="border-0 text-center">Quantity</th>
                        <th class="border-0 text-end">Price</th>
                        <th class="border-0 text-end">Total</th>
                        <th class="border-0 text-center">Expires In</th>
                        <th class="border-0 text-center">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in props.cartItems" :key="item.id" 
                          :class="{'expired-row': item.is_expired}" 
                          class="align-middle">
                        <!-- Product Image -->
                        <td>
                          <div class="cart-product-image" @click="goToProduct(item.product)">
                            <img
                              :src="getProductImage(item.product.image)"
                              :alt="item.product.name"
                              class="img-fluid rounded"
                              @error="handleImageError"
                            />
                          </div>
                        </td>
                        
                        <!-- Product Details -->
                        <td>
                          <div>
                            <h6 class="fw-bold mb-1 cursor-pointer hover-text-primary" @click="goToProduct(item.product)">
                              {{ item.product.name }}
                              <span v-if="item.is_expired" class="badge bg-danger ms-2">
                                <i class="fas fa-clock me-1"></i>Expired
                              </span>
                            </h6>
                            <p class="text-muted small mb-2">
                              <span class="badge bg-light text-dark me-2 border">
                                {{ getCategoryName(item.product.category_id) }}
                              </span>
                              <span v-if="item.product.stock > 0" class="badge bg-success">
                                In Stock
                              </span>
                              <span v-else class="badge bg-danger">
                                Out of Stock
                              </span>
                            </p>
                            <div class="d-flex align-items-center gap-3">
                              <span class="text-primary fw-bold">
                                {{ formatPrice(item.price) }} Birr
                              </span>
                              <span v-if="item.product.discount && item.product.discount.status === 'active'" 
                                    class="badge bg-danger">
                                {{ item.product.discount.discount_amount }}% OFF
                              </span>
                            </div>
                          </div>
                        </td>
                        
                        <!-- Quantity -->
                        <td class="text-center">
                          <div class="quantity-control d-inline-flex align-items-center">
                            <button
                              @click="updateQuantity(item, item.quantity - 1)"
                              :disabled="item.quantity <= 1 || loading || item.is_expired"
                              class="btn btn-outline-secondary btn-sm border"
                              type="button"
                            >
                              <i class="fas fa-minus"></i>
                            </button>
                            <input
                              type="number"
                              v-model="item.quantity"
                              @change="updateQuantity(item, $event.target.value)"
                              min="1"
                              :max="item.product.stock"
                              class="form-control form-control-sm text-center mx-2 border"
                              style="width: 60px;"
                              :disabled="loading || item.is_expired"
                            />
                            <button
                              @click="updateQuantity(item, item.quantity + 1)"
                              :disabled="item.quantity >= item.product.stock || loading || item.is_expired"
                              class="btn btn-outline-secondary btn-sm border"
                              type="button"
                            >
                              <i class="fas fa-plus"></i>
                            </button>
                          </div>
                          <div class="small text-muted mt-1">
                            Max: {{ item.product.stock }}
                          </div>
                        </td>
                        
                        <!-- Price -->
                        <td class="text-end">
                          <div class="text-nowrap">
                            {{ formatPrice(item.price) }} Birr
                          </div>
                        </td>
                        
                        <!-- Total -->
                        <td class="text-end">
                          <div class="text-nowrap fw-bold text-primary">
                            {{ formatPrice(item.price * item.quantity) }} Birr
                          </div>
                        </td>
                        
                        <!-- Expiration -->
                        <td class="text-center">
                          <div v-if="item.expired_date">
                            <span v-if="item.is_expired" class="badge bg-danger">
                              Expired
                            </span>
                            <span v-else class="badge bg-success">
                              {{ item.expires_in }}
                            </span>
                          </div>
                          <div v-else class="text-muted small">
                            No expiry
                          </div>
                        </td>
                        
                        <!-- Actions -->
                        <td class="text-center">
                          <div class="d-flex justify-content-center gap-2">
                            <button
                              @click="removeFromCart(item.id)"
                              class="btn btn-outline-danger btn-sm border"
                              type="button"
                              title="Remove"
                              :disabled="loading"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                            <button
                              @click="moveToWishlist(item)"
                              class="btn btn-outline-warning btn-sm border"
                              type="button"
                              title="Move to Wishlist"
                              :disabled="loading || item.is_expired"
                            >
                              <i class="fas fa-heart"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                    <tfoot class="table-light">
                      <tr>
                        <td colspan="4" class="text-end fw-bold">Subtotal (Active Items):</td>
                        <td class="text-end fw-bold text-primary">{{ formatPrice(subtotal) }} Birr</td>
                        <td colspan="2"></td>
                      </tr>
                      <tr v-if="expiredItems.length > 0">
                        <td colspan="4" class="text-end fw-bold text-danger">Expired Items:</td>
                        <td class="text-end fw-bold text-danger">
                          {{ formatPrice(expiredTotal) }} Birr
                        </td>
                        <td colspan="2" class="text-center">
                          <button @click="cleanExpiredItems" class="btn btn-sm btn-outline-danger">
                            Remove All
                          </button>
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
            
            <!-- Continue Shopping -->
            <div class="text-center mt-4">
              <button
                @click="continueShopping"
                class="btn btn-outline-secondary me-2"
              >
                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
              </button>
              
              <!-- Checkout Button -->
              <button
                v-if="activeItems.length > 0"
                @click="proceedToCheckout"
                class="btn btn-primary"
                :disabled="loading"
              >
                <i class="fas fa-lock me-2"></i>Proceed to Checkout
              </button>
            </div>
          </div>
        </div>
        
        <!-- Empty Cart State -->
        <div v-else class="text-center py-5">
          <div class="empty-state">
            <div class="empty-icon mb-4">
              <i class="fas fa-shopping-cart fa-4x text-muted"></i>
            </div>
            <h3 class="h4 fw-bold mb-3">Your Cart is Empty</h3>
            <p class="text-muted mb-4">
              Looks like you haven't added any products to your cart yet.
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

// Use props directly from Inertia
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

// Computed properties
const activeItems = computed(() => {
  return props.cartItems.filter(item => !item.is_expired)
})

const expiredItems = computed(() => {
  return props.cartItems.filter(item => item.is_expired)
})

const subtotal = computed(() => {
  return activeItems.value.reduce((sum, item) => {
    return sum + (item.price * item.quantity)
  }, 0)
})

const expiredTotal = computed(() => {
  return expiredItems.value.reduce((sum, item) => {
    return sum + (item.price * item.quantity)
  }, 0)
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

const goToProduct = (product) => {
  if (!product || !product.product_id) {
    showToast('Product not found', 'error')
    return
  }
  
  try {
    router.visit(route('product.details', product.product_id))
  } catch (error) {
    router.visit(`/product/${product.product_id}`)
  }
}

const updateQuantity = async (item, newQuantity) => {
  newQuantity = parseInt(newQuantity)
  if (isNaN(newQuantity) || newQuantity < 1 || newQuantity > item.product.stock || loading.value || item.is_expired) return
  
  loading.value = true
  
  try {
    await router.put(route('cart.update', { id: item.id }), {
      quantity: newQuantity
    }, {
      preserveState: false,
      preserveScroll: true,
      onSuccess: () => {
        showToast('Quantity updated successfully', 'success')
        window.dispatchEvent(new CustomEvent('cart-updated'))
      },
      onError: (errors) => {
        showToast(errors.message || 'Error updating quantity', 'error')
      },
      onFinish: () => {
        loading.value = false
      }
    })
    
  } catch (error) {
    console.error('Error updating quantity:', error)
    showToast('Error updating quantity', 'error')
    loading.value = false
  }
}

const removeFromCart = (cartId) => {
  modalMessage.value = 'Are you sure you want to remove this item from your cart?'
  pendingAction.value = 'remove'
  pendingItemId.value = cartId
  confirmModal.show()
}

const cleanExpiredItems = () => {
  modalMessage.value = `Are you sure you want to remove ${expiredItems.value.length} expired item(s) from your cart?`
  pendingAction.value = 'cleanExpired'
  confirmModal.show()
}

const clearCart = () => {
  modalMessage.value = 'Are you sure you want to clear your entire cart?'
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
  
  pendingAction.value = null
  pendingItemId.value = null
}

const performRemove = async (cartId) => {
  loading.value = true
  
  try {
    await router.delete(route('cart.destroy', { id: cartId }), {
      preserveState: false,
      preserveScroll: true,
      onSuccess: () => {
        showToast('Item removed from cart', 'success')
        window.dispatchEvent(new CustomEvent('cart-updated'))
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
      await router.delete(route('cart.destroy', { id: item.id }), {
        preserveState: false,
        preserveScroll: true,
        onError: (errors) => {
          console.error('Error removing expired item:', errors)
        }
      })
    }
    
    showToast(`${expiredItems.value.length} expired item(s) removed`, 'success')
    window.dispatchEvent(new CustomEvent('cart-updated'))
    
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
    await router.delete(route('cart.clear'), {
      preserveState: false,
      preserveScroll: true,
      onSuccess: () => {
        showToast('Cart cleared successfully', 'success')
        window.dispatchEvent(new CustomEvent('cart-updated'))
      },
      onError: (errors) => {
        showToast(errors.message || 'Error clearing cart', 'error')
      },
      onFinish: () => {
        loading.value = false
      }
    })
    
  } catch (error) {
    console.error('Error clearing cart:', error)
    showToast('Error clearing cart', 'error')
    loading.value = false
  }
}

const moveToWishlist = async (item) => {
  if (item.is_expired) {
    showToast('Cannot move expired items to wishlist', 'error')
    return
  }
  
  loading.value = true
  
  try {
    await router.post(route('wishlist.store'), {
      product_id: item.product.product_id
    }, {
      preserveState: false,
      preserveScroll: true,
      onSuccess: () => {
        performRemove(item.id)
        showToast('Item moved to wishlist', 'success')
        window.dispatchEvent(new CustomEvent('wishlist-updated'))
      },
      onError: (errors) => {
        showToast(errors.message || 'Error moving to wishlist', 'error')
        loading.value = false
      }
    })
    
  } catch (error) {
    console.error('Error moving to wishlist:', error)
    showToast('Error moving to wishlist', 'error')
    loading.value = false
  }
}

const proceedToCheckout = () => {
  if (activeItems.value.length === 0) {
    showToast('No active items to checkout', 'warning')
    return
  }
  
  router.visit('/checkout')
}

const continueShopping = () => {
  router.visit('/')
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
.cart-page.light-theme {
  background-color: #ffffff;
  color: #1e293b;
}

.cart-page.light-theme .card {
  background-color: #ffffff;
  border-color: #e2e8f0;
}

.cart-page.light-theme .table {
  color: #1e293b;
}

.cart-page.light-theme .table-light {
  background-color: #f8f9fa;
}

.cart-page.light-theme .table-hover tbody tr:hover {
  background-color: #f8fafc;
}

/* Dark theme styles */
.cart-page.dark-theme {
  background-color: #0f172a;
  color: #f1f5f9;
}

.cart-page.dark-theme .card {
  background-color: #1e293b;
  border-color: #334155;
  color: #f1f5f9;
}

.cart-page.dark-theme .table {
  color: #f1f5f9;
}

.cart-page.dark-theme .table-light {
  background-color: #334155;
  color: #f1f5f9;
}

.cart-page.dark-theme .table-hover tbody tr:hover {
  background-color: #2d3748;
}

.cart-page.dark-theme .table td,
.cart-page.dark-theme .table th {
  border-color: #475569;
}

.cart-page.dark-theme .text-muted {
  color: #94a3b8 !important;
}

.cart-page.dark-theme .form-control {
  background-color: #1e293b;
  border-color: #475569;
  color: #f1f5f9;
}

.cart-page.dark-theme .form-control:disabled {
  background-color: #334155;
}

.cart-page.dark-theme .btn-outline-secondary {
  color: #94a3b8;
  border-color: #475569;
}

.cart-page.dark-theme .btn-outline-secondary:hover {
  background-color: #334155;
  color: #f1f5f9;
}

.cart-page.dark-theme .btn-outline-secondary:disabled {
  color: #475569;
  border-color: #334155;
}

.cart-page.dark-theme .modal-content {
  background-color: #1e293b;
  color: #f1f5f9;
}

.cart-page.dark-theme .modal-header {
  border-bottom-color: #334155;
}

.cart-page.dark-theme .modal-footer {
  border-top-color: #334155;
}

.cart-page.dark-theme .btn-close {
  filter: invert(1) grayscale(100%) brightness(200%);
}

.cart-page.dark-theme .empty-state .text-muted {
  color: #94a3b8 !important;
}

/* Common styles */
.cart-page {
  min-height: 70vh;
}

.cart-product-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease;
  border: 1px solid var(--page-border-color, #dee2e6);
  background-color: var(--page-card-bg, #f8f9fa);
}

.cart-product-image:hover {
  transform: scale(1.05);
}

.cart-product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.quantity-control .form-control::-webkit-outer-spin-button,
.quantity-control .form-control::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.cursor-pointer {
  cursor: pointer;
}

.hover-text-primary:hover {
  color: #667eea !important;
  text-decoration: underline;
}

.empty-state {
  padding: 3rem 0;
}

.empty-icon {
  opacity: 0.5;
}

.table td {
  vertical-align: middle;
  padding: 1rem 0.75rem;
  border-bottom: 1px solid var(--page-border-color, #dee2e6);
}

.table th {
  padding: 1rem 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.875rem;
  letter-spacing: 0.5px;
  background-color: var(--page-card-bg, #f8f9fa);
  border-bottom: 2px solid var(--page-border-color, #dee2e6);
}

.table tr:last-child td {
  border-bottom: 0;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Expired row styling */
.expired-row {
  opacity: 0.7;
  background-color: rgba(220, 53, 69, 0.05) !important;
}

.cart-page.dark-theme .expired-row {
  background-color: rgba(220, 53, 69, 0.1) !important;
}

/* Toast styles */
.toast {
  border-radius: 10px;
  border: none;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .table-responsive {
    border: 1px solid var(--page-border-color, #dee2e6);
    border-radius: 8px;
    overflow: hidden;
  }
  
  .table thead {
    display: none;
  }
  
  .table tr {
    display: block;
    border-bottom: 1px solid var(--page-border-color, #dee2e6);
    padding: 1rem;
  }
  
  .table tr:last-child {
    border-bottom: 0;
  }
  
  .table td {
    display: block;
    text-align: left;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }
  
  .cart-page.dark-theme .table td {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  }
  
  .table td::before {
    content: attr(data-label);
    float: left;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.875rem;
    color: #6c757d;
    margin-right: 10px;
  }
  
  .cart-page.dark-theme .table td::before {
    color: #94a3b8;
  }
  
  .table td:last-child {
    border-bottom: 0;
    padding-top: 1rem;
    padding-bottom: 0;
  }
  
  .quantity-control {
    justify-content: flex-start;
  }
  
  .cart-product-image {
    width: 60px;
    height: 60px;
    margin: 0 auto;
  }
}

/* Custom scrollbar for table */
.table-responsive::-webkit-scrollbar {
  height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.cart-page.dark-theme .table-responsive::-webkit-scrollbar-track {
  background: #334155;
}

.table-responsive::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.cart-page.dark-theme .table-responsive::-webkit-scrollbar-thumb {
  background: #475569;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

.cart-page.dark-theme .table-responsive::-webkit-scrollbar-thumb:hover {
  background: #64748b;
}
</style>