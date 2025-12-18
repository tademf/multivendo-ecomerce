<template>
  <AppLayout>
  <div class="verification-page">
    <div class="container py-5">
      <!-- Header -->
      <div class="text-center mb-5">
        <div class="icon-container mb-4">
          <i class="fas fa-shield-check fa-3x text-primary"></i>
        </div>
        <h1 class="display-5 fw-bold text-primary">Account Verification</h1>
        <p class="lead text-muted">
          Get verified to unlock premium features and build trust with other users
        </p>
        
        <!-- Status Badge -->
        <div v-if="userVerificationStatus" class="d-inline-block mt-3">
          <div class="alert alert-success d-inline-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Your account is already verified!</strong>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <!-- Verification Benefits Card -->
          <div >
            <div >
              <h5 >
                
              </h5>
              <div class="row">
                <div class="col-md-4 mb-3" v-for="benefit in benefits" :key="benefit.title">
                  <div class="d-flex align-items-start">
                    <div>
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Verification Form -->
          <div class="card form-card border-0 shadow-sm">
            <div class="card-body p-4">
              <!-- Process Steps -->
              <div class="process-steps mb-5">
                <div class="steps-container">
                  <div class="step active">
                    <div class="step-icon">1</div>
                    <div class="step-label">Account Details</div>
                  </div>
                  <div class="step" :class="{ 'active': currentStep >= 2 }">
                    <div class="step-icon">2</div>
                    <div class="step-label">ID Upload</div>
                  </div>
                  <div class="step" :class="{ 'active': currentStep >= 3 }">
                    <div class="step-icon">3</div>
                    <div class="step-label">Review</div>
                  </div>
                </div>
              </div>

              <!-- Step 1: Account Details -->
              <div v-if="currentStep === 1" class="step-content">
                <h4 class="mb-4 fw-bold">
                  <i class="fas fa-user-circle me-2 text-primary"></i>
                  Account Information
                </h4>
                
                <!-- User Info Display -->
                <div class="user-info-display mb-4 p-3 bg-light rounded">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label text-muted small mb-1">Full Name</label>
                      <div class="fw-bold">{{ user.name || user.full_name || 'Not provided' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label text-muted small mb-1">Email</label>
                      <div class="fw-bold">{{ user.email }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label text-muted small mb-1">Phone</label>
                      <div class="fw-bold">{{ user.phone || 'Not provided' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label text-muted small mb-1">User Type</label>
                      <div>
                        <span class="badge bg-info">{{ user.user_type || 'Customer' }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Account Number -->
                <div class="mb-4">
                  <label for="accountNumber" class="form-label fw-bold">
                    <i class="fas fa-credit-card me-2"></i>
                    Account Number
                    <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-light">
                      <i class="fas fa-bank"></i>
                    </span>
                    <input
                      type="text"
                      id="accountNumber"
                      v-model="form.account_number"
                      class="form-control"
                      :class="{ 'is-invalid': errors.account_number }"
                      placeholder="Enter your bank account number"
                      maxlength="20"
                      @input="formatAccountNumber"
                    />
                    <button 
                      v-if="user.account_number && !form.account_number"
                      type="button"
                      class="btn btn-outline-secondary"
                      @click="useExistingAccountNumber"
                    >
                      Use Existing
                    </button>
                  </div>
                  <div v-if="errors.account_number" class="invalid-feedback d-block">
                    {{ errors.account_number }}
                  </div>
                  <div class="form-text">
                    Required for vendor payouts. Must be 10-20 digits.
                  </div>
                </div>

                <!-- National ID -->
                <div class="mb-4">
                  <label for="nationalId" class="form-label fw-bold">
                    <i class="fas fa-id-card me-2"></i>
                    National ID Number
                    <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-light">
                      <i class="fas fa-fingerprint"></i>
                    </span>
                    <input
                      type="text"
                      id="nationalId"
                      v-model="form.national_id"
                      class="form-control"
                      :class="{ 'is-invalid': errors.national_id }"
                      placeholder="Enter your national ID number"
                      maxlength="20"
                    />
                  </div>
                  <div v-if="errors.national_id" class="invalid-feedback d-block">
                    {{ errors.national_id }}
                  </div>
                  <div class="form-text">
                    Your government-issued national identification number
                  </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-5">
                  <button
                    type="button"
                    class="btn btn-outline-secondary"
                    @click="$inertia.visit(route('dashboard'))"
                  >
                    <i class="fas fa-arrow-left me-1"></i>
                    Back to Dashboard
                  </button>
                  <button
                    type="button"
                    class="btn btn-primary"
                    @click="nextStep"
                    :disabled="!form.account_number || !form.national_id"
                  >
                    Continue to ID Upload
                    <i class="fas fa-arrow-right ms-1"></i>
                  </button>
                </div>
              </div>

              <!-- Step 2: ID Upload -->
              <div v-if="currentStep === 2" class="step-content">
                <h4 class="mb-4 fw-bold">
                  <i class="fas fa-camera me-2 text-primary"></i>
                  Upload National ID
                </h4>

                <!-- Upload Area -->
                <div class="upload-area mb-4">
                  <div 
                    class="drop-zone"
                    :class="{ 'dragover': isDragging, 'has-image': idPreview }"
                    @dragover.prevent="onDragOver"
                    @dragleave.prevent="onDragLeave"
                    @drop.prevent="onDrop"
                    @click="triggerFileInput"
                  >
                    <div v-if="!idPreview" class="upload-placeholder">
                      <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                      <h5>Drag & Drop your National ID image</h5>
                      <p class="text-muted mb-3">or click to browse files</p>
                      <small class="text-muted">
                        Supported formats: JPG, PNG, PDF • Max size: 5MB
                      </small>
                    </div>
                    <div v-else class="preview-container">
                      <img :src="idPreview" alt="ID Preview" class="preview-image" />
                      <div class="preview-overlay">
                        <button type="button" class="btn btn-light btn-sm" @click.stop="removeImage">
                          <i class="fas fa-times"></i> Remove
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Hidden file input -->
                  <input
                    type="file"
                    ref="fileInput"
                    @change="onFileSelect"
                    accept="image/*,.pdf"
                    class="d-none"
                  />
                  
                  <!-- Upload Progress -->
                  <div v-if="uploading" class="mt-3">
                    <div class="d-flex align-items-center">
                      <div class="flex-grow-1 me-3">
                        <div class="progress" style="height: 8px;">
                          <div 
                            class="progress-bar progress-bar-striped progress-bar-animated" 
                            role="progressbar" 
                            :style="{ width: uploadProgress + '%' }"
                          ></div>
                        </div>
                      </div>
                      <span class="text-muted">{{ uploadProgress }}%</span>
                    </div>
                  </div>
                  
                  <div v-if="errors.national_id_image" class="invalid-feedback d-block mt-2">
                    {{ errors.national_id_image }}
                  </div>
                </div>

                <!-- Requirements -->
                <div class="requirements alert alert-info">
                  <h6 class="fw-bold mb-2">
                    <i class="fas fa-info-circle me-2"></i>
                    Photo Requirements
                  </h6>
                  <ul class="mb-0">
                    <li>Clear, readable photo of your National ID</li>
                    <li>All details must be visible and legible</li>
                    <li>No glare or reflections on the ID</li>
                    <li>ID must be valid and not expired</li>
                    <li>File size must not exceed 5MB</li>
                  </ul>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-5">
                  <button
                    type="button"
                    class="btn btn-outline-secondary"
                    @click="previousStep"
                  >
                    <i class="fas fa-arrow-left me-1"></i>
                    Back to Account Details
                  </button>
                  <button
                    type="button"
                    class="btn btn-primary"
                    @click="nextStep"
                    :disabled="!form.national_id_image"
                  >
                    Review & Submit
                    <i class="fas fa-check ms-1"></i>
                  </button>
                </div>
              </div>

              <!-- Step 3: Review & Submit -->
              <div v-if="currentStep === 3" class="step-content">
                <h4 class="mb-4 fw-bold">
                  <i class="fas fa-clipboard-check me-2 text-primary"></i>
                  Review & Submit
                </h4>

                <!-- Review Summary -->
                <div class="review-summary card border-0 bg-light mb-4">
                  <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold">Verification Summary</h5>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <div class="review-item">
                          <span class="review-label">Account Number:</span>
                          <span class="review-value fw-bold">{{ form.account_number }}</span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="review-item">
                          <span class="review-label">National ID:</span>
                          <span class="review-value fw-bold">{{ form.national_id }}</span>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="review-item">
                          <span class="review-label">ID Image:</span>
                          <div class="review-image mt-2">
                            <img :src="idPreview" alt="ID Preview" class="img-thumbnail" style="max-height: 150px;" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="terms-card mb-4">
                  <div class="form-check">
                    <input
                      type="checkbox"
                      id="terms"
                      v-model="form.agree_terms"
                      class="form-check-input"
                      :class="{ 'is-invalid': errors.agree_terms }"
                    />
                    <label for="terms" class="form-check-label">
                      I confirm that:
                      <ul class="mt-2 mb-0">
                        <li>The information provided is accurate and complete</li>
                        <li>The National ID belongs to me and is valid</li>
                        <li>I authorize verification of my identity</li>
                        <li>I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a></li>
                      </ul>
                    </label>
                    <div v-if="errors.agree_terms" class="invalid-feedback d-block">
                      {{ errors.agree_terms }}
                    </div>
                  </div>
                </div>

                <!-- Processing Info -->
                <div class="alert alert-warning">
                  <div class="d-flex">
                    <i class="fas fa-clock me-3 mt-1"></i>
                    <div>
                      <h6 class="alert-heading fw-bold">Processing Time</h6>
                      <p class="mb-0">
                        Verification typically takes 1-3 business days. You'll receive a notification once your verification is complete.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-5">
                  <button
                    type="button"
                    class="btn btn-outline-secondary"
                    @click="previousStep"
                  >
                    <i class="fas fa-arrow-left me-1"></i>
                    Back to ID Upload
                  </button>
                  <button
                    type="button"
                    class="btn btn-success"
                    @click="submitVerification"
                    :disabled="!form.agree_terms || submitting"
                  >
                    <span v-if="submitting">
                      <span class="spinner-border spinner-border-sm me-2"></span>
                      Submitting...
                    </span>
                    <span v-else>
                      <i class="fas fa-paper-plane me-1"></i>
                      Submit for Verification
                    </span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Request Info -->
          <div v-if="hasPendingRequest && !userVerificationStatus" class="card mt-4 border-warning">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <i class="fas fa-clock text-warning fa-2x me-3"></i>
                <div>
                  <h5 class="fw-bold mb-1">Verification Pending</h5>
                  <p class="mb-0">Your verification request is under review. You'll be notified once it's processed.</p>
                  <small class="text-muted">Submitted on {{ pendingRequestDate }}</small>
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

<script>
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  name: 'VerificationRequest',
  
  components: {
    Link,
    AppLayout  
  },
  
  props: {
    user: {
      type: Object,
      required: true
    },
    pendingRequest: {
      type: Object,
      default: null
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  
  data() {
    return {
      currentStep: 1,
      isDragging: false,
      idPreview: null,
      uploading: false,
      uploadProgress: 0,
      submitting: false,
      
      form: {
        account_number: this.user.account_number || '',
        national_id: '',
        national_id_image: null,
        agree_terms: false
      },
      
      benefits: [
        {
          title: 'Trust Badge',
          description: 'Get verified badge on your profile',
          icon: 'fas fa-badge-check'
        },
        {
          title: 'Higher Limits',
          description: 'Increased transaction limits',
          icon: 'fas fa-chart-line'
        },
        {
          title: 'Vendor Payouts',
          description: 'Receive payments directly',
          icon: 'fas fa-money-check-alt'
        },
        {
          title: 'Priority Support',
          description: 'Faster customer support',
          icon: 'fas fa-headset'
        },
        {
          title: 'Featured Listings',
          description: 'Products shown more prominently',
          icon: 'fas fa-star'
        },
        {
          title: 'Secure Transactions',
          description: 'Enhanced security features',
          icon: 'fas fa-shield-alt'
        }
      ]
    }
  },
  
  computed: {
    userVerificationStatus() {
      return this.user.is_verified || this.user.verified_at || false
    },
    
    hasPendingRequest() {
      return this.pendingRequest && this.pendingRequest.status === 'pending'
    },
    
    pendingRequestDate() {
      if (!this.pendingRequest) return ''
      return new Date(this.pendingRequest.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
  },
  
  mounted() {
    if (this.userVerificationStatus) {
      this.currentStep = 0
    } else if (this.hasPendingRequest) {
      this.currentStep = 0
    }
  },
  
  methods: {
    nextStep() {
      if (this.currentStep < 3) {
        this.currentStep++
      }
    },
    
    previousStep() {
      if (this.currentStep > 1) {
        this.currentStep--
      }
    },
    
    formatAccountNumber() {
      // Remove non-numeric characters
      this.form.account_number = this.form.account_number.replace(/\D/g, '')
      // Format with spaces every 4 digits
      this.form.account_number = this.form.account_number.replace(/(\d{4})(?=\d)/g, '$1 ')
    },
    
    useExistingAccountNumber() {
      if (this.user.account_number) {
        this.form.account_number = this.user.account_number
      }
    },
    
    triggerFileInput() {
      this.$refs.fileInput.click()
    },
    
    onDragOver(event) {
      event.preventDefault()
      this.isDragging = true
    },
    
    onDragLeave(event) {
      event.preventDefault()
      this.isDragging = false
    },
    
    onDrop(event) {
      event.preventDefault()
      this.isDragging = false
      
      const files = event.dataTransfer.files
      if (files.length > 0) {
        this.handleFile(files[0])
      }
    },
    
    onFileSelect(event) {
      const file = event.target.files[0]
      if (file) {
        this.handleFile(file)
      }
    },
    
    handleFile(file) {
      // Validate file type
      const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf']
      if (!validTypes.includes(file.type)) {
        this.showNotification('Please select a JPG, PNG, or PDF file', 'error')
        return
      }
      
      // Validate file size (5MB)
      const maxSize = 5 * 1024 * 1024
      if (file.size > maxSize) {
        this.showNotification('File size must be less than 5MB', 'error')
        return
      }
      
      // Upload the file
      this.uploadFile(file)
    },
    
    async uploadFile(file) {
      this.uploading = true
      this.uploadProgress = 0
      
      const formData = new FormData()
      formData.append('national_id_image', file)
      
      try {
        // FIXED: Changed from '/api/upload-id-image' to '/verification/upload-id-image'
        const response = await axios.post('/verification/upload-id-image', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          onUploadProgress: (progressEvent) => {
            if (progressEvent.total) {
              this.uploadProgress = Math.round((progressEvent.loaded * 100) / progressEvent.total)
            }
          }
        })
        
        if (response.data.success) {
          this.form.national_id_image = response.data.file_path
          
          // Create preview for images
          if (file.type.startsWith('image/')) {
            const reader = new FileReader()
            reader.onload = (e) => {
              this.idPreview = e.target.result
            }
            reader.readAsDataURL(file)
          } else {
            this.idPreview = '/images/pdf-icon.png' // Placeholder for PDF
          }
          
          this.showNotification('ID image uploaded successfully', 'success')
        }
      } catch (error) {
        console.error('Upload error:', error)
        this.showNotification(
          error.response?.data?.message || 'Upload failed. Please try again.',
          'error'
        )
      } finally {
        this.uploading = false
        this.uploadProgress = 0
      }
    },
    
    removeImage() {
      this.idPreview = null
      this.form.national_id_image = null
      this.$refs.fileInput.value = ''
    },
    
    async submitVerification() {
      this.submitting = true
      
      try {
        // First, update user account number
        if (this.form.account_number !== this.user.account_number) {
          // FIXED: Changed from '/api/update-account-number' to '/verification/update-account-number'
          await axios.put('/verification/update-account-number', {
            account_number: this.form.account_number.replace(/\s/g, '') // Remove spaces
          })
        }
        
        // Submit verification request
        await this.$inertia.post(route('verification.submit'), {
          national_id: this.form.national_id,
          national_id_image: this.form.national_id_image,
          agree_terms: this.form.agree_terms
        }, {
          preserveScroll: true,
          onSuccess: () => {
            this.showNotification('Verification request submitted successfully!', 'success')
          },
          onError: () => {
            this.showNotification('Please check the form for errors', 'error')
          }
        })
      } catch (error) {
        console.error('Submission error:', error)
        this.showNotification('Submission failed. Please try again.', 'error')
      } finally {
        this.submitting = false
      }
    },
    
    showNotification(message, type = 'success') {
      const toastContainer = document.createElement('div')
      toastContainer.className = 'position-fixed bottom-0 end-0 p-3'
      toastContainer.style.zIndex = '1055'
      
      const toast = document.createElement('div')
      toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`
      toast.setAttribute('role', 'alert')
      toast.setAttribute('aria-live', 'assertive')
      toast.setAttribute('aria-atomic', 'true')
      
      toast.innerHTML = `
        <div class="d-flex">
          <div class="toast-body">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      `
      
      toastContainer.appendChild(toast)
      document.body.appendChild(toastContainer)
      
      const bsToast = new bootstrap.Toast(toast)
      bsToast.show()
      
      toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toastContainer)
      })
    }
  }
}
</script>

<style scoped>
.verification-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.icon-container {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 80px;
  height: 80px;
  background: white;
  border-radius: 50%;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.benefits-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.benefits-card .text-muted {
  color: rgba(255,255,255,0.8) !important;
}

.form-card {
  border-radius: 15px;
  overflow: hidden;
}

.process-steps {
  position: relative;
}

.steps-container {
  display: flex;
  justify-content: space-between;
  position: relative;
}

.steps-container::before {
  content: '';
  position: absolute;
  top: 25px;
  left: 50px;
  right: 50px;
  height: 3px;
  background: #dee2e6;
  z-index: 1;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  z-index: 2;
  flex: 1;
}

.step-icon {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #dee2e6;
  color: #6c757d;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.2rem;
  margin-bottom: 10px;
  transition: all 0.3s;
}

.step.active .step-icon {
  background: #0d6efd;
  color: white;
  box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
}

.step-label {
  font-size: 0.9rem;
  color: #6c757d;
  font-weight: 500;
  text-align: center;
}

.step.active .step-label {
  color: #0d6efd;
  font-weight: 600;
}

.user-info-display {
  background: rgba(13, 110, 253, 0.05);
  border-left: 4px solid #0d6efd;
}

.upload-area {
  position: relative;
}

.drop-zone {
  border: 3px dashed #dee2e6;
  border-radius: 10px;
  padding: 40px 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  background: white;
  min-height: 250px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.drop-zone:hover {
  border-color: #0d6efd;
  background: rgba(13, 110, 253, 0.05);
}

.drop-zone.dragover {
  border-color: #0d6efd;
  background: rgba(13, 110, 253, 0.1);
  transform: scale(1.01);
}

.drop-zone.has-image {
  border-color: #198754;
  background: rgba(25, 135, 84, 0.05);
  padding: 10px;
}

.upload-placeholder i {
  opacity: 0.5;
}

.preview-container {
  position: relative;
  width: 100%;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.preview-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  border-radius: 5px;
}

.preview-overlay {
  position: absolute;
  top: 10px;
  right: 10px;
}

.requirements ul {
  list-style-type: none;
  padding-left: 0;
}

.requirements li {
  position: relative;
  padding-left: 25px;
  margin-bottom: 8px;
}

.requirements li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: #198754;
  font-weight: bold;
}

.review-summary {
  border: 1px solid #dee2e6;
}

.review-item {
  margin-bottom: 10px;
}

.review-label {
  display: block;
  color: #6c757d;
  font-size: 0.9rem;
  margin-bottom: 3px;
}

.review-value {
  display: block;
  font-size: 1.1rem;
}

.review-image {
  border: 1px solid #dee2e6;
  padding: 10px;
  border-radius: 5px;
  background: white;
}

.terms-card {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 10px;
  border-left: 4px solid #0d6efd;
}

.terms-card ul {
  padding-left: 20px;
}

.terms-card li {
  margin-bottom: 5px;
  color: #495057;
}

@media (max-width: 768px) {
  .steps-container::before {
    left: 40px;
    right: 40px;
  }
  
  .step-icon {
    width: 40px;
    height: 40px;
    font-size: 1rem;
  }
  
  .step-label {
    font-size: 0.8rem;
  }
  
  .drop-zone {
    padding: 30px 15px;
    min-height: 200px;
  }
}
</style>