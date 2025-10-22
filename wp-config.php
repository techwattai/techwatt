<?php
define( 'WP_CACHE', true );

// ** Database settings — use Railway environment variables if available ** //
define( 'DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'dbmtechwatt' );
define( 'DB_USER', getenv('WORDPRESS_DB_USER') ?: 'wpuser345' );
define( 'DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'P!@#$55w0rD' );
define( 'DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'db:3306' );

define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

define( 'AUTH_KEY',         'LL5H)g|]jUzXm,Jlvv7R+(fv~aX&T<F9e:HUH:M<Y,)ov%-ls)T#sQk 2+)AA+-^' );
define( 'SECURE_AUTH_KEY',  'X[Zel&+S@;68x?`(>tBwcM6<#qIz>~(A^3v&dL;wIL{&MI-|rbPS~2$#LQ2oZiz.' );
define( 'LOGGED_IN_KEY',    'y6Qf.NWb-:6mXL)c^&.]p_If8KnJ%rfb75zrmb`U&(3>E)Qn4XIB#Z7XLZ?*ue(s' );
define( 'NONCE_KEY',        '}y<aH2rqJ;nmLOrHbDC[x#apvUG{Ap}Dm1aHiUpgL+LG?l2(`VZQB#Q*-CW%2l4V' );
define( 'AUTH_SALT',        '+}a3Cy!>7eAJgC)p$:r(QPh4ccW@-#xwPP<1ML:I.ED{ZI-i6xgRr*h3f1l?&?)]' );
define( 'SECURE_AUTH_SALT', 'W/~_% Z=cyF.N~p^k_tKBiQ<JGX=4GZY+^<VO94v~4!LYc|uD%u/7#=>F,W/WmR_' );
define( 'LOGGED_IN_SALT',   'dqd%mfJ!QYA2>MvwY%_;Ff-C~g*E!kW2Yof@!(]N|^bL>fnM2S69wgrA0-q)_H8:' );
define( 'NONCE_SALT',       'VosJPGqXO-U`V[#_@xj1/]cENvJv$hgqXD(0}1f/v_i(57&*e&zQm$@bpBU(QlkF' );

$table_prefix = 'tw_';

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
