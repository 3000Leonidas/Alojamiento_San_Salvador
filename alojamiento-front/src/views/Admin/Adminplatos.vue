<template>
  <div class="admin-platos container">
    <h2>👨‍🍳 Administración de Platos</h2>
    <p>Desde aquí puedes gestionar los platos del restaurante.</p>
    
    <GestionarPlatos />
  </div>
</template>

<script setup>
import GestionarPlatos from './Gestionarplatos.vue';
</script>

<style scoped>
.container {
  padding: 2rem;
}
h2 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}
p {
  color: #7f8c8d;
  margin-bottom: 2rem;
}
</style>
