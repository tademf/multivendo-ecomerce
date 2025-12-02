<template>
    <div :style="containerStyle">
        <!-- Background Decoration -->
        <div :style="backgroundDecorationStyle">
            <div :style="floatingCircle1Style"></div>
            <div :style="floatingCircle2Style"></div>
            <div :style="floatingCircle3Style"></div>
        </div>

        <!-- Main Card -->
        <div :style="cardContainerStyle">
            <!-- Header Section -->
            <div :style="headerStyle">
                <div :style="iconContainerStyle">
                    <svg :style="iconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <h1 :style="titleStyle">Create New User</h1>
                <p :style="subtitleStyle">Add a new member to your organization</p>
            </div>

            <!-- Progress Steps -->
            <div :style="progressContainerStyle">
                <div :style="progressStepStyle">
                    <div :style="progressNumberActiveStyle">1</div>
                    <span :style="progressTextActiveStyle">Basic Info</span>
                </div>
                <div :style="progressLineStyle"></div>
                <div :style="progressStepStyle">
                    <div :style="progressNumberStyle">2</div>
                    <span :style="progressTextStyle">Contact Details</span>
                </div>
                <div :style="progressLineStyle"></div>
                <div :style="progressStepStyle">
                    <div :style="progressNumberStyle">3</div>
                    <span :style="progressTextStyle">Security</span>
                </div>
            </div>

            <!-- Form Section -->
            <form @submit.prevent="submit" :style="formStyle">
                <!-- Basic Information Section -->
                <div :style="sectionStyle">
                    <h3 :style="sectionTitleStyle">
                        <svg :style="sectionIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Basic Information
                    </h3>

                    <div :style="gridStyle">
                        <!-- Username Field -->
                        <div :style="fieldContainerStyle">
                            <label :style="labelStyle">
                                Username
                                <span :style="requiredStyle">*</span>
                            </label>
                            <div :style="inputWrapperStyle">
                                <input
                                    v-model="form.username"
                                    type="text"
                                    required
                                    :style="inputStyle"
                                    placeholder="Enter unique username"
                                    @focus="focusField('username')"
                                    @blur="blurField('username')"
                                >
                                <svg :style="inputIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div :style="helperTextStyle">Choose a unique username for the user</div>
                        </div>

                        <!-- Email Field -->
                        <div :style="fieldContainerStyle">
                            <label :style="labelStyle">
                                Email Address
                                <span :style="requiredStyle">*</span>
                            </label>
                            <div :style="inputWrapperStyle">
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    :style="inputStyle"
                                    placeholder="user@example.com"
                                    @focus="focusField('email')"
                                    @blur="blurField('email')"
                                >
                                <svg :style="inputIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div :style="helperTextStyle">User's primary email address</div>
                        </div>
                    </div>
                </div>

                <!-- Contact Details Section -->
                <div :style="sectionStyle">
                    <h3 :style="sectionTitleStyle">
                        <svg :style="sectionIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Contact Details
                    </h3>

                    <div :style="gridStyle">
                        <!-- Phone Field -->
                        <div :style="fieldContainerStyle">
                            <label :style="labelStyle">Phone Number</label>
                            <div :style="inputWrapperStyle">
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    :style="inputStyle"
                                    placeholder="+1 (555) 123-4567"
                                    @focus="focusField('phone')"
                                    @blur="blurField('phone')"
                                >
                                <svg :style="inputIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div :style="helperTextStyle">Optional contact number</div>
                        </div>

                        <!-- National ID Field -->
                        <div :style="fieldContainerStyle">
                            <label :style="labelStyle">National ID</label>
                            <div :style="inputWrapperStyle">
                                <input
                                    v-model="form.national_id"
                                    type="text"
                                    :style="inputStyle"
                                    placeholder="Enter national identification"
                                    @focus="focusField('national_id')"
                                    @blur="blurField('national_id')"
                                >
                                <svg :style="inputIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div :style="helperTextStyle">Government issued ID number</div>
                        </div>
                    </div>
                </div>

                <!-- Security Section -->
                <div :style="sectionStyle">
                    <h3 :style="sectionTitleStyle">
                        <svg :style="sectionIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Security
                    </h3>

                    <!-- Password Field -->
                    <div :style="fieldContainerStyle">
                        <label :style="labelStyle">
                            Password
                            <span :style="requiredStyle">*</span>
                        </label>
                        <div :style="inputWrapperStyle">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                :style="inputStyle"
                                placeholder="Create strong password"
                                @focus="focusField('password')"
                                @blur="blurField('password')"
                            >
                            <svg :style="inputIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                :style="passwordToggleStyle"
                            >
                                <svg :style="eyeIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path v-if="showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <div :style="passwordStrengthStyle">
                            <div :style="passwordMeterStyle">
                                <div :style="getPasswordStrengthBarStyle()"></div>
                            </div>
                            <span :style="passwordStrengthTextStyle">{{ getPasswordStrengthText() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div :style="actionsStyle">
                    <button
                        type="button"
                        :style="cancelButtonStyle"
                        @click="resetForm"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        :style="submitButtonStyle"
                    >
                        <span v-if="form.processing" :style="buttonLoadingStyle">
                            <svg :style="spinnerStyle" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"
                                        stroke-dasharray="32" stroke-dashoffset="32">
                                    <animate attributeName="stroke-dashoffset" dur="1s" values="32;0" fill="freeze" repeatCount="indefinite"/>
                                </circle>
                            </svg>
                            Creating User...
                        </span>
                        <span v-else>
                            <svg :style="buttonIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create User
                        </span>
                    </button>
                </div>
            </form>

            <!-- Success Message -->
            <div v-if="form.recentlySuccessful" :style="successMessageStyle">
                <svg :style="successIconStyle" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                User created successfully! Redirecting...
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const showPassword = ref(false)
const focusedField = ref('')

const form = useForm({
    username: '',
    email: '',
    password: '',
    phone: '',
    national_id: ''
})

const focusField = (field) => {
    focusedField.value = field
}

const blurField = (field) => {
    if (focusedField.value === field) {
        focusedField.value = ''
    }
}

const getPasswordStrength = computed(() => {
    const password = form.password
    if (!password) return 0

    let strength = 0
    if (password.length >= 8) strength += 1
    if (/[A-Z]/.test(password)) strength += 1
    if (/[a-z]/.test(password)) strength += 1
    if (/[0-9]/.test(password)) strength += 1
    if (/[^A-Za-z0-9]/.test(password)) strength += 1

    return Math.min(strength, 5)
})

const getPasswordStrengthText = () => {
    const strength = getPasswordStrength.value
    const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong']
    return texts[strength]
}

const getPasswordStrengthBarStyle = () => {
    const strength = getPasswordStrength.value
    const colors = ['#ef4444', '#f97316', '#eab308', '#84cc16', '#22c55e', '#16a34a']
    const widths = ['20%', '40%', '60%', '80%', '90%', '100%']

    return {
        width: widths[strength],
        background: `linear-gradient(90deg, ${colors[strength]}, ${colors[Math.min(strength + 1, 5)]})`,
        height: '6px',
        borderRadius: '3px',
        transition: 'all 0.3s ease',
        boxShadow: `0 0 10px ${colors[strength]}40`
    }
}

const submit = () => {
    form.post('/users', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            setTimeout(() => {
                // Optional: Redirect or show success message
            }, 2000)
        }
    })
}

const resetForm = () => {
    form.reset()
    focusedField.value = ''
    showPassword.value = false
}

// Inline CSS Styles
const containerStyle = {
    minHeight: '100vh',
    background: 'linear-gradient(135deg, #f0fdf4 0%, #ffffff 50%, #ecfdf5 100%)',
    padding: '2rem 1rem',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
    position: 'relative',
    overflow: 'hidden'
}

const backgroundDecorationStyle = {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    overflow: 'hidden',
    pointerEvents: 'none'
}

const floatingCircle1Style = {
    position: 'absolute',
    top: '10%',
    left: '5%',
    width: '120px',
    height: '120px',
    background: 'linear-gradient(135deg, #86efac40 0%, #4ade8040 100%)',
    borderRadius: '50%',
    filter: 'blur(20px)',
    animation: 'float 6s ease-in-out infinite'
}

const floatingCircle2Style = {
    position: 'absolute',
    top: '60%',
    right: '8%',
    width: '80px',
    height: '80px',
    background: 'linear-gradient(135deg, #bbf7d040 0%, #22c55e40 100%)',
    borderRadius: '50%',
    filter: 'blur(15px)',
    animation: 'float 8s ease-in-out infinite 1s'
}

const floatingCircle3Style = {
    position: 'absolute',
    bottom: '15%',
    left: '15%',
    width: '60px',
    height: '60px',
    background: 'linear-gradient(135deg, #dcfce740 0%, #16a34a40 100%)',
    borderRadius: '50%',
    filter: 'blur(12px)',
    animation: 'float 7s ease-in-out infinite 0.5s'
}

const cardContainerStyle = {
    background: 'white',
    borderRadius: '20px',
    boxShadow: '0 20px 60px rgba(34, 197, 94, 0.15)',
    padding: 0,
    width: '100%',
    maxWidth: '580px',
    position: 'relative',
    zIndex: 1,
    border: '1px solid #dcfce7',
    overflow: 'hidden'
}

const headerStyle = {
    background: 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)',
    padding: '2.5rem 2rem',
    textAlign: 'center',
    color: 'white',
    position: 'relative'
}

const iconContainerStyle = {
    width: '80px',
    height: '80px',
    background: 'rgba(255, 255, 255, 0.2)',
    borderRadius: '50%',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    margin: '0 auto 1rem',
    backdropFilter: 'blur(10px)',
    border: '2px solid rgba(255, 255, 255, 0.3)'
}

const iconStyle = {
    width: '40px',
    height: '40px',
    color: 'white'
}

const titleStyle = {
    fontSize: '2rem',
    fontWeight: '700',
    margin: '0 0 0.5rem 0',
    background: 'linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%)',
    WebkitBackgroundClip: 'text',
    WebkitTextFillColor: 'transparent',
    backgroundClip: 'text'
}

const subtitleStyle = {
    fontSize: '1.1rem',
    opacity: 0.9,
    margin: 0,
    fontWeight: '400'
}

const progressContainerStyle = {
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    padding: '1.5rem 2rem',
    background: '#f8fff8',
    borderBottom: '1px solid #dcfce7'
}

const progressStepStyle = {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    gap: '0.5rem'
}

const progressNumberActiveStyle = {
    width: '40px',
    height: '40px',
    borderRadius: '50%',
    background: 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)',
    color: 'white',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    fontWeight: '600',
    fontSize: '1rem',
    boxShadow: '0 4px 12px rgba(34, 197, 94, 0.3)'
}

const progressNumberStyle = {
    width: '40px',
    height: '40px',
    borderRadius: '50%',
    background: '#e5e7eb',
    color: '#6b7280',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    fontWeight: '600',
    fontSize: '1rem'
}

const progressTextActiveStyle = {
    fontSize: '0.875rem',
    fontWeight: '600',
    color: '#16a34a'
}

const progressTextStyle = {
    fontSize: '0.875rem',
    fontWeight: '500',
    color: '#6b7280'
}

const progressLineStyle = {
    width: '60px',
    height: '2px',
    background: '#e5e7eb',
    margin: '0 1rem'
}

const formStyle = {
    padding: '2rem'
}

const sectionStyle = {
    marginBottom: '2rem',
    padding: '1.5rem',
    background: '#f8fff8',
    borderRadius: '12px',
    border: '1px solid #dcfce7'
}

const sectionTitleStyle = {
    fontSize: '1.25rem',
    fontWeight: '600',
    color: '#15803d',
    margin: '0 0 1.5rem 0',
    display: 'flex',
    alignItems: 'center',
    gap: '0.75rem'
}

const sectionIconStyle = {
    width: '20px',
    height: '20px'
}

const gridStyle = {
    display: 'grid',
    gridTemplateColumns: '1fr 1fr',
    gap: '1.5rem'
}

const fieldContainerStyle = {
    marginBottom: '1.5rem'
}

const labelStyle = {
    display: 'block',
    fontSize: '0.875rem',
    fontWeight: '600',
    color: '#374151',
    marginBottom: '0.5rem'
}

const requiredStyle = {
    color: '#ef4444'
}

const inputWrapperStyle = {
    position: 'relative',
    display: 'flex',
    alignItems: 'center'
}

const inputStyle = {
    width: '100%',
    padding: '0.75rem 1rem 0.75rem 2.5rem',
    border: '1px solid #d1d5db',
    borderRadius: '8px',
    fontSize: '1rem',
    transition: 'all 0.2s ease',
    background: 'white',
    outline: 'none'
}

const inputIconStyle = {
    position: 'absolute',
    left: '0.75rem',
    width: '1.25rem',
    height: '1.25rem',
    color: '#9ca3af',
    pointerEvents: 'none'
}

const helperTextStyle = {
    fontSize: '0.75rem',
    color: '#6b7280',
    marginTop: '0.25rem'
}

const passwordToggleStyle = {
    position: 'absolute',
    right: '0.75rem',
    background: 'none',
    border: 'none',
    cursor: 'pointer',
    padding: '0.25rem',
    borderRadius: '4px',
    color: '#6b7280',
    transition: 'color 0.2s ease'
}

const eyeIconStyle = {
    width: '1.25rem',
    height: '1.25rem'
}

const passwordStrengthStyle = {
    display: 'flex',
    alignItems: 'center',
    gap: '1rem',
    marginTop: '0.5rem'
}

const passwordMeterStyle = {
    flex: 1,
    height: '6px',
    background: '#e5e7eb',
    borderRadius: '3px',
    overflow: 'hidden'
}

const passwordStrengthTextStyle = {
    fontSize: '0.75rem',
    fontWeight: '500',
    minWidth: '60px'
}

const actionsStyle = {
    display: 'flex',
    gap: '1rem',
    justifyContent: 'flex-end',
    marginTop: '2rem',
    paddingTop: '1.5rem',
    borderTop: '1px solid #e5e7eb'
}

const cancelButtonStyle = {
    padding: '0.75rem 1.5rem',
    border: '1px solid #d1d5db',
    background: 'white',
    color: '#374151',
    borderRadius: '8px',
    fontSize: '1rem',
    fontWeight: '500',
    cursor: 'pointer',
    transition: 'all 0.2s ease'
}

const submitButtonStyle = {
    padding: '0.75rem 2rem',
    background: 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)',
    color: 'white',
    border: 'none',
    borderRadius: '8px',
    fontSize: '1rem',
    fontWeight: '600',
    cursor: 'pointer',
    transition: 'all 0.2s ease',
    boxShadow: '0 4px 12px rgba(34, 197, 94, 0.3)',
    display: 'flex',
    alignItems: 'center',
    gap: '0.5rem'
}

const buttonLoadingStyle = {
    display: 'flex',
    alignItems: 'center',
    gap: '0.5rem'
}

const spinnerStyle = {
    width: '1.25rem',
    height: '1.25rem',
    animation: 'spin 1s linear infinite'
}

const buttonIconStyle = {
    width: '1.25rem',
    height: '1.25rem'
}

const successMessageStyle = {
    position: 'absolute',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
    background: 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)',
    color: 'white',
    padding: '1.5rem 2rem',
    borderRadius: '12px',
    display: 'flex',
    alignItems: 'center',
    gap: '0.75rem',
    fontWeight: '600',
    boxShadow: '0 10px 30px rgba(34, 197, 94, 0.4)',
    zIndex: 10,
    animation: 'slideIn 0.3s ease-out'
}

const successIconStyle = {
    width: '1.5rem',
    height: '1.5rem'
}
</script>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translate(-50%, -40%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

input:focus {
    border-color: #22c55e !important;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1) !important;
}

button:hover {
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
}
</style>
