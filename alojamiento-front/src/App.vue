<template>
  <div id="app">
    <Header />
    <router-view />
    <Footer />
  </div>
</template>

<script setup>
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';

</script>

<style>
/* Global layout styles opcionales */
#app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  width: 98%;
}
header {
  background-color: #2c3e50;
  color: white;
  padding: 1rem;
}
footer {
  background-color: #2c3e50;
  color: white;
  padding: 1rem;
  text-align: center;
  margin-top: auto;
}
h1 {
  color: #2c3e50;
  text-align: center;
  margin: 2rem 0;
}     
</style>
