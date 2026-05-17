/**
 * Banner de Cookies - MultiAnalityca
 * Manejo de consentimiento conforme a GDPR/LOPD
 * Configuración desde el admin de WordPress
 */

(function() {
    'use strict';

    var COOKIE_NAME = 'ma_cookie_consent';

    // Obtener configuración del admin (pasada via wp_localize_script)
    var config = window.maCookieBanner || {};
    var COOKIE_DAYS = config.cookieDays || 365;
    var GA_TRACKING_ID = config.gaTrackingId || '';

    /**
     * Establecer cookie
     */
    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
    }

    /**
     * Obtener cookie
     */
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) {
                return decodeURIComponent(c.substring(nameEQ.length));
            }
        }
        return null;
    }

    /**
     * Ocultar banner con animacion
     */
    function hideBanner() {
        var banner = document.getElementById('ma-cookie-banner');
        if (banner) {
            banner.classList.remove('ma-cookie-banner--visible');
            setTimeout(function() {
                banner.style.display = 'none';
            }, 400);
        }
    }

    /**
     * Habilitar scripts de tracking (Google Analytics, GTM, etc.)
     */
    function enableTracking() {
        // Disparar evento personalizado para que otros scripts puedan reaccionar
        var event;
        try {
            event = new CustomEvent('ma_cookies_accepted');
        } catch (e) {
            event = document.createEvent('CustomEvent');
            event.initCustomEvent('ma_cookies_accepted', true, true, {});
        }
        document.dispatchEvent(event);

        // Google Consent Mode - otorgar permisos
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted',
                'ad_storage': 'granted'
            });
        }
    }

    /**
     * Deshabilitar tracking
     */
    function disableTracking() {
        // Google Analytics opt-out (usa el ID configurado en admin)
        if (GA_TRACKING_ID) {
            window['ga-disable-' + GA_TRACKING_ID] = true;
        }

        // Google Consent Mode - denegar
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'analytics_storage': 'denied',
                'ad_storage': 'denied'
            });
        }

        // Disparar evento para otros scripts
        var event;
        try {
            event = new CustomEvent('ma_cookies_rejected');
        } catch (e) {
            event = document.createEvent('CustomEvent');
            event.initCustomEvent('ma_cookies_rejected', true, true, {});
        }
        document.dispatchEvent(event);
    }

    /**
     * Manejar aceptacion de cookies
     */
    function handleAccept() {
        setCookie(COOKIE_NAME, 'accepted', COOKIE_DAYS);
        enableTracking();
        hideBanner();
    }

    /**
     * Manejar rechazo de cookies
     */
    function handleReject() {
        setCookie(COOKIE_NAME, 'rejected', COOKIE_DAYS);
        disableTracking();
        hideBanner();
    }

    /**
     * Inicializar banner
     */
    function init() {
        var banner = document.getElementById('ma-cookie-banner');
        var acceptBtn = document.getElementById('ma-cookie-accept');
        var rejectBtn = document.getElementById('ma-cookie-reject');

        if (!banner) return;

        // Verificar si ya hay consentimiento
        var consent = getCookie(COOKIE_NAME);
        if (consent) {
            banner.style.display = 'none';
            return;
        }

        // Mostrar banner con delay para mejor UX
        setTimeout(function() {
            banner.classList.add('ma-cookie-banner--visible');
        }, 500);

        // Event listeners
        if (acceptBtn) {
            acceptBtn.addEventListener('click', handleAccept);
        }
        if (rejectBtn) {
            rejectBtn.addEventListener('click', handleReject);
        }

        // Accesibilidad: cerrar con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && banner.classList.contains('ma-cookie-banner--visible')) {
                handleReject();
            }
        });
    }

    // Iniciar cuando el DOM este listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
