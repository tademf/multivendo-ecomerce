<template>
  <AppLayout>
    <div class="products-management-page">
      <!-- Hero Header -->
      <div class="bg-gradient-primary py-5 mb-5">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-8">
              <nav aria-label="breadcrumb">
                <!-- <ol class="breadcrumb bg-transparent p-0 mb-3">
                  <li class="breadcrumb-item"><Link href="/" class="text-white-50">Home</Link></li>
                  <li class="breadcrumb-item"><Link href="/dashboard" class="text-white-50">Dashboard</Link></li>
                  <li class="breadcrumb-item active text-white" aria-current="page">Products</li>
                </ol> -->
              </nav>
              <h1 class="display-5 fw-bold text-white mb-3">Product Management</h1>
              <p class="lead text-white-80 mb-0">
                Effortlessly manage your product catalog with advanced inventory control and pricing strategies
              </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
              <div class="d-flex flex-column flex-lg-row gap-3 justify-content-lg-end">
                <button @click="toggleFilters" class="btn btn-light btn-lg shadow-sm">
                  <i class="bi bi-funnel-fill me-2"></i>
                  <span class="d-none d-md-inline">Filters</span>
                </button>
                <button @click="exportProducts" class="btn btn-success btn-lg shadow-sm">
                  <i class="bi bi-cloud-download-fill me-2"></i>
                  <span class="d-none d-md-inline">Export</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <!-- Quick Stats Dashboard -->
        <div class="row g-4 mb-5">
          <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm bg-gradient-info">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="text-white-80 mb-1">Total Products</h6>
                    <h2 class="display-6 fw-bold text-white mb-0">{{ filteredProducts.length }}</h2>
                    <span class="text-white-80 small">
                      <i class="bi bi-arrow-up-short"></i> Active items
                    </span>
                  </div>
                  <div class="icon-wrapper bg-white-20 rounded-circle p-3">
                    <i class="bi bi-box-seam text-white display-6"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm bg-gradient-success">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="text-white-80 mb-1">Inventory Value</h6>
                    <h2 class="display-6 fw-bold text-white mb-0">{{ formatPrice(totalValue) }}</h2>
                    <span class="text-white-80 small">Current stock value</span>
                  </div>
                  <div class="icon-wrapper bg-white-20 rounded-circle p-3">
                    <i class="bi bi-cash-stack text-white display-6"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm bg-gradient-warning">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="text-white-80 mb-1">Low Stock</h6>
                    <h2 class="display-6 fw-bold text-white mb-0">{{ lowStockCount }}</h2>
                    <span class="text-white-80 small">Needs restocking</span>
                  </div>
                  <div class="icon-wrapper bg-white-20 rounded-circle p-3">
                    <i class="bi bi-exclamation-triangle-fill text-white display-6"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm bg-gradient-primary">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="text-white-80 mb-1">Categories</h6>
                    <h2 class="display-6 fw-bold text-white mb-0">{{ categories.length }}</h2>
                    <span class="text-white-80 small">Product categories</span>
                  </div>
                  <div class="icon-wrapper bg-white-20 rounded-circle p-3">
                    <i class="bi bi-tags-fill text-white display-6"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Flash Messages -->
        <div v-if="$page.props.success" class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
          <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-3 fs-4"></i>
            <div class="flex-grow-1">
              <h5 class="alert-heading mb-1">Success!</h5>
              <p class="mb-0">{{ $page.props.success }}</p>
            </div>
          </div>
          <button type="button" class="btn-close" @click="$page.props.success = null"></button>
        </div>
        
        <div v-if="$page.props.error" class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
          <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div class="flex-grow-1">
              <h5 class="alert-heading mb-1">Attention Required</h5>
              <p class="mb-0">{{ $page.props.error }}</p>
            </div>
          </div>
          <button type="button" class="btn-close" @click="$page.props.error = null"></button>
        </div>

        <!-- Main Content Row -->
        <div class="row g-4">
          <!-- Left Column - Filters & Form -->
          <div class="col-lg-4">
            <!-- Product Form Card -->
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
              <div class="card-header" :class="isEditing ? 'bg-warning' : 'bg-primary'">
                <h3 class="mb-0 text-white">
                  <i class="bi me-2" :class="isEditing ? 'bi-pencil-square' : 'bi-plus-square'"></i>
                  {{ isEditing ? 'Edit Product' : 'Add New Product' }}
                </h3>
              </div>
              <div class="card-body">
                <form @submit.prevent="handleFormSubmit" class="needs-validation" novalidate>
                  <!-- Product Type Selection -->
                  <div class="mb-4">
                    <label class="form-label fw-bold text-primary">Product Type</label>
                    <div class="btn-group w-100" role="group">
                      <button type="button" 
                              class="btn" 
                              :class="formData.product_type === 'one-time' ? 'btn-primary' : 'btn-outline-primary'"
                              @click="formData.product_type = 'one-time'">
                        <i class="bi bi-box me-2"></i>One-Time
                      </button>
                      <button type="button" 
                              class="btn" 
                              :class="formData.product_type === 'onstock' ? 'btn-info' : 'btn-outline-info'"
                              @click="formData.product_type = 'onstock'">
                        <i class="bi bi-boxes me-2"></i>On-Stock
                      </button>
                    </div>
                    <small class="form-text text-muted">
                      <span v-if="formData.product_type === 'one-time'">Single price for all customers</span>
                      <span v-else>Quantity-based pricing tiers</span>
                    </small>
                  </div>

                  <!-- Basic Info -->
                  <div class="mb-3">
                    <label class="form-label fw-bold">
                      <i class="bi bi-tag text-primary me-1"></i>
                      Product Name
                      <span class="text-danger">*</span>
                    </label>
                    <input v-model="formData.name" 
                           type="text" 
                           class="form-control form-control-lg" 
                           placeholder="Enter product name" 
                           required />
                  </div>

                  <div class="row g-3">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label fw-bold">
                          <i class="bi bi-upc-scan text-primary me-1"></i>
                          Reference Code
                        </label>
                        <input v-model="formData.reference" 
                               type="text" 
                               class="form-control" 
                               placeholder="e.g., PROD-001" />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label fw-bold">
                          <i class="bi bi-grid text-primary me-1"></i>
                          Category
                        </label>
                        <select v-model="formData.category_id" class="form-select" required>
                          <option value="">Select Category</option>
                          <option v-for="category in categories" 
                                  :key="category.category_id" 
                                  :value="category.category_id">
                            {{ category.name }}
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- Price Section -->
                  <div class="mb-4">
                    <label class="form-label fw-bold">
                      <i class="bi bi-currency-dollar text-primary me-1"></i>
                      Pricing
                    </label>
                    
                    <!-- One-Time Price -->
                    <div v-if="formData.product_type === 'one-time'" class="card bg-light border-0">
                      <div class="card-body p-3">
                        <div class="input-group">
                          <span class="input-group-text bg-white">
                            <i class="bi bi-tag text-primary"></i>
                          </span>
                          <input v-model.number="formData.price" 
                                 type="number" 
                                 step="0.01" 
                                 min="0.01" 
                                 class="form-control" 
                                 placeholder="Enter price" 
                                 required />
                          <span class="input-group-text bg-white">Birr</span>
                        </div>
                      </div>
                    </div>

                    <!-- On-Stock Price Tiers -->
                    <div v-else class="card bg-light border-0">
                      <div class="card-body p-3">
                        <div v-for="(tier, index) in formData.price_tiers" 
                             :key="index" 
                             class="price-tier mb-3 p-3 bg-white rounded">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 text-primary">Tier {{ index + 1 }}</h6>
                            <button v-if="index > 0" 
                                    type="button" 
                                    @click="removePriceTier(index)"
                                    class="btn btn-sm btn-outline-danger">
                              <i class="bi bi-trash"></i>
                            </button>
                          </div>
                          <div class="row g-2">
                            <div class="col-5">
                              <input type="number" 
                                     v-model.number="tier.min_quantity" 
                                     class="form-control form-control-sm" 
                                     placeholder="Min" 
                                     min="1" 
                                     required />
                            </div>
                            <div class="col-5">
                              <input type="number" 
                                     v-model.number="tier.max_quantity" 
                                     class="form-control form-control-sm" 
                                     :placeholder="index === formData.price_tiers.length - 1 ? '∞' : 'Max'" 
                                     :min="tier.min_quantity + 1" 
                                     :required="index < formData.price_tiers.length - 1" />
                            </div>
                            <div class="col-12 mt-2">
                              <div class="input-group input-group-sm">
                                <span class="input-group-text">Birr</span>
                                <input type="number" 
                                       v-model.number="tier.price" 
                                       class="form-control" 
                                       placeholder="Price" 
                                       step="0.01" 
                                       min="0.01" 
                                       required />
                              </div>
                            </div>
                          </div>
                        </div>
                        <button type="button" 
                                @click="addPriceTier" 
                                class="btn btn-sm btn-outline-primary w-100">
                          <i class="bi bi-plus-circle me-1"></i>Add Price Tier
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Stock Management -->
                  <div class="mb-4">
                    <label class="form-label fw-bold">
                      <i class="bi bi-box-seam text-primary me-1"></i>
                      Inventory
                    </label>
                    <div class="card bg-light border-0">
                      <div class="card-body p-3">
                        <div class="input-group mb-3">
                          <span class="input-group-text bg-white">
                            <i class="bi bi-box text-primary"></i>
                          </span>
                          <input v-model.number="formData.stock" 
                                 type="number" 
                                 class="form-control" 
                                 placeholder="Stock quantity" 
                                 required 
                                 min="0" />
                          <span class="input-group-text bg-white">units</span>
                        </div>
                        <div class="form-text">
                          Current available stock
                        </div>
                        
                        <!-- Auto-delete when out of stock -->
                        <div class="form-check mt-3">
                          <input v-model="formData.auto_delete_out_of_stock" 
                                 type="checkbox" 
                                 class="form-check-input" 
                                 id="autoDeleteStock">
                          <label class="form-check-label" for="autoDeleteStock">
                            <i class="bi bi-trash text-danger me-1"></i>
                            Auto-delete when out of stock
                          </label>
                          <div class="form-text text-muted">
                            Product will be automatically removed when stock reaches 0
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Image Upload -->
                  <div class="mb-4">
                    <label class="form-label fw-bold">
                      <i class="bi bi-image text-primary me-1"></i>
                      Product Image
                    </label>
                    <div class="image-upload-area border-2 border-dashed rounded-3 p-4 text-center"
                         @click="triggerFileInput"
                         @dragover.prevent="handleDragOver"
                         @drop.prevent="handleDrop"
                         :class="{ 'border-primary bg-primary-10': isDragging }">
                      <input type="file" 
                             ref="fileInput" 
                             @change="handleImageChange" 
                             class="d-none" 
                             accept="image/*" />
                      
                      <div v-if="!formData.image && !imagePreview" class="upload-placeholder">
                        <i class="bi bi-cloud-arrow-up text-muted display-4 mb-3"></i>
                        <h6 class="text-muted mb-2">Drag & drop image here</h6>
                        <p class="text-muted small mb-0">or click to browse files</p>
                        <p class="text-muted small">JPG, PNG, GIF • Max 2MB</p>
                      </div>
                      
                      <div v-else class="image-preview">
                        <img :src="imagePreview || getProductImage(formData.image)" 
                             alt="Preview" 
                             class="img-fluid rounded shadow-sm" />
                        <div class="preview-overlay">
                          <button type="button" 
                                  @click.stop="removeImage" 
                                  class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                          </button>
                          <button type="button" 
                                  @click.stop="triggerFileInput" 
                                  class="btn btn-sm btn-light">
                            <i class="bi bi-arrow-repeat"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tags Selection -->
                  <div v-if="allTags.length > 0" class="mb-4">
                    <label class="form-label fw-bold">
                      <i class="bi bi-tags text-primary me-1"></i>
                      Product Tags
                    </label>
                    <div class="tags-container border rounded-3 p-3" style="max-height: 150px; overflow-y: auto;">
                      <div class="d-flex flex-wrap gap-2">
                        <div v-for="tag in allTags" 
                             :key="tag.tag_id" 
                             class="tag-item">
                          <input type="checkbox" 
                                 :value="tag.tag_id" 
                                 :id="`tag-${tag.tag_id}`" 
                                 v-model="formData.selectedTags"
                                 class="btn-check">
                          <label :for="`tag-${tag.tag_id}`" 
                                 class="btn btn-sm btn-outline-secondary mb-1">
                            {{ tag.name }}
                          </label>
                        </div>
                      </div>
                    </div>
                    <div v-if="formData.selectedTags.length > 0" class="mt-2 text-success small">
                      <i class="bi bi-check2-circle me-1"></i>
                      {{ formData.selectedTags.length }} tag(s) selected
                    </div>
                  </div>

                  <!-- Description -->
                  <div class="mb-4">
                    <label class="form-label fw-bold">
                      <i class="bi bi-text-paragraph text-primary me-1"></i>
                      Description
                    </label>
                    <textarea v-model="formData.description" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Describe your product..."></textarea>
                  </div>

                  <!-- Form Actions -->
                  <div class="d-grid gap-2">
                    <button type="submit" 
                            class="btn btn-lg" 
                            :class="isEditing ? 'btn-warning' : 'btn-primary'"
                            :disabled="isSubmitting">
                      <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
                      <i class="bi me-2" :class="isEditing ? 'bi-check-circle' : 'bi-plus-circle'"></i>
                      {{ isEditing ? 'Update Product' : 'Add Product' }}
                    </button>
                    
                    <div class="d-flex gap-2">
                      <button type="button" 
                              @click="resetForm" 
                              class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                      </button>
                      <button v-if="isEditing" 
                              type="button" 
                              @click="cancelEdit" 
                              class="btn btn-outline-warning flex-grow-1">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Right Column - Products List -->
          <div class="col-lg-8">
            <!-- Products Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h2 class="h3 fw-bold mb-1">Your Products</h2>
                <p class="text-muted mb-0">{{ filteredProducts.length }} products found</p>
              </div>
              <div class="d-flex gap-2">
                <!-- View Toggle -->
                <div class="btn-group shadow-sm">
                  <button @click="viewMode = 'grid'" 
                          class="btn" 
                          :class="viewMode === 'grid' ? 'btn-primary' : 'btn-outline-primary'">
                    <i class="bi bi-grid-3x3"></i>
                  </button>
                  <button @click="viewMode = 'list'" 
                          class="btn" 
                          :class="viewMode === 'list' ? 'btn-primary' : 'btn-outline-primary'">
                    <i class="bi bi-list-task"></i>
                  </button>
                </div>
                
                <!-- Filters Toggle -->
                <button @click="toggleFilters" 
                        class="btn btn-light shadow-sm">
                  <i class="bi bi-funnel" :class="{ 'text-primary': showFilters }"></i>
                  <span class="badge bg-primary ms-1" v-if="filterTags.length > 0 || filterCategory !== 'all'">
                    {{ (filterTags.length > 0 ? 1 : 0) + (filterCategory !== 'all' ? 1 : 0) }}
                  </span>
                </button>
                
                <!-- Sort Dropdown -->
                <div class="dropdown">
                  <button class="btn btn-light shadow-sm dropdown-toggle" 
                          type="button" 
                          data-bs-toggle="dropdown">
                    <i class="bi bi-sort-down"></i> Sort
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" @click.prevent="sortByColumn('name')">Name</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="sortByColumn('price')">Price</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="sortByColumn('stock')">Stock</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="sortByColumn('created_at')">Date Added</a></li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Advanced Filters -->
            <div v-if="showFilters" class="card mb-4 border-0 shadow-sm">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-8">
                    <label class="form-label fw-bold">Search Products</label>
                    <div class="input-group input-group-lg">
                      <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                      </span>
                      <input type="text" 
                             v-model="searchQuery" 
                             placeholder="Search by name, reference, or description..." 
                             class="form-control border-start-0" />
                      <button class="btn btn-primary" type="button">
                        <i class="bi bi-search"></i>
                      </button>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-bold">Category</label>
                    <select v-model="filterCategory" class="form-select">
                      <option value="all">All Categories</option>
                      <option v-for="category in categories" 
                              :key="category.category_id" 
                              :value="category.category_id">
                        {{ category.name }}
                      </option>
                    </select>
                  </div>
                </div>
                
                <!-- Tags Filter -->
                <div v-if="allTags.length > 0" class="mt-4">
                  <label class="form-label fw-bold mb-2">Filter by Tags</label>
                  <div class="d-flex flex-wrap gap-2">
                    <div v-for="tag in allTags" 
                         :key="tag.tag_id" 
                         class="tag-filter-item">
                      <input type="checkbox" 
                             :value="tag.tag_id" 
                             :id="`filter-tag-${tag.tag_id}`" 
                             v-model="filterTags"
                             class="btn-check">
                      <label :for="`filter-tag-${tag.tag_id}`" 
                             class="btn btn-sm" 
                             :class="filterTags.includes(tag.tag_id) ? 'btn-primary' : 'btn-outline-primary'">
                        {{ tag.name }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredProducts.length === 0" class="text-center py-5">
              <div class="empty-state-card border-0 shadow-sm rounded-3 p-5">
                <i class="bi bi-box-seam text-muted display-1 mb-4"></i>
                <h3 class="fw-bold mb-3">No products found</h3>
                <p class="text-muted mb-4">
                  {{ searchQuery || filterTags.length > 0 || filterCategory !== 'all' 
                    ? 'Try adjusting your search filters' 
                    : 'Get started by adding your first product' }}
                </p>
                <button v-if="!searchQuery && filterTags.length === 0 && filterCategory === 'all'" 
                        @click="scrollToForm" 
                        class="btn btn-primary btn-lg">
                  <i class="bi bi-plus-circle me-2"></i>Add First Product
                </button>
                <button v-else 
                        @click="clearFilters" 
                        class="btn btn-outline-primary">
                  Clear Filters
                </button>
              </div>
            </div>

            <!-- Grid View -->
            <div v-else-if="viewMode === 'grid'" class="row row-cols-1 row-cols-md-2 g-4">
              <div v-for="product in filteredProducts" 
                   :key="product.product_id" 
                   class="col">
                <div class="card product-card h-100 border-0 shadow-sm hover-lift">
                  <!-- Product Image -->
                  <div class="product-image-container position-relative">
                    <img :src="getProductImage(product.image)" 
                         :alt="product.name" 
                         class="card-img-top product-image"
                         @error="handleImageError">
                    <div class="product-badges position-absolute top-0 start-0 p-3">
                      <span class="badge rounded-pill" 
                            :class="getProductStatusClass(product.stock)">
                        {{ getProductStatus(product.stock) }}
                      </span>
                    </div>
                    <div class="product-overlay position-absolute top-0 end-0 p-3">
                      <span class="badge bg-dark rounded-pill">
                        {{ product.product_type === 'onstock' ? 'Tiered' : 'Fixed' }}
                      </span>
                    </div>
                  </div>
                  
                  <!-- Product Body -->
                  <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <div>
                        <h5 class="card-title fw-bold mb-1">{{ product.name }}</h5>
                        <span class="badge bg-light text-dark border me-2">
                          {{ product.reference }}
                        </span>
                        <span class="badge" :class="getCategoryClass(product.category_id)">
                          {{ getCategoryName(product.category_id) }}
                        </span>
                      </div>
                      <div class="text-end">
                        <div class="h4 fw-bold text-success mb-0">{{ formatPrice(product.price) }}</div>
                        <small class="text-muted">per unit</small>
                      </div>
                    </div>
                    
                    <p class="card-text text-muted mb-4">{{ truncateDescription(product.description) }}</p>
                    
                    <!-- Tags -->
                    <div class="mb-4">
                      <div class="d-flex flex-wrap gap-1">
                        <span v-for="tag in product.tags" 
                              :key="tag.tag_id" 
                              class="badge bg-light text-dark border small">
                          {{ tag.name }}
                        </span>
                      </div>
                    </div>
                    
                    <!-- Stock Progress -->
                    <div class="mt-auto">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">
                          <i class="bi bi-box me-1"></i>Stock Level
                        </span>
                        <span class="fw-bold" :class="getStockColor(product.stock)">
                          {{ product.stock }} units
                        </span>
                      </div>
                      <div class="progress" style="height: 8px;">
                        <div class="progress-bar" 
                             :class="getStockProgressClass(product.stock)"
                             :style="{ width: getStockPercentage(product.stock) + '%' }"
                             role="progressbar"></div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Card Footer -->
                  <div class="card-footer bg-white border-top-0 pt-0">
                    <div class="d-flex justify-content-between">
                      <button @click="editProduct(product)" 
                              class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil me-1"></i>Edit
                      </button>
                      <button @click="viewProductDetails(product)" 
                              class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye me-1"></i>View
                      </button>
                      <button @click="deleteProduct(product)" 
                              class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Delete
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- List View -->
            <div v-else class="card border-0 shadow-sm">
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th class="border-0">
                          <button @click="sortByColumn('name')" class="btn btn-link text-decoration-none p-0 fw-bold">
                            Product <i class="bi bi-arrow-down-up ms-1"></i>
                          </button>
                        </th>
                        <th class="border-0">
                          <button @click="sortByColumn('price')" class="btn btn-link text-decoration-none p-0 fw-bold">
                            Price <i class="bi bi-arrow-down-up ms-1"></i>
                          </button>
                        </th>
                        <th class="border-0">Category</th>
                        <th class="border-0">
                          <button @click="sortByColumn('stock')" class="btn btn-link text-decoration-none p-0 fw-bold">
                            Stock <i class="bi bi-arrow-down-up ms-1"></i>
                          </button>
                        </th>
                        <th class="border-0">Type</th>
                        <th class="border-0 text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="product in filteredProducts" 
                          :key="product.product_id"
                          class="product-row">
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="product-thumbnail me-3">
                              <img :src="getProductImage(product.image)" 
                                   :alt="product.name"
                                   class="rounded-2 shadow-sm"
                                   style="width: 60px; height: 60px; object-fit: cover;">
                            </div>
                            <div>
                              <h6 class="fw-bold mb-1">{{ product.name }}</h6>
                              <p class="text-muted small mb-2">{{ truncateDescription(product.description, 50) }}</p>
                              <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-light text-dark border small">
                                  {{ product.reference }}
                                </span>
                                <span v-for="tag in product.tags" 
                                      :key="tag.tag_id" 
                                      class="badge bg-light text-dark border small">
                                  {{ tag.name }}
                                </span>
                              </div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="fw-bold text-success">{{ formatPrice(product.price) }}</div>
                          <small class="text-muted">per unit</small>
                        </td>
                        <td>
                          <span class="badge" :class="getCategoryClass(product.category_id)">
                            {{ getCategoryName(product.category_id) }}
                          </span>
                        </td>
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="flex-grow-1 me-3">
                              <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Available</span>
                                <span :class="getStockColor(product.stock)" class="fw-bold">
                                  {{ product.stock }}
                                </span>
                              </div>
                              <div class="progress" style="height: 6px;">
                                <div class="progress-bar" 
                                     :class="getStockProgressClass(product.stock)"
                                     :style="{ width: getStockPercentage(product.stock) + '%' }"></div>
                              </div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <span class="badge" :class="product.product_type === 'onstock' ? 'bg-info' : 'bg-primary'">
                            {{ product.product_type === 'onstock' ? 'Tiered' : 'Fixed' }}
                          </span>
                        </td>
                        <td class="text-end">
                          <div class="btn-group btn-group-sm">
                            <button @click="editProduct(product)" 
                                    class="btn btn-outline-warning">
                              <i class="bi bi-pencil"></i>
                            </button>
                            <button @click="deleteProduct(product)" 
                                    class="btn btn-outline-danger">
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
            <div v-if="props.products?.links" class="mt-4">
              <nav aria-label="Product pagination">
                <ul class="pagination justify-content-center">
                  <li v-for="(link, index) in props.products.links" 
                      :key="index" 
                      :class="['page-item', link.active ? 'active' : '', link.url ? '' : 'disabled']">
                    <a class="page-link" :href="link.url || '#'" v-html="link.label"></a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

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
const isDragging = ref(false);
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
  status: 'active',
  auto_delete_out_of_stock: false  // New field for auto-delete
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

const clearFilters = () => {
  searchQuery.value = '';
  filterCategory.value = 'all';
  filterTags.value = [];
};

const sortByColumn = (column) => {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value = column;
    sortOrder.value = 'asc';
  }
};

const handleDragOver = () => {
  isDragging.value = true;
};

const handleDrop = (event) => {
  isDragging.value = false;
  const files = event.dataTransfer.files;
  if (files.length > 0) {
    handleImageChange({ target: { files } });
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
    status: 'active',
    auto_delete_out_of_stock: false
  };
  imagePreview.value = null;
  isEditing.value = false;
  editingProductId.value = null;
};

const cancelEdit = () => {
  resetForm();
};

const scrollToForm = () => {
  const formElement = document.querySelector('.card-header');
  if (formElement) {
    formElement.scrollIntoView({ behavior: 'smooth' });
  }
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
      auto_delete_out_of_stock: formData.value.auto_delete_out_of_stock,
      image: formData.value.image,
      _method: isEditing.value ? 'PUT' : 'POST'
    });

    if (isEditing.value && editingProductId.value) {
      // Update existing product in products table
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
      // Create new product in products table
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
    status: product.status || 'active',
    auto_delete_out_of_stock: product.auto_delete_out_of_stock || false
  };
  
  // Scroll to form
  setTimeout(() => {
    scrollToForm();
  }, 100);
};

const deleteProduct = async (product) => {
  if (confirm(`Are you sure you want to delete "${product.name}"? This action cannot be undone.`)) {
    const form = useForm({});
    await form.delete(route('products.destroy', product.product_id));
  }
};

const viewProductDetails = (product) => {
  window.location.href = route('products.show', product.product_id);
};

const exportProducts = () => {
  // Export functionality
  const dataStr = JSON.stringify(filteredProducts.value, null, 2);
  const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
  const exportFileDefaultName = `products_export_${new Date().toISOString().split('T')[0]}.json`;
  
  const linkElement = document.createElement('a');
  linkElement.setAttribute('href', dataUri);
  linkElement.setAttribute('download', exportFileDefaultName);
  linkElement.click();
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

// Format price as "200 Birr" (with Birr text after number)
const formatPrice = (price) => {
  const num = parseFloat(price) || 0;
  // Format number with 2 decimal places and add "Birr" after
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(num) + ' Birr';
};

// Format price without "Birr" text (just the number)
const formatPriceNumber = (price) => {
  const num = parseFloat(price) || 0;
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(num);
};

// Format total value for stats
const formatTotalValue = (value) => {
  const num = parseFloat(value) || 0;
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(num) + ' Birr';
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
  if (stockNum === 0) return 'bg-danger';
  if (stockNum < 10) return 'bg-warning';
  return 'bg-success';
};

const truncateDescription = (text, length = 100) => {
  if (!text) return 'No description';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

// Function to handle customer order placement
const placeCustomerOrder = async (orderData) => {
  try {
    // Create order in orders table
    const response = await fetch('/orders', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json'
      },
      body: JSON.stringify(orderData)
    });
    
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to place order');
    }
    
    const data = await response.json();
    
    // Decrease product stock after successful order
    if (data.success && data.product_id) {
      const stockResponse = await fetch(`/products/${data.product_id}/decrease-stock`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({
          quantity: orderData.quantity || 1,
          order_id: data.order_id
        })
      });
      
      if (!stockResponse.ok) {
        console.warn('Order placed but stock update failed');
      }
    }
    
    // Redirect to orders page
    window.location.href = '/orders';
    
    return { success: true, orderId: data.order_id };
    
  } catch (error) {
    console.error('Error placing order:', error);
    return { success: false, error: error.message };
  }
};

// Initialize Bootstrap icons
onMounted(() => {
  // Add Bootstrap Icons if not already loaded
  if (!document.querySelector('link[href*="bootstrap-icons"]')) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css';
    document.head.appendChild(link);
  }
  
  // Listen for order placed events from payment page
  window.addEventListener('customer-order-placed', async (event) => {
    const orderData = event.detail;
    
    // Place the order
    const result = await placeCustomerOrder(orderData);
    
    if (result.success) {
      // Success notification
      alert('Order placed successfully! Redirecting to orders page...');
    } else {
      // Error notification
      alert('Failed to place order: ' + result.error);
    }
  });
});
</script>

<style scoped>
.products-management-page {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  min-height: 100vh;
}

/* Hero Header */
.bg-gradient-primary {
  background: linear-gradient(135deg, #4a90e2 0%, #3b82f6 100%);
}

.text-white-80 {
  color: rgba(255, 255, 255, 0.8);
}

.text-white-50 {
  color: rgba(255, 255, 255, 0.5);
}

.bg-white-20 {
  background: rgba(255, 255, 255, 0.2);
}

/* Stat Cards */
.stat-card {
  border-radius: 15px;
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.bg-gradient-info {
  background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
}

.bg-gradient-success {
  background: linear-gradient(135deg, #198754 0%, #146c43 100%);
}

.bg-gradient-warning {
  background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

/* Product Card */
.product-card {
  border-radius: 15px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.product-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
}

.hover-lift:hover {
  transform: translateY(-5px);
  transition: transform 0.2s ease;
}

.product-image-container {
  height: 200px;
  overflow: hidden;
}

.product-image {
  height: 100%;
  width: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.product-card:hover .product-image {
  transform: scale(1.1);
}

.product-badges .badge {
  font-size: 0.7rem;
  padding: 0.35em 0.65em;
}

/* Image Upload */
.image-upload-area {
  background: #f8f9fa;
  border: 2px dashed #dee2e6;
  transition: all 0.3s ease;
  cursor: pointer;
}

.image-upload-area:hover {
  border-color: #4a90e2;
  background: rgba(74, 144, 226, 0.05);
}

.border-primary {
  border-color: #4a90e2 !important;
}

.bg-primary-10 {
  background: rgba(74, 144, 226, 0.1);
}

.image-preview {
  position: relative;
  max-width: 300px;
  margin: 0 auto;
}

.preview-overlay {
  position: absolute;
  top: 10px;
  right: 10px;
  display: flex;
  gap: 5px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.image-preview:hover .preview-overlay {
  opacity: 1;
}

/* Tags */
.tags-container {
  background: #f8f9fa;
}

.tag-item .btn-check:checked + .btn {
  background-color: #4a90e2;
  border-color: #4a90e2;
  color: white;
}

/* Form Elements */
.form-control:focus, .form-select:focus {
  border-color: #4a90e2;
  box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
}

.form-control-lg {
  border-radius: 10px;
}

/* Table */
.product-row:hover {
  background-color: rgba(74, 144, 226, 0.05);
}

.table thead th {
  border-bottom: 2px solid #dee2e6;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.875rem;
  color: #6c757d;
}

/* Buttons */
.btn-primary {
  background: linear-gradient(135deg, #4a90e2 0%, #3b82f6 100%);
  border: none;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #3b82f6 0%, #4a90e2 100%);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.btn-outline-primary:hover {
  transform: translateY(-2px);
}

/* Progress Bars */
.progress {
  border-radius: 10px;
  overflow: hidden;
}

.progress-bar {
  border-radius: 10px;
}

/* Breadcrumb */
.breadcrumb-item + .breadcrumb-item::before {
  color: rgba(255, 255, 255, 0.5);
}

/* Empty State */
.empty-state-card {
  background: white;
}

/* Responsive */
@media (max-width: 768px) {
  .stat-card .display-6 {
    font-size: 1.75rem;
  }
  
  .product-image-container {
    height: 150px;
  }
  
  .btn-group {
    flex-wrap: wrap;
  }
}

/* Custom Scrollbar */
.tags-container::-webkit-scrollbar,
.table-responsive::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

.tags-container::-webkit-scrollbar-track,
.table-responsive::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.tags-container::-webkit-scrollbar-thumb,
.table-responsive::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.tags-container::-webkit-scrollbar-thumb:hover,
.table-responsive::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>