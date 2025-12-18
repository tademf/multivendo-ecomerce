<template>
  <AppLayout>
    <!-- Blur overlay when any modal is open -->
    <div v-if="isAnyModalOpen" class="modal-blur-overlay"></div>
    
    <div class="container-fluid py-5 bg-light" :class="{'blur-background': isAnyModalOpen}">
      <div class="container">
        <!-- Header -->
        <div class="row mb-5">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h1 class="display-5 fw-bold text-dark mb-2">
                  <i class="fas fa-cog me-3 text-primary"></i>Settings
                </h1>
                <p class="text-muted mb-0">Manage your account preferences and settings</p>
              </div>
              <div class="badge bg-success" v-if="user.is_verified">
                <i class="fas fa-check-circle me-1"></i>Verified
              </div>
              <div class="badge bg-warning" v-else>
                <i class="fas fa-exclamation-triangle me-1"></i>Not Verified
              </div>
            </div>
          </div>
        </div>

        <!-- User Profile Card -->
        <div class="row mb-5">
          <div class="col-lg-4">
            <div class="card border-0 shadow-lg h-100">
              <div class="card-body text-center p-5">
                <div class="mb-4 position-relative">
                  <div class="avatar-lg mx-auto position-relative">
                    <img v-if="user.profile_picture" :src="user.profile_picture" 
                         class="avatar-title rounded-circle display-4 text-white"
                         style="width: 120px; height: 120px; object-fit: cover;">
                    <div v-else class="avatar-title bg-primary bg-gradient rounded-circle display-4 text-white">
                      {{ userInitials }}
                    </div>
                    <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle"
                            @click="triggerFileInput" title="Change profile picture">
                      <i class="fas fa-camera"></i>
                    </button>
                    <input type="file" ref="fileInput" @change="handleProfilePicture" class="d-none" 
                           accept="image/*" id="profile-picture-main" name="profile_picture_main">
                  </div>
                </div>
                <h4 class="card-title fw-bold">{{ user.name }}</h4>
                <p class="text-muted mb-3">
                  <i class="fas fa-envelope me-2"></i>{{ user.email }}
                </p>
                <div class="d-grid gap-2">
                  <button class="btn btn-outline-primary" @click="openProfileEdit">
                    <i class="fas fa-user-edit me-2"></i>Edit Profile
                  </button>
                </div>
              </div>
              <div class="card-footer bg-transparent border-top">
                <div class="row text-center">
                  <div class="col-6 border-end">
                    <h5 class="fw-bold mb-1">Member Since</h5>
                    <p class="text-muted mb-0">{{ formattedDate(user.created_at) }}</p>
                  </div>
                  <div class="col-6">
                    <h5 class="fw-bold mb-1">Account No</h5>
                    <p class="text-muted mb-0" v-if="user.account_number">{{ user.account_number }}</p>
                    <button v-else class="btn btn-link btn-sm p-0" @click="handleAccountNumberUpdate">
                      <i class="fas fa-plus me-1"></i>Add Account
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Settings Options -->
          <div class="col-lg-8">
            <div class="row g-4">
              <!-- Privacy & Policy -->
              <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                  <div class="card-body p-4">
                    <div class="d-flex align-items-start mb-3">
                      <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                        <i class="fas fa-shield-alt fa-2x text-primary"></i>
                      </div>
                      <div>
                        <h5 class="card-title fw-bold mb-1">Privacy & Policy</h5>
                        <p class="text-muted small">View our privacy terms</p>
                      </div>
                    </div>
                    <p class="card-text text-muted mb-4">
                      Read about how we collect, use, and protect your personal information.
                    </p>
                    <button class="btn btn-outline-primary w-100" @click="showPrivacyModal = true">
                      <i class="fas fa-file-contract me-2"></i>View Privacy Policy
                    </button>
                  </div>
                </div>
              </div>

              <!-- Terms & Conditions -->
              <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                  <div class="card-body p-4">
                    <div class="d-flex align-items-start mb-3">
                      <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                        <i class="fas fa-file-signature fa-2x text-success"></i>
                      </div>
                      <div>
                        <h5 class="card-title fw-bold mb-1">Terms & Conditions</h5>
                        <p class="text-muted small">Read our terms of service</p>
                      </div>
                    </div>
                    <p class="card-text text-muted mb-4">
                      Understand the rules and guidelines for using our platform.
                    </p>
                    <button class="btn btn-outline-success w-100" @click="showTermsModal = true">
                      <i class="fas fa-balance-scale me-2"></i>View Terms
                    </button>
                  </div>
                </div>
              </div>

              <!-- Account Security -->
              <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                  <div class="card-body p-4">
                    <div class="d-flex align-items-start mb-3">
                      <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                        <i class="fas fa-lock fa-2x text-warning"></i>
                      </div>
                      <div>
                        <h5 class="card-title fw-bold mb-1">Account Security</h5>
                        <p class="text-muted small">Secure your account</p>
                      </div>
                    </div>
                    <p class="card-text text-muted mb-4">
                      Manage your account security settings and preferences.
                    </p>
                    <button class="btn btn-outline-warning w-100" @click="handleAccountNumberUpdate">
                      <i class="fas fa-university me-2"></i>Update Account Number
                    </button>
                  </div>
                </div>
              </div>

              <!-- Help & Support -->
              <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                  <div class="card-body p-4">
                    <div class="d-flex align-items-start mb-3">
                      <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                        <i class="fas fa-question-circle fa-2x text-info"></i>
                      </div>
                      <div>
                        <h5 class="card-title fw-bold mb-1">Help & Support</h5>
                        <p class="text-muted small">Get help and support</p>
                      </div>
                    </div>
                    <p class="card-text text-muted mb-4">
                      Contact our support team for assistance with any issues.
                    </p>
                    <a href="mailto:temuclassic986@gmail.com" class="btn btn-outline-info w-100">
                      <i class="fas fa-headset me-2"></i>Contact Support
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-5">
          <div class="col-12">
            <div class="card border-0 shadow-sm">
              <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-4">
                  <i class="fas fa-chart-bar me-2 text-primary"></i>Quick Overview
                </h5>
                <div class="row text-center">
                  <div class="col-md-3 mb-3 mb-md-0">
                    <div class="border-end-md">
                      <h2 class="fw-bold text-primary mb-1">0</h2>
                      <p class="text-muted mb-0">Total Orders</p>
                    </div>
                  </div>
                  <div class="col-md-3 mb-3 mb-md-0">
                    <div class="border-end-md">
                      <h2 class="fw-bold text-success mb-1">0</h2>
                      <p class="text-muted mb-0">Wishlist Items</p>
                    </div>
                  </div>
                  <div class="col-md-3 mb-3 mb-md-0">
                    <div class="border-end-md">
                      <h2 class="fw-bold text-warning mb-1" v-if="user.is_verified">100%</h2>
                      <h2 class="fw-bold text-danger mb-1" v-else>0%</h2>
                      <p class="text-muted mb-0">Account Verified</p>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div>
                      <h2 class="fw-bold text-info mb-1">Active</h2>
                      <p class="text-muted mb-0">Account Status</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- =========================================== -->
      <!-- PASSWORD VERIFICATION MODAL -->
      <!-- =========================================== -->
      <div v-if="showPasswordVerifyModal" class="modal-backdrop fade show">
        <div class="modal fade show d-block" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-blur-container">
            <div class="modal-content border-0 shadow-lg modal-blur-content">
              <div class="modal-header bg-primary text-white border-bottom-0 rounded-top">
                <h5 class="modal-title fw-bold">
                  <i class="fas fa-lock me-2"></i>Step 1: Verify Your Identity
                </h5>
                <button type="button" class="btn-close btn-close-white" @click="closePasswordVerifyModal"></button>
              </div>
              <form @submit.prevent="verifyPassword">
                <div class="modal-body">
                  <div class="alert alert-info border mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Security Check:</strong> Enter your current password to proceed to profile editing.
                  </div>
                  <div class="mb-3">
                    <label for="current-password-verify" class="form-label fw-bold">Current Password *</label>
                    <div class="input-group border rounded">
                      <input :type="showPassword ? 'text' : 'password'" 
                            id="current-password-verify"
                            name="current_password" 
                            class="form-control border-0" 
                            v-model="passwordForm.current_password" 
                            :class="{'is-invalid': passwordForm.errors.current_password}"
                            required
                            placeholder="Enter your current password"
                            autocomplete="current-password">
                      <button class="btn btn-outline-secondary border-0" type="button" @click="togglePasswordVisibility">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      </button>
                    </div>
                    <div class="invalid-feedback" v-if="passwordForm.errors.current_password">
                      {{ passwordForm.errors.current_password }}
                    </div>
                    <small class="text-muted">Password verification is required for security.</small>
                  </div>
                </div>
                <div class="modal-footer border-top-0">
                  <button type="button" class="btn btn-secondary" @click="closePasswordVerifyModal">
                    <i class="fas fa-times me-2"></i>Cancel
                  </button>
                  <button type="submit" class="btn btn-primary" :disabled="passwordForm.processing">
                    <span v-if="passwordForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                    <i class="fas fa-check me-2"></i>Verify & Continue
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================================== -->
      <!-- PROFILE EDIT MODAL (Step 2) -->
      <!-- ==================================== -->
      <div v-if="showProfileEditModal" class="modal-backdrop fade show">
        <div class="modal fade show d-block" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-blur-container">
            <div class="modal-content border-0 shadow-lg modal-blur-content">
              <!-- Header - Same as Privacy Policy -->
              <div class="modal-header bg-primary text-white border-bottom-0 rounded-top">
                <h5 class="modal-title fw-bold">
                  <i class="fas fa-user-edit me-2"></i>Step 2: Edit Your Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" @click="closeProfileEditModal"></button>
              </div>
              
              <form @submit.prevent="updateProfile">
                <div class="modal-body">
                  <div class="alert alert-success border mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Identity Verified!</strong> You can now edit your profile information.
                  </div>
                  
                  <div class="row">
                    <!-- Profile Picture Upload -->
                    <div class="col-md-4 mb-4">
                      <div class="text-center">
                        <div class="mb-3 position-relative d-inline-block">
                          <div class="rounded-circle overflow-hidden border border-3 border-primary" style="width: 150px; height: 150px;">
                            <img v-if="previewImage" :src="previewImage" 
                                 class="w-100 h-100 object-fit-cover"
                                 id="profile-preview">
                            <img v-else-if="user.profile_picture" :src="user.profile_picture" 
                                 class="w-100 h-100 object-fit-cover"
                                 id="profile-preview-current">
                            <div v-else class="w-100 h-100 bg-primary bg-gradient d-flex align-items-center justify-content-center text-white fs-1"
                                 id="profile-initials">
                              {{ userInitials }}
                            </div>
                          </div>
                          <button type="button" class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle"
                                  @click="triggerEditFileInput"
                                  style="transform: translate(25%, 25%); border: 2px solid #007bff;">
                            <i class="fas fa-camera"></i>
                          </button>
                          <input type="file" ref="editFileInput" @change="handleEditProfilePicture" class="d-none" 
                                 accept="image/*" id="profile-picture-edit" name="profile_picture">
                        </div>
                        <p class="text-muted small">Click camera icon to change profile picture</p>
                      </div>
                    </div>
                    
                    <!-- Profile Form Fields -->
                    <div class="col-md-8">
                      <div class="row">
                        <div class="col-md-12 mb-3">
                          <label for="full-name" class="form-label fw-bold">Full Name *</label>
                          <input type="text" id="full-name" name="name" class="form-control border" v-model="profileForm.name" 
                                :class="{'is-invalid': profileForm.errors.name}"
                                required
                                placeholder="Enter your full name"
                                autocomplete="name">
                          <div class="invalid-feedback" v-if="profileForm.errors.name">
                            {{ profileForm.errors.name }}
                          </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                          <label for="email-address" class="form-label fw-bold">Email Address *</label>
                          <input type="email" id="email-address" name="email" class="form-control border" v-model="profileForm.email"
                                :class="{'is-invalid': profileForm.errors.email}"
                                required
                                placeholder="Enter your email address"
                                autocomplete="email">
                          <div class="invalid-feedback" v-if="profileForm.errors.email">
                            {{ profileForm.errors.email }}
                          </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                          <label for="phone-number" class="form-label fw-bold">Phone Number</label>
                          <input type="tel" id="phone-number" name="phone" class="form-control border" v-model="profileForm.phone"
                                :class="{'is-invalid': profileForm.errors.phone}"
                                placeholder="Enter your phone number"
                                autocomplete="tel">
                          <div class="invalid-feedback" v-if="profileForm.errors.phone">
                            {{ profileForm.errors.phone }}
                          </div>
                        </div>
                        
                        <!-- Current Password (hidden but required for validation) -->
                        <input type="hidden" name="current_password" :value="profileForm.current_password">
                        
                        <!-- Password Change Section -->
                        <div class="col-md-12">
                          <div class="card border-warning">
                            <div class="card-header bg-warning bg-opacity-10 border-warning">
                              <h6 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-key me-2 text-warning"></i>Change Password (Optional)
                              </h6>
                              <small class="text-muted">Leave blank if you don't want to change password</small>
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label for="new-password" class="form-label fw-bold">New Password</label>
                                  <div class="input-group border border-warning rounded">
                                    <input :type="showNewPassword ? 'text' : 'password'" 
                                          id="new-password" 
                                          name="password"
                                          class="form-control border-0" 
                                          v-model="profileForm.password"
                                          :class="{'is-invalid': profileForm.errors.password}"
                                          placeholder="Enter new password"
                                          autocomplete="new-password">
                                    <button class="btn btn-outline-warning border-0" type="button" @click="toggleNewPasswordVisibility">
                                      <i :class="showNewPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                  </div>
                                  <div class="invalid-feedback" v-if="profileForm.errors.password">
                                    {{ profileForm.errors.password }}
                                  </div>
                                </div>
                                
                                <div class="col-md-6 mb-3" v-if="profileForm.password">
                                  <label for="confirm-password" class="form-label fw-bold">Confirm New Password *</label>
                                  <div class="input-group border border-warning rounded">
                                    <input :type="showConfirmPassword ? 'text' : 'password'" 
                                          id="confirm-password" 
                                          name="password_confirmation"
                                          class="form-control border-0" 
                                          v-model="profileForm.password_confirmation"
                                          :class="{'is-invalid': profileForm.errors.password_confirmation}"
                                          :required="!!profileForm.password"
                                          placeholder="Confirm new password"
                                          autocomplete="new-password">
                                    <button class="btn btn-outline-warning border-0" type="button" @click="toggleConfirmPasswordVisibility">
                                      <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                  </div>
                                  <div class="invalid-feedback" v-if="profileForm.errors.password_confirmation">
                                    {{ profileForm.errors.password_confirmation }}
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="alert alert-info border mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    All fields marked with * are required. Changes will be saved immediately.
                  </div>
                </div>
                <div class="modal-footer border-top-0">
                  <button type="button" class="btn btn-secondary" @click="closeProfileEditModal">
                    <i class="fas fa-times me-2"></i>Cancel
                  </button>
                  <button type="submit" class="btn btn-primary" :disabled="profileForm.processing">
                    <span v-if="profileForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                    <i class="fas fa-save me-2"></i>Save Changes
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================================== -->
      <!-- ACCOUNT NUMBER MODAL -->
      <!-- ==================================== -->
      <div v-if="showAccountModal" class="modal-backdrop fade show">
        <div class="modal fade show d-block" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-blur-container">
            <div class="modal-content border-0 shadow-lg modal-blur-content">
              <div class="modal-header" :class="user.is_verified ? 'bg-success text-white' : 'bg-danger text-white'">
                <h5 class="modal-title fw-bold">
                  <i class="fas fa-university me-2"></i>Account Number
                </h5>
                <button type="button" class="btn-close btn-close-white" @click="closeAccountModal"></button>
              </div>
              
              <div class="modal-body">
                <!-- If NOT verified -->
                <div v-if="!user.is_verified" class="text-center py-4">
                  <div class="mb-4">
                    <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                    <h4 class="fw-bold text-danger">Verification Required</h4>
                    <p class="text-muted">You must be verified to update your account number.</p>
                  </div>
                  
                  <div class="alert alert-danger border">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Please contact support at <strong>temuclassic986@gmail.com</strong> for verification.
                  </div>
                  
                  <button type="button" class="btn btn-danger mt-3" @click="closeAccountModal">
                    <i class="fas fa-times me-2"></i>Close
                  </button>
                </div>
                
                <!-- If IS verified -->
                <form v-else @submit.prevent="updateAccountNumber">
                  <div class="alert alert-info border mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    This account number will be used for receiving payments from customers.
                  </div>
                  
                  <div class="mb-3">
                    <label for="account-number" class="form-label fw-bold">Bank Account Number *</label>
                    <input type="text" id="account-number" name="account_number" class="form-control border" v-model="accountForm.account_number" 
                          :class="{'is-invalid': accountForm.errors.account_number}"
                          required
                          placeholder="Enter your bank account number"
                          autocomplete="off">
                    <div class="invalid-feedback" v-if="accountForm.errors.account_number">
                      {{ accountForm.errors.account_number }}
                    </div>
                  </div>
                  
                  <div class="alert alert-warning border">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Please double-check your account number before saving.
                  </div>
                  
                  <div class="modal-footer border-top-0 px-0 pb-0">
                    <button type="button" class="btn btn-secondary" @click="closeAccountModal">Cancel</button>
                    <button type="submit" class="btn btn-success" :disabled="accountForm.processing">
                      <span v-if="accountForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                      Save Account Number
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================================== -->
      <!-- PRIVACY POLICY MODAL -->
      <!-- ==================================== -->
      <div v-if="showPrivacyModal" class="modal-backdrop fade show">
        <div class="modal fade show d-block" tabindex="-1">
          <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-blur-container">
            <div class="modal-content border-0 shadow-lg modal-blur-content">
              <div class="modal-header bg-primary text-white border-bottom-0 rounded-top">
                <h5 class="modal-title fw-bold">
                  <i class="fas fa-shield-alt me-2"></i>Privacy Policy
                </h5>
                <button type="button" class="btn-close btn-close-white" @click="showPrivacyModal = false"></button>
              </div>
              <div class="modal-body">
                <div class="privacy-content">
                  <h4 class="fw-bold mb-4">Our Privacy Commitment</h4>
                  
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
                    <p class="mb-0"><strong>Last Updated:</strong> January 2024</p>
                  </div>
                </div>
              </div>
              <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-primary" @click="showPrivacyModal = false">I Understand</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================================== -->
      <!-- TERMS & CONDITIONS MODAL -->
      <!-- ==================================== -->
      <div v-if="showTermsModal" class="modal-backdrop fade show">
        <div class="modal fade show d-block" tabindex="-1">
          <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-blur-container">
            <div class="modal-content border-0 shadow-lg modal-blur-content">
              <div class="modal-header bg-success text-white border-bottom-0 rounded-top">
                <h5 class="modal-title fw-bold">
                  <i class="fas fa-file-signature me-2"></i>Terms & Conditions
                </h5>
                <button type="button" class="btn-close btn-close-white" @click="showTermsModal = false"></button>
              </div>
              <div class="modal-body">
                <div class="terms-content">
                  <h4 class="fw-bold mb-4">Terms of Service</h4>
                  
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
                    <p class="mb-0"><strong>Effective Date:</strong> January 2024</p>
                  </div>
                </div>
              </div>
              <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-success" @click="showTermsModal = false">Agree & Continue</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  user: {
    type: Object,
    default: () => ({})
  }
})

// State
const showPasswordVerifyModal = ref(false)
const showProfileEditModal = ref(false)
const showAccountModal = ref(false)
const showPrivacyModal = ref(false)
const showTermsModal = ref(false)
const showPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)
const fileInput = ref(null)
const editFileInput = ref(null)
const previewImage = ref(null)
const verifiedPassword = ref('') // Store verified password separately

// Computed property to check if any modal is open
const isAnyModalOpen = computed(() => {
  return showPasswordVerifyModal.value || 
         showProfileEditModal.value || 
         showAccountModal.value || 
         showPrivacyModal.value || 
         showTermsModal.value
})

// Forms
const profileForm = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  password: '',
  password_confirmation: '',
  profile_picture: null,
  current_password: '',
})

const accountForm = useForm({
  account_number: props.user.account_number || '',
})

const passwordForm = useForm({
  current_password: '',
})

// Computed
const userInitials = computed(() => {
  if (!props.user.name) return 'U'
  return props.user.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .substring(0, 2)
})

// Methods
const formattedDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const openProfileEdit = () => {
  showPasswordVerifyModal.value = true
}

const handleAccountNumberUpdate = () => {
  showAccountModal.value = true
}

const triggerFileInput = () => {
  fileInput.value.click()
}

const triggerEditFileInput = () => {
  editFileInput.value.click()
}

const handleProfilePicture = (event) => {
  const file = event.target.files[0]
  if (file) {
    const formData = new FormData()
    formData.append('profile_picture', file)
    
    // This route uses POST method
    useForm(formData).post(route('settings.profile-picture.update'), {
      preserveScroll: true,
      onSuccess: () => {
        fileInput.value.value = ''
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

const closePasswordVerifyModal = () => {
  showPasswordVerifyModal.value = false
  passwordForm.reset()
  passwordForm.errors.current_password = ''
}

const closeProfileEditModal = () => {
  showProfileEditModal.value = false
  profileForm.reset()
  profileForm.name = props.user.name
  profileForm.email = props.user.email
  profileForm.phone = props.user.phone || ''
  previewImage.value = null
  profileForm.profile_picture = null
}

const closeAccountModal = () => {
  showAccountModal.value = false
}

const verifyPassword = () => {
  passwordForm.post(route('settings.verify-password'), {
    preserveScroll: true,
    onSuccess: () => {
      // Password verified successfully - store it
      verifiedPassword.value = passwordForm.current_password
      
      // Set it in profile form
      profileForm.current_password = passwordForm.current_password
      
      closePasswordVerifyModal()
      
      // Show profile edit modal
      showProfileEditModal.value = true
    },
    onError: (errors) => {
      passwordForm.errors.current_password = 'Incorrect password. Please try again.'
    }
  })
}

// CORRECTED: This WILL update all fields in user table
const updateProfile = () => {
  // Use the verified password
  if (!verifiedPassword.value) {
    alert('Password verification is required. Please verify your password first.')
    showPasswordVerifyModal.value = true
    return
  }
  
  console.log('Updating profile with:', {
    name: profileForm.name,
    email: profileForm.email,
    phone: profileForm.phone,
    hasPassword: !!profileForm.password,
    hasPicture: !!profileForm.profile_picture,
    verifiedPassword: !!verifiedPassword.value
  })
  
  // Create FormData for file upload
  const formData = new FormData()
  formData.append('name', profileForm.name)
  formData.append('email', profileForm.email)
  formData.append('phone', profileForm.phone || '')
  formData.append('current_password', verifiedPassword.value) // Use verified password
  
  // Only append password fields if password is being changed
  if (profileForm.password) {
    formData.append('password', profileForm.password)
    formData.append('password_confirmation', profileForm.password_confirmation)
  }
  
  // Append profile picture if selected
  if (profileForm.profile_picture) {
    formData.append('profile_picture', profileForm.profile_picture)
  }
  
  console.log('Sending FormData to:', route('settings.profile.update'))
  
  // Use POST with FormData
  profileForm.transform(() => formData)
    .post(route('settings.profile.update'), {
      preserveScroll: true,
      forceFormData: true,
      onSuccess: () => {
        console.log('Profile updated successfully')
        // Clear verified password
        verifiedPassword.value = ''
        profileForm.current_password = ''
        
        closeProfileEditModal()
      },
      onError: (errors) => {
        console.log('Profile update errors:', errors)
        
        // If password verification expired, show verify modal again
        if (errors.current_password && errors.current_password.includes('expired')) {
          alert('Password verification expired. Please verify again.')
          verifiedPassword.value = ''
          showProfileEditModal.value = false
          showPasswordVerifyModal.value = true
          return
        }
        
        // Show error messages
        if (errors.current_password) {
          profileForm.errors.current_password = errors.current_password
        }
        if (errors.password) {
          profileForm.errors.password = errors.password
        }
        if (errors.email) {
          profileForm.errors.email = errors.email
        }
        if (errors.name) {
          profileForm.errors.name = errors.name
        }
        if (errors.phone) {
          profileForm.errors.phone = errors.phone
        }
      }
    })
}

const updateAccountNumber = () => {
  if (!props.user.is_verified) {
    return
  }

  accountForm.put(route('settings.account-number.update'), {
    preserveScroll: true,
    onSuccess: () => {
      closeAccountModal()
    }
  })
}

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const toggleNewPasswordVisibility = () => {
  showNewPassword.value = !showNewPassword.value
}

const toggleConfirmPasswordVisibility = () => {
  showConfirmPassword.value = !showConfirmPassword.value
}
</script>

<style scoped>
/* Custom Styles */
.hover-lift {
  transition: all 0.3s ease;
  cursor: pointer;
}

.hover-lift:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

.avatar-lg {
  width: 120px;
  height: 120px;
}

.avatar-title {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  font-size: 3rem;
}

.border-end-md {
  border-right: 1px solid #dee2e6;
}

@media (max-width: 768px) {
  .border-end-md {
    border-right: none;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
  }
}

.privacy-content h5,
.terms-content h5 {
  color: #2c3e50;
  border-left: 4px solid #3498db;
  padding-left: 1rem;
}

.privacy-content ul,
.terms-content ul {
  padding-left: 1.5rem;
}

.privacy-content li,
.terms-content li {
  margin-bottom: 0.5rem;
}

/* Profile picture button */
.position-relative .btn {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

/* Modal styles */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1040;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1050;
  width: 100%;
  height: 100%;
  overflow-x: hidden;
  overflow-y: auto;
  outline: 0;
}

.modal.show {
  display: block;
}

.modal-dialog {
  position: relative;
  width: auto;
  margin: 0.5rem;
  pointer-events: none;
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 1rem);
}

.modal-content {
  position: relative;
  display: flex;
  flex-direction: column;
  width: 100%;
  pointer-events: auto;
  background-color: #fff;
  background-clip: padding-box;
  border-radius: 0.5rem;
  outline: 0;
}

/* Form validation styles */
.form-control.is-invalid {
  border-color: #dc3545;
  padding-right: calc(1.5em + 0.75rem);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right calc(0.375em + 0.1875rem) center;
  background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

/* Password visibility toggle */
.input-group .btn-outline-secondary {
  border-color: #ced4da;
  border-left: 0;
}

.input-group .btn-outline-secondary:hover {
  background-color: #e9ecef;
  border-color: #ced4da;
}

/* Badge styles */
.badge {
  font-size: 0.875rem;
  padding: 0.5rem 1rem;
}

/* Card hover effects */
.card {
  transition: all 0.3s ease;
}

.card:hover {
  border-color: #0d6efd;
}

/* Required field asterisk */
.form-label:has(+ input[required])::after {
  content: " *";
  color: #dc3545;
}

.object-fit-cover {
  object-fit: cover;
}

/* Consistent border styling for all modals */
.alert.border {
  border-width: 2px !important;
}

.form-control.border {
  border-width: 2px !important;
}

.card.border-warning {
  border-width: 2px !important;
}

.input-group.border {
  border-width: 2px !important;
}

/* Border radius consistency */
.rounded-top {
  border-top-left-radius: 0.5rem !important;
  border-top-right-radius: 0.5rem !important;
}

/* Make modals clearly readable */
.modal-body {
  color: #333;
  line-height: 1.6;
}

.modal-body h4,
.modal-body h5 {
  color: #2c3e50;
}

.modal-body .text-muted {
  color: #215c8f !important;
}

/* Center content in account number modal when not verified */
.text-center .fa-4x {
  font-size: 4rem;
}

/* =========================================== */
/* BLUR EFFECT STYLES */
/* =========================================== */

/* Blur overlay - semi-transparent dark layer */
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

/* Blur the background content when modal is open */
.blur-background {
  transition: filter 0.3s ease;
  pointer-events: none;
  user-select: none;
}

/* Ensure modal containers are above the blur */
.modal-blur-container {
  z-index: 1041 !important;
  position: relative;
}

/* Ensure modal content is above everything */
.modal-blur-content {
  z-index: 1042 !important;
  position: relative;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
  animation: modalSlideIn 0.3s ease-out;
}

/* Animation for modal appearance */
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

/* Ensure modals are always centered */
.modal.show .modal-dialog.modal-blur-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  margin: 0 auto;
}

/* Make modal backdrop slightly darker for better contrast */
.modal-backdrop.fade.show {
  background-color: rgba(0, 0, 0, 0.7) !important;
  backdrop-filter: blur(5px);
  -webkit-backdrop-filter: blur(5px);
}

/* Ensure modal content is crisp and not blurred */
.modal-content.modal-blur-content {
  filter: none !important;
  -webkit-filter: none !important;
  backdrop-filter: none !important;
  -webkit-backdrop-filter: none !important;
}

/* Make the modal stand out with a subtle glow */
.modal-content.modal-blur-content {
  box-shadow: 
    0 0 0 1px rgba(255, 255, 255, 0.1),
    0 20px 60px rgba(0, 0, 0, 0.4),
    0 0 40px rgba(0, 123, 255, 0.1) !important;
}

/* Smooth transition for background blur */
.container-fluid.bg-light {
  transition: all 0.3s ease;
}

/* Ensure no blur on modal content */
.modal-body,
.modal-header,
.modal-footer {
  filter: none !important;
  -webkit-filter: none !important;
}

/* Make sure modal is always on top */
.modal.fade.show {
  z-index: 1040 !important;
}
</style>