<template>
  <AppLayout>
    <div class="news-page bg-light">
      <!-- Bootstrap Icons CDN -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
      
      <!-- Hero Section - Simple & Clean -->
      <section class="bg-white py-5">
        <div class="container py-4">
          <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
              <!-- Simple Badge -->
              <!-- <span class="badge bg-warning bg-opacity-15 text-warning px-3 py-2 rounded-pill mb-4">
                <i class="bi bi-newspaper me-1"></i> LATEST NEWS
              </span> -->
              
              <!-- Clean Title -->
              <h1 class="display-4 fw-bold text-dark mb-3">
                News & <span class="text-warning">Updates</span>
              </h1>
              
              <!-- Simple Subtitle -->
              <p class="lead text-secondary mb-4">
                Stay informed with the latest news and announcements
              </p>
              
              <!-- Simple Search Bar -->
              <!-- <div class="mx-auto" style="max-width: 500px;">
                <div class="input-group">
                  <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-secondary"></i>
                  </span>
                  <input 
                    type="text" 
                    v-model="searchQuery" 
                    @keyup.enter="performSearch"
                    class="form-control border-start-0 ps-0" 
                    placeholder="Search news..."
                  >
                  <button 
                    @click="performSearch"
                    class="btn btn-warning px-4"
                  >
                    Search
                  </button>
                </div>
              </div> -->
            </div>
          </div>
        </div>
      </section>

      <!-- Stats Section - Simple Cards -->
      <!-- <section class="py-4 bg-white border-bottom">
        <div class="container">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="card border-0 bg-light p-3">
                <div class="d-flex align-items-center justify-content-center">
                  <i class="bi bi-newspaper fs-1 text-warning me-3"></i>
                  <div class="text-start">
                    <h3 class="h2 fw-bold text-dark mb-0">{{ totalNews }}</h3>
                    <p class="text-secondary mb-0">Articles</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-0 bg-light p-3">
                <div class="d-flex align-items-center justify-content-center">
                  <i class="bi bi-calendar-check fs-1 text-warning me-3"></i>
                  <div class="text-start">
                    <h3 class="h2 fw-bold text-dark mb-0">{{ thisMonthNews }}</h3>
                    <p class="text-secondary mb-0">This Month</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-0 bg-light p-3">
                <div class="d-flex align-items-center justify-content-center">
                  <i class="bi bi-people fs-1 text-warning me-3"></i>
                  <div class="text-start">
                    <h3 class="h2 fw-bold text-dark mb-0">{{ readersCount }}K+</h3>
                    <p class="text-secondary mb-0">Readers</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section> -->

      <!-- News Grid Section -->
      <section class="py-5">
        <div class="container">
          <!-- Section Header - Simple -->
          <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
              <div>
                <h2 class="h3 fw-bold text-dark mb-1">Recent Articles</h2>
                <p class="text-secondary mb-0">Stay updated with our latest news</p>
              </div>
              <a href="/news" class="btn btn-outline-warning rounded-pill px-4">
                View All <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-warning" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-secondary mt-3">Loading news...</p>
          </div>

          <!-- News Grid -->
          <div v-else>
            <!-- Featured Article -->
            <div v-if="news.data && news.data.length > 0" class="row mb-4">
              <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden">
                  <div class="row g-0">
                    <div class="col-lg-6">
                      <img 
                        :src="news.data[0].image || 'https://placehold.co/800x600/f8f9fa/6c757d?text=News'"
                        :alt="news.data[0].title"
                        class="img-fluid w-100 h-100 object-fit-cover"
                        style="min-height: 300px;"
                      >
                    </div>
                    <div class="col-lg-6">
                      <div class="card-body p-4 p-lg-5">
                        <div class="d-flex align-items-center mb-3">
                          <span class="badge bg-warning text-white me-2">Featured</span>
                          <small class="text-secondary">
                            <i class="bi bi-calendar3 me-1"></i> {{ news.data[0].published_at }}
                          </small>
                        </div>
                        <h3 class="h2 fw-bold text-dark mb-3">{{ news.data[0].title }}</h3>
                        <p class="text-secondary mb-4">{{ news.data[0].excerpt }}</p>
                        <button 
                          @click="viewNews(news.data[0].id)"
                          class="btn btn-warning rounded-pill px-4"
                        >
                          Read Article <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Regular Articles Grid -->
            <div class="row g-4">
              <div v-for="item in regularNews" :key="item.id" class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                  <img 
                    :src="item.image || 'https://placehold.co/600x400/f8f9fa/6c757d?text=News'"
                    :alt="item.title"
                    class="card-img-top"
                    style="height: 200px; object-fit: cover;"
                  >
                  <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-2">
                      <small class="text-secondary">
                        <i class="bi bi-clock me-1"></i> {{ timeAgo(item.published_at) }}
                      </small>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-3">
                      <a href="#" @click.prevent="viewNews(item.id)" class="text-decoration-none text-dark">
                        {{ truncateTitle(item.title, 60) }}
                      </a>
                    </h5>
                    <p class="card-text text-secondary small mb-3">
                      {{ truncateText(item.excerpt, 100) }}
                    </p>
                    <button 
                      @click="viewNews(item.id)"
                      class="btn btn-link text-warning p-0 text-decoration-none"
                    >
                      Read More <i class="bi bi-arrow-right"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="!loading && (!news.data || news.data.length === 0)" class="text-center py-5">
            <div class="py-5">
              <i class="bi bi-newspaper display-1 text-secondary opacity-50"></i>
              <h3 class="fw-bold text-dark mt-4">No News Found</h3>
              <p class="text-secondary">Check back later for new articles.</p>
              <button @click="resetFilters" class="btn btn-warning rounded-pill px-4 mt-3">
                <i class="bi bi-arrow-repeat me-1"></i> Refresh
              </button>
            </div>
          </div>

          <!-- Simple Pagination -->
          <div v-if="news.last_page > 1" class="row mt-5">
            <div class="col-12">
              <nav aria-label="News pagination">
                <ul class="pagination justify-content-center">
                  <li class="page-item" :class="{ disabled: news.current_page === 1 }">
                    <button class="page-link" @click="changePage(news.current_page - 1)">
                      <i class="bi bi-chevron-left"></i>
                    </button>
                  </li>
                  
                  <li v-for="page in displayedPages" :key="page" class="page-item" 
                      :class="{ 
                        active: page === news.current_page,
                        disabled: page === '...'
                      }">
                    <button v-if="page !== '...'" class="page-link" @click="changePage(page)">
                      {{ page }}
                    </button>
                    <span v-else class="page-link">{{ page }}</span>
                  </li>
                  
                  <li class="page-item" :class="{ disabled: news.current_page === news.last_page }">
                    <button class="page-link" @click="changePage(news.current_page + 1)">
                      <i class="bi bi-chevron-right"></i>
                    </button>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </section>

      <!-- Simple Newsletter Section -->
      <section class="py-5 bg-white border-top">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
              <div class="py-4">
                <i class="bi bi-envelope-paper fs-1 text-warning mb-3"></i>
                <h3 class="fw-bold text-dark mb-2">Stay Updated</h3>
                <p class="text-secondary mb-4">Get the latest news delivered to your inbox</p>
                <div class="mx-auto" style="max-width: 400px;">
                  <div class="input-group">
                    <input type="email" class="form-control" placeholder="Your email address">
                    <button class="btn btn-warning px-4">
                      Subscribe
                    </button>
                  </div>
                  <small class="text-secondary d-block mt-2">We respect your privacy. Unsubscribe anytime.</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  news: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
});

const searchQuery = ref(props.filters.search || '');
const loading = ref(false);

// Computed
const regularNews = computed(() => {
  return props.news.data ? props.news.data.slice(1) : [];
});

const totalNews = computed(() => props.news.total || 0);
const thisMonthNews = computed(() => Math.floor(totalNews.value * 0.3) || 5);
const readersCount = computed(() => Math.floor(totalNews.value * 1.5) || 12);

const displayedPages = computed(() => {
  const current = props.news.current_page;
  const last = props.news.last_page;
  
  if (last <= 5) {
    return Array.from({ length: last }, (_, i) => i + 1);
  }
  
  if (current <= 3) {
    return [1, 2, 3, '...', last];
  }
  
  if (current >= last - 2) {
    return [1, '...', last - 2, last - 1, last];
  }
  
  return [1, '...', current - 1, current, current + 1, '...', last];
});

// Methods
const performSearch = () => {
  router.get('/news', { search: searchQuery.value }, {
    preserveState: true,
    preserveScroll: true
  });
};

const viewNews = (id) => {
  router.visit(`/news/${id}`);
};

const changePage = (page) => {
  if (page && page !== '...') {
    router.get('/news', { page }, {
      preserveState: true,
      preserveScroll: true
    });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const resetFilters = () => {
  searchQuery.value = '';
  router.get('/news', {}, {
    preserveState: true,
    preserveScroll: true
  });
};

const timeAgo = (date) => {
  const days = Math.floor((new Date() - new Date(date)) / (1000 * 60 * 60 * 24));
  if (days === 0) return 'Today';
  if (days === 1) return 'Yesterday';
  if (days < 7) return `${days} days ago`;
  if (days < 30) return `${Math.floor(days / 7)} weeks ago`;
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const truncateTitle = (title, length) => {
  return title.length > length ? title.substring(0, length) + '...' : title;
};

const truncateText = (text, length) => {
  return text.length > length ? text.substring(0, length) + '...' : text;
};
</script>

<style scoped>
.bg-opacity-15 {
  --bs-bg-opacity: 0.15;
}

.object-fit-cover {
  object-fit: cover;
}

.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}

.pagination .page-link {
  color: #6c757d;
  border: none;
  padding: 0.5rem 1rem;
  margin: 0 0.25rem;
  border-radius: 0.375rem;
}

.pagination .page-item.active .page-link {
  background-color: #ffc107;
  color: #000;
}

.pagination .page-item.disabled .page-link {
  background-color: transparent;
  color: #adb5bd;
}

@media (max-width: 768px) {
  .display-4 {
    font-size: 2rem;
  }
}
</style>