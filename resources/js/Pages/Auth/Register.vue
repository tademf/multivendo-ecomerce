<template>
  <div style="min-height: 100vh; background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e8 100%); display: flex; align-items: center; justify-content: center; padding: 40px 20px;">
    <!-- Registration Container -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 8px 30px rgba(0, 100, 0, 0.1); width: 100%; max-width: 420px; padding: 40px 30px; border: 1px solid #e0f0e0;">

      <!-- Header -->
      <div style="text-align: center; margin-bottom: 30px;">
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
          <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #008000 0%, #00a000 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px; box-shadow: 0 4px 12px rgba(0, 128, 0, 0.2);">
            <i class="fas fa-leaf" style="color: white; font-size: 20px;"></i>
          </div>
          <h1 style="font-size: 24px; color: #008000; font-weight: 700; margin: 0;">GreenMarket</h1>
        </div>
        <h2 style="font-size: 22px; color: #333; font-weight: 600; margin: 0 0 8px 0;">Create Account</h2>
        <p style="font-size: 14px; color: #666; margin: 0;">Join our global B2B marketplace</p>
      </div>

      <!-- Registration Form -->
      <form @submit.prevent="submit">
        <!-- Username -->
        <div style="margin-bottom: 20px;">
          <input
            v-model="form.username"
            placeholder="Username"
            style="width: 100%; padding: 14px 16px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
            onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
            onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
          />
          <div v-if="form.errors.username" style="color: #e74c3c; font-size: 12px; margin-top: 6px; display: flex; align-items: center; background: #fff5f5; padding: 8px 12px; border-radius: 4px; border-left: 3px solid #e74c3c;">
            <i class="fas fa-exclamation-circle" style="margin-right: 6px; font-size: 12px;"></i>{{ form.errors.username }}
          </div>
        </div>

        <!-- Email -->
        <div style="margin-bottom: 20px;">
          <input
            v-model="form.email"
            placeholder="Email"
            type="email"
            style="width: 100%; padding: 14px 16px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
            onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
            onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
          />
          <div v-if="form.errors.email" style="color: #e74c3c; font-size: 12px; margin-top: 6px; display: flex; align-items: center; background: #fff5f5; padding: 8px 12px; border-radius: 4px; border-left: 3px solid #e74c3c;">
            <i class="fas fa-exclamation-circle" style="margin-right: 6px; font-size: 12px;"></i>{{ form.errors.email }}
          </div>
        </div>

        <!-- Phone & National ID -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
          <div>
            <input
              v-model="form.phone"
              placeholder="Phone (optional)"
              style="width: 100%; padding: 14px 16px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
              onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
              onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
            />
          </div>
          <div>
            <input
              v-model="form.national_id"
              placeholder="National ID (optional)"
              style="width: 100%; padding: 14px 16px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
              onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
              onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
            />
          </div>
        </div>

        <!-- Password -->
        <div style="margin-bottom: 20px;">
          <input
            type="password"
            v-model="form.password"
            placeholder="Password"
            style="width: 100%; padding: 14px 16px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
            onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
            onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
          />
          <div v-if="form.errors.password" style="color: #e74c3c; font-size: 12px; margin-top: 6px; display: flex; align-items: center; background: #fff5f5; padding: 8px 12px; border-radius: 4px; border-left: 3px solid #e74c3c;">
            <i class="fas fa-exclamation-circle" style="margin-right: 6px; font-size: 12px;"></i>{{ form.errors.password }}
          </div>
        </div>

        <!-- Confirm Password -->
        <div style="margin-bottom: 20px;">
          <input
            type="password"
            v-model="form.password_confirmation"
            placeholder="Confirm Password"
            style="width: 100%; padding: 14px 16px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
            onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
            onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
          />
        </div>

        <!-- Terms Agreement -->
        <div style="margin-bottom: 25px; padding: 15px; background: #f8fcf8; border-radius: 6px; border: 1px solid #e0f0e0;">
          <label style="display: flex; align-items: flex-start; cursor: pointer; font-size: 14px; color: #333; line-height: 1.4;">
            <input
              type="checkbox"
              v-model="form.agree_terms"
              style="margin-right: 10px; margin-top: 2px; width: 16px; height: 16px; accent-color: #008000;"
            />
            <span>I agree to the <a href="#" style="color: #008000; text-decoration: none; font-weight: 500;">Terms of Service</a> and <a href="#" style="color: #008000; text-decoration: none; font-weight: 500;">Privacy Policy</a></span>
          </label>
          <div v-if="form.errors.agree_terms" style="color: #e74c3c; font-size: 12px; margin-top: 8px; display: flex; align-items: center; background: #fff5f5; padding: 8px 12px; border-radius: 4px; border-left: 3px solid #e74c3c;">
            <i class="fas fa-exclamation-circle" style="margin-right: 6px; font-size: 12px;"></i>{{ form.errors.agree_terms }}
          </div>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="form.processing"
          style="width: 100%; background: linear-gradient(135deg, #008000 0%, #00a000 100%); color: white; border: none; padding: 14px 16px; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0, 128, 0, 0.2);"
          :style="{ opacity: form.processing ? 0.7 : 1 }"
          onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(0, 128, 0, 0.3)'"
          onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 128, 0, 0.2)'"
        >
          <span v-if="form.processing">
            <i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>Creating Account...
          </span>
          <span v-else>
            Create Account
          </span>
        </button>

        <!-- Login Link -->
        <div style="text-align: center; padding-top: 20px; border-top: 1px solid #e0f0e0;">
          <p style="font-size: 14px; color: #666; margin: 0 0 15px 0;">
            Already have an account?
          </p>
          <a href="/login" style="display: inline-block; padding: 10px 24px; border: 1px solid #008000; color: #008000; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 14px; transition: all 0.3s; background: white;"
            onmouseover="this.style.backgroundColor='#008000'; this.style.color='white'; this.style.transform='translateY(-1px)'"
            onmouseout="this.style.backgroundColor='white'; this.style.color='#008000'; this.style.transform='translateY(0)'"
          >
            Sign In
          </a>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3'

const form = useForm({
  username: '',
  email: '',
  phone: '',
  national_id: '',
  password: '',
  password_confirmation: '',
  agree_terms: false
})

const submit = () => {
  form.post('/register', {
    onSuccess: () => {
      // 🔥 Redirect to Login.vue
      router.visit('/login')
    }
  })
}
</script>
