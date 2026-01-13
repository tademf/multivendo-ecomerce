<template>
  <AppLayout>
    <div class="container-fluid py-4" :class="themeClass">
      <!-- Flash Messages -->
      <div class="row mb-3">
        <div class="col-12">
          <div v-if="$page.props.flash.success" class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <div class="d-flex align-items-center">
              <div class="alert-icon me-2">
                <i class="fas fa-check-circle fa-sm"></i>
              </div>
              <div class="flex-grow-1">
                <p class="mb-0">{{ $page.props.flash.success }}</p>
              </div>
              <button type="button" class="btn-close" @click="$page.props.flash.success = null" aria-label="Close"></button>
            </div>
          </div>

          <div v-if="$page.props.flash.error" class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <div class="d-flex align-items-center">
              <div class="alert-icon me-2">
                <i class="fas fa-exclamation-triangle fa-sm"></i>
              </div>
              <div class="flex-grow-1">
                <p class="mb-0">{{ $page.props.flash.error }}</p>
              </div>
              <button type="button" class="btn-close" @click="$page.props.flash.error = null" aria-label="Close"></button>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="row g-3">
            <div class="col-md-2 col-6" @click="filterOrders('')">
              <div class="stat-card-medium">
                <div class="stat-icon-medium" :class="theme === 'dark' ? 'bg-primary-dark' : 'bg-primary-subtle'">
                  <i class="fas fa-shopping-bag text-primary"></i>
                </div>
                <div class="stat-content-medium">
                  <div class="stat-number-medium">{{ orders.length }}</div>
                  <div class="stat-label-medium">Total Orders</div>
                </div>
              </div>
            </div>
            
            <div class="col-md-2 col-6" @click="filterOrders('pending')">
              <div class="stat-card-medium">
                <div class="stat-icon-medium" :class="theme === 'dark' ? 'bg-warning-dark' : 'bg-warning-subtle'">
                  <i class="fas fa-clock text-warning"></i>
                </div>
                <div class="stat-content-medium">
                  <div class="stat-number-medium">{{ getStatusCount('pending') }}</div>
                  <div class="stat-label-medium">Pending</div>
                </div>
              </div>
            </div>
            
            <div class="col-md-2 col-6" @click="filterOrders('processing')">
              <div class="stat-card-medium">
                <div class="stat-icon-medium" :class="theme === 'dark' ? 'bg-info-dark' : 'bg-info-subtle'">
                  <i class="fas fa-cog text-info"></i>
                </div>
                <div class="stat-content-medium">
                  <div class="stat-number-medium">{{ getStatusCount('processing') }}</div>
                  <div class="stat-label-medium">Processing</div>
                </div>
              </div>
            </div>

            <div class="col-md-2 col-6" @click="filterOrders('shipped')">
              <div class="stat-card-medium">
                <div class="stat-icon-medium" :class="theme === 'dark' ? 'bg-primary-dark' : 'bg-primary-subtle'">
                  <i class="fas fa-truck text-primary"></i>
                </div>
                <div class="stat-content-medium">
                  <div class="stat-number-medium">{{ getStatusCount('shipped') }}</div>
                  <div class="stat-label-medium">Shipped</div>
                </div>
              </div>
            </div>
            
            <div class="col-md-2 col-6" @click="filterOrders('delivered')">
              <div class="stat-card-medium">
                <div class="stat-icon-medium" :class="theme === 'dark' ? 'bg-success-dark' : 'bg-success-subtle'">
                  <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="stat-content-medium">
                  <div class="stat-number-medium">{{ getStatusCount('delivered') }}</div>
                  <div class="stat-label-medium">Delivered</div>
                </div>
              </div>
            </div>
            
            <div class="col-md-2 col-6" @click="filterOrders('cancelled')">
              <div class="stat-card-medium">
                <div class="stat-icon-medium" :class="theme === 'dark' ? 'bg-danger-dark' : 'bg-danger-subtle'">
                  <i class="fas fa-times-circle text-danger"></i>
                </div>
                <div class="stat-content-medium">
                  <div class="stat-number-medium">{{ getStatusCount('cancelled') }}</div>
                  <div class="stat-label-medium">Cancelled</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Bar and Filter -->
      <div class="row mb-3">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="fw-bold mb-0">All Orders</h5>
            
            <!-- Search Bar -->
            <div class="search-container">
              <div class="input-group search-group" :class="theme === 'dark' ? 'bg-dark border-secondary' : 'bg-white'">
                <span class="input-group-text border-end-0" :class="theme === 'dark' ? 'bg-dark border-secondary text-light' : 'bg-white'">
                  <i class="fas fa-search" :class="theme === 'dark' ? 'text-light' : 'text-muted'"></i>
                </span>
                <input
                  type="text"
                  v-model="searchQuery"
                  @input="performSearch"
                  class="form-control search-input border-start-0"
                  :class="theme === 'dark' ? 'bg-dark border-secondary text-light' : ''"
                  placeholder="Search orders..."
                />
                <button 
                  v-if="searchQuery" 
                  @click="clearSearch"
                  class="input-group-text border-start-0"
                  :class="theme === 'dark' ? 'bg-dark border-secondary text-light' : 'bg-white'"
                  type="button"
                >
                  <i class="fas fa-times" :class="theme === 'dark' ? 'text-light' : 'text-muted'"></i>
                </button>
              </div>
            </div>

            <!-- Filter Dropdown -->
            <div class="custom-filter-dropdown">
              <button class="filter-dropdown-btn" @click="toggleFilterDropdown" :class="theme === 'dark' ? 'bg-dark border-secondary text-light' : 'bg-white'">
                <i class="fas fa-filter me-2"></i>
                {{ selectedFilter === 'all' ? 'All Status' : formatStatus(selectedFilter) }}
                <i class="fas fa-chevron-down ms-2"></i>
              </button>
              <div class="filter-dropdown-menu" v-if="showFilterDropdown" :class="theme === 'dark' ? 'bg-dark border-secondary' : ''">
                <div class="filter-dropdown-header" :class="theme === 'dark' ? 'bg-dark text-light border-secondary' : ''">Filter by Status</div>
                <div class="filter-dropdown-item" @click="filterOrders('all')" :class="theme === 'dark' ? 'text-light' : ''">
                  <span class="filter-badge-all">All Orders</span>
                  <span class="filter-count">{{ orders.length }}</span>
                </div>
                <div class="filter-dropdown-divider" :class="theme === 'dark' ? 'bg-secondary' : ''"></div>
                <div class="filter-dropdown-item" @click="filterOrders('pending')" :class="theme === 'dark' ? 'text-light' : ''">
                  <span class="filter-badge filter-badge-warning me-2"></span>
                  <span>Pending</span>
                  <span class="filter-count">{{ getStatusCount('pending') }}</span>
                </div>
                <div class="filter-dropdown-item" @click="filterOrders('processing')" :class="theme === 'dark' ? 'text-light' : ''">
                  <span class="filter-badge filter-badge-info me-2"></span>
                  <span>Processing</span>
                  <span class="filter-count">{{ getStatusCount('processing') }}</span>
                </div>
                <div class="filter-dropdown-item" @click="filterOrders('shipped')" :class="theme === 'dark' ? 'text-light' : ''">
                  <span class="filter-badge filter-badge-primary me-2"></span>
                  <span>Shipped</span>
                  <span class="filter-count">{{ getStatusCount('shipped') }}</span>
                </div>
                <div class="filter-dropdown-item" @click="filterOrders('delivered')" :class="theme === 'dark' ? 'text-light' : ''">
                  <span class="filter-badge filter-badge-success me-2"></span>
                  <span>Delivered</span>
                  <span class="filter-count">{{ getStatusCount('delivered') }}</span>
                </div>
                <div class="filter-dropdown-item" @click="filterOrders('cancelled')" :class="theme === 'dark' ? 'text-light' : ''">
                  <span class="filter-badge filter-badge-danger me-2"></span>
                  <span>Cancelled</span>
                  <span class="filter-count">{{ getStatusCount('cancelled') }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Results Info -->
      <div v-if="searchQuery" class="row mb-3">
        <div class="col-12">
          <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 py-2" role="alert">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <i class="fas fa-search me-2 text-info"></i>
                <div class="small">
                  <span class="fw-medium">Search results for:</span>
                  <span class="ms-1 fw-bold">"{{ searchQuery }}"</span>
                  <span class="ms-2" :class="theme === 'dark' ? 'text-light' : 'text-muted'">({{ filteredOrders.length }} orders found)</span>
                </div>
              </div>
              <button @click="clearSearch" class="btn btn-sm btn-outline-info">
                <i class="fas fa-times me-1"></i>Clear
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Grid - 1 order per row -->
      <div class="row">
        <div class="col-12">
          <div v-if="filteredOrders.length === 0" class="text-center py-5">
            <div class="empty-state">
              <i class="fas fa-store-slash fa-2x mb-3" :class="theme === 'dark' ? 'text-light' : 'text-muted'"></i>
              <h6 class="fw-bold mb-2">
                {{ searchQuery ? 'No orders found' : 'No orders found' }}
              </h6>
              <p class="mb-3 small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">
                <template v-if="searchQuery">
                  No orders match your search "{{ searchQuery }}"
                </template>
                <template v-else>
                  {{ selectedFilter !== 'all' ? `No ${formatStatus(selectedFilter).toLowerCase()} orders found` : "No orders yet" }}
                </template>
              </p>
              <div class="d-flex gap-2 justify-content-center">
                <button v-if="searchQuery" @click="clearSearch" class="btn btn-primary btn-sm">
                  <i class="fas fa-redo me-1"></i>Clear Search
                </button>
                <button @click="filterOrders('all')" class="btn btn-outline-secondary btn-sm">
                  <i class="fas fa-list me-1"></i>Show All
                </button>
              </div>
            </div>
          </div>

          <div v-else class="row g-3">
            <div v-for="order in sortedOrders" :key="order.id" class="col-12">
              <div class="order-card" :class="{'active': selectedOrder?.id === order.id}" :style="theme === 'dark' ? 'background: #1e1e1e; border-color: #444;' : ''">
                <div class="row g-0 align-items-center">
                  <div class="col-md-2 col-12 p-3">
                    <div class="d-flex flex-column">
                      <strong class="text-primary mb-1">#{{ order.order_number || order.id }}</strong>
                      <small :class="theme === 'dark' ? 'text-light' : 'text-muted'">{{ formatDate(order.created_at) }}</small>
                    </div>
                  </div>

                  <div class="col-md-2 col-12 p-3">
                    <div class="d-flex align-items-center">
                      <div class="customer-avatar me-2">
                        <i class="fas fa-user"></i>
                      </div>
                      <div>
                        <div class="fw-medium">{{ order.customer_name || 'Customer' }}</div>
                        <small :class="theme === 'dark' ? 'text-light' : 'text-muted'">{{ order.customer_email || '' }}</small>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-3 col-12 p-3">
                    <div class="d-flex align-items-center">
                      <div class="product-img me-2">
                        <img :src="getProductImage(order.product_image || order.image)" 
                             :alt="order.product_name"
                             @error="handleImageError">
                      </div>
                      <div>
                        <div class="fw-medium">{{ order.product_name || 'Product' }}</div>
                        <small :class="theme === 'dark' ? 'text-light' : 'text-muted'">{{ order.quantity || 1 }} pcs</small>
                        <!-- Discount badge -->
                        <div v-if="order.is_discounted" class="discount-badge-container mt-1">
                          <span class="badge bg-success me-1">
                            <i class="fas fa-tag me-1"></i>{{ order.discount_name || 'Discount Applied' }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-2 col-12 p-3">
                    <span :class="statusBadgeClass(order.status)" class="badge">
                      {{ formatStatus(order.status) }}
                    </span>
                  </div>

                  <div class="col-md-2 col-12 p-3">
                    <div class="fw-bold text-primary">{{ formatPrice(order.amount) }} Birr</div>
                    <!-- Original price for discount products -->
                    <div v-if="order.is_discounted && order.original_price" 
                         class="small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">
                      <s>{{ formatPrice(order.original_price) }} Birr</s>
                      <span class="text-success ms-1">
                        (Save {{ formatPrice(order.original_price - order.amount) }} Birr)
                      </span>
                    </div>
                  </div>

                  <div class="col-md-1 col-12 p-3">
                    <button @click="selectOrder(order)" class="btn btn-sm btn-outline-primary w-100" title="View">
                      <i class="fas fa-eye"></i> View
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredOrders.length > 0" class="row mt-3">
        <div class="col-12">
          <div class="text-center small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">
            Showing {{ filteredOrders.length }} of {{ orders.length }} orders
            <span v-if="selectedFilter !== 'all'">
              ({{ formatStatus(selectedFilter) }})
            </span>
            <span v-if="searchQuery">
              • Search: "{{ searchQuery }}"
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Sidebar -->
    <div v-if="selectedOrder" class="offcanvas offcanvas-end show" tabindex="-1" style="visibility: visible; width: 450px;" :class="theme === 'dark' ? 'bg-dark text-light' : ''">
      <div class="offcanvas-header border-bottom py-3" :class="theme === 'dark' ? 'border-secondary' : ''">
        <div>
          <h5 class="offcanvas-title fw-bold">Order #{{ selectedOrder.order_number || selectedOrder.id }}</h5>
          <p :class="theme === 'dark' ? 'text-light' : 'text-muted'" class="mb-0 small">{{ formatDate(selectedOrder.created_at) }}</p>
        </div>
        <button type="button" class="btn-close" @click="selectedOrder = null" :class="theme === 'dark' ? 'btn-close-white' : ''"></button>
      </div>
      <div class="offcanvas-body p-0">
        <div class="order-details">
          <div class="p-3 border-bottom" :class="theme === 'dark' ? 'border-secondary' : ''">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="fw-bold mb-0">Status</h6>
              <span :class="statusBadgeClass(selectedOrder.status)" class="badge">
                <i :class="statusIcon(selectedOrder.status) + ' me-1'"></i>
                {{ formatStatus(selectedOrder.status) }}
              </span>
            </div>

            <div class="timeline">
              <div v-for="step in statusSteps" :key="step.status" 
                   class="timeline-step"
                   :class="{'active': isStepActive(step.status), 'completed': isStepCompleted(step.status)}">
                <div class="timeline-dot">
                  <i :class="step.icon"></i>
                </div>
                <div class="timeline-content">
                  <div class="step-title">{{ step.label }}</div>
                </div>
              </div>
            </div>

            <div class="mt-3">
              <label class="form-label fw-bold mb-2 small">Update Status</label>
              <div class="d-flex flex-wrap gap-1">
                <button v-for="status in statusOptions" :key="status.value"
                        @click="confirmStatusChange(status.value)"
                        :class="{'active': status.value === selectedOrder.status}"
                        class="btn btn-sm d-flex align-items-center"
                        :disabled="status.value === selectedOrder.status"
                        :style="theme === 'dark' ? 'background: #2d2d2d; border-color: #444; color: #f8f9fa;' : ''">
                  {{ status.label }}
                </button>
              </div>
            </div>
          </div>

          <div class="p-3 border-bottom" :class="theme === 'dark' ? 'border-secondary' : ''">
            <h6 class="fw-bold mb-3">Product Details</h6>
            <div class="d-flex gap-2">
              <div class="product-img-lg">
                <img :src="getProductImage(selectedOrder.product_image || selectedOrder.image)" 
                     :alt="selectedOrder.product_name"
                     @error="handleImageError">
              </div>
              <div class="flex-grow-1">
                <h6 class="fw-bold mb-2">{{ selectedOrder.product_name || 'Product' }}</h6>
                
                <!-- Discount information -->
                <div v-if="selectedOrder.is_discounted" class="mb-3">
                  <div class="p-2 rounded" :class="theme === 'dark' ? 'bg-success-dark' : 'bg-success bg-opacity-10'">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="text-success small fw-bold">
                          <i class="fas fa-tag me-1"></i>Discount Applied
                        </div>
                        <div class="small text-success">
                          {{ selectedOrder.discount_name || 'Special Discount' }}
                        </div>
                      </div>
                      <div class="text-end">
                        <div class="small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">Customer saved</div>
                        <div class="text-success fw-bold">
                          {{ formatPrice(selectedOrder.original_price - selectedOrder.amount) }} Birr
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="row g-2">
                  <div class="col-6">
                    <div class="p-2 rounded" :class="theme === 'dark' ? 'bg-secondary' : 'bg-light'">
                      <div class="small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">Quantity</div>
                      <div class="fw-bold">{{ selectedOrder.quantity || 1 }}</div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="p-2 rounded" :class="theme === 'dark' ? 'bg-secondary' : 'bg-light'">
                      <div class="small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">Unit Price</div>
                      <div class="fw-bold">{{ formatPrice(selectedOrder.amount / (selectedOrder.quantity || 1)) }} Birr</div>
                    </div>
                  </div>
                  
                  <!-- Original price for discount -->
                  <div v-if="selectedOrder.is_discounted && selectedOrder.original_price" 
                       class="col-12 mt-2">
                    <div class="p-2 rounded" :class="theme === 'dark' ? 'bg-secondary' : 'bg-light'">
                      <div class="d-flex justify-content-between">
                        <div class="small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">Original Price</div>
                        <div class="fw-bold" :class="theme === 'dark' ? 'text-light' : 'text-muted'">
                          <s>{{ formatPrice(selectedOrder.original_price) }} Birr</s>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-12 mt-2">
                    <div class="p-2 rounded" :class="theme === 'dark' ? 'bg-primary-dark' : 'bg-primary bg-opacity-10'">
                      <div class="small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">Total Amount Received</div>
                      <div class="fw-bold text-primary">{{ formatPrice(selectedOrder.amount) }} Birr</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="p-3 border-bottom" :class="theme === 'dark' ? 'border-secondary' : ''">
            <h6 class="fw-bold mb-3">Payment Information</h6>
            <div class="mb-3">
              <span :class="paymentStatusBadgeClass(selectedOrder)" class="badge">
                <i :class="paymentStatusIcon(selectedOrder) + ' me-1'"></i>
                {{ getPaymentStatus(selectedOrder) }}
              </span>
            </div>
            
            <div class="row g-2 mb-3">
              <div class="col-6">
                <div class="p-2 rounded" :class="theme === 'dark' ? 'bg-secondary' : 'bg-light'">
                  <div class="small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">Payment Method</div>
                  <div class="fw-medium">{{ selectedOrder.payment_method || 'Bank Transfer' }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="p-2 rounded" :class="theme === 'dark' ? 'bg-secondary' : 'bg-light'">
                  <div class="small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">Reference</div>
                  <div class="fw-medium">{{ selectedOrder.payment_reference || selectedOrder.order_reference || 'N/A' }}</div>
                </div>
              </div>
            </div>

            <div v-if="selectedOrder.payment_image" class="mt-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0">Payment Proof</h6>
                <button @click="downloadPaymentProof(selectedOrder)" class="btn btn-sm btn-outline-primary" :class="theme === 'dark' ? 'border-primary text-primary' : ''">
                  <i class="fas fa-download me-1"></i>Download
                </button>
              </div>
              <div class="payment-proof" @click="viewPaymentProof(selectedOrder)">
                <img :src="getPaymentImage(selectedOrder.payment_image)" 
                     alt="Payment Proof"
                     class="img-fluid rounded">
              </div>
            </div>
          </div>

          <div class="p-3">
            <h6 class="fw-bold mb-3">Customer Information</h6>
            <div class="d-flex align-items-center gap-2 mb-3">
              <div class="customer-avatar">
                <i class="fas fa-user"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">{{ selectedOrder.customer_name || 'Customer' }}</h6>
                <div v-if="selectedOrder.customer_email" class="small" :class="theme === 'dark' ? 'text-light' : 'text-muted'">
                  {{ selectedOrder.customer_email }}
                </div>
              </div>
            </div>

            <div class="mt-3">
              <div class="mb-2">
                <i class="fas fa-map-marker-alt small me-2" :class="theme === 'dark' ? 'text-light' : 'text-muted'"></i>
                <span class="fw-medium small">Shipping Address</span>
              </div>
              <div class="p-2 rounded small" :class="theme === 'dark' ? 'bg-secondary' : 'bg-light'">
                {{ selectedOrder.shipment_address || 'No address provided' }}
              </div>
            </div>

            <div v-if="selectedOrder.tracking_number" class="mt-3">
              <div class="mb-2">
                <i class="fas fa-shipping-fast small me-2" :class="theme === 'dark' ? 'text-light' : 'text-muted'"></i>
                <span class="fw-medium small">Tracking Number</span>
              </div>
              <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm" :value="selectedOrder.tracking_number" readonly :class="theme === 'dark' ? 'bg-secondary border-secondary text-light' : ''">
                <button @click="copyTrackingNumber" class="btn btn-outline-secondary" type="button" :class="theme === 'dark' ? 'border-secondary text-light' : ''">
                  <i class="fas fa-copy"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="offcanvas-footer border-top p-3" :class="theme === 'dark' ? 'border-secondary' : ''">
        <div class="d-flex gap-2">
          <button v-if="selectedOrder.status !== 'cancelled'" 
                  @click="openCancelModal"
                  class="btn btn-outline-danger btn-sm flex-grow-1" :class="theme === 'dark' ? 'border-danger text-danger' : ''">
            <i class="fas fa-times me-1"></i>Cancel
          </button>
          <!-- Changed from "Contact" to "Message Customer" -->
          <button @click="messageCustomer(selectedOrder)" class="btn btn-primary btn-sm flex-grow-1">
            <i class="fas fa-comments me-1"></i>Message
          </button>
        </div>
      </div>
    </div>
    <div v-if="selectedOrder" class="offcanvas-backdrop fade show" @click="selectedOrder = null"></div>

    <!-- Status Change Modal -->
    <div v-if="showStatusConfirm" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" :class="theme === 'dark' ? 'bg-dark text-light' : ''">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">Confirm Status Change</h5>
          </div>
          <div class="modal-body py-4">
            <div class="text-center mb-4">
              <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
                <span :class="statusBadgeClass(currentStatusChange.from)" class="badge">
                  {{ formatStatus(currentStatusChange.from) }}
                </span>
                <i class="fas fa-arrow-right" :class="theme === 'dark' ? 'text-light' : 'text-muted'"></i>
                <span :class="statusBadgeClass(currentStatusChange.to)" class="badge">
                  {{ formatStatus(currentStatusChange.to) }}
                </span>
              </div>
              <p class="mb-0">Change order status?</p>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-outline-secondary btn-sm" @click="cancelStatusChange">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" @click="proceedStatusChange">Confirm</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Proof Modal -->
    <div v-if="showPaymentProof" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.8)">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
          <div class="modal-header border-0 position-absolute top-0 end-0 z-3">
            <button type="button" class="btn-close btn-close-white" @click="closePaymentProof"></button>
          </div>
          <div class="modal-body p-0">
            <img :src="currentPaymentProof" alt="Payment Proof" class="img-fluid w-100">
          </div>
          <div class="modal-footer border-0">
            <button @click="downloadCurrentProof" class="btn btn-primary btn-sm">
              <i class="fas fa-download me-2"></i>Download
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Order Modal -->
    <div v-if="showCancelModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" :class="theme === 'dark' ? 'bg-dark text-light' : ''">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">Cancel Order</h5>
          </div>
          <div class="modal-body py-4">
            <div class="alert alert-danger small">
              <i class="fas fa-exclamation-triangle me-2"></i>
              This action cannot be undone.
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-bold small">Reason *</label>
              <textarea v-model="cancellationReason" 
                        class="form-control form-control-sm"
                        rows="3"
                        placeholder="Reason for cancellation..."
                        required :class="theme === 'dark' ? 'bg-secondary border-secondary text-light' : ''"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-outline-secondary btn-sm" @click="closeCancelModal">Keep</button>
            <button type="button" class="btn btn-danger btn-sm" 
                    @click="confirmCancelOrder"
                    :disabled="!cancellationReason.trim()">
              Cancel Order
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  },
  user: {
    type: Object,
    required: true
  }
})

const showFilterDropdown = ref(false)
const selectedFilter = ref('all')
const filterStatus = ref('')
const selectedOrder = ref(null)
const showPaymentProof = ref(false)
const currentPaymentProof = ref('')
const showStatusConfirm = ref(false)
const currentStatusChange = ref({ from: '', to: '' })
const showCancelModal = ref(false)
const cancellationReason = ref('')
const searchQuery = ref('')
const debounceTimeout = ref(null)
const theme = ref('light')

// Theme handling
const initTheme = () => {
  const savedTheme = localStorage.getItem('theme') || 'light'
  theme.value = savedTheme
  applyTheme(savedTheme)
  
  window.addEventListener('theme-changed', (event) => {
    const newTheme = event.detail.theme
    theme.value = newTheme
    applyTheme(newTheme)
  })
}

const applyTheme = (themeMode) => {
  const html = document.documentElement
  html.setAttribute('data-theme', themeMode)
}

const themeClass = computed(() => {
  return theme.value === 'dark' ? 'dark-theme' : 'light-theme'
})

onMounted(() => {
  initTheme()
})

// Status options
const statusOptions = [
  { value: 'pending', label: 'Pending' },
  { value: 'processing', label: 'Processing' },
  { value: 'shipped', label: 'Shipping' },
  { value: 'delivered', label: 'Delivered' }
]

const statusSteps = [
  { status: 'pending', label: 'Order Placed', icon: 'fas fa-shopping-cart' },
  { status: 'processing', label: 'Processing', icon: 'fas fa-cog' },
  { status: 'shipped', label: 'Shipping', icon: 'fas fa-truck' },
  { status: 'delivered', label: 'Delivered', icon: 'fas fa-check-circle' }
]

// Close dropdown when clicking outside
const closeDropdown = (event) => {
  if (!event.target.closest('.custom-filter-dropdown')) {
    showFilterDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown)
  if (debounceTimeout.value) {
    clearTimeout(debounceTimeout.value)
  }
})

const toggleFilterDropdown = (event) => {
  event.stopPropagation()
  showFilterDropdown.value = !showFilterDropdown.value
}

// Search functionality
const performSearch = () => {
  // Clear any existing timeout
  if (debounceTimeout.value) {
    clearTimeout(debounceTimeout.value)
  }
  
  // Debounce search to avoid too many re-renders
  debounceTimeout.value = setTimeout(() => {
    // Search logic is handled in computed property
  }, 300)
}

const clearSearch = () => {
  searchQuery.value = ''
  if (debounceTimeout.value) {
    clearTimeout(debounceTimeout.value)
  }
}

// Filter and sort with search
const filteredOrders = computed(() => {
  let filtered = props.orders
  
  // Apply status filter
  if (filterStatus.value) {
    filtered = filtered.filter(order => order.status === filterStatus.value)
  }
  
  // Apply search filter if search query exists
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.trim().toLowerCase()
    filtered = filtered.filter(order => {
      // Search in multiple fields
      return (
        // Order number
        (order.order_number && order.order_number.toLowerCase().includes(query)) ||
        (order.id && order.id.toString().includes(query)) ||
        
        // Customer information
        (order.customer_name && order.customer_name.toLowerCase().includes(query)) ||
        (order.customer_email && order.customer_email.toLowerCase().includes(query)) ||
        
        // Product information
        (order.product_name && order.product_name.toLowerCase().includes(query)) ||
        
        // Tracking number
        (order.tracking_number && order.tracking_number.toLowerCase().includes(query)) ||
        
        // Order reference
        (order.order_reference && order.order_reference.toLowerCase().includes(query)) ||
        
        // Payment reference
        (order.payment_reference && order.payment_reference.toLowerCase().includes(query))
      )
    })
  }
  
  return filtered
})

const sortedOrders = computed(() => {
  return [...filteredOrders.value].sort((a, b) => 
    new Date(b.created_at) - new Date(a.created_at)
  )
})

// Filter orders by status
const filterOrders = (filter) => {
  selectedFilter.value = filter
  filterStatus.value = filter === 'all' ? '' : filter
  showFilterDropdown.value = false
  
  // Clear search when changing filter
  if (searchQuery.value) {
    searchQuery.value = ''
  }
}

// Helper methods
const getStatusCount = (status) => {
  if (!status) return props.orders.length
  return props.orders.filter(order => order.status === status).length
}

const formatPrice = (price) => {
  if (!price) return '0'
  const num = parseFloat(price)
  return num.toLocaleString('en-US')
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatStatus = (status) => {
  const statusMap = {
    'pending': 'Pending',
    'processing': 'Processing',
    'shipped': 'Shipping',
    'delivered': 'Delivered',
    'cancelled': 'Cancelled',
    'all': 'All Status'
  }
  return statusMap[status] || status
}

const statusBadgeClass = (status) => {
  const classes = {
    'pending': 'bg-warning text-dark',
    'processing': 'bg-info text-white',
    'shipped': 'bg-primary text-white',
    'delivered': 'bg-success text-white',
    'cancelled': 'bg-danger text-white'
  }
  return classes[status] || 'bg-secondary text-white'
}

const statusIcon = (status) => {
  const icons = {
    'pending': 'fas fa-clock',
    'processing': 'fas fa-cog',
    'shipped': 'fas fa-truck',
    'delivered': 'fas fa-check-circle',
    'cancelled': 'fas fa-times-circle'
  }
  return icons[status] || 'fas fa-question-circle'
}

const getProductImage = (imagePath) => {
  if (!imagePath) return 'https://placehold.co/400x300/e0e7ff/667eea?text=Product'
  if (imagePath.startsWith('http')) return imagePath
  if (imagePath.startsWith('storage/')) return `/${imagePath}`
  return `/storage/${imagePath}`
}

const getPaymentImage = (imagePath) => {
  return getProductImage(imagePath)
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/400x300/e0e7ff/667eea?text=Product'
}

const getPaymentStatus = (order) => {
  if (order.payment_status === 'completed') return 'Paid'
  if (order.payment_status === 'pending') return 'Pending'
  if (order.payment_status === 'cancelled') return 'Refunded'
  if (order.status === 'cancelled') return 'Refunded'
  return 'Pending'
}

const paymentStatusBadgeClass = (order) => {
  const status = getPaymentStatus(order)
  if (status === 'Paid') return 'bg-success text-white'
  if (status === 'Pending') return 'bg-warning text-dark'
  return 'bg-light text-dark'
}

const paymentStatusIcon = (order) => {
  const status = getPaymentStatus(order)
  if (status === 'Paid') return 'fas fa-check-circle'
  if (status === 'Pending') return 'fas fa-clock'
  return 'fas fa-question-circle'
}

// Order status timeline
const isStepActive = (stepStatus) => {
  return selectedOrder.value?.status === stepStatus
}

const isStepCompleted = (stepStatus) => {
  const statusOrder = ['pending', 'processing', 'shipped', 'delivered']
  const currentIndex = statusOrder.indexOf(selectedOrder.value?.status)
  const stepIndex = statusOrder.indexOf(stepStatus)
  return stepIndex <= currentIndex
}

// Order actions
const selectOrder = (order) => {
  selectedOrder.value = order
}

const confirmStatusChange = (newStatus) => {
  if (!selectedOrder.value || selectedOrder.value.status === newStatus) return
  
  currentStatusChange.value = {
    from: selectedOrder.value.status,
    to: newStatus
  }
  showStatusConfirm.value = true
}

const cancelStatusChange = () => {
  showStatusConfirm.value = false
  currentStatusChange.value = { from: '', to: '' }
}

const proceedStatusChange = async () => {
  try {
    await router.put(`/orders/${selectedOrder.value.id}/status`, {
      status: currentStatusChange.value.to
    }, {
      preserveScroll: true,
      onSuccess: () => {
        showStatusConfirm.value = false
        selectedOrder.value.status = currentStatusChange.value.to
        setTimeout(() => router.reload({ preserveScroll: true }), 300)
      }
    })
  } catch (error) {
    alert('Error updating status.')
  }
}

const viewPaymentProof = (order) => {
  if (order.payment_image) {
    currentPaymentProof.value = getPaymentImage(order.payment_image)
    showPaymentProof.value = true
  }
}

const closePaymentProof = () => {
  showPaymentProof.value = false
  currentPaymentProof.value = ''
}

const downloadCurrentProof = () => {
  if (currentPaymentProof.value) {
    const link = document.createElement('a')
    link.href = currentPaymentProof.value
    link.download = `payment-proof-${selectedOrder.value?.order_number || 'proof'}.jpg`
    link.click()
  }
}

const downloadPaymentProof = (order) => {
  if (order.payment_image) {
    const link = document.createElement('a')
    link.href = getPaymentImage(order.payment_image)
    link.download = `payment-proof-${order.order_number || order.id}.jpg`
    link.click()
  }
}

const openCancelModal = () => {
  if (!selectedOrder.value) return
  showCancelModal.value = true
}

const closeCancelModal = () => {
  showCancelModal.value = false
  cancellationReason.value = ''
}

const confirmCancelOrder = async () => {
  if (!cancellationReason.value.trim()) {
    alert('Please provide a reason.')
    return
  }

  try {
    await router.post(`/orders/${selectedOrder.value.id}/cancel`, {
      cancellation_reason: cancellationReason.value
    }, {
      preserveScroll: true,
      onSuccess: () => {
        showCancelModal.value = false
        cancellationReason.value = ''
        setTimeout(() => router.reload({ preserveScroll: true }), 300)
      }
    })
  } catch (error) {
    alert('Error cancelling order.')
  }
}

// CHANGED: From email to chat messaging
const messageCustomer = (order) => {
  // Navigate to the chat page for this specific order/shipment
  // Use the messages.show route with the shipment ID
  router.get(route('messages.show', { shipment: order.id }), {
    order_id: order.id,
    customer_id: order.user_id || order.customer_id,
    customer_name: order.customer_name || 'Customer',
    product_name: order.product_name
  })
}

const copyTrackingNumber = () => {
  if (selectedOrder.value?.tracking_number) {
    navigator.clipboard.writeText(selectedOrder.value.tracking_number)
    alert('Tracking number copied to clipboard!')
  }
}
</script>

<style scoped>
/* Theme variables */
.light-theme {
  background-color: #f8f9fa !important;
  color: #212529 !important;
}

.dark-theme {
  background-color: #121212 !important;
  color: #f8f9fa !important;
}

/* Dark theme specific styles */
.dark-theme .bg-primary-dark { background-color: rgba(108, 92, 231, 0.2) !important; }
.dark-theme .bg-warning-dark { background-color: rgba(255, 193, 7, 0.2) !important; }
.dark-theme .bg-info-dark { background-color: rgba(13, 202, 240, 0.2) !important; }
.dark-theme .bg-success-dark { background-color: rgba(25, 135, 84, 0.2) !important; }
.dark-theme .bg-danger-dark { background-color: rgba(220, 53, 69, 0.2) !important; }

.dark-theme .bg-success.bg-opacity-10 {
  background-color: rgba(25, 135, 84, 0.2) !important;
}

.dark-theme .bg-primary.bg-opacity-10 {
  background-color: rgba(108, 92, 231, 0.2) !important;
}

.dark-theme .bg-light {
  background-color: #2d2d2d !important;
}

.dark-theme .text-light {
  color: #f8f9fa !important;
}

.dark-theme .border-secondary {
  border-color: #444 !important;
}

.dark-theme .table {
  color: #f8f9fa;
}

.dark-theme .table thead th {
  background-color: #2d2d2d;
  border-color: #444;
  color: #f8f9fa;
}

.dark-theme .table tbody tr {
  background-color: #1e1e1e;
  border-color: #444;
}

.dark-theme .order-row:hover {
  background-color: #2d2d2d;
}

.dark-theme .modal-content {
  background: #1e1e1e;
  color: #f8f9fa;
}

.dark-theme .offcanvas {
  background: #1e1e1e;
  color: #f8f9fa;
}

/* Base styles */
.search-container {
  min-width: 250px;
  flex-grow: 1;
  max-width: 400px;
  margin-left: 600px;
}

.search-group {
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  overflow: hidden;
  transition: all 0.2s ease;
}

.search-group:focus-within {
  border-color: #0d6efd;
  box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.1);
}

.search-input {
  border: none;
  padding: 8px 12px;
  font-size: 14px;
  color: #495057;
}

.search-input:focus {
  box-shadow: none;
  outline: none;
}

.search-input::placeholder {
  color: #adb5bd;
}

/* Custom Filter Dropdown */
.custom-filter-dropdown {
  position: relative;
  display: inline-block;
}

.filter-dropdown-btn {
  display: flex;
  align-items: center;
  padding: 8px 16px;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  color: #495057;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  min-width: 160px;
  justify-content: space-between;
}

.filter-dropdown-btn:hover {
  border-color: #6c5ce7;
  color: #6c5ce7;
}

.filter-dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 4px;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  min-width: 220px;
  z-index: 1000;
  animation: slideDown 0.2s ease;
  overflow: hidden;
}

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

.filter-dropdown-header {
  padding: 12px 16px;
  background: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  font-size: 12px;
  font-weight: 600;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.filter-dropdown-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 16px;
  cursor: pointer;
  transition: background-color 0.2s ease;
  border-bottom: 1px solid #f8f9fa;
  font-size: 14px;
  color: #495057;
}

.filter-dropdown-item:last-child {
  border-bottom: none;
}

.filter-dropdown-item:hover {
  background-color: #f8f9fa;
}

.filter-dropdown-divider {
  height: 1px;
  background: #f8f9fa;
  margin: 4px 0;
}

.filter-count {
  background: #e9ecef;
  color: #6c757d;
  font-size: 12px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 12px;
  min-width: 24px;
  text-align: center;
}

.filter-badge-all {
  color: #495057;
  font-weight: 500;
}

.filter-badge {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
}

.filter-badge-warning { background-color: #ffc107; }
.filter-badge-info { background-color: #0dcaf0; }
.filter-badge-primary { background-color: #6c5ce7; }
.filter-badge-success { background-color: #198754; }
.filter-badge-danger { background-color: #dc3545; }

/* Medium Stats Cards */
.stat-card-medium {
  background: white;
  border-radius: 10px;
  padding: 1rem;
  border: 1px solid #e9ecef;
  cursor: pointer;
  transition: all 0.2s ease;
  height: 100%;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.dark-theme .stat-card-medium {
  background: #1e1e1e;
  border-color: #444;
}

.stat-card-medium:hover {
  border-color: #6c5ce7;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.dark-theme .stat-card-medium:hover {
  border-color: #6c5ce7;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.stat-icon-medium {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  flex-shrink: 0;
}

.bg-primary-subtle { background-color: rgba(108, 92, 231, 0.1); }
.bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1); }
.bg-info-subtle { background-color: rgba(13, 202, 240, 0.1); }
.bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
.bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1); }

.stat-content-medium {
  flex-grow: 1;
  min-width: 0;
}

.stat-number-medium {
  font-size: 1.5rem;
  font-weight: 700;
  color: #212529;
  line-height: 1;
  margin-bottom: 4px;
}

.dark-theme .stat-number-medium {
  color: #f8f9fa;
}

.stat-label-medium {
  font-size: 0.8rem;
  color: #6c757d;
  font-weight: 500;
}

.dark-theme .stat-label-medium {
  color: #adb5bd;
}

/* Order Cards */
.order-card {
  background: white;
  border-radius: 8px;
  border: 1px solid #e9ecef;
  transition: all 0.2s;
  overflow: hidden;
}

.order-card:hover {
  border-color: #dee2e6;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.dark-theme .order-card:hover {
  border-color: #444;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.order-card.active {
  border-color: #0d6efd;
  background-color: #f8faff;
}

.dark-theme .order-card.active {
  border-color: #0d6efd;
  background-color: #2d2d2d;
}

.customer-avatar {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.8rem;
  flex-shrink: 0;
}

.product-img {
  width: 40px;
  height: 40px;
  border-radius: 6px;
  overflow: hidden;
  flex-shrink: 0;
  position: relative;
}

.product-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-img-lg {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
  position: relative;
}

.product-img-lg img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.discount-badge-container {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
}

.discount-badge-container .badge {
  font-size: 10px;
  padding: 2px 6px;
}

/* Timeline */
.timeline {
  position: relative;
  padding-left: 20px;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 5px;
  top: 0;
  bottom: 0;
  width: 2px;
  background-color: #dee2e6;
}

.dark-theme .timeline::before {
  background-color: #444;
}

.timeline-step {
  position: relative;
  margin-bottom: 0.5rem;
}

.timeline-step:last-child { margin-bottom: 0; }

.timeline-dot {
  position: absolute;
  left: -20px;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background-color: white;
  border: 2px solid #dee2e6;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.3rem;
}

.dark-theme .timeline-dot {
  background-color: #1e1e1e;
  border-color: #444;
}

.timeline-step.completed .timeline-dot {
  background-color: #198754;
  border-color: #198754;
  color: white;
}

.timeline-step.active .timeline-dot {
  background-color: #0d6efd;
  border-color: #0d6efd;
  color: white;
}

.step-title {
  font-size: 0.7rem;
  font-weight: 500;
}

.dark-theme .step-title {
  color: #f8f9fa;
}

/* Payment Proof */
.payment-proof {
  position: relative;
  border-radius: 6px;
  overflow: hidden;
  cursor: pointer;
}

.payment-proof img {
  transition: transform 0.2s;
}

.payment-proof:hover img { transform: scale(1.03); }

/* Badge Styles */
.badge {
  padding: 0.3em 0.6em;
  font-size: 0.75em;
  font-weight: 500;
}

/* Button Styles */
.btn-sm {
  padding: 0.2rem 0.5rem;
  font-size: 0.75rem;
}

.btn-outline-primary, .btn-outline-danger {
  border-width: 1px;
}

/* Responsive */
@media (max-width: 768px) {
  .stat-card-medium {
    padding: 0.75rem;
    gap: 0.75rem;
  }
  
  .stat-icon-medium {
    width: 40px;
    height: 40px;
    font-size: 1rem;
  }
  
  .stat-number-medium { font-size: 1.25rem; }
  .stat-label-medium { font-size: 0.75rem; }
  
  .product-img {
    width: 35px;
    height: 35px;
  }
  
  .product-img-lg {
    width: 50px;
    height: 50px;
  }
  
  .order-card .col-md-2,
  .order-card .col-md-3 {
    margin-bottom: 0.5rem;
  }
  
  .order-card .btn {
    width: auto;
  }
  
  .filter-dropdown-menu {
    position: fixed;
    top: auto;
    right: 16px;
    left: 16px;
    bottom: 16px;
    margin-top: 0;
    max-height: 60vh;
    overflow-y: auto;
  }
  
  .search-container {
    order: -1;
    width: 100%;
    max-width: 100%;
    margin-bottom: 10px;
  }
  
  .custom-filter-dropdown {
    width: 100%;
  }
  
  .filter-dropdown-btn {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .stat-card-medium {
    padding: 0.6rem;
    gap: 0.6rem;
  }
  
  .stat-icon-medium {
    width: 35px;
    height: 35px;
    font-size: 0.9rem;
  }
  
  .order-card .row > div {
    padding: 0.5rem !important;
    margin-bottom: 0.25rem;
  }
  
  .offcanvas-end {
    width: 100% !important;
  }
}

/* Compact Styles */
.small { font-size: 0.75rem !important; }
.form-control-sm { font-size: 0.75rem !important; }

.offcanvas-body {
  max-height: calc(100vh - 120px);
  overflow-y: auto;
}

.empty-state {
  padding: 3rem 1rem;
}

.empty-state i {
  font-size: 1.5rem;
}

/* Grid spacing */
.row.g-3 > .col-12 {
  margin-bottom: 0.5rem;
}
</style>