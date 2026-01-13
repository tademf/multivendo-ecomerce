<template>
  <nav class="navbar navbar-expand-lg sticky-top premium-nav white-theme slim-nav">
    <div class="container slim-container">
      <!-- Brand Logo -->
      <a href="/" class="navbar-brand d-flex align-items-center slim-brand">
        <div class="brand-logo-box slim-logo">
          <i class="fas fa-gem text-gold"></i>
        </div>
        <div class="brand-text-group slim-text">
          <span class="brand-title slim-title">E-SHOP</span>
          <div class="brand-subtitle slim-subtitle">PREMIUM</div>
        </div>
      </a>

      <!-- Mobile Toggle -->
      <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <i class="fas fa-bars text-gold"></i>
      </button>

      <!-- Main Content -->
      <div class="collapse navbar-collapse" id="navbarContent">
        <!-- Left Navigation -->
        <ul class="navbar-nav mx-auto align-items-center slim-nav-items">
          <!-- Categories Dropdown -->
          <li class="nav-item dropdown-pure-css me-lg-3 slim-dropdown">
            <div class="dropdown-trigger">
              <a href="#" class="nav-link category-trigger slim-category" @click.prevent="toggleCategoryDropdown">
                <i class="fas fa-th-large me-2 gold-icon"></i>
                <span class="category-label slim-label">{{ selectedCategory || 'Categories' }}</span>
                <i class="fas fa-chevron-down ms-2 small-icon" :class="{ 'rotate-180': showCategoryDropdown }"></i>
              </a>
              <div v-if="showCategoryDropdown" class="dropdown-content luxury-drop" @click.stop>
                <div class="drop-header">Browse Collections</div>
                <a href="#" @click.prevent="selectCategory('')" class="dropdown-item slim-item">
                   <i class="fas fa-border-all me-3"></i> All Categories
                </a>
                <div class="dropdown-divider"></div>
                <a v-for="category in $page.props.categories" :key="category.id" 
                   href="#" @click.prevent="selectCategory(category.name)" class="dropdown-item slim-item">
                   <i :class="getCategoryIcon(category.name)" class="me-3"></i> {{ category.name }}
                </a>
              </div>
            </div>
          </li>

          <!-- Search Bar -->
          <li class="nav-item slim-search-container">
            <div class="premium-search-box slim-search">
              <i class="fas fa-search search-icon"></i>
              <input type="text" v-model="searchQuery" @keyup.enter="performSearch" 
                     @focus="showSearchSuggestions = true" placeholder="Search...">
              <button @click="performSearch" class="search-btn-gold slim-search-btn">
                <i class="fas fa-arrow-right"></i>
              </button>
            </div>
          </li>
        </ul>

        <!-- Right Actions -->
        <div class="dashboard-wrapper ms-auto slim-dashboard">
<!-- Quick Actions -->
<div v-if="isLoggedIn" class="action-dock slim-dock">
  <a href="/wishlist" class="dock-icon slim-icon" title="Wishlist">
    <i class="far fa-heart fa-sm"></i> <!-- Added fa-sm class -->
    <span v-if="wishlistCount > 0" class="notification-badge slim-badge">{{ wishlistCount }}</span>
  </a>

  <a :href="route('messages.conversations')" class="dock-icon slim-icon" title="Messages">
    <i class="far fa-envelope fa-sm"></i> <!-- Added fa-sm class -->
    <span v-if="unreadCount > 0" class="notification-badge slim-badge">{{ unreadCount > 9 ? '9+' : unreadCount }}</span>
  </a>

  <a href="/cart" class="dock-icon slim-icon" title="Cart">
    <i class="fas fa-shopping-cart fa-sm"></i> <!-- Added fa-sm class -->
    <span v-if="cartCount > 0" class="notification-badge slim-badge">{{ cartCount }}</span>
  </a>
</div>
          <!-- User Profile -->
          <div class="profile-section slim-profile">
            <div v-if="!isLoggedIn" class="auth-buttons slim-auth">
              <button @click="toggleGlobalTheme" class="theme-switch slim-theme" title="Toggle Theme">
                <i :class="theme === 'light' ? 'fas fa-moon' : 'fas fa-sun'"></i>
              </button>
              <a href="/login" class="btn-login slim-login">
                <i class="fas fa-sign-in-alt me-2"></i>LOGIN
              </a>
            </div>

            <div v-else class="dropdown-pure-css profile-dropdown">
              <div class="dropdown-trigger">
                <div class="profile-trigger slim-profile-trigger" @click.prevent="toggleProfileDropdown">
                  <div class="user-avatar slim-avatar">
                    <img v-if="userProfilePicture" :src="userProfilePicture" :alt="userName">
                    <div v-else class="avatar-initials slim-initials">{{ userName.charAt(0).toUpperCase() }}</div>
                  </div>
                  <div class="user-info slim-user-info">
                    <span class="user-name slim-username">{{ userName.split(' ')[0] }}</span>
                  </div>
                  <i class="fas fa-chevron-down dropdown-arrow slim-arrow" 
                     :class="{ 'rotate-180': showProfileDropdown }"></i>
                </div>

                <!-- Dropdown Menu -->
                <div v-if="showProfileDropdown" class="dropdown-content user-menu slim-menu" @click.stop>
                  <!-- User Info Header -->
                  <!-- <div class="user-profile-header slim-header"> -->
                    <!-- <div class="user-avatar-large slim-avatar-large"> -->
                      <!-- <img v-if="userProfilePicture" :src="userProfilePicture" :alt="userName">
                      <div v-else class="avatar-initials-large slim-initials-large">{{ userName.charAt(0).toUpperCase() }}</div> -->
                    <!-- </div> -->
                    <div class="user-details slim-details">
                      <!-- <h6>{{ userName }}</h6>
                      <span class="email-text slim-email">{{ user.email }}</span> -->
                      <div class="verification-badge" :class="{ verified: isVerified }">
                        <i :class="isVerified ? 'fas fa-check-circle' : 'fas square'"></i>
                        {{ isVerified ? 'vendor' : 'customer' }}
                      </div>
                    </div>
                  <!-- </div> -->

                  <!-- Menu Items -->
                  <div class="menu-section slim-menu-section">
                    <div class="section-title slim-section-title">Shopping</div>
                    <a :href="route('orders.customer')" class="dropdown-item slim-menu-item" @click="closeDropdowns">
                      <i class="fas fa-shopping-bag me-3"></i>
                      <div class="menu-item-content slim-menu-content">
                        <span class="item-title slim-menu-title">My Orders</span>
                      </div>
                    </a>
                  </div>

                  <div class="menu-section slim-menu-section" v-if="isVerified">
                    <div class="section-title slim-section-title">Seller</div>
                    <a :href="route('orders.vendor')" class="dropdown-item slim-menu-item" @click="closeDropdowns">
                      <i class="fas fa-clipboard-list me-3"></i>
                      <div class="menu-item-content slim-menu-content">
                        <span class="item-title slim-menu-title">Manage Orders</span>
                      </div>
                    </a>
                    <a :href="route('products.index')" class="dropdown-item slim-menu-item" @click="closeDropdowns">
                      <i class="fas fa-box me-3"></i>
                      <div class="menu-item-content slim-menu-content">
                        <span class="item-title slim-menu-title">My Products</span>
                      </div>
                    </a>
                    <a :href="route('discounts.index')" class="dropdown-item slim-menu-item" @click="closeDropdowns">
    <i class="fas fa-tag me-3"></i> 
    <div class="menu-item-content slim-menu-content">
        <span class="item-title slim-menu-title">Discounts</span> 
    </div>
</a>
                  </div>

                  <div class="menu-section slim-menu-section">
                    <div class="section-title slim-section-title">Account</div>
                    <button @click="toggleGlobalTheme" class="dropdown-item slim-menu-item">
                      <i :class="theme === 'light' ? 'fas fa-moon me-3' : 'fas fa-sun me-3'"></i>
                      <div class="menu-item-content slim-menu-content">
                        <span class="item-title slim-menu-title">{{ theme === 'light' ? 'Dark Mode' : 'Light Mode' }}</span>
                      </div>
                    </button>
                    <a :href="route('settings.page')" class="dropdown-item slim-menu-item" @click="closeDropdowns">
                      <i class="fas fa-cog me-3"></i>
                      <div class="menu-item-content slim-menu-content">
                        <span class="item-title slim-menu-title">Settings</span>
                      </div>
                    </a>
                  </div>

                  <div class="menu-section slim-menu-section" v-if="!isVerified">
                    <a :href="route('verification.request')" class="dropdown-item highlight-item slim-menu-item" @click="closeDropdowns">
                      <i class="fas fa-user-check me-3"></i>
                      <div class="menu-item-content slim-menu-content">
                        <span class="item-title slim-menu-title">Get Verified</span>
                      </div>
                    </a>
                  </div>

                  <!-- Logout -->
                  <div class="menu-section slim-menu-section">
                    <a href="#" @click.prevent="logout" class="dropdown-item logout-item slim-menu-item">
                      <i class="fas fa-sign-out-alt me-3"></i>
                      <div class="menu-item-content slim-menu-content">
                        <span class="item-title slim-menu-title">Logout</span>
                      </div>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Suggestions -->
    <div v-if="searchQuery && searchSuggestions.length > 0 && showSearchSuggestions" 
         class="search-suggestions slim-suggestions" @click.stop>
      <div class="container slim-container">
        <div class="suggestions-box slim-suggestions-box">
          <div v-for="suggestion in searchSuggestions" :key="suggestion.id" 
               class="suggestion-item slim-suggestion" @click="selectSuggestion(suggestion)">
            <i :class="suggestion.icon" class="me-3"></i>
            <div class="suggestion-content">
              <div class="suggestion-title slim-suggestion-title">{{ suggestion.name }}</div>
              <div class="suggestion-category slim-suggestion-category">{{ suggestion.category }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage()

const searchQuery = ref('')
const selectedCategory = ref('')
const searchSuggestions = ref([])
const csrfToken = ref(document.querySelector('meta[name="csrf-token"]')?.content || '')
const unreadCount = ref(page.props.auth?.unread_messages || 0)
const wishlistCount = ref(0)
const cartCount = ref(0)

// Dropdown states
const showCategoryDropdown = ref(false)
const showProfileDropdown = ref(false)
const showSearchSuggestions = ref(false)

let searchTimeout = null
let pollInterval = null

// Theme management
const theme = ref(localStorage.getItem('theme') || 'light')

// Initialize theme
const initTheme = () => {
  const savedTheme = localStorage.getItem('theme') || 'light'
  theme.value = savedTheme
  applyTheme(savedTheme)
}

// Apply theme to entire page
const applyTheme = (themeMode) => {
  // Apply to html element
  const html = document.documentElement
  html.setAttribute('data-theme', themeMode)
  
  // Apply to body
  document.body.classList.remove('light-theme', 'dark-theme')
  document.body.classList.add(`${themeMode}-theme`)
  
  // Set CSS variables for entire page
  if (themeMode === 'dark') {
    document.documentElement.style.setProperty('--page-bg-color', '#0f172a')
    document.documentElement.style.setProperty('--page-text-color', '#f1f5f9')
    document.documentElement.style.setProperty('--page-card-bg', '#1e293b')
    document.documentElement.style.setProperty('--page-border-color', '#334155')
    document.documentElement.style.setProperty('--page-shadow-color', 'rgba(0, 0, 0, 0.3)')
  } else {
    document.documentElement.style.setProperty('--page-bg-color', '#ffffff')
    document.documentElement.style.setProperty('--page-text-color', '#1e293b')
    document.documentElement.style.setProperty('--page-card-bg', '#ffffff')
    document.documentElement.style.setProperty('--page-border-color', '#e2e8f0')
    document.documentElement.style.setProperty('--page-shadow-color', 'rgba(0, 0, 0, 0.1)')
  }
}

// Toggle global theme
const toggleGlobalTheme = () => {
  const newTheme = theme.value === 'light' ? 'dark' : 'light'
  theme.value = newTheme
  localStorage.setItem('theme', newTheme)
  applyTheme(newTheme)
  
  // Dispatch event for other components to listen
  window.dispatchEvent(new CustomEvent('theme-changed', {
    detail: { theme: newTheme }
  }))
}

// Session management
const sessionKey = 'user_session'
const sessionTimeout = 24 * 60 * 60 * 1000 // 24 hours

const user = computed(() => page.props.auth?.user || null)
const categories = computed(() => page.props.categories || [])

const isLoggedIn = computed(() => {
  if (!user.value || user.value.id === undefined || user.value.id === null) {
    return false
  }
  
  const sessionData = localStorage.getItem(sessionKey)
  if (!sessionData) {
    redirectToLogin('Session expired')
    return false
  }
  
  try {
    const session = JSON.parse(sessionData)
    const now = Date.now()
    
    if (now - session.timestamp > sessionTimeout) {
      localStorage.removeItem(sessionKey)
      redirectToLogin('Session expired')
      return false
    }
    
    if (now - session.timestamp > 30 * 60 * 1000) {
      updateSession()
    }
    
    return true
  } catch (error) {
    localStorage.removeItem(sessionKey)
    redirectToLogin('Invalid session')
    return false
  }
})

function redirectToLogin(reason = '') {
  if (window.location.pathname === '/login') return
  
  const currentPath = window.location.pathname + window.location.search
  if (currentPath !== '/' && currentPath !== '/login') {
    localStorage.setItem('redirect_after_login', currentPath)
  }
  
  if (typeof router !== 'undefined') {
    router.get('/login')
  } else {
    window.location.href = `/login${reason ? `?reason=${encodeURIComponent(reason)}` : ''}`
  }
}

function initSession() {
  if (user.value && user.value.id) {
    const sessionData = {
      userId: user.value.id,
      timestamp: Date.now(),
      userName: user.value.name || user.value.email
    }
    localStorage.setItem(sessionKey, JSON.stringify(sessionData))
  }
}

function updateSession() {
  if (user.value && user.value.id) {
    const sessionData = {
      userId: user.value.id,
      timestamp: Date.now(),
      userName: user.value.name || user.value.email
    }
    localStorage.setItem(sessionKey, JSON.stringify(sessionData))
  }
}

function clearSession() {
  localStorage.removeItem(sessionKey)
  localStorage.removeItem('redirect_after_login')
}

// Toggle dropdowns
function toggleCategoryDropdown() {
  showCategoryDropdown.value = !showCategoryDropdown.value
  showProfileDropdown.value = false
  showSearchSuggestions.value = false
}

function toggleProfileDropdown() {
  showProfileDropdown.value = !showProfileDropdown.value
  showCategoryDropdown.value = false
  showSearchSuggestions.value = false
}

function closeDropdowns() {
  showCategoryDropdown.value = false
  showProfileDropdown.value = false
  showSearchSuggestions.value = false
}

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
    'Sports': 'fas fa-futbol',
    'Beauty': 'fas fa-spa',
    'Fashion': 'fas fa-tshirt',
    'Jewelry': 'fas fa-gem',
    'Shoes': 'fas fa-shoe-prints',
    'Watches': 'fas fa-clock',
    'Phones': 'fas fa-mobile-alt',
    'Computers': 'fas fa-desktop',
    'Audio': 'fas fa-headphones',
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

// Watch search query for suggestions
watch(searchQuery, (newValue) => {
  if (searchTimeout) clearTimeout(searchTimeout)
  
  if (newValue.trim().length >= 2) {
    searchTimeout = setTimeout(async () => {
      await fetchSearchSuggestions(newValue)
    }, 300)
  } else {
    searchSuggestions.value = []
  }
})

async function fetchSearchSuggestions(query) {
  try {
    const response = await axios.get(`/api/search/suggestions?q=${encodeURIComponent(query)}`)
    searchSuggestions.value = response.data.suggestions || []
  } catch (error) {
    console.error('Error fetching search suggestions:', error)
    searchSuggestions.value = []
  }
}

function selectSuggestion(suggestion) {
  searchQuery.value = suggestion.name
  searchSuggestions.value = []
  showSearchSuggestions.value = false
  performSearch()
}

function selectCategory(categoryName) {
  selectedCategory.value = categoryName
  showCategoryDropdown.value = false
  
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
    searchSuggestions.value = []
    showSearchSuggestions.value = false
    
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

// Fetch unread message count
async function fetchUnreadCount() {
  if (!isLoggedIn.value) return
  
  try {
    const response = await axios.get(route('messages.unread-count'))
    if (response.data && typeof response.data.count === 'number') {
      unreadCount.value = response.data.count
    }
  } catch (error) {
    console.error('Error fetching unread message count:', error)
  }
}

function logout() {
  if (pollInterval) {
    clearInterval(pollInterval)
    pollInterval = null
  }
  
  clearSession()
  
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

onMounted(() => {
  // Initialize theme
  initTheme()
  
  readCategoryFromURL()
  
  // Initialize session
  if (user.value && user.value.id) {
    initSession()
  }
  
  // Load counts
  loadWishlistCount()
  loadCartCount()
  
  // Start polling for unread messages
  if (isLoggedIn.value) {
    fetchUnreadCount()
    pollInterval = setInterval(fetchUnreadCount, 30000)
  }
  
  // Listen for theme changes from other components
  window.addEventListener('theme-changed', (event) => {
    const newTheme = event.detail.theme
    theme.value = newTheme
    applyTheme(newTheme)
  })
  
  // Close dropdowns when clicking outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown-trigger') && 
        !e.target.closest('.search-suggestions') &&
        !e.target.closest('.premium-search-box')) {
      closeDropdowns()
    }
  })
  
  // Close search suggestions when clicking outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.premium-search-box') && !e.target.closest('.search-suggestions')) {
      showSearchSuggestions.value = false
    }
  })
  
  // Cleanup
  onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval)
    window.removeEventListener('theme-changed', () => {})
    document.removeEventListener('click', closeDropdowns)
  })
})
</script>
<style scoped>
/* ===== GLOBAL THEME VARIABLES ===== */
:root {
  --page-bg-color: #ffffff;
  --page-text-color: #1e293b;
  --page-card-bg: #ffffff;
  --page-border-color: #e2e8f0;
  --page-shadow-color: rgba(0, 0, 0, 0.1);
}

[data-theme="dark"] {
  --page-bg-color: #0f172a;
  --page-text-color: #f1f5f9;
  --page-card-bg: #1e293b;
  --page-border-color: #334155;
  --page-shadow-color: rgba(0, 0, 0, 0.3);
}

/* Apply theme to body */
body.light-theme {
  background-color: #ffffff !important;
  color: #1e293b !important;
}

body.dark-theme {
  background-color: #0f172a !important;
  color: #f1f5f9 !important;
}

/* ===== SLIM NAVBAR STYLES ===== */
.premium-nav.slim-nav {
  min-height: 60px !important;
  height: 80px !important;
  padding: 0.25rem 0 !important;
  /* CRITICAL FIX: Allow dropdowns to overflow */
  overflow: visible !important;
}

/* CRITICAL FIX: Ensure navbar container doesn't clip dropdowns */
.navbar {
  overflow: visible !important;
}

.navbar-collapse {
  overflow: visible !important;
}

.container {
  position: relative !important;
}

.slim-container {
  padding-left: 1rem !important;
  padding-right: 1rem !important;
}

/* Brand Logo - Slim */
.slim-brand {
  padding: 0 !important;
}

.slim-logo {
  width: 36px !important;
  height: 36px !important;
  border-radius: 8px !important;
}

.slim-text {
  margin-left: 0.5rem !important;
}

.slim-title {
  font-size: 1.3rem !important;
  letter-spacing: 1px !important;
}

.slim-subtitle {
  font-size: 0.55rem !important;
  letter-spacing: 2px !important;
}

/* Navigation Items - Slim */
.slim-nav-items {
  min-height: 40px !important;
}

/* Categories Dropdown - Slim */
.slim-dropdown {
  margin-right: 1rem !important;
  position: relative !important;
}

/* CRITICAL FIX: Dropdown trigger container */
.dropdown-pure-css {
  position: relative !important;
}

.dropdown-trigger {
  position: relative !important;
}

.slim-category {
  padding: 0.5rem 0.75rem !important;
  font-size: 0.85rem !important;
  min-height: 38px !important;
  position: relative !important;
}

.slim-label {
  font-size: 0.85rem !important;
}

.slim-item {
  padding: 0.5rem 0.75rem !important;
  font-size: 0.85rem !important;
}

/* Search Box - Slim */
.slim-search-container {
  min-height: 40px !important;
  position: relative !important;
}

.slim-search {
  padding: 0.25rem 1rem !important;
  min-height: 38px !important;
  width: 300px !important;
  position: relative !important;
}

.slim-search input {
  font-size: 0.85rem !important;
}

.slim-search-btn {
  width: 32px !important;
  height: 32px !important;
}

/* Dashboard - Slim */
.slim-dashboard {
  min-height: 40px !important;
  position: relative !important;
}

.slim-dock {
  gap: 1rem !important;
  margin-right: 1rem !important;
}

.slim-icon {
  width: 36px !important;
  height: 36px !important;
  font-size: 1rem !important;
}

.slim-badge {
  font-size: 0.65rem !important;
  min-width: 16px !important;
  height: 16px !important;
  top: -4px !important;
  right: -4px !important;
}

/* Auth Buttons - Slim */
.slim-auth {
  gap: 0.75rem !important;
}

.slim-theme {
  width: 34px !important;
  height: 34px !important;
}

.slim-login {
  padding: 0.5rem 1.25rem !important;
  font-size: 0.8rem !important;
}

/* Profile - Slim */
.slim-profile {
  position: relative !important;
}

.slim-profile-trigger {
  padding: 0.25rem 0.5rem !important;
  min-height: 38px !important;
  position: relative !important;
}

.slim-avatar {
  width: 32px !important;
  height: 32px !important;
}

.slim-initials {
  font-size: 1rem !important;
}

.slim-user-info {
  margin-right: 0.5rem !important;
}

.slim-username {
  font-size: 0.85rem !important;
}

.slim-arrow {
  font-size: 0.7rem !important;
}

/* ========== DROPDOWN FIXES ========== */
/* User Menu Dropdown - FIXED */
.slim-menu {
  min-width: 260px !important;
  padding: 1rem !important;
  /* CRITICAL FIX: Position absolutely from the profile dropdown */
  position: absolute !important;
  top: 100% !important;
  right: 0 !important;
  left: auto !important;
  margin-top: 8px !important;
  z-index: 1100 !important; /* Higher than navbar */
  /* Add scroll if needed */
  max-height: 500px !important;
  overflow-y: auto !important;
}

/* Categories Dropdown - FIXED */
.luxury-drop {
  min-width: 220px !important;
  padding: 0.75rem !important;
  /* CRITICAL FIX: Position absolutely from categories dropdown */
  position: absolute !important;
  top: 100% !important;
  left: 0 !important;
  margin-top: 8px !important;
  z-index: 1100 !important; /* Higher than navbar */
  /* Add scroll if needed */
  max-height: 400px !important;
  overflow-y: auto !important;
}

/* Search Suggestions - FIXED */
.slim-suggestions {
  position: absolute !important;
  top: 100% !important;
  left: 0 !important;
  right: 0 !important;
  margin-top: 8px !important;
  z-index: 1100 !important;
}

.slim-suggestions-box {
  max-width: 300px !important;
  padding: 0.5rem 0 !important;
  margin: 0 auto !important;
}

.slim-suggestion {
  padding: 0.5rem 0.75rem !important;
}

.slim-suggestion-title {
  font-size: 0.85rem !important;
}

.slim-suggestion-category {
  font-size: 0.7rem !important;
}

/* Dropdown headers */
.drop-header {
  font-size: 0.9rem !important;
  padding: 0.5rem 0.75rem !important;
  margin-bottom: 0.25rem !important;
}

.slim-header {
  padding-bottom: 1rem !important;
  margin-bottom: 1rem !important;
}

.slim-avatar-large {
  width: 48px !important;
  height: 48px !important;
}

.slim-initials-large {
  font-size: 1.4rem !important;
}

.slim-details h6 {
  font-size: 1rem !important;
}

.slim-email {
  font-size: 0.75rem !important;
}

.slim-menu-section {
  margin-bottom: 0.75rem !important;
}

.slim-section-title {
  font-size: 0.7rem !important;
  padding-left: 0.75rem !important;
  margin-bottom: 0.5rem !important;
}

.slim-menu-item {
  padding: 0.5rem 0.75rem !important;
  font-size: 0.85rem !important;
  margin-bottom: 0.125rem !important;
}

.slim-menu-item i {
  font-size: 1rem !important;
  width: 20px !important;
}

.slim-menu-title {
  font-size: 0.85rem !important;
}

/* Rotate animation for dropdown arrows */
.rotate-180 {
  transform: rotate(180deg) !important;
  transition: transform 0.2s ease !important;
}

/* ===== THEME-AWARE NAVBAR STYLES ===== */
.premium-nav.white-theme {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
  backdrop-filter: blur(10px);
  position: sticky;
  top: 0;
  z-index: 1030;
}

/* Dark theme navbar */
[data-theme="dark"] .premium-nav.white-theme {
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

/* Brand Logo */
.brand-logo-box {
  background: linear-gradient(135deg, #c5a059 0%, #a67c00 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(197, 160, 89, 0.2);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.brand-logo-box:hover {
  transform: rotate(-5deg) scale(1.05);
  box-shadow: 0 8px 25px rgba(197, 160, 89, 0.3);
}

.text-gold {
  color: white;
}

.brand-title {
  color: #1e293b;
  font-weight: 900;
  letter-spacing: 2px;
  background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

[data-theme="dark"] .brand-title {
  background: linear-gradient(135deg, #f1f5f9 0%, #cbd5e1 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.brand-subtitle {
  color: #c5a059;
  font-weight: 600;
  text-transform: uppercase;
}

/* Categories Dropdown */
.category-trigger {
  color: #475569 !important;
  font-weight: 600;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  text-decoration: none !important;
}

[data-theme="dark"] .category-trigger {
  color: #cbd5e1 !important;
  background: rgba(30, 41, 59, 0.8);
  border: 1px solid #334155;
}

.category-trigger:hover {
  background: white;
  border-color: #c5a059;
  color: #c5a059 !important;
  box-shadow: 0 3px 15px rgba(197, 160, 89, 0.15);
}

[data-theme="dark"] .category-trigger:hover {
  background: #1e293b;
  border-color: #c5a059;
  color: #c5a059 !important;
  box-shadow: 0 3px 15px rgba(197, 160, 89, 0.15);
}

.gold-icon {
  color: #c5a059;
}

/* Premium Search Box */
.premium-search-box {
  background: white;
  border: 1.5px solid #e2e8f0;
  border-radius: 50px;
  display: flex;
  align-items: center;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

[data-theme="dark"] .premium-search-box {
  background: #1e293b;
  border: 1.5px solid #334155;
}

.premium-search-box:focus-within {
  border-color: #c5a059;
  box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.1);
  transform: translateY(-1px);
}

[data-theme="dark"] .premium-search-box:focus-within {
  box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.2);
}

.search-icon {
  color: #94a3b8;
  margin-right: 0.75rem;
}

[data-theme="dark"] .search-icon {
  color: #94a3b8;
}

.premium-search-box input {
  background: transparent;
  border: none;
  color: #1e293b;
  width: 100%;
  outline: none;
  font-weight: 500;
}

[data-theme="dark"] .premium-search-box input {
  color: #f1f5f9;
}

.premium-search-box input::placeholder {
  color: #94a3b8;
  font-weight: 400;
}

.search-btn-gold {
  background: linear-gradient(135deg, #c5a059 0%, #a67c00 100%);
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: white;
  margin-left: 0.5rem;
}

.search-btn-gold:hover {
  transform: scale(1.1) rotate(5deg);
  box-shadow: 0 3px 10px rgba(197, 160, 89, 0.3);
}

/* Search Suggestions */
.search-suggestions {
  background: white;
  border-top: 1px solid #e2e8f0;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  animation: slideDown 0.2s ease;
}

[data-theme="dark"] .search-suggestions {
  background: #1e293b;
  border-top: 1px solid #334155;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.suggestions-box {
  margin: 0 auto;
}

.suggestion-item {
  padding: 0.75rem 1.25rem;
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: all 0.2s ease;
  border-radius: 8px;
  margin: 0.125rem 0;
}

[data-theme="dark"] .suggestion-item {
  color: #f1f5f9;
}

.suggestion-item:hover {
  background: #f8fafc;
}

[data-theme="dark"] .suggestion-item:hover {
  background: #0f172a;
}

.suggestion-item i {
  color: #c5a059;
}

/* Action Dock */
.action-dock {
  display: flex;
}

.dock-icon {
  position: relative;
  color: #64748b;
  transition: all 0.3s ease;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  /* border-radius: 8px;
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid #050505; */
   /* background: transparent !important;
  border: none !important;
  width: auto !important;
  height: auto !important;
  padding: 0.25rem !important; */
}

[data-theme="dark"] .dock-icon {
  color: #cbd5e1;
  background: rgba(30, 41, 59, 0.8);
  border: 1px solid #334155;
}

.dock-icon:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

[data-theme="dark"] .dock-icon:hover {
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.dock-icon:nth-child(1):hover {
  color: #ef4444;
  border-color: #ef4444;
  background: rgba(239, 68, 68, 0.1);
}

.dock-icon:nth-child(2):hover {
  color: #3b82f6;
  border-color: #3b82f6;
  background: rgba(59, 130, 246, 0.1);
}

.dock-icon:nth-child(3):hover {
  color: #10b981;
  border-color: #10b981;
  background: rgba(16, 185, 129, 0.1);
}

.notification-badge {
  position: absolute;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  font-weight: 700;
  border-radius: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid white;
  box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
}

[data-theme="dark"] .notification-badge {
  border: 2px solid #1e293b;
}

/* Auth Buttons */
.auth-buttons {
  display: flex;
  align-items: center;
}

.theme-switch {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #64748b;
  transition: all 0.3s ease;
}

[data-theme="dark"] .theme-switch {
  background: #1e293b;
  border: 1px solid #334155;
  color: #cbd5e1;
}

.theme-switch:hover {
  color: #c5a059;
  border-color: #c5a059;
  transform: rotate(15deg);
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}

[data-theme="dark"] .theme-switch:hover {
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
}

.btn-login {
  background: linear-gradient(135deg, #c5a059 0%, #a67c00 100%);
  color: white;
  border: none;
  border-radius: 50px;
  font-weight: 700;
  text-decoration: none;
  display: flex;
  align-items: center;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 15px rgba(197, 160, 89, 0.2);
}

.btn-login:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(197, 160, 89, 0.3);
  color: white;
}

/* Profile Dropdown */
.profile-trigger {
  display: flex;
  align-items: center;
  cursor: pointer;
  /* border-radius: 0px; */
  transition: all 0.3s ease;
  background: white;
  /* border: 1px solid #e2e8f0; */
}

[data-theme="dark"] .profile-trigger {
  background: #1e293b;
  border: 1px solid #334155;
}

.profile-trigger:hover {
  border-color: #c5a059;
  box-shadow: 0 3px 15px rgba(197, 160, 89, 0.15);
}

.user-avatar {
  position: relative;
  border-radius: 50%;
  overflow: hidden;
  margin-right: 0.75rem;
  border: 2px solid white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

[data-theme="dark"] .user-avatar {
  border: 2px solid #1e293b;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-initials {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #c5a059 0%, #a67c00 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
}

.user-info {
  display: flex;
  flex-direction: column;
  margin-right: 0.75rem;
}

.user-name {
  color: #1e293b;
  font-weight: 600;
}

[data-theme="dark"] .user-name {
  color: #f1f5f9;
}

.user-status {
  color: #64748b;
  font-weight: 500;
}

[data-theme="dark"] .user-status {
  color: #94a3b8;
}

.dropdown-arrow {
  color: #94a3b8;
  transition: transform 0.3s ease;
}

/* User Menu Dropdown */
.user-menu {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2) !important;
  animation: fadeInUp 0.2s ease;
}

[data-theme="dark"] .user-menu {
  background: #1e293b;
  border: 1px solid #334155;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4) !important;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.user-profile-header {
  display: flex;
  align-items: center;
  border-bottom: 1px solid #e2e8f0;
}

[data-theme="dark"] .user-profile-header {
  border-bottom: 1px solid #334155;
}

.user-avatar-large {
  border-radius: 50%;
  overflow: hidden;
  margin-right: 1rem;
  border: 3px solid white;
  box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
}

[data-theme="dark"] .user-avatar-large {
  border: 3px solid #1e293b;
  box-shadow: 0 3px 15px rgba(0, 0, 0, 0.3);
}

.avatar-initials-large {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #c5a059 0%, #a67c00 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
}

.user-details h6 {
  color: #1e293b;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

[data-theme="dark"] .user-details h6 {
  color: #f1f5f9;
}

.email-text {
  color: #64748b;
  display: block;
  margin-bottom: 0.5rem;
}

[data-theme="dark"] .email-text {
  color: #94a3b8;
}

.verification-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 600;
  gap: 0.25rem;
}

.verification-badge.verified {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

[data-theme="dark"] .verification-badge.verified {
  background: rgba(16, 185, 129, 0.2);
  color: #10b981;
}

.verification-badge:not(.verified) {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

[data-theme="dark"] .verification-badge:not(.verified) {
  background: rgba(245, 158, 11, 0.2);
  color: #f59e0b;
}

/* Menu Sections */
.menu-section {
  margin-bottom: 1rem;
}

.section-title {
  color: #94a3b8;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
}

[data-theme="dark"] .section-title {
  color: #64748b;
}

.dropdown-item {
  display: flex;
  align-items: center;
  color: #475569;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s ease;
  border: none;
  background: transparent;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

[data-theme="dark"] .dropdown-item {
  color: #cbd5e1;
}

.dropdown-item:hover {
  background: #f8fafc;
  color: #c5a059;
  transform: translateX(3px);
}

[data-theme="dark"] .dropdown-item:hover {
  background: #0f172a;
  color: #c5a059;
}

.dropdown-item i {
  width: 20px;
}

.menu-item-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.item-title {
  font-weight: 600;
  color: #1e293b;
}

[data-theme="dark"] .item-title {
  color: #f1f5f9;
}

.item-subtitle {
  color: #64748b;
  margin-top: 0.125rem;
}

[data-theme="dark"] .item-subtitle {
  color: #94a3b8;
}

.highlight-item {
  background: linear-gradient(135deg, rgba(197, 160, 89, 0.1) 0%, rgba(197, 160, 89, 0.05) 100%);
  border: 1px solid rgba(197, 160, 89, 0.2);
  color: #c5a059;
}

[data-theme="dark"] .highlight-item {
  background: linear-gradient(135deg, rgba(197, 160, 89, 0.2) 0%, rgba(197, 160, 89, 0.1) 100%);
  border: 1px solid rgba(197, 160, 89, 0.3);
}

.highlight-item:hover {
  background: linear-gradient(135deg, rgba(197, 160, 89, 0.2) 0%, rgba(197, 160, 89, 0.1) 100%);
  border-color: #c5a059;
}

[data-theme="dark"] .highlight-item:hover {
  background: linear-gradient(135deg, rgba(197, 160, 89, 0.3) 0%, rgba(197, 160, 89, 0.2) 100%);
}

.logout-item {
  color: #ef4444 !important;
}

.logout-item:hover {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444 !important;
}

[data-theme="dark"] .logout-item:hover {
  background: rgba(239, 68, 68, 0.2);
}

/* Categories Dropdown */
.luxury-drop {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2) !important;
}

[data-theme="dark"] .luxury-drop {
  background: #1e293b;
  border: 1px solid #334155;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4) !important;
}

.drop-header {
  color: #1e293b;
  font-weight: 700;
  margin-bottom: 0.5rem;
  border-bottom: 1px solid #e2e8f0;
}

[data-theme="dark"] .drop-header {
  color: #f1f5f9;
  border-bottom: 1px solid #334155;
}

.dropdown-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 0.5rem 0;
}

[data-theme="dark"] .dropdown-divider {
  background: #334155;
}

/* Mobile Toggle */
.custom-toggler {
  border: none;
  background: transparent;
  color: #c5a059;
}

/* ========== RESPONSIVE FIXES ========== */
@media (max-width: 991px) {
  .premium-nav.slim-nav {
    min-height: 70px !important;
    padding: 0.5rem 0 !important;
  }
  
  .slim-search {
    width: 100% !important;
    margin: 0.5rem 0 !important;
  }
  
  .action-dock {
    margin: 0.5rem 0 !important;
    justify-content: center !important;
  }
  
  .dashboard-wrapper {
    flex-direction: column;
    align-items: stretch;
  }
  
  .profile-trigger {
    justify-content: center;
    margin-top: 0.5rem !important;
  }
  
  /* Fixed positioning for mobile dropdowns */
  .user-menu, .luxury-drop, .search-suggestions {
    position: fixed !important;
    top: 70px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    width: 90% !important;
    max-width: 320px !important;
    max-height: 70vh !important;
    overflow-y: auto !important;
    margin-top: 0 !important;
  }
  
  .user-menu {
    max-width: 280px !important;
  }
  
  .luxury-drop {
    max-width: 250px !important;
  }
  
  .search-suggestions {
    max-width: 350px !important;
  }
}

@media (max-width: 768px) {
  .slim-title {
    font-size: 1.2rem !important;
  }
  
  .slim-subtitle {
    font-size: 0.5rem !important;
  }
  
  .category-trigger .category-label {
    display: none !important;
  }
  
  .category-trigger i:first-child {
    margin-right: 0 !important;
  }
  
  .slim-username {
    display: none !important;
  }
  
  .slim-user-info {
    display: none !important;
  }
  
  .slim-search {
    width: 250px !important;
  }
}

@media (max-width: 576px) {
  .container {
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
  }
  
  .brand-text-group {
    display: none !important;
  }
  
  .slim-dock {
    gap: 0.75rem !important;
    margin-right: 0.75rem !important;
  }
  
  .slim-auth {
    gap: 0.5rem !important;
  }
  
  .slim-login {
    padding: 0.4rem 1rem !important;
    font-size: 0.75rem !important;
  }
  
  .slim-search {
    width: 200px !important;
  }
}

/* Custom scrollbar for dropdowns */
.luxury-drop::-webkit-scrollbar,
.user-menu::-webkit-scrollbar {
  width: 6px;
}

.luxury-drop::-webkit-scrollbar-track,
.user-menu::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

[data-theme="dark"] .luxury-drop::-webkit-scrollbar-track,
[data-theme="dark"] .user-menu::-webkit-scrollbar-track {
  background: #334155;
}

.luxury-drop::-webkit-scrollbar-thumb,
.user-menu::-webkit-scrollbar-thumb {
  background: #c5a059;
  border-radius: 3px;
}

.luxury-drop::-webkit-scrollbar-thumb:hover,
.user-menu::-webkit-scrollbar-thumb:hover {
  background: #a67c00;
}
</style>