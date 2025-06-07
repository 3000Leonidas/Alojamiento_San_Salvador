<template>
  <div class="admin-dashboard">
    <div class="dashboard-header">
      <h1 class="dashboard-title">🏢 Panel Administrativo</h1>
      <p class="dashboard-subtitle">Gestión integral del sistema hotelero</p>
    </div>
    
    <div class="admin-columns-container">
      <!-- Columna 1 -->
      <div class="admin-column">
        <router-link to="/admin/users" class="menu-item">
          <div class="menu-icon user-icon"><i class="fas fa-users"></i></div>
          <div class="menu-content">
            <h3>Gestión de Usuarios</h3>
            <p>Administra usuarios y permisos</p>
          </div>
          <div class="menu-arrow"><i class="fas fa-chevron-right"></i></div>
        </router-link>

        <router-link to="/admin/huespedes" class="menu-item">
          <div class="menu-icon guest-icon"><i class="fas fa-address-book"></i></div>
          <div class="menu-content">
            <h3>Gestión de Huéspedes</h3>
            <p>Registro de huéspedes</p>
          </div>
          <div class="menu-arrow"><i class="fas fa-chevron-right"></i></div>
        </router-link>
      </div>

      <!-- Columna 2 -->
      <div class="admin-column">
        <router-link to="/admin/rooms" class="menu-item">
          <div class="menu-icon room-icon"><i class="fas fa-bed"></i></div>
          <div class="menu-content">
            <h3>Habitaciones</h3>
            <p>Administra habitaciones</p>
          </div>
          <div class="menu-arrow"><i class="fas fa-chevron-right"></i></div>
        </router-link>

        <router-link to="/admin/registrar-habitacion" class="menu-item">
          <div class="menu-icon add-icon"><i class="fas fa-plus-circle"></i></div>
          <div class="menu-content">
            <h3>Registrar Habitación</h3>
            <p>Agrega nuevas habitaciones</p>
          </div>
          <div class="menu-arrow"><i class="fas fa-chevron-right"></i></div>
        </router-link>
      </div>

      <!-- Columna 3 -->
      <div class="admin-column">
        <router-link to="/admin/reservas" class="menu-item">
          <div class="menu-icon booking-icon"><i class="fas fa-calendar-check"></i></div>
          <div class="menu-content">
            <h3>Gestión de Reservas</h3>
            <p>Administra las reservas</p>
          </div>
          <div class="menu-arrow"><i class="fas fa-chevron-right"></i></div>
        </router-link>

        <router-link to="/admin/images" class="menu-item">
          <div class="menu-icon image-icon"><i class="fas fa-images"></i></div>
          <div class="menu-content">
            <h3>Galería de Imágenes</h3>
            <p>Administra imágenes</p>
          </div>
          <div class="menu-arrow"><i class="fas fa-chevron-right"></i></div>
        </router-link>
      </div>

      <!-- Columna 4 -->
      <div class="admin-column">
        <router-link to="/admin/discounts" class="menu-item">
          <div class="menu-icon discount-icon"><i class="fas fa-tags"></i></div>
          <div class="menu-content">
            <h3>Promociones</h3>
            <p>Configura descuentos</p>
          </div>
          <div class="menu-arrow"><i class="fas fa-chevron-right"></i></div>
        </router-link>

        <router-link to="/admin/platos" class="menu-item">
          <div class="menu-icon report-icon"><i class="fas fa-utensils"></i></div>
          <div class="menu-content">
            <h3>Gestión de Platos</h3>
            <p>Platos del restaurante</p>
          </div>
          <div class="menu-arrow"><i class="fas fa-chevron-right"></i></div>
        </router-link>
      </div>

      <!-- Columna 5 -->
      <div class="admin-column">
        <router-link to="/admin/reportes-clientes" class="menu-item">
          <div class="menu-icon discount-icon"><i class="fas fa-user-friends"></i></div>
          <div class="menu-content">
            <h3>Clientes Frecuentes</h3>
            <p>Reporte de clientes frecuentes</p>
          </div>
          <div class="menu-arrow"><i class="fas fa-chevron-right"></i></div>
        </router-link>

        <router-link to="/admin/reportes" class="menu-item">
          <div class="menu-icon report-icon">
            <i class="fas fa-chart-bar"></i>
          </div>
          <div class="menu-content">
            <h3>Reportes de ganancias</h3>
            <p>Genera estadísticas de ganacias</p>
          </div>
          <div class="menu-arrow">
            <i class="fas fa-chevron-right"></i>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

// Cargar Font Awesome
onMounted(() => {
  const script = document.createElement('script');
  script.crossOrigin = 'anonymous';
  document.head.appendChild(script);
});
</script>

<style scoped>
.admin-dashboard {
  max-width: 1200px;
  margin: auto auto;
  padding: 2rem;
  font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

.dashboard-header {
  text-align: center;
  margin-bottom: 3rem;
}

.dashboard-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.dashboard-subtitle {
  font-size: 1.1rem;
  color: #7f8c8d;
  font-weight: 400;
  max-width: 600px;
  margin: 0 auto;
}

.admin-columns-container {
  display: flex;
  gap: 1.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.admin-column {
  flex: 1;
  min-width: 250px;
  max-width: 300px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  text-decoration: none;
  color: #2c3e50;
  border-radius: 8px;
  margin-bottom: 0.75rem;
  transition: all 0.3s ease;
}

.menu-item:hover {
  background-color: #f8f9fa;
  transform: translateX(5px);
}

.menu-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  margin-right: 1rem;
  flex-shrink: 0;
}

.user-icon {
  background-color: #e3f2fd;
  color: #1976d2;
}

.guest-icon {
  background-color: #e8f5e9;
  color: #388e3c;
}

.room-icon {
  background-color: #f3e5f5;
  color: #8e24aa;
}

.add-icon {
  background-color: #e0f7fa;
  color: #00acc1;
}

.booking-icon {
  background-color: #fff3e0;
  color: #fb8c00;
}

.image-icon {
  background-color: #fce4ec;
  color: #e91e63;
}

.discount-icon {
  background-color: #e8f5e9;
  color: #43a047;
}

.report-icon {
  background-color: #e3f2fd;
  color: #1e88e5;
}

.menu-content {
  flex: 1;
}

.menu-content h3 {
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0 0 0.25rem;
}

.menu-content p {
  font-size: 0.85rem;
  color: #7f8c8d;
  margin: 0;
}

.menu-arrow {
  color: #bdc3c7;
  font-size: 1rem;
  margin-left: 0.5rem;
}

.router-link-exact-active {
  background-color: #f0f7ff;
  border-left: 3px solid #3498db;
}

/* Responsive */
@media (max-width: 1024px) {
  .admin-column {
    min-width: 220px;
  }
}

@media (max-width: 768px) {
  .admin-dashboard {
    padding: 1.5rem;
  }
  
  .dashboard-title {
    font-size: 2rem;
  }
  
  .admin-columns-container {
    gap: 1rem;
  }
  
  .admin-column {
    padding: 1rem;
  }
}

@media (max-width: 480px) {
  .admin-dashboard {
    padding: 1rem;
  }
  
  .dashboard-title {
    font-size: 1.75rem;
  }
  
  .admin-column {
    min-width: 100%;
    max-width: 100%;
  }
}
</style>