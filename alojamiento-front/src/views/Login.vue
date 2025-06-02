<template>
  <div class="login-form">
    <h2>Iniciar Sesión</h2>
    <form @submit.prevent="handleLogin">
      <label>
        Nombre de usuario:
        <input type="text" v-model="nombre_usuario" required />
      </label>

      <label>
        Contraseña:
        <input type="password" v-model="password" required />
      </label>

      <button type="submit">Ingresar</button>
    </form>

    <p v-if="error" class="error">Usuario o contraseña inválidos.</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { auth } from '../store/auth.js';

const router = useRouter();

const nombre_usuario = ref('');
const password = ref('');
const error = ref(false);

async function handleLogin() {
  try {
    const res = await fetch('http://localhost:8001/api/auth/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre_usuario: nombre_usuario.value, password: password.value })
    });

    const data = await res.json();

    if (res.ok && data.success) {
      auth.login(data.user);
      error.value = false;
      router.push('/');
    } else {
      error.value = true;
    }
  } catch (err) {
    console.error('Error de red o servidor', err);
    error.value = true;
  }
}
</script>

<style scoped>
.login-form {
  max-width: 400px;
  margin: auto;
  padding: 2rem;
}

form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

label {
  display: flex;
  flex-direction: column;
  font-weight: bold;
  color: var(--card-text);
}

input {
  padding: 0.5rem;
  font-size: 1rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  margin-top: 0.25rem;
}

.error {
  margin-top: 1rem;
  color: #ff6b6b;
  font-weight: bold;
  text-align: center;
}
</style>
