<template>
  <AppLayout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light py-5">
      <div class="container py-5">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">
            <!-- Register Card -->
            <div class="card border-0 shadow-lg">
              <div class="card-body p-5">
                <!-- Header -->
                <div class="text-center mb-4">
                  <div class="mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3">
                      <i class="fas fa-user-plus text-primary fa-2x"></i>
                    </div>
                  </div>
                  <h2 class="fw-bold">Create Account</h2>
                  <p class="text-muted">Join us and start shopping</p>
                </div>

                <!-- Success Message -->
                <div v-if="successMessage" class="alert alert-success alert-dismissible fade show mb-4">
                  <i class="fas fa-check-circle me-2"></i>
                  {{ successMessage }}
                  <button type="button" class="btn-close" @click="successMessage = ''"></button>
                </div>

                <!-- Register Form -->
                <form @submit.prevent="register">
                  <!-- Full Name -->
                  <div class="mb-3">
                    <label for="full_name" class="form-label fw-semibold">Full Name</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-user text-muted"></i>
                      </span>
                      <input
                        type="text"
                        id="full_name"
                        class="form-control"
                        v-model="form.full_name"
                        placeholder="Enter your full name"
                        :class="{ 'is-invalid': form.errors.full_name }"
                        required
                      />
                    </div>
                    <div v-if="form.errors.full_name" class="invalid-feedback d-block">
                      {{ form.errors.full_name }}
                    </div>
                  </div>

                  <!-- Email -->
                  <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-envelope text-muted"></i>
                      </span>
                      <input
                        type="email"
                        id="email"
                        class="form-control"
                        v-model="form.email"
                        placeholder="you@example.com"
                        :class="{ 'is-invalid': form.errors.email }"
                        required
                      />
                    </div>
                    <div v-if="form.errors.email" class="invalid-feedback d-block">
                      {{ form.errors.email }}
                    </div>
                  </div>

                  <!-- Phone -->
                  <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">Phone Number</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-phone text-muted"></i>
                      </span>
                      <input
                        type="tel"
                        id="phone"
                        class="form-control"
                        v-model="form.phone"
                        placeholder="Enter your phone number"
                        :class="{ 'is-invalid': form.errors.phone }"
                        required
                      />
                    </div>
                    <div v-if="form.errors.phone" class="invalid-feedback d-block">
                      {{ form.errors.phone }}
                    </div>
                  </div>

                  <!-- Password -->
                  <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-lock text-muted"></i>
                      </span>
                      <input
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        class="form-control border-end-0"
                        v-model="form.password"
                        placeholder="Create a password"
                        :class="{ 'is-invalid': form.errors.password }"
                        required
                      />
                      <button
                        type="button"
                        class="input-group-text bg-light"
                        @click="showPassword = !showPassword"
                      >
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      </button>
                    </div>
                    <div v-if="form.errors.password" class="invalid-feedback d-block">
                      {{ form.errors.password }}
                    </div>
                  </div>

                  <!-- Confirm Password -->
                  <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-lock text-muted"></i>
                      </span>
                      <input
                        :type="showConfirmPassword ? 'text' : 'password'"
                        id="password_confirmation"
                        class="form-control border-end-0"
                        v-model="form.password_confirmation"
                        placeholder="Confirm your password"
                        required
                      />
                      <button
                        type="button"
                        class="input-group-text bg-light"
                        @click="showConfirmPassword = !showConfirmPassword"
                      >
                        <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Terms Agreement -->
                  <div class="mb-4">
                    <div class="form-check">
                      <input
                        type="checkbox"
                        id="terms"
                        v-model="form.agreeTerms"
                        class="form-check-input"
                        :class="{ 'is-invalid': form.errors.agreeTerms }"
                        required
                      />
                      <label for="terms" class="form-check-label small">
                        I agree to the 
                        <a href="#" class="text-decoration-none">Terms of Service</a> 
                        and 
                        <a href="#" class="text-decoration-none">Privacy Policy</a>
                      </label>
                    </div>
                    <div v-if="form.errors.agreeTerms" class="invalid-feedback d-block">
                      {{ form.errors.agreeTerms }}
                    </div>
                  </div>

                  <!-- Error Message -->
                  <div v-if="form.hasErrors" class="alert alert-danger alert-dismissible fade show mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ form.errors.message || 'Please check the form for errors' }}
                    <button type="button" class="btn-close" @click="form.clearErrors()"></button>
                  </div>

                  <!-- Submit Button -->
                  <button
                    type="submit"
                    class="btn btn-primary w-100 py-3 mb-4"
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
                    <p class="mb-0">
                      Already have an account?
                      <Link href="/login" class="text-decoration-none fw-semibold text-primary ms-1">
                        Sign in here
                      </Link>
                    </p>
                  </div>
                </form>
              </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-4">
              <p class="text-muted small">
                © {{ new Date().getFullYear() }} OnlineBuy. All rights reserved.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  name: "RegisterPage",
  
  components: {
    Link,
    AppLayout
  },

  setup() {
    const form = useForm({
      full_name: '',
      email: '',
      phone: '',
      password: '',
      password_confirmation: '',
      agreeTerms: false
    })

    return { form }
  },

  data() {
    return {
      showPassword: false,
      showConfirmPassword: false,
      successMessage: ''
    };
  },

  methods: {
    register() {
      this.successMessage = '';

      // Basic validation
      if (!this.form.full_name.trim() || !this.form.email.trim() || 
          !this.form.phone.trim() || !this.form.password.trim() || 
          !this.form.password_confirmation.trim()) {
        this.form.setError('email', 'Please fill in all fields!');
        return;
      }

      if (this.form.password !== this.form.password_confirmation) {
        this.form.setError('password', 'Passwords do not match!');
        return;
      }

      // Submit using Inertia
      this.form.post('/register', {
        onSuccess: () => {
          // Show success message
          this.successMessage = 'Registration successful! You can now log in.';
          
          // Clear form
          this.form.reset();
          this.showPassword = false;
          this.showConfirmPassword = false;
        },
        onError: (errors) => {
          console.error("Registration error:", errors);
        },
        preserveScroll: true,
        preserveState: true
      });
    }
  }
};
</script>

<style scoped>
.min-vh-100 {
  min-height: 100vh;
}

.bg-light {
  background-color: #f8f9fa !important;
}

.card {
  border-radius: 1rem;
}

.input-group-text {
  transition: all 0.3s ease;
}

.input-group:focus-within .input-group-text {
  background-color: #e3f2fd;
  color: #0d6efd;
  border-color: #0d6efd;
}

.btn-primary {
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.bg-primary.bg-opacity-10 {
  background-color: rgba(13, 110, 253, 0.1);
}

/* Smooth animations */
.card {
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .card-body.p-5 {
    padding: 2rem !important;
  }
}

@media (max-width: 576px) {
  .card-body.p-5 {
    padding: 1.5rem !important;
  }
}

/* Password toggle button styling */
.input-group button[type="button"] {
  cursor: pointer;
}

.input-group button[type="button"]:hover {
  background-color: #e9ecef;
}
</style>