<template>
  <div class="main-app-container">
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" @click="toggleMobileMenu">
      <span class="menu-line"></span>
      <span class="menu-line"></span>
      <span class="menu-line"></span>
    </button>

    <nav class="navbar" :class="{ 'mobile-open': isMobileMenuOpen }">
      <!-- Brand/Logo on Left - Top Left Position -->
      <div class="nav-brand">
        <Link href="/" class="brand-link">
          <div class="brand-logo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path d="M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375z"/>
              <path fill-rule="evenodd" d="M3.087 9l.54 9.176A3 3 0 006.62 21h10.757a3 3 0 002.995-2.824L20.913 9H3.087zm6.163 3.75A.75.75 0 0110 12h4a.75.75 0 010 1.5h-4a.75.75 0 01-.75-.75z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="brand-text">
            <h1 class="brand-title">E-Shop</h1>
            <p class="brand-tagline">Premium Shopping</p>
          </div>
        </Link>
      </div>

      <!-- Center Section - Home Icon & Categories -->
      <div class="nav-center">
        <div class="nav-center-content">
          <!-- Home Link -->
          <Link href="/" class="nav-item home-center" :class="{ active: $page.url === '/' }">
            <i class="nav-icon fas fa-home"></i>
            <span class="nav-text">Home</span>
          </Link>

          <!-- Categories Dropdown -->
          <div class="categories-dropdown-container" @mouseleave="closeDropdowns">
            <button 
              class="nav-item categories-btn" 
              @click="toggleCategoriesDropdown"
              :class="{ active: $page.url.startsWith('/products') || $page.url.includes('category=') }"
            >
              <i class="nav-icon fas fa-th-large"></i>
              <span class="nav-text">Categories</span>
              <i class="dropdown-arrow fas fa-chevron-down"></i>
            </button>

            <!-- Categories Dropdown Menu -->
            <transition name="dropdown">
              <div v-if="isCategoriesDropdownOpen" class="dropdown-menu categories-dropdown">
                <div class="dropdown-header">
                  <h4>All Categories</h4>
                  <p class="dropdown-subtitle">Browse by category</p>
                </div>
                
                <div class="dropdown-divider"></div>
                
                <!-- Categories List -->
                <div v-if="categories.length > 0" class="categories-list">
                  <Link 
                    href="/" 
                    class="dropdown-item category-item"
                    @click="closeDropdowns"
                  >
                    <i class="fas fa-th-large"></i>
                    <span>All Products</span>
                    <span class="category-count">{{ totalProducts || 0 }}</span>
                  </Link>
                  
                  <Link
                    v-for="category in categories" 
                    :key="category.id"
                    :href="`/?category=${category.slug || category.name}`"
                    class="dropdown-item category-item"
                    @click="closeDropdowns"
                  >
                    <i :class="getCategoryIcon(category.name)"></i>
                    <span>{{ category.name }}</span>
                    <span class="category-count">{{ category.products_count || 0 }}</span>
                  </Link>
                </div>
                
                <!-- Loading State -->
                <div v-else-if="loadingCategories" class="loading-categories">
                  <i class="fas fa-spinner fa-spin"></i>
                  <span>Loading categories...</span>
                </div>
                
                <!-- Empty State -->
                <div v-else class="empty-categories">
                  <i class="fas fa-folder-open"></i>
                  <span>No categories found</span>
                </div>
                
                <div class="dropdown-divider"></div>
                
                <!-- View All Categories -->
                <Link 
                  href="/products" 
                  class="dropdown-item view-all"
                  @click="closeDropdowns"
                >
                  <i class="fas fa-arrow-right"></i>
                  <span>View All Products</span>
                </Link>
              </div>
            </transition>
          </div>
        </div>
      </div>

      <!-- Right Side Navigation -->
      <div class="nav-right">
        <!-- Login/Register (Only show when NOT logged in) -->
        <div v-if="!isLoggedIn" class="auth-buttons">
          <Link href="/login" class="nav-item login-btn" :class="{ active: $page.url === '/login' }">
            <i class="nav-icon fas fa-sign-in-alt"></i>
            <span class="nav-text">Login</span>
          </Link>
        </div>

        <!-- User Menu (Only show when logged in) -->
        <div v-else class="utility-nav">
          <!-- Cart with badge -->
          <div class="utility-item cart-container" @click="toggleCartPreview">
            <div class="utility-icon">
              <i class="fas fa-shopping-cart"></i>
              <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
            </div>
            <span class="utility-text">Cart</span>
            
            <!-- Cart Preview Dropdown -->
            <div v-if="showCartPreview" class="cart-preview" @mouseleave="showCartPreview = false">
              <div class="cart-preview-header">
                <h4>Shopping Cart</h4>
                <span class="cart-count">{{ cartCount }} items</span>
              </div>
              <div v-if="cartItems.length > 0" class="cart-preview-items">
                <div v-for="item in cartItems" :key="item.id" class="cart-preview-item">
                  <img :src="item.image || '/images/placeholder.jpg'" :alt="item.name" class="cart-item-image">
                  <div class="cart-item-details">
                    <h5>{{ item.name }}</h5>
                    <p class="cart-item-price">${{ item.price.toFixed(2) }} x {{ item.quantity }}</p>
                  </div>
                </div>
              </div>
              <div v-else class="cart-empty">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
              </div>
              <div v-if="cartItems.length > 0" class="cart-preview-footer">
                <div class="cart-total">
                  <span>Total:</span>
                  <span class="total-amount">${{ cartTotal.toFixed(2) }}</span>
                </div>
                <Link href="/cart" class="btn-view-cart" @click="showCartPreview = false">
                  View Cart
                </Link>
              </div>
            </div>
          </div>

          <!-- Wishlist -->
          <Link href="/wishlist" class="utility-item" :class="{ active: $page.url === '/wishlist' }">
            <div class="utility-icon">
              <i class="fas fa-heart"></i>
              <span v-if="wishlistCount > 0" class="wishlist-badge">{{ wishlistCount }}</span>
            </div>
            <span class="utility-text">Wishlist</span>
          </Link>

          <!-- Messages -->
          <Link href="/messages" class="utility-item" :class="{ active: $page.url === '/messages' }">
            <div class="utility-icon">
              <i class="fas fa-envelope"></i>
              <span v-if="unreadMessages > 0" class="message-badge">{{ unreadMessages }}</span>
            </div>
            <span class="utility-text">Messages</span>
          </Link>

          <!-- User Menu Dropdown -->
          <div class="utility-item dropdown-container" @mouseleave="closeDropdowns">
            <div class="user-avatar" @click="toggleUserDropdown">
              <!-- Show uploaded profile picture or first letter -->
              <img :src="userProfilePicture" :alt="userName" class="avatar-image" v-if="userProfilePicture">
              <div class="avatar-placeholder" v-else>
                {{ userName.charAt(0).toUpperCase() }}
              </div>
            </div>
            
            <!-- User Dropdown -->
            <transition name="dropdown">
              <div v-if="isUserDropdownOpen" class="dropdown-menu user-dropdown">
                <div class="dropdown-header">
                  <!-- Clickable Avatar for Upload -->
                  <div class="user-avatar large clickable-avatar" @click="triggerFileInput">
                    <!-- Hidden file input -->
                    <input type="file" 
                           ref="fileInput"
                           @change="handleFileUpload"
                           accept="image/*"
                           class="hidden">
                    
                    <!-- Profile Picture or Placeholder -->
                    <img :src="userProfilePicture" :alt="userName" class="avatar-image" v-if="userProfilePicture">
                    <div class="avatar-placeholder" v-else>
                      {{ userName.charAt(0).toUpperCase() }}
                    </div>
                    
                    <!-- Upload Overlay -->
                    <div class="upload-overlay">
                      <i class="fas fa-camera"></i>
                      <span>Change Photo</span>
                    </div>
                  </div>
                  
                  <div class="user-info">
                    <h4>{{ userName }}</h4>
                    <p class="user-email">{{ userEmail }}</p>
                    
                    <!-- Upload Progress -->
                    <div v-if="uploading" class="upload-progress mt-2">
                      <div class="progress-bar">
                        <div class="progress-fill" :style="{ width: uploadProgress + '%' }"></div>
                      </div>
                      <span class="progress-text text-xs">{{ uploadProgress }}%</span>
                    </div>
                    
                    <!-- Upload Success Message -->
                    <div v-if="uploadSuccess" class="upload-success mt-2">
                      <span class="text-green-600 text-xs">✓ Profile picture updated!</span>
                    </div>
                    
                    <!-- Upload Error Message -->
                    <div v-if="uploadError" class="upload-error mt-2">
                      <span class="text-red-600 text-xs">{{ uploadError }}</span>
                    </div>
                  </div>
                </div>
                <div class="dropdown-divider"></div>
                
                <!-- User Menu Items -->
                 <Link href="/profile" class="dropdown-item" @click="closeDropdowns" :class="{ active: $page.url === '/profile' }">
                  <i class="fas fa-user-cog"></i>
                  <span>My Profile</span>
                </Link>
                
                <Link href="/orders" class="dropdown-item" @click="closeDropdowns" :class="{ active: $page.url === '/orders' }">
                  <i class="fas fa-shopping-bag"></i>
                  <span>Orders</span>
                </Link>
                
                <Link href="/products" class="dropdown-item" @click="closeDropdowns" :class="{ active: $page.url.startsWith('/products') }">
                  <i class="fas fa-box"></i>
                  <span>Products</span>
                </Link>
                
                <Link href="/SettingsPage" class="dropdown-item" @click="closeDropdowns" :class="{ active: $page.url === '/settingsPage' }">
                  <i class="fas fa-cog"></i>
                  <span>Settings</span>
                </Link>
                
                <!-- <Link href="/wishlist" class="dropdown-item" @click="closeDropdowns" :class="{ active: $page.url === '/wishlist' }">
                  <i class="fas fa-heart"></i>
                  <span>Wishlist</span>
                </Link> -->
                
                <div class="dropdown-divider"></div>
                
                <!-- Logout Button -->
                <button @click="logout" class="dropdown-item logout">
                  <i class="fas fa-sign-out-alt"></i>
                  <span>Logout</span>
                </button>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Overlay -->
    <div v-if="isMobileMenuOpen" class="mobile-overlay" @click="toggleMobileMenu"></div>
  </div>
</template>

<script>
import { Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'

export default {
  name: 'AppNavbar',
  
  components: {
    Link
  },
  
  setup() {
    const page = usePage();
    return { page };
  },
  
  props: {
    cartCount: {
      type: Number,
      default: 0
    },
    wishlistCount: {
      type: Number,
      default: 0
    },
    // Remove categories prop since we'll fetch them directly
    totalProducts: {
      type: Number,
      default: 0
    }
  },
  
  data() {
    return {
      isMobileMenuOpen: false,
      isUserDropdownOpen: false,
      isCategoriesDropdownOpen: false,
      showCartPreview: false,
      cartItems: [],
      unreadMessages: 3,
      
      // Categories state
      loadingCategories: false,
      categories: [], // Store fetched categories here
      
      // Upload state
      uploading: false,
      uploadProgress: 0,
      uploadSuccess: false,
      uploadError: null
    };
  },
  
  computed: {
    // Get user from Inertia's shared auth data
    user() {
      return this.page.props.auth?.user || null;
    },
    
    // Check if user is logged in
    isLoggedIn() {
      return this.user && this.user.id !== undefined && this.user.id !== null;
    },
    
    // Get user's full name or use email
    userName() {
      if (!this.user) return 'User';
      
      if (this.user.full_name) {
        return this.user.full_name;
      }
      if (this.user.name) {
        return this.user.name;
      }
      return this.user.email?.split('@')[0] || 'User';
    },
    
    // Get user's email
    userEmail() {
      return this.user?.email || '';
    },
    
    // Get user's profile picture
    userProfilePicture() {
      if (!this.user?.profile_picture) return null;
      
      // Check if it's a full URL or relative path
      if (this.user.profile_picture.startsWith('http') || this.user.profile_picture.startsWith('/')) {
        return this.user.profile_picture;
      }
      return `/storage/${this.user.profile_picture}`;
    },
    
    // Calculate cart total
    cartTotal() {
      return this.cartItems.reduce((total, item) => {
        return total + (item.price * item.quantity);
      }, 0);
    }
  },
  
  mounted() {
    console.log('Navbar mounted - User:', this.user);
    console.log('Is logged in:', this.isLoggedIn);
    
    // Load sample cart data
    this.loadCartData();
    
    // Always fetch categories on mount
    this.fetchCategories();
  },
  
  methods: {
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
      if (this.isMobileMenuOpen) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    },
    
    toggleUserDropdown() {
      this.isUserDropdownOpen = !this.isUserDropdownOpen;
      this.isCategoriesDropdownOpen = false;
      this.showCartPreview = false;
      
      // Reset upload messages when opening dropdown
      if (this.isUserDropdownOpen) {
        setTimeout(() => {
          this.uploadSuccess = false;
          this.uploadError = null;
        }, 3000);
      }
    },
    
    toggleCategoriesDropdown() {
      this.isCategoriesDropdownOpen = !this.isCategoriesDropdownOpen;
      this.isUserDropdownOpen = false;
      this.showCartPreview = false;
      
      // If opening and no categories loaded, fetch them
      if (this.isCategoriesDropdownOpen && this.categories.length === 0 && !this.loadingCategories) {
        this.fetchCategories();
      }
    },
    
    toggleCartPreview() {
      this.showCartPreview = !this.showCartPreview;
      this.isUserDropdownOpen = false;
      this.isCategoriesDropdownOpen = false;
    },
    
    closeDropdowns() {
      if (!this.isMobileMenuOpen) {
        this.isUserDropdownOpen = false;
        this.isCategoriesDropdownOpen = false;
        this.showCartPreview = false;
      }
    },
    
    // Fetch categories from Laravel API
    async fetchCategories() {
      // Don't fetch if already loading
      if (this.loadingCategories) return;
      
      this.loadingCategories = true;
      try {
        // Try to fetch from Laravel API endpoint
        const response = await axios.get('/api/categories');
        console.log('Fetched categories:', response.data);
        
        // Store the fetched categories
        this.categories = response.data.categories || response.data || [];
        
      } catch (error) {
        console.error('Error fetching categories:', error);
        
        // If API endpoint doesn't exist, you may need to create it
        // For now, show sample data
        this.categories = [
          { id: 1, name: 'Electronics', slug: 'electronics', products_count: 24 },
          { id: 2, name: 'Clothing', slug: 'clothing', products_count: 42 },
          { id: 3, name: 'Books', slug: 'books', products_count: 18 },
          { id: 4, name: 'Home & Garden', slug: 'home-garden', products_count: 31 },
          { id: 5, name: 'Sports', slug: 'sports', products_count: 15 },
          { id: 6, name: 'Furniture', slug: 'furniture', products_count: 22 },
          { id: 7, name: 'Wearables', slug: 'wearables', products_count: 12 },
          { id: 8, name: 'Beauty', slug: 'beauty', products_count: 28 }
        ];
        
        console.log('Using sample categories:', this.categories);
      } finally {
        this.loadingCategories = false;
      }
    },
    
    // Get category icon
    getCategoryIcon(categoryName) {
      const iconMap = {
        'Electronics': 'fas fa-laptop',
        'Clothing': 'fas fa-tshirt',
        'Books': 'fas fa-book',
        'Home & Garden': 'fas fa-home',
        'Sports': 'fas fa-futbol',
        'Furniture': 'fas fa-couch',
        'Wearables': 'fas fa-clock',
        'Fashion': 'fas fa-tshirt',
        'Home & Kitchen': 'fas fa-home',
        'Accessories': 'fas fa-glasses',
        'Beauty': 'fas fa-spa',
        'Toys': 'fas fa-gamepad',
        'Grocery': 'fas fa-shopping-basket',
        'Health': 'fas fa-heartbeat',
        'Automotive': 'fas fa-car'
      };
      
      return iconMap[categoryName] || 'fas fa-box';
    },
    
    // Logout method
    logout() {
      this.$inertia.post('/logout');
      this.closeDropdowns();
    },
    
    loadCartData() {
      // Sample cart data - replace with actual data from your API
      this.cartItems = [
        { id: 1, name: 'Wireless Headphones', price: 99.99, quantity: 1, image: '/images/headphones.jpg' },
        { id: 2, name: 'Smart Watch', price: 249.99, quantity: 1, image: '/images/smartwatch.jpg' },
        { id: 3, name: 'USB-C Cable', price: 19.99, quantity: 2, image: '/images/cable.jpg' }
      ];
    },
    
    // Trigger file input click
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    
    // Handle file upload
    async handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) return;
      
      // Validate file
      if (!file.type.startsWith('image/')) {
        this.uploadError = 'Please select an image file';
        return;
      }
      
      if (file.size > 5 * 1024 * 1024) { // 5MB limit
        this.uploadError = 'File size must be less than 5MB';
        return;
      }
      
      // Reset states
      this.uploading = true;
      this.uploadProgress = 0;
      this.uploadSuccess = false;
      this.uploadError = null;
      
      // Create FormData
      const formData = new FormData();
      formData.append('profile_picture', file);
      
      try {
        // Upload the file
        const response = await axios.post('/api/upload-profile-picture', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          onUploadProgress: (progressEvent) => {
            if (progressEvent.total) {
              this.uploadProgress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            }
          }
        });
        
        // Update success state
        this.uploading = false;
        this.uploadSuccess = true;
        this.uploadProgress = 100;
        
        // Update user data in Inertia page props
        if (response.data.user) {
          // Update the user data in Inertia's shared state
          this.page.props.auth.user.profile_picture = response.data.user.profile_picture;
          
          // Force a small re-render to show new image
          setTimeout(() => {
            this.$forceUpdate();
          }, 100);
        }
        
        // Auto hide success message after 3 seconds
        setTimeout(() => {
          this.uploadSuccess = false;
        }, 3000);
        
      } catch (error) {
        console.error('Upload error:', error);
        this.uploading = false;
        this.uploadError = error.response?.data?.message || 'Upload failed. Please try again.';
      } finally {
        // Reset file input
        event.target.value = '';
      }
    }
  }
};
</script>

<style scoped>
/* Modern Variables - Updated to match hero images background */
:root {
  --primary-color: #8B5CF6; /* Purple to match hero images */
  --primary-dark: #7C3AED;
  --primary-light: #A78BFA;
  --secondary-color: #10B981; /* Emerald */
  --accent-color: #F59E0B; /* Amber */
  --danger-color: #EF4444; /* Red */
  --dark-bg: #0F172A; /* Slate 900 */
  --light-bg: #F8FAFC; /* Slate 50 */
  --card-bg: rgba(255, 255, 255, 0.95); /* Semi-transparent white */
  --text-primary: #1E293B; /* Slate 800 */
  --text-secondary: #64748B; /* Slate 500 */
  --border-color: rgba(226, 232, 240, 0.7); /* Semi-transparent */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  --radius-sm: 0.375rem;
  --radius-md: 0.5rem;
  --radius-lg: 0.75rem;
  --radius-xl: 1rem;
  --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-normal: 300ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* Base Styles with gradient background */
.main-app-container {
  position: sticky;
  top: 0;
  z-index: 50;
  background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  box-shadow: var(--shadow-md);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

/* Navbar Layout with transparent background */
.navbar {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  padding: 0 2rem;
  height: 5rem;
  max-width: 1440px;
  margin: 0 auto;
  background: transparent;
}

/* Brand/Logo on Left - Top Left Position */
.nav-brand {
  display: flex;
  align-items: center;
  justify-self: start;
  margin-right: auto;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
  color: var(--text-primary);
  transition: var(--transition-fast);
}

.brand-link:hover {
  transform: translateY(-1px);
}

.brand-logo {
  width: 2.5rem;
  height: 2.5rem;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem;
  box-shadow: var(--shadow-md);
}

.brand-logo svg {
  width: 100%;
  height: 100%;
  fill: white;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-title {
  font-size: 1.5rem;
  font-weight: 800;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0;
  line-height: 1;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.brand-tagline {
  font-size: 0.7rem;
  color: var(--text-secondary);
  margin: 0.125rem 0 0 0;
  font-weight: 500;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

/* Center Section - Home & Categories */
.nav-center {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
}

.nav-center-content {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.home-center {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  text-decoration: none;
  color: var(--text-secondary);
  border-radius: var(--radius-lg);
  transition: var(--transition-normal);
  font-weight: 500;
  background: rgba(255, 255, 255, 0.9);
  border: 2px solid transparent;
  box-shadow: var(--shadow-sm);
  backdrop-filter: blur(10px);
}

.home-center:hover {
  color: var(--primary-color);
  background: rgba(255, 255, 255, 0.95);
  border-color: var(--primary-color);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.home-center.active {
  color: var(--primary-color);
  background: rgba(139, 92, 246, 0.15);
  border-color: var(--primary-color);
}

/* Categories Dropdown */
.categories-dropdown-container {
  position: relative;
}

.categories-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  text-decoration: none;
  color: var(--text-secondary);
  border-radius: var(--radius-lg);
  transition: var(--transition-normal);
  font-weight: 500;
  background: rgba(255, 255, 255, 0.9);
  border: 2px solid transparent;
  cursor: pointer;
  font-family: inherit;
  font-size: inherit;
  box-shadow: var(--shadow-sm);
  backdrop-filter: blur(10px);
}

.categories-btn:hover {
  color: var(--primary-color);
  background: rgba(255, 255, 255, 0.95);
  border-color: var(--primary-color);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.categories-btn.active {
  color: var(--primary-color);
  background: rgba(139, 92, 246, 0.15);
  border-color: var(--primary-color);
}

.dropdown-arrow {
  font-size: 0.875rem;
  transition: transform var(--transition-fast);
  margin-left: 0.25rem;
}

.categories-btn:hover .dropdown-arrow {
  transform: translateY(1px);
}

/* Categories Dropdown Menu */
.categories-dropdown {
  position: absolute;
  top: calc(100% + 0.5rem);
  left: 50%;
  transform: translateX(-50%);
  background: rgba(255, 255, 255, 0.98);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-xl);
  min-width: 20rem;
  max-width: 24rem;
  z-index: 40;
  overflow: hidden;
  border: 1px solid rgba(226, 232, 240, 0.5);
  backdrop-filter: blur(20px);
}

.dropdown-header {
  padding: 1.25rem;
  background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(16, 185, 129, 0.1));
}

.dropdown-header h4 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
}

.dropdown-subtitle {
  margin: 0.25rem 0 0 0;
  font-size: 0.85rem;
  color: var(--text-secondary);
}

/* ... Rest of your existing styles remain the same ... */

/* Keep all your existing CSS styles below, they will work with the updated variables */

/* Right Side Navigation */
.nav-right {
  display: flex;
  align-items: center;
  justify-self: end;
  gap: 1rem;
  margin-left: auto;
}

/* Login Button (when not logged in) */
.auth-buttons {
  display: flex;
  gap: 1rem;
}

.login-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  text-decoration: none;
  color: var(--primary-color);
  border: 2px solid var(--primary-color);
  border-radius: var(--radius-lg);
  font-weight: 500;
  transition: var(--transition-normal);
  background: white;
}

.login-btn:hover {
  background: var(--primary-color);
  color: white;
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.login-btn.active {
  background: var(--primary-color);
  color: white;
}

/* Utility Navigation (when logged in) */
.utility-nav {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.utility-item {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
  padding: 0.75rem;
  border-radius: var(--radius-lg);
  text-decoration: none;
  color: var(--text-secondary);
  transition: var(--transition-fast);
  cursor: pointer;
  min-width: 4.5rem;
}

.utility-item:hover {
  background: rgba(99, 102, 241, 0.05);
  color: var(--primary-color);
  transform: translateY(-1px);
}

.utility-icon {
  position: relative;
  width: 2.5rem;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(99, 102, 241, 0.1);
  border-radius: var(--radius-md);
  font-size: 1.125rem;
  transition: var(--transition-fast);
}

.utility-item:hover .utility-icon {
  background: var(--primary-color);
  color: white;
  transform: scale(1.05);
}

.utility-text {
  font-size: 0.75rem;
  font-weight: 500;
  white-space: nowrap;
}

/* Badges */
.cart-badge,
.wishlist-badge,
.message-badge {
  position: absolute;
  top: -0.25rem;
  right: -0.25rem;
  background: var(--danger-color);
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  min-width: 1.25rem;
  height: 1.25rem;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 0.375rem;
  border: 2px solid var(--card-bg);
}

.wishlist-badge {
  background: var(--danger-color);
}

.message-badge {
  background: var(--secondary-color);
}

/* User Avatar */
.user-avatar {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  overflow: hidden;
  cursor: pointer;
  transition: var(--transition-normal);
  border: 2px solid transparent;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  padding: 2px;
  position: relative;
}

.user-avatar:hover {
  transform: scale(1.05);
  border-color: var(--primary-color);
}

.user-avatar.large {
  width: 3.5rem;
  height: 3.5rem;
  cursor: pointer;
  position: relative;
}

/* Clickable avatar for upload */
.clickable-avatar {
  position: relative;
  cursor: pointer;
}

.clickable-avatar:hover .upload-overlay {
  opacity: 1;
}

.upload-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  opacity: 0;
  transition: opacity var(--transition-normal);
}

.upload-overlay i {
  font-size: 1.2rem;
  margin-bottom: 0.25rem;
}

.upload-overlay span {
  font-size: 0.7rem;
  font-weight: 500;
}

.avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid white;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  color: var(--primary-color);
  font-weight: 600;
  font-size: 1.2rem;
  border-radius: 50%;
  border: 2px solid white;
}

.user-avatar.large .avatar-placeholder {
  font-size: 1.5rem;
}

/* Upload Progress */
.upload-progress {
  width: 100%;
}

.progress-bar {
  width: 100%;
  height: 4px;
  background: var(--border-color);
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 2px;
}

.progress-fill {
  height: 100%;
  background: var(--primary-color);
  transition: width 0.3s ease;
}

.progress-text {
  color: var(--text-secondary);
  font-weight: 500;
}

.upload-success {
  color: var(--secondary-color);
}

.upload-error {
  color: var(--danger-color);
}

/* Dropdown Menu */
.dropdown-container {
  position: relative;
}

.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity var(--transition-fast), transform var(--transition-fast);
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-0.5rem);
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  background: var(--card-bg);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-xl);
  min-width: 18rem;
  z-index: 40;
  overflow: hidden;
  border: 1px solid var(--border-color);
}

.user-dropdown .dropdown-header {
  padding: 1.25rem;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(16, 185, 129, 0.1));
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-info {
  flex: 1;
}

.user-info h4 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
}

.user-email {
  margin: 0.25rem 0 0 0;
  font-size: 0.85rem;
  color: var(--text-secondary);
  word-break: break-all;
}

.dropdown-divider {
  height: 1px;
  background: var(--border-color);
  margin: 0.5rem 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  text-decoration: none;
  color: var(--text-secondary);
  transition: var(--transition-fast);
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  font-family: inherit;
  font-size: 0.9rem;
  cursor: pointer;
}

.dropdown-item:hover {
  background: rgba(99, 102, 241, 0.05);
  color: var(--primary-color);
}

.dropdown-item i {
  width: 1rem;
  text-align: center;
  color: var(--text-secondary);
}

.dropdown-item:hover i {
  color: var(--primary-color);
}

.dropdown-item.logout {
  color: var(--danger-color);
}

.dropdown-item.logout i {
  color: var(--danger-color);
}

.dropdown-item.logout:hover {
  background: rgba(239, 68, 68, 0.05);
}

/* Cart Preview */
.cart-preview {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  width: 22rem;
  background: var(--card-bg);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-xl);
  z-index: 40;
  overflow: hidden;
  border: 1px solid var(--border-color);
}

.cart-preview-header {
  padding: 1rem;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(16, 185, 129, 0.1));
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-preview-header h4 {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-primary);
}

.cart-count {
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--text-secondary);
  background: white;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
}

.cart-preview-items {
  max-height: 20rem;
  overflow-y: auto;
  padding: 0.5rem;
}

.cart-preview-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  border-radius: var(--radius-md);
  transition: var(--transition-fast);
}

.cart-preview-item:hover {
  background: rgba(99, 102, 241, 0.05);
}

.cart-item-image {
  width: 3rem;
  height: 3rem;
  object-fit: cover;
  border-radius: var(--radius-md);
  background: var(--border-color);
}

.cart-item-details {
  flex: 1;
}

.cart-item-details h5 {
  margin: 0 0 0.25rem 0;
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--text-primary);
}

.cart-item-price {
  margin: 0;
  font-size: 0.85rem;
  color: var(--text-secondary);
}

.cart-empty {
  padding: 2rem;
  text-align: center;
  color: var(--text-secondary);
}

.cart-empty i {
  font-size: 2rem;
  margin-bottom: 0.5rem;
  opacity: 0.5;
}

.cart-empty p {
  margin: 0;
  font-size: 0.9rem;
}

.cart-preview-footer {
  padding: 1rem;
  border-top: 1px solid var(--border-color);
}

.cart-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  font-weight: 600;
}

.total-amount {
  color: var(--primary-color);
  font-size: 1.25rem;
}

.btn-view-cart {
  display: block;
  width: 100%;
  padding: 0.75rem;
  background: var(--primary-color);
  color: white;
  text-decoration: none;
  text-align: center;
  border-radius: var(--radius-md);
  font-weight: 500;
  transition: var(--transition-normal);
}

.btn-view-cart:hover {
  background: var(--primary-dark);
  transform: translateY(-1px);
}

/* Mobile Overlay */
.mobile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 40;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .navbar {
    padding: 0 1.5rem;
  }
  
  .home-center .nav-text,
  .categories-btn .nav-text {
    display: none;
  }
  
  .utility-text {
    display: none;
  }
  
  .utility-item {
    min-width: auto;
    padding: 0.5rem;
  }
  
  .auth-buttons .nav-text {
    display: none;
  }
  
  .login-btn {
    padding: 0.75rem;
  }
}

@media (max-width: 768px) {
  .mobile-menu-toggle {
    display: flex;
  }
  
  .navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    justify-content: flex-start;
    padding: 6rem 2rem 2rem 2rem;
    background: var(--card-bg);
    transform: translateX(-100%);
    transition: transform var(--transition-normal);
    overflow-y: auto;
    z-index: 50;
    grid-template-columns: 1fr;
  }
  
  .navbar.mobile-open {
    transform: translateX(0);
  }
  
  .nav-brand {
    order: 1;
    justify-content: center;
    margin: 0 0 2rem 0;
  }
  
  .nav-center {
    order: 2;
    margin-bottom: 2rem;
    width: 100%;
  }
  
  .nav-center-content {
    flex-direction: column;
    width: 100%;
    gap: 1rem;
  }
  
  .home-center,
  .categories-btn {
    width: 100%;
    justify-content: center;
  }
  
  .home-center .nav-text,
  .categories-btn .nav-text {
    display: block;
  }
  
  .categories-dropdown {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    max-width: none;
    border-radius: 0;
    transform: none;
    left: 0;
  }
  
  .nav-right {
    order: 3;
    flex-direction: column;
    width: 100%;
    gap: 1rem;
    margin: 0;
  }
  
  .auth-buttons {
    width: 100%;
  }
  
  .login-btn {
    width: 100%;
    justify-content: center;
  }
  
  .auth-buttons .nav-text {
    display: block;
  }
  
  .utility-nav {
    flex-direction: column;
    width: 100%;
  }
  
  .utility-item {
    flex-direction: row;
    justify-content: flex-start;
    gap: 1rem;
    padding: 1rem;
    width: 100%;
  }
  
  .utility-text {
    display: block;
  }
  
  .cart-preview,
  .dropdown-menu {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    max-width: 24rem;
    border-radius: 0;
  }
}

@media (max-width: 480px) {
  .navbar {
    padding: 6rem 1rem 1rem 1rem;
  }
  
  .brand-title {
    font-size: 1.25rem;
  }
  
  .brand-logo {
    width: 2rem;
    height: 2rem;
  }
  
  .cart-preview,
  .dropdown-menu {
    max-width: 100%;
  }
  
  .dropdown-header {
    flex-direction: column;
    text-align: center;
  }
  
  .user-avatar.large {
    margin-bottom: 1rem;
  }
}
</style>