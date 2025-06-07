<template>
  <div class="restaurant-view container">
    <div class="header-section">
      <h2>🍽️ Restaurante AlojaFácil</h2>
      <p class="description">Disfruta de nuestros platos especiales durante tu estadía</p>
      
      <div class="filter-controls">
        <button 
          @click="setFilter('all')"
          :class="{ active: activeFilter === 'all' }"
          class="filter-btn"
        >
          Todos los platos
        </button>
        <button 
          @click="setFilter('promo')"
          :class="{ active: activeFilter === 'promo' }"
          class="filter-btn"
        >
          En promoción
        </button>
        <button 
          @click="setFilter('regular')"
          :class="{ active: activeFilter === 'regular' }"
          class="filter-btn"
        >
          Precio regular
        </button>
      </div>
    </div>

    <div class="menu-grid">
      <div v-for="item in filteredMenu" :key="item.id" class="menu-card">
        <div class="image-container">
          <img :src="getImageUrl(item.image)" :alt="item.name" class="menu-image" />
          <div v-if="hasValidDiscount(item)" class="promo-badge">🎉 En promoción</div>
          <div v-if="hasValidDiscount(item)" class="discount-badge">
            -{{ getDiscountPercentage(item) }}%
          </div>
        </div>
        <div class="menu-content">
          <h3>{{ item.name }}</h3>
          <p class="item-description">{{ item.description }}</p>
          <div class="menu-footer">
            <div class="price-container">
              <span v-if="hasValidDiscount(item)" class="old-price">
                Precio original: ${{ formatPrice(item.precio_original) }}
              </span>
              <span class="price" :class="{ 'promo-price': hasValidDiscount(item) }">
                {{ hasValidDiscount(item) ? 'Precio con descuento:' : 'Precio:' }} ${{ formatPrice(item.price) }}
              </span>
            </div>
            <div v-if="hasValidDiscount(item)" class="discount-details">
              <span class="discount-text">
                🎁 Ahorras ${{ formatPrice(calculateSavings(item)) }} ({{ getDiscountPercentage(item) }}% de descuento)
              </span>
              <span class="date-range">
                📅 Válido del {{ formatDate(item.descuento.fecha_inicio) }} al {{ formatDate(item.descuento.fecha_fin) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const menu = ref([]);
const activeFilter = ref('all');

// Funciones de utilidad
const hasValidDiscount = (item) => {
  return item.promocion && item.descuento;
};

const getDiscountPercentage = (item) => {
  return hasValidDiscount(item) ? Math.round(item.descuento.porcentaje_descuento) : 0;
};

const calculateSavings = (item) => {
  return hasValidDiscount(item) ? (item.precio_original - item.price) : 0;
};

const formatPrice = (price) => {
  const num = parseFloat(price || 0);
  return num.toFixed(2);
};

const formatDate = (dateString) => {
  const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

const getImageUrl = (path) => {
  if (!path) return '/img/restaurante/default.jpg';
  return path.startsWith('http') ? path : `http://localhost:8001/${path}`;
};

// Filtrado del menú
const filteredMenu = computed(() => {
  if (activeFilter.value === 'all') return menu.value;
  if (activeFilter.value === 'promo') return menu.value.filter(item => hasValidDiscount(item));
  return menu.value.filter(item => !hasValidDiscount(item));
});

const setFilter = (filterType) => {
  activeFilter.value = filterType;
};

// Carga de datos
onMounted(async () => {
  try {
    const response = await fetch('http://localhost:8001/api/platos/listar.php');
    const data = await response.json();
    
    console.log('Datos recibidos del API:', data);
    
    if (data.success) {
      menu.value = data.platos.map(p => ({
        id: p.id,
        name: p.nombre,
        description: p.descripcion,
        price: p.tiene_descuento ? p.precio : p.precio_original,
        precio_original: p.precio_original,
        image: p.imagen,
        descuento: p.descuento,
        promocion: p.tiene_descuento
      }));
    }
  } catch (error) {
    console.error('Error al cargar el menú:', error);
    alert('Error al cargar el menú. Por favor revisa la consola para más detalles.');
  }
});
</script>

<style scoped>
.container {
  max-width: 1200px;
  width: 100%;
  margin: auto;
  padding: 2rem;
}

.header-section {
  text-align: center;
  margin-bottom: 3rem;
}

.header-section h2 {
  font-size: 2.2rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.description {
  color: #7f8c8d;
  font-size: 1.1rem;
  max-width: 600px;
  margin: 0 auto 1.5rem;
}

.filter-controls {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}

.filter-btn {
  padding: 0.6rem 1.2rem;
  border: 2px solid #3498db;
  background: white;
  color: #3498db;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-btn:hover {
  background: #f8f9fa;
}

.filter-btn.active {
  background: #3498db;
  color: white;
}

.menu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.menu-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease;
}

.menu-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.image-container {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.menu-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.menu-card:hover .menu-image {
  transform: scale(1.05);
}

.promo-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: #e74c3c;
  color: white;
  padding: 0.4rem 0.8rem;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: bold;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.discount-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #2ecc71;
  color: white;
  padding: 0.4rem 0.8rem;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: bold;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.menu-content {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.menu-content h3 {
  margin: 0 0 0.5rem;
  color: #2c3e50;
  font-size: 1.3rem;
}

.item-description {
  color: #666;
  margin-bottom: 1rem;
  flex: 1;
  font-size: 0.95rem;
  line-height: 1.5;
}

.menu-footer {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: auto;
}

.price-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.old-price {
  text-decoration: line-through;
  color: #95a5a6;
  font-size: 1rem;
}

.price {
  font-weight: bold;
  font-size: 1.2rem;
  color: #2c3e50;
}

.promo-price {
  color: #e74c3c;
  font-size: 1.3rem;
}

.discount-details {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  margin-top: 0.5rem;
}

.discount-text {
  font-size: 0.9rem;
  color: #2ecc71;
  font-weight: bold;
  background: #f8f9fa;
  padding: 0.3rem 0.6rem;
  border-radius: 4px;
}

.date-range {
  font-size: 0.8rem;
  color: #7f8c8d;
  background: #f8f9fa;
  padding: 0.3rem 0.6rem;
  border-radius: 4px;
}

@media (max-width: 768px) {
  .menu-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }
  
  .filter-controls {
    flex-direction: column;
    align-items: center;
  }
  
  .filter-btn {
    width: 100%;
    max-width: 250px;
  }
  
  .price-container {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.2rem;
  }
}
</style>