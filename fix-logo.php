<?php
/**
 * Corregir logo de Avada
 */
define('WP_USE_THEMES', false);
require_once('./wp-load.php');

echo "<h1>Corrección del Logo</h1>";
echo "<style>body{font-family:monospace;padding:20px;} .ok{color:green;} .error{color:red;}</style>";

// Obtener fusion_options
$fusion_options = get_option('fusion_options');

if (!$fusion_options) {
    die("<p class='error'>No se encontró fusion_options</p>");
}

// Logo actual
echo "<h2>Logo Actual</h2>";
echo "<pre>" . print_r($fusion_options['logo'], true) . "</pre>";

// Nuevo logo
$new_logo_url = home_url('/wp-content/uploads/2024/06/logo-multianalityca-400x124.webp');

// Buscar el attachment ID
global $wpdb;
$attachment_id = $wpdb->get_var($wpdb->prepare(
    "SELECT ID FROM {$wpdb->posts} WHERE guid LIKE %s AND post_type = 'attachment' LIMIT 1",
    '%logo-multianalityca%'
));

echo "<h2>Logo encontrado en uploads</h2>";
echo "<p>URL: $new_logo_url</p>";
echo "<p>Attachment ID: " . ($attachment_id ? $attachment_id : 'No encontrado') . "</p>";

// Mostrar imagen
echo "<p><img src='$new_logo_url' style='max-width:300px;border:1px solid #ccc;'></p>";

// Actualizar
if (isset($_GET['fix']) && $_GET['fix'] === 'yes') {
    $fusion_options['logo'] = [
        'url' => home_url('/wp-content/uploads/2024/06/logo-multianalityca-400x124.webp'),
        'id' => $attachment_id ? $attachment_id : '',
        'height' => '124',
        'width' => '400',
        'thumbnail' => ''
    ];

    update_option('fusion_options', $fusion_options);

    // Limpiar transients de Avada
    delete_transient('fusion_dynamic_css_posts');
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%fusion_dynamic_css%'");

    echo "<p class='ok'>✓ Logo actualizado correctamente</p>";
    echo "<p class='ok'>✓ Cache de CSS limpiado</p>";
    echo "<p><a href='" . home_url() . "'>Ver sitio →</a></p>";
} else {
    echo "<h2>¿Aplicar corrección?</h2>";
    echo "<p><a href='?fix=yes' style='background:green;color:white;padding:10px 20px;text-decoration:none;'>Sí, corregir el logo</a></p>";
}
