<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid">
      <!-- Brand/Logo -->
      <Link href="/" class="navbar-brand d-flex align-items-center">
        <div class="brand-logo me-2">
          <i class="fas fa-store fa-lg text-primary"></i>
        </div>
        <div>
          <span class="fw-bold fs-4">E-Shop</span>
          <small class="d-block text-muted">Premium Shopping</small>
        </div>
      </Link>

      <!-- Mobile Toggle -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navigation Content -->
      <div class="collapse navbar-collapse" id="navbarContent">
        <!-- Center Navigation -->
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <!-- Categories Dropdown -->
          <li class="nav-item dropdown me-4">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-th-large me-1"></i> Categories
            </a>
            <ul class="dropdown-menu dropdown-menu-lg p-3">
              <li>
                <h6 class="dropdown-header fw-bold">All Categories</h6>
              </li>
              <li><hr class="dropdown-divider"></li>
              
              <!-- All Products -->
              <li>
                <button @click="filterByCategory('')" class="dropdown-item d-flex justify-content-between align-items-center py-2 w-100 border-0 bg-transparent">
                  <span><i class="fas fa-th-large me-2 text-primary"></i> All Products</span>
                </button>
              </li>
              
              <!-- Category List -->
              <li v-for="category in categories" :key="category.id">
                <button 
                  @click="filterByCategory(category.name)"
                  class="dropdown-item d-flex justify-content-between align-items-center py-2 w-100 border-0 bg-transparent"
                >
                  <span>
                    <i :class="getCategoryIcon(category.name)" class="me-2 text-muted"></i>
                    {{ category.name }}
                  </span>
                </button>
              </li>
            </ul>
          </li>

          <!-- Search Bar -->
          <li class="nav-item search-container">
            <div class="input-group search-group">
              <input
                type="text"
                v-model="searchQuery"
                @keyup.enter="performSearch"
                class="form-control search-input"
                placeholder="Search products..."
                aria-label="Search products"
              />
              <button @click="performSearch" class="btn btn-primary search-btn">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </li>
        </ul>

        <!-- Right Navigation -->
        <div class="d-flex align-items-center">
          <!-- Not Logged In -->
          <div v-if="!isLoggedIn" class="nav-buttons">
            <Link href="/login" class="btn btn-outline-primary me-2">
              <i class="fas fa-sign-in-alt me-1"></i> Login
            </Link>
          </div>

          <!-- Logged In User -->
          <div v-else class="d-flex align-items-center gap-3">
            <!-- Messages Icon (disabled for now) -->
            <div class="position-relative me-3">
              <a href="#" class="text-dark" @click.prevent="showComingSoon('Messages')">
                <i class="fas fa-envelope fa-lg"></i>
              </a>
            </div>

            <!-- User Profile Dropdown -->
            <div class="dropdown">
              <button 
                class="btn btn-link p-0 d-flex align-items-center text-decoration-none" 
                type="button" 
                data-bs-toggle="dropdown"
                aria-expanded="false"
                style="background: none; border: none;"
              >
                <!-- User Avatar -->
                <div class="position-relative user-avatar-container me-2">
                  <div class="user-avatar">
                    <img v-if="userProfilePicture" :src="userProfilePicture" :alt="userName" class="rounded-circle avatar-img">
                    <div v-else class="rounded-circle bg-primary text-white avatar-placeholder d-flex align-items-center justify-content-center">
                      {{ userName.charAt(0).toUpperCase() }}
                    </div>
                  </div>
                </div>
                
                <!-- User Name -->
                <span class="fw-bold text-dark d-none d-lg-inline">{{ userName }}</span>
                <i class="fas fa-chevron-down ms-1 text-muted small"></i>
              </button>
              
              <!-- Dropdown Menu -->
              <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="min-width: 250px;">
                <!-- Header with Profile Picture -->
                <li class="dropdown-item-text">
                  <div class="d-flex align-items-center p-2">
                    <!-- Avatar -->
                    <div class="position-relative me-3">
                      <div class="user-avatar-lg">
                        <img v-if="userProfilePicture" :src="userProfilePicture" :alt="userName" class="rounded-circle avatar-img-lg">
                        <div v-else class="rounded-circle bg-primary text-white avatar-placeholder-lg d-flex align-items-center justify-content-center">
                          {{ userName.charAt(0).toUpperCase() }}
                        </div>
                      </div>
                    </div>
                    
                    <!-- User Info -->
                    <div class="flex-grow-1">
                      <h6 class="mb-0 fw-bold">{{ userName }}</h6>
                      <!-- <small class="text-muted">{{ user.email }}</small> -->
                      <div class="mt-1">
                        <span v-if="isVerified" class="badge bg-success">
                          <i class="fas fa-check-circle me-1"></i>Verified
                        </span>
                        <span v-else class="badge bg-warning">
                          <i class="fas fa-clock me-1"></i>Not Verified
                        </span>
                      </div>
                    </div>
                  </div>
                </li>
                
                <li><hr class="dropdown-divider"></li>
                
                <!-- My Orders -->
                <li>
                  <Link :href="route('orders.customer')" class="dropdown-item d-flex align-items-center py-2">
                    <i class="fas fa-shopping-bag me-3 text-info"></i>
                    <div>
                      <div class="fw-medium">My Orders</div>
                    </div>
                  </Link>
                </li>
                
                <!-- Manage Orders -->
                <li>
                  <Link :href="route('orders.vendor')" class="dropdown-item d-flex align-items-center py-2">
                    <i class="fas fa-clipboard-list me-3 text-warning"></i>
                    <div>
                      <div class="fw-medium">Manage Orders</div>
                    </div>
                  </Link>
                </li>
                
                <!-- Products -->
                <li>
                  <Link :href="route('products.index')" class="dropdown-item d-flex align-items-center py-2">
                    <i class="fas fa-box me-3 text-success"></i>
                    <div>
                      <div class="fw-medium">Products</div>
                    </div>
                  </Link>
                </li>

                <!-- Get Verified -->
                <li>
                  <Link :href="route('verification.request')" class="dropdown-item d-flex align-items-center py-2">
                    <i class="fas fa-user-check me-3 text-success"></i>
                    <div>
                      <div class="fw-medium">Get Verified</div>
                    </div>
                  </Link>
                </li>
                
                <!-- Settings -->
                <li>
                  <Link :href="route('settings.page')" class="dropdown-item d-flex align-items-center py-2">
                    <i class="fas fa-cog me-3 text-dark"></i>
                    <div>
                      <div class="fw-medium">Settings</div>
                    </div>
                  </Link>
                </li>
                
                <li><hr class="dropdown-divider"></li>
                
                <!-- Logout -->
                <li>
                  <button @click="logout" class="dropdown-item d-flex align-items-center py-2 text-danger">
                    <i class="fas fa-sign-out-alt me-3"></i>
                    <div>
                      <div class="fw-medium">Logout</div>
                    </div>
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { Link, usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

export default {
  name: 'AppNavbar',
  
  components: {
    Link
  },
  
  data() {
    return {
      categories: [],
      searchQuery: '',
      unreadMessages: 0
    };
  },
  
  computed: {
    user() {
      return this.$page.props.auth?.user || null;
    },
    
    isLoggedIn() {
      return this.user && this.user.id !== undefined && this.user.id !== null;
    },
    
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
    
    isVerified() {
      if (!this.user) return false;
      return this.user.email_verified_at !== null || 
             this.user.is_verified === true ||
             this.user.verified_at !== null;
    },
    
    userProfilePicture() {
      if (!this.user) return null;
      
      if (this.user.profile_picture) {
        return this.formatProfilePictureUrl(this.user.profile_picture);
      }
      
      if (this.user.profile_picture_url) {
        return this.formatProfilePictureUrl(this.user.profile_picture_url);
      }
      
      return null;
    }
  },
  
  mounted() {
    // Only fetch categories
    this.fetchCategories();
  },
  
  methods: {
    formatProfilePictureUrl(path) {
      if (!path) return null;
      
      if (path.startsWith('http') || path.startsWith('/')) {
        return path;
      }
      
      if (path.startsWith('storage/')) {
        return path.replace('storage/', '/storage/');
      }
      
      return `/storage/${path}`;
    },
    
    async fetchCategories() {
      try {
        const response = await axios.get('/api/categories');
        this.categories = response.data.categories || response.data || [];
      } catch (error) {
        console.error('Error fetching categories:', error);
        // Fallback categories
        this.categories = [
          { id: 1, name: 'Electronics', slug: 'electronics'},
          { id: 2, name: 'Clothing', slug: 'clothing'},
          { id: 3, name: 'Books', slug: 'books' },
          { id: 4, name: 'Home & Garden', slug: 'home-garden' },
          { id: 5, name: 'Sports', slug: 'sports' },
        ];
      }
    },
    
    getCategoryIcon(categoryName) {
      const iconMap = {
        'Electronics': 'fas fa-laptop',
        'Clothing': 'fas fa-tshirt',
        'Books': 'fas fa-book',
        'Home & Garden': 'fas fa-home',
        'Sports': 'fas fa-futbol',
        'Furniture': 'fas fa-couch',
        'Fashion': 'fas fa-tshirt',
        'Home & Kitchen': 'fas fa-home',
        'Accessories': 'fas fa-glasses',
        'Beauty': 'fas fa-spa',
      };
      return iconMap[categoryName] || 'fas fa-box';
    },
    
    filterByCategory(categoryName) {
      // Emit event for homepage to handle
      window.dispatchEvent(new CustomEvent('navbar-category-select', {
        detail: { categoryName }
      }));
      
      this.closeMobileDropdown();
    },
    
    performSearch() {
      if (this.searchQuery.trim()) {
        // Emit event for homepage to handle
        window.dispatchEvent(new CustomEvent('navbar-search', {
          detail: { searchTerm: this.searchQuery.trim() }
        }));
        
        this.searchQuery = '';
        this.closeMobileDropdown();
      }
    },
    
    closeMobileDropdown() {
      if (window.innerWidth < 992) {
        const navbarCollapse = document.getElementById('navbarContent');
        if (navbarCollapse && navbarCollapse.classList.contains('show')) {
          const bsCollapse = new bootstrap.Collapse(navbarCollapse);
          bsCollapse.hide();
        }
      }
    },
    
    logout() {
      router.post('/logout');
    },
    
    showNotification(message, type = 'success') {
      const toastContainer = document.createElement('div');
      toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
      toastContainer.style.zIndex = '1055';
      
      const toast = document.createElement('div');
      toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
      toast.setAttribute('role', 'alert');
      toast.setAttribute('aria-live', 'assertive');
      toast.setAttribute('aria-atomic', 'true');
      
      toast.innerHTML = `
        <div class="d-flex">
          <div class="toast-body">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      `;
      
      toastContainer.appendChild(toast);
      document.body.appendChild(toastContainer);
      
      const bsToast = new bootstrap.Toast(toast);
      bsToast.show();
      
      toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toastContainer);
      });
    },
    
    showComingSoon(feature) {
      this.showNotification(`${feature} feature coming soon!`, 'info');
    }
  }
};
</script>

<style scoped>

/* Brand Logo */
.brand-logo {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #55575b 0%, #dde2ef 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white !important;
}

/* Navbar Customization */
.navbar {
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid rgba(0,0,0,.05);
}

/* Search Bar */
.search-container {
  width: 350px;
}

.search-group {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.search-input {
  border: none;
  padding: 0.5rem 1.25rem;
  border-radius: 20px 0 0 20px;
}

.search-input:focus {
  box-shadow: none;
  border-color: #86b7fe;
}

.search-btn {
  border-radius: 0 20px 20px 0;
  padding: 0.5rem 1.25rem;
  border: none;
}

/* User Avatar */
.user-avatar-container {
  position: relative;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  position: relative;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border: 2px solid #e2e8f0;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  font-size: 1.1rem;
  font-weight: 600;
  border: 2px solid #e2e8f0;
}

/* Camera overlay on avatar */
.camera-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.3s ease;
  border-radius: 50%;
}

.user-avatar:hover .camera-overlay {
  opacity: 1;
}

/* Large avatar in dropdown */
.user-avatar-lg {
  position: relative;
  width: 60px;
  height: 60px;
}

.avatar-img-lg {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border: 3px solid #e2e8f0;
  border-radius: 50%;
}

.avatar-placeholder-lg {
  width: 100%;
  height: 100%;
  font-size: 1.5rem;
  font-weight: 600;
  border: 3px solid #e2e8f0;
  border-radius: 50%;
}

/* Upload button on avatar */
.avatar-upload-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 24px;
  height: 24px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid white;
}

.avatar-upload-btn:hover {
  transform: scale(1.1);
}

/* Dropdown Customization */
.dropdown-menu {
  border: 1px solid rgba(0,0,0,.08) !important;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1) !important;
}

.dropdown-item {
  border-radius: 6px;
  transition: all 0.2s ease;
  margin: 2px 0;
}

.dropdown-item:hover {
  background-color: rgba(59, 130, 246, 0.08);
}

.dropdown-item-text {
  cursor: default;
}

/* Profile dropdown button */
.btn-link:hover {
  text-decoration: none;
}

/* Responsive Adjustments */
@media (max-width: 991.98px) {
  .navbar-collapse {
    background: white;
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-top: 1rem;
  }
  
  .search-container {
    width: 100%;
    margin: 1rem 0;
  }
  
  .nav-buttons {
    display: flex;
    gap: 0.5rem;
    width: 100%;
  }
  
  .nav-buttons .btn {
    flex: 1;
  }
  
  .dropdown {
    width: 100%;
  }
  
  .dropdown-toggle {
    width: 100%;
    justify-content: center;
    padding: 0.5rem 0;
  }
  
  .d-flex.gap-3 {
    flex-wrap: wrap;
    justify-content: center;
    width: 100%;
  }
}

@media (max-width: 576px) {
  .navbar-brand .fs-4 {
    font-size: 1.25rem !important;
  }
  
  .navbar-brand small {
    font-size: 0.7rem !important;
  }
  
  .user-avatar {
    width: 35px;
    height: 35px;
  }
  
  .avatar-placeholder {
    font-size: 1rem;
  }
  
  .user-avatar-lg {
    width: 50px;
    height: 50px;
  }
  
  .avatar-placeholder-lg {
    font-size: 1.3rem;
  }
}
</style>