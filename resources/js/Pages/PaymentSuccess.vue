<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white py-12">
      <div class="container mx-auto px-4 max-w-4xl">
        <!-- Success Message -->
        <div class="text-center mb-10">
          <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check-circle text-green-500 text-5xl"></i>
          </div>
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Payment Submitted Successfully!
          </h1>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Thank you for your payment. Your order is being processed and will be verified soon.
          </p>
        </div>

        <!-- Order Details Card -->
        <div class="card-elevated max-w-3xl mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Order Information -->
            <div>
              <h2 class="title-medium mb-6 pb-4 border-b">Order Information</h2>
              <div class="space-y-4">
                <div class="info-row">
                  <span class="info-label">Order Reference:</span>
                  <span class="info-value font-mono">{{ payment.reference_number }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Transaction ID:</span>
                  <span class="info-value font-mono">{{ payment.transaction_id }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Payment Method:</span>
                  <span class="info-value capitalize">{{ payment.payment_method.replace('_', ' ') }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Payment Status:</span>
                  <span :class="statusBadgeClass" class="px-3 py-1 rounded-full text-sm font-medium">
                    {{ payment.payment_status }}
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Verification Status:</span>
                  <span :class="verificationBadgeClass" class="px-3 py-1 rounded-full text-sm font-medium">
                    {{ payment.verification_status }}
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Order Date:</span>
                  <span class="info-value">{{ formatDate(payment.created_at) }}</span>
                </div>
              </div>
            </div>

            <!-- Payment Summary -->
            <div>
              <h2 class="title-medium mb-6 pb-4 border-b">Payment Summary</h2>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-600">Subtotal:</span>
                  <span class="font-medium">₹{{ formatPrice(payment.subtotal) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Tax (18%):</span>
                  <span class="font-medium">₹{{ formatPrice(payment.tax_amount) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Shipping:</span>
                  <span class="font-medium">₹{{ formatPrice(payment.shipping_cost) }}</span>
                </div>
                <div class="flex justify-between pt-4 border-t font-bold text-lg">
                  <span>Total Paid:</span>
                  <span class="text-primary">₹{{ formatPrice(payment.total_amount) }}</span>
                </div>
              </div>
              
              <!-- Shipping Information -->
              <div class="mt-8 pt-6 border-t">
                <h3 class="font-semibold text-gray-800 mb-3">Shipping Address</h3>
                <p class="text-gray-600 whitespace-pre-line">{{ payment.shipping_address }}</p>
              </div>
            </div>
          </div>

          <!-- Payment Proof -->
          <div v-if="payment.payment_proof_image" class="mt-8 pt-8 border-t">
            <h2 class="title-medium mb-6">Payment Proof</h2>
            <div class="flex items-center space-x-4">
              <div class="w-24 h-24 border rounded-lg overflow-hidden">
                <img 
                  :src="paymentProofUrl" 
                  alt="Payment Proof"
                  class="w-full h-full object-cover"
                  @error="(e) => e.target.src = '/images/file-placeholder.png'"
                />
              </div>
              <div>
                <p class="font-medium text-gray-800">Payment Receipt Uploaded</p>
                <p class="text-sm text-gray-600">{{ payment.payment_proof_original_name }}</p>
                <a 
                  :href="route('payment.downloadProof', { id: payment.id })"
                  class="inline-flex items-center text-primary hover:text-primary-dark mt-2"
                >
                  <i class="fas fa-download mr-2"></i>
                  Download Proof
                </a>
              </div>
            </div>
          </div>

          <!-- Next Steps -->
          <div class="mt-10 p-6 bg-blue-50 rounded-xl">
            <h3 class="font-bold text-blue-800 mb-4 flex items-center">
              <i class="fas fa-info-circle mr-2"></i>
              What Happens Next?
            </h3>
            <ul class="space-y-3 text-blue-700">
              <li class="flex items-start">
                <i class="fas fa-clock mt-1 mr-3"></i>
                <span>Your payment is now <strong>pending verification</strong>. This usually takes 24-48 hours.</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-envelope mt-1 mr-3"></i>
                <span>You will receive an email notification once your payment is verified.</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-truck mt-1 mr-3"></i>
                <span>After verification, your order will be shipped within 2-3 business days.</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-headset mt-1 mr-3"></i>
                <span>For any questions, contact support with your Order Reference: <strong>{{ payment.reference_number }}</strong></span>
              </li>
            </ul>
          </div>

          <!-- Action Buttons -->
          <div class="mt-10 flex flex-col sm:flex-row gap-4">
            <Link 
              :href="route('home')"
              class="btn-secondary flex-1 text-center"
            >
              <i class="fas fa-home mr-2"></i>
              Back to Home
            </Link>
            <Link 
              :href="route('payment.history')"
              class="btn-primary flex-1 text-center"
            >
              <i class="fas fa-history mr-2"></i>
              View Order History
            </Link>
            <button 
              @click="printReceipt"
              class="btn-outline flex-1"
            >
              <i class="fas fa-print mr-2"></i>
              Print Receipt
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  payment: {
    type: Object,
    default: () => ({})
  },
  user: {
    type: Object,
    default: () => ({})
  }
})

// Format price
const formatPrice = (price) => {
  const num = parseFloat(price || 0)
  return isNaN(num) ? '0.00' : num.toFixed(2)
}

// Format date
const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-IN', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Payment proof URL
const paymentProofUrl = computed(() => {
  if (!props.payment.payment_proof_image) return ''
  return `/storage/${props.payment.payment_proof_image}`
})

// Status badge classes
const statusBadgeClass = computed(() => {
  const status = props.payment.payment_status
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    verified: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
})

// Verification badge classes
const verificationBadgeClass = computed(() => {
  const status = props.payment.verification_status
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    verified: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
})

// Print receipt
const printReceipt = () => {
  window.print()
}
</script>

<style scoped>
/* Color scheme */
.bg-primary {
  background-color: #3b82f6;
}

.text-primary {
  color: #3b82f6;
}

.text-primary-dark {
  color: #1d4ed8;
}

/* Cards */
.card-elevated {
  background: white;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 
    0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Info rows */
.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #f3f4f6;
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  color: #6b7280;
  font-weight: 500;
}

.info-value {
  color: #1f2937;
  font-weight: 600;
}

/* Buttons */
.btn-primary {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  font-weight: 600;
  padding: 14px 24px;
  border-radius: 10px;
  border: none;
  font-size: 16px;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px -3px rgba(59, 130, 246, 0.5);
}

.btn-secondary {
  background: white;
  color: #4b5563;
  font-weight: 600;
  padding: 14px 24px;
  border-radius: 10px;
  border: 2px solid #d1d5db;
  font-size: 16px;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
  transform: translateY(-2px);
}

.btn-outline {
  background: white;
  color: #3b82f6;
  font-weight: 600;
  padding: 14px 24px;
  border-radius: 10px;
  border: 2px solid #3b82f6;
  font-size: 16px;
  transition: all 0.3s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-outline:hover {
  background: #eff6ff;
  transform: translateY(-2px);
}

/* Typography */
.title-medium {
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

/* Print styles */
@media print {
  .no-print {
    display: none !important;
  }
  
  .card-elevated {
    box-shadow: none !important;
    border: 1px solid #ddd;
  }
  
  .btn-primary,
  .btn-secondary,
  .btn-outline {
    display: none !important;
  }
}
</style>