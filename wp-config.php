<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'addweb' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '#%g6nb>2K5u.4JweU/J*IrG@HTv0EORf<!TL>Gfyz:fY;R0uE470mjK9vY&pE#-R' );
define( 'SECURE_AUTH_KEY',  'g/1|y$+<h>UZ.j-+&d)Fk^10&LJn<mD,V/gq&W5:j,$f=S[@D*, k(2|axT$<44O' );
define( 'LOGGED_IN_KEY',    '@|V_,BZ>cprK%lIj~ A>[DLl4Q=Klo~Uh_Kbe ?AW,#+m>W3 ujB436)S1/hR5i3' );
define( 'NONCE_KEY',        'SR] Y44Gu@h~i^rRHF)PD$tr<saD2w%Ad]hSJ0(X7gZ*RV@5Q1QTb1>B?VM!+CME' );
define( 'AUTH_SALT',        '*DUaP:/*wT4Lg#N9,P F;ZAreIQs?<Z:DG_9|^k<r*Fuv?{Gh8#|2J&c~P5GjM?&' );
define( 'SECURE_AUTH_SALT', '5s:^ltam<ct}cAle}Ae_IX80pWMp|o7Q7CldR&Zs7V*dl@&C.2E9w_U<9zrB nuI' );
define( 'LOGGED_IN_SALT',   '(/lmGUDG62v:tY?1j$<QxSGS|0Jhtmi!X 9u5>y(bq@Xt9MSh.|G/z~$B{T3GZMh' );
define( 'NONCE_SALT',       '@Y=5$<%ruNY%n5Qyw<oAn~oUWEeFxTa[()bES4?}*@bSaw>K&N*o .7j%pDMA>//' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
