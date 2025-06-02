<template>
  <div class="home">
    <!-- Hero Section (sin efectos) -->
    <section class="superHero">
      <section class="hero1">
        <div class="hero1-content">
          <h1 class="h1hero">Descubre tu hospedaje ideal</h1>
          <p>Reservá habitaciones cómodas, modernas y seguras al instante. <br>
          Tu mejor opción para reservas cómodas, seguras y rápidas</p>
          <div class="filters">
            <button @click="filter('Todos')">Todos</button>
            <button @click="filter('Familiar')">Familiar</button>
            <button @click="filter('Amigos')">Amigos</button>
            <button @click="filter('Matrimonial')">Matrimonios</button>
            <button @click="filter('Eventos')">Eventos</button>
          </div>
        </div>
      </section>
      <section class="hero2">
        <div class="hero2-content">
          <h1 class="h1hero">Bienvenido a AlojaFácil</h1>
          <router-link to="/search" class="hero-btn">Realizar mi Reserva YA!</router-link>
        </div>
      </section>
    </section>

    <!-- About/Feature Section (con efecto) -->
    <section class="features" ref="features">
      <div class="text">
        <h2>Comodidad y elegancia</h2>
        <p>Diseñadas para brindarte una experiencia inolvidable. Ya sea en familia, pareja o con amigos, tenemos la habitación perfecta para vos.</p>
        <div class="icons">
          <div><span>🛏️</span> Confort</div>
          <div><span>📶</span> Conectividad</div>
          <div><span>💳</span> Pagos fáciles</div>
        </div>
      </div>
      <img src="../assets/istockphoto-1065161022-1024x1024.jpg" alt="habitacion elegante" />
    </section>

    <!-- Galería de Habitaciones (con efecto) -->
     <section class="galeria" ref="galeria">
    <h2>Nuestras habitaciones</h2>
    <div class="carousel-container">
      <button 
        class="carousel-button prev" 
        @click="prevSlide"
        :disabled="currentIndex === 0"
      >
        &#10094;
      </button>
      
      <div class="carousel-wrapper">
        <div 
          class="carousel" 
          :style="{ transform: `translateX(-${offset}px)` }"
        >
          <div 
            class="carousel-item" 
            v-for="(r, index) in filteredRooms" 
            :key="r.id"
          >
            <RoomCard :room="r" />
          </div>
        </div>
      </div>
      
      <button 
        class="carousel-button next" 
        @click="nextSlide"
        :disabled="currentIndex >= maxIndex"
      >
        &#10095;
      </button>
    </div>
    
    <div class="carousel-dots" v-if="filteredRooms.length > itemsPerView">
      <span 
        v-for="(dot, index) in dotCount" 
        :key="index" 
        :class="{ active: currentIndex === index }"
        @click="goToSlide(index)"
      ></span>
    </div>
  </section>
<div class="home-benefits container">
    <h2>¿Por qué elegirnos?</h2>
    <div class="benefits-grid">
      <div class="benefit">
        <span class="icon">📶</span>
        <h4>Wi-Fi Gratis</h4>
        <p>Conexión de alta velocidad en todas las habitaciones.</p>
      </div>
      <div class="benefit">
        <span class="icon">🅿️</span>
        <h4>Parqueo Privado</h4>
        <p>Seguridad para tu vehículo durante toda tu estadía.</p>
      </div>
      <div class="benefit">
        <span class="icon">🍽️</span>
        <h4>Restaurante</h4>
        <p>Comidas deliciosas sin salir del alojamiento.</p>
      </div>
      <div class="benefit">
        <span class="icon">🚿</span>
        <h4>Duchas Calientes</h4>
        <p>Comodidad garantizada después de un día largo.</p>
      </div>
      <div class="benefit">
        <span class="icon">📞</span>
        <h4>Atención 24/7</h4>
        <p>Siempre disponibles para ayudarte en cualquier momento.</p>
      </div>
    </div>
  </div>
    <!-- Benefits (con efecto) -->
    <section class="benefits" ref="benefits">
      <h2>¿Por qué elegirnos?</h2>
      <ul>
        <li>✔️ Proceso de reserva sencillo y rápido</li>
        <li>✔️ Atención personalizada las 24 horas</li>
        <li>✔️ Habitaciones adaptadas a cada necesidad</li>
        <li>✔️ Pagos seguros con código QR</li>
      </ul>
    </section>

    <!-- Testimonials (con efecto) -->
    <section class="testimonials" ref="testimonials">
      <h2>Lo que opinan nuestros clientes</h2>
      <div class="testimonial-list">
        <div class="testimonial">
          <p>“Excelente servicio, rápido y confiable. ¡Recomiendo AlojaFácil!”</p>
          <span>- Carla R.</span>
        </div>
        <div class="testimonial">
          <p>“Reservé en minutos y me encantó la habitación. ¡Muy recomendable!”</p>
          <span>- Marco L.</span>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { ref, computed, onMounted, watch } from 'vue';
import RoomCard from '../components/RoomCard.vue';

const features = ref(null);
const galeria = ref(null);
const categories = ref(null);
const benefits = ref(null);
const testimonials = ref(null);

const selected = ref('');
const rooms = ref([]);

const categoriesList = [
  { name: 'Familiar', image: 'https://via.placeholder.com/300x180?text=Familiar' },
  { name: 'Amigos', image: 'https://via.placeholder.com/300x180?text=Amigos' },
  { name: 'Matrimonios', image: 'https://via.placeholder.com/300x180?text=Matrimonios' },
  { name: 'Eventos', image: 'https://via.placeholder.com/300x180?text=Eventos' },
];

const router = useRouter();

function goToCategory(type) {
  router.push({ path: '/search', query: { type } });
}

function filter(category) {
  selected.value = category;
}

const filteredRooms = computed(() => {
  return selected.value && selected.value !== 'Todos'
    ? rooms.value.filter(r => r.tipo_habitacion === selected.value)
    : rooms.value;
});

async function cargarHabitaciones() {
  try {
    const res = await fetch('http://localhost:8001/api/habitaciones/listar.php');
    const data = await res.json();
    if (data.success) {
      rooms.value = data.habitaciones.map(h => ({
        ...h,
        imagen: h.imagen ? `http://localhost:8001/${h.imagen}` : 'https://via.placeholder.com/300x200?text=Sin+imagen'
      }));
    }
  } catch (err) {
    console.error('Error cargando habitaciones:', err);
  }
}

onMounted(() => {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const animateOnScroll = (entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate');
        observer.unobserve(entry.target);
      }
    });
  };

  const observer = new IntersectionObserver(animateOnScroll, observerOptions);

  [features.value, galeria.value, categories.value, benefits.value, testimonials.value].forEach(section => {
    if (section) observer.observe(section);
  });

  cargarHabitaciones();
});
// Carrusel logic
const currentIndex = ref(0);
const itemsPerView = ref(5);
const itemWidth = ref(320); // Aumentado para incluir padding
const carouselWrapper = ref(null);

const offset = computed(() => {
  return currentIndex.value * itemWidth.value * itemsPerView.value;
});

const maxIndex = computed(() => {
  return Math.max(0, Math.ceil(filteredRooms.value.length / itemsPerView.value) - 1);
});

const dotCount = computed(() => {
  return Math.ceil(filteredRooms.value.length / itemsPerView.value);
});

function updateItemsPerView() {
  if (carouselWrapper.value) {
    const containerWidth = carouselWrapper.value.offsetWidth;
    itemsPerView.value = Math.max(1, Math.floor(containerWidth / itemWidth.value));
  }
}

function nextSlide() {
  if (currentIndex.value < maxIndex.value) {
    currentIndex.value++;
  }
}

function prevSlide() {
  if (currentIndex.value > 0) {
    currentIndex.value--;
  }
}

function goToSlide(index) {
  currentIndex.value = index;
}

onMounted(() => {
  updateItemsPerView();
  window.addEventListener('resize', updateItemsPerView);
});

watch(filteredRooms, () => {
  currentIndex.value = 0;
  updateItemsPerView();
}, { immediate: true });
</script>


<style scoped>
/* Estilos generales */
.home {
  width: 98%;
  margin: auto;
  padding: 0.1rem 1rem;
  overflow-x: hidden;
}

.superHero {
  width: 100%;
  display: inline-flex;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.hero1 {
  background-image: url('../assets/Entrada1.jpg');
  background-size: cover;
  background-position: center;
  padding: 15rem 0rem;
  border-radius: 12px;
  color: white;
  text-align: center;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.384);
  width: 80%;
}

.h1hero {
  font-size: 2.8rem;
  margin-bottom: 1rem;
  text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.986);
}

.hero1-content p {
  font-size: 1.2rem;
  margin-bottom: 1.5rem;
}

.hero2 {
  background-image: url('../assets/Entrada2.jpg');
  background-size: cover;
  background-position: center;
  padding: 4rem 2rem;
  border-radius: 12px;
  color: white;
  text-align: center;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.384);
  flex-grow: 1;
}

.hero2 h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.986);
}

.hero-btn {
  background-color: #ffffff;
  color: #2c3e50;
  font-weight: bold;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-block;
}

.hero-btn:hover {
  background-color: #e1e1e1;
  transform: scale(1.05);
}

.filters {
  display: flex;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.filters button {
  background: white;
  color: #2c3e50;
  border: none;
  padding: 0.6rem 1.2rem;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filters button:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Estilos para secciones con efectos de desplazamiento */
.features, .galeria, .categories, .benefits, .testimonials {
  opacity: 0;
  transition: all 0.8s ease;
  transform: translateX(50px);
  margin-bottom: 3rem;
}

.features.animate, 
.galeria.animate, 
.categories.animate, 
.benefits.animate, 
.testimonials.animate {
  opacity: 1;
  transform: translateX(0);
}

/* Estilos específicos para cada sección */
.features {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 2rem;
  padding: 3rem 0rem;
  align-items: center;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 12px;
}

.features .text {
  flex: 1;
  min-width: 300px;
}

.features img {
  flex: 1;
  max-width: 100%;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.384);
  transition: transform 0.5s ease;
}

.features img:hover {
  transform: scale(1.05);
}

.icons {
  display: flex;
  gap: 2rem;
  margin-top: 1rem;
  flex-wrap: wrap;
}

.icons div {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: bold;
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  transition: all 0.3s ease;
}

.icons div:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-3px);
}

.galeria {
  padding: 3rem 2rem;
  text-align: center;
}

.galeria h2 {
  margin-bottom: 2rem;
  font-size: 2.2rem;
  color: #2c3e50;
}

.categories {
  text-align: center;
  padding: 2rem;
}

.categories h2 {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: #2c3e50;
}

.category-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 2rem;
}

.category-card {
  background: white;
  padding: 0.5rem;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  cursor: pointer;
  overflow: hidden;
}

.category-card:hover {
  transform: translateY(-5px) scale(1.02);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.category-card img {
  width: 100%;
  height: 160px;
  object-fit: cover;
  border-radius: 8px;
  transition: transform 0.5s ease;
}

.category-card:hover img {
  transform: scale(1.05);
}

.category-card h3 {
  margin-top: 1rem;
  font-size: 1.1rem;
  color: #2c3e50;
}

.benefits {
  background-color: #f4f6f8;
  padding: 3rem 2rem;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.04);
}

.benefits h2 {
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  color: #2c3e50;
}

.benefits ul {
  list-style: none;
  padding: 0;
  font-size: 1.1rem;
  color: #2c3e50;
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  margin-top: 1rem;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.benefits li {
  padding: 0.8rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  transition: all 0.3s ease;
}

.benefits li:hover {
  transform: translateX(10px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.home-benefits {
  margin-top: 3rem;
  text-align: center;
}

.benefits-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.benefit {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  transition: all 0.3s ease;
}

.benefit:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.1);
}

.icon {
  font-size: 2rem;
  display: block;
  margin-bottom: 0.5rem;
}
.testimonials {
  text-align: center;
  padding: 3rem 1rem;
}

.testimonials h2 {
  font-size: 1.8rem;
  margin-bottom: 2rem;
  color: #2c3e50;
}

.testimonial-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  max-width: 800px;
  margin: auto;
}

.testimonial {
  background: #ffffff;
  padding: 1.5rem 2rem;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  font-style: italic;
  transition: all 0.3s ease;
  position: relative;
}

.testimonial:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.testimonial::before {
  content: '"';
  font-size: 4rem;
  position: absolute;
  top: -1rem;
  left: 0.5rem;
  color: rgba(66, 185, 131, 0.2);
  font-family: serif;
}

.testimonial p {
  position: relative;
  z-index: 1;
}

.testimonial span {
  display: block;
  margin-top: 1rem;
  font-weight: bold;
  color: #2c3e50;
  font-style: normal;
}

/* Responsive design */
@media (max-width: 768px) {
  .superHero {
    flex-direction: column;
  }
  
  .hero1, .hero2 {
    width: 100%;
    padding: 8rem 1rem;
  }
  
  .h1hero {
    font-size: 2rem;
  }
  
  .features {
    flex-direction: column;
    padding: 2rem 1rem;
  }
  
  .icons {
    flex-direction: column;
    gap: 1rem;
  }
}
.carousel-container {
  position: relative;
  max-width: 1600px;
  margin: 0 auto;
  padding: 0 60px; /* Más padding para los botones */
}

.carousel-wrapper {
  width: 100%;
  overflow: hidden;
  margin: 0 auto;
}

.carousel {
  display: flex;
  transition: transform 0.5s ease;
  padding: 20px 0;
  width: max-content; /* Importante para el cálculo */
}

.carousel-item {
  flex: 0 0 calc(100% / var(--items-per-view, 5) - 20px);
  min-width: 280px; /* Ancho mínimo */
  padding: 0 10px;
  box-sizing: border-box;
  margin: 0 5px; /* Espaciado entre items */
}

/* Botones más visibles */
.carousel-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(255, 255, 255, 0.95);
  border: none;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  font-size: 1.5rem;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2c3e50;
}

.carousel-button.prev {
  left: 10px;
}

.carousel-button.next {
  right: 10px;
}
.carousel-button:hover:not(:disabled) {
  background-color: #42b983;
  color: white;
  transform: translateY(-50%) scale(1.1);
}

/* Puntos más visibles */
.carousel-dots {
  display: flex;
  justify-content: center;
  margin-top: 2rem;
  gap: 12px;
}

.carousel-dots span {
  display: inline-block;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background-color: #e0e0e0;
  cursor: pointer;
  transition: all 0.3s ease;
}

.carousel-dots span.active {
  background-color: #42b983;
  transform: scale(1.3);
}

.carousel-dots span:hover:not(.active) {
  background-color: #a0a0a0;
}

/* Responsive mejorado */
@media (max-width: 1400px) {
  .carousel-item {
    flex: 0 0 calc(100% / 4 - 20px);
  }
}

@media (max-width: 1100px) {
  .carousel-item {
    flex: 0 0 calc(100% / 3 - 20px);
  min-width: 260px;
  }
}

@media (max-width: 768px) {
  .carousel-container {
    padding: 0 40px;
  }
  
  .carousel-item {
    flex: 0 0 calc(100% / 2 - 20px);
    min-width: 240px;
  }
  
  .carousel-button {
    width: 40px;
    height: 40px;
  }
}

@media (max-width: 480px) {
  .carousel-container {
    padding: 0 20px;
  }
  
  .carousel-item {
    flex: 0 0 calc(100% - 20px);
  }
  
  .carousel-button {
    width: 36px;
    height: 36px;
    font-size: 1.2rem;
  }
}
</style>