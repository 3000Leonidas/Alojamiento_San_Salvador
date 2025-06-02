<template>
  <div class="admin-room container">
    <h2>🏨 Gestión de Habitaciones</h2>

    <div class="room-list">
      <div class="room-card" v-for="room in rooms" :key="room.id">
        <img :src="room.imagen || 'https://via.placeholder.com/300x200?text=Habitaci\u00f3n'" class="room-image" />

        <input v-model="room.tipo_habitacion" placeholder="Tipo de habitación" />
        <input type="number" v-model.number="room.precio_por_noche" placeholder="Precio por noche" />
        <input v-model="room.numero_habitacion" placeholder="Número de habitación" />
        <select v-model="room.esta_disponible">
          <option :value="1">Disponible</option>
          <option :value="0">No disponible</option>
        </select>

        <input type="file" @change="e => subirImagen(e, room.id)" />

        <button @click="guardarCambios(room)">💾 Guardar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const rooms = ref([]);

async function cargarHabitaciones() {
  const res = await fetch('http://localhost:8001/api/habitaciones/listar.php');
  const data = await res.json();
  if (data.success) {
    rooms.value = data.habitaciones.map(h => ({
      ...h,
      imagen: h.imagen
        ? `http://localhost:8001/${h.imagen}`
        : 'http://localhost:8001/uploads/imagen-default.jpg'
    }));
  }
}

async function guardarCambios(room) {
  const res = await fetch(`http://localhost:8001/api/habitaciones/actualizar.php?id=${room.id}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(room)
  });
  const data = await res.json();
  if (data.success) alert('Habitación actualizada');
  else alert(data.error || 'Error al actualizar');
}

async function subirImagen(event, idHabitacion) {
  const file = event.target.files[0];
  if (!file) return;
  const formData = new FormData();
  formData.append('imagen', file);
  formData.append('id_habitacion', idHabitacion);

  const res = await fetch('http://localhost:8001/api/imagenes/subir.php', {
    method: 'POST',
    body: formData
  });

  const data = await res.json();
  if (data.success) cargarHabitaciones();
  else alert('Error al subir imagen');
}

onMounted(cargarHabitaciones);
</script>

<style scoped>
.container {
  padding: 2rem;
  margin-bottom: auto;
  height: 100%;
}

.room-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.room-card {
  border: 1px solid #ccc;
  padding: 1rem;
  border-radius: 8px;
  background: #fff;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.room-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 6px;
  background-color: #f0f0f0;
}

input, select {
  padding: 0.5rem;
  border-radius: 4px;
  border: 1px solid #ccc;
}

button {
  background-color: #2c3e50;
  color: white;
  border: none;
  padding: 0.6rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #1a1a1a;
}
</style>
