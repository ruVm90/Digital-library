# 📚 Proyecto: Biblioteca Digital (Arquitectura en Microservicios)



🎯 Objetivo



Construir una aplicación que gestione una biblioteca digital con arquitectura en microservicios usando PHP nativo y APIs REST.

Cada servicio será independiente, con su propia API, base de datos y responsabilidades claras.



🔹 Microservicios y funcionalidades



## 1\. Users Service (Gestión de usuarios y autenticación)



Endpoints principales:



1. POST /register → Registro de usuarios.
2. POST /login → Generar JWT para autenticación.
3. GET /profile → Ver perfil del usuario autenticado.



Roles:



Lector: puede buscar libros y pedir préstamos.



Bibliotecario: puede gestionar libros.



## 2\. Books Service (Gestión de libros)





Endpoints principales:



1. GET /books → Listar todos los libros.
2. GET /books/{id} → Ver detalles de un libro.
3. POST /books → Crear libro (solo rol bibliotecario).
4. PUT /books/{id} → Editar libro (solo rol bibliotecario).
5. DELETE /books/{id} → Eliminar libro (solo rol bibliotecario).



Funcionalidades:



Manejo de stock (ejemplares disponibles).



Validaciones (no permitir stock negativo).





## 3\. Loans Service (Gestión de préstamos)





Endpoints principales:



1. POST /loans → Crear un préstamo (lector solicita libro).
2. PUT /loans/{id}/return → Marcar devolución.
3. GET /loans/my → Ver préstamos del usuario autenticado.



Reglas de negocio:



No se puede pedir un libro si no hay stock.



Un usuario no puede tener más de X libros prestados (ej: 3).



Al devolver un libro → stock del books-service aumenta.





## 4\. Notifications Service (Notificaciones) – opcional





Endpoints principales:



1. GET /notifications/my → Listar notificaciones del usuario.
2. POST /notifications/send → Crear una notificación (usado por loans-service).



Ejemplos de notificaciones:



“Has solicitado el libro PHP para todos.”



“Tu préstamo vence mañana.”



👉 Puedes simularlo con registros en BD o archivos de log.



🔹 Tecnologías y prácticas



PHP nativo (sin frameworks).



APIs RESTful con JSON.



JWT para autenticación.



Bases de datos separadas (ej: MySQL o SQLite por microservicio).



Documentación de endpoints (Swagger/OpenAPI o README con ejemplos).



Postman/Insomnia para pruebas.



(Opcional) Docker Compose para levantar los servicios.



🔹 Flujo de uso del sistema



Un usuario se registra en users-service.



Se loguea y obtiene un token JWT.



Con ese token puede:



Si es lector: consultar libros y pedir préstamos.



Si es bibliotecario: gestionar libros (crear/editar/eliminar).



Cuando un lector pide un préstamo:



loans-service consulta stock en books-service.



Si hay ejemplares → lo asigna y descuenta stock.



Se genera una notificación en notifications-service.



Cuando devuelve el libro:



loans-service actualiza el préstamo.



books-service aumenta el stock.



✅ Con esto, tendrás un proyecto con arquitectura distribuida, roles, autenticación JWT, validaciones de negocio y comunicación entre servicios.

