<template>
  <AppLayout>
    <div class="promotions-page bg-white">
      <!-- Bootstrap Icons -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
      
      <!-- ========== HERO SECTION ========== -->
      <section class="bg-white py-5 border-bottom">
        <div class="container py-4">
          <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
              <span class="badge bg-warning bg-opacity-15 text-warning px-4 py-2 rounded-pill mb-4">
                <i class="bi bi-megaphone me-2"></i> HOT DEALS 🔥
              </span>
              <h1 class="display-4 fw-bold text-dark mb-3">
                Exclusive <span class="text-warning">Promotions</span>
              </h1>
              <p class="lead text-secondary mb-4">
                Discover amazing discounts and special offers from verified vendors
              </p>
              
              <!-- Search Bar -->
              <div class="mx-auto" style="max-width: 500px;">
                <div class="input-group">
                  <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-secondary"></i>
                  </span>
                  <input 
                    v-model="searchQuery" 
                    @keyup.enter="performSearch"
                    type="text" 
                    class="form-control border-start-0 ps-0" 
                    placeholder="Search promotions..."
                  >
                  <button @click="performSearch" class="btn btn-warning px-4">
                    Search
                  </button>
                </div>
              </div>
              
              <!-- Add Promotion Button -->
              <div v-if="isVerified" class="mt-4">
                <button @click="openAddPromotionModal" class="btn btn-outline-warning rounded-pill px-5 py-2">
                  <i class="bi bi-plus-circle me-2"></i> Add Promotion
                </button>
              </div>
              <div v-else-if="isLoggedIn && !isVerified" class="mt-4">
                <Link href="/verification/request" class="btn btn-outline-secondary rounded-pill px-5 py-2">
                  <i class="bi bi-patch-check me-2"></i> Get Verified to Add
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== STATS SECTION ========== -->
      <section class="py-4 bg-white">
        <div class="container">
          <div class="row g-3">
            <div class="col-md-3 col-6">
              <div class="text-center p-3">
                <i class="bi bi-fire fs-1 text-warning mb-2"></i>
                <h3 class="h2 fw-bold text-dark mb-0">{{ stats.activePromotions }}</h3>
                <p class="text-secondary mb-0">Active Deals</p>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="text-center p-3">
                <i class="bi bi-tags fs-1 text-warning mb-2"></i>
                <h3 class="h2 fw-bold text-dark mb-0">{{ stats.categories }}</h3>
                <p class="text-secondary mb-0">Categories</p>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="text-center p-3">
                <i class="bi bi-clock fs-1 text-warning mb-2"></i>
                <h3 class="h2 fw-bold text-dark mb-0">{{ stats.todayDeals }}</h3>
                <p class="text-secondary mb-0">Today's Deals</p>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="text-center p-3">
                <i class="bi bi-percent fs-1 text-warning mb-2"></i>
                <h3 class="h2 fw-bold text-dark mb-0">{{ stats.avgDiscount }}%</h3>
                <p class="text-secondary mb-0">Avg. Discount</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== FEATURED PROMOTIONS ========== -->
      <section v-if="featured?.length > 0" class="py-5 bg-light">
        <div class="container">
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
              <h2 class="h3 fw-bold text-dark mb-1">Featured Deals</h2>
              <p class="text-secondary mb-0">Hand-picked promotions just for you</p>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary rounded-circle" @click="prevSlide">
                <i class="bi bi-chevron-left"></i>
              </button>
              <button class="btn btn-outline-secondary rounded-circle" @click="nextSlide">
                <i class="bi bi-chevron-right"></i>
              </button>
            </div>
          </div>

          <div class="row g-4">
            <div v-for="promo in featured" :key="promo.id" class="col-lg-4 col-md-6">
              <div class="card h-100 border-0 shadow-sm">
                <!-- VIDEO/IMAGE DISPLAY SECTION -->
                <div class="position-relative bg-light" style="height: 220px; border-radius: 0.75rem 0.75rem 0 0; overflow: hidden;">
                  
                  <!-- Video Player -->
                  <video 
                    v-if="promo.video_url"
                    :src="promo.video_url" 
                    class="w-100 h-100"
                    style="object-fit: cover;"
                    controls
                  >
                    <source :src="promo.video_url" type="video/mp4">
                    Your browser does not support the video tag.
                  </video>
                  
                  <!-- Image -->
                  <img 
                    v-else-if="promo.image_url"
                    :src="promo.image_url" 
                    :alt="promo.description"
                    class="w-100 h-100"
                    style="object-fit: cover;"
                  >
                  
                  <!-- No Media Placeholder -->
                  <div v-else class="w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-image fs-1 text-secondary opacity-50 mb-2"></i>
                    <span class="small text-secondary">No image</span>
                  </div>
                  
                  <!-- Featured Badge -->
                  <span class="position-absolute top-0 start-0 m-3 badge bg-warning text-dark rounded-pill px-3 py-2">
                    <i class="bi bi-star-fill me-1"></i> Featured
                  </span>
                  
                  <!-- Expiry Badge -->
                  <span class="position-absolute top-0 end-0 m-3 badge bg-white text-dark rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-clock me-1 text-warning"></i> {{ promo.formatted_expiry_date || 'Limited' }}
                  </span>
                </div>
                
                <div class="card-body p-4">
                  <div class="d-flex align-items-center mb-3">
                    <span class="small text-secondary">
                      <i class="bi bi-person-circle me-1"></i> {{ promo.user_name || 'Vendor' }}
                    </span>
                  </div>
                  <p class="text-dark mb-3">{{ truncateText(promo.description, 100) }}</p>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-warning">Special Offer</span>
                    <button @click="viewPromotion(promo.id)" class="btn btn-link text-warning p-0">
                      View Deal <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== ACTIVE PROMOTIONS BANNER ========== -->
      <section v-if="isVerified && activeUserPromotions?.length > 0" class="py-4">
        <div class="container">
          <div class="alert alert-success border-0 rounded-4 p-4 mb-0">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center">
                <div class="bg-success rounded-circle p-3 me-3">
                  <i class="bi bi-megaphone fs-4 text-white"></i>
                </div>
                <div>
                  <h5 class="fw-bold text-success mb-1">You have {{ activeUserPromotions.length }} active promotion(s)</h5>
                  <p class="text-secondary small mb-0">Your promotions are live and visible to customers</p>
                </div>
              </div>
              <Link href="/vendor/promotions" class="btn btn-success rounded-pill px-4 py-2">
                Manage <i class="bi bi-arrow-right ms-1"></i>
              </Link>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== ALL PROMOTIONS GRID ========== -->
      <section class="py-5 bg-white">
        <div class="container">
          <!-- Header -->
          <div class="row mb-4">
            <div class="col-md-8">
              <h2 class="h3 fw-bold text-dark mb-1">All Promotions</h2>
              <p class="text-secondary mb-0">
                Showing {{ promotions?.from || 0 }} - {{ promotions?.to || 0 }} of {{ promotions?.total || 0 }} deals
              </p>
            </div>
            <div class="col-md-4">
              <div class="d-flex justify-content-md-end align-items-center gap-2">
                <span class="text-secondary small">Sort by:</span>
                <select v-model="sortBy" @change="sortPromotions" class="form-select w-auto border-0 bg-light rounded-pill px-4 py-2">
                  <option value="latest">Latest</option>
                  <option value="oldest">Oldest</option>
                  <option value="expiring">Expiring Soon</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-warning" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-secondary mt-3">Loading promotions...</p>
          </div>

          <!-- Promotions Grid -->
          <div v-else-if="promotions?.data?.length > 0" class="row g-4">
            <div v-for="promo in promotions.data" :key="promo.id" class="col-xl-3 col-lg-4 col-md-6">
              <div class="card h-100 border-0 shadow-sm">
                
                <!-- VIDEO/IMAGE DISPLAY SECTION -->
                <div class="position-relative bg-light" style="height: 200px; border-radius: 0.75rem 0.75rem 0 0; overflow: hidden;">
                  
                  <!-- Video Player -->
                  <video 
                    v-if="promo.video_url"
                    :src="promo.video_url" 
                    class="w-100 h-100"
                    style="object-fit: cover;"
                    controls
                  >
                    <source :src="promo.video_url" type="video/mp4">
                    Your browser does not support the video tag.
                  </video>
                  
                  <!-- Image -->
                  <img 
                    v-else-if="promo.image_url"
                    :src="promo.image_url" 
                    :alt="promo.description"
                    class="w-100 h-100"
                    style="object-fit: cover;"
                  >
                  
                  <!-- No Media Placeholder -->
                  <div v-else class="w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-image fs-1 text-secondary opacity-25 mb-2"></i>
                    <span class="small text-secondary">No image</span>
                  </div>
                  
                  <!-- Your Deal Badge -->
                  <span v-if="promo.user_id === userId" class="position-absolute top-0 start-0 m-3 badge bg-info text-white rounded-pill px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i> Your Deal
                  </span>
                </div>
                
                <div class="card-body p-3">
                  <div class="d-flex align-items-center mb-2">
                    <span class="small text-secondary">
                      <i class="bi bi-person-circle me-1"></i> {{ promo.user_name || 'Vendor' }}
                    </span>
                  </div>
                  <p class="text-dark small mb-3">{{ truncateText(promo.description, 80) }}</p>
                  <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="small text-secondary">
                      <i class="bi bi-calendar3 me-1 text-warning"></i> {{ promo.formatted_published_date || 'Date not set' }}
                    </span>
                    <button @click="viewPromotion(promo.id)" class="btn btn-sm btn-link text-warning p-0">
                      View <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else-if="!loading" class="text-center py-5">
            <div class="py-5">
              <i class="bi bi-tag display-1 text-secondary opacity-25"></i>
              <h3 class="fw-bold text-dark mt-4">No Promotions Found</h3>
              <p class="text-secondary mb-4">Check back later for exclusive deals and offers</p>
              <button @click="resetFilters" class="btn btn-outline-warning rounded-pill px-5 py-2">
                <i class="bi bi-arrow-repeat me-2"></i> Clear Filters
              </button>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="promotions?.last_page > 1" class="d-flex justify-content-center mt-5">
            <nav>
              <ul class="pagination">
                <li class="page-item" :class="{ disabled: promotions.current_page === 1 }">
                  <button class="page-link" @click="changePage(promotions.current_page - 1)">
                    <i class="bi bi-chevron-left"></i>
                  </button>
                </li>
                
                <li v-for="page in displayedPages" :key="page" class="page-item" 
                    :class="{ active: page === promotions.current_page, disabled: page === '...' }">
                  <button v-if="page !== '...'" class="page-link" @click="changePage(page)">{{ page }}</button>
                  <span v-else class="page-link">{{ page }}</span>
                </li>
                
                <li class="page-item" :class="{ disabled: promotions.current_page === promotions.last_page }">
                  <button class="page-link" @click="changePage(promotions.current_page + 1)">
                    <i class="bi bi-chevron-right"></i>
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </section>

      <!-- ========== ADD PROMOTION MODAL - FIXED WITH PREVIEWS ========== -->
      <div class="modal fade" id="addPromotionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-white border-0">
              <h5 class="modal-title fw-bold">
                <i class="bi bi-megaphone me-2"></i> Create New Promotion
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" @click="closeModal"></button>
            </div>
            
            <form @submit.prevent="submitPromotion">
              <div class="modal-body p-4">
                <div class="row g-4">
                  
                  <!-- Description -->
                  <div class="col-12">
                    <label class="form-label fw-semibold text-dark">
                      <i class="bi bi-chat-text me-1 text-warning"></i> Description <span class="text-danger">*</span>
                    </label>
                    <textarea 
                      v-model="promotionForm.description" 
                      class="form-control bg-light border-0 rounded-3 p-3" 
                      rows="3"
                      placeholder="Describe your promotion, discount, or special offer..."
                      required
                    ></textarea>
                  </div>

                  <!-- Duration & Payment -->
                  <div class="col-12">
                    <label class="form-label fw-semibold text-dark">
                      <i class="bi bi-currency-dollar me-1 text-warning"></i> Duration & Payment <span class="text-danger">*</span>
                    </label>
                    <div class="row g-2">
                      <div class="col-md-8">
                        <select 
                          v-model="promotionForm.duration" 
                          class="form-select bg-light border-0 rounded-3 py-2" 
                          required
                          @change="calculateExpiryDate"
                        >
                          <option value="">Select Duration</option>
                          <option value="1">1 Day - 500 Birr</option>
                          <option value="7">1 Week - 1,500 Birr</option>
                          <option value="14">2 Weeks - 2,000 Birr</option>
                          <option value="21">3 Weeks - 2,500 Birr</option>
                          <option value="30">1 Month - 3,000 Birr</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <div class="bg-light rounded-3 p-3 text-center h-100 d-flex align-items-center justify-content-center">
                          <div>
                            <span class="text-secondary small d-block">Total</span>
                            <span class="fw-bold fs-4 text-warning">{{ formatPrice(selectedPrice) }} Birr</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="mt-3">
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="paymentConfirm" v-model="promotionForm.paymentConfirmed" required>
                        <label class="form-check-label small text-secondary" for="paymentConfirm">
                          I confirm payment of <strong class="text-warning">{{ formatPrice(selectedPrice) }} Birr</strong> for {{ getDurationText(promotionForm.duration) }}
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- Payment Proof - Required -->
                  <div class="col-12">
                    <label class="form-label fw-semibold text-dark">
                      <i class="bi bi-receipt me-1 text-warning"></i> Payment Proof <span class="text-danger">*</span>
                    </label>
                    <div class="bg-light rounded-3 p-3">
                      <input 
                        @change="handlePaymentProofUpload" 
                        type="file" 
                        accept="image/*" 
                        class="form-control bg-transparent border-0 p-0"
                        required
                      >
                      <small class="text-secondary">Upload payment receipt (Max 5MB)</small>
                      <div v-if="promotionForm.payment_proof_preview" class="mt-2">
                        <img :src="promotionForm.payment_proof_preview" class="img-fluid rounded-3" style="max-height: 80px;">
                      </div>
                    </div>
                  </div>

                  <!-- Promotion Image - Optional - WITH PREVIEW -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">
                      <i class="bi bi-image me-1 text-warning"></i> Promotion Image
                    </label>
                    <div class="bg-light rounded-3 p-3">
                      <input 
                        @change="handleImageUpload" 
                        type="file" 
                        accept="image/*" 
                        class="form-control bg-transparent border-0 p-0"
                      >
                      <small class="text-secondary">Max 5MB</small>
                      <div v-if="promotionForm.image_preview" class="mt-2">
                        <img :src="promotionForm.image_preview" class="img-fluid rounded-3" style="max-height: 80px;">
                      </div>
                    </div>
                  </div>

                  <!-- Promotion Video - Optional - WITH PREVIEW -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">
                      <i class="bi bi-camera-reel me-1 text-warning"></i> Promotion Video
                    </label>
                    <div class="bg-light rounded-3 p-3">
                      <input 
                        @change="handleVideoUpload" 
                        type="file" 
                        accept="video/*" 
                        class="form-control bg-transparent border-0 p-0"
                      >
                      <small class="text-secondary">
                        <i class="bi bi-info-circle me-1"></i> Max 500MB (MP4, MOV, AVI, MKV)
                      </small>
                      
                      <!-- VIDEO PREVIEW - FIXED -->
                      <div v-if="promotionForm.video_preview" class="mt-3">
                        <video 
                          :src="promotionForm.video_preview" 
                          class="w-100 rounded-3"
                          style="max-height: 150px; object-fit: cover;"
                          controls
                        >
                          Your browser does not support the video tag.
                        </video>
                        <div class="d-flex align-items-center mt-2">
                          <i class="bi bi-file-play me-2 text-warning fs-5"></i>
                          <span class="small text-secondary">{{ promotionForm.video?.name || 'Video ready' }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Expiry Date Display -->
                  <div v-if="promotionForm.expired_at" class="col-12">
                    <div class="alert alert-info bg-opacity-10 border-0 rounded-3">
                      <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-check fs-4 me-3 text-info"></i>
                        <div>
                          <strong>Expires on:</strong>
                          <span class="d-block mt-1 fw-bold">{{ formatDate(promotionForm.expired_at) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Terms -->
                  <div class="col-12">
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="termsConfirm" v-model="promotionForm.termsConfirmed" required>
                      <label class="form-check-label small text-secondary" for="termsConfirm">
                        I agree to the <a href="#" class="text-warning">Terms and Conditions</a> and understand my promotion needs admin approval
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" @click="closeModal">
                  Cancel
                </button>
                <button 
                  type="submit" 
                  class="btn btn-warning rounded-pill px-5 fw-bold" 
                  :disabled="submitting || !canSubmit"
                >
                  <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                  <span v-else><i class="bi bi-check-circle me-2"></i> Submit Promotion</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, onMounted, watch, onUnmounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { Modal } from 'bootstrap';

const props = defineProps({
  promotions: { type: Object, default: () => ({ data: [], current_page: 1, last_page: 1, total: 0 }) },
  featured: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  auth: { type: Object, default: () => ({}) }
});

// ========== AUTH ==========
const isLoggedIn = computed(() => !!props.auth?.user);
const isVerified = computed(() => props.auth?.user?.is_verified || false);
const userId = computed(() => props.auth?.user?.id || null);

// ========== STATE ==========
const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const sortBy = ref(props.filters.sort || 'latest');
const loading = ref(false);
const currentSlide = ref(0);
const activeUserPromotions = ref([]);

// ========== FORM ==========
const promotionForm = ref({
  description: '',
  duration: '',
  image: null,
  video: null,
  payment_proof: null,
  paymentConfirmed: false,
  termsConfirmed: false,
  image_preview: null,
  payment_proof_preview: null,
  video_preview: null,
  published_at: '',
  expired_at: ''
});

const submitting = ref(false);
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ========== PRICES ==========
const priceMap = { '1': 500, '7': 1500, '14': 2000, '21': 2500, '30': 3000 };
const selectedPrice = computed(() => priceMap[promotionForm.value.duration] || 0);

const canSubmit = computed(() => {
  return promotionForm.value.description &&
         promotionForm.value.duration &&
         promotionForm.value.paymentConfirmed &&
         promotionForm.value.termsConfirmed &&
         promotionForm.value.payment_proof;
});

// ========== MODAL ==========
let modalInstance = null;

// ========== CLEANUP PREVIEW URLS ==========
const cleanupPreviewUrls = () => {
  if (promotionForm.value.image_preview) {
    URL.revokeObjectURL(promotionForm.value.image_preview);
  }
  if (promotionForm.value.payment_proof_preview) {
    URL.revokeObjectURL(promotionForm.value.payment_proof_preview);
  }
  if (promotionForm.value.video_preview) {
    URL.revokeObjectURL(promotionForm.value.video_preview);
  }
};

// ========== FILE HANDLERS - FIXED FOR LOCAL PREVIEW ==========
const handleImageUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  
  if (file.size > 5 * 1024 * 1024) {
    alert('Image size must be less than 5MB');
    return;
  }
  
  // Clean up old preview
  if (promotionForm.value.image_preview) {
    URL.revokeObjectURL(promotionForm.value.image_preview);
  }
  
  promotionForm.value.image = file;
  promotionForm.value.image_preview = URL.createObjectURL(file);
  console.log('✅ Image preview created');
};

const handlePaymentProofUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  
  if (file.size > 5 * 1024 * 1024) {
    alert('Payment proof must be less than 5MB');
    return;
  }
  
  if (promotionForm.value.payment_proof_preview) {
    URL.revokeObjectURL(promotionForm.value.payment_proof_preview);
  }
  
  promotionForm.value.payment_proof = file;
  promotionForm.value.payment_proof_preview = URL.createObjectURL(file);
};

const handleVideoUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  
  if (file.size > 500 * 1024 * 1024) {
    alert('Video size must be less than 500MB');
    return;
  }
  
  if (promotionForm.value.video_preview) {
    URL.revokeObjectURL(promotionForm.value.video_preview);
  }
  
  promotionForm.value.video = file;
  promotionForm.value.video_preview = URL.createObjectURL(file);
  console.log('✅ Video preview created');
};

// ========== OPEN MODAL ==========
const openAddPromotionModal = () => {
  cleanupPreviewUrls();
  
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const seconds = String(now.getSeconds()).padStart(2, '0');
  
  promotionForm.value = {
    description: '',
    duration: '',
    image: null,
    video: null,
    payment_proof: null,
    paymentConfirmed: false,
    termsConfirmed: false,
    image_preview: null,
    payment_proof_preview: null,
    video_preview: null,
    published_at: `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`,
    expired_at: ''
  };
  
  modalInstance = new Modal(document.getElementById('addPromotionModal'));
  modalInstance.show();
};

// ========== CLOSE MODAL ==========
const closeModal = () => {
  cleanupPreviewUrls();
  if (modalInstance) {
    modalInstance.hide();
  }
};

// ========== DATE CALCULATION ==========
const calculateExpiryDate = () => {
  if (!promotionForm.value.duration) return;
  
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const seconds = String(now.getSeconds()).padStart(2, '0');
  
  promotionForm.value.published_at = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
  
  const days = parseInt(promotionForm.value.duration);
  const expiryDate = new Date(now);
  expiryDate.setDate(expiryDate.getDate() + days);
  
  const expYear = expiryDate.getFullYear();
  const expMonth = String(expiryDate.getMonth() + 1).padStart(2, '0');
  const expDay = String(expiryDate.getDate()).padStart(2, '0');
  const expHours = String(expiryDate.getHours()).padStart(2, '0');
  const expMinutes = String(expiryDate.getMinutes()).padStart(2, '0');
  const expSeconds = String(expiryDate.getSeconds()).padStart(2, '0');
  
  promotionForm.value.expired_at = `${expYear}-${expMonth}-${expDay} ${expHours}:${expMinutes}:${expSeconds}`;
};

// ========== UTILITIES ==========
const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
};

const getDurationText = (days) => {
  const map = { '1':'1 Day', '7':'1 Week', '14':'2 Weeks', '21':'3 Weeks', '30':'1 Month' };
  return map[days] || `${days} Days`;
};

const formatPrice = (price) => {
  if (!price) return '0';
  return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
};

const truncateText = (text, len) => {
  if (!text) return '';
  return text.length > len ? text.substring(0, len) + '...' : text;
};

// ========== SUBMIT ==========
const submitPromotion = async () => {
  if (!canSubmit.value) {
    alert('Please complete all required fields');
    return;
  }

  submitting.value = true;
  
  try {
    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('description', promotionForm.value.description);
    formData.append('duration', promotionForm.value.duration);
    formData.append('published_at', promotionForm.value.published_at);
    formData.append('expired_at', promotionForm.value.expired_at);
    
    if (promotionForm.value.image) {
      formData.append('image', promotionForm.value.image);
    }
    
    if (promotionForm.value.video) {
      formData.append('video', promotionForm.value.video);
    }
    
    if (promotionForm.value.payment_proof) {
      formData.append('payment_proof', promotionForm.value.payment_proof);
    }

    await axios.post('/vendor/promotions', formData, {
      headers: { 
        'Content-Type': 'multipart/form-data',
        'X-CSRF-TOKEN': csrfToken 
      }
    });

    alert('✅ Promotion submitted successfully! Pending admin approval.');
    closeModal();
    submitting.value = false;
    router.visit('/promotions', { preserveState: false });
    
  } catch (error) {
    console.error('Submission error:', error);
    if (error.response?.data?.message) {
      alert(error.response.data.message);
    } else {
      alert('Failed to submit promotion. Please try again.');
    }
    submitting.value = false;
  }
};

// ========== FETCH ACTIVE PROMOTIONS ==========
const fetchActivePromotions = async () => {
  if (!isVerified.value) return;
  try {
    const res = await axios.get('/vendor/promotions/active');
    activeUserPromotions.value = res.data || [];
  } catch (e) {
    activeUserPromotions.value = [];
  }
};

// ========== STATS ==========
const stats = computed(() => ({
  activePromotions: props.promotions?.total || 0,
  categories: props.categories?.length || 0,
  todayDeals: Math.floor((props.promotions?.total || 0) * 0.2) || 0,
  avgDiscount: 35
}));

// ========== PAGINATION ==========
const displayedPages = computed(() => {
  if (!props.promotions?.last_page) return [];
  const current = props.promotions.current_page || 1;
  const last = props.promotions.last_page || 1;
  if (last <= 5) return Array.from({ length: last }, (_, i) => i + 1);
  if (current <= 3) return [1, 2, 3, '...', last];
  if (current >= last - 2) return [1, '...', last - 2, last - 1, last];
  return [1, '...', current - 1, current, current + 1, '...', last];
});

// ========== ROUTER METHODS ==========
const performSearch = () => router.get('/promotions', { 
  search: searchQuery.value, 
  sort: sortBy.value 
}, { preserveState: true });

const sortPromotions = () => router.get('/promotions', { 
  sort: sortBy.value,
  search: searchQuery.value 
}, { preserveState: true });

const viewPromotion = (id) => router.visit(`/promotions/${id}`);

const changePage = (page) => {
  if (page && page !== '...') {
    router.get('/promotions', { 
      page,
      search: searchQuery.value,
      sort: sortBy.value
    }, { preserveState: true });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const resetFilters = () => {
  searchQuery.value = '';
  sortBy.value = 'latest';
  router.get('/promotions', {}, { preserveState: true });
};

const nextSlide = () => {
  if (currentSlide.value < (props.featured?.length || 0) - 3) {
    currentSlide.value++;
  }
};

const prevSlide = () => {
  if (currentSlide.value > 0) {
    currentSlide.value--;
  }
};

// ========== WATCH ==========
watch(() => isVerified.value, (v) => {
  if (v) fetchActivePromotions();
});

// ========== CLEANUP ON UNMOUNT ==========
onUnmounted(() => {
  cleanupPreviewUrls();
});

// ========== MOUNTED ==========
onMounted(() => {
  const now = new Date();
  const y = now.getFullYear();
  const m = String(now.getMonth() + 1).padStart(2, '0');
  const d = String(now.getDate()).padStart(2, '0');
  const h = String(now.getHours()).padStart(2, '0');
  const min = String(now.getMinutes()).padStart(2, '0');
  const sec = String(now.getSeconds()).padStart(2, '0');
  
  promotionForm.value.published_at = `${y}-${m}-${d} ${h}:${min}:${sec}`;
  
  if (isVerified.value) {
    fetchActivePromotions();
  }
});
</script>

<style scoped>
.bg-opacity-15 {
  --bs-bg-opacity: 0.15;
}

.bg-opacity-10 {
  --bs-bg-opacity: 0.1;
}

.bg-opacity-25 {
  --bs-bg-opacity: 0.25;
}

/* Card Hover Effect */
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-radius: 0.75rem;
  overflow: hidden;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05) !important;
}

/* Video Player */
video {
  background-color: #000;
  border-radius: 0.75rem 0.75rem 0 0;
}

/* Modal */
.modal-content {
  border-radius: 1rem;
  overflow: hidden;
}

.modal-header {
  padding: 1.25rem 1.5rem;
}

.modal-footer {
  padding: 1.25rem 1.5rem;
}

/* Form Controls */
.form-control:focus,
.form-select:focus {
  border-color: #ffc107;
  box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.1);
}

.form-check-input:checked {
  background-color: #ffc107;
  border-color: #ffc107;
}

/* Pagination */
.pagination .page-link {
  color: #6c757d;
  border: none;
  padding: 0.5rem 1rem;
  margin: 0 0.25rem;
  border-radius: 0.5rem;
}

.pagination .page-item.active .page-link {
  background-color: #ffc107;
  color: #000;
}

.pagination .page-item.disabled .page-link {
  background-color: transparent;
  color: #adb5bd;
}

/* Responsive */
@media (max-width: 768px) {
  .display-4 {
    font-size: 2rem;
  }
  
  .modal-dialog {
    margin: 0.5rem;
  }
}
</style>