<template>
  <AppLayout>
    <div class="cart-page py-5">
      <div class="container">
        <!-- Page Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h1 class="h2 fw-bold mb-2">Shopping Cart</h1>
                <p class="text-muted mb-0">
                  {{ cartItems.length }} {{ cartItems.length === 1 ? 'item' : 'items' }} in your cart
                </p>
              </div>
              <div class="d-flex gap-2">
                <button
                  v-if="cartItems.length > 0"
                  @click="clearCart"
                  class="btn btn-outline-danger"
                  type="button"
                >
                  <i class="fas fa-trash me-2"></i>Clear Cart
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

        <!-- Cart Items -->
        <div v-if="cartItems.length > 0" class="row">
          <div class="col-lg-8">
            <!-- Cart Items List -->
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover mb-0">
                    <thead class="bg-light">
                      <tr>
                        <th class="border-0" style="width: 120px;">Product</th>
                        <th class="border-0">Details</th>
                        <th class="border-0 text-center">Quantity</th>
                        <th class="border-0 text-end">Price</th>
                        <th class="border-0 text-end">Total</th>
                        <th class="border-0 text-center">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in cartItems" :key="item.id" class="align-middle">
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
                            <h6 class="fw-bold mb-1 cursor-pointer" @click="goToProduct(item.product)">
                              {{ item.product.name }}
                            </h6>
                            <p class="text-muted small mb-2">
                              <span class="badge bg-light text-dark me-2">
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
                              :disabled="item.quantity <= 1"
                              class="btn btn-outline-secondary btn-sm"
                              type="button"
                            >
                              <i class="fas fa-minus"></i>
                            </button>
                            <input
                              type="number"
                              :value="item.quantity"
                              @change="updateQuantity(item, $event.target.value)"
                              min="1"
                              :max="item.product.stock"
                              class="form-control form-control-sm text-center mx-2"
                              style="width: 60px;"
                            />
                            <button
                              @click="updateQuantity(item, item.quantity + 1)"
                              :disabled="item.quantity >= item.product.stock"
                              class="btn btn-outline-secondary btn-sm"
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
                        
                        <!-- Actions -->
                        <td class="text-center">
                          <div class="d-flex justify-content-center gap-2">
                            <button
                              @click="removeFromCart(item.id)"
                              class="btn btn-outline-danger btn-sm"
                              type="button"
                              title="Remove"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                            <button
                              @click="moveToWishlist(item)"
                              class="btn btn-outline-warning btn-sm"
                              type="button"
                              title="Move to Wishlist"
                            >
                              <i class="fas fa-heart"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            
            <!-- Additional Info -->
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Shipping Information</h6>
                    <p class="text-muted small mb-0">
                      Free shipping on orders over 1000 Birr. 
                      Standard delivery: 3-5 business days.
                    </p>
                  </div>
                  <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Return Policy</h6>
                    <p class="text-muted small mb-0">
                      30-day return policy. 
                      Items must be in original condition.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Order Summary -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
              <div class="card-body">
                <h5 class="fw-bold mb-4">Order Summary</h5>
                
                <!-- Summary Items -->
                <div class="mb-3">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-bold">{{ formatPrice(cartTotal) }} Birr</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="fw-bold text-success">
                      {{ cartTotal >= 1000 ? 'FREE' : '100 Birr' }}
                    </span>
                  </div>
                  <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Tax (15%)</span>
                    <span class="fw-bold">{{ formatPrice(cartTotal * 0.15) }} Birr</span>
                  </div>
                  
                  <hr />
                  
                  <div class="d-flex justify-content-between mb-3">
                    <span class="h5 fw-bold">Total</span>
                    <span class="h5 fw-bold text-primary">
                      {{ formatPrice(calculateTotal()) }} Birr
                    </span>
                  </div>
                </div>
                
                <!-- Checkout Button -->
                <button
                  @click="proceedToCheckout"
                  class="btn btn-primary btn-lg w-100 mb-3"
                  type="button"
                  :disabled="cartItems.length === 0"
                >
                  <i class="fas fa-lock me-2"></i>Proceed to Checkout
                </button>
                
                <!-- Payment Methods -->
                <div class="mb-3">
                  <p class="small text-muted mb-2">We accept:</p>
                  <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark">
                      <i class="fab fa-cc-visa me-1"></i>Visa
                    </span>
                    <span class="badge bg-light text-dark">
                      <i class="fab fa-cc-mastercard me-1"></i>MasterCard
                    </span>
                    <span class="badge bg-light text-dark">
                      <i class="fas fa-mobile-alt me-1"></i>CBE Birr
                    </span>
                  </div>
                </div>
                
                <!-- Security Info -->
                <div class="text-center">
                  <p class="small text-muted mb-0">
                    <i class="fas fa-lock me-1"></i>
                    Secure checkout • SSL encrypted
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Discount Code -->
            <div class="card border-0 shadow-sm mt-4">
              <div class="card-body">
                <h6 class="fw-bold mb-3">Discount Code</h6>
                <div class="input-group">
                  <input
                    v-model="discountCode"
                    type="text"
                    class="form-control"
                    placeholder="Enter code"
                  />
                  <button
                    @click="applyDiscount"
                    class="btn btn-outline-primary"
                    type="button"
                  >
                    Apply
                  </button>
                </div>
                <div v-if="discountError" class="text-danger small mt-2">
                  {{ discountError }}
                </div>
              </div>
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
              class="btn btn-primary"
              type="button"
            >
              <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
  cartItems: {
    type: Array,
    default: () => []
  },
  cartTotal: {
    type: Number,
    default: 0
  },
  itemCount: {
    type: Number,
    default: 0
  }
})

// Refs
const discountCode = ref('')
const discountError = ref('')
const loading = ref(false)

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
  // You might need to pass categories as prop or fetch them
  return 'Category' // Replace with actual logic
}

const updateQuantity = async (item, newQuantity) => {
  if (newQuantity < 1 || newQuantity > item.product.stock) return
  
  loading.value = true
  try {
    const response = await axios.put(`/cart/${item.id}`, {
      quantity: parseInt(newQuantity)
    })
    
    // Update local data
    item.quantity = response.data.cartItem.quantity
    
    // Emit event to update parent
    window.dispatchEvent(new CustomEvent('cart-updated', {
      detail: {
        cartCount: response.data.cartCount,
        cartTotal: response.data.cartTotal
      }
    }))
    
    showNotification('Cart updated successfully', 'success')
  } catch (error) {
    console.error('Error updating cart:', error)
    showNotification(error.response?.data?.message || 'Error updating cart', 'error')
  } finally {
    loading.value = false
  }
}

const removeFromCart = async (cartId) => {
  if (!confirm('Are you sure you want to remove this item from cart?')) return
  
  loading.value = true
  try {
    const response = await axios.delete(`/cart/${cartId}`)
    
    // Remove item from local array
    const index = props.cartItems.findIndex(item => item.id === cartId)
    if (index > -1) {
      props.cartItems.splice(index, 1)
    }
    
    // Emit event to update parent
    window.dispatchEvent(new CustomEvent('cart-updated', {
      detail: {
        cartCount: response.data.cartCount,
        cartTotal: response.data.cartTotal
      }
    }))
    
    showNotification('Item removed from cart', 'success')
  } catch (error) {
    console.error('Error removing from cart:', error)
    showNotification('Error removing item from cart', 'error')
  } finally {
    loading.value = false
  }
}

const clearCart = async () => {
  if (!confirm('Are you sure you want to clear your entire cart?')) return
  
  loading.value = true
  try {
    const response = await axios.delete('/cart/clear')
    
    // Clear local array
    props.cartItems.splice(0, props.cartItems.length)
    
    // Emit event to update parent
    window.dispatchEvent(new CustomEvent('cart-updated', {
      detail: {
        cartCount: response.data.cartCount,
        cartTotal: response.data.cartTotal
      }
    }))
    
    showNotification('Cart cleared successfully', 'success')
  } catch (error) {
    console.error('Error clearing cart:', error)
    showNotification('Error clearing cart', 'error')
  } finally {
    loading.value = false
  }
}

const moveToWishlist = async (item) => {
  loading.value = true
  try {
    const response = await axios.post('/wishlist', {
      product_id: item.product.product_id
    })
    
    // Remove from cart
    await removeFromCart(item.id)
    
    // Emit event to update wishlist count
    window.dispatchEvent(new CustomEvent('wishlist-updated', {
      detail: {
        wishlistCount: response.data.wishlistCount
      }
    }))
    
    showNotification('Product moved to wishlist', 'success')
  } catch (error) {
    console.error('Error moving to wishlist:', error)
    showNotification(error.response?.data?.message || 'Error moving to wishlist', 'error')
  } finally {
    loading.value = false
  }
}

const applyDiscount = () => {
  discountError.value = 'Discount code feature coming soon!'
  setTimeout(() => {
    discountError.value = ''
  }, 3000)
}

const calculateTotal = () => {
  let total = props.cartTotal
  // Add shipping
  if (total < 1000) {
    total += 100
  }
  // Add tax (15%)
  total *= 1.15
  return total
}

const proceedToCheckout = () => {
  router.visit('/checkout')
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
}

.cart-product-image:hover {
  transform: scale(1.05);
}

.cart-product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* .quantity-control .form-control {
  -moz-appearance: textfield;
} */

.quantity-control .form-control::-webkit-outer-spin-button,
.quantity-control .form-control::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
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

.table td {
  vertical-align: middle;
  padding: 1rem 0.75rem;
}

.table th {
  padding: 1rem 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.875rem;
  letter-spacing: 0.5px;
}

@media (max-width: 768px) {
  .table-responsive {
    border: 0;
  }
  
  .table thead {
    display: none;
  }
  
  .table tr {
    display: block;
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
  }
  
  .table td {
    display: block;
    text-align: right;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
  }
  
  .table td::before {
    content: attr(data-label);
    float: left;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.875rem;
  }
  
  .table td:last-child {
    border-bottom: 0;
  }
  
  .quantity-control {
    justify-content: flex-end;
  }
}
</style>