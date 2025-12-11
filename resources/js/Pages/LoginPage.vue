<template>
  <div class="auth-container">
    <!-- Back to Home Link -->
    <Link href="/" class="back-to-home">
      <i class="fas fa-arrow-left"></i>
      Back to Home
    </Link>

    <div class="auth-card">
      <h2 class="title">Welcome Back 👋</h2>
      <p class="subtitle">Login to continue shopping</p>

      <form @submit.prevent="login">
        <div class="input-group">
          <input type="email" v-model="form.email" placeholder="you@example.com" />
          <div v-if="form.errors.email" class="input-error">{{ form.errors.email }}</div>
        </div>

        <div class="input-group password-group">
          <input :type="showPassword ? 'text' : 'password'" v-model="form.password" placeholder="Enter password" />
          <span class="toggle-password" @click="showPassword = !showPassword">
            {{ showPassword ? 'Hide' : 'Show' }}
          </span>
          <div v-if="form.errors.password" class="input-error">{{ form.errors.password }}</div>
        </div>

        <!-- Remember Me -->
        <div class="remember-me">
          <input type="checkbox" id="remember" v-model="form.remember" />
          <label for="remember">Remember Me</label>
        </div>

        <button class="btn-primary" :disabled="form.processing">
          {{ form.processing ? 'Logging in...' : 'Login' }}
        </button>
      </form>

      <div v-if="form.hasErrors" class="error-msg">
        {{ form.errors.message || 'Please check your credentials' }}
      </div>

      <p class="switch-text">
        Don't have an account?
        <Link href="/register" class="switch-link">Register</Link>
      </p>
    </div>
  </div>
</template>

<script>
import { Link, useForm } from '@inertiajs/vue3'

export default {
  name: "LoginPage",
  
  components: {
    Link
  },

  data() {
    return {
      form: useForm({
        email: '', // Always empty
        password: '',
        remember: false // Always false by default
      }),
      showPassword: false
    };
  },

  methods: {
    login() {
      // Only save to localStorage if user explicitly checked "Remember Me"
      if (this.form.remember) {
        localStorage.setItem("rememberedEmail", this.form.email);
      } else {
        localStorage.removeItem("rememberedEmail");
      }

      this.form.post('/login', {
        onSuccess: () => {
          this.form.password = '';
          console.log("Login successful via Inertia");
        },
        onError: (errors) => {
          console.error("Login error:", errors);
        },
        preserveScroll: true
      });
    }
  }
};
</script>
<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  background: #ffffff;
  position: relative;
}

/* Back to Home Link */
.back-to-home {
  position: absolute;
  top: 30px;
  left: 30px;
  display: flex;
  align-items: center;
  gap: 8px;
  color: #666;
  text-decoration: none;
  font-size: 15px;
  padding: 8px 16px;
  border-radius: 8px;
  transition: all 0.3s ease;
  border: 1px solid #e0e0e0;
  background: #f9f9f9;
}

.back-to-home:hover {
  background: #f0f0f0;
  color: #333;
  transform: translateX(-2px);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.back-to-home i {
  font-size: 14px;
  transition: transform 0.3s ease;
}

.back-to-home:hover i {
  transform: translateX(-2px);
}

.auth-card {
  width: 100%;
  max-width: 420px;
  background: #ffffff;
  padding: 35px;
  border-radius: 18px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  animation: fadeIn 0.8s ease;
  border: 1px solid #f0f0f0;
}

.title {
  font-size: 28px;
  font-weight: bold;
  text-align: center;
  color: #333;
  margin-bottom: 8px;
}

.subtitle {
  text-align: center;
  font-size: 14px;
  margin-bottom: 25px;
  color: #666;
}

.input-group {
  margin-bottom: 18px;
  position: relative;
}

.input-group input {
  width: 100%;
  padding: 12px;
  margin-top: 6px;
  border: 1px solid #cfcfcf;
  border-radius: 10px;
  transition: 0.3s ease;
  font-size: 15px;
}

.input-group input:focus {
  border-color: #6a11cb;
  box-shadow: 0 0 5px rgba(106, 17, 203, 0.4);
  outline: none;
}

.password-group .toggle-password {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  font-size: 0.85rem;
  color: #2575fc;
  user-select: none;
  padding: 4px 8px;
  border-radius: 4px;
  transition: background 0.2s;
}

.password-group .toggle-password:hover {
  background: #f0f0f0;
}

.remember-me {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.remember-me input {
  margin-right: 8px;
  width: 16px;
  height: 16px;
  cursor: pointer;
}

.remember-me label {
  cursor: pointer;
  color: #555;
  font-size: 14px;
}

.btn-primary {
  width: 100%;
  background: linear-gradient(to right, #2575fc, #6a11cb);
  padding: 14px;
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  margin-top: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(37, 117, 252, 0.3);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(37, 117, 252, 0.4);
  background: linear-gradient(to right, #1a68fa, #5a0fb6);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}

.switch-text {
  text-align: center;
  margin-top: 20px;
  color: #555;
  font-size: 14px;
}

.switch-link {
  color: #2575fc;
  font-weight: 600;
  text-decoration: none;
  margin-left: 5px;
}

.switch-link:hover {
  text-decoration: underline;
  color: #1a68fa;
}

.error-msg {
  color: #e74c3c;
  text-align: center;
  margin-top: 10px;
  font-weight: 500;
  padding: 12px;
  background-color: #fdf0f0;
  border-radius: 8px;
  border: 1px solid #fad4d4;
  font-size: 14px;
}

.input-error {
  color: #e74c3c;
  font-size: 0.85rem;
  margin-top: 0.25rem;
  font-weight: 500;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0px);
  }
}

/* Responsive Design */
@media (max-width: 480px) {
  .auth-container {
    padding: 15px;
  }
  
  .back-to-home {
    top: 20px;
    left: 20px;
    font-size: 14px;
    padding: 6px 12px;
  }
  
  .auth-card {
    padding: 25px;
    margin-top: 40px;
  }
  
  .title {
    font-size: 24px;
  }
}
</style>