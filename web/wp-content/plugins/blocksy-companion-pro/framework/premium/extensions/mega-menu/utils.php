<?php

namespace Blocksy\Extensions\MegaMenu;

if (! defined('ABSPATH')) {
	exit;
}

class Utils {
	public function get_menu_token($menu_id) {
		return $menu_id . ':' . hash_hmac(
			'sha256',
			'blocksy-mega-menu:' . $menu_id,
			wp_salt()
		);
	}
}
