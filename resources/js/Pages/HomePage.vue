<template>
  <AppLayout>
    <!-- Hero Section with Slideshow -->
    <section class="hero-section">
      <!-- Background Slideshow -->
      <div class="hero-slideshow">
        <div 
          v-for="(image, index) in heroImages" 
          :key="index"
          class="slide"
          :class="{ active: currentSlide === index }"
          :style="{ backgroundImage: `url(${image})` }"
        ></div>
        <div class="slideshow-overlay"></div>
      </div>

      <div class="hero-content">
        <h1 class="hero-title">Discover Amazing Products</h1>
        <p class="hero-subtitle">Shop the latest trends with exclusive deals</p>
        <div class="hero-search">
          <input
            type="text"
            v-model="searchTerm"
            placeholder="Search for products..."
            class="hero-search-input"
          />
          <button class="hero-search-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </div>

        <!-- Slideshow Indicators -->
        <div class="slideshow-indicators">
          <button 
            v-for="(_, index) in heroImages" 
            :key="index"
            class="indicator"
            :class="{ active: currentSlide === index }"
            @click="goToSlide(index)"
          ></button>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <!-- <section class="categories-section">
      <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="categories-grid">
          All Categories Card -->
          <!-- <div 
            class="category-card all-category"
            @click="filterByCategory('')"
            :class="{ active: selectedCategory === '' }"
          >
            <h3 class="category-name">All</h3>
            <p class="category-count">{{ products.length }} products</p>
          </div> -->

          <!-- Other Categories -->
          <!-- <div 
            v-for="category in categories" 
            :key="category.category_id"
            class="category-card"
            @click="filterByCategory(category.name)"
            :class="{ active: selectedCategory === category.name }"
          >
            <h3 class="category-name">{{ category.name }}</h3>
            <p class="category-count">{{ getCategoryCount(category.name) }} products</p>
          </div>
        </div>
      </div>
    </section> -->

    <!-- Products Section -->
    <section class="products-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">{{ selectedCategory ? selectedCategory + ' Products' : 'All Products' }}</h2>
          <div class="sort-controls">
            <select v-model="sortBy" class="sort-select">
              <option value="name">Sort by Name</option>
              <option value="price_low">Price: Low to High</option>
              <option value="price_high">Price: High to Low</option>
              <option value="newest">Newest First</option>
            </select>
          </div>
        </div>

        <!-- Products Grid -->
        <div v-if="filteredProducts.length > 0" class="products-grid">
          <div 
            v-for="product in filteredProducts" 
            :key="product.product_id"
            class="product-card"
          >
            <!-- Product Image -->
            <div class="product-image-container">
              <img 
                :src="getProductImage(product.image)" 
                :alt="product.name"
                class="product-image"
                @error="handleImageError"
              />
              <button 
                class="wishlist-btn"
                @click="toggleWishlist(product.product_id)"
                :class="{ active: isInWishlist(product.product_id) }"
              >
                <i class="fas fa-heart"></i>
              </button>
              <div v-if="product.stock <= 0" class="out-of-stock">
                Out of Stock
              </div>
              <div v-else-if="product.stock < 10" class="low-stock">
                Only {{ product.stock }} left
              </div>
            </div>

            <!-- Product Info -->
            <div class="product-info">
              <div class="product-header">
                <h3 class="product-name">{{ product.name }}</h3>
                <!-- <span class="product-reference">#{{ product.reference }}</span> -->
              </div>
              
              <!-- <p class="product-description" v-if="product.description">
                {{ truncateDescription(product.description) }}
              </p>
              
              <div class="product-meta">
                <span class="product-category">
                  <i class="fas fa-tag"></i> {{ getCategoryName(product.category_id) }}
                </span>
                <span class="product-stock">
                  <i class="fas fa-box"></i> {{ product.stock }} in stock
                </span>
              </div> -->

              <div class="product-footer">
                <div class="price-section">
                  <span class="product-price">${{ parseFloat(product.price).toFixed(2) }}</span>
                  <span v-if="product.original_price" class="original-price">
                    ${{ parseFloat(product.original_price).toFixed(2) }}
                  </span>
                </div>

                <!-- Buy Button -->
                <div class="buy-controls">
                  <button 
                    class="buy-btn"
                    @click="buyNow(product)"
                    :disabled="product.stock <= 0"
                    :class="{ disabled: product.stock <= 0 }"
                  >
                    <i class="fas fa-bolt"></i>
                    {{ product.stock > 0 ? 'Buy Now' : 'Out of Stock' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-box-open"></i>
          </div>
          <h3>No Products Found</h3>
          <p>Try adjusting your search or filter to find what you're looking for.</p>
          <button @click="clearFilters" class="btn-primary">
            Clear Filters
          </button>
        </div>
      </div>
    </section>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" :class="{ open: isCartOpen }">
      <div class="cart-header">
        <h3>Shopping Cart</h3>
        <button @click="toggleCart" class="close-cart">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div v-if="cartItems.length > 0" class="cart-items">
        <div v-for="item in cartItems" :key="item.product_id" class="cart-item">
          <img :src="getProductImage(item.image)" :alt="item.name" class="cart-item-image">
          <div class="cart-item-details">
            <h4>{{ item.name }}</h4>
            <p class="cart-item-price">${{ parseFloat(item.price).toFixed(2) }}</p>
            <div class="cart-item-qty">
              <button 
                class="qty-btn small minus"
                @click="decreaseCartQuantity(item.product_id)"
              >
                <i class="fas fa-minus"></i>
              </button>
              <span>{{ item.quantity }}</span>
              <button 
                class="qty-btn small plus"
                @click="increaseCartQuantity(item.product_id)"
                :disabled="item.quantity >= getProductStock(item.product_id)">
                <i class="fas fa-plus"></i>
              </button>
            </div>
          </div>
          <button @click="removeFromCart(item.product_id)" class="remove-item">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
      
      <div v-else class="empty-cart">
        <i class="fas fa-shopping-cart"></i>
        <p>Your cart is empty</p>
      </div>
      
      <div v-if="cartItems.length > 0" class="cart-footer">
        <div class="cart-total">
          <span>Total:</span>
          <span class="total-amount">${{ cartTotal.toFixed(2) }}</span>
        </div>
        <button @click="checkout" class="btn-checkout">
          Proceed to Checkout
        </button>
      </div>
    </div>

    <!-- Cart Toggle Button -->
    <button @click="toggleCart" class="cart-toggle">
      <i class="fas fa-shopping-cart"></i>
      <span v-if="cartTotalItems > 0" class="cart-badge">{{ cartTotalItems }}</span>
    </button>

    <!-- Notification Toast -->
    <div v-if="notification.show" class="notification-toast" :class="notification.type">
      <i :class="notification.icon"></i>
      <span>{{ notification.message }}</span>
      <button @click="hideNotification" class="close-notification">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </AppLayout>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
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
    initialWishlistIds: {
      type: Array,
      default: () => []
    },
    auth: {
      type: Object,
      default: () => ({})
    }
  },

  setup(props) {
    // Use Inertia's usePage to get auth user
    const page = usePage()
    
    // Check if props are available
    console.log('HomePage props:', props)
    console.log('Products:', props.products)
    console.log('Categories:', props.categories)
    console.log('Auth user from props:', props.auth?.user)
    console.log('Auth user from page:', page.props.auth?.user)

    // Hero images slideshow - 10 amazing e-commerce images
    const heroImages = ref([
      'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Fashion shopping
      'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Electronics store
      'https://images.unsplash.com/photo-1486401899868-0e435ed85128?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Smartphones
      'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-4.0.3&auto=format&fit=crop&w=2071&q=80', // Laptops
      'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Watches
      'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Sneakers
      'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Books
      'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Furniture
      'https://images.unsplash.com/photo-1526948128573-703ee1aeb6fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Cosmetics
      'https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'  // Shopping bags
    ])
    
    const currentSlide = ref(0)
    let slideshowInterval = null

    // State
    const searchTerm = ref('')
    const selectedCategory = ref('')
    const sortBy = ref('newest')
    const isCartOpen = ref(false)
    const cartItems = ref([])
    const wishlist = ref([])
    
    // Get user correctly from Inertia props
    const user = computed(() => {
      return props.auth?.user || page.props.auth?.user || null
    })
    
    const notification = ref({
      show: false,
      message: '',
      type: 'success',
      icon: 'fas fa-check-circle'
    })

    // Initialize wishlist from props (from backend)
    onMounted(() => {
      loadCartFromLocalStorage()
      // Initialize wishlist from backend data
      wishlist.value = props.initialWishlistIds || []
      console.log('Wishlist initialized:', wishlist.value)
      console.log('User status on mount:', user.value ? 'Logged in' : 'Not logged in')
      
      // Start slideshow
      startSlideshow()
    })

    onUnmounted(() => {
      if (slideshowInterval) {
        clearInterval(slideshowInterval)
      }
    })

    // Slideshow functions
    const startSlideshow = () => {
      slideshowInterval = setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % heroImages.value.length
      }, 5000) // Change slide every 5 seconds
    }

    const goToSlide = (index) => {
      currentSlide.value = index
      // Reset the slideshow timer when manually changing slide
      if (slideshowInterval) {
        clearInterval(slideshowInterval)
        startSlideshow()
      }
    }

    // Computed Properties
    const filteredProducts = computed(() => {
      // Check if products exist
      if (!props.products || !Array.isArray(props.products)) {
        console.error('Products prop is not available or not an array:', props.products)
        return []
      }

      let products = [...props.products]

      // Filter by search term
      if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase()
        products = products.filter(p => 
          p.name.toLowerCase().includes(term) ||
          p.description?.toLowerCase().includes(term) ||
          p.reference.toLowerCase().includes(term)
        )
      }

      // Filter by category
      if (selectedCategory.value) {
        const category = props.categories.find(c => c.name === selectedCategory.value)
        if (category) {
          products = products.filter(p => p.category_id === category.category_id)
        }
      }

      // Sort products
      switch (sortBy.value) {
        case 'price_low':
          products.sort((a, b) => parseFloat(a.price) - parseFloat(b.price))
          break
        case 'price_high':
          products.sort((a, b) => parseFloat(b.price) - parseFloat(a.price))
          break
        case 'name':
          products.sort((a, b) => a.name.localeCompare(b.name))
          break
        case 'newest':
          products.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          break
      }

      console.log('Filtered products:', products.length)
      return products
    })

    const cartTotal = computed(() => {
      return cartItems.value.reduce((total, item) => {
        return total + (parseFloat(item.price) * item.quantity)
      }, 0)
    })

    const cartTotalItems = computed(() => {
      return cartItems.value.reduce((total, item) => total + item.quantity, 0)
    })

    const getCategoryCount = (categoryName) => {
      if (!props.products || !Array.isArray(props.products)) return 0
      const category = props.categories.find(c => c.name === categoryName)
      if (!category) return 0
      return props.products.filter(p => p.category_id === category.category_id).length
    }

    // Methods
    const getProductImage = (imagePath) => {
      if (!imagePath) return 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image'
      return imagePath.startsWith('http') ? imagePath : `/storage/${imagePath}`
    }

    const handleImageError = (event) => {
      event.target.src = 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image'
    }

    const getCategoryName = (categoryId) => {
      if (!props.categories || !Array.isArray(props.categories)) return 'Uncategorized'
      const category = props.categories.find(c => c.category_id === categoryId)
      return category ? category.name : 'Uncategorized'
    }

    const truncateDescription = (description) => {
      if (!description) return ''
      if (description.length > 100) {
        return description.substring(0, 100) + '...'
      }
      return description
    }

    const filterByCategory = (categoryName) => {
      selectedCategory.value = categoryName
    }

    const clearFilters = () => {
      searchTerm.value = ''
      selectedCategory.value = ''
    }

    // Check if user is logged in
    const isLoggedIn = () => {
      return !!user.value
    }

    // ======================
    // BUY NOW METHOD - FIXED
    // ======================
    const buyNow = (product) => {
      console.log('Buy now clicked for:', product.name)
      console.log('Product data:', product)
      console.log('User status:', isLoggedIn() ? 'Logged in' : 'Not logged in')
      
      if (product.stock <= 0) {
        showNotification('Product out of stock', 'warning', 'fas fa-exclamation-triangle')
        return
      }
      
      // Check if user is logged in
      if (!isLoggedIn()) {
        showNotification('You must be logged in to buy products', 'warning', 'fas fa-exclamation-triangle')
        // Save product info to session for after login
        sessionStorage.setItem('buyAfterLogin', JSON.stringify({
          product_id: product.product_id,
          product_name: product.name,
          product_image: product.image,
          price: product.price,
          stock: product.stock,
          quantity: 1
        }))
        // Redirect to login page
        setTimeout(() => {
          router.visit('/login')
        }, 1500)
        return
      }
      
      // FIXED: Pass ALL product data to payments page
      router.visit('/submit-payment', {
        method: 'get',
        data: {
          product_id: product.product_id,
          product_name: product.name,
          product_image: product.image,
          price: product.price,
          stock: product.stock,
          quantity: 1
        }
      })
    }

    // ======================
    // CHECKOUT METHOD - FIXED  
    // ======================
    const checkout = () => {
      console.log('Checkout clicked, cart items:', cartItems.value.length)
      
      if (cartItems.value.length === 0) {
        showNotification('Your cart is empty', 'warning', 'fas fa-shopping-cart')
        return
      }
      
      // Check if user is logged in
      if (!isLoggedIn()) {
        showNotification('Please login to checkout', 'warning', 'fas fa-sign-in-alt')
        // Save cart to session for after login
        sessionStorage.setItem('cartAfterLogin', JSON.stringify(cartItems.value))
        // Redirect to login page
        setTimeout(() => {
          router.visit('/login')
        }, 1500)
        return
      }
      
      // Save cart to session for payment page
      sessionStorage.setItem('currentCart', JSON.stringify(cartItems.value))
      
      // FIXED: Redirect to submit-payment page
      router.visit('/submit-payment', {
        method: 'get',
        data: {
          cart_items: cartItems.value
        }
      })
    }

    // Add to Cart Method
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

    // Cart Methods
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
      isCartOpen.value = !isCartOpen.value
    }

    // Wishlist Methods
    const isInWishlist = (productId) => {
      return wishlist.value.includes(productId)
    }

    const toggleWishlist = (productId) => {
      if (!isLoggedIn()) {
        showNotification('Please login to add to wishlist', 'warning', 'fas fa-heart')
        router.visit('/login')
        return
      }
      
      router.post('/wishlist/toggle', {
        product_id: productId
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          wishlist.value = page.props.initialWishlistIds || []
          if (wishlist.value.includes(productId)) {
            showNotification('Added to wishlist', 'success', 'fas fa-heart')
          } else {
            showNotification('Removed from wishlist', 'info', 'fas fa-heart-broken')
          }
        },
        onError: (errors) => {
          showNotification('Failed to update wishlist', 'error', 'fas fa-exclamation-circle')
        }
      })
    }

    // Notification
    const showNotification = (message, type = 'success', icon = 'fas fa-check-circle') => {
      notification.value = { show: true, message, type, icon }
      setTimeout(hideNotification, 3000)
    }

    const hideNotification = () => {
      notification.value.show = false
    }

    // Local Storage
    const saveCartToLocalStorage = () => {
      localStorage.setItem('cart', JSON.stringify(cartItems.value))
    }

    const loadCartFromLocalStorage = () => {
      const savedCart = localStorage.getItem('cart')
      if (savedCart) {
        cartItems.value = JSON.parse(savedCart)
      }
    }

    return {
      heroImages,
      currentSlide,
      searchTerm,
      selectedCategory,
      sortBy,
      isCartOpen,
      cartItems,
      wishlist,
      user,
      notification,
      filteredProducts,
      cartTotal,
      cartTotalItems,
      getProductImage,
      handleImageError,
      getCategoryName,
      getCategoryCount,
      truncateDescription,
      filterByCategory,
      clearFilters,
      getProductStock,
      buyNow,
      addToCart,
      checkout,
      increaseCartQuantity,
      decreaseCartQuantity,
      removeFromCart,
      toggleCart,
      isInWishlist,
      toggleWishlist,
      showNotification,
      hideNotification,
      isLoggedIn,
      goToSlide,
      saveCartToLocalStorage,
      loadCartFromLocalStorage
    }
  }
}
</script>

<style scoped>
/* Hero Section */
.hero-section {
  position: relative;
  height: 600px;
  overflow: hidden;
}

.hero-slideshow {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.slide.active {
  opacity: 1;
}

.slideshow-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
}

.hero-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: white;
  text-align: center;
  padding: 0 20px;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 800;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.hero-subtitle {
  font-size: 1.5rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.hero-search {
  display: flex;
  max-width: 600px;
  width: 100%;
  margin-bottom: 3rem;
}

.hero-search-input {
  flex: 1;
  padding: 1rem 1.5rem;
  border: none;
  border-radius: 50px 0 0 50px;
  font-size: 1rem;
  outline: none;
}

.hero-search-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 0 2rem;
  border-radius: 0 50px 50px 0;
  cursor: pointer;
  transition: background 0.3s;
}

.hero-search-btn:hover {
  background: #2563eb;
}

.search-icon {
  width: 24px;
  height: 24px;
}

.slideshow-indicators {
  display: flex;
  gap: 10px;
  margin-top: 2rem;
}

.indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: rgba(255,255,255,0.5);
  border: none;
  cursor: pointer;
  transition: all 0.3s;
}

.indicator.active {
  background: white;
  transform: scale(1.2);
}

/* Categories Section */
.categories-section {
  padding: 4rem 0;
  background: #f8fafc;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 3rem;
  text-align: center;
  color: #1e293b;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.category-card {
  background: white;
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
  cursor: pointer;
  transition: all 0.3s;
  text-align: center;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
}

.category-card.active {
  background: #3b82f6;
  color: white;
}

.category-card.all-category {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.category-name {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.category-count {
  opacity: 0.8;
  font-size: 0.9rem;
}

/* Products Section */
.products-section {
  padding: 4rem 0;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.sort-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.sort-select {
  padding: 0.75rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  color: #1e293b;
  font-size: 1rem;
  outline: none;
  cursor: pointer;
  transition: border-color 0.3s;
}

.sort-select:focus {
  border-color: #3b82f6;
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.product-card {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
  transition: all 0.3s;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
}

.product-image-container {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s;
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.wishlist-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(255,255,255,0.9);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #64748b;
  transition: all 0.3s;
}

.wishlist-btn:hover {
  background: white;
  color: #ef4444;
}

.wishlist-btn.active {
  background: #ef4444;
  color: white;
}

.out-of-stock, .low-stock {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 0.5rem;
  text-align: center;
  font-weight: 600;
  font-size: 0.9rem;
}

.out-of-stock {
  background: rgba(239,68,68,0.9);
  color: white;
}

.low-stock {
  background: rgba(245,158,11,0.9);
  color: white;
}

.product-info {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-header {
  margin-bottom: 1rem;
}

.product-name {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 0.25rem;
}

.product-reference {
  font-size: 0.9rem;
  color: #64748b;
}

.product-description {
  color: #64748b;
  margin-bottom: 1rem;
  line-height: 1.5;
  flex: 1;
}

.product-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  color: #64748b;
}

.product-category, .product-stock {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.product-footer {
  margin-top: auto;
}

.price-section {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.product-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #3b82f6;
}

.original-price {
  font-size: 1rem;
  color: #94a3b8;
  text-decoration: line-through;
}

.buy-controls {
  display: flex;
  gap: 0.5rem;
}

.buy-btn {
  flex: 1;
  background: #3b82f6;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.buy-btn:hover:not(.disabled) {
  background: #2563eb;
}

.buy-btn.disabled {
  background: #94a3b8;
  cursor: not-allowed;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-icon {
  font-size: 4rem;
  color: #cbd5e1;
  margin-bottom: 1.5rem;
}

.empty-state h3 {
  font-size: 1.75rem;
  color: #64748b;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #94a3b8;
  margin-bottom: 2rem;
}

.btn-primary {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 0.75rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-primary:hover {
  background: #2563eb;
}

/* Cart Sidebar */
.cart-sidebar {
  position: fixed;
  top: 0;
  right: -400px;
  width: 400px;
  height: 100vh;
  background: white;
  box-shadow: -5px 0 25px rgba(0,0,0,0.1);
  transition: right 0.3s ease-in-out;
  z-index: 1000;
  display: flex;
  flex-direction: column;
}

.cart-sidebar.open {
  right: 0;
}

.cart-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-header h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
}

.close-cart {
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  font-size: 1.25rem;
  padding: 0.5rem;
  border-radius: 50%;
  transition: background 0.3s;
}

.close-cart:hover {
  background: #f1f5f9;
}

.cart-items {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
}

.cart-item {
  display: flex;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid #e2e8f0;
}

.cart-item:last-child {
  border-bottom: none;
}

.cart-item-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  margin-right: 1rem;
}

.cart-item-details {
  flex: 1;
}

.cart-item-details h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.cart-item-price {
  color: #3b82f6;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.cart-item-qty {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.qty-btn {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: 1px solid #cbd5e1;
  background: white;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.qty-btn:hover:not(:disabled) {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.qty-btn.small {
  width: 24px;
  height: 24px;
}

.remove-item {
  background: none;
  border: none;
  color: #ef4444;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: background 0.3s;
  margin-left: 0.5rem;
}

.remove-item:hover {
  background: #fee2e2;
}

.empty-cart {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
}

.empty-cart i {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.cart-footer {
  padding: 1.5rem;
  border-top: 1px solid #e2e8f0;
}

.cart-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
}

.total-amount {
  color: #3b82f6;
}

.btn-checkout {
  width: 100%;
  background: #10b981;
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-checkout:hover {
  background: #059669;
}

/* Cart Toggle Button */
.cart-toggle {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #3b82f6;
  color: white;
  border: none;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(59,130,246,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  transition: all 0.3s;
  z-index: 999;
}

.cart-toggle:hover {
  background: #2563eb;
  transform: scale(1.1);
}

.cart-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ef4444;
  color: white;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 600;
}

/* Notification Toast */
.notification-toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: white;
  color: #1e293b;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  display: flex;
  align-items: center;
  gap: 1rem;
  max-width: 400px;
  animation: slideIn 0.3s ease-out;
  z-index: 1001;
}

.notification-toast.success {
  border-left: 4px solid #10b981;
}

.notification-toast.warning {
  border-left: 4px solid #f59e0b;
}

.notification-toast.error {
  border-left: 4px solid #ef4444;
}

.notification-toast.info {
  border-left: 4px solid #3b82f6;
}

.notification-toast i {
  font-size: 1.25rem;
}

.notification-toast.success i {
  color: #10b981;
}

.notification-toast.warning i {
  color: #f59e0b;
}

.notification-toast.error i {
  color: #ef4444;
}

.notification-toast.info i {
  color: #3b82f6;
}

.close-notification {
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 50%;
  transition: background 0.3s;
  margin-left: auto;
}

.close-notification:hover {
  background: #f1f5f9;
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
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-subtitle {
    font-size: 1.25rem;
  }
  
  .hero-search {
    max-width: 90%;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }
  
  .cart-sidebar {
    width: 100%;
    right: -100%;
  }
  
  .cart-toggle {
    bottom: 1rem;
    right: 1rem;
  }
  
  .notification-toast {
    left: 1rem;
    right: 1rem;
    max-width: calc(100% - 2rem);
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-subtitle {
    font-size: 1rem;
  }
  
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .cart-toggle {
    width: 50px;
    height: 50px;
    font-size: 1.25rem;
  }
}
</style>