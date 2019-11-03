<?php
	include "handler_lib.php";
	include "settings.php";

	$result = array();
	$mysqli = new mysqli($host, $user, $password, $database);
	if ($mysqli->connect_errno){
		$result['error'] = "Connection error: "."(".$mysqli->connect_errno.") ". $mysqli->connect_error; 
	}
	else
	{
		if (array_key_exists('action',$_REQUEST)){
			switch($_REQUEST['action'])
			{
				case "show_books":
					$from = array_key_exists('from',$_REQUEST)? $_REQUEST['from'] : "";
					$to = array_key_exists('to',$_REQUEST)? $_REQUEST['to'] : "";
					$sort = array_key_exists('sort',$_REQUEST)? $_REQUEST['sort'] : "";
					$result = show_books($mysqli, $from, $to, $sort);
					break;
				case "add":
					$result = change($mysqli, $_REQUEST['action'], array('name' => $_REQUEST['name'],'author' => $_REQUEST['author'],'date' => $_REQUEST['date'], 'key_words' => $_REQUEST['key_words'], 'description' => $_REQUEST['description']));
				case "delete":
					$result = delete($mysqli, array('name' => $_REQUEST['name'],'author' => $_REQUEST['author'],'date' => $_REQUEST['date']));
					break;
				case "find":
					$result = show_books($mysqli, $_REQUEST['from'], $_REQUEST['to'], $_REQUEST['sort'], $_REQUEST['column'], $_REQUEST['pattern']);
					break;
				case "rangeId":
					$result = getRangeId($mysqli, $_REQUEST['table']);
					break;
				default:
					$result['error'] = "Unknow action";
					break;
			}
		}
		else{
			$result['error'] = "Key 'action' don't exists in request";
		}
	}
	echo json_encode($result);
?>