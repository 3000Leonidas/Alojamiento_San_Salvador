<template>
  <div class="register-container">
    <div class="register-header">
      <h2>➕ Registrar Nueva Habitación</h2>
      <p class="subtitle">Complete todos los campos para registrar una nueva habitación</p>
    </div>

    <form @submit.prevent="registrar" class="register-form">
      <div class="form-section">
        <h3 class="section-title">📋 Información Básica</h3>
        
        <div class="form-group">
          <label>Número de Habitación</label>
          <input 
            v-model="nueva.numero_habitacion" 
            type="text" 
            class="form-input"
            placeholder="Ej. 101, 202, etc."
            required 
          />
        </div>

        <div class="form-group">
          <label>Tipo de Habitación</label>
          <select v-model="nueva.tipo_habitacion" class="form-select" required>
            <option disabled value="">Seleccione una opción</option>
            <option value="Familiar">Familiar (hasta 5 personas)</option>
            <option value="Matrimonial">Matrimonial (2 personas)</option>
            <option value="Individual">Amigos (hasta 6 personas)</option>
            <option value="Eventos">Eventos (hasta 10 personas)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Precio por Noche (Bs)</label>
          <input 
            v-model.number="nueva.precio_por_noche" 
            type="number" 
            class="form-input"
            min="0"
            step="0.01"
            placeholder="Ej. 250.00"
            required 
          />
        </div>

        <div class="form-group checkbox-group">
          <label class="checkbox-label">
            <input 
              v-model="nueva.esta_disponible" 
              type="checkbox" 
              class="checkbox-input"
            />
            <span class="checkbox-custom"></span>
            Disponible para reservas
          </label>
        </div>
      </div>

      <div class="form-section">
        <h3 class="section-title">🖼️ Imagen de la Habitación</h3>
        
        <div class="upload-area" @click="triggerFileInput" @dragover.prevent @drop="handleDrop">
          <input 
            type="file" 
            ref="fileInput"
            @change="handleImageUpload" 
            accept="image/*"
            class="file-input"
          />
          
          <div v-if="!imagenUrl" class="upload-prompt">
            <span class="upload-icon">📁</span>
            <p>Arrastra una imagen aquí o haz clic para seleccionar</p>
            <small>Formatos aceptados: JPG, PNG (Máx. 5MB)</small>
          </div>
          
          <div v-else class="image-preview">
            <img :src="imagenUrl" alt="Vista previa" class="preview-image" />
            <button type="button" @click.stop="removeImage" class="remove-btn">
              🗑️
            </button>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="submit-btn" :disabled="processing">
          <span v-if="processing">Guardando...</span>
          <span v-else>💾 Guardar Habitación</span>
        </button>
      </div>
    </form>

    <div v-if="mensaje" class="alert-message" :class="messageClass">
      {{ mensaje }}
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const nueva = ref({
  numero_habitacion: '',
  tipo_habitacion: '',
  precio_por_noche: 0,
  esta_disponible: true
});

const imagen = ref(null);
const imagenUrl = ref('');
const mensaje = ref('');
const processing = ref(false);
const fileInput = ref(null);

function triggerFileInput() {
  fileInput.value.click();
}

function handleDrop(e) {
  e.preventDefault();
  const file = e.dataTransfer.files[0];
  if (file && file.type.match('image.*')) {
    processImage(file);
  }
}

function handleImageUpload(event) {
  const file = event.target.files[0];
  if (file) {
    processImage(file);
  }
}

function processImage(file) {
  // Validar tamaño (5MB máximo)
  if (file.size > 5 * 1024 * 1024) {
    mensaje.value = '❌ La imagen no debe exceder los 5MB';
    return;
  }
  
  // Validar tipo
  if (!file.type.match('image.*')) {
    mensaje.value = '❌ Por favor selecciona un archivo de imagen válido';
    return;
  }

  imagen.value = file;
  imagenUrl.value = URL.createObjectURL(file);
  mensaje.value = '';
}

function removeImage() {
  imagen.value = null;
  imagenUrl.value = '';
  fileInput.value.value = '';
}

async function registrar() {
  if (!nueva.value.numero_habitacion || !nueva.value.tipo_habitacion || nueva.value.precio_por_noche <= 0) {
    mensaje.value = '❌ Por favor complete todos los campos requeridos';
    return;
  }

  const formData = new FormData();
  formData.append('numero_habitacion', nueva.value.numero_habitacion);
  formData.append('tipo_habitacion', nueva.value.tipo_habitacion);
  formData.append('precio_por_noche', nueva.value.precio_por_noche);
  formData.append('esta_disponible', nueva.value.esta_disponible ? 1 : 0);
  
  if (imagen.value) {
    formData.append('imagen', imagen.value);
  }

  processing.value = true;
  mensaje.value = '';

  try {
    const res = await fetch('http://localhost:8001/api/habitaciones/registrar.php', {
      method: 'POST',
      body: formData
    });
    
    const data = await res.json();
    
    if (data.success) {
      mensaje.value = '✅ Habitación registrada con éxito';
      resetForm();
    } else {
      throw new Error(data.error || 'Error desconocido al registrar');
    }
  } catch (err) {
    console.error('Error al registrar habitación:', err);
    mensaje.value = `❌ ${err.message}`;
  } finally {
    processing.value = false;
  }
}

function resetForm() {
  nueva.value = { 
    numero_habitacion: '', 
    tipo_habitacion: '', 
    precio_por_noche: 0, 
    esta_disponible: true 
  };
  removeImage();
}
</script>

<style scoped>
.register-container {
  max-width: 800px;
  margin: auto auto;
  padding: 2rem;
}

.register-header {
  text-align: center;
  margin-bottom: 2rem;
}

.register-header h2 {
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #7f8c8d;
  font-size: 1rem;
}

.register-form {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
}

.form-section {
  margin-bottom: 2rem;
}

.section-title {
  font-size: 1.1rem;
  color: #2c3e50;
  margin-top: 0;
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #eee;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
  color: #2c3e50;
  font-weight: 500;
}

.form-input, .form-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  transition: all 0.2s;
}

.form-input:focus, .form-select:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.checkbox-group {
  display: flex;
  align-items: center;
}

.checkbox-label {
  display: flex;
  align-items: center;
  cursor: pointer;
}

.checkbox-input {
  position: absolute;
  opacity: 0;
}

.checkbox-custom {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid #ddd;
  border-radius: 4px;
  margin-right: 0.75rem;
  position: relative;
  transition: all 0.2s;
}

.checkbox-input:checked + .checkbox-custom {
  background-color: #3498db;
  border-color: #3498db;
}

.checkbox-input:checked + .checkbox-custom::after {
  content: '✓';
  position: absolute;
  color: white;
  font-size: 14px;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.upload-area {
  border: 2px dashed #ccc;
  border-radius: 8px;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.upload-area:hover {
  border-color: #3498db;
  background-color: #f8fafb;
}

.file-input {
  display: none;
}

.upload-prompt {
  color: #7f8c8d;
}

.upload-icon {
  font-size: 2rem;
  display: block;
  margin-bottom: 0.5rem;
}

.upload-prompt small {
  display: block;
  margin-top: 0.5rem;
  font-size: 0.8rem;
}

.image-preview {
  position: relative;
  max-width: 100%;
}

.preview-image {
  max-width: 100%;
  max-height: 300px;
  border-radius: 6px;
  display: block;
  margin: 0 auto;
}

.remove-btn {
  position: absolute;
  top: -10px;
  right: -10px;
  background: #e74c3c;
  color: white;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1rem;
}

.form-actions {
  margin-top: 2rem;
  text-align: center;
}

.submit-btn {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background-color: #2980b9;
}

.submit-btn:disabled {
  background-color: #bdc3c7;
  cursor: not-allowed;
}

.alert-message {
  padding: 1rem;
  border-radius: 6px;
  margin-top: 1.5rem;
  text-align: center;
  font-weight: 500;
}

.alert-message.success {
  background-color: #e8f5e9;
  color: #2e7d32;
}

.alert-message.error {
  background-color: #ffebee;
  color: #d32f2f;
}

/* Responsive */
@media (max-width: 768px) {
  .register-container {
    padding: 1.5rem;
  }
}

@media (max-width: 480px) {
  .register-container {
    padding: 1rem;
  }
  
  .register-header h2 {
    font-size: 1.5rem;
  }
}
</style>