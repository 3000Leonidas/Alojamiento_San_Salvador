<template>
  <div class="report-container">
    <div class="report-header">
      <h1 class="report-title">📊 Reporte de Clientes Frecuentes</h1>
      <div class="report-actions">
        <button class="export-btn" @click="exportToExcel">
          <i class="icon-download"></i> Exportar a Excel
        </button>
        <button class="print-btn" @click="printReport">
          <i class="icon-printer"></i> Imprimir
        </button>
      </div>
    </div>
<!--
    <div class="report-filters">
      <div class="filter-group">
        <label for="sort">Ordenar por:</label>
        <select id="sort" v-model="sortBy" @change="sortClients">
          <option value="reservas">Reservas (Mayor a menor)</option>
          <option value="reciente">Última reserva (Más reciente)</option>
          <option value="nombre">Nombre (A-Z)</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="search">Buscar:</label>
        <input 
          id="search" 
          type="text" 
          v-model="searchQuery" 
          placeholder="Nombre, apellido o correo"
        >
      </div>
    </div>
  -->
    <div class="report-summary">
      <div class="summary-card">
        <div class="summary-value">{{ totalClients }}</div>
        <div class="summary-label">Clientes totales</div>
      </div>
      <div class="summary-card">
        <div class="summary-value">{{ topClient.reservas || 0 }}</div>
        <div class="summary-label">Máx. reservas</div>
        <div class="summary-client" v-if="topClient.nombre">
          {{ topClient.nombre }} {{ topClient.apellido }}
        </div>
      </div>
      <div class="summary-card">
        <div class="summary-value">{{ avgReservations }}</div>
        <div class="summary-label">Reservas promedio</div>
      </div>
    </div>

    <div class="table-container">
      <table class="report-table">
        <thead>
          <tr>
            <th @click="sort('nombre')" :class="{ active: sortField === 'nombre' }">
              Nombre <i :class="sortIcon('nombre')"></i>
            </th>
            <th @click="sort('apellido')" :class="{ active: sortField === 'apellido' }">
              Apellido <i :class="sortIcon('apellido')"></i>
            </th>
            <th @click="sort('email')" :class="{ active: sortField === 'email' }">
              Correo <i :class="sortIcon('email')"></i>
            </th>
            <th @click="sort('cantidad_reservas')" :class="{ active: sortField === 'cantidad_reservas' }">
              Reservas <i :class="sortIcon('cantidad_reservas')"></i>
            </th>
            <th @click="sort('ultima_reserva')" :class="{ active: sortField === 'ultima_reserva' }">
              Última Reserva <i :class="sortIcon('ultima_reserva')"></i>
            </th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cliente in paginatedClients" :key="cliente.id">
            <td>{{ cliente.nombre }}</td>
            <td>{{ cliente.apellido }}</td>
            <td>{{ cliente.email }}</td>
            <td class="reservas-cell">
              <span class="badge" :class="getReservationsClass(cliente.cantidad_reservas)">
                {{ cliente.cantidad_reservas }}
              </span>
            </td>
            <td>{{ formatDate(cliente.ultima_reserva) }}</td>
            <td class="actions-cell">
              <button class="action-btn view-btn" @click="viewClient(cliente.id)" title="Ver detalle">
                <i class="icon-eye"></i>
              </button>
              <button class="action-btn email-btn" @click="sendEmail(cliente.email)" title="Enviar email">
                <i class="icon-email"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="filteredClients.length === 0" class="empty-message">
      <i class="icon-empty"></i>
      <p>No se encontraron clientes que coincidan con los criterios</p>
    </div>

    <div class="pagination-controls" v-if="filteredClients.length > itemsPerPage">
      <button 
        class="pagination-btn" 
        @click="prevPage" 
        :disabled="currentPage === 1"
      >
        &lt;
      </button>
      <span class="page-info">
        Página {{ currentPage }} de {{ totalPages }}
      </span>
      <button 
        class="pagination-btn" 
        @click="nextPage" 
        :disabled="currentPage === totalPages"
      >
        &gt;
      </button>
      <select v-model="itemsPerPage" class="items-per-page">
        <option value="5">5 por página</option>
        <option value="10">10 por página</option>
        <option value="20">20 por página</option>
        <option value="50">50 por página</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const clientes = ref([]);
const searchQuery = ref('');
const sortField = ref('cantidad_reservas');
const sortDirection = ref('desc');
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Obtener datos
onMounted(async () => {
  try {
    const res = await fetch('http://localhost:8001/api/report/clientes_frecuentes.php');
    const data = await res.json();
    if (data.success) {
      clientes.value = data.clientes;
    } else {
      console.error(data.error);
    }
  } catch (error) {
    console.error('Error al cargar reporte de clientes:', error);
  }
});

// Computed properties
const filteredClients = computed(() => {
  let result = clientes.value;
  
  // Aplicar búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(cliente => 
      cliente.nombre.toLowerCase().includes(query) ||
      cliente.apellido.toLowerCase().includes(query) ||
      cliente.email.toLowerCase().includes(query)
    );
  }
  
  // Aplicar ordenamiento
  return result.sort((a, b) => {
    let fieldA = a[sortField.value];
    let fieldB = b[sortField.value];
    
    if (sortField.value === 'ultima_reserva') {
      fieldA = new Date(fieldA);
      fieldB = new Date(fieldB);
    }
    
    if (fieldA < fieldB) return sortDirection.value === 'asc' ? -1 : 1;
    if (fieldA > fieldB) return sortDirection.value === 'asc' ? 1 : -1;
    return 0;
  });
});

const paginatedClients = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredClients.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredClients.value.length / itemsPerPage.value);
});

const totalClients = computed(() => {
  return filteredClients.value.length;
});

const topClient = computed(() => {
  if (filteredClients.value.length === 0) return {};
  return [...filteredClients.value].sort((a, b) => b.cantidad_reservas - a.cantidad_reservas)[0];
});

const avgReservations = computed(() => {
  if (filteredClients.value.length === 0) return 0;
  const total = filteredClients.value.reduce((sum, cliente) => sum + cliente.cantidad_reservas, 0);
  return Math.round(total / filteredClients.value.length);
});

// Métodos
function sort(field) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'desc';
  }
}

function sortIcon(field) {
  if (sortField.value !== field) return 'icon-sort';
  return sortDirection.value === 'asc' ? 'icon-sort-up' : 'icon-sort-down';
}

function getReservationsClass(count) {
  if (count >= 10) return 'badge-high';
  if (count >= 5) return 'badge-medium';
  return 'badge-low';
}

function formatDate(dateString) {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
}

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

function viewClient(id) {
  console.log('Ver cliente:', id);
  // Navegar a vista de detalle del cliente
}

function sendEmail(email) {
  console.log('Enviar email a:', email);
  window.location.href = `mailto:${email}`;
}

function exportToExcel() {
  console.log('Exportar a Excel');
  // Implementar lógica de exportación
}

function printReport() {
  window.print();
}
</script>

<style scoped>
/* Variables */
:root {
  --primary-color: #2c3e50;
  --secondary-color: #42b983;
  --accent-color: #3498db;
  --danger-color: #e74c3c;
  --light-gray: #f8f9fa;
  --medium-gray: #e9ecef;
  --dark-gray: #6c757d;
  --white: #ffffff;
  --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  --border-radius: 8px;
  --transition: all 0.3s ease;
}

/* Estilos generales */
.report-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #333;
}

.report-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.report-title {
  font-size: 1.8rem;
  color: var(--primary-color);
  margin: 0;
}

.report-actions {
  display: flex;
  gap: 1rem;
}

/* Botones */
.export-btn, .print-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: var(--border-radius);
  background-color: var(--primary-color);
  color: var(--white);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  transition: var(--transition);
}

.print-btn {
  background-color: var(--dark-gray);
}

.export-btn:hover {
  background-color: #1a252f;
}

.print-btn:hover {
  background-color: #5a6268;
}

/* Filtros */
.report-filters {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.filter-group label {
  font-weight: 500;
  color: var(--primary-color);
}

.filter-group select, .filter-group input {
  padding: 0.5rem;
  border: 1px solid var(--medium-gray);
  border-radius: var(--border-radius);
  min-width: 200px;
}

.filter-group input {
  padding: 0.5rem 1rem;
}

/* Resumen */
.report-summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.summary-card {
  background: var(--white);
  border-radius: var(--border-radius);
  padding: 1.5rem;
  box-shadow: var(--box-shadow);
  text-align: center;
}

.summary-value {
  font-size: 2rem;
  font-weight: 700;
  color: var(--secondary-color);
  margin-bottom: 0.5rem;
}

.summary-label {
  font-size: 0.9rem;
  color: var(--dark-gray);
}

.summary-client {
  margin-top: 0.5rem;
  font-size: 0.9rem;
  color: var(--primary-color);
  font-weight: 500;
}

/* Tabla */
.table-container {
  overflow-x: auto;
  margin-bottom: 2rem;
  background: var(--white);
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  padding: 1rem;
}

.report-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
}

.report-table th {
  background-color: var(--primary-color);
  color: var(--white);
  padding: 1rem;
  text-align: left;
  font-weight: 500;
  cursor: pointer;
  user-select: none;
  transition: var(--transition);
}

.report-table th:hover {
  background-color: #1a252f;
}

.report-table th.active {
  background-color: var(--secondary-color);
}

.report-table td {
  padding: 1rem;
  border-bottom: 1px solid var(--medium-gray);
  vertical-align: middle;
}

.report-table tr:not(:last-child) td {
  border-bottom: 1px solid var(--medium-gray);
}

.report-table tr:hover td {
  background-color: rgba(66, 185, 131, 0.05);
}

/* Celdas especiales */
.reservas-cell {
  text-align: center;
}

.badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
}

.badge-high {
  background-color: rgba(66, 185, 131, 0.2);
  color: var(--secondary-color);
}

.badge-medium {
  background-color: rgba(52, 152, 219, 0.2);
  color: var(--accent-color);
}

.badge-low {
  background-color: rgba(233, 236, 239, 0.5);
  color: var(--dark-gray);
}

.actions-cell {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
}

.action-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition);
}

.view-btn {
  color: var(--accent-color);
  background-color: rgba(52, 152, 219, 0.1);
}

.email-btn {
  color: var(--danger-color);
  background-color: rgba(231, 76, 60, 0.1);
}

.action-btn:hover {
  transform: scale(1.1);
}

/* Mensaje vacío */
.empty-message {
  text-align: center;
  padding: 3rem;
  color: var(--dark-gray);
  background-color: var(--light-gray);
  border-radius: var(--border-radius);
}

.empty-message i {
  font-size: 3rem;
  margin-bottom: 1rem;
  display: block;
  color: var(--medium-gray);
}

/* Paginación */
.pagination-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
  flex-wrap: wrap;
}

.pagination-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid var(--medium-gray);
  background: none;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
}

.pagination-btn:hover:not(:disabled) {
  background-color: var(--secondary-color);
  color: var(--white);
  border-color: var(--secondary-color);
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 0.9rem;
  color: var(--dark-gray);
}

.items-per-page {
  padding: 0.5rem;
  border-radius: var(--border-radius);
  border: 1px solid var(--medium-gray);
}

/* Íconos (simulados - usar librería real) */
[class^="icon-"]::before {
  font-family: 'Arial', sans-serif;
  font-style: normal;
}

.icon-download::before {
  content: '⬇';
}

.icon-printer::before {
  content: '🖨';
}

.icon-sort::before {
  content: '⇅';
}

.icon-sort-up::before {
  content: '↑';
}

.icon-sort-down::before {
  content: '↓';
}

.icon-eye::before {
  content: '👁';
}

.icon-email::before {
  content: '✉';
}

.icon-empty::before {
  content: '😕';
}

/* Responsive */
@media (max-width: 768px) {
  .report-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .report-filters {
    flex-direction: column;
    gap: 1rem;
  }
  
  .filter-group select, .filter-group input {
    width: 100%;
  }
  
  .report-table th:nth-child(3),
  .report-table td:nth-child(3) {
    display: none;
  }
}

@media print {
  .report-actions, .report-filters, .actions-cell {
    display: none;
  }
  
  .report-table {
    width: 100%;
    font-size: 12pt;
  }
}
</style>