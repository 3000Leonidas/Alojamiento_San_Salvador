<template>
  <div class="restaurant-view container">
    <div class="header-section">
      <h2>🍽️ Restaurante AlojaFácil</h2>
      <p class="description">Disfruta de nuestros platos especiales durante tu estadía</p>
    </div>

    <div class="menu-grid">
      <div v-for="item in menu" :key="item.id" class="menu-card">
        <div class="image-container">
          <img :src="item.image" :alt="item.name" class="menu-image" />
        </div>
        <div class="menu-content">
          <h3>{{ item.name }}</h3>
          <p class="item-description">{{ item.description }}</p>
          <div class="menu-footer">
            <span class="price">${{ item.price.toFixed(2) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const menu = ref([
  { 
    id: 1, 
    name: 'Desayuno Continental', 
    description: 'Pan, jugo natural, frutas y café.', 
    price: 25, 
    image: '/img/restaurante/desayuno.jfif' 
  },
  { 
    id: 2, 
    name: 'Almuerzo Ejecutivo', 
    description: 'Plato fuerte, sopa y bebida.', 
    price: 45, 
    image: '/img/restaurante/Almuerzo_Ejecutivo.jpg' 
  },
  { 
    id: 3, 
    name: 'Cena Romántica', 
    description: 'Entrada, plato principal, postre y vino.', 
    price: 75, 
    image: '/img/restaurante/CenaRomantica.jfif' 
  }
]);
</script>

<style scoped>
.container {
  max-width: 1200px;
  width: 100%;
  height: 100%;
  margin: auto auto;
  padding: 2rem;
}

.header-section {
  text-align: center;
  margin-bottom: 5rem;
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
  margin: 0 auto;
}

.menu-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr); /* 3 columnas fijas */
  gap: 2rem;
}

.menu-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.menu-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.image-container {
  width: 100%;
  height: 70%;
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

.menu-content {
  padding: 3rem;
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
}

.menu-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.price {
  font-weight: bold;
  font-size: 1.2rem;
  color: #2c3e50;
}

.order-btn {
  background: #42b983;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.order-btn:hover {
  background: #3aa876;
}

/* Responsive */
@media (max-width: 900px) {
  .menu-grid {
    grid-template-columns: repeat(2, 1fr); /* 2 columnas en tablets */
  }
}

@media (max-width: 600px) {
  .menu-grid {
    grid-template-columns: 1fr; /* 1 columna en móviles */
  }
  
  .container {
    padding: 1.5rem;
  }
  
  .header-section h2 {
    font-size: 1.8rem;
  }
}
</style>