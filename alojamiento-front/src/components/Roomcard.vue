<template>
  <div class="room-card">
    <img :src="room.imagen" alt="Imagen habitación" class="room-img" />
    <h3>{{ room.tipo_habitacion }}</h3>
    <p class="room-number">Nº {{ room.numero_habitacion }}</p>

    <p class="price">
      <template v-if="room.porcentaje_descuento">
        <del class="original">{{ room.precio_por_noche }} Bs</del><br />
        <span class="descuento">
          {{ calcularDescuento(room.precio_por_noche, room.porcentaje_descuento) }} Bs
        </span>
      </template>
      <template v-else>
        {{ room.precio_por_noche }} Bs
      </template>
    </p>

    <p :class="['estado', room.disponible ? 'verde' : 'rojo']">
      {{ room.disponible ? 'Disponible' : 'No Disponible' }}
    </p>

    <router-link :to="{ name: 'RoomDetail', params: { id: room.id } }" class="detalle-btn">
      Detalle y Reservar
    </router-link>

    <button
      v-if="mostrarBotonCambio"
      @click="cambiarDisponibilidad"
      class="cambiar-btn"
    >
      {{ room.disponible ? 'Marcar como No Disponible' : 'Marcar como Disponible' }}
    </button>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  room: Object,
  mostrarBotonCambio: Boolean
});

const emit = defineEmits(['cambiar-estado']);

function cambiarDisponibilidad() {
  emit('cambiar-estado', props.room.id, !props.room.disponible);
}

function calcularDescuento(precio, porcentaje) {
  const final = precio - (precio * porcentaje / 100);
  return final.toFixed(2);
}
</script>
<style scoped>
.room-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  height: 100%;
  transition: all 0.3s ease;
}

.room-img {
  width: 100%;
  height: 160px;
  object-fit: cover;
}

h3 {
  margin: 0.5rem auto;
  color: #2c3e50;
  font-size: 1.1rem;
}

.room-number {
  color: #666;
  font-size: 1rem;
  margin: auto;
}

.price {
  font-weight: bold;
  color: #42b983;
  margin: auto;
  font-size: 1.6rem;
  margin-bottom: 1rem;
}

.price-con-descuento {
  display: flex;
  flex-direction: column;
  align-items: center;
  font-size: 1.4rem;
  margin-bottom: 1rem;
}

.original {
  text-decoration: line-through;
  color: #888;
  font-size: 1rem;
}

.descuento {
  color: #e74c3c;
  font-weight: bold;
}

.final {
  color: #27ae60;
  font-weight: bold;
}

.estado {
  font-weight: bold;
  margin: 0.5rem 0;
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  display: inline-block;
  width: fit-content;
  align-self: center;
}

.estado.verde {
  background-color: #28a745;
  color: white;
}

.estado.rojo {
  background-color: #dc3545;
  color: white;
}

.detalle-btn {
  background-color: #42b983;
  color: white;
  padding: 0.6rem;
  text-decoration: none;
  border-radius: 6px;
  text-align: center;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  margin-top: 1rem;
}

.detalle-btn:hover {
  background-color: #3aa876;
}

.cambiar-btn {
  background-color: #6c757d;
  color: white;
  border: none;
  padding: 0.6rem;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.cambiar-btn:hover {
  background-color: #5a6268;
}
</style>
