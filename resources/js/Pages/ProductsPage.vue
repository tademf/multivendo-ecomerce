<template>
  <AppLayout>
    <div class="products-management-page">
      <!-- Subtle Animated Background -->
      <div class="subtle-background">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="bubble bubble-3"></div>
        <div class="bubble bubble-4"></div>
      </div>

      <div class="container py-4">
        <!-- Page Header -->
        <div class="page-header mb-4">
          <div class="row align-items-center">
            <div class="col-lg-8">
              <h1 class="h2 fw-bold mb-2">
                <i class="fas fa-boxes me-2 text-primary"></i>Product Management
              </h1>
            </div>
            <div class="col-lg-4 text-lg-end">
              <button @click="showAddForm" 
                      class="btn btn-primary btn-sm rounded-pill">
                <i class="fas fa-plus me-1"></i>Add Product
              </button>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
          <div class="col-xl-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stat-icon me-3">
                    <i class="fas fa-box fa-2x text-primary"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Total Products</h6>
                    <h3 class="mb-0 fw-bold">{{ filteredProducts.length }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stat-icon me-3">
                    <i class="fas fa-coins fa-2x text-success"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Stock Value</h6>
                    <h3 class="mb-0 fw-bold">{{ formatPrice(calculateTotalValue()) }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stat-icon me-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Low Stock</h6>
                    <h3 class="mb-0 fw-bold">{{ calculateLowStock() }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stat-icon me-3">
                    <i class="fas fa-tags fa-2x text-info"></i>
                  </div>
                  <div>
                    <h6 class="text-muted mb-1">Categories</h6>
                    <h3 class="mb-0 fw-bold">{{ categories.length }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Always Visible Search & Filters -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body p-3">
            <div class="row g-2 align-items-end">
              <!-- Search -->
              <div class="col-md-4">
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-light border-0">
                    <i class="fas fa-search text-muted"></i>
                  </span>
                  <input type="text" 
                         v-model="searchQuery" 
                         placeholder="Search products..." 
                         class="form-control form-control-sm border-0" />
                  <button v-if="searchQuery" 
                          @click="searchQuery = ''" 
                          class="btn btn-outline-secondary border-0 btn-sm" 
                          type="button">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              
              <!-- Category Filter -->
              <div class="col-md-3">
                <select v-model="filterCategory" class="form-select form-select-sm">
                  <option value="all">All Categories</option>
                  <option v-for="category in categories" 
                          :key="category.category_id" 
                          :value="category.category_id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              
              <!-- Tags Filter Dropdown -->
              <div class="col-md-3" >
                <select v-model="filterCategory" class="form-select form-select-sm">
                  <option value="all">All Tags</option>
                  <option v-for="tag in allTags" 
                          :key="tag.tag_id" 
                          :value="tag.tag_id">
                    {{ tag.name }}
                  </option>
                </select>
              </div>
              
              <!-- Clear Filters -->
              <div class="col-md-2">
                <button @click="clearFilters" 
                        class="btn btn-outline-secondary btn-sm w-100">
                  <i class="fas fa-times me-1"></i>Clear
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Small Cards Grid View -->
        <div class="row g-3">
          <div v-for="product in filteredProducts" 
               :key="product.product_id" 
               class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
            <div class="product-small-card card border-0 shadow-sm h-100">
              <!-- Product Image -->
              <div class="product-image-small position-relative">
                <img :src="getProductImage(product.image)" 
                     :alt="product.name" 
                     class="card-img-top"
                     @error="handleImageError">
                
                <!-- Stock Badge -->
                <div class="position-absolute top-0 end-0 p-1">
                  <span class="badge badge-sm" 
                        :class="getProductStatusClass(product.stock)">
                    {{ product.stock }}
                  </span>
                </div>
              </div>
              
              <!-- Product Body -->
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1 small text-truncate" 
                    :title="product.name">
                  {{ product.name }}
                </h6>
                <div class="mb-1">
                  <span class="text-success fw-bold small">
                    {{ formatPrice(product.price) }}
                  </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="badge bg-light text-dark border small">
                    {{ getCategoryName(product.category_id) }}
                  </span>
                  <span class="badge small" 
                        :class="product.product_type === 'onstock' ? 'bg-info' : 'bg-primary'">
                    {{ product.product_type === 'onstock' ? 'T' : 'F' }}
                  </span>
                </div>
                
                <!-- Quick Actions -->
                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                  <button @click="editProduct(product)" 
                          class="btn btn-sm btn-outline-warning px-2 py-0" 
                          title="Edit">
                    <i class="fas fa-edit fa-xs"></i>
                  </button>
                  <button @click="viewProduct(product)" 
                          class="btn btn-sm btn-outline-info px-2 py-0" 
                          title="View">
                    <i class="fas fa-eye fa-xs"></i>
                  </button>
                  <!-- NEW: Manage Images Button -->
                  <button @click="manageImages(product)" 
                          class="btn btn-sm btn-outline-primary px-2 py-0" 
                          title="Manage Images">
                    <i class="fas fa-images fa-xs"></i>
                  </button>
                  <button @click="deleteProduct(product)" 
                          class="btn btn-sm btn-outline-danger px-2 py-0" 
                          title="Delete">
                    <i class="fas fa-trash fa-xs"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredProducts.length === 0" class="text-center py-5">
          <div class="empty-state">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <h5 class="fw-bold mb-2">No products found</h5>
            <p class="text-muted mb-3">Try adjusting your search filters</p>
            <button @click="clearFilters" 
                    class="btn btn-outline-primary btn-sm">
              <i class="fas fa-times me-1"></i>Clear Filters
            </button>
          </div>
        </div>

        <!-- Pagination - Fixed v-html issue -->
        <div v-if="products?.data?.length > 0 && products?.links" class="mt-4">
          <nav aria-label="Product pagination">
            <ul class="pagination justify-content-center pagination-sm">
              <li v-for="(link, index) in products.links" 
                  :key="index" 
                  class="page-item" 
                  :class="{ active: link.active, disabled: !link.url }">
                <Link v-if="link.url" 
                      :href="link.url" 
                      class="page-link">
                  <!-- Fixed: Render pagination icons safely -->
                  <span v-if="link.label.includes('&laquo; Previous')">
                    <i class="fas fa-chevron-left"></i> Previous
                  </span>
                  <span v-else-if="link.label.includes('Next &raquo;')">
                    Next <i class="fas fa-chevron-right"></i>
                  </span>
                  <span v-else-if="link.label.includes('&laquo;')">
                    <i class="fas fa-angle-double-left"></i>
                  </span>
                  <span v-else-if="link.label.includes('&raquo;')">
                    <i class="fas fa-angle-double-right"></i>
                  </span>
                  <span v-else-if="link.label.includes('&lsaquo;')">
                    <i class="fas fa-angle-left"></i>
                  </span>
                  <span v-else-if="link.label.includes('&rsaquo;')">
                    <i class="fas fa-angle-right"></i>
                  </span>
                  <span v-else>
                    {{ link.label }}
                  </span>
                </Link>
                <span v-else class="page-link">{{ link.label }}</span>
              </li>
            </ul>
          </nav>
        </div>
      </div>

      <!-- Bootstrap Modal for Product Form -->
      <div class="modal fade" :class="{ show: showFormModal }" 
           :style="{ display: showFormModal ? 'block' : 'none' }" 
           tabindex="-1"
           @click.self="hideFormModal"
           style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">
                <i class="fas me-2" :class="isEditing ? 'fa-edit' : 'fa-plus-circle'"></i>
                {{ isEditing ? 'Edit Product' : 'Add New Product' }}
              </h5>
              <button type="button" 
                      class="btn-close btn-close-white" 
                      @click="hideFormModal"
                      aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="handleFormSubmit">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">Product Name *</label>
                    <input v-model="formData.name" 
                           type="text" 
                           class="form-control form-control-sm" 
                           placeholder="Enter product name" 
                           required
                           @input="generateReference">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">Reference</label>
                    <input v-model="formData.reference" 
                           type="text" 
                           class="form-control form-control-sm bg-light" 
                           readonly>
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">Category *</label>
                    <select v-model="formData.category_id" 
                            class="form-select form-select-sm" 
                            required>
                      <option value="">Select Category</option>
                      <option v-for="category in categories" 
                              :key="category.category_id" 
                              :value="category.category_id">
                        {{ category.name }}
                      </option>
                    </select>
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">Product Type</label>
                    <select v-model="formData.product_type" 
                            class="form-select form-select-sm"
                            @change="handleProductTypeChange">
                      <option value="one-time">One-Time</option>
                      <option value="onstock">On-Stock</option>
                    </select>
                  </div>
                  
                  <!-- One-Time Pricing -->
                  <div v-if="formData.product_type === 'one-time'" class="col-md-6">
                    <label class="form-label small fw-bold">Price *</label>
                    <div class="input-group input-group-sm">
                      <input v-model.number="formData.price" 
                             type="number" 
                             step="0.01" 
                             min="0.01"
                             class="form-control" 
                             required>
                      <span class="input-group-text">Birr</span>
                    </div>
                  </div>
                  
                  <div v-if="formData.product_type === 'one-time'" class="col-md-6">
                    <label class="form-label small fw-bold">Stock *</label>
                    <div class="input-group input-group-sm">
                      <input v-model.number="formData.stock" 
                             type="number" 
                             min="1"
                             class="form-control" 
                             required>
                      <span class="input-group-text">units</span>
                    </div>
                  </div>
                  
                  <!-- On-Stock Price Tiers -->
                  <div v-if="formData.product_type === 'onstock'" class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label small fw-bold">Price Tiers *</label>
                      <button type="button" 
                              @click="addPriceTier" 
                              class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>Add Tier
                      </button>
                    </div>
                    
                    <div v-for="(tier, index) in formData.price_tiers" 
                         :key="index" 
                         class="card mb-2">
                      <div class="card-body p-2">
                        <div class="row g-2">
                          <div class="col-md-6">
                            <label class="form-label extra-small">Price (Birr)</label>
                            <input type="number" 
                                   v-model.number="tier.price" 
                                   class="form-control form-control-sm" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0.01" 
                                   required>
                          </div>
                          <div class="col-md-5">
                            <label class="form-label extra-small">Stock</label>
                            <input type="number" 
                                   v-model.number="tier.stock" 
                                   class="form-control form-control-sm" 
                                   placeholder="Quantity" 
                                   min="1" 
                                   required>
                          </div>
                          <div class="col-md-1 d-flex align-items-end">
                            <button v-if="formData.price_tiers.length > 1" 
                                    type="button" 
                                    @click="removePriceTier(index)"
                                    class="btn btn-sm btn-outline-danger">
                              <i class="fas fa-times"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Tags -->
                  <div v-if="allTags.length > 0" class="col-12">
                    <label class="form-label small fw-bold">Tags</label>
                    <div class="d-flex flex-wrap gap-2">
                      <div v-for="tag in allTags" 
                           :key="tag.tag_id" 
                           class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               :value="tag.tag_id" 
                               :id="`tag-${tag.tag_id}`" 
                               v-model="formData.selectedTags">
                        <label class="form-check-label small" 
                               :for="`tag-${tag.tag_id}`">
                          {{ tag.name }}
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Description -->
                  <div class="col-12">
                    <label class="form-label small fw-bold">Description</label>
                    <textarea v-model="formData.description" 
                              class="form-control form-control-sm" 
                              rows="2"
                              placeholder="Optional description"></textarea>
                  </div>
                  
                  <!-- Image Upload -->
                  <div class="col-12">
                    <label class="form-label small fw-bold">Product Image</label>
                    <input type="file" 
                           @change="handleImageChange" 
                           class="form-control form-control-sm" 
                           accept="image/*">
                    <div v-if="imagePreview" class="mt-2">
                      <img :src="imagePreview" 
                           alt="Preview" 
                           class="img-thumbnail" 
                           style="max-height: 100px;">
                    </div>
                  </div>
                </div>
                
                <div class="modal-footer">
                  <button type="button" 
                          class="btn btn-secondary btn-sm" 
                          @click="hideFormModal">
                    Cancel
                  </button>
                  <button type="submit" 
                          class="btn btn-primary btn-sm"
                          :disabled="isSubmitting">
                    <span v-if="isSubmitting" 
                          class="spinner-border spinner-border-sm me-1"></span>
                    {{ isEditing ? 'Update' : 'Save' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Bootstrap Modal for Product Details -->
      <div class="modal fade" :class="{ show: showViewModal }" 
           :style="{ display: showViewModal ? 'block' : 'none' }" 
           tabindex="-1"
           @click.self="hideViewModal"
           style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-info text-white">
              <h5 class="modal-title">
                <i class="fas fa-eye me-2"></i>Product Details
              </h5>
              <button type="button" 
                      class="btn-close btn-close-white" 
                      @click="hideViewModal"
                      aria-label="Close"></button>
            </div>
            <div class="modal-body" v-if="selectedProduct">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <img :src="getProductImage(selectedProduct.image)" 
                       :alt="selectedProduct.name" 
                       class="img-fluid rounded shadow-sm">
                </div>
                <div class="col-md-8">
                  <h4 class="fw-bold">{{ selectedProduct.name }}</h4>
                  <div class="mb-3">
                    <!-- Reference removed from visibility -->
                    <span class="badge me-2" :class="getCategoryClass(selectedProduct.category_id)">
                      {{ getCategoryName(selectedProduct.category_id) }}
                    </span>
                    <span class="badge ms-2" 
                          :class="selectedProduct.product_type === 'onstock' ? 'bg-info' : 'bg-primary'">
                      {{ selectedProduct.product_type === 'onstock' ? 'Tiered' : 'Fixed' }}
                    </span>
                  </div>
                  
                  <div class="row mb-3">
                    <div class="col-6">
                      <div class="fw-bold text-muted small">Price</div>
                      <div class="h5 text-success">{{ formatPrice(selectedProduct.price) }}</div>
                    </div>
                    <div class="col-6">
                      <div class="fw-bold text-muted small">Stock</div>
                      <div class="h5" :class="getStockColor(selectedProduct.stock)">
                        {{ selectedProduct.stock }} units
                      </div>
                    </div>
                  </div>
                  
                  <!-- Reference in table format -->
                  <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered">
                      <tbody>
                        <tr>
                          <td class="fw-bold">Reference Code</td>
                          <td>
                            <span class="badge bg-secondary">
                              {{ selectedProduct.reference }}
                            </span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  
                  <div class="mb-3">
                    <div class="fw-bold text-muted small mb-2">Description</div>
                    <p>{{ selectedProduct.description || 'No description provided' }}</p>
                  </div>
                  
                  <div v-if="selectedProduct.tags?.length > 0" class="mb-3">
                    <div class="fw-bold text-muted small mb-2">Tags</div>
                    <div class="d-flex flex-wrap gap-1">
                      <span v-for="tag in selectedProduct.tags" 
                            :key="tag.tag_id" 
                            class="badge bg-light text-dark border">
                        {{ tag.name }}
                      </span>
                    </div>
                  </div>
                  
                  <div v-if="selectedProduct.product_type === 'onstock' && selectedProduct.price_tiers" class="mb-3">
                    <div class="fw-bold text-muted small mb-2">Price Tiers</div>
                    <div class="table-responsive">
                      <table class="table table-sm table-bordered">
                        <thead class="table-light">
                          <tr>
                            <th>Tier</th>
                            <th>Price</th>
                            <th>Stock</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(tier, index) in parsePriceTiers(selectedProduct.price_tiers)" 
                              :key="index">
                            <td>Tier {{ index + 1 }}</td>
                            <td class="text-success">{{ formatPrice(tier.price) }}</td>
                            <td>{{ tier.stock }} units</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" 
                      class="btn btn-secondary btn-sm" 
                      @click="hideViewModal">
                Close
              </button>
              <button type="button" 
                      class="btn btn-primary btn-sm" 
                      @click="editProduct(selectedProduct)">
                <i class="fas fa-edit me-1"></i>Edit Product
              </button>
              <!-- NEW: Manage Images Button -->
              <button type="button" 
                      class="btn btn-info btn-sm" 
                      @click="manageImages(selectedProduct)">
                <i class="fas fa-images me-1"></i>Manage Images
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  products: Object,
  categories: Array,
  allTags: Array,
  success: String,
  error: String
});

// State
const searchQuery = ref('');
const filterCategory = ref('all');
const filterTags = ref([]);
const showFormModal = ref(false);
const showViewModal = ref(false);
const selectedProduct = ref(null);
const isEditing = ref(false);
const editingProductId = ref(null);
const isSubmitting = ref(false);
const imagePreview = ref(null);
const fileInput = ref(null);

// Form data
const formData = ref({
  name: '',
  reference: '',
  description: '',
  product_type: 'one-time',
  price: '',
  stock: 1,
  category_id: '',
  selectedTags: [],
  price_tiers: [{ price: '', stock: 1 }],
  image: null
});

// Computed properties
const productsArray = computed(() => {
  if (!props.products) return [];
  return props.products.data || props.products || [];
});

const filteredProducts = computed(() => {
  if (productsArray.value.length === 0) return [];

  let filtered = productsArray.value.filter(product => {
    const matchesSearch = !searchQuery.value || 
      product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      product.reference?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      product.description?.toLowerCase().includes(searchQuery.value.toLowerCase());
    
    const matchesCategory = filterCategory.value === 'all' || 
      product.category_id?.toString() === filterCategory.value.toString();
    
    const matchesTags = filterTags.value.length === 0 || 
      filterTags.value.some(tagId => 
        product.tags?.some(tag => tag.tag_id == tagId)
      );
    
    return matchesSearch && matchesCategory && matchesTags;
  });

  return filtered;
});

// Add these calculation functions to your existing script
const calculateTotalValue = () => {
  return filteredProducts.value.reduce((total, product) => {
    const price = parseFloat(product.price) || 0;
    const stock = parseInt(product.stock) || 0;
    return total + (price * stock);
  }, 0);
};

const calculateLowStock = () => {
  return filteredProducts.value.filter(product => {
    const stock = parseInt(product.stock) || 0;
    return stock > 0 && stock < 10;
  }).length;
};

// Helper Functions
const getProductImage = (imagePath) => {
  if (!imagePath) return '/images/default-product.jpg';
  if (imagePath instanceof File) {
    return URL.createObjectURL(imagePath);
  }
  return imagePath.startsWith('http') ? imagePath : `/storage/${imagePath}`;
};

const handleImageError = (event) => {
  event.target.src = '/images/default-product.jpg';
};

const getCategoryName = (categoryId) => {
  if (!props.categories || !categoryId) return 'Uncategorized';
  const category = props.categories.find(cat => cat.category_id == categoryId);
  return category ? category.name : 'Uncategorized';
};

const getCategoryClass = (categoryId) => {
  const colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
  const index = categoryId ? categoryId % colors.length : 0;
  return colors[index];
};

const getProductStatusClass = (stock) => {
  const stockNum = parseInt(stock) || 0;
  if (stockNum === 0) return 'bg-danger';
  if (stockNum < 10) return 'bg-warning';
  return 'bg-success';
};

const formatPrice = (price) => {
  const num = parseFloat(price) || 0;
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(num) + ' Birr';
};

const getStockColor = (stock) => {
  const stockNum = parseInt(stock) || 0;
  if (stockNum === 0) return 'text-danger';
  if (stockNum < 10) return 'text-warning';
  return 'text-success';
};

const parsePriceTiers = (priceTiers) => {
  if (!priceTiers) return [];
  
  if (typeof priceTiers === 'string') {
    try {
      return JSON.parse(priceTiers);
    } catch (e) {
      console.error('Error parsing price tiers:', e);
      return [];
    }
  }
  
  return priceTiers;
};

// Methods
const showAddForm = () => {
  resetForm();
  showFormModal.value = true;
  isEditing.value = false;
  editingProductId.value = null;
  generateReference();
};

const hideFormModal = () => {
  showFormModal.value = false;
  resetForm();
};

const viewProduct = (product) => {
  selectedProduct.value = product;
  showViewModal.value = true;
};

const hideViewModal = () => {
  showViewModal.value = false;
  selectedProduct.value = null;
};

const editProduct = (product) => {
  isEditing.value = true;
  editingProductId.value = product.product_id;
  showViewModal.value = false;
  
  // Set form data from product
  formData.value = {
    name: product.name || '',
    reference: product.reference || '',
    description: product.description || '',
    product_type: product.product_type || 'one-time',
    price: product.price || '',
    stock: product.stock || 1,
    category_id: product.category_id || '',
    selectedTags: product.tags?.map(tag => tag.tag_id) || [],
    price_tiers: parsePriceTiers(product.price_tiers) || [{ price: '', stock: 1 }],
    image: product.image || null
  };
  
  imagePreview.value = product.image ? getProductImage(product.image) : null;
  showFormModal.value = true;
};

// NEW METHOD: Manage additional images
const manageImages = (product) => {
  if (product && product.product_id) {
    router.visit(`/products/${product.product_id}/add-images`);
  }
};

const generateReference = () => {
  if (formData.value.name && !isEditing.value) {
    const name = formData.value.name.trim();
    if (name.length > 0) {
      const words = name.split(' ').filter(w => w.length > 0);
      const prefix = words.map(w => w.substring(0, 3).toUpperCase()).join('');
      const timestamp = Date.now().toString().slice(-4);
      formData.value.reference = `${prefix}-${timestamp}`;
    }
  }
};

const handleProductTypeChange = () => {
  if (formData.value.product_type === 'onstock' && formData.value.price_tiers.length === 0) {
    formData.value.price_tiers = [{ price: '', stock: 1 }];
  }
};

const addPriceTier = () => {
  formData.value.price_tiers.push({ price: '', stock: 1 });
};

const removePriceTier = (index) => {
  if (formData.value.price_tiers.length > 1) {
    formData.value.price_tiers.splice(index, 1);
  }
};

const handleImageChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      alert('File size must be less than 2MB');
      return;
    }
    if (!file.type.startsWith('image/')) {
      alert('Please upload an image file');
      return;
    }
    
    formData.value.image = file;
    
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const resetForm = () => {
  formData.value = {
    name: '',
    reference: '',
    description: '',
    product_type: 'one-time',
    price: '',
    stock: 1,
    category_id: '',
    selectedTags: [],
    price_tiers: [{ price: '', stock: 1 }],
    image: null
  };
  imagePreview.value = null;
  isEditing.value = false;
  editingProductId.value = null;
};

const clearFilters = () => {
  searchQuery.value = '';
  filterCategory.value = 'all';
  filterTags.value = [];
};

const handleFormSubmit = async () => {
  // Validate category selection
  if (!formData.value.category_id) {
    alert('Please select a category');
    return;
  }
  
  isSubmitting.value = true;
  
  try {
    const calculateTotalTierStock = () => {
      return formData.value.price_tiers.reduce((sum, tier) => sum + (parseInt(tier.stock) || 0), 0);
    };

    const form = useForm({
      name: formData.value.name,
      reference: formData.value.reference,
      description: formData.value.description,
      product_type: formData.value.product_type,
      category_id: formData.value.category_id,
      selectedTags: formData.value.selectedTags,
      price: formData.value.product_type === 'one-time' ? formData.value.price : (formData.value.price_tiers[0]?.price || 0),
      price_tiers: formData.value.product_type === 'onstock' ? formData.value.price_tiers : [],
      stock: formData.value.product_type === 'one-time' ? formData.value.stock : calculateTotalTierStock(),
      image: formData.value.image,
      _method: isEditing.value ? 'PUT' : 'POST'
    });

    if (isEditing.value && editingProductId.value) {
      await form.post(route('products.update', editingProductId.value), {
        onSuccess: () => {
          resetForm();
          hideFormModal();
          isSubmitting.value = false;
        },
        onError: (errors) => {
          console.error('Update errors:', errors);
          isSubmitting.value = false;
        }
      });
    } else {
      await form.post(route('products.store'), {
        onSuccess: () => {
          resetForm();
          hideFormModal();
          isSubmitting.value = false;
        },
        onError: (errors) => {
          console.error('Create errors:', errors);
          isSubmitting.value = false;
        }
      });
    }

  } catch (error) {
    console.error('Error submitting form:', error);
    isSubmitting.value = false;
  }
};

const deleteProduct = async (product) => {
  if (confirm(`Are you sure you want to delete "${product.name}"? This action cannot be undone.`)) {
    const form = useForm({});
    await form.delete(route('products.destroy', product.product_id));
  }
};
</script>

<style scoped>
.products-management-page {
  background: #f8f9fa;
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
}

/* Subtle Animated Background */
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

.bubble-4 {
  width: 180px;
  height: 180px;
  top: 30%;
  right: 20%;
  animation-delay: 15s;
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

/* Small Cards */
.product-small-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
}

.product-small-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.product-image-small {
  height: 120px;
  overflow: hidden;
}

.product-image-small img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-small-card:hover .product-image-small img {
  transform: scale(1.05);
}

/* Badge Sizes */
.badge-sm {
  font-size: 0.65rem;
  padding: 0.2rem 0.4rem;
}

.extra-small {
  font-size: 0.75rem;
}

/* Modal Styles */
.modal {
  backdrop-filter: blur(3px);
}

.modal-content {
  border-radius: 10px;
  border: none;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.modal-header {
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}

/* Form Controls */
.form-control-sm {
  border-radius: 5px;
  border: 1px solid #dee2e6;
}

.form-control-sm:focus {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Responsive Grid */
@media (max-width: 576px) {
  .col-6 {
    padding: 0.25rem;
  }
  
  .product-image-small {
    height: 100px;
  }
  
  .stat-icon {
    width: 40px;
    height: 40px;
  }
}

/* Button Hover Effects */
.btn-sm {
  transition: all 0.2s ease;
}

.btn-sm:hover {
  transform: translateY(-2px);
}

/* Table Styles */
.table-sm th,
.table-sm td {
  padding: 0.5rem;
}

.table-bordered {
  border-color: #dee2e6;
}

.table-light {
  background-color: #f8f9fa;
}

/* Pagination */
.pagination-sm .page-link {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  border-radius: 5px;
  margin: 0 2px;
}

.pagination-sm .page-item.active .page-link {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

/* Animation for modal appearance */
.modal.fade.show {
  animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>