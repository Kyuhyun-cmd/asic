<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require './config.php';

$mac = $_POST['mac'];
$get_list = $db -> query("SELECT * FROM cmx_info WHERE CLIENT_MAC='$mac' ORDER BY no DESC LIMIT 20");

$rows = [];
while($row = $get_list -> fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);
?>
