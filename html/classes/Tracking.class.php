<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tracking
 *
 * @author Richard
 */
class Tracking {
    private $dbres;
    private $actual;
    private $get_actual_query =
    'select actual from tracking
     where lc = :lc_id
       and term = :term_id
       and area = :area_id';

    private $get_actual_count_query =
    'select count(actual) from tracking
     where lc = :lc_id
       and term = :term_id
       and area = :area_id';

    private $update_actual_query =
    'update tracking set actual = :actual, updated = current_timestamp()
     where lc = :lc_id
       and term = :term_id
       and area = :area_id';

    private $insert_actual_query =
    'insert into tracking (lc, term, area, actual, updated)
       values(:lc_id, :term_id, :area_id, :actual, current_timestamp())';



    function Tracking($dbres, $actual = 0) {

        $this->dbres = $dbres;
        $this->actual = $actual;

    }
    //put your code here
    function parse_query_actual($pre_query, $values) {
        $tags = array(':lc_id', ':term_id', ':area_id', ':actual');
        $query = str_replace($tags, $values, $pre_query);
        return $query;
    }

    function parse_query_target($pre_query, $values) {
        $tags = array(':lc_id', ':term_id', ':area_id', ':target');
        $a_query = str_replace('actual', 'target' , $pre_query);
        $query = str_replace($tags, $values, $a_query);
        return $query;
    }

    function parse_query($pre_query, $values) {
        if( $this->actual == 0 ) {
            return $this->parse_query_target($pre_query, $values);
        } else {
            return $this->parse_query_actual($pre_query, $values);
        }
    }


    function get_actual($lc_id, $term_id, $area_id) {
        $count = $this->get_actual_count($lc_id, $term_id, $area_id);
        if( $count == 0 ) {
            return '';
        }
        $pre_query = $this->get_actual_query;
        $values = array($lc_id, $term_id, $area_id);
        $query = $this->parse_query($pre_query, $values);

        $res = mysql_query( $query, $this->dbres );
        if( !$res ) {
            die( 'Invalid query: ' . mysql_error($this->dbres) );
        }

        $out_array = array();

        while( $row = mysql_fetch_assoc($res) ) {
            $out_array[] = $row;
        }

        if( $this->actual == 0 ) {
            return $out_array[0]['target'];
        } else {
            return $out_array[0]['actual'];
        }
    }

    function get_actual_count($lc_id, $term_id, $area_id) {
        $pre_query = $this->get_actual_count_query;
        $values = array($lc_id, $term_id, $area_id);
        $query = $this->parse_query($pre_query, $values );

        $res = mysql_query( $query, $this->dbres );
        if( !$res ) {
            die( 'Invalid query: ' . mysql_error($this->dbres) );
        }

        $out_array = array();

        while( $row = mysql_fetch_row($res) ) {
            $out_array[] = $row;
        }

        return $out_array[0][0];
    }

    function set_actual($lc_id, $term_id, $area_id, $actual) {
        $count = $this->get_actual_count($lc_id, $term_id, $area_id);
        if( $count == 0 ) {
            $this->insert_actual($lc_id, $term_id, $area_id, $actual);
        } else {
            $this->update_actual($lc_id, $term_id, $area_id, $actual);
        }
    }

    function insert_actual($lc_id, $term_id, $area_id, $actual) {
        $values = array( $lc_id, $term_id, $area_id, $actual );
        $pre_query = $this->insert_actual_query;
        $query = $this->parse_query( $pre_query, $values );

        $res = mysql_query( $query, $this->dbres );
        if( !$res ) {
            die( 'Invalid query: ' . mysql_error($this->dbres) );
        }

    }

    function update_actual($lc_id, $term_id, $area_id, $actual) {
        $values = array( $lc_id, $term_id, $area_id, $actual );
        $pre_query = $this->update_actual_query;
        $query = $this->parse_query( $pre_query, $values );

        $res = mysql_query( $query, $this->dbres );
        if( !$res ) {
            die( 'Invalid query: ' . mysql_error($this->dbres) );
        }
    }

}
?>
