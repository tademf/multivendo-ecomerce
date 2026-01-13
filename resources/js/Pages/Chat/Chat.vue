<template>
  <AppLayout>
    <div class="chat-page" :class="themeClasses">
      <!-- Header -->
      <div class="chat-header border-bottom" :class="headerThemeClass">
        <div class="container">
          <div class="d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center">
              <button @click="goToConversations" class="btn btn-outline-secondary btn-sm me-3">
                <i class="fas fa-arrow-left me-1"></i> Back
              </button>
              <div>
                <h5 class="mb-0 fw-bold">Order #{{ shipment.order_number }}</h5>
                <small class="text-muted">
                  Chat with {{ otherUser?.name || 'User' }} 
                  <span v-if="otherUser?.is_verified" class="badge bg-success ms-2">
                    <i class="fas fa-check-circle me-1"></i> Verified Vendor
                  </span>
                  <span v-else-if="otherUser" class="badge bg-secondary ms-2">
                    Customer
                  </span>
                </small>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="me-3">
                <span :class="statusBadgeClass" class="badge">{{ formattedStatus }}</span>
              </div>
              <!-- Product info button -->
              <button 
                @click="toggleProductInfo" 
                class="btn btn-outline-primary btn-sm"
                title="Order Details"
              >
                <i class="fas fa-info-circle"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Chat Area -->
      <div class="chat-main">
        <div class="container-fluid">
          <div class="row">
            <!-- Chat Messages Column -->
            <div :class="showProductInfo ? 'col-lg-8' : 'col-lg-12'">
              <!-- Chat Messages Container -->
              <div class="chat-messages-container" ref="messagesContainer" :class="messagesContainerTheme">
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading messages...</span>
                  </div>
                  <p class="text-muted mt-2 small">Loading messages...</p>
                </div>

                <!-- No Messages State -->
                <div v-else-if="messages.length === 0" class="text-center py-5">
                  <div class="empty-chat-state">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h6 class="fw-bold mb-2">No messages yet</h6>
                    <p class="text-muted mb-3">Start the conversation about this order</p>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                      <button @click="sendInitialMessage" class="btn btn-primary btn-sm">
                        <i class="fas fa-comment me-1"></i> Start Chat
                      </button>
                      <button @click="toggleProductInfo" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-info-circle me-1"></i> View Order Details
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Messages List -->
                <div v-else class="messages-list">
                  <!-- Load More Button -->
                  <div v-if="hasMoreMessages" class="text-center mb-3">
                    <button @click="loadMoreMessages" class="btn btn-outline-secondary btn-sm">
                      <i class="fas fa-history me-1"></i> Load Earlier Messages
                    </button>
                  </div>

                  <!-- Messages -->
                  <div v-for="message in messages" :key="message.id" 
                       :class="['message-item', message.sender_id === currentUser.id ? 'sent' : 'received']">
                    
                    <!-- Message Avatar -->
                    <div class="message-avatar">
                      <div class="avatar-circle">
                        <span class="avatar-text">
                          {{ getInitials(message.sender?.name || 'U') }}
                        </span>
                      </div>
                    </div>

                    <!-- Message Content -->
                    <div class="message-content shadow-sm">
                      <!-- Message Header -->
                      <div class="message-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                          <strong class="me-2">{{ message.sender?.name || 'User' }}</strong>
                          <span v-if="message.sender?.is_verified" 
                                class="badge bg-success badge-sm">
                            <i class="fas fa-check-circle me-1"></i> Vendor
                          </span>
                        </div>
                        <small class="text-muted">{{ formatTime(message.created_at) }}</small>
                      </div>

                      <!-- Message Text -->
                      <div class="message-text">{{ message.message }}</div>

                      <!-- Message Footer -->
                      <div class="message-footer d-flex justify-content-between align-items-center">
                        <small class="text-muted">{{ formatDate(message.created_at) }}</small>
                        <div class="message-status">
                          <!-- UPDATED: Show read/unread icons with proper theme colors -->
                          <i v-if="message.sender_id === currentUser.id && message.is_read" 
                             class="fas fa-check-double ms-2" 
                             title="Read" :class="statusIconClass"></i>
                          <i v-else-if="message.sender_id === currentUser.id" 
                             class="fas fa-check ms-2" 
                             title="Sent" :class="statusIconClass"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- UPDATED: Message Input with new design -->
              <div class="chat-input-container border-top" :class="inputContainerTheme">
                <form @submit.prevent="sendMessage" class="message-form">
                  <div class="input-wrapper" :class="inputWrapperTheme">
                    <textarea v-model="newMessage" 
                              @keydown.enter.exact.prevent="sendMessage"
                              @keydown.shift.enter.prevent="newMessage += '\n'"
                              class="message-input" 
                              placeholder="Type your message..."
                              rows="1"
                              :disabled="!otherUser"
                              ref="messageInput"
                              style="resize: none;"
                              :class="messageInputTheme"></textarea>
                    <button type="submit" 
                            class="send-button" 
                            :disabled="!newMessage.trim() || !otherUser || sending">
                      <span v-if="sending">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                      </span>
                      <span v-else>
                        <i class="fas fa-paper-plane"></i>
                      </span>
                    </button>
                  </div>
                </form>
                <div v-if="!otherUser" class="alert alert-warning alert-sm border-0 rounded-0 m-0">
                  <div class="container">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cannot send message: Other user not found for this shipment
                  </div>
                </div>
              </div>
            </div>

            <!-- Product Info Sidebar (Collapsible) -->
            <div v-if="showProductInfo" class="col-lg-4 border-start" :class="sidebarBorderTheme">
              <div class="product-info-sidebar" :class="sidebarTheme">
                <div class="sidebar-header border-bottom p-3" :class="sidebarHeaderTheme">
                  <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Order Details</h6>
                    <button @click="toggleProductInfo" class="btn btn-close" :class="closeButtonTheme"></button>
                  </div>
                </div>

                <div class="sidebar-body p-3">
                  <!-- Order Status -->
                  <div class="mb-4">
                    <h6 class="fw-bold mb-3">Order Status</h6>
                    <div class="d-flex align-items-center">
                      <span :class="statusBadgeClass" class="badge me-2">{{ formattedStatus }}</span>
                      <span class="text-muted small">Updated: {{ formatDate(shipment.updated_at) }}</span>
                    </div>
                    
                    <!-- Status Timeline -->
                    <div class="timeline mt-3">
                      <div v-for="step in statusSteps" :key="step.status" 
                           class="timeline-step"
                           :class="{'active': isStepActive(step.status), 'completed': isStepCompleted(step.status)}">
                        <div class="timeline-dot">
                          <i :class="step.icon"></i>
                        </div>
                        <div class="timeline-content">
                          <div class="step-title small">{{ step.label }}</div>
                          <div v-if="step.status === shipment.status" class="step-time small text-muted">
                            Current
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Product Information -->
                  <div class="mb-4">
                    <h6 class="fw-bold mb-3">Product Information</h6>
                    <div class="d-flex gap-3">
                      <div class="product-image">
                        <img :src="getProductImage(shipment.product_image)" 
                             :alt="shipment.product_name"
                             class="img-fluid rounded"
                             @error="handleImageError">
                      </div>
                      <div>
                        <h6 class="fw-bold">{{ shipment.product_name || 'Product' }}</h6>
                        <p class="text-muted small mb-2">
                          Quantity: {{ shipment.quantity || 1 }} pcs
                        </p>
                        <div class="fw-bold text-primary">
                          {{ formatPrice(shipment.amount) }} Birr
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Customer Information -->
                  <div class="mb-4">
                    <h6 class="fw-bold mb-3">Shipment Address</h6>
                    <div class="p-3 rounded" :class="addressBoxTheme">
                      <div class="d-flex align-items-start">
                        <i class="fas fa-map-marker-alt text-muted me-2 mt-1"></i>
                        <span class="small">{{ shipment.shipment_address || 'No address provided' }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bootstrap Toast for Notifications -->
      <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div v-if="showToast" 
             :class="['toast align-items-center border-0', toastType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']" 
             role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              <i :class="toastType === 'success' ? 'fas fa-check-circle me-2' : 'fas fa-exclamation-circle me-2'"></i>
              {{ toastMessage }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="hideToast"></button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, onUnmounted, watchEffect } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  shipment: Object,
  messages: {
    type: Array,
    default: () => []
  },
  currentUser: Object,
  otherUser: Object,
  userType: String,
  page: Number,
  hasMore: Boolean
})

// Refs
const newMessage = ref('')
const loading = ref(false)
const sending = ref(false)
const messagesContainer = ref(null)
const messageInput = ref(null)
const showProductInfo = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
const currentPage = ref(props.page || 1)
const hasMoreMessages = ref(props.hasMore || false)
const loadingMore = ref(false)

// Dark mode
const currentTheme = ref(localStorage.getItem('theme') || 'light')

// Polling interval
let pollInterval = null

// Computed
const statusBadgeClass = computed(() => {
  const status = props.shipment.status?.toLowerCase()
  switch(status) {
    case 'pending': return 'bg-warning text-dark'
    case 'processing': return 'bg-info text-white'
    case 'shipped': return 'bg-primary text-white'
    case 'delivered': return 'bg-success text-white'
    case 'cancelled': return 'bg-danger text-white'
    default: return 'bg-secondary text-white'
  }
})

const formattedStatus = computed(() => {
  const status = props.shipment.status
  return status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Unknown'
})

// Status timeline steps
const statusSteps = [
  { status: 'pending', label: 'Order Placed', icon: 'fas fa-shopping-cart' },
  { status: 'processing', label: 'Processing', icon: 'fas fa-cog' },
  { status: 'shipped', label: 'Shipping', icon: 'fas fa-truck' },
  { status: 'delivered', label: 'Delivered', icon: 'fas fa-check-circle' }
]

// Theme computed properties
const themeClasses = computed(() => {
  return {
    'light-theme': currentTheme.value === 'light',
    'dark-theme': currentTheme.value === 'dark'
  }
})

const headerThemeClass = computed(() => {
  return currentTheme.value === 'dark' ? 'bg-dark text-light' : 'bg-white text-dark'
})

const messagesContainerTheme = computed(() => {
  return currentTheme.value === 'dark' ? 'bg-dark text-light' : 'bg-white'
})

const inputContainerTheme = computed(() => {
  return currentTheme.value === 'dark' ? 'bg-dark' : 'bg-white'
})

const inputWrapperTheme = computed(() => {
  return currentTheme.value === 'dark' ? 'dark-input-wrapper' : 'light-input-wrapper'
})

const messageInputTheme = computed(() => {
  return currentTheme.value === 'dark' ? 'dark-message-input' : 'light-message-input'
})

const sidebarBorderTheme = computed(() => {
  return currentTheme.value === 'dark' ? 'border-dark' : 'border-light'
})

const sidebarTheme = computed(() => {
  return currentTheme.value === 'dark' ? 'bg-dark text-light' : 'bg-white'
})

const sidebarHeaderTheme = computed(() => {
  return currentTheme.value === 'dark' ? 'bg-secondary text-light' : 'bg-light text-dark'
})

const addressBoxTheme = computed(() => {
  return currentTheme.value === 'dark' ? 'bg-secondary' : 'bg-light'
})

const closeButtonTheme = computed(() => {
  return currentTheme.value === 'dark' ? 'btn-close-white' : ''
})

const statusIconClass = computed(() => {
  return currentTheme.value === 'dark' ? 'text-light' : 'text-dark'
})

// Watch for theme changes
watchEffect(() => {
  const html = document.documentElement
  html.setAttribute('data-theme', currentTheme.value)
  
  // Listen for theme changes from navbar
  window.addEventListener('theme-changed', (event) => {
    currentTheme.value = event.detail.theme
  })
})

// Methods
const goToConversations = () => {
  router.visit('/messages/conversations')
}

const formatTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleTimeString([], { 
    hour: '2-digit', 
    minute: '2-digit',
    hour12: true 
  })
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatPrice = (price) => {
  if (!price) return '0'
  const num = parseFloat(price)
  return num.toLocaleString('en-US')
}

const getInitials = (name) => {
  if (!name) return 'U'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
}

const getProductImage = (imagePath) => {
  if (!imagePath) return 'https://placehold.co/400x300/e0e7ff/667eea?text=Product'
  if (imagePath.startsWith('http')) return imagePath
  if (imagePath.startsWith('storage/')) return `/${imagePath}`
  return `/storage/${imagePath}`
}

const handleImageError = (event) => {
  event.target.src = 'https://placehold.co/400x300/e0e7ff/667eea?text=Product'
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const scrollToTop = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = 0
    }
  })
}

const showNotification = (message, type = 'success') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
  
  // Auto hide after 5 seconds
  setTimeout(() => {
    showToast.value = false
  }, 5000)
}

const hideToast = () => {
  showToast.value = false
}

const toggleProductInfo = () => {
  showProductInfo.value = !showProductInfo.value
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || !props.otherUser || sending.value) return

  sending.value = true
  try {
    const response = await axios.post(route('messages.send', props.shipment.id), {
      message: newMessage.value
    }, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })

    if (response.data.success) {
      props.messages.push(response.data.message)
      newMessage.value = ''
      showNotification('Message sent successfully', 'success')
      scrollToBottom()
    }
  } catch (error) {
    console.error('Error sending message:', error)
    showNotification(error.response?.data?.error || 'Failed to send message', 'error')
  } finally {
    sending.value = false
    messageInput.value?.focus()
  }
}

const sendInitialMessage = () => {
  newMessage.value = "Hello! I'd like to discuss this order."
  messageInput.value?.focus()
}

const loadMoreMessages = async () => {
  if (loadingMore.value || !hasMoreMessages.value) return

  loadingMore.value = true
  try {
    const nextPage = currentPage.value + 1
    const response = await axios.get(route('messages.list', props.shipment.id), {
      params: { page: nextPage }
    })

    if (response.data.messages && response.data.messages.length > 0) {
      // Insert older messages at the beginning
      props.messages.unshift(...response.data.messages.reverse())
      currentPage.value = nextPage
      hasMoreMessages.value = response.data.hasMore
      
      // Keep scroll position
      nextTick(() => {
        if (messagesContainer.value) {
          const firstMessage = messagesContainer.value.querySelector('.message-item')
          if (firstMessage) {
            messagesContainer.value.scrollTop = firstMessage.offsetHeight * 10
          }
        }
      })
    }
  } catch (error) {
    console.error('Error loading more messages:', error)
    showNotification('Failed to load more messages', 'error')
  } finally {
    loadingMore.value = false
  }
}

const updateOrderStatus = () => {
  router.visit(`/orders/${props.shipment.id}`)
}

const viewOrderDetails = () => {
  router.visit(`/orders/${props.shipment.id}`)
}

const copyTrackingNumber = () => {
  if (props.shipment.tracking_number) {
    navigator.clipboard.writeText(props.shipment.tracking_number)
    showNotification('Tracking number copied to clipboard!', 'success')
  } else {
    showNotification('No tracking number available', 'error')
  }
}

// Status timeline helpers
const isStepActive = (stepStatus) => {
  return props.shipment.status === stepStatus
}

const isStepCompleted = (stepStatus) => {
  const statusOrder = ['pending', 'processing', 'shipped', 'delivered']
  const currentIndex = statusOrder.indexOf(props.shipment.status)
  const stepIndex = statusOrder.indexOf(stepStatus)
  return stepIndex <= currentIndex
}

// Poll for new messages
const pollMessages = () => {
  pollInterval = setInterval(async () => {
    try {
      const response = await axios.get(route('messages.list', props.shipment.id), {
        params: { page: 1, limit: 50 }
      })
      
      if (response.data.messages) {
        const newMessages = response.data.messages
        const currentMessageIds = new Set(props.messages.map(m => m.id))
        const incomingMessages = newMessages.filter(msg => !currentMessageIds.has(msg.id))
        
        if (incomingMessages.length > 0) {
          props.messages.push(...incomingMessages)
          scrollToBottom()
          
          // Play notification sound for new messages from others
          if (incomingMessages.some(msg => msg.sender_id !== props.currentUser.id)) {
            playNotificationSound()
          }
        }
      }
    } catch (error) {
      console.error('Error polling messages:', error)
    }
  }, 5000) // Poll every 5 seconds
}

const playNotificationSound = () => {
  // Simple notification sound
  const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-correct-answer-tone-2870.mp3')
  audio.volume = 0.3
  audio.play().catch(e => console.log('Audio play failed:', e))
}

// Auto-resize textarea
const autoResizeTextarea = () => {
  if (messageInput.value) {
    messageInput.value.style.height = 'auto'
    messageInput.value.style.height = Math.min(messageInput.value.scrollHeight, 120) + 'px'
  }
}

// Lifecycle
onMounted(() => {
  scrollToBottom()
  messageInput.value?.focus()
  pollMessages()
  
  // Get current theme
  currentTheme.value = localStorage.getItem('theme') || 'light'
  
  // Add auto-resize listener
  if (messageInput.value) {
    messageInput.value.addEventListener('input', autoResizeTextarea)
  }
})

onUnmounted(() => {
  if (pollInterval) {
    clearInterval(pollInterval)
  }
  
  // Remove auto-resize listener
  if (messageInput.value) {
    messageInput.value.removeEventListener('input', autoResizeTextarea)
  }
})
</script>

<style scoped>
/* Light theme base */
.light-theme .chat-page {
  background-color: hsl(210, 17%, 98%);
}

/* Dark theme base */
.dark-theme .chat-page {
  background-color: #0f172a;
  color: #f1f5f9;
}

/* Chat Header */
.chat-header {
  position: sticky;
  top: 0;
  z-index: 1030;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.dark-theme .chat-header {
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
  border-bottom-color: #334155 !important;
}

/* Chat Main Area */
.chat-main {
  padding: 0px 0;
}

/* Chat Messages Container */
.chat-messages-container {
  height: 65vh;
  overflow-y: auto;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.dark-theme .chat-messages-container {
  background-color: #1e293b;
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

/* Empty Chat State */
.empty-chat-state {
  padding: 60px 20px;
}

.dark-theme .empty-chat-state .text-muted {
  color: #94a3b8 !important;
}

/* Messages List */
.messages-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Message Item */
.message-item {
  display: flex;
  max-width: 85%;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.message-item.sent {
  align-self: flex-end;
  flex-direction: row-reverse;
}

.message-item.received {
  align-self: flex-start;
}

/* Message Avatar */
.message-avatar {
  margin: 0 12px;
}

.avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.light-theme .avatar-circle {
  background: linear-gradient(135deg, #667eea, #764ba2);
}

.dark-theme .avatar-circle {
  background: linear-gradient(135deg, #475569, #334155);
}

.avatar-text {
  color: white;
  font-size: 14px;
  font-weight: 600;
}

/* Message Content */
.message-content {
  border-radius: 15px;
  padding: 15px;
  max-width: 100%;
}

.light-theme .message-content {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  color: #212529;
}

.dark-theme .message-content {
  background-color: #2d3748;
  border: 1px solid #475569;
  color: #f1f5f9;
}

.message-item.sent .message-content {
  border-bottom-right-radius: 5px;
  border: none;
}

.light-theme .message-item.sent .message-content {
  background: linear-gradient(135deg, #0d6efd, #0b5ed7);
  color: white;
}

.dark-theme .message-item.sent .message-content {
  background: linear-gradient(135deg, #1e40af, #1e3a8a);
  color: white;
}

.message-item.received .message-content {
  border-bottom-left-radius: 5px;
}

.light-theme .message-item.received .message-content {
  background-color: white;
  color: #212529;
}

.dark-theme .message-item.received .message-content {
  background-color: #374151;
  color: #f1f5f9;
}

/* Message Header */
.message-header {
  margin-bottom: 8px;
}

/* Message Text */
.message-text {
  line-height: 1.5;
  word-wrap: break-word;
  white-space: pre-wrap;
  margin-bottom: 8px;
}

/* Message Footer */
.message-footer {
  font-size: 12px;
  opacity: 0.8;
}

.dark-theme .message-footer .text-muted {
  color: #94a3b8 !important;
}

/* Chat Input Container */
.chat-input-container {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.dark-theme .chat-input-container {
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
  border-top-color: #334155 !important;
}

/* Message Form */
.message-form {
  padding: 15px;
}

/* Input Wrapper */
.input-wrapper {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  border-radius: 25px;
  padding: 8px 15px;
  transition: all 0.3s ease;
}

.light-input-wrapper {
  background: white;
  border: 1px solid #dee2e6;
}

.dark-input-wrapper {
  background: #374151;
  border: 1px solid #4b5563;
}

.input-wrapper:focus-within {
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
}

/* Message Input */
.message-input {
  flex: 1;
  border: none !important;
  outline: none !important;
  background: transparent;
  font-size: 14px;
  line-height: 1.5;
  padding: 8px 0;
  min-height: 20px;
  max-height: 120px;
  resize: none;
}

.light-message-input {
  color: #212529;
}

.dark-message-input {
  color: #f1f5f9;
}

.message-input::placeholder {
  color: #6c757d;
}

.dark-theme .message-input::placeholder {
  color: #9ca3af;
}

.message-input:disabled {
  background: #f8f9fa;
  opacity: 0.6;
  cursor: not-allowed;
}

.dark-theme .message-input:disabled {
  background: #4b5563;
}

/* Send Button */
.send-button {
  width: 40px;
  height: 40px;
  min-width: 40px;
  border-radius: 50%;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.light-theme .send-button {
  background: linear-gradient(135deg, #0d6efd, #0b5ed7);
  color: white;
}

.dark-theme .send-button {
  background: linear-gradient(135deg, #1e40af, #1e3a8a);
  color: white;
}

.send-button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.dark-theme .send-button:hover:not(:disabled) {
  box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
}

.send-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.light-theme .send-button:disabled {
  background: #6c757d;
}

.dark-theme .send-button:disabled {
  background: #4b5563;
}

.send-button i {
  font-size: 16px;
}

/* Alert */
.dark-theme .alert-warning {
  background-color: #78350f;
  border-color: #92400e;
  color: #fef3c7;
}

/* Product Info Sidebar */
.product-info-sidebar {
  border-radius: 12px;
  height: calc(65vh + 100px);
  overflow-y: auto;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.dark-theme .product-info-sidebar {
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.sidebar-header {
  position: sticky;
  top: 0;
  z-index: 1020;
}

.dark-theme .sidebar-header {
  border-bottom-color: #334155 !important;
}

.product-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Timeline */
.timeline {
  position: relative;
  padding-left: 20px;
}

.light-theme .timeline::before {
  background-color: #dee2e6;
}

.dark-theme .timeline::before {
  background-color: #4b5563;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 9px;
  top: 0;
  bottom: 0;
  width: 2px;
}

.timeline-step {
  position: relative;
  margin-bottom: 20px;
}

.timeline-step:last-child {
  margin-bottom: 0;
}

.timeline-dot {
  position: absolute;
  left: -20px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  z-index: 1;
}

.light-theme .timeline-dot {
  background-color: white;
  border: 2px solid #dee2e6;
}

.dark-theme .timeline-dot {
  background-color: #374151;
  border: 2px solid #4b5563;
  color: #f1f5f9;
}

.timeline-step.completed .timeline-dot {
  background-color: #198754;
  border-color: #198754;
  color: white;
}

.timeline-step.active .timeline-dot {
  background-color: #0d6efd;
  border-color: #0d6efd;
  color: white;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
  }
}

.step-title {
  font-size: 14px;
  font-weight: 500;
}

.step-time {
  font-size: 12px;
  margin-top: 2px;
}

/* Scrollbar Styling */
.chat-messages-container::-webkit-scrollbar,
.product-info-sidebar::-webkit-scrollbar {
  width: 6px;
}

.light-theme .chat-messages-container::-webkit-scrollbar-track,
.light-theme .product-info-sidebar::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.dark-theme .chat-messages-container::-webkit-scrollbar-track,
.dark-theme .product-info-sidebar::-webkit-scrollbar-track {
  background: #334155;
}

.chat-messages-container::-webkit-scrollbar-track,
.product-info-sidebar::-webkit-scrollbar-track {
  border-radius: 3px;
}

.chat-messages-container::-webkit-scrollbar-thumb,
.product-info-sidebar::-webkit-scrollbar-thumb {
  border-radius: 3px;
}

.light-theme .chat-messages-container::-webkit-scrollbar-thumb,
.light-theme .product-info-sidebar::-webkit-scrollbar-thumb {
  background: #c1c1c1;
}

.dark-theme .chat-messages-container::-webkit-scrollbar-thumb,
.dark-theme .product-info-sidebar::-webkit-scrollbar-thumb {
  background: #6b7280;
}

.chat-messages-container::-webkit-scrollbar-thumb:hover,
.product-info-sidebar::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

.dark-theme .chat-messages-container::-webkit-scrollbar-thumb:hover,
.dark-theme .product-info-sidebar::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Responsive Design */
@media (max-width: 992px) {
  .chat-messages-container {
    height: 60vh;
  }
  
  .message-item {
    max-width: 95%;
  }
  
  .product-info-sidebar {
    height: auto;
    max-height: 60vh;
    margin-top: 20px;
  }
}

@media (max-width: 768px) {
  .chat-messages-container {
    height: 55vh;
    padding: 15px;
  }
  
  .message-content {
    padding: 12px;
  }
  
  .avatar-circle {
    width: 35px;
    height: 35px;
  }
  
  .avatar-text {
    font-size: 12px;
  }
  
  .input-wrapper {
    padding: 6px 12px;
  }
  
  .send-button {
    width: 36px;
    height: 36px;
    min-width: 36px;
  }
  
  .send-button i {
    font-size: 14px;
  }
}

@media (max-width: 576px) {
  .chat-messages-container {
    height: 50vh;
  }
  
  .message-item {
    max-width: 100%;
  }
  
  .message-form {
    padding: 10px;
  }
  
  .input-wrapper {
    border-radius: 20px;
  }
  
  .send-button {
    width: 32px;
    height: 32px;
    min-width: 32px;
  }
  
  .send-button i {
    font-size: 12px;
  }
}

/* Message status icons */
.message-status i {
  opacity: 0.7;
}

.light-theme .message-status i {
  color: #212529 !important;
}

.dark-theme .message-status i {
  color: #f1f5f9 !important;
}

.message-item.sent .message-status i {
  opacity: 0.8;
}
</style>