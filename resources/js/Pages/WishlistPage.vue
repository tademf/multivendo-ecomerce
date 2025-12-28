<template>
  <AppLayout>
    <div class="wishlist-page py-5">
      <div class="container">
        <!-- Page Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h1 class="h2 fw-bold mb-2">My Wishlist</h1>
                <p class="text-muted mb-0">
                  {{ wishlistItems.length }} {{ wishlistItems.length === 1 ? 'item' : 'items' }} in your wishlist
                </p>
              </div>
              <div class="d-flex gap-2">
                <button
                  v-if="wishlistItems.length > 0"
                  @click="clearWishlist"
                  class="btn btn-outline-danger"
                  type="button"
                >
                  <i class="fas fa-trash me-2"></i>Clear All
                </button>
                <button
                  @click="continueShopping"
                  class="btn btn-outline-primary"
                  type="button"
                >
                  <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Wishlist Items Grid -->
        <div v-if="wishlistItems.length > 0" class="row g-4">
          <div
            v-for="item in wishlistItems"
            :key="item.id"
            class="col-6 col-md-4 col-lg-3"
          >
            <div class="card product-card h-100 border-0 shadow-sm">
              <!-- Product Image -->
              <div class="product-image-container position-relative overflow-hidden bg-light rounded-top">
                <img
                  :src="getProductImage(item.product.image)"
                  :alt="item.product.name"
                  class="product-img"
                  @error="handleImageError"
                  @click="goToProduct(item.product)"
                />

                <!-- Remove from Wishlist Button -->
                <button
                  @click="removeFromWishlist(item.id)"
                  class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                  type="button"
                  title="Remove from wishlist"
                >
                  <i class="fas fa-times"></i>
                </button>

                <!-- Stock Badge -->
                <div v-if="item.product.stock <= 0" class="badge bg-danger position-absolute top-0 start-0 m-2">
                  <i class="fas fa-times-circle me-1"></i> Out of Stock
                </div>
                <div v-else-if="item.product.stock < 10" class="badge bg-warning position-absolute top-0 start-0 m-2">
                  <i class="fas fa-exclamation-triangle me-1"></i> {{ item.product.stock }} left
                </div>

                <!-- Discount Badge -->
                <div v-if="item.product.discount && item.product.discount.status === 'active'" 
                     class="badge bg-danger position-absolute bottom-0 start-0 m-2">
                  <i class="fas fa-fire me-1"></i>{{ item.product.discount.discount_amount }}% OFF
                </div>
              </div>

              <!-- Product Info -->
              <div class="card-body d-flex flex-column p-3">
                <!-- Product Name -->
                <h6 class="card-title fw-bold mb-2 text-truncate cursor-pointer" @click="goToProduct(item.product)">
                  {{ item.product.name }}
                </h6>

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
                    v-if="item.product.stock > 0"
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
                    v-else
                    class="btn btn-outline-danger"
                    type="button"
                    disabled
                  >
                    <i class="fas fa-times me-2"></i>Out of Stock
                  </button>

                  <!-- View Details Button -->
                  <button
                    @click="goToProduct(item.product)"
                    class="btn btn-outline-secondary"
                    type="button"
                  >
                    <i class="fas fa-eye me-2"></i>View Details
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
              class="btn btn-primary"
              type="button"
            >
              <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </button>
          </div>
        </div>

        <!-- Wishlist Tips -->
        <div v-if="wishlistItems.length > 0" class="row mt-5">
          <div class="col-12">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <h5 class="fw-bold mb-3">Wishlist Tips</h5>
                <div class="row">
                  <div class="col-md-4">
                    <div class="d-flex align-items-start mb-3">
                      <div class="me-3">
                        <i class="fas fa-bell text-primary fa-lg"></i>
                      </div>
                      <div>
                        <h6 class="fw-bold mb-1">Price Drop Alerts</h6>
                        <p class="text-muted small mb-0">
                          We'll notify you if prices drop on items in your wishlist.
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="d-flex align-items-start mb-3">
                      <div class="me-3">
                        <i class="fas fa-sync text-primary fa-lg"></i>
                      </div>
                      <div>
                        <h6 class="fw-bold mb-1">Restock Notifications</h6>
                        <p class="text-muted small mb-0">
                          Get notified when out-of-stock items are back in stock.
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="d-flex align-items-start mb-3">
                      <div class="me-3">
                        <i class="fas fa-share-alt text-primary fa-lg"></i>
                      </div>
                      <div>
                        <h6 class="fw-bold mb-1">Share with Friends</h6>
                        <p class="text-muted small mb-0">
                          Share your wishlist with friends and family.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
  wishlistItems: {
    type: Array,
    default: () => []
  },
  itemCount: {
    type: Number,
    default: 0
  }
})

// Refs
const loading = ref(false)

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

const removeFromWishlist = async (wishlistId) => {
  if (!confirm('Are you sure you want to remove this item from wishlist?')) return
  
  loading.value = true
  try {
    const response = await axios.delete(`/wishlist/${wishlistId}`)
    
    // Remove item from local array
    const index = props.wishlistItems.findIndex(item => item.id === wishlistId)
    if (index > -1) {
      props.wishlistItems.splice(index, 1)
    }
    
    // Emit event to update navbar
    window.dispatchEvent(new CustomEvent('wishlist-updated', {
      detail: {
        wishlistCount: response.data.wishlistCount
      }
    }))
    
    showNotification('Item removed from wishlist', 'success')
  } catch (error) {
    console.error('Error removing from wishlist:', error)
    showNotification('Error removing item from wishlist', 'error')
  } finally {
    loading.value = false
  }
}

const clearWishlist = async () => {
  if (!confirm('Are you sure you want to clear your entire wishlist?')) return
  
  loading.value = true
  try {
    // Remove each item one by one (or create a bulk delete endpoint)
    for (const item of [...props.wishlistItems]) {
      await axios.delete(`/wishlist/${item.id}`)
    }
    
    // Clear local array
    props.wishlistItems.splice(0, props.wishlistItems.length)
    
    // Emit event to update navbar
    window.dispatchEvent(new CustomEvent('wishlist-updated', {
      detail: {
        wishlistCount: 0
      }
    }))
    
    showNotification('Wishlist cleared successfully', 'success')
  } catch (error) {
    console.error('Error clearing wishlist:', error)
    showNotification('Error clearing wishlist', 'error')
  } finally {
    loading.value = false
  }
}

const addToCart = async (wishlistItem) => {
  loading.value = true
  try {
    // First, move to cart using the moveToCart endpoint
    const response = await axios.post(`/wishlist/${wishlistItem.id}/move-to-cart`)
    
    // Remove from wishlist locally
    const index = props.wishlistItems.findIndex(item => item.id === wishlistItem.id)
    if (index > -1) {
      props.wishlistItems.splice(index, 1)
    }
    
    // Emit events to update navbar counts
    window.dispatchEvent(new CustomEvent('wishlist-updated', {
      detail: {
        wishlistCount: response.data.wishlistCount
      }
    }))
    
    window.dispatchEvent(new CustomEvent('cart-updated', {
      detail: {
        cartCount: response.data.cartCount
      }
    }))
    
    showNotification('Product added to cart', 'success')
  } catch (error) {
    console.error('Error adding to cart:', error)
    showNotification(error.response?.data?.message || 'Error adding to cart', 'error')
  } finally {
    loading.value = false
  }
}

const continueShopping = () => {
  router.visit('/')
}

const goToProduct = (product) => {
  if (!product || !product.product_id) return
  router.visit(`/product/${product.product_id}`)
}

const showNotification = (message, type = 'success') => {
  window.dispatchEvent(new CustomEvent('show-notification', {
    detail: {
      message,
      type
    }
  }))
}
</script>

<style scoped>
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
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
}

.product-image-container {
  height: 180px;
  position: relative;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 15px;
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

@media (max-width: 768px) {
  .product-image-container {
    height: 150px;
  }
}

@media (max-width: 576px) {
  .product-image-container {
    height: 120px;
  }
}
</style>