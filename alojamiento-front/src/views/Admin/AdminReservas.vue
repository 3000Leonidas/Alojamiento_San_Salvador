<template>
  <div class="reservations-container">
    <div class="reservations-header">
      <h2>📋 Gestión de Reservas</h2>
      <p class="subtitle">Listado completo de reservas activas</p>
    </div>

    <div class="reservations-actions">
      <div class="search-filter">
        <input 
          type="text" 
          v-model="searchQuery" 
          placeholder="Buscar reservas..." 
          class="search-input"
          @input="filterReservations"
        />
        <span class="search-icon">🔍</span>
      </div>
      <div class="status-filter">
        <select v-model="statusFilter" class="status-select" @change="filterReservations">
          <option value="all">Todas las reservas</option>
          <option value="active">Reservas activas</option>
          <option value="completed">Reservas completadas</option>
        </select>
      </div>
    </div>

    <div class="table-responsive">
      <table class="reservations-table">
        <thead>
          <tr>
            <th @click="sortReservations('id_reserva')" class="sortable">
              ID <span :class="sortIcon('id_reserva')"></span>
            </th>
            <th @click="sortReservations('nombre')" class="sortable">
              Huésped <span :class="sortIcon('nombre')"></span>
            </th>
            <th>CI</th>
            <th @click="sortReservations('numero_habitacion')" class="sortable">
              Habitación <span :class="sortIcon('numero_habitacion')"></span>
            </th>
            <th>Tipo</th>
            <th @click="sortReservations('fecha_entrada')" class="sortable">
              Ingreso <span :class="sortIcon('fecha_entrada')"></span>
            </th>
            <th @click="sortReservations('fecha_salida')" class="sortable">
              Salida <span :class="sortIcon('fecha_salida')"></span>
            </th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in paginatedReservations" :key="r.id_reserva">
            <td>{{ r.id_reserva }}</td>
            <td>{{ r.nombre }} {{ r.apellido }}</td>
            <td>{{ r.ci }}</td>
            <td>{{ r.numero_habitacion }}</td>
            <td>
              <span class="room-type-badge">{{ r.tipo_habitacion }}</span>
            </td>
            <td>{{ formatDate(r.fecha_entrada) }}</td>
            <td>{{ formatDate(r.fecha_salida) }}</td>
            <td>
              <span :class="['status-badge', getStatusClass(r)]">
                {{ getStatusText(r) }}
              </span>
            </td>
            <td class="actions">
              <button @click="releaseRoom(r)" class="action-btn release-btn" title="Liberar habitación">
                🔓
              </button>
              <button @click="editReservation(r)" class="action-btn edit-btn" title="Editar reserva">
                ✏️
              </button>
              <button @click="confirmDelete(r.id_reserva)" class="action-btn delete-btn" title="Eliminar reserva">
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="pagination" v-if="filteredReservations.length > itemsPerPage">
      <button 
        @click="prevPage" 
        :disabled="currentPage === 1"
        class="pagination-btn"
      >
        &laquo; Anterior
      </button>
      <span class="page-info">
        Página {{ currentPage }} de {{ totalPages }}
      </span>
      <button 
        @click="nextPage" 
        :disabled="currentPage === totalPages"
        class="pagination-btn"
      >
        Siguiente &raquo;
      </button>
    </div>

    <!-- Modal de confirmación -->
    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal-content">
        <h3>Confirmar Eliminación</h3>
        <p>¿Estás seguro de eliminar la reserva #{{ reservationToDelete}}?</p>
        <div class="modal-actions">
          <button @click="deleteReservation" class="confirm-btn">Eliminar</button>
          <button @click="cancelDelete" class="cancel-btn">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const reservas = ref([]);
const filteredReservations = ref([]);
const searchQuery = ref('');
const statusFilter = ref('all');
const currentSort = ref({ field: 'fecha_entrada', order: 'desc' });
const currentPage = ref(1);
const itemsPerPage = 10;
const showDeleteModal = ref(false);
const reservationToDelete = ref(null);

// Formatear fecha
function formatDate(dateString) {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
}

// Determinar estado de la reserva
function getStatusText(reservation) {
  const today = new Date();
  const checkIn = new Date(reservation.fecha_entrada);
  const checkOut = new Date(reservation.fecha_salida);
  
  if (today < checkIn) return 'Pendiente';
  if (today >= checkIn && today <= checkOut) return 'Activa';
  return 'Completada';
}

function getStatusClass(reservation) {
  const status = getStatusText(reservation);
  return {
    'pending': status === 'Pendiente',
    'active': status === 'Activa',
    'completed': status === 'Completada'
  };
}

// Ordenamiento
function sortIcon(field) {
  return {
    'sort-icon': true,
    'asc': currentSort.value.field === field && currentSort.value.order === 'asc',
    'desc': currentSort.value.field === field && currentSort.value.order === 'desc'
  };
}

function sortReservations(field) {
  if (currentSort.value.field === field) {
    currentSort.value.order = currentSort.value.order === 'asc' ? 'desc' : 'asc';
  } else {
    currentSort.value.field = field;
    currentSort.value.order = 'asc';
  }
  
  filteredReservations.value.sort((a, b) => {
    let comparison = 0;
    if (a[field] > b[field]) comparison = 1;
    if (a[field] < b[field]) comparison = -1;
    
    return currentSort.value.order === 'asc' ? comparison : -comparison;
  });
}

// Filtrado
function filterReservations() {
  let results = [...reservas.value];
  
  // Filtro por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    results = results.filter(r => 
      r.nombre.toLowerCase().includes(query) ||
      r.apellido.toLowerCase().includes(query) ||
      r.ci.toLowerCase().includes(query) ||
      r.numero_habitacion.toString().includes(query) ||
      r.tipo_habitacion.toLowerCase().includes(query)
    );
  }
  
  // Filtro por estado
  if (statusFilter.value !== 'all') {
    const today = new Date();
    results = results.filter(r => {
      const checkIn = new Date(r.fecha_entrada);
      const checkOut = new Date(r.fecha_salida);
      
      if (statusFilter.value === 'active') {
        return today >= checkIn && today <= checkOut;
      } else {
        return today > checkOut;
      }
    });
  }
  
  filteredReservations.value = results;
  currentPage.value = 1;
}

// Paginación
const totalPages = computed(() => {
  return Math.ceil(filteredReservations.value.length / itemsPerPage);
});

const paginatedReservations = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredReservations.value.slice(start, end);
});

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

// Acciones
function releaseRoom(reservation) {
  alert(`Liberar habitación ${reservation.numero_habitacion} para ${reservation.nombre}`);
  // Lógica para liberar habitación
}

function editReservation(reservation) {
  alert(`Editar reserva #${reservation.id_reserva}`);
  // Lógica para editar reserva
}

function confirmDelete(id) {
  reservationToDelete.value = id;
  showDeleteModal.value = true;
}

function cancelDelete() {
  showDeleteModal.value = false;
  reservationToDelete.value = null;
}

async function deleteReservation() {
  try {
    const res = await fetch(`http://localhost:8001/api/reservas/eliminar.php?id=${reservationToDelete.value}`, {
      method: 'DELETE'
    });
    const data = await res.json();
    if (data.success) {
      alert('✅ Reserva eliminada correctamente');
      cargarReservas();
    } else {
      alert('❌ ' + (data.error || 'Error al eliminar reserva'));
    }
  } catch (error) {
    console.error('Error al eliminar reserva:', error);
    alert('Error al eliminar la reserva');
  } finally {
    showDeleteModal.value = false;
    reservationToDelete.value = null;
  }
}

// Cargar datos
async function cargarReservas() {
  try {
    const res = await fetch('http://localhost:8001/api/reservas/listar.php');
    const data = await res.json();
    if (data.success) {
      reservas.value = data.reservas;
      filteredReservations.value = [...data.reservas];
      sortReservations('fecha_entrada');
    } else {
      console.error('Error desde el backend:', data.error);
    }
  } catch (error) {
    console.error('Error al cargar reservas:', error);
  }
}

onMounted(cargarReservas);
</script>

<style scoped>
.reservations-container {
  max-width: 1200px;
  margin: auto auto;
  padding: 2rem;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.reservations-header {
  text-align: center;
  margin-bottom: 1rem;
}

.reservations-header h2 {
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #7f8c8d;
  font-size: 1rem;
}

.reservations-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.search-filter {
  position: relative;
  flex: 1;
  min-width: 250px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 2rem 0.75rem 1rem;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  font-size: 1rem;
  transition: all 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.search-icon {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #7f8c8d;
}

.status-filter {
  min-width: 200px;
}

.status-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  font-size: 1rem;
  transition: all 0.2s;
}

.status-select:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.table-responsive {
  overflow-x: auto;
  margin-bottom: 1.5rem;
  border-radius: 8px;
  border: 1px solid #f0f0f0;
}

.reservations-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.95rem;
}

.reservations-table th {
  background-color: #f8f9fa;
  color: #2c3e50;
  font-weight: 600;
  padding: 1rem;
  text-align: left;
  border-bottom: 2px solid #eee;
  position: sticky;
  top: 0;
}

.reservations-table td {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  vertical-align: middle;
}

.reservations-table tr:last-child td {
  border-bottom: none;
}

.reservations-table tr:hover {
  background-color: #f8f9fa;
}

.sortable {
  cursor: pointer;
  user-select: none;
  position: relative;
}

.sort-icon {
  margin-left: 0.5rem;
  opacity: 0.5;
}

.sort-icon.asc::after {
  content: '↑';
}

.sort-icon.desc::after {
  content: '↓';
}

.sort-icon.asc, .sort-icon.desc {
  opacity: 1;
  color: #3498db;
}

.room-type-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background-color: #e3f2fd;
  color: #1976d2;
  border-radius: 50px;
  font-weight: 500;
  font-size: 0.85rem;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 50px;
  font-weight: 500;
  font-size: 0.85rem;
}

.status-badge.pending {
  background-color: #fff3e0;
  color: #e65100;
}

.status-badge.active {
  background-color: #e8f5e9;
  color: #2e7d32;
}

.status-badge.completed {
  background-color: #f5f5f5;
  color: #616161;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  border: none;
  background: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 4px;
  font-size: 1.1rem;
  transition: all 0.2s;
}

.release-btn:hover {
  background-color: #fff3e0;
  color: #e65100;
}

.edit-btn:hover {
  background-color: #e3f2fd;
  color: #1976d2;
}

.delete-btn:hover {
  background-color: #ffebee;
  color: #d32f2f;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 1.5rem;
}

.pagination-btn {
  padding: 0.5rem 1rem;
  background-color: #f8f9fa;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
  background-color: #e9ecef;
  border-color: #adb5bd;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 0.9rem;
  color: #495057;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  max-width: 500px;
  width: 90%;
}

.modal-content h3 {
  margin-top: 0;
  color: #2c3e50;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.confirm-btn {
  background-color: #d32f2f;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}

.cancel-btn {
  background-color: #f5f5f5;
  color: #2c3e50;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}

/* Responsive */
@media (max-width: 768px) {
  .reservations-container {
    padding: 1.5rem;
  }
  
  .reservations-table th, 
  .reservations-table td {
    padding: 0.75rem;
  }
  
  .actions {
    flex-direction: column;
    gap: 0.25rem;
  }
}

@media (max-width: 480px) {
  .reservations-container {
    padding: 1rem;
  }
  
  .reservations-header h2 {
    font-size: 1.5rem;
  }
  
  .reservations-actions {
    flex-direction: column;
    align-items: stretch;
  }
  
  .pagination {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style>