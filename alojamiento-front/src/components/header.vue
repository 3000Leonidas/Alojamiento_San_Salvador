<template>
  <header class="navbar">
    <div class="container">
      <router-link to="/" class="logo">
        <span class="brand">Alojamiento</span>
        <span class="location">San Salvador</span>
      </router-link>

      <nav class="nav-links">
        <router-link to="/" exact-active-class="active">Inicio</router-link>
        <router-link to="/search" active-class="active">Habitaciones</router-link>
        <router-link to="/servicios" active-class="active">Restaurante</router-link>

        <template v-if="!isLoggedIn">
          <router-link to="/login" class="auth-link" active-class="active">
            Iniciar sesión
          </router-link>
        </template>

        <template v-else>
          <router-link v-if="isAdmin" to="/admin" class="admin-link" active-class="active">
            Panel Admin
          </router-link>
          <button class="logout-btn" @click="handleLogout">
            <span>Cerrar sesión</span>
            <i class="icon-logout"></i>
          </button>
        </template>
      </nav>

      <button class="menu-toggle" @click="toggleMobileMenu" aria-label="Toggle menu">
        <i class="icon-menu"></i>
      </button>
    </div>
  </header>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { auth } from '../store/auth';

const router = useRouter();
const mobileMenuOpen = ref(false);

const isLoggedIn = computed(() => auth.isLoggedIn);
const isAdmin = computed(() => auth.isAdmin);

function handleLogout() {
  auth.logout();
  mobileMenuOpen.value = false;
  router.push('/');
}

function toggleMobileMenu() {
  mobileMenuOpen.value = !mobileMenuOpen.value;
}
</script>

<style scoped>
/* Variables de diseño */
:root {
  --navbar-height: 70px;
  --primary-color: #2c3e50;
  --primary-dark: #1a252f; /* Color más oscuro para hover/active */
  --secondary-color: #42b983;
  --text-light: #ffffff;
  --text-dark: #2c3e50;
  --transition-speed: 0.3s;
}

.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: var(--navbar-height);
  /* background-color: var(--primary-dark);  */
  /* color: var(--text-light); */
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); 
  z-index: 1000;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  width: 100%;
  height: 100%;
  margin: 0 auto;
  padding: 0 2rem;
  
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-light);
  text-decoration: none;
  transition: transform 0.2s ease;
}

.logo:hover {
  transform: scale(1.02);
}

.logo .brand {
  color: var(--text-light);
}

.logo .location {
  color: var(--secondary-color);
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.nav-links a,
.nav-links button {
  position: relative;
  color: inherit;
  text-decoration: none;
  font-weight: 500;
  font-size: 1rem;
  padding: 0.5rem 0;
  transition: all var(--transition-speed) ease;
  background: none;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.3rem;
}

.nav-links a:hover,
.nav-links button:hover {
  color: var(--secondary-color);
}

.nav-links a.active {
  color: var(--secondary-color);
  font-weight: 600;
}

.nav-links a.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: var(--secondary-color);
}

.admin-link {
  color: #ffcc00 !important;
  background-color: rgba(0, 0, 0, 0.2);
  padding: 0.5rem 1rem;
  border-radius: 4px;
}

.admin-link:hover {
  background-color: rgba(0, 0, 0, 0.3);
}

.logout-btn {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  background-color: rgba(255, 255, 255, 0.1);
  transition: all var(--transition-speed) ease;
}

.logout-btn:hover {
  background-color: rgba(255, 255, 255, 0.2);
  color: #ff6b6b;
}

.menu-toggle {
  display: none;
  background: none;
  border: none;
  color: var(--text-light);
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 4px;
}

.menu-toggle:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

/* Íconos */
.icon-logout::before {
  content: '→';
  font-size: 0.9em;
}

.icon-menu::before {
  content: '☰';
}

/* Responsive */
@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }

  .menu-toggle {
    display: block;
  }

  .nav-links {
    position: fixed;
    top: var(--navbar-height);
    left: 0;
    right: 0;
    flex-direction: column;
    background-color: var(--primary-dark); /* Fondo más oscuro en móvil */
    padding: 1rem;
    gap: 1rem;
    transform: translateY(-150%);
    opacity: 0;
    transition: all 0.4s ease;
    pointer-events: none;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  }

  .nav-links.open {
    transform: translateY(0);
    opacity: 1;
    pointer-events: all;
  }

  .nav-links a,
  .nav-links button {
    width: 100%;
    padding: 0.75rem;
    justify-content: center;
    border-radius: 4px;
  }

  .nav-links a:hover,
  .nav-links button:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }

  .logout-btn {
    margin-top: 0.5rem;
    background-color: rgba(255, 255, 255, 0.15);
  }
}
</style>