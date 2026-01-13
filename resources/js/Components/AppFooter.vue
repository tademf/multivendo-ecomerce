<template>
  <footer class="footer-ecommerce" :data-theme="theme">
    <div class="social-section py-5">
      <div class="container">
        <div class="row align-items-center justify-content-between">
          <div class="col-lg-12 text-center">
            <h6 class="follow-text mb-4">FOLLOW OUR ELITE COMMUNITY</h6>
            <div class="social-icons-wrapper">
              <a href="https://www.facebook.com/omishtujoy" target="_blank" class="social-icon facebook" aria-label="Facebook">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="https://twitter.com/OmishtuJoy" target="_blank" class="social-icon twitter" aria-label="Twitter">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="https://www.instagram.com/omishtu_joy" target="_blank" class="social-icon instagram" aria-label="Instagram">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="https://www.linkedin.com/company/omishtu-joy-tech-solutions" target="_blank" class="social-icon linkedin" aria-label="LinkedIn">
                <i class="fab fa-linkedin-in"></i>
              </a>
              <a href="https://www.youtube.com/@omishtu-joytechsolutions2218" target="_blank" class="social-icon youtube" aria-label="YouTube">
                <i class="fab fa-youtube"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="copyright-section py-4">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 mb-3 mb-md-0">
            <p class="mb-0 small copyright-text">
              © {{ currentYear }} <span class="brand-highlight">E-SHOP</span> Multivendor Marketplace. All rights reserved.
            </p>
          </div>
          <div class="col-md-6">
            <div class="d-flex justify-content-md-end flex-wrap gap-4 footer-links">
              <a href="/settings" class="footer-link">Privacy Policy</a>
              <a href="/settings" class="footer-link">Terms of Service</a>
              <a href="/settings" class="footer-link">Cookie Policy</a>
              <a href="/settings" class="footer-link">Contact</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue'
const currentYear = new Date().getFullYear()

const theme = ref('light')

// Listen for theme changes from the navbar
const updateTheme = () => {
  const html = document.documentElement
  const currentTheme = html.getAttribute('data-theme')
  theme.value = currentTheme || 'light'
}

onMounted(() => {
  // Get initial theme
  updateTheme()
  
  // Listen for theme changes
  window.addEventListener('theme-changed', updateTheme)
})

// Cleanup
import { onUnmounted } from 'vue'
onUnmounted(() => {
  window.removeEventListener('theme-changed', updateTheme)
})
</script>

<style scoped>
/* --- 1. BASE FOOTER --- */
.footer-ecommerce {
  background: #ffffff;
  border-top: 1px solid rgba(243, 198, 35, 0.1);
  transition: all 0.3s ease;
}

/* Dark theme for footer */
[data-theme="dark"] .footer-ecommerce {
  background: #0f172a;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

/* --- 2. SOCIAL SECTION PREMIUM STYLE --- */
.social-section {
  background: inherit;
  position: relative;
}

.follow-text {
  font-weight: 800;
  letter-spacing: 4px;
  color: #888;
  font-size: 0.75rem;
}

/* Dark theme follow text */
[data-theme="dark"] .follow-text {
  color: #a0a0a0;
}

.social-icons-wrapper {
  display: flex;
  gap: 1.5rem;
  justify-content: center;
}

.social-icon {
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(243, 198, 35, 0.1);
  border-radius: 12px;
  color: #555;
  text-decoration: none;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Dark theme social icons */
[data-theme="dark"] .social-icon {
  background: rgba(255, 255, 255, 0.05);
  color: #a0a0a0;
  border-color: rgba(255, 255, 255, 0.1);
}

.social-icon:hover {
  transform: translateY(-8px);
  color: #000 !important;
  box-shadow: 0 10px 20px rgba(243, 198, 35, 0.2);
  border-color: #c5a059;
}

/* Dark theme social icon hover */
[data-theme="dark"] .social-icon:hover {
  color: #fff !important;
  box-shadow: 0 10px 20px rgba(197, 160, 89, 0.2);
}

/* Hover Effects with Brand Colors */
.social-icon.facebook:hover { background: #1877f2; color: #fff !important; border-color: #1877f2; }
.social-icon.twitter:hover { background: #1da1f2; color: #fff !important; border-color: #1da1f2; }
.social-icon.instagram:hover { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); color: #fff !important; border-color: transparent; }
.social-icon.linkedin:hover { background: #0077b5; color: #fff !important; border-color: #0077b5; }
.social-icon.youtube:hover { background: #ff0000; color: #fff !important; border-color: #ff0000; }

/* --- 3. COPYRIGHT SECTION --- */
.copyright-section {
  background: #080808;
  border-top: 1px solid rgba(255, 255, 255, 0.03);
}

/* Dark theme copyright section */
[data-theme="dark"] .copyright-section {
  background: #1e293b;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.copyright-text {
  color: #666;
  font-weight: 500;
  letter-spacing: 0.5px;
}

/* Dark theme copyright text */
[data-theme="dark"] .copyright-text {
  color: #a0a0a0;
}

.brand-highlight {
  color: #c5a059;
  font-weight: 800;
}

/* --- 4. FOOTER LINKS --- */
.footer-link {
  color: #555 !important;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
  position: relative;
}

/* Dark theme footer links */
[data-theme="dark"] .footer-link {
  color: #a0a0a0 !important;
}

.footer-link::after {
  content: '';
  position: absolute;
  width: 0;
  height: 1px;
  bottom: -4px;
  left: 0;
  background-color: #c5a059;
  transition: 0.3s;
}

.footer-link:hover {
  color: #c5a059 !important;
}

.footer-link:hover::after {
  width: 100%;
}

/* --- 5. RESPONSIVE --- */
@media (max-width: 768px) {
  .social-icons-wrapper { gap: 1rem; }
  .footer-links { justify-content: center !important; margin-top: 15px; }
  .copyright-text { text-align: center; }
}
</style>