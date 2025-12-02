<template>
    <div style="min-height: 100vh; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); padding: 2rem 1rem;">
        <div style="max-width: 28rem; margin: 0 auto;">
            <!-- Card Container -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); border: 1px solid #dcfce7; overflow: hidden;">
                <!-- Header -->
                <div style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); padding: 1.5rem 1.5rem;">
                    <h1 style="font-size: 1.5rem; font-weight: bold; color: white; text-align: center;">
                        Create New User
                    </h1>
                    <p style="color: #bbf7d0; text-align: center; font-size: 0.875rem; margin-top: 0.25rem;">
                        Fill in the user details below
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Username -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-size: 0.875rem; font-weight: 500; color: #374151;">
                            Username
                            <span style="color: #ef4444;">*</span>
                        </label>
                        <input
                            v-model="form.username"
                            type="text"
                            required
                            style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; transition: all 0.2s;"
                            placeholder="Enter username"
                        >
                    </div>

                    <!-- Email -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-size: 0.875rem; font-weight: 500; color: #374151;">
                            Email Address
                            <span style="color: #ef4444;">*</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; transition: all 0.2s;"
                            placeholder="Enter email address"
                        >
                    </div>

                    <!-- Password -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-size: 0.875rem; font-weight: 500; color: #374151;">
                            Password
                            <span style="color: #ef4444;">*</span>
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; transition: all 0.2s;"
                            placeholder="Enter password"
                        >
                    </div>

                    <!-- Phone -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-size: 0.875rem; font-weight: 500; color: #374151;">
                            Phone Number
                        </label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; transition: all 0.2s;"
                            placeholder="Enter phone number"
                        >
                    </div>

                    <!-- National ID -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-size: 0.875rem; font-weight: 500; color: #374151;">
                            National ID
                        </label>
                        <input
                            v-model="form.national_id"
                            type="text"
                            style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; transition: all 0.2s;"
                            placeholder="Enter national ID"
                        >
                    </div>

                    <!-- Submit Button -->
                    <div style="padding-top: 1rem;">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            style="width: 100; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; font-weight: 600; padding: 0.75rem 1rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.2s;"
                        >
                            <span v-if="form.processing">
                                Creating User...
                            </span>
                            <span v-else>
                                Create User
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    username: '',
    email: '',
    password: '',
    phone: '',
    national_id: ''
})

const submit = () => {
    form.post('/users')
}
</script>
