<template>
  <div class="room-search container">
    <div class="search-header">
      <div class="filters">
        <label class="filter-label">
          <h2>Tipo de habitación</h2>
          <select v-model="roomType" class="filter-select">
            <option value="">Todas</option>
            <option value="Familiar">Familiar</option>
            <option value="Amigos">Amigos</option>
            <option value="Matrimonial">Matrimonial</option>
            <option value="Eventos">Eventos</option>
          </select>
        </label>
      </div>
    </div>
    <div class="room-grid">
      <RoomCard
        v-for="r in filteredRooms"
        :key="r.id"
        :room="r"
        :mostrarBotonCambio="auth.isAdmin"
        @cambiar-estado="actualizarEstado"
      />
    </div>
    <div v-if="filteredRooms.length === 0" class="no-rooms">
      No se encontraron habitaciones con los filtros seleccionados
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import RoomCard from '../components/RoomCard.vue';
import { auth } from '../store/auth'; // Asegúrate de usar la ruta correcta

const roomType = ref('');
const rooms = ref([]);

const filteredRooms = computed(() => {
  return roomType.value
    ? rooms.value.filter(r => r.tipo_habitacion === roomType.value)
    : rooms.value;
});

onMounted(() => {
  cargarHabitaciones();
});

async function cargarHabitaciones() {
  try {
    const res = await fetch('http://localhost:8001/api/habitaciones/listar.php');
    const data = await res.json();
    if (data.success) {
  rooms.value = data.habitaciones.map(h => ({
    ...h,
    imagen: h.imagen
    ? `http://localhost:8001/${h.imagen}`
    : 'http://localhost:8001/uploads/imagen-default.jpg',
      id_descuento_especial: h.id_descuento_especial || null,
      disponible: h.disponible == 1
      }));
    }
  } catch (err) {
    console.error('Error al cargar habitaciones', err);
  }
}

async function actualizarEstado(roomId, nuevoEstado) {
  try {
    const res = await fetch('http://localhost:8001/api/habitaciones/cambiar_estado.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id: roomId,
        esta_disponible: nuevoEstado ? 1 : 0
      })
    });

    const data = await res.json();
    if (!data.success) throw new Error(data.error);

    // 👉 vuelve a cargar las habitaciones para reflejar el cambio
    await cargarHabitaciones();
  } catch (error) {
    console.error('Error en actualizarEstado:', error);
    alert('Error al cambiar estado');
  }
}
</script>

<style scoped>
.room-search {
  max-width: 1400px;
  width: 100%;
  height: 100%; 
  margin: auto auto;
  padding: 2rem 1rem;
}

.search-header {
  margin-bottom: 5rem;
  text-align: center;
}

.search-header h2 {
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 1rem;
}

.filters {
  display: flex;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
  
}

.filter-label {
  font-weight: 600;
  color: #34495e;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-select {
  padding: 0.75rem 1rem;
  font-size: 1rem;
  border-radius: 8px;
  border: 1px solid #ddd;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  transition: all 0.2s;
  min-width: 250px;
}

.filter-select:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
}

.room-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 1.5rem;
  padding: 0 1rem;
}

.no-rooms {
  text-align: center;
  padding: 2rem;
  font-size: 1.1rem;
  color: #7f8c8d;
  background-color: #f8f9fa;
  border-radius: 8px;
  margin-top: 2rem;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
  padding: 1rem;
}

.pagination-button {
  padding: 0.5rem 1rem;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.pagination-button:hover:not(:disabled) {
  background-color: #2980b9;
}

.pagination-button:disabled {
  background-color: #bdc3c7;
  cursor: not-allowed;
}

.page-info {
  font-size: 1rem;
  color: #2c3e50;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
  .room-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 900px) {
  .room-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .room-grid {
    grid-template-columns: 1fr;
  }
  
  .filter-select {
    min-width: 200px;
  }
  
  .pagination {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style>
