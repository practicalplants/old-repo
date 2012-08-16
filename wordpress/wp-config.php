<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'pp-wp');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'mV-vt.ZIlgu^]#^Uu?n~.x?}5uVd$hqyWUR]zS7E{.@1h4^V%Z-YSTKNo3-t4cx^');
define('SECURE_AUTH_KEY',  'o!`E<AP[pvry[?B)yS(#YeRjwZ{:9ZR5.T{,9_|yD|--,WV!!BRWG!]v!<QXs{/A');
define('LOGGED_IN_KEY',    'I1wr.Np-o2f%D(*<[6=ER V{Mh-^Sj|kLqfbXm8_?;xYR_`;,oEm-{^$g{a>IV*#');
define('NONCE_KEY',        'C9[@w}j:.-|en|[;P(b@<GzB(0qF<1|jGWx`5c~<r0EwS0H}@?%K-r[>!Aw(H<wU');
define('AUTH_SALT',        '7*3hOh: kMR3lNVVijf^z-v+*9X,$Y(WG__01EX8]n[eRi[q,;N+[~b&/^8|.2*E');
define('SECURE_AUTH_SALT', 'A-q+]4d7F.Bzxn-8*x!ZD,,X|}ZEovhyEO&jvS4dwo^Q4k]^$--F6$SqM#FKO`>u');
define('LOGGED_IN_SALT',   '_(W>:0-1)C8Ew|GFU(n-MX[]rRHhD%P+z?&w@):{]^=<]lbSn?vmRld(c.A:7%+x');
define('NONCE_SALT',       'vemvD>2-a-`9:QVk+M@m}7}eP!/ab NrzisSPBGWx0A?7Ns<>`5D|g[@~:]umy)t');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define('WP_ALLOW_REPAIR', true);
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
define( 'SSO_URL' ,'http://practicalplants.local/sso');
define( 'FRONT_END_COOKIE_SSO_SECRET', 'ThereArePlantsWhichArePracticalAndTheyAreTotallyAwesome' );

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
