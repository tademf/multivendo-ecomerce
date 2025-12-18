<template>
  <AppLayout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-10">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
            <i class="fas fa-store text-3xl text-green-600"></i>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-3">Vendor Orders</h1>
          <p class="text-gray-600">Manage your product orders</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600 mb-2">{{ orders.length }}</div>
              <div class="text-gray-600 text-sm">Total Orders</div>
            </div>
          </div>
          
          <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-yellow-600 mb-2">{{ pendingOrders }}</div>
              <div class="text-gray-600 text-sm">Pending</div>
            </div>
          </div>
          
          <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600 mb-2">{{ deliveredOrders }}</div>
              <div class="text-gray-600 text-sm">Delivered</div>
            </div>
          </div>
          
          <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-gray-900 mb-2">{{ formatPrice(totalRevenue) }} Birr</div>
              <div class="text-gray-600 text-sm">Total Revenue</div>
            </div>
          </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
              <div>
                <h2 class="text-lg font-semibold text-gray-900">Order Management</h2>
                <p class="text-sm text-gray-600">
                  {{ filteredOrders.length }} order(s) found
                  <span v-if="filterStatus" class="ml-2">
                    <span :class="statusBadgeClass(filterStatus)" class="px-2 py-1 rounded-full text-xs font-medium">
                      {{ formatStatus(filterStatus) }}
                    </span>
                  </span>
                </p>
              </div>
              
              <div class="flex items-center gap-3">
                <div class="relative">
                  <button @click="toggleFilterDropdown" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-filter text-gray-500"></i>
                    <span class="text-gray-700">{{ filterStatus ? formatStatus(filterStatus) : 'All Status' }}</span>
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                  </button>
                  
                  <div v-if="showFilterDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="py-1">
                      <button @click="filterStatus = ''" class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-th-large text-gray-400"></i>
                        All Orders
                      </button>
                      <div class="border-t border-gray-200 my-1"></div>
                      <button @click="filterStatus = 'pending'" class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                        Pending
                      </button>
                      <button @click="filterStatus = 'processing'" class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Processing
                      </button>
                      <button @click="filterStatus = 'shipped'" class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        Shipping
                      </button>
                      <button @click="filterStatus = 'delivered'" class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        Delivered
                      </button>
                      <button @click="filterStatus = 'cancelled'" class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Cancelled
                      </button>
                    </div>
                  </div>
                </div>
                
                <button @click="refreshOrders" :disabled="refreshing" class="p-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                  <i :class="refreshing ? 'fas fa-spinner fa-spin' : 'fas fa-sync-alt'"></i>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Empty State -->
          <div v-if="filteredOrders.length === 0" class="text-center py-16">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
              <i class="fas fa-store-slash text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">No orders found</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
              {{ filterStatus ? `You don't have any ${filterStatus} orders` : "You haven't received any orders yet" }}
            </p>
            <a href="/products" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
              <i class="fas fa-plus"></i>
              Add Products
            </a>
          </div>
          
          <!-- Orders List -->
          <div v-else class="divide-y divide-gray-200">
            <div v-for="order in sortedOrders" :key="order.id" class="p-6 hover:bg-gray-50 transition-colors">
              <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Product & Customer Info -->
                <div class="col-span-2">
                  <div class="flex items-start gap-4">
                    <div class="relative">
                      <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-2xl text-gray-400"></i>
                      </div>
                      <span class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                        {{ order.quantity || 1 }}
                      </span>
                    </div>
                    <div class="flex-1">
                      <h3 class="font-semibold text-gray-900 mb-1">{{ order.product_name || 'Product' }}</h3>
                      <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                        <span>#{{ order.order_number || order.id }}</span>
                        <span>•</span>
                        <span>{{ formatDate(order.created_at) }}</span>
                      </div>
                      <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center gap-2">
                          <i class="fas fa-user text-blue-600"></i>
                          <span class="font-medium text-gray-900">{{ order.customer_name || 'Customer' }}</span>
                        </div>
                        <div v-if="order.customer_email" class="mt-1 flex items-center gap-2">
                          <i class="fas fa-envelope text-gray-400 text-sm"></i>
                          <span class="text-sm text-gray-600">{{ order.customer_email }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Amount & Payment -->
                <div>
                  <div class="mb-3">
                    <div class="text-sm text-gray-600">Total Amount</div>
                    <div class="text-xl font-bold text-gray-900">{{ formatPrice(order.amount) }} Birr</div>
                    <div class="text-sm text-gray-500">
                      {{ order.quantity || 1 }} × {{ order.unit_price_formatted || formatPrice(order.amount / (order.quantity || 1)) + ' Birr' }}
                    </div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-600">Payment Status</div>
                    <span :class="paymentStatusBadgeClass(order)" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium">
                      <i :class="paymentStatusIcon(order)"></i>
                      {{ getPaymentStatus(order) }}
                    </span>
                  </div>
                </div>
                
                <!-- Order Status & Actions -->
                <div class="col-span-2">
                  <div class="mb-4">
                    <div class="text-sm text-gray-600 mb-2">Order Status</div>
                    <div class="flex items-center gap-3">
                      <span :class="statusBadgeClass(order.status)" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium">
                        <i :class="statusIcon(order.status)"></i>
                        {{ formatStatus(order.status) }}
                      </span>
                      
                      <!-- Status Update Buttons -->
                      <div v-if="order.status === 'pending'" class="flex gap-2">
                        <button @click="updateOrderStatus(order, 'processing')" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                          Accept
                        </button>
                        <button @click="openRejectModal(order)" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                          Reject
                        </button>
                      </div>
                      
                      <div v-if="order.status === 'processing'" class="flex gap-2">
                        <button @click="updateOrderStatus(order, 'shipped')" class="px-3 py-1.5 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700">
                          Ship
                        </button>
                      </div>
                      
                      <div v-if="order.status === 'shipped'" class="flex gap-2">
                        <button @click="updateOrderStatus(order, 'delivered')" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                          Mark Delivered
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Quick Actions -->
                  <div class="flex gap-3">
                    <button @click="viewOrderDetails(order)" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                      <i class="fas fa-eye"></i>
                      View Details
                    </button>
                    
                    <button @click="contactCustomer(order)" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                      <i class="fas fa-comment"></i>
                      Contact
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- Shipping Address -->
              <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-gray-600 text-sm"></i>
                  </div>
                  <div>
                    <h4 class="font-medium text-gray-900 mb-1">Shipping Address</h4>
                    <p class="text-gray-600">{{ order.shipment_address || 'No address provided' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Footer -->
          <div v-if="filteredOrders.length > 0" class="px-6 py-5 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
              <div class="text-sm text-gray-600">
                Showing {{ filteredOrders.length }} of {{ orders.length }} orders
              </div>
              <div class="flex items-center gap-3">
                <button @click="exportOrders" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                  <i class="fas fa-download"></i>
                  Export Orders
                </button>
                <a href="/products" class="flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                  <i class="fas fa-plus"></i>
                  Add New Product
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Reject Order Modal -->
      <div v-if="isRejectModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full">
          <div class="p-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-xl text-red-600"></i>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900">Reject Order</h3>
                <p class="text-sm text-gray-600">Order #{{ currentRejectOrder?.order_number || currentRejectOrder?.id }}</p>
              </div>
            </div>
          </div>
          
          <form @submit.prevent="processRejection" class="p-6">
            <div class="mb-6">
              <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg mb-4">
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-box text-2xl text-gray-400"></i>
                </div>
                <div>
                  <h4 class="font-medium text-gray-900">{{ currentRejectOrder?.product_name || 'Product' }}</h4>
                  <p class="text-lg font-bold text-gray-900">{{ formatPrice(currentRejectOrder?.amount) }} Birr</p>
                  <p class="text-sm text-gray-600">Customer: {{ currentRejectOrder?.customer_name || 'Customer' }}</p>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-3">
                  Reason for rejecting this order *
                </label>
                <textarea 
                  v-model="rejectionReasonText"
                  rows="4"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                  placeholder="Please tell the customer why you're rejecting this order..."
                  required
                  minlength="10"
                  maxlength="500"
                ></textarea>
                <div class="mt-2 text-sm text-gray-600">
                  <span :class="{'text-red-600': rejectionReasonText.length > 0 && rejectionReasonText.length < 10}">
                    {{ rejectionReasonText.length }}/500 characters (minimum 10)
                  </span>
                </div>
                
                <!-- Quick Reasons -->
                <div class="mt-4">
                  <p class="text-sm text-gray-600 mb-2">Quick reasons:</p>
                  <div class="flex flex-wrap gap-2">
                    <button type="button" @click="setRejectionReason('Product out of stock')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                      Out of stock
                    </button>
                    <button type="button" @click="setRejectionReason('Payment verification failed')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                      Payment failed
                    </button>
                    <button type="button" @click="setRejectionReason('Shipping not available to location')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                      Shipping unavailable
                    </button>
                    <button type="button" @click="setRejectionReason('Customer requested cancellation')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                      Customer request
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="flex gap-3">
              <button type="button" @click="closeRejectModal" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                Keep Order
              </button>
              <button type="submit" :disabled="isRejecting || rejectionReasonText.length < 10" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="isRejecting">
                  <i class="fas fa-spinner fa-spin mr-2"></i>
                  Rejecting...
                </span>
                <span v-else>
                  Reject Order
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
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

// State variables - Using unique names for vendor orders
const filterStatus = ref('')
const showFilterDropdown = ref(false)
const refreshing = ref(false)

// Reject modal state - Completely different variable names
const isRejectModalOpen = ref(false)
const currentRejectOrder = ref(null)
const rejectionReasonText = ref('')
const isRejecting = ref(false)

// Computed properties
const pendingOrders = computed(() => {
  return props.orders.filter(order => order.status === 'pending').length
})

const deliveredOrders = computed(() => {
  return props.orders.filter(order => order.status === 'delivered').length
})

const totalRevenue = computed(() => {
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
    'pending': 'bg-yellow-100 text-yellow-800',
    'processing': 'bg-blue-100 text-blue-800',
    'shipped': 'bg-purple-100 text-purple-800',
    'delivered': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
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

const getPaymentStatus = (order) => {
  if (order.payment_status === 'completed') return 'Paid'
  if (order.payment_status === 'pending') return 'Pending'
  if (order.payment_status === 'cancelled') return 'Refunded'
  if (order.status === 'cancelled') return 'Refunded'
  return 'Pending'
}

const paymentStatusBadgeClass = (order) => {
  const status = getPaymentStatus(order)
  if (status === 'Paid') return 'bg-green-100 text-green-800'
  if (status === 'Pending') return 'bg-yellow-100 text-yellow-800'
  if (status === 'Refunded') return 'bg-gray-100 text-gray-800'
  return 'bg-gray-100 text-gray-800'
}

const paymentStatusIcon = (order) => {
  const status = getPaymentStatus(order)
  if (status === 'Paid') return 'fas fa-check-circle'
  if (status === 'Pending') return 'fas fa-clock'
  if (status === 'Refunded') return 'fas fa-undo'
  return 'fas fa-question-circle'
}

// Order status update
const updateOrderStatus = async (order, status) => {
  try {
    await router.post(`/orders/${order.id}/status`, {
      status: status
    }, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        router.reload({ preserveScroll: true })
      },
      onError: (errors) => {
        alert('Error updating order: ' + (errors.message || 'Please try again.'))
      }
    })
  } catch (error) {
    console.error('Update error:', error)
    alert('Network error. Please check your connection.')
  }
}

// Dropdown handling
const toggleFilterDropdown = () => {
  showFilterDropdown.value = !showFilterDropdown.value
}

const closeDropdowns = (event) => {
  if (!event.target.closest('.relative')) {
    showFilterDropdown.value = false
  }
}

// Order actions
const viewOrderDetails = (order) => {
  router.visit(`/orders/${order.id}`)
}

const contactCustomer = (order) => {
  const email = order.customer_email || order.user?.email
  if (email) {
    window.location.href = `mailto:${email}?subject=Regarding your order #${order.order_number || order.id}`
  } else {
    alert('Customer email not available.')
  }
}

const exportOrders = () => {
  const csvContent = "data:text/csv;charset=utf-8," 
    + "Order Number,Product,Quantity,Amount,Customer,Status,Order Date\n"
    + filteredOrders.value.map(order => 
        `"${order.order_number || order.id}","${order.product_name}",${order.quantity || 1},"${formatPrice(order.amount)} Birr","${order.customer_name || 'Customer'}","${formatStatus(order.status)}","${formatDate(order.created_at)}"`
      ).join("\n")
  
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement("a")
  link.setAttribute("href", encodedUri)
  link.setAttribute("download", `vendor_orders_${new Date().toISOString().split('T')[0]}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// Reject modal functions - Different from customer cancel
const openRejectModal = (order) => {
  currentRejectOrder.value = order
  rejectionReasonText.value = ''
  isRejectModalOpen.value = true
  showFilterDropdown.value = false
}

const closeRejectModal = () => {
  isRejectModalOpen.value = false
  currentRejectOrder.value = null
  rejectionReasonText.value = ''
  isRejecting.value = false
}

const setRejectionReason = (reason) => {
  rejectionReasonText.value = reason
}

const processRejection = async () => {
  if (!rejectionReasonText.value.trim() || rejectionReasonText.value.length < 10) {
    alert('Please provide a reason for rejection (at least 10 characters).')
    return
  }

  if (!currentRejectOrder.value) return

  isRejecting.value = true

  try {
    await router.post(`/orders/${currentRejectOrder.value.id}/cancel`, {
      cancellation_reason: rejectionReasonText.value
    }, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        closeRejectModal()
        router.reload({ preserveScroll: true })
      },
      onError: (errors) => {
        isRejecting.value = false
        console.error('Reject error:', errors)
        alert('Error rejecting order: ' + (errors.message || 'Please try again.'))
      }
    })
  } catch (error) {
    isRejecting.value = false
    console.error('Network error:', error)
    alert('Network error. Please check your connection and try again.')
  }
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

// Event listeners
onMounted(() => {
  document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdowns)
})
</script>

<style scoped>
.min-h-screen {
  min-height: 100vh;
}

.bg-gray-50 {
  background-color: #f9fafb;
}

.container {
  max-width: 1280px;
  margin-left: auto;
  margin-right: auto;
}

.inline-flex {
  display: inline-flex;
}

.items-center {
  align-items: center;
}

.justify-center {
  justify-content: center;
}

.rounded-full {
  border-radius: 9999px;
}

.rounded-xl {
  border-radius: 0.75rem;
}

.rounded-lg {
  border-radius: 0.5rem;
}

.shadow-sm {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.shadow-lg {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.bg-blue-100 {
  background-color: #dbeafe;
}

.text-blue-600 {
  color: #2563eb;
}

.text-green-600 {
  color: #16a34a;
}

.text-yellow-600 {
  color: #ca8a04;
}

.text-gray-900 {
  color: #111827;
}

.text-gray-600 {
  color: #4b5563;
}

.text-gray-500 {
  color: #6b7280;
}

.text-gray-400 {
  color: #9ca3af;
}

.font-bold {
  font-weight: 700;
}

.font-semibold {
  font-weight: 600;
}

.font-medium {
  font-weight: 500;
}

.text-3xl {
  font-size: 1.875rem;
  line-height: 2.25rem;
}

.text-2xl {
  font-size: 1.5rem;
  line-height: 2rem;
}

.text-xl {
  font-size: 1.25rem;
  line-height: 1.75rem;
}

.text-lg {
  font-size: 1.125rem;
  line-height: 1.75rem;
}

.text-sm {
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.text-xs {
  font-size: 0.75rem;
  line-height: 1rem;
}

.mb-10 {
  margin-bottom: 2.5rem;
}

.mb-8 {
  margin-bottom: 2rem;
}

.mb-6 {
  margin-bottom: 1.5rem;
}

.mb-4 {
  margin-bottom: 1rem;
}

.mb-3 {
  margin-bottom: 0.75rem;
}

.mb-2 {
  margin-bottom: 0.5rem;
}

.mb-1 {
  margin-bottom: 0.25rem;
}

.mt-6 {
  margin-top: 1.5rem;
}

.mt-4 {
  margin-top: 1rem;
}

.mt-3 {
  margin-top: 0.75rem;
}

.mt-2 {
  margin-top: 0.5rem;
}

.mt-1 {
  margin-top: 0.25rem;
}

.ml-2 {
  margin-left: 0.5rem;
}

.mr-2 {
  margin-right: 0.5rem;
}

.p-6 {
  padding: 1.5rem;
}

.p-4 {
  padding: 1rem;
}

.p-3 {
  padding: 0.75rem;
}

.p-2\.5 {
  padding: 0.625rem;
}

.px-4 {
  padding-left: 1rem;
  padding-right: 1rem;
}

.py-8 {
  padding-top: 2rem;
  padding-bottom: 2rem;
}

.py-5 {
  padding-top: 1.25rem;
  padding-bottom: 1.25rem;
}

.py-3 {
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
}

.py-2\.5 {
  padding-top: 0.625rem;
  padding-bottom: 0.625rem;
}

.px-6 {
  padding-left: 1.5rem;
  padding-right: 1.5rem;
}

.py-2 {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.py-1\.5 {
  padding-top: 0.375rem;
  padding-bottom: 0.375rem;
}

.py-1 {
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
}

.px-3 {
  padding-left: 0.75rem;
  padding-right: 0.75rem;
}

.px-2 {
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

.gap-6 {
  gap: 1.5rem;
}

.gap-4 {
  gap: 1rem;
}

.gap-3 {
  gap: 0.75rem;
}

.gap-2 {
  gap: 0.5rem;
}

.gap-1 {
  gap: 0.25rem;
}

.grid {
  display: grid;
}

.grid-cols-1 {
  grid-template-columns: repeat(1, minmax(0, 1fr));
}

.md\:grid-cols-4 {
  @media (min-width: 768px) {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
}

.lg\:grid-cols-5 {
  @media (min-width: 1024px) {
    grid-template-columns: repeat(5, minmax(0, 1fr));
  }
}

.col-span-2 {
  grid-column: span 2 / span 2;
}

.flex {
  display: flex;
}

.flex-col {
  flex-direction: column;
}

.md\:flex-row {
  @media (min-width: 768px) {
    flex-direction: row;
  }
}

.flex-wrap {
  flex-wrap: wrap;
}

.items-start {
  align-items: flex-start;
}

.md\:items-center {
  @media (min-width: 768px) {
    align-items: center;
  }
}

.justify-between {
  justify-content: space-between;
}

.justify-center {
  justify-content: center;
}

.flex-1 {
  flex: 1 1 0%;
}

.relative {
  position: relative;
}

.absolute {
  position: absolute;
}

.right-0 {
  right: 0;
}

.top-0 {
  top: 0;
}

.-top-2 {
  top: -0.5rem;
}

.-right-2 {
  right: -0.5rem;
}

.mt-2 {
  margin-top: 0.5rem;
}

.z-50 {
  z-index: 50;
}

.w-20 {
  width: 5rem;
}

.h-20 {
  height: 5rem;
}

.w-24 {
  width: 6rem;
}

.h-24 {
  height: 6rem;
}

.w-48 {
  width: 12rem;
}

.w-16 {
  width: 4rem;
}

.h-16 {
  height: 4rem;
}

.w-12 {
  width: 3rem;
}

.h-12 {
  height: 3rem;
}

.w-8 {
  width: 2rem;
}

.h-8 {
  height: 2rem;
}

.w-6 {
  width: 1.5rem;
}

.h-6 {
  height: 1.5rem;
}

.w-2 {
  width: 0.5rem;
}

.h-2 {
  height: 0.5rem;
}

.bg-white {
  background-color: #ffffff;
}

.bg-green-100 {
  background-color: #dcfce7;
}

.bg-yellow-100 {
  background-color: #fef9c3;
}

.bg-red-100 {
  background-color: #fee2e2;
}

.bg-blue-50 {
  background-color: #eff6ff;
}

.bg-gray-100 {
  background-color: #f3f4f6;
}

.bg-blue-600 {
  background-color: #2563eb;
}

.bg-purple-600 {
  background-color: #9333ea;
}

.bg-green-600 {
  background-color: #16a34a;
}

.bg-red-600 {
  background-color: #dc2626;
}

.hover\:bg-blue-700:hover {
  background-color: #1d4ed8;
}

.hover\:bg-green-700:hover {
  background-color: #15803d;
}

.hover\:bg-red-700:hover {
  background-color: #b91c1c;
}

.hover\:bg-purple-700:hover {
  background-color: #7e22ce;
}

.hover\:bg-gray-50:hover {
  background-color: #f9fafb;
}

.hover\:bg-gray-200:hover {
  background-color: #e5e7eb;
}

.border {
  border-width: 1px;
}

.border-gray-200 {
  border-color: #e5e7eb;
}

.border-gray-300 {
  border-color: #d1d5db;
}

.border-b {
  border-bottom-width: 1px;
}

.border-t {
  border-top-width: 1px;
}

.text-white {
  color: #ffffff;
}

.text-center {
  text-align: center;
}

.text-left {
  text-align: left;
}

.overflow-hidden {
  overflow: hidden;
}

.divide-y > * + * {
  border-top-width: 1px;
}

.divide-gray-200 > * + * {
  border-color: #e5e7eb;
}

.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.hover\:bg-gray-50:hover {
  background-color: #f9fafb;
}

.disabled\:opacity-50:disabled {
  opacity: 0.5;
}

.disabled\:cursor-not-allowed:disabled {
  cursor: not-allowed;
}

.rounded-full {
  border-radius: 9999px;
}

.max-w-md {
  max-width: 28rem;
}

.mx-auto {
  margin-left: auto;
  margin-right: auto;
}

.fixed {
  position: fixed;
}

.inset-0 {
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}

.bg-black {
  background-color: #000000;
}

.bg-opacity-50 {
  --tw-bg-opacity: 0.5;
}

.focus\:ring-2:focus {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}

.focus\:ring-red-500:focus {
  --tw-ring-color: #ef4444;
}

.focus\:border-red-500:focus {
  border-color: #ef4444;
}

.text-red-600 {
  color: #dc2626;
}

.placeholder-gray-400::placeholder {
  color: #9ca3af;
}
</style>