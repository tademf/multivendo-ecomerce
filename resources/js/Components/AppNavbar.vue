<template>
  <nav class="navbar navbar-expand-lg navbar-white sticky-top shadow-sm">
    <div class="container">
      <!-- Brand/Logo -->
      <a href="/" class="navbar-brand d-flex align-items-center">
        <div class="brand-logo me-2">
          <i class="fas fa-shopping-bag fa-lg text-white"></i>
        </div>
        <div class="brand-text">
          <span class="fw-bold fs-4 brand-title">E-SHOP</span>
          <div class="brand-subtitle">Premium Marketplace</div>
        </div>
      </a>


      <!-- Mobile Toggle -->
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarContent">
        <!-- Center Navigation -->
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <!-- Categories Dropdown with Pure CSS -->
          <li class="nav-item dropdown-pure-css me-3">
            <div class="dropdown-trigger">
              <a href="#" class="nav-link dropdown-toggle-btn d-flex align-items-center">
                <i class="fas fa-th-large me-2"></i>
                <span class="category-label">{{ selectedCategory || 'Categories' }}</span>
                <i class="fas fa-chevron-down ms-2"></i>
              </a>
              <div class="dropdown-content">
                <div class="dropdown-header">
                  <h6 class="fw-bold text-primary mb-0">Browse Categories</h6>
                </div>
                <div class="dropdown-divider"></div>
                <a href="#" @click.prevent="selectCategory('')" class="dropdown-item">
                  <div class="d-flex align-items-center">
                    <div class="category-icon me-3">
                      <i class="fas fa-th-large text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="fw-medium">All Categories</div>
                    </div>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a v-for="category in $page.props.categories" 
                   :key="category.id || category.category_id"
                   href="#" 
                   @click.prevent="selectCategory(category.name)"
                   class="dropdown-item">
                  <div class="d-flex align-items-center">
                    <div class="category-icon me-3">
                      <i :class="getCategoryIcon(category.name)" class="text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="fw-medium">{{ category.name }}</div>
                      <small v-if="category.products_count" class="text-muted">{{ category.products_count }} products</small>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </li>

          <!-- Search Bar -->
          <li class="nav-item search-container">
            <div class="input-group search-group">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
              <input
                type="text"
                v-model="searchQuery"
                @keyup.enter="performSearch"
                class="form-control search-input"
                placeholder="Search for products..."
              />
              <button @click="performSearch" class="btn btn-primary search-btn px-3" type="button">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </li>
        </ul>

              <!-- Right side: Theme toggle for logged out users and Login text -->
      <div class="d-flex align-items-center ms-auto me-3">
        <!-- Theme toggle when NOT logged in (moved to right) -->
        <div v-if="!isLoggedIn" class="d-flex align-items-center gap-3">
          <!-- Theme Toggle Button -->
          <button 
            @click="toggleTheme" 
            class="theme-toggle-btn btn btn-outline-secondary btn-sm d-flex align-items-center"
            :title="theme === 'light' ? 'Switch to Dark Mode' : 'Switch to Light Mode'"
          >
            <i :class="theme === 'light' ? 'fas fa-moon' : 'fas fa-sun'"></i>
          </button>
          
          <!-- Login Text -->
          <div class="login-text-container">
            <a href="/login" class="login-text fw-bold text-decoration-none">
               LOGIN
            </a>
          </div>
        </div>
      </div>


        <!-- Right Navigation for logged in users -->
        <div v-if="isLoggedIn" class="d-flex align-items-center">


<!-- Wishlist Link -->
<a href="/wishlist" class="nav-link position-relative">
  <i class="fas fa-heart"></i>
  <span v-if="wishlistCount > 0" 
        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
    {{ wishlistCount }}
  </span>
</a>

<!-- Cart Link -->
<a href="/cart" class="nav-link position-relative">
  <i class="fas fa-shopping-cart"></i>
  <span v-if="cartCount > 0" 
        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
    {{ cartCount }}
  </span>
</a>          <!-- Messages Icon with Notification Badge -->
          <div class="position-relative me-3">
            <a :href="route('messages.conversations')" class="nav-icon border-0 bg-transparent position-relative text-decoration-none">
              <i class="fas fa-envelope"></i>
              <!-- Unread Message Badge -->
              <span v-if="unreadCount > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ unreadCount > 9 ? '9+' : unreadCount }}
                <span class="visually-hidden">unread messages</span>
              </span>
            </a>
          </div>

          <!-- User Profile Dropdown with Pure CSS -->
          <div class="dropdown-pure-css user-dropdown ms-2">
            <div class="dropdown-trigger">
              <a href="#" class="user-dropdown-btn d-flex align-items-center text-decoration-none">
                <div class="position-relative me-2">
                  <div class="user-avatar">
                    <img v-if="userProfilePicture" :src="userProfilePicture" :alt="userName" class="rounded-circle">
                    <div v-else class="rounded-circle avatar-placeholder d-flex align-items-center justify-content-center">
                      {{ userName.charAt(0).toUpperCase() }}
                    </div>
                  </div>
                  <div class="online-indicator"></div>
                </div>
                <!-- <span class="fw-medium">{{ userName }}</span> -->
                <i class="fas fa-chevron-down ms-2"></i>
              </a>
              <div class="dropdown-content user-dropdown-content">
                <!-- Header with Profile Picture -->
                <div class="dropdown-header-user">
                  <div class="d-flex align-items-center">
                    <div class="position-relative me-3">
                      <div class="user-avatar-lg">
                        <img v-if="userProfilePicture" :src="userProfilePicture" :alt="userName" class="rounded-circle">
                        <div v-else class="rounded-circle avatar-placeholder-lg d-flex align-items-center justify-content-center">
                          {{ userName.charAt(0).toUpperCase() }}
                        </div>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-1 fw-bold">{{ userName }}</h6>
                      <div class="mt-1">
                        <span v-if="isVerified" class="badge bg-success text-white">
                          <i class="fas fa-check-circle me-1"></i>Verified
                        </span>
                        <span v-else class="badge bg-warning text-dark">
                          <i class="fas fa-clock me-1"></i>Not Verified
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="dropdown-divider"></div>
                
                <!-- My Orders (Always visible) -->
                <a :href="route('orders.customer')" class="dropdown-item">
                  <i class="fas fa-shopping-bag me-3 text-primary"></i>
                  <div class="fw-medium">My Orders</div>
                </a>

                <!-- Vendor features - only shown if verified -->
                <template v-if="isVerified">
                  <!-- Manage Orders -->
                  <a :href="route('orders.vendor')" class="dropdown-item">
                    <i class="fas fa-clipboard-list me-3 text-primary"></i>
                    <div class="fw-medium">Manage Orders</div>
                  </a>

                  <!-- My Products -->
                  <a :href="route('products.index')" class="dropdown-item">
                    <i class="fas fa-box me-3 text-primary"></i>
                    <div class="fw-medium">My Products</div>
                  </a>
                  
                  <!-- Discounts -->
                  <a :href="route('discounts.index')" class="dropdown-item">
                    <i class="fas fa-percent me-3 text-primary"></i>
                    <div class="fw-medium">Discounts</div>
                  </a>
                </template>
                
                <!-- Verification link - only shown if NOT verified -->
                <a v-if="!isVerified" :href="route('verification.request')" class="dropdown-item">
                  <i class="fas fa-user-check me-3 text-primary"></i>
                  <div class="fw-medium">Get Verified</div>
                </a>
                
                <!-- Messages/Conversations Link -->
                <!-- <a :href="route('messages.conversations')" class="dropdown-item position-relative">
                  <i class="fas fa-envelope me-3 text-primary"></i>
                  <div class="fw-medium">Messages</div> -->
                  <!-- Unread badge in dropdown -->
                  <!-- <span v-if="unreadCount > 0" class="position-absolute top-50 end-0 translate-middle-y badge rounded-pill bg-danger me-3">
                    {{ unreadCount > 9 ? '9+' : unreadCount }}
                  </span>
                </a> -->
                
                <!-- Dark/Light Mode Toggle - Moved to top of dropdown -->
                <button @click="toggleTheme" class="dropdown-item theme-toggle-item w-100 text-start">
                  <i :class="theme === 'light' ? 'fas fa-moon me-3 text-primary' : 'fas fa-sun me-3 text-warning'"></i>
                  <div class="fw-medium">{{ theme === 'light' ? 'Dark Mode' : 'Light Mode' }}</div>
                </button>

                <!-- Settings -->
                <a :href="route('settings.page')" class="dropdown-item">
                  <i class="fas fa-cog me-3 text-primary"></i>
                  <div class="fw-medium">Settings</div>
                </a>
                
                <div class="dropdown-divider"></div>
                
                <!-- Logout -->
                <a href="#" @click.prevent="logout" class="dropdown-item text-danger">
                  <i class="fas fa-sign-out-alt me-3"></i>
                  <div class="fw-medium">Logout</div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { useTheme } from '@/Composables/useTheme'
import axios from 'axios'

const page = usePage()
const { theme, toggleTheme } = useTheme()

const searchQuery = ref('')
const selectedCategory = ref('')
const csrfToken = ref(document.querySelector('meta[name="csrf-token"]')?.content || '')
const unreadCount = ref(page.props.auth?.unread_messages || 0)
const wishlistCount = ref(0) // ADDED
const cartCount = ref(0) // ADDED
let pollInterval = null

const user = computed(() => page.props.auth?.user || null)
const categories = computed(() => page.props.categories || [])

const isLoggedIn = computed(() => user.value && user.value.id !== undefined && user.value.id !== null)

const userName = computed(() => {
  if (!user.value) return 'User'
  if (user.value.full_name) return user.value.full_name
  if (user.value.name) return user.value.name
  return user.value.email?.split('@')[0] || 'User'
})

const isVerified = computed(() => {
  if (!user.value) return false
  return user.value.email_verified_at !== null || 
         user.value.is_verified === true ||
         user.value.verified_at !== null
})

const userProfilePicture = computed(() => {
  if (!user.value) return null
  if (user.value.profile_picture) return formatProfilePictureUrl(user.value.profile_picture)
  if (user.value.profile_picture_url) return formatProfilePictureUrl(user.value.profile_picture_url)
  return null
})

function formatProfilePictureUrl(path) {
  if (!path) return null
  if (path.startsWith('http') || path.startsWith('/')) return path
  if (path.startsWith('storage/')) return path.replace('storage/', '/storage/')
  return `/storage/${path}`
}

function getCategoryIcon(categoryName) {
  const iconMap = {
    'Electronics': 'fas fa-laptop',
    'Clothing': 'fas fa-tshirt',
    'Books': 'fas fa-book',
    'Home & Garden': 'fas fa-home',
    'Home & Kitchen': 'fas fa-home',
    'Sports': 'fas fa-futbol',
    'Art': 'fas fa-palette',
    'Furniture': 'fas fa-couch',
    'Beauty': 'fas fa-spa',
    'Fashion': 'fas fa-tshirt',
    'Accessories': 'fas fa-glasses',
    'Health': 'fas fa-heartbeat',
    'Toys': 'fas fa-gamepad',
    'Automotive': 'fas fa-car',
    'Garden': 'fas fa-seedling',
    'Office': 'fas fa-briefcase',
    'Pet Supplies': 'fas fa-paw',
    'Food': 'fas fa-utensils',
    'Drinks': 'fas fa-wine-glass',
    'Jewelry': 'fas fa-gem',
    'Shoes': 'fas fa-shoe-prints',
    'Bags': 'fas fa-shopping-bag',
    'Watches': 'fas fa-clock',
    'Music': 'fas fa-music',
    'Movies': 'fas fa-film',
    'Games': 'fas fa-gamepad',
    'Phones': 'fas fa-mobile-alt',
    'Computers': 'fas fa-desktop',
    'Tablets': 'fas fa-tablet-alt',
    'Cameras': 'fas fa-camera',
    'TV': 'fas fa-tv',
    'Audio': 'fas fa-headphones',
    'Appliances': 'fas fa-plug',
    'Tools': 'fas fa-tools',
    'Building': 'fas fa-hammer',
    'Lighting': 'fas fa-lightbulb',
    'Bedding': 'fas fa-bed',
    'Bath': 'fas fa-bath',
    'Storage': 'fas fa-archive',
    'Decor': 'fas fa-palette',
    'Outdoor': 'fas fa-umbrella-beach',
    'Exercise': 'fas fa-dumbbell',
    'Cycling': 'fas fa-bicycle',
    'Fishing': 'fas fa-fish',
    'Camping': 'fas fa-campground',
    'Hiking': 'fas fa-hiking',
    'Swimming': 'fas fa-swimming-pool',
    'Yoga': 'fas fa-spa',
    'Golf': 'fas fa-golf-ball',
    'Tennis': 'fas fa-baseball-ball',
    'Basketball': 'fas fa-basketball-ball',
    'Football': 'fas fa-football-ball',
    'Soccer': 'fas fa-futbol',
    'Baseball': 'fas fa-baseball-ball',
    'Hockey': 'fas fa-hockey-puck',
    'Boxing': 'fas fa-boxing-glove',
    'Martial Arts': 'fas fa-user-ninja',
    'Skateboarding': 'fas fa-skating',
    'Snow Sports': 'fas fa-snowflake',
    'Water Sports': 'fas fa-water',
    'Winter Sports': 'fas fa-snowman',
  }
  
  for (const [key, icon] of Object.entries(iconMap)) {
    if (categoryName.toLowerCase().includes(key.toLowerCase())) {
      return icon
    }
  }
  
  return 'fas fa-box'
}

function readCategoryFromURL() {
  const urlParams = new URLSearchParams(window.location.search)
  const categoryParam = urlParams.get('category')
  if (categoryParam) selectedCategory.value = decodeURIComponent(categoryParam)
  
  const searchParam = urlParams.get('search')
  if (searchParam) searchQuery.value = decodeURIComponent(searchParam)
}

function selectCategory(categoryName) {
  selectedCategory.value = categoryName
  
  window.dispatchEvent(new CustomEvent('navbar-category-select', {
    detail: { categoryName }
  }))
  
  const urlParams = new URLSearchParams(window.location.search)
  if (categoryName) urlParams.set('category', encodeURIComponent(categoryName))
  else urlParams.delete('category')
  
  if (searchQuery.value.trim()) urlParams.set('search', encodeURIComponent(searchQuery.value.trim()))
  
  const queryString = urlParams.toString()
  const url = queryString ? `/?${queryString}` : '/'
  
  if (typeof router !== 'undefined') router.get(url)
  else window.location.href = url
}

function performSearch() {
  if (searchQuery.value.trim()) {
    window.dispatchEvent(new CustomEvent('navbar-search', {
      detail: { searchTerm: searchQuery.value.trim() }
    }))
    
    const urlParams = new URLSearchParams()
    urlParams.set('search', encodeURIComponent(searchQuery.value.trim()))
    if (selectedCategory.value) urlParams.set('category', encodeURIComponent(selectedCategory.value))
    
    const url = `/?${urlParams.toString()}`
    
    if (typeof router !== 'undefined') router.get(url)
    else window.location.href = url
  }
}

// Load wishlist and cart counts
const loadWishlistCount = async () => {
  if (!isLoggedIn.value) {
    wishlistCount.value = 0
    return
  }
  
  try {
    const response = await axios.get('/api/wishlist/count')
    wishlistCount.value = response.data.count || 0
  } catch (error) {
    console.error('Error loading wishlist count:', error)
    wishlistCount.value = 0
  }
}

const loadCartCount = async () => {
  if (!isLoggedIn.value) {
    cartCount.value = 0
    return
  }
  
  try {
    const response = await axios.get('/api/cart/count')
    cartCount.value = response.data.count || 0
  } catch (error) {
    console.error('Error loading cart count:', error)
    cartCount.value = 0
  }
}

// Listen for cart and wishlist updates
const setupEventListeners = () => {
  window.addEventListener('wishlist-updated', (event) => {
    wishlistCount.value = event.detail?.wishlistCount || 0
  })
  
  window.addEventListener('cart-updated', (event) => {
    cartCount.value = event.detail?.cartCount || 0
  })
}

// Fetch unread message count
async function fetchUnreadCount() {
  try {
    const response = await axios.get(route('messages.unread-count'))
    if (response.data && typeof response.data.count === 'number') {
      unreadCount.value = response.data.count
    }
  } catch (error) {
    console.error('Error fetching unread message count:', error)
  }
}

// Start polling for unread messages
function startPollingUnreadMessages() {
  if (!isLoggedIn.value) return
  
  // Initial fetch
  fetchUnreadCount()
  
  // Poll every 30 seconds
  pollInterval = setInterval(fetchUnreadCount, 30000)
}

function logout() {
  // Stop polling before logout
  if (pollInterval) {
    clearInterval(pollInterval)
    pollInterval = null
  }
  
  const form = document.createElement('form')
  form.method = 'POST'
  form.action = '/logout'
  form.style.display = 'none'
  
  if (csrfToken.value) {
    const csrfInput = document.createElement('input')
    csrfInput.type = 'hidden'
    csrfInput.name = '_token'
    csrfInput.value = csrfToken.value
    form.appendChild(csrfInput)
  }
  
  const methodInput = document.createElement('input')
  methodInput.type = 'hidden'
  methodInput.name = '_method'
  methodInput.value = 'POST'
  form.appendChild(methodInput)
  
  document.body.appendChild(form)
  form.submit()
}

function showComingSoon(feature) {
  showNotification(`${feature} feature coming soon!`, 'info')
}

function showNotification(message, type = 'success') {
  const toastContainer = document.createElement('div')
  toastContainer.className = 'position-fixed bottom-0 end-0 p-3'
  toastContainer.style.zIndex = '1055'
  
  const toast = document.createElement('div')
  toast.className = `toast align-items-center text-white bg-${type} border-0`
  toast.setAttribute('role', 'alert')
  toast.setAttribute('aria-live', 'assertive')
  toast.setAttribute('aria-atomic', 'true')
  
  const icon = type === 'success' ? 'fa-check-circle' : 
              type === 'error' ? 'fa-exclamation-triangle' : 
              'fa-info-circle'
  
  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">
        <i class="fas ${icon} me-2"></i>
        ${message}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  `
  
  toastContainer.appendChild(toast)
  document.body.appendChild(toastContainer)
  
  if (window.bootstrap) {
    const bsToast = new window.bootstrap.Toast(toast, { delay: 3000 })
    bsToast.show()
    
    toast.addEventListener('hidden.bs.toast', () => {
      document.body.removeChild(toastContainer)
    })
  }
}

onMounted(() => {
  readCategoryFromURL()
  window.addEventListener('navbar-category-select', handleExternalCategorySelect)
  window.addEventListener('popstate', readCategoryFromURL)
  
  if (!document.querySelector('link[href*="font-awesome"]')) {
    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
    document.head.appendChild(link)
  }
  
  // Load cart and wishlist counts
  loadWishlistCount()
  loadCartCount()
  setupEventListeners()
  
  // Start polling for unread messages if user is logged in
  if (isLoggedIn.value) {
    startPollingUnreadMessages()
  }
})

onUnmounted(() => {
  // Clean up polling interval
  if (pollInterval) {
    clearInterval(pollInterval)
    pollInterval = null
  }
  
  // Remove event listeners
  window.removeEventListener('wishlist-updated', () => {})
  window.removeEventListener('cart-updated', () => {})
})

function handleExternalCategorySelect(event) {
  selectedCategory.value = event.detail.categoryName
}

if (typeof router !== 'undefined') {
  router.on('success', () => {
    setTimeout(readCategoryFromURL, 100)
  })
}
</script>
<style scoped>
/* Navbar Base */
.navbar-white {
  background: var(--navbar-bg);
  border-bottom: 1px solid var(--card-border);
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  position: sticky;
  top: 0;
  z-index: 1030;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
}

/* Brand Logo */
.brand-logo {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.brand-logo:hover {
  transform: rotate(8deg) scale(1.05);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
}

.brand-title {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 700;
  font-size: 1.75rem;
  letter-spacing: 0.5px;
}

.brand-subtitle {
  font-size: 0.7rem;
  color: var(--secondary-color);
  font-weight: 500;
  letter-spacing: 0.3px;
  margin-top: -2px;
}

/* Theme toggle button for logged out users */
.theme-toggle-btn {
  background: var(--card-bg) !important;
  border-color: var(--card-border) !important;
  color: var(--body-color) !important;
  padding: 0.25rem 0.5rem !important;
  border-radius: 6px !important;
  transition: all 0.3s ease;
}

.theme-toggle-btn:hover {
  background: var(--primary-color) !important;
  border-color: var(--primary-color) !important;
  color: white !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

/* Login Text - Bold Only */
.login-text-container {
  margin-left: 0.5rem;
}

.login-text {
  color: var(--body-color);
  font-size: 0.95rem;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  background: var(--card-bg);
  border: 1px solid var(--card-border);
}

.login-text:hover {
  background: var(--primary-color);
  color: white !important;
  text-decoration: none;
  border-color: var(--primary-color);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

/* Pure CSS Dropdown Container */
.dropdown-pure-css {
  position: relative;
  display: inline-block;
}

/* Dropdown Trigger */
.dropdown-trigger {
  position: relative;
}

/* Category Dropdown Button */
.dropdown-toggle-btn {
  background: var(--card-bg);
  border: 1px solid var(--card-border);
  border-radius: 10px;
  padding: 0.5rem 1rem;
  color: var(--body-color) !important;
  font-weight: 500;
  transition: all 0.3s ease;
  min-width: 160px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  text-decoration: none !important;
}

.dropdown-toggle-btn:hover {
  background: var(--card-bg);
  border-color: var(--primary-color);
  color: var(--primary-color) !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.category-label {
  flex-grow: 1;
  text-align: left;
  font-weight: 500;
}

/* Dropdown Content */
.dropdown-content {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  background-color: var(--dropdown-bg);
  border: 1px solid var(--dropdown-border);
  border-radius: 12px;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
  min-width: 250px;
  padding: 0.5rem;
  z-index: 1000;
  margin-top: 5px;
}

.dropdown-trigger:hover .dropdown-content {
  display: block;
}

.dropdown-header {
  padding: 0.5rem 0.75rem;
}

.dropdown-divider {
  height: 1px;
  background-color: var(--card-border);
  margin: 0.5rem 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  color: var(--body-color) !important;
  text-decoration: none !important;
  transition: all 0.2s ease;
  cursor: pointer;
  position: relative;
}

.dropdown-item:hover {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
  transform: translateX(5px);
  color: var(--primary-color) !important;
}

.category-icon {
  width: 32px;
  height: 32px;
  background: rgba(102, 126, 234, 0.1);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.category-icon i {
  font-size: 1rem;
  transition: all 0.2s ease;
}

.dropdown-item:hover .category-icon i {
  transform: scale(1.2);
}

/* Search Bar */
.search-container {
  width: 400px;
  margin: 0 auto;
}

.search-group {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  border: 1px solid var(--card-border);
  height: 46px;
}

.input-group-text {
  background-color: var(--card-bg);
  border-color: var(--card-border);
  color: var(--body-color);
}

.search-input {
  border: none;
  padding: 0.5rem 1rem;
  font-size: 0.95rem;
  background: var(--card-bg);
  color: var(--body-color);
}

.search-input:focus {
  box-shadow: none;
  background: var(--card-bg);
}

.search-btn {
  border: none;
  padding: 0 1.25rem;
  transition: all 0.3s ease;
}

.search-btn:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  transform: scale(1.05);
}

/* User Dropdown */
.user-dropdown-btn {
  display: flex;
  align-items: center;
  padding: 0.25rem 0.5rem;
  border-radius: 8px;
  color: var(--body-color) !important;
  transition: all 0.3s ease;
}

.user-dropdown-btn:hover {
  background-color: rgba(102, 126, 234, 0.1);
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  position: relative;
  border: 2px solid rgba(102, 126, 234, 0.2);
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  color: white;
  font-size: 1rem;
  font-weight: 600;
}

.online-indicator {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 8px;
  height: 8px;
  background: #4ade80;
  border: 2px solid white;
  border-radius: 50%;
  z-index: 2;
}

/* User Dropdown Content */
.user-dropdown-content {
  right: 0;
  left: auto;
  min-width: 280px;
}

.dropdown-header-user {
  padding: 1rem;
}

.user-avatar-lg {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid #667eea;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.user-avatar-lg img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder-lg {
  width: 100%;
  height: 100%;
  color: white;
  font-size: 1.2rem;
  font-weight: 600;
}

/* Theme toggle item in dropdown (now at the top) */
.theme-toggle-item {
  border: none;
  background: none;
  text-align: left;
  padding: 0.75rem 1rem !important;
  margin-top: 0.25rem;
}

.theme-toggle-item:hover {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%) !important;
  transform: translateX(5px) !important;
  color: var(--primary-color) !important;
}

/* Nav Icon - Updated for Messages */
.nav-icon {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--card-bg);
  border-radius: 50%;
  color: var(--body-color);
  text-decoration: none;
  transition: all 0.3s ease;
  border: 1px solid var(--card-border);
  cursor: pointer;
  position: relative;
}

.nav-icon:hover {
  background: var(--primary-color);
  color: white !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
  border-color: var(--primary-color);
}

/* Message Notification Badge */
.badge {
  font-size: 0.65rem;
  padding: 0.25em 0.5em;
  min-width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .search-container {
    width: 350px;
  }
}

@media (max-width: 992px) {
  .navbar-collapse {
    background: var(--navbar-bg);
    padding: 1.5rem;
    border-radius: 0 0 12px 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-top: 0.5rem;
  }
  
  .search-container {
    width: 100%;
    margin: 1rem 0;
  }
  
  .dropdown-toggle-btn {
    width: 100%;
    margin: 0.5rem 0;
  }
  
  .login-text-container {
    margin: 0.5rem 0;
    text-align: center;
    width: 100%;
  }
  
  .theme-toggle-btn {
    margin-bottom: 0.5rem;
  }
  
  .brand-title {
    font-size: 1.5rem;
  }
  
  .brand-logo {
    width: 40px;
    height: 40px;
  }
  
  .user-dropdown-content {
    right: auto;
    left: 0;
  }
  
  /* Stack theme toggle and login vertically on mobile */
  .d-flex.align-items-center.gap-3 {
    flex-direction: column;
    gap: 0.5rem !important;
  }
  
  .login-text-container {
    margin-left: 0 !important;
  }
}

@media (max-width: 768px) {
  .brand-title {
    font-size: 1.3rem;
  }
  
  .brand-subtitle {
    font-size: 0.65rem;
  }
  
  .login-text {
    font-size: 0.9rem;
    padding: 0.35rem 0.7rem;
  }
  
  .theme-toggle-btn {
    font-size: 0.8rem;
    padding: 0.2rem 0.4rem !important;
  }
  
  .badge {
    font-size: 0.6rem;
    padding: 0.2em 0.4em;
    min-width: 16px;
    height: 16px;
  }
}

@media (max-width: 576px) {
  .navbar-white {
    padding: 0.5rem;
  }
  
  .brand-logo {
    width: 36px;
    height: 36px;
  }
  
  .brand-title {
    font-size: 1.2rem;
  }
  
  .search-group {
    height: 44px;
  }
  
  .user-avatar {
    width: 36px;
    height: 36px;
  }
  
  .nav-icon {
    width: 32px;
    height: 32px;
    font-size: 0.9rem;
  }
  
  .login-text {
    font-size: 0.85rem;
    padding: 0.3rem 0.6rem;
  }
  
  .theme-toggle-btn {
    font-size: 0.75rem;
    padding: 0.15rem 0.35rem !important;
  }
  
  .badge {
    font-size: 0.55rem;
    padding: 0.15em 0.35em;
    min-width: 14px;
    height: 14px;
  }
}
</style>