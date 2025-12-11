<template>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

  <div class="min-h-screen bg-light py-5">
    <!-- Header -->
    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
          <h1 class="display-5 fw-bold text-dark mb-2">Orders</h1>
          <p class="text-muted mb-0">Track and manage all your orders in one place</p>
        </div>
        <div>
          <a href="/" class="btn btn-outline-primary me-2">
            <i class="fas fa-home me-2"></i>Back To Home
          </a>
          <a href="/submit-payment" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Order
          </a>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="row mb-5">
        <div class="col-md-2">
          <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-white-50 mb-1">Total Orders</h6>
                  <h3 class="mb-0">{{ orders.length }}</h3>
                </div>
                <i class="fas fa-shopping-bag fa-2x opacity-50"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-white-50 mb-1">Completed</h6>
                  <h3 class="mb-0">{{ completedOrders }}</h3>
                </div>
                <i class="fas fa-check-circle fa-2x opacity-50"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-dark-50 mb-1">Pending</h6>
                  <h3 class="mb-0">{{ pendingOrders }}</h3>
                </div>
                <i class="fas fa-clock fa-2x opacity-50"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-white-50 mb-1">Processing</h6>
                  <h3 class="mb-0">{{ processingOrders }}</h3>
                </div>
                <i class="fas fa-cog fa-2x opacity-50"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-sm bg-purple text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-white-50 mb-1">Shipping</h6>
                  <h3 class="mb-0">{{ shippedOrders }}</h3>
                </div>
                <i class="fas fa-truck fa-2x opacity-50"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-white-50 mb-1">Cancelled</h6>
                  <h3 class="mb-0">{{ cancelledOrders }}</h3>
                </div>
                <i class="fas fa-times-circle fa-2x opacity-50"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="card border-0 shadow-lg">
        <div class="card-header bg-white py-4">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">Order History</h5>
            <div class="d-flex gap-2">
              <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" @click="toggleFilterDropdown">
                  <i class="fas fa-filter me-2"></i>Filter Status
                </button>
                <div v-if="showFilterDropdown" class="dropdown-menu show" style="display: block; position: absolute; transform: translate(0px, 38px);">
                  <a class="dropdown-item" href="#" @click.prevent="filterStatus = ''">All Orders</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#" @click.prevent="filterStatus = 'pending'">
                    <span class="badge bg-warning me-2">●</span>Pending
                  </a>
                  <a class="dropdown-item" href="#" @click.prevent="filterStatus = 'processing'">
                    <span class="badge bg-info me-2">●</span>Processing
                  </a>
                  <a class="dropdown-item" href="#" @click.prevent="filterStatus = 'shipped'">
                    <span class="badge bg-purple me-2">●</span>Shipped
                  </a>
                  <a class="dropdown-item" href="#" @click.prevent="filterStatus = 'delivered'">
                    <span class="badge bg-success me-2">●</span>Delivered
                  </a>
                  <a class="dropdown-item" href="#" @click.prevent="filterStatus = 'cancelled'">
                    <span class="badge bg-danger me-2">●</span>Cancelled
                  </a>
                </div>
              </div>
              <button class="btn btn-outline-secondary" @click="refreshOrders">
                <i class="fas fa-sync-alt"></i>
              </button>
            </div>
          </div>
        </div>
        
        <div class="card-body p-0">
          <!-- Empty State -->
          <div v-if="filteredOrders.length === 0" class="text-center py-5">
            <div class="py-5">
              <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
              <h4 class="text-muted mb-3">No orders found</h4>
              <p class="text-muted mb-4">You haven't placed any orders yet</p>
              <a href="/submit-payment" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>Place Your First Order
              </a>
            </div>
          </div>

          <!-- Orders Table -->
          <div v-else class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th class="ps-4">PRODUCT</th>
                  <th>ORDER NO.</th>
                  <th>DATE</th>
                  <th>CUSTOMER</th>
                  <th>AMOUNT</th>
                  <th>STATUS</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in filteredOrders" :key="order.id" class="align-middle">
                  <td class="ps-4">
                    <div class="d-flex align-items-center">
                      <div class="me-3">
                        <img 
                          :src="getProductImage(order)" 
                          :alt="order.product_name || 'Product'" 
                          class="rounded border"
                          style="width: 50px; height: 50px; object-fit: cover;"
                          @error="handleImageError"
                        >
                      </div>
                      <div>
                        <div class="fw-bold">{{ order.product_name || 'Product Name' }}</div>
                        <small class="text-muted d-block">Qty: {{ order.quantity || 1 }}</small>
                        <small v-if="order.product_id" class="text-muted">Product ID: {{ order.product_id }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="fw-bold">#{{ order.order_number }}</div>
                    <small class="text-muted">ID: {{ order.id }}</small>
                  </td>
                  <td>
                    <div>{{ formatDate(order.created_at) }}</div>
                    <small class="text-muted">{{ formatTime(order.created_at) }}</small>
                  </td>
                  <td>
                    <div class="fw-bold">{{ order.name }}</div>
                    <small class="text-muted">{{ truncateAddress(order.shipment_address, 30) }}</small>
                  </td>
                  <td>
                    <span class="fw-bold fs-5 text-dark">₹{{ order.amount }}</span>
                    <div class="text-muted small">Qty: {{ order.quantity || 1 }}</div>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button 
                        class="btn btn-sm dropdown-toggle" 
                        :class="getStatusClass(order.status)"
                        type="button" 
                        @click.stop="toggleStatusDropdown(order)"
                      >
                        <i :class="getStatusIcon(order.status)" class="me-2"></i>
                        {{ formatStatus(order.status) }}
                      </button>
                      <div v-if="activeDropdown === order.id" class="dropdown-menu show" style="display: block; position: absolute; transform: translate(0px, 34px);">
                        <h6 class="dropdown-header">Change Status To:</h6>
                        <template v-if="canChangeStatus(order)">
                          <a class="dropdown-item" href="#" @click.prevent="updateStatus(order, 'processing')">
                            <i class="fas fa-cog text-info me-2"></i>
                            Mark as Processing
                          </a>
                          <a class="dropdown-item" href="#" @click.prevent="updateStatus(order, 'shipped')">
                            <i class="fas fa-truck text-purple me-2"></i>
                            Mark as Shipped
                          </a>
                          <a class="dropdown-item" href="#" @click.prevent="updateStatus(order, 'delivered')">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Mark as Delivered
                          </a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item text-danger" href="#" @click.prevent="showCancelModal(order)">
                            <i class="fas fa-times-circle me-2"></i>
                            Cancel Order
                          </a>
                        </template>
                        <template v-else>
                          <a class="dropdown-item disabled" href="#">
                            <i class="fas fa-lock me-2"></i>
                            Status cannot be changed
                          </a>
                        </template>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <button 
                        class="btn btn-outline-info btn-sm" 
                        @click="viewOrderDetails(order)"
                        title="View Details"
                      >
                        <i class="fas fa-eye"></i>
                      </button>
                      <button 
                        v-if="order.payment_image"
                        class="btn btn-outline-success btn-sm"
                        @click="viewPaymentProof(order)"
                        title="View Payment Proof"
                      >
                        <i class="fas fa-receipt"></i>
                      </button>
                      <button 
                        v-if="order.cancellation_reason"
                        class="btn btn-outline-warning btn-sm"
                        @click="showCancellationReason(order)"
                        title="View Cancellation Reason"
                      >
                        <i class="fas fa-comment-alt"></i>
                      </button>
                      <button 
                        v-if="canCancel(order)"
                        class="btn btn-outline-danger btn-sm"
                        @click="showCancelModal(order)"
                        title="Cancel Order"
                      >
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="filteredOrders.length > 0" class="card-footer bg-white py-3">
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
              Showing {{ filteredOrders.length }} of {{ orders.length }} orders
            </div>
            <div class="text-muted">
              Total Amount: <span class="fw-bold text-dark">₹{{ totalAmount }}</span>
            </div>
            <nav>
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <!-- Order Details Modal -->
    <div v-if="showOrderDetailsModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Order Details - #{{ selectedOrder?.order_number }}</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeOrderDetailsModal"></button>
          </div>
          <div class="modal-body" v-if="selectedOrder">
            <div class="row">
              <!-- Product Information -->
              <div class="col-md-12 mb-4">
                <h6 class="text-muted mb-3">PRODUCT INFORMATION</h6>
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-3">
                        <img 
                          :src="getProductImage(selectedOrder)" 
                          :alt="selectedOrder.product_name" 
                          class="img-fluid rounded"
                          @error="handleImageError"
                        >
                      </div>
                      <div class="col-md-9">
                        <h5 class="fw-bold">{{ selectedOrder.product_name || 'Product Name' }}</h5>
                        <div class="row mt-3">
                          <div class="col-md-6">
                            <table class="table table-sm">
                              <tr>
                                <td class="text-muted">Product ID:</td>
                                <td class="fw-bold">{{ selectedOrder.product_id || 'N/A' }}</td>
                              </tr>
                              <tr>
                                <td class="text-muted">Quantity:</td>
                                <td class="fw-bold">{{ selectedOrder.quantity || 1 }}</td>
                              </tr>
                              <tr>
                                <td class="text-muted">Unit Price:</td>
                                <td class="fw-bold">₹{{ (selectedOrder.amount / (selectedOrder.quantity || 1)).toFixed(2) }}</td>
                              </tr>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <h6 class="text-muted mb-3">ORDER INFORMATION</h6>
                <table class="table table-sm">
                  <tr>
                    <td class="text-muted">Order Number:</td>
                    <td class="fw-bold">#{{ selectedOrder.order_number }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Order Date:</td>
                    <td>{{ formatFullDate(selectedOrder.created_at) }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Status:</td>
                    <td>
                      <span :class="['badge', getStatusBadgeClass(selectedOrder.status)]">
                        {{ formatStatus(selectedOrder.status) }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-muted">Amount:</td>
                    <td class="fw-bold fs-5 text-dark">₹{{ selectedOrder.amount }}</td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <h6 class="text-muted mb-3">CUSTOMER INFORMATION</h6>
                <table class="table table-sm">
                  <tr>
                    <td class="text-muted">Name:</td>
                    <td>{{ selectedOrder.name }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Shipping Address:</td>
                    <td class="small">{{ selectedOrder.shipment_address }}</td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div v-if="selectedOrder.cancellation_reason" class="alert alert-warning mt-3">
              <h6><i class="fas fa-exclamation-triangle me-2"></i>Cancellation Reason</h6>
              <p class="mb-0">{{ selectedOrder.cancellation_reason }}</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeOrderDetailsModal">Close</button>
            <button 
              v-if="canCancel(selectedOrder)"
              type="button" 
              class="btn btn-danger"
              @click="showCancelModal(selectedOrder)"
            >
              <i class="fas fa-times me-2"></i>Cancel Order
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Proof Modal -->
    <div v-if="showPaymentProofModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">Payment Proof - #{{ selectedOrder?.order_number }}</h5>
            <button type="button" class="btn-close btn-close-white" @click="closePaymentProofModal"></button>
          </div>
          <div class="modal-body text-center" v-if="selectedOrder && selectedOrder.payment_image">
            <img 
              :src="getPaymentImage(selectedOrder)" 
              alt="Payment Proof"
              class="img-fluid rounded shadow"
              style="max-height: 500px;"
              @error="handlePaymentImageError"
            >
            <div class="mt-3 text-muted">
              <small>Order #{{ selectedOrder.order_number }} | ₹{{ selectedOrder.amount }}</small>
            </div>
          </div>
          <div class="modal-footer">
            <a 
              v-if="selectedOrder && selectedOrder.payment_image"
              :href="getPaymentImage(selectedOrder)" 
              target="_blank"
              class="btn btn-outline-primary"
            >
              <i class="fas fa-external-link-alt me-2"></i>Open Full Size
            </a>
            <button type="button" class="btn btn-secondary" @click="closePaymentProofModal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancellation Reason Modal -->
    <div v-if="showCancellationReasonModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title">Cancellation Reason - #{{ selectedOrder?.order_number }}</h5>
            <button type="button" class="btn-close" @click="closeCancellationReasonModal"></button>
          </div>
          <div class="modal-body" v-if="selectedOrder && selectedOrder.cancellation_reason">
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle me-2"></i>
              This order was cancelled for the following reason:
            </div>
            <div class="p-3 bg-light rounded">
              <p class="mb-0">{{ selectedOrder.cancellation_reason }}</p>
            </div>
            <div class="mt-3 text-muted">
              <small>Cancelled on: {{ formatFullDate(selectedOrder.updated_at) }}</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeCancellationReasonModal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Order Modal -->
    <div v-if="showCancelOrderModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Cancel Order - #{{ orderToCancel?.order_number }}</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeCancelOrderModal"></button>
          </div>
          <form @submit.prevent="confirmCancel">
            <div class="modal-body">
              <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Are you sure you want to cancel this order?
              </div>
              
              <div v-if="orderToCancel" class="mb-4">
                <!-- Product Info in Cancel Modal -->
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <img 
                        :src="getProductImage(orderToCancel)" 
                        :alt="orderToCancel.product_name" 
                        class="rounded me-3"
                        style="width: 60px; height: 60px; object-fit: cover;"
                        @error="handleImageError"
                      >
                      <div>
                        <h6 class="mb-1 fw-bold">{{ orderToCancel.product_name || 'Product' }}</h6>
                        <p class="mb-0 text-muted">Qty: {{ orderToCancel.quantity || 1 }}</p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <table class="table table-sm">
                  <tr>
                    <td class="text-muted">Order:</td>
                    <td class="fw-bold">#{{ orderToCancel.order_number }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Amount:</td>
                    <td class="fw-bold">₹{{ orderToCancel.amount }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Current Status:</td>
                    <td>
                      <span :class="['badge', getStatusBadgeClass(orderToCancel.status)]">
                        {{ formatStatus(orderToCancel.status) }}
                      </span>
                    </td>
                  </tr>
                </table>
              </div>
              
              <div class="mb-3">
                <label class="form-label fw-bold">Reason for cancellation *</label>
                <textarea 
                  v-model="cancelReason"
                  class="form-control" 
                  rows="4" 
                  placeholder="Please provide a reason for cancelling this order..."
                  required
                  minlength="10"
                  maxlength="500"
                ></textarea>
                <div class="form-text">
                  Please provide at least 10 characters ({{ cancelReason.length }}/500)
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeCancelOrderModal">
                <i class="fas fa-times me-2"></i>Keep Order
              </button>
              <button type="submit" class="btn btn-danger" :disabled="cancelling || cancelReason.length < 10">
                <span v-if="cancelling">
                  <span class="spinner-border spinner-border-sm me-2"></span>
                  Cancelling...
                </span>
                <span v-else>
                  <i class="fas fa-times me-2"></i>Confirm Cancellation
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
})

// State
const filterStatus = ref('')
const selectedOrder = ref(null)
const orderToCancel = ref(null)
const cancelReason = ref('')
const cancelling = ref(false)
const activeDropdown = ref(null)
const showFilterDropdown = ref(false)

// Modal states
const showOrderDetailsModal = ref(false)
const showPaymentProofModal = ref(false)
const showCancellationReasonModal = ref(false)
const showCancelOrderModal = ref(false)

// Computed properties
const completedOrders = computed(() => {
  return props.orders.filter(order => order.status === 'delivered').length
})

const pendingOrders = computed(() => {
  return props.orders.filter(order => order.status === 'pending').length
})

const processingOrders = computed(() => {
  return props.orders.filter(order => order.status === 'processing').length
})

const shippedOrders = computed(() => {
  return props.orders.filter(order => order.status === 'shipped').length
})

const cancelledOrders = computed(() => {
  return props.orders.filter(order => order.status === 'cancelled').length
})

const totalAmount = computed(() => {
  return props.orders.reduce((sum, order) => sum + parseFloat(order.amount), 0).toFixed(2)
})

const filteredOrders = computed(() => {
  if (!filterStatus.value) return props.orders
  return props.orders.filter(order => order.status === filterStatus.value)
})

// Methods
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatFullDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatStatus = (status) => {
  const statusMap = {
    'pending': 'Pending',
    'processing': 'Processing',
    'shipped': 'Shipped',
    'delivered': 'Delivered',
    'cancelled': 'Cancelled'
  }
  return statusMap[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    'pending': 'btn-warning',
    'processing': 'btn-info',
    'shipped': 'btn-purple',
    'delivered': 'btn-success',
    'cancelled': 'btn-danger'
  }
  return classes[status] || 'btn-secondary'
}

const getStatusBadgeClass = (status) => {
  const classes = {
    'pending': 'bg-warning',
    'processing': 'bg-info',
    'shipped': 'bg-purple',
    'delivered': 'bg-success',
    'cancelled': 'bg-danger'
  }
  return classes[status] || 'bg-secondary'
}

const getStatusIcon = (status) => {
  const icons = {
    'pending': 'fas fa-clock',
    'processing': 'fas fa-cog',
    'shipped': 'fas fa-truck',
    'delivered': 'fas fa-check-circle',
    'cancelled': 'fas fa-times-circle'
  }
  return icons[status] || 'fas fa-question-circle'
}

const truncateAddress = (address, length) => {
  if (!address) return ''
  if (address.length <= length) return address
  return address.substring(0, length) + '...'
}

const getProductImage = (order) => {
  if (!order) return 'https://placehold.co/50x50/e0f2f1/065f46?text=Product'
  
  // Try product_image field first
  if (order.product_image) {
    if (order.product_image.startsWith('http')) {
      return order.product_image
    }
    return `/storage/${order.product_image}`
  }
  
  // Fallback to product main image URL if available
  if (order.product?.main_image_url) {
    return order.product.main_image_url
  }
  
  // Final fallback
  return 'https://placehold.co/50x50/e0f2f1/065f46?text=' + (order.product_name?.charAt(0) || 'P')
}

const getPaymentImage = (order) => {
  if (!order || !order.payment_image) {
    return 'https://placehold.co/400x300/e0f2f1/065f46?text=Payment+Screenshot'
  }
  
  if (order.payment_image.startsWith('http')) {
    return order.payment_image
  }
  
  return `/storage/${order.payment_image}`
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/50x50/e0f2f1/065f46?text=Product'
}

const handlePaymentImageError = (event) => {
  event.target.src = 'https://placehold.co/400x300/e0f2f1/065f46?text=Payment+Screenshot'
}

const canCancel = (order) => {
  if (!order) return false
  return ['pending', 'processing'].includes(order.status)
}

const canChangeStatus = (order) => {
  if (!order) return false
  return order.status !== 'cancelled' && order.status !== 'delivered'
}

// Dropdown handling
const toggleStatusDropdown = (order) => {
  if (activeDropdown.value === order.id) {
    activeDropdown.value = null
  } else {
    activeDropdown.value = order.id
  }
  showFilterDropdown.value = false
}

const toggleFilterDropdown = () => {
  showFilterDropdown.value = !showFilterDropdown.value
  activeDropdown.value = null
}

// Close dropdown when clicking outside
const closeDropdowns = (event) => {
  if (!event.target.closest('.dropdown')) {
    activeDropdown.value = null
    showFilterDropdown.value = false
  }
}

// Order actions
const viewOrderDetails = (order) => {
  selectedOrder.value = order
  showOrderDetailsModal.value = true
  activeDropdown.value = null
  showFilterDropdown.value = false
}

const closeOrderDetailsModal = () => {
  showOrderDetailsModal.value = false
  selectedOrder.value = null
}

const viewPaymentProof = (order) => {
  selectedOrder.value = order
  showPaymentProofModal.value = true
  activeDropdown.value = null
  showFilterDropdown.value = false
}

const closePaymentProofModal = () => {
  showPaymentProofModal.value = false
  selectedOrder.value = null
}

const showCancellationReason = (order) => {
  selectedOrder.value = order
  showCancellationReasonModal.value = true
  activeDropdown.value = null
  showFilterDropdown.value = false
}

const closeCancellationReasonModal = () => {
  showCancellationReasonModal.value = false
  selectedOrder.value = null
}

const showCancelModal = (order) => {
  orderToCancel.value = order
  cancelReason.value = ''
  showCancelOrderModal.value = true
  activeDropdown.value = null
  showFilterDropdown.value = false
  // Close other modals if open
  showOrderDetailsModal.value = false
  showPaymentProofModal.value = false
  showCancellationReasonModal.value = false
}

const closeCancelOrderModal = () => {
  showCancelOrderModal.value = false
  orderToCancel.value = null
  cancelReason.value = ''
}

// FIXED Cancel function
const confirmCancel = async () => {
  if (!cancelReason.value.trim() || cancelReason.value.length < 10) {
    alert('Please provide a reason for cancellation (at least 10 characters).')
    return
  }

  if (!orderToCancel.value) return

  cancelling.value = true

  try {
    await router.post(`/orders/${orderToCancel.value.id}/cancel`, {
      cancellation_reason: cancelReason.value,
      product_id: orderToCancel.value.product_id,
      quantity: orderToCancel.value.quantity || 1
    }, {
      preserveScroll: true,
      onSuccess: () => {
        cancelling.value = false
        closeCancelOrderModal()
        // Refresh orders to show updated status
        router.reload({ preserveScroll: true })
      },
      onError: (errors) => {
        cancelling.value = false
        console.error('Cancel error:', errors)
        alert('Error cancelling order: ' + (errors.message || 'Please try again.'))
      }
    })
  } catch (error) {
    cancelling.value = false
    console.error('Network error:', error)
    alert('Network error. Please check your connection and try again.')
  }
}

const updateStatus = async (order, newStatus) => {
  // Don't allow cancellation through updateStatus - use showCancelModal instead
  if (newStatus === 'cancelled') {
    showCancelModal(order)
    return
  }
  
  if (confirm(`Change order #${order.order_number} status to ${formatStatus(newStatus)}?`)) {
    try {
      await router.put(`/orders/${order.id}/status`, {
        status: newStatus
      }, {
        preserveScroll: true,
        onSuccess: () => {
          activeDropdown.value = null
        },
        onError: (errors) => {
          console.error('Status update error:', errors)
          let errorMessage = 'Error updating status: Please try again.'
          
          if (errors && errors.message) {
            errorMessage = 'Error: ' + errors.message
          } else if (errors && errors.error) {
            errorMessage = 'Error: ' + errors.error
          }
          
          alert(errorMessage)
        }
      })
    } catch (error) {
      console.error('Status update caught error:', error)
      alert('Network error. Please check your connection and try again.')
    }
  }
}

const refreshOrders = () => {
  router.reload({ preserveScroll: true })
}

// Event listeners
onMounted(() => {
  document.addEventListener('click', closeDropdowns)
  console.log('Orders component mounted')
  console.log('Orders data:', props.orders)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdowns)
})
</script>

<style scoped>
.bg-light {
  background-color: #f8f9fa !important;
}

.table th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

.table tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.02);
}

.card {
  border-radius: 15px;
  overflow: hidden;
}

.card-header {
  border-bottom: 2px solid #f1f3f4;
}

.badge {
  padding: 0.5em 0.8em;
}

.btn-group .btn {
  border-radius: 6px !important;
  margin-right: 5px;
}

.dropdown-toggle::after {
  vertical-align: 0.15em;
}

.modal-content {
  border-radius: 15px;
  overflow: hidden;
}

.alert {
  border-radius: 10px;
}

.text-dark-50 {
  color: rgba(0, 0, 0, 0.5);
}

.text-white-50 {
  color: rgba(255, 255, 255, 0.5);
}

/* Status badges */
.badge.bg-warning { background-color: #ffc107 !important; }
.badge.bg-info { background-color: #0dcaf0 !important; }
.badge.bg-primary { background-color: #0d6efd !important; }
.badge.bg-purple { background-color: #6f42c1 !important; }
.badge.bg-success { background-color: #198754 !important; }
.badge.bg-danger { background-color: #dc3545 !important; }

/* Background colors for cards */
.bg-purple {
  background-color: #6f42c1 !important;
}

/* Button hover effects */
.btn-outline-info:hover {
  background-color: #0dcaf0;
  color: white;
}

.btn-outline-success:hover {
  background-color: #198754;
  color: white;
}

.btn-outline-warning:hover {
  background-color: #ffc107;
  color: black;
}

.btn-outline-danger:hover {
  background-color: #dc3545;
  color: white;
}

/* Status button colors */
.btn-purple {
  background-color: #6f42c1;
  color: white;
  border-color: #6f42c1;
}

.btn-purple:hover {
  background-color: #5a32a3;
  border-color: #5a32a3;
  color: white;
}

/* Cursor pointer for clickable elements */
.dropdown-item,
.btn-group .btn,
.dropdown-toggle {
  cursor: pointer;
}

/* Dropdown menu styling */
.dropdown-menu {
  z-index: 1050 !important;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Modal backdrop */
.modal {
  z-index: 1060 !important;
}

/* Fix for modal display */
.modal.show {
  display: block;
}

/* Responsive table */
@media (max-width: 768px) {
  .table-responsive {
    font-size: 0.9rem;
  }
  
  .btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
  
  .row.mb-5 {
    margin-bottom: 2rem !important;
  }
  
  .col-md-2 {
    margin-bottom: 1rem;
  }
}
</style>