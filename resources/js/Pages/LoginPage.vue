<!-- Login.vue -->
<template>
  <AppLayout>
    <div class="minimal-bg"></div>

    <div class="login-container min-vh-100 d-flex align-items-center justify-content-center" :class="{'dark-theme': isDarkMode}">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">
            
            <div class="login-card shadow-sm">
              <div class="card-header text-center p-4">
                <div class="login-header">
                  <h2 class="fw-bold mb-2">Welcome Back</h2>
                  <p class="text-muted small">Sign in to your account</p>
                </div>
              </div>

              <div class="card-body p-4">
                <div v-if="accountStatus === 'banned'" class="alert alert-danger alert-dismissible fade show mb-4">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-ban fa-lg me-3"></i>
                    <div>
                      <h6 class="fw-bold mb-1">Account Banned</h6>
                      <p class="mb-0 small">Your account has been suspended. Please contact our support team.</p>
                      <div class="mt-2">
                        <a href="mailto:temuclassic986@gmail.com" class="btn btn-sm btn-outline-danger">
                          <i class="fas fa-headset me-1"></i> Contact Support
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-else-if="accountStatus === 'inactive'" class="alert alert-warning alert-dismissible fade show mb-4">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
                    <div>
                      <h6 class="fw-bold mb-1">Account Inactive</h6>
                      <p class="mb-0 small">Your account is currently inactive. Please contact support to reactivate.</p>
                    <div class="mt-2">
                        <a href="mailto:temuclassic986@gmail.com" class="btn btn-sm btn-outline-danger">
                          <i class="fas fa-headset me-1"></i> Contact Support
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="$page.props.flash.success" class="alert alert-success small mb-4">
                    <i class="fas fa-check-circle me-2"></i> {{ $page.props.flash.success }}
                </div>

                <form @submit.prevent="login">
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

                  <div class="form-group mb-2">
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

                  <div class="text-end mb-3">
                    <button type="button" class="btn btn-link p-0 small text-decoration-none" @click="openOtpModal">
                        Forgot Password?
                    </button>
                  </div>

                  <div v-if="loginError && !accountStatus" class="alert alert-danger alert-dismissible fade show mb-4 small">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ loginError }}
                  </div>

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

    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" :class="{'dark-modal': isDarkMode}">
          <div class="modal-header" :class="{'bg-dark text-light': isDarkMode, 'bg-light': !isDarkMode}">
            <h5 class="modal-title fw-bold" id="otpModalLabel">
               <i class="fas fa-shield-alt me-2 text-primary"></i>
               {{ !otpStep ? 'Reset Password via OTP' : 'Enter 6-Digit Code' }}
            </h5>
            <button type="button" class="btn-close" :class="{'btn-close-white': isDarkMode}" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4" :class="{'dark-modal-body': isDarkMode}">
            <div v-if="!otpStep">
                <p class="text-muted small mb-4">Enter your registered email address. We will send you a One-Time Password (OTP) to log you in securely.</p>
                <form @submit.prevent="sendOtp">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input v-model="otpForm.email" type="email" class="form-control" placeholder="name@example.com" required :class="{'dark-input': isDarkMode}">
                        <div v-if="otpForm.errors.email" class="text-danger small mt-1">{{ otpForm.errors.email }}</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" :disabled="otpForm.processing">
                        <span v-if="otpForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                        Send OTP Code
                    </button>
                </form>
            </div>

            <div v-else>
                <div class="text-center mb-4">
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-2"></i>
                        OTP sent to <strong>{{ otpForm.email }}</strong>
                    </div>
                </div>
                <form @submit.prevent="verifyOtp">
                    <div class="mb-3 text-center">
                        <label class="form-label small fw-bold d-block mb-3">Enter Verification Code</label>
                        <input 
                            v-model="otpForm.otp" 
                            type="text" 
                            class="form-control form-control-lg text-center fw-bold letter-spacing-lg" 
                            placeholder="0 0 0 0 0 0" 
                            maxlength="6" 
                            required
                            :class="{'dark-input': isDarkMode}"
                        >
                        <div v-if="otpForm.errors.otp" class="text-danger small mt-2">{{ otpForm.errors.otp }}</div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2" :disabled="otpForm.processing">
                        <span v-if="otpForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                        Verify & Sign In
                    </button>
                    <button type="button" class="btn btn-link w-100 mt-3 text-muted small" @click="otpStep = false">
                        Use a different email
                    </button>
                </form>
            </div>
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

// Login Form
const form = useForm({
  email: '',
  password: '',
  remember: false
})

// OTP Form
const otpForm = useForm({
    email: '',
    otp: '',
})

// State
const showPassword = ref(false)
const loginError = ref('')
const accountStatus = ref('') // 'active', 'inactive', 'banned'
const otpStep = ref(false) // false = Email input, true = OTP input

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
  loginError.value = '';
  accountStatus.value = '';

  form.post('/login', {
    preserveScroll: true,
    onSuccess: () => {
      form.password = '';
    },
    onError: (errors) => {
      if (errors.status) {
        accountStatus.value = errors.status;
        loginError.value = errors.email || `Your account is ${errors.status}.`;
      } else if (errors.email) {
        loginError.value = errors.email;
      } else if (errors.password) {
        loginError.value = errors.password;
      }
    }
  });
}

/**
 * OTP FLOW METHODS
 */
const openOtpModal = () => {
    otpStep.value = false;
    otpForm.reset();
    otpForm.clearErrors();
    const modal = new bootstrap.Modal(document.getElementById('otpModal'));
    modal.show();
}

const sendOtp = () => {
    otpForm.post('/forgot-password-otp', {
        preserveScroll: true,
        onSuccess: () => {
            otpStep.value = true;
        },
        onError: (errors) => {
            console.error("OTP Send Error:", errors);
        }
    });
}

const verifyOtp = () => {
    otpForm.post('/verify-login-otp', {
        onSuccess: () => {
            // Success logic is handled by backend redirect
        },
        onError: (errors) => {
            console.error("OTP Verify Error:", errors);
        }
    });
}
</script>

<style scoped>
  /* .login-container{
      background: white;

  } */
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

.login-container:not(.dark-theme) .btn-eye:hover:not(:disabled) {
  background: #e9ecef;
}

.login-container:not(.dark-theme) .text-muted {
  color: #6c757d !important;
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

.login-container.dark-theme .btn-eye:hover:not(:disabled) {
  background: #64748b;
}

.login-container.dark-theme .text-muted {
  color: #94a3b8 !important;
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

.dark-modal .dark-input {
  background-color: #334155 !important;
  border-color: #475569 !important;
  color: #f1f5f9 !important;
}

.dark-modal .dark-input:focus {
  background-color: #334155 !important;
  border-color: #0d6efd !important;
  color: #f1f5f9 !important;
}

.dark-modal .alert-info {
  background-color: rgba(13, 110, 253, 0.1) !important;
  border-color: rgba(13, 110, 253, 0.2) !important;
  color: #93c5fd !important;
}

.dark-modal .btn-link {
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

/* Modal and Letter Spacing */
.letter-spacing-lg {
  letter-spacing: 0.5rem;
  font-size: 1.5rem;
}

@media (max-width: 768px) {
  .card-body {
    padding: 1.5rem;
  }
}
</style>