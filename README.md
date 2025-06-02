# 🏨 Alojamiento San Salvador - Sistema de Gestión Hotelera

![Captura del Sistema](https://github.com/user-attachments/assets/6f9d26ab-1e51-4b0f-8d61-a4685222d870)

Sistema integral para la gestión de reservaciones, habitaciones y servicios complementarios en establecimientos hoteleros.

<div align="center">
  <img src="https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vuedotjs&logoColor=4FC08D" alt="Vue">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind">
</div>

## ✨ Características Principales

- 🛎️ **Gestión completa** de reservaciones y habitaciones
- 🍽️ **Módulo integrado** para restaurante y servicios
- 👨‍💻 **Panel administrativo** con métricas clave
- 🔒 **Autenticación segura** por roles de usuario
- 📱 **Diseño responsive** para cualquier dispositivo
- 📊 **Generación de reportes** y estadísticas

## 🛠️ Instalación

### Requisitos Previos

| Componente       | Versión    |
|------------------|------------|
| PHP              | 7.4+       |
| MySQL            | 5.7+       |
| Node.js          | 16+        |
| npm              | 8+         |

### Backend (`alojamiento-back`)

```bash
# Entrar al directorio
cd alojamiento-back

# Configurar base de datos (importar archivo SQL)
mysql -u usuario -p database < database/dump.sql

# Iniciar servidor
php -S localhost:8001

bash
npm run dev
🌐 Acceso al Sistema
Frontend: http://localhost:3000

Backend (API): http://localhost:8001

Credenciales de prueba:

Usuario: admin

Contraseña: admin

📂 Estructura del Proyecto
alojamiento-san-salvador/
├── alojamiento-back/          # Backend PHP
│   ├── api/                   # Endpoints de la API
│   ├── config/                # Configuraciones
│   ├── database/              # Esquemas y migraciones
│   └── ...
├── alojamiento-front/         # Frontend Vue.js
│   ├── public/                # Assets públicos
│   ├── src/                   # Código fuente
│   │   ├── assets/            # Imágenes, estilos
│   │   ├── components/        # Componentes Vue
│   │   ├── router/            # Configuración de rutas
│   │   ├── store/             # Gestión de estado
│   │   └── ...
│   └── ...
└── README.md                  # Este archivo
🤝 Contribución
Las contribuciones son bienvenidas. Sigue estos pasos:

Haz un fork del proyecto

Crea tu rama (git checkout -b feature/nueva-funcionalidad)

Haz commit de tus cambios (git commit -m 'Añade nueva funcionalidad')

Haz push a la rama (git push origin feature/nueva-funcionalidad)

Abre un Pull Request

📄 Licencia
Este proyecto está bajo la licencia MIT. Consulta el archivo LICENSE para más información.

💻 Desarrollado con ❤️ por [Steven André Lecoña Saravia]
