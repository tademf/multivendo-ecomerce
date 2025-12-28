<template>
  <AppLayout>
    <div class="container-fluid py-4">
      <!-- Flash Messages -->
      <div class="row mb-3">
        <div class="col-12">
          <div v-if="$page.props.flash.success" class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <div class="d-flex align-items-center">
              <i class="fas fa-check-circle me-3"></i>
              <div class="flex-grow-1">
                <p class="mb-0">{{ $page.props.flash.success }}</p>
              </div>
              <button type="button" class="btn-close" @click="$page.props.flash.success = null"></button>
            </div>
          </div>

          <div v-if="$page.props.flash.error" class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <div class="d-flex align-items-center">
              <i class="fas fa-exclamation-triangle me-3"></i>
              <div class="flex-grow-1">
                <p class="mb-0">{{ $page.props.flash.error }}</p>
              </div>
              <button type="button" class="btn-close" @click="$page.props.flash.error = null"></button>
            </div>
          </div>
        </div>
      </div>

      <!-- Page Header -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h1 class="h3 fw-bold mb-1">My Orders</h1>
              <p class="text-muted mb-0">Track and manage all your orders</p>
            </div>
            <div class="d-flex gap-2">
              <a href="/" class="btn btn-outline-primary">
                <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="border-0">Order Details</th>
                  <th class="border-0">Product</th>
                  <th class="border-0 text-center">Quantity</th>
                  <th class="border-0">Price</th>
                  <th class="border-0 text-center">Status</th>
                  <th class="border-0 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="orders.length === 0">
                  <td colspan="6" class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-2x text-muted mb-3"></i>
                    <h6 class="fw-bold mb-2">No orders yet</h6>
                    <p class="text-muted mb-3">You haven't placed any orders yet</p>
                    <a href="/" class="btn btn-primary">
                      <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                    </a>
                  </td>
                </tr>
                
                <tr v-for="order in orders" :key="order.id" class="order-row">
                  <td>
                    <div class="d-flex flex-column">
                      <strong class="text-primary">#{{ order.order_number || order.id }}</strong>
                      <small class="text-muted">{{ formatDate(order.created_at) }}</small>
                      <div v-if="order.vendor_name" class="mt-1">
                        <small class="text-muted">
                          <i class="fas fa-store me-1"></i>{{ order.vendor_name }}
                        </small>
                      </div>
                    </div>
                  </td>
                  
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="product-thumb me-3">
                        <img :src="getProductImage(order.product_image)" 
                             :alt="order.product_name"
                             class="rounded"
                             @error="handleImageError">
                      </div>
                      <div>
                        <div class="fw-medium">{{ order.product_name || 'Product' }}</div>
                        <!-- Discount Badge -->
                        <div v-if="order.is_discounted && order.discount_name" class="mt-1">
                          <span class="badge bg-success">
                            <i class="fas fa-tag me-1"></i>{{ order.discount_name }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </td>
                  
                  <td class="text-center">
                    <span class="fw-medium">{{ order.quantity || 1 }}</span>
                  </td>
                  
                  <td>
                    <div class="d-flex flex-column">
                      <span class="fw-bold text-primary">{{ formatPrice(order.amount) }} Birr</span>
                      
                      <!-- Original Price for Discounted Orders -->
                      <div v-if="order.is_discounted && order.original_price" class="mt-1">
                        <small class="text-muted">
                          <s>{{ formatPrice(order.original_price) }} Birr</s>
                          <span class="text-success ms-2">
                            <i class="fas fa-piggy-bank me-1"></i>
                            Save {{ formatPrice(order.original_price - order.amount) }} Birr
                          </span>
                        </small>
                      </div>
                    </div>
                  </td>
                  
                  <td class="text-center">
                    <span :class="statusBadgeClass(order.status)" class="badge">
                      <i :class="statusIcon(order.status) + ' me-1'"></i>
                      {{ formatStatus(order.status) }}
                    </span>
                    
                    <!-- Cancellation Reason -->
                    <div v-if="order.status === 'cancelled' && order.cancellation_reason" class="mt-1">
                      <small class="text-muted d-block" style="max-width: 200px;">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ order.cancellation_reason }}
                      </small>
                    </div>
                  </td>
                  
                  <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                      <!-- View Order Details Button -->
                      <button @click="viewOrderDetails(order)" 
                              class="btn btn-sm btn-outline-primary"
                              title="View Order Details">
                        <i class="fas fa-eye"></i>
                      </button>
                      
                      <!-- Chat with Vendor Button - ALWAYS SHOW -->
                      <button @click="openChatWithVendor(order)"
                              class="btn btn-sm btn-outline-info"
                              :class="{'btn-outline-warning': order.status === 'cancelled'}"
                              :title="order.status === 'cancelled' ? 'Chat about cancelled order' : 'Chat with Vendor'">
                        <i class="fas fa-comments"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Table Footer -->
        <div v-if="orders.length > 0" class="card-footer bg-white border-0">
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
              Showing {{ orders.length }} orders
            </div>
            <div class="text-end">
              <div class="fw-bold text-primary">
                Total Spent: {{ formatPrice(totalSpent) }} Birr
              </div>
              <div v-if="totalSavings > 0" class="text-success small">
                <i class="fas fa-piggy-bank me-1"></i>
                Total Savings: {{ formatPrice(totalSavings) }} Birr
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Details Modal -->
      <div v-if="selectedOrder" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
              <div>
                <h5 class="modal-title fw-bold">Order #{{ selectedOrder.order_number || selectedOrder.id }}</h5>
                <p class="text-muted mb-0 small">{{ formatDate(selectedOrder.created_at) }}</p>
              </div>
              <button type="button" class="btn-close" @click="closeOrderDetails"></button>
            </div>
            
            <div class="modal-body">
              <div class="row">
                <!-- Order Status -->
                <div class="col-12 mb-4">
                  <div class="card border">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="fw-bold mb-1">Order Status</h6>
                          <p class="text-muted small mb-0">Track your order progress</p>
                        </div>
                        <span :class="statusBadgeClass(selectedOrder.status)" class="badge">
                          {{ formatStatus(selectedOrder.status) }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Product Information -->
                <div class="col-12 mb-4">
                  <div class="card border">
                    <div class="card-body">
                      <h6 class="fw-bold mb-3">
                        <i class="fas fa-box me-2"></i>Product Information
                      </h6>
                      <div class="row align-items-center">
                        <div class="col-md-3 mb-3">
                          <div class="product-image-lg">
                            <img :src="getProductImage(selectedOrder.product_image)" 
                                 :alt="selectedOrder.product_name"
                                 class="img-fluid rounded"
                                 @error="handleImageError">
                          </div>
                        </div>
                        <div class="col-md-9">
                          <h5 class="fw-bold mb-2">{{ selectedOrder.product_name }}</h5>
                          
                          <!-- Discount Info -->
                          <div v-if="selectedOrder.is_discounted" class="mb-3">
                            <div class="alert alert-success">
                              <div class="d-flex align-items-center">
                                <i class="fas fa-tag me-3 fa-lg"></i>
                                <div>
                                  <strong>Discount Applied: {{ selectedOrder.discount_name || 'Special Discount' }}</strong>
                                  <div class="mt-1">
                                    <span class="text-muted">Original Price: </span>
                                    <s class="text-muted">{{ formatPrice(selectedOrder.original_price) }} Birr</s>
                                    <span class="text-success ms-3">
                                      <i class="fas fa-piggy-bank me-1"></i>
                                      You saved {{ formatPrice(selectedOrder.original_price - selectedOrder.amount) }} Birr
                                    </span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          
                          <div class="row">
                            <div class="col-md-6">
                              <div class="mb-3">
                                <label class="text-muted small">Quantity</label>
                                <div class="fw-bold">{{ selectedOrder.quantity || 1 }}</div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="mb-3">
                                <label class="text-muted small">Unit Price</label>
                                <div class="fw-bold">{{ formatPrice(selectedOrder.amount / (selectedOrder.quantity || 1)) }} Birr</div>
                              </div>
                            </div>
                            <div class="col-12">
                              <div class="bg-light p-3 rounded">
                                <label class="text-muted small">Total Amount Paid</label>
                                <div class="fw-bold text-primary fs-4">{{ formatPrice(selectedOrder.amount) }} Birr</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Shipping Information -->
                <div class="col-12 mb-4">
                  <div class="card border">
                    <div class="card-body">
                      <h6 class="fw-bold mb-3">
                        <i class="fas fa-truck me-2"></i>Shipping Information
                      </h6>
                      <div class="mb-3">
                        <label class="text-muted small">Shipping Address</label>
                        <div class="p-3 bg-light rounded">
                          <i class="fas fa-map-marker-alt me-2"></i>
                          {{ selectedOrder.shipment_address || 'No address provided' }}
                        </div>
                      </div>
                      
                      <div v-if="selectedOrder.tracking_number" class="mt-3">
                        <label class="text-muted small">Tracking Number</label>
                        <div class="input-group">
                          <input type="text" class="form-control" :value="selectedOrder.tracking_number" readonly>
                          <button @click="copyTrackingNumber" class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-copy"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Payment Information -->
                <div v-if="selectedOrder.payment_image" class="col-12">
                  <div class="card border">
                    <div class="card-body">
                      <h6 class="fw-bold mb-3">
                        <i class="fas fa-receipt me-2"></i>Payment Information
                      </h6>
                      <div class="text-center">
                        <div class="mb-3">
                          <img :src="getPaymentImage(selectedOrder.payment_image)" 
                               alt="Payment Proof"
                               class="img-fluid rounded border"
                               style="max-height: 300px;">
                        </div>
                        <button @click="downloadPaymentProof(selectedOrder)" 
                                class="btn btn-outline-success">
                          <i class="fas fa-download me-2"></i>Download Payment Proof
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="modal-footer border-top">
              <button type="button" class="btn btn-secondary" @click="closeOrderDetails">Close</button>
              <button @click="openChatFromModal(selectedOrder)"
                      class="btn btn-info"
                      :class="{'btn-warning': selectedOrder.status === 'cancelled'}">
                <i class="fas fa-comments me-2"></i>
                {{ selectedOrder.status === 'cancelled' ? 'Chat about Cancelled Order' : 'Chat with Vendor' }}
              </button>
              <button v-if="['pending', 'processing'].includes(selectedOrder.status)"
                      @click="openCancelModal(selectedOrder)"
                      class="btn btn-danger">
                <i class="fas fa-times me-2"></i>Cancel Order
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Cancel Order Modal -->
      <div v-if="showCancelModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
              <h5 class="modal-title fw-bold">Cancel Order</h5>
              <button type="button" class="btn-close" @click="closeCancelModal"></button>
            </div>
            
            <div class="modal-body">
              <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Are you sure you want to cancel this order?
              </div>
              
              <div class="mb-3">
                <label class="form-label fw-bold">Reason for Cancellation</label>
                <textarea v-model="cancellationReason" 
                          class="form-control"
                          rows="3"
                          placeholder="Please provide a reason for cancellation..."
                          required></textarea>
              </div>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeCancelModal">Keep Order</button>
              <button type="button" class="btn btn-danger" 
                      @click="confirmCancelOrder"
                      :disabled="!cancellationReason.trim()">
                <i class="fas fa-times me-2"></i>Cancel Order
              </button>
            </div>
          </div>
        </div>
      </div>
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

const selectedOrder = ref(null)
const showCancelModal = ref(false)
const cancellationReason = ref('')
const orderToCancel = ref(null)

// Computed Properties
const totalSpent = computed(() => {
  return props.orders
    .filter(order => order.status !== 'cancelled')
    .reduce((sum, order) => sum + parseFloat(order.amount || 0), 0)
})

const totalSavings = computed(() => {
  return props.orders
    .filter(order => order.status !== 'cancelled' && order.is_discounted && order.original_price)
    .reduce((sum, order) => {
      const savings = parseFloat(order.original_price || 0) - parseFloat(order.amount || 0)
      return sum + (savings > 0 ? savings : 0)
    }, 0)
})

// Helper Methods
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
    'shipped': 'Shipped',
    'delivered': 'Delivered',
    'cancelled': 'Cancelled'
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

// Order Actions
const viewOrderDetails = (order) => {
  selectedOrder.value = order
}

const closeOrderDetails = () => {
  selectedOrder.value = null
}

// Chat Functions
const openChatWithVendor = (order) => {
  // Navigate to the chat page for this specific shipment/order
  router.get(route('messages.show', { shipment: order.id }), {
    // Pass order information for context
    order_id: order.id,
    vendor_id: order.vendor_id,
    product_name: order.product_name
  })
}

const openChatFromModal = (order) => {
  closeOrderDetails()
  openChatWithVendor(order)
}

const openCancelModal = (order) => {
  orderToCancel.value = order
  showCancelModal.value = true
}

const closeCancelModal = () => {
  showCancelModal.value = false
  cancellationReason.value = ''
  orderToCancel.value = null
}

const confirmCancelOrder = async () => {
  if (!orderToCancel.value || !cancellationReason.value.trim()) return

  try {
    await router.post(`/orders/${orderToCancel.value.id}/cancel`, {
      cancellation_reason: cancellationReason.value
    }, {
      preserveScroll: true,
      onSuccess: () => {
        closeCancelModal()
        setTimeout(() => {
          router.reload({ preserveScroll: true })
        }, 500)
      }
    })
  } catch (error) {
    alert('Error cancelling order.')
  }
}

const copyTrackingNumber = () => {
  if (selectedOrder.value?.tracking_number) {
    navigator.clipboard.writeText(selectedOrder.value.tracking_number)
    alert('Tracking number copied to clipboard!')
  }
}

const downloadPaymentProof = (order) => {
  if (order.payment_image) {
    const link = document.createElement('a')
    link.href = getPaymentImage(order.payment_image)
    link.download = `payment-proof-${order.order_number || order.id}.jpg`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }
}
</script>

<style scoped>
.container-fluid {
  background-color: #f8f9fa;
}

.order-row {
  transition: background-color 0.2s;
}

.order-row:hover {
  background-color: #f8f9fa;
}

.product-thumb {
  width: 60px;
  height: 60px;
  border-radius: 6px;
  overflow: hidden;
}

.product-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-image-lg {
  width: 100%;
  max-height: 200px;
  overflow: hidden;
  border-radius: 8px;
}

.product-image-lg img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.badge {
  padding: 8px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
}

.table thead th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

.modal-content {
  border-radius: 12px;
}

.modal-header {
  padding: 1.5rem;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  padding: 1rem 1.5rem;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.d-flex.gap-1 > * {
  margin-right: 0.25rem;
}

@media (max-width: 768px) {
  .table-responsive {
    font-size: 0.9rem;
  }
  
  .product-thumb {
    width: 50px;
    height: 50px;
  }
  
  .badge {
    padding: 6px 10px;
    font-size: 0.8rem;
  }
  
  .btn-sm {
    padding: 0.2rem 0.4rem;
    font-size: 0.8rem;
  }
}
</style>