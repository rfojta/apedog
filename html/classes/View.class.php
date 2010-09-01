<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of View - view of bsc excel structure
 *
 * @author rf
 */
class BSC_View {

	//put your code here
	private $dbutil;
	private $csfs;
	private $term_id;
	private $csf_id;
	private $user;
	private $query;
	private $page;
	private $responsibles_query;
	private $ths;

	function __construct($dbutil, $csfs, $user, $current_term) {
		$this->dbutil = $dbutil;
		$this->csfs = $csfs;
		$this->user = $user;
		$this->query = "select s.name strategy, sa.name action, "
			. " o.name operation, r.name responsible, o.when, o.status, o.id as operation_id"
			. " from bsc_strategy s join bsc_strategic_action sa on (sa.strategy = s.id) "
			. " join bsc_operations o on (o.strategic_action = sa.id) "
			. " join bsc_responsible r on (o.responsible = r.id)";
		if ($this->csfs != 'all') {
			$this->query .= " where s.csfs = " . $this->csfs;
		}

		$this->page = "view.php?";
		$this->lc_query = "select lc from users where login = ";
		if (isset($_GET['term_id'])) {
			$this->term_id = $_GET['term_id'];
		} else {
			$this->term_id = $current_term;
		}
		$this->query .= " and s.term = " . $this->term_id;
		$this->responsibles_query = "select r.name from bsc_responsible r
			join bsc_responsible_term rt on r.id=rt.responsible
			join terms t on rt.term=t.id
			where t.id = " . $this->term_id;
	}

	/**
	 * generates html table from strategy,
	 * operations, actions and responsible
	 */
	function get_form_content() {
		$lcs = $this->dbutil->process_query_assoc($this->lc_query . "'" . $this->user . "'");
		$this->query = $this->query . " and s.lc = " . $lcs['0']['lc'];
		$rows = $this->dbutil->process_query_assoc($this->query);
		$csf_query = 'select * from csfs order by 1';
		$csfs = $this->dbutil->process_query_assoc($csf_query);
		$term_query = 'select * from terms order by 2';
		$terms = $this->dbutil->process_query_assoc($term_query);
		$r = $this->dbutil->process_query_assoc($this->responsibles_query);
		$responsibles = $r[0];

		$this->getCsfDropDown($csfs);
		$this->get_term_section($terms);
		$items_with_plus = array('strategy', 'action', 'operation', 'responsible');
		echo "<table id='test1' class='sortable-onload-show-4-5r rowstyle-alt no-arrow max-pages-4 paginate-10'>";
		if ($rows != null) {
			echo "<thead>\n";
			echo "<tr>\n";
			foreach ($rows[0] as $key => $value) {
				if ($key != 'operation_id') {
					$this->ths[] = $key;
				}
				switch ($key) {
					case "strategy":
						echo "<th class=\"sortable-text create-list datatype-text\">" . $key . "</th>";
						break;
					case "action":
						echo "<th class=\"sortable-text create-list datatype-text\">" . $key . "</th>";
						break;
					case "operation":
						echo "<th class=\"sortable-text datatype-text\">" . $key . "</th>";
						break;
					case "responsible":
						echo "<th class=\"sortable-text create-list datatype-text\">" . $key . "</th>";
						break;
					case "when":
						echo "<th class=\"sortable-date datatype-datedmy\">" . $key . "</th>";
						break;
					default:
						if ($key != 'operation_id') {
							echo "<th>" . $key . "</th>";
						}
				}
			}
			echo "</tr>\n";
			echo "</thead>\n";
			foreach ($rows as $row) {
				echo "<tr";
				if ($row['when'] < date('Y-m-d') && $row['status'] != '1') {
					echo " class='overtime";
				} else if ($row['status'] == '1') {
					echo " class='done";
				}
				echo "'>";

				foreach ($row as $key => $value) {
					if ($key == 'status') {
						echo "<td><input type=checkbox name='status-" . $row['operation_id']
						. (($value == 1) ? "' checked=\"checked\"" : "'") . "/></td>";
					} else if ($key != 'operation_id')
						echo "<td>" . htmlspecialchars($value);
					echo "</td>";
				}
				echo "</tr>\n";
			}
			echo "<tfoot id='table_footer'>";
			foreach ($rows[0] as $key => $value) {
				if ($key != 'operation_id') {
					echo "<td id='table_footer'>";
					if (in_array($key, $items_with_plus)) {
						echo "<input type='button' value='+' onclick=\"addRow('test1','$key','$responsibles');\">";
					}
					echo "</td>";
				}
			}
			echo "</tfoot>";
		}
		echo "</table>\n";

		$this->javascripts();
		$this->get_submit_button();
	}

	/**
	 * Builds submit button
	 */
	function get_submit_button() {
		echo '<p>';
		echo '<input type="hidden" name="posted" value="1" />';
		echo '<input type=submit';
		echo ' value="Save" />';
		echo '</p>';
	}

	/**
	 * handle status changes, sends emails
	 */
	function submit($post) {
		$operation_ids = array();
		$operations = $this->dbutil->process_query_assoc($this->query);
		if ($operations != null) {
			foreach ($operations as $row) {
				$operation_ids[$row['operation_id']] = 0;
			}
		}
		foreach ($post as $key => $value) {
			$operation_id;
			if (preg_match('/^status-(\d+)$/', $key, $tokens)) {
				$operation_id = $tokens;
				$operation_ids[$operation_id[1]] = 1;
			}
		}
		foreach ($operation_ids as $op_id => $value) {

			$s = "select status from bsc_operations where id =" . $op_id;
			$old_value = $this->dbutil->process_query_assoc($s);
			$old_status = $old_value[0]['status'];
			$update;
			switch ($old_status) {
				case 1:
					if ($value == 0) {
						$update = "update bsc_operations set status=0 where id = " . $op_id;
						$this->dbutil->do_query($update);
						$this->sendNotification($op_id, 'from "Done" to "Not Done"');
					}
					break;
				case 0:
					if ($value == 1) {
						$update = "update bsc_operations set status=1 where id = " . $op_id;
						$this->dbutil->do_query($update);
						$this->sendNotification($op_id, 'from "Not Done" to "Done"');
					}
					break;
				case null:
					if ($value == 1) {
						$update = "update bsc_operations set status=1 where id = " . $op_id;
						$this->dbutil->do_query($update);
						$this->sendNotification($op_id, 'from "Not Done" to "Done"');
					}
					break;
				default:
					break;
			}
		}
	}

	/**
	 * print simple help
	 */
	function get_help() {
		echo "Columns: strategy, action, operation, responsible, deadline and status<br><br>";
		echo "Use dropdown menus or filter by typing keyword and hitting ENTER.<br>";
		echo "For numeric and date values >, <, = and ! operators are allowed.<br>";
		echo "You can sort by multiple columns using SHIFT.";
	}

	function getCsfDropDown($csfList) {

		echo "CSF: \n";
		echo "<select name=\"csf_id\" id=\"csf_id\"\n";
		echo "onchange=\"window.location.href='" . $this->page .
		"term_id=" . $this->term_id . "&csfs='+this.value\">\n";

		echo "<option value='all'";
		if (isset($_GET['csfs'])) {
			if ('all' == $_GET['csfs']) {
				$this->csf_id = 'all';
				$this->csfs = 'all';
				echo " selected ";
			}
		} else if ('all' == $this->csf_id) {
			echo " selected ";
		}

		echo ">";
		echo 'All';
		echo "</option>\n";

		foreach ($csfList as $csf) {
			echo "<option value=\"" . $csf['id'] . "\"";
			if (isset($_GET['csfs'])) {
				if ($csf['id'] == $_GET['csfs']) {
					$this->csf_id = $csf['id'];
					$this->csfs = $csf['name'];
					echo " selected ";
				}
			} else if ($csf['id'] == $this->csfs) {
				$this->csf_id = $csf['id'];
				echo " selected ";
			}

			echo ">";
			echo $csf['name'];
			echo "</option>\n";
		}

		echo "</select>\n";
	}

	function get_term_section($term_list) {
		echo "Select term: \n";
		echo "<select name=\"term_id\" id=\"term_id\"\n";
		echo "onchange=\"window.location.href='" . $this->page .
		"csfs=" . $this->csf_id . "&term_id='+this.value\">\n";

		foreach ($term_list as $term) {
			echo "<option value=\"" . $term['id'] . "\"";
			if (isset($_REQUEST['term_id'])) {
				if ($term['id'] == $_REQUEST['term_id']) {
					$this->term_id = $term['id'];
					echo " selected ";
				}
			} else if ($term['id'] == $this->term_id) {
				echo " selected";
			}

			echo ">";
			echo date('Y', strtotime($term['term_from'])) . '/' . date('Y', strtotime($term['term_to']));
			echo "</option>\n";
		}

		echo "</select>\n";
	}

	function sendNotification($op_id, $typeOfChange) {
		$query = "select c.name csf_name, s.name strategy_name, sa.name action_name, o.name operation_name, o.when ddl, r.name user, u.email LCPemail, u.name LCPname
	from bsc_responsible r join
	bsc_operations o on r.id=o.responsible join
	bsc_strategic_action sa on sa.id = o.strategic_action join
	bsc_strategy s on s.id = sa.strategy join
	csfs c on s.csfs = c.id join
	users u on u.lc = s.lc
	where o.id = " . $op_id;
		$rows = $this->dbutil->process_query_assoc($query);
		$rows = $rows[0];

		$to = $rows['LCPemail'];
		$user = $rows['user'];
		$subject = 'Apedog - operation status changed by ' . $user;
		$message = "Hello " . $rows['LCPname'] . "!

	$user just changed status $typeOfChange for following operation:

CSF: " . $rows['csf_name'] . "
Strategy: " . $rows['strategy_name'] . "
Strategic action: " . $rows['action_name'] . "
Operation: " . $rows['operation_name'] . "

DDL for finishing this operation was " . $rows['ddl'] . " .
Regards,
Your Apedog.";
		$headers = 'From: noreply@apedog.cz';
		mail($to, $subject, $message, $headers);
	}

	/*
	 * prints all javascripts
	 */

	function javascripts() {
		echo '<script>
function addRow(id,freeColumn, responsibles){
var tbody = document.getElementById(id).getElementsByTagName("tbody")[0];
var row = document.createElement("tr");';
		foreach ($this->ths as $th) {

			echo '
				var data1 = document.createElement("td");
switch ("' . $th . '")
	{
		case freeColumn:
			var input = document.createElement("input");
			input.className="free";
			break;
		case "status":
			var input = document.createElement("input");
			input.type = "checkbox";
			break;
		case "when":
			var input = document.createElement("input");
			input.setAttribute("datepicker","true");
			random = Math.round(Math.random()*1000);
			input.setAttribute("id","fdp"+random);
			input.setAttribute("datepicker_format","YYYY-MM-DD");
			input.className="free";
			break;
		default:
			var input = document.createElement("select");
			var options = getColumnValues(id,"2");

			input.options[0] = new Option("selection 1","value 1");
			input.options[1] = new Option("selection 2","value 2");
			input.options[2] = new Option("selection 3","value 3");
			input.options[3] = new Option("selection 4","value 4");
			break;
	}
row.appendChild(data1);
data1.appendChild(input);
';
		}
		echo '
tbody.appendChild(row);
DatePickerControl.init();
}
function getColumnValues(tableId,columnNo){
var tbody = document.getElementById(tableId).getElementsByTagName("tbody")[0];
var trs = tbody.getElementsByTagName("tr");
}

</script>';
	}

}
?>
