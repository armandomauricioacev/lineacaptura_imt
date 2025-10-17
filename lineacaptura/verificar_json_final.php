<?php
echo "Validando JSON corregido...\n";

$jsonFile = 'c:\lineacaptura_imt\lineacaptura\resources\views\forms\codigo.json';
$json = file_get_contents($jsonFile);

echo "Tamaño del archivo: " . strlen($json) . " bytes\n";
echo "Últimos 50 caracteres: '" . substr($json, -50) . "'\n";

$data = json_decode($json, true);

if (json_last_error() === JSON_ERROR_NONE) {
    echo "✓ JSON válido!\n";
    echo "Estructura encontrada:\n";
    echo "- DatosGenerales: " . (isset($data['DatosGenerales']) ? 'Sí' : 'No') . "\n";
    echo "- Acuse: " . (isset($data['Acuse']) ? 'Sí' : 'No') . "\n";
    if (isset($data['Acuse']['HTML'])) {
        echo "- HTML base64: " . substr($data['Acuse']['HTML'], 0, 50) . "...\n";
    }
} else {
    echo "✗ Error JSON: " . json_last_error_msg() . "\n";
    echo "Código de error: " . json_last_error() . "\n";
}
?>