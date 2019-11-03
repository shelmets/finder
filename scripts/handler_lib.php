 <?php

/*
$row_data_book
 {
 	"id_book":,
 	"author":,
 	"date":,
 	"key_words":,
 	"description":
 }
$row_data_author
{
	"id_author":,
	"name": 
}
*/
function getRangeId($mysql_conn, $table){
	$name_idfield = $mysql_conn->query("show columns from $table")->fetch_assoc()["Field"];
	return $mysql_conn->query("select min($name_idfield) as min, max($name_idfield) as max from $table")->fetch_assoc();
}
function show_books($mysql_conn, $from, $to, $sort, $column="id_book", $pattern=".*")
{
	$result  = array('action'=>'show_books','id_book'=>array(), 'author'=>array(), 'date'=>array(), 'key_words'=>array(), 'description'=>array());
	$sort_block = ($sort=="increase") ? "order by id_book":"order by id_book desc";
	$query = "select * from books";
	$query_mod = "where id_book>=$from and id_book<=$to and $column regexp '$pattern' $sort_block";
	$query = ($from==""and $to=="")? $query : $query.$query_mod;
	if ($res = $mysql_conn->query($query)){
		while ($row = $res->fetch_assoc()){
			$result['id_book'][] = $row['id_book'];
			try {
				$result['author'][] = getAuthorName($mysql_conn, $row['author']);
			} catch (Exception $e) {
				$result['author'][] = "error";
			}
			$result['name'][] = $row['name'];
			$result['date'][] =  $row['date_create'];
			$result['key_words'][] = $row['key_words'];
			$result['description'][] = $row['description'];
		}
	}
	else{
		$errno = $mysql_conn->errno;
		$error = $mysql_conn->error; 
		$result['error'] = "Function show_books: Error, $errno, $error";
	}
	return $result;
}

function getAuthorName($mysql_conn, $id){
	$select_query = "select name from authors where id_author=$id";
	if ($res = $mysql_conn->query($select_query)){
		if ($res->num_rows > 0){
			return $res->fetch_assoc()["name"];
		}
		else{
			throw new Exception("Function getAuthorName: Result of query '$insert_query' is null");
		}
	} else{
		$errno = $mysql_conn->errno;
		$error = $mysql_conn->error;
		throw new Exception("Function getAuthorName: Error, $errno, $error");
	}
}

function getAuthorId($mysql_conn, $name)
{
	$select_query = "select id_author as id from authors where name='$name'";
	if ($res = $mysql_conn->query($select_query)){
		if ($res->num_rows > 0){
			return $res->fetch_assoc()["id"];
		}
		else{
			$insert_query = "insert into authors(name) value($name)";
			if ($mysql_conn->query($insert_query)){
				if ($res = $mysql_conn->query($select_query)){
					return $res->fetch_assoc()["id"];
				}
				else{
					throw new Exception("Error query '$select_query'");
				}
			}
			else{
				$errno = $mysql_conn->errno;
				$error = $mysql_conn->error;
				throw new Exception("Function getAuthorId: Error, $errno, $error");
			}
		}
	}else{
		$errno = $mysql_conn->errno;
		$error = $mysql_conn->error;
		throw new Exception("Function getAuthorId: Error, $errno, $error");
	}
}
function add($mysql_conn, $row_data_book){
	$result = array('action'=>'delete');
	$date = $row_data_book["date"];
	$name = $row_data_book["name"];
	$key_words = $row_data_book["key_words"];
	$description = $row_data_book["description"];
	try {
		$author_id = getAuthorId($mysql_conn, $row_data_book["author"]);	
	}catch (Exception $e) {
		$result["error"] = "Function change: $e";
		return $result;
	}
	$query = "insert into books(id_author,date,key_words,description) values($id,'$date','$key_words','$description')";
}
function delete($mysql_conn, $row_data_book){
	$result = array('action'=>'delete');
	$date = $row_data_book["date"];
	$name = $row_data_book["name"];
	try {
		$author_id = getAuthorId($mysql_conn, $row_data_book["author"]);	
	}catch (Exception $e) {
		$result["error"] = "Function change: $e";
		return $result;
	}
	$query = "delete from books where author=$author_id and date_create='$date' and name='$name'";
	if (!$res = $mysql_conn->query($query)){
		$errno = $mysql_conn->errno;
		$error = $mysql_conn->error;
		$result["error"] = "Function delete:$date Error, $errno, $error";
	}
	return $result;
}
function change($mysql_conn, $action_change, $row_data_book){
	$result  = array();
	$query = "";
	$author = $row_data_book["author"];
	$date = $row_data_book["date"];
	switch ($action_change){
		case "add":
			$key_words = $row_data_book["key_words"];
			$description = $row_data_book["description"];
			try {
				$id = getAuthorId($mysql_conn, $author);	
			}catch (Exception $e) {
				$result["error"] = "Function change: $e";
				return $result;
			}
			$query = "insert into books(id_author,date,key_words,description) values($id,'$date','$key_words','$description')";
			break;
		case "delete":
			$query = "delete from books where author='$author' and date_create=$date";
			break;
		case "edit":
			$query = "update books set";
			foreach ($row_data_book as $key=>$value){
				if (is_null($value))
					$query=$query." $key='$value'";
			}
			$query=$query." where id_book = $id";
			break;
		default:
			$result["error"] = "Function change: action_change is not add or delete or edit";
			return $result;
			break;
	}
	if ($res = $mysql_conn->query($query)){
		$result["success"] = $res->fetch_assoc();
	}
	else{
		$errno = $mysql_conn->errno;
		$error = $mysql_conn->error;
		$result["error"] = "Function change: Error, $errno, $error";
	}
	return $result;
}
?>