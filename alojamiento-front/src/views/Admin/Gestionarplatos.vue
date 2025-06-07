<template>
  <div class="gestion-platos">
    <!-- Header con título y acciones -->
    <div class="header">
      <h1 class="title">🍽️ Gestión de Menú</h1>
      <p class="subtitle">Administra los platos del restaurante</p>
    </div>

    <!-- Formulario en tarjeta -->
    <div class="card form-card">
      <h2 class="form-title">{{ form.id ? 'Editar Plato' : 'Nuevo Plato' }}</h2>
      
      <form @submit.prevent="guardarPlato" class="plato-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Nombre del Plato</label>
            <input 
              type="text" 
              v-model="form.nombre" 
              class="form-input"
              placeholder="Ej: Lomo Montado"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label">Precio (Bs)</label>
            <input 
              type="number" 
              step="0.01" 
              v-model="form.precio" 
              class="form-input"
              placeholder="0.00"
              required
            />
          </div>

          <div class="form-group full-width">
            <label class="form-label">Descripción</label>
            <textarea 
              v-model="form.descripcion" 
              class="form-textarea"
              placeholder="Ingredientes y detalles del plato..."
              required
              rows="3"
            ></textarea>
          </div>

          <div class="form-group full-width">
            <label class="form-label">Imagen del Plato</label>
            <div class="file-upload">
              <label class="file-upload-label">
                <input type="file" @change="handleFileUpload" accept="image/*" />
                <span class="file-upload-button">Seleccionar Imagen</span>
                <span class="file-upload-name" v-if="form.imagen">
                  {{ form.imagen.name }}
                </span>
                <span class="file-upload-name" v-else>
                  Ningún archivo seleccionado
                </span>
              </label>
            </div>
            <div v-if="form.id && !form.imagen" class="current-image">
              <img :src="getImageUrl(platos.find(p => p.id === form.id)?.imagen)" alt="Imagen actual" class="image-preview" />
              <span>Imagen actual</span>
            </div>
          </div>

                    <div class="form-group">
            <label class="form-label">Categoría</label>
            <select v-model="form.categoria" class="form-input" required>
                <option value="">Seleccione una categoría</option>
                <option v-for="categoria in categorias" :key="categoria" :value="categoria">
                {{ categoria }}
                </option>
            </select>
            </div>

            <div class="form-group" v-if="descuentosEspeciales.length > 0">
            <label class="form-label">Descuento Especial</label>
            <select v-model="form.id_descuento" class="form-input">
                <option value="">Sin descuento</option>
                <option v-for="descuento in descuentosEspeciales" 
                        :key="descuento.id" 
                        :value="descuento.id">
                {{ descuento.nombre_descuento }} ({{ descuento.porcentaje_descuento }}%)
                </option>
            </select>
            </div>
        </div>

        <div class="form-actions">
          <button 
            type="button" 
            class="btn btn-secondary"
            @click="resetForm"
            v-if="form.id"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            class="btn btn-primary"
            :disabled="loading"
          >
            <span v-if="!loading">{{ form.id ? 'Actualizar' : 'Guardar' }} Plato</span>
            <span v-else class="spinner"></span>
          </button>
        </div>
      </form>
    </div>

    <!-- Tabla de platos en tarjeta -->
    <div class="card table-card">
      <div class="table-header">
        <h2 class="table-title">Lista de Platos</h2>
        <div class="table-actions">
          <input 
            type="text" 
            v-model="searchQuery"
            class="search-input"
            placeholder="Buscar platos..."
          />
        </div>
      </div>

      <div class="table-responsive">
        <table class="platos-table">
          <thead>
            <tr>
              <th class="image-col">Imagen</th>
              <th>Nombre</th>
              <th class="desc-col">Descripción</th>
              <th class="price-col">Precio</th>
              <th class="actions-col">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="plato in filteredPlatos" :key="plato.id">
              <td class="image-col">
                <div class="image-wrapper">
                  <img :src="getImageUrl(plato.imagen)" alt="Imagen plato" class="table-image" />
                </div>
              </td>
              <td class="name-cell">{{ plato.nombre }}</td>
              <td class="desc-col">{{ plato.descripcion }}</td>
              <td class="price-col">{{ formatPrice(plato.precio) }}</td>
              <td class="actions-col">
                <button 
                  @click="editarPlato(plato)"
                  class="icon-btn edit-btn"
                  title="Editar"
                >
                  <svg class="icon" viewBox="0 0 24 24">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                  </svg>
                </button>
                <button 
                  @click="eliminarPlato(plato.id)"
                  class="icon-btn delete-btn"
                  title="Eliminar"
                >
                  <svg class="icon" viewBox="0 0 24 24">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="table-footer" v-if="platos.length > 5">
        <div class="pagination">
          <button class="pagination-btn" :disabled="currentPage === 1" @click="prevPage">
            &lt;
          </button>
          <span class="page-info">Página {{ currentPage }} de {{ totalPages }}</span>
          <button class="pagination-btn" :disabled="currentPage === totalPages" @click="nextPage">
            &gt;
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const platos = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 5;

onMounted(() => cargarPlatos());

// Computed
const filteredPlatos = computed(() => {
  let filtered = platos.value;
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(plato => 
      plato.nombre.toLowerCase().includes(query) || 
      plato.descripcion.toLowerCase().includes(query))
  }
  return filtered;
});

const totalPages = computed(() => {
  return Math.ceil(filteredPlatos.value.length / itemsPerPage);
});

// Métodos
function getImageUrl(path) {
  if (!path) return '/placeholder-food.jpg';
  return path.startsWith('http') ? path : `http://localhost:8001/${path}`;
}

function formatPrice(price) {
  return `Bs ${parseFloat(price).toFixed(2)}`;
}
async function cargarPlatos() {
  try {
    loading.value = true;
    const res = await fetch('http://localhost:8001/api/platos/listar.php');
    const data = await res.json();
    if (data.success) {
      platos.value = data.platos.map(plato => ({
        ...plato,
        // Asegurar que el precio sea número para los cálculos
        precio: parseFloat(plato.precio)
      }));
    }
  } finally {
    loading.value = false;
  }
}

function handleFileUpload(e) {
  form.value.imagen = e.target.files[0];
}

function editarPlato(plato) {
  form.value = { 
    ...plato, 
    imagen: null,
    id_descuento: plato.id_descuento || null // Asegurar que maneje null si no hay descuento
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
  form.value = { 
    id: null, 
    nombre: '', 
    descripcion: '', 
    precio: '', 
    categoria: '', 
    id_descuento: null,
    imagen: null 
  };
}

async function eliminarPlato(id) {
  if (!confirm('¿Estás seguro de eliminar este plato?')) return;
  
  try {
    loading.value = true;
    await fetch('http://localhost:8001/api/platos/Eliminar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });
    await cargarPlatos();
  } finally {
    loading.value = false;
  }
}

const categorias = ref(['Entrada', 'Plato Principal', 'Postre', 'Bebida']);
const descuentosEspeciales = ref([]);
const form = ref({ 
  id: null, 
  nombre: '', 
  descripcion: '', 
  precio: '', 
  categoria: '', 
  id_descuento: null,
  imagen: null 
});

// Cargar descuentos especiales
async function cargarDescuentosEspeciales() {
  try {
    const res = await fetch('http://localhost:8001/api/descuentos/listar.php');
    const data = await res.json();
    if (data.success) descuentosEspeciales.value = data.descuentos;
  } catch (error) {
    console.error('Error cargando descuentos:', error);
  }
}

// Modificar guardarPlato para incluir la categoría y descuento
async function guardarPlato() {
  try {
    loading.value = true;
    const formData = new FormData();
    formData.append('nombre', form.value.nombre);
    formData.append('descripcion', form.value.descripcion);
    formData.append('precio', form.value.precio);
    formData.append('categoria', form.value.categoria);

    if (form.value.id_descuento) {
      formData.append('id_descuento', form.value.id_descuento);
    }
    if (form.value.imagen) formData.append('imagen', form.value.imagen);
    // Si hay un ID, lo incluimos para actualizar
    if (form.value.id) formData.append('id', form.value.id);

    const response = await fetch('http://localhost:8001/api/platos/' + 
      (form.value.id ? 'actualizar.php' : 'registrar.php'), {
      method: 'POST',
      body: formData
    });
    
    resetForm();
    await cargarPlatos();

  } catch (error) {
    console.error('Error:', error);
    alert('Error al guardar: ' + error.message);
  } finally {
    loading.value = false;
  }
}

// En onMounted
onMounted(() => {
  cargarPlatos();
  cargarDescuentosEspeciales();
});

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}
</script>

<style scoped>
/* Variables */
:root {
  --primary-color: #2c3e50;
  --secondary-color: #42b983;
  --accent-color: #ff6b6b;
  --light-gray: #f5f7fa;
  --medium-gray: #e1e5eb;
  --dark-gray: #6c757d;
  --white: #ffffff;
  --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  --border-radius: 8px;
  --transition: all 0.3s ease;
}

/* Estilos generales */
.gestion-platos {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #333;
}

.header {
  text-align: center;
  margin-bottom: 2rem;
}

.title {
  font-size: 2.2rem;
  color: var(--primary-color);
  margin-bottom: 0.5rem;
}

.subtitle {
  font-size: 1.1rem;
  color: var(--dark-gray);
}

/* Tarjetas */
.card {
  background: var(--white);
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.form-card {
  border-top: 4px solid var(--secondary-color);
}

.table-card {
  border-top: 4px solid var(--primary-color);
}

/* Formulario */
.form-title {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  color: var(--primary-color);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group.full-width {
  grid-column: span 2;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: var(--primary-color);
}

.form-input, .form-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--medium-gray);
  border-radius: var(--border-radius);
  font-size: 1rem;
  transition: var(--transition);
}

.form-input:focus, .form-textarea:focus {
  outline: none;
  border-color: var(--secondary-color);
  box-shadow: 0 0 0 3px rgba(66, 185, 131, 0.2);
}

.form-textarea {
  min-height: 100px;
  resize: vertical;
}

/* File upload */
.file-upload {
  margin-top: 0.5rem;
}

.file-upload-label {
  display: flex;
  align-items: center;
  cursor: pointer;
}

.file-upload-label input[type="file"] {
  display: none;
}

.file-upload-button {
  padding: 0.75rem 1rem;
  background-color: var(--medium-gray);
  color: var(--primary-color);
  border-radius: var(--border-radius);
  margin-right: 1rem;
  transition: var(--transition);
}

.file-upload-button:hover {
  background-color: #d1d7e0;
}

.file-upload-name {
  color: var(--dark-gray);
  font-size: 0.9rem;
}

.current-image {
  margin-top: 1rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.current-image span {
  font-size: 0.9rem;
  color: var(--dark-gray);
}

.image-preview {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 4px;
  border: 1px solid var(--medium-gray);
}

/* Botones */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: var(--border-radius);
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-primary {
  background-color: var(--secondary-color);
  color: var(--white);
}

.btn-primary:hover {
  background-color: #3aa876;
  transform: translateY(-2px);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background-color: var(--medium-gray);
  color: var(--primary-color);
}

.btn-secondary:hover {
  background-color: #d1d7e0;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: var(--white);
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Tabla */
.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.table-title {
  font-size: 1.5rem;
  color: var(--primary-color);
  margin: 0;
}

.search-input {
  padding: 0.5rem 1rem;
  border: 1px solid var(--medium-gray);
  border-radius: var(--border-radius);
  min-width: 250px;
}

.table-responsive {
  overflow-x: auto;
}

.platos-table {
  width: 100%;
  border-collapse: collapse;
}

.platos-table th {
  background-color: var(--primary-color);
  color: var(--white);
  padding: 1rem;
  text-align: left;
  font-weight: 500;
}

.platos-table td {
  padding: 1rem;
  border-bottom: 1px solid var(--medium-gray);
  vertical-align: middle;
}

.platos-table tr:not(:last-child) td {
  border-bottom: 1px solid var(--medium-gray);
}

.platos-table tr:hover td {
  background-color: rgba(66, 185, 131, 0.05);
}

.image-col {
  width: 80px;
}

.desc-col {
  max-width: 300px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.price-col {
  width: 120px;
  font-weight: 600;
  color: var(--secondary-color);
}

.actions-col {
  width: 100px;
}

.image-wrapper {
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.table-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 4px;
}

.name-cell {
  font-weight: 500;
}

/* Botones de acción */
.icon-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: none;
  cursor: pointer;
  transition: var(--transition);
  margin: 0 0.25rem;
}

.icon-btn:hover {
  transform: scale(1.1);
}

.edit-btn:hover {
  background-color: rgba(66, 185, 131, 0.1);
}

.delete-btn:hover {
  background-color: rgba(255, 107, 107, 0.1);
}

.icon {
  width: 20px;
  height: 20px;
  fill: currentColor;
}

.edit-btn .icon {
  color: var(--secondary-color);
}

.delete-btn .icon {
  color: var(--accent-color);
}

/* Paginación */
.table-footer {
  display: flex;
  justify-content: center;
  margin-top: 1.5rem;
}

.pagination {
  display: flex;
  align-items: center;
  gap: 1rem;
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

/* Responsive */
@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .form-group.full-width {
    grid-column: span 1;
  }
  
  .table-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .search-input {
    width: 100%;
  }
  
  .desc-col {
    display: none;
  }
}
</style>