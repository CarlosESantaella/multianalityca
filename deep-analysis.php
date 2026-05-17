<?php
/**
 * Análisis profundo - Comparación Local vs Producción
 */
define('WP_USE_THEMES', false);
require_once('./wp-load.php');

echo "<h1>Análisis Profundo - Local vs Producción</h1>";
echo "<style>
body{font-family:monospace;padding:20px;font-size:12px;}
.error{color:red;background:#ffe0e0;padding:2px 5px;}
.ok{color:green;}
.warn{color:orange;background:#fff3cd;padding:2px 5px;}
table{border-collapse:collapse;margin:10px 0;width:100%;}
td,th{border:1px solid #ccc;padding:6px;text-align:left;vertical-align:top;}
pre{background:#f5f5f5;padding:10px;overflow:auto;max-height:200px;font-size:11px;}
h2{background:#333;color:white;padding:10px;margin-top:20px;}
</style>";

global $wpdb;

// 1. Versiones de tema y plugins clave
echo "<h2>1. Versiones de Tema y Plugins</h2>";
$theme = wp_get_theme('Avada');
echo "<table>";
echo "<tr><th>Componente</th><th>Versión Local</th></tr>";
echo "<tr><td>Tema Avada</td><td>" . $theme->get('Version') . "</td></tr>";
echo "<tr><td>WordPress</td><td>" . get_bloginfo('version') . "</td></tr>";
echo "<tr><td>PHP</td><td>" . phpversion() . "</td></tr>";

// Plugins clave
$plugins_to_check = [
    'fusion-builder/fusion-builder.php' => 'Fusion Builder',
    'fusion-core/fusion-core.php' => 'Fusion Core',
    'elementor/elementor.php' => 'Elementor',
    'wp-rocket/wp-rocket.php' => 'WP Rocket',
];

foreach ($plugins_to_check as $plugin_file => $name) {
    $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
    if (file_exists($plugin_path)) {
        $plugin_data = get_plugin_data($plugin_path);
        $active = is_plugin_active($plugin_file) ? '✓ Activo' : '✗ Inactivo';
        echo "<tr><td>$name</td><td>{$plugin_data['Version']} - $active</td></tr>";
    }
}
echo "</table>";

// 2. Opciones críticas de Avada
echo "<h2>2. Opciones Críticas de Avada (fusion_options)</h2>";
$fusion_options = get_option('fusion_options');
$critical_options = [
    'css_cache_method', 'css_vars', 'responsive', 'site_width',
    'header_100_width', 'page_title_100_width', 'footer_100_width',
    'hundredp_padding', 'side_header_width', 'layout',
    'primary_color', 'header_layout', 'logo'
];

echo "<table><tr><th>Opción</th><th>Valor</th></tr>";
foreach ($critical_options as $key) {
    $val = isset($fusion_options[$key]) ? $fusion_options[$key] : '<em>no definido</em>';
    if (is_array($val)) $val = json_encode($val);
    if (strlen($val) > 100) $val = substr($val, 0, 100) . '...';
    echo "<tr><td>$key</td><td>" . htmlspecialchars($val) . "</td></tr>";
}
echo "</table>";

// 3. Contenido del Hero - primer container
echo "<h2>3. Configuración del Hero (Página de Inicio)</h2>";
$home_id = get_option('page_on_front');
$content = get_post_field('post_content', $home_id);

// Extraer TODOS los containers
preg_match_all('/\[fusion_builder_container([^\]]*)\]/s', $content, $containers);
echo "<p>Total de containers en la página: " . count($containers[0]) . "</p>";

if (!empty($containers[1][0])) {
    echo "<h3>Primer Container (Hero):</h3>";
    $attrs_str = $containers[1][0];

    // Parsear atributos importantes
    $important_attrs = ['hundred_percent', 'hundred_percent_height', 'type', 'padding_top', 'padding_right', 'padding_bottom', 'padding_left', 'margin_top', 'margin_bottom', 'background_color', 'background_image'];

    echo "<table><tr><th>Atributo</th><th>Valor</th></tr>";
    foreach ($important_attrs as $attr) {
        if (preg_match('/' . $attr . '="([^"]*)"/', $attrs_str, $m)) {
            $highlight = ($attr == 'hundred_percent' && $m[1] != 'yes') ? 'class="warn"' : '';
            echo "<tr $highlight><td>$attr</td><td>{$m[1]}</td></tr>";
        }
    }
    echo "</table>";
}

// 4. CSS Custom de Avada
echo "<h2>4. Custom CSS en Avada Options</h2>";
$custom_css = isset($fusion_options['custom_css']) ? $fusion_options['custom_css'] : '';
if (!empty($custom_css)) {
    echo "<p>Longitud: " . strlen($custom_css) . " caracteres</p>";
    echo "<pre>" . htmlspecialchars($custom_css) . "</pre>";
} else {
    echo "<p class='warn'>No hay Custom CSS en Avada Options</p>";
}

// 5. CSS de la página específica
echo "<h2>5. CSS Específico de la Página de Inicio</h2>";
$page_css = get_post_meta($home_id, '_fusion_builder_custom_css', true);
if (!empty($page_css)) {
    echo "<pre>" . htmlspecialchars($page_css) . "</pre>";
} else {
    echo "<p>No hay CSS específico en esta página</p>";
}

// 6. Revisar posts/pages recientes para ver si hay contenido de producción
echo "<h2>6. Verificar URLs en Contenido</h2>";
$prod_in_content = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE '%multianalityca.com%'");
$prod_in_meta = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_value LIKE '%multianalityca.com%'");
echo "<p>URLs .com en posts: " . ($prod_in_content > 0 ? "<span class='error'>$prod_in_content</span>" : "<span class='ok'>0</span>") . "</p>";
echo "<p>URLs .com en postmeta: " . ($prod_in_meta > 0 ? "<span class='error'>$prod_in_meta</span>" : "<span class='ok'>0</span>") . "</p>";

// 7. Comprobar archivos críticos
echo "<h2>7. Archivos del Tema</h2>";
$theme_files = [
    'wp-content/themes/Avada/style.css',
    'wp-content/themes/Avada-Child-Theme/style.css',
    'wp-content/themes/Avada-Child-Theme/functions.php',
];
echo "<table><tr><th>Archivo</th><th>Tamaño</th><th>Modificado</th></tr>";
foreach ($theme_files as $file) {
    $full_path = ABSPATH . $file;
    if (file_exists($full_path)) {
        $size = filesize($full_path);
        $mod = date('Y-m-d H:i', filemtime($full_path));
        echo "<tr><td>$file</td><td>$size bytes</td><td>$mod</td></tr>";
    } else {
        echo "<tr><td>$file</td><td colspan='2' class='error'>No existe</td></tr>";
    }
}
echo "</table>";

// 8. Child theme functions.php content
echo "<h2>8. Contenido de Child Theme functions.php</h2>";
$child_functions = ABSPATH . 'wp-content/themes/Avada-Child-Theme/functions.php';
if (file_exists($child_functions)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($child_functions)) . "</pre>";
}

// 9. Obtener el HTML del hero para comparar
echo "<h2>9. Para Comparar Manualmente</h2>";
echo "<p><strong>Pasos para identificar la diferencia exacta:</strong></p>";
echo "<ol>";
echo "<li>Abre <a href='https://multianalityca.test' target='_blank'>Local</a> y <a href='https://multianalityca.com' target='_blank'>Producción</a> en ventanas separadas</li>";
echo "<li>En ambas, presiona F12 → Inspector</li>";
echo "<li>Haz clic derecho en el elemento que se ve diferente → 'Inspeccionar'</li>";
echo "<li>Compara las clases CSS y los estilos aplicados</li>";
echo "<li>Busca diferencias en: width, max-width, padding, margin</li>";
echo "</ol>";

echo "<h2>10. Herramienta de Comparación Directa</h2>";
echo "<p>Pega aquí la clase del elemento problemático de producción para que lo busque:</p>";
echo "<form method='get'><input type='text' name='search_class' placeholder='ej: fusion-builder-row' style='width:300px;padding:5px;'> <button type='submit'>Buscar</button></form>";

if (isset($_GET['search_class']) && !empty($_GET['search_class'])) {
    $search = sanitize_text_field($_GET['search_class']);
    echo "<h3>Buscando '$search' en el contenido...</h3>";
    $found = $wpdb->get_results($wpdb->prepare(
        "SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_content LIKE %s AND post_status = 'publish' LIMIT 10",
        '%' . $search . '%'
    ));
    if ($found) {
        echo "<ul>";
        foreach ($found as $p) {
            echo "<li>{$p->post_type}: {$p->post_title} (ID: {$p->ID})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No encontrado en contenido de posts</p>";
    }
}
