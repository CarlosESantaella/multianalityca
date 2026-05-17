<?php

// File Security Check.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class The7_Dev_Tools {

	public static function init() {
		add_action( 'load-toplevel_page_the7-dev', array( __CLASS__, 'save_mode' ) );
	}

	public static function save_mode() {
		if ( ! check_ajax_referer( 'the7-dev-tools', false, false ) || ! current_user_can( 'switch_themes' )  ) {
			return;
		}

		if ( isset( $_POST['regenerate_shortcodes_css'] ) ) {
			include_once PRESSCORE_MODS_DIR . '/theme-update/the7-update-utility-functions.php';
			the7_mass_regenerate_short_codes_inline_css();
		}
	}

}
