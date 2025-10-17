<?php
$archivo = 'c:\lineacaptura_imt\lineacaptura\resources\views\forms\codigo.json';

if (!file_exists($archivo)) {
    echo "El archivo no existe: $archivo\n";
    exit(1);
}

$content = file_get_contents($archivo);
echo "Tamaño del archivo: " . strlen($content) . " bytes\n";

// Mostrar los últimos 200 caracteres
echo "\nÚltimos 200 caracteres:\n";
echo substr($content, -200) . "\n";

// Verificar si termina con }
echo "\n¿Termina con }? " . (substr(trim($content), -1) === '}' ? 'SÍ' : 'NO') . "\n";

// Intentar decodificar JSON
echo "\nIntentando decodificar JSON...\n";
$decoded = json_decode($content, true);

if ($decoded === null) {
    echo "Error JSON: " . json_last_error_msg() . "\n";
    echo "Código de error: " . json_last_error() . "\n";
    
    // Buscar caracteres problemáticos
    echo "\nBuscando caracteres de control...\n";
    for ($i = 0; $i < strlen($content); $i++) {
        $char = $content[$i];
        $ord = ord($char);
        if ($ord < 32 && $ord !== 9 && $ord !== 10 && $ord !== 13) {
            echo "Carácter de control encontrado en posición $i: ASCII $ord\n";
        }
    }
    
    // Verificar balance de llaves
    $open = substr_count($content, '{');
    $close = substr_count($content, '}');
    echo "\nBalance de llaves: { = $open, } = $close\n";
    
} else {
    echo "JSON válido!\n";
    echo "Estructura encontrada:\n";
    print_r(array_keys($decoded));
}
?>