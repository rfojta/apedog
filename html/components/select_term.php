<script type="text/javascript">
    function refreshWindow(source) {
        old_href = window.location.href;
        tokens = old_href.split('?');
        window.location.href = tokens[0] + "?term_id=" + source.value;
    }
</script>

<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

echo "Select term: \n";
echo "<select name=\"term_id\" id=\"term_id\"\n";
// echo "onchange=\"window.location.href='planning.php?term_id='+this.value\">\n";
echo "onchange=\"refreshWindow(this)\">\n";

foreach( $terms as $term ) {
    echo "<option value=\"$term[0]\"";
    if( isset($_REQUEST['term_id']) ) {
        if( $term[0] == $_REQUEST['term_id']) {
            echo " selected ";
        }
    }

    echo ">";
    echo $term[1];
    echo "</option>\n";
}

echo "</select>\n";




/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$area_query = 'select * from areas';
$res = mysql_query( $area_query, $dbres );

if( !$res ) {
            die( 'Invalid query: ' . mysql_error($this->dbres) );
        }

        $areas = array();
        while( $row = mysql_fetch_assoc($res) ) {
            $areas[] = $row;
}

echo "Select area: \n";
echo "<select name=\"area_id\" id=\"area_id\"\n";
// echo "onchange=\"window.location.href='planning.php?term_id='+this.value\">\n";
echo "onchange=\"refreshWindow(this)\">\n";

foreach( $areas as $area ) {

    echo "<option value=\"".$area['id']."\"";
    if( isset($_REQUEST['area_id']) ) {
        if( $area['id'] == $_REQUEST['area_id']) {
            $area_id=$area['id'];
            echo " selected ";
        }
    }

    echo ">";
    echo $area['name'];
    echo "</option>\n";
}

echo "</select>\n";
$damn=array();
$damn[] += $term[0];
$damn[] += $area_id;
return $damn;


?>
