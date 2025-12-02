<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">Edit User</h1>
        <form @submit.prevent="submit">
            <div class="mb-2">
                <label>Username:</label>
                <input v-model="form.username" class="border p-2 w-full">
            </div>

            <div class="mb-2">
                <label>Email:</label>
                <input v-model="form.email" type="email" class="border p-2 w-full">
            </div>

            <div class="mb-2">
                <label>Phone:</label>
                <input v-model="form.phone" class="border p-2 w-full">
            </div>

            <div class="mb-2">
                <label>National ID:</label>
                <input v-model="form.national_id" class="border p-2 w-full">
            </div>

            <button class="bg-blue-500 text-white p-2 rounded">Update</button>
        </form>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({ user: Object })

const form = useForm({
    username: props.user.username,
    email: props.user.email,
    phone: props.user.phone,
    national_id: props.user.national_id
})

const submit = () => {
    form.put(`/users/${props.user.id}`)
}
</script>
