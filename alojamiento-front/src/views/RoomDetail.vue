<template>
  <div class="booking-container">
    <div class="booking-header">
      <h2>🏨 Reserva de Habitación</h2>
      <p class="subtitle">Complete sus datos para realizar la reserva</p>
    </div>

    <div class="booking-content">
      <!-- Sección de información de la habitación -->
      <div class="room-preview">
        <div class="room-image-container">
          <img :src="imageUrl" :alt="'Habitación ' + room?.numero_habitacion" class="room-image" />
          <div v-if="!room?.disponible" class="room-status unavailable">
            No disponible
          </div>
          <div v-else class="room-status available">
            Disponible
          </div>
        </div>
        
     <div class="room-details">
  <h3>{{ room?.tipo_habitacion }}</h3>
  <div class="detail-item">
    <span class="detail-label">Número:</span>
    <span class="detail-value">{{ room?.numero_habitacion }}</span>
  </div>
  <div class="detail-item">
    <span class="detail-label">Precio/noche:</span>
    <span class="detail-value">
      <template v-if="hasActiveDiscount">
        <span class="original-price">{{ room?.precio_por_noche }} Bs</span>
        <span class="discounted-price">{{ discountedsPrice }} Bs</span>
        <span class="discount-badge">-{{ room?.porcentaje_descuento }}%</span>
      </template>
      <template v-else>
        {{ room?.precio_por_noche }} Bs
      </template>
    </span>
  </div>
     <div class="detail-item">
    <span class="detail-label">Capacidad:</span>
    <span class="detail-value">{{ getCapacityText(room?.tipo_habitacion) }}</span>
  </div>
    
    <!-- Mostrar información del descuento si existe -->
    <div v-if="hasActiveDiscount" class="discount-info">
    <div class="discount-title">🏷️ Promoción Activa: {{ room?.nombre_descuento }}</div>
    <div class="discount-dates">
      Válido del {{ formatDate(room?.fecha_inicio_descuento) }} al {{ formatDate(room?.fecha_fin_descuento) }}
    </div>
    <div class="discount-savings">
      Ahorras {{ calculateSavings }} Bs por noche
    </div>
  </div>
</div>
      </div>

      <!-- Formulario de reserva -->
      <form @submit.prevent="registrarReserva" class="booking-form">
        <h3 class="form-section-title">📝 Datos del Huésped</h3>
        
        <div class="form-grid">
          <div class="form-group">
            <label>Nombre</label>
            <input v-model="huesped.nombre" type="text" class="form-input" required />
          </div>
          
          <div class="form-group">
            <label>Apellido</label>
            <input v-model="huesped.apellido" type="text" class="form-input" required />
          </div>
          
          <div class="form-group">
            <label>CI / Documento</label>
            <input v-model="huesped.ci" type="text" class="form-input" required />
          </div>
          
          <div class="form-group">
            <label>Correo electrónico</label>
            <input v-model="huesped.email" type="email" class="form-input" required />
          </div>
          
          <div class="form-group">
            <label>Teléfono</label>
            <input v-model="huesped.numero_telefono" type="tel" class="form-input" />
          </div>
        </div>

        <h3 class="form-section-title">📅 Fechas de Estancia</h3>
        
        <div class="form-row">
          <div class="form-group">
            <label>Fecha de ingreso</label>
            <input 
              type="date" 
              v-model="huesped.fecha_entrada" 
              class="form-input" 
              :min="minDate"
              required 
            />
          </div>
          
          <div class="form-group">
            <label>Fecha de salida</label>
            <input 
              type="date" 
              v-model="huesped.fecha_salida" 
              class="form-input" 
              :min="huesped.fecha_entrada || minDate"
              required 
            />
          </div>
        </div>

        <div class="form-group">
          <label>Número de personas</label>
          <input 
            type="number" 
            v-model.number="personas" 
            class="form-input" 
            min="1" 
            :max="getMaxCapacity(room?.tipo_habitacion)"
            required 
          />
          <small v-if="room?.tipo_habitacion" class="capacity-hint">
            Máximo recomendado: {{ getMaxCapacity(room?.tipo_habitacion) }} personas
          </small>
        </div>

        <div class="form-actions">
          <button 
            type="submit" 
            class="submit-btn"
            :disabled="!room?.disponible || processing"
          >
            <span v-if="processing">Procesando...</span>
            <span v-else>Reservar ahora</span>
          </button>
        </div>
      </form>

      <!-- Resumen de reserva -->
       <div v-if="showSummary" class="booking-summary">
        <h3>💰 Resumen de Pago</h3>
        <div class="summary-item">
          <span>Noches:</span>
          <span>{{ nightsCount }}</span>
        </div>
        <div class="summary-item">
          <span>Precio por noche:</span>
          <span>
            <template v-if="room?.porcentaje_descuento">
              {{ calcularDescuento(room?.precio_por_noche, room?.porcentaje_descuento) }} Bs
            </template>
            <template v-else>
              {{ room?.precio_por_noche }} Bs
            </template>
          </span>
        </div>
        <div class="summary-item total">
          <span>Total a pagar:</span>
          <span>{{ totalAmount }} Bs</span>
        </div>
      </div>

      <!-- Sección de pago QR -->
      <div v-if="pagoQR" class="payment-section">
        <h3>💳 Pago con QR</h3>
        <p>Complete el pago escaneando el código QR con su aplicación bancaria</p>
        
        <div class="qr-container">
          <img :src="qrUrl" alt="Código QR para pago" class="qr-image" />
        </div>
        
        <div class="payment-instructions">
          <p><strong>Instrucciones:</strong></p>
          <ol>
            <li>Abra su aplicación de banca móvil</li>
            <li>Seleccione la opción de pagar con QR</li>
            <li>Escanee el código QR mostrado</li>
            <li>Confirme el pago de {{ totalAmount }} Bs</li>
          </ol>
        </div>
      </div>

      <!-- Mensajes de estado -->
      <div v-if="mensaje" class="alert-message" :class="messageClass">
        {{ mensaje }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const room = ref(null);
const personas = ref(1);
const mensaje = ref('');
const pagoQR = ref(false);
const qrUrl = ref('');
const processing = ref(false);
const showSummary = ref(false);

const huesped = ref({
  nombre: '',
  apellido: '',
  ci: '',
  email: '',
  numero_telefono: '',
  fecha_entrada: '',
  fecha_salida: ''
});



// Fecha mínima (hoy)
const minDate = computed(() => {
  const today = new Date();
  return today.toISOString().split('T')[0];
});

// Calcular noches y total
const nightsCount = computed(() => {
  if (!huesped.value.fecha_entrada || !huesped.value.fecha_salida) return 0;
  
  const fecha1 = new Date(huesped.value.fecha_entrada);
  const fecha2 = new Date(huesped.value.fecha_salida);
  return Math.ceil((fecha2 - fecha1) / (1000 * 60 * 60 * 24));
});

const totalAmount = computed(() => {
  return nightsCount.value * parseFloat(room.value?.precio_por_noche || 0);
});

// Clase para mensajes de error/éxito
const messageClass = computed(() => {
  return mensaje.value.startsWith('✅') ? 'success' : 'error';
});

// Capacidad según tipo de habitación
function getMaxCapacity(roomType) {
  const capacities = {
    'Matrimonial': 2,
    'Familiar': 5,
    'Amigos': 4,
    'Eventos': 10,

  };
  return capacities[roomType] || 2;
}

// Computed properties para descuentos
const hasActiveDiscount = computed(() => {
  return room.value?.tiene_descuento_activo && room.value?.descuento_id;
});

const discountedsPrice = computed(() => {
  if (!hasActiveDiscount.value) return room.value?.precio_por_noche;
  return room.value?.precio_con_descuento;
});

const calculateSavings = computed(() => {
  if (!hasActiveDiscount.value) return 0;
  return (room.value.precio_por_noche - room.value.precio_con_descuento).toFixed(2);
});

function getCapacityText(roomType) {
  const max = getMaxCapacity(roomType);
  return max === 1 ? '1 persona' : `${max} personas`;
}

// Función para formatear fechas
const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

// Computed para verificar si hay descuento
const hasRoomDiscount = computed(() => {
  return room.value?.porcentaje_descuento > 0 && 
         isDiscountActive(room.value?.fecha_inicio_descuento, room.value?.fecha_fin_descuento);
});

const discountedPrice = computed(() => {
  if (!hasRoomDiscount.value) return room.value?.precio_por_noche;
  const discount = room.value.porcentaje_descuento / 100;
  return (room.value.precio_por_noche * (1 - discount)).toFixed(2);
});

// Función para verificar si el descuento está activo
const isDiscountActive = (startDate, endDate) => {
  if (!startDate || !endDate) return false;
  const today = new Date();
  const start = new Date(startDate);
  const end = new Date(endDate);
  return today >= start && today <= end;
};


// Cargar datos de la habitación
onMounted(async () => {
  try {
    const habitacionId = parseInt(route.params.id);
    const res = await fetch(`http://localhost:8001/api/habitaciones/detalle.php?id=${habitacionId}`);
    const data = await res.json();
    
    if (data.success) {
      room.value = data.habitacion;
    } else {
      throw new Error(data.error || 'Error al cargar la habitación');
    }
  } catch (error) {
    console.error('Error al cargar la habitación', error);
    mensaje.value = '❌ No se pudo cargar la información de la habitación.';
  }
});

// Mostrar resumen cuando hay fechas válidas
watch([() => huesped.value.fecha_entrada, () => huesped.value.fecha_salida], () => {
  showSummary.value = !!huesped.value.fecha_entrada && !!huesped.value.fecha_salida;
});

// URL de la imagen
const imageUrl = computed(() => {
  return room.value?.imagen
    ? `http://localhost:8001/${room.value.imagen}`
    : `http://localhost:8001/uploads/imagen-default.jpg`;
});

// Registrar reserva
async function registrarReserva() {
  try {
    mensaje.value = '';
    pagoQR.value = false;
    processing.value = true;

    // Validaciones adicionales
    if (personas.value > getMaxCapacity(room.value?.tipo_habitacion)) {
      throw new Error(`El número de personas excede la capacidad recomendada para esta habitación.`);
    }

    const reservaBody = {
      nombre: huesped.value.nombre,
      apellido: huesped.value.apellido,
      ci: huesped.value.ci,
      email: huesped.value.email,
      numero_telefono: huesped.value.numero_telefono,
      id_habitacion: room.value.id,
      fecha_entrada: huesped.value.fecha_entrada,
      fecha_salida: huesped.value.fecha_salida,
      tipo_reserva: personas.value > 4 ? 'grupo' : 'normal'
    };

    const res = await fetch('http://localhost:8001/api/reservas/registrar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(reservaBody)
    });

    const data = await res.json();
    if (!data.success || !data.id_reserva) {
      throw new Error(data.error || 'Error al registrar reserva');
    }

    // Registrar pago
    const pagoRes = await fetch('http://localhost:8001/api/pagos/registrar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id_reserva: data.id_reserva,
        metodo_pago: 'Simulado',
          monto: totalAmount.value
        })
    });

    const pagoData = await pagoRes.json();
    if (!pagoData.success) {
      throw new Error(pagoData.error || 'Error al registrar pago');
    }

    // Mostrar QR de pago
    qrUrl.value = `http://localhost:8001/${pagoData.url_codigo_qr}`;
    mensaje.value = '✅ Reserva registrada correctamente. Complete el pago escaneando el código QR.';
    pagoQR.value = true;
  } catch (error) {
    console.error(error);
    mensaje.value = `❌ Error: ${error.message}`;
  } finally {
    processing.value = false;
  }
}
</script>

<style scoped>
.booking-container {
  /* max-width: 1000px; */
  width: 50%;
  margin: auto;
  padding: 2rem;
}

.booking-header {
  text-align: center;
  margin-bottom: 2rem;
}

.booking-header h2 {
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #7f8c8d;
  font-size: 1rem;
}

.booking-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.room-preview {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.room-image-container {
  position: relative;
  height: 300px;
  overflow: hidden;
}

.room-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.room-status {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.25rem 0.75rem;
  border-radius: 50px;
  font-size: 0.85rem;
  font-weight: 500;
  color: white;
}

.room-status.available {
  background-color: #27ae60;
}

.room-status.unavailable {
  background-color: #e74c3c;
}

.room-details {
  padding: 2rem;
}

.room-details h3 {
  font-size: 1.25rem;
  color: #2c3e50;
  margin-top: 0;
  margin-bottom: 1rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.detail-label {
  font-weight: 500;
  color: #2c3e50;
}

.detail-value {
  color: #7f8c8d;
}

.booking-form {
  width: 100%;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  padding: 1rem 3rem;
}

.form-section-title {
  font-size: 1.1rem;
  color: #2c3e50;
  margin-top: 0;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #eee;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2.5rem;
  margin-bottom: 1rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2.5rem;
  margin-bottom: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
  color: #2c3e50;
  font-weight: 500;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.capacity-hint {
  display: block;
  margin-top: 0.25rem;
  font-size: 0.8rem;
  color: #7f8c8d;
}

.form-actions {
  margin-top: 1.5rem;
}

.submit-btn {
  width: 100%;
  background-color: #3498db;
  color: white;
  padding: 0.75rem;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background-color: #2980b9;
}

.submit-btn:disabled {
  background-color: #bdc3c7;
  cursor: not-allowed;
}

.booking-summary {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 1.5rem;
  margin-top: 1rem;
  grid-column: 1;
}

.booking-summary h3 {
  font-size: 1.1rem;
  color: #2c3e50;
  margin-top: 0;
  margin-bottom: 1rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #eee;
}

.summary-item.total {
  font-weight: bold;
  color: #2c3e50;
  border-bottom: none;
  margin-top: 1rem;
  padding-top: 0.75rem;
  border-top: 1px solid #ddd;
}

.payment-section {
  grid-column: 1 / -1;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
  margin-top: 1rem;
  text-align: center;
}

.payment-section h3 {
  font-size: 1.25rem;
  color: #2c3e50;
  margin-top: 0;
  margin-bottom: 1rem;
}

.payment-section p {
  color: #7f8c8d;
  margin-bottom: 1.5rem;
}

.qr-container {
  max-width: 300px;
  margin: 0 auto 1.5rem;
  padding: 1rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.qr-image {
  width: 100%;
  height: auto;
}

.payment-instructions {
  text-align: left;
  max-width: 500px;
  margin: 0 auto;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.payment-instructions ol {
  padding-left: 1.5rem;
  margin: 0.5rem 0 0;
}

.alert-message {
  grid-column: 1 / -1;
  padding: 1rem;
  border-radius: 6px;
  margin-top: 1rem;
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
@media (max-width: 900px) {
  .booking-content {
    grid-template-columns: 1fr;
  }
  
  .booking-summary {
    grid-column: auto;
  }
}

@media (max-width: 600px) {
  .booking-container {
    padding: 1.5rem;
  }
  
  .form-grid,
  .form-row {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .booking-container {
    padding: 1rem;
  }
  
  .booking-header h2 {
    font-size: 1.5rem;
  }
}
.original-price {
  text-decoration: line-through;
  color: #95a5a6;
  margin-right: 0.5rem;
}

.discounted-price {
  color: #e74c3c;
  font-weight: bold;
  margin-right: 0.5rem;
}

.discount-badge {
  display: inline-block;
  padding: 0.2rem 0.5rem;
  background-color: #2ecc71;
  color: white;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: bold;
}

.discount-info {
  margin-top: 1rem;
  padding: 0.75rem;
  background-color: #f8f9fa;
  border-radius: 6px;
  border-left: 4px solid #3498db;
}

.discount-title {
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 0.25rem;
}

.discount-dates {
  font-size: 0.85rem;
  color: #7f8c8d;
  margin-bottom: 0.25rem;
}

.discount-savings {
  font-size: 0.9rem;
  color: #27ae60;
  font-weight: 500;
}
</style>