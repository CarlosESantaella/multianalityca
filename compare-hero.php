<?php
/**
 * Comparar Hero local vs producción
 */
define('WP_USE_THEMES', false);
require_once('./wp-load.php');

echo "<h1>Comparar Configuración del Hero</h1>";
echo "<style>
body{font-family:monospace;padding:20px;}
.compare{display:flex;gap:20px;}
.local,.prod{flex:1;padding:10px;border:2px solid #ccc;}
.local{border-color:blue;}
.prod{border-color:green;}
pre{background:#f5f5f5;padding:10px;font-size:11px;overflow:auto;max-height:400px;}
.diff{background:yellow;}
</style>";

// Obtener contenido local
$home_id = get_option('page_on_front');
$local_content = get_post_field('post_content', $home_id);

// Extraer primer container
preg_match('/\[fusion_builder_container([^\]]*)\]/', $local_content, $local_match);
$local_attrs = isset($local_match[1]) ? $local_match[1] : '';

// Parsear atributos relevantes
function parse_attrs($str) {
    $attrs = [];
    preg_match_all('/(\w+)="([^"]*)"/', $str, $matches, PREG_SET_ORDER);
    foreach ($matches as $m) {
        $attrs[$m[1]] = $m[2];
    }
    return $attrs;
}

$local_parsed = parse_attrs($local_attrs);

echo "<h2>Configuración del Hero (Container Principal)</h2>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>Atributo</th><th>Valor Local</th><th>¿Afecta al ancho?</th></tr>";

$width_attrs = [
    'hundred_percent' => 'Sí - controla si usa 100% del viewport',
    'hundred_percent_height' => 'Sí - altura 100%',
    'padding_left' => 'Sí - padding izquierdo',
    'padding_right' => 'Sí - padding derecho',
    'padding_left_small' => 'Mobile padding izquierdo',
    'padding_right_small' => 'Mobile padding derecho',
    'type' => 'Tipo de container',
];

foreach ($width_attrs as $attr => $desc) {
    $val = isset($local_parsed[$attr]) ? $local_parsed[$attr] : '<em>no definido</em>';
    $highlight = ($attr === 'hundred_percent' && $val === 'no') ? 'class="diff"' : '';
    echo "<tr $highlight><td>$attr</td><td>$val</td><td>$desc</td></tr>";
}
echo "</table>";

// Extraer primera columna
preg_match('/\[fusion_builder_column([^\]]*)\]/', $local_content, $col_match);
$col_attrs = isset($col_match[1]) ? $col_match[1] : '';
$col_parsed = parse_attrs($col_attrs);

echo "<h2>Configuración de la Columna Principal</h2>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>Atributo</th><th>Valor Local</th></tr>";

$col_width_attrs = ['type', 'type_medium', 'type_small', 'spacing_left', 'spacing_right', 'margin_top', 'margin_bottom'];
foreach ($col_width_attrs as $attr) {
    $val = isset($col_parsed[$attr]) ? $col_parsed[$attr] : '<em>no definido</em>';
    $highlight = (in_array($attr, ['spacing_left', 'spacing_right']) && !empty($val)) ? 'class="diff"' : '';
    echo "<tr $highlight><td>$attr</td><td>$val</td></tr>";
}
echo "</table>";

echo "<h2>⚠️ Posibles Causas de Diferencia</h2>";
echo "<ol>";
echo "<li><strong>hundred_percent='no'</strong> → El container no usa el 100% del viewport</li>";
echo "<li><strong>spacing_left='10%' y spacing_right='10%'</strong> → La columna tiene márgenes de 10% a cada lado</li>";
echo "<li><strong>type_medium='3_5'</strong> → En pantallas medianas solo usa 3/5 del ancho</li>";
echo "</ol>";

echo "<h2>🔧 Solución Rápida</h2>";
echo "<p>Si en producción se ve a ancho completo, probablemente esos valores son diferentes allá.</p>";
echo "<p><strong>Opción 1:</strong> Exporta la base de datos de PRODUCCIÓN y reemplaza la local</p>";
echo "<p><strong>Opción 2:</strong> Edita la página manualmente en Fusion Builder y cambia:</p>";
echo "<ul>";
echo "<li>Container → hundred_percent = yes</li>";
echo "<li>Columna → spacing_left = 0, spacing_right = 0</li>";
echo "</ul>";

echo "<p><a href='" . admin_url("post.php?post=$home_id&action=edit") . "' class='button'>Editar Página de Inicio</a></p>";
