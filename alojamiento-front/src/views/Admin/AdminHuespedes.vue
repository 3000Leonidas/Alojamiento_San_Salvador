<template>
  <div class="guest-management">
    <div class="header-section">
      <h2>📋 Gestión de Huéspedes</h2>
      <p class="subtitle">Listado completo de huéspedes registrados</p>
    </div>

    <div class="search-filter">
      <div class="search-box">
        <input 
          type="text" 
          v-model="searchQuery" 
          placeholder="Buscar huésped..." 
          class="search-input"
          @input="filterGuests"
        />
        <span class="search-icon">🔍</span>
      </div>
      <div class="total-guests">
        Mostrando {{ filteredGuests.length }} de {{ huespedes.length }} huéspedes
      </div>
    </div>

    <div class="table-container">
      <table class="guest-table">
        <thead>
          <tr>
            <th @click="sortGuests('id')" class="sortable">
              ID <span :class="sortIcon('id')"></span>
            </th>
            <th @click="sortGuests('nombre')" class="sortable">
              Nombre <span :class="sortIcon('nombre')"></span>
            </th>
            <th @click="sortGuests('apellido')" class="sortable">
              Apellido <span :class="sortIcon('apellido')"></span>
            </th>
            <th>CI</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="h in paginatedGuests" :key="h.id">
            <td>{{ h.id }}</td>
            <td>{{ h.nombre }}</td>
            <td>{{ h.apellido }}</td>
            <td>{{ h.ci }}</td>
            <td>{{ h.email }}</td>
            <td>{{ h.numero_telefono }}</td>
            <td class="actions">
              <button @click="viewDetails(h)" class="action-btn view-btn" title="Ver detalles">
                👁️
              </button>
              <button @click="editGuest(h)" class="action-btn edit-btn" title="Editar">
                ✏️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="pagination" v-if="filteredGuests.length > itemsPerPage">
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

    <!-- Modal de detalles (simulado) -->
    <div class="modal" v-if="selectedGuest" @click.self="selectedGuest = null">
      <div class="modal-content">
        <span class="close-btn" @click="selectedGuest = null">&times;</span>
        <h3>Detalles del Huésped</h3>
        <div class="guest-details">
          <div class="detail-row">
            <span class="detail-label">Nombre completo:</span>
            <span class="detail-value">{{ selectedGuest.nombre }} {{ selectedGuest.apellido }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">CI:</span>
            <span class="detail-value">{{ selectedGuest.ci }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Email:</span>
            <span class="detail-value">{{ selectedGuest.email }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Teléfono:</span>
            <span class="detail-value">{{ selectedGuest.numero_telefono }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Historial de reservas:</span>
            <span class="detail-value">3 reservas (ver detalles)</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const huespedes = ref([]);
const searchQuery = ref('');
const filteredGuests = ref([]);
const selectedGuest = ref(null);
const currentSort = ref({ field: 'id', order: 'asc' });
const currentPage = ref(1);
const itemsPerPage = 10;

const totalPages = computed(() => {
  return Math.ceil(filteredGuests.value.length / itemsPerPage);
});

const paginatedGuests = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredGuests.value.slice(start, end);
});

function sortIcon(field) {
  return {
    'sort-icon': true,
    'asc': currentSort.value.field === field && currentSort.value.order === 'asc',
    'desc': currentSort.value.field === field && currentSort.value.order === 'desc'
  };
}

function sortGuests(field) {
  if (currentSort.value.field === field) {
    currentSort.value.order = currentSort.value.order === 'asc' ? 'desc' : 'asc';
  } else {
    currentSort.value.field = field;
    currentSort.value.order = 'asc';
  }
  
  filteredGuests.value.sort((a, b) => {
    let comparison = 0;
    if (a[field] > b[field]) comparison = 1;
    if (a[field] < b[field]) comparison = -1;
    
    return currentSort.value.order === 'asc' ? comparison : -comparison;
  });
}

function filterGuests() {
  if (!searchQuery.value) {
    filteredGuests.value = [...huespedes.value];
    return;
  }
  
  const query = searchQuery.value.toLowerCase();
  filteredGuests.value = huespedes.value.filter(guest => 
    guest.nombre.toLowerCase().includes(query) ||
    guest.apellido.toLowerCase().includes(query) ||
    guest.ci.toLowerCase().includes(query) ||
    guest.email.toLowerCase().includes(query) ||
    guest.numero_telefono.toLowerCase().includes(query)
  );
  currentPage.value = 1;
}

function viewDetails(guest) {
  selectedGuest.value = guest;
}

function editGuest(guest) {
  // Lógica para editar huésped
  alert(`Editar huésped: ${guest.nombre} ${guest.apellido}`);
}

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

onMounted(async () => {
  try {
    const res = await fetch('http://localhost:8001/api/huespedes/listar.php');
    const data = await res.json();
    if (data.success) {
      huespedes.value = data.huespedes;
      filteredGuests.value = [...huespedes.value];
      sortGuests('id'); // Orden inicial por ID
    }
  } catch (err) {
    console.error('Error al cargar huéspedes:', err);
    alert('Error al cargar la lista de huéspedes');
  }
});
</script>

<style scoped>
.guest-management {
  max-width: 1200px;
  margin: auto auto;
  padding: 2rem;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.header-section {
  text-align: center;
  margin-bottom: 2rem;
}

.header-section h2 {
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #7f8c8d;
  font-size: 1rem;
}

.search-filter {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.search-box {
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

.total-guests {
  font-size: 0.9rem;
  color: #7f8c8d;
}

.table-container {
  overflow-x: auto;
  margin-bottom: 1.5rem;
  border-radius: 8px;
  border: 1px solid #f0f0f0;
}

.guest-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.95rem;
}

.guest-table th {
  background-color: #f8f9fa;
  color: #2c3e50;
  font-weight: 600;
  padding: 1rem;
  text-align: left;
  border-bottom: 2px solid #eee;
  position: sticky;
  top: 0;
}

.guest-table td {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  vertical-align: middle;
}

.guest-table tr:last-child td {
  border-bottom: none;
}

.guest-table tr:hover {
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

.view-btn:hover {
  background-color: #e3f2fd;
  color: #1976d2;
}

.edit-btn:hover {
  background-color: #e8f5e9;
  color: #388e3c;
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
.modal {
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
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
}

.close-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 1.5rem;
  cursor: pointer;
  color: #7f8c8d;
}

.guest-details {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1.5rem;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #eee;
}

.detail-label {
  font-weight: 500;
  color: #2c3e50;
}

.detail-value {
  color: #7f8c8d;
}

/* Responsive */
@media (max-width: 768px) {
  .guest-management {
    padding: 1.5rem;
  }
  
  .guest-table th, 
  .guest-table td {
    padding: 0.75rem;
  }
  
  .actions {
    flex-direction: column;
    gap: 0.25rem;
  }
}

@media (max-width: 480px) {
  .guest-management {
    padding: 1rem;
  }
  
  .header-section h2 {
    font-size: 1.5rem;
  }
  
  .search-filter {
    flex-direction: column;
    align-items: stretch;
  }
  
  .pagination {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style>