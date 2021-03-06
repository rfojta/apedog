<?php
session_cache_expire(60);
session_start();
if (!isset($_SESSION['user'])||!isset($_SESSION['country_code'])) {
session_destroy();header("Location: index.php"); exit; }

if (isset($_REQUEST['shtu'])){
    $info = "<p id=\"welcome\">Hello, it's nice to have you here again!</p><p></p>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Apedog: Main Page</title>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <link href="default.css" rel="stylesheet" type="text/css" />
        <link href="images/common/favicon.png" rel="icon" type="image/png" />
    </head>
    <body>
        <?php
        $page='main_page';
        include('components/menu.php'); ?>
        <div id="content">
            <div id="colOne">
                <h2 class="section">Main Page</h2>
                <div class="content">
                    <?php echo $info;?>
                    <!-- rozcestnik -->
                    <table>
                        <tr><td><a href="detail_planning.php" title="Enter plans for each KPI"><img src="images/common/planning.png" border="0" alt="Enter plans for each KPI" /></a></td></tr>
                        <tr><td><a href="entering_values.php" title="Enter actual values for each KPI"><img src="images/common/entering.png" border="0" alt="Enter actual values for each KPI" /></a></td></tr>
                        <tr><td><a href="reports.php" title="See reports according to plans and fullfilling"><img src="images/common/reports.png" border="0" alt="See reports according to plans and fullfilling" /></a></td></tr>
                        <?php if( $_SESSION['user'] == 'MC'):
                            echo "<tr><td><a href=\"locking.php\" title=\"You can lock planning and entering values from editing\"><img src='images/common/locking.png' border='0' alt='Locking'></a></td></tr>";
                        endif; ?>                        
                        <?php if( $_SESSION['user'] == 'MC'):
                            echo "<tr><td><a href=\"admin.php\" title=\"You can choose which object you want to configure\"><img src='images/common/configuration.png' border='0' alt='Configuration'></a></td></tr>";
                        endif; ?>
                    </table>
                </div>
            </div>
            <div id="colTwo">
                <h2 class="section">Help</h2>
                <div class="content">
                    <h3>Main Page</h3>
                    <p>Choose Planning, Entering actual values or Reporting</p>
                </div>
            </div>
            <div style="clear: both;">&nbsp;</div>
        </div>
        <?php include('components/footer.php'); ?>
    </body>
</html>
