<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'XEwRLKNP91FJ29^Hr^S8$8(pYW6Vez$461lIE/oL#9UAIH/yYty;bmqBSPoS/XCj' );
define( 'SECURE_AUTH_KEY',   ';sqb 5=}a7;vW&EQhtP`9>`1OP.(h=SSa)mWg5?cWc-~FonRnN+DpicGrY+,gk~!' );
define( 'LOGGED_IN_KEY',     'B]r{aONxFuOzZ(Es#:~5S-w( Rw4}1O8:u|<ww<2q-?Inr<@#C<g5$UVrThC7MXI' );
define( 'NONCE_KEY',         '9D20BNU$d|s2+iIO><u6i::n38lH~|bR]jyq)wOwlz#.A+,3()6wI(<,`#>/>EoM' );
define( 'AUTH_SALT',         '6L!)8!}eCjI7{^!Ht<*b(FN|rpyC9bJ[;lcgbdwpA>[mx43;^i2k20TaC!a#SpTr' );
define( 'SECURE_AUTH_SALT',  ':P8L`{KZ|E-/R@l{SDg>%1PH5@A?(i|Ih~MQxvy[^?K{m!,KlzqXKaLdvfi^43@&' );
define( 'LOGGED_IN_SALT',    '99`)AxcB1IQ&,Bl@L~0.CZx3Jpl/:gE,-y1.%SSyR0o.>b9*e`V8v82=h.Aum>O4' );
define( 'NONCE_SALT',        '`]`euM)-cj[!5wQw%K0hlF*$Oqu2XD`QT-#(ZI3^1:%wd*p`lcJWpT/ld=,7tH~F' );
define( 'WP_CACHE_KEY_SALT', '_LO3v9lK~v/,olg^hOeu0ly,ORntK3MDl85a~Cx&bURQ^=q+)0zC3Hf((j_k_j4m' );

define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');
/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}

// Guardar logs en archivo
define( 'WP_DEBUG_LOG', true );

// No mostrar errores en pantalla (seguridad)
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

// Log específico para emails
define( 'WP_MAIL_SMTP_DEBUG', true );

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
$_SERVER['HTTPS'] = 'on';
}