<template>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <div class="products-page container py-5">
    
    <!-- Back to Home Button -->
    <div class="mb-4">
      <a href="/" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Home
      </a>
    </div>

    <!-- Flash Messages -->
    <div v-if="$page.props.success" class="alert alert-success alert-dismissible fade show mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      {{ $page.props.success }}
      <button type="button" class="btn-close" @click="clearFlashMessage"></button>
    </div>
    
    <div v-if="$page.props.error" class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
      <i class="bi bi-exclamation-circle-fill me-2"></i>
      {{ $page.props.error }}
      <button type="button" class="btn-close" @click="clearFlashMessage"></button>
    </div>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 fw-bold mb-1">Product Management</h1>
        <p class="text-muted mb-0">Add and manage your products with advanced pricing options</p>
      </div>
      <div class="d-flex gap-2">
        <button @click="toggleFilters" class="btn btn-outline-secondary">
          <i class="bi bi-funnel me-1"></i>Filters
        </button>
        <button @click="exportProducts" class="btn btn-outline-success">
          <i class="bi bi-download me-1"></i>Export
        </button>
      </div>
    </div>

    <!-- Filters Section -->
    <div v-if="showFilters" class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Search Products</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Search by name, reference..."
                class="form-control"
              />
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label">Category</label>
            <select v-model="filterCategory" class="form-select">
              <option value="all">All Categories</option>
              <option v-for="category in categories" :key="category.category_id" :value="category.category_id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Sort By</label>
            <select v-model="sortBy" class="form-select">
              <option value="name">Name</option>
              <option value="price">Price</option>
              <option value="stock">Stock</option>
              <option value="created_at">Date Added</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Order</label>
            <button @click="toggleSortOrder" class="btn btn-outline-secondary w-100">
              <i class="bi" :class="sortOrder === 'asc' ? 'bi-sort-down' : 'bi-sort-up'"></i>
              {{ sortOrder === 'asc' ? 'Asc' : 'Desc' }}
            </button>
          </div>
        </div>
        
        <!-- Tag Filters - DYNAMIC FROM DATABASE -->
        <div v-if="allTags.length > 0" class="row mt-3">
          <div class="col-12">
            <label class="form-label">Filter by Tags</label>
            <div class="d-flex flex-wrap gap-2">
              <div v-for="tag in allTags" :key="tag.tag_id" class="form-check">
                <input class="form-check-input" type="checkbox" :value="tag.tag_id" 
                       :id="`filter-tag-${tag.tag_id}`" v-model="filterTags">
                <label class="form-check-label small" :for="`filter-tag-${tag.tag_id}`">
                  {{ tag.name }}
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== Add/Edit Product Form ===== -->
    <div class="card mb-5 border-0 shadow">
      <div class="card-header" :class="isEditing ? 'bg-warning' : 'bg-primary'">
        <h3 class="mb-0 text-white">
          <i class="bi" :class="isEditing ? 'bi-pencil' : 'bi-plus-circle'"></i>
          {{ isEditing ? 'Edit Product' : 'Add a New Product' }}
        </h3>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleFormSubmit" class="needs-validation" novalidate>
          <div class="row g-4">
            <!-- Left Column -->
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label required">Product Name</label>
                <input v-model="formData.name" type="text" class="form-control" required />
                <div class="invalid-feedback">Please enter a product name</div>
              </div>

              <div class="mb-3">
                <label class="form-label required">Reference Code</label>
                <input v-model="formData.reference" type="text" class="form-control" required />
                <div class="invalid-feedback">Please enter a reference code</div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label required">Product Type</label>
                    <select v-model="formData.product_type" @change="handleProductTypeChange" class="form-select" required>
                      <option value="">Select Type</option>
                      <option value="one-time">One-Time Product</option>
                      <option value="onstock">On-Stock Product</option>
                    </select>
                    <div class="form-text small">
                      <span v-if="formData.product_type === 'one-time'">One price for all customers</span>
                      <span v-else>Different prices based on quantity</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label required">Category</label>
                    <select v-model="formData.category_id" class="form-select" required>
                      <option value="">Select Category</option>
                      <option v-for="category in categories" :key="category.category_id" :value="category.category_id">
                        {{ category.name }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- One-Time Price -->
              <div v-if="formData.product_type === 'one-time'" class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label required">Price ($)</label>
                    <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input v-model.number="formData.price" type="number" step="0.01" min="0.01" class="form-control" required />
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label required">Stock Available</label>
                    <input v-model.number="formData.stock" type="number" class="form-control" required min="0" />
                    <div class="form-text small">Number of units available</div>
                  </div>
                </div>
              </div>

              <!-- Stock Field for onstock products -->
              <div v-if="formData.product_type === 'onstock'" class="mb-3">
                <label class="form-label required">Initial Stock</label>
                <input v-model.number="formData.stock" type="number" class="form-control" required min="0" />
                <div class="form-text small">Initial quantity in inventory</div>
              </div>

              <!-- Tags Selection - DYNAMIC FROM DATABASE -->
              <div v-if="allTags.length > 0" class="mb-3">
                <label class="form-label">Product Tags</label>
                <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto;">
                  <div class="row">
                    <div v-for="tag in allTags" :key="tag.tag_id" class="col-md-6 mb-2">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" :value="tag.tag_id" 
                               :id="`tag-${tag.tag_id}`" v-model="formData.selectedTags">
                        <label class="form-check-label small" :for="`tag-${tag.tag_id}`">
                          {{ tag.name }}
                        </label>
                      </div>
                    </div>
                  </div>
                  <div v-if="formData.selectedTags.length > 0" class="mt-2">
                    <small class="text-success">
                      <i class="bi bi-check-circle me-1"></i>
                      {{ formData.selectedTags.length }} tag(s) selected
                    </small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea v-model="formData.description" class="form-control" rows="4" placeholder="Enter detailed product description..."></textarea>
              </div>

              <!-- On-Stock Pricing Table -->
              <div v-if="formData.product_type === 'onstock'" class="mb-4">
                <label class="form-label required">Quantity-Based Pricing</label>
                <div class="table-responsive">
                  <table class="table table-sm table-bordered">
                    <thead class="table-light">
                      <tr>
                        <th>Min Qty</th>
                        <th>Max Qty</th>
                        <th>Price per Unit</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(tier, index) in formData.price_tiers" :key="index">
                        <td>
                          <input type="number" v-model.number="tier.min_quantity" class="form-control form-control-sm" min="1" required />
                        </td>
                        <td>
                          <input type="number" v-model.number="tier.max_quantity" class="form-control form-control-sm" :min="tier.min_quantity + 1" required />
                        </td>
                        <td>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>
                            <input type="number" v-model.number="tier.price" class="form-control" step="0.01" min="0.01" required />
                          </div>
                        </td>
                        <td class="text-center">
                          <button v-if="index > 0" type="button" @click="removePriceTier(index)" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <button type="button" @click="addPriceTier" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-plus me-1"></i>Add Price Tier
                </button>
                <div class="form-text small">Define different prices based on quantity purchased</div>
              </div>

              <!-- Image Upload -->
              <div class="mb-3">
                <label class="form-label">Product Image</label>
                <div class="border rounded p-3 text-center" @click="triggerFileInput" style="cursor: pointer;">
                  <input type="file" ref="fileInput" @change="handleImageChange" class="d-none" accept="image/*" />
                  
                  <div v-if="!formData.image && !imagePreview">
                    <i class="bi bi-cloud-arrow-up display-6 text-muted mb-3"></i>
                    <p class="text-muted mb-1">Click to upload product image</p>
                    <p class="text-muted small">Recommended: 800x600px, max 2MB</p>
                  </div>
                  
                  <div v-else>
                    <img :src="imagePreview || getProductImage(formData.image)" 
                         alt="Preview" 
                         class="img-fluid rounded" 
                         style="max-height: 150px;" />
                  </div>
                </div>
                
                <div v-if="imagePreview || formData.image" class="mt-2 text-center">
                  <button type="button" @click="removeImage" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>Remove Image
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary px-4" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
              <i class="bi" :class="isEditing ? 'bi-check-circle' : 'bi-plus-circle'"></i>
              {{ isEditing ? 'Update Product' : 'Add Product' }}
            </button>
            <button type="button" @click="resetForm" class="btn btn-outline-secondary ms-2">
              <i class="bi bi-arrow-clockwise me-2"></i>Reset Form
            </button>
            <button v-if="isEditing" type="button" @click="cancelEdit" class="btn btn-outline-warning ms-2">
              <i class="bi bi-x-circle me-2"></i>Cancel Edit
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4 g-3">
      <div class="col-md-3">
        <div class="card border-0 bg-primary bg-opacity-10">
          <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-muted mb-1">Total Products</h6>
                <h4 class="fw-bold mb-0">{{ filteredProducts.length }}</h4>
              </div>
              <i class="bi bi-box-seam text-primary fs-3"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 bg-success bg-opacity-10">
          <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-muted mb-1">Total Value</h6>
                <h4 class="fw-bold mb-0">{{ formatPrice(totalValue) }}</h4>
              </div>
              <i class="bi bi-cash-stack text-success fs-3"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 bg-warning bg-opacity-10">
          <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-muted mb-1">Low Stock</h6>
                <h4 class="fw-bold mb-0">{{ lowStockCount }}</h4>
              </div>
              <i class="bi bi-exclamation-triangle text-warning fs-3"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 bg-info bg-opacity-10">
          <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-muted mb-1">Categories</h6>
                <h4 class="fw-bold mb-0">{{ categories.length }}</h4>
              </div>
              <i class="bi bi-tags text-info fs-3"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== Display Products ===== -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="h4 fw-bold mb-0">All Products ({{ filteredProducts.length }})</h2>
      <div class="btn-group">
        <button @click="viewMode = 'grid'" class="btn btn-outline-secondary" :class="{ 'active': viewMode === 'grid' }">
          <i class="bi bi-grid"></i>
        </button>
        <button @click="viewMode = 'list'" class="btn btn-outline-secondary" :class="{ 'active': viewMode === 'list' }">
          <i class="bi bi-list"></i>
        </button>
      </div>
    </div>

    <!-- Grid View -->
    <div v-if="filteredProducts.length === 0" class="text-center py-5">
      <div class="empty-state">
        <i class="bi bi-box-seam display-1 text-muted"></i>
        <h4 class="mt-4">No products found</h4>
        <p class="text-muted">Add your first product or adjust your search filters</p>
      </div>
    </div>

    <!-- Grid View -->
    <div v-else-if="viewMode === 'grid'" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <div v-for="product in filteredProducts" :key="product.product_id" class="col">
        <div class="card h-100 shadow-sm border-0 product-card">
          <div class="position-relative">
            <img :src="getProductImage(product.image)" 
                 :alt="product.name" 
                 class="card-img-top product-image"
                 @error="handleImageError">
            <div class="position-absolute top-0 end-0 m-2">
              <span class="badge" :class="getProductStatusClass(product.stock)">
                {{ getProductStatus(product.stock) }}
              </span>
            </div>
            <div class="position-absolute top-0 start-0 m-2">
              <span class="badge bg-primary">
                {{ product.product_type === 'onstock' ? 'On-Stock' : 'One-Time' }}
              </span>
            </div>
          </div>
          
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title mb-0">{{ product.name }}</h5>
              <span class="fw-bold text-success">{{ formatPrice(product.price) }}</span>
            </div>
            
            <div class="mb-2">
              <span class="badge bg-light text-dark border me-2">{{ product.reference }}</span>
              <span class="badge" :class="getCategoryClass(product.category_id)">
                {{ getCategoryName(product.category_id) }}
              </span>
            </div>
            
            <p class="card-text text-muted small mb-3">{{ truncateDescription(product.description) }}</p>
            
            <!-- Tags -->
            <div class="mb-3">
              <div class="d-flex flex-wrap gap-1">
                <span v-for="tag in product.tags" :key="tag.tag_id" class="badge bg-light text-dark border small">
                  {{ tag.name }}
                </span>
              </div>
            </div>
            
            <!-- Stock Info -->
            <div class="mt-auto">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Stock</span>
                <span class="fw-bold" :class="getStockColor(product.stock)">{{ product.stock }}</span>
              </div>
              <div class="progress" style="height: 5px;">
                <div class="progress-bar" 
                     :class="getStockProgressClass(product.stock)"
                     :style="{ width: getStockPercentage(product.stock) + '%' }"></div>
              </div>
            </div>
          </div>
          
          <div class="card-footer bg-white border-top-0 pt-0">
            <div class="d-flex justify-content-between">
              <button @click="editProduct(product)" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-pencil"></i> Edit
              </button>
              <button @click="deleteProduct(product)" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash"></i> Delete
              </button>
              <button @click="viewProductDetails(product)" class="btn btn-sm btn-outline-info">
                <i class="bi bi-eye"></i> View
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- List View -->
    <div v-else class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Image</th>
                <th @click="sortByColumn('name')" class="cursor-pointer">
                  Name <i class="bi bi-arrow-down-up small"></i>
                </th>
                <th>Reference</th>
                <th @click="sortByColumn('price')" class="cursor-pointer">
                  Price <i class="bi bi-arrow-down-up small"></i>
                </th>
                <th>Category</th>
                <th @click="sortByColumn('stock')" class="cursor-pointer">
                  Stock <i class="bi bi-arrow-down-up small"></i>
                </th>
                <th>Type</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in filteredProducts" :key="product.product_id">
                <td>
                  <img :src="getProductImage(product.image)" 
                       :alt="product.name"
                       class="product-thumbnail rounded"
                       @error="handleImageError">
                </td>
                <td>
                  <strong>{{ product.name }}</strong>
                  <p class="text-muted small mb-0">{{ truncateDescription(product.description, 50) }}</p>
                  <div class="d-flex flex-wrap gap-1 mt-1">
                    <span v-for="tag in product.tags" :key="tag.tag_id" class="badge bg-light text-dark border small">
                      {{ tag.name }}
                    </span>
                  </div>
                </td>
                <td>
                  <span class="badge bg-secondary">{{ product.reference }}</span>
                </td>
                <td>
                  <span class="fw-bold text-success">{{ formatPrice(product.price) }}</span>
                </td>
                <td>
                  <span class="badge" :class="getCategoryClass(product.category_id)">
                    {{ getCategoryName(product.category_id) }}
                  </span>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <span :class="getStockColor(product.stock)" class="fw-bold me-2">{{ product.stock }}</span>
                    <div class="progress flex-grow-1" style="width: 60px; height: 6px;">
                      <div class="progress-bar" 
                           :class="getStockProgressClass(product.stock)"
                           :style="{ width: getStockPercentage(product.stock) + '%' }"></div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge" :class="product.product_type === 'onstock' ? 'bg-info' : 'bg-primary'">
                    {{ product.product_type === 'onstock' ? 'On-Stock' : 'One-Time' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button @click="editProduct(product)" class="btn btn-outline-warning">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="deleteProduct(product)" class="btn btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="props.products?.links" class="d-flex justify-content-center mt-4">
      <nav>
        <ul class="pagination">
          <li v-for="(link, index) in props.products.links" :key="index" 
              :class="['page-item', link.active ? 'active' : '', link.url ? '' : 'disabled']">
            <a class="page-link" :href="link.url || '#'" v-html="link.label"></a>
          </li>
        </ul>
      </nav>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

// Props from Laravel backend
const props = defineProps({
  products: Object,
  categories: Array,
  allTags: Array,
  success: String,
  error: String
});

// Reactive state
const viewMode = ref('grid');
const showFilters = ref(false);
const searchQuery = ref('');
const filterCategory = ref('all');
const filterTags = ref([]);
const sortBy = ref('created_at');
const sortOrder = ref('desc');
const isSubmitting = ref(false);
const imagePreview = ref(null);
const fileInput = ref(null);
const fallbackImage = 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image';
const isEditing = ref(false);
const editingProductId = ref(null);

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
  price_tiers: [
    { min_quantity: 1, max_quantity: 10, price: '' },
    { min_quantity: 11, max_quantity: 50, price: '' },
  ],
  image: null,
  manage_stock: true,
  minimum_stock: 10,
  low_stock_threshold: 5,
  status: 'active'
});

// Get products array from paginated object
const productsArray = computed(() => {
  if (!props.products) return [];
  return props.products.data || props.products || [];
});

// Watch for product type changes
watch(() => formData.value.product_type, (newType) => {
  if (newType === 'one-time') {
    formData.value.price_tiers = [];
    formData.value.manage_stock = true;
    if (formData.value.stock <= 0) {
      formData.value.stock = 1;
    }
  } else {
    if (formData.value.price_tiers.length === 0) {
      formData.value.price_tiers = [
        { min_quantity: 1, max_quantity: 10, price: '' },
        { min_quantity: 11, max_quantity: 50, price: '' },
      ];
    }
    formData.value.manage_stock = true;
    if (formData.value.stock <= 0) {
      formData.value.stock = 10;
    }
  }
});

// Computed properties for filtered products
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
      filterTags.value.every(tagId => 
        product.tags?.some(tag => tag.tag_id == tagId)
      );
    
    return matchesSearch && matchesCategory && matchesTags;
  });

  filtered.sort((a, b) => {
    let aValue = a[sortBy.value];
    let bValue = b[sortBy.value];

    if (sortBy.value === 'price') {
      aValue = parseFloat(aValue || 0);
      bValue = parseFloat(bValue || 0);
    } else if (sortBy.value === 'stock') {
      aValue = parseInt(aValue || 0);
      bValue = parseInt(bValue || 0);
    } else if (sortBy.value === 'created_at') {
      aValue = new Date(aValue || 0);
      bValue = new Date(bValue || 0);
    } else {
      aValue = String(aValue || '').toLowerCase();
      bValue = String(bValue || '').toLowerCase();
    }

    if (sortOrder.value === 'asc') {
      return aValue > bValue ? 1 : aValue < bValue ? -1 : 0;
    } else {
      return aValue < bValue ? 1 : aValue > bValue ? -1 : 0;
    }
  });

  return filtered;
});

// Stats
const totalValue = computed(() => {
  return filteredProducts.value.reduce((total, product) => {
    return total + (parseFloat(product.price) || 0) * (parseInt(product.stock) || 0);
  }, 0);
});

const lowStockCount = computed(() => {
  return filteredProducts.value.filter(product => (parseInt(product.stock) || 0) < 10 && (parseInt(product.stock) || 0) > 0).length;
});

// Methods
const toggleFilters = () => {
  showFilters.value = !showFilters.value;
};

const toggleSortOrder = () => {
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
};

const sortByColumn = (column) => {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value = column;
    sortOrder.value = 'asc';
  }
};

const handleProductTypeChange = () => {
  if (formData.value.product_type === 'one-time') {
    formData.value.price_tiers = [];
    formData.value.manage_stock = true;
  } else {
    if (formData.value.price_tiers.length === 0) {
      formData.value.price_tiers = [
        { min_quantity: 1, max_quantity: 10, price: '' },
        { min_quantity: 11, max_quantity: 50, price: '' },
      ];
    }
    formData.value.manage_stock = true;
  }
};

const addPriceTier = () => {
  const lastTier = formData.value.price_tiers[formData.value.price_tiers.length - 1];
  const newMin = lastTier ? lastTier.max_quantity + 1 : 1;
  formData.value.price_tiers.push({
    min_quantity: newMin,
    max_quantity: newMin + 9,
    price: ''
  });
};

const removePriceTier = (index) => {
  if (formData.value.price_tiers.length > 1) {
    formData.value.price_tiers.splice(index, 1);
  }
};

const triggerFileInput = () => {
  fileInput.value?.click();
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

const removeImage = () => {
  formData.value.image = null;
  imagePreview.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
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
    price_tiers: [
      { min_quantity: 1, max_quantity: 10, price: '' },
      { min_quantity: 11, max_quantity: 50, price: '' },
    ],
    image: null,
    manage_stock: true,
    minimum_stock: 10,
    low_stock_threshold: 5,
    status: 'active'
  };
  imagePreview.value = null;
  isEditing.value = false;
  editingProductId.value = null;
};

const cancelEdit = () => {
  resetForm();
};

const handleFormSubmit = async () => {
  isSubmitting.value = true;
  
  try {
    const form = useForm({
      name: formData.value.name,
      reference: formData.value.reference,
      description: formData.value.description,
      product_type: formData.value.product_type,
      category_id: formData.value.category_id,
      selectedTags: formData.value.selectedTags,
      price: formData.value.product_type === 'one-time' ? formData.value.price : (formData.value.price_tiers[0]?.price || 0),
      price_tiers: formData.value.price_tiers,
      stock: formData.value.stock,
      manage_stock: formData.value.manage_stock,
      minimum_stock: formData.value.minimum_stock,
      low_stock_threshold: formData.value.low_stock_threshold,
      status: formData.value.status,
      image: formData.value.image,
      _method: isEditing.value ? 'PUT' : 'POST'
    });

    if (isEditing.value && editingProductId.value) {
      // Update existing product
      await form.post(route('products.update', editingProductId.value), {
        onSuccess: () => {
          resetForm();
          isSubmitting.value = false;
        },
        onError: (errors) => {
          console.error('Update errors:', errors);
          isSubmitting.value = false;
        },
        preserveScroll: true
      });
    } else {
      // Create new product
      await form.post(route('products.store'), {
        onSuccess: () => {
          resetForm();
          isSubmitting.value = false;
        },
        onError: (errors) => {
          console.error('Create errors:', errors);
          isSubmitting.value = false;
        },
        preserveScroll: true
      });
    }

  } catch (error) {
    console.error('Error submitting form:', error);
    isSubmitting.value = false;
  }
};

const editProduct = (product) => {
  isEditing.value = true;
  editingProductId.value = product.product_id;
  
  // Convert price tiers if they exist
  let priceTiers = [];
  if (product.product_type === 'onstock' && product.price_tiers) {
    if (typeof product.price_tiers === 'string') {
      try {
        priceTiers = JSON.parse(product.price_tiers);
      } catch (e) {
        console.error('Error parsing price tiers:', e);
        priceTiers = [
          { min_quantity: 1, max_quantity: 10, price: '' },
          { min_quantity: 11, max_quantity: 50, price: '' },
        ];
      }
    } else {
      priceTiers = product.price_tiers;
    }
  }
  
  // Set form data
  formData.value = {
    name: product.name || '',
    reference: product.reference || '',
    description: product.description || '',
    product_type: product.product_type || 'one-time',
    price: product.price || '',
    stock: product.stock || 1,
    category_id: product.category_id || '',
    selectedTags: product.tags?.map(tag => tag.tag_id) || [],
    price_tiers: priceTiers,
    image: product.image || null,
    manage_stock: product.manage_stock !== undefined ? product.manage_stock : true,
    minimum_stock: product.minimum_stock || 10,
    low_stock_threshold: product.low_stock_threshold || 5,
    status: product.status || 'active'
  };
  
  // Scroll to form
  setTimeout(() => {
    const formElement = document.querySelector('.card-header');
    if (formElement) {
      formElement.scrollIntoView({ behavior: 'smooth' });
    }
  }, 100);
};

const deleteProduct = async (product) => {
  if (confirm(`Are you sure you want to delete "${product.name}"?`)) {
    const form = useForm({});
    await form.delete(route('products.destroy', product.product_id));
  }
};

const viewProductDetails = (product) => {
  window.location.href = route('products.show', product.product_id);
};

const exportProducts = () => {
  alert('Export functionality would be implemented here');
};

// Helper functions
const getProductImage = (imagePath) => {
  if (!imagePath) return fallbackImage;
  if (imagePath instanceof File) {
    return URL.createObjectURL(imagePath);
  }
  return imagePath.startsWith('http') ? imagePath : `/storage/${imagePath}`;
};

const handleImageError = (event) => {
  event.target.src = fallbackImage;
};

const formatPrice = (price) => {
  const num = parseFloat(price) || 0;
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2
  }).format(num);
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

const getProductStatus = (stock) => {
  const stockNum = parseInt(stock) || 0;
  if (stockNum === 0) return 'Out of Stock';
  if (stockNum < 10) return 'Low Stock';
  return 'In Stock';
};

const getProductStatusClass = (stock) => {
  const stockNum = parseInt(stock) || 0;
  if (stockNum === 0) return 'bg-danger';
  if (stockNum < 10) return 'bg-warning';
  return 'bg-success';
};

const getStockColor = (stock) => {
  const stockNum = parseInt(stock) || 0;
  if (stockNum === 0) return 'text-danger';
  if (stockNum < 10) return 'text-warning';
  return 'text-success';
};

const getStockPercentage = (stock) => {
  const maxStock = 200;
  return Math.min((parseInt(stock) / maxStock) * 100, 100).toFixed(0);
};

const getStockProgressClass = (stock) => {
  const stockNum = parseInt(stock) || 0;
  if (stockNum < 10) return 'bg-danger';
  if (stockNum < 30) return 'bg-warning';
  return 'bg-success';
};

const truncateDescription = (text, length = 100) => {
  if (!text) return 'No description';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const clearFlashMessage = () => {
  document.querySelectorAll('.alert').forEach(alert => {
    alert.style.display = 'none';
  });
};

// Initialize
onMounted(() => {
  if (!document.querySelector('link[href*="bootstrap-icons"]')) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css';
    document.head.appendChild(link);
  }
});
</script>

<style scoped>
.products-page {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  min-height: 100vh;
}

.required:after {
  content: " *";
  color: #dc3545;
}

.cursor-pointer {
  cursor: pointer;
}

.product-card {
  transition: transform 0.2s, box-shadow 0.2s;
  overflow: hidden;
  border-radius: 10px;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

.product-image {
  height: 200px;
  object-fit: cover;
  transition: transform 0.3s;
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.product-thumbnail {
  width: 60px;
  height: 60px;
  object-fit: cover;
}

.empty-state {
  padding: 3rem;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.badge {
  font-weight: 500;
  letter-spacing: 0.3px;
}

.table tbody tr:hover {
  background-color: rgba(0,123,255,0.05);
}

.progress {
  border-radius: 3px;
}

.progress-bar {
  border-radius: 3px;
}

.form-control:focus, .form-select:focus {
  border-color: #4a90e2;
  box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
}

.btn-primary {
  background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
  border: none;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #3d7bc8 0%, #2c69a8 100%);
}

.form-check-input:checked {
  background-color: #4a90e2;
  border-color: #4a90e2;
}

.card-header.bg-primary {
  background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
}

.card-header.bg-warning {
  background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
}

.bg-primary.bg-opacity-10 {
  background-color: rgba(74, 144, 226, 0.1) !important;
}

.text-primary {
  color: #4a90e2 !important;
}

.border-primary {
  border-color: #4a90e2 !important;
}
</style>