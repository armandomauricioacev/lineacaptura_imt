<?php
echo "Verificando archivo codigo.txt...\n";

$rutaArchivo = 'resources/views/forms/codigo.txt';

if (!file_exists($rutaArchivo)) {
    echo "Error: El archivo no existe.\n";
    exit(1);
}

$contenido = file_get_contents($rutaArchivo);
echo "Tamaño del archivo: " . strlen($contenido) . " bytes\n";

// Verificar si hay caracteres de control
$caracteresControl = [];
for ($i = 0; $i < strlen($contenido); $i++) {
    $char = $contenido[$i];
    $ascii = ord($char);
    if ($ascii < 32 && $ascii != 9 && $ascii != 10 && $ascii != 13) {
        $caracteresControl[] = [
            'posicion' => $i,
            'ascii' => $ascii,
            'hex' => dechex($ascii)
        ];
    }
}

if (!empty($caracteresControl)) {
    echo "Caracteres de control encontrados:\n";
    foreach ($caracteresControl as $char) {
        echo "Posición: {$char['posicion']}, ASCII: {$char['ascii']}, HEX: {$char['hex']}\n";
    }
} else {
    echo "No se encontraron caracteres de control problemáticos.\n";
}

// Intentar decodificar JSON
$datosJson = json_decode($contenido, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error JSON: " . json_last_error_msg() . "\n";
    echo "Código de error: " . json_last_error() . "\n";
    
    // Mostrar los primeros 200 caracteres para diagnóstico
    echo "Primeros 200 caracteres del archivo:\n";
    echo substr($contenido, 0, 200) . "\n";
    
    // Mostrar los últimos 200 caracteres
    echo "Últimos 200 caracteres del archivo:\n";
    echo substr($contenido, -200) . "\n";
} else {
    echo "JSON válido - decodificación exitosa.\n";
    if (isset($datosJson['Acuse']['HTML'])) {
        echo "Campo HTML encontrado en el JSON.\n";
        echo "Longitud del HTML base64: " . strlen($datosJson['Acuse']['HTML']) . " caracteres\n";
    }
}
?>