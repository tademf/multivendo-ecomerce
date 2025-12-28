<template>
  <AppLayout>
    <div class="chat-page">
      <!-- Header -->
      <div class="chat-header">
        <div class="container">
          <div class="d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center">
              <button @click="$inertia.visit(route('orders.customer'))" class="btn btn-light me-3">
                <i class="fas fa-arrow-left"></i>
              </button>
              <div>
                <h5 class="mb-0 fw-bold">Order #{{ shipment.order_number }}</h5>
                <small class="text-muted">
                  Chat with {{ otherUser?.name || 'User' }} 
                  <span v-if="otherUser?.is_verified" class="badge bg-success ms-2">
                    <i class="fas fa-check-circle"></i> Verified Vendor
                  </span>
                  <span v-else-if="otherUser" class="badge bg-secondary ms-2">
                    Customer
                  </span>
                </small>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="me-3">
                <span class="badge bg-primary">{{ shipment.status }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Chat Container -->
      <div class="chat-container">
        <div class="container">
          <div class="row">
            <!-- Chat Messages -->
            <div class="col-lg-12">
              <div class="chat-messages-container" ref="messagesContainer">
                <!-- Loading -->
                <div v-if="loading" class="text-center py-5">
                  <div class="spinner-border text-primary"></div>
                </div>

                <!-- No Messages -->
                <div v-else-if="messages.length === 0" class="text-center py-5">
                  <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                  <h6>No messages yet</h6>
                  <p class="text-muted">Start the conversation about this order</p>
                </div>

                <!-- Messages List -->
                <div v-else class="messages-list">
                  <div v-for="message in messages" :key="message.id" 
                       :class="['message-item', message.sender_id === currentUser.id ? 'sent' : 'received']">
                    <div class="message-avatar">
                      <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="message-content">
                      <div class="message-header">
                        <strong>{{ message.sender?.name || 'User' }}</strong>
                        <small class="text-muted ms-2">{{ formatTime(message.created_at) }}</small>
                        <span v-if="message.sender?.is_verified" class="badge bg-success badge-sm ms-2">
                          <i class="fas fa-check-circle"></i> Vendor
                        </span>
                      </div>
                      <div class="message-text">{{ message.message }}</div>
                      <div class="message-footer">
                        <small class="text-muted">{{ formatDate(message.created_at) }}</small>
                        <i v-if="message.sender_id === currentUser.id && message.is_read" 
                           class="fas fa-check-double text-success ms-2"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Message Input -->
              <div class="chat-input-container">
                <form @submit.prevent="sendMessage" class="input-group">
                  <textarea v-model="newMessage" 
                            @keydown.enter.exact.prevent="sendMessage"
                            @keydown.enter.shift.exact.prevent="newMessage += '\n'"
                            class="form-control" 
                            placeholder="Type your message..."
                            rows="2"
                            :disabled="!otherUser"
                            ref="messageInput"></textarea>
                  <button type="submit" class="btn btn-primary" :disabled="!newMessage.trim() || !otherUser">
                    <i class="fas fa-paper-plane"></i> Send
                  </button>
                </form>
                <div v-if="!otherUser" class="alert alert-warning mt-2 mb-0 small">
                  <i class="fas fa-exclamation-triangle me-2"></i>
                  Cannot send message: Other user not found for this shipment
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
import { ref, onMounted, nextTick, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  shipment: Object,
  messages: Array,
  currentUser: Object,
  otherUser: Object,
  userType: String
})

const newMessage = ref('')
const loading = ref(false)
const messagesContainer = ref(null)
const messageInput = ref(null)

const formatTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleTimeString([], { 
    hour: '2-digit', 
    minute: '2-digit' 
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

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || !props.otherUser) return

  try {
    const response = await axios.post(route('messages.send', props.shipment.id), {
      message: newMessage.value
    }, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })

    if (response.data.success) {
      props.messages.push(response.data.message)
      newMessage.value = ''
      scrollToBottom()
    }
  } catch (error) {
    console.error('Error sending message:', error)
    alert(error.response?.data?.error || 'Failed to send message. Please try again.')
  }
}

// Poll for new messages
let pollInterval = null

const pollMessages = () => {
  pollInterval = setInterval(async () => {
    try {
      const response = await axios.get(route('messages.list', props.shipment.id))
      if (response.data.messages.length !== props.messages.length) {
        // Update messages if different
        props.messages = response.data.messages
        scrollToBottom()
      }
    } catch (error) {
      console.error('Error polling messages:', error)
    }
  }, 3000)
}

onMounted(() => {
  scrollToBottom()
  messageInput.value?.focus()
  
  // Start polling for new messages
  pollMessages()
})

// Clean up interval when component unmounts
import { onUnmounted } from 'vue'
onUnmounted(() => {
  if (pollInterval) {
    clearInterval(pollInterval)
  }
})
</script>

<style scoped>
.chat-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.chat-header {
  background: white;
  border-bottom: 1px solid #e9ecef;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.chat-container {
  padding: 2rem 0;
}

.chat-messages-container {
  height: 500px;
  overflow-y: auto;
  background: white;
  border-radius: 10px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.messages-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.message-item {
  display: flex;
  max-width: 80%;
}

.message-item.sent {
  align-self: flex-end;
  flex-direction: row-reverse;
}

.message-item.received {
  align-self: flex-start;
}

.message-avatar {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6c757d;
  font-size: 2rem;
}

.message-item.sent .message-avatar {
  margin-left: 1rem;
}

.message-item.received .message-avatar {
  margin-right: 1rem;
}

.message-content {
  background: #f1f3f4;
  border-radius: 15px;
  padding: 12px 16px;
  max-width: 100%;
}

.message-item.sent .message-content {
  background: linear-gradient(135deg, #0d6efd, #0dcaf0);
  color: white;
  border-bottom-right-radius: 5px;
}

.message-item.received .message-content {
  background: #e9ecef;
  color: #333;
  border-bottom-left-radius: 5px;
}

.message-header {
  display: flex;
  align-items: center;
  margin-bottom: 6px;
}

.message-item.sent .message-header {
  justify-content: flex-end;
}

.badge-sm {
  font-size: 0.65rem;
  padding: 2px 6px;
}

.message-text {
  line-height: 1.5;
  word-wrap: break-word;
}

.message-footer {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin-top: 4px;
  font-size: 0.75rem;
}

.message-item.sent .message-footer {
  color: rgba(255,255,255,0.8);
}

.chat-input-container {
  background: white;
  border-radius: 10px;
  padding: 1rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.input-group textarea {
  resize: none;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 12px;
  font-size: 0.95rem;
}

.input-group textarea:focus {
  box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.1);
  border-color: #0d6efd;
}

.input-group textarea:disabled {
  background-color: #f8f9fa;
  cursor: not-allowed;
}

/* Scrollbar styling */
.chat-messages-container::-webkit-scrollbar {
  width: 6px;
}

.chat-messages-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.chat-messages-container::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

@media (max-width: 768px) {
  .chat-messages-container {
    height: 400px;
  }
  
  .message-item {
    max-width: 90%;
  }
}
</style>