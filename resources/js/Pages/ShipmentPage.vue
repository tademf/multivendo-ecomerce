<template>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <div class="shipments-container">
    <!-- Back to Home Button -->
    <div class="back-home-container">
      <a href="/" class="back-home-btn">
        <i class="bi bi-arrow-left me-2"></i>Back to Home
      </a>
    </div>

    <div class="container-fluid py-4">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="h4 mb-1">📦 Shipments & Orders</h2>
          <p class="text-muted mb-0">View and manage all customer orders</p>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-primary" @click="refreshData">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
          </button>
          <a href="/payments" class="btn btn-sm btn-success">
            <i class="bi bi-cart-plus me-1"></i>New Order
          </a>
        </div>
      </div>

      <!-- Debug Alert -->
      <div v-if="shipments.length === 0" class="alert alert-warning mb-4">
        <h5><i class="bi bi-exclamation-triangle me-2"></i>Debug Information</h5>
        <p class="mb-1">No shipments found in database</p>
        <p class="mb-0">Total shipments: {{ shipments.length }}</p>
        <button class="btn btn-sm btn-outline-primary mt-2" @click="checkDatabase">
          Check Database
        </button>
      </div>

      <!-- Stats Cards -->
      <div v-if="shipments.length > 0" class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
          <div class="card border-0 shadow-sm stats-card">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon-shape bg-warning-light text-warning rounded-2 me-3">
                  <i class="bi bi-clock-history fs-4"></i>
                </div>
                <div>
                  <h6 class="mb-0">Pending</h6>
                  <p class="text-dark fw-bold mb-0">{{ stats.pending || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
          <div class="card border-0 shadow-sm stats-card">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon-shape bg-info-light text-info rounded-2 me-3">
                  <i class="bi bi-gear fs-4"></i>
                </div>
                <div>
                  <h6 class="mb-0">Processing</h6>
                  <p class="text-dark fw-bold mb-0">{{ stats.processing || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
          <div class="card border-0 shadow-sm stats-card">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon-shape bg-primary-light text-primary rounded-2 me-3">
                  <i class="bi bi-truck fs-4"></i>
                </div>
                <div>
                  <h6 class="mb-0">Shipped</h6>
                  <p class="text-dark fw-bold mb-0">{{ stats.shipped || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
          <div class="card border-0 shadow-sm stats-card">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon-shape bg-success-light text-success rounded-2 me-3">
                  <i class="bi bi-check-circle fs-4"></i>
                </div>
                <div>
                  <h6 class="mb-0">Delivered</h6>
                  <p class="text-dark fw-bold mb-0">{{ stats.delivered || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
          <div class="card border-0 shadow-sm stats-card">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon-shape bg-danger-light text-danger rounded-2 me-3">
                  <i class="bi bi-x-circle fs-4"></i>
                </div>
                <div>
                  <h6 class="mb-0">Cancelled</h6>
                  <p class="text-dark fw-bold mb-0">{{ stats.cancelled || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
          <div class="card border-0 shadow-sm stats-card">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon-shape bg-dark-light text-dark rounded-2 me-3">
                  <i class="bi bi-box-seam fs-4"></i>
                </div>
                <div>
                  <h6 class="mb-0">Total</h6>
                  <p class="text-dark fw-bold mb-0">{{ stats.total || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="bg-light">
                <tr>
                  <th class="border-0 ps-4">ORDER ID</th>
                  <th class="border-0">CUSTOMER</th>
                  <th class="border-0">ADDRESS</th>
                  <th class="border-0">AMOUNT</th>
                  <th class="border-0">STATUS</th>
                  <th class="border-0">DATE</th>
                  <th class="border-0 pe-4">ACTIONS</th>
                </tr>
              </thead>
              <tbody>
                <!-- Empty State -->
                <tr v-if="shipments.length === 0">
                  <td colspan="7" class="text-center py-5">
                    <div class="empty-icon mb-3">
                      <i class="bi bi-truck fs-1 text-muted"></i>
                    </div>
                    <h5 class="text-muted mb-2">No orders found</h5>
                    <p class="text-muted mb-0">
                      <a href="/payments" class="text-decoration-none">Create your first order</a>
                    </p>
                  </td>
                </tr>

                <!-- Orders List -->
                <tr v-for="shipment in shipments" :key="shipment.id" class="order-row">
                  <td class="ps-4">
                    <div class="fw-semibold">#{{ shipment.id }}</div>
                    <small class="text-muted">{{ shipment.tracking_number || 'No tracking' }}</small>
                  </td>
                  <td>
                    <div class="fw-medium">{{ shipment.customer_name || 'Customer' }}</div>
                    <small class="text-muted">{{ shipment.customer_email || '' }}</small>
                    <div v-if="shipment.customer_phone" class="text-muted small">
                      <i class="bi bi-telephone me-1"></i>{{ shipment.customer_phone }}
                    </div>
                  </td>
                  <td>
                    <div class="small" style="max-width: 200px;">
                      {{ shipment.delivery_address }}
                    </div>
                  </td>
                  <td>
                    <div class="fw-semibold">${{ formatPrice(shipment.total_amount) }}</div>
                    <small class="text-muted">Shipping: ${{ formatPrice(shipment.shipping_cost) }}</small>
                  </td>
                  <td>
                    <span :class="`badge bg-${getStatusColor(shipment.status)}`">
                      {{ formatStatus(shipment.status) }}
                    </span>
                  </td>
                  <td>
                    <div>{{ formatDate(shipment.created_at) }}</div>
                    <small class="text-muted">{{ formatTime(shipment.created_at) }}</small>
                  </td>
                  <td class="pe-4">
                    <button class="btn btn-sm btn-outline-primary me-1" @click="viewShipment(shipment)">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" @click="updateStatus(shipment)">
                      <i class="bi bi-pencil"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Debug Console -->
      <div class="mt-4">
        <button class="btn btn-sm btn-outline-secondary" @click="toggleConsole">
          {{ showConsole ? 'Hide' : 'Show' }} Console
        </button>
        
        <div v-if="showConsole" class="card mt-2">
          <div class="card-body">
            <h6 class="card-title">Debug Console</h6>
            <pre class="bg-light p-3 rounded">{{ debugInfo }}</pre>
          </div>
        </div>
      </div>
    </div>

    <!-- View Shipment Modal -->
    <div v-if="selectedShipment" class="modal show d-block" style="background: rgba(0,0,0,0.5);" @click.self="closeModal">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Order #{{ selectedShipment.id }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <h6>Customer Information</h6>
                <p><strong>Name:</strong> {{ selectedShipment.customer_name }}</p>
                <p><strong>Email:</strong> {{ selectedShipment.customer_email }}</p>
                <p><strong>Phone:</strong> {{ selectedShipment.customer_phone || 'N/A' }}</p>
              </div>
              <div class="col-md-6">
                <h6>Order Details</h6>
                <p><strong>Status:</strong> 
                  <span :class="`badge bg-${getStatusColor(selectedShipment.status)}`">
                    {{ formatStatus(selectedShipment.status) }}
                  </span>
                </p>
                <p><strong>Tracking:</strong> {{ selectedShipment.tracking_number || 'N/A' }}</p>
                <p><strong>Carrier:</strong> {{ selectedShipment.carrier || 'Standard' }}</p>
              </div>
            </div>
            
            <div class="mb-3">
              <h6>Shipping Address</h6>
              <p>{{ selectedShipment.delivery_address }}</p>
            </div>
            
            <div class="mb-3" v-if="selectedShipment.items && selectedShipment.items.length > 0">
              <h6>Order Items</h6>
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in selectedShipment.items" :key="item.id">
                    <td>{{ item.product_name }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>${{ formatPrice(item.unit_price) }}</td>
                    <td>${{ formatPrice(item.total_price) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <h6>Amount Details</h6>
                <p><strong>Subtotal:</strong> ${{ formatPrice(selectedShipment.total_amount - selectedShipment.shipping_cost) }}</p>
                <p><strong>Shipping:</strong> ${{ formatPrice(selectedShipment.shipping_cost) }}</p>
                <p><strong>Total:</strong> ${{ formatPrice(selectedShipment.total_amount) }}</p>
              </div>
              <div class="col-md-6">
                <h6>Timeline</h6>
                <p><strong>Created:</strong> {{ formatDateTime(selectedShipment.created_at) }}</p>
                <p v-if="selectedShipment.shipped_at">
                  <strong>Shipped:</strong> {{ formatDateTime(selectedShipment.shipped_at) }}
                </p>
                <p v-if="selectedShipment.delivered_at">
                  <strong>Delivered:</strong> {{ formatDateTime(selectedShipment.delivered_at) }}
                </p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
            <button type="button" class="btn btn-primary" @click="printOrder">Print</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

// Props
const props = defineProps({
  shipments: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  user: {
    type: Object,
    default: () => ({})
  }
})

// State
const selectedShipment = ref(null)
const showConsole = ref(false)

// Debug Info
const debugInfo = computed(() => {
  return {
    shipmentsCount: props.shipments.length,
    shipments: props.shipments,
    stats: props.stats,
    user: props.user
  }
})

// Log props on mount
onMounted(() => {
  console.log('ShipmentPage mounted')
  console.log('Shipments:', props.shipments)
  console.log('Stats:', props.stats)
  console.log('User:', props.user)
  
  // Check if shipments is an array
  if (!Array.isArray(props.shipments)) {
    console.error('Shipments is not an array:', typeof props.shipments)
  }
})

// Methods
const formatPrice = (price) => {
  const num = parseFloat(price || 0)
  return isNaN(num) ? '0.00' : num.toFixed(2)
}

const formatStatus = (status) => {
  const statusMap = {
    pending: 'Pending',
    processing: 'Processing',
    shipped: 'Shipped',
    delivered: 'Delivered',
    cancelled: 'Cancelled'
  }
  return statusMap[status] || status
}

const getStatusColor = (status) => {
  const colorMap = {
    pending: 'warning',
    processing: 'info',
    shipped: 'primary',
    delivered: 'success',
    cancelled: 'danger'
  }
  return colorMap[status] || 'secondary'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  return `${formatDate(dateString)} at ${formatTime(dateString)}`
}

const viewShipment = (shipment) => {
  selectedShipment.value = shipment
}

const updateStatus = (shipment) => {
  const newStatus = prompt('Enter new status (pending, processing, shipped, delivered, cancelled):', shipment.status)
  if (newStatus) {
    router.patch(`/shipments/${shipment.id}/status`, {
      status: newStatus
    })
  }
}

const refreshData = () => {
  router.reload()
}

const checkDatabase = () => {
  // Call API to check database
  fetch('/api/debug/shipments')
    .then(res => res.json())
    .then(data => {
      console.log('Database check:', data)
      alert(`Database check complete. Found ${data.count} shipments.`)
    })
    .catch(err => {
      console.error('Database check failed:', err)
      alert('Database check failed. Check console for details.')
    })
}

const toggleConsole = () => {
  showConsole.value = !showConsole.value
}

const printOrder = () => {
  window.print()
}

const closeModal = () => {
  selectedShipment.value = null
}
</script>

<style scoped>
.shipments-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding-top: 20px;
}

.back-home-container {
  position: fixed;
  top: 20px;
  left: 20px;
  z-index: 1000;
}

.back-home-btn {
  display: inline-flex;
  align-items: center;
  padding: 10px 20px;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid #dee2e6;
  border-radius: 50px;
  color: #495057;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.back-home-btn:hover {
  background: #fff;
  color: #0d6efd;
  transform: translateX(-5px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.stats-card {
  transition: all 0.3s ease;
}

.stats-card:hover {
  transform: translateY(-5px);
}

.order-row:hover {
  background-color: rgba(0, 123, 255, 0.05);
}

.icon-shape {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.bg-primary-light { background-color: rgba(13, 110, 253, 0.1); }
.bg-info-light { background-color: rgba(13, 202, 240, 0.1); }
.bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
.bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
.bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
.bg-dark-light { background-color: rgba(33, 37, 41, 0.1); }

.badge {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.35rem 0.75rem;
}

.modal {
  backdrop-filter: blur(4px);
}

pre {
  font-size: 12px;
  max-height: 300px;
  overflow: auto;
}
</style>