<?php
require_once(__DIR__ . '/../const.php');

$db = new SQLite3(DB_FILE, SQLITE3_OPEN_READWRITE);

if (!empty($_REQUEST)) {
    $stmt = $db->prepare('SELECT * FROM logs WHERE url_id = :url_id ORDER BY date DESC LIMIT 864;');
    $stmt->bindValue(':url_id', $_REQUEST["id"]);
    $result = $stmt->execute();
    $logs = [];
    while($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $logs[] = [
            "url_id" => $row['url_id'],
            "date" => $row['date'],
            "name_lookup_ms" => $row['name_lookup_ms'],
            "connect_ms" => max($row['connect_ms'] - $row['name_lookup_ms'], 0),
            "ssl_connect_ms" => max($row['ssl_connect_ms'] - $row['connect_ms'], 0),
            "start_transfer_ms" => max($row['start_transfer_ms'] - $row['ssl_connect_ms'], 0),
            "total_ms" => max($row['total_ms'] - $row['start_transfer_ms'], 0)
        ];
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<script src="moment.2.22.2.min.js"></script>
<script src="chart.2.7.2.min.js"></script>
<script type="text/javascript">
const drawChart = () => {
    new Chart(document.getElementById('chart'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_map(function($x) { return str_replace("/", "-", $x["date"]); }, $logs)) ?>,
            datasets: [{
                label: "name lookup",
                backgroundColor: "#e65d53",
                pointRadius: 0,
                data: <?= json_encode(array_map(function($x) { return $x["name_lookup_ms"]; }, $logs)) ?>
            }, {
                label: "connect",
                backgroundColor: "#cd4976",
                pointRadius: 0,
                data: <?= json_encode(array_map(function($x) { return $x["connect_ms"]; }, $logs)) ?>
            }, {
                label: "ssl connect",
                backgroundColor: "#775ea2",
                pointRadius: 0,
                data: <?= json_encode(array_map(function($x) { return $x["ssl_connect_ms"]; }, $logs)) ?>
            }, {
                label: "start transfer",
                backgroundColor: "#4b9ad8",
                pointRadius: 0,
                data: <?= json_encode(array_map(function($x) { return $x["start_transfer_ms"]; }, $logs)) ?>
            }, {
                label: "total",
                backgroundColor: "#29a5dd",
                pointRadius: 0,
                data: <?= json_encode(array_map(function($x) { return $x["total_ms"]; }, $logs)) ?>
            }]
        },
        options: {
            scales: {
                xAxes: [{
                    type: "time",
                    distribution: "linear",
                    time: { unit: "day", displayFormats: { day: "MMM D" } }
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Time [ms]'
                    }
                }]
            }
        }
    });
};
</script>
<body onload="drawChart();">
<div style="width: 100%;">
<canvas id="chart"></canvas>
</div>
