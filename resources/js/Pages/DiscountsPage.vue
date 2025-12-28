<template>
  <AppLayout>
    <!-- Flash Messages -->
    <div v-if="$page.props.flash.success" class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" @click="$page.props.flash.success = null"></button>
    </div>
    
    <div v-if="$page.props.flash.error" class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i>
      {{ $page.props.flash.error }}
      <button type="button" class="btn-close" @click="$page.props.flash.error = null"></button>
    </div>

    <div class="container-fluid py-4">
      <!-- Header Section -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h1 class="h2 fw-bold mb-1">
                <i class="fas fa-percentage text-primary me-2"></i>
                Discount Management
              </h1>
              <p class="text-muted mb-0">Manage your product discounts and promotions</p>
            </div>
            <button @click="openCreateModal" class="btn btn-primary btn-lg shadow-sm">
              <i class="fas fa-plus-circle me-2"></i>
              Create Discount
            </button>
          </div>
        </div>
      </div>

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
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                  <!-- Pure CSS Dropdown -->
                  <div class="custom-dropdown">
                    <button class="custom-dropdown-toggle btn btn-outline-secondary" type="button">
                      <i class="fas fa-filter me-2"></i>
                      {{ filterStatus ? statusLabels[filterStatus] : 'All Status' }}
                      <i class="fas fa-caret-down ms-2"></i>
                    </button>
                    <ul class="custom-dropdown-menu">
                      <li>
                        <button @click="applyFilter('')" class="custom-dropdown-item">
                          <i class="fas fa-list me-2"></i> All Discounts
                        </button>
                      </li>
                      <li><hr class="custom-dropdown-divider"></li>
                      <li>
                        <button @click="applyFilter('active')" class="custom-dropdown-item">
                          <span class="badge bg-success me-2">●</span> Active
                        </button>
                      </li>
                      <li>
                        <button @click="applyFilter('upcoming')" class="custom-dropdown-item">
                          <span class="badge bg-warning me-2">●</span> Upcoming
                        </button>
                      </li>
                      <li>
                        <button @click="applyFilter('expired')" class="custom-dropdown-item">
                          <span class="badge bg-danger me-2">●</span> Expired
                        </button>
                      </li>
                    </ul>
                  </div>
                  
                  <div class="input-group" style="width: 300px;">
                    <span class="input-group-text bg-transparent">
                      <i class="fas fa-search"></i>
                    </span>
                    <input type="text" v-model="searchQuery" 
                           class="form-control" 
                           placeholder="Search discounts...">
                  </div>
                </div>
                
                <!-- <div>
                  <button @click="refreshData" class="btn btn-outline-primary">
                    <i class="fas fa-sync-alt me-2"></i> Refresh
                  </button>
                </div> -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Discounts Grid -->
      <div class="row">
        <div class="col-12">
          <!-- Empty State -->
          <div v-if="filteredDiscounts.length === 0" class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
              <div class="empty-state-icon mb-4">
                <i class="fas fa-tag fa-4x text-muted opacity-25"></i>
              </div>
              <h4 class="fw-bold mb-3">No Discounts Found</h4>
              <p class="text-muted mb-4">
                {{ searchQuery ? 'No discounts match your search' : 
                   filterStatus ? `You don't have any ${filterStatus} discounts` : 
                   "You haven't created any discounts yet" }}
              </p>
              <button @click="openCreateModal" class="btn btn-primary btn-lg">
                <i class="fas fa-plus-circle me-2"></i>
                Create Your First Discount
              </button>
            </div>
          </div>

          <!-- Discounts Grid -->
          <div v-else class="row g-4">
            <div v-for="discount in filteredDiscounts" :key="discount.discount_id" class="col-xl-4 col-lg-6">
              <div class="card border-0 shadow-sm h-100 discount-card" 
                   :class="`border-start border-5 border-${getStatusColor(discount.status)}`">
                <div class="card-body">
                  <!-- Discount Header -->
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                      <h5 class="fw-bold text-primary mb-1">
                        <i class="fas fa-tag me-2"></i>
                        {{ discount.discount_name }}
                      </h5>
                      <div class="d-flex align-items-center gap-2">
                        <span :class="`badge bg-${getStatusColor(discount.status)}`">
                          {{ discount.status.toUpperCase() }}
                        </span>
                        <small class="text-muted">
                          <i class="far fa-calendar me-1"></i>
                          {{ formatDate(discount.start_date) }} - {{ formatDate(discount.end_date) }}
                        </small>
                      </div>
                    </div>
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                      <button @click.stop="openEditModal(discount)" 
                              class="btn btn-sm btn-outline-primary"
                              title="Edit Discount">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button @click.stop="confirmDelete(discount)" 
                              class="btn btn-sm btn-outline-danger"
                              title="Delete Discount">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Compact Product Card -->
                  <div class="compact-product-card mb-3 p-3 bg-light rounded clickable" @click="openViewModal(discount)">
                    <div class="d-flex align-items-center">
                      <!-- Product Image -->
                      <div class="product-image-small me-3">
                        <img :src="getProductImage(discount.product)" 
                             :alt="discount.product?.product_name"
                             class="img-fluid rounded" 
                             style="width: 60px; height: 60px; object-fit: cover;">
                      </div>
                      
                      <!-- Product Info -->
                      <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1 text-truncate" style="max-width: 200px;">
                          {{ discount.product?.product_name || 'Product not found' }}
                        </h6>
                        
                        <!-- Price Comparison -->
                        <div class="price-comparison mt-2">
                          <!-- Original Price -->
                          <div class="original-price text-muted small mb-1">
                            <span class="text-decoration-line-through">
                              {{ formatPrice(discount.product?.price) }} Birr
                            </span>
                            <span class="badge bg-danger ms-2">
                              -{{ discount.discount_amount }}%
                            </span>
                          </div>
                          
                          <!-- New Price -->
                          <div class="new-price">
                            <span class="fw-bold text-danger h5 mb-0">
                              {{ formatPrice(calculateDiscountedPrice(discount.product?.price, discount.discount_amount)) }} Birr
                            </span>
                          </div>
                          
                          <!-- Savings -->
                          <div class="savings small text-success">
                            <i class="fas fa-save me-1"></i>
                            Save {{ formatPrice(calculateSavings(discount.product?.price, discount.discount_amount)) }} Birr
                          </div>
                        </div>
                      </div>
                      
                      <!-- View Icon -->
                      <div class="view-icon ms-2">
                        <button class="btn btn-sm btn-outline-info border-0"
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
                      <small class="text-muted">
                        <i class="far fa-calendar me-1"></i>
                        {{ getRemainingDays(discount) }} days remaining
                      </small>
                      <small class="text-muted">{{ Math.round(discount.progress_percentage || 0) }}%</small>
                    </div>
                    <div class="progress" style="height: 6px;">
                      <div :class="`progress-bar bg-${getStatusColor(discount.status)}`" 
                           :style="{ width: (discount.progress_percentage || 0) + '%' }"></div>
                    </div>
                  </div>
                </div>
                
                <div class="card-footer bg-transparent border-top-0 pt-0">
                  <div class="text-muted small">
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
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <!-- Modal Header -->
          <div class="modal-header" :class="editingDiscount ? 'bg-warning text-dark' : 'bg-primary text-white'">
            <h5 class="modal-title fw-bold">
              <i :class="editingDiscount ? 'fas fa-edit me-2' : 'fas fa-plus-circle me-2'"></i>
              {{ editingDiscount ? 'Edit Discount' : 'Create New Discount' }}
            </h5>
            <button type="button" class="btn-close" :class="editingDiscount ? '' : 'btn-close-white'" 
                    @click="closeModal"></button>
          </div>
          
          <!-- Modal Body -->
          <div class="modal-body">
            <form @submit.prevent="saveDiscount">
              <!-- Discount Name -->
              <div class="mb-4">
                <label class="form-label fw-bold">
                  <i class="fas fa-tag me-2 text-primary"></i>
                  Discount Name *
                </label>
                <input type="text" 
                       v-model="form.discount_name" 
                       class="form-control form-control-lg" 
                       placeholder="e.g., Summer Sale, Black Friday, Flash Sale"
                       required>
                <div class="form-text">Give your discount a descriptive name</div>
              </div>

              <!-- Product Selection -->
              <div class="mb-4">
                <label class="form-label fw-bold">
                  <i class="fas fa-box me-2 text-primary"></i>
                  Select Product *
                </label>
                <div v-if="products.length === 0" class="alert alert-warning">
                  <i class="fas fa-exclamation-triangle me-2"></i>
                  You don't have any products yet. 
                  <a :href="route('products.create')" class="alert-link">Create a product first</a>
                </div>
                <select v-else 
                        v-model="form.product_id" 
                        class="form-select form-select-lg" 
                        required>
                  <option value="">-- Select a Product --</option>
                  <option v-for="product in products" 
                          :key="product.product_id" 
                          :value="product.product_id">
                    {{ product.product_name }} 
                    <template v-if="product.price">({{ formatPrice(product.price) }} Birr)</template>
                  </option>
                </select>
                <div class="form-text">Select the product you want to discount</div>
              </div>

              <!-- Discount Amount -->
              <div class="mb-4">
                <label class="form-label fw-bold">
                  <i class="fas fa-percent me-2 text-primary"></i>
                  Discount Percentage *
                </label>
                <div class="input-group input-group-lg">
                  <input type="number" 
                         v-model.number="form.discount_amount" 
                         class="form-control" 
                         min="1" 
                         max="100" 
                         step="1" 
                         required>
                  <span class="input-group-text">%</span>
                </div>
                
                <!-- Quick Percentage Buttons -->
                <div class="mt-2">
                  <label class="form-label">Quick Select:</label>
                  <div class="d-flex flex-wrap gap-2">
                    <button type="button" 
                            v-for="percent in [10, 20, 30, 40, 50, 60, 70]" 
                            :key="percent"
                            @click="form.discount_amount = percent"
                            :class="`btn btn-outline-primary ${form.discount_amount === percent ? 'active' : ''}`">
                      {{ percent }}%
                    </button>
                  </div>
                </div>
              </div>

              <!-- Date Range -->
              <div class="row mb-4">
                <div class="col-md-6">
                  <label class="form-label fw-bold">
                    <i class="far fa-calendar-alt me-2 text-primary"></i>
                    Start Date *
                  </label>
                  <input type="date" 
                         v-model="form.start_date" 
                         class="form-control form-control-lg" 
                         :min="minDate"
                         required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">
                    <i class="far fa-calendar-alt me-2 text-primary"></i>
                    End Date *
                  </label>
                  <input type="date" 
                         v-model="form.end_date" 
                         class="form-control form-control-lg" 
                         :min="form.start_date || minDate"
                         required>
                </div>
                <div class="col-12 mt-2">
                  <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Discount will be active between these dates
                  </div>
                </div>
              </div>

              <!-- Preview -->
              <div v-if="form.product_id && form.discount_amount" class="card border-success mb-4">
                <div class="card-header bg-success bg-opacity-10 border-success">
                  <h6 class="fw-bold mb-0 text-success">
                    <i class="fas fa-eye me-2"></i>
                    Price Preview
                  </h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-2">
                        <span class="text-muted">Original Price:</span>
                        <div class="fw-bold text-decoration-line-through">
                          {{ formatPrice(selectedProduct?.price) }} Birr
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-2">
                        <span class="text-muted">Discount:</span>
                        <div class="fw-bold text-success">{{ form.discount_amount }}%</div>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="text-center">
                    <div class="text-muted small mb-1">Discounted Price</div>
                    <div class="h2 fw-bold text-danger">
                      {{ formatPrice(calculateDiscountedPrice(selectedProduct?.price, form.discount_amount)) }} Birr
                    </div>
                    <div class="text-success">
                      <i class="fas fa-save me-1"></i>
                      Save {{ formatPrice(calculateSavings(selectedProduct?.price, form.discount_amount)) }} Birr
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          
          <!-- Modal Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-lg" @click="closeModal">
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
    <div v-if="showViewModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <!-- Modal Header -->
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fw-bold">
              <i class="fas fa-eye me-2"></i>
              Discount Details
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="closeViewModal"></button>
          </div>
          
          <!-- Modal Body -->
          <div class="modal-body">
            <div v-if="selectedViewDiscount" class="row">
              <!-- Discount Info -->
              <div class="col-md-8">
                <div class="mb-4">
                  <h3 class="fw-bold text-primary mb-2">
                    <i class="fas fa-tag me-2"></i>
                    {{ selectedViewDiscount.discount_name }}
                  </h3>
                  <div class="d-flex align-items-center gap-3">
                    <span :class="`badge bg-${getStatusColor(selectedViewDiscount.status)} fs-6`">
                      {{ selectedViewDiscount.status.toUpperCase() }}
                    </span>
                    <div class="text-muted">
                      <i class="far fa-calendar me-1"></i>
                      {{ formatDate(selectedViewDiscount.start_date) }} - {{ formatDate(selectedViewDiscount.end_date) }}
                    </div>
                  </div>
                </div>

                <!-- Product Info -->
                <div class="card mb-4 border-primary">
                  <div class="card-header bg-primary bg-opacity-10 border-primary">
                    <h6 class="fw-bold mb-0 text-primary">
                      <i class="fas fa-box me-2"></i>
                      Product Information
                    </h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-4">
                        <img :src="getProductImage(selectedViewDiscount.product)" 
                             :alt="selectedViewDiscount.product?.product_name"
                             class="img-fluid rounded mb-3"
                             style="width: 150px; height: 150px; object-fit: cover;">
                      </div>
                      <div class="col-md-8">
                        <h5 class="fw-bold mb-2">{{ selectedViewDiscount.product?.product_name || 'Product not found' }}</h5>
                        <div class="row">
                          <div class="col-6">
                            <div class="mb-2">
                              <span class="text-muted">Category:</span>
                              <div class="fw-bold">{{ selectedViewDiscount.product?.category?.name || 'N/A' }}</div>
                            </div>
                            <div class="mb-2">
                              <span class="text-muted">Stock:</span>
                              <div class="fw-bold">{{ selectedViewDiscount.product?.stock || 0 }} units</div>
                            </div>
                          </div>
                          <div class="col-6">
                            <!-- <div class="mb-2">
                              <span class="text-muted">SKU:</span>
                              <div class="fw-bold">{{ selectedViewDiscount.product?.sku || 'N/A' }}</div>
                            </div>
                            <div class="mb-2">
                              <span class="text-muted">Barcode:</span>
                              <div class="fw-bold">{{ selectedViewDiscount.product?.barcode || 'N/A' }}</div>
                            </div> -->
                          </div>
                        </div>
                        <div class="mt-3">
                          <span class="text-muted">Description:</span>
                          <p class="mb-0">{{ selectedViewDiscount.product?.description || 'No description available' }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Timeline -->
                <div class="card mb-4">
                  <div class="card-header">
                    <h6 class="fw-bold mb-0">
                      <i class="fas fa-history me-2"></i>
                      Discount Timeline
                    </h6>
                  </div>
                  <div class="card-body">
                    <div class="timeline">
                      <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                          <h6 class="fw-bold">Start Date</h6>
                          <p class="text-muted mb-0">{{ formatDate(selectedViewDiscount.start_date) }}</p>
                          <small class="text-muted">{{ getRelativeTime(selectedViewDiscount.start_date) }}</small>
                        </div>
                      </div>
                      <div class="timeline-item">
                        <div class="timeline-marker" :class="`bg-${getStatusColor(selectedViewDiscount.status)}`"></div>
                        <div class="timeline-content">
                          <h6 class="fw-bold">Current Status</h6>
                          <p class="mb-0">{{ selectedViewDiscount.status.toUpperCase() }}</p>
                          <small class="text-muted">{{ getRemainingDays(selectedViewDiscount) }} days remaining</small>
                        </div>
                      </div>
                      <div class="timeline-item">
                        <div class="timeline-marker bg-secondary"></div>
                        <div class="timeline-content">
                          <h6 class="fw-bold">End Date</h6>
                          <p class="text-muted mb-0">{{ formatDate(selectedViewDiscount.end_date) }}</p>
                          <small class="text-muted">{{ getRelativeTime(selectedViewDiscount.end_date) }}</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Price Summary -->
              <div class="col-md-4">
                <div class="sticky-top" style="top: 20px;">
                  <div class="card border-success shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                      <h6 class="fw-bold mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Price Summary
                      </h6>
                    </div>
                    <div class="card-body">
                      <!-- Original Price -->
                      <div class="mb-3">
                        <div class="text-muted small mb-1">Original Price</div>
                        <div class="fw-bold text-decoration-line-through fs-4 text-muted">
                          {{ formatPrice(selectedViewDiscount.product?.price) }} Birr
                        </div>
                      </div>

                      <!-- Discount -->
                      <div class="mb-3">
                        <div class="text-muted small mb-1">Discount</div>
                        <div class="d-flex align-items-center">
                          <span class="fw-bold text-success fs-3 me-2">
                            {{ selectedViewDiscount.discount_amount }}%
                          </span>
                          <span class="badge bg-danger fs-6">OFF</span>
                        </div>
                      </div>

                      <hr>

                      <!-- Discounted Price -->
                      <div class="mb-3">
                        <div class="text-muted small mb-1">Discounted Price</div>
                        <div class="fw-bold text-danger fs-2">
                          {{ formatPrice(calculateDiscountedPrice(selectedViewDiscount.product?.price, selectedViewDiscount.discount_amount)) }} Birr
                        </div>
                      </div>

                      <!-- Savings -->
                      <div class="mb-3">
                        <div class="text-muted small mb-1">You Save</div>
                        <div class="fw-bold text-success fs-4">
                          <i class="fas fa-piggy-bank me-2"></i>
                          {{ formatPrice(calculateSavings(selectedViewDiscount.product?.price, selectedViewDiscount.discount_amount)) }} Birr
                        </div>
                      </div>

                      <!-- Progress Bar -->
                      <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                          <small class="text-muted">Progress</small>
                          <small class="text-muted">{{ Math.round(selectedViewDiscount.progress_percentage || 0) }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                          <div :class="`progress-bar bg-${getStatusColor(selectedViewDiscount.status)}`" 
                               :style="{ width: (selectedViewDiscount.progress_percentage || 0) + '%' }"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="card">
                    <div class="card-body">
                      <div class="d-grid gap-3">
                        <button @click="openEditModal(selectedViewDiscount)" 
                                class="btn btn-warning btn-lg">
                          <i class="fas fa-edit me-2"></i>
                          Edit Discount
                        </button>
                        <button @click="confirmDelete(selectedViewDiscount)" 
                                class="btn btn-danger btn-lg">
                          <i class="fas fa-trash me-2"></i>
                          Delete Discount
                        </button>
                        <button @click="closeViewModal" 
                                class="btn btn-outline-secondary btn-lg">
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
import { ref, reactive, computed, onMounted } from 'vue'
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

// Computed
const minDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const selectedProduct = computed(() => {
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
    discounts = discounts.filter(d => 
      d.discount_name.toLowerCase().includes(query) ||
      d.product?.product_name?.toLowerCase().includes(query)
    )
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

const refreshData = () => {
  router.reload()
}

const formatPrice = (price) => {
  const num = parseFloat(price) || 0
  return new Intl.NumberFormat('en-US').format(num)
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
  if (product.image) {
    if (product.image.startsWith('http') || product.image.startsWith('/')) {
      return product.image
    }
    return `/storage/${product.image}`
  }
  return '/images/default-product.png'
}

const calculateDiscountedPrice = (originalPrice, discountPercent) => {
  const price = parseFloat(originalPrice) || 0
  const discount = parseFloat(discountPercent) || 0
  const discountAmount = (price * discount) / 100
  return price - discountAmount
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
})
</script>

<style scoped>
.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.bg-gradient-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
}

.bg-gradient-warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
}

.bg-gradient-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
}

.avatar-sm {
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.discount-card {
  transition: all 0.3s ease;
  border-radius: 15px;
  overflow: hidden;
}

.discount-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

.compact-product-card {
  transition: all 0.3s ease;
  cursor: pointer;
}

.compact-product-card:hover {
  background-color: #f8f9fa !important;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.product-image-small img {
  border: 2px solid #e9ecef;
  transition: all 0.3s ease;
}

.compact-product-card:hover .product-image-small img {
  border-color: #0d6efd;
  transform: scale(1.05);
}

.price-comparison .original-price {
  font-size: 0.875rem;
}

.price-comparison .new-price {
  line-height: 1.2;
}

.view-icon button {
  transition: all 0.3s ease;
}

.view-icon button:hover {
  background-color: #0dcaf0 !important;
  color: white !important;
  transform: scale(1.1);
}

.text-decoration-line-through {
  text-decoration: line-through;
}

.modal {
  z-index: 1060;
}

.modal-backdrop {
  z-index: 1055;
}

.empty-state-icon {
  opacity: 0.5;
}

.progress {
  border-radius: 10px;
  overflow: hidden;
}

.progress-bar {
  border-radius: 10px;
}

.btn-outline-primary.active {
  background-color: #0d6efd;
  color: white;
  border-color: #0d6efd;
}

.card-footer {
  font-size: 0.875rem;
}

/* Action Buttons */
.btn-outline-primary, .btn-outline-danger, .btn-outline-info {
  transition: all 0.2s ease;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

.btn-outline-primary:hover {
  background-color: #0d6efd;
  color: white;
  transform: translateY(-2px);
}

.btn-outline-danger:hover {
  background-color: #dc3545;
  color: white;
  transform: translateY(-2px);
}

.btn-outline-info:hover {
  background-color: #0dcaf0;
  color: white;
  transform: translateY(-2px);
}

/* Custom CSS Dropdown */
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
  color: #212529;
  font-weight: 400;
  font-size: 1rem;
  text-align: left;
  cursor: pointer;
  transition: all 0.2s ease;
}

.custom-dropdown-toggle:hover {
  background-color: #f8f9fa;
  border-color: #adb5bd;
}

.custom-dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 1000;
  display: none;
  min-width: 200px;
  padding: 0.5rem 0;
  margin: 0.125rem 0 0;
  font-size: 1rem;
  color: #212529;
  text-align: left;
  list-style: none;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.custom-dropdown:hover .custom-dropdown-menu {
  display: block;
}

.custom-dropdown-item {
  display: block;
  width: 100%;
  padding: 0.5rem 1rem;
  clear: both;
  font-weight: 400;
  color: #212529;
  text-align: inherit;
  text-decoration: none;
  white-space: nowrap;
  background-color: transparent;
  border: 0;
  cursor: pointer;
  transition: all 0.2s ease;
}

.custom-dropdown-item:hover {
  background-color: #f8f9fa;
  color: #1e2125;
}

.custom-dropdown-divider {
  height: 0;
  margin: 0.5rem 0;
  overflow: hidden;
  border-top: 1px solid #dee2e6;
}

/* Timeline */
.timeline {
  position: relative;
  padding-left: 30px;
}

.timeline-item {
  position: relative;
  margin-bottom: 30px;
}

.timeline-marker {
  position: absolute;
  left: -30px;
  top: 0;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  z-index: 2;
}

.timeline-content {
  padding-left: 10px;
}

.timeline::before {
  content: '';
  position: absolute;
  left: -25px;
  top: 0;
  bottom: 0;
  width: 2px;
  background-color: #dee2e6;
}

.sticky-top {
  position: sticky;
  z-index: 1020;
}

@media (max-width: 768px) {
  .modal-dialog {
    margin: 1rem;
  }
  
  .btn-lg {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
  }
  
  .form-control-lg {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
  }
  
  .h2 {
    font-size: 1.5rem;
  }
  
  .h3 {
    font-size: 1.25rem;
  }
  
  .custom-dropdown-menu {
    min-width: 180px;
  }
  
  .compact-product-card .price-comparison .new-price {
    font-size: 1rem;
  }
  
  .d-flex.gap-2 > .btn-sm {
    width: 32px;
    height: 32px;
    padding: 0.25rem;
  }
}
</style>