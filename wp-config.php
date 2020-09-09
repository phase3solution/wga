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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wgadvisory');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '(xvBt.~|@2:;&l^%pzi~bR&yvl!<w6gdX9n&8]nt3Cf1l/%z^mrduv1v!}-N6TT[');
define('SECURE_AUTH_KEY',  '5Cgd-_~^!.{3r8g9dkzHK+aP[,%.TktTAh@!}!Qh5aYBG4*?SDdXkm]}3;tQ3tjc');
define('LOGGED_IN_KEY',    '!tyW9~O{4>pq4![|P_rZff,>0Bw2:*{?z<:L;@TSO=DA=wyI%eM/*=Mojx^xUm0;');
define('NONCE_KEY',        'f#+f@yWg{LJe#aZUQvtzBr?0lAz+0A-KE`]JG`xL9ZK|hU=m!eP/UYL^Kw<zy4MN');
define('AUTH_SALT',        '[94u7,)HaOI!/66c*fe-@*AW$pEHU!CRUXV2wlMLo67v3 Te4NaS#QNM1p?uVrfn');
define('SECURE_AUTH_SALT', 'Ch5nvXlrDne)@Cbk6:ZO+:a6>x6AEzuvlxez@LR^{;Eu)|D9AHMfaW&U~<2y~(,T');
define('LOGGED_IN_SALT',   'O)4PwT{9.;y$E$nf3s{k|cXpUdM`.AN:]3@)bPyd(.{.@jh3PgS%qjKqT*34N2gq');
define('NONCE_SALT',       '6x}m5U|VeL@#$pm*pRSK& |cbVvNS;??4ndS]pB,Ws&H+q*4t|yv|y|^]3<-a]fd');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
// define('WP_DEBUG', false);

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', true);
@ini_set('display_errors', 1);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
