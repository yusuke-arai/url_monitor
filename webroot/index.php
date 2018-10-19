<?php
require_once(__DIR__ . '/../const.php');

$db = new SQLite3(DB_FILE, SQLITE3_OPEN_READWRITE);
$result = $db->query('SELECT * FROM urls;');
?>
<!DOCTYPE html>
<html>
<head>
<title>URL monitor</title>
<style type="text/css">
table { border-collapse: collapse; }
tr.green td { background-color: lightgreen; }
tr.red td { background-color: orangered; }
th, td { border: 1px solid gray; padding: 5px; }
th { background-color: lightgray; }
</style>
<script type="text/javascript">
const delete_func = url_id => {
    if (confirm('Delete?')) location.href = 'delete.php?id=' + url_id;
};
const check = () => {
    if (confirm("Force re-check?\nChanges will be discarded.")) location.href = 'check.php';
};
</script>
<body>
<h1>URL monitor</h1>
<form method="POST" action="update.php">
<table>
<thead>
<tr>
<th>Name</th>
<th>URL</th>
<th>Timeout(sec)</th>
<th>Status</th>
<th>Last check</th>
<th>Delete</th>
<th>Graph</th>
</tr>
</thead>
<tbody>
<?php while($result !== false && $url = $result->fetchArray()): ?>
<tr class="<?= $url['status_code'] == 0 ? 'green' : ($url['status_code'] > 0 ? 'red' : '') ?>">
<td><input type="text" name="desc[]" value="<?= $url['desc'] ?>"><input type="hidden" name="id[]" value="<?= $url['id'] ?>"></td>
<td><input type="text" name="url[]" value="<?= $url['url'] ?>"></td>
<td><input type="text" name="timeout[]" value="<?= $url['timeout'] ?>"></td>
<td><?= $url['message'] ?></td>
<td><?= !empty($url['modified']) ? $url['modified'] : '-' ?></td>
<td style="text-align: center;"><button type="button" onclick="delete_func(<?= $url['id'] ?>);">Del</button></td>
<td style="text-align: center;"><a href="graph.php?id=<?= $url['id'] ?>" target="_blank">open</a></td>
</tr>
<?php endwhile; ?>
<tr>
<td><input type="text" name="desc[]" value=""></td>
<td><input type="text" name="url[]" value=""></td>
<td><input type="text" name="timeout[]" value="10"></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
</tbody>
</table>
<p><input type="submit" value="Save changes"> <button type="button" onclick="check();">Force re-check</button></p>
</form>
