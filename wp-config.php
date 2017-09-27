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
define('DB_NAME', 'newspage');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '123456789');

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
define('AUTH_KEY',         'Hn%%D0+xW*Vxv(x_,4wGE7DhM{{*I;F#ukZ+RY$-vbRAdR/a:DDF$jH[JI.}>njX');
define('SECURE_AUTH_KEY',  ')Kr&HjkQr;=$6juPn.oI9UQS@Mn2G$Pba@VqbV5D/1O/TzD.TpGR+4?vs&Ex0?/G');
define('LOGGED_IN_KEY',    ',PR(/()KVg5#~YCAm>RhvsQ,ErcDPw1=ydGgJqg9?PQaB@Kl}^jBXPdJGK_,=Z/`');
define('NONCE_KEY',        'xbXXtA9UOoZYN2C@]1PfqcH21TWl OAg`jX+}i9DD+M~|Hh3n<rC@+Yl(/]yB}v!');
define('AUTH_SALT',        't&_?Cw-e;y3TBE=+|@,;S%.T126l!C+ORW,CsSK_(lT;3SD<,>b[U,k=HiYzFr%X');
define('SECURE_AUTH_SALT', 'kPLCy|uk,R]hHd>eq]()Qr^sapa6ErLk:vlt~i|hqt<*rPJPF%c7D?i]I)Jklhqd');
define('LOGGED_IN_SALT',   'Fgd9qxJ:~Xfs2KZ!lRz!eq] nC3 (y3XCFtOkcub_Ms!.>=)`RwaV}Kvan]e+Sqt');
define('NONCE_SALT',       '97{uzpZP>S>66^j,{Q5Zmfb!XSv%%{XJLdiS;L|=<WROsHA_%ltJyyQH]y>@(5[S');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'np_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
