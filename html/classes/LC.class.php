<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LC
 *
 * @author Richard
 */
class LC {
    //put your code here
    var $dbres;
    var $by_user_query = 'select id from lcs where name = ';

    function LC( $dbres ) {
        $this->dbres = $dbres;
    }

    function get_lc_by_name($login) {
        $query = $this->by_user_query;
        $query .= '\'' . mysql_real_escape_string($login, $this->dbres);
        $query .= '\'';

        $res = mysql_query( $query, $this->dbres );
        if( !$res ) {
            die( 'Invalid query: ' . mysql_error($this->dbres) );
        }
       $lc_id = '';

        while( $row = mysql_fetch_assoc($res) ) {
            $lc_id = $row['id'];
        }
        return $lc_id;
    }
}
?>
