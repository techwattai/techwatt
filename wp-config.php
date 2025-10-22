<?php
define( 'WP_CACHE', true );

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
define( 'DB_NAME', 'dbmtechwatt' );

/** Database username */
define( 'DB_USER', 'wpuser345' );

/** Database password */
define( 'DB_PASSWORD', 'P!@#$55w0rD' );

/** Database hostname */
define( 'DB_HOST', 'db:3306' );

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
define( 'AUTH_KEY',         'LL5H)g|]jUzXm,Jlvv7R+(fv~aX&T<F9e:HUH:M<Y,)ov%-ls)T#sQk 2+)AA+-^' );
define( 'SECURE_AUTH_KEY',  'X[Zel&+S@;68x?`(>tBwcM6<#qIz>~(A^3v&dL;wIL{&MI-|rbPS~2$#LQ2oZiz.' );
define( 'LOGGED_IN_KEY',    'y6Qf.NWb-:6mXL)c^&.]p_If8KnJ%rfb75zrmb`U&(3>E)Qn4XIB#Z7XLZ?*ue(s' );
define( 'NONCE_KEY',        '}y<aH2rqJ;nmLOrHbDC[x#apvUG{Ap}Dm1aHiUpgL+LG?l2(`VZQB#Q*-CW%2l4V' );
define( 'AUTH_SALT',        '+}a3Cy!>7eAJgC)p$:r(QPh4ccW@-#xwPP<1ML:I.ED{ZI-i6xgRr*h3f1l?&?)]' );
define( 'SECURE_AUTH_SALT', 'W/~_% Z=cyF.N~p^k_tKBiQ<JGX=4GZY+^<VO94v~4!LYc|uD%u/7#=>F,W/WmR_' );
define( 'LOGGED_IN_SALT',   'dqd%mfJ!QYA2>MvwY%_;Ff-C~g*E!kW2Yof@!(]N|^bL>fnM2S69wgrA0-q)_H8:' );
define( 'NONCE_SALT',       'VosJPGqXO-U`V[#_@xj1/]cENvJv$hgqXD(0}1f/v_i(57&*e&zQm$@bpBU(QlkF' );

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
$table_prefix = 'tw_';

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
# define( 'JETPACK_DEV_DEBUG', true );
# define( 'WCPAY_DEV_MODE', true );
/* Add any custom values between this line and the "stop editing" line. */

// ** Allow WordPress to detect HTTPS correctly when behind Railway proxy ** //
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
if ( getenv('WP_HOME') && getenv('WP_SITEURL') ) {
    define('WP_HOME', getenv('WP_HOME'));
    define('WP_SITEURL', getenv('WP_SITEURL'));
} else {
	define( 'WP_HOME', 'http://localhost:8000' );
	define( 'WP_SITEURL', 'http://localhost:8000' );
}
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
