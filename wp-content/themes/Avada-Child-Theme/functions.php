<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

// Permitir subida de archivo .vcf a Medios //
function custom_mime_types($mimes) {
    // Añade el tipo MIME para archivos .vcf
    $mimes['vcf'] = 'text/x-vcard';
    return $mimes;
}
add_filter('upload_mimes', 'custom_mime_types');

/**
 * =====================================================
 * Banner de Cookies - Conforme a GDPR/LOPD
 * Editable desde el escritorio de WordPress
 * =====================================================
 */

/**
 * Obtener opciones del banner con valores por defecto
 */
function ma_cookie_get_options() {
    $defaults = array(
        'enabled' => '1',
        'message' => 'Utilizamos cookies propias y de terceros para mejorar nuestros servicios y mostrarle publicidad relacionada con sus preferencias mediante el análisis de sus hábitos de navegación.',
        'accept_text' => 'Aceptar',
        'reject_text' => 'Rechazar',
        'more_info_text' => 'Más información',
        'privacy_url' => '/privacidad',
        'cookie_days' => 365,
        'bg_color' => '#023a51',
        'text_color' => '#ffffff',
        'btn_accept_bg' => '#008ed2',
        'btn_accept_text' => '#ffffff',
        'btn_reject_bg' => 'transparent',
        'btn_reject_text' => '#e0ecf0',
        'btn_reject_border' => '#e0ecf0',
        'link_color' => '#008ed2',
        'ga_tracking_id' => 'G-78XL2YYXN3',
    );

    $options = get_option('ma_cookie_banner_options', array());
    return wp_parse_args($options, $defaults);
}

/**
 * Agregar página de opciones en el menú de administración
 */
function ma_cookie_admin_menu() {
    add_menu_page(
        'Banner de Cookies',
        'Banner Cookies',
        'manage_options',
        'ma-cookie-banner',
        'ma_cookie_admin_page',
        'dashicons-shield',
        81
    );
}
add_action('admin_menu', 'ma_cookie_admin_menu');

/**
 * Registrar configuraciones
 */
function ma_cookie_register_settings() {
    register_setting('ma_cookie_banner_group', 'ma_cookie_banner_options', 'ma_cookie_sanitize_options');
}
add_action('admin_init', 'ma_cookie_register_settings');

/**
 * Sanitizar opciones
 */
function ma_cookie_sanitize_options($input) {
    $sanitized = array();

    $sanitized['enabled'] = isset($input['enabled']) ? '1' : '0';
    $sanitized['message'] = wp_kses_post($input['message'] ?? '');
    $sanitized['accept_text'] = sanitize_text_field($input['accept_text'] ?? 'Aceptar');
    $sanitized['reject_text'] = sanitize_text_field($input['reject_text'] ?? 'Rechazar');
    $sanitized['more_info_text'] = sanitize_text_field($input['more_info_text'] ?? 'Más información');
    $sanitized['privacy_url'] = esc_url_raw($input['privacy_url'] ?? '/privacidad');
    $sanitized['cookie_days'] = absint($input['cookie_days'] ?? 365);
    $sanitized['bg_color'] = sanitize_hex_color($input['bg_color'] ?? '#023a51');
    $sanitized['text_color'] = sanitize_hex_color($input['text_color'] ?? '#ffffff');
    $sanitized['btn_accept_bg'] = sanitize_hex_color($input['btn_accept_bg'] ?? '#008ed2');
    $sanitized['btn_accept_text'] = sanitize_hex_color($input['btn_accept_text'] ?? '#ffffff');
    $sanitized['btn_reject_bg'] = ($input['btn_reject_bg'] === 'transparent') ? 'transparent' : sanitize_hex_color($input['btn_reject_bg'] ?? 'transparent');
    $sanitized['btn_reject_text'] = sanitize_hex_color($input['btn_reject_text'] ?? '#e0ecf0');
    $sanitized['btn_reject_border'] = sanitize_hex_color($input['btn_reject_border'] ?? '#e0ecf0');
    $sanitized['link_color'] = sanitize_hex_color($input['link_color'] ?? '#008ed2');
    $sanitized['ga_tracking_id'] = sanitize_text_field($input['ga_tracking_id'] ?? '');

    return $sanitized;
}

/**
 * Página de administración del banner de cookies
 */
function ma_cookie_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = ma_cookie_get_options();
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-shield" style="font-size: 30px; margin-right: 10px;"></span> Configuración del Banner de Cookies</h1>

        <form method="post" action="options.php">
            <?php settings_fields('ma_cookie_banner_group'); ?>

            <div style="display: flex; gap: 30px; margin-top: 20px;">
                <!-- Columna izquierda: Configuración -->
                <div style="flex: 1; max-width: 600px;">

                    <!-- Sección: Estado -->
                    <div class="postbox" style="padding: 15px 20px;">
                        <h2 style="margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                            <span class="dashicons dashicons-admin-plugins"></span> Estado del Banner
                        </h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Activar Banner</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="ma_cookie_banner_options[enabled]" value="1" <?php checked($options['enabled'], '1'); ?>>
                                        Mostrar el banner de cookies a los visitantes
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Sección: Textos -->
                    <div class="postbox" style="padding: 15px 20px;">
                        <h2 style="margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                            <span class="dashicons dashicons-edit"></span> Textos del Banner
                        </h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="ma_message">Mensaje Principal</label></th>
                                <td>
                                    <textarea id="ma_message" name="ma_cookie_banner_options[message]" rows="4" class="large-text"><?php echo esc_textarea($options['message']); ?></textarea>
                                    <p class="description">Texto que se muestra a los visitantes sobre el uso de cookies.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_accept">Botón Aceptar</label></th>
                                <td>
                                    <input type="text" id="ma_accept" name="ma_cookie_banner_options[accept_text]" value="<?php echo esc_attr($options['accept_text']); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_reject">Botón Rechazar</label></th>
                                <td>
                                    <input type="text" id="ma_reject" name="ma_cookie_banner_options[reject_text]" value="<?php echo esc_attr($options['reject_text']); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_more_info">Enlace Más Información</label></th>
                                <td>
                                    <input type="text" id="ma_more_info" name="ma_cookie_banner_options[more_info_text]" value="<?php echo esc_attr($options['more_info_text']); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_privacy_url">URL Política de Privacidad</label></th>
                                <td>
                                    <input type="text" id="ma_privacy_url" name="ma_cookie_banner_options[privacy_url]" value="<?php echo esc_attr($options['privacy_url']); ?>" class="regular-text">
                                    <p class="description">Puede ser relativa (/privacidad) o absoluta (https://...)</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Sección: Colores -->
                    <div class="postbox" style="padding: 15px 20px;">
                        <h2 style="margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                            <span class="dashicons dashicons-art"></span> Colores y Apariencia
                        </h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="ma_bg_color">Fondo del Banner</label></th>
                                <td>
                                    <input type="color" id="ma_bg_color" name="ma_cookie_banner_options[bg_color]" value="<?php echo esc_attr($options['bg_color']); ?>">
                                    <code><?php echo esc_html($options['bg_color']); ?></code>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_text_color">Texto del Mensaje</label></th>
                                <td>
                                    <input type="color" id="ma_text_color" name="ma_cookie_banner_options[text_color]" value="<?php echo esc_attr($options['text_color']); ?>">
                                    <code><?php echo esc_html($options['text_color']); ?></code>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_link_color">Color del Enlace</label></th>
                                <td>
                                    <input type="color" id="ma_link_color" name="ma_cookie_banner_options[link_color]" value="<?php echo esc_attr($options['link_color']); ?>">
                                    <code><?php echo esc_html($options['link_color']); ?></code>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_btn_accept_bg">Botón Aceptar - Fondo</label></th>
                                <td>
                                    <input type="color" id="ma_btn_accept_bg" name="ma_cookie_banner_options[btn_accept_bg]" value="<?php echo esc_attr($options['btn_accept_bg']); ?>">
                                    <code><?php echo esc_html($options['btn_accept_bg']); ?></code>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_btn_accept_text">Botón Aceptar - Texto</label></th>
                                <td>
                                    <input type="color" id="ma_btn_accept_text" name="ma_cookie_banner_options[btn_accept_text]" value="<?php echo esc_attr($options['btn_accept_text']); ?>">
                                    <code><?php echo esc_html($options['btn_accept_text']); ?></code>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_btn_reject_text">Botón Rechazar - Texto</label></th>
                                <td>
                                    <input type="color" id="ma_btn_reject_text" name="ma_cookie_banner_options[btn_reject_text]" value="<?php echo esc_attr($options['btn_reject_text']); ?>">
                                    <code><?php echo esc_html($options['btn_reject_text']); ?></code>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_btn_reject_border">Botón Rechazar - Borde</label></th>
                                <td>
                                    <input type="color" id="ma_btn_reject_border" name="ma_cookie_banner_options[btn_reject_border]" value="<?php echo esc_attr($options['btn_reject_border']); ?>">
                                    <code><?php echo esc_html($options['btn_reject_border']); ?></code>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Sección: Configuración Técnica -->
                    <div class="postbox" style="padding: 15px 20px;">
                        <h2 style="margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                            <span class="dashicons dashicons-admin-tools"></span> Configuración Técnica
                        </h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="ma_cookie_days">Duración de la Cookie (días)</label></th>
                                <td>
                                    <input type="number" id="ma_cookie_days" name="ma_cookie_banner_options[cookie_days]" value="<?php echo esc_attr($options['cookie_days']); ?>" min="1" max="365" class="small-text">
                                    <p class="description">Días que se recordará la preferencia del usuario (máx. 365)</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="ma_ga_id">ID de Google Analytics</label></th>
                                <td>
                                    <input type="text" id="ma_ga_id" name="ma_cookie_banner_options[ga_tracking_id]" value="<?php echo esc_attr($options['ga_tracking_id']); ?>" class="regular-text" placeholder="G-XXXXXXXXXX">
                                    <p class="description">Para bloquear tracking cuando el usuario rechaza cookies</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <?php submit_button('Guardar Cambios', 'primary', 'submit', true, array('style' => 'font-size: 14px; padding: 8px 20px;')); ?>

                </div>

                <!-- Columna derecha: Vista previa -->
                <div style="flex: 1; max-width: 500px;">
                    <div class="postbox" style="padding: 15px 20px; position: sticky; top: 40px;">
                        <h2 style="margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                            <span class="dashicons dashicons-visibility"></span> Vista Previa
                        </h2>
                        <p class="description">Así se verá el banner en tu sitio:</p>

                        <div id="ma-cookie-preview" style="margin-top: 15px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.15);">
                            <div style="background-color: <?php echo esc_attr($options['bg_color']); ?>; padding: 16px 20px;">
                                <p style="color: <?php echo esc_attr($options['text_color']); ?>; font-size: 13px; line-height: 1.5; margin: 0 0 12px 0;">
                                    <?php echo esc_html(wp_trim_words($options['message'], 25, '...')); ?>
                                    <a href="#" style="color: <?php echo esc_attr($options['link_color']); ?>; text-decoration: underline;"><?php echo esc_html($options['more_info_text']); ?></a>
                                </p>
                                <div style="display: flex; gap: 10px;">
                                    <button type="button" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: 1px solid <?php echo esc_attr($options['btn_reject_border']); ?>; border-radius: 4px; background: transparent; color: <?php echo esc_attr($options['btn_reject_text']); ?>; cursor: pointer; text-transform: uppercase;">
                                        <?php echo esc_html($options['reject_text']); ?>
                                    </button>
                                    <button type="button" style="padding: 8px 16px; font-size: 12px; font-weight: 600; border: none; border-radius: 4px; background-color: <?php echo esc_attr($options['btn_accept_bg']); ?>; color: <?php echo esc_attr($options['btn_accept_text']); ?>; cursor: pointer; text-transform: uppercase;">
                                        <?php echo esc_html($options['accept_text']); ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 20px; padding: 15px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0; font-size: 13px;"><strong>Nota:</strong> Los cambios se reflejarán en el sitio después de guardar. Los usuarios que ya dieron su consentimiento no verán el banner hasta que expire la cookie.</p>
                        </div>

                        <div style="margin-top: 15px;">
                            <button type="button" class="button" onclick="if(confirm('¿Eliminar la cookie de prueba para ver el banner?')) { document.cookie = 'ma_cookie_consent=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'; alert('Cookie eliminada. Visita el sitio para ver el banner.'); }">
                                <span class="dashicons dashicons-update" style="margin-top: 4px;"></span> Resetear Cookie de Prueba
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Actualizar vista previa en tiempo real
        function updatePreview() {
            var preview = $('#ma-cookie-preview > div');
            preview.css('background-color', $('#ma_bg_color').val());
            preview.find('p').css('color', $('#ma_text_color').val());
            preview.find('a').css('color', $('#ma_link_color').val());
            preview.find('button:first').css({
                'color': $('#ma_btn_reject_text').val(),
                'border-color': $('#ma_btn_reject_border').val()
            });
            preview.find('button:last').css({
                'background-color': $('#ma_btn_accept_bg').val(),
                'color': $('#ma_btn_accept_text').val()
            });
        }

        $('input[type="color"]').on('input', function() {
            $(this).next('code').text($(this).val());
            updatePreview();
        });

        // Actualizar textos de botones en preview
        $('#ma_accept').on('input', function() {
            $('#ma-cookie-preview button:last').text($(this).val());
        });
        $('#ma_reject').on('input', function() {
            $('#ma-cookie-preview button:first').text($(this).val());
        });
        $('#ma_more_info').on('input', function() {
            $('#ma-cookie-preview a').text($(this).val());
        });
    });
    </script>
    <?php
}

/**
 * Registrar estilos y scripts del banner de cookies
 */
function multianalityca_cookie_banner_assets() {
    $options = ma_cookie_get_options();

    // No cargar si está deshabilitado o hay consentimiento
    if ($options['enabled'] !== '1' || isset($_COOKIE['ma_cookie_consent'])) {
        return;
    }

    wp_enqueue_style(
        'ma-cookie-banner-css',
        get_stylesheet_directory_uri() . '/assets/css/cookie-banner.css',
        array(),
        '1.1.0'
    );
    wp_enqueue_script(
        'ma-cookie-banner-js',
        get_stylesheet_directory_uri() . '/assets/js/cookie-banner.js',
        array(),
        '1.1.0',
        true
    );

    // Pasar configuración al JavaScript
    wp_localize_script('ma-cookie-banner-js', 'maCookieBanner', array(
        'cookieDays' => intval($options['cookie_days']),
        'gaTrackingId' => $options['ga_tracking_id'],
    ));

    // Agregar estilos dinámicos basados en las opciones
    $custom_css = "
        .ma-cookie-banner {
            --ma-bg-color: {$options['bg_color']};
            --ma-text-color: {$options['text_color']};
            --ma-link-color: {$options['link_color']};
            --ma-btn-accept-bg: {$options['btn_accept_bg']};
            --ma-btn-accept-text: {$options['btn_accept_text']};
            --ma-btn-reject-bg: {$options['btn_reject_bg']};
            --ma-btn-reject-text: {$options['btn_reject_text']};
            --ma-btn-reject-border: {$options['btn_reject_border']};
        }
    ";
    wp_add_inline_style('ma-cookie-banner-css', $custom_css);
}
add_action('wp_enqueue_scripts', 'multianalityca_cookie_banner_assets');

/**
 * Mostrar el HTML del banner
 */
function multianalityca_cookie_banner_html() {
    $options = ma_cookie_get_options();

    // No mostrar si está deshabilitado o hay consentimiento
    if ($options['enabled'] !== '1' || isset($_COOKIE['ma_cookie_consent'])) {
        return;
    }

    $privacy_url = $options['privacy_url'];
    if (strpos($privacy_url, 'http') !== 0) {
        $privacy_url = home_url($privacy_url);
    }
    ?>
    <div id="ma-cookie-banner" class="ma-cookie-banner" role="dialog" aria-label="Aviso de cookies" aria-describedby="ma-cookie-message">
        <div class="ma-cookie-banner__container">
            <p id="ma-cookie-message" class="ma-cookie-banner__message">
                <?php echo wp_kses_post($options['message']); ?>
                <a href="<?php echo esc_url($privacy_url); ?>" class="ma-cookie-banner__link" target="_blank" rel="noopener"><?php echo esc_html($options['more_info_text']); ?></a>
            </p>
            <div class="ma-cookie-banner__buttons">
                <button type="button" id="ma-cookie-reject" class="ma-cookie-banner__btn ma-cookie-banner__btn--reject">
                    <?php echo esc_html($options['reject_text']); ?>
                </button>
                <button type="button" id="ma-cookie-accept" class="ma-cookie-banner__btn ma-cookie-banner__btn--accept">
                    <?php echo esc_html($options['accept_text']); ?>
                </button>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'multianalityca_cookie_banner_html', 999);

/**
 * Bloquear scripts de tracking si no hay consentimiento
 */
function multianalityca_block_tracking_scripts() {
    $options = ma_cookie_get_options();

    // Si rechazó las cookies, bloquear scripts de tracking
    if (isset($_COOKIE['ma_cookie_consent']) && $_COOKIE['ma_cookie_consent'] === 'rejected') {
        $ga_id = esc_js($options['ga_tracking_id']);
        if (!empty($ga_id)) {
            echo "<script>window['ga-disable-{$ga_id}'] = true;</script>\n";
        }
    }
}
add_action('wp_head', 'multianalityca_block_tracking_scripts', 1);

/**
 * =====================================================
 * SEO - Schema Markup para MultiAnalityca
 * =====================================================
 */

/**
 * Schema LocalBusiness/MedicalBusiness para la página de inicio
 * Mejora la visibilidad en búsquedas locales y de servicios médicos
 */
function multianalityca_schema_organization() {
    if (!is_front_page()) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'MedicalBusiness',
        '@id' => home_url('/#organization'),
        'name' => 'MultiAnalityca S.A.',
        'alternateName' => 'MultiAnalityca',
        'description' => 'Laboratorio de análisis clínicos, bromatológicos y microbiológicos. Servicios de análisis de alimentos, registro sanitario, control de calidad y BPM en Ecuador.',
        'url' => home_url('/'),
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => get_stylesheet_directory_uri() . '/assets/images/logo.png',
            'width' => 300,
            'height' => 100
        ),
        'image' => home_url('/wp-content/uploads/multianalityca-laboratorio.jpg'),
        'telephone' => '+593 95 885 0928',
        'email' => 'info@multianalityca.com',
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => 'Jorge Erazo N50-109 y Cristóbal Sandoval',
            'addressLocality' => 'Quito',
            'addressRegion' => 'Pichincha',
            'postalCode' => '170150',
            'addressCountry' => 'EC'
        ),
        'geo' => array(
            '@type' => 'GeoCoordinates',
            'latitude' => '-0.1807',
            'longitude' => '-78.4678'
        ),
        'openingHoursSpecification' => array(
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
                'opens' => '08:00',
                'closes' => '17:00'
            )
        ),
        'priceRange' => '$$',
        'currenciesAccepted' => 'USD',
        'paymentAccepted' => 'Cash, Credit Card, Bank Transfer',
        'areaServed' => array(
            '@type' => 'Country',
            'name' => 'Ecuador'
        ),
        'hasOfferCatalog' => array(
            '@type' => 'OfferCatalog',
            'name' => 'Servicios de Análisis de Laboratorio',
            'itemListElement' => array(
                array(
                    '@type' => 'Offer',
                    'itemOffered' => array(
                        '@type' => 'Service',
                        'name' => 'Análisis Bromatológico de Alimentos',
                        'description' => 'Análisis de composición nutricional, aditivos y contaminantes en alimentos'
                    )
                ),
                array(
                    '@type' => 'Offer',
                    'itemOffered' => array(
                        '@type' => 'Service',
                        'name' => 'Registro Sanitario',
                        'description' => 'Trámite y análisis para obtención de registro sanitario de alimentos'
                    )
                ),
                array(
                    '@type' => 'Offer',
                    'itemOffered' => array(
                        '@type' => 'Service',
                        'name' => 'Análisis Microbiológico',
                        'description' => 'Análisis de microorganismos en alimentos, cosméticos y productos varios'
                    )
                ),
                array(
                    '@type' => 'Offer',
                    'itemOffered' => array(
                        '@type' => 'Service',
                        'name' => 'Control de Calidad BPM',
                        'description' => 'Consultoría y análisis para Buenas Prácticas de Manufactura'
                    )
                )
            )
        ),
        'sameAs' => array(
            'https://www.facebook.com/multianalityca',
            'https://www.instagram.com/multianalityca'
        )
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'multianalityca_schema_organization', 2);

/**
 * Schema BreadcrumbList para navegación estructurada
 * Mejora la visualización en resultados de búsqueda
 */
function multianalityca_schema_breadcrumbs() {
    if (is_front_page() || is_admin()) {
        return;
    }

    global $post;
    $breadcrumbs = array();
    $position = 1;

    // Inicio
    $breadcrumbs[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Inicio',
        'item' => home_url('/')
    );

    // Página actual
    if (is_singular('page') && $post) {
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title($post),
            'item' => get_permalink($post)
        );
    } elseif (is_singular('post') && $post) {
        // Para posts, agregar categoría intermedia
        $categories = get_the_category($post->ID);
        if (!empty($categories)) {
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $categories[0]->name,
                'item' => get_category_link($categories[0]->term_id)
            );
        }
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title($post),
            'item' => get_permalink($post)
        );
    }

    if (count($breadcrumbs) > 1) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'multianalityca_schema_breadcrumbs', 3);

/**
 * Schema Service para páginas de servicios específicos
 */
function multianalityca_schema_service_pages() {
    if (!is_singular('page')) {
        return;
    }

    global $post;
    $slug = $post->post_name;

    // Mapeo de slugs a datos de servicio
    $services = array(
        'registro-sanitario' => array(
            'name' => 'Análisis para Registro Sanitario',
            'description' => 'Realizamos todos los análisis requeridos por ARCSA para la obtención del registro sanitario de alimentos, bebidas, cosméticos y productos naturales en Ecuador.',
            'serviceType' => 'Análisis de Laboratorio'
        ),
        'bpm' => array(
            'name' => 'Buenas Prácticas de Manufactura (BPM)',
            'description' => 'Consultoría y análisis de laboratorio para implementación y certificación de Buenas Prácticas de Manufactura en la industria alimentaria.',
            'serviceType' => 'Consultoría y Análisis'
        ),
        'calidad-bpm' => array(
            'name' => 'Control de Calidad para BPM',
            'description' => 'Análisis de control de calidad microbiológico y fisicoquímico para cumplimiento de normativas BPM.',
            'serviceType' => 'Control de Calidad'
        ),
        'servicio-de-laboratorio' => array(
            'name' => 'Servicios de Laboratorio',
            'description' => 'Servicios completos de análisis de laboratorio: bromatológico, microbiológico, fisicoquímico y toxicológico.',
            'serviceType' => 'Análisis de Laboratorio'
        ),
        'determinacion-principio-activo' => array(
            'name' => 'Determinación de Principio Activo',
            'description' => 'Análisis cuantitativo y cualitativo de principios activos en productos farmacéuticos y naturales.',
            'serviceType' => 'Análisis Farmacéutico'
        ),
        'carnes' => array(
            'name' => 'Análisis de Carnes y Embutidos',
            'description' => 'Análisis microbiológico y bromatológico especializado para carnes, embutidos y productos cárnicos.',
            'serviceType' => 'Análisis de Alimentos'
        ),
        'industrias' => array(
            'name' => 'Análisis para Industrias',
            'description' => 'Servicios de análisis de laboratorio para la industria alimentaria, cosmética, farmacéutica y veterinaria.',
            'serviceType' => 'Análisis Industrial'
        )
    );

    if (!isset($services[$slug])) {
        return;
    }

    $service = $services[$slug];

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service['name'],
        'description' => $service['description'],
        'serviceType' => $service['serviceType'],
        'url' => get_permalink($post),
        'provider' => array(
            '@type' => 'MedicalBusiness',
            '@id' => home_url('/#organization'),
            'name' => 'MultiAnalityca S.A.'
        ),
        'areaServed' => array(
            '@type' => 'Country',
            'name' => 'Ecuador'
        ),
        'availableChannel' => array(
            '@type' => 'ServiceChannel',
            'serviceUrl' => home_url('/contacto'),
            'servicePhone' => '+593 95 885 0928',
            'availableLanguage' => 'Spanish'
        )
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'multianalityca_schema_service_pages', 4);

/**
 * Schema ContactPage para página de contacto
 */
function multianalityca_schema_contact_page() {
    if (!is_page('contacto')) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'ContactPage',
        'name' => 'Contacto - MultiAnalityca',
        'description' => 'Contáctenos para cotizaciones de análisis de laboratorio, registro sanitario y servicios de control de calidad.',
        'url' => home_url('/contacto'),
        'mainEntity' => array(
            '@type' => 'MedicalBusiness',
            '@id' => home_url('/#organization')
        )
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
add_action('wp_head', 'multianalityca_schema_contact_page', 5);

/**
 * Schema AboutPage para página Nosotros
 */
function multianalityca_schema_about_page() {
    if (!is_page('nosotros')) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'AboutPage',
        'name' => 'Sobre Nosotros - MultiAnalityca',
        'description' => 'Conozca a MultiAnalityca, laboratorio líder en análisis clínicos, bromatológicos y microbiológicos en Ecuador.',
        'url' => home_url('/nosotros'),
        'mainEntity' => array(
            '@type' => 'MedicalBusiness',
            '@id' => home_url('/#organization')
        )
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
add_action('wp_head', 'multianalityca_schema_about_page', 6);

/**
 * =====================================================
 * OPTIMIZACIONES DE RENDIMIENTO - PageSpeed 90+
 * =====================================================
 */

/**
 * 1. Preconnect y DNS Prefetch para recursos externos
 * Reduce latencia de conexión a servidores de terceros
 */
function multianalityca_resource_hints($hints, $relation_type) {
    if ('preconnect' === $relation_type) {
        // Google Fonts
        $hints[] = array(
            'href' => 'https://fonts.googleapis.com',
            'crossorigin' => 'anonymous',
        );
        $hints[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        );
        // Google Analytics
        $hints[] = array(
            'href' => 'https://www.googletagmanager.com',
        );
        $hints[] = array(
            'href' => 'https://www.google-analytics.com',
        );
        // CDN de WordPress
        $hints[] = array(
            'href' => 'https://cdnjs.cloudflare.com',
            'crossorigin' => 'anonymous',
        );
    }

    if ('dns-prefetch' === $relation_type) {
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = 'https://fonts.gstatic.com';
        $hints[] = 'https://www.googletagmanager.com';
        $hints[] = 'https://www.google-analytics.com';
        $hints[] = 'https://static.hotjar.com';
    }

    return $hints;
}
add_filter('wp_resource_hints', 'multianalityca_resource_hints', 10, 2);

/**
 * 2. Deshabilitar emojis de WordPress (mejora rendimiento)
 */
function multianalityca_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // Remover DNS prefetch para emojis
    add_filter('emoji_svg_url', '__return_false');
}
add_action('init', 'multianalityca_disable_emojis');

// Remover emoji prefetch
function multianalityca_disable_emojis_dns_prefetch($urls, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        $urls = array_filter($urls, function($url) {
            return strpos($url, 'https://s.w.org/images/core/emoji/') === false;
        });
    }
    return $urls;
}
add_filter('wp_resource_hints', 'multianalityca_disable_emojis_dns_prefetch', 10, 2);

/**
 * 3. Deshabilitar embeds de WordPress (si no se necesitan)
 */
function multianalityca_disable_embeds_code_init() {
    remove_action('rest_api_init', 'wp_oembed_register_route');
    add_filter('embed_oembed_discover', '__return_false');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    add_filter('tiny_mce_plugins', function($plugins) {
        return array_diff($plugins, array('wpembed'));
    });
    add_filter('rewrite_rules_array', function($rules) {
        foreach ($rules as $rule => $rewrite) {
            if (false !== strpos($rewrite, 'embed=true')) {
                unset($rules[$rule]);
            }
        }
        return $rules;
    });
}
add_action('init', 'multianalityca_disable_embeds_code_init', 9999);

/**
 * 4. Remover query strings de recursos estáticos
 */
function multianalityca_remove_script_version($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'multianalityca_remove_script_version', 15, 1);
add_filter('style_loader_src', 'multianalityca_remove_script_version', 15, 1);

/**
 * 5. Optimizar Heartbeat API (reduce requests en admin)
 */
function multianalityca_heartbeat_settings($settings) {
    $settings['interval'] = 60; // Reducir frecuencia a 60 segundos
    return $settings;
}
add_filter('heartbeat_settings', 'multianalityca_heartbeat_settings');

// Deshabilitar heartbeat en frontend completamente
function multianalityca_deregister_heartbeat() {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}
add_action('init', 'multianalityca_deregister_heartbeat', 1);

/**
 * 6. Agregar loading="lazy" y decoding="async" a imágenes
 */
function multianalityca_lazy_load_images($content) {
    if (is_admin() || is_feed() || is_preview()) {
        return $content;
    }

    // Agregar loading="lazy" y decoding="async" a imágenes que no lo tengan
    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        function($matches) {
            $img = $matches[0];

            // No agregar lazy a imágenes que ya lo tienen
            if (strpos($img, 'loading=') !== false) {
                return $img;
            }

            // No agregar lazy a imágenes above the fold (con clase específica)
            if (strpos($img, 'no-lazy') !== false || strpos($img, 'skip-lazy') !== false) {
                return $img;
            }

            // Agregar atributos de optimización
            $img = str_replace('<img', '<img loading="lazy" decoding="async"', $img);

            return $img;
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'multianalityca_lazy_load_images', 99);
add_filter('post_thumbnail_html', 'multianalityca_lazy_load_images', 99);
add_filter('widget_text', 'multianalityca_lazy_load_images', 99);

/**
 * 7. Agregar fetchpriority="high" a imagen LCP (Largest Contentful Paint)
 */
function multianalityca_optimize_lcp_image($content) {
    if (is_admin() || !is_singular()) {
        return $content;
    }

    // Solo optimizar la primera imagen grande (probable LCP)
    $count = 0;
    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        function($matches) use (&$count) {
            if ($count > 0) {
                return $matches[0];
            }

            $img = $matches[0];

            // Si la imagen tiene class que indica hero/banner, agregar fetchpriority
            if (preg_match('/class=["\'][^"\']*(?:hero|banner|featured|main|slider)[^"\']*["\']/i', $img) ||
                $count === 0) {
                // Primera imagen: quitar lazy y agregar fetchpriority high
                $img = str_replace(' loading="lazy"', '', $img);
                $img = str_replace(' decoding="async"', '', $img);
                if (strpos($img, 'fetchpriority') === false) {
                    $img = str_replace('<img', '<img fetchpriority="high"', $img);
                }
                $count++;
            }

            return $img;
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'multianalityca_optimize_lcp_image', 100);

/**
 * 8. Deshabilitar Gutenberg Block Library CSS si no se usa
 */
function multianalityca_remove_block_library_css() {
    // Solo remover si no se usa el editor de bloques en el contenido
    if (!is_admin()) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('wc-blocks-style'); // WooCommerce blocks
        wp_dequeue_style('global-styles'); // Estilos globales de bloques
    }
}
add_action('wp_enqueue_scripts', 'multianalityca_remove_block_library_css', 100);

/**
 * 9. Preload de recursos críticos
 */
function multianalityca_preload_critical_resources() {
    // Preload del CSS principal del tema
    $theme_css = get_stylesheet_directory_uri() . '/style.css';
    echo '<link rel="preload" href="' . esc_url($theme_css) . '" as="style">' . "\n";

    // Preload de fuentes críticas (Avada usa fuentes personalizadas)
    // Detectar si hay fuentes específicas y precargarlas
    ?>
    <link rel="preload" href="<?php echo esc_url(get_template_directory_uri()); ?>/includes/lib/assets/fonts/icomoon/awb-icons.woff2" as="font" type="font/woff2" crossorigin>
    <?php
}
add_action('wp_head', 'multianalityca_preload_critical_resources', 1);

/**
 * 10. Defer scripts no críticos
 */
function multianalityca_defer_scripts($tag, $handle, $src) {
    // Lista de scripts que NO deben ser diferidos
    $no_defer = array(
        'jquery',
        'jquery-core',
        'jquery-migrate',
        'avada-header',
    );

    if (in_array($handle, $no_defer)) {
        return $tag;
    }

    // Scripts de terceros que deben ser diferidos
    $defer_handles = array(
        'hotjar',
        'google-analytics',
        'gtag',
        'ga',
        'facebook-pixel',
        'hj-tracking',
    );

    // Diferir scripts de tracking y analytics
    if (in_array($handle, $defer_handles) ||
        strpos($src, 'hotjar') !== false ||
        strpos($src, 'analytics') !== false ||
        strpos($src, 'gtag') !== false) {

        if (strpos($tag, 'defer') === false && strpos($tag, 'async') === false) {
            $tag = str_replace(' src', ' defer src', $tag);
        }
    }

    return $tag;
}
add_filter('script_loader_tag', 'multianalityca_defer_scripts', 10, 3);

/**
 * 11. Optimizar jQuery (mover al footer si es posible)
 */
function multianalityca_move_jquery_to_footer() {
    if (!is_admin()) {
        wp_scripts()->add_data('jquery', 'group', 1);
        wp_scripts()->add_data('jquery-core', 'group', 1);
        wp_scripts()->add_data('jquery-migrate', 'group', 1);
    }
}
add_action('wp_enqueue_scripts', 'multianalityca_move_jquery_to_footer');

/**
 * 12. Limpiar wp_head de elementos innecesarios
 */
function multianalityca_cleanup_head() {
    // Remover versión de WordPress
    remove_action('wp_head', 'wp_generator');

    // Remover links de feeds RSD y wlwmanifest
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');

    // Remover shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');

    // Remover REST API link
    remove_action('wp_head', 'rest_output_link_wp_head');

    // Remover links de post anterior/siguiente
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
}
add_action('init', 'multianalityca_cleanup_head');

/**
 * 13. Optimizar Contact Form 7 - Solo cargar donde se necesita
 */
function multianalityca_dequeue_cf7_scripts() {
    // Solo cargar CF7 en páginas de contacto
    if (!is_page(array('contacto', 'contact', 'contactenos'))) {
        wp_dequeue_style('contact-form-7');
        wp_dequeue_script('contact-form-7');
        wp_dequeue_script('wpcf7-recaptcha');
    }
}
add_action('wp_enqueue_scripts', 'multianalityca_dequeue_cf7_scripts', 99);

/**
 * 14. Optimizar Revolution Slider - Solo cargar donde se usa
 */
function multianalityca_optimize_revslider() {
    if (!is_front_page() && !is_home()) {
        // Remover RevSlider de páginas que no lo usan
        wp_dequeue_style('rs-plugin-settings');
        wp_dequeue_script('tp-tools');
        wp_dequeue_script('revmin');
    }
}
add_action('wp_enqueue_scripts', 'multianalityca_optimize_revslider', 99);

/**
 * 15. Agregar Critical CSS inline para above-the-fold
 */
function multianalityca_critical_css() {
    if (!is_front_page() && !is_singular()) {
        return;
    }
    ?>
    <style id="critical-css">
    /* Critical CSS para contenido above-the-fold */
    body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif}
    .fusion-header-wrapper{position:relative}
    .fusion-logo{display:block}
    .fusion-logo img{max-height:100%;width:auto}
    .fusion-main-menu{display:flex;align-items:center}
    .fusion-header{background-color:#fff}
    .fusion-slider-wrapper{position:relative;overflow:hidden}
    img{max-width:100%;height:auto}
    .fusion-page-title-bar{padding:20px 0;background-size:cover}
    .avada-page-titlebar-wrapper{text-align:center}
    </style>
    <?php
}
add_action('wp_head', 'multianalityca_critical_css', 2);

/**
 * 16. Optimizar carga de fuentes
 */
function multianalityca_optimize_fonts() {
    // Agregar font-display: swap a Google Fonts
    add_filter('style_loader_tag', function($html, $handle) {
        if (strpos($handle, 'google-fonts') !== false || strpos($handle, 'font') !== false) {
            // Agregar display=swap si es Google Fonts
            $html = str_replace("googleapis.com/css?", "googleapis.com/css?display=swap&", $html);
            $html = str_replace("googleapis.com/css2?", "googleapis.com/css2?display=swap&", $html);
        }
        return $html;
    }, 10, 2);
}
add_action('wp_enqueue_scripts', 'multianalityca_optimize_fonts');

/**
 * 17. Comprimir HTML output
 */
class Multianalityca_HTML_Compression {
    protected $compress_css = true;
    protected $compress_js = true;
    protected $info_comment = false;
    protected $remove_comments = true;
    protected $html;

    public function __construct($html) {
        if (!empty($html)) {
            $this->parseHTML($html);
        }
    }

    public function __toString() {
        return $this->html;
    }

    protected function minifyHTML($html) {
        $pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>(googletag|gtag|ga|dataLayer)\([^)]+\)|[^<]*)/si';
        preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
        $overriding = false;
        $raw_tag = false;
        $html = '';

        foreach ($matches as $token) {
            $tag = (isset($token['tag'])) ? strtolower($token['tag']) : null;
            $content = $token[0];

            if (is_null($tag)) {
                if (!empty($token['script'])) {
                    $strip = $this->compress_js;
                } else if (!empty($token['style'])) {
                    $strip = $this->compress_css;
                } else if ($content == '<!--wp-hierarchical-json-->') {
                    $strip = true;
                } else if ($this->remove_comments) {
                    if (!empty($token['comment'])) {
                        // Mantener comentarios condicionales IE
                        if (substr($content, 0, 12) === '<!--[if' || substr($content, 0, 5) === '<!--!') {
                            $strip = false;
                        } else {
                            $strip = true;
                        }
                    } else {
                        $strip = false;
                    }
                } else {
                    $strip = false;
                }

                if ($strip) {
                    $content = '';
                }
            } else {
                if ($tag == 'pre' || $tag == 'textarea' || $tag == 'script') {
                    $raw_tag = $tag;
                } else if ($tag == '/pre' || $tag == '/textarea' || $tag == '/script') {
                    $raw_tag = false;
                } else {
                    if ($raw_tag || $overriding) {
                        // No comprimir
                    } else {
                        $content = preg_replace('/(\s+)(\w+)=/', ' $2=', $content);
                        $content = preg_replace('/\s+/', ' ', $content);
                    }
                }
            }
            $html .= $content;
        }
        return $html;
    }

    protected function parseHTML($html) {
        $this->html = $this->minifyHTML($html);
    }
}

function multianalityca_html_compression_start() {
    if (!is_admin() && !is_feed() && !is_preview() && !defined('DOING_AJAX')) {
        ob_start(function($html) {
            if (strlen($html) > 255) {
                return new Multianalityca_HTML_Compression($html);
            }
            return $html;
        });
    }
}
// Comentado por defecto - puede causar conflictos con algunos plugins
// add_action('template_redirect', 'multianalityca_html_compression_start', -1);

/**
 * 18. Deshabilitar Self Pingbacks
 */
function multianalityca_no_self_ping(&$links) {
    $home = get_option('home');
    foreach ($links as $l => $link) {
        if (0 === strpos($link, $home)) {
            unset($links[$l]);
        }
    }
}
add_action('pre_ping', 'multianalityca_no_self_ping');

/**
 * 19. Limitar revisiones de posts
 */
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 5);
}

/**
 * 20. Añadir atributos async/defer dinámicamente según el script
 */
function multianalityca_add_async_defer_attributes($tag, $handle) {
    // Scripts que deben tener async
    $async_scripts = array('google-analytics', 'gtag', 'ga', 'hotjar');

    // Scripts que deben tener defer
    $defer_scripts = array('comment-reply', 'wp-embed');

    foreach ($async_scripts as $async_script) {
        if (strpos($handle, $async_script) !== false) {
            return str_replace(' src', ' async src', $tag);
        }
    }

    foreach ($defer_scripts as $defer_script) {
        if ($handle === $defer_script) {
            return str_replace(' src', ' defer src', $tag);
        }
    }

    return $tag;
}
add_filter('script_loader_tag', 'multianalityca_add_async_defer_attributes', 10, 2);

/**
 * =====================================================
 * OPTIMIZACIONES WP ROCKET AVANZADAS
 * =====================================================
 */

/**
 * 21. Habilitar minificación de CSS en WP Rocket
 */
add_filter('pre_get_rocket_option_minify_css', '__return_true');
add_filter('pre_get_rocket_option_minify_concatenate_css', '__return_true');

/**
 * 22. Habilitar minificación de JS en WP Rocket
 */
add_filter('pre_get_rocket_option_minify_js', '__return_true');
add_filter('pre_get_rocket_option_minify_concatenate_js', '__return_true');

/**
 * 23. Habilitar lazy load de imágenes en WP Rocket
 */
add_filter('pre_get_rocket_option_lazyload', '__return_true');
add_filter('pre_get_rocket_option_lazyload_iframes', '__return_true');
add_filter('pre_get_rocket_option_lazyload_youtube', '__return_true');

/**
 * 24. Habilitar preload de fuentes en WP Rocket
 */
add_filter('pre_get_rocket_option_preload_fonts', '__return_true');

/**
 * 25. Excluir scripts críticos de la combinación
 */
function multianalityca_exclude_js_from_combine($excluded_js) {
    $excluded_js[] = '/jquery(-migrate)?\.min\.js';
    $excluded_js[] = '/jquery\.js';
    $excluded_js[] = 'js/jquery/jquery.js';
    return $excluded_js;
}
add_filter('rocket_exclude_js', 'multianalityca_exclude_js_from_combine');

/**
 * 26. Habilitar Remove Unused CSS (RUCSS) - Elimina CSS no utilizado
 */
add_filter('pre_get_rocket_option_remove_unused_css', '__return_true');

/**
 * 27. Delay JavaScript Execution - Retrasa scripts no críticos
 */
add_filter('pre_get_rocket_option_delay_js', '__return_true');

/**
 * 28. Scripts a retrasar (delay) para mejor rendimiento
 */
function multianalityca_delay_js_scripts($scripts) {
    $scripts[] = '/jquery(-migrate)?\.min\.js';
    $scripts[] = 'gtag';
    $scripts[] = 'google-analytics';
    $scripts[] = 'googletagmanager';
    $scripts[] = 'hotjar';
    $scripts[] = 'facebook';
    $scripts[] = 'fbevents';
    $scripts[] = 'hj\(';
    $scripts[] = 'leadin';
    $scripts[] = 'hubspot';
    return $scripts;
}
add_filter('rocket_delay_js_scripts', 'multianalityca_delay_js_scripts');

/**
 * 29. Preload links - Precargar páginas al hover
 */
add_filter('pre_get_rocket_option_preload_links', '__return_true');

/**
 * 30. Optimización de base de datos automática
 */
function multianalityca_schedule_db_optimization() {
    if (!wp_next_scheduled('multianalityca_weekly_db_optimization')) {
        wp_schedule_event(time(), 'weekly', 'multianalityca_weekly_db_optimization');
    }
}
add_action('wp', 'multianalityca_schedule_db_optimization');

function multianalityca_optimize_database() {
    global $wpdb;

    // Limpiar revisiones antiguas (más de 30 días)
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'revision' AND post_date < DATE_SUB(NOW(), INTERVAL 30 DAY)");

    // Limpiar auto-drafts
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_status = 'auto-draft' AND post_date < DATE_SUB(NOW(), INTERVAL 7 DAY)");

    // Limpiar posts en papelera
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_status = 'trash' AND post_date < DATE_SUB(NOW(), INTERVAL 30 DAY)");

    // Limpiar comentarios spam
    $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_approved = 'spam'");

    // Limpiar comentarios en papelera
    $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_approved = 'trash'");

    // Limpiar transients expirados
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%' AND option_value < UNIX_TIMESTAMP()");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%' AND option_name NOT LIKE '_transient_timeout_%' AND option_name NOT IN (SELECT CONCAT('_transient_', SUBSTRING(option_name, 20)) FROM (SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%' AND option_value > UNIX_TIMESTAMP()) AS valid_transients)");

    // Optimizar tablas
    $tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}%'");
    foreach ($tables as $table) {
        $wpdb->query("OPTIMIZE TABLE {$table}");
    }
}
add_action('multianalityca_weekly_db_optimization', 'multianalityca_optimize_database');

/**
 * 31. Desactivar XML-RPC si no se usa (mejora seguridad y rendimiento)
 */
add_filter('xmlrpc_enabled', '__return_false');

// Remover header pingback
function multianalityca_remove_x_pingback($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'multianalityca_remove_x_pingback');

/**
 * 32. Optimizar WooCommerce si está instalado
 */
function multianalityca_optimize_woocommerce() {
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Deshabilitar widgets de WooCommerce en páginas que no lo necesitan
    if (!is_woocommerce() && !is_cart() && !is_checkout()) {
        wp_dequeue_style('woocommerce-general');
        wp_dequeue_style('woocommerce-layout');
        wp_dequeue_style('woocommerce-smallscreen');
        wp_dequeue_script('wc-cart-fragments');
    }
}
add_action('wp_enqueue_scripts', 'multianalityca_optimize_woocommerce', 99);

/**
 * 33. Optimizar Elementor
 */
function multianalityca_optimize_elementor() {
    // Desactivar Google Fonts de Elementor si no se necesitan
    add_filter('elementor/frontend/print_google_fonts', '__return_false');

    // Usar font-display: swap en Elementor
    add_filter('elementor/fonts/additional_fonts', function($fonts) {
        foreach ($fonts as $key => $font) {
            if (is_array($font)) {
                $fonts[$key]['font-display'] = 'swap';
            }
        }
        return $fonts;
    });
}
add_action('init', 'multianalityca_optimize_elementor');

/**
 * 34. Optimizar imágenes SVG
 */
function multianalityca_optimize_svg_upload($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'multianalityca_optimize_svg_upload');

/**
 * 35. Agregar dimensiones a imágenes sin ellas (mejora CLS)
 */
function multianalityca_add_image_dimensions($content) {
    if (is_admin() || is_feed()) {
        return $content;
    }

    // Buscar imágenes sin width/height
    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        function($matches) {
            $img = $matches[0];

            // Si ya tiene width y height, no modificar
            if (preg_match('/\swidth=/i', $img) && preg_match('/\sheight=/i', $img)) {
                return $img;
            }

            // Intentar obtener dimensiones del src
            if (preg_match('/src=["\']([^"\']+)["\']/i', $img, $src_match)) {
                $src = $src_match[1];

                // Solo procesar imágenes locales
                if (strpos($src, home_url()) !== false || strpos($src, '/') === 0) {
                    $upload_dir = wp_upload_dir();
                    $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $src);

                    if (file_exists($file_path)) {
                        $size = @getimagesize($file_path);
                        if ($size) {
                            $width = $size[0];
                            $height = $size[1];

                            if (!preg_match('/\swidth=/i', $img)) {
                                $img = str_replace('<img', '<img width="' . $width . '"', $img);
                            }
                            if (!preg_match('/\sheight=/i', $img)) {
                                $img = str_replace('<img', '<img height="' . $height . '"', $img);
                            }
                        }
                    }
                }
            }

            return $img;
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'multianalityca_add_image_dimensions', 98);

/**
 * 36. Preload LCP image automáticamente
 */
function multianalityca_preload_lcp_image() {
    if (is_front_page()) {
        // Detectar imagen hero/slider principal
        // Ajustar esta URL según la imagen principal del sitio
        $lcp_images = array(
            get_template_directory_uri() . '/assets/images/logo.png',
        );

        foreach ($lcp_images as $image) {
            if (!empty($image)) {
                echo '<link rel="preload" as="image" href="' . esc_url($image) . '" fetchpriority="high">' . "\n";
            }
        }
    }
}
add_action('wp_head', 'multianalityca_preload_lcp_image', 1);

/**
 * 37. Deshabilitar dashboard widgets innecesarios
 */
function multianalityca_remove_dashboard_widgets() {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'multianalityca_remove_dashboard_widgets', 999);

/**
 * 38. Optimizar Admin AJAX
 */
function multianalityca_optimize_admin_ajax() {
    // Reducir frecuencia de auto-save
    if (!defined('AUTOSAVE_INTERVAL')) {
        define('AUTOSAVE_INTERVAL', 120); // 2 minutos en lugar de 60 segundos
    }
}
add_action('admin_init', 'multianalityca_optimize_admin_ajax');

/**
 * 39. Minificar CSS inline
 */
function multianalityca_minify_inline_css($css) {
    // Remover comentarios
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // Remover espacios y líneas
    $css = str_replace(array("\r\n", "\r", "\n", "\t"), '', $css);
    // Remover espacios múltiples
    $css = preg_replace('/\s+/', ' ', $css);
    // Remover espacios antes y después de caracteres especiales
    $css = preg_replace('/\s*([\{\}\[\]\(\);:,>~+])\s*/', '$1', $css);

    return trim($css);
}

/**
 * 40. Implementar lazy load para videos e iframes
 */
function multianalityca_lazy_load_videos($content) {
    if (is_admin() || is_feed()) {
        return $content;
    }

    // Lazy load para iframes (YouTube, Vimeo, etc.)
    $content = preg_replace_callback(
        '/<iframe([^>]+)>/i',
        function($matches) {
            $iframe = $matches[0];

            if (strpos($iframe, 'loading=') !== false) {
                return $iframe;
            }

            return str_replace('<iframe', '<iframe loading="lazy"', $iframe);
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'multianalityca_lazy_load_videos', 99);
add_filter('embed_oembed_html', 'multianalityca_lazy_load_videos', 99);

/**
 * =====================================================
 * PROTECCIÓN CONTRA BOTS MALICIOSOS - Rate Limiting 404
 * =====================================================
 */

/**
 * 41. Sistema de Rate Limiting para errores 404
 * Bloquea IPs que generen demasiados errores 404 en poco tiempo
 */
class Multianalityca_Bot_Protection {

    private static $instance = null;
    private $max_404_errors = 10;      // Máximo de errores 404 permitidos
    private $time_window = 60;          // Ventana de tiempo en segundos (1 minuto)
    private $block_duration = 3600;     // Duración del bloqueo en segundos (1 hora)
    private $log_file;
    private $blocked_ips_file;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $upload_dir = wp_upload_dir();
        $this->log_file = $upload_dir['basedir'] . '/security-logs/404-attempts.log';
        $this->blocked_ips_file = $upload_dir['basedir'] . '/security-logs/blocked-ips.json';

        // Crear directorio de logs si no existe
        $log_dir = dirname($this->log_file);
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
            // Proteger directorio con .htaccess
            file_put_contents($log_dir . '/.htaccess', 'Deny from all');
        }

        // Verificar si la IP está bloqueada antes de cargar WordPress
        add_action('init', array($this, 'check_blocked_ip'), 1);

        // Monitorear errores 404
        add_action('template_redirect', array($this, 'monitor_404'), 1);
    }

    /**
     * Obtener IP real del visitante
     */
    private function get_real_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // Proxy
            'HTTP_X_REAL_IP',            // Nginx proxy
            'HTTP_CLIENT_IP',            // Otros proxies
            'REMOTE_ADDR'                // Directo
        );

        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // Si hay múltiples IPs (X-Forwarded-For), tomar la primera
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    /**
     * Verificar si la IP está bloqueada
     */
    public function check_blocked_ip() {
        $ip = $this->get_real_ip();

        // No bloquear localhost
        if (in_array($ip, array('127.0.0.1', '::1', '0.0.0.0'))) {
            return;
        }

        $blocked_ips = $this->get_blocked_ips();

        if (isset($blocked_ips[$ip])) {
            $block_time = $blocked_ips[$ip]['blocked_until'];

            if (time() < $block_time) {
                // IP aún bloqueada
                $this->send_blocked_response($ip, $block_time);
                exit;
            } else {
                // Bloqueo expirado, remover de la lista
                unset($blocked_ips[$ip]);
                $this->save_blocked_ips($blocked_ips);
            }
        }
    }

    /**
     * Monitorear errores 404
     */
    public function monitor_404() {
        if (!is_404()) {
            return;
        }

        $ip = $this->get_real_ip();

        // No monitorear localhost
        if (in_array($ip, array('127.0.0.1', '::1', '0.0.0.0'))) {
            return;
        }

        $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        // Registrar el intento
        $this->log_404_attempt($ip, $request_uri, $user_agent);

        // Contar errores 404 recientes de esta IP
        $error_count = $this->count_recent_404_errors($ip);

        if ($error_count >= $this->max_404_errors) {
            $this->block_ip($ip, $user_agent);
        }
    }

    /**
     * Registrar intento de acceso 404
     */
    private function log_404_attempt($ip, $uri, $user_agent) {
        $log_entry = sprintf(
            "[%s] IP: %s | URI: %s | UA: %s\n",
            date('Y-m-d H:i:s'),
            $ip,
            substr($uri, 0, 200),
            substr($user_agent, 0, 150)
        );

        file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);

        // Mantener archivo de log manejable (máx 5MB)
        if (file_exists($this->log_file) && filesize($this->log_file) > 5242880) {
            $this->rotate_log();
        }
    }

    /**
     * Rotar archivo de log
     */
    private function rotate_log() {
        $backup = str_replace('.log', '-' . date('Y-m-d-His') . '.log', $this->log_file);
        rename($this->log_file, $backup);

        // Eliminar logs antiguos (más de 7 días)
        $log_dir = dirname($this->log_file);
        $files = glob($log_dir . '/404-attempts-*.log');
        foreach ($files as $file) {
            if (filemtime($file) < time() - 604800) {
                unlink($file);
            }
        }
    }

    /**
     * Contar errores 404 recientes de una IP
     */
    private function count_recent_404_errors($ip) {
        if (!file_exists($this->log_file)) {
            return 0;
        }

        $count = 0;
        $cutoff_time = time() - $this->time_window;

        // Leer últimas líneas del log
        $lines = file($this->log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_slice($lines, -100); // Últimas 100 líneas

        foreach ($lines as $line) {
            if (strpos($line, "IP: {$ip}") !== false) {
                // Extraer timestamp
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    $log_time = strtotime($matches[1]);
                    if ($log_time >= $cutoff_time) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Bloquear una IP
     */
    private function block_ip($ip, $user_agent) {
        $blocked_ips = $this->get_blocked_ips();

        $blocked_ips[$ip] = array(
            'blocked_at' => time(),
            'blocked_until' => time() + $this->block_duration,
            'reason' => 'Excessive 404 errors',
            'user_agent' => substr($user_agent, 0, 200)
        );

        $this->save_blocked_ips($blocked_ips);

        // Log del bloqueo
        $this->log_404_attempt($ip, '*** IP BLOCKED ***', $user_agent);

        // Responder con bloqueo
        $this->send_blocked_response($ip, $blocked_ips[$ip]['blocked_until']);
        exit;
    }

    /**
     * Obtener lista de IPs bloqueadas
     */
    private function get_blocked_ips() {
        if (!file_exists($this->blocked_ips_file)) {
            return array();
        }

        $content = file_get_contents($this->blocked_ips_file);
        $data = json_decode($content, true);

        return is_array($data) ? $data : array();
    }

    /**
     * Guardar lista de IPs bloqueadas
     */
    private function save_blocked_ips($blocked_ips) {
        file_put_contents(
            $this->blocked_ips_file,
            json_encode($blocked_ips, JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }

    /**
     * Enviar respuesta de bloqueo
     */
    private function send_blocked_response($ip, $block_until) {
        $remaining = $block_until - time();
        $minutes = ceil($remaining / 60);

        status_header(403);
        header('Content-Type: text/html; charset=UTF-8');
        header('X-Blocked-Reason: Suspicious activity detected');

        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Acceso Bloqueado - MultiAnalityca</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
                .container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }
                h1 { color: #e74c3c; margin-bottom: 20px; }
                p { color: #666; line-height: 1.6; }
                .time { font-weight: bold; color: #333; }
                .contact { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Acceso Temporalmente Bloqueado</h1>
                <p>Hemos detectado actividad sospechosa desde tu dirección IP.</p>
                <p>Tu acceso será restaurado en aproximadamente <span class="time">' . $minutes . ' minutos</span>.</p>
                <div class="contact">
                    <p>Si crees que esto es un error, contacta a:<br>
                    <a href="mailto:info@multianalityca.com">info@multianalityca.com</a></p>
                </div>
            </div>
        </body>
        </html>';
    }
}

// Inicializar protección contra bots
add_action('plugins_loaded', function() {
    Multianalityca_Bot_Protection::get_instance();
}, 1);

/**
 * 42. Agregar página de administración para ver IPs bloqueadas
 */
function multianalityca_security_admin_menu() {
    add_management_page(
        'Seguridad - IPs Bloqueadas',
        'IPs Bloqueadas',
        'manage_options',
        'ma-blocked-ips',
        'multianalityca_blocked_ips_page'
    );
}
add_action('admin_menu', 'multianalityca_security_admin_menu');

function multianalityca_blocked_ips_page() {
    $upload_dir = wp_upload_dir();
    $blocked_ips_file = $upload_dir['basedir'] . '/security-logs/blocked-ips.json';
    $log_file = $upload_dir['basedir'] . '/security-logs/404-attempts.log';

    // Procesar desbloqueo de IP
    if (isset($_POST['unblock_ip']) && wp_verify_nonce($_POST['_wpnonce'], 'unblock_ip')) {
        $ip_to_unblock = sanitize_text_field($_POST['unblock_ip']);
        if (file_exists($blocked_ips_file)) {
            $blocked = json_decode(file_get_contents($blocked_ips_file), true);
            if (isset($blocked[$ip_to_unblock])) {
                unset($blocked[$ip_to_unblock]);
                file_put_contents($blocked_ips_file, json_encode($blocked, JSON_PRETTY_PRINT));
                echo '<div class="notice notice-success"><p>IP ' . esc_html($ip_to_unblock) . ' desbloqueada.</p></div>';
            }
        }
    }

    // Obtener IPs bloqueadas
    $blocked_ips = array();
    if (file_exists($blocked_ips_file)) {
        $blocked_ips = json_decode(file_get_contents($blocked_ips_file), true) ?: array();
    }

    // Obtener últimos logs
    $recent_logs = array();
    if (file_exists($log_file)) {
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $recent_logs = array_slice($lines, -50);
        $recent_logs = array_reverse($recent_logs);
    }

    ?>
    <div class="wrap">
        <h1>Protección contra Bots - MultiAnalityca</h1>

        <h2>IPs Actualmente Bloqueadas (<?php echo count($blocked_ips); ?>)</h2>
        <?php if (empty($blocked_ips)): ?>
            <p>No hay IPs bloqueadas actualmente.</p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Bloqueada desde</th>
                        <th>Bloqueada hasta</th>
                        <th>Razón</th>
                        <th>User Agent</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blocked_ips as $ip => $data): ?>
                        <tr>
                            <td><strong><?php echo esc_html($ip); ?></strong></td>
                            <td><?php echo date('Y-m-d H:i:s', $data['blocked_at']); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', $data['blocked_until']); ?></td>
                            <td><?php echo esc_html($data['reason']); ?></td>
                            <td><small><?php echo esc_html(substr($data['user_agent'], 0, 50)); ?>...</small></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <?php wp_nonce_field('unblock_ip'); ?>
                                    <input type="hidden" name="unblock_ip" value="<?php echo esc_attr($ip); ?>">
                                    <button type="submit" class="button button-small">Desbloquear</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h2 style="margin-top: 30px;">Últimos Intentos 404 Sospechosos</h2>
        <?php if (empty($recent_logs)): ?>
            <p>No hay registros de intentos 404.</p>
        <?php else: ?>
            <div style="background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 4px; max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                <?php foreach ($recent_logs as $log): ?>
                    <div style="margin-bottom: 5px; <?php echo strpos($log, 'BLOCKED') !== false ? 'color: #ff6b6b;' : ''; ?>">
                        <?php echo esc_html($log); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * 43. Bloquear acceso a archivos sensibles via PHP
 */
function multianalityca_block_sensitive_files() {
    $request_uri = isset($_SERVER['REQUEST_URI']) ? strtolower($_SERVER['REQUEST_URI']) : '';

    // Patrones de archivos sensibles
    $blocked_patterns = array(
        '/\.env',
        '/\.git',
        '/\.svn',
        '/wp-config.php.bak',
        '/wp-config.php.old',
        '/debug.log',
        '/error_log',
        '/phpinfo.php',
        '/.htpasswd',
        '/composer.json',
        '/composer.lock',
        '/package.json',
        '/package-lock.json',
        '/yarn.lock',
        '/readme.html',
        '/license.txt',
        '/xmlrpc.php'
    );

    foreach ($blocked_patterns as $pattern) {
        if (strpos($request_uri, $pattern) !== false) {
            status_header(403);
            exit('Access Denied');
        }
    }
}
add_action('init', 'multianalityca_block_sensitive_files', 1);

/**
 * 44. Desactivar avisos de Really Simple SSL sobre bots 404
 * Ya tenemos nuestra propia protección implementada
 */
function multianalityca_dismiss_rsssl_notices() {
    // Marcar el aviso de bots 404 como descartado
    update_option('rsssl_dismissed_notices', array(
        '404_blocking' => true,
        'suspected_bots' => true,
    ));
}
add_action('admin_init', 'multianalityca_dismiss_rsssl_notices');

// Ocultar avisos de upsell de Really Simple SSL Pro
function multianalityca_hide_rsssl_upsell_notices() {
    echo '<style>
        .rsssl-notice-warning,
        .rsssl-task[data-task="404_blocking"],
        .rsssl-premium-notice,
        tr[data-task_id="suspected_bots_404"] { display: none !important; }
    </style>';
}
add_action('admin_head', 'multianalityca_hide_rsssl_upsell_notices');

/**
 * =====================================================
 * OPTIMIZACIONES ADICIONALES - Enero 2026
 * =====================================================
 */

/**
 * 45. Conversión automática de imágenes a WebP al subir
 * Requiere GD o Imagick con soporte WebP
 */
function multianalityca_convert_to_webp_on_upload($metadata, $attachment_id) {
    // Solo procesar imágenes
    $mime_type = get_post_mime_type($attachment_id);
    if (!in_array($mime_type, array('image/jpeg', 'image/png'))) {
        return $metadata;
    }

    // Verificar soporte WebP
    if (!function_exists('imagewebp') && !class_exists('Imagick')) {
        return $metadata;
    }

    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . '/' . $metadata['file'];

    // Convertir imagen original a WebP
    multianalityca_create_webp_version($file_path);

    // Convertir tamaños adicionales
    if (!empty($metadata['sizes'])) {
        $file_dir = dirname($file_path);
        foreach ($metadata['sizes'] as $size => $size_data) {
            $size_path = $file_dir . '/' . $size_data['file'];
            if (file_exists($size_path)) {
                multianalityca_create_webp_version($size_path);
            }
        }
    }

    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'multianalityca_convert_to_webp_on_upload', 10, 2);

/**
 * Crear versión WebP de una imagen
 */
function multianalityca_create_webp_version($source_path, $quality = 80) {
    if (!file_exists($source_path)) {
        return false;
    }

    $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $source_path);

    // Si ya existe WebP, no recrear
    if (file_exists($webp_path)) {
        return $webp_path;
    }

    $image_type = exif_imagetype($source_path);

    // Usar GD
    if (function_exists('imagewebp')) {
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source_path);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source_path);
                // Preservar transparencia
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            default:
                return false;
        }

        if ($image) {
            $result = imagewebp($image, $webp_path, $quality);
            imagedestroy($image);
            return $result ? $webp_path : false;
        }
    }

    // Fallback a Imagick
    if (class_exists('Imagick')) {
        try {
            $imagick = new Imagick($source_path);
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality($quality);
            $imagick->writeImage($webp_path);
            $imagick->destroy();
            return $webp_path;
        } catch (Exception $e) {
            return false;
        }
    }

    return false;
}

/**
 * 46. Servir imágenes WebP automáticamente si existen
 */
function multianalityca_serve_webp_images($content) {
    if (is_admin() || is_feed()) {
        return $content;
    }

    // Solo servir WebP a navegadores que lo soporten
    $accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
    if (strpos($accept, 'image/webp') === false) {
        return $content;
    }

    // Reemplazar extensiones de imágenes con WebP si existe
    $content = preg_replace_callback(
        '/(src|srcset)=["\']([^"\']+)\.(jpe?g|png)([^"\']*)["\']/',
        function($matches) {
            $attr = $matches[1];
            $url = $matches[2];
            $ext = $matches[3];
            $suffix = $matches[4];

            // Verificar si existe versión WebP
            $upload_dir = wp_upload_dir();
            $file_url = $url . '.' . $ext;

            if (strpos($file_url, $upload_dir['baseurl']) !== false) {
                $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file_url);
                $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file_path);

                if (file_exists($webp_path)) {
                    return $attr . '="' . $url . '.webp' . $suffix . '"';
                }
            }

            return $matches[0];
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'multianalityca_serve_webp_images', 999);
add_filter('post_thumbnail_html', 'multianalityca_serve_webp_images', 999);

/**
 * 47. Cache de consultas pesadas de WordPress
 */
function multianalityca_cache_heavy_queries() {
    // Cache de widgets
    add_filter('sidebars_widgets', function($sidebars) {
        static $cached = null;
        if ($cached === null) {
            $cached = $sidebars;
        }
        return $cached;
    });
}
add_action('init', 'multianalityca_cache_heavy_queries');

/**
 * 48. Deshabilitar pingbacks completamente
 */
function multianalityca_disable_pingbacks($links) {
    return array();
}
add_filter('pre_ping', 'multianalityca_disable_pingbacks');

// Deshabilitar pingbacks en posts existentes
function multianalityca_disable_pingback_flag($post_id) {
    global $wpdb;
    $wpdb->update(
        $wpdb->posts,
        array('ping_status' => 'closed'),
        array('ID' => $post_id)
    );
}
add_action('publish_post', 'multianalityca_disable_pingback_flag');

/**
 * 49. Optimizar wp_options autoload
 * Reduce memoria al cargar solo opciones necesarias
 */
function multianalityca_optimize_autoload_options() {
    // Ejecutar solo una vez por semana
    if (get_transient('ma_autoload_optimized')) {
        return;
    }

    global $wpdb;

    // Opciones que no necesitan autoload
    $no_autoload = array(
        'recently_edited',
        'auto_core_update_notified',
        '_site_transient_%',
        '_transient_%',
        'action_scheduler_%',
    );

    foreach ($no_autoload as $option) {
        if (strpos($option, '%') !== false) {
            $wpdb->query($wpdb->prepare(
                "UPDATE {$wpdb->options} SET autoload = 'no' WHERE option_name LIKE %s AND autoload = 'yes'",
                $option
            ));
        } else {
            $wpdb->query($wpdb->prepare(
                "UPDATE {$wpdb->options} SET autoload = 'no' WHERE option_name = %s AND autoload = 'yes'",
                $option
            ));
        }
    }

    set_transient('ma_autoload_optimized', 1, WEEK_IN_SECONDS);
}
add_action('admin_init', 'multianalityca_optimize_autoload_options');

/**
 * 50. Especular prefetch para navegación más rápida
 */
function multianalityca_speculation_rules() {
    if (is_admin()) {
        return;
    }
    ?>
    <script type="speculationrules">
    {
        "prerender": [
            {
                "where": {
                    "href_matches": "/*"
                },
                "eagerness": "moderate"
            }
        ],
        "prefetch": [
            {
                "where": {
                    "selector_matches": "a[href^='/'], a[href^='<?php echo esc_url(home_url('/')); ?>']"
                },
                "eagerness": "moderate"
            }
        ]
    }
    </script>
    <?php
}
add_action('wp_head', 'multianalityca_speculation_rules', 99);

/**
 * 51. Optimizar REST API - Solo cargar cuando es necesario
 */
function multianalityca_rest_api_only_for_logged_in($result) {
    // Permitir REST API para usuarios logueados y endpoints específicos
    if (!is_user_logged_in()) {
        $allowed_endpoints = array(
            '/wp/v2/posts',        // Para previews públicos
            '/contact-form-7/',    // Para formularios
            '/oembed/',            // Para embeds
        );

        $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        $is_allowed = false;
        foreach ($allowed_endpoints as $endpoint) {
            if (strpos($request_uri, $endpoint) !== false) {
                $is_allowed = true;
                break;
            }
        }

        // Bloquear endpoints de enumeración de usuarios
        if (strpos($request_uri, '/wp/v2/users') !== false) {
            return new WP_Error(
                'rest_forbidden',
                'API access restricted',
                array('status' => 403)
            );
        }
    }

    return $result;
}
add_filter('rest_authentication_errors', 'multianalityca_rest_api_only_for_logged_in', 99);

/**
 * 52. Limitar revisiones por tipo de contenido
 */
function multianalityca_limit_revisions_by_type($num, $post) {
    if ($post->post_type === 'page') {
        return 3; // Páginas: máximo 3 revisiones
    }
    return 5; // Posts: máximo 5 revisiones
}
add_filter('wp_revisions_to_keep', 'multianalityca_limit_revisions_by_type', 10, 2);

/**
 * 53. Optimizar consultas de términos/taxonomías
 */
function multianalityca_optimize_term_queries($query_vars) {
    // Añadir cache a consultas de términos
    if (!isset($query_vars['update_term_meta_cache'])) {
        $query_vars['update_term_meta_cache'] = false;
    }
    return $query_vars;
}
add_filter('get_terms_args', 'multianalityca_optimize_term_queries');

/**
 * 54. Prefetch DNS para recursos de terceros adicionales
 */
function multianalityca_dns_prefetch_third_party() {
    $domains = array(
        '//www.google.com',
        '//www.gstatic.com',
        '//ajax.googleapis.com',
        '//cdnjs.cloudflare.com',
        '//unpkg.com',
    );

    foreach ($domains as $domain) {
        echo '<link rel="dns-prefetch" href="' . esc_url($domain) . '">' . "\n";
    }
}
add_action('wp_head', 'multianalityca_dns_prefetch_third_party', 0);

/**
 * 55. Caché estático de menús
 */
function multianalityca_cache_nav_menus($nav_menu, $args) {
    // No cachear en admin
    if (is_admin()) {
        return $nav_menu;
    }

    $cache_key = 'ma_nav_' . md5(serialize($args));
    $cached = wp_cache_get($cache_key, 'nav_menus');

    if ($cached !== false) {
        return $cached;
    }

    wp_cache_set($cache_key, $nav_menu, 'nav_menus', 3600);
    return $nav_menu;
}
add_filter('wp_nav_menu', 'multianalityca_cache_nav_menus', 10, 2);

/**
 * =====================================================
 * OPTIMIZACIONES AGRESIVAS - Plugins Pesados
 * =====================================================
 */

/**
 * 56. Optimizar Jetpack - Desactivar módulos innecesarios
 */
function multianalityca_optimize_jetpack() {
    // Desactivar módulos pesados de Jetpack que no se usan
    add_filter('jetpack_get_available_modules', function($modules) {
        $disable = array(
            'photon',           // CDN de imágenes (ya tenemos optimización)
            'photon-cdn',       // CDN assets
            'carousel',         // Galería
            'tiled-gallery',    // Galería
            'infinite-scroll',  // Scroll infinito
            'likes',            // Likes de WordPress.com
            'subscriptions',    // Suscripciones
            'related-posts',    // Posts relacionados (lento)
            'publicize',        // Compartir en redes
            'sharedaddy',       // Botones compartir
            'shortcodes',       // Shortcodes extra
            'widget-visibility',// Visibilidad widgets
            'custom-css',       // CSS personalizado
            'post-by-email',    // Post por email
            'gravatar-hovercards', // Hovercards
            'latex',            // LaTeX
            'videopress',       // VideoPress
            'contact-form',     // Formularios (ya tenemos CF7)
        );
        foreach ($disable as $module) {
            unset($modules[$module]);
        }
        return $modules;
    });

    // Desactivar Jetpack Sync donde no se necesita
    add_filter('jetpack_sync_modules', function($modules) {
        // Solo mantener módulos esenciales
        return array_filter($modules, function($module) {
            $keep = array('Jetpack_Sync_Modules_Options', 'Jetpack_Sync_Modules_Users');
            return in_array($module, $keep);
        });
    });

    // Desactivar estadísticas de Jetpack en admin (pesado)
    add_filter('jetpack_just_in_time_msgs', '__return_false');
    add_filter('jetpack_show_promotions', '__return_false');
}
add_action('plugins_loaded', 'multianalityca_optimize_jetpack', 1);

/**
 * 57. Descargar Jetpack en frontend donde no se necesita
 */
function multianalityca_dequeue_jetpack_frontend() {
    if (is_admin()) return;

    // Remover CSS de Jetpack
    wp_dequeue_style('jetpack_css');
    wp_dequeue_style('jetpack-widgets');

    // Remover JS innecesario de Jetpack
    wp_dequeue_script('jetpack-spin');
    wp_dequeue_script('jetpack-slideshow');
    wp_dequeue_script('jetpack-carousel');
    wp_dequeue_script('grunion-contact-form');
    wp_dequeue_script('grofiles-cards');
    wp_dequeue_script('wpgroho');
}
add_action('wp_enqueue_scripts', 'multianalityca_dequeue_jetpack_frontend', 999);

/**
 * 58. Optimizar LeadIn/HubSpot - Solo cargar donde se necesita
 */
function multianalityca_optimize_leadin() {
    if (is_admin()) return;

    // Solo cargar HubSpot en páginas específicas
    $allowed_pages = array('contacto', 'contact', 'servicios');

    global $post;
    $current_slug = isset($post->post_name) ? $post->post_name : '';

    if (!in_array($current_slug, $allowed_pages) && !is_front_page()) {
        // Remover HubSpot/LeadIn de páginas no necesarias
        remove_action('wp_head', 'leadin_add_frontend_tracking_code');
        remove_action('wp_footer', 'leadin_add_frontend_tracking_code');
        wp_dequeue_script('leadin-script-loader-js');
        wp_dequeue_script('leadin-forms-script-loader-js');
    }
}
add_action('wp_enqueue_scripts', 'multianalityca_optimize_leadin', 999);

/**
 * 59. Optimizar Google Site Kit - Reducir peticiones
 */
function multianalityca_optimize_site_kit() {
    // Deshabilitar dashboard widgets de Site Kit
    add_filter('googlesitekit_dashboard_widget_disabled', '__return_true');

    // Reducir frecuencia de sync
    add_filter('googlesitekit_batch_reporting_queue_size', function() {
        return 5; // Reducir de 25 a 5
    });
}
add_action('plugins_loaded', 'multianalityca_optimize_site_kit');

/**
 * 60. Optimizar MonsterInsights/Google Analytics
 */
function multianalityca_optimize_monsterinsights() {
    // Deshabilitar notices de MonsterInsights
    add_filter('monsterinsights_admin_notices', '__return_false');

    // Usar modo lite (menos features)
    add_filter('monsterinsights_tracking_mode', function() {
        return 'gtag'; // gtag es más ligero que analytics.js
    });
}
add_action('plugins_loaded', 'multianalityca_optimize_monsterinsights');

/**
 * 61. Descargar scripts de tracking en páginas de admin info
 */
function multianalityca_conditional_tracking() {
    if (is_admin()) return;

    // No cargar tracking en páginas de política de privacidad, términos, etc.
    $no_tracking_pages = array('privacidad', 'terminos', 'cookies', 'legal');

    global $post;
    if (isset($post->post_name) && in_array($post->post_name, $no_tracking_pages)) {
        // Remover Hotjar
        remove_action('wp_head', 'hotjar_output_tracking_code');
        wp_dequeue_script('hotjar');

        // Remover Google Analytics
        wp_dequeue_script('google-analytics');
        wp_dequeue_script('monsterinsights-frontend-script');
    }
}
add_action('wp_enqueue_scripts', 'multianalityca_conditional_tracking', 999);

/**
 * 62. Optimizar Avada Theme - Desactivar features no usados
 */
function multianalityca_optimize_avada() {
    // Desactivar features de Avada que no se usan
    add_filter('fusion_builder_demo_import', '__return_false');
    add_filter('fusion_enable_live_builder', '__return_false'); // Si no usas Live Builder

    // Optimizar Fusion Builder
    add_filter('fusion_load_main_fonts', '__return_false'); // Si ya cargas fuentes manualmente

    // Desactivar animaciones en móviles (mejor rendimiento)
    add_filter('fusion_disable_all_animations', function($disable) {
        if (wp_is_mobile()) {
            return true;
        }
        return $disable;
    });
}
add_action('after_setup_theme', 'multianalityca_optimize_avada', 20);

/**
 * 63. Defer agresivo de scripts de terceros
 */
function multianalityca_aggressive_defer($tag, $handle, $src) {
    if (is_admin()) return $tag;

    // Scripts que SIEMPRE deben ser diferidos
    $always_defer = array(
        'hotjar',
        'hj-',
        'hubspot',
        'leadin',
        'hs-',
        'monsterinsights',
        'google-analytics',
        'gtag',
        'ga-',
        'jetpack',
        'jp-',
        'sucuri',
        'tracking',
        'fbevents',
        'facebook',
        'twitter',
        'linkedin',
        'pinterest',
    );

    foreach ($always_defer as $defer_string) {
        if (stripos($handle, $defer_string) !== false || stripos($src, $defer_string) !== false) {
            if (strpos($tag, 'defer') === false && strpos($tag, 'async') === false) {
                return str_replace(' src=', ' defer src=', $tag);
            }
        }
    }

    return $tag;
}
add_filter('script_loader_tag', 'multianalityca_aggressive_defer', 20, 3);

/**
 * 64. Reducir consultas de postmeta
 */
function multianalityca_optimize_postmeta_queries($pieces, $queries) {
    global $wpdb;

    // Añadir índice sugerido a postmeta si no existe (solo información)
    // ALTER TABLE wptt_postmeta ADD INDEX meta_value_idx (meta_value(191));

    return $pieces;
}
add_filter('get_meta_sql', 'multianalityca_optimize_postmeta_queries', 10, 2);

/**
 * 65. Limpiar base de datos agresivamente
 */
function multianalityca_aggressive_db_cleanup() {
    global $wpdb;

    // Limpiar transients expirados
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%' AND option_value < UNIX_TIMESTAMP()");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%' AND option_name NOT IN (
        SELECT CONCAT('_transient_', SUBSTRING(option_name, 20))
        FROM (SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%' AND option_value > UNIX_TIMESTAMP()) as t
    )");

    // Limpiar site transients expirados
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_timeout_%' AND option_value < UNIX_TIMESTAMP()");

    // Limpiar action scheduler completados (más de 7 días)
    $wpdb->query("DELETE FROM {$wpdb->prefix}actionscheduler_actions WHERE status = 'complete' AND scheduled_date_gmt < DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $wpdb->query("DELETE FROM {$wpdb->prefix}actionscheduler_logs WHERE action_id NOT IN (SELECT action_id FROM {$wpdb->prefix}actionscheduler_actions)");

    // Limpiar postmeta huérfanos
    $wpdb->query("DELETE pm FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE p.ID IS NULL");

    // Limpiar comentarios spam y trash
    $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_approved IN ('spam', 'trash')");
    $wpdb->query("DELETE FROM {$wpdb->commentmeta} WHERE comment_id NOT IN (SELECT comment_ID FROM {$wpdb->comments})");
}

// Ejecutar limpieza una vez al día
if (!wp_next_scheduled('multianalityca_daily_cleanup')) {
    wp_schedule_event(time(), 'daily', 'multianalityca_daily_cleanup');
}
add_action('multianalityca_daily_cleanup', 'multianalityca_aggressive_db_cleanup');

/**
 * 66. Deshabilitar WP-Cron en cada visita (refuerzo)
 */
if (!defined('DISABLE_WP_CRON')) {
    define('DISABLE_WP_CRON', true);
}

/**
 * 67. Optimizar consultas de widgets
 */
function multianalityca_optimize_widget_queries() {
    // Cachear opciones de widgets
    global $wp_registered_widgets;

    if (!is_admin() && !empty($wp_registered_widgets)) {
        foreach ($wp_registered_widgets as $widget_id => $widget) {
            if (isset($widget['callback']) && is_array($widget['callback'])) {
                // Los widgets ya cargados no necesitan reconsultar
            }
        }
    }
}
add_action('widgets_init', 'multianalityca_optimize_widget_queries', 100);

/**
 * 68. Reducir heartbeat en admin aún más
 */
function multianalityca_reduce_heartbeat_further($settings) {
    // Solo cada 2 minutos en admin
    $settings['interval'] = 120;

    // Deshabilitar completamente en editor de posts (usar autosave manual)
    global $pagenow;
    if ($pagenow === 'post.php' || $pagenow === 'post-new.php') {
        $settings['interval'] = 300; // 5 minutos en editor
    }

    return $settings;
}
add_filter('heartbeat_settings', 'multianalityca_reduce_heartbeat_further', 99);

/**
 * 69. Precargar recursos críticos de Avada
 */
function multianalityca_preload_avada_critical() {
    if (is_admin()) return;
    ?>
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/css/style.min.css" as="style">
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/js/general/fusion.js" as="script">
    <?php
}
add_action('wp_head', 'multianalityca_preload_avada_critical', 1);

/**
 * 70. Deshabilitar AIOSEO si Yoast está activo (evitar conflicto)
 */
function multianalityca_disable_duplicate_seo() {
    // Si ambos están activos, deshabilitar AIOSEO frontend
    if (defined('WPSEO_VERSION') && defined('AIOSEO_VERSION')) {
        // AIOSEO debería ser desactivado manualmente, pero reducir su impacto
        add_filter('aioseo_disable', '__return_true');

        // Remover metaboxes duplicados de AIOSEO
        add_action('admin_init', function() {
            remove_meta_box('aioseo-mainpage', 'page', 'normal');
            remove_meta_box('aioseo-mainpage', 'post', 'normal');
        }, 99);
    }
}
add_action('plugins_loaded', 'multianalityca_disable_duplicate_seo', 99);

/**
 * 71. Optimizar carga de Fusion Builder
 */
function multianalityca_optimize_fusion_builder() {
    // Solo cargar Fusion Builder en páginas que lo usan
    if (!is_admin()) {
        global $post;
        if ($post && !has_shortcode($post->post_content, 'fusion_builder_container')) {
            // Página no usa Fusion Builder, reducir carga
            add_filter('fusion_builder_load_scripts', '__return_false');
        }
    }
}
add_action('wp', 'multianalityca_optimize_fusion_builder');

/**
 * 72. Inline CSS crítico de Avada
 */
function multianalityca_avada_critical_css() {
    if (is_admin()) return;
    ?>
    <style id="avada-critical-css">
    /* Reset y tipografía base */
    *,*::before,*::after{box-sizing:border-box}
    html{-webkit-text-size-adjust:100%;line-height:1.15}
    body{margin:0;font-family:var(--body_typography-font-family,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif);font-size:var(--body_typography-font-size,16px);line-height:var(--body_typography-line-height,1.7)}
    /* Header crítico */
    .fusion-header-wrapper{position:relative;z-index:10000}
    .fusion-header{background-color:var(--header_bg_color,#fff)}
    .fusion-logo{display:inline-block}
    .fusion-logo img{max-width:100%;height:auto}
    .fusion-main-menu{display:flex;flex-wrap:wrap;align-items:center}
    .fusion-main-menu>ul{display:flex;list-style:none;margin:0;padding:0}
    .fusion-main-menu>ul>li{position:relative}
    .fusion-main-menu>ul>li>a{display:block;padding:10px 15px;text-decoration:none}
    /* Contenido principal */
    #main{min-height:50vh}
    .fusion-row{max-width:var(--site_width,1200px);margin:0 auto;padding:0 15px}
    /* Imágenes responsive */
    img{max-width:100%;height:auto;display:block}
    /* Ocultar contenido hasta que cargue */
    .fusion-animated{opacity:1!important;transform:none!important}
    /* Botones */
    .fusion-button{display:inline-block;padding:12px 24px;text-decoration:none;border-radius:4px;transition:all .3s ease}
    </style>
    <?php
}
add_action('wp_head', 'multianalityca_avada_critical_css', 1);

/**
 * 73. Lazy load de iframes de terceros (YouTube, Maps, etc.)
 */
function multianalityca_lazy_third_party_iframes($content) {
    if (is_admin() || is_feed()) return $content;

    // Convertir iframes de YouTube a lazy load con facada
    $content = preg_replace_callback(
        '/<iframe[^>]+src=["\']([^"\']*youtube[^"\']*)["\'][^>]*><\/iframe>/i',
        function($matches) {
            $src = $matches[1];
            // Extraer ID del video
            preg_match('/(?:embed\/|v=)([a-zA-Z0-9_-]{11})/', $src, $id_match);
            if (!empty($id_match[1])) {
                $video_id = $id_match[1];
                $thumbnail = "https://i.ytimg.com/vi/{$video_id}/hqdefault.jpg";
                return '<div class="ma-youtube-facade" data-src="' . esc_attr($src) . '" style="position:relative;padding-bottom:56.25%;background:url(' . $thumbnail . ') center/cover;cursor:pointer">
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:68px;height:48px;background:#f00;border-radius:10px;display:flex;align-items:center;justify-content:center">
                        <div style="border:solid #fff;border-width:0 0 12px 20px;margin-left:5px"></div>
                    </div>
                </div>
                <script>document.querySelector(".ma-youtube-facade").addEventListener("click",function(){this.outerHTML=\'<iframe src="\'+this.dataset.src+\'?autoplay=1" frameborder="0" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%"></iframe>\'})</script>';
            }
            return $matches[0];
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'multianalityca_lazy_third_party_iframes', 100);

/**
 * 74. Optimizar Google Maps embeds
 */
function multianalityca_lazy_google_maps($content) {
    if (is_admin() || is_feed()) return $content;

    // Reemplazar iframes de Google Maps con versión lazy
    $content = preg_replace_callback(
        '/<iframe[^>]+src=["\']([^"\']*google\.com\/maps[^"\']*)["\'][^>]*><\/iframe>/i',
        function($matches) {
            return '<div class="ma-map-placeholder" data-src="' . esc_attr($matches[1]) . '" style="background:#e0e0e0;padding:40px;text-align:center;cursor:pointer;min-height:300px;display:flex;align-items:center;justify-content:center;border-radius:8px">
                <span style="font-size:16px;color:#666">Haga clic para cargar el mapa</span>
            </div>
            <script>document.querySelector(".ma-map-placeholder").addEventListener("click",function(){this.outerHTML=\'<iframe src="\'+this.dataset.src+\'" width="100%" height="400" frameborder="0" style="border:0" allowfullscreen loading="lazy"></iframe>\'})</script>';
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'multianalityca_lazy_google_maps', 100);

/**
 * 75. Ejecutar limpieza inicial si es primera vez
 */
function multianalityca_initial_cleanup() {
    if (get_option('ma_initial_cleanup_done')) {
        return;
    }

    // Ejecutar limpieza agresiva una vez
    multianalityca_aggressive_db_cleanup();

    // Marcar como completado
    update_option('ma_initial_cleanup_done', time());
}
add_action('admin_init', 'multianalityca_initial_cleanup');
