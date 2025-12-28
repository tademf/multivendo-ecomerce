<template>
  <AppLayout>
    <!-- Simple White Background -->
    <div class="minimal-bg"></div>

    <!-- Main Container -->
    <div class="login-container min-vh-100 d-flex align-items-center justify-content-center">
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
                      <a href="#" class="text-primary">Terms of Service</a> 
                      and 
                      <a href="#" class="text-primary">Privacy Policy</a>
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
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

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

// Function to update full_name by combining name parts
const updateFullName = () => {
  const parts = []
  
  if (form.first_name.trim()) parts.push(form.first_name.trim())
  if (form.middle_name.trim()) parts.push(form.middle_name.trim())
  if (form.last_name.trim()) parts.push(form.last_name.trim())
  
  form.full_name = parts.join(' ')
}

// Watch all name fields and update full_name
watch([
  () => form.first_name,
  () => form.middle_name,
  () => form.last_name
], updateFullName, { immediate: true })

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
/* Minimal White Background */
.minimal-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
  z-index: -1;
}

/* Login Container */
.login-container {
  position: relative;
  z-index: 1;
  padding: 20px;
}

/* Login Card */
.login-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.05);
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

/* Card Header */
.card-header {
  background: white;
  border-bottom: 1px solid #e9ecef;
}

/* Card Body */
.card-body {
  padding: 2rem;
}

@media (max-width: 768px) {
  .card-body {
    padding: 1.5rem;
  }
}

/* Form Styles */
.form-group {
  position: relative;
}

.input-group .form-control {
  border: 1px solid #e9ecef;
  padding: 0.5rem 0.75rem;
  transition: all 0.2s;
}

.input-group .form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.1);
}

.btn-eye {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-left: none;
  color: #6c757d;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-eye:hover {
  background: #e9ecef;
}

/* Name Fields */
.name-row .form-control {
  font-size: 0.9rem;
}

/* Buttons */
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

.btn-primary:disabled {
  opacity: 0.7;
}

/* Terms Checkbox */
.form-check-input:checked {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

/* Responsive */
@media (max-width: 768px) {
  .row.g-3 {
    margin-bottom: 1rem !important;
  }
  
  .col-md-4 {
    margin-bottom: 0.5rem;
  }
}

@media (max-width: 576px) {
  .card-body {
    padding: 1rem;
  }
  
  .name-row {
    flex-direction: column;
  }
  
  .col-md-4 {
    width: 100%;
    margin-bottom: 0.75rem;
  }
}
</style>