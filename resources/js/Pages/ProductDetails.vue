<template>
  <AppLayout>
    <div class="product-details-page" :class="currentThemeClass">
      <!-- Background Pattern -->
      <div class="background-pattern"></div>

      <div class="container py-5">
        <!-- Product Details -->
        <div class="row">
          <!-- Left Column: Images -->
          <div class="col-lg-6 mb-4">
            <!-- Image Slider -->
            <div class="product-images-slider theme-card">
              <!-- Main Image -->
              <div class="main-image-container position-relative mb-3 theme-main-image">
                <img :src="currentImage.image_url" 
                     :alt="product.name" 
                     class="img-fluid rounded"
                     style="max-height: 400px; width: 100%; object-fit: contain;">
                
                <!-- Discount Badge -->
                <div v-if="isDiscountActive" 
                     class="position-absolute top-0 start-0 m-3">
                  <span class="badge discount-badge">
                    <i class="fas fa-fire me-1"></i>{{ discountPercent }}% OFF
                  </span>
                </div>
              </div>
              
              <!-- Thumbnails -->
              <div v-if="allImages.length > 1" class="thumbnails-container">
                <div class="d-flex justify-content-center flex-wrap gap-2">
                  <button v-for="(image, index) in allImages" 
                          :key="index"
                          @click="selectImageAndPurchase(index)"
                          class="thumbnail-btn p-0 border rounded"
                          :class="{ active: currentIndex === index, 'selected-purchase': selectedImageIndex === index }">
                    <img :src="image.image_url" 
                         :alt="`Thumbnail ${index + 1}`"
                         class="img-fluid rounded theme-thumbnail"
                         style="width: 60px; height: 60px; object-fit: cover;">
                    <!-- Purchase selection indicator -->
                    <div v-if="selectedImageIndex === index" class="purchase-indicator">
                      <i class="fas fa-shopping-cart"></i>
                    </div>
                  </button>
                </div>
              </div>
              
              <!-- Navigation Arrows -->
              <div v-if="allImages.length > 1" class="navigation-arrows">
                <button @click="prevImage" 
                        class="btn theme-btn rounded-circle arrow-btn"
                        :disabled="currentIndex === 0">
                  <i class="fas fa-chevron-left"></i>
                </button>
                <button @click="nextImage" 
                        class="btn theme-btn rounded-circle arrow-btn"
                        :disabled="currentIndex === allImages.length - 1">
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
              
              <!-- Image Counter -->
              <div v-if="allImages.length > 1" class="image-counter text-center mt-3">
                <span class="badge theme-counter-badge">
                  Image {{ currentIndex + 1 }} of {{ allImages.length }}
                </span>
              </div>
            </div>
            
            <!-- Image Selection for Purchase (for customers) -->
            <div v-if="!isOwner && allImages.length > 1" class="theme-card mt-4">
              <div class="card-header theme-card-header">
                <h6 class="mb-0 theme-text">
                  <i class="fas fa-image me-2 text-primary"></i>Select Image for Purchase
                </h6>
              </div>
              <div class="card-body">
                <div class="row g-2">
                  <div v-for="(image, index) in allImages" 
                       :key="index"
                       class="col-4 col-sm-3">
                    <div class="selection-card text-center"
                         :class="{ 'selected': selectedImageIndex === index }"
                         @click="selectImageForPurchase(index)">
                      <img :src="image.image_url" 
                           :alt="`Option ${index + 1}`"
                           class="img-fluid rounded mb-2 theme-selection-image"
                           style="height: 80px; width: 100%; object-fit: cover;">
                      <div class="form-check">
                        <input class="form-check-input theme-checkbox" 
                               type="radio" 
                               :id="`image-${index}`"
                               :checked="selectedImageIndex === index">
                        <label class="form-check-label small theme-text" :for="`image-${index}`">
                          {{ image.is_main ? 'Main' : `Option ${index + 1}` }}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="mt-3 text-center">
                  <small class="theme-text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Selected image will be used for your order
                  </small>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right Column: Product Info -->
          <div class="col-lg-6">
            <div class="product-info theme-card h-100">
              <div class="card-body">
                <!-- Product Header -->
                <div class="mb-4">
                  <h1 class="h2 fw-bold mb-2 theme-text">{{ product.name }}</h1>
                  
                  <!-- Seller Info -->
                  <div class="d-flex align-items-center mb-3">
                    <div class="d-flex align-items-center me-3">
                      <i class="fas fa-store theme-text-muted me-2"></i>
                      <span class="theme-text-muted">Seller:</span>
                      <!-- <span class="ms-1 fw-medium theme-text">{{ product.user?.name || 'Unknown' }}</span> -->
                    </div>
                    
                    <!-- Verified Badge -->
                    <div v-if="product.user?.is_verified" class="badge bg-success">
                      <i class="fas fa-check-circle me-1"></i>Verified Seller
                    </div>
                  </div>
                  
                  <!-- Category & Type -->
                  <div class="d-flex flex-wrap gap-2 mb-3">
                    <!-- <span class="badge theme-badge-primary">
                      <i class="fas fa-tag me-1"></i>{{ product.category?.name || 'Uncategorized' }}
                    </span> -->
                    <!-- <span class="badge theme-badge-info" 
                          :class="product.product_type === 'onstock' ? 'theme-badge-info' : 'theme-badge-warning'">
                      <i class="fas" :class="product.product_type === 'onstock' ? 'fa-layer-group' : 'fa-box'"></i>
                      {{ product.product_type === 'onstock' ? 'On-Stock' : 'One-Time' }}
                    </span> -->
                    <span class="badge" :class="getStockBadgeClass">
                      <i class="fas" :class="getStockIcon"></i>
                      {{ product.stock }} {{ product.stock === 1 ? 'unit' : 'units' }} available
                    </span>
                  </div>
                </div>
                
                <!-- Price Section -->
                <div class="price-section mb-4 p-3 rounded theme-price-section">
                  <div v-if="isDiscountActive" class="discount-price mb-2">
                    <div class="d-flex align-items-center">
                      <span class="text-decoration-line-through h4 me-3 theme-text-muted">
                        {{ formatPrice(product.price) }} Birr
                      </span>
                      <span class="h1 fw-bold text-danger">
                        {{ formatPrice(discountedPrice) }} Birr
                      </span>
                    </div>
                    <div class="mt-2">
                      <span class="badge bg-success">
                        <i class="fas fa-piggy-bank me-1"></i>
                        Save {{ formatPrice(savingsAmount) }} Birr
                      </span>
                      <span class="badge theme-discount-badge ms-2">
                        <i class="fas fa-tag me-1"></i>{{ discountName }}
                      </span>
                      <div v-if="discountEndDate" class="small theme-text-muted mt-1">
                        <i class="fas fa-clock me-1"></i>
                        Discount ends: {{ discountEndDate }}
                      </div>
                    </div>
                  </div>
                  <div v-else>
                    <div class="regular-price">
                      <span class="h1 fw-bold text-primary">
                        {{ formatPrice(product.price) }} Birr
                      </span>
                    </div>
                  </div>
                </div>
                
                <!-- Description -->
                <div class="description-section mb-4">
                  <h5 class="fw-bold mb-3 theme-text">
                    <i class="fas fa-align-left me-2 text-primary"></i>Description
                  </h5>
                  <div class="theme-description-bg p-3 rounded border">
                    <p class="theme-text" style="white-space: pre-line;">
                      {{ product.description || 'No description available.' }}
                    </p>
                  </div>
                </div>
                
                <!-- Product Details Table -->
                <!-- <div class="details-table mb-4">
                  <h5 class="fw-bold mb-3 theme-text">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Product Details
                  </h5>
                  <div class="table-responsive">
                    <table class="table table-bordered theme-table">
                      <tbody>
                        <tr>
                          <th class="theme-table-header">Product ID</th>
                          <td class="theme-text">{{ product.product_id }}</td>
                        </tr>
                        <tr>
                          <th class="theme-table-header">Reference</th>
                          <td>
                            <span class="badge theme-badge-secondary">
                              {{ product.reference || 'N/A' }}
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <th class="theme-table-header">Status</th>
                          <td>
                            <span :class="getStatusBadgeClass">
                              {{ product.status_label }}
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <th class="theme-table-header">Sold Count</th>
                          <td class="theme-text">{{ product.sold_count || 0 }} units</td>
                        </tr>
                        <tr>
                          <th class="theme-table-header">Added Date</th>
                          <td class="theme-text">{{ formatDate(product.created_at) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div> -->
                
                <!-- Action Buttons -->
                <div class="action-buttons mt-4 pt-4 border-top theme-border">
                  <div class="row g-3">
                    <!-- Owner Actions -->
                    <div v-if="isOwner" class="col-12">
                      <div class="d-grid gap-2">
                        <button @click="manageImages" 
                                class="btn btn-primary btn-lg theme-primary-btn">
                          <i class="fas fa-images me-2"></i>Add Additional Images For This Product
                        </button>
                      </div>
                    </div>
                    
                    <!-- Customer Actions -->
                    <div v-else class="col-12">
                      <div class="d-grid gap-2">
                        <button @click="buyNow" 
                                :disabled="product.stock <= 0"
                                class="btn btn-danger btn-lg theme-buy-btn"
                                :class="{ 'theme-discount-btn': isDiscountActive }">
                          <i class="fas fa-shopping-cart me-2"></i>
                          {{ isDiscountActive ? 'Buy with Discount' : 'Buy Now' }}
                        </button>
                        
                        <!-- Selected Image Info -->
                        <div v-if="allImages.length > 1 && selectedImageIndex !== null" 
                             class="alert alert-info mt-3 mb-0 theme-alert">
                          <i class="fas fa-image me-2"></i>
                          You have selected image option {{ selectedImageIndex + 1 }} for purchase
                        </div>
                        
                        <div v-if="product.stock <= 0" class="alert alert-warning mt-3 mb-0 theme-alert">
                          <i class="fas fa-exclamation-triangle me-2"></i>
                          This product is currently out of stock
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Related Products (Optional) -->
        <div v-if="!isOwner" class="row mt-5">
          <div class="col-12">
            <div class="theme-card">
              <div class="card-header theme-card-header">
                <h5 class="mb-0 theme-text">
                  <i class="fas fa-th-large me-2 text-primary"></i>More from this Seller
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-12 text-center py-4">
                    <p class="text-muted mb-0 theme-text-muted">No other products from this seller</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Toast Notification -->
      <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055">
        <div class="toast align-items-center" 
             :class="[`toast-${notification.type}`, { show: notification.show }]" 
             role="alert">
          <div class="d-flex">
            <div class="toast-body d-flex align-items-center">
              <i :class="notification.icon" class="me-2"></i>
              {{ notification.message }}
            </div>
            <button type="button" 
                    class="btn-close btn-close-white me-2 m-auto" 
                    @click="hideNotification"></button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  product: Object,
  isOwner: Boolean,
  allImages: Array,
  selectedImageId: String
});

// Theme Management - Synced with Navbar
const theme = ref(localStorage.getItem('theme') || 'light');
const currentThemeClass = computed(() => `${theme.value}-theme`);

// Listen to theme changes from navbar
const handleThemeChange = (event) => {
  const newTheme = event.detail.theme;
  theme.value = newTheme;
  localStorage.setItem('theme', newTheme);
  document.documentElement.setAttribute('data-theme', newTheme);
  
  // Remove all theme classes and add the new one
  document.body.className = document.body.className
    .replace('light-theme', '')
    .replace('dark-theme', '')
    .trim();
  document.body.classList.add(`${newTheme}-theme`);
};

// Refs
const currentIndex = ref(0);
const selectedImageIndex = ref(props.selectedImageId ? 
  props.allImages.findIndex(img => img.id === parseInt(props.selectedImageId)) : 0);
const currentImage = computed(() => props.allImages[currentIndex.value] || {});

// Computed Properties
const getStockBadgeClass = computed(() => {
  if (props.product.stock <= 0) return 'badge bg-danger';
  if (props.product.stock < 10) return 'badge bg-warning';
  return 'badge bg-success';
});

const getStockIcon = computed(() => {
  if (props.product.stock <= 0) return 'fa-times-circle';
  if (props.product.stock < 10) return 'fa-exclamation-triangle';
  return 'fa-check-circle';
});

const getStatusBadgeClass = computed(() => {
  switch (props.product.status) {
    case 'active': return 'badge bg-success';
    case 'inactive': return 'badge bg-secondary';
    case 'out_of_stock': return 'badge bg-danger';
    case 'draft': return 'badge bg-warning';
    default: return 'badge bg-info';
  }
});

// Discount calculations
const discountPercent = computed(() => {
  if (!props.product.discount || props.product.discount.status !== 'active') return 0;
  const discountAmount = parseFloat(props.product.discount.discount_amount) || 0;
  return discountAmount;
});

const discountedPrice = computed(() => {
  if (!props.product.discount || props.product.discount.status !== 'active') {
    return parseFloat(props.product.price) || 0;
  }
  const originalPrice = parseFloat(props.product.price) || 0;
  const discountPercentValue = discountPercent.value;
  return originalPrice * (1 - discountPercentValue / 100);
});

const savingsAmount = computed(() => {
  if (!props.product.discount || props.product.discount.status !== 'active') return 0;
  const originalPrice = parseFloat(props.product.price) || 0;
  const discounted = discountedPrice.value;
  return originalPrice - discounted;
});

const discountName = computed(() => {
  if (!props.product.discount) return '';
  if (typeof props.product.discount === 'object' && props.product.discount.name) {
    return props.product.discount.name;
  }
  if (discountPercent.value > 0) {
    return `${discountPercent.value}% Discount`;
  }
  return 'Special Discount';
});

const discountEndDate = computed(() => {
  if (!props.product.discount || !props.product.discount.end_date) return '';
  const date = new Date(props.product.discount.end_date);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
});

const isDiscountActive = computed(() => {
  return props.product.discount && 
         props.product.discount.status === 'active' && 
         discountPercent.value > 0;
});

// Notification
const notification = ref({
  show: false,
  message: '',
  type: 'success',
  icon: 'fas fa-check-circle'
});

// Methods
const selectImage = (index) => {
  currentIndex.value = index;
};

// NEW METHOD: Select image and set it for purchase automatically
const selectImageAndPurchase = (index) => {
  selectImage(index);
  selectImageForPurchase(index);
  
  // Show notification for customer
  if (!props.isOwner) {
    showNotification(`Selected image ${index + 1} for purchase`, 'success', 'fas fa-check-circle');
  }
};

const selectImageForPurchase = (index) => {
  selectedImageIndex.value = index;
  currentIndex.value = index;
};

const prevImage = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--;
    // Also update purchase selection if not owner
    if (!props.isOwner && selectedImageIndex.value !== null) {
      selectImageForPurchase(currentIndex.value);
    }
  }
};

const nextImage = () => {
  if (currentIndex.value < props.allImages.length - 1) {
    currentIndex.value++;
    // Also update purchase selection if not owner
    if (!props.isOwner && selectedImageIndex.value !== null) {
      selectImageForPurchase(currentIndex.value);
    }
  }
};

const formatPrice = (price) => {
  const num = parseFloat(price) || 0;
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(num);
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const editProduct = () => {
  router.visit(`/products/${props.product.product_id}/edit`);
};

const manageImages = () => {
  router.visit(`/products/${props.product.product_id}/add-images`);
};

const buyNow = () => {
  if (props.product.stock <= 0) {
    showNotification('Product is out of stock', 'warning', 'fas fa-exclamation-triangle');
    return;
  }
  
  if (props.isOwner) {
    showNotification('You cannot buy your own product', 'warning', 'fas fa-exclamation-triangle');
    return;
  }
  
  const selectedImage = props.allImages[selectedImageIndex.value];
  
  const queryParams = new URLSearchParams({
    product_id: props.product.product_id,
    quantity: 1,
    product_name: props.product.name,
    price: props.product.price,
    stock: props.product.stock,
    product_image: selectedImage.image_url,
    selected_image_id: selectedImage && !selectedImage.is_main ? selectedImage.id : null,
    ...(isDiscountActive.value ? {
      discount_id: props.product.discount.discount_id,
      discounted_price: discountedPrice.value,
      discount_name: discountName.value,
      is_discounted: true,
      original_price: props.product.price,
      discount_percent: discountPercent.value
    } : {})
  });
  
  window.location.href = `/payment?${queryParams.toString()}`;
};

const showNotification = (message, type = 'success', icon = null) => {
  const icons = {
    success: 'fas fa-check-circle',
    warning: 'fas fa-exclamation-triangle',
    error: 'fas fa-times-circle',
    info: 'fas fa-info-circle'
  };

  notification.value = {
    show: true,
    message,
    type,
    icon: icon || icons[type] || icons.success
  };

  setTimeout(() => {
    notification.value.show = false;
  }, 3000);
};

const hideNotification = () => {
  notification.value.show = false;
};

// Initialize theme on mount
onMounted(() => {
  // Get theme from localStorage or default to light
  const savedTheme = localStorage.getItem('theme') || 'light';
  theme.value = savedTheme;
  
  // Apply theme to HTML and body
  document.documentElement.setAttribute('data-theme', savedTheme);
  document.body.classList.add(`${savedTheme}-theme`);
  
  // Listen for theme changes from navbar
  window.addEventListener('theme-changed', handleThemeChange);
  
  // Auto-select image from URL parameter
  if (props.selectedImageId) {
    const index = props.allImages.findIndex(img => img.id === parseInt(props.selectedImageId));
    if (index !== -1) {
      selectedImageIndex.value = index;
      currentIndex.value = index;
    }
  }
  
  // Set up keyboard navigation
  const handleKeyDown = (event) => {
    if (event.key === 'ArrowLeft') prevImage();
    if (event.key === 'ArrowRight') nextImage();
  };
  
  window.addEventListener('keydown', handleKeyDown);
});

// Cleanup on unmount
onUnmounted(() => {
  window.removeEventListener('theme-changed', handleThemeChange);
  window.removeEventListener('keydown', handleKeyDown);
});
</script>

<style scoped>
/* ===== DARK MODE VARIABLES ===== */
:root {
  --page-bg-color: #f8f9fa;
  --card-bg-color: #ffffff;
  --card-border-color: #dee2e6;
  --text-color: #212529;
  --text-muted: #6c757d;
  --border-color: #dee2e6;
  --light-bg: #f8f9fa;
  --header-bg: #f8f9fa;
  --header-text: #212529;
  --primary-color: #0d6efd;
  --primary-color-light: #e3f2fd;
  --danger-color: #dc3545;
  --danger-color-light: #fde8e8;
  --warning-color: #ffc107;
  --warning-color-light: #fff3cd;
  --success-color: #198754;
  --success-color-light: #d1e7dd;
  --info-color: #0dcaf0;
  --info-color-light: #cff4fc;
  --secondary-color: #6c757d;
  --secondary-color-light: #e9ecef;
}

[data-theme="dark"] {
  --page-bg-color: #0f172a;
  --card-bg-color: #1e293b;
  --card-border-color: #334155;
  --text-color: #f1f5f9;
  --text-muted: #94a3b8;
  --border-color: #475569;
  --light-bg: #1e293b;
  --header-bg: #334155;
  --header-text: #f1f5f9;
  --primary-color: #3b82f6;
  --primary-color-light: #1e3a8a;
  --danger-color: #ef4444;
  --danger-color-light: #7f1d1d;
  --warning-color: #f59e0b;
  --warning-color-light: #92400e;
  --success-color: #10b981;
  --success-color-light: #065f46;
  --info-color: #06b6d4;
  --info-color-light: #164e63;
  --secondary-color: #9ca3af;
  --secondary-color-light: #374151;
}

/* ===== BASE STYLES ===== */
.product-details-page {
  min-height: 100vh;
  position: relative;
  transition: background-color 0.3s ease;
}

.light-theme .product-details-page {
  background-color: var(--page-bg-color);
  color: var(--text-color);
}

.dark-theme .product-details-page {
  background-color: var(--page-bg-color);
  color: var(--text-color);
}

/* Background Pattern */
.background-pattern {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
  opacity: 0.1;
  pointer-events: none;
}

.light-theme .background-pattern {
  background: radial-gradient(circle at 20% 80%, rgba(13, 110, 253, 0.1) 0%, transparent 50%),
              radial-gradient(circle at 80% 20%, rgba(220, 53, 69, 0.1) 0%, transparent 50%);
}

.dark-theme .background-pattern {
  background: radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
              radial-gradient(circle at 80% 20%, rgba(239, 68, 68, 0.15) 0%, transparent 50%);
}

/* ===== THEME UTILITY CLASSES ===== */
.theme-card {
  background-color: var(--card-bg-color);
  border: 1px solid var(--card-border-color);
  border-radius: 12px;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.dark-theme .theme-card {
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.25);
}

.theme-card-header {
  background-color: var(--header-bg) !important;
  border-bottom: 1px solid var(--border-color) !important;
  color: var(--header-text) !important;
}

.theme-text {
  color: var(--text-color) !important;
}

.theme-text-muted {
  color: var(--text-muted) !important;
}

.theme-border {
  border-color: var(--border-color) !important;
}

.theme-bg-light {
  background-color: var(--light-bg) !important;
}

.theme-btn {
  background-color: var(--light-bg);
  border: 1px solid var(--border-color);
  color: var(--text-color);
  transition: all 0.3s ease;
}

.theme-btn:hover:not(:disabled) {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(var(--primary-color), 0.2);
}

.theme-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none !important;
}

/* ===== IMAGE SLIDER ===== */
.product-images-slider {
  position: relative;
  padding: 1.5rem;
  background: linear-gradient(145deg, var(--card-bg-color), var(--light-bg));
}

.theme-main-image {
  background: var(--card-bg-color);
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  border: 1px solid var(--border-color);
  transition: transform 0.3s ease;
}

.dark-theme .theme-main-image {
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
}

.theme-main-image:hover {
  transform: scale(1.005);
}

/* Thumbnails */
.thumbnails-container {
  margin-top: 1.5rem;
}

.thumbnail-btn {
  background: none;
  border: 2px solid transparent;
  transition: all 0.3s ease;
  cursor: pointer;
  overflow: hidden;
  position: relative;
  border-radius: 8px;
}

.thumbnail-btn:hover {
  border-color: var(--primary-color);
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.dark-theme .thumbnail-btn:hover {
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.thumbnail-btn.active {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(var(--primary-color), 0.3);
  transform: translateY(-2px);
}

.thumbnail-btn.selected-purchase {
  border-color: var(--success-color);
  box-shadow: 0 0 0 3px rgba(var(--success-color), 0.3);
}

.purchase-indicator {
  position: absolute;
  top: 5px;
  right: 5px;
  background: var(--success-color);
  color: white;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  z-index: 2;
}

.theme-thumbnail {
  border: 1px solid var(--border-color);
  transition: transform 0.3s ease;
}

.thumbnail-btn:hover .theme-thumbnail {
  transform: scale(1.1);
}

/* Navigation Arrows */
.navigation-arrows {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  transform: translateY(-50%);
  padding: 0 1.5rem;
  pointer-events: none;
}

.arrow-btn {
  pointer-events: all;
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
  border: none;
  background: var(--card-bg-color);
  color: var(--text-color);
  transition: all 0.3s ease;
}

.arrow-btn:hover:not(:disabled) {
  background: var(--primary-color);
  color: white;
  transform: scale(1.1);
}

.arrow-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.theme-counter-badge {
  background: var(--primary-color);
  color: white;
  padding: 0.5rem 1rem;
  font-weight: 500;
  letter-spacing: 0.5px;
}

/* ===== BADGE STYLES ===== */
.theme-badge-primary {
  background: var(--primary-color);
  color: white;
  padding: 0.5rem 0.75rem;
  font-weight: 500;
}

.theme-badge-secondary {
  background: var(--secondary-color);
  color: white;
}

.theme-badge-info {
  background: var(--info-color);
  color: white;
}

.theme-badge-warning {
  background: var(--warning-color);
  color: #212529;
}

.discount-badge {
  background: linear-gradient(135deg, var(--danger-color), #c53030);
  color: white;
  font-size: 1.1rem;
  padding: 0.6rem 1rem;
  font-weight: 600;
  box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.theme-discount-badge {
  background: rgba(var(--danger-color), 0.1);
  color: var(--danger-color);
  border: 1px solid rgba(var(--danger-color), 0.3);
  font-weight: 500;
}

.dark-theme .theme-discount-badge {
  background: rgba(239, 68, 68, 0.2);
  border-color: rgba(239, 68, 68, 0.4);
}

/* ===== SELECTION CARD ===== */
.selection-card {
  padding: 1rem;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  background: var(--card-bg-color);
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.selection-card:hover {
  border-color: var(--border-color);
  background: var(--light-bg);
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.dark-theme .selection-card:hover {
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.selection-card.selected {
  border-color: var(--primary-color);
  background: rgba(var(--primary-color), 0.05);
  box-shadow: 0 0 0 3px rgba(var(--primary-color), 0.1);
}

.dark-theme .selection-card.selected {
  background: rgba(59, 130, 246, 0.1);
}

.theme-selection-image {
  border: 1px solid var(--border-color);
  border-radius: 8px;
  transition: transform 0.3s ease;
}

.selection-card:hover .theme-selection-image {
  transform: scale(1.05);
}

.theme-checkbox {
  background-color: var(--card-bg-color);
  border: 2px solid var(--border-color);
  cursor: pointer;
  width: 1.2em;
  height: 1.2em;
  margin-top: 0.3em;
}

.theme-checkbox:checked {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e");
}

.dark-theme .theme-checkbox:checked {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%230f172a' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e");
}

/* ===== PRICE SECTION ===== */
.theme-price-section {
  border-left: 4px solid var(--primary-color);
  background: linear-gradient(135deg, var(--light-bg), var(--card-bg-color));
  border-radius: 12px;
  position: relative;
  overflow: hidden;
}

.theme-price-section::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100px;
  background: radial-gradient(circle, rgba(var(--primary-color), 0.1) 0%, transparent 70%);
  border-radius: 50%;
  transform: translate(30%, -30%);
}

.text-danger {
  color: var(--danger-color) !important;
}

.text-primary {
  color: var(--primary-color) !important;
}

/* ===== DESCRIPTION SECTION ===== */
.theme-description-bg {
  background: var(--card-bg-color);
  border: 1px solid var(--border-color) !important;
  border-radius: 10px;
  line-height: 1.8;
  font-size: 1.05rem;
}

.theme-description-bg p {
  margin-bottom: 0;
}

/* ===== DETAILS TABLE ===== */
.theme-table {
  background: var(--card-bg-color);
  border: 1px solid var(--border-color) !important;
  border-radius: 10px;
  overflow: hidden;
}

.theme-table-header {
  background: var(--light-bg) !important;
  font-weight: 600;
  color: var(--text-color) !important;
  border-color: var(--border-color) !important;
  padding: 1rem !important;
}

.theme-table td {
  border-color: var(--border-color) !important;
  padding: 1rem !important;
  color: var(--text-color) !important;
}

/* ===== ACTION BUTTONS ===== */
.action-buttons {
  border-top: 2px solid var(--border-color);
}

.theme-primary-btn {
  background: linear-gradient(135deg, var(--primary-color), #0b5ed7);
  border: none;
  color: white;
  font-weight: 600;
  letter-spacing: 0.5px;
  padding: 1rem 2rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(var(--primary-color), 0.3);
}

.theme-primary-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #0b5ed7, #0a58ca);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(var(--primary-color), 0.4);
}

.theme-buy-btn {
  background: linear-gradient(135deg, var(--danger-color), #c53030);
  border: none;
  color: white;
  font-weight: 600;
  letter-spacing: 0.5px;
  padding: 1rem 2rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(var(--danger-color), 0.3);
}

.theme-buy-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #c53030, #b91c1c);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(var(--danger-color), 0.4);
}

.theme-discount-btn {
  background: linear-gradient(135deg, var(--success-color), #0d9263);
  border: none;
  color: white;
  font-weight: 600;
  letter-spacing: 0.5px;
  padding: 1rem 2rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(var(--success-color), 0.3);
}

.theme-discount-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #0d9263, #0b8457);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(var(--success-color), 0.4);
}

/* ===== ALERTS ===== */
.theme-alert {
  background: var(--light-bg);
  border: 1px solid var(--border-color);
  color: var(--text-color);
  border-radius: 10px;
  padding: 1rem 1.25rem;
}

.dark-theme .alert-warning {
  background: rgba(245, 158, 11, 0.1);
  border-color: rgba(245, 158, 11, 0.3);
  color: #fbbf24;
}

.dark-theme .alert-info {
  background: rgba(59, 130, 246, 0.1);
  border-color: rgba(59, 130, 246, 0.3);
  color: #60a5fa;
}

/* ===== TOAST NOTIFICATIONS ===== */
.toast-container {
  z-index: 9999;
}

.toast {
  border-radius: 12px;
  border: none;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.25);
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.95);
}

.dark-theme .toast {
  background: rgba(30, 41, 59, 0.95);
  backdrop-filter: blur(10px);
}

.toast-success {
  background: linear-gradient(135deg, rgba(25, 135, 84, 0.95), rgba(21, 128, 61, 0.95)) !important;
  color: white;
}

.toast-error {
  background: linear-gradient(135deg, rgba(220, 53, 69, 0.95), rgba(185, 28, 28, 0.95)) !important;
  color: white;
}

.toast-warning {
  background: linear-gradient(135deg, rgba(255, 193, 7, 0.95), rgba(217, 119, 6, 0.95)) !important;
  color: #212529;
}

.toast-info {
  background: linear-gradient(135deg, rgba(13, 202, 240, 0.95), rgba(6, 182, 212, 0.95)) !important;
  color: white;
}

/* ===== RESPONSIVE STYLES ===== */
@media (max-width: 768px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .product-images-slider {
    padding: 1rem;
  }
  
  .theme-main-image {
    padding: 0.75rem;
  }
  
  .thumbnails-container {
    margin-top: 1rem;
  }
  
  .thumbnail-btn img {
    width: 50px !important;
    height: 50px !important;
  }
  
  .navigation-arrows {
    padding: 0 1rem;
  }
  
  .arrow-btn {
    width: 40px;
    height: 40px;
  }
  
  .h1 {
    font-size: 2rem !important;
  }
  
  .h2 {
    font-size: 1.75rem !important;
  }
  
  .price-section .h1 {
    font-size: 2rem !important;
  }
  
  .theme-primary-btn,
  .theme-buy-btn,
  .theme-discount-btn {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
  }
}

@media (max-width: 576px) {
  .thumbnails-container {
    flex-wrap: nowrap;
    overflow-x: auto;
    justify-content: flex-start;
    padding-bottom: 0.75rem;
    margin-left: -0.5rem;
    margin-right: -0.5rem;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
  
  .thumbnails-container::-webkit-scrollbar {
    height: 6px;
  }
  
  .thumbnails-container::-webkit-scrollbar-track {
    background: var(--light-bg);
    border-radius: 3px;
  }
  
  .thumbnails-container::-webkit-scrollbar-thumb {
    background: var(--secondary-color);
    border-radius: 3px;
  }
  
  .thumbnail-btn {
    flex-shrink: 0;
  }
  
  .selection-card {
    flex: 0 0 auto;
    width: 100px;
    padding: 0.75rem;
  }
  
  .theme-selection-image {
    height: 70px !important;
  }
  
  .purchase-indicator {
    top: 3px;
    right: 3px;
    width: 18px;
    height: 18px;
    font-size: 0.6rem;
  }
}

/* ===== ANIMATIONS ===== */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease, transform 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* ===== SCROLLBAR STYLING ===== */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: var(--light-bg);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: var(--secondary-color);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--primary-color);
}

/* ===== FOCUS STATES ===== */
button:focus,
input:focus,
textarea:focus,
select:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(var(--primary-color), 0.3) !important;
}

/* ===== LOADING STATES ===== */
.btn:disabled {
  position: relative;
  overflow: hidden;
}

.btn:disabled::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  100% {
    left: 100%;
  }
}

/* ===== TEXT SELECTION ===== */
::selection {
  background-color: rgba(var(--primary-color), 0.3);
  color: var(--text-color);
}

/* ===== IMAGE ZOOM EFFECT ===== */
.main-image-container img {
  cursor: zoom-in;
  transition: transform 0.3s ease;
}

.main-image-container:hover img {
  transform: scale(1.02);
}

/* ===== GLOW EFFECTS ===== */
.discount-badge,
.theme-primary-btn,
.theme-buy-btn,
.theme-discount-btn {
  position: relative;
  overflow: hidden;
}

.discount-badge::after,
.theme-primary-btn::after,
.theme-buy-btn::after,
.theme-discount-btn::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.discount-badge:hover::after,
.theme-primary-btn:hover::after,
.theme-buy-btn:hover::after,
.theme-discount-btn:hover::after {
  opacity: 1;
}

/* ===== HOVER EFFECTS FOR CARDS ===== */
.theme-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.dark-theme .theme-card:hover {
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}

/* ===== PRICE ANIMATION ===== */
@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

.price-section .h1 {
  animation: pulse 2s infinite;
}
</style>