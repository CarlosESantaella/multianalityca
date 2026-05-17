<?php
/**
 * Diagnóstico de URLs y configuración de WordPress
 * Accede a: https://multianalityca.test/diagnostico.php
 */

// Cargar WordPress sin ejecutar todo
define('WP_USE_THEMES', false);
require_once('./wp-load.php');

echo "<h1>Diagnóstico de WordPress</h1>";
echo "<style>body{font-family:monospace;padding:20px;} .ok{color:green;} .error{color:red;} table{border-collapse:collapse;} td,th{border:1px solid #ccc;padding:8px;}</style>";

// 1. URLs configuradas
echo "<h2>1. URLs Configuradas</h2>";
echo "<table>";
echo "<tr><th>Configuración</th><th>Valor</th></tr>";
echo "<tr><td>WP_HOME (constante)</td><td>" . (defined('WP_HOME') ? WP_HOME : 'No definido') . "</td></tr>";
echo "<tr><td>WP_SITEURL (constante)</td><td>" . (defined('WP_SITEURL') ? WP_SITEURL : 'No definido') . "</td></tr>";
echo "<tr><td>home (BD)</td><td>" . get_option('home') . "</td></tr>";
echo "<tr><td>siteurl (BD)</td><td>" . get_option('siteurl') . "</td></tr>";
echo "<tr><td>home_url()</td><td>" . home_url() . "</td></tr>";
echo "<tr><td>site_url()</td><td>" . site_url() . "</td></tr>";
echo "</table>";

// 2. SSL y seguridad
echo "<h2>2. Configuración SSL</h2>";
echo "<table>";
echo "<tr><th>Configuración</th><th>Valor</th></tr>";
echo "<tr><td>is_ssl()</td><td>" . (is_ssl() ? '<span class="ok">SÍ (HTTPS activo)</span>' : '<span class="error">NO</span>') . "</td></tr>";
echo "<tr><td>FORCE_SSL_ADMIN</td><td>" . (defined('FORCE_SSL_ADMIN') ? (FORCE_SSL_ADMIN ? 'true' : 'false') : 'No definido') . "</td></tr>";
echo "<tr><td>\$_SERVER['HTTPS']</td><td>" . (isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'No definido') . "</td></tr>";
echo "<tr><td>\$_SERVER['SERVER_PORT']</td><td>" . $_SERVER['SERVER_PORT'] . "</td></tr>";
echo "</table>";

// 3. Plugins activos relacionados con SSL/Redirect
echo "<h2>3. Plugins Relacionados con SSL/Redirección</h2>";
$active_plugins = get_option('active_plugins');
$ssl_plugins = array();
foreach ($active_plugins as $plugin) {
    if (stripos($plugin, 'ssl') !== false ||
        stripos($plugin, 'redirect') !== false ||
        stripos($plugin, 'rocket') !== false ||
        stripos($plugin, 'litespeed') !== false) {
        $ssl_plugins[] = $plugin;
    }
}
if (!empty($ssl_plugins)) {
    echo "<ul>";
    foreach ($ssl_plugins as $plugin) {
        echo "<li>$plugin</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No se encontraron plugins relacionados activos.</p>";
}

// 4. Configuración de Really Simple SSL
echo "<h2>4. Configuración Really Simple SSL</h2>";
$rsssl_options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->prefix}options WHERE option_name LIKE '%rsssl%'");
if (!empty($rsssl_options)) {
    echo "<table>";
    echo "<tr><th>Opción</th><th>Valor</th></tr>";
    foreach ($rsssl_options as $option) {
        echo "<tr><td>" . esc_html($option->option_name) . "</td><td>" . esc_html(substr($option->option_value, 0, 100)) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No se encontraron opciones de Really Simple SSL.</p>";
}

// 5. Headers enviados
echo "<h2>5. Headers HTTP</h2>";
echo "<pre>";
foreach ($_SERVER as $key => $value) {
    if (stripos($key, 'HTTP_') === 0) {
        echo esc_html("$key: $value\n");
    }
}
echo "</pre>";

echo "<hr>";
echo "<p><strong>URL actual:</strong> " . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : 'No disponible') . "</p>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
