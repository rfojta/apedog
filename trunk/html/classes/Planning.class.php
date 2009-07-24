<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Planning
 *
 * @author Richard
 */
class Planning {
    protected $dbres;

    protected $term_id;
    protected $lc_id;
    protected $target_values;

    protected $area_query = 'select * from areas';
    protected $kpi_query = 'select * from kpis where area = ';

    function __construct( $dbres, $term_id, $user ) {
        $this->dbres = $dbres;
        $this->term_id = $term_id;

        $lc = new LC($dbres);
        $this->lc_id = $lc->get_lc_by_user($user);

        $this->target_values = new Tracking($dbres);

    }

    function get_area_list() {
        $query = $this->area_query;

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

    function get_kpis ($area_id ) {
        $query = $this->kpi_query
            . mysql_real_escape_string($area_id, $this->dbres);

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

    protected function get_form_content_row( $area_id, $area_name ) {
        echo '<li>';
        echo "\n";
        echo $area_name . ': ';
        $actual = $this->target_values->get_actual(
            $this->lc_id, $this->term_id, $area_id
        );

        echo '<input name="'
            . $this->lc_id . '-'
            . $this->term_id . '-'
            . $area_id .'" value="' . $actual . '">';
        echo "\n";
        echo '</li>';
        echo "\n";
    }

    function get_form_content() {
        $area_list = $this->get_area_list();

        echo "<ul>\n";
        foreach( $area_list as $row ) {
            $area_id = $row['id'];
            $area_name = $row['name']; 
            $this->get_form_content_row( $area_id, $area_name );
        }
        echo "</ul>\n";
    }

    protected function set_values($tokens, $value ) {
        $this->target_values->set_actual(
            $tokens[1], $tokens[2],
            $tokens[3], $value);
    }

    function submit( $post ) {
        foreach( $post as $key => $value ) {
        // $tokens = array();
            if( preg_match('/^(\d)-(\d)-(\d)$/', $key, $tokens) ) {
                if( $value > 0 ) {
                    $this->set_values($tokens, $value);
                }
            }
        }
    }
}

?>
