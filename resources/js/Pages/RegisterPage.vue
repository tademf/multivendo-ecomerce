<template>
  <div class="auth-container">
    <!-- Back to Home Link -->
    <Link href="/" class="back-to-home">
      <i class="fas fa-arrow-left"></i>
      Back to Home
    </Link>

    <div class="auth-card">
      <h2 class="title">Create Your Account</h2>
      <p class="subtitle">Join us and start shopping now!</p>

      <!-- Message Box -->
      <div v-if="successMessage" class="success-msg">{{ successMessage }}</div>
      
      <form @submit.prevent="register">
        <div class="input-group">
          <input type="text" v-model="form.full_name" placeholder="Full Name" required />
          <div v-if="form.errors.full_name" class="input-error">{{ form.errors.full_name }}</div>
        </div>

        <div class="input-group">
          <input type="email" v-model="form.email" placeholder="you@example.com" required />
          <div v-if="form.errors.email" class="input-error">{{ form.errors.email }}</div>
        </div>

        <div class="input-group">
          <input type="tel" v-model="form.phone" placeholder="Phone Number" required />
          <div v-if="form.errors.phone" class="input-error">{{ form.errors.phone }}</div>
        </div>

        <div class="input-group password-group">
          <input :type="showPassword ? 'text' : 'password'" v-model="form.password" placeholder="Enter password" required />
          <span class="toggle-password" @click="showPassword = !showPassword">
            {{ showPassword ? 'Hide' : 'Show' }}
          </span>
          <div v-if="form.errors.password" class="input-error">{{ form.errors.password }}</div>
        </div>

        <div class="input-group password-group">
          <input :type="showPassword ? 'text' : 'password'" v-model="form.password_confirmation" placeholder="Confirm password" required />
        </div>

        <!-- Terms Agreement -->
        <div class="terms-agreement" v-if="showTermsCheckbox">
          <input type="checkbox" id="terms" v-model="form.agreeTerms" required />
          <label for="terms">I agree to the Terms of Service</label>
          <div v-if="form.errors.agreeTerms" class="input-error">{{ form.errors.agreeTerms }}</div>
        </div>

        <button class="btn-primary" :disabled="form.processing">
          {{ form.processing ? 'Registering...' : 'Register' }}
        </button>
      </form>

      <div v-if="form.hasErrors" class="error-msg">
        {{ form.errors.message || 'Please check the form for errors' }}
      </div>

      <p class="switch-text">
        Already have an account?
        <Link href="/login" class="switch-link">Login</Link>
      </p>
    </div>
  </div>
</template>

<script>
import { Link, useForm } from '@inertiajs/vue3'

export default {
  name: "RegisterPage",
  
  components: {
    Link
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
      showTermsCheckbox: true,
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
          
          console.log("Registration successful via Inertia");
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

.terms-agreement {
  display: flex;
  align-items: center;
  margin: 15px 0;
  gap: 8px;
}

.terms-agreement input {
  width: 16px;
  height: 16px;
  cursor: pointer;
}

.terms-agreement label {
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

.success-msg {
  color: #27ae60;
  text-align: center;
  margin-top: 10px;
  font-weight: 500;
  padding: 12px;
  background-color: #e9f7ef;
  border-radius: 8px;
  border: 1px solid #a3e9c4;
  font-size: 14px;
  margin-bottom: 15px;
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