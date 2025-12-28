<template>
  <AppLayout>
    <div v-if="isAnyModalOpen" class="modal-blur-overlay"></div>
    
    <div class="settings-page" :class="{'blur-background': isAnyModalOpen}">
      <div class="bg-shapes">
        <div class="shape-1"></div>
        <div class="shape-2"></div>
        <div class="shape-3"></div>
        <div class="shape-4"></div>
        <div class="shape-5"></div>
      </div>

      <div class="container py-5">
        <div class="row">
          <div class="col-lg-4 col-xl-3">
            <div class="profile-card">
              <div class="profile-header">
                <div class="profile-picture-wrapper">
                  <div class="profile-picture" @click="triggerProfilePictureInput">
                    <img v-if="user.profile_picture" :src="user.profile_picture" 
                         class="profile-img" alt="Profile">
                    <div v-else class="profile-initials">
                      {{ userInitials }}
                    </div>
                  </div>
                  <input type="file" ref="profilePictureInput" @change="handleProfilePicture" 
                         class="d-none" accept="image/*">
                </div>
              </div>
            </div>

            <div class="settings-nav">
              <div class="nav-header">
                <i class="fas fa-sliders-h"></i>
                <span>Navigation</span>
              </div>
              <div class="nav-items">
                <a href="#" class="nav-item" :class="{ 'active': activeSection === 'profile' }" @click.prevent="setActiveSection('profile')">
                  <i class="fas fa-user"></i>
                  <span>Profile</span>
                  <i class="fas fa-chevron-right"></i>
                </a>
                <a href="#" class="nav-item" :class="{ 'active': activeSection === 'security' }" @click.prevent="setActiveSection('security')">
                  <i class="fas fa-shield-alt"></i>
                  <span>Security</span>
                  <i class="fas fa-chevron-right"></i>
                </a>
                <a href="#" class="nav-item" :class="{ 'active': activeSection === 'account' }" @click.prevent="setActiveSection('account')" v-if="user.is_verified">
                  <i class="fas fa-university"></i>
                  <span>Bank Account</span>
                  <i class="fas fa-chevron-right"></i>
                </a>
                <a href="#" class="nav-item" :class="{ 'active': activeSection === 'documents' }" @click.prevent="setActiveSection('documents')">
                  <i class="fas fa-file-contract"></i>
                  <span>Legal Documents</span>
                  <i class="fas fa-chevron-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-8 col-xl-9">
            <div v-if="activeSection === 'profile'" class="settings-section">
              <div class="section-header">
                <div class="section-icon">
                  <i class="fas fa-user"></i>
                </div>
                <div>
                  <h2 class="section-title">Profile Information</h2>
                  <p class="section-subtitle">Update your personal details</p>
                </div>
                <button class="btn-edit-section" @click="editProfile">
                  <i class="fas fa-edit"></i> Edit
                </button>
              </div>

              <div class="section-content">
                <div class="profile-details">
                  <div class="detail-item">
                    <div class="detail-label">
                      <i class="fas fa-user-tag"></i> Full Name
                    </div>
                    <div class="detail-value">{{ user.name }}</div>
                  </div>
                  <div class="detail-item">
                    <div class="detail-label">
                      <i class="fas fa-envelope"></i> Email Address
                    </div>
                    <div class="detail-value">{{ user.email }}</div>
                  </div>
                  <div class="detail-item">
                    <div class="detail-label">
                      <i class="fas fa-phone"></i> Phone Number
                    </div>
                    <div class="detail-value">{{ user.phone || 'Not set' }}</div>
                  </div>
                  <div class="detail-item">
                    <div class="detail-label">
                      <i class="fas fa-user-circle"></i> User Type
                    </div>
                    <div class="detail-value">
                      <span class="user-type-badge">{{ getUserType() }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="activeSection === 'security'" class="settings-section">
              <div class="section-header">
                <div class="section-icon">
                  <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                  <h2 class="section-title">Security Settings</h2>
                  <p class="section-subtitle">Manage your password and account security</p>
                </div>
              </div>

              <div class="section-content">
                <div class="security-options">
                  <div class="security-card">
                    <div class="card-header">
                      <i class="fas fa-key"></i>
                      <h4>Change Password</h4>
                    </div>
                    <div class="card-body">
                      <p class="card-text">Update your account password for better security.</p>
                      <button class="btn-security-action" @click="openPasswordModal">
                        <i class="fas fa-lock"></i> Change Password
                      </button>
                    </div>
                  </div>

                  <div class="security-card">
                    <div class="card-header">
                      <i class="fas fa-mobile-alt"></i>
                      <h4>Two-Factor Authentication</h4>
                    </div>
                    <div class="card-body">
                      <p class="card-text">Add an extra layer of security to your account.</p>
                      <button class="btn-security-action disabled">
                        <i class="fas fa-qrcode"></i> Coming Soon
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="activeSection === 'account' && user.is_verified" class="settings-section">
              <div class="section-header">
                <div class="section-icon">
                  <i class="fas fa-university"></i>
                </div>
                <div>
                  <h2 class="section-title">Bank Account</h2>
                  <p class="section-subtitle">Manage your payment account details</p>
                </div>
                <button class="btn-edit-section" @click="openAccountModal">
                  <i class="fas fa-plus-circle"></i> 
                  {{ user.account_number ? 'Update' : 'Add' }} Account
                </button>
              </div>

              <div class="section-content">
                <div v-if="user.account_number" class="account-details">
                  <div class="account-card">
                    <div class="account-card-header">
                      <div class="bank-logo">
                        <i class="fas fa-landmark"></i>
                      </div>
                      <div class="bank-info">
                        <h4>Commercial Bank of Ethiopia (CBE)</h4>
                        <p class="text-muted">Primary Account</p>
                      </div>
                      <div class="account-status">
                        <span class="status-badge active">Active</span>
                      </div>
                    </div>
                    <div class="account-card-body">
                      <div class="account-number">
                        <span class="number-label">Account Number</span>
                        <span class="number-value">{{ user.account_number }}</span>
                      </div>
                      <div class="account-actions">
                        <button class="btn-action copy-btn" @click="copyAccountNumber">
                          <i class="fas fa-copy"></i> Copy
                        </button>
                        <button class="btn-action edit-btn" @click="openAccountModal">
                          <i class="fas fa-edit"></i> Edit
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="account-empty">
                  <div class="empty-state">
                    <i class="fas fa-university fa-4x"></i>
                    <h4>No Bank Account Added</h4>
                    <p>Add your CBE account number to receive payments</p>
                    <button class="btn-add-account" @click="openAccountModal">
                      <i class="fas fa-plus-circle"></i> Add Account
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="activeSection === 'documents'" class="settings-section">
              <div class="section-header">
                <div class="section-icon">
                  <i class="fas fa-file-contract"></i>
                </div>
                <div>
                  <h2 class="section-title">Legal Documents</h2>
                  <p class="section-subtitle">Important policies and agreements</p>
                </div>
              </div>

              <div class="section-content">
                <div class="documents-grid">
                  <div class="document-card" @click="openDocumentModal('privacy')">
                    <div class="document-icon">
                      <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Privacy Policy</h4>
                    <p>How we protect and use your data</p>
                    <div class="document-action">
                      <i class="fas fa-external-link-alt"></i> View
                    </div>
                  </div>

                  <div class="document-card" @click="openDocumentModal('terms')">
                    <div class="document-icon">
                      <i class="fas fa-file-signature"></i>
                    </div>
                    <h4>Terms & Conditions</h4>
                    <p>Rules for using our platform</p>
                    <div class="document-action">
                      <i class="fas fa-external-link-alt"></i> View
                    </div>
                  </div>

                  <div class="document-card">
                    <div class="document-icon">
                      <i class="fas fa-question-circle"></i>
                    </div>
                    <h4>Help Center</h4>
                    <p>Get help and support</p>
                    <a href="mailto:temuclassic986@gmail.com" class="document-action">
                      <i class="fas fa-envelope"></i> Contact
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="!activeSection" class="settings-section">
              <div class="section-header">
                <div class="section-icon">
                  <i class="fas fa-info-circle"></i>
                </div>
                <div>
                  <h2 class="section-title">Welcome to Settings</h2>
                  <p class="section-subtitle">Select a section from the navigation menu to get started</p>
                </div>
              </div>
              <div class="section-content text-center py-5">
                <i class="fas fa-sliders-h fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Select a Setting Category</h4>
                <p class="text-muted">Choose from Profile, Security, Bank Account, or Legal Documents</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showProfileEditModal" class="modal-backdrop fade show">
        <div class="modal fade show d-block" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-blur-container">
            <div class="modal-content border-0 shadow-lg modal-blur-content">
              <div class="modal-header bg-gradient-primary text-white border-bottom-0 rounded-top">
                <h5 class="modal-title fw-bold">
                  <i class="fas fa-user-edit me-2"></i>Edit Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" @click="closeProfileEditModal"></button>
              </div>
              
              <form @submit.prevent="updateProfile">
                <div class="modal-body">
                  <div class="row align-items-center mb-4">
                    <div class="col-auto">
                      <div class="profile-picture-edit" @click="triggerEditProfilePicture">
                        <img v-if="previewImage" :src="previewImage" 
                             class="profile-img-edit" alt="Profile">
                        <img v-else-if="user.profile_picture" :src="user.profile_picture" 
                             class="profile-img-edit" alt="Profile">
                        <div v-else class="profile-initials-edit">
                          {{ userInitials }}
                        </div>
                        <div class="profile-edit-overlay">
                          <i class="fas fa-camera"></i>
                        </div>
                      </div>
                      <input type="file" ref="editProfilePictureInput" @change="handleEditProfilePicture" 
                             class="d-none" accept="image/*">
                    </div>
                    <div class="col">
                      <h6 class="mb-1">Profile Picture</h6>
                      <p class="text-muted small mb-0">Click on the picture to change</p>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">
                        <i class="fas fa-user me-2"></i>Full Name *
                      </label>
                      <input type="text" class="form-control" v-model="profileForm.name" 
                            :class="{'is-invalid': profileForm.errors.name}"
                            required placeholder="Enter your full name">
                      <div class="invalid-feedback" v-if="profileForm.errors.name">
                        {{ profileForm.errors.name }}
                      </div>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">
                        <i class="fas fa-envelope me-2"></i>Email Address *
                      </label>
                      <input type="email" class="form-control" v-model="profileForm.email"
                            :class="{'is-invalid': profileForm.errors.email}"
                            required placeholder="Enter your email">
                      <div class="invalid-feedback" v-if="profileForm.errors.email">
                        {{ profileForm.errors.email }}
                      </div>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">
                        <i class="fas fa-phone me-2"></i>Phone Number
                      </label>
                      <input type="tel" class="form-control" v-model="profileForm.phone"
                            :class="{'is-invalid': profileForm.errors.phone}"
                            placeholder="Enter your phone number">
                      <div class="invalid-feedback" v-if="profileForm.errors.phone">
                        {{ profileForm.errors.phone }}
                      </div>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">
                        <i class="fas fa-user-tag me-2"></i>User Type
                      </label>
                      <div class="form-control-static">
                        <span class="badge bg-primary">{{ getUserType() }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Changes will be saved immediately. No password required for basic profile updates.
                  </div>
                </div>
                <div class="modal-footer border-top-0">
                  <button type="button" class="btn btn-light" @click="closeProfileEditModal">
                    Cancel
                  </button>
                  <button type="submit" class="btn btn-gradient-primary" :disabled="profileForm.processing">
                    <span v-if="profileForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                    Save Changes
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showPasswordModal" class="modal-backdrop fade show">
        <div class="modal fade show d-block" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-blur-container">
            <div class="modal-content border-0 shadow-lg modal-blur-content">
              <div class="modal-header bg-gradient-warning text-white border-bottom-0 rounded-top">
                <h5 class="modal-title fw-bold">
                  <i class="fas fa-lock me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close btn-close-white" @click="closePasswordModal"></button>
              </div>
              
              <form @submit.prevent="updatePassword">
                <div class="modal-body">
                  <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Security Check:</strong> Enter your current password to change it.
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Current Password *</label>
                    <div class="input-group">
                      <input :type="showCurrentPassword ? 'text' : 'password'" 
                            class="form-control" v-model="passwordForm.current_password"
                            :class="{'is-invalid': passwordForm.errors.current_password}"
                            required placeholder="Enter current password">
                      <button class="btn btn-outline-secondary" type="button" @click="toggleCurrentPassword">
                        <i :class="showCurrentPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      </button>
                    </div>
                    <div class="invalid-feedback" v-if="passwordForm.errors.current_password">
                      {{ passwordForm.errors.current_password }}
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">New Password *</label>
                    <div class="input-group">
                      <input :type="showNewPassword ? 'text' : 'password'" 
                            class="form-control" v-model="passwordForm.password"
                            :class="{'is-invalid': passwordForm.errors.password}"
                            required placeholder="Enter new password">
                      <button class="btn btn-outline-secondary" type="button" @click="toggleNewPassword">
                        <i :class="showNewPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      </button>
                    </div>
                    <div class="invalid-feedback" v-if="passwordForm.errors.password">
                      {{ passwordForm.errors.password }}
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Confirm New Password *</label>
                    <div class="input-group">
                      <input :type="showConfirmPassword ? 'text' : 'password'" 
                            class="form-control" v-model="passwordForm.password_confirmation"
                            :class="{'is-invalid': passwordForm.errors.password_confirmation}"
                            required placeholder="Confirm new password">
                      <button class="btn btn-outline-secondary" type="button" @click="toggleConfirmPassword">
                        <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      </button>
                    </div>
                    <div class="invalid-feedback" v-if="passwordForm.errors.password_confirmation">
                      {{ passwordForm.errors.password_confirmation }}
                    </div>
                  </div>

                  <div class="alert alert-light">
                    <small class="fw-bold">Password Requirements:</small>
                    <ul class="small mb-0">
                      <li>At least 8 characters</li>
                      <li>Include uppercase and lowercase letters</li>
                      <li>Include at least one number</li>
                    </ul>
                  </div>
                </div>
                <div class="modal-footer border-top-0">
                  <button type="button" class="btn btn-light" @click="closePasswordModal">
                    Cancel
                  </button>
                  <button type="submit" class="btn btn-gradient-warning" :disabled="passwordForm.processing">
                    <span v-if="passwordForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                    Update Password
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showAccountModal" class="modal-backdrop fade show">
        <div class="modal fade show d-block" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-blur-container">
            <div class="modal-content border-0 shadow-lg modal-blur-content">
              <div class="modal-header bg-gradient-success text-white border-bottom-0 rounded-top">
                <h5 class="modal-title fw-bold">
                  <i class="fas fa-university me-2"></i>
                  {{ user.account_number ? 'Update' : 'Add' }} Bank Account
                </h5>
                <button type="button" class="btn-close btn-close-white" @click="closeAccountModal"></button>
              </div>
              
              <form @submit.prevent="updateAccountNumber">
                <div class="modal-body">
                  <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Verified Account:</strong> You can add or update your bank account.
                  </div>

                  <div class="mb-4">
                    <label class="form-label fw-bold">
                      <i class="fas fa-credit-card me-2"></i>Bank Account Number *
                    </label>
                    <div class="input-group input-group-lg">
                      <span class="input-group-text bg-light">
                        <i class="fas fa-landmark text-success"></i>
                      </span>
                      <input type="text" class="form-control form-control-lg" 
                            v-model="accountForm.account_number"
                            :class="{'is-invalid': accountForm.errors.account_number}"
                            required placeholder="Enter your CBE account number">
                      <span class="input-group-text bg-success text-white">
                        CBE
                      </span>
                    </div>
                    <div class="form-text text-muted mt-2">
                      <i class="fas fa-info-circle me-1"></i>
                      Only Commercial Bank of Ethiopia (CBE) account numbers are accepted
                    </div>
                    <div class="invalid-feedback" v-if="accountForm.errors.account_number">
                      {{ accountForm.errors.account_number }}
                    </div>
                  </div>

                  <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Important:</strong> This account will be used for receiving payments from customers. 
                    Please double-check the account number before saving.
                  </div>
                </div>
                <div class="modal-footer border-top-0">
                  <button type="button" class="btn btn-light" @click="closeAccountModal">
                    Cancel
                  </button>
                  <button type="submit" class="btn btn-gradient-success" :disabled="accountForm.processing">
                    <span v-if="accountForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                    {{ user.account_number ? 'Update' : 'Save' }} Account
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showDocumentModal" class="modal-backdrop fade show">
        <div class="modal fade show d-block" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-blur-container">
            <div class="modal-content border-0 shadow-lg modal-blur-content">
              <div class="modal-header" :class="documentType === 'privacy' ? 'bg-primary text-white' : 'bg-success text-white'">
                <h5 class="modal-title fw-bold">
                  <i :class="documentType === 'privacy' ? 'fas fa-shield-alt me-2' : 'fas fa-file-signature me-2'"></i>
                  {{ documentType === 'privacy' ? 'Privacy Policy' : 'Terms & Conditions' }}
                </h5>
                <button type="button" class="btn-close btn-close-white" @click="closeDocumentModal"></button>
              </div>
              
              <div class="modal-body">
                <div v-if="documentType === 'privacy'" class="document-content">
                  <h4 class="fw-bold mb-4">Privacy Policy</h4>
                  
                  <h5 class="fw-bold mt-4 mb-3">1. Information We Collect</h5>
                  <p class="text-muted">We collect information that you provide directly to us, including:</p>
                  <ul class="text-muted">
                    <li>Name and contact information</li>
                    <li>Account credentials</li>
                    <li>Payment information</li>
                    <li>Communication preferences</li>
                  </ul>

                  <h5 class="fw-bold mt-4 mb-3">2. How We Use Your Information</h5>
                  <p class="text-muted">We use the information we collect to:</p>
                  <ul class="text-muted">
                    <li>Provide and maintain our services</li>
                    <li>Process transactions</li>
                    <li>Send you important updates</li>
                    <li>Improve our platform</li>
                  </ul>

                  <h5 class="fw-bold mt-4 mb-3">3. Data Security</h5>
                  <p class="text-muted">We implement appropriate security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction.</p>

                  <h5 class="fw-bold mt-4 mb-3">4. Your Rights</h5>
                  <p class="text-muted">You have the right to:</p>
                  <ul class="text-muted">
                    <li>Access your personal data</li>
                    <li>Correct inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Object to data processing</li>
                  </ul>

                  <div class="alert alert-light border mt-4">
                    <p class="mb-0"><strong>Last Updated:</strong> {{ new Date().getFullYear() }}</p>
                  </div>
                </div>

                <div v-else class="document-content">
                  <h4 class="fw-bold mb-4">Terms & Conditions</h4>
                  
                  <h5 class="fw-bold mt-4 mb-3">1. Acceptance of Terms</h5>
                  <p class="text-muted">By accessing and using this platform, you accept and agree to be bound by these Terms and Conditions.</p>

                  <h5 class="fw-bold mt-4 mb-3">2. User Responsibilities</h5>
                  <p class="text-muted">As a user, you agree to:</p>
                  <ul class="text-muted">
                    <li>Provide accurate information</li>
                    <li>Maintain account security</li>
                    <li>Use the platform legally</li>
                    <li>Respect other users</li>
                  </ul>

                  <h5 class="fw-bold mt-4 mb-3">3. Transactions</h5>
                  <p class="text-muted">All transactions are final. We are not responsible for:</p>
                  <ul class="text-muted">
                    <li>Product quality from vendors</li>
                    <li>Delivery issues</li>
                    <li>Payment disputes between users</li>
                  </ul>

                  <h5 class="fw-bold mt-4 mb-3">4. Platform Rules</h5>
                  <p class="text-muted">You must not:</p>
                  <ul class="text-muted">
                    <li>Use the platform for illegal activities</li>
                    <li>Share inappropriate content</li>
                    <li>Attempt to hack the system</li>
                    <li>Create multiple accounts</li>
                  </ul>

                  <div class="alert alert-light border mt-4">
                    <p class="mb-0"><strong>Effective Date:</strong> {{ new Date().getFullYear() }}</p>
                  </div>
                </div>
              </div>
              
              <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light" @click="closeDocumentModal">
                  Close
                </button>
                <button type="button" :class="documentType === 'privacy' ? 'btn btn-primary' : 'btn btn-success'" @click="closeDocumentModal">
                  I Understand
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  user: {
    type: Object,
    default: () => ({})
  }
})

const activeSection = ref('documents')

const showProfileEditModal = ref(false)
const showPasswordModal = ref(false)
const showAccountModal = ref(false)
const showDocumentModal = ref(false)
const documentType = ref('privacy')

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const previewImage = ref(null)
const profilePictureInput = ref(null)
const editProfilePictureInput = ref(null)

const isAnyModalOpen = computed(() => {
  return showProfileEditModal.value || 
         showPasswordModal.value || 
         showAccountModal.value || 
         showDocumentModal.value
})

const userInitials = computed(() => {
  if (!props.user.name) return 'U'
  return props.user.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .substring(0, 2)
})

const getUserType = () => {
  return props.user.is_verified ? 'Vendor' : 'Customer'
}

const profileForm = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  profile_picture: null,
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const accountForm = useForm({
  account_number: props.user.account_number || '',
})

const setActiveSection = (section) => {
  activeSection.value = section
}

const triggerProfilePictureInput = () => {
  profilePictureInput.value.click()
}

const triggerEditProfilePicture = () => {
  editProfilePictureInput.value.click()
}

const handleProfilePicture = (event) => {
  const file = event.target.files[0]
  if (file) {
    const formData = new FormData()
    formData.append('profile_picture', file)
    
    useForm(formData).post(route('settings.profile-picture.update'), {
      preserveScroll: true,
      onSuccess: () => {
        profilePictureInput.value.value = ''
      }
    })
  }
}

const handleEditProfilePicture = (event) => {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImage.value = e.target.result
    }
    reader.readAsDataURL(file)
    
    profileForm.profile_picture = file
  }
}

const editProfile = () => {
  profileForm.name = props.user.name
  profileForm.email = props.user.email
  profileForm.phone = props.user.phone || ''
  profileForm.profile_picture = null
  previewImage.value = null
  showProfileEditModal.value = true
}

const updateProfile = () => {
  if (profileForm.profile_picture) {
    const formData = new FormData()
    formData.append('name', profileForm.name)
    formData.append('email', profileForm.email)
    formData.append('phone', profileForm.phone || '')
    formData.append('profile_picture', profileForm.profile_picture)
    
    profileForm.transform(() => formData)
      .post(route('settings.profile.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
          closeProfileEditModal()
        }
      })
  } else {
    profileForm.put(route('settings.profile.update'), {
      preserveScroll: true,
      onSuccess: () => {
        closeProfileEditModal()
      }
    })
  }
}

const closeProfileEditModal = () => {
  showProfileEditModal.value = false
  profileForm.clearErrors()
}

const openPasswordModal = () => {
  passwordForm.reset()
  passwordForm.clearErrors()
  showCurrentPassword.value = false
  showNewPassword.value = false
  showConfirmPassword.value = false
  showPasswordModal.value = true
}

const closePasswordModal = () => {
  showPasswordModal.value = false
  passwordForm.reset()
  passwordForm.clearErrors()
}

const updatePassword = () => {
  passwordForm.put(route('settings.password.update'), {
    preserveScroll: true,
    onSuccess: () => {
      closePasswordModal()
    }
  })
}

const openAccountModal = () => {
  if (!props.user.is_verified) {
    alert('You must be verified to add a bank account. Please complete verification first.')
    window.location.href = route('verification.request')
    return
  }
  
  accountForm.account_number = props.user.account_number || ''
  accountForm.clearErrors()
  showAccountModal.value = true
}

const closeAccountModal = () => {
  showAccountModal.value = false
  accountForm.reset()
  accountForm.clearErrors()
}

const updateAccountNumber = () => {
  accountForm.put(route('settings.account-number.update'), {
    preserveScroll: true,
    onSuccess: () => {
      closeAccountModal()
    }
  })
}

const openDocumentModal = (type) => {
  documentType.value = type
  showDocumentModal.value = true
}

const closeDocumentModal = () => {
  showDocumentModal.value = false
}

const copyAccountNumber = () => {
  if (props.user.account_number) {
    navigator.clipboard.writeText(props.user.account_number)
      .then(() => {
        alert('Account number copied to clipboard!')
      })
      .catch(err => {
        console.error('Failed to copy: ', err)
      })
  }
}

const toggleCurrentPassword = () => {
  showCurrentPassword.value = !showCurrentPassword.value
}

const toggleNewPassword = () => {
  showNewPassword.value = !showNewPassword.value
}

const toggleConfirmPassword = () => {
  showConfirmPassword.value = !showConfirmPassword.value
}

onMounted(() => {
  activeSection.value = 'documents'
})
</script>

<style scoped>
.settings-page {
  position: relative;
  min-height: 100vh;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.bg-shapes {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 0;
}

.bg-shapes .shape-1,
.bg-shapes .shape-2,
.bg-shapes .shape-3,
.bg-shapes .shape-4,
.bg-shapes .shape-5 {
  position: absolute;
  border-radius: 50%;
  background: linear-gradient(45deg, rgba(var(--bs-primary-rgb, 13, 110, 253), 0.05), rgba(var(--bs-success-rgb, 25, 135, 84), 0.05));
  animation: float 20s infinite linear;
}

.bg-shapes .shape-1 { width: 300px; height: 300px; top: -150px; right: -100px; animation-delay: 0s; }
.bg-shapes .shape-2 { width: 200px; height: 200px; bottom: -100px; left: -50px; animation-delay: 5s; }
.bg-shapes .shape-3 { width: 150px; height: 150px; top: 50%; right: 10%; animation-delay: 10s; }
.bg-shapes .shape-4 { width: 100px; height: 100px; bottom: 30%; left: 10%; animation-delay: 15s; }
.bg-shapes .shape-5 { width: 120px; height: 120px; top: 20%; left: 5%; animation-delay: 7s; }

@keyframes float {
  0% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(180deg); }
  100% { transform: translateY(0) rotate(360deg); }
}

.profile-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  margin-bottom: 2rem;
  position: relative;
  z-index: 1;
}

.profile-header {
  padding: 2rem;
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  color: white;
  position: relative;
}

.profile-picture-wrapper {
  position: relative;
  width: 120px;
  height: 120px;
  margin: 0 auto 1.5rem;
}

.profile-picture {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  overflow: hidden;
  border: 4px solid white;
  cursor: pointer;
  position: relative;
}

.profile-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-initials {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, var(--bs-success), #20c997);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2.5rem;
  font-weight: bold;
}

.settings-nav {
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  position: relative;
  z-index: 1;
}

.nav-header {
  padding: 1.5rem;
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  color: white;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.nav-items {
  padding: 1rem 0;
}

.nav-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  color: #495057;
  text-decoration: none;
  transition: all 0.3s;
  border-left: 4px solid transparent;
}

.nav-item:hover {
  background: #f8f9fa;
  color: var(--bs-primary);
  border-left-color: var(--bs-primary);
}

.nav-item.active {
  background: linear-gradient(90deg, rgba(var(--bs-primary-rgb), 0.1), transparent);
  color: var(--bs-primary);
  border-left-color: var(--bs-primary);
  font-weight: 600;
}

.nav-item i:first-child {
  width: 24px;
  margin-right: 0.75rem;
}

.settings-section {
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
  overflow: hidden;
  position: relative;
  z-index: 1;
}

.section-header {
  padding: 1.5rem 2rem;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  border-bottom: 1px solid #f1f3f4;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.section-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
  color: #2c3e50;
}

.section-subtitle {
  color: #6c757d;
  margin: 0;
}

.btn-edit-section {
  margin-left: auto;
  padding: 0.5rem 1.5rem;
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  color: white;
  border: none;
  border-radius: 50px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.btn-edit-section:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(var(--bs-primary-rgb), 0.3);
}

.section-content {
  padding: 2rem;
}

.profile-details {
  display: grid;
  gap: 1.5rem;
}

.detail-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 12px;
  transition: all 0.3s;
}

.detail-item:hover {
  background: #e9ecef;
  transform: translateX(5px);
}

.detail-label {
  flex: 0 0 200px;
  font-weight: 600;
  color: #495057;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.detail-value {
  flex: 1;
  color: #2c3e50;
  font-size: 1.1rem;
}

.user-type-badge {
  padding: 0.25rem 1rem;
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  color: white;
  border-radius: 20px;
  font-weight: 600;
}

.security-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.security-card {
  background: white;
  border: 2px solid #f1f3f4;
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.3s;
}

.security-card:hover {
  border-color: var(--bs-primary);
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.card-header {
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.card-header i {
  font-size: 1.5rem;
  color: var(--bs-primary);
}

.card-header h4 {
  margin: 0;
  font-size: 1.25rem;
}

.card-body {
  padding: 1.5rem;
}

.card-text {
  color: #6c757d;
  margin-bottom: 1.5rem;
}

.btn-security-action {
  width: 100%;
  padding: 0.75rem;
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.btn-security-action:hover:not(.disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(var(--bs-primary-rgb), 0.3);
}

.btn-security-action.disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.account-details {
  max-width: 600px;
}

.account-card {
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  border-radius: 16px;
  overflow: hidden;
  border: 2px solid #dee2e6;
}

.account-card-header {
  padding: 1.5rem;
  background: white;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-bottom: 1px solid #f1f3f4;
}

.bank-logo {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, var(--bs-success), #20c997);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.bank-info h4 {
  margin: 0;
  font-size: 1.25rem;
}

.account-status {
  margin-left: auto;
}

.status-badge {
  padding: 0.25rem 1rem;
  background: linear-gradient(135deg, var(--bs-success), #20c997);
  color: white;
  border-radius: 20px;
  font-weight: 600;
}

.account-card-body {
  padding: 1.5rem;
}

.account-number {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: white;
  border-radius: 8px;
}

.number-label {
  font-weight: 600;
  color: #495057;
}

.number-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--bs-success);
  font-family: 'Courier New', monospace;
}

.account-actions {
  display: flex;
  gap: 1rem;
}

.btn-action {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.copy-btn {
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  color: white;
}

.edit-btn {
  background: linear-gradient(135deg, var(--bs-warning), #fd7e14);
  color: white;
}

.btn-action:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.account-empty {
  text-align: center;
  padding: 3rem 2rem;
}

.empty-state i {
  color: #dee2e6;
  margin-bottom: 1.5rem;
}

.empty-state h4 {
  color: #6c757d;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #adb5bd;
  margin-bottom: 2rem;
}

.btn-add-account {
  padding: 0.75rem 2rem;
  background: linear-gradient(135deg, var(--bs-success), #20c997);
  color: white;
  border: none;
  border-radius: 50px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.btn-add-account:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(var(--bs-success-rgb), 0.3);
}

.documents-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.document-card {
  background: white;
  border: 2px solid #f1f3f4;
  border-radius: 16px;
  padding: 2rem;
  text-align: center;
  transition: all 0.3s;
  cursor: pointer;
}

.document-card:hover {
  border-color: var(--bs-primary);
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.document-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.5rem;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: var(--bs-primary);
}

.document-card h4 {
  margin-bottom: 0.5rem;
  color: #2c3e50;
}

.document-card p {
  color: #6c757d;
  margin-bottom: 1.5rem;
}

.document-action {
  color: var(--bs-primary);
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
}

.profile-picture-edit {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid var(--bs-primary);
  cursor: pointer;
  position: relative;
}

.profile-img-edit {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-initials-edit {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2rem;
  font-weight: bold;
}

.profile-edit-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
  color: white;
  font-size: 1.5rem;
}

.profile-picture-edit:hover .profile-edit-overlay {
  opacity: 1;
}

.btn-gradient-primary {
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  border: none;
  color: white;
}

.btn-gradient-warning {
  background: linear-gradient(135deg, var(--bs-warning), #fd7e14);
  border: none;
  color: white;
}

.btn-gradient-success {
  background: linear-gradient(135deg, var(--bs-success), #20c997);
  border: none;
  color: white;
}

.document-content {
  padding: 0.5rem;
}

.document-content h4 {
  color: #2c3e50;
  border-bottom: 2px solid var(--bs-primary);
  padding-bottom: 0.5rem;
  margin-bottom: 1.5rem;
}

.document-content h5 {
  color: #495057;
  margin-top: 1.5rem;
  padding-left: 0.75rem;
  border-left: 4px solid var(--bs-primary);
}

.document-content .text-muted {
  color: #6c757d;
  line-height: 1.6;
}

.document-content ul {
  padding-left: 1.5rem;
  margin-bottom: 1rem;
}

.document-content li {
  margin-bottom: 0.5rem;
  position: relative;
  padding-left: 0.5rem;
}

.document-content li::before {
  content: "•";
  color: var(--bs-primary);
  font-weight: bold;
  position: absolute;
  left: -0.75rem;
}

.modal-blur-overlay {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1039;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
}

.blur-background {
  transition: filter 0.3s ease;
  pointer-events: none;
  user-select: none;
}

.modal-blur-container {
  z-index: 1041 !important;
  position: relative;
}

.modal-blur-content {
  z-index: 1042 !important;
  position: relative;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-backdrop.fade.show {
  background-color: rgba(0, 0, 0, 0.7) !important;
  backdrop-filter: blur(5px);
  -webkit-backdrop-filter: blur(5px);
}

@media (max-width: 992px) {
  .security-options {
    grid-template-columns: 1fr;
  }
  
  .documents-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .detail-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .detail-label {
    flex: none;
  }
  
  .account-number {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .account-actions {
    flex-direction: column;
  }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.settings-section {
  animation: fadeIn 0.5s ease-out;
}

::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f3f4;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, var(--bs-info), var(--bs-primary));
}
</style>