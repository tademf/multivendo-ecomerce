<template>
  <footer class="bg-dark text-white pt-5 pb-4">
    <div class="container">
      <div class="row g-4">
        <!-- Brand/Logo Section -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="footer-brand">
            <div class="d-flex align-items-center mb-3">
              <div class="brand-icon me-3">
                <i class="fas fa-store fa-2x text-primary"></i>
              </div>
              <div>
                <h2 class="h3 fw-bold text-primary mb-0">E-Shop</h2>
                <span class="text-light fs-6">Premium Shopping</span>
              </div>
            </div>
            <p class="text-light opacity-75 mb-4">
              Your trusted online shopping destination offering quality products with fast delivery and excellent customer service.
            </p>
            
            <!-- Newsletter Subscription -->
            <div class="newsletter mt-4">
              <h6 class="text-white fw-bold mb-3">Subscribe to our newsletter</h6>
              <div class="input-group">
                <input type="email" class="form-control border-0" placeholder="Your email address" aria-label="Email">
                <button class="btn btn-primary" type="button">
                  <i class="fas fa-paper-plane"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-2 col-md-6 mb-4">
          <h5 class="text-white fw-bold mb-4 border-bottom pb-2 border-primary">Quick Links</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <Link href="/" class="text-light opacity-75 text-decoration-none hover-lift">
                <i class="fas fa-home me-2"></i>Home
              </Link>
            </li>
            <li class="mb-2">
              <Link href="/" class="text-light opacity-75 text-decoration-none hover-lift">
                <i class="fas fa-shopping-bag me-2"></i>Products
              </Link>
            </li>
            <li class="mb-2">
              <Link href="/" class="text-light opacity-75 text-decoration-none hover-lift">
                <i class="fas fa-th-large me-2"></i>Categories
              </Link>
            </li>
            <li class="mb-2">
              <Link href="/" class="text-light opacity-75 text-decoration-none hover-lift">
                <i class="fas fa-fire me-2"></i>Hot Deals
              </Link>
            </li>
            <li class="mb-2">
              <Link href="/" class="text-light opacity-75 text-decoration-none hover-lift">
                <i class="fas fa-star me-2"></i>New Arrivals
              </Link>
            </li>
          </ul>
        </div>

        <!-- Customer Support -->
        <div class="col-lg-2 col-md-6 mb-4">
          <h5 class="text-white fw-bold mb-4 border-bottom pb-2 border-primary">Support</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <Link href="/settings" class="text-light opacity-75 text-decoration-none hover-lift">
                <i class="fas fa-info-circle me-2"></i>About Us
              </Link>
            </li>
            <li class="mb-2">
              <Link href="/settings" class="text-light opacity-75 text-decoration-none hover-lift">
                <i class="fas fa-headset me-2"></i>Contact Us
              </Link>
            </li>
            <li class="mb-2">
              <Link href="/settings" class="text-light opacity-75 text-decoration-none hover-lift">
                <i class="fas fa-question-circle me-2"></i>FAQ
              </Link>
            </li>
            <li class="mb-2">
              <Link href="/settings" class="text-light opacity-75 text-decoration-none hover-lift">
                <i class="fas fa-shipping-fast me-2"></i>Shipping Info
              </Link>
            </li>
            <li class="mb-2">
              <Link href="/settings" class="text-light opacity-75 text-decoration-none hover-lift">
                <i class="fas fa-exchange-alt me-2"></i>Returns Policy
              </Link>
            </li>
          </ul>
        </div>

        <!-- Contact & Social -->
        <div class="col-lg-4 col-md-6 mb-4">
          <h5 class="text-white fw-bold mb-4 border-bottom pb-2 border-primary">Contact Us</h5>
          
          <!-- Contact Info -->
          <div class="contact-info mb-4">
            <div class="d-flex align-items-start mb-3">
              <div class="contact-icon me-3">
                <i class="fas fa-map-marker-alt text-primary"></i>
              </div>
              <div>
                <h6 class="text-white mb-1">Our Location</h6>
                <p class="text-light opacity-75 mb-0">Kenenisa Street, Addis Abeba, Ethiopia</p>
              </div>
            </div>
            
            <div class="d-flex align-items-start mb-3">
              <div class="contact-icon me-3">
                <i class="fas fa-phone text-primary"></i>
              </div>
              <div>
                <h6 class="text-white mb-1">Phone Number</h6>
                <p class="text-light opacity-75 mb-0">+251 993 702 920</p>
              </div>
            </div>
            
            <div class="d-flex align-items-start mb-4">
              <div class="contact-icon me-3">
                <i class="fas fa-envelope text-primary"></i>
              </div>
              <div>
                <h6 class="text-white mb-1">Email Address</h6>
                <p class="text-light opacity-75 mb-0">temuclassic986@gmail.com</p>
              </div>
            </div>
          </div>

          <!-- Social Media -->
          <div class="social-section">
            <h6 class="text-white fw-bold mb-3">Follow Us</h6>
            <div class="social-buttons d-flex gap-2">
              <a href="#" class="btn btn-outline-light rounded-circle social-btn" title="Facebook">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="#" class="btn btn-outline-light rounded-circle social-btn" title="Twitter">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" class="btn btn-outline-light rounded-circle social-btn" title="Instagram">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="#" class="btn btn-outline-light rounded-circle social-btn" title="LinkedIn">
                <i class="fab fa-linkedin-in"></i>
              </a>
              <a href="#" class="btn btn-outline-light rounded-circle social-btn" title="YouTube">
                <i class="fab fa-youtube"></i>
              </a>
            </div>
            
            <!-- App Store Badges -->
            <div class="app-download mt-4">
              <h6 class="text-white fw-bold mb-3">Download Our App</h6>
              <div class="d-flex gap-2 flex-wrap">
                <a href="#" class="btn btn-dark border border-light rounded-3 px-3 py-2">
                  <div class="d-flex align-items-center">
                    <i class="fab fa-google-play me-2"></i>
                    <div class="text-start">
                      <small class="d-block text-light opacity-75">GET IT ON</small>
                      <span class="fw-bold">Google Play</span>
                    </div>
                  </div>
                </a>
                <a href="#" class="btn btn-dark border border-light rounded-3 px-3 py-2">
                  <div class="d-flex align-items-center">
                    <i class="fab fa-apple me-2"></i>
                    <div class="text-start">
                      <small class="d-block text-light opacity-75">Download on the</small>
                      <span class="fw-bold">App Store</span>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <hr class="border-secondary my-4">

      <!-- Bottom Section -->
      <div class="row align-items-center">
        <div class="col-md-6 mb-3 mb-md-0">
          <p class="text-light opacity-75 mb-0">
            <i class="fas fa-shield-alt text-primary me-2"></i>
            Secure Payment • 100% Safe • SSL Encrypted
          </p>
        </div>
        <div class="col-md-6">
          <div class="payment-methods d-flex justify-content-md-end gap-2 mb-3">
            <div class="payment-icon">
              <i class="fab fa-cc-visa fa-2x text-light opacity-75"></i>
            </div>
            <div class="payment-icon">
              <i class="fab fa-cc-mastercard fa-2x text-light opacity-75"></i>
            </div>
            <div class="payment-icon">
              <i class="fab fa-cc-paypal fa-2x text-light opacity-75"></i>
            </div>
            <div class="payment-icon">
              <i class="fab fa-cc-amazon-pay fa-2x text-light opacity-75"></i>
            </div>
            <div class="payment-icon">
              <i class="fab fa-cc-apple-pay fa-2x text-light opacity-75"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Copyright -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center py-3 border-top border-secondary">
            <p class="text-light opacity-75 mb-2 mb-md-0">
              &copy; {{ currentYear }} <span class="text-primary fw-bold">E-Shop</span>. All rights reserved.
            </p>
            <div class="legal-links d-flex gap-3">
              <Link href="/settings" class="text-light opacity-75 text-decoration-none hover-lift">
                Privacy Policy
              </Link>
              <span class="text-light opacity-75">•</span>
              <Link href="/settings" class="text-light opacity-75 text-decoration-none hover-lift">
                Terms of Service
              </Link>
              <span class="text-light opacity-75">•</span>
              <Link href="/settings" class="text-light opacity-75 text-decoration-none hover-lift">
                Sitemap
              </Link>
              <span class="text-light opacity-75">•</span>
              <Link href="/settings" class="text-light opacity-75 text-decoration-none hover-lift">
                Cookie Policy
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Back to Top Button -->
      <button 
        class="btn btn-primary rounded-circle position-fixed bottom-0 end-0 m-4 shadow-lg back-to-top"
        @click="scrollToTop"
        style="width: 50px; height: 50px; z-index: 1000;"
      >
        <i class="fas fa-arrow-up"></i>
      </button>
    </div>
  </footer>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  name: 'AppFooter',
  
  components: {
    Link
  },

  computed: {
    currentYear() {
      return new Date().getFullYear()
    }
  },

  methods: {
    scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      })
    }
  }
}
</script>

<style scoped>
/* Custom Styles for Bootstrap Footer */

/* Hover Effects */
.hover-lift {
  transition: all 0.3s ease;
}

.hover-lift:hover {
  color: #0d6efd !important;
  transform: translateY(-2px);
  text-decoration: underline !important;
}

/* Brand Icon */
.brand-icon {
  width: 60px;
  height: 60px;
  background: rgba(13, 110, 253, 0.1);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Social Buttons */
.social-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.social-btn:hover {
  background-color: #0d6efd;
  border-color: #0d6efd;
  transform: translateY(-3px);
}

/* Contact Icons */
.contact-icon {
  width: 36px;
  height: 36px;
  background: rgba(13, 110, 253, 0.1);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Payment Icons */
.payment-icon {
  opacity: 0.8;
  transition: opacity 0.3s ease;
}

.payment-icon:hover {
  opacity: 1;
}

/* Newsletter Input */
.newsletter .input-group {
  border-radius: 8px;
  overflow: hidden;
}

.newsletter .form-control {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
}

.newsletter .form-control::placeholder {
  color: rgba(255, 255, 255, 0.6);
}

.newsletter .form-control:focus {
  background: rgba(255, 255, 255, 0.15);
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  color: white;
}

/* Back to Top Button */
.back-to-top {
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.3s ease;
}

.back-to-top.show {
  opacity: 1;
  transform: translateY(0);
}

.back-to-top:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
}

/* App Download Buttons */
.app-download .btn {
  transition: all 0.3s ease;
}

.app-download .btn:hover {
  background: rgba(255, 255, 255, 0.1) !important;
  transform: translateY(-2px);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .brand-icon {
    width: 50px;
    height: 50px;
  }
  
  .social-buttons {
    justify-content: center;
  }
  
  .payment-methods {
    justify-content: center !important;
  }
  
  .legal-links {
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px !important;
  }
  
  .legal-links span {
    display: none;
  }
  
  .app-download .btn {
    width: 100%;
    justify-content: center;
  }
}

/* Dark mode adjustments */
@media (prefers-color-scheme: dark) {
  .footer {
    background: #1a1d20 !important;
  }
}

/* Animation for links */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.footer-brand,
.footer-links,
.footer-info {
  animation: fadeInUp 0.5s ease forwards;
}

.footer-links {
  animation-delay: 0.1s;
}

.footer-info {
  animation-delay: 0.2s;
}

/* Scroll indicator */
.scroll-indicator {
  position: absolute;
  top: -20px;
  left: 50%;
  transform: translateX(-50%);
  width: 40px;
  height: 40px;
  background: #0d6efd;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}
</style>