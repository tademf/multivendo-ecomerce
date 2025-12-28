<template>
  <AppLayout>
    <div class="verification-page">
      <div class="animated-bg">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
      </div>

      <div class="container py-5">
        <div class="text-center mb-5">
          <div class="icon-container mb-4">
            <div class="icon-wrapper">
              <i class="fas fa-shield-alt fa-3x text-primary"></i>
              <div class="icon-pulse">
              </div>
            </div>
          </div>
          <h1 class="display-5 fw-bold text-primary mb-3">
            Account Verification
          </h1>
          
          <div v-if="shouldShowVerifiedMessage" class="d-inline-block mt-3">
            <div class="verified-badge">
              <i class="fas fa-check-circle me-2"></i>
              <strong>You are Verified!</strong>
            </div>
            <div class="mt-2 text-success">
              <i class="fas fa-calendar-check me-1"></i>
              <span v-if="verificationRequest && verificationRequest.reviewed_at">
                Approved on: {{ formatDate(verificationRequest.reviewed_at) }}
              </span>
              <span v-else>
                Account Verified
              </span>
            </div>
            <p class="mt-2 mb-0">
              <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Your account is verified in our verification system.
              </small>
            </p>
          </div>

          <div v-else-if="verificationRequest && verificationRequest.status === 'rejected'" class="d-inline-block mt-3">
            <div class="rejected-alert">
              <i class="fas fa-exclamation-circle me-2"></i>
              <strong>Your request was rejected</strong>
              <div v-if="verificationRequest.rejection_reason" class="mt-2">
                <strong>Reason:</strong> {{ verificationRequest.rejection_reason }}
              </div>
              <p class="mb-0 mt-2">You can submit a new verification request below.</p>
            </div>
          </div>

          <div v-else-if="verificationRequest && verificationRequest.status === 'pending'" class="d-inline-block mt-3">
            <div class="pending-badge">
              <i class="fas fa-clock me-2"></i>
              <strong>Verification Pending</strong>
            </div>
            <p class="mt-2 mb-0">
              <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Your verification request is under review. Please wait for approval.
              </small>
            </p>
            <div class="mt-2 text-muted">
              <i class="fas fa-calendar me-1"></i>
              Submitted on: {{ formatDate(verificationRequest.created_at) }}
            </div>
          </div>

          <div v-else class="d-inline-block mt-3">
            <div class="info-badge">
              <i class="fas fa-info-circle me-2"></i>
              <strong>Get Verified Now</strong>
            </div>
            <p class="mt-2 mb-0">
              <small class="text-muted">
                Complete the verification process
              </small>
            </p>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-10 col-xl-8">
            <div class="row g-4 mb-5">
              <div class="col-md-6 col-lg-4" v-for="benefit in benefits" :key="benefit.title">
                <div class="benefit-card h-100">
                  <div class="benefit-icon mb-3">
                    <i :class="benefit.icon"></i>
                  </div>
                  <h5 class="benefit-title mb-2">{{ benefit.title }}</h5>
                  <p class="benefit-desc text-muted mb-0">{{ benefit.description }}</p>
                </div>
              </div>
            </div>

            <div v-if="shouldShowVerifiedMessage" class="card border-success border-2">
              <div class="card-body text-center py-5">
                <div class="verified-icon mb-4">
                  <i class="fas fa-check-circle fa-5x text-success"></i>
                </div>
                <h3 class="card-title fw-bold mb-3">You are Verified!</h3>
                <p class="card-text mb-3">
                  Your account is fully verified in our verification system.
                </p>
                <div v-if="verificationRequest && verificationRequest.reviewed_at" class="text-muted mb-4">
                  <i class="fas fa-calendar-alt me-1"></i>
                  Approved on: {{ formatDate(verificationRequest.reviewed_at) }}
                </div>
                <a href="/" class="btn btn-success btn-lg px-5">
                  <i class="fas fa-home me-2"></i>
                  Go to Homepage
                </a>
              </div>
            </div>

            <div v-else-if="shouldShowForm" class="card form-card border-0">
              <div class="card-body p-4 p-md-5">
                <div class="process-steps mb-5">
                  <div class="steps-container" :style="{'--progress': progressPercentage}">
                    <div class="step active">
                      <div class="step-icon">
                        <i class="fas fa-user-check"></i>
                        <div class="step-number">1</div>
                      </div>
                      <div class="step-label">Basic Info</div>
                    </div>
                    <div class="step" :class="{ 'active': currentStep >= 2 }">
                      <div class="step-icon">
                        <i class="fas fa-id-card"></i>
                        <div class="step-number">2</div>
                      </div>
                      <div class="step-label">ID Upload</div>
                    </div>
                    <div class="step" :class="{ 'active': currentStep >= 3 }">
                      <div class="step-icon">
                        <i class="fas fa-clipboard-check"></i>
                        <div class="step-number">3</div>
                      </div>
                      <div class="step-label">Review</div>
                    </div>
                  </div>
                </div>

                <div v-if="currentStep === 1" class="step-content">
                  <h4 class="mb-4 fw-bold step-title">
                    <i class="fas fa-user-circle me-2"></i>
                    Basic Information
                  </h4>
                  
                  <div class="user-info-display mb-4">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <div class="info-item">
                          <div class="info-label">
                            <i class="fas fa-user me-2"></i>
                            Full Name
                          </div>
                          <div class="info-value">{{ user.name || 'Not provided' }}</div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="info-item">
                          <div class="info-label">
                            <i class="fas fa-envelope me-2"></i>
                            Email
                          </div>
                          <div class="info-value">{{ user.email }}</div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="info-item">
                          <div class="info-label">
                            <i class="fas fa-phone me-2"></i>
                            Phone
                          </div>
                          <div class="info-value">{{ user.phone || 'Not provided' }}</div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="info-item">
                          <div class="info-label">
                            <i class="fas fa-user-tag me-2"></i>
                            User Type
                          </div>
                          <div class="info-value">
                            <span class="user-type-badge">{{ user.user_type || 'Customer' }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="mb-4">
                    <label for="nationalId" class="form-label fw-bold">
                      <i class="fas fa-id-card me-2"></i>
                      National ID Number
                      <span class="text-danger">*</span>
                    </label>
                    <div class="input-group-lg">
                      <div class="input-group">
                        <span class="input-group-text bg-gradient-primary text-white">
                          <i class="fas fa-fingerprint"></i>
                        </span>
                        <input
                          type="text"
                          id="nationalId"
                          v-model="form.national_id"
                          class="form-control form-control-lg"
                          :class="{ 'is-invalid': errors && errors.national_id }"
                          placeholder="Enter your national ID number"
                          maxlength="20"
                        />
                      </div>
                      <div v-if="errors && errors.national_id" class="invalid-feedback d-block mt-2">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        {{ errors.national_id }}
                      </div>
                      <div class="form-text mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Your government-issued national identification number
                      </div>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between mt-5 pt-3">
                    <button
                      type="button"
                      class="btn btn-outline-primary btn-lg px-4"
                      @click="$inertia.visit('/')"
                    >
                      <i class="fas fa-home me-2"></i>
                      Back to Home
                    </button>
                    <button
                      type="button"
                      class="btn btn-gradient-primary btn-lg px-5"
                      @click="nextStep"
                      :disabled="!form.national_id"
                    >
                      Continue to ID Upload
                      <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                  </div>
                </div>

                <div v-if="currentStep === 2" class="step-content">
                  <h4 class="mb-4 fw-bold step-title">
                    <i class="fas fa-camera me-2"></i>
                    Upload National ID
                  </h4>

                  <div class="upload-area mb-5">
                    <div 
                      class="drop-zone"
                      :class="{ 'dragover': isDragging, 'has-image': idPreview }"
                      @dragover.prevent="onDragOver"
                      @dragleave.prevent="onDragLeave"
                      @drop.prevent="onDrop"
                      @click="triggerFileInput"
                    >
                      <div v-if="!idPreview" class="upload-placeholder">
                        <div class="upload-icon mb-3">
                          <i class="fas fa-cloud-upload-alt fa-4x"></i>
                          <div class="upload-pulse"></div>
                        </div>
                        <h4 class="mb-2">Drag & Drop your National ID</h4>
                        <p class="text-muted mb-4">or click to browse files</p>
                        <div class="upload-requirements">
                          <span class="badge bg-light text-dark me-2">
                            <i class="fas fa-file-image me-1"></i>JPG, PNG
                          </span>
                          <span class="badge bg-light text-dark ms-2">
                            <i class="fas fa-weight me-1"></i>Max 5MB
                          </span>
                        </div>
                      </div>
                      <div v-else class="preview-container">
                        <div class="preview-wrapper">
                          <img :src="idPreview" alt="ID Preview" class="preview-image" />
                          <div class="preview-overlay">
                            <button type="button" class="btn btn-light btn-sm me-2" @click.stop="triggerFileInput">
                              <i class="fas fa-sync me-1"></i> Change
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" @click.stop="removeImage">
                              <i class="fas fa-trash me-1"></i> Remove
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <input
                      type="file"
                      ref="fileInput"
                      @change="onFileSelect"
                      accept="image/*"
                      class="d-none"
                    />
                    
                    <div v-if="uploading" class="upload-progress mt-4">
                      <div class="d-flex align-items-center mb-2">
                        <span class="me-3">Uploading...</span>
                        <span class="text-primary fw-bold">{{ uploadProgress }}%</span>
                      </div>
                      <div class="progress" style="height: 10px;">
                        <div 
                          class="progress-bar progress-bar-striped progress-bar-animated bg-gradient-primary" 
                          role="progressbar" 
                          :style="{ width: uploadProgress + '%' }"
                        ></div>
                      </div>
                    </div>
                    
                    <div v-if="errors && errors.national_id_image" class="alert alert-danger mt-3">
                      <i class="fas fa-exclamation-triangle me-2"></i>
                      {{ errors.national_id_image }}
                    </div>
                  </div>

                  <div class="requirements-card mb-5">
                    <div class="card border-0 bg-light">
                      <div class="card-body">
                        <h5 class="card-title mb-3">
                          <i class="fas fa-clipboard-list me-2 text-primary"></i>
                          Photo Requirements
                        </h5>
                        <div class="row">
                          <div class="col-md-6" v-for="req in requirements" :key="req">
                            <div class="requirement-item mb-2">
                              <i class="fas fa-check-circle text-success me-2"></i>
                              {{ req }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between mt-5 pt-3">
                    <button
                      type="button"
                      class="btn btn-outline-primary btn-lg px-4"
                      @click="previousStep"
                    >
                      <i class="fas fa-arrow-left me-2"></i>
                      Back to Basic Info
                    </button>
                    <button
                      type="button"
                      class="btn btn-gradient-primary btn-lg px-5"
                      @click="nextStep"
                      :disabled="!form.national_id_image"
                    >
                      Review & Submit
                      <i class="fas fa-check ms-2"></i>
                    </button>
                  </div>
                </div>

                <div v-if="currentStep === 3" class="step-content">
                  <h4 class="mb-4 fw-bold step-title">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Review & Submit
                  </h4>

                  <div class="review-summary mb-5">
                    <div class="card border-0 shadow-sm">
                      <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0">
                          <i class="fas fa-file-alt me-2"></i>
                          Verification Summary
                        </h5>
                      </div>
                      <div class="card-body">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <div class="review-item">
                              <div class="review-label">
                                <i class="fas fa-id-card me-2"></i>
                                National ID Number
                              </div>
                              <div class="review-value">{{ form.national_id }}</div>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="review-item">
                              <div class="review-label mb-2">
                                <i class="fas fa-image me-2"></i>
                                ID Image Preview
                              </div>
                              <div class="review-image">
                                <img :src="idPreview" alt="ID Preview" class="img-thumbnail" />
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="terms-card mb-5">
                    <div class="card border-0 bg-light">
                      <div class="card-body">
                        <div class="form-check">
                          <input
                            type="checkbox"
                            id="terms"
                            v-model="form.agree_terms"
                            class="form-check-input"
                            :class="{ 'is-invalid': errors && errors.agree_terms }"
                          />
                          <label for="terms" class="form-check-label">
                            <h6 class="mb-3">
                              <i class="fas fa-file-contract me-2"></i>
                              I confirm that:
                            </h6>
                            <ul class="mb-0">
                              <li>The information provided is accurate and complete</li>
                              <li>The National ID belongs to me and is valid</li>
                              <li>I authorize verification of my identity</li>
                              <li>I agree to the <a href="#" class="text-primary fw-bold">Terms of Service</a> and <a href="#" class="text-primary fw-bold">Privacy Policy</a></li>
                            </ul>
                          </label>
                          <div v-if="errors && errors.agree_terms" class="invalid-feedback d-block mt-2">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            {{ errors.agree_terms }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="alert alert-warning border-0 shadow-sm mb-5">
                    <div class="d-flex">
                      <div class="flex-shrink-0">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <h5 class="alert-heading fw-bold mb-2">Processing Time</h5>
                        <p class="mb-0">
                          Verification typically takes 1-3 business days. You'll receive a notification once your verification is complete.
                          <br>
                          <small class="text-muted">You will be redirected to homepage after submission.</small>
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between mt-5 pt-3">
                    <button
                      type="button"
                      class="btn btn-outline-primary btn-lg px-4"
                      @click="previousStep"
                    >
                      <i class="fas fa-arrow-left me-2"></i>
                      Back to ID Upload
                    </button>
                    <button
                      type="button"
                      class="btn btn-gradient-success btn-lg px-5"
                      @click="submitVerification"
                      :disabled="!form.agree_terms || submitting"
                    >
                      <span v-if="submitting">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Submitting...
                      </span>
                      <span v-else>
                        <i class="fas fa-paper-plane me-2"></i>
                        Submit for Verification
                      </span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="shouldShowPendingMessage" class="card border-warning border-2">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="pending-icon me-4">
                    <i class="fas fa-clock fa-3x text-warning"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h4 class="fw-bold mb-2">Verification Pending</h4>
                    <p class="mb-2">Your verification request is under review. You'll be notified once it's processed.</p>
                    <small class="text-muted">
                      <i class="fas fa-calendar me-1"></i>
                      Submitted on: {{ formatDate(verificationRequest.created_at) }}
                    </small>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="verificationRequest && verificationRequest.status === 'rejected'" class="card border-danger border-2 mb-4">
              <div class="card-body">
                <h4 class="fw-bold mb-2 text-danger">
                  <i class="fas fa-times-circle me-2"></i>
                  Previous Request Rejected
                </h4>
                <p class="mb-3">Your previous verification request was not approved.</p>
                <div v-if="verificationRequest.rejection_reason" class="alert alert-danger">
                  <h6 class="alert-heading fw-bold mb-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Rejection Reason
                  </h6>
                  <p class="mb-0">{{ verificationRequest.rejection_reason }}</p>
                </div>
                <div class="mt-3">
                  <p class="mb-0">
                    <i class="fas fa-lightbulb me-2 text-warning"></i>
                    <strong>Tip:</strong> Please fix the issues mentioned above and submit a new request.
                  </p>
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
    verificationRequest: {
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
        national_id: '',
        national_id_image: null,
        agree_terms: false
      },
      
      // benefits: [
      //   {
      //     title: 'Trust Badge',
      //     description: 'Get verified badge on your profile to build credibility',
      //     icon: 'fas fa-badge-check'
      //   },
      //   {
      //     title: 'Higher Limits',
      //     description: 'Increased transaction and posting limits',
      //     icon: 'fas fa-chart-line'
      //   },
      //   {
      //     title: 'Priority Support',
      //     description: '24/7 priority customer support access',
      //     icon: 'fas fa-headset'
      //   },
      //   {
      //     title: 'Featured Listings',
      //     description: 'Products shown more prominently to buyers',
      //     icon: 'fas fa-star'
      //   },
      //   {
      //     title: 'Secure Transactions',
      //     description: 'Enhanced security and fraud protection',
      //     icon: 'fas fa-shield-alt'
      //   },
      //   {
      //     title: 'Early Access',
      //     description: 'Get early access to new features',
      //     icon: 'fas fa-rocket'
      //   }
      // ],
      
      requirements: [
        'Clear, readable photo of your National ID',
        'All details must be visible and legible',
        'No glare or reflections on the ID',
        'ID must be valid and not expired',
        'File size must not exceed 5MB',
        'Accepted formats: JPG, PNG'
      ]
    }
  },
  
  computed: {
    progressPercentage() {
      return ((this.currentStep - 1) / 2) * 100 + '%'
    },
    
    isVerifiedInVerificationPage() {
      if (this.verificationRequest && this.verificationRequest.status === 'approved') {
        return true
      }
      
      return this.user.is_verified === true
    },
    
    canSubmit() {
      if (!this.verificationRequest) {
        return true
      }
      
      if (this.verificationRequest.status === 'pending') {
        return false
      }
      
      if (this.verificationRequest.status === 'rejected') {
        return true
      }
      
      if (this.verificationRequest.status === 'approved') {
        return false
      }
      
      return false
    },
    
    shouldShowVerifiedMessage() {
      return this.isVerifiedInVerificationPage
    },
    
    shouldShowForm() {
      return !this.shouldShowVerifiedMessage && this.canSubmit
    },
    
    shouldShowPendingMessage() {
      return this.verificationRequest && this.verificationRequest.status === 'pending'
    }
  },
  
  mounted() {
    if (this.shouldShowVerifiedMessage) {
      this.currentStep = 0
    } else if (this.shouldShowPendingMessage) {
      this.currentStep = 0
    } else {
      this.currentStep = 1
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
    
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
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
      const validTypes = ['image/jpeg', 'image/jpg', 'image/png']
      if (!validTypes.includes(file.type)) {
        this.showToast('Please select a JPG or PNG image file', 'error')
        return
      }
      
      const maxSize = 5 * 1024 * 1024
      if (file.size > maxSize) {
        this.showToast('File size must be less than 5MB', 'error')
        return
      }
      
      this.uploadFile(file)
    },
    
    async uploadFile(file) {
      this.uploading = true
      this.uploadProgress = 0
      
      const formData = new FormData()
      formData.append('national_id_image', file)
      
      try {
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
          
          const reader = new FileReader()
          reader.onload = (e) => {
            this.idPreview = e.target.result
          }
          reader.readAsDataURL(file)
          
          this.showToast('ID image uploaded successfully!', 'success')
        }
      } catch (error) {
        console.error('Upload error:', error)
        this.showToast(
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
      if (!this.canSubmit) {
        if (this.isVerifiedInVerificationPage) {
          this.showToast('Your account is already verified in our system.', 'error')
        } else if (this.shouldShowPendingMessage) {
          this.showToast('You already have a pending verification request.', 'error')
        }
        return
      }
      
      this.submitting = true
      
      try {
        await this.$inertia.post('/verification/submit', {
          national_id: this.form.national_id,
          national_id_image: this.form.national_id_image,
          agree_terms: this.form.agree_terms
        }, {
          preserveScroll: false,
          onSuccess: () => {
            this.showToast('Verification request submitted successfully!', 'success')
          },
          onError: (errors) => {
            this.showToast('Please check the form for errors', 'error')
          },
          onFinish: () => {
            this.submitting = false
          }
        })
      } catch (error) {
        console.error('Submission error:', error)
        this.showToast('Submission failed. Please try again.', 'error')
        this.submitting = false
      }
    },
    
    showToast(message, type = 'success') {
      const existingToasts = document.querySelectorAll('.custom-toast')
      existingToasts.forEach(toast => toast.remove())
      
      const toast = document.createElement('div')
      toast.className = `custom-toast toast-${type}`
      
      const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle'
      const bgColor = type === 'success' ? 'linear-gradient(135deg, #00b09b, #96c93d)' : 'linear-gradient(135deg, #ff416c, #ff4b2b)'
      
      toast.innerHTML = `
        <div class="toast-content">
          <div class="toast-icon">
            <i class="fas fa-${icon}"></i>
          </div>
          <div class="toast-message">${message}</div>
          <button class="toast-close" onclick="this.parentElement.parentElement.remove()">
            <i class="fas fa-times"></i>
          </button>
        </div>
      `
      
      toast.style.background = bgColor
      document.body.appendChild(toast)
      
      setTimeout(() => {
        if (toast.parentNode) {
          toast.remove()
        }
      }, 5000)
    }
  }
}
</script>

<style scoped>
.pending-badge {
  background: linear-gradient(135deg, #ffc107, #fd7e14);
  color: white;
  padding: 12px 24px;
  border-radius: 50px;
  display: inline-flex;
  align-items: center;
  font-size: 1.1rem;
  box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
}

.rejected-alert {
  background: linear-gradient(135deg, #dc3545, #fd7e14);
  color: white;
  padding: 15px 25px;
  border-radius: 10px;
  display: inline-block;
  font-size: 1.1rem;
  box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
  max-width: 600px;
}

.rejected-alert i {
  font-size: 1.3rem;
}

.verified-icon {
  animation: bounceIn 1s ease;
}

@keyframes bounceIn {
  0% {
    transform: scale(0.3);
    opacity: 0;
  }
  50% {
    transform: scale(1.05);
  }
  70% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.verification-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  position: relative;
  overflow-x: hidden;
}

.animated-bg {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
  z-index: 0;
}

.shape {
  position: absolute;
  border-radius: 50%;
  background: linear-gradient(45deg, rgba(13, 110, 253, 0.1), rgba(108, 92, 231, 0.1));
  animation: float 20s infinite linear;
}

.shape-1 {
  width: 300px;
  height: 300px;
  top: -150px;
  right: -100px;
  animation-delay: 0s;
}

.shape-2 {
  width: 200px;
  height: 200px;
  bottom: -100px;
  left: -50px;
  animation-delay: 5s;
}

.shape-3 {
  width: 150px;
  height: 150px;
  top: 50%;
  right: 10%;
  animation-delay: 10s;
}

.shape-4 {
  width: 100px;
  height: 100px;
  bottom: 30%;
  left: 10%;
  animation-delay: 15s;
}

@keyframes float {
  0% {
    transform: translateY(0) rotate(0deg);
  }
  50% {
    transform: translateY(-20px) rotate(180deg);
  }
  100% {
    transform: translateY(0) rotate(360deg);
  }
}

.icon-wrapper {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 120px;
  height: 120px;
  background: white;
  border-radius: 50%;
  box-shadow: 0 10px 30px rgba(13, 110, 253, 0.2);
}

.icon-pulse {
  position: absolute;
  width: 140px;
  height: 140px;
  border: 2px solid rgba(13, 110, 253, 0.3);
  border-radius: 50%;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(0.95);
    opacity: 0.8;
  }
  70% {
    transform: scale(1.1);
    opacity: 0;
  }
  100% {
    transform: scale(0.95);
    opacity: 0;
  }
}

.verified-badge {
  background: linear-gradient(135deg, #198754, #20c997);
  color: white;
  padding: 12px 24px;
  border-radius: 50px;
  display: inline-flex;
  align-items: center;
  font-size: 1.1rem;
  box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
}

.benefit-card {
  background: white;
  border-radius: 15px;
  padding: 30px 20px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(13, 110, 253, 0.1);
}

.benefit-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(13, 110, 253, 0.15);
  border-color: rgba(13, 110, 253, 0.3);
}

.benefit-icon {
  font-size: 2.5rem;
  color: #0d6efd;
  margin-bottom: 20px;
}

.benefit-title {
  color: #212529;
  font-weight: 600;
}

.form-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  position: relative;
  z-index: 1;
  overflow: hidden;
}

.form-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(90deg, #0d6efd, #6f42c1, #d63384);
}

.process-steps {
  position: relative;
  padding: 0 20px;
}

.steps-container {
  display: flex;
  justify-content: space-between;
  position: relative;
  max-width: 600px;
  margin: 0 auto;
}

.steps-container::before {
  content: '';
  position: absolute;
  top: 30px;
  left: 30px;
  right: 30px;
  height: 4px;
  background: linear-gradient(90deg, #0d6efd 0%, #dee2e6 0%);
  z-index: 1;
  transition: all 0.5s ease;
}

.steps-container::after {
  content: '';
  position: absolute;
  top: 30px;
  left: 30px;
  width: calc((100% - 60px) * var(--progress, 0));
  height: 4px;
  background: linear-gradient(90deg, #0d6efd, #6f42c1);
  z-index: 2;
  transition: width 0.5s ease;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  z-index: 3;
  flex: 1;
}

.step-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #dee2e6;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin-bottom: 10px;
  position: relative;
  transition: all 0.3s ease;
}

.step.active .step-icon {
  background: linear-gradient(135deg, #0d6efd, #6f42c1);
  transform: scale(1.1);
  box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
}

.step-number {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #198754;
  color: white;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.step-label {
  font-size: 0.9rem;
  color: #6c757d;
  font-weight: 600;
  text-align: center;
  margin-top: 5px;
}

.step.active .step-label {
  color: #0d6efd;
}

.step-title {
  color: #2c3e50;
  padding-bottom: 15px;
  border-bottom: 2px solid #f8f9fa;
  margin-bottom: 30px;
}

.user-info-display {
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  border-radius: 15px;
  padding: 25px;
  border-left: 5px solid #0d6efd;
}

.info-item {
  margin-bottom: 15px;
}

.info-label {
  color: #6c757d;
  font-size: 0.9rem;
  margin-bottom: 5px;
  font-weight: 500;
}

.info-value {
  font-size: 1.1rem;
  font-weight: 600;
  color: #212529;
}

.user-type-badge {
  background: linear-gradient(135deg, #20c997, #198754);
  color: white;
  padding: 5px 15px;
  border-radius: 20px;
  font-size: 0.85rem;
}

.btn-gradient-primary {
  background: linear-gradient(135deg, #0d6efd, #6f42c1);
  color: white;
  border: none;
  transition: all 0.3s ease;
}

.btn-gradient-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
  color: white;
}

.btn-gradient-success {
  background: linear-gradient(135deg, #198754, #20c997);
  color: white;
  border: none;
  transition: all 0.3s ease;
}

.btn-gradient-success:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(25, 135, 84, 0.3);
  color: white;
}

.upload-area {
  position: relative;
}

.drop-zone {
  border: 3px dashed #dee2e6;
  border-radius: 20px;
  padding: 50px 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  background: white;
  min-height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.drop-zone:hover {
  border-color: #0d6efd;
  background: rgba(13, 110, 253, 0.02);
  transform: scale(1.01);
}

.drop-zone.dragover {
  border-color: #0d6efd;
  background: rgba(13, 110, 253, 0.1);
  transform: scale(1.02);
}

.drop-zone.has-image {
  border-color: #198754;
  padding: 20px;
  min-height: auto;
}

.upload-icon {
  position: relative;
  color: #0d6efd;
}

.upload-pulse {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80px;
  height: 80px;
  border: 2px solid rgba(13, 110, 253, 0.2);
  border-radius: 50%;
  animation: uploadPulse 2s infinite;
}

@keyframes uploadPulse {
  0% {
    width: 80px;
    height: 80px;
    opacity: 1;
  }
  100% {
    width: 120px;
    height: 120px;
    opacity: 0;
  }
}

.upload-requirements {
  margin-top: 20px;
}

.preview-wrapper {
  position: relative;
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
}

.preview-image {
  width: 100%;
  max-height: 250px;
  object-fit: contain;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.preview-overlay {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 10px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.preview-wrapper:hover .preview-overlay {
  opacity: 1;
}

.upload-progress {
  max-width: 500px;
  margin: 0 auto;
}

.requirements-card {
  position: relative;
}

.requirements-card::before {
  content: '';
  position: absolute;
  top: -10px;
  left: -10px;
  right: -10px;
  bottom: -10px;
  background: linear-gradient(135deg, #0d6efd, #6f42c1);
  border-radius: 20px;
  z-index: -1;
  opacity: 0.1;
}

.requirement-item {
  display: flex;
  align-items: center;
  padding: 8px 0;
}

.review-summary .card-header {
  border-radius: 15px 15px 0 0 !important;
}

.review-item {
  padding: 15px;
  background: white;
  border-radius: 10px;
  border: 1px solid #f1f3f4;
}

.review-label {
  color: #6c757d;
  font-size: 0.9rem;
  margin-bottom: 5px;
  font-weight: 500;
}

.review-value {
  font-size: 1.1rem;
  font-weight: 600;
  color: #212529;
}

.review-image {
  background: white;
  padding: 15px;
  border-radius: 10px;
  border: 2px dashed #dee2e6;
  text-align: center;
}

.review-image img {
  max-height: 150px;
  border-radius: 5px;
}

.terms-card .card {
  border: 2px solid rgba(13, 110, 253, 0.1);
}

.terms-card ul {
  list-style-type: none;
  padding-left: 0;
}

.terms-card li {
  position: relative;
  padding-left: 30px;
  margin-bottom: 10px;
}

.terms-card li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: #198754;
  font-weight: bold;
  font-size: 1.2rem;
}

.pending-request-card {
  position: relative;
}

.pending-request-card::before {
  content: '';
  position: absolute;
  top: -5px;
  left: -5px;
  right: -5px;
  bottom: -5px;
  background: linear-gradient(135deg, #ffc107, #fd7e14);
  border-radius: 20px;
  z-index: -1;
  opacity: 0.2;
}

.pending-icon {
  position: relative;
}

.pending-icon::after {
  content: '';
  position: absolute;
  top: -10px;
  left: -10px;
  right: -10px;
  bottom: -10px;
  border: 2px solid #ffc107;
  border-radius: 50%;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.custom-toast {
  position: fixed;
  top: 30px;
  right: 30px;
  min-width: 300px;
  padding: 15px 20px;
  border-radius: 15px;
  color: white;
  z-index: 9999;
  animation: slideIn 0.3s ease, fadeOut 0.3s ease 4.7s;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.toast-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.toast-icon {
  font-size: 1.5rem;
  margin-right: 15px;
  flex-shrink: 0;
}

.toast-message {
  flex-grow: 1;
  font-weight: 500;
}

.toast-close {
  background: none;
  border: none;
  color: white;
  font-size: 1rem;
  cursor: pointer;
  opacity: 0.8;
  transition: opacity 0.3s;
  flex-shrink: 0;
  margin-left: 15px;
}

.toast-close:hover {
  opacity: 1;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes fadeOut {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
  }
}

@media (max-width: 768px) {
  .icon-wrapper {
    width: 90px;
    height: 90px;
  }
  
  .icon-wrapper i {
    font-size: 2.5rem;
  }
  
  .steps-container {
    padding: 0 10px;
  }
  
  .step-icon {
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
  }
  
  .steps-container::before,
  .steps-container::after {
    top: 25px;
    left: 25px;
    right: 25px;
  }
  
  .drop-zone {
    padding: 30px 15px;
    min-height: 250px;
  }
  
  .preview-overlay {
    opacity: 1;
    bottom: 10px;
  }
  
  .btn-lg {
    padding: 10px 20px;
    font-size: 0.9rem;
  }
}

@media (max-width: 576px) {
  .verification-page .container {
    padding-left: 15px;
    padding-right: 15px;
  }
  
  .steps-container {
    flex-direction: column;
    align-items: center;
    gap: 30px;
  }
  
  .steps-container::before,
  .steps-container::after {
    display: none;
  }
  
  .step {
    flex-direction: row;
    gap: 15px;
    width: 100%;
  }
  
  .step-label {
    text-align: left;
    margin-top: 0;
  }
  
  .benefit-card {
    padding: 20px 15px;
  }
}
</style>