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
define( 'DB_NAME', 'retroemula2_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '>Ju<W{W9/6~IxvITvh)e bw!4sY|n(hgQv0!*#0lFJ-5?8Ut^j7p.<GA~EY^]ZHd' );
define( 'SECURE_AUTH_KEY',  'vP|CAm>fryzA&-FeHGQkMl/l^CV_rmH*1m;6]d!r=[B0B&qu$,8D:H(#I N^lJHz' );
define( 'LOGGED_IN_KEY',    '&V.N`}5j:5s?vQ>f+7,jc#+?K)7:{(1S2[{j@l2q;SRWP1:<0GPJhEmM76v9bO@V' );
define( 'NONCE_KEY',        'tBH-(2$!YKXTT(B)Vn40jdl;d+8c-$bumWx/VM6$EX?|>iocUoep2JKFF2Xt(Va%' );
define( 'AUTH_SALT',        ')xnQ*BAkTsH<2u?sA@1}S4`~QwgmRs6#RGbG_HO+<&iHCtMC:&>I;y+rsYW]t;vo' );
define( 'SECURE_AUTH_SALT', 'H%CN_#l%g*_+4TFvipBiP{}it;F|w`_M535yWjERG8{Xse(]3rT:8h7B1^(z6cn;' );
define( 'LOGGED_IN_SALT',   'd]|1u7eGZBGsHel:U[Bf-~;@29AXY[KwBy#z,cxj.R{bH:]-~8+J6{])9?]Xl1${' );
define( 'NONCE_SALT',       'GHE6g:n^f@(U7pG=I-M.ND}AviD_+aS!`!7l<}oo.zG^hlf8IJRp{y=B1gQKKOqu' );

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
