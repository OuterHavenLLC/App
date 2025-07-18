<?php
 $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https://" : "http://";
 $host = $protocol.$_SERVER["HTTP_HOST"]."/";
 header("Location: $host");
?>