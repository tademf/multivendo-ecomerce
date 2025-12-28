<template>
  <footer class="footer-ecommerce" :class="theme === 'dark' ? 'dark-theme' : ''">
    <div class="social-section py-4 border-top">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <h6 class="fw-bold mb-3">Follow Us</h6>
            <div class="social-icons">
              <a href="#" class="social-icon facebook" aria-label="Facebook">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="#" class="social-icon twitter" aria-label="Twitter">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" class="social-icon instagram" aria-label="Instagram">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="#" class="social-icon linkedin" aria-label="LinkedIn">
                <i class="fab fa-linkedin-in"></i>
              </a>
              <a href="#" class="social-icon youtube" aria-label="YouTube">
                <i class="fab fa-youtube"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="copyright-section py-4 bg-dark text-white">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 mb-3 mb-md-0">
            <p class="mb-0 small">
              © {{ currentYear }} E-SHOP Multivendor Marketplace. All rights reserved.
            </p>
          </div>
          <div class="col-md-6">
            <div class="d-flex justify-content-md-end flex-wrap gap-3">
              <a href="/settings" class="text-white-50 small text-decoration-none">Privacy Policy</a>
              <span class="text-white-50">•</span>
              <a href="/settings" class="text-white-50 small text-decoration-none">Terms of Service</a>
              <span class="text-white-50">•</span>
              <a href="/settings" class="text-white-50 small text-decoration-none">Cookie Policy</a>
              <span class="text-white-50">•</span>
              <a href="/settings" class="text-white-50 small text-decoration-none">Contact</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'
import { useTheme } from '@/Composables/useTheme'

const { theme } = useTheme()
const currentYear = new Date().getFullYear()
</script>

<style scoped>
.footer-ecommerce {
  background: var(--body-bg);
  transition: all 0.3s ease;
}

.social-section {
  background: var(--body-bg);
}

.social-icons {
  display: flex;
  gap: 1rem;
}

.social-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
  border-radius: 50%;
  color: #666;
  text-decoration: none;
  transition: all 0.3s ease;
}

.dark-theme .social-icon {
  background: #2d2d2d;
  color: #e0e0e0;
}

.social-icon:hover {
  transform: translateY(-3px) scale(1.1);
  color: white;
}

.social-icon.facebook:hover {
  background: #1877f2;
}

.social-icon.twitter:hover {
  background: #1da1f2;
}

.social-icon.instagram:hover {
  background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
}

.social-icon.linkedin:hover {
  background: #0077b5;
}

.social-icon.youtube:hover {
  background: #ff0000;
}

.copyright-section {
  background: #212529 !important;
}

.dark-theme .copyright-section {
  background: #1a1a1a !important;
}

@media (max-width: 768px) {
  .social-icons {
    justify-content: center;
  }
  
  .copyright-section .d-flex {
    justify-content: center;
  }
  
  .copyright-section .col-md-6 {
    text-align: center;
  }
}
</style>