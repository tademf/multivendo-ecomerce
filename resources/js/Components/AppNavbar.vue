<template>
  <div class="main-app-container">
    <nav class="navbar">
      <h1>E-commerce</h1>

      <!-- Primary Navigation Links -->
      <ul class="primary-nav">
        <li><a href="/" class="nav-item">Home</a></li>
        <li><a href="/register" class="nav-item">SignUp</a></li>
        <li><a href="/login" class="nav-item">SignIn</a></li>
      </ul>

      <!-- Utility Links -->
      <ul class="utility-nav">
        <!-- Wishlist & Cart -->
        <li><a href="/Wish_Lists" class="icon-item" title="Wishlist">❤️</a></li>
        <li><a href="/Cart" class="icon-item" title="Cart">🛒</a></li>

        <!-- Message Icon -->
        <li>
          <a href="/messages" class="icon-item" title="Messages">✉️</a>
        </li>

        <!-- Notification Icon -->
        <li>
          <a href="/notifications" class="icon-item" title="Notifications">🔔</a>
        </li>

        <!-- Dashboard Dropdown -->
        <li class="dropdown-menu-container" @mouseleave="isDashboardDropdownOpen = false">
          <button
            @click="toggleDashboardDropdown"
            @mouseover="isDashboardDropdownOpen = true"
            class="nav-item dropdown-toggle"
          >
            Dashboard
          </button>

          <div v-if="isDashboardDropdownOpen" class="dropdown-content">
            <a href="/products" class="dropdown-item" @click="isDashboardDropdownOpen = false">Products</a>
            <a href="/orders" class="dropdown-item" @click="isDashboardDropdownOpen = false">Orders</a>
            <a href="/profile" class="dropdown-item" @click="isDashboardDropdownOpen = false">Profile</a>
          </div>
        </li>

        <!-- More Menu -->
        <li class="dropdown-menu-container more-menu" @mouseleave="isMoreDropdownOpen = false">
          <button
            @click="toggleMoreDropdown"
            @mouseover="isMoreDropdownOpen = true"
            class="icon-item dropdown-toggle more-toggle"
            title="More Options"
          >
            ...
          </button>

          <div v-if="isMoreDropdownOpen" class="dropdown-content">
            <a href="/settings" class="dropdown-item" @click="isMoreDropdownOpen = false">
              ⚙️ Settings
            </a>
            <a href="/Logout" class="dropdown-item" @click="isMoreDropdownOpen = false">
                 Logout
            </a>
          </div>
        </li>

      </ul>
    </nav>
  </div>
</template>

<script>
export default {
  name: 'AppNavbar',
  data() {
    return {
      isDashboardDropdownOpen: false,
      isMoreDropdownOpen: false
    };
  },
  methods: {
    toggleDashboardDropdown() {
      this.isDashboardDropdownOpen = !this.isDashboardDropdownOpen;
      this.isMoreDropdownOpen = false;
    },
    toggleMoreDropdown() {
      this.isMoreDropdownOpen = !this.isMoreDropdownOpen;
      this.isDashboardDropdownOpen = false;
    }
  }
};
</script>

<style>
:root {
  --bg: #ffffff;
  --text: #2c3e50;
  --navbar-bg: #e6ffe6;
  --navbar-text: #0db13e;
  --accent: #d1ffdd;
  --accent-hover: #b8f1c8;
  --card-bg: #f5f5f5;
  --border: #e0e0e0;
}

/* Base layout */
.main-app-container {
  background: var(--bg);
  color: var(--text);
  transition: background 0.4s, color 0.4s;
  font-family: 'Inter', sans-serif;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 30px;
  background: var(--navbar-bg);
  color: var(--navbar-text);
  height: 100px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.navbar ul {
  display: flex;
  list-style: none;
  gap: 20px;
  padding: 0;
  margin: 0 15px;
}

/* Links */
.nav-item {
  text-decoration: none;
  font-weight: 600;
  color: var(--navbar-text);
  padding: 6px 12px;
  border-radius: 8px;
  transition: 0.3s;
  cursor: pointer;
}

.nav-item:hover {
  background: var(--accent);
}

.icon-item {
  font-size: 1.2rem;
  background: var(--accent);
  padding: 8px;
  border-radius: 50%;
  text-decoration: none;
  color: var(--navbar-text);
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-item:hover {
  background: var(--accent-hover);
  transform: scale(1.05);
}

/* Dropdowns */
.dropdown-menu-container {
  position: relative;
}

.dropdown-content {
  position: absolute;
  right: 0;
  top: 100%;
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: 10px;
  overflow: hidden;
  min-width: 150px;
  z-index: 10;
  margin-top: 5px;
  animation: fadeInDown 0.2s forwards;
  opacity: 0;
  transform: translateY(-10px);
}

@keyframes fadeInDown {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-item {
  display: block;
  padding: 10px 15px;
  text-decoration: none;
  color: var(--text);
  background: none;
  cursor: pointer;
}

.dropdown-item:hover {
  background: var(--accent-hover);
}

/* ===================== Responsive ===================== */
@media (max-width: 768px) {
  .navbar {
    flex-wrap: wrap;
    height: auto;
    padding: 10px 15px;
  }

  .navbar h1 {
    flex-basis: 100%;
    text-align: center;
    margin-bottom: 10px;
  }

  .primary-nav,
  .utility-nav {
    gap: 8px;
    margin: 0;
  }

  .primary-nav {
    order: 2;
    width: 60%;
    justify-content: flex-start;
  }

  .utility-nav {
    order: 3;
    width: 40%;
    justify-content: flex-end;
  }
}
</style>
