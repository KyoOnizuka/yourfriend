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
define( 'DB_NAME', 'wordpress' );

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
define( 'AUTH_KEY',         'o:`Pn6;^;8Z| 5y]5Mi%QHH5pB_lsVW} LrbB`%<BH~60G^|Y1*=>A45m@VN1KD$' );
define( 'SECURE_AUTH_KEY',  'SMp_vHb!Xf1jsQv]r>T??needs&7];z}Oce}Cl5P9Pjd8B8MhDN,uD9-&CPjs<$h' );
define( 'LOGGED_IN_KEY',    ',WV>[Fb~GjMX1f$*d.w`X^qDM[q_gLlc72Fp|&_Ni8-9l|](kY/00N.XpdrQAKNr' );
define( 'NONCE_KEY',        '!Wwr^vJj=J!ExCoU{G9S5]Kfr3dwWj-:8sTkYW=c;IKOuYz371B[Zpq wt@eJ{!m' );
define( 'AUTH_SALT',        ',mFljq^YbzoM0hAHJ0<$yf{WK;ZX,H{R7x/n-Cdq{U;gA9Dgo,DSDp%%`2M-aegq' );
define( 'SECURE_AUTH_SALT', 'bB:v++XGgncl8ix[0<u_J!N#Y:!y$JZ|yV}qk,v?0jTCAv*+:nM3Pt###Pjy&)v}' );
define( 'LOGGED_IN_SALT',   ')UdB,5!nq?K+rV4^43vP0v/-BMdbtczNLXvp#KE|tG72f?,3//i69&}xSZ_~1e?$' );
define( 'NONCE_SALT',       'sKXrkzD6LyqvY:hQ{]}O(]oz.,Fv{Rh2}*~J)6}h/Qzn=okN Ed0%>.o;0bd#b/`' );

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
