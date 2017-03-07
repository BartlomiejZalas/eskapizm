<?php
echo isset($_SERVER['HTTPS']) . ":". $_SERVER['HTTPS'] . "<br>";
echo isset($_SERVER['SSL']) . ":". $_SERVER['SSL'] . "<br>";
echo isset($_SERVER['REDIRECT_HTTPS']) . ":". $_SERVER['REDIRECT_HTTPS'] . "<br>";
echo isset($_SERVER['HTTP_SSL']) . ":". $_SERVER['HTTP_SSL'] . "<br>";
echo isset($_SERVER['HTTP_X_FORWARDED_PROTO']) . ":". $_SERVER['HTTP_X_FORWARDED_PROTO'] . "<br>";

?>