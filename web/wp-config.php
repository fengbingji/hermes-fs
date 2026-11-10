<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'hermes-fs' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'E25Z_%bnqZw0qvUm)k<m1)Omb1%4H=d%g-$5Bh>})(U.u9QDRC]oJFh}a5<z6Faw' );
define( 'SECURE_AUTH_KEY',  'a8yh1D& Tvvx]?](U@cZ.qqXwwV15idIqd]tvzZG0wvPf*A(V~!zkrb%laGBDXS9' );
define( 'LOGGED_IN_KEY',    'A$-/&..!<C67S)E7QpwfTE@8<@S!NM=3xZLv1P/QIr=v%-#(7IMN=A9YSL_*d;+%' );
define( 'NONCE_KEY',        '9i<HuXm>|06AeEw8K03R{x*4ffVR@msgeN[U#78${T6w_[`F{bT.%)C|bulhkjh}' );
define( 'AUTH_SALT',        'q8HJ@dFbmG^<sQL[e<iS5sFt7F%_hZEZ?Rh {7m$Q[tOVzsMt6d`j=zD,OvpvNXI' );
define( 'SECURE_AUTH_SALT', 'B@0+G0WUdk<10a{Kq+F2CozB_0-pdfxAaYq%(Bn.j[*-_WDwggPf! tM48b:tw s' );
define( 'LOGGED_IN_SALT',   'b&XeV_7qjH}]3{lJ2BJn}Lt}8AO5y-N;&n4-# p`a<m0+ZKR-6U?6q! m*yRqz;y' );
define( 'NONCE_SALT',       'c]{xO4oW]_O*Wr4N/oK=%7S~%>0.o|dD@oNs&% hUbWhV$SS+0NQ40=R$(rJ.:>B' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
