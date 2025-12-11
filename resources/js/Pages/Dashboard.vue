<template>
  <div class="dashboard">
    <h1>Welcome to Dashboard</h1>
    <p v-if="$page.props.auth.user">Hello, {{ $page.props.auth.user.full_name }}!</p>
    <p v-if="$page.props.auth.user">Email: {{ $page.props.auth.user.email }}</p>
    
    <div class="quick-links">
      <Link :href="route('products.index')" class="btn">My Products</Link>
      <Link :href="route('shipments.index')" class="btn">My Shipments</Link>
      <Link :href="route('profile.index')" class="btn">My Profile</Link>
      <Link :href="route('wishlist.index')" class="btn">My Wishlist</Link>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.dashboard {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

h1 {
  color: #333;
  margin-bottom: 10px;
}

p {
  color: #666;
  margin-bottom: 5px;
}

.quick-links {
  display: flex;
  gap: 15px;
  margin-top: 30px;
  flex-wrap: wrap;
}

.btn {
  padding: 12px 24px;
  background: #6366f1;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.3s ease;
  border: none;
  cursor: pointer;
  display: inline-block;
}

.btn:hover {
  background: #4f46e5;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}
</style>