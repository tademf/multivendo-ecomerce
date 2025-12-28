<template>
  <AppLayout>
    <!-- Simple White Background -->
    <div class="minimal-bg"></div>

    <!-- Main Container -->
    <div class="login-container min-vh-100 d-flex align-items-center justify-content-center">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">
            
            <!-- Login Card -->
            <div class="login-card">
              <!-- Card Header -->
              <div class="card-header text-center p-4">
                <div class="login-header">
                  <h2 class="fw-bold mb-2">Welcome Back</h2>
                  <p class="text-muted small">Sign in to your account</p>
                </div>
              </div>

              <!-- Card Body -->
              <div class="card-body p-4">
                <!-- Status Alert Messages -->
                <!-- Banned Account Message -->
                <div v-if="accountStatus === 'banned'" class="alert alert-danger alert-dismissible fade show mb-4">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-ban fa-lg me-3"></i>
                    <div>
                      <h6 class="fw-bold mb-1">Account Banned</h6>
                      <p class="mb-0 small">Your account has been suspended. Please contact our support team.</p>
                      <div class="mt-2">
                        <a href="mailto:support@example.com" class="btn btn-sm btn-outline-danger">
                          <i class="fas fa-headset me-1"></i> Contact Support
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Inactive Account Message -->
                <div v-else-if="accountStatus === 'inactive'" class="alert alert-warning alert-dismissible fade show mb-4">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
                    <div>
                      <h6 class="fw-bold mb-1">Account Inactive</h6>
                      <p class="mb-0 small">Your account is currently inactive. Please contact support to reactivate.</p>
                    </div>
                  </div>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="login">
                  <!-- Email Field -->
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
                      :class="{ 'is-invalid': form.errors.email || loginError }"
                      required
                      :disabled="accountStatus === 'banned'"
                    />
                    <div v-if="form.errors.email" class="invalid-feedback d-block mt-1 small">
                      <i class="fas fa-exclamation-circle me-1"></i>
                      {{ form.errors.email }}
                    </div>
                  </div>

                  <!-- Password Field -->
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
                        placeholder="Enter your password"
                        :class="{ 'is-invalid': form.errors.password || loginError }"
                        required
                        :disabled="accountStatus === 'banned'"
                      />
                      <button
                        type="button"
                        class="input-group-text btn-eye"
                        @click="showPassword = !showPassword"
                        :disabled="accountStatus === 'banned'"
                      >
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      </button>
                    </div>
                    <div v-if="form.errors.password" class="invalid-feedback d-block mt-1 small">
                      <i class="fas fa-exclamation-circle me-1"></i>
                      {{ form.errors.password }}
                    </div>
                  </div>

                  <!-- General Login Error -->
                  <div v-if="loginError && !accountStatus" class="alert alert-danger alert-dismissible fade show mb-4 small">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ loginError }}
                  </div>

                  <!-- Remember Me -->
                  <div class="form-check mb-4">
                    <input
                      type="checkbox"
                      id="remember"
                      v-model="form.remember"
                      class="form-check-input"
                      :disabled="accountStatus === 'banned'"
                    />
                    <label for="remember" class="form-check-label small">
                      <i class="fas fa-check me-1"></i>
                      Keep me signed in
                    </label>
                  </div>

                  <!-- Submit Button -->
                  <button
                    type="submit"
                    class="btn btn-primary w-100 py-2 mb-3"
                    :disabled="form.processing || accountStatus === 'banned'"
                    :class="{ 'btn-danger': accountStatus === 'banned' }"
                  >
                    <span v-if="form.processing">
                      <span class="spinner-border spinner-border-sm me-2"></span>
                      Signing in...
                    </span>
                    <span v-else>
                      <i :class="accountStatus === 'banned' ? 'fas fa-ban me-2' : 'fas fa-sign-in-alt me-2'"></i>
                      {{ accountStatus === 'banned' ? 'Account Banned' : 'Sign In' }}
                    </span>
                  </button>

                  <!-- Register Link -->
                  <div class="text-center">
                    <p class="mb-0 text-muted small">
                      Don't have an account?
                      <Link href="/register" class="text-primary fw-medium ms-1">
                        Sign up
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
import { ref, onMounted } from 'vue'
import { router, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Login Form
const form = useForm({
  email: '',
  password: '',
  remember: false
})

// State
const showPassword = ref(false)
const loginError = ref('')
const accountStatus = ref('') // 'active', 'inactive', 'banned'

// Check URL for status errors
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const status = urlParams.get('status');
  
  if (status) {
    if (status === 'banned') {
      accountStatus.value = 'banned';
      loginError.value = 'Your account has been banned. Please contact support.';
    } else if (status === 'inactive') {
      accountStatus.value = 'inactive';
      loginError.value = 'Your account is inactive. Please contact support.';
    }
  }
})

// Login method
const login = () => {
  // Clear previous errors
  loginError.value = '';
  accountStatus.value = '';

  form.post('/login', {
    preserveScroll: false,
    preserveState: false,
    onSuccess: () => {
      form.password = '';
    },
    onError: (errors) => {
      // Check for custom status errors from backend
      if (errors.status) {
        if (errors.status === 'banned') {
          accountStatus.value = 'banned';
          loginError.value = 'Your account has been banned. Please contact support.';
        } else if (errors.status === 'inactive') {
          accountStatus.value = 'inactive';
          loginError.value = 'Your account is inactive. Please contact support.';
        }
      } else if (errors.email) {
        loginError.value = errors.email;
      } else if (errors.password) {
        loginError.value = errors.password;
      }
    }
  });
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

.input-group .form-control:disabled {
  background-color: #f8f9fa;
  cursor: not-allowed;
}

.btn-eye {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-left: none;
  color: #6c757d;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-eye:hover:not(:disabled) {
  background: #e9ecef;
}

.btn-eye:disabled {
  background: #f8f9fa;
  cursor: not-allowed;
  opacity: 0.6;
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

.btn-primary:disabled,
.btn-primary.btn-danger:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-primary.btn-danger {
  background: linear-gradient(135deg, #dc3545 0%, #dc3545 100%);
  cursor: not-allowed;
}

/* Alert Styles */
.alert {
  border: none;
  border-radius: 8px;
}

.alert-danger {
  background: rgba(220, 53, 69, 0.1);
  border-left: 4px solid #dc3545;
}

.alert-warning {
  background: rgba(255, 193, 7, 0.1);
  border-left: 4px solid #ffc107;
}

/* Responsive */
@media (max-width: 576px) {
  .card-body {
    padding: 1rem;
  }
  
  .alert .d-flex {
    flex-direction: column;
    text-align: center;
  }
  
  .alert i {
    margin-bottom: 0.5rem;
  }
}
</style>