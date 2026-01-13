<template>
  <AppLayout>
    <!-- Flash Messages -->
    <div v-if="$page.props.flash.success" class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert" :class="{'dark-alert': isDarkMode}">
      <i class="fas fa-check-circle me-2"></i>
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" :class="{'btn-close-white': isDarkMode}" @click="$page.props.flash.success = null"></button>
    </div>
    
    <div v-if="$page.props.flash.error" class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert" :class="{'dark-alert': isDarkMode}">
      <i class="fas fa-exclamation-circle me-2"></i>
      {{ $page.props.flash.error }}
      <button type="button" class="btn-close" :class="{'btn-close-white': isDarkMode}" @click="$page.props.flash.error = null"></button>
    </div>

    <div class="container-fluid py-4" :class="{'dark-theme': isDarkMode}">
      <!-- Stats Cards -->
      <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="avatar-sm rounded-circle bg-white bg-opacity-25 p-3 me-3">
                    <i class="fas fa-tags fa-2x text-white"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h2 class="fw-bold mb-1">{{ discountStats.total || 0 }}</h2>
                  <p class="mb-0 opacity-75">Total Discounts</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="avatar-sm rounded-circle bg-white bg-opacity-25 p-3 me-3">
                    <i class="fas fa-bolt fa-2x text-white"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h2 class="fw-bold mb-1">{{ discountStats.active || 0 }}</h2>
                  <p class="mb-0 opacity-75">Active Now</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="avatar-sm rounded-circle bg-white bg-opacity-25 p-3 me-3">
                    <i class="fas fa-clock fa-2x text-white"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h2 class="fw-bold mb-1">{{ discountStats.upcoming || 0 }}</h2>
                  <p class="mb-0 opacity-75">Upcoming</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
          <div class="card border-0 shadow-sm h-100 bg-gradient-danger text-white">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="avatar-sm rounded-circle bg-white bg-opacity-25 p-3 me-3">
                    <i class="fas fa-hourglass-end fa-2x text-white"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h2 class="fw-bold mb-1">{{ discountStats.expired || 0 }}</h2>
                  <p class="mb-0 opacity-75">Expired</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Actions -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-0 shadow-sm" :class="{'dark-card': isDarkMode}">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                  <!-- Pure CSS Dropdown -->
                  <div class="custom-dropdown">
                    <button class="custom-dropdown-toggle btn btn-outline-secondary" type="button" :class="{'dark-dropdown-toggle': isDarkMode}">
                      <i class="fas fa-filter me-2"></i>
                      {{ filterStatus ? statusLabels[filterStatus] : 'All Status' }}
                      <i class="fas fa-caret-down ms-2"></i>
                    </button>
                    <ul class="custom-dropdown-menu" :class="{'dark-dropdown-menu': isDarkMode}">
                      <li>
                        <button @click="applyFilter('')" class="custom-dropdown-item" :class="{'dark-dropdown-item': isDarkMode}">
                          <i class="fas fa-list me-2"></i> All Discounts
                        </button>
                      </li>
                      <li><hr class="custom-dropdown-divider" :class="{'dark-divider': isDarkMode}"></li>
                      <li>
                        <button @click="applyFilter('active')" class="custom-dropdown-item" :class="{'dark-dropdown-item': isDarkMode}">
                          <span class="badge bg-success me-2">●</span> Active
                        </button>
                      </li>
                      <li>
                        <button @click="applyFilter('upcoming')" class="custom-dropdown-item" :class="{'dark-dropdown-item': isDarkMode}">
                          <span class="badge bg-warning me-2">●</span> Upcoming
                        </button>
                      </li>
                      <li>
                        <button @click="applyFilter('expired')" class="custom-dropdown-item" :class="{'dark-dropdown-item': isDarkMode}">
                          <span class="badge bg-danger me-2">●</span> Expired
                        </button>
                      </li>
                    </ul>
                  </div>
                  
                  <div class="input-group" style="width: 300px;">
                    <span class="input-group-text bg-transparent" :class="{'dark-input-group': isDarkMode}">
                      <i class="fas fa-search"></i>
                    </span>
                    <input type="text" v-model="searchQuery" 
                           class="form-control" 
                           :class="{'dark-input': isDarkMode}"
                           placeholder="Search discounts...">
                  </div>
                  <button @click="openCreateModal" class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-plus-circle me-2"></i>
                    Add Discount
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Discounts Grid -->
      <div class="row">
        <div class="col-12">
          <!-- Empty State -->
          <div v-if="filteredDiscounts.length === 0" class="card border-0 shadow-sm" :class="{'dark-card': isDarkMode}">
            <div class="card-body text-center py-5">
              <div class="empty-state-icon mb-4">
                <i class="fas fa-tag fa-4x" :class="isDarkMode ? 'text-light' : 'text-muted'"></i>
              </div>
              <h4 class="fw-bold mb-3" :class="isDarkMode ? 'text-white' : ''">No Discounts Found</h4>
              <p class="mb-4" :class="isDarkMode ? 'text-light' : 'text-muted'">
                {{ searchQuery ? 'No discounts match your search' : 
                   filterStatus ? `You don't have any ${filterStatus} discounts` : 
                   "You haven't created any discounts yet" }}
              </p>
              <button @click="openCreateModal" class="btn btn-primary btn-lg">
                <i class="fas fa-plus-circle me-2"></i>
                Add Your First Discount
              </button>
            </div>
          </div>

          <!-- Discounts Grid -->
          <div v-else class="row g-4">
            <div v-for="discount in filteredDiscounts" :key="discount.discount_id" class="col-xl-4 col-lg-6">
              <div class="card border-0 shadow-sm h-100 discount-card" 
                   :class="[`border-start border-5 border-${getStatusColor(discount.status)}`, {'dark-card': isDarkMode}]">
                <div class="card-body">
                  <!-- Discount Header -->
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                      <h5 class="fw-bold mb-1" :class="isDarkMode ? 'text-white' : 'text-primary'">
                        <i class="fas fa-tag me-2"></i>
                        {{ discount.discount_name }}
                      </h5>
                      <div class="d-flex align-items-center gap-2">
                        <span :class="`badge bg-${getStatusColor(discount.status)}`">
                          {{ discount.status.toUpperCase() }}
                        </span>
                        <small :class="isDarkMode ? 'text-light' : 'text-muted'">
                          <i class="far fa-calendar me-1"></i>
                          {{ formatDate(discount.start_date) }} - {{ formatDate(discount.end_date) }}
                        </small>
                      </div>
                    </div>
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                      <button @click.stop="openEditModal(discount)" 
                              class="btn btn-sm" 
                              :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-primary'"
                              title="Edit Discount">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button @click.stop="confirmDelete(discount)" 
                              class="btn btn-sm" 
                              :class="isDarkMode ? 'btn-outline-danger' : 'btn-outline-danger'"
                              title="Delete Discount">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Compact Product Card -->
                  <div class="compact-product-card mb-3 p-3 rounded clickable" 
                       @click="openViewModal(discount)"
                       :class="isDarkMode ? 'bg-dark-secondary' : 'bg-light'">
                    <div class="d-flex align-items-center">
                      <!-- Product Image -->
                      <div class="product-image-small me-3">
                        <img :src="getProductImage(discount.product)" 
                             :alt="discount.product?.name || discount.product?.product_name"
                             class="img-fluid rounded" 
                             style="width: 60px; height: 60px; object-fit: cover;">
                      </div>
                      
                      <!-- Product Info -->
                      <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1 text-truncate" style="max-width: 200px;" :class="isDarkMode ? 'text-white' : ''">
                          {{ discount.product?.name || discount.product?.product_name || 'Product not found' }}
                        </h6>
                        
                        <!-- Price Comparison -->
                        <div class="price-comparison mt-2">
                          <!-- Original Price -->
                          <div class="original-price small mb-1" :class="isDarkMode ? 'text-light' : 'text-muted'">
                            <span class="text-decoration-line-through">
                              {{ formatPrice(discount.product?.price) }} Birr
                            </span>
                            <span class="badge bg-danger ms-2">
                              -{{ discount.discount_amount }}%
                            </span>
                          </div>
                          
                          <!-- New Price -->
                          <div class="new-price">
                            <span class="fw-bold h5 mb-0" :class="isDarkMode ? 'text-danger' : 'text-danger'">
                              {{ formatPrice(calculateDiscountedPrice(discount.product?.price, discount.discount_amount)) }} Birr
                            </span>
                          </div>
                          
                          <!-- Savings -->
                          <div class="savings small" :class="isDarkMode ? 'text-success' : 'text-success'">
                            <i class="fas fa-save me-1"></i>
                            Save {{ formatPrice(calculateSavings(discount.product?.price, discount.discount_amount)) }} Birr
                          </div>
                        </div>
                      </div>
                      
                      <!-- View Icon -->
                      <div class="view-icon ms-2">
                        <button class="btn btn-sm border-0"
                                :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-info'"
                                @click.stop="openViewModal(discount)"
                                title="View Details">
                          <i class="fas fa-eye"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Progress Bar -->
                  <div class="progress-info">
                    <div class="d-flex justify-content-between mb-1">
                      <small :class="isDarkMode ? 'text-light' : 'text-muted'">
                        <i class="far fa-calendar me-1"></i>
                        {{ getRemainingDays(discount) }} days remaining
                      </small>
                      <small :class="isDarkMode ? 'text-light' : 'text-muted'">{{ Math.round(discount.progress_percentage || 0) }}%</small>
                    </div>
                    <div class="progress" style="height: 6px;" :class="isDarkMode ? 'bg-dark' : ''">
                      <div :class="`progress-bar bg-${getStatusColor(discount.status)}`" 
                           :style="{ width: (discount.progress_percentage || 0) + '%' }"></div>
                    </div>
                  </div>
                </div>
                
                <div class="card-footer bg-transparent border-top-0 pt-0">
                  <div class="small" :class="isDarkMode ? 'text-light' : 'text-muted'">
                    <i class="far fa-clock me-1"></i>
                    Created {{ formatRelativeTime(discount.created_at) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Discount Modal -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" :style="isDarkMode ? 'background-color: rgba(0,0,0,0.8)' : 'background-color: rgba(0,0,0,0.5)'">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" :class="{'dark-modal': isDarkMode}">
          <!-- Modal Header -->
          <div class="modal-header" :class="[editingDiscount ? 'bg-warning text-dark' : 'bg-primary text-white', {'dark-modal-header': isDarkMode}]">
            <h5 class="modal-title fw-bold">
              <i :class="editingDiscount ? 'fas fa-edit me-2' : 'fas fa-plus-circle me-2'"></i>
              {{ editingDiscount ? 'Edit Discount' : 'Create New Discount' }}
            </h5>
            <button type="button" class="btn-close" :class="[editingDiscount ? '' : 'btn-close-white', {'btn-close-white': isDarkMode}]" 
                    @click="closeModal"></button>
          </div>
          
          <!-- Modal Body -->
          <div class="modal-body" :class="{'dark-modal-body': isDarkMode}">
            <form @submit.prevent="saveDiscount">
              <!-- Discount Name -->
              <div class="mb-4">
                <label class="form-label fw-bold" :class="isDarkMode ? 'text-white' : ''">
                  <i class="fas fa-tag me-2 text-primary"></i>
                  Discount Name *
                </label>
                <input type="text" 
                       v-model="form.discount_name" 
                       class="form-control form-control-lg" 
                       :class="{'dark-input': isDarkMode}"
                       placeholder="e.g., Summer Sale, Black Friday, Flash Sale"
                       required>
                <div class="form-text" :class="isDarkMode ? 'text-light' : ''">Give your discount a descriptive name</div>
              </div>

              <!-- Product Selection -->
              <div class="mb-4">
                <label class="form-label fw-bold" :class="isDarkMode ? 'text-white' : ''">
                  <i class="fas fa-box me-2 text-primary"></i>
                  Select Product *
                </label>
                <div v-if="products.length === 0" class="alert alert-warning" :class="{'dark-alert': isDarkMode}">
                  <i class="fas fa-exclamation-triangle me-2"></i>
                  You don't have any products yet. 
                  <a :href="route('products.create')" class="alert-link" :class="isDarkMode ? 'text-warning' : ''">Create a product first</a>
                </div>
                <select v-else 
                        v-model="form.product_id" 
                        class="form-select form-select-lg" 
                        :class="{'dark-select': isDarkMode}"
                        required>
                  <option value="">-- Select a Product --</option>
                  <option v-for="product in filteredProducts" 
                          :key="product.product_id" 
                          :value="product.product_id"
                          :class="isDarkMode ? 'dark-option' : ''">
                    {{ product.name }} - {{ formatPrice(product.price) }} Birr 
                    <template v-if="product.stock !== undefined">
                      (Stock: {{ product.stock }})
                    </template>
                    <template v-if="product.category && product.category.name">
                      | {{ product.category.name }}
                    </template>
                  </option>
                </select>
                <div class="form-text" :class="isDarkMode ? 'text-light' : ''">Select the product you want to discount</div>
              </div>

              <!-- Show selected product details -->
              <div v-if="selectedProduct" class="card mb-4" :class="[isDarkMode ? 'border-light bg-dark' : 'border-primary']">
                <div class="card-header" :class="[isDarkMode ? 'bg-dark-secondary border-light' : 'bg-primary bg-opacity-10 border-primary']">
                  <h6 class="fw-bold mb-0" :class="isDarkMode ? 'text-white' : 'text-primary'">
                    <i class="fas fa-info-circle me-2"></i>
                    Selected Product Details
                  </h6>
                </div>
                <div class="card-body" :class="isDarkMode ? 'bg-dark' : ''">
                  <div class="row align-items-center">
                    <div class="col-md-4 text-center">
                      <img :src="getProductImage(selectedProduct)" 
                           :alt="selectedProduct.name"
                           class="img-fluid rounded mb-3"
                           style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    <div class="col-md-8">
                      <h6 class="fw-bold mb-2" :class="isDarkMode ? 'text-white' : ''">{{ selectedProduct.name }}</h6>
                      <div class="row">
                        <div class="col-6">
                          <div class="mb-2">
                            <span :class="isDarkMode ? 'text-light' : 'text-muted'">Current Price:</span>
                            <div class="fw-bold" :class="isDarkMode ? 'text-success' : 'text-success'">{{ formatPrice(selectedProduct.price) }} Birr</div>
                          </div>
                          <div class="mb-2">
                            <span :class="isDarkMode ? 'text-light' : 'text-muted'">Stock:</span>
                            <div :class="`fw-bold ${selectedProduct.stock > 10 ? 'text-success' : selectedProduct.stock > 0 ? 'text-warning' : 'text-danger'}`">
                              {{ selectedProduct.stock }} units
                            </div>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="mb-2">
                            <span :class="isDarkMode ? 'text-light' : 'text-muted'">Category:</span>
                            <div class="fw-bold">
                              <span class="badge bg-secondary">
                                {{ selectedProduct.category?.name || 'Uncategorized' }}
                              </span>
                            </div>
                          </div>
                          <div class="mb-2">
                            <span :class="isDarkMode ? 'text-light' : 'text-muted'">Product Type:</span>
                            <div class="fw-bold">
                              <span class="badge" :class="selectedProduct.product_type === 'onstock' ? 'bg-info' : 'bg-primary'">
                                {{ selectedProduct.product_type === 'onstock' ? 'On-Stock' : 'One-Time' }}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Discount Amount -->
              <div class="mb-4">
                <label class="form-label fw-bold" :class="isDarkMode ? 'text-white' : ''">
                  <i class="fas fa-percent me-2 text-primary"></i>
                  Discount Percentage *
                </label>
                <div class="input-group input-group-lg">
                  <input type="number" 
                         v-model.number="form.discount_amount" 
                         class="form-control" 
                         :class="{'dark-input': isDarkMode}"
                         min="1" 
                         max="100" 
                         step="1" 
                         required>
                  <span class="input-group-text" :class="{'dark-input-group': isDarkMode}">%</span>
                </div>
                
                <!-- Quick Percentage Buttons -->
                <div class="mt-2">
                  <label class="form-label" :class="isDarkMode ? 'text-white' : ''">Quick Select:</label>
                  <div class="d-flex flex-wrap gap-2">
                    <button type="button" 
                            v-for="percent in [10, 20, 30, 40, 50, 60, 70]" 
                            :key="percent"
                            @click="form.discount_amount = percent"
                            :class="[`btn ${form.discount_amount === percent ? 'active' : ''}`, 
                                     isDarkMode ? 'btn-outline-light' : 'btn-outline-primary']">
                      {{ percent }}%
                    </button>
                  </div>
                </div>
              </div>

              <!-- Date Range -->
              <div class="row mb-4">
                <div class="col-md-6">
                  <label class="form-label fw-bold" :class="isDarkMode ? 'text-white' : ''">
                    <i class="far fa-calendar-alt me-2 text-primary"></i>
                    Start Date *
                  </label>
                  <input type="date" 
                         v-model="form.start_date" 
                         class="form-control form-control-lg" 
                         :class="{'dark-input': isDarkMode}"
                         :min="minDate"
                         required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold" :class="isDarkMode ? 'text-white' : ''">
                    <i class="far fa-calendar-alt me-2 text-primary"></i>
                    End Date *
                  </label>
                  <input type="date" 
                         v-model="form.end_date" 
                         class="form-control form-control-lg" 
                         :class="{'dark-input': isDarkMode}"
                         :min="form.start_date || minDate"
                         required>
                </div>
                <div class="col-12 mt-2">
                  <div class="form-text" :class="isDarkMode ? 'text-light' : ''">
                    <i class="fas fa-info-circle me-1"></i>
                    Discount will be active between these dates
                  </div>
                </div>
              </div>

              <!-- Preview -->
              <div v-if="form.product_id && form.discount_amount && selectedProduct" class="card mb-4" :class="isDarkMode ? 'border-light bg-dark' : 'border-success'">
                <div class="card-header" :class="[isDarkMode ? 'bg-dark-secondary border-light' : 'bg-success bg-opacity-10 border-success']">
                  <h6 class="fw-bold mb-0" :class="isDarkMode ? 'text-white' : 'text-success'">
                    <i class="fas fa-eye me-2"></i>
                    Price Preview
                  </h6>
                </div>
                <div class="card-body" :class="isDarkMode ? 'bg-dark' : ''">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-2">
                        <span :class="isDarkMode ? 'text-light' : 'text-muted'">Original Price:</span>
                        <div class="fw-bold text-decoration-line-through" :class="isDarkMode ? 'text-light' : ''">
                          {{ formatPrice(selectedProduct.price) }} Birr
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-2">
                        <span :class="isDarkMode ? 'text-light' : 'text-muted'">Discount:</span>
                        <div class="fw-bold" :class="isDarkMode ? 'text-success' : 'text-success'">{{ form.discount_amount }}%</div>
                      </div>
                    </div>
                  </div>
                  <hr :class="isDarkMode ? 'border-light' : ''">
                  <div class="text-center">
                    <div class="small mb-1" :class="isDarkMode ? 'text-light' : 'text-muted'">Discounted Price</div>
                    <div class="h2 fw-bold" :class="isDarkMode ? 'text-danger' : 'text-danger'">
                      {{ formatPrice(calculateDiscountedPrice(selectedProduct.price, form.discount_amount)) }} Birr
                    </div>
                    <div :class="isDarkMode ? 'text-success' : 'text-success'">
                      <i class="fas fa-save me-1"></i>
                      Save {{ formatPrice(calculateSavings(selectedProduct.price, form.discount_amount)) }} Birr
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          
          <!-- Modal Footer -->
          <div class="modal-footer" :class="{'dark-modal-footer': isDarkMode}">
            <button type="button" class="btn btn-lg" :class="isDarkMode ? 'btn-dark' : 'btn-secondary'" @click="closeModal">
              <i class="fas fa-times me-2"></i> Cancel
            </button>
            <button type="button" 
                    class="btn btn-primary btn-lg" 
                    @click="saveDiscount"
                    :disabled="processing || !formValid">
              <template v-if="processing">
                <span class="spinner-border spinner-border-sm me-2"></span>
                Processing...
              </template>
              <template v-else>
                <i :class="editingDiscount ? 'fas fa-save me-2' : 'fas fa-check-circle me-2'"></i>
                {{ editingDiscount ? 'Update Discount' : 'Create Discount' }}
              </template>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- View Discount Details Modal -->
    <div v-if="showViewModal" class="modal fade show d-block" tabindex="-1" :style="isDarkMode ? 'background-color: rgba(0,0,0,0.8)' : 'background-color: rgba(0,0,0,0.5)'">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" :class="{'dark-modal': isDarkMode}">
          <!-- Modal Header -->
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fw-bold">
              <i class="fas fa-eye me-2"></i>
              Discount Details
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="closeViewModal"></button>
          </div>
          
          <!-- Modal Body -->
          <div class="modal-body" :class="{'dark-modal-body': isDarkMode}">
            <div v-if="selectedViewDiscount" class="row">
              <!-- Discount Info -->
              <div class="col-md-8">
                <div class="mb-4">
                  <h3 class="fw-bold mb-2" :class="isDarkMode ? 'text-white' : 'text-primary'">
                    <i class="fas fa-tag me-2"></i>
                    {{ selectedViewDiscount.discount_name }}
                  </h3>
                  <div class="d-flex align-items-center gap-3">
                    <span :class="`badge bg-${getStatusColor(selectedViewDiscount.status)} fs-6`">
                      {{ selectedViewDiscount.status.toUpperCase() }}
                    </span>
                    <div :class="isDarkMode ? 'text-light' : 'text-muted'">
                      <i class="far fa-calendar me-1"></i>
                      {{ formatDate(selectedViewDiscount.start_date) }} - {{ formatDate(selectedViewDiscount.end_date) }}
                    </div>
                  </div>
                </div>

                <!-- Product Info -->
                <div class="card mb-4" :class="isDarkMode ? 'border-light bg-dark' : 'border-primary'">
                  <div class="card-header" :class="[isDarkMode ? 'bg-dark-secondary border-light' : 'bg-primary bg-opacity-10 border-primary']">
                    <h6 class="fw-bold mb-0" :class="isDarkMode ? 'text-white' : 'text-primary'">
                      <i class="fas fa-box me-2"></i>
                      Product Information
                    </h6>
                  </div>
                  <div class="card-body" :class="isDarkMode ? 'bg-dark' : ''">
                    <div class="row">
                      <div class="col-md-4">
                        <img :src="getProductImage(selectedViewDiscount.product)" 
                             :alt="selectedViewDiscount.product?.name || selectedViewDiscount.product?.product_name"
                             class="img-fluid rounded mb-3"
                             style="width: 150px; height: 150px; object-fit: cover;">
                      </div>
                      <div class="col-md-8">
                        <h5 class="fw-bold mb-2" :class="isDarkMode ? 'text-white' : ''">{{ selectedViewDiscount.product?.name || selectedViewDiscount.product?.product_name || 'Product not found' }}</h5>
                        <div class="row">
                          <div class="col-6">
                            <div class="mb-2">
                              <span :class="isDarkMode ? 'text-light' : 'text-muted'">Category:</span>
                              <div class="fw-bold" :class="isDarkMode ? 'text-white' : ''">{{ selectedViewDiscount.product?.category?.name || 'N/A' }}</div>
                            </div>
                            <div class="mb-2">
                              <span :class="isDarkMode ? 'text-light' : 'text-muted'">Stock:</span>
                              <div class="fw-bold" :class="isDarkMode ? 'text-white' : ''">{{ selectedViewDiscount.product?.stock || 0 }} units</div>
                            </div>
                          </div>
                          <div class="col-6">
                            <div class="mb-2">
                              <span :class="isDarkMode ? 'text-light' : 'text-muted'">Product Type:</span>
                              <div class="fw-bold">
                                <span class="badge" :class="selectedViewDiscount.product?.product_type === 'onstock' ? 'bg-info' : 'bg-primary'">
                                  {{ selectedViewDiscount.product?.product_type === 'onstock' ? 'On-Stock' : 'One-Time' }}
                                </span>
                              </div>
                            </div>
                            <div class="mb-2">
                              <span :class="isDarkMode ? 'text-light' : 'text-muted'">Reference:</span>
                              <div class="fw-bold" :class="isDarkMode ? 'text-white' : ''">{{ selectedViewDiscount.product?.reference || 'N/A' }}</div>
                            </div>
                          </div>
                        </div>
                        <div class="mt-3">
                          <span :class="isDarkMode ? 'text-light' : 'text-muted'">Description:</span>
                          <p class="mb-0" :class="isDarkMode ? 'text-white' : ''">{{ selectedViewDiscount.product?.description || 'No description available' }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Timeline -->
                <div class="card mb-4" :class="{'dark-card': isDarkMode}">
                  <div class="card-header" :class="{'dark-card-header': isDarkMode}">
                    <h6 class="fw-bold mb-0" :class="isDarkMode ? 'text-white' : ''">
                      <i class="fas fa-history me-2"></i>
                      Discount Timeline
                    </h6>
                  </div>
                  <div class="card-body" :class="{'dark-card-body': isDarkMode}">
                    <div class="timeline">
                      <div class="timeline-item">
                        <div class="timeline-marker" :class="isDarkMode ? 'bg-white' : 'bg-primary'"></div>
                        <div class="timeline-content">
                          <h6 class="fw-bold" :class="isDarkMode ? 'text-white' : ''">Start Date</h6>
                          <p class="mb-0" :class="isDarkMode ? 'text-light' : 'text-muted'">{{ formatDate(selectedViewDiscount.start_date) }}</p>
                          <small :class="isDarkMode ? 'text-light' : 'text-muted'">{{ getRelativeTime(selectedViewDiscount.start_date) }}</small>
                        </div>
                      </div>
                      <div class="timeline-item">
                        <div class="timeline-marker" :class="`bg-${getStatusColor(selectedViewDiscount.status)}`"></div>
                        <div class="timeline-content">
                          <h6 class="fw-bold" :class="isDarkMode ? 'text-white' : ''">Current Status</h6>
                          <p class="mb-0" :class="isDarkMode ? 'text-white' : ''">{{ selectedViewDiscount.status.toUpperCase() }}</p>
                          <small :class="isDarkMode ? 'text-light' : 'text-muted'">{{ getRemainingDays(selectedViewDiscount) }} days remaining</small>
                        </div>
                      </div>
                      <div class="timeline-item">
                        <div class="timeline-marker" :class="isDarkMode ? 'bg-light' : 'bg-secondary'"></div>
                        <div class="timeline-content">
                          <h6 class="fw-bold" :class="isDarkMode ? 'text-white' : ''">End Date</h6>
                          <p class="mb-0" :class="isDarkMode ? 'text-light' : 'text-muted'">{{ formatDate(selectedViewDiscount.end_date) }}</p>
                          <small :class="isDarkMode ? 'text-light' : 'text-muted'">{{ getRelativeTime(selectedViewDiscount.end_date) }}</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Price Summary -->
              <div class="col-md-4">
                <div class="sticky-top" style="top: 20px;">
                  <div class="card shadow-sm mb-4" :class="isDarkMode ? 'border-light bg-dark' : 'border-success'">
                    <div class="card-header" :class="isDarkMode ? 'bg-dark-secondary border-light text-white' : 'bg-success text-white'">
                      <h6 class="fw-bold mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Price Summary
                      </h6>
                    </div>
                    <div class="card-body" :class="isDarkMode ? 'bg-dark' : ''">
                      <!-- Original Price -->
                      <div class="mb-3">
                        <div class="small mb-1" :class="isDarkMode ? 'text-light' : 'text-muted'">Original Price</div>
                        <div class="fw-bold text-decoration-line-through fs-4" :class="isDarkMode ? 'text-light' : 'text-muted'">
                          {{ formatPrice(selectedViewDiscount.product?.price) }} Birr
                        </div>
                      </div>

                      <!-- Discount -->
                      <div class="mb-3">
                        <div class="small mb-1" :class="isDarkMode ? 'text-light' : 'text-muted'">Discount</div>
                        <div class="d-flex align-items-center">
                          <span class="fw-bold fs-3 me-2" :class="isDarkMode ? 'text-success' : 'text-success'">
                            {{ selectedViewDiscount.discount_amount }}%
                          </span>
                          <span class="badge bg-danger fs-6">OFF</span>
                        </div>
                      </div>

                      <hr :class="isDarkMode ? 'border-light' : ''">

                      <!-- Discounted Price -->
                      <div class="mb-3">
                        <div class="small mb-1" :class="isDarkMode ? 'text-light' : 'text-muted'">Discounted Price</div>
                        <div class="fw-bold fs-2" :class="isDarkMode ? 'text-danger' : 'text-danger'">
                          {{ formatPrice(calculateDiscountedPrice(selectedViewDiscount.product?.price, selectedViewDiscount.discount_amount)) }} Birr
                        </div>
                      </div>

                      <!-- Savings -->
                      <div class="mb-3">
                        <div class="small mb-1" :class="isDarkMode ? 'text-light' : 'text-muted'">You Save</div>
                        <div class="fw-bold fs-4" :class="isDarkMode ? 'text-success' : 'text-success'">
                          <i class="fas fa-piggy-bank me-2"></i>
                          {{ formatPrice(calculateSavings(selectedViewDiscount.product?.price, selectedViewDiscount.discount_amount)) }} Birr
                        </div>
                      </div>

                      <!-- Progress Bar -->
                      <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                          <small :class="isDarkMode ? 'text-light' : 'text-muted'">Progress</small>
                          <small :class="isDarkMode ? 'text-light' : 'text-muted'">{{ Math.round(selectedViewDiscount.progress_percentage || 0) }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;" :class="isDarkMode ? 'bg-dark' : ''">
                          <div :class="`progress-bar bg-${getStatusColor(selectedViewDiscount.status)}`" 
                               :style="{ width: (selectedViewDiscount.progress_percentage || 0) + '%' }"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="card" :class="{'dark-card': isDarkMode}">
                    <div class="card-body" :class="{'dark-card-body': isDarkMode}">
                      <div class="d-grid gap-3">
                        <button @click="openEditModal(selectedViewDiscount)" 
                                class="btn btn-lg" 
                                :class="isDarkMode ? 'btn-warning' : 'btn-warning'">
                          <i class="fas fa-edit me-2"></i>
                          Edit Discount
                        </button>
                        <button @click="confirmDelete(selectedViewDiscount)" 
                                class="btn btn-danger btn-lg">
                          <i class="fas fa-trash me-2"></i>
                          Delete Discount
                        </button>
                        <button @click="closeViewModal" 
                                class="btn btn-lg" 
                                :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-secondary'">
                          <i class="fas fa-times me-2"></i>
                          Close
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
  discounts: {
    type: Array,
    default: () => []
  },
  products: {
    type: Array,
    default: () => []
  },
  user: {
    type: Object,
    required: true
  },
  discountStats: {
    type: Object,
    default: () => ({
      total: 0,
      active: 0,
      upcoming: 0,
      expired: 0
    })
  },
  filterStatus: {
    type: String,
    default: ''
  }
})

// Dark Mode State
const isDarkMode = ref(false)

// Watch for theme changes from navbar
const checkTheme = () => {
  const html = document.documentElement
  isDarkMode.value = html.getAttribute('data-theme') === 'dark'
  
  // Apply theme to body
  if (isDarkMode.value) {
    document.body.classList.add('dark-theme')
    document.body.classList.remove('light-theme')
  } else {
    document.body.classList.add('light-theme')
    document.body.classList.remove('dark-theme')
  }
}

// Listen for theme changes from navbar
onMounted(() => {
  checkTheme()
  
  // Listen for theme changes
  window.addEventListener('theme-changed', () => {
    setTimeout(checkTheme, 100) // Small delay to ensure DOM is updated
  })
  
  // Also check periodically
  const themeCheckInterval = setInterval(checkTheme, 1000)
  
  // Cleanup on unmount
  onUnmounted(() => {
    clearInterval(themeCheckInterval)
    window.removeEventListener('theme-changed', checkTheme)
  })
})

// State
const showModal = ref(false)
const showViewModal = ref(false)
const editingDiscount = ref(null)
const selectedViewDiscount = ref(null)
const processing = ref(false)
const searchQuery = ref('')
const statusLabels = {
  'active': 'Active',
  'upcoming': 'Upcoming', 
  'expired': 'Expired'
}

// Form
const form = useForm({
  discount_name: '',
  product_id: '',
  discount_amount: 10,
  start_date: '',
  end_date: ''
})

// Computed Properties
const minDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const filteredProducts = computed(() => {
  if (!Array.isArray(props.products)) return []
  
  // Filter products that are active and in stock
  return props.products.filter(product => {
    const hasName = product.name || product.product_name
    const hasPrice = product.price !== undefined && product.price !== null
    const isActive = product.status === 'active' || product.status === undefined
    const hasStock = product.stock > 0
    
    return hasName && hasPrice && isActive && hasStock
  })
})

const selectedProduct = computed(() => {
  if (!form.product_id) return null
  return props.products.find(p => p.product_id == form.product_id)
})

const filteredDiscounts = computed(() => {
  let discounts = props.discounts
  
  // Apply status filter
  if (props.filterStatus) {
    discounts = discounts.filter(d => d.status === props.filterStatus)
  }
  
  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    discounts = discounts.filter(d => {
      const discountName = d.discount_name?.toLowerCase() || ''
      const productName = d.product?.name?.toLowerCase() || d.product?.product_name?.toLowerCase() || ''
      return discountName.includes(query) || productName.includes(query)
    })
  }
  
  return discounts
})

const formValid = computed(() => {
  return form.discount_name && 
         form.product_id && 
         form.discount_amount >= 1 && 
         form.discount_amount <= 100 &&
         form.start_date && 
         form.end_date &&
         form.start_date < form.end_date
})

// Methods
const openCreateModal = () => {
  editingDiscount.value = null
  form.reset()
  form.start_date = minDate.value
  form.end_date = ''
  showModal.value = true
}

const openEditModal = (discount) => {
  closeViewModal()
  editingDiscount.value = discount
  form.discount_name = discount.discount_name
  form.product_id = discount.product_id
  form.discount_amount = discount.discount_amount
  form.start_date = discount.start_date.split('T')[0]
  form.end_date = discount.end_date.split('T')[0]
  showModal.value = true
}

const openViewModal = (discount) => {
  selectedViewDiscount.value = discount
  showViewModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingDiscount.value = null
  form.reset()
}

const closeViewModal = () => {
  showViewModal.value = false
  selectedViewDiscount.value = null
}

const saveDiscount = () => {
  if (!formValid.value) {
    alert('Please fill all required fields correctly')
    return
  }
  
  processing.value = true
  
  if (editingDiscount.value) {
    form.put(route('discounts.update', { id: editingDiscount.value.discount_id }), {
      onSuccess: () => {
        closeModal()
        processing.value = false
      },
      onError: () => {
        alert('Failed to update discount')
        processing.value = false
      }
    })
  } else {
    form.post(route('discounts.store'), {
      onSuccess: () => {
        closeModal()
        processing.value = false
      },
      onError: () => {
        alert('Failed to create discount')
        processing.value = false
      }
    })
  }
}

const confirmDelete = (discount) => {
  if (confirm(`Are you sure you want to delete the discount "${discount.discount_name}"? This action cannot be undone.`)) {
    router.delete(route('discounts.destroy', { id: discount.discount_id }), {
      onSuccess: () => {
        if (showViewModal.value && selectedViewDiscount.value?.discount_id === discount.discount_id) {
          closeViewModal()
        }
      }
    })
  }
}

const applyFilter = (status) => {
  router.get(route('discounts.index'), { status }, {
    preserveState: true,
    preserveScroll: true
  })
}

const formatPrice = (price) => {
  const num = parseFloat(price) || 0
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(num)
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatRelativeTime = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffMs = now - date
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))
  
  if (diffDays === 0) return 'today'
  if (diffDays === 1) return 'yesterday'
  if (diffDays < 7) return `${diffDays} days ago`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
  if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`
  return `${Math.floor(diffDays / 365)} years ago`
}

const getRelativeTime = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffMs = date - now
  const diffDays = Math.ceil(diffMs / (1000 * 60 * 60 * 24))
  
  if (diffDays > 0) {
    return `in ${diffDays} day${diffDays !== 1 ? 's' : ''}`
  } else if (diffDays < 0) {
    return `${Math.abs(diffDays)} day${Math.abs(diffDays) !== 1 ? 's' : ''} ago`
  } else {
    return 'today'
  }
}

const getProductImage = (product) => {
  if (!product) return '/images/default-product.png'
  
  const imagePath = product.image || product.image_url || product.main_image_url
  
  if (imagePath) {
    if (imagePath.startsWith('http') || imagePath.startsWith('/')) {
      return imagePath
    }
    return `/storage/${imagePath}`
  }
  
  return '/images/default-product.png'
}

const calculateDiscountedPrice = (originalPrice, discountPercent) => {
  const price = parseFloat(originalPrice) || 0
  const discount = parseFloat(discountPercent) || 0
  const discountAmount = (price * discount) / 100
  const discountedPrice = price - discountAmount
  return Math.max(discountedPrice, 0)
}

const calculateSavings = (originalPrice, discountPercent) => {
  const price = parseFloat(originalPrice) || 0
  const discount = parseFloat(discountPercent) || 0
  return (price * discount) / 100
}

const getStatusColor = (status) => {
  const colors = {
    active: 'success',
    upcoming: 'warning',
    expired: 'danger'
  }
  return colors[status] || 'secondary'
}

const getRemainingDays = (discount) => {
  const endDate = new Date(discount.end_date)
  const today = new Date()
  const diffTime = endDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays > 0 ? diffDays : 0
}

// Initialize
onMounted(() => {
  // Initialize Bootstrap tooltips
  if (window.bootstrap) {
    const tooltips = document.querySelectorAll('[title]')
    tooltips.forEach(tooltip => {
      new window.bootstrap.Tooltip(tooltip)
    })
  }
  
  // Add custom dropdown functionality
  document.addEventListener('click', (event) => {
    const dropdowns = document.querySelectorAll('.custom-dropdown')
    dropdowns.forEach(dropdown => {
      if (!dropdown.contains(event.target)) {
        dropdown.classList.remove('show')
      }
    })
  })
  
  // Toggle custom dropdown
  const dropdownToggles = document.querySelectorAll('.custom-dropdown-toggle')
  dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', function(event) {
      event.stopPropagation()
      const dropdown = this.closest('.custom-dropdown')
      dropdown.classList.toggle('show')
    })
  })
})
</script>

<style scoped>
/* ===== GLOBAL DARK MODE STYLES ===== */
body.dark-theme {
  background-color: #0f172a !important;
  color: #f1f5f9 !important;
}

body.light-theme {
  background-color: #ffffff !important;
  color: #1e293b !important;
}

.container-fluid.dark-theme {
  background-color: #0f172a;
  color: #f1f5f9;
  min-height: 100vh;
}

.container-fluid.light-theme {
  background-color: #ffffff;
  color: #1e293b;
}

/* ===== DARK MODE UTILITY CLASSES ===== */
.bg-dark-secondary {
  background-color: #1e293b !important;
}

.text-light {
  color: #f1f5f9 !important;
}

.border-light {
  border-color: #475569 !important;
}

.bg-dark {
  background-color: #0f172a !important;
}

/* ===== CARDS ===== */
.dark-card {
  background-color: #1e293b !important;
  border-color: #334155 !important;
  color: #f1f5f9 !important;
}

.dark-card .card-header {
  background-color: #334155 !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.dark-card .card-body {
  background-color: #1e293b !important;
  color: #f1f5f9 !important;
}

.dark-card-header {
  background-color: #334155 !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.dark-card-body {
  background-color: #1e293b !important;
  color: #f1f5f9 !important;
}

/* ===== FORMS ===== */
.dark-input {
  background-color: #1e293b !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.dark-input::placeholder {
  color: #94a3b8 !important;
}

.dark-input:focus {
  background-color: #1e293b !important;
  border-color: #667eea !important;
  color: #f1f5f9 !important;
  box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.5) !important;
}

.dark-select {
  background-color: #1e293b !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.dark-option {
  background-color: #1e293b !important;
  color: #f1f5f9 !important;
}

.dark-option:hover {
  background-color: #334155 !important;
}

.dark-option:checked {
  background-color: rgba(102, 126, 234, 0.3) !important;
  color: #7c93ff !important;
}

.dark-input-group {
  background-color: #334155 !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

/* ===== DROPDOWNS ===== */
.dark-dropdown-toggle {
  background-color: #1e293b !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.dark-dropdown-toggle:hover {
  background-color: #334155 !important;
  border-color: #475569 !important;
}

.dark-dropdown-menu {
  background-color: #1e293b !important;
  border-color: #475569 !important;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.5) !important;
}

.dark-dropdown-item {
  color: #f1f5f9 !important;
}

.dark-dropdown-item:hover {
  background-color: #334155 !important;
  color: #f1f5f9 !important;
}

.dark-divider {
  border-top: 1px solid #475569 !important;
}

/* ===== MODALS ===== */
.dark-modal {
  background-color: #1e293b !important;
  border-color: #475569 !important;
}

.dark-modal-header {
  background-color: #334155 !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.dark-modal-body {
  background-color: #1e293b !important;
  color: #f1f5f9 !important;
}

.dark-modal-footer {
  background-color: #334155 !important;
  border-color: #475569 !important;
}

/* ===== ALERTS ===== */
.dark-alert {
  background-color: rgba(30, 41, 59, 0.8) !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.dark-alert.alert-success {
  background-color: rgba(16, 185, 129, 0.2) !important;
  border-color: rgba(16, 185, 129, 0.3) !important;
  color: #34d399 !important;
}

.dark-alert.alert-danger {
  background-color: rgba(239, 68, 68, 0.2) !important;
  border-color: rgba(239, 68, 68, 0.3) !important;
  color: #f87171 !important;
}

.dark-alert.alert-warning {
  background-color: rgba(245, 158, 11, 0.2) !important;
  border-color: rgba(245, 158, 11, 0.3) !important;
  color: #fbbf24 !important;
}

/* ===== BUTTONS ===== */
.btn-outline-light {
  color: #f1f5f9 !important;
  border-color: #475569 !important;
}

.btn-outline-light:hover {
  background-color: #475569 !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.btn-dark {
  background-color: #334155 !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.btn-dark:hover {
  background-color: #475569 !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

/* ===== PROGRESS BARS ===== */
.progress.bg-dark {
  background-color: #475569 !important;
}

/* ===== EXISTING STYLES (Keep all your original styles below) ===== */

/* Custom Dropdown Styles */
.custom-dropdown {
  position: relative;
  display: inline-block;
}

.custom-dropdown-toggle {
  display: flex;
  align-items: center;
  padding: 0.5rem 1rem;
  background-color: white;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
}

.custom-dropdown-toggle:hover {
  background-color: #f8f9fa;
}

.custom-dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 1000;
  display: none;
  min-width: 200px;
  margin-top: 0.125rem;
  padding: 0.5rem 0;
  background-color: white;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 0.375rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.custom-dropdown.show .custom-dropdown-menu {
  display: block;
}

.custom-dropdown-item {
  display: block;
  width: 100%;
  padding: 0.375rem 1rem;
  clear: both;
  font-weight: 400;
  color: #212529;
  text-align: left;
  text-decoration: none;
  white-space: nowrap;
  background-color: transparent;
  border: 0;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.custom-dropdown-item:hover {
  background-color: #f8f9fa;
  color: #16181b;
}

.custom-dropdown-divider {
  height: 0;
  margin: 0.5rem 0;
  overflow: hidden;
  border-top: 1px solid #e9ecef;
}

/* Discount Card Styles */
.discount-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.discount-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

/* Compact Product Card */
.compact-product-card {
  transition: background-color 0.2s ease;
  cursor: pointer;
}

.compact-product-card:hover {
  background-color: #e9ecef !important;
}

/* Timeline Styles */
.timeline {
  position: relative;
  padding-left: 2rem;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 0.75rem;
  top: 0;
  bottom: 0;
  width: 2px;
  background-color: #dee2e6;
}

.timeline-item {
  position: relative;
  margin-bottom: 1.5rem;
}

.timeline-marker {
  position: absolute;
  left: -2rem;
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 50%;
  background-color: #6c757d;
}

.timeline-content {
  padding-left: 1rem;
}

/* Avatar Styles */
.avatar-sm {
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Gradient Backgrounds */
.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.bg-gradient-warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.bg-gradient-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

/* Price Comparison */
.price-comparison .original-price {
  font-size: 0.875rem;
}

.price-comparison .new-price {
  font-size: 1.25rem;
}

.price-comparison .savings {
  font-size: 0.875rem;
}

/* Clickable Elements */
.clickable {
  cursor: pointer;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .custom-dropdown-menu {
    position: fixed;
    left: 50%;
    transform: translateX(-50%);
    min-width: 250px;
  }
  
  .discount-card {
    margin-bottom: 1rem;
  }
  
  .price-comparison .new-price {
    font-size: 1rem;
  }
}

@media (max-width: 576px) {
  .compact-product-card {
    flex-direction: column;
    text-align: center;
  }
  
  .product-image-small {
    margin-right: 0 !important;
    margin-bottom: 1rem;
  }
  
  .view-icon {
    margin-left: 0 !important;
    margin-top: 1rem;
  }
}

/* Animation for modal */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal.fade.show {
  animation: fadeIn 0.3s ease;
}

/* Custom scrollbar for dropdown */
.custom-dropdown-menu {
  max-height: 300px;
  overflow-y: auto;
}

.custom-dropdown-menu::-webkit-scrollbar {
  width: 6px;
}

.custom-dropdown-menu::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.custom-dropdown-menu::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}

.custom-dropdown-menu::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Product selection dropdown styling */
.form-select-lg option {
  padding: 10px 15px;
  font-size: 1rem;
  border-bottom: 1px solid #f1f1f1;
}

.form-select-lg option:hover {
  background-color: #f8f9fa;
}

.form-select-lg option:checked {
  background-color: #e3f2fd;
  color: #0d6efd;
  font-weight: 600;
}

/* Badge adjustments */
.badge {
  font-weight: 500;
  letter-spacing: 0.5px;
}

/* Progress bar customization */
.progress {
  border-radius: 10px;
  overflow: hidden;
}

.progress-bar {
  border-radius: 10px;
  transition: width 0.6s ease;
}

/* Alert styling */
.alert {
  border: none;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Card hover effects */
.card {
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
}

/* Button hover effects */
.btn {
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.btn:active {
  transform: translateY(0);
}

/* Input focus effects */
.form-control:focus,
.form-select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}

/* Quick percentage buttons */
.btn-outline-primary.active {
  background-color: #667eea;
  border-color: #667eea;
  color: white;
}

/* Modal backdrop */
.modal {
  backdrop-filter: blur(5px);
}

/* Empty state styling */
.empty-state-icon {
  opacity: 0.3;
}

.empty-state-icon i {
  font-size: 4rem;
}

/* Stock status colors */
.text-success {
  color: #10b981 !important;
}

.text-warning {
  color: #f59e0b !important;
}

.text-danger {
  color: #ef4444 !important;
}

/* Border colors for status */
.border-success {
  border-color: #10b981 !important;
}

.border-warning {
  border-color: #f59e0b !important;
}

.border-danger {
  border-color: #ef4444 !important;
}

/* Background opacity for cards */
.bg-opacity-10 {
  background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

.bg-opacity-25 {
  background-color: rgba(255, 255, 255, 0.25) !important;
}

/* Text opacity */
.opacity-75 {
  opacity: 0.75;
}

.opacity-25 {
  opacity: 0.25;
}

/* Custom spacing */
.gap-2 {
  gap: 0.5rem !important;
}

.gap-3 {
  gap: 1rem !important;
}

/* Shadow utilities */
.shadow-sm {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.shadow-lg {
  box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

/* Border utilities */
.border-start {
  border-left-style: solid !important;
  border-left-width: 5px !important;
}

/* Position utilities */
.sticky-top {
  position: sticky !important;
  top: 1rem !important;
}

/* Text truncation */
.text-truncate {
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
}

/* Flex utilities */
.flex-shrink-0 {
  flex-shrink: 0 !important;
}

.flex-grow-1 {
  flex-grow: 1 !important;
}

/* Form text styling */
.form-text {
  font-size: 0.875rem;
  color: #6c757d;
  margin-top: 0.25rem;
}

/* Alert link styling */
.alert-link {
  text-decoration: underline;
  font-weight: 600;
}

.alert-link:hover {
  text-decoration: none;
}

/* Modal header gradient */
.modal-header.bg-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

/* Custom dropdown animation */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.custom-dropdown.show .custom-dropdown-menu {
  animation: slideDown 0.2s ease;
}

/* Discount card border animation */
.discount-card {
  border-left-width: 5px !important;
}

/* Product image container */
.product-image-small img {
  object-fit: cover;
  border-radius: 8px;
}

/* Price comparison styling */
.text-decoration-line-through {
  text-decoration-thickness: 2px;
}

/* Savings badge */
.badge.bg-danger {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
}

/* View icon button */
.view-icon .btn {
  padding: 0.25rem;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Timeline marker sizes */
.timeline-marker {
  width: 1.25rem;
  height: 1.25rem;
  margin-top: 0.25rem;
}

/* Responsive adjustments for timeline */
@media (max-width: 768px) {
  .timeline {
    padding-left: 1.5rem;
  }
  
  .timeline-marker {
    left: -1.5rem;
    width: 1rem;
    height: 1rem;
  }
}

/* Custom scrollbar for modal content */
.modal-body {
  max-height: 70vh;
  overflow-y: auto;
}

.modal-body::-webkit-scrollbar {
  width: 8px;
}

.modal-body::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

/* Spinner animation */
.spinner-border {
  animation: spinner-border 0.75s linear infinite;
}

@keyframes spinner-border {
  to { transform: rotate(360deg); }
}

/* Form validation styling */
.form-control:invalid,
.form-select:invalid {
  border-color: #dc3545;
}

.form-control:valid,
.form-select:valid {
  border-color: #198754;
}

/* Quick percentage buttons container */
.d-flex.flex-wrap {
  flex-wrap: wrap;
}

/* Ensure buttons don't overflow on small screens */
@media (max-width: 576px) {
  .d-flex.gap-2 {
    gap: 0.25rem !important;
  }
  
  .btn-lg {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
  }
  
  .form-control-lg,
  .form-select-lg {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
  }
}

/* Make sure images don't overflow */
img {
  max-width: 100%;
  height: auto;
}

/* Ensure modal is properly centered */
.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 1rem);
}

/* Custom backdrop for modal */
.modal.show::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: -1;
}

/* ===== DARK MODE SPECIFIC STYLES ===== */
.dark-theme .timeline::before {
  background-color: #475569 !important;
}

.dark-theme .timeline-item .timeline-content h6,
.dark-theme .timeline-item .timeline-content p {
  color: #f1f5f9 !important;
}

.dark-theme .timeline-item .timeline-content small {
  color: #94a3b8 !important;
}

.dark-theme .form-text {
  color: #94a3b8 !important;
}

.dark-theme .alert-link {
  color: #fbbf24 !important;
}

.dark-theme .alert-link:hover {
  color: #f59e0b !important;
}

.dark-theme .form-select-lg option:hover {
  background-color: #334155 !important;
}

.dark-theme .custom-dropdown-menu::-webkit-scrollbar-track {
  background: #334155 !important;
}

.dark-theme .custom-dropdown-menu::-webkit-scrollbar-thumb {
  background: #475569 !important;
}

.dark-theme .custom-dropdown-menu::-webkit-scrollbar-thumb:hover {
  background: #64748b !important;
}

.dark-theme .modal-body::-webkit-scrollbar-track {
  background: #334155 !important;
}

.dark-theme .modal-body::-webkit-scrollbar-thumb {
  background: #475569 !important;
}

.dark-theme .modal-body::-webkit-scrollbar-thumb:hover {
  background: #64748b !important;
}

.dark-theme .btn-outline-primary {
  border-color: #7c93ff !important;
  color: #7c93ff !important;
}

.dark-theme .btn-outline-primary:hover,
.dark-theme .btn-outline-primary.active {
  background-color: #7c93ff !important;
  border-color: #7c93ff !important;
  color: white !important;
}

.dark-theme .text-primary {
  color: #7c93ff !important;
}

.dark-theme .border-primary {
  border-color: #7c93ff !important;
}

.dark-theme .bg-opacity-10 {
  background-color: rgba(124, 147, 255, 0.1) !important;
}

/* Fix for Bootstrap tooltips in dark mode */
.dark-theme .tooltip {
  --bs-tooltip-bg: #1e293b;
  --bs-tooltip-color: #f1f5f9;
}

/* Fix for date inputs in dark mode */
.dark-theme input[type="date"]::-webkit-calendar-picker-indicator {
  filter: invert(1) brightness(2);
}

/* Fix for select dropdown arrow in dark mode */
.dark-theme .form-select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23f1f5f9' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
}

/* Ensure gradient cards work in dark mode */
.dark-theme .bg-gradient-primary {
  background: linear-gradient(135deg, #7c93ff 0%, #8a6bb5 100%) !important;
}

.dark-theme .bg-gradient-success {
  background: linear-gradient(135deg, #34d399 0%, #10b981 100%) !important;
}

.dark-theme .bg-gradient-warning {
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
}

.dark-theme .bg-gradient-danger {
  background: linear-gradient(135deg, #f87171 0%, #ef4444 100%) !important;
}

/* Ensure text on gradient cards remains white */
.bg-gradient-primary,
.bg-gradient-success,
.bg-gradient-warning,
.bg-gradient-danger {
  color: white !important;
}

.bg-gradient-primary .text-white,
.bg-gradient-success .text-white,
.bg-gradient-warning .text-white,
.bg-gradient-danger .text-white {
  color: white !important;
}

/* Progress bar text colors in dark mode */
.dark-theme .text-success { color: #34d399 !important; }
.dark-theme .text-warning { color: #fbbf24 !important; }
.dark-theme .text-danger { color: #f87171 !important; }
.dark-theme .text-muted { color: #94a3b8 !important; }

/* Badge colors in dark mode */
.dark-theme .badge.bg-success { background-color: #34d399 !important; }
.dark-theme .badge.bg-warning { background-color: #fbbf24 !important; }
.dark-theme .badge.bg-danger { background-color: #f87171 !important; }
.dark-theme .badge.bg-secondary { background-color: #94a3b8 !important; }
.dark-theme .badge.bg-info { background-color: #38bdf8 !important; }

/* Border colors in dark mode */
.dark-theme .border-success { border-color: #34d399 !important; }
.dark-theme .border-warning { border-color: #fbbf24 !important; }
.dark-theme .border-danger { border-color: #f87171 !important; }

/* Ensure hover states work in dark mode */
.dark-theme .compact-product-card:hover {
  background-color: #334155 !important;
}

.dark-theme .discount-card:hover {
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3) !important;
}

/* Fix for close button in dark mode modals */
.dark-theme .btn-close-white {
  filter: invert(1) brightness(2) grayscale(100%);
}

/* Ensure placeholder text is visible in dark mode */
.dark-theme ::placeholder {
  color: #94a3b8 !important;
  opacity: 0.7 !important;
}

/* Fix for disabled buttons in dark mode */
.dark-theme .btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Ensure modal overlay is darker in dark mode */
.dark-theme .modal.fade.show {
  background-color: rgba(0, 0, 0, 0.8) !important;
}
</style>