// src/stores/auth.js (o similar)
import { reactive } from 'vue';

export const auth = reactive({
  user: JSON.parse(localStorage.getItem('user')) || null,

  get isLoggedIn() {
    return !!this.user;
  },

  get isAdmin() {
    return this.user?.rol === 'admin';
  },

  login(userData) {
    this.user = userData;
    localStorage.setItem('user', JSON.stringify(userData));
  },

  logout() {
    this.user = null;
    localStorage.removeItem('user');
  }
});
