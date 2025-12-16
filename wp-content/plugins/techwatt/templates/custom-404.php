<!DOCTYPE html>
<html>
<head>
    <title>Techwatt - 404 Page Not Found</title>
    <style>
        body { font-family: Arial, sans-serif; text-align:center; padding:50px; background: #092286ff; }
        h1 { font-size:40px; color:#111; margin:5px 0px; }
        p { font-size:18px; }
        a { color:#0073aa; text-decoration:none; padding:0 5px; }
        .errContainer {
            background: #fff; padding: 30px; border-radius: 8px;
            display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width:70%; margin:5% auto;
        }
    </style>
</head>
<body>
    <div class="errContainer">
        <img src="<?php echo esc_url(plugins_url('assets/images/logo.png', dirname(__FILE__, 2))); ?>"
             alt="Techwatt Logo"
             style="max-width:100px;height:auto;margin-top:-70px;">
        <h1>Page Not Found</h1>
        <p>
            The requested URL
            <a href="<?php echo esc_url(home_url($_SERVER['REQUEST_URI'])); ?>">
                <?php echo esc_html(home_url($_SERVER['REQUEST_URI'])); ?>
            </a>
            was not found on this server. Try links below.
        </p>
        <p>
            <a href="<?php echo esc_url(home_url()); ?>">Home</a> |
            <a href="<?php echo esc_url(home_url('/shop')); ?>">Products</a> |
            <a href="<?php echo esc_url(home_url('/courses')); ?>">Courses</a> |
            <a href="<?php echo esc_url(home_url('/contact')); ?>">Contacts</a>
        </p>
    </div>
</body>
</html>
