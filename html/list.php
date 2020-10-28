<?php
require './config.php';

$mac = isset($_GET['mac']) ? $_GET['mac'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

if($mac != '')
    $list = $db -> query("SELECT * FROM cmx_info WHERE CLIENT_MAC='$mac' ORDER BY SEEN_TIME DESC");
else if($date != '')
    $list = $db -> query("SELECT * FROM cmx_info WHERE FROM_UNIXTIME(SEEN_TIME, '%Y.%m.%d') = '$date' ORDER BY SEEN_TIME DESC");

?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <title>CMX INFO</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="./common.css">
    </head>
    <body>
        <table class="tbl_list">
            <thead>
                <tr>
                    <th>no</th>
                    <th>IPv4</th>
                    <th>AP Mac</th>
                    <th>Client Mac</th>
                    <th>Seen Time</th>
                    <th>Manufacturer</th>
                    <th>OS</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>X</th>
                    <th>Y</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $list -> fetch_array()) {?>
                    <tr>
                        <td><?=$row['no']?></td>
                        <td><?=$row['IPV4']?></td>
                        <td><?=$row['AP_MAC']?></td>
                        <td><?=$row['CLIENT_MAC']?></td>
                        <td><?=date('Y.m.d H:i:s', $row['SEEN_TIME'])?></td>
                        <td><?=$row['MANU']?></td>
                        <td><?=$row['OS']?></td>
                        <td><?=$row['LAT']?></td>
                        <td><?=$row['LNG']?></td>
                        <td><?=$row['X']?></td>
                        <td><?=$row['Y']?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </body>
</html>
