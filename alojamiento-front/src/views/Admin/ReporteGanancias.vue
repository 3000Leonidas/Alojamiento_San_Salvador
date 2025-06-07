<template>
  <div class="report-container">
    <div class="report-header">
      <div class="header-content">
        <h1 class="report-title">💰 Reporte de Ganancias por Habitación</h1>
        <p class="report-subtitle">Análisis de rentabilidad por tipo de habitación</p>
      </div>
      <div class="report-actions">
        <button class="action-btn export-btn" @click="exportToPDF">
          <i class="icon-pdf"></i> Exportar PDF
        </button>
        <button class="action-btn print-btn" @click="printReport">
          <i class="icon-print"></i> Imprimir
        </button>
      </div>
    </div>

    <div class="date-range-picker">
      <div class="date-input">
        <label for="start-date">Desde:</label>
        <input type="date" id="start-date" v-model="startDate">
      </div>
      <div class="date-input">
        <label for="end-date">Hasta:</label>
        <input type="date" id="end-date" v-model="endDate">
      </div>
      <button class="filter-btn" @click="filterByDate">
        <i class="icon-filter"></i> Filtrar
      </button>
    </div>

    <div class="summary-cards">
      <div class="summary-card total-card">
        <div class="summary-value">{{ formatCurrency(totalGanancias) }}</div>
        <div class="summary-label">Ganancias Totales</div>
      </div>
      <div class="summary-card">
        <div class="summary-value">{{ habitacionMasRentable.numero || '-' }}</div>
        <div class="summary-label">Habitación Más Rentable</div>
        <div class="summary-subvalue" v-if="habitacionMasRentable.numero">
          {{ formatCurrency(habitacionMasRentable.ganancias) }}
        </div>
      </div>
      <div class="summary-card">
        <div class="summary-value">{{ totalReservas }}</div>
        <div class="summary-label">Reservas Totales</div>
      </div>
    </div>

    <div class="table-wrapper">
      <table class="report-table">
        <thead>
          <tr>
            <th @click="sortBy('numero_habitacion')" :class="{ active: sortField === 'numero_habitacion' }">
              Habitación <i :class="sortIcon('numero_habitacion')"></i>
            </th>
            <th @click="sortBy('tipo_habitacion')" :class="{ active: sortField === 'tipo_habitacion' }">
              Tipo <i :class="sortIcon('tipo_habitacion')"></i>
            </th>
            <th @click="sortBy('total_reservas')" :class="{ active: sortField === 'total_reservas' }">
              Reservas <i :class="sortIcon('total_reservas')"></i>
            </th>
            <th @click="sortBy('total_ganancias')" :class="{ active: sortField === 'total_ganancias' }">
              Ganancias <i :class="sortIcon('total_ganancias')"></i>
            </th>
            <th @click="sortBy('ultima_reserva')" :class="{ active: sortField === 'ultima_reserva' }">
              Última Reserva <i :class="sortIcon('ultima_reserva')"></i>
            </th>
            <th>Rentabilidad</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in sortedGanancias" :key="item.numero_habitacion">
            <td class="room-number">#{{ item.numero_habitacion }}</td>
            <td class="room-type">{{ item.tipo_habitacion }}</td>
            <td class="reservations">{{ item.total_reservas }}</td>
            <td class="earnings">{{ formatCurrency(item.total_ganancias) }}</td>
            <td class="last-reservation">{{ formatDate(item.ultima_reserva) }}</td>
            <td class="profitability">
              <div class="progress-bar">
                <div 
                  class="progress-fill" 
                  :style="{ width: calculateProfitability(item.total_ganancias) + '%' }"
                  :class="getProfitabilityClass(item.total_ganancias)"
                ></div>
                <span class="progress-text">{{ calculateProfitability(item.total_ganancias) }}%</span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="loading" class="loading-overlay">
      <div class="spinner"></div>
    </div>

    <div v-if="ganancias.length === 0 && !loading" class="empty-state">
      <i class="icon-empty"></i>
      <p>No se encontraron datos de ganancias</p>
      <button class="retry-btn" @click="loadData">Reintentar</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const ganancias = ref([]);
const loading = ref(false);
const sortField = ref('total_ganancias');
const sortDirection = ref('desc');
const startDate = ref('');
const endDate = ref('');

// Cargar datos iniciales
onMounted(() => {
  loadData();
});

// Computed properties
const sortedGanancias = computed(() => {
  return [...ganancias.value].sort((a, b) => {
    let fieldA = a[sortField.value];
    let fieldB = b[sortField.value];
    
    if (sortField.value === 'ultima_reserva') {
      fieldA = new Date(fieldA);
      fieldB = new Date(fieldB);
    } else if (sortField.value === 'total_ganancias') {
      fieldA = parseFloat(fieldA);
      fieldB = parseFloat(fieldB);
    }
    
    if (fieldA < fieldB) return sortDirection.value === 'asc' ? -1 : 1;
    if (fieldA > fieldB) return sortDirection.value === 'asc' ? 1 : -1;
    return 0;
  });
});

const totalGanancias = computed(() => {
  return ganancias.value.reduce((sum, item) => sum + parseFloat(item.total_ganancias), 0);
});

const totalReservas = computed(() => {
  return ganancias.value.reduce((sum, item) => sum + parseInt(item.total_reservas), 0);
});

const habitacionMasRentable = computed(() => {
  if (ganancias.value.length === 0) return {};
  const sorted = [...ganancias.value].sort((a, b) => parseFloat(b.total_ganancias) - parseFloat(a.total_ganancias));
  return {
    numero: sorted[0].numero_habitacion,
    ganancias: parseFloat(sorted[0].total_ganancias)
  };
});

// Métodos
async function loadData() {
  try {
    loading.value = true;
    let url = 'http://localhost:8001/api/report/ganancias.php';
    
    if (startDate.value && endDate.value) {
      url += `?fecha_inicio=${startDate.value}&fecha_fin=${endDate.value}`;
    }
    
    const res = await fetch(url);
    const data = await res.json();
    
    if (data.success) {
      ganancias.value = data.reporte;
    } else {
      console.error(data.error);
      ganancias.value = [];
    }
  } catch (err) {
    console.error('Error al cargar reporte de ganancias:', err);
    ganancias.value = [];
  } finally {
    loading.value = false;
  }
}

function sortBy(field) {
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

function formatCurrency(value) {
  return `Bs ${parseFloat(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`;
}

function formatDate(dateString) {
  if (!dateString) return '-';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
}

function calculateProfitability(ganancia) {
  if (totalGanancias.value === 0) return 0;
  return ((parseFloat(ganancia) / totalGanancias.value) * 100).toFixed(1);
}

function getProfitabilityClass(ganancia) {
  const percentage = calculateProfitability(ganancia);
  if (percentage > 20) return 'high-profit';
  if (percentage > 10) return 'medium-profit';
  return 'low-profit';
}

function filterByDate() {
  if (startDate.value && endDate.value && startDate.value > endDate.value) {
    alert('La fecha de inicio no puede ser mayor a la fecha final');
    return;
  }
  loadData();
}

function exportToPDF() {
  console.log('Exportando a PDF...');
  // Implementar lógica de exportación a PDF
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
  --success-color: #2ecc71;
  --warning-color: #f39c12;
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
  color: var(--primary-color);
  position: relative;
}

.report-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.header-content {
  flex: 1;
}

.report-title {
  font-size: 1.8rem;
  margin: 0;
  color: var(--primary-color);
}

.report-subtitle {
  font-size: 1rem;
  color: var(--dark-gray);
  margin: 0.5rem 0 0;
}

.report-actions {
  display: flex;
  gap: 1rem;
}

.action-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: var(--border-radius);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  cursor: pointer;
  transition: var(--transition);
}

.export-btn {
  background-color: var(--danger-color);
  color: var(--white);
}

.print-btn {
  background-color: var(--dark-gray);
  color: var(--white);
}

.action-btn:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

/* Filtros por fecha */
.date-range-picker {
  display: flex;
  gap: 1rem;
  align-items: flex-end;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.date-input {
  display: flex;
  flex-direction: column;
}

.date-input label {
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  color: var(--dark-gray);
}

.date-input input {
  padding: 0.5rem;
  border: 1px solid var(--medium-gray);
  border-radius: var(--border-radius);
}

.filter-btn {
  padding: 0.5rem 1rem;
  background-color: var(--secondary-color);
  color: var(--white);
  border: none;
  border-radius: var(--border-radius);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: var(--transition);
}

.filter-btn:hover {
  background-color: #3aa876;
}

/* Tarjetas de resumen */
.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.summary-card {
  background: var(--white);
  border-radius: var(--border-radius);
  padding: 1.5rem;
  box-shadow: var(--box-shadow);
}

.total-card {
  background-color: var(--primary-color);
  color: var(--white);
}

.total-card .summary-value {
  color: var(--white);
}

.total-card .summary-label {
  color: rgba(255, 255, 255, 0.8);
}

.summary-value {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: var(--secondary-color);
}

.summary-label {
  font-size: 0.9rem;
  color: var(--dark-gray);
}

.summary-subvalue {
  font-size: 0.9rem;
  margin-top: 0.5rem;
  color: var(--primary-color);
  font-weight: 500;
}

/* Tabla */
.table-wrapper {
  overflow-x: auto;
  background: var(--white);
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  padding: 1rem;
  margin-bottom: 2rem;
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

/* Columnas específicas */
.room-number {
  font-weight: 600;
  color: var(--primary-color);
}

.room-type {
  text-transform: capitalize;
}

.earnings {
  font-weight: 600;
  color: var(--success-color);
}

.last-reservation {
  white-space: nowrap;
}

/* Barra de rentabilidad */
.progress-bar {
  height: 24px;
  background-color: var(--light-gray);
  border-radius: 12px;
  position: relative;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 12px;
  transition: width 0.5s ease;
}

.progress-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--white);
  z-index: 1;
}

.high-profit .progress-fill {
  background-color: var(--success-color);
}

.medium-profit .progress-fill {
  background-color: var(--accent-color);
}

.low-profit .progress-fill {
  background-color: var(--warning-color);
}

/* Estado vacío */
.empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--dark-gray);
  background-color: var(--light-gray);
  border-radius: var(--border-radius);
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  display: block;
  color: var(--medium-gray);
}

.retry-btn {
  margin-top: 1rem;
  padding: 0.5rem 1.5rem;
  background-color: var(--accent-color);
  color: var(--white);
  border: none;
  border-radius: var(--border-radius);
  cursor: pointer;
  transition: var(--transition);
}

.retry-btn:hover {
  background-color: #2980b9;
}

/* Loading */
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--medium-gray);
  border-top-color: var(--secondary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Íconos */
[class^="icon-"]::before {
  font-family: 'Arial', sans-serif;
  font-style: normal;
}

.icon-pdf::before {
  content: '📄';
}

.icon-print::before {
  content: '🖨';
}

.icon-filter::before {
  content: '🔍';
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

.icon-empty::before {
  content: '📊';
}

/* Responsive */
@media (max-width: 768px) {
  .report-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .report-actions {
    width: 100%;
    justify-content: flex-end;
  }
  
  .date-range-picker {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .date-input {
    width: 100%;
  }
  
  .filter-btn {
    width: 100%;
    justify-content: center;
  }
  
  .report-table th:nth-child(5),
  .report-table td:nth-child(5) {
    display: none;
  }
}

@media print {
  .report-actions, .date-range-picker {
    display: none;
  }
  
  .report-table {
    width: 100%;
    font-size: 10pt;
  }
}
</style>