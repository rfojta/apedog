<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GenericController
 *
 * @author Richard
 */
class GenericController {
//put your code here

    protected $model;
    protected $view;
    protected $child_view;
    protected $parent_view;

    // Developer can define parent controller
    protected $parent_conf;

    protected $parent2_conf;

    // Developer can define child model
    protected $child_conf;

    // This entinty name
    protected $name;

    protected $request;

    // for inserting purposes
    protected $insert_cache = array();

    /**
     *
     * @param <type> $model data model object for related data table
     * @param <type> $links further configuration of certain behaviour
     */
    function  __construct($model, $links = array()) {
        $this->model = $model;

        $this->parent_conf = $links['parent'];
        $this->parent2_conf = $links['parent2'];
        $this->child_conf = $links['child'];
        $this->name = $links['name'];

        $this->view = new ViewController($this->name, $this, null);
        $this->child_view = new ChildView($this->name, $this->child_conf, $this);

        if( isset( $this->parent_conf['name']) ) {
            $this->parent_view =
                new ParentView($this->name,
                $this->parent_conf, $this);
        }
        elseif( is_array( $this->parent_conf ) ) {
            $this->parent_view = array();
            $parent_conf = $this->parent_conf;
        // new style to set more than one parent
            foreach( $parent_conf as $name => $p_conf ) {
                $this->parent_view[$name] = new ParentView(
                    $this->name, $p_conf, $this);
            }
        }
    }

    public function get_model() {
        return $this->model;
    }

    public function set_view( $view ) {
        $this->view = $view;
    }

    /**
     * reset object cache
     */
    protected function clear_cache() {
        $this->insert_cache = array();
    }

    /**
     * Check whether controller cache is used and call insert into table.
     */
    protected function flush() {
        if(count($this->insert_cache) > 0) {
            $columns = array_keys($this->insert_cache);
            $values = array_values($this->insert_cache);
            $this->model->insert($columns, $values);
            $this->clear_cache();
        }
    }

    /**
     * Calls update into DB, when id is 'new', store value into cache
     * @param <type> $field table column
     * @param <type> $value value
     * @param <type> $id row id
     */
    protected function update($field, $value, $id) {
        if( $field == 'id') {
        // cannot update id
        }
        elseif( $id == 'new' ) {
            $this->insert_cache[$field] = $value;
        }
        elseif( $this->model->update($field, $value, $id) ) {
            echo "... updated $field!<br>";
        }
        else {
        // field is not editable
        }
    }

    /**
     * display html input with loaded value
     * @param $id of deleted item
     */
    protected function delete_item($id) {
        $this->model->delete_row($id);
    }


    /**
     * Handles page form submit
     * @param <type> $post HTTP POST data
     */
    public function submit($post) {

        $this->clear_cache();

        foreach($post as $key => $value) {
        // $tokens = array();
            if( preg_match('/^(\w+)-(\w+)$/', $key, $tokens) ) {
                $this->set_values($tokens, $value);
            }
        }

        $this->flush();
    }

    /**
     * Calls update with proper parameters
     * @param <type> $tokens parsed input name
     * @param <type> $value
     */
    protected function set_values($tokens, $value) {
        $this->update($tokens[2], $value, $tokens[1]);
    }

    /**
     * View<br>
     * Generates select tag with option according to this type
     * @param <type> $id source object id
     * @param <type> $selected current target object id
     */
    public function get_list_box($id, $selected) {
        $rows = $this->model->find_all();
        $name = $this->name;
        echo "<select name=\"$id-$name\">";
        echo "<option value=\"" . $row['id'] . "\"";
        if( $row[id] == $selected ) {
            echo "selected=\"1\"";
        }
        echo ">";
        echo "NULL</option>";
        foreach( $rows as $row ) {
            echo "<option value=\"" . $row['id'] . "\"";
            if( $row[id] == $selected ) {
                echo "selected=\"1\"";
            }
            echo ">";
            echo $this->model->get_row_label($row)
                . "</option>";
        }
        echo "</select>";
    }

    public function has_child() {
        return isset( $this->child_conf);
    }

    public function child_list($id) {
        $this->child_view->child_list($id);
    }


    /**
     * Proxy method for view
     * @param <type> $request
     */
    public function get_form_content($request) {
        $this->view->get_form_content($request);
    }


    /**
     * View<br>
     * Retrieve rows from table
     * @param <type> $id
     * @return array of db rows
     */
    public function child_rows($id) {
        $name = $this->name;
        $model = $this->child_conf['model'];
        $rows = $model->find_by($name, $id);
        return $rows;
    }

    public function is_parent( $name ) {
        if( isset ($this->parent_conf['name']) && $name == $this->parent_conf['name']
        // || $name = $this->parent2_conf['name']

        ) {
            return true;
        }
        elseif( isset( $this->parent_conf[$name])) {
            return true;
        }

    }

    public function parent_list($key, $id, $selected) {
        if( is_array( $this->parent_view )) {
            $this->parent_view[$key]->parent_list($key, $id, $selected);
        } else {
            $this->parent_view->parent_list($key, $id, $selected);
        }
    }

    public function get_label($id) {
        $this->view->get_label($id );
    }


}
?>
