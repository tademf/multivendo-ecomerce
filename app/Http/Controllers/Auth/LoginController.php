<template>
    <div class="login-container">
        <div class="login-card">
            <!-- Back to Home Link -->
            <Link href="/" class="back-link">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 8H1M1 8L8 15M1 8L8 1" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            Back to Home
            </Link>

            <!-- Header Section -->
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your account to continue</p>
            </div>

            <!-- Error Message -->
            <div v-if="form.errors.login" class="error-message">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 8V12M12 16H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z"
                        stroke="#ff4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                {{ form.errors.login }}
            </div>

            <!-- Login Form -->
            <form @submit.prevent="submit" class="login-form">
                <!-- Username or Email Field -->
                <div class="form-group">
                    <label for="login">Username or Email</label>
                    <div class="input-container">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"
                                stroke="#008000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="12" cy="7" r="4" stroke="#008000" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <input type="text" id="login" v-model="form.login" placeholder="Enter your username or email"
                            required :class="{ 'error': form.errors.login }">
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-container">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="#008000" stroke-width="2" />
                            <path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11" stroke="#008000"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <input :type="showPassword ? 'text' : 'password'" id="password" v-model="form.password"
                            placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" @click="showPassword = !showPassword">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path v-if="showPassword"
                                    d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z"
                                    stroke="#008000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path v-if="showPassword"
                                    d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
                                    stroke="#008000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path v-if="!showPassword" d="M2 2L22 22" stroke="#008000" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path v-if="!showPassword"
                                    d="M6.71277 6.7226C3.66479 8.79527 2 12 2 12C2 12 5 19 12 19C14.0505 19 15.8174 18.2734 17.2711 17.2884M11 5.05822C11.3254 5.02013 11.6588 5 12 5C19 5 22 12 22 12C22 12 21.3083 13.3316 20 14.8335"
                                    stroke="#008000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" v-model="form.remember">
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                    <Link href="/forgot-password" class="forgot-password">Forgot password?</Link>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-btn" :disabled="form.processing">
                    <span v-if="form.processing">Signing In...</span>
                    <span v-else>Sign In</span>
                </button>

                <!-- Sign Up Link -->
                <div class="signup-link">
                    Don't have an account?
                    <Link href="/register">Sign up</Link>
                </div>
            </form>
        </div>

        <!-- Decorative Elements -->
        <div class="decorative-circle circle-1"></div>
        <div class="decorative-circle circle-2"></div>
        <div class="decorative-circle circle-3"></div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { Link, useForm } from '@inertiajs/vue3';

    const showPassword = ref(false);

    const form = useForm({
        login: '',
        password: '',
        remember: false,
    });

    const submit = () => {
        form.post('/login', {
            onFinish: () => form.reset('password'),
        });
    };
</script>

<style scoped>
    .login-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .login-card {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 128, 0, 0.1);
        width: 100%;
        max-width: 450px;
        position: relative;
        z-index: 10;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        color: #008000;
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 30px;
        transition: color 0.3s ease;
    }

    .back-link:hover {
        color: #00B300;
    }

    .login-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .login-header h1 {
        color: #008000;
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .login-header p {
        color: #666;
        font-size: 16px;
    }

    .error-message {
        background: #fff5f5;
        border: 2px solid #ff4444;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 25px;
        color: #ff4444;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        color: #333;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .input-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        z-index: 2;
    }

    .input-container input {
        width: 100%;
        padding: 15px 15px 15px 45px;
        border: 2px solid #e8f5e8;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #f8fff8;
    }

    .input-container input:focus {
        outline: none;
        border-color: #008000;
        background: white;
        box-shadow: 0 0 0 3px rgba(0, 128, 0, 0.1);
    }

    .input-container input.error {
        border-color: #ff4444;
        background: #fff8f8;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        background: none;
        border: none;
        cursor: pointer;
        color: #008000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .checkbox-container {
        display: flex;
        align-items: center;
        cursor: pointer;
        color: #666;
        font-size: 14px;
    }

    .checkbox-container input {
        display: none;
    }

    .checkmark {
        width: 18px;
        height: 18px;
        border: 2px solid #e8f5e8;
        border-radius: 4px;
        margin-right: 8px;
        position: relative;
        transition: all 0.3s ease;
    }

    .checkbox-container input:checked+.checkmark {
        background: #008000;
        border-color: #008000;
    }

    .checkbox-container input:checked+.checkmark::after {
        content: '';
        position: absolute;
        left: 5px;
        top: 2px;
        width: 4px;
        height: 8px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .forgot-password {
        color: #008000;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .forgot-password:hover {
        color: #00B300;
    }

    .login-btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #008000, #00B300);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 25px;
    }

    .login-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 128, 0, 0.3);
    }

    .login-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .signup-link {
        text-align: center;
        color: #666;
        font-size: 14px;
    }

    .signup-link a {
        color: #008000;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .signup-link a:hover {
        color: #00B300;
    }

    /* Decorative Circles */
    .decorative-circle {
        position: absolute;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(0, 128, 0, 0.1), rgba(0, 179, 0, 0.05));
        z-index: 1;
    }

    .circle-1 {
        width: 200px;
        height: 200px;
        top: -50px;
        left: -50px;
    }

    .circle-2 {
        width: 150px;
        height: 150px;
        bottom: -30px;
        right: 10%;
    }

    .circle-3 {
        width: 100px;
        height: 100px;
        top: 20%;
        right: -30px;
    }

    /* Responsive Design */
    @media (max-width: 480px) {
        .login-card {
            padding: 30px 25px;
        }

        .login-header h1 {
            font-size: 28px;
        }

        .form-options {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
    }
</style>