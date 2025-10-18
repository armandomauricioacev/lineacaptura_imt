# Integración con API del SAT - Instrucciones de Configuración

## 📋 Resumen de la Implementación

Se ha implementado la integración completa con la API del SAT para validación de líneas de captura. El sistema ahora:

1. ✅ Genera JSON estructurado con los datos de la línea de captura
2. ✅ Envía el JSON a la API del SAT para validación
3. ✅ Recibe y procesa la respuesta del SAT
4. ✅ Decodifica el HTML codificado en Base64
5. ✅ Muestra una vista previa del documento
6. ✅ Permite descargar el HTML como archivo
7. ✅ Almacena toda la información en la base de datos

## 🔧 Configuración Requerida

### 1. Variables de Entorno (.env)

Agrega las siguientes variables a tu archivo `.env`:

```env
# ==========================================================
#  CONFIGURACIÓN DE LA API DEL SAT
#  ¡AQUÍ DEBES COLOCAR LA URL Y CREDENCIALES CUANDO LAS TENGAS!
# ==========================================================

# URL de la API del SAT para validación de líneas de captura
SAT_API_URL=https://api.sat.gob.mx/validacion/linea-captura

# Token de autenticación para la API del SAT (si es necesario)
# SAT_API_TOKEN=tu_token_aqui

# Clave de API para el SAT (si es necesario)
# SAT_API_KEY=tu_api_key_aqui

# Timeout en segundos para las peticiones a la API del SAT
SAT_API_TIMEOUT=30
```

### 2. Ejecutar Migraciones

```bash
php artisan migrate
```

## 📊 Nuevos Campos en la Base de Datos

Se agregaron los siguientes campos a la tabla `lineas_capturadas`:

- `json_recibido` (JSON) - Respuesta completa del SAT
- `id_documento` (VARCHAR) - ID del documento del SAT
- `tipo_pago` (VARCHAR) - Tipo de pago del SAT
- `html_codificado` (TEXT) - HTML codificado en Base64 del SAT
- `resultado` (VARCHAR) - Resultado de la validación del SAT
- `linea_captura` (VARCHAR) - Línea de captura generada por el SAT
- `importe_sat` (DECIMAL) - Importe validado por el SAT
- `fecha_vigencia_sat` (DATE) - Fecha de vigencia del SAT
- `errores_sat` (JSON) - Errores reportados por el SAT
- `fecha_respuesta_sat` (DATETIME) - Fecha y hora de la respuesta del SAT
- `procesado_exitosamente` (BOOLEAN) - Indica si el procesamiento fue exitoso

## 🚀 Funcionalidades Implementadas

### 1. Generación y Envío de JSON
- El sistema genera automáticamente el JSON con los datos de la línea de captura
- Se envía a la API del SAT usando cURL con manejo robusto de errores
- Soporte para autenticación con token y API key

### 2. Procesamiento de Respuesta
- Decodifica la respuesta JSON del SAT
- Extrae y decodifica el HTML en Base64
- Almacena todos los datos en la base de datos

### 3. Vista Previa y Descarga
- Muestra una vista previa del HTML decodificado
- Botón para descargar el HTML como archivo
- Botón para abrir el HTML en una nueva ventana
- Visualización de todos los datos de respuesta del SAT

## 🔍 Cómo Probar la Funcionalidad

### 1. Acceder al Sistema
1. Inicia el servidor: `php artisan serve`
2. Ve a: `http://127.0.0.1:8000`
3. Navega al formulario de línea de captura

### 2. Generar una Línea de Captura
1. Llena todos los campos requeridos del formulario
2. Haz clic en "Generar Línea de Captura"
3. El sistema automáticamente:
   - Genera el JSON
   - Lo envía al SAT (actualmente simulado)
   - Procesa la respuesta
   - Muestra los resultados

### 3. Revisar los Resultados
- **Datos del SAT**: ID Documento, Tipo de Pago, Resultado, etc.
- **JSON Completo**: Respuesta completa del SAT
- **Vista Previa HTML**: Documento decodificado
- **Botones de Acción**: Descargar y abrir en nueva ventana

## ⚠️ Notas Importantes

### Para Desarrollo
- La URL de la API del SAT es un placeholder
- Actualmente el sistema está preparado pero necesita la URL real
- Los métodos de autenticación están listos para configurar

### Para Producción
1. **Configurar URL Real**: Reemplaza `SAT_API_URL` con la URL real del SAT
2. **Agregar Credenciales**: Configura `SAT_API_TOKEN` y/o `SAT_API_KEY` si son necesarios
3. **Verificar SSL**: El sistema usa verificación SSL completa
4. **Monitorear Logs**: Revisa los logs para errores de conexión

## 🛠️ Archivos Modificados

### Controlador
- `app/Http/Controllers/LineaCapturaController.php`
  - Método `generarLineaCaptura()` - Integración principal
  - Método `enviarJsonASat()` - Comunicación con API
  - Método `procesarRespuestaSat()` - Procesamiento de respuesta

### Modelo
- `app/Models/LineasCapturadas.php`
  - Agregados nuevos campos fillable
  - Configurados casts para tipos de datos

### Vista
- `resources/views/forms/lineacaptura.blade.php`
  - Sección de resultados del SAT
  - Funciones JavaScript para descarga y vista previa
  - Estilos CSS para la presentación

### Base de Datos
- `database/migrations/2025_10_17_232634_add_sat_fields_to_lineas_capturadas_table.php`
  - Migración con todos los nuevos campos

### Configuración
- `.env.example` - Variables de configuración del SAT

## 📞 Soporte

Si encuentras algún problema:
1. Verifica que las migraciones se ejecutaron correctamente
2. Revisa la configuración en el archivo `.env`
3. Consulta los logs de Laravel para errores específicos
4. Asegúrate de que la URL de la API del SAT sea correcta cuando esté disponible

---

**¡La integración está lista para usar una vez que tengas la URL real de la API del SAT!**