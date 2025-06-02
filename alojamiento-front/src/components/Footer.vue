<template>
  <footer class="footer">
    <div class="footer-container">

      <div class="footer-section">
        <router-link to="/" class="footer-logo">
          <span>Alojamiento</span>
          <span>San Salvador</span>
        </router-link>
        <p class="footer-description">
          Ofrecemos la mejor experiencia de hospedaje en la ciudad con servicios premium y atención personalizada.
        </p>
      </div>
      
      <div class="footer-section">
        <h4 class="footer-title">Enlaces rápidos</h4>
        <nav class="footer-nav">
          <router-link to="/">Inicio</router-link>
          <router-link to="/search">Habitaciones</router-link>
          <router-link to="/servicios">Restaurante</router-link>
        </nav>
      </div>
      
      <div class="footer-section">
        <h4 class="footer-title">Contacto</h4>
        <address class="footer-contact">
          <p><i class="icon icon-location"></i> Calle Principal #123, San Salvador</p>
          <p><i class="icon icon-phone"></i> +503 1234 5678</p>
          <p><i class="icon icon-email"></i> info@alojamientosansalvador.com</p>
        </address>
      </div>

    </div>
      <div class="footer-bottom">
        <p>&copy; 2025 Alojamiento San Salvador. Todos los derechos reservados.</p>
    </div>
  </footer>
</template>

<script setup>
</script>

<style scoped>
/* Estilos base */
.footer {
  padding: 1rem 0 0;
  width: 100%;
  font-size: 0.95rem;
}

.footer-container {
  max-width: 2000px;
  width: 100%;
  margin: auto;
  padding: 0 1rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 3rem;
}


.footer-logo {
  display: flex;
  flex-direction: column;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-light);
  text-decoration: none;
  margin-bottom: 1rem;
}

.footer-logo span:first-child {
  color: var(--text-light);
}

.footer-logo span:last-child {
  color: var(--secondary-color);
}

.footer-description {
  color: var(--text-gray);
  line-height: 1.6;
  margin:0 auto;
}

.footer-title {
  /* color: var(--text-light); */
  font-size: 1.2rem;
  /* margin-bottom: 1rem; */
  position: relative;
  /* padding-bottom: 0.5rem; */
}

.footer-title::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  width: 50px;
  height: 2px;
  background-color: var(--secondary-color);
}

.footer-nav {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.footer-nav a {
  color: var(--text-gray);
  text-decoration: none;
  transition: color var(--transition-speed) ease;
  width: fit-content;
}

.footer-nav a:hover {
  color: var(--secondary-color);
  text-decoration: underline;
}

.footer-contact p {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.8rem;
  color: var(--text-gray);
  line-height: 1.5;
}

.social-links {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.social-links a {
  color: var(--text-light);
  background-color: rgba(255, 255, 255, 0.1);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all var(--transition-speed) ease;
}

.social-links a:hover {
  background-color: var(--secondary-color);
  transform: translateY(-3px);
}

.newsletter p {
  color: var(--text-gray);
  margin-bottom: 0.8rem;
}

.newsletter-form {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.newsletter-form input {
  flex: 1;
  min-width: 200px;
  padding: 0.6rem 0.8rem;
  border: none;
  border-radius: 4px;
  font-size: 0.9rem;
}

.newsletter-form button {
  background-color: var(--secondary-color);
  color: var(--text-light);
  border: none;
  padding: 0.6rem 1.2rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color var(--transition-speed) ease;
}

.newsletter-form button:hover {
  background-color: #3aa876;
}

.footer-bottom {
  background-color: var(--primary-dark);
  padding: 1.5rem 0;
  text-align: center;
  margin-top: 2rem;
}

.footer-bottom p {
  color: var(--text-gray);
  margin-bottom: 0.5rem;
}

.legal-links {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.legal-links a {
  color: var(--text-gray);
  text-decoration: none;
  transition: color var(--transition-speed) ease;
}

.legal-links a:hover {
  color: var(--secondary-color);
}

/* Íconos (puedes reemplazar con una librería de íconos) */
.icon::before {
  font-family: 'Arial', sans-serif;
}

.icon-location::before {
  content: '📍';
}

.icon-phone::before {
  content: '📞';
}

.icon-email::before {
  content: '✉️';
}

.icon-facebook::before {
  content: 'f';
  font-weight: bold;
}

.icon-instagram::before {
  content: 'ig';
  font-style: italic;
}

.icon-twitter::before {
  content: '𝕏';
}

.icon-whatsapp::before {
  content: '✓';
}

/* Responsive */
@media (max-width: 768px) {
  .footer-container {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .footer-section {
    margin-bottom: 1.5rem;
  }

  .newsletter-form {
    flex-direction: column;
  }

  .newsletter-form input,
  .newsletter-form button {
    width: 100%;
  }
}
</style>