<?php
function neema_registrar_cadenas() {
    if (!function_exists('pll_register_string')) return;

    // General
    pll_register_string('Volver a ', 'Volver a ', 'General');
    pll_register_string('Home', 'Home', 'General');
    pll_register_string('Resiliencia Alimentaria y Nutricional', 'Resiliencia Alimentaria y Nutricional', 'General');
    pll_register_string('Resultados aproximados', 'Resultados aproximados', 'General');
    pll_register_string('No se especificó ningún criterio de búsqueda.', 'No se especificó ningún criterio de búsqueda.', 'General');

    // Header
    pll_register_string('Iniciar Sesión', 'Iniciar Sesión', 'Header');
    pll_register_string('Guía Introductoria', 'Guía Introductoria', 'Header');
    pll_register_string('Programas de RAN', 'Programas de RAN', 'Header');
    pll_register_string('Resiliencia Alimentaria y Nutricional', 'Resiliencia Alimentaria y Nutricional', 'Header');
    pll_register_string('Servicios de apoyo', 'Servicios de apoyo', 'Header');

    // Footer
    pll_register_string('Imágenes de la web diseñadas por', 'Imágenes de la web diseñadas por', 'Footer');

    // Front page
    pll_register_string('hero-title', '¡Bienvenido<br> a la Toolkit de<br> NEEMA!', 'Home');
    pll_register_string('Qué es la caja de herramientas', 'Qué es la caja de herramientas', 'Home');
    pll_register_string('home_info_paragraph1', '<b>La Caja de Herramientas</b> o <b>Toolkit</b> es un gestor de contenidos diseñado para consultar y crear formaciones, proyectos, informes, normativas y otros recursos de Seguridad Alimentaria. Reúne documentos, bases de datos, estadísticas, casos prácticos y más, organizados de forma intuitiva para facilitar el trabajo de los usuarios y aportar eficiencia e innovación.', 'Home');
    pll_register_string('home_info_paragraph2', 'La <b>Toolkit</b>, parte del Proyecto NEEMA de la Universidad de Sevilla financiado por ERASMUS+, busca adaptar el Pacto Verde Europeo y la Estrategia “De la Granja a la Mesa” al Sahel y África Occidental, promoviendo resiliencia alimentaria y nutricional frente a sequías, conflictos y cambio climático.', 'Home');
    pll_register_string('¿Quieres saber más sobre el proyecto NEEMA?', '¿Quieres saber más sobre el proyecto NEEMA?', 'Home');
    pll_register_string('Visita la web de NEEMA', 'Visita la web de NEEMA', 'Home');
    pll_register_string('Quiénes somos', 'Quiénes somos', 'Home');
    pll_register_string('La Toolkit de NEEMA reúne a un equipo multidisciplinar de profesionales de diversas facultades de la Universidad de Sevilla.', 'La Toolkit de NEEMA reúne a un equipo multidisciplinar de profesionales de diversas facultades de la Universidad de Sevilla.', 'Home');
    pll_register_string('¿Quieres saber más? Pincha aquí', '¿Quieres saber más? Pincha aquí', 'Home');
    pll_register_string('Además del equipo de la Caja de Herramientas, NEEMA colabora con numerosas universidades e instituciones de Europa y África. Puedes conocerlas en ', 'Además del equipo de la Caja de Herramientas, NEEMA colabora con numerosas universidades e instituciones de Europa y África. Puedes conocerlas en ', 'Home');
    pll_register_string('Equipo NEEMA', 'Equipo NEEMA', 'Home');

    // Página 404
    pll_register_string('Parece que esta página no existe.', 'Parece que esta página no existe.', '404 Page');
    pll_register_string('Es posible que el enlace no exista o que la página haya sido movida.', 'Es posible que el enlace no exista o que la página haya sido movida.', '404 Page');
    pll_register_string('Volver al inicio', 'Volver al inicio', '404 Page');

    // Pagina de Programas RAN
    pll_register_string('Resumen de los programas de Resiliencia Alimentaria y Nutricional', 'Resumen de los programas de Resiliencia Alimentaria y Nutricional', 'Programas RAN');
    pll_register_string('Como parte del Proyecto NEEMA, liderado por la Universidad de Sevilla ...', 'Como parte del Proyecto NEEMA, liderado por la Universidad de Sevilla ...', 'Programas RAN');
    pll_register_string('Buscador', 'Buscador', 'Programas RAN');
    pll_register_string('Módulos', 'Módulos', 'Programas RAN');

    // Página de Módulo
    pll_register_string('Objetivo', 'Objetivo', 'Módulo Page');
    pll_register_string('Competencias a adquirir', 'Competencias a adquirir', 'Módulo Page');
    pll_register_string('Buscador', 'Buscador', 'Módulo Page');
    pll_register_string('Recursos', 'Recursos', 'Módulo Page');
    pll_register_string('Contextuales', 'Contextuales', 'Módulo Page');
    pll_register_string('Formativos', 'Formativos', 'Módulo Page');
    pll_register_string('Metodológicos', 'Metodológicos', 'Módulo Page');
    pll_register_string('Procedimentales', 'Procedimentales', 'Módulo Page');
    pll_register_string('No se han encontrado resultados', 'No se han encontrado resultados', 'Módulo Page');

    // Buscador de Recursos
    pll_register_string('Buscar por palabras o frases...', 'Buscar por palabras o frases...', 'Buscador');
    pll_register_string('Países', 'Países', 'Buscador');
    pll_register_string('Tipo de recurso', 'Tipo de recurso', 'Buscador');
    pll_register_string('Temáticas', 'Temáticas', 'Buscador');
    pll_register_string('Regiones', 'Regiones', 'Buscador');
    pll_register_string('Buscar', 'Buscar', 'Buscador');
    pll_register_string('Limpiar filtros', 'Limpiar filtros', 'Buscador');
    pll_register_string('Limpiar Búsqueda', 'Limpiar Búsqueda', 'Buscador');
    pll_register_string('Resultados de búsqueda', 'Resultados de búsqueda', 'Buscador');
    pll_register_string('Resultados de búsqueda en este módulo', 'Resultados de búsqueda en este módulo', 'Buscador');
    pll_register_string('Cargando...', 'Cargando...', 'Buscador');
    pll_register_string('Categorías', 'Categorías', 'Buscador');
   
    // Página de Recurso
    pll_register_string('Acceso restringido', 'Acceso restringido', 'Recurso Page');
    pll_register_string('Debes iniciar sesión para descargar este recurso.', 'Debes iniciar sesión para descargar este recurso.', 'Recurso Page');
    pll_register_string('Iniciar sesión', 'Iniciar sesión', 'Recurso Page');
    pll_register_string('Cerrar', 'Cerrar', 'Recurso Page');
    pll_register_string('Cualquier país', 'Cualquier país', 'Recurso Page');
    pll_register_string('Cualquier región', 'Cualquier región', 'Recurso Page');
    pll_register_string('Sin temáticas', 'Sin temáticas', 'Recurso Page');
    pll_register_string('Detalles de este recurso:', 'Detalles de este recurso:', 'Recurso Page');

    // Página de Login
    pll_register_string('Iniciar sesión', 'Iniciar sesión', 'Login Page');
    pll_register_string('Usuario o contraseña incorrectos.', 'Usuario o contraseña incorrectos.', 'Login Page');
    pll_register_string('Por favor, introduce usuario y contraseña.', 'Por favor, introduce usuario y contraseña.', 'Login Page');
    pll_register_string('Has cerrado sesión correctamente.', 'Has cerrado sesión correctamente.', 'Login Page');
    pll_register_string('Usuario o correo electrónico', 'Usuario o correo electrónico', 'Login Page');
    pll_register_string('Recuérdame', 'Recuérdame', 'Login Page');
    pll_register_string('Acceder', 'Acceder', 'Login Page');
    pll_register_string('¿No tienes cuenta?', '¿No tienes cuenta?', 'Login Page');
    pll_register_string('Regístrate aquí', 'Regístrate aquí', 'Login Page');
    pll_register_string('¡Cuenta activada con éxito! Ya puedes iniciar sesión.', '¡Cuenta activada con éxito! Ya puedes iniciar sesión.', 'Login Page');
    pll_register_string('Tu cuenta aún no ha sido activada. Revisa tu correo electrónico.', 'Tu cuenta aún no ha sido activada. Revisa tu correo electrónico.', 'Login Page');
    pll_register_string('¿Olvidaste tu contraseña?', '¿Olvidaste tu contraseña?', 'Login Page');

    //Página de Registro
    pll_register_string('Registro de usuario', 'Registro de usuario', 'Register Page');
    pll_register_string('Nombre y apellidos', 'Nombre y apellidos', 'Register Page');
    pll_register_string('Nombre de usuario', 'Nombre de usuario', 'Register Page');
    pll_register_string('Correo electrónico', 'Correo electrónico', 'Register Page');
    pll_register_string('Entidad proveniente', 'Entidad proveniente', 'Register Page');
    pll_register_string('Selecciona una entidad', 'Selecciona una entidad', 'Register Page');
    pll_register_string('Rol', 'Rol', 'Register Page');
    pll_register_string('Selecciona tu rol', 'Selecciona tu rol', 'Register Page');
    pll_register_string('Profesor', 'Profesor', 'Register Page');
    pll_register_string('Estudiante', 'Estudiante', 'Register Page');
    pll_register_string('Tomador de decisiones', 'Tomador de decisiones', 'Register Page');
    pll_register_string('Equipo de soporte', 'Equipo de soporte', 'Register Page');
    pll_register_string('Organización', 'Organización', 'Register Page');
    pll_register_string('Otra', 'Otra', 'Register Page');
    pll_register_string('Contraseña', 'Contraseña', 'Register Page');
    pll_register_string('Confirmar contraseña', 'Confirmar contraseña', 'Register Page');
    pll_register_string('Registrarse', 'Registrarse', 'Register Page');
    pll_register_string('¿Ya tienes una cuenta?', '¿Ya tienes una cuenta?', 'Register Page');
    pll_register_string('Inicia sesión aquí', 'Inicia sesión aquí', 'Register Page');
    pll_register_string('Por favor, introduce un correo electrónico válido.', 'Por favor, introduce un correo electrónico válido.', 'Register Page');
    pll_register_string('Por favor, completa todos los campos.', 'Por favor, completa todos los campos.', 'Register Page');
    pll_register_string('El nombre de usuario ya existe.', 'El nombre de usuario ya existe.', 'Register Page');
    pll_register_string('El correo electrónico ya está registrado.', 'El correo electrónico ya está registrado.', 'Register Page');
    pll_register_string('Las contraseñas no coinciden.', 'Las contraseñas no coinciden.', 'Register Page');
    pll_register_string('La contraseña debe tener al menos 8 caracteres e incluir mayúsculas, minúsculas, números y símbolos.', 'La contraseña debe tener al menos 8 caracteres e incluir mayúsculas, minúsculas, números y símbolos.', 'Register Page');
    pll_register_string('Error al crear la cuenta. Inténtalo de nuevo.', 'Error al crear la cuenta. Inténtalo de nuevo.', 'Register Page');
    pll_register_string('El código de activación no es válido o ha expirado.', 'El código de activación no es válido o ha expirado.', 'Register Page');
    pll_register_string('Otro', 'Otro', 'Register Page');
    
    // Errores de reCAPTCHA
    pll_register_string('Error de verificación. Por favor, recarga la página e intenta de nuevo.', 'Error de verificación. Por favor, recarga la página e intenta de nuevo.', 'Register Page');
    pll_register_string('Verificación de seguridad fallida. Si eres humano, inténtalo de nuevo.', 'Verificación de seguridad fallida. Si eres humano, inténtalo de nuevo.', 'Register Page');
    pll_register_string('Error al verificar la seguridad. Por favor, inténtalo de nuevo.', 'Error al verificar la seguridad. Por favor, inténtalo de nuevo.', 'Register Page');
    
    //Modal de registro exitoso
    pll_register_string('¡Registro exitoso!', '¡Registro exitoso!', 'Register Page');
    pll_register_string('Te hemos enviado un correo electrónico con un enlace para activar tu cuenta.', 'Te hemos enviado un correo electrónico con un enlace para activar tu cuenta.', 'Register Page');
    pll_register_string('Por favor, revisa tu bandeja de entrada (y la carpeta de spam) y haz clic en el enlace de activación.', 'Por favor, revisa tu bandeja de entrada (y la carpeta de spam) y haz clic en el enlace de activación.', 'Register Page');
    pll_register_string('Entendido', 'Entendido', 'Register Page');
    
    // Email de activación
    pll_register_string('Activa tu cuenta', 'Activa tu cuenta', 'Activation Email');
    pll_register_string('¡Gracias por registrarte en NEEMA!', '¡Gracias por registrarte en NEEMA!', 'Activation Email');
    pll_register_string('Para activar tu cuenta, por favor haz clic en el botón de abajo:', 'Para activar tu cuenta, por favor haz clic en el botón de abajo:', 'Activation Email');
    pll_register_string('Activar mi cuenta', 'Activar mi cuenta', 'Activation Email');
    pll_register_string('¿Necesitas ayuda?', '¿Necesitas ayuda?', 'Activation Email');
    pll_register_string('Contáctanos', 'Contáctanos', 'Activation Email');
    pll_register_string('Gracias', 'Gracias', 'Activation Email');
    pll_register_string('El equipo de NEEMA', 'El equipo de NEEMA', 'Activation Email');

    //Página de recuperar contraseña
    pll_register_string('Recuperar contraseña', 'Recuperar contraseña', 'Forgot Password Page');
    pll_register_string('Si el correo electrónico está registrado, recibirás un enlace para restablecer tu contraseña.', 'Si el correo electrónico está registrado, recibirás un enlace para restablecer tu contraseña.', 'Forgot Password Page');
    pll_register_string('Volver al inicio de sesión', 'Volver al inicio de sesión', 'Forgot Password Page');
    pll_register_string('Por favor, introduce tu correo electrónico.', 'Por favor, introduce tu correo electrónico.', 'Forgot Password Page');
    pll_register_string('Error al enviar el correo. Inténtalo de nuevo.', 'Error al enviar el correo. Inténtalo de nuevo.', 'Forgot Password Page');
    pll_register_string('Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.', 'Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.', 'Forgot Password Page');
    pll_register_string('Enviar enlace', 'Enviar enlace', 'Forgot Password Page');

    // Email de restablecimiento de contraseña
    pll_register_string('Recuperación de contraseña', 'Recuperación de contraseña', 'Reset Password Email');
    pll_register_string('Hola %s,', 'Hola %s,', 'Reset Password Email');
    pll_register_string('Has solicitado restablecer tu contraseña. Haz clic en el siguiente botón para crear una nueva contraseña:', 'Has solicitado restablecer tu contraseña. Haz clic en el siguiente botón para crear una nueva contraseña:', 'Reset Password Email');
    pll_register_string('Restablecer mi contraseña', 'Restablecer mi contraseña', 'Reset Password Email');
    pll_register_string('Este enlace expirará en 1 hora.', 'Este enlace expirará en 1 hora.', 'Reset Password Email');
    pll_register_string('Si no solicitaste este cambio, ignora este correo.', 'Si no solicitaste este cambio, ignora este correo.', 'Reset Password Email');

    // Página de restablecimiento de contraseña
    pll_register_string('Restablecer contraseña', 'Restablecer contraseña', 'Reset Password Page');
    pll_register_string('¡Contraseña actualizada con éxito!', '¡Contraseña actualizada con éxito!', 'Reset Password Page');
    pll_register_string('El enlace de recuperación no es válido o ha expirado.', 'El enlace de recuperación no es válido o ha expirado.', 'Reset Password Page');
    pll_register_string('Solicitar nuevo enlace', 'Solicitar nuevo enlace', 'Reset Password Page');
    pll_register_string('Por favor, introduce una nueva contraseña.', 'Por favor, introduce una nueva contraseña.', 'Reset Password Page');
    pll_register_string('La contraseña debe tener al menos 8 caracteres.', 'La contraseña debe tener al menos 8 caracteres.', 'Reset Password Page');
    pll_register_string('El enlace ha expirado. Solicita uno nuevo.', 'El enlace ha expirado. Solicita uno nuevo.', 'Reset Password Page');
    pll_register_string('Nueva contraseña', 'Nueva contraseña', 'Reset Password Page');
    pll_register_string('Cambiar contraseña', 'Cambiar contraseña', 'Reset Password Page');

    // Página En Construcción
    pll_register_string('Sitio en construcción', 'Sitio en construcción', 'Under Construction Page');
    pll_register_string('La página a la que has accedido (%s) está todavía en desarrollo.', 'La página a la que has accedido (%s) está todavía en desarrollo.', 'Under Construction Page');
    
    // Guardar recursos favoritos - Modal de login
    pll_register_string('Debes iniciar sesión para guardar recursos favoritos.', 'Debes iniciar sesión para guardar recursos favoritos.', 'Guardados');
    pll_register_string('Guardar', 'Guardar', 'Guardados');
    pll_register_string('Guardado', 'Guardado', 'Guardados');
    pll_register_string('Mis Recursos Guardados', 'Mis Recursos Guardados', 'Guardados');
    pll_register_string('Debes iniciar sesión para ver tus recursos guardados.', 'Debes iniciar sesión para ver tus recursos guardados.', 'Guardados');
    pll_register_string('Aún no has guardado ningún recurso', 'Aún no has guardado ningún recurso', 'Guardados');
    pll_register_string('Guarda tus recursos favoritos para acceder a ellos rápidamente', 'Guarda tus recursos favoritos para acceder a ellos rápidamente', 'Guardados');
    pll_register_string('¡Explorar recursos!', '¡Explorar recursos!', 'Guardados');

    //Menú de usuario
    pll_register_string('Mi Perfil', 'Mi Perfil', 'User Menu');
    pll_register_string('Guardados', 'Guardados', 'User Menu');
    pll_register_string('Estadísticas', 'Estadísticas', 'User Menu');
    pll_register_string('Mis Recursos', 'Mis Recursos', 'User Menu');
    pll_register_string('Panel de Administración', 'Panel de Administración', 'User Menu');
    pll_register_string('Cerrar Sesión', 'Cerrar Sesión', 'User Menu');
    pll_register_string('Hola', 'Hola', 'User Menu');

    //Página de Perfil
    pll_register_string('Mi perfil', 'Mi perfil', 'Profile Page');
    pll_register_string('¿Deseas cambiar tu contraseña?', '¿Deseas cambiar tu contraseña?', 'Profile Page');
    pll_register_string('Cambia tu contraseña aquí', 'Cambia tu contraseña aquí', 'Profile Page');
    pll_register_string('Actualizar perfil', 'Actualizar perfil', 'Profile Page');
    pll_register_string('Eliminar cuenta', 'Eliminar cuenta', 'Profile Page');
    pll_register_string('El correo electrónico ya está siendo utilizado por otro usuario.', 'El correo electrónico ya está siendo utilizado por otro usuario.', 'Profile Page');
    pll_register_string('El enlace de verificación no es válido o ha expirado.', 'El enlace de verificación no es válido o ha expirado.', 'Profile Page');
    pll_register_string('Error al eliminar la cuenta. Inténtalo de nuevo.', 'Error al eliminar la cuenta. Inténtalo de nuevo.', 'Profile Page');
    pll_register_string('El nombre de usuario no se puede cambiar una vez creada la cuenta.', 'El nombre de usuario no se puede cambiar una vez creada la cuenta.', 'Profile Page');
    pll_register_string('El nombre de usuario no se puede cambiar', 'El nombre de usuario no se puede cambiar', 'Profile Page');
    pll_register_string('El nombre de usuario no se puede modificar una vez creada la cuenta.', 'El nombre de usuario no se puede modificar una vez creada la cuenta.', 'Profile Page');
    pll_register_string('Perfil actualizado correctamente.', 'Perfil actualizado correctamente.', 'Profile Page');
    pll_register_string('Perfil actualizado. Te hemos enviado un correo de verificación a tu nueva dirección. Por favor, revisa tu bandeja de entrada y haz clic en el enlace para confirmar el cambio.', 'Perfil actualizado. Te hemos enviado un correo de verificación a tu nueva dirección. Por favor, revisa tu bandeja de entrada y haz clic en el enlace para confirmar el cambio.', 'Profile Page');
    pll_register_string('¡Correo electrónico verificado y actualizado correctamente!', '¡Correo electrónico verificado y actualizado correctamente!', 'Profile Page');
    pll_register_string('Tienes un cambio de correo pendiente a: %s. Por favor, revisa tu bandeja de entrada para verificarlo.', 'Tienes un cambio de correo pendiente a: %s. Por favor, revisa tu bandeja de entrada para verificarlo.', 'Profile Page');
    pll_register_string('Error al actualizar el perfil. Inténtalo de nuevo.', 'Error al actualizar el perfil. Inténtalo de nuevo.', 'Profile Page');
    pll_register_string('Verifica tu nuevo correo electrónico', 'Verifica tu nuevo correo electrónico', 'Profile Page');
    pll_register_string('Error al enviar el correo de verificación. Inténtalo de nuevo.', 'Error al enviar el correo de verificación. Inténtalo de nuevo.', 'Profile Page');

    // Email de verificación de correo electrónico
    pll_register_string('Has solicitado cambiar tu dirección de correo electrónico. Para completar este cambio, necesitamos verificar que esta dirección te pertenece.', 'Has solicitado cambiar tu dirección de correo electrónico. Para completar este cambio, necesitamos verificar que esta dirección te pertenece.', 'Verify Email');
    pll_register_string('Haz clic en el siguiente botón para verificar tu correo:', 'Haz clic en el siguiente botón para verificar tu correo:', 'Verify Email');
    pll_register_string('Verificar correo electrónico', 'Verificar correo electrónico', 'Verify Email');

    // Página de Guía Introductoria
    pll_register_string('¡Bienvenido/a a NEEMA Toolkit! Esta guía te acompañará paso a paso en el uso de la plataforma. Consúltala siempre que necesites ayuda o tengas alguna duda sobre cómo navegar y aprovechar al máximo todo el material disponible.', '¡Bienvenido/a a NEEMA Toolkit! Esta guía te acompañará paso a paso en el uso de la plataforma. Consúltala siempre que necesites ayuda o tengas alguna duda sobre cómo navegar y aprovechar al máximo todo el material disponible.', 'Guía Introductoria');
    pll_register_string('Vaya... ahora mismo no hay una guía introductoria disponible', 'Vaya... ahora mismo no hay una guía introductoria disponible', 'Guía Introductoria');

    // Página de Servicios de Apoyo
    pll_register_string('Cómo tener acceso a servicios que puedan ofrecer apoyo.', 'Cómo tener acceso a servicios que puedan ofrecer apoyo.', 'Servicios de Apoyo');
    pll_register_string('En esta sección encontrarás diferentes organismos que ofrecen apoyo técnico, institucional y operativo en el ámbito de la resiliencia alimentaria y nutricional. Selecciona el tipo de organización que mejor se adapte a tu situación para acceder a más información.', 'En esta sección encontrarás diferentes organismos que ofrecen apoyo técnico, institucional y operativo en el ámbito de la resiliencia alimentaria y nutricional. Selecciona el tipo de organización que mejor se adapte a tu situación para acceder a más información.', 'Servicios de Apoyo');
    
    // Página de Categoría de Organismo
    pll_register_string('Organismos', 'Organismos', 'Servicios de Apoyo');
    pll_register_string('Alcance', 'Alcance', 'Servicios de Apoyo');
    pll_register_string('Ámbito', 'Ámbito', 'Servicios de Apoyo');
    pll_register_string('País/es', 'País/es', 'Servicios de Apoyo');
    pll_register_string('Ciudad/Localidad', 'Ciudad/Localidad', 'Servicios de Apoyo');
    pll_register_string('Contacto', 'Contacto', 'Servicios de Apoyo');
    pll_register_string('Web', 'Web', 'Servicios de Apoyo');
    pll_register_string('Email', 'Email', 'Servicios de Apoyo');
    pll_register_string('No hay organismos disponibles en esta categoría.', 'No hay organismos disponibles en esta categoría.', 'Servicios de Apoyo');
    pll_register_string('No se encontraron organismos con los criterios seleccionados.', 'No se encontraron organismos con los criterios seleccionados.', 'Servicios de Apoyo');
    pll_register_string('Internacional', 'Internacional', 'Servicios de Apoyo');
    pll_register_string('Nacional', 'Nacional', 'Servicios de Apoyo');
    pll_register_string('Local', 'Local', 'Servicios de Apoyo');
    pll_register_string('Ciudad o Localidad', 'Ciudad o Localidad', 'Servicios de Apoyo');

    // Funding statement
    pll_register_string('NEEMA: Desarrollo de capacidades en la enseñanza superior mediante la elaboración de un plan de estudios sobre resiliencia alimentaria y nutricional adaptado al Pacto Verde Europeo, a la estrategia «de la granja a la mesa» y a las necesidades de África Occidental. NEEMA ha recibido financiación de la Unión Europea en el marco del Grant Agreenment no 101128930. No obstante, las opiniones expresadas en esta comunicación son exclusivamente las del autor o autores y no reflejan necesariamente las de la Unión Europea. Ni la Unión Europea ni la autoridad que concede la subvención pueden ser consideradas responsables de las mismas.', 'NEEMA: Desarrollo de capacidades en la enseñanza superior mediante la elaboración de un plan de estudios sobre resiliencia alimentaria y nutricional adaptado al Pacto Verde Europeo, a la estrategia «de la granja a la mesa» y a las necesidades de África Occidental. NEEMA ha recibido financiación de la Unión Europea en el marco del Grant Agreenment no 101128930. No obstante, las opiniones expresadas en esta comunicación son exclusivamente las del autor o autores y no reflejan necesariamente las de la Unión Europea. Ni la Unión Europea ni la autoridad que concede la subvención pueden ser consideradas responsables de las mismas.', 'Funding Statement');

    // Page-summary
    pll_register_string('Contenidos de la página', 'Contenidos de la página', 'Page Summary');

    // Página Resiliencia Alimentaria y Nutricional
    pll_register_string('Cómo obtener Resiliencia Alimentaria y Nutricional', 'Cómo obtener Resiliencia Alimentaria y Nutricional', 'Resiliencia Alimentaria y Nutricional');
    pll_register_string('En esta sección encontrarás herramientas de apoyo para el diseño de cursos, entrenamientos, proyectos de cooperación... vinculadas a la resiliencia alimentaria y nutricional.', 'En esta sección encontrarás herramientas de apoyo para el diseño de cursos, entrenamientos, proyectos de cooperación... vinculadas a la resiliencia alimentaria y nutricional.', 'Resiliencia Alimentaria y Nutricional');
    pll_register_string('Propuestas de diseños', 'Propuestas de diseños', 'Resiliencia Alimentaria y Nutricional');


}
add_action('init', 'neema_registrar_cadenas');
