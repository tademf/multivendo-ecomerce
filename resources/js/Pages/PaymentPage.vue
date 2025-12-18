<template>
  <AppLayout :initial-wishlist-count="0" :initial-cart-count="0" :user="user">
    <div class="min-vh-100 bg-light py-4 py-md-5">
      <!-- Container -->
      <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
          <div class="mb-3">
            <i class="fas fa-credit-card fa-3x text-primary"></i>
          </div>
          <h1 class="display-6 fw-bold text-dark mb-2">Complete Your Purchase</h1>
          <p class="text-muted mb-0">Secure payment process with instant confirmation</p>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-10 col-xl-8">
            <!-- Progress Steps -->
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-body">
                <div class="steps">
                  <div class="step active">
                    <div class="step-circle">1</div>
                    <div class="step-label">Product</div>
                  </div>
                  <div class="step-divider"></div>
                  <div class="step active">
                    <div class="step-circle">2</div>
                    <div class="step-label">Payment</div>
                  </div>
                  <div class="step-divider"></div>
                  <div class="step">
                    <div class="step-circle">3</div>
                    <div class="step-label">Confirmation</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-3">Loading product information...</p>
            </div>

            <!-- Product Summary Card -->
            <div v-else-if="productData.product_id" class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white border-0 pb-0">
                <h5 class="card-title mb-0">
                  <i class="fas fa-shopping-bag me-2 text-primary"></i>
                  Order Summary
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3 align-items-center">
                  <div class="col-md-3">
                    <div class="product-image-container">
                      <img 
                        :src="getProductImage(productData.product_image)" 
                        :alt="productData.product_name"
                        class="img-fluid rounded-3 border"
                        @error="handleImageError"
                      />
                    </div>
                  </div>
                  <div class="col-md-9">
                    <div class="d-flex flex-column h-100">
                      <div class="mb-3">
                        <h4 class="fw-bold mb-2">{{ productData.product_name }}</h4>
                        <div class="d-flex align-items-center mb-2">
                          <span class="badge bg-primary me-2">In Stock</span>
                          <span class="text-muted">
                            <i class="fas fa-box me-1"></i>
                            {{ productData.stock || '∞' }} available
                          </span>
                        </div>
                        <div class="price-display mb-3">
                          <span class="price-main">{{ formatPrice(productData.price) }} Birr</span>
                          <span class="price-unit text-muted">per unit</span>
                        </div>
                      </div>
                      
                      <div class="row align-items-center mt-auto">
                        <div class="col-md-6">
                          <div class="quantity-selector">
                            <label class="form-label mb-2">Quantity:</label>
                            <div class="input-group input-group-lg w-auto">
                              <button 
                                class="btn btn-outline-secondary" 
                                @click="decreaseQuantity"
                                :disabled="productData.quantity <= 1"
                              >
                                <i class="fas fa-minus"></i>
                              </button>
                              <input 
                                type="number" 
                                v-model.number="productData.quantity"
                                min="1"
                                :max="productData.stock || 100"
                                class="form-control text-center"
                                style="width: 70px;"
                                @change="validateQuantity"
                              >
                              <button 
                                class="btn btn-outline-secondary" 
                                @click="increaseQuantity"
                                :disabled="productData.quantity >= (productData.stock || 100)"
                              >
                                <i class="fas fa-plus"></i>
                              </button>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                          <div class="total-section">
                            <h5 class="text-muted mb-1">Order Total</h5>
                            <h2 class="text-success fw-bold">{{ formatPrice(calculatedAmount) }} Birr</h2>
                            <small class="text-muted">
                              {{ productData.quantity }} item(s) × {{ formatPrice(productData.price) }} Birr
                            </small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment Form Card (Only shown when product is loaded) -->
            <div v-if="productData.product_id" class="card border-0 shadow-sm">
              <div class="card-header bg-white border-0">
                <div class="d-flex align-items-center">
                  <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
                  <div>
                    <h5 class="card-title mb-0">Payment & Shipping Details</h5>
                    <p class="card-text text-muted small mb-0">Fill in your information to complete the order</p>
                  </div>
                </div>
              </div>
              
              <div class="card-body">
                <form @submit.prevent="submitPayment" class="needs-validation" id="paymentForm" novalidate>
                  <!-- Personal Information -->
                  <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3">
                      <i class="fas fa-user me-2"></i>
                      Personal Information
                    </h6>
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label for="name" class="form-label required">Full Name</label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="fas fa-user"></i>
                          </span>
                          <input 
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="form-control form-control-lg"
                            placeholder="Enter your full name"
                            required
                          />
                          <div class="invalid-feedback">
                            Please enter your name
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="email" class="form-label">Email Address (Optional)</label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                          </span>
                          <input 
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="form-control form-control-lg"
                            placeholder="you@example.com"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Vendor Account Number Section -->
                  <div class="mb-4" v-if="vendorAccountNumber">
                    <h6 class="border-bottom pb-2 mb-3">
                      <i class="fas fa-university me-2 text-success"></i>
                      Payment Instructions
                    </h6>
                    <div class="alert alert-success">
                      <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle me-3 mt-1"></i>
                        <div>
                          <h6 class="alert-heading fw-bold mb-2">Send Payment to Vendor's Account</h6>
                          <p class="mb-2">
                            Please transfer <strong class="text-success">{{ formatPrice(calculatedAmount) }} Birr</strong> 
                            to the following account number:
                          </p>
                          <div class="card bg-white mt-3">
                            <div class="card-body">
                              <div class="d-flex align-items-center">
                                <div class="me-3">
                                  <i class="fas fa-bank fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                  <h5 class="fw-bold text-primary mb-1">Vendor Account Number</h5>
                                  <div class="d-flex align-items-center">
                                    <code class="h4 mb-0 me-3">{{ vendorAccountNumber }}</code>
                                    <button 
                                      type="button" 
                                      class="btn btn-outline-primary btn-sm"
                                      @click="copyToClipboard(vendorAccountNumber)"
                                    >
                                      <i class="fas fa-copy me-1"></i> Copy
                                    </button>
                                  </div>
                                  <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    After payment, upload the transaction screenshot below
                                  </small>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Amount Display -->
                  <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3">
                      <i class="fas fa-money-bill-wave me-2"></i>
                      Payment Amount
                    </h6>
                    <div class="card bg-light border-0">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col-md-6">
                            <div class="d-flex align-items-center">
                              <div class="amount-icon me-3">
                                <i class="fas fa-money-bill fa-2x text-success"></i>
                              </div>
                              <div>
                                <h6 class="text-muted mb-1">Total Amount</h6>
                                <h3 class="fw-bold text-success mb-0">{{ formatPrice(calculatedAmount) }} Birr</h3>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <div class="amount-breakdown">
                              <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Price:</span>
                                <span>{{ formatPrice(productData.price) }} Birr</span>
                              </div>
                              <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Quantity:</span>
                                <span>{{ productData.quantity }}</span>
                              </div>
                              <hr class="my-2">
                              <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold">{{ formatPrice(calculatedAmount) }} Birr</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Shipping Address -->
                  <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3">
                      <i class="fas fa-truck me-2"></i>
                      Shipping Address
                    </h6>
                    <div class="mb-3">
                      <label for="shipment_address" class="form-label required">Complete Address</label>
                      <div class="input-group">
                        <span class="input-group-text">
                          <i class="fas fa-home"></i>
                        </span>
                        <textarea 
                          id="shipment_address"
                          v-model="form.shipment_address"
                          rows="4"
                          class="form-control form-control-lg"
                          placeholder="Enter your complete shipping address including postal code, city, and state"
                          required
                        ></textarea>
                        <div class="invalid-feedback">
                          Please provide your shipping address
                        </div>
                      </div>
                      <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Please provide accurate address for successful delivery
                      </div>
                    </div>
                  </div>

                  <!-- Payment Screenshot Upload -->
                  <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3">
                      <i class="fas fa-camera me-2"></i>
                      Payment Proof
                    </h6>
                    
                    <div class="upload-area mb-3">
                      <div 
                        class="upload-zone text-center py-5"
                        :class="{ 'border-primary bg-primary bg-opacity-10': uploadedFile }"
                        @click="() => fileInput?.click()"
                      >
                        <input 
                          ref="fileInput"
                          type="file" 
                          accept="image/*"
                          @change="handleFileUpload"
                          class="d-none"
                          required
                          id="payment_screenshot"
                        />
                        
                        <div v-if="!uploadedFile" class="py-4">
                          <div class="mb-3">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                          </div>
                          <h5 class="mb-2">Upload Payment Screenshot</h5>
                          <p class="text-muted mb-3">
                            Drag & drop or click to upload image<br>
                            JPG, PNG (Max 5MB)
                          </p>
                          <button type="button" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>
                            Choose File
                          </button>
                        </div>
                        
                        <div v-else class="py-3">
                          <div class="d-flex align-items-center justify-content-center mb-3">
                            <i class="fas fa-file-image fa-3x text-success me-3"></i>
                            <div class="text-start">
                              <h6 class="mb-1">{{ uploadedFile.name }}</h6>
                              <p class="text-muted small mb-0">
                                {{ formatFileSize(uploadedFile.size) }} • {{ uploadedFile.type }}
                              </p>
                            </div>
                          </div>
                          
                          <div class="mb-3">
                            <img 
                              :src="uploadedPreview" 
                              alt="Preview" 
                              class="img-thumbnail"
                              style="max-height: 150px;"
                            />
                          </div>
                          
                          <div class="d-flex justify-content-center gap-2">
                            <button 
                              type="button" 
                              @click.stop="removeFile"
                              class="btn btn-outline-danger btn-sm"
                            >
                              <i class="fas fa-trash me-1"></i> Remove
                            </button>
                            <button 
                              type="button" 
                              @click.stop="() => fileInput?.click()"
                              class="btn btn-outline-primary btn-sm"
                            >
                              <i class="fas fa-sync me-1"></i> Change
                            </button>
                          </div>
                        </div>
                      </div>
                      
                      <div class="upload-tips">
                        <div class="alert alert-info mb-0">
                          <div class="d-flex">
                            <i class="fas fa-lightbulb me-2 mt-1"></i>
                            <div>
                              <strong>Important:</strong> 
                              <ul class="mb-0 ps-3">
                                <li>Capture clear screenshot showing <strong>{{ formatPrice(calculatedAmount) }} Birr</strong> payment</li>
                                <li>Ensure transaction ID and vendor account number are visible</li>
                                <li>Payment must be sent to: <strong>{{ vendorAccountNumber || 'Vendor Account' }}</strong></li>
                                <li>Accepted formats: JPG, PNG, GIF • Maximum file size: 5MB</li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Hidden Fields -->
                  <input type="hidden" :value="productData.product_id" name="product_id" />
                  <input type="hidden" :value="productData.product_name" name="product_name" />
                  <input type="hidden" :value="productData.product_image" name="product_image" />
                  <input type="hidden" :value="productData.quantity" name="quantity" />
                  <input type="hidden" :value="calculatedAmount" name="amount" />

                  <!-- Terms and Submit -->
                  <div class="mb-4">
                    <div class="form-check mb-3">
                      <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="terms" 
                        required
                      >
                      <label class="form-check-label" for="terms">
                        I confirm that I have transferred {{ formatPrice(calculatedAmount) }} Birr to vendor account 
                        <strong>{{ vendorAccountNumber || 'XXXXXX' }}</strong> and agree to the 
                        <a href="#" class="text-primary">Terms & Conditions</a>
                      </label>
                    </div>
                    
                    <div class="d-grid">
                      <button 
                        type="submit"
                        :disabled="processing || !productData.product_id"
                        class="btn btn-success btn-lg"
                        :class="{ 'disabled': !productData.product_id }"
                      >
                        <template v-if="processing">
                          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                          Processing Payment...
                        </template>
                        <template v-else>
                          <i class="fas fa-lock me-2"></i>
                          Submit Payment & Place Order
                        </template>
                      </button>
                    </div>
                    
                    <div class="text-center mt-3">
                      <p class="text-muted small mb-0">
                        <i class="fas fa-shield-alt me-1"></i>
                        Your payment information is secured with 256-bit SSL encryption
                      </p>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <!-- Missing Product Alert -->
            <div v-if="!loading && !productData.product_id" class="card border-warning mt-4">
              <div class="card-body text-center py-5">
                <div class="mb-4">
                  <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                </div>
                <h4 class="mb-3">No Product Selected</h4>
                <p class="text-muted mb-4">
                  Please select a product first to proceed with payment
                </p>
                <Link href="/" class="btn btn-primary">
                  <i class="fas fa-shopping-cart me-2"></i>
                  Browse Products
                </Link>
              </div>
            </div>

            <!-- Support Info -->
            <div class="mt-4 text-center">
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                      <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-headset fa-2x text-primary me-3"></i>
                        <div class="text-start">
                          <h6 class="mb-0">24/7 Support</h6>
                          <small class="text-muted">We're here to help</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                      <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-shield-alt fa-2x text-success me-3"></i>
                        <div class="text-start">
                          <h6 class="mb-0">Secure Payment</h6>
                          <small class="text-muted">SSL Protected</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-truck-fast fa-2x text-info me-3"></i>
                        <div class="text-start">
                          <h6 class="mb-0">Fast Delivery</h6>
                          <small class="text-muted">3-5 Business Days</small>
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
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  productData: {
    type: Object,
    default: () => ({
      product_id: null,
      product_name: '',
      product_image: '',
      price: 0,
      quantity: 1,
      stock: null
    })
  },
  user: {
    type: Object,
    required: true
  },
  vendorAccountNumber: {
    type: String,
    default: null
  }
})

const processing = ref(false)
const fileInput = ref(null)
const uploadedFile = ref(null)
const uploadedPreview = ref(null)
const loading = ref(false)

// Form data
const form = reactive({
  name: props.user.name || props.user.full_name || '',
  email: props.user.email || '',
  amount: 0,
  shipment_address: '',
})

// Product data (reactive copy)
const productData = reactive({
  product_id: props.productData.product_id,
  product_name: props.productData.product_name,
  product_image: props.productData.product_image,
  price: parseFloat(props.productData.price) || 0,
  quantity: parseInt(props.productData.quantity) || 1,
  stock: props.productData.stock
})

// Computed properties
const calculatedAmount = computed(() => {
  return (productData.price * productData.quantity).toFixed(2)
})

// Initialize form with product amount
onMounted(() => {
  form.amount = calculatedAmount.value
  form.name = props.user.name || props.user.full_name || ''
  form.email = props.user.email || ''
  
  // Fix for Bootstrap validation - wait for DOM to be ready
  nextTick(() => {
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
  })
})

// Methods
const getProductImage = (imagePath) => {
  if (!imagePath) return 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image'
  
  // Fix for image 403 errors
  if (imagePath.startsWith('http')) return imagePath
  
  // Handle different image path formats
  if (imagePath.startsWith('storage/')) {
    return `/${imagePath}`
  }
  
  if (imagePath.startsWith('/storage/')) {
    return imagePath
  }
  
  // Default - assume it's a relative path from storage
  return `/storage/${imagePath}`
}

const handleImageError = (event) => {
  console.warn('Image failed to load, using fallback:', event.target.src)
  event.target.src = 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image'
  event.target.onerror = null // Prevent infinite loop
}

const decreaseQuantity = () => {
  if (productData.quantity > 1) {
    productData.quantity--
    form.amount = calculatedAmount.value
  }
}

const increaseQuantity = () => {
  if (!productData.stock || productData.quantity < productData.stock) {
    productData.quantity++
    form.amount = calculatedAmount.value
  }
}

const validateQuantity = () => {
  if (productData.quantity < 1) {
    productData.quantity = 1
  }
  
  if (productData.stock && productData.quantity > productData.stock) {
    productData.quantity = productData.stock
    showNotification(`Maximum available stock is ${productData.stock}`, 'warning')
  }
  
  form.amount = calculatedAmount.value
}

const copyToClipboard = (text) => {
  if (!text) {
    showNotification('No account number to copy', 'error')
    return
  }
  
  navigator.clipboard.writeText(text)
    .then(() => {
      showNotification('Account number copied to clipboard!', 'success')
    })
    .catch(err => {
      console.error('Failed to copy: ', err)
      // Fallback for older browsers
      const textArea = document.createElement('textarea')
      textArea.value = text
      document.body.appendChild(textArea)
      textArea.select()
      try {
        document.execCommand('copy')
        showNotification('Account number copied!', 'success')
      } catch (e) {
        showNotification('Failed to copy account number', 'error')
      }
      document.body.removeChild(textArea)
    })
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (!file.type.startsWith('image/')) {
    showNotification('Please upload only image files (JPG, PNG, GIF).', 'error')
    return
  }

  const maxSize = 5 * 1024 * 1024
  if (file.size > maxSize) {
    showNotification('File size should be less than 5MB.', 'error')
    return
  }

  uploadedFile.value = file
  
  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    uploadedPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
  
  showNotification('File uploaded successfully!', 'success')
}

const removeFile = () => {
  uploadedFile.value = null
  uploadedPreview.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatPrice = (price) => {
  const num = parseFloat(price)
  if (isNaN(num)) return '0.00'
  return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

const showNotification = (message, type = 'success') => {
  // Check if Bootstrap is available
  if (typeof bootstrap === 'undefined') {
    console.log(`${type}: ${message}`)
    return
  }
  
  // Create Bootstrap toast
  const toastContainer = document.createElement('div')
  toastContainer.className = 'position-fixed bottom-0 end-0 p-3'
  toastContainer.style.zIndex = '1050'
  
  const toast = document.createElement('div')
  toast.className = `toast align-items-center text-bg-${type === 'error' ? 'danger' : type} border-0`
  toast.setAttribute('role', 'alert')
  toast.setAttribute('aria-live', 'assertive')
  toast.setAttribute('aria-atomic', 'true')
  
  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  `
  
  toastContainer.appendChild(toast)
  document.body.appendChild(toastContainer)
  
  try {
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast)
    bsToast.show()
    
    // Remove after animation
    toast.addEventListener('hidden.bs.toast', () => {
      if (toastContainer.parentNode) {
        document.body.removeChild(toastContainer)
      }
    })
  } catch (e) {
    console.error('Toast error:', e)
  }
}

const validateForm = () => {
  // Manual validation
  let isValid = true
  
  // Check name
  if (!form.name.trim()) {
    isValid = false
    showNotification('Please enter your name', 'error')
  }
  
  // Check shipment address
  if (!form.shipment_address.trim()) {
    isValid = false
    showNotification('Please enter your shipment address', 'error')
  }
  
  // Check file upload
  if (!uploadedFile.value) {
    isValid = false
    showNotification('Please upload payment screenshot', 'error')
  }
  
  // Check terms
  const termsCheckbox = document.getElementById('terms')
  if (!termsCheckbox || !termsCheckbox.checked) {
    isValid = false
    showNotification('Please accept the terms and conditions', 'error')
  }
  
  return isValid
}

const submitPayment = async () => {
  // Validation
  if (!productData.product_id) {
    showNotification('No product selected. Please go back and select a product.', 'error')
    return
  }

  if (!validateForm()) {
    return
  }

  processing.value = true

  // Update form with calculated amount
  form.amount = calculatedAmount.value

  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('email', form.email)
  formData.append('amount', form.amount)
  formData.append('quantity', productData.quantity)
  formData.append('shipment_address', form.shipment_address)
  formData.append('payment_image', uploadedFile.value)
  formData.append('product_id', productData.product_id)
  formData.append('product_name', productData.product_name)
  formData.append('product_image', productData.product_image)
  formData.append('vendor_account_number', props.vendorAccountNumber || '')

  try {
    // Submit payment, create order, and update stock
    await router.post('/payment/process', formData, {
      forceFormData: true,
      preserveScroll: true,
      preserveState: false, // IMPORTANT: Changed to false
      onSuccess: () => {
        // Show success message
        showNotification('Order placed successfully! Redirecting to your orders...', 'success')
        
        // Use Inertia router to redirect after delay
        setTimeout(() => {
          router.visit('/orders/my-orders')
        }, 1500)
      },
      onError: (errors) => {
        console.error('Payment submission error:', errors)
        let errorMessage = 'Error submitting payment. Please try again.'
        
        if (errors.payment_image) {
          errorMessage = errors.payment_image[0]
        } else if (errors.amount) {
          errorMessage = errors.amount[0]
        } else if (errors.shipment_address) {
          errorMessage = errors.shipment_address[0]
        } else if (errors.message) {
          errorMessage = errors.message
        }
        
        showNotification(errorMessage, 'error')
        processing.value = false
      }
    })
  } catch (error) {
    console.error('Payment submission failed:', error)
    showNotification('Something went wrong. Please try again.', 'error')
    processing.value = false
  }
}
</script>
<style scoped>
/* Progress Steps */
.steps {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
}

.step-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #e9ecef;
  color: #6c757d;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  margin-bottom: 8px;
  border: 3px solid #e9ecef;
  transition: all 0.3s;
}

.step.active .step-circle {
  background: #0d6efd;
  color: white;
  border-color: #0d6efd;
}

.step-label {
  font-size: 0.875rem;
  color: #6c757d;
  font-weight: 500;
}

.step.active .step-label {
  color: #0d6efd;
  font-weight: 600;
}

.step-divider {
  flex: 1;
  height: 2px;
  background: #e9ecef;
  margin: 0 10px;
  margin-bottom: 20px;
}

.step.active ~ .step-divider {
  background: #e9ecef;
}

/* Product Image */
.product-image-container {
  position: relative;
  overflow: hidden;
  border-radius: 10px;
}

.product-image-container img {
  transition: transform 0.3s;
  width: 100%;
  height: auto;
  object-fit: cover;
}

.product-image-container:hover img {
  transform: scale(1.05);
}

/* Price Display */
.price-display {
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.price-main {
  font-size: 1.75rem;
  font-weight: 700;
  color: #198754;
}

.price-unit {
  font-size: 0.875rem;
}

/* Quantity Selector */
.quantity-selector .input-group {
  width: auto;
}

.quantity-selector .form-control {
  max-width: 70px;
}

/* Upload Zone */
.upload-zone {
  border: 2px dashed #dee2e6;
  border-radius: 10px;
  background: #f8f9fa;
  cursor: pointer;
  transition: all 0.3s;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-zone:hover {
  border-color: #0d6efd;
  background: rgba(13, 110, 253, 0.05);
}

.upload-zone.border-primary {
  border-color: #0d6efd;
  background: rgba(13, 110, 253, 0.1);
}

/* Required field indicator */
.form-label.required::after {
  content: " *";
  color: #dc3545;
}

/* Card enhancements */
.card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Submit button */
.btn-success {
  padding: 1rem 2rem;
  font-weight: 600;
  letter-spacing: 0.5px;
  transition: all 0.3s;
}

.btn-success:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
}

/* Vendor Account Number Display */
code {
  font-size: 1.25rem;
  color: #0d6efd;
  background: #f8f9fa;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  border: 1px dashed #0d6efd;
  font-family: 'Courier New', monospace;
  word-break: break-all;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .steps {
    flex-direction: column;
    gap: 20px;
  }
  
  .step-divider {
    display: none;
  }
  
  .step {
    flex-direction: row;
    width: 100%;
    justify-content: flex-start;
    gap: 15px;
  }
  
  .step-circle {
    margin-bottom: 0;
  }
  
  .upload-zone {
    padding: 2rem 1rem !important;
  }
  
  code {
    font-size: 1rem;
    padding: 0.25rem 0.5rem;
  }
  
  .price-main {
    font-size: 1.5rem;
  }
}

/* Animation for quantity changes */
.quantity-selector .btn:active {
  transform: scale(0.95);
}

/* Custom focus styles */
.form-control:focus,
.form-select:focus {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Success state for validated fields */
.form-control.is-valid {
  border-color: #198754;
  padding-right: calc(1.5em + 0.75rem);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right calc(0.375em + 0.1875rem) center;
  background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

/* Invalid state */
.form-control.is-invalid {
  border-color: #dc3545;
  padding-right: calc(1.5em + 0.75rem);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right calc(0.375em + 0.1875rem) center;
  background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

/* Loading state for submit button */
.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none !important;
}

/* Image error fallback */
.img-fluid[src*="placehold.co"] {
  background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
  padding: 20px;
}

/* Form validation improvements */
.was-validated .form-control:invalid {
  border-color: #dc3545;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
}

.was-validated .form-control:valid {
  border-color: #198754;
}
</style>