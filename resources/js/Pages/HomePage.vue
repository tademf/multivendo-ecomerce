<template>
  <AppLayout>
    <!-- Hero Section with Bootstrap Carousel -->
    <section class="hero-section">
      <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
          <button 
            v-for="(_, index) in heroImages" 
            :key="index"
            type="button" 
            data-bs-target="#heroCarousel"
            :data-bs-slide-to="index"
            :class="{ active: currentSlide === index }"
          ></button>
        </div>

        <!-- Carousel Items -->
        <div class="carousel-inner">
          <div 
            v-for="(image, index) in heroImages" 
            :key="index"
            class="carousel-item"
            :class="{ active: currentSlide === index }"
            :style="{ backgroundImage: `url(${image})` }"
          >
            <div class="slideshow-overlay"></div>
          </div>
        </div>

        <!-- Carousel Content -->
        <div class="carousel-content">
          <div class="container">
            <div class="row justify-content-center text-center">
              <div class="col-12 col-lg-10 col-xl-8">
                <h1 class="display-3 fw-bold mb-4 text-white">Discover Amazing Products</h1>
                <p class="lead mb-5 text-white">Shop the latest trends with exclusive deals</p>
                
                <!-- REMOVED: Search container -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Products Section with Bootstrap Grid -->
    <section class="products-section py-5 bg-light">
      <div class="container">
        <!-- Section Header -->
        <div class="row align-items-center mb-5">
          <div class="col-12 col-md-6">
            <h2 class="h1 fw-bold mb-3">
              {{ selectedCategory ? selectedCategory + ' Products' : 'All Products' }}
              <!-- Show search term if searching -->
              <span v-if="searchTerm" class="text-primary">: "{{ searchTerm }}"</span>
            </h2>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex justify-content-md-end">
              <div class="sort-controls w-100 w-md-auto">
                <select v-model="sortBy" class="form-select form-select-lg border-0 shadow-sm">
                  <option value="name">Sort by Name</option>
                  <option value="price_low">Price: Low to High</option>
                  <option value="price_high">Price: High to Low</option>
                  <option value="newest">Newest First</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Products Grid - Bootstrap 5 Grid -->
        <div v-if="filteredProducts.length > 0" class="row g-4">
          <div 
            v-for="product in filteredProducts" 
            :key="product.product_id"
            class="col-6 col-md-4 col-lg-3 col-xl-2-4"
          >
            <div class="card product-card h-100 border-0 shadow-sm hover-shadow">
              <!-- Product Image -->
              <div class="position-relative overflow-hidden bg-light rounded-top">
                <img 
                  :src="getProductImage(product.image)" 
                  :alt="product.name"
                  class="card-img-top product-image p-3"
                  @error="handleImageError"
                  style="height: 200px; object-fit: contain;"
                />
                
                <!-- Stock Badge -->
                <div v-if="product.stock <= 0" class="badge bg-danger position-absolute start-0 bottom-0 m-3">
                  Out of Stock
                </div>
                <div v-else-if="product.stock < 10" class="badge bg-warning text-dark position-absolute start-0 bottom-0 m-3">
                  Only {{ product.stock }} left
                </div>
              </div>

              <!-- Product Info -->
              <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold mb-2 text-truncate" :title="product.name">
                  {{ product.name }}
                </h5>
                
                <div class="mt-auto">
                  <!-- Price -->
                  <div class="d-flex align-items-center mb-3">
                    <span class="h4 fw-bold text-primary mb-0">
                      {{ formatPrice(product.price) }} Birr
                    </span>
                    <span v-if="product.original_price" class="text-muted text-decoration-line-through ms-2">
                      {{ formatPrice(product.original_price) }} Birr
                    </span>
                  </div>

                  <!-- Buy Button -->
                  <button 
                    class="btn btn-primary w-100 fw-bold py-2"
                    @click="buyNow(product)"
                    :disabled="product.stock <= 0"
                    :class="{ 'btn-secondary': product.stock <= 0 }"
                  >
                    <i class="fas fa-bolt me-2"></i>
                    {{ product.stock > 0 ? 'Buy Now' : 'Out of Stock' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-5">
          <div class="empty-state">
            <div class="empty-icon mb-4">
              <i class="fas fa-box-open fa-4x text-muted"></i>
            </div>
            <h3 class="h2 fw-bold mb-3">No Products Found</h3>
            <p class="text-muted mb-4" v-if="searchTerm">
              No products found for "{{ searchTerm }}"
            </p>
            <p class="text-muted mb-4" v-else-if="selectedCategory">
              No products found in {{ selectedCategory }}
            </p>
            <p class="text-muted mb-4" v-else>
              No products available
            </p>
            <button @click="clearFilters" class="btn btn-primary btn-lg px-4">
              Clear Filters
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Cart Offcanvas (Bootstrap 5 Offcanvas) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold" id="cartOffcanvasLabel">
          <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      
      <div class="offcanvas-body">
        <div v-if="cartItems.length > 0" class="cart-items">
          <div v-for="item in cartItems" :key="item.product_id" class="card mb-3 border-0 shadow-sm">
            <div class="row g-0 align-items-center">
              <div class="col-4">
                <img :src="getProductImage(item.image)" :alt="item.name" class="img-fluid rounded-start" style="height: 80px; object-fit: cover;">
              </div>
              <div class="col-8">
                <div class="card-body py-2">
                  <h6 class="card-title fw-bold mb-1 text-truncate">{{ item.name }}</h6>
                  <p class="card-text text-primary fw-bold mb-2">{{ formatPrice(item.price) }} Birr</p>
                  <div class="d-flex align-items-center">
                    <div class="btn-group btn-group-sm" role="group">
                      <button 
                        class="btn btn-outline-secondary"
                        @click="decreaseCartQuantity(item.product_id)"
                      >
                        <i class="fas fa-minus"></i>
                      </button>
                      <span class="btn btn-light border px-3">{{ item.quantity }}</span>
                      <button 
                        class="btn btn-outline-secondary"
                        @click="increaseCartQuantity(item.product_id)"
                        :disabled="item.quantity >= getProductStock(item.product_id)"
                      >
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                    <button @click="removeFromCart(item.product_id)" class="btn btn-link text-danger ms-auto">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-5">
          <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
          <p class="text-muted">Your cart is empty</p>
        </div>
      </div>
      
      <div v-if="cartItems.length > 0" class="offcanvas-footer border-top p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="h5 fw-bold mb-0">Total:</span>
          <span class="h4 fw-bold text-primary mb-0">{{ formatPrice(cartTotal) }} Birr</span>
        </div>
        <button @click="checkout" class="btn btn-success w-100 fw-bold py-3">
          Proceed to Checkout
        </button>
      </div>
    </div>

    <!-- Cart Toggle Button -->
    <button 
      class="btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle shadow-lg"
      style="width: 64px; height: 64px; z-index: 1000;"
      data-bs-toggle="offcanvas"
      data-bs-target="#cartOffcanvas"
      aria-controls="cartOffcanvas"
    >
      <i class="fas fa-shopping-cart"></i>
      <span v-if="cartTotalItems > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        {{ cartTotalItems }}
      </span>
    </button>

    <!-- Bootstrap Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
      <div 
        class="toast"
        :class="{ show: notification.show }"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div class="toast-header" :class="getToastHeaderClass(notification.type)">
          <i :class="notification.icon" class="me-2"></i>
          <strong class="me-auto">{{ getNotificationTitle(notification.type) }}</strong>
          <button type="button" class="btn-close btn-close-white" @click="hideNotification"></button>
        </div>
        <div class="toast-body">
          {{ notification.message }}
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  name: "HomePage",
  
  components: {
    AppLayout
  },

  props: {
    products: {
      type: Array,
      default: () => []
    },
    categories: {
      type: Array,
      default: () => []
    },
    auth: {
      type: Object,
      default: () => ({})
    },
    // Add these props to receive search/category from URL
    search: {
      type: String,
      default: ''
    },
    category: {
      type: String,
      default: ''
    }
  },

  setup(props) {
    let heroCarousel = null
    
    // Hero images slideshow
    const heroImages = ref([
      'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80',
      'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80',
      'https://images.unsplash.com/photo-1486401899868-0e435ed85128?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80',
      'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-4.0.3&auto=format&fit=crop&w=2071&q=80',
      'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80',
    ])
    
    const currentSlide = ref(0)

    // State - Initialize with props from URL
    const searchTerm = ref(props.search || '')
    const selectedCategory = ref(props.category || '')
    const sortBy = ref('newest')
    const cartItems = ref([])
    
    const user = computed(() => {
      return props.auth?.user || usePage().props.auth?.user || null
    })
    
    const notification = ref({
      show: false,
      message: '',
      type: 'success',
      icon: 'fas fa-check-circle'
    })

    // Initialize
    onMounted(() => {
      loadCartFromLocalStorage()
      
      // Initialize Bootstrap Carousel using window.bootstrap
      const carouselElement = document.getElementById('heroCarousel')
      if (carouselElement && window.bootstrap) {
        heroCarousel = new window.bootstrap.Carousel(carouselElement, {
          interval: 5000,
          ride: 'carousel'
        })
        
        // Update current slide when carousel slides
        carouselElement.addEventListener('slid.bs.carousel', (event) => {
          currentSlide.value = event.to
        })
      }
      
      // Listen for custom search events from navbar
      window.addEventListener('navbar-search', handleNavbarSearch)
      window.addEventListener('navbar-category-select', handleNavbarCategorySelect)
    })

    onUnmounted(() => {
      if (heroCarousel) {
        heroCarousel.dispose()
      }
      // Remove event listeners
      window.removeEventListener('navbar-search', handleNavbarSearch)
      window.removeEventListener('navbar-category-select', handleNavbarCategorySelect)
    })

    // Watch for changes in search/category and update URL
    watch(searchTerm, (newValue) => {
      updateURL()
    })

    watch(selectedCategory, (newValue) => {
      updateURL()
    })

    // Computed Properties
    const filteredProducts = computed(() => {
      if (!props.products || !Array.isArray(props.products)) {
        return []
      }

      let filtered = [...props.products]

      // Filter by category if selected
      if (selectedCategory.value) {
        // Find the category object
        const categoryObj = props.categories.find(c => 
          c.name.toLowerCase() === selectedCategory.value.toLowerCase()
        )
        
        if (categoryObj) {
          filtered = filtered.filter(product => {
            return product.category_id === categoryObj.category_id
          })
        }
      }

      // Filter by search term if provided
      if (searchTerm.value && searchTerm.value.trim()) {
        const term = searchTerm.value.toLowerCase().trim()
        
        filtered = filtered.filter(product => {
          const nameMatch = product.name && product.name.toLowerCase().includes(term)
          const descMatch = product.description && product.description.toLowerCase().includes(term)
          const refMatch = product.reference && product.reference.toLowerCase().includes(term)
          
          return nameMatch || descMatch || refMatch
        })
      }

      // Sort products
      switch (sortBy.value) {
        case 'price_low':
          filtered.sort((a, b) => parseFloat(a.price) - parseFloat(b.price))
          break
        case 'price_high':
          filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price))
          break
        case 'name':
          filtered.sort((a, b) => a.name.localeCompare(b.name))
          break
        case 'newest':
          filtered.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))
          break
      }

      return filtered
    })

    const cartTotal = computed(() => {
      return cartItems.value.reduce((total, item) => {
        return total + (parseFloat(item.price || 0) * (item.quantity || 1))
      }, 0)
    })

    const cartTotalItems = computed(() => {
      return cartItems.value.reduce((total, item) => total + (item.quantity || 1), 0)
    })

    // Methods
    const getProductImage = (imagePath) => {
      if (!imagePath) return 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image'
      return imagePath.startsWith('http') ? imagePath : `/storage/${imagePath}`
    }

    const handleImageError = (event) => {
      event.target.src = 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image'
    }

    const formatPrice = (price) => {
      const num = parseFloat(price) || 0
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(num)
    }

    const clearFilters = () => {
      searchTerm.value = ''
      selectedCategory.value = ''
      updateURL()
      showNotification('Showing all products', 'info', 'fas fa-list')
    }

    const isLoggedIn = () => {
      return !!user.value
    }

    // Update URL with current filters
    const updateURL = () => {
      const url = new URL(window.location)
      
      if (searchTerm.value) {
        url.searchParams.set('search', encodeURIComponent(searchTerm.value))
      } else {
        url.searchParams.delete('search')
      }
      
      if (selectedCategory.value) {
        url.searchParams.set('category', encodeURIComponent(selectedCategory.value))
      } else {
        url.searchParams.delete('category')
      }
      
      window.history.replaceState({}, '', url)
    }

    // Handle navbar search event
    const handleNavbarSearch = (event) => {
      searchTerm.value = event.detail.searchTerm
      selectedCategory.value = '' // Clear category when searching
      
      if (event.detail.searchTerm) {
        showNotification(`Search results for: "${event.detail.searchTerm}"`, 'info', 'fas fa-search')
      }
    }

    // Handle navbar category selection
    const handleNavbarCategorySelect = (event) => {
      selectedCategory.value = event.detail.categoryName
      searchTerm.value = '' // Clear search when selecting category
      
      if (event.detail.categoryName) {
        showNotification(`Showing: ${event.detail.categoryName}`, 'info', 'fas fa-filter')
      }
    }

    // Buy Now function
    const buyNow = (product) => {
      if (product.stock <= 0) {
        showNotification('Product out of stock', 'warning', 'fas fa-exclamation-triangle')
        return
      }
      
      if (!isLoggedIn()) {
        showNotification('You must be logged in to buy products', 'warning', 'fas fa-exclamation-triangle')
        sessionStorage.setItem('buyAfterLogin', JSON.stringify({
          product_id: product.product_id,
          product_name: product.name,
          product_image: product.image,
          price: product.price,
          stock: product.stock,
          quantity: 1
        }))
        setTimeout(() => {
          router.visit('/login')
        }, 1500)
        return
      }
      
      router.visit('/payment', {
        method: 'get',
        data: {
          product_id: product.product_id,
          product_name: product.name,
          product_image: product.image,
          price: product.price,
          quantity: 1
        }
      })
    }

    // Checkout function
    const checkout = () => {
      if (cartItems.value.length === 0) {
        showNotification('Your cart is empty', 'warning', 'fas fa-shopping-cart')
        return
      }
      
      if (!isLoggedIn()) {
        showNotification('Please login to checkout', 'warning', 'fas fa-sign-in-alt')
        sessionStorage.setItem('cartAfterLogin', JSON.stringify(cartItems.value))
        setTimeout(() => {
          router.visit('/login')
        }, 1500)
        return
      }
      
      // For cart checkout, redirect to payment with first item
      const firstItem = cartItems.value[0]
      router.visit('/payment', {
        method: 'get',
        data: {
          product_id: firstItem.product_id,
          product_name: firstItem.name,
          product_image: firstItem.image,
          price: firstItem.price,
          quantity: firstItem.quantity
        }
      })
    }

    const addToCart = (product) => {
      const existingItem = cartItems.value.find(item => item.product_id === product.product_id)
      
      if (existingItem) {
        if (existingItem.quantity < product.stock) {
          existingItem.quantity++
          showNotification(`Increased ${product.name} quantity to ${existingItem.quantity}`, 'success', 'fas fa-cart-plus')
        } else {
          showNotification(`Cannot add more than available stock (${product.stock})`, 'warning', 'fas fa-exclamation-triangle')
          return
        }
      } else {
        if (product.stock > 0) {
          cartItems.value.push({
            product_id: product.product_id,
            name: product.name,
            price: product.price,
            image: product.image,
            stock: product.stock,
            quantity: 1
          })
          showNotification(`${product.name} added to cart`, 'success', 'fas fa-cart-plus')
        } else {
          showNotification('Product out of stock', 'warning', 'fas fa-exclamation-triangle')
          return
        }
      }
      
      saveCartToLocalStorage()
    }

    const getProductStock = (productId) => {
      if (!props.products || !Array.isArray(props.products)) return 0
      const product = props.products.find(p => p.product_id === productId)
      return product ? product.stock : 0
    }

    const increaseCartQuantity = (productId) => {
      const product = props.products.find(p => p.product_id === productId)
      if (!product) return
      
      const cartItem = cartItems.value.find(item => item.product_id === productId)
      if (cartItem && cartItem.quantity < product.stock) {
        cartItem.quantity++
        saveCartToLocalStorage()
      }
    }

    const decreaseCartQuantity = (productId) => {
      const cartItem = cartItems.value.find(item => item.product_id === productId)
      if (cartItem) {
        if (cartItem.quantity > 1) {
          cartItem.quantity--
        } else {
          removeFromCart(productId)
        }
        saveCartToLocalStorage()
      }
    }

    const removeFromCart = (productId) => {
      const index = cartItems.value.findIndex(item => item.product_id === productId)
      if (index > -1) {
        cartItems.value.splice(index, 1)
        saveCartToLocalStorage()
      }
    }

    const toggleCart = () => {
      const offcanvasElement = document.getElementById('cartOffcanvas')
      if (offcanvasElement && window.bootstrap) {
        const offcanvas = window.bootstrap.Offcanvas.getInstance(offcanvasElement) || new window.bootstrap.Offcanvas(offcanvasElement)
        offcanvas.toggle()
      }
    }

    const showNotification = (message, type = 'success', icon = 'fas fa-check-circle') => {
      notification.value = { show: true, message, type, icon }
      setTimeout(hideNotification, 3000)
    }

    const hideNotification = () => {
      notification.value.show = false
    }

    const getToastHeaderClass = (type) => {
      const classes = {
        success: 'bg-success text-white',
        warning: 'bg-warning text-dark',
        error: 'bg-danger text-white',
        info: 'bg-info text-white'
      }
      return classes[type] || classes.success
    }

    const getNotificationTitle = (type) => {
      const titles = {
        success: 'Success',
        warning: 'Warning',
        error: 'Error',
        info: 'Information'
      }
      return titles[type] || 'Notification'
    }

    const saveCartToLocalStorage = () => {
      localStorage.setItem('cart', JSON.stringify(cartItems.value))
    }

    const loadCartFromLocalStorage = () => {
      const savedCart = localStorage.getItem('cart')
      if (savedCart) {
        try {
          cartItems.value = JSON.parse(savedCart)
        } catch (error) {
          console.error('Error loading cart from localStorage:', error)
          cartItems.value = []
        }
      }
    }

    return {
      heroImages,
      currentSlide,
      searchTerm,
      selectedCategory,
      sortBy,
      cartItems,
      user,
      notification,
      filteredProducts,
      cartTotal,
      cartTotalItems,
      getProductImage,
      handleImageError,
      formatPrice,
      clearFilters,
      getProductStock,
      buyNow,
      addToCart,
      checkout,
      increaseCartQuantity,
      decreaseCartQuantity,
      removeFromCart,
      toggleCart,
      showNotification,
      hideNotification,
      getToastHeaderClass,
      getNotificationTitle,
      isLoggedIn,
      saveCartToLocalStorage,
      loadCartFromLocalStorage,
      handleNavbarSearch,
      handleNavbarCategorySelect
    }
  }
}
</script>

<style scoped>
.col-xl-2-4 {
  width: 20%;
  flex: 0 0 auto;
}

/* Hero Section */
.hero-section {
  position: relative;
}

.carousel-item {
  height: 85vh;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.slideshow-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, 
    rgba(0, 0, 0, 0.85) 0%, 
    rgba(0, 0, 0, 0.6) 50%, 
    rgba(0, 0, 0, 0.4) 100%);
}

.carousel-content {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  z-index: 10;
}

.search-container {
  max-width: 600px;
}

/* Product Card Customizations */
.product-card {
  transition: all 0.3s ease;
  border-radius: 16px !important;
  overflow: hidden;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
}

.hover-shadow:hover {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Offcanvas Customization */
.offcanvas-footer {
  background: #f8f9fa;
}

/* Custom carousel indicator color */
.carousel-indicators button {
  width: 12px !important;
  height: 12px !important;
  border-radius: 50% !important;
  background-color: rgba(255, 255, 255, 0.5) !important;
  border: 2px solid transparent !important;
}

.carousel-indicators button.active {
  background-color: white !important;
  border-color: white !important;
  transform: scale(1.2);
}

/* Toast Customization */
.toast {
  border-radius: 12px !important;
  border: none !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
  transition: opacity 0.3s ease;
}

/* Responsive Adjustments */
@media (max-width: 1400px) {
  .col-xl-2-4 {
    width: 25%;
    flex: 0 0 auto;
  }
}

@media (max-width: 1200px) {
  .col-xl-2-4 {
    width: 33.333333%;
    flex: 0 0 auto;
  }
}

@media (max-width: 992px) {
  .col-xl-2-4 {
    width: 50%;
    flex: 0 0 auto;
  }
  
  .hero-section {
    min-height: 70vh;
  }
  
  .carousel-item {
    height: 70vh;
  }
}

@media (max-width: 768px) {
  .col-xl-2-4 {
    width: 100%;
    flex: 0 0 auto;
  }
  
  .hero-section {
    min-height: 60vh;
  }
  
  .carousel-item {
    height: 60vh;
  }
  
  .display-3 {
    font-size: 2.5rem !important;
  }
}

@media (max-width: 576px) {
  .hero-section {
    min-height: 50vh;
  }
  
  .carousel-item {
    height: 50vh;
  }
  
  .display-3 {
    font-size: 2rem !important;
  }
  
  .lead {
    font-size: 1rem !important;
  }
}

/* Ensure text is visible on carousel */
.text-white {
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

/* Fix for badge positioning on cart button */
.position-absolute.badge {
  font-size: 0.7rem;
  padding: 0.25em 0.6em;
  min-width: 1.5em;
}
</style>