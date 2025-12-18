<template>
  <AppLayout>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">

            <!-- Login Card -->
            <div class="card border-0 shadow-lg">
              <div class="card-body p-5">
                <!-- Header -->
                <div class="text-center mb-4">
                  <div class="mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3">
                      <i class="fas fa-user text-primary fa-2x"></i>
                    </div>
                  </div>
                  <h2 class="fw-bold">Welcome Back</h2>
                  <p class="text-muted">Sign in to your account</p>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="login">
                  <!-- Email -->
                  <div class="mb-4">
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
                        placeholder="Enter your email"
                        :class="{ 'is-invalid': form.errors.email }"
                        required
                      />
                    </div>
                    <div v-if="form.errors.email" class="invalid-feedback d-block">
                      {{ form.errors.email }}
                    </div>
                  </div>

                  <!-- Password -->
                  <div class="mb-4">
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
                        placeholder="Enter your password"
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

                  <!-- Remember Me & Forgot Password -->
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                      <input
                        type="checkbox"
                        id="remember"
                        v-model="form.remember"
                        class="form-check-input"
                      />
                      <label for="remember" class="form-check-label">Remember me</label>
                    </div>
                    <Link href="/forgot-password" class="text-decoration-none small text-primary">
                      Forgot password?
                    </Link>
                  </div>

                  <!-- Error Message -->
                  <div v-if="form.hasErrors" class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ form.errors.message || 'Invalid credentials' }}
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
                      Signing in...
                    </span>
                    <span v-else>
                      <i class="fas fa-sign-in-alt me-2"></i>
                      Sign In
                    </span>
                  </button>

                  <!-- Register Link -->
                  <div class="text-center">
                    <p class="mb-0">
                      Don't have an account?
                      <Link href="/register" class="text-decoration-none fw-semibold text-primary ms-1">
                        Sign up now
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
  name: "LoginPage",
  
  components: {
    Link,
    AppLayout
  },

  data() {
    return {
      form: useForm({
        email: '', 
        password: '',
        remember: false 
      }),
      showPassword: false
    };
  },

  methods: {
    login() {
      if (this.form.remember) {
        localStorage.setItem("rememberedEmail", this.form.email);
      } else {
        localStorage.removeItem("rememberedEmail");
      }

      this.form.post('/login', {
        onSuccess: () => {
          this.form.password = '';
        },
        preserveScroll: true
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
</style>