<template>
  <div class="discounts-container">
    <div class="discounts-header">
      <h2>🎁 Descuentos y Promociones</h2>
      <p class="subtitle">Administra las promociones activas para tus clientes</p>
    </div>

    <div class="add-discount-card">
      <h3>➕ Crear Nuevo Descuento</h3>
      <form @submit.prevent="registrarDescuento" class="discount-form">
        <div class="form-row">
          <div class="form-group">
            <label>Nombre del descuento</label>
            <input type="text" 
                   v-model="nuevo.nombre_descuento" 
                   placeholder="Ej. Verano 2023" 
                   required 
                   class="form-input" />
          </div>
          
          <div class="form-group">
            <label>Porcentaje de descuento</label>
            <div class="percentage-input">
              <input type="number" 
                     step="0.01" 
                     min="0" 
                     max="100"
                     v-model.number="nuevo.porcentaje_descuento" 
                     placeholder="0.00" 
                     required 
                     class="form-input" />
              <span class="percent-symbol">%</span>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Fecha de inicio</label>
            <input type="date" 
                   v-model="nuevo.fecha_inicio" 
                   required 
                   class="form-input" />
          </div>
          
          <div class="form-group">
            <label>Fecha de fin</label>
            <input type="date" 
                   v-model="nuevo.fecha_fin" 
                   required 
                   class="form-input" />
          </div>
        </div>

        <button type="submit" class="submit-btn">
          Registrar Descuento
        </button>
      </form>
    </div>

    <div class="active-discounts">
      <h3>📋 Lista de Descuentos Activos</h3>
      
      <div class="table-container">
        <table class="discount-table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Descuento</th>
              <th>Vigencia</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="descuento in descuentos" :key="descuento.id">
              <td>{{ descuento.nombre_descuento }}</td>
              <td class="discount-percent">
                <span class="percent-badge">
                  {{ descuento.porcentaje_descuento }}%
                </span>
              </td>
              <td>
                <div class="date-range">
                  <span>{{ formatDate(descuento.fecha_inicio) }}</span>
                  <span class="date-separator">al</span>
                  <span>{{ formatDate(descuento.fecha_fin) }}</span>
                </div>
              </td>
              <td>
                <button @click="eliminarDescuento(descuento.id)" class="delete-btn">
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const descuentos = ref([]);
const nuevo = ref({
  nombre_descuento: '',
  porcentaje_descuento: 0,
  fecha_inicio: '',
  fecha_fin: ''
});

function formatDate(dateString) {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
}

async function cargarDescuentos() {
  try {
    const res = await fetch('http://localhost:8001/api/descuentos/listar.php');
    const data = await res.json();
    if (data.success) descuentos.value = data.descuentos;
  } catch (err) {
    console.error('Error al cargar descuentos:', err);
    alert('Error al cargar los descuentos');
  }
}

async function registrarDescuento() {
  try {
    const res = await fetch('http://localhost:8001/api/descuentos/registrar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(nuevo.value)
    });
    const data = await res.json();
    if (data.success) {
      alert('✅ Descuento registrado correctamente');
      cargarDescuentos();
      nuevo.value = { nombre_descuento: '', porcentaje_descuento: 0, fecha_inicio: '', fecha_fin: '' };
    } else {
      alert('❌ ' + (data.error || 'Error al registrar descuento'));
    }
  } catch (err) {
    console.error('Error al registrar descuento:', err);
    alert('Error al registrar el descuento');
  }
}

async function eliminarDescuento(id) {
  if (!confirm('¿Estás seguro de eliminar este descuento?')) return;
  try {
    const res = await fetch(`http://localhost:8001/api/descuentos/eliminar.php?id=${id}`, {
      method: 'DELETE'
    });
    const data = await res.json();
    if (data.success) {
      alert('✅ Descuento eliminado correctamente');
      cargarDescuentos();
    } else {
      alert('❌ ' + (data.error || 'Error al eliminar descuento'));
    }
  } catch (err) {
    console.error('Error al eliminar descuento:', err);
    alert('Error al eliminar el descuento');
  }
}

onMounted(cargarDescuentos);
</script>

<style scoped>
.discounts-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 2rem;
}

.discounts-header {
  text-align: center;
  margin-bottom: 2rem;
}

.discounts-header h2 {
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #7f8c8d;
  font-size: 1rem;
}

.add-discount-card {
  background: white;
  border-radius: 10px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.add-discount-card h3 {
  font-size: 1.25rem;
  color: #2c3e50;
  margin-bottom: 1.5rem;
}

.discount-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.9rem;
  color: #2c3e50;
  font-weight: 500;
}

.form-input {
  padding: 0.75rem;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  font-size: 1rem;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.percentage-input {
  position: relative;
}

.percent-symbol {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #7f8c8d;
}

.submit-btn {
  background-color: #27ae60;
  color: white;
  padding: 0.75rem;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  align-self: flex-end;
  min-width: 200px;
}

.submit-btn:hover {
  background-color: #219653;
}

.active-discounts h3 {
  font-size: 1.25rem;
  color: #2c3e50;
  margin-bottom: 1.5rem;
}

.table-container {
  overflow-x: auto;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.discount-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.95rem;
}

.discount-table th {
  background-color: #f8f9fa;
  color: #2c3e50;
  font-weight: 600;
  padding: 1rem;
  text-align: left;
  border-bottom: 2px solid #eee;
}

.discount-table td {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  vertical-align: middle;
}

.discount-table tr:last-child td {
  border-bottom: none;
}

.discount-table tr:hover {
  background-color: #f8f9fa;
}

.percent-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background-color: #e3f2fd;
  color: #1976d2;
  border-radius: 50px;
  font-weight: 500;
}

.date-range {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.date-separator {
  font-size: 0.8rem;
  color: #7f8c8d;
  text-align: center;
}

.delete-btn {
  background-color: #ffebee;
  color: #d32f2f;
  padding: 0.5rem 0.75rem;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.delete-btn:hover {
  background-color: #ef9a9a;
  color: #b71c1c;
}

/* Responsive Design */
@media (max-width: 768px) {
  .discounts-container {
    padding: 1.5rem;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .submit-btn {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .discounts-container {
    padding: 1rem;
  }
  
  .discounts-header h2 {
    font-size: 1.5rem;
  }
  
  .discount-table th,
  .discount-table td {
    padding: 0.75rem;
  }
}
</style>