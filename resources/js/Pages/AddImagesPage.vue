<template>
  <AppLayout>
    <div class="additional-images-page">
      <!-- Subtle Background -->
      <div class="subtle-background">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="bubble bubble-3"></div>
      </div>

      <div class="container py-4">
        <!-- Page Header -->
        <div class="page-header mb-4">
          <div class="row align-items-center">
            <div class="col-lg-8">
              <div class="d-flex align-items-center">
                <button @click="goBack" 
                        class="btn btn-sm btn-outline-secondary me-3 rounded-pill">
                  <i class="fas fa-arrow-left me-1"></i>Back
                </button>
                <div>
                  <h1 class="h3 fw-bold mb-1">
                    <i class="fas fa-images me-2 text-primary"></i>Manage Product Images
                  </h1>
                  <p class="text-muted mb-0">
                    Add and manage additional images for: 
                    <span class="fw-bold text-primary">{{ product.name }}</span>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-lg-4 text-lg-end">
              <div class="d-flex gap-2 justify-content-end">
                <button @click="viewProductDetails" 
                        class="btn btn-sm btn-info rounded-pill">
                  <i class="fas fa-eye me-1"></i>View Product
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stat-icon me-3">
                    <i class="fas fa-image fa-2x text-primary"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Main Image</h6>
                    <h3 class="mb-0 fw-bold">1</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stat-icon me-3">
                    <i class="fas fa-layer-group fa-2x text-success"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Additional Images</h6>
                    <h3 class="mb-0 fw-bold">{{ additionalImages.length }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stat-icon me-3">
                    <i class="fas fa-star fa-2x text-warning"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Selected Image</h6>
                    <h3 class="mb-0 fw-bold">
                      {{ selectedImage ? 'Yes' : 'No' }}
                    </h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="row">
          <!-- Left Column: Upload Section -->
          <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="fas fa-upload me-2"></i>Upload Additional Images
                </h5>
              </div>
              <div class="card-body">
                <!-- Upload Area -->
                <div class="upload-area p-4 text-center border rounded" 
                     @dragover.prevent="dragover = true" 
                     @dragleave="dragover = false" 
                     @drop.prevent="handleDrop"
                     :class="{ 'border-primary bg-primary-subtle': dragover }"
                     @click="triggerFileInput">
                  <input type="file" 
                         ref="fileInput" 
                         @change="handleFileSelect" 
                         multiple 
                         accept="image/*" 
                         class="d-none">
                  
                  <div class="py-5">
                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold mb-2">Drag & Drop Images</h5>
                    <p class="text-muted mb-3">or click to browse files</p>
                    <p class="small text-muted">Supports JPG, PNG, GIF up to 2MB each</p>
                  </div>
                </div>
                
                <!-- Selected Files Preview -->
                <div v-if="selectedFiles.length > 0" class="mt-4">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Selected Files ({{ selectedFiles.length }})</h6>
                    <button @click="clearSelectedFiles" 
                            class="btn btn-sm btn-outline-danger">
                      <i class="fas fa-times me-1"></i>Clear All
                    </button>
                  </div>
                  
                  <div class="row g-2">
                    <div v-for="(file, index) in selectedFiles" 
                         :key="index" 
                         class="col-4">
                      <div class="position-relative">
                        <img :src="getFilePreview(file)" 
                             alt="Preview" 
                             class="img-thumbnail w-100" 
                             style="height: 80px; object-fit: cover;">
                        <button @click="removeSelectedFile(index)" 
                                class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                style="transform: translate(50%, -50%);">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Upload Button -->
                  <div class="mt-4">
                    <button @click="uploadImages" 
                            :disabled="isUploading || selectedFiles.length === 0" 
                            class="btn btn-primary w-100">
                      <span v-if="isUploading" 
                            class="spinner-border spinner-border-sm me-2"></span>
                      <i v-else class="fas fa-upload me-2"></i>
                      Upload {{ selectedFiles.length }} Image{{ selectedFiles.length !== 1 ? 's' : '' }}
                    </button>
                  </div>
                </div>
                
                <!-- Instructions -->
                <div class="mt-4 pt-4 border-top">
                  <h6 class="fw-bold mb-3">
                    <i class="fas fa-info-circle me-2 text-info"></i>Instructions
                  </h6>
                  <ul class="small text-muted mb-0">
                    <li class="mb-2">Upload high-quality images for better display</li>
                    <li class="mb-2">Customers can select any image when buying</li>
                    <li class="mb-2">Drag to reorder images (first image shows first)</li>
                    <li class="mb-2">Mark one image as "selected" - it will be pre-selected for customers</li>
                    <li>Delete images you don't want to keep</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right Column: Image Gallery -->
          <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="fas fa-images me-2"></i>Product Image Gallery
                </h5>
                <div v-if="additionalImages.length > 0" class="d-flex gap-2">
                  <button @click="saveOrder" 
                          :disabled="isSavingOrder" 
                          class="btn btn-sm btn-light">
                    <span v-if="isSavingOrder" 
                          class="spinner-border spinner-border-sm me-1"></span>
                    <i v-else class="fas fa-save me-1"></i>
                    Save Order
                  </button>
                </div>
              </div>
              
              <div class="card-body">
                <!-- Main Image -->
                <div class="mb-4">
                  <h6 class="fw-bold mb-3">
                    <i class="fas fa-crown text-warning me-2"></i>Main Product Image
                  </h6>
                  <div class="main-image-container p-3 border rounded bg-light">
                    <div class="row align-items-center">
                      <div class="col-md-3">
                        <img :src="product.main_image_url" 
                             :alt="product.name" 
                             class="img-fluid rounded shadow-sm">
                      </div>
                      <div class="col-md-9">
                        <div class="d-flex align-items-center mb-2">
                          <span class="badge bg-warning me-2">MAIN IMAGE</span>
                          <span class="text-muted small">This is the primary image shown in listings</span>
                        </div>
                        <p class="mb-2 small">
                          <strong>Note:</strong> To change the main image, edit the product from the products page.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Additional Images -->
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">
                      <i class="fas fa-layer-group text-primary me-2"></i>
                      Additional Images ({{ additionalImages.length }})
                      <span v-if="additionalImages.length > 0" class="text-muted small ms-2">
                        Drag to reorder • Click star to select default
                      </span>
                    </h6>
                    <div v-if="additionalImages.length > 0" class="text-muted small">
                      First image shows first
                    </div>
                  </div>
                  
                  <!-- Empty State -->
                  <div v-if="additionalImages.length === 0" class="text-center py-5">
                    <div class="empty-state">
                      <i class="fas fa-images fa-3x text-muted mb-3"></i>
                      <h5 class="fw-bold mb-2">No additional images yet</h5>
                      <p class="text-muted mb-3">Upload some images to get started</p>
                    </div>
                  </div>
                  
                  <!-- Images Grid -->
                  <div v-else class="additional-images-grid">
                    <draggable v-model="additionalImages" 
                               @end="onDragEnd"
                               item-key="additional_image_id"
                               handle=".drag-handle"
                               class="row g-3">
                      <template #item="{ element: image, index }">
                        <div class="col-md-4 col-sm-6">
                          <div class="image-card card border-0 shadow-sm h-100"
                               :class="{ 'border-primary': image.is_selected }">
                            <!-- Image Header -->
                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                              <div class="d-flex align-items-center">
                                <span class="drag-handle me-2 text-muted" style="cursor: move;">
                                  <i class="fas fa-bars"></i>
                                </span>
                                <span class="badge bg-light text-dark small">
                                  #{{ index + 1 }}
                                </span>
                              </div>
                              <div class="d-flex gap-1">
                                <!-- Select Button -->
                                <button @click="toggleSelectImage(image)" 
                                        class="btn btn-sm p-0"
                                        :class="image.is_selected ? 'btn-warning' : 'btn-outline-warning'"
                                        :title="image.is_selected ? 'Selected as default' : 'Set as default'">
                                  <i class="fas fa-star"></i>
                                </button>
                                
                                <!-- Delete Button -->
                                <button @click="deleteImage(image)" 
                                        class="btn btn-sm btn-outline-danger p-0"
                                        title="Delete image">
                                  <i class="fas fa-trash"></i>
                                </button>
                              </div>
                            </div>
                            
                            <!-- Image -->
                            <div class="card-body p-2 text-center">
                              <img :src="image.image_url" 
                                   :alt="'Additional image ' + (index + 1)" 
                                   class="img-fluid rounded"
                                   style="height: 120px; width: 100%; object-fit: cover;">
                            </div>
                            
                            <!-- Image Footer -->
                            <div class="card-footer bg-transparent border-0 py-2 px-3">
                              <div class="small text-muted text-center">
                                <i class="fas fa-calendar me-1"></i>
                                {{ formatDate(image.created_at) }}
                              </div>
                            </div>
                          </div>
                        </div>
                      </template>
                    </draggable>
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
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import draggable from 'vuedraggable';

const props = defineProps({
  product: Object,
  success: String,
  error: String
});

// Refs
const dragover = ref(false);
const fileInput = ref(null);
const selectedFiles = ref([]);
const additionalImages = ref(props.product.additional_images || []);
const isUploading = ref(false);
const isSavingOrder = ref(false);
const selectedImage = computed(() => additionalImages.value.find(img => img.is_selected));

// Notification
const notification = ref({
  show: false,
  message: '',
  type: 'success',
  icon: 'fas fa-check-circle'
});

// Methods
const triggerFileInput = () => {
  fileInput.value.click();
};

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files);
  addFiles(files);
};

const handleDrop = (event) => {
  dragover.value = false;
  const files = Array.from(event.dataTransfer.files);
  addFiles(files);
};

const addFiles = (files) => {
  const validFiles = files.filter(file => {
    const isValidType = file.type.startsWith('image/');
    const isValidSize = file.size <= 2 * 1024 * 1024; // 2MB
    
    if (!isValidType) {
      showNotification('Please select only image files', 'warning', 'fas fa-exclamation-triangle');
      return false;
    }
    
    if (!isValidSize) {
      showNotification('File size must be less than 2MB', 'warning', 'fas fa-exclamation-triangle');
      return false;
    }
    
    return true;
  });
  
  selectedFiles.value = [...selectedFiles.value, ...validFiles];
};

const getFilePreview = (file) => {
  return URL.createObjectURL(file);
};

const removeSelectedFile = (index) => {
  selectedFiles.value.splice(index, 1);
};

const clearSelectedFiles = () => {
  selectedFiles.value.forEach(file => {
    if (file.preview) URL.revokeObjectURL(file.preview);
  });
  selectedFiles.value = [];
};

const uploadImages = async () => {
  if (selectedFiles.value.length === 0) return;
  
  isUploading.value = true;
  
  const formData = new FormData();
  selectedFiles.value.forEach((file, index) => {
    formData.append(`images[${index}]`, file);
  });
  
  try {
    const response = await fetch(`/api/product/${props.product.product_id}/additional-images`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Add new images to the list
      additionalImages.value = [...additionalImages.value, ...result.images];
      
      // Clear selected files
      selectedFiles.value.forEach(file => {
        if (file.preview) URL.revokeObjectURL(file.preview);
      });
      selectedFiles.value = [];
      
      showNotification(`Successfully uploaded ${result.images.length} image(s)`, 'success', 'fas fa-check-circle');
    } else {
      showNotification('Failed to upload images', 'error', 'fas fa-times-circle');
    }
  } catch (error) {
    console.error('Upload error:', error);
    showNotification('Error uploading images', 'error', 'fas fa-times-circle');
  } finally {
    isUploading.value = false;
  }
};

const deleteImage = async (image) => {
  if (!confirm('Are you sure you want to delete this image?')) return;
  
  try {
    const response = await fetch(`/api/additional-images/${image.additional_image_id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Remove image from list
      additionalImages.value = additionalImages.value.filter(
        img => img.additional_image_id !== image.additional_image_id
      );
      showNotification('Image deleted successfully', 'success', 'fas fa-check-circle');
    } else {
      showNotification('Failed to delete image', 'error', 'fas fa-times-circle');
    }
  } catch (error) {
    console.error('Delete error:', error);
    showNotification('Error deleting image', 'error', 'fas fa-times-circle');
  }
};

const toggleSelectImage = async (image) => {
  // If already selected, don't do anything (or you could add unselect logic)
  if (image.is_selected) return;
  
  try {
    const response = await fetch(`/api/additional-images/${image.additional_image_id}/select`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Update all images to show only this one as selected
      additionalImages.value = additionalImages.value.map(img => ({
        ...img,
        is_selected: img.additional_image_id === image.additional_image_id
      }));
      
      showNotification('Default image set successfully', 'success', 'fas fa-check-circle');
    } else {
      showNotification('Failed to set default image', 'error', 'fas fa-times-circle');
    }
  } catch (error) {
    console.error('Select error:', error);
    showNotification('Error setting default image', 'error', 'fas fa-times-circle');
  }
};

const onDragEnd = () => {
  // Re-index display order visually
  additionalImages.value = additionalImages.value.map((img, index) => ({
    ...img,
    display_order: index + 1
  }));
};

const saveOrder = async () => {
  if (additionalImages.value.length === 0) return;
  
  isSavingOrder.value = true;
  
  const order = additionalImages.value.map(img => img.additional_image_id);
  
  try {
    const response = await fetch(`/api/product/${props.product.product_id}/additional-images/order`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ order })
    });
    
    const result = await response.json();
    
    if (result.success) {
      showNotification('Image order saved successfully', 'success', 'fas fa-save');
    } else {
      showNotification('Failed to save order', 'error', 'fas fa-times-circle');
    }
  } catch (error) {
    console.error('Save order error:', error);
    showNotification('Error saving order', 'error', 'fas fa-times-circle');
  } finally {
    isSavingOrder.value = false;
  }
};

const goBack = () => {
  window.history.back();
};

const viewProductDetails = () => {
  router.visit(`/products/${props.product.product_id}`);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
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

// Show success/error messages from props
onMounted(() => {
  if (props.success) {
    showNotification(props.success, 'success');
  }
  
  if (props.error) {
    showNotification(props.error, 'error');
  }
});
</script>

<style scoped>
.additional-images-page {
  background: #f8f9fa;
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
}

/* Subtle Background */
.subtle-background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
  overflow: hidden;
}

.bubble {
  position: absolute;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
  animation: float 25s infinite linear;
  opacity: 0.3;
}

.bubble-1 {
  width: 200px;
  height: 200px;
  top: 10%;
  left: 5%;
  animation-delay: 0s;
}

.bubble-2 {
  width: 150px;
  height: 150px;
  top: 60%;
  right: 10%;
  animation-delay: 5s;
}

.bubble-3 {
  width: 120px;
  height: 120px;
  bottom: 20%;
  left: 15%;
  animation-delay: 10s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) translateX(0) rotate(0deg);
  }
  25% {
    transform: translateY(-20px) translateX(10px) rotate(90deg);
  }
  50% {
    transform: translateY(0) translateX(20px) rotate(180deg);
  }
  75% {
    transform: translateY(20px) translateX(10px) rotate(270deg);
  }
}

/* Upload Area */
.upload-area {
  cursor: pointer;
  transition: all 0.3s ease;
  background: #f8f9fa;
  border: 2px dashed #dee2e6;
}

.upload-area:hover {
  border-color: #0d6efd;
  background: rgba(13, 110, 253, 0.05);
}

.upload-area.border-primary {
  border-color: #0d6efd !important;
  background: rgba(13, 110, 253, 0.1) !important;
}

/* Stats Cards */
.stat-card {
  transition: all 0.3s ease;
  border-radius: 10px;
  overflow: hidden;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.03);
}

/* Image Cards */
.image-card {
  transition: all 0.3s ease;
  border-radius: 8px;
  overflow: hidden;
}

.image-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.image-card.border-primary {
  border: 2px solid #0d6efd !important;
}

.drag-handle {
  cursor: move;
}

.drag-handle:hover {
  color: #0d6efd !important;
}

/* Main Image Container */
.main-image-container {
  border-radius: 8px;
}

/* Empty State */
.empty-state {
  padding: 3rem 1rem;
}

/* Toast */
.toast {
  border-radius: 10px;
  border: none;
}

/* Responsive */
@media (max-width: 768px) {
  .upload-area .py-5 {
    padding: 2rem 1rem !important;
  }
  
  .main-image-container .row {
    flex-direction: column;
    text-align: center;
  }
  
  .main-image-container .col-md-3 {
    margin-bottom: 1rem;
  }
}

/* Custom scrollbar for images grid */
.additional-images-grid {
  max-height: 500px;
  overflow-y: auto;
}

.additional-images-grid::-webkit-scrollbar {
  width: 6px;
}

.additional-images-grid::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.additional-images-grid::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}

.additional-images-grid::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>