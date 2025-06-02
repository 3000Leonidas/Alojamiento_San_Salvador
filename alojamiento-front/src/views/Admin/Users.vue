<template>
  <div class="admin-users full-width">
    <div class="admin-header">
      <h2><i class="fas fa-users"></i> Gestión de Usuarios</h2>
      <p class="admin-subtitle">Administra los usuarios del sistema</p>
    </div>

    <div class="admin-content">
      <div class="card add-user-card">
        <h3><i class="fas fa-user-plus"></i> Registrar Nuevo Usuario</h3>
        <form @submit.prevent="registrarUsuario" class="user-form">
          <div class="form-group">
            <label>Nombre de usuario</label>
            <input 
              type="text" 
              v-model="nuevoUsuario.nombre_usuario" 
              placeholder="Ingrese nombre de usuario" 
              required 
              class="form-input"
            />
          </div>
          
          <div class="form-group">
            <label>Contraseña</label>
            <input 
              type="password" 
              v-model="nuevoUsuario.password" 
              placeholder="Ingrese contraseña" 
              required 
              class="form-input"
            />
          </div>
          
          <div class="form-group">
            <label>Rol</label>
            <select v-model="nuevoUsuario.rol" class="form-select">
              <option value="cliente">Cliente</option>
              <option value="admin">Administrador</option>
            </select>
          </div>
          
          <button type="submit" class="submit-btn">
            <i class="fas fa-save"></i> Registrar Usuario
          </button>
        </form>
      </div>

      <div class="card user-list-card">
        <h3><i class="fas fa-list"></i> Usuarios Registrados</h3>
        
        <div class="table-responsive">
          <table class="user-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td>{{ user.id }}</td>
                <td>{{ user.nombre_usuario }}</td>
                <td>
                  <span :class="['role-badge', user.rol === 'admin' ? 'admin-badge' : 'client-badge']">
                    {{ user.rol === 'admin' ? 'Administrador' : 'Cliente' }}
                  </span>
                </td>
                <td>{{ formatDate(user.creado_en) }}</td>
                <td>
                  <button @click="deleteUser(user.id)" class="delete-btn">
                    <i class="fas fa-trash-alt"></i> Eliminar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faUsers, faUserPlus, faList, faSave, faTrashAlt } from '@fortawesome/free-solid-svg-icons';

library.add(faUsers, faUserPlus, faList, faSave, faTrashAlt);

const users = ref([]);
const nuevoUsuario = ref({ nombre_usuario: '', password: '', rol: 'cliente' });

function formatDate(dateString) {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
}

async function fetchUsers() {
  const res = await fetch('http://localhost:8001/api/usuarios/listar.php');
  const data = await res.json();
  if (data.success) users.value = data.usuarios;
}

async function registrarUsuario() {
  const res = await fetch('http://localhost:8001/api/auth/register.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(nuevoUsuario.value)
  });
  const data = await res.json();
  if (data.success) {
    alert('Usuario registrado con éxito');
    nuevoUsuario.value = { nombre_usuario: '', password: '', rol: 'cliente' };
    fetchUsers();
  } else {
    alert(data.error || 'Error al registrar usuario');
  }
}

async function deleteUser(id) {
  if (!confirm('¿Está seguro que desea eliminar este usuario?')) return;
  const res = await fetch(`http://localhost:8001/api/usuarios/eliminar.php?id=${id}`, {
    method: 'DELETE'
  });
  const data = await res.json();
  if (data.success) {
    users.value = users.value.filter(u => u.id !== id);
    alert('Usuario eliminado correctamente');
  } else {
    alert(data.error || 'Error al eliminar usuario');
  }
}

onMounted(fetchUsers);
</script>

<style scoped>
.admin-users.full-width {
  padding: 2rem;
  max-width: 100%;
  width: 100%;
  margin: auto auto;
  font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

.admin-header {
  margin-bottom: 2rem;
  text-align: center;
}

.admin-header h2 {
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.admin-subtitle {
  color: #7f8c8d;
  font-size: 1rem;
}

.admin-content {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 2rem;
}

.card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  padding: 2rem;
  margin-bottom: 2rem;
}

.card h3 {
  font-size: 1.25rem;
  color: #34495e;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.user-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
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

.form-input, .form-select {
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

.submit-btn {
  background-color: #3498db;
  color: white;
  padding: 0.75rem;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.submit-btn:hover {
  background-color: #2980b9;
}

.table-responsive {
  overflow-x: auto;
}

.user-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.95rem;
}

.user-table th {
  background-color: #f8f9fa;
  color: #2c3e50;
  font-weight: 600;
  padding: 1rem;
  text-align: left;
  border-bottom: 2px solid #eee;
}

.user-table td {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  vertical-align: middle;
}

.user-table tr:last-child td {
  border-bottom: none;
}

.user-table tr:hover {
  background-color: #f8f9fa;
}

.role-badge {
  padding: 0.35rem 0.75rem;
  border-radius: 50px;
  font-size: 0.85rem;
  font-weight: 500;
}

.admin-badge {
  background-color: #e3f2fd;
  color: #1976d2;
}

.client-badge {
  background-color: #e8f5e9;
  color: #388e3c;
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
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.delete-btn:hover {
  background-color: #ef9a9a;
  color: #b71c1c;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .admin-content {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .admin-users {
    padding: 1.5rem;
  }
  
  .card {
    padding: 1.25rem;
  }
  
  .user-table th, 
  .user-table td {
    padding: 0.75rem;
  }
}

@media (max-width: 480px) {
  .admin-users {
    padding: 1rem;
  }
  
  .admin-header h2 {
    font-size: 1.5rem;
  }
  
  .form-input, 
  .form-select,
  .submit-btn {
    padding: 0.65rem;
  }
}
</style>