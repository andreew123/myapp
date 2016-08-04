<?php

$index = file_get_contents( 'public/index.html' );
header( 'Content-Type: text/html, charset=utf-8' );
echo $index;
