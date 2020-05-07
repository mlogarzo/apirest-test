<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

/*
//-- Autenticacion via HTTP
//------------------------------->
// Si hay un usuario registrado en $_SERVER quiero obtener su valor, sino valor vacio.
$user = array_key_exists( 'PHP_AUTH_USER', $_SERVER ) ? $_SERVER['PHP_AUTH_USER'] : '';
$pwd = array_key_exists( 'PHP_AUTH_PW', $_SERVER ) ? $_SERVER['PHP_AUTH_PW'] : '';

if ( $user !== 'mlogarzo' || $pwd !== '1234' ) {
  header('Status-Code: 403');

  echo json_encode(
    [
      'error' => 'User/pass incorrect',
    ]
    );
  die;
}

//-------------------------------<
*/


/*
//-- Autenticacion via HMAC
//------------------------------->
// Si un encabezado no esta en el pedido, no autentica.
if ( 
    !array_key_exists('HTTP_X_HASH', $_SERVER) ||
    !array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) ||
    !array_key_exists('HTTP_X_UID', $_SERVER)
  ) {
    die;
}

// Suponiendo encabezados correctos.
// list: permite asignar mas de un variable en una misma instruccion.
list( $hash, $uid, $timestamp ) = [
    $_SERVER['HTTP_X_HASH'],
    $_SERVER['HTTP_X_UID'],
    $_SERVER['HTTP_X_TIMESTAMP'],
];

// Clave secreta servidor cliente.
$secret = 'Quien no? Who knows?, dijo';

$newHash = sha1($uid.$timestamp.$secret);

// Comparo hash del usuario vs genereado.
if ( $newHash !== $hash ) {
  header( 'Status-Code: 403' );
  
  echo json_encode(
    [
      'error' => "No autorizado. Hash esperado: $newHash, hash recibido: $hash",
    ]
  );
  die;

}

//-------------------------------<
*/


/*
// Autenticación vía Access Tokens
//------------------------------->
// Validamos si el propio servidor recibio token del cliente.
// Si no recibio token, die.
if ( !array_key_exists('HTTP_X_TOKEN', $_SERVER) ) {

    die;
}

// Si recibio token, validamos en auth server.
// $url = 'http://localhost:8001';

// Llamada via curl al servidor de autenticacion para que diga si el token es valido.
$ch = curl_init( $url );
curl_setopt(
  $ch,
  CURLOPT_HTTPHEADER,
  [
    "X-Token: {$_SERVER['HTTP_X_TOKEN']}"
  ]
  );

// Obtener el resultado de lo que devuelve el servidor.
curl_setopt(
  $ch,
  CURLOPT_RETURNTRANSFER,
  true
);

// Llamada.
$ret = curl_exec( $ch );

// Validar si resultado del serv es true.
if ( $ret !== 'true') {
  die;
}

//-------------------------------<
*/

// Definicion interna de los recursos disponibles.
$allowedResourceTypes = [
  'cakes',
  'authors',
  'genres',

];

// Validacion si el tipo de recurso que nos solicitan esta dentro de los recursos posibles.
// El parametro resoruceType tendra que ser alguno de los permitidos.
$resourceType = $_GET['resource_type'];

if ( !in_array( $resourceType, $allowedResourceTypes ) ) {
  http_response_code( 400 );
  die;
}

// Defino los recursos.
$cakes = [
  1 => [
    'name' => '🥕Carrot cake',
    'id_cake' => 1,
    'id_author' => 2,
  ],
  2 => [
    'name' => '🍏Apple crumble',
    'id_cake' => 2,
    'id_author' => 2,
  ],
  3 => [
    'name' => '🍰Shortcake',
    'id_cake' => 3,
    'id_author' => 1,
  ],
  4 => [
    'name' => '🍌Banana bread',
    'id_cake' => 5,
    'id_author' => 2,
  ],

];

// Variable $_SERVER que provee PHP.
// A través de esta variable conozco el metodo que se utilizo en la peticion.

// Genero la respuesta asumiendo que el pedido es correcto.

// Levantamos id del recurso especifico buscado.
// Sintaxis if inline. operador ternario.
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : '';

switch ( strtoupper($_SERVER['REQUEST_METHOD']) ) {
  case 'GET':
    // Si el tipo de recurso no esta, devuelvo coleccion entera.
    if ( empty( $resourceId ) ) {
      echo json_encode( $cakes );
    } else {
      // Asumo que se corresponde con alguna de las claves.
      if ( array_key_exists( $resourceId, $cakes ) ) {
          echo json_encode( $cakes[ $resourceId ] );
      } else {
        http_response_code( 404 );
      }
    }

    break;
  case 'POST':
    $json = file_get_contents('php://input');

    $cakes[] = json_decode( $json, true );

    echo array_keys( $cakes )[ count ($cakes) - 1 ];
    // echo json_encode( $cakes );
    
    break;
  case 'PUT':
    // Validamos que el recurso buscado exista.
    if ( !empty( $resourceId ) && array_key_exists( $resourceId, $cakes ) ) {
      // Tomamos la entrada cruda del usuario.
      $json = file_get_contents('php://input');

      // Transformamos el json recibido a un nuevo elemento.
      $cakes[ $resourceId ] = json_decode( $json, true );

      // Retornamos la coleccion modificada en formato json.
      echo json_encode( $cakes );
    }

    break;
  case 'DELETE':
    // Validamos que el recurso exista. no podriamos eliminar un recurso que no existe.
    if ( !empty( $resourceId ) && array_key_exists( $resourceId, $cakes ) ) {
      // Eliminamos el recurso.
      unset( $cakes[ $resourceId ] );

    }

    echo json_encode( $cakes );
  
  break;
}