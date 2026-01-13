<template>
  <AppLayout :initial-wishlist-count="0" :initial-cart-count="0" :user="user">
    <div class="min-vh-100 py-4 py-md-5" :class="isDarkMode ? 'dark-theme-bg' : 'bg-light'">
      <!-- Container -->
      <div class="container">

        <div class="row justify-content-center">
          <div class="col-lg-10 col-xl-8">

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border" :class="isDarkMode ? 'text-light' : 'text-primary'" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-3" :class="isDarkMode ? 'text-light' : ''">Loading product information...</p>
            </div>

            <!-- Product Summary Card -->
            <div v-else-if="productData.product_id" class="card mb-4" :class="isDarkMode ? 'dark-card' : 'border-0 shadow-sm'">
              <div class="card-header" :class="isDarkMode ? 'dark-card-header' : 'bg-white border-0 pb-0'">
                <h5 class="card-title mb-0" :class="isDarkMode ? 'text-white' : ''">
                  <i class="fas fa-shopping-bag me-2" :class="isDarkMode ? 'text-light' : 'text-primary'"></i>
                  Order Summary
                </h5>
              </div>
              <div class="card-body" :class="isDarkMode ? 'dark-card-body' : ''">
                <div class="row g-3 align-items-center">
                  <div class="col-md-3">
                    <div class="product-image-container">
                      <img 
                        :src="getProductImage(productData.product_image)" 
                        :alt="productData.product_name"
                        class="img-fluid rounded-3 border"
                        :class="isDarkMode ? 'border-secondary' : ''"
                        @error="handleImageError"
                      />
                    </div>
                  </div>
                  <div class="col-md-9">
                    <div class="d-flex flex-column h-100">
                      <div class="mb-3">
                        <h4 class="fw-bold mb-2" :class="isDarkMode ? 'text-white' : ''">{{ productData.product_name }}</h4>
                        <div class="d-flex align-items-center mb-2">
                          <span class="badge bg-primary me-2">In Stock</span>
                          <span :class="isDarkMode ? 'text-light' : 'text-muted'">
                            <i class="fas fa-box me-1"></i>
                            {{ productData.stock || '∞' }} available
                          </span>
                        </div>
                        
                        <!-- DISCOUNT BADGE IF APPLICABLE -->
                        <div v-if="productData.is_discounted" class="mb-3">
                          <span class="badge bg-danger fs-6">
                            <i class="fas fa-fire me-1"></i>{{ productData.discount_amount || calculateDiscountPercent() }}% OFF
                          </span>
                          <span v-if="productData.discount_name" class="badge ms-2" :class="isDarkMode ? 'bg-dark text-light border-light' : 'bg-danger-subtle text-danger border border-danger'">
                            <i class="fas fa-tag me-1"></i>{{ productData.discount_name }}
                          </span>
                        </div>
                        
                        <div class="price-display mb-3">
                          <!-- Show original price with strikethrough if discounted -->
                          <div v-if="productData.is_discounted" class="mb-1">
                            <span :class="isDarkMode ? 'text-light text-decoration-line-through' : 'text-muted text-decoration-line-through'">
                              Original: {{ formatPrice(productData.original_price) }} Birr
                            </span>
                          </div>
                          
                          <!-- Show current price -->
                          <div class="d-flex align-items-center">
                            <span class="price-main" :class="isDarkMode ? 'text-success' : 'text-success'">
                              {{ formatPrice(getCurrentPrice()) }} Birr
                            </span>
                            <span class="price-unit ms-2" :class="isDarkMode ? 'text-light' : 'text-muted'">per unit</span>
                          </div>
                          
                          <!-- Show savings if discounted -->
                          <div v-if="productData.is_discounted" class="mt-1">
                            <span class="badge bg-success">
                              <i class="fas fa-piggy-bank me-1"></i>
                              Save {{ formatPrice(calculateSavings()) }} Birr
                            </span>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row align-items-center mt-auto">
                        <div class="col-md-6">
                          <div class="quantity-selector">
                            <label class="form-label mb-2" :class="isDarkMode ? 'text-light' : ''">Quantity:</label>
                            <div class="input-group input-group-lg w-auto">
                              <button 
                                class="btn" 
                                :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-secondary'"
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
                                :class="isDarkMode ? 'dark-input' : ''"
                                style="width: 70px;"
                                @change="validateQuantity"
                              >
                              <button 
                                class="btn" 
                                :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-secondary'"
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
                            <h5 :class="isDarkMode ? 'text-light' : 'text-muted mb-1'">Order Total</h5>
                            <h2 class="fw-bold" :class="isDarkMode ? 'text-success' : 'text-success'">{{ formatPrice(calculatedAmount) }} Birr</h2>
                            <small :class="isDarkMode ? 'text-light' : 'text-muted'">
                              {{ productData.quantity }} item(s) × {{ formatPrice(getCurrentPrice()) }} Birr
                            </small>
                            <!-- Show total savings if discounted -->
                            <div v-if="productData.is_discounted" class="mt-2">
                              <small class="text-success">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                Total Savings: {{ formatPrice(calculateTotalSavings()) }} Birr
                              </small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment Form Card -->
            <div v-if="productData.product_id" class="card" :class="isDarkMode ? 'dark-card' : 'border-0 shadow-sm'">
              <div class="card-header" :class="isDarkMode ? 'dark-card-header' : 'bg-white border-0'">
                <div class="d-flex align-items-center">
                  <i class="fas fa-user-circle fa-2x me-3" :class="isDarkMode ? 'text-light' : 'text-primary'"></i>
                  <div>
                    <h5 class="card-title mb-0" :class="isDarkMode ? 'text-white' : ''">Payment & Shipping Details</h5>
                    <p class="card-text small mb-0" :class="isDarkMode ? 'text-light' : 'text-muted'">Fill in your information to complete the order</p>
                  </div>
                </div>
              </div>
              
              <div class="card-body" :class="isDarkMode ? 'dark-card-body' : ''">
                <form @submit.prevent="submitPayment" class="needs-validation" id="paymentForm" novalidate>
                  <!-- Personal Information -->
                  <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3" :class="isDarkMode ? 'border-light text-white' : ''">
                      <i class="fas fa-user me-2"></i>
                      Personal Information
                    </h6>
                    <div class="row g-3">
                      <div class="col-md-12">
                        <label for="name" class="form-label required" :class="isDarkMode ? 'text-light' : ''">Full Name</label>
                        <div class="input-group">
                          <span class="input-group-text" :class="isDarkMode ? 'dark-input-group' : ''">
                            <i class="fas fa-user"></i>
                          </span>
                          <input 
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="form-control form-control-lg"
                            :class="isDarkMode ? 'dark-input' : ''"
                            placeholder="Enter your full name"
                            required
                          />
                          <div class="invalid-feedback">
                            Please enter your name
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Vendor Account Number Section -->
                  <div class="mb-4" v-if="vendorAccountNumber">
                    <h6 class="border-bottom pb-2 mb-3" :class="isDarkMode ? 'border-light text-white' : ''">
                      <i class="fas fa-university me-2 text-success"></i>
                      Payment Instructions
                    </h6>
                    <div class="alert" :class="isDarkMode ? 'alert-dark text-light border-light' : 'alert-success'">
                      <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle me-3 mt-1" :class="isDarkMode ? 'text-light' : ''"></i>
                        <div>
                          <h6 class="alert-heading fw-bold mb-2" :class="isDarkMode ? 'text-light' : ''">Send Payment to Vendor's Account</h6>
                          <p class="mb-2" :class="isDarkMode ? 'text-light' : ''">
                            Please transfer <strong :class="isDarkMode ? 'text-success' : 'text-success'">{{ formatPrice(calculatedAmount) }} Birr</strong> 
                            to the following account number:
                          </p>
                          <div class="card mt-3" :class="isDarkMode ? 'dark-card' : 'bg-white'">
                            <div class="card-body" :class="isDarkMode ? 'dark-card-body' : ''">
                              <div class="d-flex align-items-center">
                                <div class="me-3">
                                  <i class="fas fa-bank fa-2x" :class="isDarkMode ? 'text-light' : 'text-primary'"></i>
                                </div>
                                <div class="flex-grow-1">
                                  <h5 class="fw-bold mb-1" :class="isDarkMode ? 'text-light' : 'text-primary'">Vendor Account Number</h5>
                                  <div class="d-flex align-items-center">
                                    <code class="h4 mb-0 me-3" :class="isDarkMode ? 'text-light' : ''">{{ vendorAccountNumber }}(CBE)</code>
                                    <button 
                                      type="button" 
                                      class="btn btn-sm"
                                      :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-primary'"
                                      @click="copyToClipboard(vendorAccountNumber)"
                                    >
                                      <i class="fas fa-copy me-1"></i> Copy
                                    </button>
                                  </div>
                                  <small :class="isDarkMode ? 'text-light' : 'text-muted'">
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
                    <h6 class="border-bottom pb-2 mb-3" :class="isDarkMode ? 'border-light text-white' : ''">
                      <i class="fas fa-money-bill-wave me-2"></i>
                      Payment Amount
                    </h6>
                    <div class="card" :class="isDarkMode ? 'dark-card' : 'bg-light border-0'">
                      <div class="card-body" :class="isDarkMode ? 'dark-card-body' : ''">
                        <div class="row align-items-center">
                          <div class="col-md-6">
                            <div class="d-flex align-items-center">
                              <div class="amount-icon me-3">
                                <i class="fas fa-money-bill fa-2x text-success"></i>
                              </div>
                              <div>
                                <h6 :class="isDarkMode ? 'text-light' : 'text-muted mb-1'">Total Amount</h6>
                                <h3 class="fw-bold text-success mb-0">{{ formatPrice(calculatedAmount) }} Birr</h3>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <div class="amount-breakdown">
                              <div class="d-flex justify-content-between mb-1">
                                <span :class="isDarkMode ? 'text-light' : 'text-muted'">Price per unit:</span>
                                <span :class="isDarkMode ? 'text-light' : ''">{{ formatPrice(getCurrentPrice()) }} Birr</span>
                              </div>
                              <div class="d-flex justify-content-between mb-1">
                                <span :class="isDarkMode ? 'text-light' : 'text-muted'">Quantity:</span>
                                <span :class="isDarkMode ? 'text-light' : ''">{{ productData.quantity }}</span>
                              </div>
                              <!-- Show discount if applicable -->
                              <div v-if="productData.is_discounted" class="d-flex justify-content-between mb-1">
                                <span :class="isDarkMode ? 'text-light' : 'text-muted'">Discount:</span>
                                <span class="text-success">-{{ formatPrice(calculateTotalSavings()) }} Birr</span>
                              </div>
                              <hr :class="isDarkMode ? 'border-light my-2' : 'my-2'">
                              <div class="d-flex justify-content-between">
                                <span class="fw-bold" :class="isDarkMode ? 'text-light' : ''">Total:</span>
                                <span class="fw-bold" :class="isDarkMode ? 'text-light' : ''">{{ formatPrice(calculatedAmount) }} Birr</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Shipping Address -->
                  <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3" :class="isDarkMode ? 'border-light text-white' : ''">
                      <i class="fas fa-truck me-2"></i>
                      Shipping Address
                    </h6>
                    <div class="mb-3">
                      <label for="shipment_address" class="form-label required" :class="isDarkMode ? 'text-light' : ''">Complete Address</label>
                      <div class="input-group">
                        <span class="input-group-text" :class="isDarkMode ? 'dark-input-group' : ''">
                          <i class="fas fa-home"></i>
                        </span>
                        <textarea 
                          id="shipment_address"
                          v-model="form.shipment_address"
                          rows="4"
                          class="form-control form-control-lg"
                          :class="isDarkMode ? 'dark-input' : ''"
                          placeholder="Enter your complete shipping address including postal code, city, and state"
                          required
                        ></textarea>
                        <div class="invalid-feedback">
                          Please provide your shipping address
                        </div>
                      </div>
                      <div class="form-text" :class="isDarkMode ? 'text-light' : ''">
                        <i class="fas fa-info-circle me-1"></i>
                        Please provide accurate address for successful delivery
                      </div>
                    </div>
                  </div>

                  <!-- Payment Screenshot Upload -->
                  <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3" :class="isDarkMode ? 'border-light text-white' : ''">
                      <i class="fas fa-camera me-2"></i>
                      Payment Proof
                    </h6>
                    
                    <div class="upload-area mb-3">
                      <div 
                        class="upload-zone text-center py-5"
                        :class="[
                          isDarkMode ? 'bg-dark-secondary border-light' : '',
                          uploadedFile ? 'border-primary bg-primary bg-opacity-10' : 'border-dashed'
                        ]"
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
                            <i class="fas fa-cloud-upload-alt fa-3x" :class="isDarkMode ? 'text-light' : 'text-muted'"></i>
                          </div>
                          <h5 class="mb-2" :class="isDarkMode ? 'text-white' : ''">Upload Payment Screenshot</h5>
                          <p class="mb-3" :class="isDarkMode ? 'text-light' : 'text-muted'">
                            Drag & drop or click to upload image<br>
                            JPG, PNG (Max 5MB)
                          </p>
                          <button type="button" class="btn" :class="isDarkMode ? 'btn-outline-light' : 'btn-primary'">
                            <i class="fas fa-upload me-2"></i>
                            Choose File
                          </button>
                        </div>
                        
                        <div v-else class="py-3">
                          <div class="d-flex align-items-center justify-content-center mb-3">
                            <i class="fas fa-file-image fa-3x text-success me-3"></i>
                            <div class="text-start">
                              <h6 class="mb-1" :class="isDarkMode ? 'text-white' : ''">{{ uploadedFile.name }}</h6>
                              <p class="small mb-0" :class="isDarkMode ? 'text-light' : 'text-muted'">
                                {{ formatFileSize(uploadedFile.size) }} • {{ uploadedFile.type }}
                              </p>
                            </div>
                          </div>
                          
                          <div class="mb-3">
                            <img 
                              :src="uploadedPreview" 
                              alt="Preview" 
                              class="img-thumbnail"
                              :class="isDarkMode ? 'border-light' : ''"
                              style="max-height: 150px;"
                            />
                          </div>
                          
                          <div class="d-flex justify-content-center gap-2">
                            <button 
                              type="button" 
                              @click.stop="removeFile"
                              class="btn btn-sm"
                              :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-danger'"
                            >
                              <i class="fas fa-trash me-1"></i> Remove
                            </button>
                            <button 
                              type="button" 
                              @click.stop="() => fileInput?.click()"
                              class="btn btn-sm"
                              :class="isDarkMode ? 'btn-outline-light' : 'btn-outline-primary'"
                            >
                              <i class="fas fa-sync me-1"></i> Change
                            </button>
                          </div>
                        </div>
                      </div>
                      
                      <div class="upload-tips">
                        <div class="alert" :class="isDarkMode ? 'alert-dark text-light border-light' : 'alert-info mb-0'">
                          <div class="d-flex">
                            <i class="fas fa-lightbulb me-2 mt-1"></i>
                            <div>
                              <strong :class="isDarkMode ? 'text-light' : ''">Important:</strong> 
                              <ul class="mb-0 ps-3">
                                <li :class="isDarkMode ? 'text-light' : ''">Capture clear screenshot showing <strong>{{ formatPrice(calculatedAmount) }} Birr</strong> payment</li>
                                <li :class="isDarkMode ? 'text-light' : ''">Ensure transaction ID and vendor account number are visible</li>
                                <li :class="isDarkMode ? 'text-light' : ''">Payment must be sent to: <strong>{{ vendorAccountNumber || 'Vendor Account' }}</strong></li>
                                <li :class="isDarkMode ? 'text-light' : ''">Accepted formats: JPG, PNG, GIF • Maximum file size: 5MB</li>
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
                  <!-- Discount Hidden Fields -->
                  <input v-if="productData.is_discounted" type="hidden" :value="productData.is_discounted" name="is_discounted" />
                  <input v-if="productData.is_discounted && productData.discount_id" type="hidden" :value="productData.discount_id" name="discount_id" />
                  <input v-if="productData.is_discounted" type="hidden" :value="productData.discounted_price" name="discounted_price" />
                  <input v-if="productData.is_discounted" type="hidden" :value="productData.original_price" name="original_price" />
                  <input v-if="productData.is_discounted && productData.discount_name" type="hidden" :value="productData.discount_name" name="discount_name" />

                  <!-- Terms and Submit -->
                  <div class="mb-4">
                    <div class="form-check mb-3">
                      <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="terms" 
                        required
                        :class="isDarkMode ? 'bg-dark border-light' : ''"
                      >
                      <label class="form-check-label" for="terms" :class="isDarkMode ? 'text-light' : ''">
                        I confirm that I have transferred {{ formatPrice(calculatedAmount) }} Birr to vendor account 
                        <strong>{{ vendorAccountNumber || 'XXXXXX' }}</strong> and agree to the 
                        <a href="#" :class="isDarkMode ? 'text-warning' : 'text-primary'">Terms & Conditions</a>
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
                          {{ productData.is_discounted ? 'Submit Discounted Payment & Place Order' : 'Submit Payment & Place Order' }}
                        </template>
                      </button>
                    </div>
                    
                    <div class="text-center mt-3">
                      <p class="small mb-0" :class="isDarkMode ? 'text-light' : 'text-muted'">
                        <i class="fas fa-shield-alt me-1"></i>
                        Your payment information is secured with 256-bit SSL encryption
                      </p>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <!-- Missing Product Alert -->
            <div v-if="!loading && !productData.product_id" class="card mt-4" :class="isDarkMode ? 'border-warning dark-card' : 'border-warning'">
              <div class="card-body text-center py-5" :class="isDarkMode ? 'dark-card-body' : ''">
                <div class="mb-4">
                  <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                </div>
                <h4 class="mb-3" :class="isDarkMode ? 'text-white' : ''">No Product Selected</h4>
                <p class="mb-4" :class="isDarkMode ? 'text-light' : 'text-muted'">
                  Please select a product first to proceed with payment
                </p>
                <Link href="/" class="btn btn-primary">
                  <i class="fas fa-shopping-cart me-2"></i>
                  Browse Products
                </Link>
              </div>
            </div>

            <!-- Notification Component -->
            <div v-if="notification.show" 
                 class="notification-toast position-fixed top-0 end-0 p-3"
                 style="z-index: 9999">
              <div class="toast show" 
                   :class="`border-0 text-bg-${notification.type}`" 
                   role="alert" 
                   aria-live="assertive" 
                   aria-atomic="true">
                <div class="toast-header border-0" :class="`text-bg-${notification.type}`">
                  <i :class="notification.icon" class="me-2"></i>
                  <strong class="me-auto">{{ notification.type.toUpperCase() }}</strong>
                  <button type="button" 
                          class="btn-close btn-close-white" 
                          @click="hideNotification" 
                          aria-label="Close"></button>
                </div>
                <div class="toast-body">
                  {{ notification.message }}
                </div>
              </div>
            </div>

            <!-- Support Info -->
            <div class="mt-4 text-center">
              <div class="card" :class="isDarkMode ? 'bg-dark-secondary border-light' : 'border-0 bg-light'">
                <div class="card-body" :class="isDarkMode ? 'dark-card-body' : ''">
                  <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                      <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-headset fa-2x me-3" :class="isDarkMode ? 'text-light' : 'text-primary'"></i>
                        <div class="text-start">
                          <h6 class="mb-0" :class="isDarkMode ? 'text-white' : ''">24/7 Support</h6>
                          <small :class="isDarkMode ? 'text-light' : 'text-muted'">We're here to help</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                      <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-shield-alt fa-2x me-3" :class="isDarkMode ? 'text-light' : 'text-success'"></i>
                        <div class="text-start">
                          <h6 class="mb-0" :class="isDarkMode ? 'text-white' : ''">Secure Payment</h6>
                          <small :class="isDarkMode ? 'text-light' : 'text-muted'">SSL Protected</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-truck-fast fa-2x me-3" :class="isDarkMode ? 'text-light' : 'text-info'"></i>
                        <div class="text-start">
                          <h6 class="mb-0" :class="isDarkMode ? 'text-white' : ''">Fast Delivery</h6>
                          <small :class="isDarkMode ? 'text-light' : 'text-muted'">3-5 Business Days</small>
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
import { router, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()

const props = defineProps({
  productData: {
    type: Object,
    default: () => ({
      product_id: null,
      product_name: '',
      product_image: '',
      unit_price: 0,
      quantity: 1,
      stock: null,
      is_discounted: false,
      discount_id: null,
      original_price: 0,
      discount_name: null,
      total_amount: 0
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

const processing = ref(false)
const fileInput = ref(null)
const uploadedFile = ref(null)
const uploadedPreview = ref(null)
const loading = ref(false)

// Notification system
const notification = reactive({
  show: false,
  message: '',
  type: 'success',
  icon: 'fas fa-check-circle'
})

// Form data
const form = reactive({
  name: props.user.name || props.user.full_name || '',
  amount: 0,
  shipment_address: '',
  is_discounted: false,
  discount_id: null,
  original_price: 0,
  discount_name: null
})

// Product data (reactive copy)
const productData = reactive({
  product_id: props.productData.product_id,
  product_name: props.productData.product_name,
  product_image: props.productData.product_image,
  unit_price: parseFloat(props.productData.unit_price) || 0,
  quantity: parseInt(props.productData.quantity) || 1,
  stock: props.productData.stock,
  is_discounted: props.productData.is_discounted || false,
  discount_id: props.productData.discount_id || null,
  original_price: parseFloat(props.productData.original_price) || parseFloat(props.productData.unit_price) || 0,
  discount_name: props.productData.discount_name || null,
  total_amount: parseFloat(props.productData.total_amount) || 0
})

// Helper methods for discount calculations
const getCurrentPrice = () => {
  return productData.unit_price
}

const calculateDiscountPercent = () => {
  if (!productData.is_discounted || productData.original_price <= 0) return 0
  
  const original = productData.original_price
  const unitPrice = productData.unit_price
  const discount = original - unitPrice
  const percent = (discount / original) * 100
  
  return Math.round(percent)
}

const calculateSavings = () => {
  if (!productData.is_discounted || productData.original_price <= 0) return 0
  
  const original = productData.original_price
  const unitPrice = productData.unit_price
  
  return original - unitPrice
}

const calculateTotalSavings = () => {
  const savingsPerUnit = calculateSavings()
  return savingsPerUnit * productData.quantity
}

// Computed properties
const calculatedAmount = computed(() => {
  const price = getCurrentPrice()
  const total = price * productData.quantity
  return parseFloat(total.toFixed(2))
})

// Show notification
const showNotification = (message, type = 'success') => {
  const icons = {
    success: 'fas fa-check-circle',
    error: 'fas fa-times-circle',
    warning: 'fas fa-exclamation-triangle',
    info: 'fas fa-info-circle'
  }
  
  notification.show = true
  notification.message = message
  notification.type = type
  notification.icon = icons[type] || 'fas fa-info-circle'
  
  setTimeout(() => {
    hideNotification()
  }, 5000)
}

// Hide notification
const hideNotification = () => {
  notification.show = false
}

// Initialize form and theme
onMounted(() => {
  // Check theme
  checkTheme()
  
  // Listen for theme changes from navbar
  window.addEventListener('theme-changed', () => {
    setTimeout(checkTheme, 100)
  })
  
  // Set form values
  form.amount = calculatedAmount.value
  form.name = props.user.name || props.user.full_name || ''
  
  // Set discount form values if applicable
  if (productData.is_discounted) {
    form.is_discounted = true
    form.discount_id = productData.discount_id
    form.original_price = productData.original_price
    form.discount_name = productData.discount_name
    
    // Show discount notification
    const savings = calculateSavings()
    const percent = calculateDiscountPercent()
    showNotification(
      `🎉 Discount applied! You're saving ${formatPrice(savings)} Birr (${percent}%) per unit!`, 
      'success'
    )
    
    console.log('Discount initialized:', {
      is_discounted: productData.is_discounted,
      discount_id: productData.discount_id,
      original_price: productData.original_price,
      unit_price: productData.unit_price,
      discount_name: productData.discount_name,
      savings_per_unit: savings,
      discount_percent: percent
    })
  } else {
    // For non-discounted products, original_price = unit_price
    form.is_discounted = false
    form.original_price = productData.unit_price
  }
  
  // Fix for Bootstrap validation
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
  
  // Cleanup
  onUnmounted(() => {
    window.removeEventListener('theme-changed', checkTheme)
  })
})

// Methods
const getProductImage = (imagePath) => {
  if (!imagePath) return 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image'
  
  if (imagePath.startsWith('http')) return imagePath
  
  if (imagePath.startsWith('storage/')) {
    return `/${imagePath}`
  }
  
  if (imagePath.startsWith('/storage/')) {
    return imagePath
  }
  
  return `/storage/${imagePath}`
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/400x300/e0f2f1/065f46?text=Product+Image'
  event.target.onerror = null
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

  // Update form with calculated amount (TOTAL amount)
  form.amount = calculatedAmount.value

  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('amount', form.amount) // TOTAL amount
  formData.append('quantity', productData.quantity)
  formData.append('shipment_address', form.shipment_address)
  formData.append('payment_image', uploadedFile.value)
  formData.append('product_id', productData.product_id)
  formData.append('product_name', productData.product_name)
  formData.append('product_image', productData.product_image)
  
  // FIX: Send is_discounted as boolean (1/0) instead of string
  if (productData.is_discounted) {
    formData.append('is_discounted', '1') // Send as string '1' for true
    if (productData.discount_id) {
      formData.append('discount_id', productData.discount_id)
    }
    formData.append('original_price', productData.original_price) // Original UNIT price
    if (productData.discount_name) {
      formData.append('discount_name', productData.discount_name)
    }
    
    console.log('Submitting discount data:', {
      is_discounted: true,
      discount_id: productData.discount_id,
      original_price: productData.original_price,
      unit_price: productData.unit_price,
      total_amount: form.amount,
      discount_name: productData.discount_name
    })
  } else {
    // For non-discounted products
    formData.append('is_discounted', '0') // Send as string '0' for false
    formData.append('original_price', productData.unit_price) // Unit price = original price
  }

  try {
    await router.post('/payment/process', formData, {
      forceFormData: true,
      preserveScroll: true,
      preserveState: false,
      onSuccess: () => {
        showNotification('Payment submitted successfully! Redirecting to your orders...', 'success')
      },
      onError: (errors) => {
        console.error('Payment submission error:', errors)
        
        let errorMessage = 'Error submitting payment. Please try again.'
        
        if (errors.message) {
          errorMessage = errors.message
        } else if (errors.payment_image) {
          errorMessage = errors.payment_image[0]
        } else if (errors.amount) {
          errorMessage = errors.amount[0]
        } else if (errors.shipment_address) {
          errorMessage = errors.shipment_address[0]
        } else if (errors.original_price) {
          errorMessage = errors.original_price[0]
        } else if (errors.discount_id) {
          errorMessage = errors.discount_id[0]
        } else if (errors.is_discounted) {
          errorMessage = 'Discount validation error: ' + errors.is_discounted[0]
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
/* ===== DARK MODE STYLES ===== */
.dark-theme-bg {
  background-color: #0f172a !important;
  color: #f1f5f9 !important;
}

.bg-dark-secondary {
  background-color: #1e293b !important;
}

.dark-card {
  background-color: #1e293b !important;
  border-color: #334155 !important;
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

.dark-input-group {
  background-color: #334155 !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.dark-input-group-text {
  background-color: #334155 !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.border-light {
  border-color: #475569 !important;
}

.border-dashed {
  border: 2px dashed #475569 !important;
}

/* ===== ALERTS IN DARK MODE ===== */
.alert-dark {
  background-color: rgba(30, 41, 59, 0.8) !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.alert-dark .alert-heading {
  color: #f1f5f9 !important;
}

.alert-dark strong {
  color: #f1f5f9 !important;
}

.alert-dark ul li {
  color: #cbd5e1 !important;
}

/* ===== FORM ELEMENTS ===== */
.form-label.text-light,
.form-check-label.text-light {
  color: #f1f5f9 !important;
}

.form-text.text-light {
  color: #94a3b8 !important;
}

/* ===== UPLOAD ZONE ===== */
.upload-zone.bg-dark-secondary {
  background-color: #1e293b !important;
}

.upload-zone.border-primary {
  border-color: #667eea !important;
  background-color: rgba(102, 126, 234, 0.1) !important;
}

/* ===== CODE STYLING ===== */
code.text-light {
  color: #f1f5f9 !important;
  background-color: #334155 !important;
  border-color: #475569 !important;
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

.btn-outline-light:disabled {
  color: #94a3b8 !important;
  border-color: #475569 !important;
  opacity: 0.5;
}

/* ===== CUSTOM CHECKBOX ===== */
.form-check-input.bg-dark {
  background-color: #1e293b !important;
  border-color: #475569 !important;
}

.form-check-input.bg-dark:checked {
  background-color: #667eea !important;
  border-color: #667eea !important;
}

/* ===== TEXT COLORS ===== */
.text-light {
  color: #f1f5f9 !important;
}

.text-success {
  color: #10b981 !important;
}

.text-primary {
  color: #667eea !important;
}

.text-warning {
  color: #f59e0b !important;
}

.text-info {
  color: #0ea5e9 !important;
}

/* ===== BADGES ===== */
.badge.bg-primary {
  background-color: #667eea !important;
}

.badge.bg-success {
  background-color: #10b981 !important;
}

.badge.bg-danger {
  background-color: #ef4444 !important;
}

/* ===== PROGRESS BAR ===== */
.progress {
  background-color: #475569 !important;
}

/* ===== IMAGE THUMBNAIL ===== */
.img-thumbnail.border-light {
  border-color: #475569 !important;
  background-color: #1e293b !important;
}

/* ===== EXISTING STYLES ===== */
.notification-toast {
  animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.toast {
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  max-width: 350px;
}

.toast-header {
  border-radius: 8px 8px 0 0;
  padding: 0.75rem 1rem;
}

.toast-body {
  padding: 1rem;
}

/* Toast color variants */
.text-bg-success {
  background-color: #198754 !important;
  color: white !important;
}

.text-bg-error {
  background-color: #dc3545 !important;
  color: white !important;
}

.text-bg-warning {
  background-color: #ffc107 !important;
  color: #000 !important;
}

.text-bg-info {
  background-color: #0dcaf0 !important;
  color: white !important;
}

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

/* Discount badge styling */
.bg-danger-subtle {
  background-color: rgba(220, 53, 69, 0.1) !important;
}

.text-decoration-line-through {
  text-decoration-thickness: 2px;
}

/* Highlight discount section */
.card .price-display .badge.bg-success {
  font-size: 0.85rem;
  padding: 0.25rem 0.5rem;
}

/* ===== DARK MODE FIXES ===== */
.dark-theme .text-muted {
  color: #94a3b8 !important;
}

.dark-theme .form-control::placeholder {
  color: #94a3b8 !important;
}

.dark-theme .border-bottom {
  border-bottom-color: #475569 !important;
}

.dark-theme hr {
  border-color: #475569 !important;
}

.dark-theme .btn-outline-primary {
  color: #7c93ff !important;
  border-color: #7c93ff !important;
}

.dark-theme .btn-outline-primary:hover {
  background-color: #7c93ff !important;
  color: white !important;
}

.dark-theme .btn-outline-danger {
  color: #f87171 !important;
  border-color: #f87171 !important;
}

.dark-theme .btn-outline-danger:hover {
  background-color: #f87171 !important;
  color: white !important;
}

.dark-theme .invalid-feedback {
  color: #f87171 !important;
}

.dark-theme .form-control:focus {
  box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.5) !important;
}

.dark-theme .spinner-border.text-primary {
  color: #667eea !important;
}

.dark-theme .spinner-border.text-light {
  color: #f1f5f9 !important;
}

/* Fix for date inputs in dark mode */
.dark-theme input[type="date"]::-webkit-calendar-picker-indicator {
  filter: invert(1) brightness(2);
}

/* Fix for select dropdown arrow in dark mode */
.dark-theme .form-select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23f1f5f9' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
  background-color: #1e293b;
  border-color: #475569;
  color: #f1f5f9;
}

/* Dark mode link colors */
.dark-theme a.text-primary {
  color: #7c93ff !important;
}

.dark-theme a.text-primary:hover {
  color: #a3b4ff !important;
}

/* Ensure text on buttons remains readable in dark mode */
.dark-theme .btn-outline-light:hover {
  color: #0f172a !important;
}

/* Fix for file upload button text */
.dark-theme .btn-outline-light i {
  color: #f1f5f9 !important;
}

.dark-theme .btn-outline-light:hover i {
  color: #0f172a !important;
}

/* Ensure placeholders are visible in dark mode */
.dark-theme ::-webkit-input-placeholder {
  color: #94a3b8 !important;
}

.dark-theme :-moz-placeholder {
  color: #94a3b8 !important;
}

.dark-theme ::-moz-placeholder {
  color: #94a3b8 !important;
}

.dark-theme :-ms-input-placeholder {
  color: #94a3b8 !important;
}

/* Fix for validation icons in dark mode */
.dark-theme .form-control.is-invalid {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23f87171'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23f87171' stroke='none'/%3e%3c/svg%3e");
}

.dark-theme .form-control.is-valid {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2334d399' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
}

/* Ensure badge text is readable */
.dark-theme .badge.bg-dark {
  color: #f1f5f9 !important;
}

/* Fix for borders in dark mode */
.dark-theme .border {
  border-color: #475569 !important;
}

.dark-theme .border-warning {
  border-color: #f59e0b !important;
}

/* Ensure text in alerts is readable */
.dark-theme .alert .text-muted,
.dark-theme .alert .text-light {
  color: #cbd5e1 !important;
}

.dark-theme .alert strong {
  color: #f1f5f9 !important;
}

/* Fix for loading text */
.dark-theme .text-center p {
  color: #f1f5f9 !important;
}

/* Ensure success text is visible */
.dark-theme .text-success {
  color: #34d399 !important;
}

/* Fix for the notification toast in dark mode */
.dark-theme .toast {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

/* Ensure card titles are visible */
.dark-theme .card-title {
  color: #f1f5f9 !important;
}

.dark-theme .card-text {
  color: #cbd5e1 !important;
}

/* Fix for support info icons */
.dark-theme .fa-headset,
.dark-theme .fa-shield-alt,
.dark-theme .fa-truck-fast {
  color: #cbd5e1 !important;
}

/* Ensure price display is readable */
.dark-theme .price-main {
  color: #34d399 !important;
}

.dark-theme .price-unit {
  color: #94a3b8 !important;
}

/* Fix for total section */
.dark-theme .total-section h5 {
  color: #cbd5e1 !important;
}

.dark-theme .total-section h2 {
  color: #34d399 !important;
}

.dark-theme .total-section small {
  color: #94a3b8 !important;
}

/* Ensure amount breakdown is readable */
.dark-theme .amount-breakdown span:not(.text-success) {
  color: #cbd5e1 !important;
}

/* Fix for upload tips */
.dark-theme .upload-tips .alert {
  background-color: rgba(30, 41, 59, 0.8) !important;
  border-color: #475569 !important;
}

.dark-theme .upload-tips .alert strong {
  color: #f1f5f9 !important;
}

.dark-theme .upload-tips .alert ul li {
  color: #cbd5e1 !important;
}

/* Ensure button text is readable */
.dark-theme .btn-success {
  color: white !important;
}

.dark-theme .btn-success:hover {
  color: white !important;
}

/* Fix for checkbox label */
.dark-theme .form-check-label strong {
  color: #f1f5f9 !important;
}

/* Ensure file preview is visible */
.dark-theme .upload-zone h5,
.dark-theme .upload-zone h6 {
  color: #f1f5f9 !important;
}

/* Fix for the "Browse Products" button */
.dark-theme .btn-primary {
  background-color: #667eea !important;
  border-color: #667eea !important;
  color: white !important;
}

.dark-theme .btn-primary:hover {
  background-color: #7c93ff !important;
  border-color: #7c93ff !important;
  color: white !important;
}

/* Ensure all text inputs have proper contrast */
.dark-theme input[type="text"],
.dark-theme input[type="number"],
.dark-theme textarea {
  color: #f1f5f9 !important;
}

/* Fix for the copy button icon */
.dark-theme .btn-outline-light .fa-copy {
  color: #f1f5f9 !important;
}

.dark-theme .btn-outline-light:hover .fa-copy {
  color: #0f172a !important;
}
</style>