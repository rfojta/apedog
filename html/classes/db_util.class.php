<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db_utilclass
 *
 * @author Richard
 */
class DB_Util {
//put your code here
    protected $dbres;
    protected $debug;

    function  __construct( $dbres, $debug = 0 ) {
        $this->dbres = $dbres;
        $this->debug = $debug;
    }

    function process_query_assoc($query) {
        if( $this->debug ) {
            echo "<pre>$query</pre>";
        }
        $res = mysql_query( $query, $this->dbres );
        if( !$res ) {
            die( 'Invalid query: ' . mysql_error($this->dbres) );
        }

        $out_array = array();

        while( $row = mysql_fetch_assoc($res) ) {
            $out_array[] = $row;
        }

        return $out_array;
    }

    function process_query_array($query) {
        if( $this->debug ) {
            echo "<pre>$query</pre>";
        }
        $res = mysql_query( $query, $this->dbres );
        if( !$res ) {
            die( 'Invalid query: ' . mysql_error($this->dbres) );
        }

        $out_array = array();

        while( $row = mysql_fetch_row($res) ) {
            $out_array[] = $row;
        }

        return $out_array;
    }

    function do_query($query) {
        if( $this->debug ) {
            echo "<pre>$query</pre>";
        }
        $res = mysql_query( $query, $this->dbres );
        if( !$res ) {
            die( 'Invalid query: ' . mysql_error($this->dbres) );
        }
    }

}
?>
