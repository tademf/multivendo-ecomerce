<template>
  <AppLayout>
    <!-- Premium E-commerce Homepage -->
    <div class="premium-ecommerce">
      <!-- Hero Section with Background Images -->
      <section class="fullscreen-hero position-relative overflow-hidden">
        <!-- Fullscreen Hero Slider -->
        <div class="hero-slider-fullscreen">
          <div id="fullscreenCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <div
                v-for="(image, index) in heroImages"
                :key="index"
                class="carousel-item"
                :class="{ active: index === 0 }"
              >
                <!-- Background Image Container -->
                <div class="hero-background">
                  <!-- Background Image -->
                  <div 
                    class="bg-image"
                    :style="{ 
                      backgroundImage: `url('${image}')`,
                      backgroundSize: 'cover',
                      backgroundPosition: 'center',
                      backgroundRepeat: 'no-repeat'
                    }"
                  ></div>
                  
                  <!-- Dark Overlay with Blur -->
                  <div class="hero-overlay-dark"></div>
                </div>
              </div>
            </div>
            
            <!-- Hero Content - Outside carousel items -->
            <div class="hero-content position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center">
              <div class="container">
                <div class="row justify-content-center">
                  <div class="col-lg-8 col-xl-7 text-center text-lg-start">
                    <!-- Premium Badge -->
                    <!-- <div class="premium-badge mb-4">
                      <span class="badge-premium">
                        <i class="fas fa-gem me-2"></i>Premium Collection
                      </span>
                    </div> -->
                    
                    <!-- Main Heading -->
                    <h1 class="display-1 fw-bold mb-4 text-light">
                      Discover <span class="text-gradient-gold">Luxury</span><br>
                      <span class="text-primary">Shopping</span>
                    </h1>
                    
                    <!-- Description -->
                    <p class="lead fs-4 mb-5 text-light">
                      Curated selection of premium products from top brands worldwide. 
                      Experience shopping redefined.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="hero-buttons d-flex flex-wrap gap-4 justify-content-center justify-content-lg-start">
                      <button
                        @click="scrollToProducts"
                        class="btn-premium btn-gold px-5 py-3"
                        type="button"
                      >
                        <i class="fas fa-shopping-bag me-2"></i>
                        Shop Collection
                      </button>
                      
                      <button
                        @click="scrollToDiscounted"
                        class="btn-premium btn-outline-light px-5 py-3"
                        type="button"
                      >
                        <i class="fas fa-fire me-2"></i>
                        View Offers
                      </button>
                    </div>
                    
                    <!-- Hero Stats - Removed numbers, simplified -->
                    <!-- <div class="hero-stats mt-5 pt-4">
                      <div class="row g-4">
                        <div class="col-4 col-md-3">
                          <div class="stat-card">
                            <h3 class="stat-number text-gold fw-bold">Premium</h3>
                            <p class="stat-label text-light mb-0">Products</p>
                          </div>
                        </div>
                        <div class="col-4 col-md-3">
                          <div class="stat-card">
                            <h3 class="stat-number text-gold fw-bold">Trusted</h3>
                            <p class="stat-label text-light mb-0">Sellers</p>
                          </div>
                        </div>
                        <div class="col-4 col-md-3">
                          <div class="stat-card">
                            <h3 class="stat-number text-gold fw-bold">Reliable</h3>
                            <p class="stat-label text-light mb-0">Delivery</p>
                          </div>
                        </div>
                      </div>
                    </div> -->
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#fullscreenCarousel" data-bs-slide="prev">
              <span class="carousel-control-icon">
                <i class="fas fa-chevron-left"></i>
              </span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#fullscreenCarousel" data-bs-slide="next">
              <span class="carousel-control-icon">
                <i class="fas fa-chevron-right"></i>
              </span>
            </button>
            
            <!-- Carousel Indicators -->
            <div class="carousel-indicators-custom">
              <div class="indicators-container">
                <button 
                  v-for="(image, index) in heroImages" 
                  :key="index"
                  :class="{ active: index === 0 }"
                  :data-bs-target="'#fullscreenCarousel'"
                  :data-bs-slide-to="index"
                  class="indicator-btn"
                >
                  <span class="indicator-progress"></span>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Scroll Down Indicator -->
        <div class="scroll-down-indicator">
          <a href="#recently-viewed" @click.prevent="scrollToRecentlyViewed">
            <i class="fas fa-chevron-down"></i>
          </a>
        </div>
      </section>

      <!-- Recently Viewed Section -->
      <section id="recently-viewed" class="recently-viewed-section py-5" ref="recentlyViewedSection">
        <div class="container">
          <div class="section-header mb-5">
            <div class="row align-items-center">
              <div class="col-md-8">
                <div class="d-flex align-items-center">
                  <div class="section-icon-wrapper me-3">
                    <div class="section-icon">
                      <i class="fas fa-history"></i>
                    </div>
                  </div>
                  <div>
                    <h2 class="section-title mb-2">
                      Recently <span class="text-gradient-primary">Viewed</span>
                    </h2>
                    <p class="section-subtitle text-muted mb-0">
                      Continue where you left off
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex justify-content-md-end align-items-center gap-3">
                  <!-- Navigation Buttons -->
                  <div class="navigation-buttons">
                    <button 
                      @click="scrollRecentlyViewed('left')" 
                      class="btn-navigation btn-nav-prev"
                      :disabled="recentlyViewedScrollPosition <= 0"
                      type="button"
                    >
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button 
                      @click="scrollRecentlyViewed('right')" 
                      class="btn-navigation btn-nav-next"
                      :disabled="recentlyViewedScrollPosition >= recentlyViewedMaxScroll"
                      type="button"
                    >
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                  
                  <!-- Clear Button -->
                  <button
                    @click="clearRecentlyViewed"
                    class="btn-clear"
                    type="button"
                    title="Clear history"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Recently Viewed Products -->
          <div v-if="recentlyViewedProducts.length > 0" class="position-relative">
            <div 
              class="recently-viewed-container"
              ref="recentlyViewedContainer"
              @scroll="updateRecentlyViewedScrollPosition"
            >
              <div class="recently-viewed-items">
                <div
                  v-for="product in recentlyViewedProducts"
                  :key="product.product_id"
                  class="recently-viewed-card"
                >
                  <div class="card product-card premium-hover">
                    <!-- Product Image -->
                    <div class="product-image-container">
                      <div class="image-wrapper">
                        <img
                          :src="getProductImage(product.image)"
                          :alt="product.name"
                          class="product-img"
                          @error="handleImageError"
                          @click="goToProductPage(product)"
                        />
                        
                        <!-- Quick Actions Overlay -->
                        <div class="quick-actions">
                          <button
                            @click.stop="toggleWishlist(product)"
                            class="btn-action btn-wishlist"
                            :class="{ active: isInWishlist(product) }"
                            type="button"
                          >
                            <i class="fas fa-heart"></i>
                          </button>
                          <button
                            v-if="!isProductOwner(product) && product.stock > 0"
                            @click.stop="addToCart(product)"
                            class="btn-action btn-cart"
                            type="button"
                          >
                            <i class="fas fa-shopping-cart"></i>
                          </button>
                        </div>
                        
                        <!-- Status Badges -->
                        <div class="status-badges">
                          <span v-if="isProductOwner(product)" class="badge owner-badge">
                            <i class="fas fa-crown"></i> Yours
                          </span>
                          <span v-else-if="product.stock <= 0" class="badge out-of-stock-badge">
                            <i class="fas fa-times"></i> Sold Out
                          </span>
                          <span v-else-if="product.stock < 10" class="badge low-stock-badge">
                            <i class="fas fa-bolt"></i> {{ product.stock }} Left
                          </span>
                        </div>
                        
                        <!-- View Time -->
                        <div class="view-time">
                          <span class="time-badge">
                            <i class="fas fa-clock"></i>
                            {{ getTimeAgo(product.viewed_at) }}
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Product Info -->
                    <div class="card-body">
                      <h6 class="product-title" @click="goToProductPage(product)">
                        {{ product.name }}
                      </h6>
                      <div class="product-price">
                        <span class="price-current">{{ formatPrice(product.price) }} ETB</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Scroll Progress -->
            <div class="scroll-progress mt-4">
              <div class="progress-track">
                <div 
                  class="progress-bar" 
                  :style="{ width: recentlyViewedProgress + '%' }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="empty-state text-center py-5">
            <div class="empty-icon mb-4">
              <i class="fas fa-eye-slash fa-4x text-muted"></i>
            </div>
            <h3 class="h4 fw-bold mb-3">No Recent Views</h3>
            <p class="text-muted mb-4">
              Products you view will appear here
            </p>
            <button @click="scrollToProducts" class="btn-premium btn-primary">
              <i class="fas fa-store me-2"></i>Browse Products
            </button>
          </div>
        </div>
      </section>

      <!-- Discounted Products Section -->
      <section id="discounted-products" class="discounted-products-section py-5" ref="discountedSection">
        <div class="container">
          <div class="section-header mb-5 text-center">
            <div class="section-badge mb-3">
              <span class="badge-hot">
                <i class="fas fa-fire"></i> HOT DEALS
              </span>
            </div>
            <h2 class="section-title mb-3">
              Limited Time <span class="text-gradient-fire">Offers</span>
            </h2>
            <p class="section-subtitle text-muted">
              Don't miss out on these exclusive discounts
            </p>
          </div>

          <!-- Discounted Products Grid -->
          <div v-if="discountedProducts.length > 0" class="row g-4">
            <div
              v-for="product in discountedProducts"
              :key="product.product_id"
              class="col-6 col-md-4 col-lg-3"
            >
              <div class="card product-card discount-card">
                <!-- Discount Badge -->
                <div class="discount-ribbon">
                  <span class="ribbon-text">
                    -{{ product.discount_percent }}%
                  </span>
                </div>

                <!-- Product Image -->
                <div class="product-image-container">
                  <img
                    :src="getProductImage(product.image)"
                    :alt="product.name"
                    class="product-img"
                    @error="handleImageError"
                    @click="goToProductPage(product)"
                  />
                  
                  <!-- Quick Actions -->
                  <div class="quick-actions">
                    <button
                      @click.stop="toggleWishlist(product)"
                      class="btn-action btn-wishlist"
                      :class="{ active: isInWishlist(product) }"
                      type="button"
                    >
                      <i class="fas fa-heart"></i>
                    </button>
                    <button
                      v-if="!isProductOwner(product) && product.stock > 0"
                      @click.stop="addToCart(product)"
                      class="btn-action btn-cart"
                      type="button"
                    >
                      <i class="fas fa-shopping-cart"></i>
                    </button>
                  </div>
                </div>

                <!-- Product Info -->
                <div class="card-body">
                  <div class="discount-name mb-2">
                    <span class="badge-discount">
                      {{ product.discount_name }}
                    </span>
                  </div>
                  <h6 class="product-title" @click="goToProductPage(product)">
                    {{ product.name }}
                  </h6>
                  <div class="price-container">
                    <span class="price-original">{{ formatPrice(product.price) }} ETB</span>
                    <span class="price-discounted">{{ formatPrice(product.discounted_price) }} ETB</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-5">
            <div class="empty-icon mb-4">
              <i class="fas fa-percent fa-4x text-muted"></i>
            </div>
            <h3 class="h4 fw-bold mb-3">No Active Discounts</h3>
            <p class="text-muted mb-4">
              Check back soon for exclusive offers
            </p>
          </div>
        </div>
      </section>

      <!-- All Products Section -->
      <section id="all-products" class="products-section py-5" ref="productsSection">
        <div class="container">
          <div class="section-header mb-5">
            <div class="row align-items-center">
              <div class="col-md-8">
                <h2 class="section-title mb-2">
                  {{ selectedCategory ? selectedCategory : 'All Products' }}
                  <span v-if="searchTerm" class="text-primary">: "{{ searchTerm }}"</span>
                </h2>
                <!-- <p class="section-subtitle text-muted mb-0">
                  {{ filteredProducts.length }} premium products available
                </p> -->
              </div>
              <div class="col-md-4">
                <div class="sorting-tools">
                  <div class="sort-dropdown" ref="sortDropdown">
                    <button class="btn-sort" @click="toggleDropdown">
                      <i class="fas fa-sort me-2"></i>
                      <span>{{ sortOptions.find(opt => opt.value === sortBy)?.label }}</span>
                      <i class="fas fa-chevron-down ms-2 dropdown-arrow"></i>
                    </button>
                    <div class="dropdown-menu" :class="{ show: isDropdownOpen }">
                      <a 
                        v-for="option in sortOptions" 
                        :key="option.value"
                        href="#" 
                        class="dropdown-item"
                        :class="{ active: sortBy === option.value }"
                        @click.prevent="setSort(option.value)"
                      >
                        <i :class="option.icon" class="me-2"></i>
                        {{ option.label }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Products Grid -->
          <div v-if="filteredProducts.length > 0" class="row g-4">
            <div
              v-for="product in paginatedProducts"
              :key="product.product_id"
              class="col-6 col-md-4 col-lg-3"
            >
              <div class="card product-card product-card-grid">
                <!-- Product Image -->
                <div class="product-image-container">
                  <img
                    :src="getProductImage(product.image)"
                    :alt="product.name"
                    class="product-img"
                    @error="handleImageError"
                    @click="goToProductPage(product)"
                  />
                  
                  <!-- Quick Actions -->
                  <div class="quick-actions">
                    <button
                      @click.stop="toggleWishlist(product)"
                      class="btn-action btn-wishlist"
                      :class="{ active: isInWishlist(product) }"
                      type="button"
                    >
                      <i class="fas fa-heart"></i>
                    </button>
                    <button
                      v-if="!isProductOwner(product) && product.stock > 0"
                      @click.stop="addToCart(product)"
                      class="btn-action btn-cart"
                      type="button"
                    >
                      <i class="fas fa-shopping-cart"></i>
                    </button>
                  </div>
                  
                  <!-- Status Badges -->
                  <div class="status-badges">
                    <span v-if="isProductOwner(product)" class="badge owner-badge">
                      <i class="fas fa-crown"></i> Yours
                    </span>
                    <span v-else-if="product.stock <= 0" class="badge out-of-stock-badge">
                      <i class="fas fa-times"></i> Sold Out
                    </span>
                    <!-- <span v-else-if="product.stock < 10" class="badge low-stock-badge">
                      <i class="fas fa-bolt"></i> Low Stock
                    </span> -->
                    <span v-else-if="product.stock < 10" class="badge low-stock-badge">
                            <i class="fas fa-bolt"></i> {{ product.stock }} Left
                          </span>
                  </div>
                </div>

                <!-- Product Info -->
                <div class="card-body">
                  <!-- <div class="category-badge mb-2">
                    <span class="badge-category">
                      {{ getCategoryName(product.category_id) }}
                    </span>
                  </div> -->
                  <h6 class="product-title" @click="goToProductPage(product)">
                    {{ product.name }}
                  </h6>
                  <!-- <div class="product-price">
                    <span class="price-current">{{ formatPrice(product.price) }} Birr</span>
                  </div> -->
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-5">
            <div class="empty-icon mb-4">
              <i class="fas fa-search fa-4x text-muted"></i>
            </div>
            <h3 class="h4 fw-bold mb-3">No Products Found</h3>
            <p class="text-muted mb-4">
              {{ searchTerm ? `No results for "${searchTerm}"` :
                 selectedCategory ? `No products in "${selectedCategory}"` :
                 'No products available' }}
            </p>
            <button @click="clearFilters" class="btn-premium btn-primary">
              <i class="fas fa-redo me-2"></i>Show All Products
            </button>
          </div>

          <!-- Pagination -->
          <div v-if="filteredProducts.length > 0 && totalPages > 1" class="mt-5">
            <nav aria-label="Product pagination">
              <ul class="pagination-premium">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                  <button class="page-link" @click="prevPage">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                </li>
                
                <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: page === currentPage }">
                  <button class="page-link" @click="goToPage(page)">
                    {{ page }}
                  </button>
                </li>
                
                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                  <button class="page-link" @click="nextPage">
                    <i class="fas fa-chevron-right"></i>
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </section>

      <!-- Notification Toast -->
      <div class="toast-notification" :class="{ show: notification.show }">
        <div class="toast-content" :class="notification.type">
          <div class="toast-icon">
            <i :class="notification.icon"></i>
          </div>
          <div class="toast-message">
            {{ notification.message }}
          </div>
          <button @click="hideNotification" class="toast-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const page = usePage()

// Refs
const heroImages = ref([
  'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1600&h=700&fit=crop&q=80',
  'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=1600&h=700&fit=crop&q=80',
  'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1600&h=700&fit=crop&q=80'
])

const searchTerm = ref(page.props.search || '')
const selectedCategory = ref(page.props.category || '')
const sortBy = ref('newest')
const currentPage = ref(1)
const itemsPerPage = 12
const productsSection = ref(null)
const recentlyViewedSection = ref(null)
const discountedSection = ref(null)
const sortDropdown = ref(null)
const isDropdownOpen = ref(false)
const wishlistItems = ref([])
const cartItems = ref([])
const loading = ref(false)
const recentlyViewedProducts = ref([])
const recentlyViewedContainer = ref(null)
const recentlyViewedScrollPosition = ref(0)
const recentlyViewedMaxScroll = ref(0)

const sortOptions = [
  { value: 'newest', label: 'Newest First', icon: 'fas fa-clock' },
  { value: 'price_low', label: 'Price: Low to High', icon: 'fas fa-arrow-up' },
  { value: 'price_high', label: 'Price: High to Low', icon: 'fas fa-arrow-down' },
  { value: 'popular', label: 'Most Popular', icon: 'fas fa-fire' }
]

const notification = ref({
  show: false,
  message: '',
  type: 'success',
  icon: 'fas fa-check-circle'
})

// Computed
const categories = computed(() => page.props.categories || [])
const products = computed(() => page.props.products || [])
const discountedProducts = computed(() => {
  if (page.props.discounted_products && Array.isArray(page.props.discounted_products)) {
    return page.props.discounted_products
  }
  
  if (!Array.isArray(products.value)) return []
  
  return products.value.filter(product => {
    return product.discount_percent && product.discount_status === 'active'
  })
})

const user = computed(() => page.props.auth?.user || null)

const filteredProducts = computed(() => {
  if (!Array.isArray(products.value)) return []

  let filtered = [...products.value]

  // Filter by category
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
      filtered.sort((a, b) => (parseFloat(a.price) || 0) - (parseFloat(b.price) || 0))
      break
    case 'price_high':
      filtered.sort((a, b) => (parseFloat(b.price) || 0) - (parseFloat(a.price) || 0))
      break
    case 'popular':
      filtered.sort((a, b) => (b.views || 0) - (a.views || 0))
      break
    case 'newest':
    default:
      filtered.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))
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

const recentlyViewedProgress = computed(() => {
  if (!recentlyViewedContainer.value || recentlyViewedMaxScroll.value === 0) return 0
  return (recentlyViewedScrollPosition.value / recentlyViewedMaxScroll.value) * 100
})

// Methods
const getProductImage = (imagePath) => {
  if (!imagePath) return 'https://placehold.co/600x400/f8fafc/1e293b?text=PREMIUM+PRODUCT'
  
  if (imagePath.startsWith('http') || imagePath.startsWith('/')) {
    return imagePath
  }
  
  return `/storage/${imagePath}`
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/600x400/f8fafc/1e293b?text=PREMIUM+PRODUCT'
}

const isProductOwner = (product) => {
  if (!user.value || !product || !product.user_id) return false
  return user.value.id === product.user_id
}

const isInWishlist = (product) => {
  if (!product || !product.product_id) return false
  return wishlistItems.value.some(item => 
    item.product?.product_id === product.product_id
  )
}

const getCategoryName = (categoryId) => {
  if (!categoryId) return 'Uncategorized'
  const category = categories.value.find(cat =>
    cat.category_id == categoryId || cat.id == categoryId
  )
  return category ? category.name : 'Uncategorized'
}

const getTimeAgo = (timestamp) => {
  if (!timestamp) return 'recently'
  
  const now = new Date()
  const viewedDate = new Date(timestamp)
  const diffInSeconds = Math.floor((now - viewedDate) / 1000)
  
  if (diffInSeconds < 60) return 'just now'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`
  if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`
  return `${Math.floor(diffInSeconds / 604800)}w ago`
}

const formatPrice = (price) => {
  const num = parseFloat(price) || 0
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(num)
}

const clearFilters = () => {
  searchTerm.value = ''
  selectedCategory.value = ''
  currentPage.value = 1
  window.location.href = '/'
}

const scrollToProducts = () => {
  if (productsSection.value) {
    productsSection.value.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

const scrollToRecentlyViewed = () => {
  if (recentlyViewedSection.value) {
    recentlyViewedSection.value.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

const scrollToDiscounted = () => {
  if (discountedSection.value) {
    discountedSection.value.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

const clearRecentlyViewed = () => {
  if (confirm('Are you sure you want to clear your recently viewed products?')) {
    localStorage.removeItem('recentlyViewed')
    recentlyViewedProducts.value = []
    showNotification('Recently viewed history cleared', 'success')
  }
}

const scrollRecentlyViewed = (direction) => {
  if (!recentlyViewedContainer.value) return
  
  const container = recentlyViewedContainer.value
  const scrollAmount = container.clientWidth * 0.8
  
  if (direction === 'left') {
    container.scrollBy({ left: -scrollAmount, behavior: 'smooth' })
  } else {
    container.scrollBy({ left: scrollAmount, behavior: 'smooth' })
  }
}

const updateRecentlyViewedScrollPosition = () => {
  if (!recentlyViewedContainer.value) return
  
  const container = recentlyViewedContainer.value
  recentlyViewedScrollPosition.value = container.scrollLeft
  recentlyViewedMaxScroll.value = container.scrollWidth - container.clientWidth
}

const cleanupOldRecentViews = () => {
  try {
    const storedViews = localStorage.getItem('recentlyViewed')
    if (!storedViews) return
    
    const views = JSON.parse(storedViews)
    const now = new Date()
    const thirtyDaysAgo = new Date(now.getTime() - (30 * 24 * 60 * 60 * 1000))
    
    const freshViews = views.filter(view => {
      if (!view.viewed_at) return false
      const viewedDate = new Date(view.viewed_at)
      return viewedDate > thirtyDaysAgo
    })
    
    const limitedViews = freshViews.slice(0, 10)
    localStorage.setItem('recentlyViewed', JSON.stringify(limitedViews))
    recentlyViewedProducts.value = limitedViews
  } catch (error) {
    console.error('Error cleaning up recently viewed:', error)
  }
}

const loadRecentlyViewedProducts = async () => {
  cleanupOldRecentViews()
  
  const storedViews = localStorage.getItem('recentlyViewed')
  if (storedViews) {
    try {
      const parsedViews = JSON.parse(storedViews)
      const validProducts = parsedViews
        .filter(view => view && view.product_id && view.name)
        .slice(0, 10)
      recentlyViewedProducts.value = validProducts
      return
    } catch (error) {
      console.error('Error parsing localStorage recently viewed:', error)
    }
  }
  
  if (user.value) {
    try {
      const response = await axios.get('/api/recently-viewed')
      if (response.data && Array.isArray(response.data)) {
        recentlyViewedProducts.value = response.data.slice(0, 10)
        localStorage.setItem('recentlyViewed', JSON.stringify(response.data))
      }
    } catch (error) {
      console.error('Error loading recently viewed:', error)
    }
  }
}

const saveProductView = (product) => {
  if (!product || !product.product_id) return
  
  try {
    const storedViews = localStorage.getItem('recentlyViewed')
    let views = storedViews ? JSON.parse(storedViews) : []
    
    views = views.filter(view => view.product_id !== product.product_id)
    
    const productWithTimestamp = {
      ...product,
      viewed_at: new Date().toISOString()
    }
    
    views.unshift(productWithTimestamp)
    views = views.slice(0, 10)
    
    localStorage.setItem('recentlyViewed', JSON.stringify(views))
    recentlyViewedProducts.value = views
    
    if (user.value) {
      axios.post('/api/save-recent-view', {
        product_id: product.product_id
      }).catch(error => {
        console.error('Error saving to server:', error)
      })
    }
  } catch (error) {
    console.error('Error saving product view:', error)
  }
}

const goToProductPage = (product) => {
  if (!product || !product.product_id) return
  
  saveProductView(product)
  window.location.href = `/product/${product.product_id}`
}

const toggleWishlist = async (product) => {
  if (!user.value) {
    showNotification('Please login to add to wishlist', 'warning')
    return
  }

  if (loading.value) return
  loading.value = true

  try {
    if (isInWishlist(product)) {
      const response = await axios.delete(`/wishlist/${product.product_id}`)
      wishlistItems.value = wishlistItems.value.filter(item => 
        item.product?.product_id !== product.product_id
      )
      showNotification('Removed from wishlist', 'success')
      
      window.dispatchEvent(new CustomEvent('wishlist-updated', {
        detail: { wishlistCount: response.data.wishlistCount || 0 }
      }))
    } else {
      const response = await axios.post('/wishlist/add', {
        product_id: product.product_id
      })
      
      if (response.data.wishlistItem) {
        wishlistItems.value.push(response.data.wishlistItem)
      } else if (response.data.action === 'added') {
        wishlistItems.value.push({ product })
      }
      
      showNotification('Added to wishlist', 'success')
      
      window.dispatchEvent(new CustomEvent('wishlist-updated', {
        detail: { wishlistCount: response.data.wishlistCount || 0 }
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
    const response = await axios.post('/cart', {
      product_id: product.product_id,
      quantity: 1
    })
    
    showNotification(response.data.message || 'Added to cart', 'success')
    
    window.dispatchEvent(new CustomEvent('cart-updated', {
      detail: { cartCount: response.data.cartCount || 0 }
    }))
  } catch (error) {
    console.error('Cart error:', error)
    
    if (error.response?.status === 404) {
      try {
        const fallbackResponse = await axios.post('/cart/add', {
          product_id: product.product_id,
          quantity: 1
        })
        
        showNotification(fallbackResponse.data.message || 'Added to cart', 'success')
        
        window.dispatchEvent(new CustomEvent('cart-updated', {
          detail: { cartCount: fallbackResponse.data.cartCount || 0 }
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

const setSort = (type) => {
  sortBy.value = type
  currentPage.value = 1
  isDropdownOpen.value = false
}

const toggleDropdown = (event) => {
  event.stopPropagation()
  isDropdownOpen.value = !isDropdownOpen.value
}

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
    window.dispatchEvent(new CustomEvent('cart-updated', {
      detail: { cartCount: response.data.count || 0 }
    }))
  } catch (error) {
    console.error('Error loading cart count:', error)
  }
}

const loadWishlistCount = async () => {
  if (!user.value) return
  
  try {
    const response = await axios.get('/api/wishlist/count')
    window.dispatchEvent(new CustomEvent('wishlist-updated', {
      detail: { wishlistCount: response.data.count || 0 }
    }))
  } catch (error) {
    console.error('Error loading wishlist count:', error)
  }
}

onMounted(() => {
  // Initialize Bootstrap carousel
  if (window.bootstrap) {
    const carouselElement = document.getElementById('fullscreenCarousel')
    if (carouselElement) {
      new window.bootstrap.Carousel(carouselElement, {
        interval: 5000,
        ride: 'carousel',
        wrap: true,
        touch: true
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

  // Load data
  loadRecentlyViewedProducts()

  if (user.value) {
    loadWishlistData()
    loadCartData()
    loadCartCount()
    loadWishlistCount()
  }
  
  // Update scroll position
  nextTick(() => {
    if (recentlyViewedContainer.value) {
      recentlyViewedMaxScroll.value = recentlyViewedContainer.value.scrollWidth - 
                                    recentlyViewedContainer.value.clientWidth
    }
  })
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
/* Premium E-commerce Homepage Styles */
.premium-ecommerce {
  --gold: #c5a059;
  --gold-light: #d4b483;
  --gold-dark: #a67c00;
  --primary: #2563eb;
  --primary-light: #3b82f6;
  --secondary: #64748b;
  --success: #10b981;
  --danger: #ef4444;
  --warning: #f59e0b;
  --light: #ffffff;
  --light-gray: #f8fafc;
  --medium-gray: #e2e8f0;
  --dark-gray: #94a3b8;
  --dark: #1e293b;
  
  /* Dark theme variables */
  --dark-bg: #0f172a;
  --dark-card: #1e293b;
  --dark-border: #334155;
  --dark-text: #f1f5f9;
  --dark-muted: #94a3b8;
  
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  background-color: var(--light);
  transition: background-color 0.3s ease, color 0.3s ease;
}

/* Dark theme support */
[data-theme="dark"] .premium-ecommerce {
  background-color: var(--dark-bg);
  color: var(--dark-text);
}

[data-theme="dark"] .text-muted {
  color: var(--dark-muted) !important;
}

[data-theme="dark"] .text-dark {
  color: var(--dark-text) !important;
}

/* ===== HERO SECTION FIXES ===== */
.fullscreen-hero {
  height: 60vh;
  min-height: 500px;
  position: relative;
  background: transparent;
  overflow: hidden;
}

.hero-slider-fullscreen {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
    margin-top: 40px;

}

.carousel-item {
  height: 60vh;
  min-height: 500px;
  position: relative;
}

/* Background image container */
.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
}

/* Background image element - reduced size */
.bg-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: var(--light-gray);
  transition: transform 8s ease;
  transform: scale(1.05);
  filter: blur(2px) brightness(0.9);
}

.carousel-item.active .bg-image {
  transform: scale(1);
}

/* Dark overlay with blur effect */
.hero-overlay-dark {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(3px);
  z-index: 2;
}

/* Hero content - appears above everything, not affected by carousel */
.hero-content {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  z-index: 10;
  padding: 0 2rem;
}

/* Premium badge */
.premium-badge {
  margin-bottom: 1.5rem;
}

.badge-premium {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: white;
  padding: 0.5rem 1.5rem;
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 600;
  letter-spacing: 1px;
  display: inline-block;
  box-shadow: 0 4px 15px rgba(197, 160, 89, 0.2);
}

/* Main heading */
.display-1 {
  font-size: 3.5rem;
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 1.5rem;
  letter-spacing: -0.5px;
}

.text-gradient-gold {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.text-primary {
  color: var(--primary) !important;
}

[data-theme="dark"] .text-primary {
  color: var(--primary-light) !important;
}

.text-light {
  color: var(--light) !important;
}

[data-theme="dark"] .text-light {
  color: var(--dark-text) !important;
}

/* Description */
.lead {
  font-size: 1.25rem;
  color: var(--light);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 600px;
}

[data-theme="dark"] .lead {
  color: var(--dark-text);
}

/* CTA Buttons */
.hero-buttons {
  display: flex;
  gap: 1rem;
  margin-bottom: 3rem;
}

.btn-premium {
  position: relative;
  border: none;
  border-radius: 50px;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  padding: 1rem 2.5rem;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 0.9rem;
}

.btn-gold {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: white;
  box-shadow: 0 8px 25px rgba(197, 160, 89, 0.25);
}

.btn-gold:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(197, 160, 89, 0.35);
}

.btn-outline-light {
  background: transparent;
  border: 2px solid var(--light);
  color: var(--light);
}

[data-theme="dark"] .btn-outline-light {
  background: transparent;
  border: 2px solid var(--dark-text);
  color: var(--dark-text);
}

.btn-outline-light:hover {
  border-color: var(--gold);
  color: var(--gold);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Hero Stats */
.hero-stats {
  display: flex;
  gap: 3rem;
  padding-top: 2rem;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}

[data-theme="dark"] .hero-stats {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.stat-card {
  text-align: center;
}

.stat-number {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--gold);
  margin-bottom: 0.25rem;
  display: block;
}

.stat-label {
  font-size: 0.875rem;
  color: var(--light);
  font-weight: 500;
}

[data-theme="dark"] .stat-label {
  color: var(--dark-text);
}

/* Carousel Controls */
.carousel-control-prev,
.carousel-control-next {
  width: 60px;
  height: 60px;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  border: 1px solid var(--medium-gray);
  margin: 0 1rem;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  opacity: 0.8;
  z-index: 20;
}

[data-theme="dark"] .carousel-control-prev,
[data-theme="dark"] .carousel-control-next {
  background: rgba(30, 41, 59, 0.9);
  border: 1px solid var(--dark-border);
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
  background: white;
  border-color: var(--gold);
  transform: translateY(-50%) scale(1.1);
  opacity: 1;
}

[data-theme="dark"] .carousel-control-prev:hover,
[data-theme="dark"] .carousel-control-next:hover {
  background: var(--dark-card);
}

.carousel-control-icon {
  color: var(--dark);
  font-size: 1.2rem;
}

[data-theme="dark"] .carousel-control-icon {
  color: var(--dark-text);
}

/* Carousel Indicators */
.carousel-indicators-custom {
  position: absolute;
  bottom: 2rem;
  left: 0;
  right: 0;
  z-index: 20;
}

.indicators-container {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.indicator-btn {
  width: 40px;
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
  border: none;
  border-radius: 2px;
  position: relative;
  overflow: hidden;
}

[data-theme="dark"] .indicator-btn {
  background: rgba(255, 255, 255, 0.2);
}

.indicator-btn.active {
  background: var(--gold);
}

.indicator-btn.active .indicator-progress {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  background: var(--gold-light);
  animation: progress 5s linear infinite;
}

@keyframes progress {
  0% { width: 0%; }
  100% { width: 100%; }
}

/* Scroll Down Indicator */
.scroll-down-indicator {
  position: absolute;
  bottom: 1rem;
  left: 50%;
  transform: translateX(-50%);
  z-index: 20;
}

.scroll-down-indicator a {
  display: block;
  width: 50px;
  height: 50px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--dark);
  text-decoration: none;
  border: 1px solid var(--medium-gray);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

[data-theme="dark"] .scroll-down-indicator a {
  background: var(--dark-card);
  color: var(--dark-text);
  border: 1px solid var(--dark-border);
}

.scroll-down-indicator a:hover {
  background: var(--gold);
  color: white;
  border-color: var(--gold);
  transform: translateY(-5px);
}

/* ===== RECENTLY VIEWED SECTION ===== */
.recently-viewed-section {
  background: var(--light);
  transition: background-color 0.3s ease;
}

[data-theme="dark"] .recently-viewed-section {
  background: var(--dark-bg);
}

.section-header {
  margin-bottom: 3rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--dark);
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
  transition: color 0.3s ease;
}

[data-theme="dark"] .section-title {
  color: var(--dark-text);
}

.text-gradient-primary {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.section-subtitle {
  font-size: 1.1rem;
  color: var(--dark-gray);
  margin-bottom: 0;
  transition: color 0.3s ease;
}

[data-theme="dark"] .section-subtitle {
  color: var(--dark-muted);
}

.section-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  box-shadow: 0 10px 30px rgba(197, 160, 89, 0.3);
}

/* Navigation Buttons */
.navigation-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-navigation {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid var(--medium-gray);
  background: white;
  color: var(--dark-gray);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

[data-theme="dark"] .btn-navigation {
  background: var(--dark-card);
  border: 2px solid var(--dark-border);
  color: var(--dark-text);
}

.btn-navigation:hover:not(:disabled) {
  border-color: var(--gold);
  background: var(--gold);
  color: white;
}

.btn-navigation:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.btn-clear {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid var(--danger);
  background: transparent;
  color: var(--danger);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.btn-clear:hover {
  background: var(--danger);
  color: white;
}

/* Product Cards */
.product-card {
  background: white;
  border: 1px solid var(--medium-gray);
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  height: 100%;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.04);
}

[data-theme="dark"] .product-card {
  background: var(--dark-card);
  border: 1px solid var(--dark-border);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.premium-hover:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
  border-color: var(--gold);
}

[data-theme="dark"] .premium-hover:hover {
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.product-image-container {
  position: relative;
  height: 220px;
  background: var(--light-gray);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

[data-theme="dark"] .product-image-container {
  background: var(--dark-bg);
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 1.5rem;
  transition: transform 0.6s ease;
}

.product-card:hover .product-img {
  transform: scale(1.08);
}

/* Quick Actions */
.quick-actions {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  opacity: 0;
  transform: translateX(20px);
  transition: all 0.3s ease;
}

.product-card:hover .quick-actions {
  opacity: 1;
  transform: translateX(0);
}

.btn-action {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: none;
  background: white;
  color: var(--dark-gray);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

[data-theme="dark"] .btn-action {
  background: var(--dark-card);
  color: var(--dark-text);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.btn-wishlist.active {
  background: var(--danger);
  color: white;
}

.btn-wishlist:hover {
  background: var(--danger);
  color: white;
  transform: scale(1.1);
}

.btn-cart:hover {
  background: var(--gold);
  color: white;
  transform: scale(1.1);
}

/* Status Badges */
.status-badges {
  position: absolute;
  top: 1rem;
  left: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.badge {
  padding: 0.375rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 20px;
  border: none;
}

.owner-badge {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: white;
}

.out-of-stock-badge {
  background: var(--danger);
  color: white;
}

.low-stock-badge {
  background: var(--warning);
  color: var(--dark);
}

[data-theme="dark"] .low-stock-badge {
  color: var(--dark-text);
}

.view-time {
  position: absolute;
  bottom: 1rem;
  left: 1rem;
}

.time-badge {
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.75rem;
  backdrop-filter: blur(10px);
}

[data-theme="dark"] .time-badge {
  background: rgba(0, 0, 0, 0.5);
}

/* Card Body */
.card-body {
  padding: 1.5rem;
}

.product-title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--dark);
  margin-bottom: 0.5rem;
  cursor: pointer;
  transition: color 0.3s ease;
  line-height: 1.4;
}

[data-theme="dark"] .product-title {
  color: var(--dark-text);
}

.product-title:hover {
  color: var(--gold);
}

.product-price {
  margin-top: 0.5rem;
}

.price-current {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--gold);
}

/* Recently Viewed Scroll Container */
.recently-viewed-container {
  overflow-x: auto;
  scroll-behavior: smooth;
  padding: 1rem 0.5rem;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.recently-viewed-container::-webkit-scrollbar {
  display: none;
}

.recently-viewed-items {
  display: flex;
  gap: 1.5rem;
  width: max-content;
}

.recently-viewed-card {
  flex: 0 0 280px;
}

/* Scroll Progress */
.scroll-progress {
  padding: 0 1rem;
}

.progress-track {
  height: 4px;
  background: var(--medium-gray);
  border-radius: 2px;
  overflow: hidden;
}

[data-theme="dark"] .progress-track {
  background: var(--dark-border);
}

.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, var(--gold), var(--gold-light));
  border-radius: 2px;
  transition: width 0.3s ease;
}

/* ===== DISCOUNTED PRODUCTS SECTION ===== */
.discounted-products-section {
  background: linear-gradient(135deg, var(--light-gray) 0%, #ffffff 100%);
  transition: background-color 0.3s ease;
}

[data-theme="dark"] .discounted-products-section {
  background: linear-gradient(135deg, var(--dark-bg) 0%, var(--dark-card) 100%);
}

.section-badge {
  display: inline-block;
  margin-bottom: 1rem;
}

.badge-hot {
  background: linear-gradient(135deg, #ef4444, #f59e0b);
  color: white;
  padding: 0.5rem 1.5rem;
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 600;
  letter-spacing: 1px;
  box-shadow: 0 5px 20px rgba(239, 68, 68, 0.2);
}

.text-gradient-fire {
  background: linear-gradient(135deg, #ef4444, #f59e0b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Discount Card */
.discount-card {
  position: relative;
}

.discount-ribbon {
  position: absolute;
  top: 1rem;
  right: -10px;
  z-index: 2;
  background: linear-gradient(135deg, #ef4444, #f59e0b);
  color: white;
  padding: 0.5rem 2rem;
  clip-path: polygon(0 0, 100% 0, 90% 50%, 100% 100%, 0 100%, 10% 50%);
  font-weight: 700;
  font-size: 0.875rem;
  box-shadow: 0 5px 15px rgba(239, 68, 68, 0.2);
}

.badge-discount {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.75rem;
  font-weight: 600;
}

.price-container {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-top: 0.5rem;
}

.price-original {
  text-decoration: line-through;
  color: var(--dark-gray);
  font-size: 0.875rem;
}

[data-theme="dark"] .price-original {
  color: var(--dark-muted);
}

.price-discounted {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--danger);
}

/* ===== ALL PRODUCTS SECTION ===== */
.products-section {
  background: var(--light);
  transition: background-color 0.3s ease;
}

[data-theme="dark"] .products-section {
  background: var(--dark-bg);
}

.category-badge {
  margin-bottom: 0.75rem;
}

.badge-category {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.75rem;
  font-weight: 600;
}

/* Sorting */
.sorting-tools {
  display: flex;
  justify-content: flex-end;
}

.sort-dropdown {
  position: relative;
}

.btn-sort {
  background: white;
  border: 2px solid var(--medium-gray);
  border-radius: 50px;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  color: var(--dark);
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
  cursor: pointer;
}

[data-theme="dark"] .btn-sort {
  background: var(--dark-card);
  border: 2px solid var(--dark-border);
  color: var(--dark-text);
}

.btn-sort:hover {
  border-color: var(--gold);
  color: var(--gold);
}

.dropdown-arrow {
  transition: transform 0.3s ease;
  margin-left: 0.5rem;
}

.sort-dropdown.show .dropdown-arrow {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  min-width: 200px;
  background: white;
  border-radius: 15px;
  border: 1px solid var(--medium-gray);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  margin-top: 0.5rem;
  padding: 0.5rem 0;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.3s ease;
  z-index: 1000;
}

[data-theme="dark"] .dropdown-menu {
  background: var(--dark-card);
  border: 1px solid var(--dark-border);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.dropdown-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-item {
  padding: 0.75rem 1.5rem;
  display: flex;
  align-items: center;
  color: var(--dark);
  text-decoration: none;
  transition: all 0.2s ease;
  cursor: pointer;
}

[data-theme="dark"] .dropdown-item {
  color: var(--dark-text);
}

.dropdown-item:hover {
  background: rgba(197, 160, 89, 0.1);
  color: var(--gold);
}

[data-theme="dark"] .dropdown-item:hover {
  background: rgba(197, 160, 89, 0.2);
}

.dropdown-item.active {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: white;
}

/* Pagination */
.pagination-premium {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.page-item .page-link {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  border: 2px solid var(--medium-gray);
  background: white;
  color: var(--dark);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
}

[data-theme="dark"] .page-item .page-link {
  background: var(--dark-card);
  border: 2px solid var(--dark-border);
  color: var(--dark-text);
}

.page-item:not(.active) .page-link:hover {
  border-color: var(--gold);
  color: var(--gold);
  transform: scale(1.1);
}

.page-item.active .page-link {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  border-color: var(--gold);
  color: white;
}

.page-item.disabled .page-link {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Empty States */
.empty-state {
  text-align: center;
  padding: 3rem 1rem;
}

.empty-icon {
  margin-bottom: 1.5rem;
  color: var(--medium-gray);
}

[data-theme="dark"] .empty-icon {
  color: var(--dark-border);
}

.empty-state h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--dark);
  margin-bottom: 0.75rem;
  transition: color 0.3s ease;
}

[data-theme="dark"] .empty-state h3 {
  color: var(--dark-text);
}

.empty-state p {
  color: var(--dark-gray);
  margin-bottom: 1.5rem;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
  transition: color 0.3s ease;
}

[data-theme="dark"] .empty-state p {
  color: var(--dark-muted);
}

/* Toast Notification */
.toast-notification {
  position: fixed;
  top: 2rem;
  right: 2rem;
  z-index: 9999;
  transform: translateX(400px);
  transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.toast-notification.show {
  transform: translateX(0);
}

.toast-content {
  background: white;
  border-radius: 15px;
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  border-left: 5px solid;
  min-width: 300px;
  animation: toastSlideIn 0.5s ease;
}

[data-theme="dark"] .toast-content {
  background: var(--dark-card);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

@keyframes toastSlideIn {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.toast-content.success {
  border-left-color: var(--success);
}

.toast-content.warning {
  border-left-color: var(--warning);
}

.toast-content.error {
  border-left-color: var(--danger);
}

.toast-content.info {
  border-left-color: var(--primary);
}

.toast-icon {
  font-size: 1.5rem;
}

.toast-content.success .toast-icon {
  color: var(--success);
}

.toast-content.warning .toast-icon {
  color: var(--warning);
}

.toast-content.error .toast-icon {
  color: var(--danger);
}

.toast-content.info .toast-icon {
  color: var(--primary);
}

.toast-message {
  flex: 1;
  font-weight: 500;
  color: var(--dark);
  transition: color 0.3s ease;
}

[data-theme="dark"] .toast-message {
  color: var(--dark-text);
}

.toast-close {
  background: none;
  border: none;
  color: var(--dark-gray);
  cursor: pointer;
  transition: color 0.2s ease;
}

[data-theme="dark"] .toast-close {
  color: var(--dark-muted);
}

.toast-close:hover {
  color: var(--dark);
}

[data-theme="dark"] .toast-close:hover {
  color: var(--dark-text);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
  .fullscreen-hero {
    height: 50vh;
    min-height: 400px;
  }
  
  .carousel-item {
    height: 50vh;
    min-height: 400px;
  }
  
  .display-1 {
    font-size: 2.5rem;
  }
  
  .lead {
    font-size: 1.1rem;
  }
  
  .hero-buttons {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .btn-premium {
    width: 100%;
    max-width: 300px;
    text-align: center;
    justify-content: center;
  }
  
  .hero-stats {
    flex-wrap: wrap;
    gap: 2rem;
  }
  
  .stat-card {
    flex: 0 0 calc(50% - 1rem);
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .recently-viewed-card {
    flex: 0 0 240px;
  }
  
  .carousel-control-prev,
  .carousel-control-next {
    width: 50px;
    height: 50px;
    margin: 0 0.5rem;
  }
}

@media (max-width: 576px) {
  .fullscreen-hero {
    height: 40vh;
    min-height: 350px;
  }
  
  .carousel-item {
    height: 40vh;
    min-height: 350px;
  }
  
  .display-1 {
    font-size: 2rem;
  }
  
  .section-title {
    font-size: 1.75rem;
  }
  
  .stat-number {
    font-size: 1.25rem;
  }
  
  .recently-viewed-card {
    flex: 0 0 220px;
  }
}

/* Utility Classes */
.text-gold { 
  color: var(--gold) !important; 
}

.bg-light { 
  background-color: var(--light) !important; 
}

[data-theme="dark"] .bg-light {
  background-color: var(--dark-bg) !important;
}

.bg-light-gray { 
  background-color: var(--light-gray) !important; 
}

[data-theme="dark"] .bg-light-gray {
  background-color: var(--dark-card) !important;
}

.cursor-pointer { 
  cursor: pointer; 
}

.shadow-soft {
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05) !important;
}

[data-theme="dark"] .shadow-soft {
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1) !important;
}

/* Smooth transitions for dark theme */
* {
  transition: background-color 0.3s ease, 
              color 0.3s ease, 
              border-color 0.3s ease,
              box-shadow 0.3s ease;
}
</style>