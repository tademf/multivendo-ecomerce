<template>
  <div class="home-page">
    <!-- Header Section -->
    <header style="background-color: #ffffff; box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08); position: sticky; top: 0; z-index: 1000;">
      <!-- Top Header -->
      <div style="background: linear-gradient(135deg, #008000 0%, #00a000 100%); color: #ffffff; padding: 8px 0; border-bottom: 1px solid #e8f5e9;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 15px; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <a href="#" style="color: #ffffff; text-decoration: none; margin-left: 20px; transition: all 0.3s ease; padding: 5px 10px; border-radius: 3px; font-size: 13px; font-weight: 500;">
              <i class="fas fa-headset" style="margin-right: 6px;"></i>Customer Service
            </a>
            <a href="#" style="color: #ffffff; text-decoration: none; margin-left: 15px; transition: all 0.3s ease; padding: 5px 10px; border-radius: 3px; font-size: 13px; font-weight: 500;">
              <i class="fas fa-question-circle" style="margin-right: 6px;"></i>Help Center
            </a>
          </div>
          <div>
  <div style="position: relative; display: inline-block;">
    <a href="/login"
       style="color: #ffffff; text-decoration: none; margin-left: 20px; transition: all 0.3s ease; padding: 5px 10px; border-radius: 3px; font-size: 13px; font-weight: 500;"
       @mouseover="showSignInTooltip = true"
       @mouseleave="showSignInTooltip = false">
      <i class="fas fa-user" style="margin-right: 6px;"></i>Sign In
    </a>

    <!-- Tooltip -->
    <div v-if="showSignInTooltip"
         style="position: absolute; bottom: -40px; left: 50%; transform: translateX(-50%); background: #ff6a00; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: 500; white-space: nowrap; z-index: 10000; box-shadow: 0 2px 10px rgba(255, 106, 0, 0.3);">
      signin here please
      <div style="position: absolute; top: -6px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-bottom: 6px solid #ff6a00;"></div>
    </div>
  </div>

  <a href="/register" style="color: #ffffff; text-decoration: none; margin-left: 15px; transition: all 0.3s ease; padding: 5px 10px; border-radius: 3px; font-size: 13px; font-weight: 500;">
    <i class="fas fa-user-plus" style="margin-right: 6px;"></i>Join Free
  </a>
  <a href="#" style="color: #ffffff; text-decoration: none; margin-left: 15px; transition: all 0.3s ease; padding: 5px 10px; border-radius: 3px; font-size: 13px; font-weight: 500;">
    <i class="fas fa-globe" style="margin-right: 6px;"></i>English <i class="fas fa-chevron-down" style="margin-left: 4px; font-size: 10px;"></i>
  </a>
</div>
        </div>
      </div>

      <!-- Main Header -->
      <div style="padding: 20px 0; background: white;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 15px; display: flex; align-items: center; justify-content: space-between;">
<!-- Extra Large Logo - Very Prominent -->
<div class="logo-container" style="display: flex; align-items: center; cursor: pointer; transition: transform 0.3s ease;">
  <div style="position: relative;">
    <img
      src="C:\xampp\php\inertia-app\image_1_1764494939092.jpg"
      alt="Ethiopian Online Market"
      style="width: 250px; height: 80px; object-fit: contain;"
      onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
    >
    <!-- Fallback logo in case image doesn't load -->
    <div style="width: 250px; height: 80px; background: linear-gradient(135deg, #008000 0%, #00c000 100%); border-radius: 12px; display: none; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(0, 128, 0, 0.4);">
      <div style="text-align: center;">
        <i class="fas fa-leaf" style="color: white; font-size: 48px; display: block; margin-bottom: 10px;"></i>
        <span style="color: white; font-size: 22px; font-weight: 900; letter-spacing: 1px;">ETHIOPIAN ONLINE MARKET</span>
      </div>
    </div>
  </div>
</div>
          <!-- Enhanced Search Bar -->
          <div style="flex: 1; max-width: 650px; margin: 0 30px; position: relative;">
            <div class="search-container" style="display: flex; background: white; border: 2px solid #e0e0e0; border-radius: 50px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; position: relative;">
              <select style="border: none; padding: 0 20px; background: #f8f9fa; color: #555; font-size: 14px; font-weight: 500; cursor: pointer; outline: none; border-right: 2px solid #e0e0e0; min-width: 140px;">
                <option>All Categories</option>
                <option>Electronics</option>
                <option>Home & Garden</option>
                <option>Fashion</option>
              </select>
              <input
                type="text"
                placeholder="What are you looking for today?"
                style="flex: 1; border: none; padding: 16px 20px; font-size: 15px; outline: none; color: #333; background: white;"
              >
              <button style="background: linear-gradient(135deg, #008000 0%, #00a000 100%); color: white; border: none; padding: 0 30px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-search" style="font-size: 16px;"></i>
                Search
              </button>
            </div>
            <div style="position: absolute; bottom: -25px; left: 20px; font-size: 12px; color: #666;">
              Popular: <span style="color: #008000; font-weight: 500; margin: 0 8px; cursor: pointer;">Smart Watch</span>
              <span style="color: #008000; font-weight: 500; margin: 0 8px; cursor: pointer;">Wireless Earbuds</span>
              <span style="color: #008000; font-weight: 500; margin: 0 8px; cursor: pointer;">LED Lights</span>
            </div>
          </div>

          <!-- Header Actions -->
          <div style="display: flex; align-items: center; gap: 25px;">
            <a @click="showAccountModal = true" class="header-action" style="display: flex; flex-direction: column; align-items: center; cursor: pointer; color: #555; text-decoration: none; transition: all 0.3s ease; padding: 8px 12px; border-radius: 10px;">
              <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 6px; border: 2px solid #e0e0e0; overflow: hidden;">
                <img v-if="userProfile.profilePicture" :src="userProfile.profilePicture" style="width: 100%; height: 100%; object-fit: cover;" />
                <i v-else class="fas fa-user-circle" style="font-size: 18px; color: #008000;"></i>
              </div>
              <span style="font-size: 12px; font-weight: 600;">Account</span>
            </a>
            <a href="#" class="header-action" style="display: flex; flex-direction: column; align-items: center; cursor: pointer; color: #555; text-decoration: none; transition: all 0.3s ease; padding: 8px 12px; border-radius: 10px;">
              <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 6px; border: 2px solid #e0e0e0; position: relative;">
                <i class="fas fa-heart" style="font-size: 18px; color: #008000;"></i>
              </div>
              <span style="font-size: 12px; font-weight: 600;">Wishlist</span>
            </a>
            <a href="#" class="header-action cart-wrapper" style="display: flex; flex-direction: column; align-items: center; cursor: pointer; color: #555; text-decoration: none; transition: all 0.3s ease; padding: 8px 12px; border-radius: 10px; position: relative;">
              <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 6px; border: 2px solid #e0e0e0;">
                <i class="fas fa-shopping-cart" style="font-size: 18px; color: #008000;"></i>
              </div>
              <span style="font-size: 12px; font-weight: 600;">Cart</span>
              <div class="cart-count" style="position: absolute; top: 5px; right: 5px; background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%); color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);">3</div>
            </a>
          </div>
        </div>
      </div>

      <!-- Enhanced Navigation -->
      <nav style="background: linear-gradient(135deg, #008000 0%, #006600 100%); border-top: 1px solid #e8f5e9;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 15px; display: flex; align-items: center;">
          <!-- Enhanced All Categories Button -->
          <div class="all-categories" style="background: linear-gradient(135deg, #ff6b00 0%, #ff8c00 100%); color: white; padding: 15px 25px; display: flex; align-items: center; cursor: pointer; font-weight: 700; position: relative; min-width: 260px; border-radius: 0 0 12px 12px; box-shadow: 0 4px 15px rgba(255, 107, 0, 0.3); transition: all 0.3s ease;">
            <i class="fas fa-th-large" style="margin-right: 12px; font-size: 18px;"></i>
            <span style="font-size: 15px; letter-spacing: 0.3px;">ALL CATEGORIES</span>
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 12px; transition: transform 0.3s ease;"></i>

            <!-- Category Dropdown Indicator -->
            <div style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid #ff8c00;"></div>
          </div>

          <ul style="display: flex; list-style: none; margin-left: 25px; gap: 10px;">
            <li v-for="(navItem, index) in navItems" :key="index">
              <a href="#" style="color: white; text-decoration: none; padding: 15px 20px; display: block; font-weight: 600; font-size: 14px; transition: all 0.3s ease; border-radius: 6px; position: relative; letter-spacing: 0.3px;">
                {{ navItem }}
                <span v-if="index === 1" style="position: absolute; top: 8px; right: 8px; background: #ff6b6b; color: white; border-radius: 10px; padding: 2px 6px; font-size: 10px; font-weight: 700;">HOT</span>
              </a>
            </li>
          </ul>

          <div style="margin-left: auto; display: flex; align-items: center; gap: 15px;">
            <a href="#" style="color: white; text-decoration: none; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px; padding: 8px 15px; border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; transition: all 0.3s ease;">
              <i class="fas fa-crown" style="color: #ffd700;"></i>
              Premium Services
            </a>
            <a href="#" style="color: white; text-decoration: none; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px; padding: 8px 15px; background: rgba(255,255,255,0.15); border-radius: 20px; transition: all 0.3s ease;">
              <i class="fas fa-bolt"></i>
              Flash Deals
            </a>
          </div>
        </div>
      </nav>
    </header>

    <!-- Account Modal -->
    <div v-if="showAccountModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 2000; padding: 20px;">
      <div style="background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2); width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto;">
        <!-- Modal Header -->
        <div style="padding: 25px 30px 20px; border-bottom: 1px solid #e8f5e9; display: flex; justify-content: space-between; align-items: center;">
          <h2 style="font-size: 24px; color: #008000; font-weight: 700; margin: 0;">Account Settings</h2>
          <button @click="showAccountModal = false" style="background: none; border: none; font-size: 20px; color: #666; cursor: pointer; padding: 5px; transition: all 0.3s ease;"
            onmouseover="this.style.color='#008000'"
            onmouseout="this.style.color='#666'">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Profile Picture Section -->
        <div style="padding: 25px 30px; border-bottom: 1px solid #e8f5e9;">
          <h3 style="font-size: 18px; color: #333; font-weight: 600; margin-bottom: 20px;">Profile Picture</h3>
          <div style="display: flex; align-items: center; gap: 20px;">
            <div style="position: relative;">
              <div style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 3px solid #e8f5e9; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                <img v-if="userProfile.profilePicture" :src="userProfile.profilePicture" style="width: 100%; height: 100%; object-fit: cover;" />
                <i v-else class="fas fa-user-circle" style="font-size: 100px; color: #008000;"></i>
              </div>
              <button @click="triggerFileInput" style="position: absolute; bottom: 5px; right: 5px; width: 32px; height: 32px; background: #008000; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; box-shadow: 0 2px 8px rgba(0, 128, 0, 0.3); transition: all 0.3s ease;"
                onmouseover="this.style.transform='scale(1.1)'"
                onmouseout="this.style.transform='scale(1)'">
                <i class="fas fa-camera"></i>
              </button>
              <input
                type="file"
                ref="fileInput"
                @change="handleProfilePictureChange"
                accept="image/*"
                style="display: none;"
              />
            </div>
            <div>
              <p style="font-size: 14px; color: #666; margin: 0 0 15px 0;">Upload a new profile picture. JPG, PNG, GIF allowed.</p>
              <button v-if="userProfile.profilePicture" @click="removeProfilePicture" style="background: none; border: 1px solid #ff6b6b; color: #ff6b6b; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;"
                onmouseover="this.style.background='#ff6b6b'; this.style.color='white'"
                onmouseout="this.style.background='none'; this.style.color='#ff6b6b'">
                Remove Picture
              </button>
            </div>
          </div>
        </div>

        <!-- Profile Information Form -->
        <div style="padding: 25px 30px;">
          <h3 style="font-size: 18px; color: #333; font-weight: 600; margin-bottom: 20px;">Profile Information</h3>
          <form @submit.prevent="updateProfile">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
              <div>
                <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">First Name</label>
                <input
                  v-model="userProfile.firstName"
                  type="text"
                  style="width: 100%; padding: 12px 15px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
                  onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
                  onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
                />
              </div>
              <div>
                <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Last Name</label>
                <input
                  v-model="userProfile.lastName"
                  type="text"
                  style="width: 100%; padding: 12px 15px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
                  onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
                  onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
                />
              </div>
            </div>

            <div style="margin-bottom: 20px;">
              <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Username</label>
              <input
                v-model="userProfile.username"
                type="text"
                style="width: 100%; padding: 12px 15px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
                onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
                onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
              />
            </div>

            <div style="margin-bottom: 20px;">
              <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Email Address</label>
              <input
                v-model="userProfile.email"
                type="email"
                style="width: 100%; padding: 12px 15px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
                onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
                onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
              />
            </div>

            <div style="margin-bottom: 20px;">
              <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Phone Number</label>
              <input
                v-model="userProfile.phone"
                type="tel"
                style="width: 100%; padding: 12px 15px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8;"
                onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
                onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
              />
            </div>

            <div style="margin-bottom: 25px;">
              <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Address</label>
              <textarea
                v-model="userProfile.address"
                rows="3"
                style="width: 100%; padding: 12px 15px; border: 1px solid #d0e0d0; border-radius: 6px; font-size: 14px; outline: none; transition: all 0.3s; box-sizing: border-box; background: #f8fcf8; resize: vertical;"
                onfocus="this.style.borderColor='#008000'; this.style.boxShadow='0 0 0 2px rgba(0, 128, 0, 0.1)'; this.style.background='white'"
                onblur="this.style.borderColor='#d0e0d0'; this.style.boxShadow='none'; this.style.background='#f8fcf8'"
              ></textarea>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
              <button
                type="button"
                @click="showAccountModal = false"
                style="padding: 12px 24px; border: 1px solid #d0e0d0; background: white; color: #666; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;"
                onmouseover="this.style.borderColor='#008000'; this.style.color='#008000'"
                onmouseout="this.style.borderColor='#d0e0d0'; this.style.color='#666'"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isUpdating"
                style="padding: 12px 24px; background: linear-gradient(135deg, #008000 0%, #00a000 100%); color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 128, 0, 0.2);"
                :style="{ opacity: isUpdating ? 0.7 : 1 }"
                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(0, 128, 0, 0.3)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 128, 0, 0.2)'"
              >
                <span v-if="isUpdating">
                  <i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>Saving...
                </span>
                <span v-else>
                  Save Changes
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- YOUR EXISTING CONTENT REMAINS EXACTLY THE SAME -->
    <!-- Main Content -->
    <main style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
      <!-- Hero Section -->
      <div style="margin-top: 20px; display: flex; gap: 20px;">
        <!-- Categories Sidebar -->
        <div style="width: 240px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); padding: 15px 0; border: 1px solid #e8f5e9;">
          <div
            v-for="category in categories"
            :key="category.id"
            class="category-item"
            @mouseover="hoverCategory = category.id"
            @mouseleave="hoverCategory = null"
            :style="{
              padding: '12px 20px',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'space-between',
              cursor: 'pointer',
              transition: 'all 0.3s ease',
              backgroundColor: hoverCategory === category.id ? '#e8f5e9' : 'transparent',
              borderLeft: hoverCategory === category.id ? '4px solid #008000' : '4px solid transparent'
            }"
          >
            <div style="display: flex; align-items: center; gap: 12px;">
              <i :class="category.icon" style="color: #008000; font-size: 16px; width: 20px;"></i>
              <span style="font-size: 14px; font-weight: 500; color: #333;">{{ category.name }}</span>
            </div>
            <i class="fas fa-chevron-right" style="color: #757575; font-size: 12px;"></i>
          </div>
        </div>

        <!-- Hero Slider -->
        <div style="flex: 1; background-color: #ffffff; border-radius: 12px; overflow: hidden; position: relative; height: 400px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);">
          <div
            v-for="(slide, index) in slides"
            :key="index"
            :style="{
              position: 'absolute',
              top: 0,
              left: 0,
              width: '100%',
              height: '100%',
              opacity: currentSlide === index ? 1 : 0,
              transition: 'opacity 1s ease',
              backgroundSize: 'cover',
              backgroundPosition: 'center',
              backgroundImage: `linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url(${slide.image})`
            }"
          >
            <div style="position: absolute; top: 50%; left: 50px; transform: translateY(-50%); color: #ffffff; max-width: 500px; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);">
              <h2 style="font-size: 36px; margin-bottom: 15px; font-weight: 700;">{{ slide.title }}</h2>
              <p style="font-size: 18px; margin-bottom: 20px; font-weight: 400;">{{ slide.description }}</p>
              <a href="#" style="display: inline-block; padding: 14px 30px; background: linear-gradient(135deg, #008000 0%, #00a000 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 128, 0, 0.3);">{{ slide.buttonText }}</a>
            </div>
          </div>
          <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px;">
            <div
              v-for="(dot, index) in slides"
              :key="index"
              :style="{
                width: '12px',
                height: '12px',
                borderRadius: '50%',
                backgroundColor: currentSlide === index ? '#ffffff' : 'rgba(255, 255, 255, 0.5)',
                cursor: 'pointer',
                transition: 'all 0.3s ease',
                transform: currentSlide === index ? 'scale(1.2)' : 'scale(1)'
              }"
              @click="setSlide(index)"
            ></div>
          </div>
        </div>
      </div>

      <!-- Quick Access -->
      <div style="margin-top: 30px; display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px;">
        <div
          v-for="item in quickAccess"
          :key="item.id"
          class="quick-item"
          @mouseover="hoverQuickItem = item.id"
          @mouseleave="hoverQuickItem = null"
          :style="{
            backgroundColor: '#ffffff',
            borderRadius: '12px',
            padding: '25px 20px',
            textAlign: 'center',
            boxShadow: '0 4px 15px rgba(0, 0, 0, 0.08)',
            transition: 'all 0.3s ease',
            cursor: 'pointer',
            transform: hoverQuickItem === item.id ? 'translateY(-5px)' : 'none',
            border: '1px solid #f0f0f0'
          }"
        >
          <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #e8f5e9 0%, #d4edda 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; border: 2px solid #00800033;">
            <i :class="item.icon" style="font-size: 24px; color: #008000;"></i>
          </div>
          <span style="font-size: 15px; font-weight: 600; color: #333;">{{ item.text }}</span>
        </div>
      </div>

      <!-- Featured Products -->
      <div style="margin: 50px 0 25px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 28px; color: #333333; position: relative; padding-bottom: 12px; font-weight: 700;">
          Featured Products
          <span style="position: absolute; bottom: 0; left: 0; width: 60px; height: 4px; background: linear-gradient(135deg, #008000 0%, #00a000 100%); border-radius: 2px;"></span>
        </h2>
        <a href="#" style="color: #008000; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border: 2px solid #008000; border-radius: 8px; transition: all 0.3s ease;">
          View All <i class="fas fa-arrow-right" style="margin-left: 5px; transition: transform 0.3s ease;"></i>
        </a>
      </div>
      <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px;">
        <div
          v-for="product in featuredProducts"
          :key="product.id"
          class="product-card"
          @mouseover="hoverProduct = product.id"
          @mouseleave="hoverProduct = null"
          :style="{
            backgroundColor: '#ffffff',
            borderRadius: '12px',
            overflow: 'hidden',
            boxShadow: '0 4px 15px rgba(0, 0, 0, 0.08)',
            transition: 'all 0.3s ease',
            transform: hoverProduct === product.id ? 'translateY(-8px)' : 'none',
            border: '1px solid #f0f0f0'
          }"
        >
          <div style="height: 180px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
            <img :src="product.image" :alt="product.title" style="max-width: 80%; max-height: 80%; transition: transform 0.3s ease;">
            <div v-if="product.tag" :style="{
              position: 'absolute',
              top: '10px',
              left: '10px',
              background: product.tag === 'Hot' ? 'linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%)' :
                       product.tag === 'New' ? 'linear-gradient(135deg, #008000 0%, #00a000 100%)' :
                       'linear-gradient(135deg, #ffa500 0%, #ffb732 100%)',
              color: '#ffffff',
              padding: '4px 12px',
              borderRadius: '20px',
              fontSize: '11px',
              fontWeight: '700',
              textTransform: 'uppercase',
              letterSpacing: '0.5px'
            }">{{ product.tag }}</div>
          </div>
          <div style="padding: 20px;">
            <div style="font-size: 14px; margin-bottom: 8px; height: 40px; overflow: hidden; line-height: 1.4; font-weight: 500; color: #333;">{{ product.title }}</div>
            <div style="font-size: 12px; color: #008000; margin-bottom: 8px; font-weight: 600;">{{ product.supplier }}</div>
            <div style="display: flex; align-items: center; margin-bottom: 12px;">
              <i v-for="n in 5" :key="n" :class="getStarClass(n, product.rating)"></i>
              <span style="font-size: 12px; color: #757575; margin-left: 8px;">({{ product.reviews }})</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between;">
              <div style="font-weight: 700; color: #008000; font-size: 18px;">${{ product.price }}</div>
              <div style="font-size: 11px; color: #757575; background: #f8f9fa; padding: 4px 8px; border-radius: 4px; font-weight: 500;">Min: {{ product.minOrder }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Categories Section -->
      <div style="margin: 50px 0 25px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 28px; color: #333333; position: relative; padding-bottom: 12px; font-weight: 700;">
          Popular Categories
          <span style="position: absolute; bottom: 0; left: 0; width: 60px; height: 4px; background: linear-gradient(135deg, #008000 0%, #00a000 100%); border-radius: 2px;"></span>
        </h2>
        <a href="#" style="color: #008000; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border: 2px solid #008000; border-radius: 8px; transition: all 0.3s ease;">
          View All <i class="fas fa-arrow-right" style="margin-left: 5px; transition: transform 0.3s ease;"></i>
        </a>
      </div>
      <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 20px;">
        <div
          v-for="category in popularCategories"
          :key="category.id"
          class="category-card"
          @mouseover="hoverCategoryCard = category.id"
          @mouseleave="hoverCategoryCard = null"
          :style="{
            backgroundColor: '#ffffff',
            borderRadius: '12px',
            padding: '25px 15px',
            textAlign: 'center',
            boxShadow: '0 4px 15px rgba(0, 0, 0, 0.08)',
            transition: 'all 0.3s ease',
            cursor: 'pointer',
            transform: hoverCategoryCard === category.id ? 'translateY(-8px)' : 'none',
            border: '1px solid #f0f0f0'
          }"
        >
          <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #e8f5e9 0%, #d4edda 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; border: 2px solid #00800033;">
            <i :class="category.icon" style="font-size: 28px; color: #008000;"></i>
          </div>
          <h3 style="font-size: 14px; font-weight: 600; color: #333; margin: 0;">{{ category.name }}</h3>
        </div>
      </div>

      <!-- Services Section -->
      <div style="margin: 50px 0; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 16px; padding: 40px 30px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); border: 1px solid #e8f5e9;">
        <div style="text-align: center; margin-bottom: 40px;">
          <h2 style="font-size: 32px; color: #333; font-weight: 700; margin-bottom: 10px;">Why Choose GreenMarket?</h2>
          <p style="font-size: 16px; color: #666; max-width: 600px; margin: 0 auto;">Experience the best B2B marketplace with premium services and global reach</p>
        </div>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px;">
          <div
            v-for="service in services"
            :key="service.id"
            style="text-align: center; padding: 25px 20px; border-radius: 12px; background: white; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06); transition: all 0.3s ease; border: 1px solid #f0f0f0;"
          >
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #e8f5e9 0%, #d4edda 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; border: 3px solid #00800033;">
              <i :class="service.icon" style="font-size: 32px; color: #008000;"></i>
            </div>
            <h3 style="font-size: 18px; margin-bottom: 12px; font-weight: 700; color: #333;">{{ service.title }}</h3>
            <p style="font-size: 14px; color: #666; line-height: 1.5;">{{ service.description }}</p>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 60px 0 20px; margin-top: 50px; border-top: 1px solid #e0e0e0;">
      <div style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 30px; margin-bottom: 40px;">
          <div v-for="column in footerColumns" :key="column.title">
            <h3 style="font-size: 18px; margin-bottom: 20px; color: #333333; position: relative; padding-bottom: 12px; font-weight: 700;">
              {{ column.title }}
              <span style="position: absolute; bottom: 0; left: 0; width: 30px; height: 3px; background: linear-gradient(135deg, #008000 0%, #00a000 100%); border-radius: 2px;"></span>
            </h3>
            <ul style="list-style: none; padding: 0;">
              <li v-for="link in column.links" :key="link.text" style="margin-bottom: 12px;">
                <a :href="link.url" style="color: #666; text-decoration: none; font-size: 14px; transition: all 0.3s ease; display: block; padding: 4px 0;">
                  {{ link.text }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div style="border-top: 1px solid #d0d0d0; padding-top: 30px; display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #666;">
          <div style="display: flex; align-items: center; gap: 20px;">
            <div>© 2024 GreenMarket. All rights reserved.</div>
            <div style="display: flex; gap: 15px;">
              <a href="#" style="color: #666; text-decoration: none; font-size: 13px;">Privacy Policy</a>
              <a href="#" style="color: #666; text-decoration: none; font-size: 13px;">Terms of Service</a>
              <a href="#" style="color: #666; text-decoration: none; font-size: 13px;">Sitemap</a>
            </div>
          </div>
          <div style="display: flex; gap: 15px;">
            <a v-for="social in socialLinks" :key="social.name" :href="social.url" style="color: #666; font-size: 18px; transition: all 0.3s ease; width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
              <i :class="social.icon"></i>
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  data() {
    return {
      showAccountModal: false,
      isUpdating: false,
      userProfile: {
        profilePicture: '',
        firstName: 'John',
        lastName: 'Doe',
        username: 'johndoe',
        email: 'john.doe@example.com',
        phone: '+251 91 234 5678',
        address: 'Addis Ababa, Ethiopia'
      },
      // ... your existing data properties remain exactly the same
      currentSlide: 0,
      hoverCategory: null,
      hoverQuickItem: null,
      hoverProduct: null,
      hoverCategoryCard: null,
      navItems: ['Home', 'Products', 'Suppliers', 'Trade Shows', 'Services', 'Help'],
      categories: [
        { id: 1, name: 'Agriculture & Food', icon: 'fas fa-seedling' },
        { id: 2, name: 'Apparel & Fashion', icon: 'fas fa-tshirt' },
        { id: 3, name: 'Automobiles & Motorcycles', icon: 'fas fa-car' },
        { id: 4, name: 'Electronics', icon: 'fas fa-laptop' },
        { id: 5, name: 'Home & Garden', icon: 'fas fa-home' },
        { id: 6, name: 'Machinery', icon: 'fas fa-cogs' },
        { id: 7, name: 'Health & Beauty', icon: 'fas fa-heartbeat' },
        { id: 8, name: 'Sports & Entertainment', icon: 'fas fa-running' },
        { id: 9, name: 'Textiles & Leather', icon: 'fas fa-cut' },
        { id: 10, name: 'Toys & Hobbies', icon: 'fas fa-gamepad' }
      ],
      slides: [
        {
          image: 'https://images.unsplash.com/photo-1607082350899-7e105aa886ae?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
          title: 'Global Sourcing Made Easy',
          description: 'Connect with verified suppliers from around the world',
          buttonText: 'Start Sourcing Now'
        },
        {
          image: 'https://images.unsplash.com/photo-1563013546-73e6835c6ab1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
          title: 'Premium Quality Products',
          description: 'Find the best products at competitive prices',
          buttonText: 'Explore Products'
        },
        {
          image: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
          title: 'Secure Trade Assurance',
          description: 'Your payments are protected until delivery is complete',
          buttonText: 'Learn More'
        }
      ],
      quickAccess: [
        { id: 1, icon: 'fas fa-shield-alt', text: 'Trade Assurance' },
        { id: 2, icon: 'fas fa-truck', text: 'Logistics' },
        { id: 3, icon: 'fas fa-search-dollar', text: 'Inspection' },
        { id: 4, icon: 'fas fa-file-invoice-dollar', text: 'Financing' },
        { id: 5, icon: 'fas fa-globe-americas', text: 'Global Sources' }
      ],
      featuredProducts: [
        {
          id: 1,
          title: 'Wireless Bluetooth Earbuds with Charging Case',
          supplier: 'Shenzhen Electronics Co.',
          rating: 4.5,
          reviews: 128,
          price: '12.50',
          minOrder: '100 pieces',
          image: 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
          tag: 'Hot'
        },
        {
          id: 2,
          title: 'Smart Watch with Heart Rate Monitor',
          supplier: 'Guangdong Tech Ltd.',
          rating: 4,
          reviews: 95,
          price: '28.00',
          minOrder: '50 pieces',
          image: 'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
        },
        {
          id: 3,
          title: 'RGB LED Strip Lights with Remote Control',
          supplier: 'Hangzhou Lighting Co.',
          rating: 5,
          reviews: 210,
          price: '8.90',
          minOrder: '200 pieces',
          image: 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
          tag: 'New'
        },
        {
          id: 4,
          title: 'Waterproof Portable Bluetooth Speaker',
          supplier: 'Dongguan Audio Ltd.',
          rating: 4.5,
          reviews: 156,
          price: '15.80',
          minOrder: '100 pieces',
          image: 'https://images.unsplash.com/photo-1605236453806-6ff36851218e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
        },
        {
          id: 5,
          title: '20000mAh Fast Charging Power Bank',
          supplier: 'Shenzhen Power Co.',
          rating: 4,
          reviews: 342,
          price: '18.50',
          minOrder: '50 pieces',
          image: 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
          tag: 'Bestseller'
        }
      ],
      popularCategories: [
        { id: 1, name: 'Consumer Electronics', icon: 'fas fa-mobile-alt' },
        { id: 2, name: 'Apparel & Fashion', icon: 'fas fa-tshirt' },
        { id: 3, name: 'Home & Garden', icon: 'fas fa-home' },
        { id: 4, name: 'Automobiles', icon: 'fas fa-car' },
        { id: 5, name: 'Machinery', icon: 'fas fa-cogs' },
        { id: 6, name: 'Health & Beauty', icon: 'fas fa-heartbeat' }
      ],
      services: [
        { id: 1, title: 'Trade Assurance', description: 'Protect your orders from payment to delivery', icon: 'fas fa-shield-alt' },
        { id: 2, title: 'Inspection Service', description: 'Ensure product quality before shipment', icon: 'fas fa-search' },
        { id: 3, title: 'Logistics Service', description: 'Door-to-door shipping solutions', icon: 'fas fa-shipping-fast' },
        { id: 4, title: 'Financial Services', description: 'Flexible payment solutions for buyers', icon: 'fas fa-hand-holding-usd' }
      ],
      footerColumns: [
        {
          title: 'Buy On GreenMarket',
          links: [
            { text: 'Payment & Security', url: '#' },
            { text: 'Shipping & Delivery', url: '#' },
            { text: 'Return & Refunds', url: '#' },
            { text: 'Buyer Central', url: '#' },
            { text: 'Supplier Discovery', url: '#' }
          ]
        },
        {
          title: 'Sell On GreenMarket',
          links: [
            { text: 'Supplier Memberships', url: '#' },
            { text: 'Learning Center', url: '#' },
            { text: 'Trade Shows', url: '#' },
            { text: 'Advertising Opportunities', url: '#' },
            { text: 'Success Stories', url: '#' }
          ]
        },
        {
          title: 'About GreenMarket',
          links: [
            { text: 'Company Info', url: '#' },
            { text: 'Investor Relations', url: '#' },
            { text: 'Careers', url: '#' },
            { text: 'Sitemap', url: '#' },
            { text: 'Affiliate Program', url: '#' }
          ]
        },
        {
          title: 'Help & Support',
          links: [
            { text: 'Help Center', url: '#' },
            { text: 'Contact Us', url: '#' },
            { text: 'Report Abuse', url: '#' },
            { text: 'Submit a Dispute', url: '#' },
            { text: 'IP Claims', url: '#' }
          ]
        },
        {
          title: 'Stay Connected',
          links: [
            { text: 'Blog', url: '#' },
            { text: 'Newsletter', url: '#' },
            { text: 'Mobile Apps', url: '#' },
            { text: 'Social Media', url: '#' },
            { text: 'Partners', url: '#' }
          ]
        }
      ],
      socialLinks: [
        { name: 'facebook', icon: 'fab fa-facebook-f', url: '#' },
        { name: 'twitter', icon: 'fab fa-twitter', url: '#' },
        { name: 'linkedin', icon: 'fab fa-linkedin-in', url: '#' },
        { name: 'instagram', icon: 'fab fa-instagram', url: '#' },
        { name: 'youtube', icon: 'fab fa-youtube', url: '#' }
      ]
    }
  },
  methods: {
    // Profile Picture Methods
    triggerFileInput() {
      this.$refs.fileInput.click();
    },

    handleProfilePictureChange(event) {
      const file = event.target.files[0];
      if (file) {
        // Validate file type
        if (!file.type.match('image.*')) {
          alert('Please select an image file (JPG, PNG, GIF)');
          return;
        }

        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
          alert('Please select an image smaller than 2MB');
          return;
        }

        // Create a preview of the image
        const reader = new FileReader();
        reader.onload = (e) => {
          this.userProfile.profilePicture = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },

    removeProfilePicture() {
      this.userProfile.profilePicture = '';
    },

    // Profile Update Method
    updateProfile() {
      this.isUpdating = true;

      // Simulate API call
      setTimeout(() => {
        // In a real application, you would send the updated profile data to your backend
        console.log('Profile updated:', this.userProfile);

        // Show success message
        alert('Profile updated successfully!');

        this.isUpdating = false;
        this.showAccountModal = false;
      }, 1500);
    },

    // Your existing methods remain exactly the same
    setSlide(index) {
      this.currentSlide = index;
    },

    getStarClass(starNumber, rating) {
      if (starNumber <= Math.floor(rating)) {
        return 'fas fa-star';
      } else if (starNumber === Math.ceil(rating) && !Number.isInteger(rating)) {
        return 'fas fa-star-half-alt';
      } else {
        return 'far fa-star';
      }
    }
  },
  mounted() {
    // Auto slide functionality
    setInterval(() => {
      this.currentSlide = (this.currentSlide + 1) % this.slides.length;
    }, 5000);
  }
}
</script>

<style>
/* Your existing styles remain exactly the same */
/* Enhanced hover effects */
.header-action:hover {
  transform: translateY(-2px);
  background: rgba(0, 128, 0, 0.05);
}

nav a:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-1px);
}

/* Search bar focus effect */
.search-container:focus-within {
  border-color: #008000 !important;
  box-shadow: 0 6px 25px rgba(0, 128, 0, 0.15) !important;
  transform: translateY(-2px);
}

/* Logo hover effect */
.logo-container:hover {
  transform: scale(1.02);
}

/* All Categories button hover */
.all-categories:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 107, 0, 0.4) !important;
}

/* Enhanced category item hover */
.category-item:hover {
  background-color: #e8f5e9 !important;
  border-left: 4px solid #008000 !important;
  padding-left: 16px !important;
}

/* Quick item hover */
.quick-item:hover {
  transform: translateY(-5px) !important;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12) !important;
}

/* Product card hover */
.product-card:hover {
  transform: translateY(-8px) !important;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
}

/* Category card hover */
.category-card:hover {
  transform: translateY(-8px) !important;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
}

/* View all button hover */
a[href="#"]:hover {
  background: #008000 !important;
  color: white !important;
  transform: translateY(-2px);
}

a[href="#"]:hover i {
  transform: translateX(3px);
}

/* Social icons hover */
footer a[href="#"]:hover {
  background: #008000 !important;
  color: white !important;
  transform: translateY(-2px);
}
</style>
