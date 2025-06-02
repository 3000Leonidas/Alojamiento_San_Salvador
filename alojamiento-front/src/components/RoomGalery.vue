<template>
  <div>
    <div class="tabs">
      <button v-for="category in categories" :key="category" @click="selected = category"
              :class="{ active: selected === category }">
        {{ category }}
      </button>
    </div>

    <div class="gallery">
      <RoomCard v-for="room in filteredRooms" :key="room.id" :room="room" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import RoomCard from './RoomCard.vue';

const categories = ['Familiar', 'Amigos', 'Matrimonios', 'Eventos'];
const selected = ref('Familiar');

const rooms = ref([
  { id: 1, name: 'Suite Familiar', category: 'Familiar', description: 'Ideal para toda la familia.', price: 120, image: 'https://via.placeholder.com/300x200?text=Familiar+1' },
  { id: 2, name: 'Suite Amigos', category: 'Amigos', description: 'Perfecta para grupos de amigos.', price: 90, image: 'https://via.placeholder.com/300x200?text=Amigos+1' },
  { id: 3, name: 'Suite Matrimonial', category: 'Matrimonios', description: 'Especial para parejas.', price: 150, image: 'https://via.placeholder.com/300x200?text=Matrimonios+1' },
  { id: 4, name: 'Salón Eventos', category: 'Eventos', description: 'Espacio para eventos privados.', price: 200, image: 'https://via.placeholder.com/300x200?text=Eventos+1' },
  { id: 5, name: 'Suite Familiar Plus', category: 'Familiar', description: 'Aún más espacio para la familia.', price: 140, image: 'https://via.placeholder.com/300x200?text=Familiar+2' }
]);

const filteredRooms = computed(() => rooms.value.filter(r => r.category === selected.value));
</script>

<style scoped>
.tabs {
  margin-bottom: 1rem;
  display: flex;
  gap: 1rem;
  justify-content: center;
}
.tabs button {
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  background: var(--button-background, #eee);
  color: var(--button-text, #000);
  font-weight: 600;
}
.tabs button.active {
  background: var(--button-active-background, #333);
  color: var(--button-active-text, white);
}
.gallery {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  justify-content: center;
}
</style>
