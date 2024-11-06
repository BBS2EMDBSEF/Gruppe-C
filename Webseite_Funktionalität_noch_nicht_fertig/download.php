<?php
$dir = '/';
$files = array_diff(scandir($dir), array('.', '..'));

foreach ($files as $file) {
    echo "<a href='uploads/$file' download>$file herunterladen</a><br>";
}
?>
