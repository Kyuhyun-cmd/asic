<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require './config.php';

$top_mac = $db -> query("SELECT CLIENT_MAC, COUNT(CLIENT_MAC) AS CNT FROM cmx_info GROUP BY CLIENT_MAC ORDER BY CNT DESC LIMIT 5");
$date_list = $db -> query("SELECT FROM_UNIXTIME(SEEN_TIME, '%Y.%m.%d') as DT, COUNT(no) AS CNT FROM cmx_info GROUP BY DT ORDER BY DT DESC LIMIT 5");
$date_visitor = $db -> query("SELECT FROM_UNIXTIME(SEEN_TIME, '%Y.%m.%d') as DT, COUNT(DISTINCT CLIENT_MAC) AS CNT FROM cmx_info GROUP BY DT ORDER BY DT DESC LIMIT 5");

$today_top_manu = $db -> query("
    SELECT MANU, FROM_UNIXTIME(SEEN_TIME, '%Y.%m.%d') AS DT,
        COUNT(MANU) AS CNT
    FROM cmx_info
    WHERE FROM_UNIXTIME(SEEN_TIME, '%Y-%m-%d')=CURDATE()
        AND MANU != ''
    GROUP BY MANU ORDER BY CNT DESC LIMIT 5
");
$today_top_os = $db -> query("
    SELECT OS, FROM_UNIXTIME(SEEN_TIME, '%Y.%m.%d') AS DT,
        COUNT(OS) AS CNT
    FROM cmx_info
    WHERE FROM_UNIXTIME(SEEN_TIME, '%Y-%m-%d')=CURDATE()
        AND OS != ''
    GROUP BY OS ORDER BY CNT DESC LIMIT 5
");

$today_visitor =  $db -> query("SELECT COUNT(DISTINCT CLIENT_MAC) as CNT FROM cmx_info WHERE FROM_UNIXTIME(SEEN_TIME, '%Y-%m-%d') = CURDATE()") -> fetch_array();
?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <title>CMX INFO</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="./common.css">
        <script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
    </head>
    <body>
        <div class="top-bar">
            <div class="today">Today Visitors: <?=$today_visitor['CNT']?></div>
        </div>
        <div class="list-wrap">
            <h2>TOP 5 MAC</h2>
            <div class="contents">
                <?php
                if($top_mac -> num_rows != 0) {
                    while($row = $top_mac -> fetch_array()) {?>
                    <a class="li" href="./list.php?mac=<?=$row['CLIENT_MAC']?>">
                        <div class="title"><?=$row['CLIENT_MAC']?></div>
                        <div class="info"><?=$row['CNT']?> 회</div>
                    </a>
                    <?php
                    }
                } else {?>
                    <a class="li nodata">정보가 없습니다</a>
                <?php }?>
            </div>
        </div>
        <div class="list-wrap">
            <h2>DAILY REPORT</h2>
            <div class="contents">
                <?php
                if($date_list -> num_rows != 0) {
                    while($row = $date_list -> fetch_array()) {?>
                    <a class="li" href="./list.php?date=<?=$row['DT']?>">
                        <div class="title"><?=$row['DT']?></div>
                        <div class="info"><?=$row['CNT']?> 회</div>
                    </a>
                    <?php
                    }
                } else {?>
                    <a class="li nodata">정보가 없습니다</a>
                <?php }?>
            </div>
        </div>
        <div class="list-wrap">
            <h2>Visitors</h2>
            <div class="contents">
                <?php
                if($date_visitor -> num_rows != 0) {
                    while($row = $date_visitor -> fetch_array()) {?>
                    <a class="li" href="./list.php?date=<?=$row['DT']?>">
                        <div class="title"><?=$row['DT']?></div>
                        <div class="info"><?=$row['CNT']?> 명</div>
                    </a>
                    <?php
                    }
                } else {?>
                    <a class="li nodata">정보가 없습니다</a>
                <?php }?>
            </div>
        </div>

        <div style="display: inline-block; width: 300px;">
            <div class="list-wrap">
                <h2>Today Top Manufacturer</h2>
                <div class="contents">
                    <?php
                    if($today_top_manu -> num_rows != 0) {
                        while($row = $today_top_manu -> fetch_array()) {?>
                        <a class="li" href="./list.php?date=<?=$row['DT']?>">
                            <div class="title"><?=$row['MANU']?></div>
                            <div class="info"><?=$row['CNT']?> 회</div>
                        </a>
                        <?php
                        }
                    } else {?>
                        <a class="li nodata">정보가 없습니다</a>
                    <?php }?>
                </div>
            </div>
            <div class="list-wrap" style="margin-top: 10px;">
                <h2>Today Top OS</h2>
                <div class="contents">
                    <?php
                    if($today_top_os -> num_rows != 0) {
                        while($row = $today_top_os -> fetch_array()) {?>
                        <a class="li" href="./list.php?date=<?=$row['DT']?>">
                            <div class="title"><?=$row['OS']?></div>
                            <div class="info"><?=$row['CNT']?> 회</div>
                        </a>
                        <?php
                        }
                    } else {?>
                        <a class="li nodata">정보가 없습니다</a>
                    <?php }?>
                </div>
            </div>
        </div>


        <div class="mac-track">
            <form id="search">
                <input type="text" name="mac" placeholder="MAC 입력">
                <input type="submit" value="track">
            </form>
            <div id="map" style="width: 100%; height: 400px;"></div>
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBvb5kr1G-_u33somzDkOhkbIvMdeg0kr0"></script>
        </div>
        <script>
            var map, marker = [], trace_path = null;
            var infowindow = new google.maps.InfoWindow();

            function sleep(ms) {
                return new Promise((resolve) => setTimeout(resolve, ms));
            }

            async function drop_marker(pos_list) {
                var i;
                for(i = 0; i < pos_list.length; i++) {
                    await sleep(500);
                    marker.push(new google.maps.Marker({
                        position: pos_list[i],
                        map: map
                    }));

                    marker[i].addListener('click', (function(marker, i) {
                        return function() {
                            infowindow.setContent(`
                                MAC: ${pos_list[i].mac} <br>
                                Manufacturer: ${pos_list[i].manu} <br>
                                OS: ${pos_list[i].os}
                            `);
                            infowindow.open(map, marker[i]);
                        }
                    })(marker, i));
                }
            }

            $(function() {

                map = new google.maps.Map(document.getElementById('map'), {zoom: 12});
                $('#search').submit(function(e) {
                    e.preventDefault();

                    for(var i = 0; i < marker.length; i++) {
                        marker[i].setMap(null);
                    }

                    marker = [];

                    if(trace_path != null)
                        trace_path.setMap(null);

                    $.ajax({
                        type: 'post',
                        url: './ajax.trace_mac.php',
                        data: $('#search').serialize(),
                        success: function(data) {
                            var location_list = [];

                            data = JSON.parse(data);
                            var flag = 0;
                            $.each(data, function() {
                                console.log(this);
                                if(this.LAT == null || this.LNG == null) return true;

                                location_list.push({
                                    mac: this.CLIENT_MAC,
                                    manu: this.MANU,
                                    os: this.OS,
                                    lat: parseFloat(this.LAT),
                                    lng: parseFloat(this.LNG)
                                });
                            });

                            map.setCenter(location_list[0]);
                            map.setZoom(30);

                            var trace_path = new google.maps.Polyline({
                                path: location_list,
                                geodesic: true,
                                strokeColor: '#FF0000',
                                strokeOpacity: 1.0,
                                strokeWeight: 2,
                                map: map
                            });

                            drop_marker(location_list);
                        }
                    })
                });
            });
        </script>
    </body>
</html>
