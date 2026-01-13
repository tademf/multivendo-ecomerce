<!-- Register.vue -->
<template>
  <AppLayout>
    <!-- Simple Background -->
    <div class="minimal-bg"></div>

    <!-- Main Container -->
    <div class="login-container min-vh-100 d-flex align-items-center justify-content-center" :class="{'dark-theme': isDarkMode}">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">
            
            <!-- Register Card -->
            <div class="login-card">
              <!-- Card Header -->
              <div class="card-header text-center p-4">
                <div class="login-header">
                  <h2 class="fw-bold mb-2">Create Account</h2>
                  <p class="text-muted small">Join us and start shopping</p>
                </div>
              </div>

              <!-- Card Body -->
              <div class="card-body p-4">
                <!-- Register Form -->
                <form @submit.prevent="register">
                  <!-- Name Fields in Row -->
                  <div class="row g-3 mb-3">
                    <!-- First Name -->
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label fw-medium small">
                          <i class="fas fa-user me-1"></i>
                          First Name
                        </label>
                        <input
                          type="text"
                          v-model="form.first_name"
                          class="form-control"
                          placeholder="First"
                          :class="{ 'is-invalid': form.errors.first_name }"
                          required
                          @input="updateFullName"
                        />
                        <div v-if="form.errors.first_name" class="invalid-feedback d-block mt-1 small">
                          <i class="fas fa-exclamation-circle me-1"></i>
                          {{ form.errors.first_name }}
                        </div>
                      </div>
                    </div>

                    <!-- Middle Name -->
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label fw-medium small">
                          <i class="fas fa-user me-1"></i>
                          Middle Name
                        </label>
                        <input
                          type="text"
                          v-model="form.middle_name"
                          class="form-control"
                          placeholder="Middle"
                          :class="{ 'is-invalid': form.errors.middle_name }"
                          @input="updateFullName"
                        />
                        <div v-if="form.errors.middle_name" class="invalid-feedback d-block mt-1 small">
                          <i class="fas fa-exclamation-circle me-1"></i>
                          {{ form.errors.middle_name }}
                        </div>
                      </div>
                    </div>

                    <!-- Last Name -->
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label fw-medium small">
                          <i class="fas fa-user me-1"></i>
                          Last Name
                        </label>
                        <input
                          type="text"
                          v-model="form.last_name"
                          class="form-control"
                          placeholder="Last"
                          :class="{ 'is-invalid': form.errors.last_name }"
                          required
                          @input="updateFullName"
                        />
                        <div v-if="form.errors.last_name" class="invalid-feedback d-block mt-1 small">
                          <i class="fas fa-exclamation-circle me-1"></i>
                          {{ form.errors.last_name }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Full Name Preview (Hidden but will be submitted) -->
                  <input type="hidden" v-model="form.full_name" />

                  <!-- Email -->
                  <div class="form-group mb-3">
                    <label class="form-label fw-medium small">
                      <i class="fas fa-envelope me-1"></i>
                      Email Address
                    </label>
                    <input
                      type="email"
                      v-model="form.email"
                      class="form-control"
                      placeholder="you@example.com"
                      :class="{ 'is-invalid': form.errors.email }"
                      required
                    />
                    <div v-if="form.errors.email" class="invalid-feedback d-block mt-1 small">
                      <i class="fas fa-exclamation-circle me-1"></i>
                      {{ form.errors.email }}
                    </div>
                  </div>

                  <!-- Phone -->
                  <div class="form-group mb-3">
                    <label class="form-label fw-medium small">
                      <i class="fas fa-phone me-1"></i>
                      Phone Number
                    </label>
                    <input
                      type="tel"
                      v-model="form.phone"
                      class="form-control"
                      placeholder="Enter your phone number"
                      :class="{ 'is-invalid': form.errors.phone }"
                      required
                    />
                    <div v-if="form.errors.phone" class="invalid-feedback d-block mt-1 small">
                      <i class="fas fa-exclamation-circle me-1"></i>
                      {{ form.errors.phone }}
                    </div>
                  </div>

                  <!-- Password -->
                  <div class="form-group mb-3">
                    <label class="form-label fw-medium small">
                      <i class="fas fa-lock me-1"></i>
                      Password
                    </label>
                    <div class="input-group">
                      <input
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        class="form-control"
                        placeholder="Create a password"
                        :class="{ 'is-invalid': form.errors.password }"
                        required
                      />
                      <button
                        type="button"
                        class="input-group-text btn-eye"
                        @click="showPassword = !showPassword"
                      >
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      </button>
                    </div>
                    <div v-if="form.errors.password" class="invalid-feedback d-block mt-1 small">
                      <i class="fas fa-exclamation-circle me-1"></i>
                      {{ form.errors.password }}
                    </div>
                  </div>

                  <!-- Confirm Password -->
                  <div class="form-group mb-4">
                    <label class="form-label fw-medium small">
                      <i class="fas fa-lock me-1"></i>
                      Confirm Password
                    </label>
                    <div class="input-group">
                      <input
                        :type="showConfirmPassword ? 'text' : 'password'"
                        v-model="form.password_confirmation"
                        class="form-control"
                        placeholder="Confirm your password"
                        required
                      />
                      <button
                        type="button"
                        class="input-group-text btn-eye"
                        @click="showConfirmPassword = !showConfirmPassword"
                      >
                        <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Terms Agreement -->
                  <div class="form-check mb-4">
                    <input
                      type="checkbox"
                      id="terms"
                      v-model="form.agreeTerms"
                      class="form-check-input"
                      :class="{ 'is-invalid': form.errors.agreeTerms }"
                      required
                    />
                    <label for="terms" class="form-check-label small">
                      <i class="fas fa-check me-1"></i>
                      I agree to the 
                      <a href="#" class="text-primary fw-medium" @click.prevent="showTermsModal">Terms of Service</a> 
                      and 
                      <a href="#" class="text-primary fw-medium" @click.prevent="showPrivacyModal">Privacy Policy</a>
                    </label>
                    <div v-if="form.errors.agreeTerms" class="invalid-feedback d-block mt-1 small">
                      <i class="fas fa-exclamation-circle me-1"></i>
                      {{ form.errors.agreeTerms }}
                    </div>
                  </div>

                  <!-- Error Message -->
                  <div v-if="form.hasErrors" class="alert alert-danger alert-dismissible fade show mb-4 small">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ form.errors.message || 'Please check the form for errors' }}
                  </div>

                  <!-- Submit Button -->
                  <button
                    type="submit"
                    class="btn btn-primary w-100 py-2 mb-3"
                    :disabled="form.processing"
                  >
                    <span v-if="form.processing">
                      <span class="spinner-border spinner-border-sm me-2"></span>
                      Creating Account...
                    </span>
                    <span v-else>
                      <i class="fas fa-user-plus me-2"></i>
                      Create Account
                    </span>
                  </button>

                  <!-- Login Link -->
                  <div class="text-center">
                    <p class="mb-0 text-muted small">
                      Already have an account?
                      <Link href="/login" class="text-primary fw-medium ms-1">
                        Sign in here
                      </Link>
                    </p>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Terms of Service Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" :class="{'dark-modal': isDarkMode}">
          <div class="modal-header" :class="{'bg-dark text-light': isDarkMode}">
            <h5 class="modal-title fw-bold" id="termsModalLabel">
              <i class="fas fa-file-contract me-2"></i>Terms of Service
            </h5>
            <button type="button" class="btn-close" :class="{'btn-close-white': isDarkMode}" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" :class="{'dark-modal-body': isDarkMode}">
            <!-- Terms Content -->
            <div class="terms-content">
              <div class="mb-4">
                <h6 class="fw-bold mb-3">Last Updated: December 2024</h6>
                <p class="text-muted">
                  Welcome to E-SHOP Premium Marketplace. By accessing or using our services, you agree to be bound by these Terms of Service.
                </p>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">1. Account Registration</h6>
                <ul class="text-muted">
                  <li class="mb-2">You must be at least 18 years old to create an account</li>
                  <li class="mb-2">You are responsible for maintaining the confidentiality of your account</li>
                  <li class="mb-2">You must provide accurate and complete information</li>
                  <li>You are responsible for all activities under your account</li>
                </ul>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">2. User Responsibilities</h6>
                <ul class="text-muted">
                  <li class="mb-2">Use the platform only for lawful purposes</li>
                  <li class="mb-2">Do not upload malicious content or viruses</li>
                  <li class="mb-2">Respect intellectual property rights</li>
                  <li>Do not engage in fraudulent activities</li>
                </ul>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">3. Product Listings & Purchases</h6>
                <ul class="text-muted">
                  <li class="mb-2">All product descriptions must be accurate</li>
                  <li class="mb-2">Sellers are responsible for product quality</li>
                  <li class="mb-2">Prices are subject to change without notice</li>
                  <li>All sales are final unless otherwise stated</li>
                </ul>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">4. Payment & Fees</h6>
                <ul class="text-muted">
                  <li class="mb-2">All payments are processed securely</li>
                  <li class="mb-2">Additional fees may apply for certain services</li>
                  <li class="mb-2">Refunds are processed according to our refund policy</li>
                  <li>We reserve the right to change fee structures</li>
                </ul>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">5. Limitation of Liability</h6>
                <p class="text-muted">
                  E-SHOP shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the service.
                </p>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">6. Termination</h6>
                <p class="text-muted">
                  We reserve the right to terminate or suspend your account at our sole discretion, without notice, for conduct that we believe violates these Terms or is harmful to other users, us, or third parties, or for any other reason.
                </p>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">7. Changes to Terms</h6>
                <p class="text-muted">
                  We may modify these Terms at any time. We will provide notice of significant changes by posting the new Terms on our website. Your continued use of the service after such modifications constitutes your acceptance of the modified Terms.
                </p>
              </div>

              <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> By creating an account, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.
              </div>
            </div>
          </div>
          <div class="modal-footer" :class="{'bg-dark': isDarkMode}">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="acceptTerms">
              <i class="fas fa-check me-2"></i>I Accept
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" :class="{'dark-modal': isDarkMode}">
          <div class="modal-header" :class="{'bg-dark text-light': isDarkMode}">
            <h5 class="modal-title fw-bold" id="privacyModalLabel">
              <i class="fas fa-shield-alt me-2"></i>Privacy Policy
            </h5>
            <button type="button" class="btn-close" :class="{'btn-close-white': isDarkMode}" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" :class="{'dark-modal-body': isDarkMode}">
            <!-- Privacy Policy Content -->
            <div class="privacy-content">
              <div class="mb-4">
                <h6 class="fw-bold mb-3">Effective Date: December 2024</h6>
                <p class="text-muted">
                  At E-SHOP, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.
                </p>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">1. Information We Collect</h6>
                <h6 class="fw-semibold mb-2">Personal Information:</h6>
                <ul class="text-muted mb-3">
                  <li class="mb-2">Name, email address, phone number</li>
                  <li class="mb-2">Billing and shipping addresses</li>
                  <li class="mb-2">Payment information (processed securely)</li>
                  <li>Account credentials</li>
                </ul>

                <h6 class="fw-semibold mb-2">Usage Information:</h6>
                <ul class="text-muted">
                  <li class="mb-2">IP address and browser type</li>
                  <li class="mb-2">Pages visited and time spent on site</li>
                  <li class="mb-2">Device information</li>
                  <li>Cookies and tracking technologies</li>
                </ul>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">2. How We Use Your Information</h6>
                <ul class="text-muted">
                  <li class="mb-2">To provide and maintain our services</li>
                  <li class="mb-2">To process your transactions</li>
                  <li class="mb-2">To communicate with you about orders, products, and promotions</li>
                  <li class="mb-2">To improve our website and services</li>
                  <li class="mb-2">To prevent fraud and ensure security</li>
                  <li>To comply with legal obligations</li>
                </ul>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">3. Data Sharing & Disclosure</h6>
                <p class="text-muted mb-2">
                  We do not sell your personal information to third parties. We may share your information with:
                </p>
                <ul class="text-muted">
                  <li class="mb-2">Service providers who assist in our operations</li>
                  <li class="mb-2">Payment processors to complete transactions</li>
                  <li class="mb-2">Shipping carriers for order delivery</li>
                  <li class="mb-2">Law enforcement when required by law</li>
                  <li>Business partners with your consent</li>
                </ul>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">4. Data Security</h6>
                <p class="text-muted">
                  We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet or electronic storage is 100% secure.
                </p>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">5. Your Rights & Choices</h6>
                <ul class="text-muted">
                  <li class="mb-2">Access and update your personal information</li>
                  <li class="mb-2">Opt-out of marketing communications</li>
                  <li class="mb-2">Request deletion of your account and data</li>
                  <li class="mb-2">Control cookie preferences through browser settings</li>
                  <li>File a complaint with data protection authorities</li>
                </ul>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">6. Cookies & Tracking Technologies</h6>
                <p class="text-muted mb-2">
                  We use cookies and similar tracking technologies to track activity on our service and hold certain information. Cookies are files with small amounts of data that may include an anonymous unique identifier.
                </p>
                <p class="text-muted">
                  You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our service.
                </p>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold mb-3">7. Changes to This Policy</h6>
                <p class="text-muted">
                  We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Effective Date" at the top. You are advised to review this Privacy Policy periodically for any changes.
                </p>
              </div>

              <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Contact Us:</strong> If you have any questions about this Privacy Policy, please contact us at privacy@eshop.com
              </div>
            </div>
          </div>
          <div class="modal-footer" :class="{'bg-dark': isDarkMode}">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="acceptPrivacy">
              <i class="fas fa-check me-2"></i>I Accept
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, watchEffect } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Dark mode
const isDarkMode = ref(localStorage.getItem('theme') === 'dark')

// Watch for theme changes
watchEffect(() => {
  window.addEventListener('theme-changed', (event) => {
    isDarkMode.value = event.detail.theme === 'dark'
  })
  
  // Get initial theme
  const theme = localStorage.getItem('theme') || 'light'
  isDarkMode.value = theme === 'dark'
})

// Register Form
const form = useForm({
  first_name: '',
  middle_name: '',
  last_name: '',
  full_name: '', // Will be auto-generated
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  agreeTerms: false
})

// State
const showPassword = ref(false)
const showConfirmPassword = ref(false)
let termsModal = null
let privacyModal = null

// Initialize modals
onMounted(() => {
  // Check if Bootstrap is available
  if (typeof bootstrap !== 'undefined') {
    const termsModalElement = document.getElementById('termsModal')
    const privacyModalElement = document.getElementById('privacyModal')
    
    termsModal = new bootstrap.Modal(termsModalElement)
    privacyModal = new bootstrap.Modal(privacyModalElement)
  }
})

// Function to update full_name by combining name parts
const updateFullName = () => {
  const parts = []
  
  if (form.first_name.trim()) parts.push(form.first_name.trim())
  if (form.middle_name.trim()) parts.push(form.middle_name.trim())
  if (form.last_name.trim()) parts.push(form.last_name.trim())
  
  form.full_name = parts.join(' ')
}

// Show Terms of Service modal
const showTermsModal = () => {
  if (termsModal) {
    termsModal.show()
  }
}

// Show Privacy Policy modal
const showPrivacyModal = () => {
  if (privacyModal) {
    privacyModal.show()
  }
}

// Accept Terms of Service
const acceptTerms = () => {
  form.agreeTerms = true
}

// Accept Privacy Policy
const acceptPrivacy = () => {
  form.agreeTerms = true
}

// Register method
const register = () => {
  // Ensure full_name is updated before submission
  updateFullName()
  
  form.post('/register', {
    preserveScroll: false,
    preserveState: false,
    onSuccess: () => {
      // Success handled by controller
    },
    onError: (errors) => {
      console.log('Registration errors:', errors)
    }
  })
}
</script>

<style scoped>
/* Light Theme */
.login-container:not(.dark-theme) .minimal-bg {
  background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
}

.login-container:not(.dark-theme) .login-card {
  background: white;
  border: 1px solid #e9ecef;
  color: #2c3e50;
}

.login-container:not(.dark-theme) .card-header {
  background: white;
  border-bottom: 1px solid #e9ecef;
}

.login-container:not(.dark-theme) .form-control {
  background-color: white;
  border: 1px solid #e9ecef;
  color: #2c3e50;
}

.login-container:not(.dark-theme) .form-control:focus {
  background-color: white;
  border-color: #0d6efd;
  color: #2c3e50;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.login-container:not(.dark-theme) .btn-eye {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  color: #6c757d;
}

.login-container:not(.dark-theme) .btn-eye:hover {
  background: #e9ecef;
}

.login-container:not(.dark-theme) .text-muted {
  color: #6c757d !important;
}

.login-container:not(.dark-theme) .form-check-label a {
  color: #0d6efd !important;
}

/* Dark Theme */
.login-container.dark-theme .minimal-bg {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
}

.login-container.dark-theme .login-card {
  background: #1e293b;
  border: 1px solid #334155;
  color: #f1f5f9;
}

.login-container.dark-theme .card-header {
  background: #1e293b;
  border-bottom: 1px solid #334155;
}

.login-container.dark-theme .form-control {
  background-color: #334155;
  border: 1px solid #475569;
  color: #f1f5f9;
}

.login-container.dark-theme .form-control:focus {
  background-color: #334155;
  border-color: #0d6efd;
  color: #f1f5f9;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.login-container.dark-theme .btn-eye {
  background: #475569;
  border: 1px solid #64748b;
  color: #cbd5e1;
}

.login-container.dark-theme .btn-eye:hover {
  background: #64748b;
}

.login-container.dark-theme .text-muted {
  color: #94a3b8 !important;
}

.login-container.dark-theme .form-check-label a {
  color: #93c5fd !important;
}

/* Modal dark theme */
.dark-modal .modal-content {
  background-color: #1e293b;
  color: #f1f5f9;
  border: 1px solid #334155;
}

.dark-modal .modal-header {
  background-color: #0f172a;
  border-bottom: 1px solid #334155;
}

.dark-modal .modal-body {
  background-color: #1e293b;
}

.dark-modal .modal-footer {
  background-color: #0f172a;
  border-top: 1px solid #334155;
}

.dark-modal .text-muted {
  color: #94a3b8 !important;
}

.dark-modal .fw-bold {
  color: #f1f5f9 !important;
}

.dark-modal .fw-semibold {
  color: #e2e8f0 !important;
}

.dark-modal .alert-info {
  background-color: rgba(13, 110, 253, 0.1) !important;
  border-color: rgba(13, 110, 253, 0.2) !important;
  color: #93c5fd !important;
}

/* Common styles */
.login-container {
  position: relative;
  z-index: 1;
  padding: 20px;
}

.login-card {
  border-radius: 12px;
  overflow: hidden;
  animation: slideUp 0.4s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.card-body {
  padding: 2rem;
}

.form-group {
  position: relative;
}

.btn-primary {
  background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
  border: none;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
}

/* Modal scrollbar */
.terms-content,
.privacy-content {
  max-height: 60vh;
  overflow-y: auto;
  padding-right: 10px;
}

.terms-content::-webkit-scrollbar,
.privacy-content::-webkit-scrollbar {
  width: 6px;
}

.login-container:not(.dark-theme) .terms-content::-webkit-scrollbar-track,
.login-container:not(.dark-theme) .privacy-content::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.login-container.dark-theme .terms-content::-webkit-scrollbar-track,
.login-container.dark-theme .privacy-content::-webkit-scrollbar-track {
  background: #334155;
}

.terms-content::-webkit-scrollbar-thumb,
.privacy-content::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.terms-content::-webkit-scrollbar-thumb:hover,
.privacy-content::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

@media (max-width: 768px) {
  .card-body {
    padding: 1.5rem;
  }
  
  .row.g-3 {
    margin-bottom: 1rem !important;
  }
}

@media (max-width: 576px) {
  .card-body {
    padding: 1rem;
  }
  
  .terms-content,
  .privacy-content {
    max-height: 50vh;
  }
}
</style>