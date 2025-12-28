<template>
  <AppLayout>
    <div class="product-details-page">
      <!-- Background Pattern -->
      <div class="background-pattern"></div>

      <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="/" class="text-decoration-none">
                <i class="fas fa-home me-1"></i>Home
              </a>
            </li>
            <li class="breadcrumb-item">
              <a :href="`/category/${product.category?.slug || ''}`" 
                 class="text-decoration-none">
                {{ product.category?.name || 'Category' }}
              </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ product.name }}</li>
          </ol>
        </nav>

        <!-- Product Details -->
        <div class="row">
          <!-- Left Column: Images -->
          <div class="col-lg-6 mb-4">
            <!-- Image Slider -->
            <div class="product-images-slider card border-0 shadow-sm">
              <!-- Main Image -->
              <div class="main-image-container position-relative mb-3">
                <img :src="currentImage.image_url" 
                     :alt="product.name" 
                     class="img-fluid rounded"
                     style="max-height: 400px; width: 100%; object-fit: contain;">
                
                <!-- Discount Badge -->
                <div v-if="product.discount && product.discount.status === 'active'" 
                     class="position-absolute top-0 start-0 m-3">
                  <span class="badge bg-danger fs-6 p-2">
                    <i class="fas fa-fire me-1"></i>{{ product.discount_percent }}% OFF
                  </span>
                </div>
                
                <!-- Owner Actions -->
                <div v-if="isOwner" class="position-absolute top-0 end-0 m-3">
                  <div class="d-flex flex-column gap-2">
                    <button @click="editProduct" 
                            class="btn btn-sm btn-warning rounded-pill">
                      <i class="fas fa-edit me-1"></i>Edit Product
                    </button>
                    <button @click="manageImages" 
                            class="btn btn-sm btn-primary rounded-pill">
                      <i class="fas fa-images me-1"></i>Manage Images
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- Thumbnails -->
              <div v-if="allImages.length > 1" class="thumbnails-container">
                <div class="d-flex justify-content-center flex-wrap gap-2">
                  <button v-for="(image, index) in allImages" 
                          :key="index"
                          @click="selectImage(index)"
                          class="thumbnail-btn p-0 border rounded"
                          :class="{ active: currentIndex === index }">
                    <img :src="image.image_url" 
                         :alt="`Thumbnail ${index + 1}`"
                         class="img-fluid rounded"
                         style="width: 60px; height: 60px; object-fit: cover;">
                  </button>
                </div>
              </div>
              
              <!-- Navigation Arrows -->
              <div v-if="allImages.length > 1" class="navigation-arrows">
                <button @click="prevImage" 
                        class="btn btn-light rounded-circle arrow-btn"
                        :disabled="currentIndex === 0">
                  <i class="fas fa-chevron-left"></i>
                </button>
                <button @click="nextImage" 
                        class="btn btn-light rounded-circle arrow-btn"
                        :disabled="currentIndex === allImages.length - 1">
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
              
              <!-- Image Counter -->
              <div v-if="allImages.length > 1" class="image-counter text-center mt-3">
                <span class="badge bg-dark">
                  Image {{ currentIndex + 1 }} of {{ allImages.length }}
                </span>
              </div>
            </div>
            
            <!-- Image Selection for Purchase (for customers) -->
            <div v-if="!isOwner && allImages.length > 1" class="card border-0 shadow-sm mt-4">
              <div class="card-header bg-light">
                <h6 class="mb-0">
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
                           class="img-fluid rounded mb-2"
                           style="height: 80px; width: 100%; object-fit: cover;">
                      <div class="form-check">
                        <input class="form-check-input" 
                               type="radio" 
                               :id="`image-${index}`"
                               :checked="selectedImageIndex === index">
                        <label class="form-check-label small" :for="`image-${index}`">
                          {{ image.is_main ? 'Main' : `Option ${index}` }}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="mt-3 text-center">
                  <small class="text-muted">
                    Selected image will be used for your order
                  </small>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right Column: Product Info -->
          <div class="col-lg-6">
            <div class="product-info card border-0 shadow-sm h-100">
              <div class="card-body">
                <!-- Product Header -->
                <div class="mb-4">
                  <h1 class="h2 fw-bold mb-2">{{ product.name }}</h1>
                  
                  <!-- Seller Info -->
                  <div class="d-flex align-items-center mb-3">
                    <div class="d-flex align-items-center me-3">
                      <i class="fas fa-store text-muted me-2"></i>
                      <span class="text-muted">Seller:</span>
                      <span class="ms-1 fw-medium">{{ product.user?.name || 'Unknown' }}</span>
                    </div>
                    
                    <!-- Verified Badge -->
                    <div v-if="product.user?.is_verified" class="badge bg-success">
                      <i class="fas fa-check-circle me-1"></i>Verified Seller
                    </div>
                  </div>
                  
                  <!-- Category & Type -->
                  <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-primary">
                      <i class="fas fa-tag me-1"></i>{{ product.category?.name || 'Uncategorized' }}
                    </span>
                    <span class="badge" 
                          :class="product.product_type === 'onstock' ? 'bg-info' : 'bg-warning'">
                      <i class="fas" :class="product.product_type === 'onstock' ? 'fa-layer-group' : 'fa-box'"></i>
                      {{ product.product_type === 'onstock' ? 'On-Stock' : 'One-Time' }}
                    </span>
                    <span class="badge" :class="getStockBadgeClass">
                      <i class="fas" :class="getStockIcon"></i>
                      {{ product.stock }} {{ product.stock === 1 ? 'unit' : 'units' }} available
                    </span>
                  </div>
                </div>
                
                <!-- Price Section -->
                <div class="price-section mb-4 p-3 bg-light rounded">
                  <div v-if="product.discount && product.discount.status === 'active'" 
                       class="discount-price mb-2">
                    <div class="d-flex align-items-center">
                      <span class="text-muted text-decoration-line-through h4 me-3">
                        {{ formatPrice(product.price) }} Birr
                      </span>
                      <span class="h1 fw-bold text-danger">
                        {{ formatPrice(product.discounted_price) }} Birr
                      </span>
                    </div>
                    <div class="mt-2">
                      <span class="badge bg-success">
                        <i class="fas fa-piggy-bank me-1"></i>
                        Save {{ formatPrice(product.price - product.discounted_price) }} Birr
                      </span>
                      <span class="badge bg-danger-subtle text-danger border border-danger ms-2">
                        <i class="fas fa-tag me-1"></i>{{ product.discount_name }}
                      </span>
                      <div class="small text-muted mt-1">
                        <i class="fas fa-clock me-1"></i>
                        Discount ends: {{ formatDate(product.discount_end_date) }}
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
                  <h5 class="fw-bold mb-3">
                    <i class="fas fa-align-left me-2 text-primary"></i>Description
                  </h5>
                  <div class="bg-white p-3 rounded border">
                    <p style="white-space: pre-line;">
                      {{ product.description || 'No description available.' }}
                    </p>
                  </div>
                </div>
                
                <!-- Product Details Table -->
                <div class="details-table mb-4">
                  <h5 class="fw-bold mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Product Details
                  </h5>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <tbody>
                        <tr>
                          <th class="bg-light" style="width: 40%">Product ID</th>
                          <td>{{ product.product_id }}</td>
                        </tr>
                        <tr>
                          <th class="bg-light">Reference</th>
                          <td>
                            <span class="badge bg-secondary">
                              {{ product.reference || 'N/A' }}
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <th class="bg-light">Status</th>
                          <td>
                            <span :class="getStatusBadgeClass">
                              {{ product.status_label }}
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <th class="bg-light">Sold Count</th>
                          <td>{{ product.sold_count || 0 }} units</td>
                        </tr>
                        <tr>
                          <th class="bg-light">Added Date</th>
                          <td>{{ formatDate(product.created_at) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons mt-4 pt-4 border-top">
                  <div class="row g-3">
                    <!-- Owner Actions -->
                    <div v-if="isOwner" class="col-12">
                      <div class="d-grid gap-2">
                        <button @click="manageImages" 
                                class="btn btn-primary btn-lg">
                          <i class="fas fa-images me-2"></i>Manage Additional Images
                        </button>
                        <button @click="editProduct" 
                                class="btn btn-outline-primary btn-lg">
                          <i class="fas fa-edit me-2"></i>Edit Product Details
                        </button>
                      </div>
                    </div>
                    
                    <!-- Customer Actions -->
                    <div v-else class="col-12">
                      <div class="d-grid gap-2">
                        <button @click="buyNow" 
                                :disabled="product.stock <= 0"
                                class="btn btn-danger btn-lg"
                                :class="{ 'btn-success': product.discount && product.discount.status === 'active' }">
                          <i class="fas fa-shopping-cart me-2"></i>
                          {{ product.discount && product.discount.status === 'active' ? 'Buy with Discount' : 'Buy Now' }}
                        </button>
                        
                        <div v-if="product.stock <= 0" class="alert alert-warning mt-3 mb-0">
                          <i class="fas fa-exclamation-triangle me-2"></i>
                          This product is currently out of stock
                        </div>
                        
                        <div v-if="allImages.length > 1 && selectedImageIndex !== null" 
                             class="alert alert-info mt-3 mb-0">
                          <i class="fas fa-image me-2"></i>
                          You have selected image option {{ selectedImageIndex + 1 }} for purchase
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
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-light">
                <h5 class="mb-0">
                  <i class="fas fa-th-large me-2 text-primary"></i>More from this Seller
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <!-- You can add related products here -->
                  <div class="col-12 text-center py-4">
                    <p class="text-muted mb-0">No other products from this seller</p>
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
             :class="[`text-bg-${notification.type}`, { show: notification.show }]" 
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
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  product: Object,
  isOwner: Boolean,
  allImages: Array,
  selectedImageId: String
});

// Refs
const currentIndex = ref(0);
const selectedImageIndex = ref(props.selectedImageId ? 
  props.allImages.findIndex(img => img.id === parseInt(props.selectedImageId)) : 0);
const currentImage = computed(() => props.allImages[currentIndex.value] || {});

// Computed Properties
const getStockBadgeClass = computed(() => {
  if (props.product.stock <= 0) return 'bg-danger';
  if (props.product.stock < 10) return 'bg-warning';
  return 'bg-success';
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

const selectImageForPurchase = (index) => {
  selectedImageIndex.value = index;
  currentIndex.value = index;
};

const prevImage = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--;
  }
};

const nextImage = () => {
  if (currentIndex.value < props.allImages.length - 1) {
    currentIndex.value++;
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
  
  // Check if user is trying to buy their own product
  if (props.isOwner) {
    showNotification('You cannot buy your own product', 'warning', 'fas fa-exclamation-triangle');
    return;
  }
  
  // Get selected image - THIS IS THE KEY FIX
  const selectedImage = props.allImages[selectedImageIndex.value];
  
  // Build query parameters - Pass the SELECTED image URL
  const queryParams = new URLSearchParams({
    product_id: props.product.product_id,
    quantity: 1,
    product_name: props.product.name,
    price: props.product.price,
    stock: props.product.stock,
    // ✅ FIXED: Pass the SELECTED image URL (not main image)
    product_image: selectedImage.image_url, // This is what fixes the issue!
    // Add image selection info
    selected_image_id: selectedImage && !selectedImage.is_main ? selectedImage.id : null,
    // Add discount info if available
    ...(props.product.discount && props.product.discount.status === 'active' ? {
      discount_id: props.product.discount.discount_id,
      discounted_price: props.product.discounted_price,
      discount_name: props.product.discount_name,
      is_discounted: true,
      original_price: props.product.price
    } : {})
  });
  
  // Redirect to payment page
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

// Auto-select image from URL parameter
onMounted(() => {
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
  
  return () => {
    window.removeEventListener('keydown', handleKeyDown);
  };
});
</script>

<style scoped>
.product-details-page {
  background: #f8f9fa;
  min-height: 100vh;
  position: relative;
}

.background-pattern {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: 
    radial-gradient(circle at 10% 20%, rgba(102, 126, 234, 0.05) 0%, transparent 20%),
    radial-gradient(circle at 90% 80%, rgba(118, 75, 162, 0.05) 0%, transparent 20%);
  z-index: -1;
}

/* Image Slider Styles */
.product-images-slider {
  position: relative;
  padding: 1rem;
  border-radius: 12px;
}

.main-image-container {
  background: #fff;
  border-radius: 8px;
  padding: 1rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.thumbnails-container {
  margin-top: 1rem;
}

.thumbnail-btn {
  background: none;
  border: 2px solid transparent;
  transition: all 0.3s ease;
  cursor: pointer;
  overflow: hidden;
}

.thumbnail-btn:hover {
  border-color: #0d6efd;
  transform: translateY(-2px);
}

.thumbnail-btn.active {
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
}

.navigation-arrows {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  transform: translateY(-50%);
  padding: 0 1rem;
  pointer-events: none;
}

.arrow-btn {
  pointer-events: all;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.arrow-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.image-counter {
  margin-top: 1rem;
}

/* Selection Card */
.selection-card {
  padding: 0.5rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.selection-card:hover {
  border-color: #dee2e6;
  background: #f8f9fa;
}

.selection-card.selected {
  border-color: #0d6efd;
  background: rgba(13, 110, 253, 0.05);
}

.selection-card .form-check-input {
  margin-top: 0;
}

/* Price Section */
.price-section {
  border-left: 4px solid #0d6efd;
}

.discount-price .text-decoration-line-through {
  opacity: 0.7;
}

/* Description Section */
.description-section p {
  line-height: 1.6;
  color: #555;
}

/* Details Table */
.details-table table {
  background: white;
}

.details-table th {
  font-weight: 600;
  background-color: #f8f9fa !important;
}

/* Action Buttons */
.action-buttons .btn {
  padding: 0.75rem 1.5rem;
  font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
  .product-images-slider {
    padding: 0.5rem;
  }
  
  .main-image-container {
    padding: 0.5rem;
  }
  
  .thumbnail-btn img {
    width: 50px !important;
    height: 50px !important;
  }
  
  .navigation-arrows {
    padding: 0 0.5rem;
  }
  
  .arrow-btn {
    width: 36px;
    height: 36px;
  }
  
  .price-section .h1 {
    font-size: 1.75rem;
  }
  
  .action-buttons .btn {
    padding: 0.5rem 1rem;
  }
}

@media (max-width: 576px) {
  .thumbnails-container {
    flex-wrap: nowrap;
    overflow-x: auto;
    justify-content: flex-start;
    padding-bottom: 0.5rem;
  }
  
  .thumbnails-container::-webkit-scrollbar {
    height: 4px;
  }
  
  .thumbnails-container::-webkit-scrollbar-track {
    background: #f1f1f1;
  }
  
  .thumbnails-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
  }
  
  .thumbnail-btn {
    flex-shrink: 0;
  }
  
  .selection-card {
    flex: 0 0 auto;
    width: 80px;
  }
}

/* Animation for image changes */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Toast */
.toast {
  border-radius: 10px;
  border: none;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
</style>