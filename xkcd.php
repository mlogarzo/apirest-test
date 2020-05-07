<?php

$json = file_get_contents('https://xkcd.com/info.0.json').PHP_EOL;

// genero un array basado en el string que obtuve
// si no paso el parámetro true, decodifica el mensaje como un objeto. Lo queremos como array
$data = json_decode( $json, true);

echo $data['img'].PHP_EOL;

