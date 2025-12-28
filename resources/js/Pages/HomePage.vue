<template>
  <AppLayout>
    <div class="animated-background">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
      <div class="shape shape-4"></div>
    </div>

    <div>
      <!-- Hero Section -->
      <section class="hero-section bg-white position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100 h-100">
          <div class="pattern-dots"></div>
          <div class="pattern-gradient"></div>
        </div>

        <div class="container position-relative z-2 py-5 py-lg-6">
          <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
              <div class="pe-lg-4">
                <h1 class="display-4 fw-bold mb-4">
                  Welcome to
                  <span class="text-gradient-primary"> E-SHOP</span>
                </h1>
                <p class="lead text-muted mb-4 fs-5">
                  Discover amazing products from trusted sellers.
                  Shop with confidence and convenience.
                </p>
                <div class="d-flex flex-wrap gap-3">
                  <button
                    @click="scrollToProducts"
                    class="btn btn-lg btn-primary px-4 shadow-sm"
                    type="button"
                  >
                    <i class="fas fa-shopping-bag me-2"></i>Shop Now
                  </button>
                </div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="position-relative">
                <div class="hero-slider rounded-4 overflow-hidden shadow-lg">
                  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded-4">
                      <div
                        v-for="(image, index) in heroImages"
                        :key="index"
                        class="carousel-item"
                        :class="{ active: index === 0 }"
                      >
                        <img
                          :src="image"
                          class="d-block w-100"
                          alt="Featured Product"
                          style="height: 400px; object-fit: cover;"
                        >
                        <div class="carousel-overlay"></div>
                      </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Next</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Regular Products Section -->
      <section class="products-section py-5 bg-white" ref="productsSection">
        <div class="container">
          <div class="row align-items-center mb-4">
            <div class="col-md-8">
              <h2 class="h3 fw-bold mb-2">
                {{ selectedCategory ? selectedCategory : 'All Products' }}
                <span v-if="searchTerm" class="text-primary">: "{{ searchTerm }}"</span>
              </h2>
              <p class="text-muted mb-0">
                {{ filteredProducts.length }} products found
              </p>
            </div>
            <div class="col-md-4">
              <div class="d-flex gap-2 justify-content-md-end">
                <!-- Pure CSS Dropdown -->
                <div class="sort-dropdown-container" ref="sortDropdown">
                  <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" 
                            type="button" 
                            @click="toggleDropdown"
                            :class="{ 'show': isDropdownOpen }"
                            aria-expanded="false">
                      <i class="fas fa-sort me-1"></i> Sort
                    </button>
                    <ul class="dropdown-menu" :class="{ 'show': isDropdownOpen }">
                      <li>
                        <a href="#"
                           class="dropdown-item"
                           :class="{ active: sortBy === 'newest' }"
                           @click.prevent="setSort('newest')">
                          Newest First
                        </a>
                      </li>
                      <li>
                        <a href="#"
                           class="dropdown-item"
                           :class="{ active: sortBy === 'price_low' }"
                           @click.prevent="setSort('price_low')">
                          Price: Low to High
                        </a>
                      </li>
                      <li>
                        <a href="#"
                           class="dropdown-item"
                           :class="{ active: sortBy === 'price_high' }"
                           @click.prevent="setSort('price_high')">
                          Price: High to Low
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Regular Products Grid -->
          <div v-if="filteredProducts.length > 0" class="row g-4">
            <div
              v-for="product in paginatedProducts"
              :key="product.product_id"
              class="col-6 col-md-4 col-lg-3"
            >
              <div class="card product-card h-100 border-0 shadow-sm">
                <!-- Product Image -->
                <div class="product-image-container position-relative overflow-hidden bg-light rounded-top">
                  <img
                    :src="getProductImage(product.image)"
                    :alt="product.name"
                    class="product-img"
                    @error="handleImageError"
                    @click="goToProductPage(product)"
                  />

                  <!-- Action Icons Overlay -->
                  <div class="product-actions position-absolute top-0 end-0 m-2 d-flex flex-column gap-2">
                    <!-- Wishlist Icon -->
                    <button
                      @click.stop="toggleWishlist(product)"
                      class="btn btn-sm shadow-sm"
                      :class="isInWishlist(product) ? 'btn-danger' : 'btn-light'"
                      type="button"
                      :title="isInWishlist(product) ? 'Remove from wishlist' : 'Add to wishlist'"
                    >
                      <i class="fas fa-heart"></i>
                    </button>
                    
                    <!-- Cart Icon -->
                    <button
                      v-if="!isProductOwner(product) && product.stock > 0"
                      @click.stop="addToCart(product)"
                      class="btn btn-sm btn-primary shadow-sm"
                      type="button"
                      title="Add to cart"
                    >
                      <i class="fas fa-shopping-cart"></i>
                    </button>
                  </div>

                  <!-- Owner Badge -->
                  <div v-if="isProductOwner(product)" class="badge bg-secondary position-absolute top-0 start-0 m-2">
                    <i class="fas fa-store me-1"></i> Your Product
                  </div>

                  <!-- Stock Badge -->
                  <div v-else-if="product.stock <= 0" class="badge bg-danger position-absolute bottom-0 start-0 m-2">
                    <i class="fas fa-times-circle me-1"></i> Out of Stock
                  </div>
                  <div v-else-if="product.stock < 10" class="badge bg-warning position-absolute bottom-0 start-0 m-2">
                    <i class="fas fa-exclamation-triangle me-1"></i> {{ product.stock }} left
                  </div>
                </div>

                <!-- Product Info -->
                <div class="card-body d-flex flex-column p-3">
                  <h6 class="card-title fw-bold mb-2 text-truncate cursor-pointer" @click="goToProductPage(product)">
                    {{ product.name }}
                  </h6>

                  <!-- Price -->
                  <div class="d-flex align-items-center mb-3">
                    <span class="h5 fw-bold text-primary mb-0">
                      {{ formatPrice(product.price) }} Birr
                    </span>
                  </div>

                  <!-- Category -->
                  <div class="mb-3">
                    <span class="badge bg-light text-dark">
                      {{ getCategoryName(product.category_id) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State for Regular Products -->
          <div v-else class="text-center py-5">
            <div class="empty-state">
              <div class="empty-icon mb-4">
                <i class="fas fa-search fa-4x text-muted"></i>
              </div>
              <h3 class="h4 fw-bold mb-3">No Products Found</h3>
              <p class="text-muted mb-4">
                {{ searchTerm ? `No results for "${searchTerm}"` :
                   selectedCategory ? `No products in "${selectedCategory}"` :
                   'No products available' }}
              </p>
              <button @click="clearFilters" class="btn btn-primary" type="button">
                <i class="fas fa-redo me-2"></i>Show All Products
              </button>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="filteredProducts.length > 0 && totalPages > 1" class="d-flex justify-content-center mt-5">
            <nav aria-label="Product pagination">
              <ul class="pagination">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                  <button class="page-link" type="button" @click="prevPage">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                </li>

                <li
                  v-for="page in visiblePages"
                  :key="page"
                  class="page-item"
                  :class="{ active: page === currentPage }"
                >
                  <button class="page-link" type="button" @click="goToPage(page)">
                    {{ page }}
                  </button>
                </li>

                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                  <button class="page-link" type="button" @click="nextPage">
                    <i class="fas fa-chevron-right"></i>
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </section>

      <!-- Discounted Products Section -->
      <section class="discounted-products-section py-5 bg-light">
        <div class="container">
          <!-- Section Header -->
          <div class="row align-items-center mb-4">
            <div class="col-md-12">
              <h2 class="h3 fw-bold mb-2 text-danger">
                <i class="fas fa-percent me-2"></i>Discounted Products
              </h2>
              <p class="text-muted mb-0">
                Limited time offers - Save big on these products
              </p>
            </div>
          </div>

          <!-- Discounted Products Grid -->
          <div v-if="discountedProducts.length > 0" class="row g-4">
            <div
              v-for="product in discountedProducts"
              :key="product.product_id"
              class="col-6 col-md-4 col-lg-3"
            >
              <div class="card product-card h-100 border-0 shadow-sm position-relative">
                <!-- Discount Badge -->
                <div class="position-absolute top-0 start-0 m-2">
                  <span class="badge bg-danger">
                    <i class="fas fa-fire me-1"></i>{{ product.discount_percent }}% OFF
                  </span>
                </div>

                <!-- Product Image -->
                <div class="product-image-container position-relative overflow-hidden bg-light rounded-top">
                  <img
                    :src="getProductImage(product.image)"
                    :alt="product.name"
                    class="product-img"
                    @error="handleImageError"
                    @click="goToProductPage(product)"
                  />

                  <!-- Action Icons Overlay -->
                  <div class="product-actions position-absolute top-0 end-0 m-2 d-flex flex-column gap-2">
                    <!-- Wishlist Icon -->
                    <button
                      @click.stop="toggleWishlist(product)"
                      class="btn btn-sm shadow-sm"
                      :class="isInWishlist(product) ? 'btn-danger' : 'btn-light'"
                      type="button"
                      :title="isInWishlist(product) ? 'Remove from wishlist' : 'Add to wishlist'"
                    >
                      <i class="fas fa-heart"></i>
                    </button>
                    
                    <!-- Cart Icon -->
                    <button
                      v-if="!isProductOwner(product) && product.stock > 0"
                      @click.stop="addToCart(product)"
                      class="btn btn-sm btn-danger shadow-sm"
                      type="button"
                      title="Add to cart"
                    >
                      <i class="fas fa-shopping-cart"></i>
                    </button>
                  </div>

                  <!-- Owner Badge -->
                  <div v-if="isProductOwner(product)" class="badge bg-secondary position-absolute top-0 end-0 m-2">
                    <i class="fas fa-store me-1"></i> Your Product
                  </div>

                  <!-- Stock Badge -->
                  <div v-else-if="product.stock <= 0" class="badge bg-danger position-absolute bottom-0 start-0 m-2">
                    <i class="fas fa-times-circle me-1"></i> Out of Stock
                  </div>
                  <div v-else-if="product.stock < 10" class="badge bg-warning position-absolute bottom-0 start-0 m-2">
                    <i class="fas fa-exclamation-triangle me-1"></i> {{ product.stock }} left
                  </div>
                </div>

                <!-- Product Info -->
                <div class="card-body d-flex flex-column p-3">
                  <!-- Discount Name -->
                  <div class="mb-2">
                    <span class="badge bg-danger-subtle text-danger border border-danger fw-normal">
                      <i class="fas fa-tag me-1"></i>{{ product.discount_name }}
                    </span>
                  </div>

                  <!-- Product Name -->
                  <h6 class="card-title fw-bold mb-2 text-truncate cursor-pointer" @click="goToProductPage(product)">
                    {{ product.name }}
                  </h6>

                  <!-- Price with Discount -->
                  <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <!-- Original Price (Strikethrough) -->
                      <span class="text-muted text-decoration-line-through small">
                        {{ formatPrice(product.price) }} Birr
                      </span>

                      <!-- Discounted Price -->
                      <span class="h5 fw-bold text-danger mb-0">
                        {{ formatPrice(product.discounted_price) }} Birr
                      </span>
                    </div>
                  </div>

                  <!-- Category -->
                  <div class="mb-3">
                    <span class="badge bg-light text-dark">
                      {{ getCategoryName(product.category_id) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State for Discounted Products -->
          <div v-else class="text-center py-5">
            <div class="empty-state">
              <div class="empty-icon mb-4">
                <i class="fas fa-percent fa-4x text-muted"></i>
              </div>
              <h3 class="h4 fw-bold mb-3">No Active Discounts</h3>
              <p class="text-muted mb-4">
                Check back later for special offers
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Toast Notification -->
      <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055">
        <div
          class="toast align-items-center"
          :class="[`text-bg-${notification.type}`, { show: notification.show }]"
          role="alert"
        >
          <div class="d-flex">
            <div class="toast-body d-flex align-items-center">
              <i :class="notification.icon" class="me-2"></i>
              {{ notification.message }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="hideNotification"></button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios' // Make sure axios is imported

const page = usePage()

// Refs
const heroImages = ref([
  'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80',
  'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-4.0.3&auto=format&fit=crop&w=2071&q=80',
  'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80',
])

const searchTerm = ref(page.props.search || '')
const selectedCategory = ref(page.props.category || '')
const sortBy = ref('newest')
const currentPage = ref(1)
const itemsPerPage = 12
const productsSection = ref(null)
const sortDropdown = ref(null)
const isDropdownOpen = ref(false)
const wishlistItems = ref([])
const cartItems = ref([])
const loading = ref(false)

const notification = ref({
  show: false,
  message: '',
  type: 'success',
  icon: 'fas fa-check-circle'
})

const stats = ref({
  products: 0,
  sellers: 0,
  orders: 0
})

// Get categories from Inertia props
const categories = computed(() => page.props.categories || [])

// Get products from Inertia props
const products = computed(() => page.props.products || [])

// Get auth user from Inertia props
const user = computed(() => page.props.auth?.user || null)

// Computed property for discounted products - REMOVED DISCOUNT LOGIC
const discountedProducts = computed(() => {
  // Return empty array since we don't have discount functionality
  return []
})

// Check if current user owns the product
const isProductOwner = (product) => {
  if (!user.value || !product || !product.user_id) return false
  return user.value.id === product.user_id
}

// Check if product is in wishlist
const isInWishlist = (product) => {
  if (!product || !product.product_id) return false
  return wishlistItems.value.some(item => 
    item.product?.product_id === product.product_id
  )
}

// Check if product is in cart
const isInCart = (product) => {
  if (!product || !product.product_id) return false
  return cartItems.value.some(item => 
    item.product?.product_id === product.product_id
  )
}

// Get category name from category_id
const getCategoryName = (categoryId) => {
  if (!categoryId) return 'Uncategorized'

  const category = categories.value.find(cat =>
    cat.category_id == categoryId || cat.id == categoryId
  )
  return category ? category.name : 'Uncategorized'
}

// Computed Properties
const filteredProducts = computed(() => {
  if (!Array.isArray(products.value)) return []

  let filtered = [...products.value]

  // Filter by category (using category_id)
  if (selectedCategory.value) {
    const selectedCat = categories.value.find(cat =>
      cat.name.toLowerCase() === selectedCategory.value.toLowerCase()
    )

    if (selectedCat) {
      filtered = filtered.filter(product =>
        product.category_id == selectedCat.category_id ||
        product.category_id == selectedCat.id
      )
    }
  }

  // Filter by search term
  if (searchTerm.value.trim()) {
    const term = searchTerm.value.toLowerCase().trim()
    filtered = filtered.filter(product => {
      const categoryName = getCategoryName(product.category_id).toLowerCase()

      return (
        product.name?.toLowerCase().includes(term) ||
        product.description?.toLowerCase().includes(term) ||
        product.reference?.toLowerCase().includes(term) ||
        categoryName.includes(term)
      )
    })
  }

  // Sort products
  switch (sortBy.value) {
    case 'price_low':
      filtered.sort((a, b) => {
        const priceA = parseFloat(a.price) || 0
        const priceB = parseFloat(b.price) || 0
        return priceA - priceB
      })
      break
    case 'price_high':
      filtered.sort((a, b) => {
        const priceA = parseFloat(a.price) || 0
        const priceB = parseFloat(b.price) || 0
        return priceB - priceA
      })
      break
    case 'newest':
    default:
      filtered.sort((a, b) => {
        const dateA = new Date(a.created_at || 0)
        const dateB = new Date(b.created_at || 0)
        return dateB - dateA
      })
  }

  return filtered
})

const totalPages = computed(() => {
  return Math.ceil(filteredProducts.value.length / itemsPerPage)
})

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredProducts.value.slice(start, end)
})

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, currentPage.value + 2)

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
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

const clearFilters = () => {
  searchTerm.value = ''
  selectedCategory.value = ''
  currentPage.value = 1
  window.location.href = '/'
}

const scrollToProducts = () => {
  if (productsSection.value) {
    productsSection.value.scrollIntoView({ behavior: 'smooth' })
  }
}

const handleSearch = () => {
  currentPage.value = 1
}

// Go to product page
const goToProductPage = (product) => {
  if (!product || !product.product_id) return
  window.location.href = `/product/${product.product_id}`
}

// Wishlist Methods
const toggleWishlist = async (product) => {
  if (!user.value) {
    showNotification('Please login to add to wishlist', 'warning')
    return
  }

  if (loading.value) return
  loading.value = true

  try {
    if (isInWishlist(product)) {
      // Remove from wishlist
      try {
        const response = await axios.delete(`/wishlist/${product.product_id}`)
        wishlistItems.value = wishlistItems.value.filter(item => 
          item.product?.product_id !== product.product_id
        )
        showNotification('Removed from wishlist', 'success')
        
        // Update wishlist count
        window.dispatchEvent(new CustomEvent('wishlist-updated', {
          detail: {
            wishlistCount: response.data.wishlistCount || 0
          }
        }))
      } catch (error) {
        // If old route fails, try to fetch wishlist items and remove by ID
        const wishlistResponse = await axios.get('/wishlist')
        const wishlistItem = wishlistResponse.data.wishlistItems?.find(
          item => item.product?.product_id === product.product_id
        )
        
        if (wishlistItem) {
          const response = await axios.delete(`/wishlist/${wishlistItem.id}`)
          wishlistItems.value = wishlistItems.value.filter(item => 
            item.product?.product_id !== product.product_id
          )
          showNotification('Removed from wishlist', 'success')
          
          // Update wishlist count
          window.dispatchEvent(new CustomEvent('wishlist-updated', {
            detail: {
              wishlistCount: response.data.wishlistCount || 0
            }
          }))
        }
      }
    } else {
      // Add to wishlist
      const response = await axios.post('/wishlist/add', {
        product_id: product.product_id
      })
      
      // Add to local wishlist items
      if (response.data.wishlistItem) {
        wishlistItems.value.push(response.data.wishlistItem)
      } else if (response.data.action === 'added') {
        wishlistItems.value.push({ product })
      }
      
      showNotification('Added to wishlist', 'success')
      
      // Update wishlist count
      window.dispatchEvent(new CustomEvent('wishlist-updated', {
        detail: {
          wishlistCount: response.data.wishlistCount || 0
        }
      }))
    }
  } catch (error) {
    console.error('Wishlist error:', error)
    if (error.response?.status === 422) {
      showNotification(error.response.data.message || 'Product already in wishlist', 'error')
    } else {
      showNotification(error.response?.data?.message || 'Something went wrong', 'error')
    }
  } finally {
    loading.value = false
  }
}

// Cart Methods - FIXED WITH PROPER ROUTES
const addToCart = async (product) => {
  if (!user.value) {
    showNotification('Please login to add to cart', 'warning')
    return
  }

  if (isProductOwner(product)) {
    showNotification('You cannot add your own product to cart', 'warning')
    return
  }

  if (product.stock <= 0) {
    showNotification('Product is out of stock', 'error')
    return
  }

  if (loading.value) return
  loading.value = true

  try {
    // Try the new store route first (/cart)
    const response = await axios.post('/cart', {
      product_id: product.product_id,
      quantity: 1
    })
    
    showNotification(response.data.message || 'Added to cart', 'success')
    
    // Update cart count
    window.dispatchEvent(new CustomEvent('cart-updated', {
      detail: {
        cartCount: response.data.cartCount || 0
      }
    }))
  } catch (error) {
    console.error('Cart error (store route):', error)
    
    // If store route fails, try the old add route (/cart/add)
    if (error.response?.status === 404) {
      try {
        console.log('Trying /cart/add route...')
        const fallbackResponse = await axios.post('/cart/add', {
          product_id: product.product_id,
          quantity: 1
        })
        
        showNotification(fallbackResponse.data.message || 'Added to cart', 'success')
        
        // Update cart count
        window.dispatchEvent(new CustomEvent('cart-updated', {
          detail: {
            cartCount: fallbackResponse.data.cartCount || 0
          }
        }))
      } catch (addError) {
        console.error('Cart error (add route):', addError)
        if (addError.response?.status === 422) {
          if (addError.response.data.message.includes('stock')) {
            showNotification('Not enough stock available', 'error')
          } else {
            showNotification('Product already in cart', 'error')
          }
        } else {
          showNotification('Something went wrong with cart', 'error')
        }
      }
    } else if (error.response?.status === 422) {
      if (error.response.data.message.includes('stock')) {
        showNotification('Not enough stock available', 'error')
      } else {
        showNotification('Product already in cart', 'error')
      }
    } else {
      showNotification('Something went wrong with cart', 'error')
    }
  } finally {
    loading.value = false
  }
}

// Pagination Methods
const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    scrollToProducts()
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    scrollToProducts()
  }
}

const goToPage = (page) => {
  currentPage.value = page
  scrollToProducts()
}

// Sort method
const setSort = (type) => {
  sortBy.value = type
  currentPage.value = 1
  isDropdownOpen.value = false // Close dropdown when item is selected
}

// Toggle dropdown
const toggleDropdown = (event) => {
  event.stopPropagation()
  isDropdownOpen.value = !isDropdownOpen.value
}

// Notification
const showNotification = (message, type = 'success', icon = null) => {
  const icons = {
    success: 'fas fa-check-circle',
    warning: 'fas fa-exclamation-triangle',
    error: 'fas fa-times-circle',
    info: 'fas fa-info-circle'
  }

  notification.value = {
    show: true,
    message,
    type,
    icon: icon || icons[type] || icons.success
  }

  setTimeout(() => {
    notification.value.show = false
  }, 3000)
}

const hideNotification = () => {
  notification.value.show = false
}

// Load wishlist and cart data
const loadWishlistData = async () => {
  if (!user.value) return
  
  try {
    const response = await axios.get('/wishlist')
    wishlistItems.value = response.data.wishlistItems || []
  } catch (error) {
    console.error('Error loading wishlist:', error)
  }
}

const loadCartData = async () => {
  if (!user.value) return
  
  try {
    const response = await axios.get('/cart')
    cartItems.value = response.data.cartItems || []
  } catch (error) {
    console.error('Error loading cart:', error)
  }
}

const loadCartCount = async () => {
  if (!user.value) return
  
  try {
    const response = await axios.get('/api/cart/count')
    // Dispatch event to update navbar
    window.dispatchEvent(new CustomEvent('cart-updated', {
      detail: {
        cartCount: response.data.count || 0
      }
    }))
  } catch (error) {
    console.error('Error loading cart count:', error)
  }
}

const loadWishlistCount = async () => {
  if (!user.value) return
  
  try {
    const response = await axios.get('/api/wishlist/count')
    // Dispatch event to update navbar
    window.dispatchEvent(new CustomEvent('wishlist-updated', {
      detail: {
        wishlistCount: response.data.count || 0
      }
    }))
  } catch (error) {
    console.error('Error loading wishlist count:', error)
  }
}

// Initialize stats
onMounted(() => {
  stats.value = {
    products: products.value.length || 0,
    sellers: new Set(products.value.map(p => p.user_id)).size,
    orders: Math.floor(products.value.length * 0.5)
  }

  // Initialize Bootstrap carousel
  if (window.bootstrap) {
    const carouselElement = document.getElementById('heroCarousel')
    if (carouselElement) {
      new window.bootstrap.Carousel(carouselElement, {
        interval: 5000,
        ride: 'carousel'
      })
    }
  }

  // Click outside to close dropdown
  document.addEventListener('click', (event) => {
    if (sortDropdown.value && !sortDropdown.value.contains(event.target)) {
      isDropdownOpen.value = false
    }
  })

  // Listen for navbar events
  window.addEventListener('navbar-search', handleNavbarSearch)
  window.addEventListener('navbar-category-select', handleNavbarCategorySelect)

  // Load user data if logged in
  if (user.value) {
    loadWishlistData()
    loadCartData()
    loadCartCount()
    loadWishlistCount()
  }
})

onUnmounted(() => {
  window.removeEventListener('navbar-search', handleNavbarSearch)
  window.removeEventListener('navbar-category-select', handleNavbarCategorySelect)
})

const handleNavbarSearch = (event) => {
  searchTerm.value = event.detail.searchTerm
  selectedCategory.value = ''
  currentPage.value = 1
  scrollToProducts()
}

const handleNavbarCategorySelect = (event) => {
  selectedCategory.value = event.detail.categoryName
  searchTerm.value = ''
  currentPage.value = 1
  scrollToProducts()
}
</script>
<style scoped>
.animated-background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
  overflow: hidden;
}

.shape {
  position: absolute;
  border-radius: 50%;
  opacity: 0.1;
  animation: float 20s infinite linear;
}

.shape-1 {
  width: 300px;
  height: 300px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  top: 10%;
  left: 5%;
  animation-delay: 0s;
}

.shape-2 {
  width: 200px;
  height: 200px;
  background: linear-gradient(135deg, #10b981, #059669);
  top: 60%;
  right: 10%;
  animation-delay: 5s;
}

.shape-3 {
  width: 150px;
  height: 150px;
  background: linear-gradient(135deg, #f59e0b, #d97706);
  bottom: 20%;
  left: 15%;
  animation-delay: 10s;
}

.shape-4 {
  width: 250px;
  height: 250px;
  background: linear-gradient(135deg, #ef4444, #dc2626);
  top: 30%;
  right: 20%;
  animation-delay: 15s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
  }
  25% {
    transform: translateY(-20px) rotate(90deg);
  }
  50% {
    transform: translateY(0) rotate(180deg);
  }
  75% {
    transform: translateY(20px) rotate(270deg);
  }
}

/* Background Patterns */
.pattern-dots {
  position: absolute;
  width: 100%;
  height: 100%;
  background-image: radial-gradient(rgba(102, 126, 234, 0.1) 1px, transparent 1px);
  background-size: 30px 30px;
  opacity: 0.5;
}

.pattern-gradient {
  position: absolute;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg,
    rgba(102, 126, 234, 0.05) 0%,
    rgba(118, 75, 162, 0.05) 50%,
    transparent 100%);
}

/* Text Gradient */
.text-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Hero Slider */
.hero-slider {
  position: relative;
}

.carousel-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom,
    rgba(0, 0, 0, 0.1) 0%,
    rgba(0, 0, 0, 0.3) 100%);
  border-radius: 1rem;
}

/* Product Cards */
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

/* Product Actions */
.product-actions {
  z-index: 2;
}

.product-actions .btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  transition: all 0.3s ease;
}

.product-actions .btn:hover {
  transform: scale(1.1);
}

.product-actions .btn-danger {
  background-color: #dc3545;
  border-color: #dc3545;
}

.product-actions .btn-light {
  background-color: rgba(255, 255, 255, 0.9);
  border-color: rgba(0, 0, 0, 0.1);
}

.product-actions .btn-light:hover {
  background-color: #dc3545;
  border-color: #dc3545;
  color: white;
}

.product-actions .btn-primary {
  background-color: #667eea;
  border-color: #667eea;
}

.product-actions .btn-primary:hover {
  background-color: #5a6fd8;
  border-color: #5a6fd8;
}

/* Cursor pointer for product name */
.cursor-pointer {
  cursor: pointer;
}

.cursor-pointer:hover {
  color: #667eea;
}

/* Pagination */
.pagination .page-item.active .page-link {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-color: #667eea;
}

.pagination .page-link {
  border: none;
  margin: 0 2px;
  border-radius: 8px;
  color: #667eea;
}

.pagination .page-link:hover {
  background: rgba(102, 126, 234, 0.1);
}

/* Toast */
.toast {
  border-radius: 10px;
  border: none;
}

/* Dropdown Fixes */
.dropdown-item.active {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%) !important;
  color: #667eea !important;
}

/* NEW: Discounted Products Section Styles */
.discounted-products-section {
  background: linear-gradient(135deg,
    rgba(220, 53, 69, 0.03) 0%,
    rgba(220, 53, 69, 0.01) 100%);
  border-top: 1px solid rgba(220, 53, 69, 0.1);
  border-bottom: 1px solid rgba(220, 53, 69, 0.1);
}

/* Discount specific card styles */
.discounted-products-section .product-card {
  border: 1px solid rgba(220, 53, 69, 0.2);
}

.discounted-products-section .product-card:hover {
  border-color: rgba(220, 53, 69, 0.4);
  box-shadow: 0 15px 35px rgba(220, 53, 69, 0.1) !important;
}

/* Discounted products cart button */
.discounted-products-section .product-actions .btn-primary {
  background-color: #dc3545;
  border-color: #dc3545;
}

.discounted-products-section .product-actions .btn-primary:hover {
  background-color: #c82333;
  border-color: #bd2130;
}

/* Price styles for discounted products */
.text-decoration-line-through {
  text-decoration-thickness: 2px;
}

/* Badge styles */
.bg-danger-subtle {
  background-color: rgba(220, 53, 69, 0.1) !important;
}

.bg-success-subtle {
  background-color: rgba(25, 135, 84, 0.1) !important;
}

/* Pure CSS Dropdown Styles */
.sort-dropdown-container {
  position: relative;
  display: inline-block;
}

.sort-dropdown-container .dropdown {
  position: relative;
}

.sort-dropdown-container .dropdown-toggle {
  position: relative;
  z-index: 1001;
}

.sort-dropdown-container .dropdown-toggle::after {
  display: inline-block;
  margin-left: 0.255em;
  vertical-align: 0.255em;
  content: "";
  border-top: 0.3em solid;
  border-right: 0.3em solid transparent;
  border-bottom: 0;
  border-left: 0.3em solid transparent;
  transition: transform 0.2s ease;
}

.sort-dropdown-container .dropdown-toggle.show::after {
  transform: rotate(180deg);
}

.sort-dropdown-container .dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 1000;
  display: none;
  min-width: 10rem;
  padding: 0.5rem 0;
  margin: 0.125rem 0 0;
  font-size: 1rem;
  color: #212529;
  text-align: left;
  list-style: none;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 0.375rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.sort-dropdown-container .dropdown-menu.show {
  display: block;
  animation: dropdownFadeIn 0.2s ease;
}

.sort-dropdown-container .dropdown-item {
  display: block;
  width: 100%;
  padding: 0.375rem 1rem;
  clear: both;
  font-weight: 400;
  color: #212529;
  text-align: inherit;
  text-decoration: none;
  white-space: nowrap;
  background-color: transparent;
  border: 0;
  cursor: pointer;
  transition: background-color 0.15s ease, color 0.15s ease;
}

.sort-dropdown-container .dropdown-item:hover {
  background-color: #f8f9fa;
  color: #16181b;
}

.sort-dropdown-container .dropdown-item.active {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  color: #667eea;
  font-weight: 500;
}

.sort-dropdown-container .dropdown-item:focus {
  outline: 2px solid rgba(102, 126, 234, 0.5);
  outline-offset: -2px;
}

/* Dropdown animation */
@keyframes dropdownFadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Ensure dropdown doesn't interfere with other Bootstrap components */
.sort-dropdown-container .dropdown-menu {
  z-index: 1060;
}

/* Mobile responsive */
@media (max-width: 768px) {
  .sort-dropdown-container .dropdown-menu {
    position: fixed;
    left: 50%;
    transform: translateX(-50%);
    min-width: 200px;
  }
  
  .product-actions .btn {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-section {
    text-align: center;
  }

  .display-4 {
    font-size: 2.5rem;
  }

  .product-image-container {
    height: 150px;
  }
}

@media (max-width: 576px) {
  .display-4 {
    font-size: 2rem;
  }

  .product-image-container {
    height: 120px;
  }
  
  .product-actions .btn {
    width: 28px;
    height: 28px;
    font-size: 0.75rem;
  }
}
</style>