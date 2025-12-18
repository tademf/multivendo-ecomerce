<template>
  <AppLayout>
    <div class="container py-5">
      <!-- Page Header -->
      <div class="row mb-5">
        <div class="col-12">
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
              <!-- <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><Link href="/" class="text-decoration-none">Home</Link></li>
                  <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
                </ol>
              </nav> -->
              <h1 class="display-5 fw-bold text-dark mb-2">
                <i class="fas fa-heart text-danger me-3"></i>
                My Wishlist
              </h1>
              <p class="text-muted mb-0">
                Save your favorite items and buy them later
                <span v-if="wishlistItems.length > 0" class="ms-3">
                  <span class="badge bg-primary rounded-pill">{{ wishlistItems.length }} items</span>
                  <span v-if="totalValue > 0" class="ms-2">
                    <i class="fas fa-dollar-sign me-1"></i>
                    Total Value: <span class="fw-bold">${{ totalValue.toFixed(2) }}</span>
                  </span>
                </span>
              </p>
            </div>
            
            <!-- Quick Actions -->
            <div class="d-none d-md-flex gap-2">
              <button @click="clearWishlist" class="btn btn-outline-danger" :disabled="wishlistItems.length === 0">
                <i class="fas fa-trash-alt me-2"></i>
                Clear All
              </button>
              <button @click="addAllToCart" class="btn btn-primary" :disabled="wishlistItems.length === 0">
                <i class="fas fa-cart-plus me-2"></i>
                Add All to Cart
              </button>
            </div>
          </div>
          
          <!-- Flash Messages -->
          <div v-if="$page.props.flash.success" class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
              <i class="fas fa-check-circle me-2"></i>
              <span>{{ $page.props.flash.success }}</span>
              <button type="button" class="btn-close ms-auto" @click="$page.props.flash.success = null"></button>
            </div>
          </div>
          
          <div v-if="$page.props.flash.error" class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
              <i class="fas fa-exclamation-circle me-2"></i>
              <span>{{ $page.props.flash.error }}</span>
              <button type="button" class="btn-close ms-auto" @click="$page.props.flash.error = null"></button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="wishlistItems.length === 0" class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 text-center py-5">
          <div class="empty-state p-5 bg-light rounded-4 shadow-sm">
            <div class="mb-4">
              <i class="fas fa-heart fa-5x text-muted opacity-25"></i>
            </div>
            <h3 class="text-dark mb-3">Your wishlist is empty</h3>
            <p class="text-muted mb-4">Start saving your favorite products to view them later. It's a great way to keep track of items you love!</p>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
              <Link href="/" class="btn btn-primary btn-lg px-4">
                <i class="fas fa-shopping-bag me-2"></i>
                Continue Shopping
              </Link>
              <Link href="/products" class="btn btn-outline-primary btn-lg px-4">
                <i class="fas fa-search me-2"></i>
                Browse Products
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Wishlist Items -->
      <div v-else>
        <!-- Mobile Actions -->
        <div class="d-block d-md-none mb-4">
          <div class="d-grid gap-2">
            <button @click="addAllToCart" class="btn btn-primary" :disabled="wishlistItems.length === 0">
              <i class="fas fa-cart-plus me-2"></i>
              Add All to Cart
            </button>
            <button @click="clearWishlist" class="btn btn-outline-danger">
              <i class="fas fa-trash-alt me-2"></i>
              Clear Wishlist
            </button>
          </div>
        </div>

        <!-- Wishlist Items Table -->
        <div class="table-responsive mb-5">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 60px;"></th>
                <th scope="col">Product</th>
                <th scope="col" class="text-center">Price</th>
                <th scope="col" class="text-center">Stock Status</th>
                <th scope="col" class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in wishlistItems" :key="item.product_id" class="product-row">
                <td>
                  <div class="position-relative">
                    <button 
                      @click="removeFromWishlist(item.product_id)"
                      class="btn btn-sm btn-outline-danger rounded-circle"
                      style="width: 36px; height: 36px;"
                      title="Remove from wishlist"
                    >
                      <i class="fas fa-times"></i>
                    </button>
                    <span class="position-absolute top-0 start-100 translate-middle badge bg-dark rounded-pill">
                      {{ index + 1 }}
                    </span>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="product-image-container me-3">
                      <img 
                        :src="getProductImage(item)" 
                        :alt="item.name"
                        class="rounded product-thumbnail"
                        @error="handleImageError"
                      />
                      <div v-if="item.stock <= 0" class="stock-badge stock-out">
                        <i class="fas fa-times"></i>
                      </div>
                      <div v-else-if="item.stock < 10" class="stock-badge stock-low">
                        <i class="fas fa-exclamation"></i>
                      </div>
                    </div>
                    <div>
                      <h6 class="fw-bold mb-1">{{ item.name }}</h6>
                      <p class="text-muted small mb-1">
                        <i class="fas fa-tag me-1"></i>
                        {{ item.category || 'Uncategorized' }}
                      </p>
                      <p v-if="item.description" class="text-muted small mb-0 text-truncate" style="max-width: 300px;">
                        {{ truncateDescription(item.description) }}
                      </p>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  <div class="d-flex flex-column">
                    <span class="h5 fw-bold text-success mb-0">${{ formatPrice(item.price) }}</span>
                    <small v-if="item.original_price" class="text-muted text-decoration-line-through">
                      ${{ formatPrice(item.original_price) }}
                    </small>
                  </div>
                </td>
                <td class="text-center">
                  <div v-if="item.stock <= 0">
                    <span class="badge bg-danger rounded-pill">
                      <i class="fas fa-times me-1"></i> Out of Stock
                    </span>
                  </div>
                  <div v-else-if="item.stock < 10">
                    <span class="badge bg-warning rounded-pill">
                      <i class="fas fa-exclamation me-1"></i> Low Stock
                    </span>
                    <small class="d-block text-muted mt-1">{{ item.stock }} left</small>
                  </div>
                  <div v-else>
                    <span class="badge bg-success rounded-pill">
                      <i class="fas fa-check me-1"></i> In Stock
                    </span>
                    <small class="d-block text-muted mt-1">{{ item.stock }} available</small>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <button 
                      @click="addToCart(item)"
                      class="btn btn-sm btn-primary"
                      :disabled="item.stock <= 0"
                      :class="{ 'btn-success': isInCart(item.product_id) }"
                      :title="isInCart(item.product_id) ? 'Already in cart' : 'Add to cart'"
                    >
                      <i :class="isInCart(item.product_id) ? 'fas fa-check' : 'fas fa-cart-plus'" class="me-1"></i>
                      {{ isInCart(item.product_id) ? 'In Cart' : 'Add to Cart' }}
                    </button>
                    <Link 
                      v-if="item.stock > 0"
                      :href="generateBuyNowLink(item)"
                      class="btn btn-sm btn-success"
                      @click="prepareBuyNow(item)"
                    >
                      <i class="fas fa-bolt me-1"></i>
                      Buy Now
                    </Link>
                    <Link 
                      :href="route('products.show', item.product_id)" 
                      class="btn btn-sm btn-outline-secondary"
                    >
                      <i class="fas fa-eye me-1"></i>
                      View
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Summary Card -->
        <div class="row">
          <div class="col-lg-8">
            <!-- Product Recommendations -->
            <!-- <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-transparent border-0 pb-0">
                <h4 class="card-title fw-bold mb-3">
                  <i class="fas fa-star text-warning me-2"></i>
                  Recommended for You
                </h4>
              </div> -->
              <div class="card-body">
                <div class="row g-3">
                  <div v-for="suggestion in suggestedProducts" :key="suggestion.id" class="col-6 col-md-3">
                    <div class="card border h-100 hover-lift">
                      <div class="position-relative">
                        <img 
                          :src="getProductImage(suggestion)" 
                          class="card-img-top product-suggestion-img"
                          alt="Product"
                          @error="handleImageError"
                        >
                        <button 
                          @click="addSuggestionToWishlist(suggestion)"
                          class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 rounded-circle"
                          style="width: 32px; height: 32px;"
                          title="Add to wishlist"
                        >
                          <i class="fas fa-heart"></i>
                        </button>
                      </div>
                      <div class="card-body">
                        <h6 class="card-title fw-bold text-truncate" :title="suggestion.name">{{ suggestion.name }}</h6>
                        <p class="card-text text-success fw-bold mb-2">${{ formatPrice(suggestion.price) }}</p>
                        <Link 
                          :href="route('products.show', suggestion.id)" 
                          class="btn btn-sm btn-outline-primary w-100"
                        >
                          View Details
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
              <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                  <i class="fas fa-list-alt me-2"></i>
                  Wishlist Summary
                </h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Items:</span>
                    <span class="fw-bold">{{ wishlistItems.length }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">In Stock Items:</span>
                    <span class="fw-bold">{{ inStockItems }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Total Value:</span>
                    <span class="fw-bold text-success">${{ totalValue.toFixed(2) }}</span>
                  </div>
                  <hr>
                </div>
                
                <div class="d-grid gap-2">
                  <button 
                    @click="addAllToCart" 
                    class="btn btn-primary"
                    :disabled="inStockItems === 0"
                    :class="{ 'btn-success': allItemsInCart }"
                  >
                    <i :class="allItemsInCart ? 'fas fa-check-circle' : 'fas fa-cart-plus'" class="me-2"></i>
                    {{ allItemsInCart ? 'All Items in Cart' : `Add ${inStockItems} to Cart` }}
                  </button>
                  <button 
                    @click="clearWishlist" 
                    class="btn btn-outline-danger"
                    :disabled="wishlistItems.length === 0"
                  >
                    <i class="fas fa-trash-alt me-2"></i>
                    Clear Wishlist
                  </button>
                  <Link href="/" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Continue Shopping
                  </Link>
                </div>
                
                <div class="mt-4 text-center">
                  <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Items will be automatically removed when out of stock
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading Modal -->
      <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow">
            <div class="modal-body text-center py-5">
              <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
              </div>
              <h5 class="mb-3">Processing...</h5>
              <p class="text-muted mb-0">Please wait while we update your wishlist</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Confirmation Modal -->
      <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmationModalLabel">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                Confirmation Required
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p id="confirmationMessage"></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" id="confirmAction">Confirm</button>
            </div>
          </div>
        </div>
      </div>
    <!-- </div> -->
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  wishlistItems: {
    type: Array,
    default: () => []
  },
  suggestions: {
    type: Array,
    default: () => []
  },
  cartItems: {
    type: Array,
    default: () => []
  }
})

// Refs
const isLoading = ref(false)
const confirmationModal = ref(null)
const loadingModal = ref(null)

// Computed
const totalValue = computed(() => {
  return props.wishlistItems.reduce((total, item) => {
    return total + (parseFloat(item.price) || 0)
  }, 0)
})

const inStockItems = computed(() => {
  return props.wishlistItems.filter(item => item.stock > 0).length
})

const suggestedProducts = computed(() => {
  return props.suggestions.slice(0, 4) // Show only 4 suggestions
})

const allItemsInCart = computed(() => {
  return props.wishlistItems.every(item => isInCart(item.product_id))
})

// Helper functions
const getProductImage = (item) => {
  if (!item) return 'https://placehold.co/300x300/e0f2f1/065f46?text=Product+Image'
  if (item.image && item.image.startsWith('http')) return item.image
  if (item.image) return `/storage/${item.image}`
  return 'https://placehold.co/300x300/e0f2f1/065f46?text=Product+Image'
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/300x300/e0f2f1/065f46?text=Product+Image'
}

const formatPrice = (price) => {
  const num = parseFloat(price || 0)
  return isNaN(num) ? '0.00' : num.toFixed(2)
}

const truncateDescription = (description) => {
  if (!description) return ''
  if (description.length > 80) {
    return description.substring(0, 80) + '...'
  }
  return description
}

const generateBuyNowLink = (item) => {
  return `/submit-payment?product_id=${item.product_id}&quantity=1&price=${item.price}&name=${encodeURIComponent(item.name)}`
}

const isInCart = (productId) => {
  return props.cartItems?.some(item => item.product_id === productId) || false
}

const prepareBuyNow = (item) => {
  const productInfo = {
    product_id: item.product_id,
    quantity: 1,
    name: item.name,
    price: item.price,
    image: item.image,
    stock: item.stock
  }
  localStorage.setItem('buy_now_product', JSON.stringify(productInfo))
}

// Modal functions
const showLoading = () => {
  if (window.bootstrap) {
    const modalElement = document.getElementById('loadingModal')
    if (modalElement) {
      const modal = new window.bootstrap.Modal(modalElement)
      modal.show()
    }
  }
}

const hideLoading = () => {
  if (window.bootstrap) {
    const modalElement = document.getElementById('loadingModal')
    if (modalElement) {
      const modal = window.bootstrap.Modal.getInstance(modalElement)
      if (modal) modal.hide()
    }
  }
}

const showConfirmation = (message, callback) => {
  const modalElement = document.getElementById('confirmationModal')
  const messageElement = document.getElementById('confirmationMessage')
  const confirmButton = document.getElementById('confirmAction')
  
  if (modalElement && messageElement && confirmButton) {
    messageElement.textContent = message
    
    const modal = new window.bootstrap.Modal(modalElement)
    
    // Remove previous event listeners
    confirmButton.replaceWith(confirmButton.cloneNode(true))
    const newConfirmButton = document.getElementById('confirmAction')
    
    newConfirmButton.onclick = () => {
      modal.hide()
      callback()
    }
    
    modal.show()
  }
}

// Wishlist actions
const removeFromWishlist = (productId) => {
  showConfirmation('Are you sure you want to remove this item from your wishlist?', () => {
    showLoading()
    router.delete(route('wishlist.remove', productId), {
      preserveScroll: true,
      onFinish: () => {
        hideLoading()
      }
    })
  })
}

const clearWishlist = () => {
  if (props.wishlistItems.length === 0) return
  
  showConfirmation('Are you sure you want to clear your entire wishlist? This action cannot be undone.', () => {
    showLoading()
    router.delete(route('wishlist.clear'), {
      preserveScroll: true,
      onFinish: () => {
        hideLoading()
      }
    })
  })
}

const addToCart = (item) => {
  if (item.stock <= 0) {
    showConfirmation('This item is out of stock. Remove it from wishlist?', () => {
      removeFromWishlist(item.product_id)
    })
    return
  }
  
  showLoading()
  router.post(route('cart.add'), {
    product_id: item.product_id,
    quantity: 1
  }, {
    preserveScroll: true,
    onFinish: () => {
      hideLoading()
    }
  })
}

const addAllToCart = () => {
  const inStockItems = props.wishlistItems.filter(item => item.stock > 0)
  const outOfStockItems = props.wishlistItems.filter(item => item.stock <= 0)
  
  if (inStockItems.length === 0) {
    if (outOfStockItems.length > 0) {
      showConfirmation('All items in your wishlist are out of stock. Clear wishlist?', () => {
        clearWishlist()
      })
    }
    return
  }
  
  showConfirmation(`Add ${inStockItems.length} items to cart?`, () => {
    showLoading()
    
    // Process items sequentially
    const processItems = async (items) => {
      for (const item of items) {
        await new Promise(resolve => {
          router.post(route('cart.add'), {
            product_id: item.product_id,
            quantity: 1
          }, {
            preserveScroll: true,
            onFinish: resolve
          })
        })
      }
      
      hideLoading()
    }
    
    processItems(inStockItems)
  })
}

const addSuggestionToWishlist = (suggestion) => {
  showLoading()
  router.post(route('wishlist.add'), {
    product_id: suggestion.id
  }, {
    preserveScroll: true,
    onFinish: () => {
      hideLoading()
    }
  })
}

// Initialize modals
onMounted(() => {
  // Check for out of stock items
  const outOfStockItems = props.wishlistItems.filter(item => item.stock <= 0)
  if (outOfStockItems.length > 0) {
    setTimeout(() => {
      showConfirmation(
        `${outOfStockItems.length} item(s) in your wishlist are out of stock. Remove them?`,
        () => {
          outOfStockItems.forEach(item => {
            removeFromWishlist(item.product_id)
          })
        }
      )
    }, 1000)
  }
})
</script>

<style scoped>
/* Custom Styles */
.product-thumbnail {
  width: 80px;
  height: 80px;
  object-fit: cover;
}

.product-image-container {
  position: relative;
  width: 80px;
  height: 80px;
}

.stock-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  color: white;
}

.stock-out {
  background-color: #dc3545;
}

.stock-low {
  background-color: #ffc107;
  color: #000 !important;
}

.product-suggestion-img {
  height: 150px;
  object-fit: cover;
}

.hover-lift {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.product-row {
  transition: background-color 0.2s ease;
}

.product-row:hover {
  background-color: rgba(13, 110, 253, 0.05);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .product-thumbnail {
    width: 60px;
    height: 60px;
  }
  
  .product-image-container {
    width: 60px;
    height: 60px;
  }
  
  .table-responsive {
    font-size: 0.9rem;
  }
  
  .display-5 {
    font-size: 2rem;
  }
}

/* Animation for new items */
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

.product-row {
  animation: fadeIn 0.3s ease forwards;
}
</style>