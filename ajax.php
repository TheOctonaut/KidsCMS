<?php
//ajax handler
$subd = "/kidsacademy";
require_once($_SERVER['DOCUMENT_ROOT'].$subd."/Classes/User.php");

if($_REQUEST['act'] == 'locmapxml'){
	// Select all the rows in the markers table
	$loc = new Location();
	$result = $loc->listLocations();
	if($result != false){

		header("Content-type: text/xml");

		// Start XML file, echo parent node
		echo '<markers>';

		// Iterate through the rows, printing XML nodes for each
		while ($row = @mysql_fetch_assoc($result)){
		  // ADD TO XML DOCUMENT NODE
		  if($row['usable'] == '1'){
			  $str = str_replace("POINT(", "", $row['map_coords']);
			  $str = str_replace(")", "", $str);
			  $pts = explode(" ", $str);
			  $tl = new Location();
			  $tl->setId($row['location_id']);
			  $cbikes = $tl->getNumBikes();
			  echo '<marker ';
			  echo 'name="' . parseToXML($row['location_name']) . '" ';
			  echo 'lat="' . $pts[0] . '" ';
			  echo 'lng="' . $pts[1] . '" ';
			  echo 'bikes="' . $cbikes . '" ';
			  echo 'lid="' . $row['location_id'] . '" ';
			  echo '/>';
		  }
		}

		// End XML file
		echo '</markers>';
	}
} elseif($_REQUEST['act'] == 'validate'){
	$val = trim($_REQUEST['val']);
	$valHelper = new User();
	if($_REQUEST['in'] == 'name'){
		//validate the name supplied on the registration field
		echo $valHelper->is_valid_name($val);
	} elseif($_REQUEST['in'] == 'display_name'){
		//validate the displayname supplied on the registration field
		echo $valHelper->is_valid_name($val);
	} elseif($_REQUEST['in'] == 'contact_number'){
		//validate the displayname supplied on the registration field
		echo $valHelper->is_valid_contact_number($val);
	} elseif($_REQUEST['in'] == 'email'){
		//check the email matches our email rules
		if($valHelper->is_valid_email($val) == true){
			//check the email doesn't already exist in the db
			if($valHelper->is_unique_email($val) == true){
				echo "1";
			} else {
				//the reason this isn't just echoed out is that the false response
				//will be handled differently in the My Details page, allowing users to
				//change email addresses - possibly.
				echo "0";
			}
		} else {
			echo "0";
		}
	} elseif($_REQUEST['in'] == 'password'){
		//check the email matches our email rules
		echo $valHelper->is_valid_password($val);
	}
}
?>