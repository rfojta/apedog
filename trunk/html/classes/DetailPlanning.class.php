<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Planning
 *
 * @author Richard, Krystof
 */
class DetailPlanning {
    protected $dbutil;

    protected $term_id;
    protected $quarter_id;
    protected $area_id;
    protected $lc_id;
    protected $this_page;
    protected $target_values;
    protected $locking;

    protected $area_query = 'select * from areas';
    protected $quarter_query = 'select * from quarters where term = ';
    protected $kpi_query = 'select * from kpis';
    protected $term_query = 'select * from terms';

    function __construct( $dbutil, $term_id, $current_area, $user, $locking ) {
        $this->dbutil = $dbutil;
        $this->term_id = $term_id;
        $this->area_id = $current_area;
        $this->page = 'detail_planning.php?';
        $this->locking = $locking;

        $lc = new LC($dbutil->dbres);
        $this->lc_id = $lc->get_lc_by_user($user);

        $this->target_values = new DetailTracking($dbutil);

    }

    function get_area_list() {
        $query = $this->area_query;
        $rows = $this->dbutil->process_query_assoc($query);
        return $rows;
    }

    protected function get_kpi_input($kpi, $i) {
        $kpi_id = $kpi['id'];
        $quarter_id = $this->quarter_id;
        $lc_id = $this->lc_id;
        $value='';
        if ($quarter_id!=null) {
            $value = $this->target_values
                ->get_value($lc_id, $quarter_id, $kpi_id);
        }
        
        echo "<tr class='kpiRow".$i."'> \n";
        echo "<td> \n";
        echo '<span title="' . $kpi['description'] . '">'
            . $kpi['name'] . ':</span>';
        echo "</td> \n";
        echo "<td> \n";
        echo "<input name=\"kpi-$kpi_id\"";
        if ($this->locking->get_count($this->lc_id, 'NULL', $this->term_id)) {
            echo ' disabled ';
        }
        echo "value=\"$value\" />";
        echo "</td> \n";
        echo "</tr> \n";
        echo "</li> \n";
    }

    protected function get_area_section( $area_list ) {
        echo "Select area: \n";
        echo "<select name=\"area_id\" id=\"area_id\"\n";
        echo "onchange=\"window.location.href='".$this->page."term_id=".$this->term_id
            ."&quarter_id=".$this->quarter_id."&area_id='+this.value\">\n";
        echo "<option value=\"all\"";
        if( isset($_REQUEST['area_id']) ) {
            if('all' == $_REQUEST['area_id']) {
                $this->area_id='all';
                echo " selected ";
            }
        }
        echo ">";
        echo 'All';
        echo "</option>\n";

        foreach( $area_list as $area ) {
            echo "<option value=\"".$area['id']."\"";
            if( isset($_REQUEST['area_id']) ) {
                if( $area['id'] == $_REQUEST['area_id']) {
                    $this->area_id=$area['id'];
                    echo " selected ";
                }
            }

            echo ">";
            echo $area['name'];
            echo "</option>\n";
        }

        echo "</select>\n";
    }

    function get_form_content() {
        $term_list = $this->get_term_list();

        $quarter_list = $this->get_quarter_list($this->term_id);
        $area_list = $this->get_area_list();
        $kpi_list = $this->get_kpi_list($this->area_id);



        $this->get_term_section($term_list);
        $this->get_quarter_section($quarter_list);
        echo '&nbsp;&nbsp;&nbsp;';
        $this->get_area_section($area_list);

        echo "<p>";
        $this->get_locked_echo();
        echo "<table class='detailTable'>";
        $i=0;
        foreach( $kpi_list as $kpi ) {
            $this->get_kpi_input( $kpi, $i);
            $i++;
            if($i>5){
            $i = 0;
        }
        }
        echo "</table>";
        echo "</p>";

        $this->get_submit_button();
    }

    protected function set_values($kpi,$quarter, $value ) {
        $this->target_values->set_value(
            $this->lc_id,
            $quarter,
            $kpi[1],
            $value
        );
    }

    function submit( $post ) {
        $quarter;
        $rec;
        $kpi=array();
        foreach( $post as $key => $value ) {
        // $tokens = array();
            if( $key=='quarter_id') {
                $quarter=$value;
            }

            if( preg_match('/^kpi-(\d+)$/', $key, $tokens) ) {

                if( isset($value) && $value != "" && $quarter!=null ) {
                    $kpi=$tokens;
                    $this->set_values($kpi,$quarter,$value);
                }
            }
        }
    }

    function get_term_list() {
        $query = $this->term_query . ' ORDER BY `number_of_term`';
        $rows = $this->dbutil->process_query_assoc($query);
        return $rows;
    }

    function get_quarter_list($term_id) {
        $query = $this->quarter_query . $term_id . ' ORDER BY `quarter_in_term`';
        $rows = $this->dbutil->process_query_assoc($query);
        $this->quarter_id=$rows[0]['id'];
        return $rows;
    }

    function get_term_section($term_list) {
        echo "Select term: \n";
        echo "<select name=\"term_id\" id=\"term_id\"\n";
        echo "onchange=\"window.location.href='".$this->page."area_id=".$this->area_id."&term_id='+this.value\">\n";

        foreach( $term_list as $term ) {
            echo "<option value=\"".$term['id']."\"";
            if( isset($_REQUEST['term_id']) ) {
                if( $term['id'] == $_REQUEST['term_id']) {
                    $this->term_id=$term['id'];
                    echo " selected ";
                }
            } else if ($term['id']==$this->term_id) {
                    echo " selected";
                }

            echo ">";
            echo date('Y', strtotime($term['term_from'])).'/'.date('Y', strtotime($term['term_to']));
            echo "</option>\n";
        }

        echo "</select>\n";
    }

    function get_quarter_section($quarter_list) {
        echo "Select quarter: \n";
        echo "<select name=\"quarter_id\" id=\"quarter_id\"\n";
        echo "onchange=\"window.location.href='".$this->page."area_id=".$this->area_id."&term_id=".$this->term_id."&quarter_id='+this.value\">\n";

        foreach( $quarter_list as $quarter ) {
            echo "<option value=\"".$quarter['id']."\"";
            if( isset($_REQUEST['quarter_id']) ) {
                if( $quarter['id'] == $_REQUEST['quarter_id']) {
                    $this->quarter_id=$quarter['id'];
                    echo " selected ";
                }
            }

            echo ">";
            echo date('j.n.Y', strtotime($quarter['quarter_from'])).'-'.date('j.n.Y', strtotime($quarter['quarter_to']));
            echo "</option>\n";
        }

        echo "</select>\n";
    }

    function get_kpi_list($area_id) {
        if ($area_id!='all') {
            $query = $this->kpi_query . " where area = " . $this->dbutil->escape($area_id);
        } else {
            $query = $this->kpi_query;
        }
        $rows = $this->dbutil->process_query_assoc($query);
        return $rows;
    }

    function get_submit_button() {
        echo '<p>';
        echo '<input type="hidden" name="posted" value="1" />';
        echo '<input type=submit';
        if ($this->locking->get_count($this->lc_id, 'NULL', $this->term_id)!=0) {
            echo ' disabled';
        }
        echo ' value="Save" />';
        echo '</p>';
    }

    function get_locked_echo(){
        if ($this->locking->get_count($this->lc_id, 'NULL', $this->term_id)) {
            echo '<p><b>Your planning for this period has been locked by MC.</b></p>';
        }
    }
}

?>
