<?php
include('classes/Apedog.class.php');
include('classes/Login.class.php');

$apedog = new Apedog('devel');
$dbres = $apedog->dbres;
$login = new Login($dbres);
list($code, $info) = $login->validate($_POST);

//  ini_set("session.use_cookies_only","1");
//  session_cache_expire(60);
session_start();
if (isset($_REQUEST['logout'])) {
    unset($_SESSION);
    session_destroy();
    $info = "You were successfully logged out.";
}

if (isset($_SESSION['user'])) {
    header("Location: main_page.php");
}

if( $code == 1 ) {
    $_SESSION['user'] = $info;
    header("Location: main_page.php?shtu");
    break;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Apedog</title>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <link href="default.css" rel="stylesheet" type="text/css" />
        <link href="images/favicon.png" rel="icon" type="image/png" />
    </head>
    <body>

        <?php
        $page='index';
        include('components/menu.php'); ?>


        <div id="content">
            <div id="colOne">
                <h2 class="section">Welcome to AEISEC CR LC Performance Monitor</h2>
                <div class="content">
                    <p>Apedog is a tool, which helps LCs in the Czech Republic to track their plans. It was developed to ensure sustainable growth, improving KPIs and improving AIESEC experience.</p>
                    <p>We recommend to use Mozilla Firefox for best look and performance.</p>
                    <b><?php echo $info; ?></b>

                    <form action="index.php" method="post"><div style="text-align: left;" >
                            
                            <table style="margin: 0pt; padding: 10px; text-align: left; border:0">
                                <tr><td><label for="userid">Login: </label></td><td><input type="text" name="userid" id="userid" size="10" /></td></tr>
                                <tr><td><label for="userpass">Password: </label></td><td><input type="password" name="userpass" id="userpass" size="10" /></td></tr>
                            </table>

                            <input type="submit" name="prihlasit" value="Sign in" />
                        </div></form>
                </div>
            </div>

            <div id="colTwo">
                <h2 class="section">Help</h2>
                <div class="content">
                    <h3>You will se help and hints while working with Apedog here</h3>
                    <p>Now login with your login name (case sensitive) and password, all provided by your MC.</p>
                </div>
            </div>

            <div style="clear: both;">&nbsp;</div>
        </div>
        <?php include('components/footer.php'); ?>
    </body>
</html>
