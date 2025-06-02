<template>
  <div class="room-images container">
    <h2>📷 Galería de Imágenes por Habitación</h2>

    <div v-for="room in rooms" :key="room.id" class="room-section">
      <h3>Habitación {{ room.numero_habitacion }} - {{ room.tipo_habitacion }}</h3>

      <div v-if="room.imagenes.length" class="image-list">
        <div
          v-for="img in room.imagenes"
          :key="img.id"
          class="image-item"
        >
          <img
            :src="`http://localhost:8001/${img.url_imagen}`"
            :alt="`Imagen de habitación ${room.numero_habitacion}`"
            @error="imgErrorHandler"
          />
          <button @click="eliminarImagen(img.id)">❌</button>
        </div>
      </div>
      <p v-else class="no-images">No hay imágenes para esta habitación.</p>

      <form @submit.prevent="subirImagen(room.id)">
        <input type="file" accept="image/*" @change="e => seleccionarImagen(e, room.id)" />
        <button :disabled="!imagenesSeleccionadas[room.id]">⬆️ Subir Imagen</button>
      </form>

      <hr />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const rooms = ref([]);
const imagenesSeleccionadas = ref({});

async function cargarHabitacionesConImagenes() {
  try {
    const res = await fetch('http://localhost:8001/api/habitaciones/listar.php');
    const data = await res.json();
    if (!data.success) throw new Error('No se pudo cargar habitaciones');

    const habitaciones = await Promise.all(data.habitaciones.map(async room => {
      try {
        const resImgs = await fetch(`http://localhost:8001/api/imagenes/listar.php?id_habitacion=${room.id}`);
        const imgData = await resImgs.json();
        room.imagenes = imgData.imagenes || [];
      } catch {
        room.imagenes = [];
      }
      return room;
    }));

    rooms.value = habitaciones;
  } catch (err) {
    console.error('❌ Error al cargar habitaciones:', err);
  }
}

function seleccionarImagen(event, roomId) {
  imagenesSeleccionadas.value[roomId] = event.target.files[0];
}

async function subirImagen(roomId) {
  const file = imagenesSeleccionadas.value[roomId];
  if (!file) return;

  const formData = new FormData();
  formData.append('imagen', file);
  formData.append('id_habitacion', roomId);

  try {
    const res = await fetch('http://localhost:8001/api/imagenes/subir.php', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();

    if (!data.success) throw new Error(data.error || 'Error al subir imagen');

    alert('✅ Imagen subida correctamente');
    await cargarHabitacionesConImagenes();
    imagenesSeleccionadas.value[roomId] = null;
  } catch (err) {
    alert(`❌ ${err.message}`);
  }
}

async function eliminarImagen(idImagen) {
  if (!confirm('¿Eliminar esta imagen?')) return;

  try {
    const res = await fetch(`http://localhost:8001/api/imagenes/eliminar.php?id=${idImagen}`, {
      method: 'DELETE'
    });
    const data = await res.json();

    if (!data.success) throw new Error(data.error || 'Error al eliminar imagen');

    await cargarHabitacionesConImagenes();
  } catch (err) {
    alert(`❌ ${err.message}`);
  }
}

function imgErrorHandler(event) {
  event.target.src = '/img/imagen-default.jpg';
}

onMounted(cargarHabitacionesConImagenes);
</script>

<style scoped>
.container {
  padding: 2rem;
  margin: auto auto;
}

.room-section {
  margin-bottom: 2.5rem;
}

.image-list {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin: 1rem 0;
}

.image-item {
  position: relative;
}

.image-item img {
  width: 180px;
  height: 120px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid #ccc;
  background-color: #f4f4f4;
}

.image-item button {
  position: absolute;
  top: 5px;
  right: 5px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  cursor: pointer;
}

.no-images {
  color: #888;
  margin: 0.5rem 0 1rem;
  font-style: italic;
}
</style>
