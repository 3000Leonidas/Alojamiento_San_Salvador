import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import Login from '../views/Login.vue';
import RoomSearch from '../views/RoomSearch.vue';
import RoomDetail from '../views/RoomDetail.vue';
import AdminRooms from '../views/Admin/AdminRoom.vue';
import AdminDashboard from '../views/Admin/Dashboard.vue';
import AdminUsers from '../views/Admin/Users.vue';
import AdminImages from '../views/Admin/RoomImages.vue';
import Restaurant from '../views/Restaurant.vue';
import Discounts from '../views/Admin/Discounts.vue';
import RegisterRoom from '../views/Admin/RegisterRoom.vue';
import AdminHuespedes from '../views/Admin/AdminHuespedes.vue';

async function verificarRolAdmin(to, from, next) {
  const user = JSON.parse(localStorage.getItem('user'));
  if (!user || !user.nombre_usuario) return next('/login');

  try {
    const res = await fetch('http://localhost:8001/api/auth/verificar_rol.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre_usuario: user.nombre_usuario })
    });
    const data = await res.json();
    if (data.rol === 'admin') next();
    else next('/');
  } catch (err) {
    console.error('Error verificando rol:', err);
    next('/login');
  }
}

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/login', name: 'Login', component: Login },
  { path: '/search', name: 'RoomSearch', component: RoomSearch },
  { path: '/room/:id', name: 'RoomDetail', component: RoomDetail, props: true },
  { path: '/servicios', name: 'Restaurant', component: Restaurant },

  // Admin Panel
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboard,
    beforeEnter: verificarRolAdmin
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsers,
    beforeEnter: verificarRolAdmin
  },
  {
    path: '/admin/huespedes',
    name: 'AdminHuespedes',
    component: AdminHuespedes,
    beforeEnter: verificarRolAdmin
  },
  {
    path: '/admin/rooms',
    name: 'AdminRooms',
    component: AdminRooms,
    beforeEnter: verificarRolAdmin
  },
  {
    path: '/admin/registrar-habitacion',
    name: 'RegisterRoom',
    component: RegisterRoom,
    beforeEnter: verificarRolAdmin
  },
  {
    path: '/admin/images',
    name: 'AdminImages',
    component: AdminImages,
    beforeEnter: verificarRolAdmin
  },
  {
    path: '/admin/discounts',
    name: 'AdminDiscounts',
    component: Discounts,
    beforeEnter: verificarRolAdmin
  },
  {
    path: '/admin/reservas',
    name: 'AdminReservas',
    component: () => import('../views/Admin/AdminReservas.vue'),
    beforeEnter: verificarRolAdmin
  },
  {
    path: '/admin/platos',
    name: 'AdminPlatos',
    component: () => import('../views/Admin/Adminplatos.vue'),
    beforeEnter: verificarRolAdmin
  },
  {
    path: '/admin/reportes-clientes',
    name: 'ReportesClientes',
    component: () => import('../views/Admin/ReportesClientes.vue'),
    beforeEnter: verificarRolAdmin
  },
  {
  path: '/admin/reportes',
  name: 'ReporteGanancias',
  component: () => import('../views/Admin/ReporteGanancias.vue'),
  beforeEnter: verificarRolAdmin
}
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
