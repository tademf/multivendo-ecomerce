<template>
  <footer class="app-footer">
    <div class="footer-container">
      <div class="footer-content">
        <!-- Left side: Brand/Logo -->
        <div class="footer-brand">
          <h3 class="brand-name">E-Shop</h3>
          <p class="brand-tagline">Your one-stop online shopping destination</p>
        </div>

        <!-- Middle: Quick Links -->
        <div class="footer-links">
          <h4 class="links-title">Quick Links</h4>
          <ul class="links-list">
            <li><Link href="/" class="footer-link">Home</Link></li>
            <li><Link href="/" class="footer-link">Products</Link></li>
            <li><Link href="/about" class="footer-link">About Us</Link></li>
            <li><Link href="/contact" class="footer-link">Contact</Link></li>
            <li><Link href="/faq" class="footer-link">FAQ</Link></li>
          </ul>
        </div>

        <!-- Right side: Contact/Info -->
        <div class="footer-info">
          <h4 class="info-title">Contact Info</h4>
          <div class="contact-details">
            <p class="contact-item">📍 Kenenisa Street, Addis Abeba, Ethiopia</p>
            <p class="contact-item">📞 +251 993702920</p>
            <p class="contact-item">✉️ temuclassic986@gmail.com</p>
          </div>
          
          <!-- Social Media -->
          <div class="social-media">
            <a href="#" class="social-icon" title="Facebook">📘</a>
            <a href="#" class="social-icon" title="Twitter">🐦</a>
            <a href="#" class="social-icon" title="Instagram">📸</a>
            <a href="#" class="social-icon" title="LinkedIn">💼</a>
          </div>
        </div>
      </div>

      <!-- Copyright -->
      <div class="footer-bottom">
        <p class="copyright">
          &copy; {{ currentYear }} E-Shop. All rights reserved.
        </p>
        <div class="legal-links">
          <Link href="/privacy" class="legal-link">Privacy Policy</Link>
          <span class="separator">•</span>
          <Link href="/terms" class="legal-link">Terms of Service</Link>
          <span class="separator">•</span>
          <Link href="/sitemap" class="legal-link">Sitemap</Link>
        </div>
      </div>
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
  }
}
</script>

<style scoped>
.app-footer {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  color: #ecf0f1;
  padding: 2rem 1rem;
  margin-top: auto;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.footer-brand .brand-name {
  font-size: 1.8rem;
  font-weight: bold;
  color: #3498db;
  margin-bottom: 0.5rem;
}

.footer-brand .brand-tagline {
  color: #bdc3c7;
  font-size: 0.95rem;
  line-height: 1.5;
}

.links-title,
.info-title {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #3498db;
}

.links-list {
  list-style: none;
  padding: 0;
}

.links-list li {
  margin-bottom: 0.5rem;
}

.footer-link {
  color: #bdc3c7;
  text-decoration: none;
  transition: color 0.3s ease;
  font-size: 0.95rem;
}

.footer-link:hover {
  color: #3498db;
}

.contact-details {
  margin-bottom: 1.5rem;
}

.contact-item {
  margin-bottom: 0.5rem;
  color: #bdc3c7;
  font-size: 0.95rem;
}

.social-media {
  display: flex;
  gap: 1rem;
}

.social-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  color: white;
  text-decoration: none;
  font-size: 1.2rem;
  transition: all 0.3s ease;
}

.social-icon:hover {
  background: #3498db;
  transform: translateY(-3px);
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding-top: 1.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  text-align: center;
}

.copyright {
  color: #bdc3c7;
  font-size: 0.9rem;
}

.legal-links {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  justify-content: center;
}

.legal-link {
  color: #bdc3c7;
  text-decoration: none;
  font-size: 0.85rem;
  transition: color 0.3s ease;
}

.legal-link:hover {
  color: #3498db;
}

.separator {
  color: #7f8c8d;
}

/* Responsive Design */
@media (max-width: 768px) {
  .footer-content {
    grid-template-columns: 1fr;
    text-align: center;
  }
  
  .social-media {
    justify-content: center;
  }
  
  .footer-bottom {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .legal-links {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .separator {
    display: none;
  }
}
</style>