<?php
$link = mysql_connect('localhost', 'root', 'root')
    or die('Could not connect: ' . mysql_error());
mysql_select_db('rangde') or die('Could not select database');


?>