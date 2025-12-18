<template>
  <AppLayout>
    <div class="container py-4">
      <!-- Header -->
      <div class="text-center mb-5">
        <div class="mb-4">
          <i class="bi bi-bag-check display-1 text-primary"></i>
        </div>
        <h1 class="display-5 fw-bold mb-3">My Orders</h1>
        <p class="lead text-muted mb-0">Track and manage all your purchases</p>
      </div>

      <!-- Stats Cards -->
      <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
              <div class="h4 fw-bold text-primary mb-1">{{ orders.length }}</div>
              <p class="text-muted mb-0 small">Total Orders</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
              <div class="h4 fw-bold text-warning mb-1">{{ pendingOrders }}</div>
              <p class="text-muted mb-0 small">Pending</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
              <div class="h4 fw-bold text-success mb-1">{{ deliveredOrders }}</div>
              <p class="text-muted mb-0 small">Delivered</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
              <div class="h4 fw-bold text-dark mb-1">{{ formatPrice(totalSpent) }} Birr</div>
              <p class="text-muted mb-0 small">Total Spent</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="card border-0 shadow-lg">
        <div class="card-header bg-white border-0 py-3">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
              <h2 class="h5 fw-bold mb-1">Order History</h2>
              <p class="text-muted mb-0 small">
                {{ filteredOrders.length }} order(s) found
                <span v-if="filterStatus" class="ms-2">
                  <span :class="`badge ${statusBadgeClass(filterStatus)} py-1 px-2`">
                    {{ formatStatus(filterStatus) }}
                  </span>
                </span>
              </p>
            </div>
            
            <div class="d-flex align-items-center gap-2">
              <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-filter me-2"></i>
                  {{ filterStatus ? formatStatus(filterStatus) : 'All Status' }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                  <li><button class="dropdown-item" @click="filterStatus = ''">
                    <i class="bi bi-grid me-2"></i>All Orders
                  </button></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><button class="dropdown-item" @click="filterStatus = 'pending'">
                    <span class="badge bg-warning me-2">●</span>Pending
                  </button></li>
                  <li><button class="dropdown-item" @click="filterStatus = 'processing'">
                    <span class="badge bg-info me-2">●</span>Processing
                  </button></li>
                  <li><button class="dropdown-item" @click="filterStatus = 'shipped'">
                    <span class="badge bg-primary me-2">●</span>Shipping
                  </button></li>
                  <li><button class="dropdown-item" @click="filterStatus = 'delivered'">
                    <span class="badge bg-success me-2">●</span>Delivered
                  </button></li>
                  <li><button class="dropdown-item" @click="filterStatus = 'cancelled'">
                    <span class="badge bg-danger me-2">●</span>Cancelled
                  </button></li>
                </ul>
              </div>
              
              <button @click="refreshOrders" :disabled="refreshing" 
                      class="btn btn-primary btn-sm" title="Refresh orders">
                <i :class="refreshing ? 'bi bi-arrow-clockwise spin' : 'bi bi-arrow-clockwise'"></i>
              </button>
            </div>
          </div>
        </div>
        
        <div class="card-body p-0">
          <!-- Empty State -->
          <div v-if="filteredOrders.length === 0" class="text-center py-5">
            <div class="py-4">
              <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
              <h3 class="h5 fw-bold mb-3">No orders found</h3>
              <p class="text-muted mb-4">
                {{ filterStatus ? `You don't have any ${filterStatus} orders` : "You haven't placed any orders yet" }}
              </p>
              <a href="/" class="btn btn-primary">
                <i class="bi bi-bag me-2"></i>Start Shopping
              </a>
            </div>
          </div>
          
          <!-- Compact Orders Table -->
          <div v-else class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th scope="col" class="ps-4">Order #</th>
                  <th scope="col">Product</th>
                  <th scope="col">Date</th>
                  <th scope="col">Amount</th>
                  <th scope="col">Status</th>
                  <th scope="col">Payment</th>
                  <th scope="col" class="text-end pe-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in sortedOrders" :key="order.id" class="align-middle">
                  <td class="ps-4 fw-medium">
                    #{{ order.order_number || order.id }}
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="bg-light rounded-2 p-2">
                        <i class="bi bi-box-seam text-muted"></i>
                      </div>
                      <div>
                        <div class="fw-medium text-truncate" style="max-width: 200px;">
                          {{ order.product_name || 'Product' }}
                        </div>
                        <div class="text-muted small">Qty: {{ order.quantity || 1 }}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="small">{{ formatDate(order.created_at) }}</div>
                  </td>
                  <td>
                    <div class="fw-bold">{{ formatPrice(order.amount) }} Birr</div>
                  </td>
                  <td>
                    <span :class="`badge ${statusBadgeClass(order.status)} py-1 px-2`">
                      <i :class="statusIcon(order.status) + ' me-1'"></i>
                      {{ formatStatus(order.status) }}
                    </span>
                  </td>
                  <td>
                    <span :class="`badge ${paymentStatusBadgeClass(order)} py-1 px-2`">
                      {{ getPaymentStatus(order) }}
                    </span>
                  </td>
                  <td class="text-end pe-4">
                    <div class="d-flex gap-2 justify-content-end">
                      <button @click="openOrderDetails(order)" 
                              class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                      </button>
                      <button v-if="canCancel(order)" @click="openCancelModal(order)"
                              class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x-circle"></i>
                      </button>
                      <button v-if="order.status === 'delivered'" @click="downloadInvoice(order)"
                              class="btn btn-sm btn-outline-success">
                        <i class="bi bi-download"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Footer -->
        <div v-if="filteredOrders.length > 0" class="card-footer bg-white border-0 py-3">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-muted small">
              Showing {{ filteredOrders.length }} of {{ orders.length }} orders
            </div>
            <div class="d-flex gap-2">
              <button @click="exportOrders" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download me-2"></i>Export
              </button>
              <a href="/" class="btn btn-primary btn-sm">
                <i class="bi bi-bag me-2"></i>Continue Shopping
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="selectedOrder" class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-0 pb-0">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                <i class="bi bi-box-seam text-primary fs-4"></i>
              </div>
              <div>
                <h5 class="modal-title fw-bold">Order Details</h5>
                <p class="text-muted mb-0 small">
                  #{{ selectedOrder.order_number || selectedOrder.id }}
                  • {{ formatFullDate(selectedOrder.created_at) }}
                </p>
              </div>
            </div>
            <button type="button" class="btn-close" @click="selectedOrder = null"></button>
          </div>
          
          <div class="modal-body">
            <div class="row g-4">
              <!-- Left Column - Order Info -->
              <div class="col-md-6">
                <!-- Product Info -->
                <div class="card border-0 bg-light">
                  <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">Product Information</h6>
                    <div class="d-flex align-items-start gap-3">
                      <div class="bg-white p-3 rounded-3">
                        <i class="bi bi-box-seam text-muted fs-4"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">{{ selectedOrder.product_name || 'Product' }}</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <span class="text-muted">Quantity: {{ selectedOrder.quantity || 1 }}</span>
                          <span class="fw-bold">{{ formatPrice(selectedOrder.amount) }} Birr</span>
                        </div>
                        <div v-if="selectedOrder.vendor_name" class="small">
                          <span class="text-muted">Vendor:</span>
                          <span class="fw-medium ms-1">{{ selectedOrder.vendor_name }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Payment & Status -->
                <div class="card border-0 bg-light mt-3">
                  <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">Order Summary</h6>
                    <div class="row">
                      <div class="col-6">
                        <div class="mb-3">
                          <div class="text-muted small">Order Status</div>
                          <span :class="`badge ${statusBadgeClass(selectedOrder.status)} py-2 px-3`">
                            <i :class="statusIcon(selectedOrder.status) + ' me-2'"></i>
                            {{ formatStatus(selectedOrder.status) }}
                          </span>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="mb-3">
                          <div class="text-muted small">Payment Status</div>
                          <span :class="`badge ${paymentStatusBadgeClass(selectedOrder)} py-2 px-3`">
                            <i :class="paymentStatusIcon(selectedOrder) + ' me-2'"></i>
                            {{ getPaymentStatus(selectedOrder) }}
                          </span>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mt-4">
                      <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Ordered</span>
                        <span>Shipped</span>
                        <span>Delivered</span>
                      </div>
                      <div class="progress" style="height: 6px;">
                        <div :class="progressBarClass(selectedOrder.status)" 
                             :style="{ width: progressWidth(selectedOrder.status) }"
                             class="progress-bar"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column - Timeline & Actions -->
              <div class="col-md-6">
                <!-- Timeline -->
                <div class="card border-0 bg-light">
                  <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">Order Timeline</h6>
                    <div class="timeline-sm">
                      <!-- Ordered -->
                      <div class="timeline-item-sm" :class="{'active': true}">
                        <div class="timeline-marker-sm">
                          <i class="bi bi-bag-check"></i>
                        </div>
                        <div class="timeline-content-sm">
                          <h6 class="fw-bold mb-1">Order Placed</h6>
                          <p class="text-muted small mb-0">{{ formatFullDate(selectedOrder.created_at) }}</p>
                        </div>
                      </div>
                      
                      <!-- Processing -->
                      <div class="timeline-item-sm" :class="{'active': ['processing', 'shipped', 'delivered'].includes(selectedOrder.status)}">
                        <div class="timeline-marker-sm">
                          <i class="bi bi-gear"></i>
                        </div>
                        <div class="timeline-content-sm">
                          <h6 class="fw-bold mb-1">Order Confirmed</h6>
                          <p class="text-muted small mb-0">Your order is being processed</p>
                        </div>
                      </div>
                      
                      <!-- Shipped -->
                      <div class="timeline-item-sm" :class="{'active': ['shipped', 'delivered'].includes(selectedOrder.status)}">
                        <div class="timeline-marker-sm">
                          <i class="bi bi-truck"></i>
                        </div>
                        <div class="timeline-content-sm">
                          <h6 class="fw-bold mb-1">Shipped</h6>
                          <p class="text-muted small mb-0">Will be shipped soon</p>
                        </div>
                      </div>
                      
                      <!-- Delivered -->
                      <div class="timeline-item-sm" :class="{'active': selectedOrder.status === 'delivered'}">
                        <div class="timeline-marker-sm">
                          <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="timeline-content-sm">
                          <h6 class="fw-bold mb-1">Delivered</h6>
                          <p class="text-muted small mb-0">Expected soon</p>
                        </div>
                      </div>
                      
                      <!-- Cancelled -->
                      <div v-if="selectedOrder.status === 'cancelled'" class="timeline-item-sm active">
                        <div class="timeline-marker-sm">
                          <i class="bi bi-x-circle"></i>
                        </div>
                        <div class="timeline-content-sm">
                          <h6 class="fw-bold mb-1">Order Cancelled</h6>
                          <p class="text-muted small mb-0">{{ formatFullDate(selectedOrder.updated_at) }}</p>
                          <p v-if="selectedOrder.cancellation_reason" class="text-danger small mt-1">
                            {{ extractCancellationReason(selectedOrder.cancellation_reason) }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 bg-light mt-3">
                  <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">Actions</h6>
                    <div class="d-grid gap-2">
                      <button v-if="canCancel(selectedOrder)" @click="openCancelModal(selectedOrder)"
                              class="btn btn-danger">
                        <i class="bi bi-x-circle me-2"></i>Cancel Order
                      </button>
                      <button v-if="selectedOrder.status === 'delivered'" @click="downloadInvoice(selectedOrder)"
                              class="btn btn-success">
                        <i class="bi bi-download me-2"></i>Download Invoice
                      </button>
                      <button @click="contactVendor(selectedOrder)"
                              class="btn btn-outline-primary">
                        <i class="bi bi-chat me-2"></i>Contact Vendor
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-backdrop fade show"></div>
    </div>

    <!-- Cancel Order Modal -->
    <div v-if="isCancelModalOpen" class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-0 pb-0">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
              </div>
              <div>
                <h5 class="modal-title fw-bold">Cancel Order</h5>
                <p class="text-muted mb-0 small">#{{ cancelOrder?.order_number || cancelOrder?.id }}</p>
              </div>
            </div>
            <button type="button" class="btn-close" @click="closeCancelModal"></button>
          </div>
          
          <form @submit.prevent="confirmCancel" class="modal-body">
            <div class="mb-4">
              <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-4">
                <div class="bg-white p-3 rounded-3">
                  <i class="bi bi-box-seam text-muted fs-4"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1">{{ cancelOrder?.product_name || 'Product' }}</h6>
                  <p class="h5 fw-bold mb-0">{{ formatPrice(cancelOrder?.amount) }} Birr</p>
                </div>
              </div>
              
              <div>
                <label class="form-label fw-bold">Why are you cancelling this order? *</label>
                <textarea 
                  v-model="cancelReason"
                  rows="3"
                  class="form-control"
                  placeholder="Please tell us why you're cancelling this order..."
                  required
                  minlength="10"
                  maxlength="500"
                ></textarea>
                <div class="form-text">
                  <span :class="{'text-danger': cancelReason.length > 0 && cancelReason.length < 10}">
                    {{ cancelReason.length }}/500 characters (minimum 10)
                  </span>
                </div>
                
                <!-- Quick Reasons -->
                <div class="mt-3">
                  <p class="small text-muted mb-2">Quick reasons:</p>
                  <div class="d-flex flex-wrap gap-2">
                    <button type="button" @click="setCancelReason('Changed my mind')" 
                            class="btn btn-sm btn-outline-secondary">Changed my mind</button>
                    <button type="button" @click="setCancelReason('Found better price')" 
                            class="btn btn-sm btn-outline-secondary">Found better price</button>
                    <button type="button" @click="setCancelReason('Ordered by mistake')" 
                            class="btn btn-sm btn-outline-secondary">Ordered by mistake</button>
                    <button type="button" @click="setCancelReason('Shipping takes too long')" 
                            class="btn btn-sm btn-outline-secondary">Shipping delay</button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="modal-footer border-0 pt-0">
              <button type="button" @click="closeCancelModal" 
                      class="btn btn-outline-secondary">Keep Order</button>
              <button type="submit" :disabled="cancelling || cancelReason.length < 10" 
                      class="btn btn-danger">
                <span v-if="cancelling">
                  <span class="spinner-border spinner-border-sm me-2"></span>
                  Cancelling...
                </span>
                <span v-else>Cancel Order</span>
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="modal-backdrop fade show"></div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
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

// State
const filterStatus = ref('')
const refreshing = ref(false)
const selectedOrder = ref(null)
const isCancelModalOpen = ref(false)
const cancelOrder = ref(null)
const cancelReason = ref('')
const cancelling = ref(false)

// Computed properties
const pendingOrders = computed(() => {
  return props.orders.filter(order => order.status === 'pending').length
})

const deliveredOrders = computed(() => {
  return props.orders.filter(order => order.status === 'delivered').length
})

const totalSpent = computed(() => {
  return props.orders
    .filter(order => order.status !== 'cancelled')
    .reduce((sum, order) => sum + parseFloat(order.amount || 0), 0)
})

const filteredOrders = computed(() => {
  if (!filterStatus.value) return props.orders
  return props.orders.filter(order => order.status === filterStatus.value)
})

const sortedOrders = computed(() => {
  return [...filteredOrders.value].sort((a, b) => 
    new Date(b.created_at) - new Date(a.created_at)
  )
})

// Helper methods
const formatPrice = (price) => {
  if (!price) return '0'
  const num = parseFloat(price)
  if (isNaN(num)) return '0'
  return num.toLocaleString('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  })
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatFullDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatStatus = (status) => {
  const statusMap = {
    'pending': 'Pending',
    'processing': 'Processing',
    'shipped': 'Shipping',
    'delivered': 'Delivered',
    'cancelled': 'Cancelled'
  }
  return statusMap[status] || status
}

const statusBadgeClass = (status) => {
  const classes = {
    'pending': 'bg-warning text-dark',
    'processing': 'bg-info text-dark',
    'shipped': 'bg-primary',
    'delivered': 'bg-success',
    'cancelled': 'bg-danger'
  }
  return classes[status] || 'bg-secondary'
}

const statusIcon = (status) => {
  const icons = {
    'pending': 'bi bi-clock',
    'processing': 'bi bi-gear',
    'shipped': 'bi bi-truck',
    'delivered': 'bi bi-check-circle',
    'cancelled': 'bi bi-x-circle'
  }
  return icons[status] || 'bi bi-question-circle'
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
  if (status === 'Paid') return 'bg-success'
  if (status === 'Pending') return 'bg-warning text-dark'
  if (status === 'Refunded') return 'bg-secondary'
  return 'bg-secondary'
}

const paymentStatusIcon = (order) => {
  const status = getPaymentStatus(order)
  if (status === 'Paid') return 'bi bi-check-circle'
  if (status === 'Pending') return 'bi bi-clock'
  if (status === 'Refunded') return 'bi bi-arrow-clockwise'
  return 'bi bi-question-circle'
}

const progressBarClass = (status) => {
  const classes = {
    'pending': 'bg-warning',
    'processing': 'bg-info',
    'shipped': 'bg-primary',
    'delivered': 'bg-success',
    'cancelled': 'bg-danger'
  }
  return classes[status] || 'bg-secondary'
}

const progressWidth = (status) => {
  const widths = {
    'pending': '25%',
    'processing': '50%',
    'shipped': '75%',
    'delivered': '100%',
    'cancelled': '100%'
  }
  return widths[status] || '25%'
}

const extractCancellationReason = (reason) => {
  if (!reason) return ''
  const parts = reason.split('| Cancelled at:')
  return parts[0].trim()
}

const canCancel = (order) => {
  if (!order) return false
  return ['pending', 'processing'].includes(order.status)
}

// Order actions
const openOrderDetails = (order) => {
  selectedOrder.value = order
}

const openCancelModal = (order) => {
  cancelOrder.value = order
  cancelReason.value = ''
  isCancelModalOpen.value = true
}

const closeCancelModal = () => {
  isCancelModalOpen.value = false
  cancelOrder.value = null
  cancelReason.value = ''
  cancelling.value = false
}

const setCancelReason = (reason) => {
  cancelReason.value = reason
}

const confirmCancel = async () => {
  if (!cancelReason.value.trim() || cancelReason.value.length < 10) {
    alert('Please provide a reason for cancellation (at least 10 characters).')
    return
  }

  if (!cancelOrder.value) return

  cancelling.value = true

  try {
    await router.post(`/orders/${cancelOrder.value.id}/cancel`, {
      cancellation_reason: cancelReason.value
    }, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        closeCancelModal()
        if (selectedOrder.value?.id === cancelOrder.value.id) {
          selectedOrder.value = null
        }
        router.reload({ preserveScroll: true })
      },
      onError: (errors) => {
        cancelling.value = false
        alert('Error cancelling order: ' + (errors.message || 'Please try again.'))
      }
    })
  } catch (error) {
    cancelling.value = false
    alert('Network error. Please check your connection and try again.')
  }
}

const downloadInvoice = (order) => {
  alert(`Invoice for order #${order.order_number || order.id} would be downloaded.`)
}

const contactVendor = (order) => {
  if (order.vendor_email) {
    window.location.href = `mailto:${order.vendor_email}?subject=Regarding my order #${order.order_number || order.id}`
  } else {
    alert('Vendor contact information not available.')
  }
}

const exportOrders = () => {
  const csvContent = "data:text/csv;charset=utf-8," 
    + "Order Number,Product,Quantity,Amount,Status,Order Date\n"
    + filteredOrders.value.map(order => 
        `"${order.order_number || order.id}","${order.product_name}",${order.quantity || 1},"${formatPrice(order.amount)} Birr","${formatStatus(order.status)}","${formatDate(order.created_at)}"`
      ).join("\n")
  
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement("a")
  link.setAttribute("href", encodedUri)
  link.setAttribute("download", `my_orders_${new Date().toISOString().split('T')[0]}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const refreshOrders = () => {
  refreshing.value = true
  router.reload({ 
    preserveScroll: true,
    preserveState: true,
    onFinish: () => {
      refreshing.value = false
    }
  })
}
</script>

<style scoped>
/* Compact table styling */
.table td, .table th {
  padding: 0.75rem;
  vertical-align: middle;
}

.table thead th {
  border-bottom: 2px solid #dee2e6;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}

/* Small timeline for modal */
.timeline-sm {
  position: relative;
  padding-left: 20px;
}

.timeline-sm::before {
  content: '';
  position: absolute;
  left: 9px;
  top: 0;
  bottom: 0;
  width: 2px;
  background-color: var(--bs-gray-300);
}

.timeline-item-sm {
  position: relative;
  margin-bottom: 16px;
}

.timeline-item-sm:last-child {
  margin-bottom: 0;
}

.timeline-marker-sm {
  position: absolute;
  left: -20px;
  width: 18px;
  height: 18px;
  background-color: white;
  border: 2px solid var(--bs-gray-300);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--bs-gray-500);
  font-size: 0.625rem;
  z-index: 1;
}

.timeline-item-sm.active .timeline-marker-sm {
  background-color: var(--bs-primary);
  border-color: var(--bs-primary);
  color: white;
}

.timeline-content-sm {
  padding-bottom: 4px;
}

/* Spin animation for refresh */
.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive table */
@media (max-width: 768px) {
  .table-responsive {
    font-size: 0.875rem;
  }
  
  .table td, .table th {
    padding: 0.5rem;
  }
  
  .display-5 {
    font-size: 1.8rem;
  }
  
  .card-header {
    padding: 0.75rem !important;
  }
}

/* Hover effects */
.table-hover tbody tr:hover {
  background-color: rgba(var(--bs-primary-rgb), 0.05);
}

/* Modal backdrop fix */
.modal-backdrop {
  opacity: 0.5;
}

/* Card enhancements */
.card {
  transition: transform 0.2s ease;
}

.card:hover {
  transform: translateY(-1px);
}

/* Button hover effects */
.btn:hover {
  transform: translateY(-1px);
}

/* Timeline inactive state */
.timeline-item-sm:not(.active) {
  opacity: 0.6;
}

/* Text truncation for product names */
.text-truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Badge sizing */
.badge {
  font-size: 0.75rem;
  font-weight: 500;
}

/* Modal body scroll */
.modal-body {
  max-height: 70vh;
  overflow-y: auto;
}

/* Form control focus */
.form-control:focus {
  box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
}

/* Progress bar rounded corners */
.progress {
  border-radius: 10px;
}

.progress-bar {
  border-radius: 10px;
}

/* Loading state */
.btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}
</style>