<template>
    <div class="wishlist-container">
      <!-- Header with Back Button -->
      <div class="wishlist-header">
        <div class="flex items-center justify-between mb-4">
          <Link 
            :href="route('home')" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all transform hover:-translate-y-0.5 hover:shadow-md"
          >
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Home
          </Link>
        </div>

        <h1 class="page-title">
          <i class="fas fa-heart text-red-500 mr-3"></i>
          My Wishlist
        </h1>
        <div class="wishlist-stats">
          <span class="stat-item">
            <i class="fas fa-box"></i>
            {{ wishlistItems.length }} items
          </span>
          <span v-if="totalValue > 0" class="stat-item">
            <i class="fas fa-dollar-sign"></i>
            Total: ${{ totalValue.toFixed(2) }}
          </span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="wishlistItems.length === 0" class="empty-wishlist">
        <div class="empty-icon">
          <i class="fas fa-heart text-gray-300"></i>
        </div>
        <h3 class="empty-title">Your wishlist is empty</h3>
        <p class="empty-description">
          Save your favorite items here to view them later
        </p>
        <Link :href="route('home')" class="btn-primary">
          <i class="fas fa-shopping-bag mr-2"></i>
          Start Shopping
        </Link>
      </div>

      <!-- Wishlist Items -->
      <div v-else class="wishlist-content">
        <!-- Actions Bar -->
        <div class="wishlist-actions">
          <div class="actions-left">
            <button @click="clearWishlist" class="btn-danger">
              <i class="fas fa-trash mr-2"></i>
              Clear All
            </button>
          </div>
          <div class="actions-right">
            <Link :href="route('cart')" class="btn-secondary">
              <i class="fas fa-shopping-cart mr-2"></i>
              View Cart
            </Link>
            <button @click="addAllToCart" class="btn-primary">
              <i class="fas fa-cart-plus mr-2"></i>
              Add All to Cart
            </button>
          </div>
        </div>

        <!-- Wishlist Grid -->
        <div class="wishlist-grid">
          <div v-for="item in wishlistItems" :key="item.product_id" class="wishlist-item">
            <!-- Product Image -->
            <div class="item-image-container">
              <img 
                :src="item.image || '/images/placeholder-product.jpg'" 
                :alt="item.name"
                class="item-image"
                @error="handleImageError"
              />
              <button 
                @click="removeFromWishlist(item.product_id)"
                class="remove-btn"
                title="Remove from wishlist"
              >
                <i class="fas fa-times"></i>
              </button>
              <div v-if="item.stock <= 0" class="stock-badge out-of-stock">
                Out of Stock
              </div>
              <div v-else-if="item.stock < 10" class="stock-badge low-stock">
                Low Stock
              </div>
            </div>

            <!-- Product Details -->
            <div class="item-details">
              <div class="item-header">
                <h3 class="item-name">{{ item.name }}</h3>
                <span class="item-reference">#{{ item.reference || item.product_id }}</span>
              </div>
              
              <p v-if="item.description" class="item-description">
                {{ truncateDescription(item.description) }}
              </p>
              
              <div class="item-meta">
                <span class="item-category">
                  <i class="fas fa-tag"></i>
                  {{ item.category || 'Uncategorized' }}
                </span>
                <span class="item-added">
                  <i class="fas fa-calendar-plus"></i>
                  Added {{ formatDate(item.created_at) }}
                </span>
              </div>

              <div class="item-footer">
                <div class="item-price">
                  <span class="price-amount">${{ formatPrice(item.price) }}</span>
                  <span v-if="item.stock > 0" class="stock-info">
                    <i class="fas fa-box"></i>
                    {{ item.stock }} available
                  </span>
                </div>

                <div class="item-actions">
                  <button 
                    @click="addToCart(item)"
                    class="btn-cart"
                    :disabled="item.stock <= 0"
                    :class="{ 'disabled': item.stock <= 0 }"
                  >
                    <i class="fas fa-shopping-cart"></i>
                    {{ item.stock > 0 ? 'Add to Cart' : 'Out of Stock' }}
                  </button>
                  
                  <Link 
                    :href="route('products.show', item.product_id)" 
                    class="btn-view"
                  >
                    <i class="fas fa-eye"></i>
                    View
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Suggestions -->
        <div v-if="wishlistItems.length > 0" class="suggestions-section">
          <h3 class="section-title">
            <i class="fas fa-star text-yellow-500 mr-2"></i>
            You might also like
          </h3>
          <div class="suggestions-grid">
            <div v-for="suggestion in suggestedProducts" :key="suggestion.id" 
                 class="suggestion-item">
              <img :src="suggestion.image" :alt="suggestion.name" class="suggestion-image">
              <div class="suggestion-details">
                <h4>{{ suggestion.name }}</h4>
                <p class="suggestion-price">${{ formatPrice(suggestion.price) }}</p>
                <button @click="addSuggestionToWishlist(suggestion)" class="btn-wishlist">
                  <i class="fas fa-heart"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Success Notification -->
      <div v-if="showNotification" class="notification" :class="notificationType">
        <i :class="notificationIcon"></i>
        <span>{{ notificationMessage }}</span>
        <button @click="showNotification = false" class="close-notification">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  wishlistItems: {
    type: Array,
    default: () => []
  },
  suggestions: {
    type: Array,
    default: () => []
  }
})

// Reactive state
const showNotification = ref(false)
const notificationMessage = ref('')
const notificationType = ref('success')
const notificationIcon = ref('fas fa-check-circle')
const suggestedProducts = ref(props.suggestions || [])

// Computed properties
const totalValue = computed(() => {
  return props.wishlistItems.reduce((total, item) => {
    return total + (parseFloat(item.price) || 0)
  }, 0)
})

// Helper functions
const formatPrice = (price) => {
  const num = parseFloat(price || 0)
  return isNaN(num) ? '0.00' : num.toFixed(2)
}

const formatDate = (dateString) => {
  if (!dateString) return 'recently'
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 1) return 'today'
  if (diffDays < 7) return `${diffDays} days ago`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
  return `${Math.floor(diffDays / 30)} months ago`
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image'
}

const truncateDescription = (description) => {
  if (!description) return ''
  if (description.length > 120) {
    return description.substring(0, 120) + '...'
  }
  return description
}

// Wishlist actions
const removeFromWishlist = (productId) => {
  if (confirm('Remove this item from wishlist?')) {
    router.delete(route('wishlist.remove', productId), {
      preserveScroll: true,
      onSuccess: (page) => {
        // Check if page has success message
        if (page.props.flash?.success) {
          showNotificationMessage(page.props.flash.success, 'success', 'fas fa-trash')
        } else {
          showNotificationMessage('Item removed from wishlist', 'success', 'fas fa-trash')
        }
      },
      onError: (errors) => {
        const errorMsg = errors.message || 'Failed to remove item'
        showNotificationMessage(errorMsg, 'error', 'fas fa-exclamation-circle')
      }
    })
  }
}

const clearWishlist = () => {
  if (confirm('Clear all items from wishlist?')) {
    router.delete(route('wishlist.clear'), {
      preserveScroll: true,
      onSuccess: (page) => {
        if (page.props.flash?.success) {
          showNotificationMessage(page.props.flash.success, 'success', 'fas fa-check-circle')
        } else {
          showNotificationMessage('Wishlist cleared', 'success', 'fas fa-check-circle')
        }
      },
      onError: (errors) => {
        const errorMsg = errors.message || 'Failed to clear wishlist'
        showNotificationMessage(errorMsg, 'error', 'fas fa-exclamation-circle')
      }
    })
  }
}

const addToCart = (item) => {
  if (item.stock <= 0) return
  
  router.post(route('cart.add'), {
    product_id: item.product_id,
    quantity: 1
  }, {
    preserveScroll: true,
    onSuccess: (page) => {
      // Don't show raw JSON - show success message from flash or generic message
      const successMsg = page.props.flash?.success || `${item.name} added to cart!`
      showNotificationMessage(successMsg, 'success', 'fas fa-cart-plus')
    },
    onError: (errors) => {
      const errorMsg = errors.message || 'Failed to add to cart'
      showNotificationMessage(errorMsg, 'error', 'fas fa-exclamation-circle')
    }
  })
}

const addAllToCart = () => {
  const inStockItems = props.wishlistItems.filter(item => item.stock > 0)
  
  if (inStockItems.length === 0) {
    showNotificationMessage('No items in stock to add to cart', 'warning', 'fas fa-exclamation-triangle')
    return
  }
  
  if (confirm(`Add ${inStockItems.length} items to cart?`)) {
    // Add items one by one (you might want to implement a batch endpoint)
    inStockItems.forEach((item, index) => {
      setTimeout(() => {
        router.post(route('cart.add'), {
          product_id: item.product_id,
          quantity: 1
        }, {
          preserveScroll: true,
          onFinish: () => {
            if (index === inStockItems.length - 1) {
              showNotificationMessage(`${inStockItems.length} items added to cart!`, 'success', 'fas fa-cart-plus')
            }
          }
        })
      }, index * 100) // Small delay between requests
    })
  }
}

const addSuggestionToWishlist = (suggestion) => {
  router.post(route('wishlist.add'), {
    product_id: suggestion.id
  }, {
    preserveScroll: true,
    onSuccess: (page) => {
      // Handle success response properly
      const successMsg = page.props.flash?.success || 'Added to wishlist'
      showNotificationMessage(successMsg, 'success', 'fas fa-heart')
      
      // Refresh page to show updated wishlist
      setTimeout(() => {
        router.reload({ only: ['wishlistItems'] })
      }, 500)
    },
    onError: (errors) => {
      const errorMsg = errors.message || 'Failed to add to wishlist'
      showNotificationMessage(errorMsg, 'error', 'fas fa-exclamation-circle')
    }
  })
}

const showNotificationMessage = (message, type = 'success', icon = 'fas fa-check-circle') => {
  notificationMessage.value = message
  notificationType.value = type
  notificationIcon.value = icon
  showNotification.value = true
  
  setTimeout(() => {
    showNotification.value = false
  }, 3000)
}

onMounted(() => {
  // Load suggested products if not passed as props
  if (suggestedProducts.value.length === 0 && props.wishlistItems.length > 0) {
    loadSuggestedProducts()
  }
})

const loadSuggestedProducts = () => {
  // Fetch suggested products from API
  fetch('/api/products/suggested')
    .then(response => response.json())
    .then(data => {
      suggestedProducts.value = data.slice(0, 4) // Get 4 suggestions
    })
    
}
</script>

<style scoped>
/* Keep all your existing CSS styles - they're good */
.wishlist-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

/* Header */
.wishlist-header {
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

.page-title {
  font-size: 2.5rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.wishlist-stats {
  display: flex;
  gap: 1.5rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.95rem;
}

.stat-item i {
  color: #ef4444;
}

/* Empty State */
.empty-wishlist {
  text-align: center;
  padding: 4rem 2rem;
  background: #f9fafb;
  border-radius: 12px;
  border: 2px dashed #d1d5db;
}

.empty-icon {
  font-size: 4rem;
  color: #d1d5db;
  margin-bottom: 1rem;
}

.empty-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #4b5563;
  margin-bottom: 0.5rem;
}

.empty-description {
  color: #6b7280;
  margin-bottom: 2rem;
}

/* Actions Bar */
.wishlist-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.actions-left,
.actions-right {
  display: flex;
  gap: 1rem;
}

/* Buttons */
.btn-primary {
  background: #3b82f6;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  text-decoration: none;
}

.btn-primary:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.btn-secondary {
  background: #6b7280;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  text-decoration: none;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-danger {
  background: #ef4444;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
}

.btn-danger:hover {
  background: #dc2626;
}

/* Wishlist Grid */
.wishlist-grid {
  display: grid;
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.wishlist-item {
  display: grid;
  grid-template-columns: 200px 1fr;
  gap: 1.5rem;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s;
}

.wishlist-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Image Container */
.item-image-container {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.item-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #ef4444;
  transition: all 0.3s;
}

.remove-btn:hover {
  background: #ef4444;
  color: white;
}

.stock-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  color: white;
}

.out-of-stock {
  background: #ef4444;
}

.low-stock {
  background: #f59e0b;
}

/* Item Details */
.item-details {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
}

.item-header {
  margin-bottom: 0.75rem;
}

.item-name {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
}

.item-reference {
  font-size: 0.85rem;
  color: #6b7280;
  background: #f3f4f6;
  padding: 2px 8px;
  border-radius: 4px;
}

.item-description {
  color: #4b5563;
  line-height: 1.5;
  margin-bottom: 1rem;
  flex: 1;
}

.item-meta {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1rem;
  font-size: 0.9rem;
  color: #6b7280;
}

.item-meta i {
  margin-right: 0.5rem;
  color: #9ca3af;
}

.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.item-price {
  display: flex;
  flex-direction: column;
}

.price-amount {
  font-size: 1.5rem;
  font-weight: bold;
  color: #10b981;
}

.stock-info {
  font-size: 0.85rem;
  color: #6b7280;
}

.item-actions {
  display: flex;
  gap: 1rem;
}

.btn-cart {
  background: #3b82f6;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.btn-cart:hover:not(:disabled) {
  background: #2563eb;
}

.btn-cart.disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

.btn-view {
  background: #6b7280;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 600;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.btn-view:hover {
  background: #4b5563;
}

/* Suggestions */
.suggestions-section {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 2px solid #e5e7eb;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1.5rem;
}

.suggestions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1.5rem;
}

.suggestion-item {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s;
}

.suggestion-item:hover {
  transform: translateY(-4px);
}

.suggestion-image {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.suggestion-details {
  padding: 1rem;
  position: relative;
}

.suggestion-details h4 {
  font-size: 0.95rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
}

.suggestion-price {
  color: #10b981;
  font-weight: 600;
  margin: 0;
}

.btn-wishlist {
  position: absolute;
  top: -15px;
  right: 10px;
  background: white;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #ef4444;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.3s;
}

.btn-wishlist:hover {
  background: #ef4444;
  color: white;
}

/* Notification */
.notification {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: white;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  gap: 1rem;
  z-index: 1000;
  animation: slideIn 0.3s ease;
}

.notification.success {
  border-left: 4px solid #10b981;
}

.notification.error {
  border-left: 4px solid #ef4444;
}

.notification.warning {
  border-left: 4px solid #f59e0b;
}

.notification i {
  font-size: 1.2rem;
}

.notification.success i {
  color: #10b981;
}

.notification.error i {
  color: #ef4444;
}

.notification.warning i {
  color: #f59e0b;
}

.close-notification {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  margin-left: 1rem;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .wishlist-item {
    grid-template-columns: 1fr;
  }
  
  .item-image-container {
    height: 200px;
  }
  
  .wishlist-actions {
    flex-direction: column;
    gap: 1rem;
  }
  
  .actions-left,
  .actions-right {
    width: 100%;
  }
  
  .actions-left button,
  .actions-right a,
  .actions-right button {
    width: 100%;
    justify-content: center;
  }
  
  .item-footer {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .item-actions {
    width: 100%;
  }
  
  .item-actions button,
  .item-actions a {
    flex: 1;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .page-title {
    font-size: 1.75rem;
  }
  
  .wishlist-stats {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .suggestions-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }
}
</style>