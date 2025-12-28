<template>
  <AppLayout>
    <div class="container-fluid py-4" style="background: #f8f9fa; min-height: 100vh;">
      <!-- Simple Header -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h1 class="h3 fw-bold mb-1">Messages</h1>
              <div class="d-flex align-items-center gap-3">
                <span class="text-muted">{{ conversations.length }} conversations</span>
                <span v-if="totalUnread > 0" class="badge bg-danger">
                  {{ totalUnread }} unread
                </span>
              </div>
            </div>
            
            <!-- Simple Search Bar -->
            <div class="search-bar">
              <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                  <i class="fas fa-search text-muted"></i>
                </span>
                <input 
                  v-model="searchQuery"
                  type="text" 
                  class="form-control border-start-0"
                  placeholder="Search conversations..."
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="container">
        <!-- Empty State -->
        <div v-if="filteredConversations.length === 0 && !searchQuery" class="text-center py-5">
          <div class="empty-icon mb-4">
            <i class="fas fa-comments fa-3x text-muted"></i>
          </div>
          <h4 class="fw-bold mb-2">No conversations yet</h4>
          <p class="text-muted mb-4">Start a conversation from your orders</p>
          <a :href="route('orders.customer')" class="btn btn-primary">
            <i class="fas fa-shopping-bag me-2"></i>View Orders
          </a>
        </div>

        <!-- Search Empty State -->
        <div v-else-if="filteredConversations.length === 0 && searchQuery" class="text-center py-5">
          <div class="search-empty-icon mb-4">
            <i class="fas fa-search fa-3x text-muted"></i>
          </div>
          <h4 class="fw-bold mb-2">No results found</h4>
          <p class="text-muted">No conversations match "{{ searchQuery }}"</p>
        </div>

        <!-- Conversations List - Simple Cards -->
        <div v-else class="row g-3">
          <div v-for="conversation in filteredConversations" :key="conversation.id" class="col-12">
            <div class="conversation-card card shadow-sm border-0" @click="openConversation(conversation)">
              <div class="card-body">
                <div class="row align-items-center">
                  <!-- Profile Picture Column -->
                  <div class="col-auto">
                    <div class="profile-picture-container position-relative">
                      <img 
                        v-if="conversation.other_user?.profile_picture" 
                        :src="formatProfilePicture(conversation.other_user.profile_picture)" 
                        :alt="conversation.other_user.name"
                        class="profile-picture"
                        @error="handleImageError"
                      >
                      <div v-else class="profile-picture-placeholder">
                        {{ getInitials(conversation.other_user?.name || 'U') }}
                      </div>
                      <!-- Unread Badge -->
                      <span v-if="conversation.unread_count > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ conversation.unread_count > 9 ? '9+' : conversation.unread_count }}
                      </span>
                    </div>
                  </div>

                  <!-- User Info Column -->
                  <div class="col">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div>
                        <h6 class="fw-bold mb-1">
                          {{ conversation.other_user?.name || 'User' }}
                          <span v-if="conversation.other_user?.is_verified" class="verified-badge ms-2">
                            <i class="fas fa-check-circle text-success"></i>
                          </span>
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                          <span class="badge bg-light text-dark">
                            {{ conversation.other_user?.is_verified ? 'Vendor' : 'Customer' }}
                          </span>
                          <span class="text-muted small">Order #{{ conversation.order_number }}</span>
                        </div>
                      </div>
                      <div class="text-end">
                        <span class="text-muted small">
                          {{ formatTime(conversation.last_message_time || conversation.created_at) }}
                        </span>
                      </div>
                    </div>

                    <!-- Product Info -->
                    <div class="mb-2">
                      <span class="fw-medium">{{ conversation.product_name }}</span>
                    </div>

                    <!-- Message Preview -->
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="message-preview">
                        <i class="fas fa-comment me-2 text-muted small"></i>
                        <span class="text-muted">
                          {{ conversation.last_message || 'No messages yet' }}
                        </span>
                      </div>
                      <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-arrow-right"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  conversations: {
    type: Array,
    default: () => []
  },
  user: {
    type: Object,
    required: true
  }
})

const searchQuery = ref('')

// Computed properties
const totalUnread = computed(() => {
  return props.conversations.reduce((sum, conv) => sum + (conv.unread_count || 0), 0)
})

const filteredConversations = computed(() => {
  if (!searchQuery.value.trim()) {
    return [...props.conversations].sort((a, b) => {
      return new Date(b.last_message_time || b.created_at) - new Date(a.last_message_time || a.created_at)
    })
  }
  
  const query = searchQuery.value.toLowerCase().trim()
  return props.conversations.filter(conv => {
    return (
      (conv.other_user?.name?.toLowerCase().includes(query)) ||
      (conv.product_name?.toLowerCase().includes(query)) ||
      (conv.order_number?.toString().includes(query)) ||
      (conv.last_message?.toLowerCase().includes(query))
    )
  }).sort((a, b) => {
    return new Date(b.last_message_time || b.created_at) - new Date(a.last_message_time || a.created_at)
  })
})

// Helper functions
const formatProfilePicture = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  if (path.startsWith('storage/')) return `/${path}`
  return `/storage/${path}`
}

const handleImageError = (event) => {
  event.target.style.display = 'none'
  // Show placeholder if image fails to load
  const placeholder = event.target.parentElement.querySelector('.profile-picture-placeholder')
  if (placeholder) placeholder.style.display = 'flex'
}

const getInitials = (name) => {
  if (!name) return 'U'
  return name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2)
}

const formatTime = (date) => {
  if (!date) return ''
  const now = new Date()
  const messageDate = new Date(date)
  const diffMs = now - messageDate
  const diffMins = Math.floor(diffMs / (1000 * 60))
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))
  
  if (diffMins < 1) return 'Just now'
  if (diffMins < 60) return `${diffMins}m ago`
  if (diffHours < 24) return `${diffHours}h ago`
  if (diffDays === 1) return 'Yesterday'
  if (diffDays < 7) return `${diffDays}d ago`
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

// Actions
const openConversation = (conversation) => {
  router.visit(route('messages.show', { shipment: conversation.id }))
}
</script>

<style scoped>
/* Simple White Background */
.conversations-page {
  background: #ffffff;
}

/* Profile Picture - VISIBLE AND CLEAR */
.profile-picture-container {
  position: relative;
}

.profile-picture {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  object-fit: cover;
  border: 2px solid #e9ecef;
  background: #f8f9fa;
}

.profile-picture-placeholder {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 1.2rem;
  border: 2px solid #e9ecef;
}

/* Conversation Card */
.conversation-card {
  border-radius: 12px;
  transition: all 0.2s ease;
  cursor: pointer;
  border: 1px solid #e9ecef;
}

.conversation-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  border-color: #dee2e6;
}

.card-body {
  padding: 1.25rem;
}

/* Verified Badge */
.verified-badge {
  font-size: 0.9rem;
}

/* Message Preview */
.message-preview {
  flex: 1;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  max-width: 70%;
}

/* Empty States */
.empty-icon, .search-empty-icon {
  opacity: 0.5;
}

.empty-icon i, .search-empty-icon i {
  font-size: 4rem;
}

/* Badge Styles */
.badge {
  font-size: 0.75rem;
  padding: 0.25em 0.6em;
}

.bg-danger {
  background-color: #dc3545 !important;
}

/* Search Bar */
.search-bar .input-group {
  border-radius: 8px;
  border: 1px solid #dee2e6;
}

.search-bar .input-group:focus-within {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.search-bar .form-control {
  border: none;
  box-shadow: none;
}

.search-bar .form-control:focus {
  box-shadow: none;
}

.search-bar .input-group-text {
  border: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .search-bar .input-group {
    width: 100% !important;
    margin-top: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .profile-picture,
  .profile-picture-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 10px;
  }
  
  .message-preview {
    max-width: 60%;
  }
}

@media (max-width: 576px) {
  .profile-picture,
  .profile-picture-placeholder {
    width: 45px;
    height: 45px;
    font-size: 1rem;
  }
  
  .conversation-card .col-auto {
    padding-right: 0.5rem;
  }
  
  .message-preview {
    max-width: 50%;
  }
}
</style>