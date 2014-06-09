<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$config = simplexml_load_file('/var/www/html/python/config-scoa.xml');

$active_group = 'financeiro';
$active_record = TRUE;

$hostname = $config->mysql->servidor;
$username = $config->mysql->usuario;
$password = $config->mysql->senha;

$db['financeiro']['hostname'] = $hostname;
$db['financeiro']['username'] = $username;
$db['financeiro']['password'] = $password;
$db['financeiro']['database'] = 'FINANCEIRO';
$db['financeiro']['dbdriver'] = 'mysql';
$db['financeiro']['dbprefix'] = '';
$db['financeiro']['pconnect'] = TRUE;
$db['financeiro']['db_debug'] = TRUE;
$db['financeiro']['cache_on'] = FALSE;
$db['financeiro']['cachedir'] = '/var/www/html/allink/Clientes/propostas/database_cache/';
$db['financeiro']['char_set'] = 'iso-8859-1';
$db['financeiro']['dbcollat'] = 'latin1_general_ci';
$db['financeiro']['swap_pre'] = '';
$db['financeiro']['autoinit'] = TRUE;
$db['financeiro']['stricton'] = FALSE;

$db['clientes']['hostname'] = $hostname;
$db['clientes']['username'] = $username;
$db['clientes']['password'] = $password;
$db['clientes']['database'] = 'CLIENTES';
$db['clientes']['dbdriver'] = 'mysql';
$db['clientes']['dbprefix'] = '';
$db['clientes']['pconnect'] = TRUE;
$db['clientes']['db_debug'] = TRUE;
$db['clientes']['cache_on'] = FALSE;
$db['clientes']['cachedir'] = '/var/www/html/allink/Clientes/propostas/database_cache/';
$db['clientes']['char_set'] = 'iso-8859-1';
$db['clientes']['dbcollat'] = 'latin1_general_ci';
$db['clientes']['swap_pre'] = '';
$db['clientes']['autoinit'] = TRUE;
$db['clientes']['stricton'] = FALSE;

$db['gerais']['hostname'] = $hostname;
$db['gerais']['username'] = $username;
$db['gerais']['password'] = $password;
$db['gerais']['database'] = 'GERAIS';
$db['gerais']['dbdriver'] = 'mysql';
$db['gerais']['dbprefix'] = '';
$db['gerais']['pconnect'] = TRUE;
$db['gerais']['db_debug'] = TRUE;
$db['gerais']['cache_on'] = FALSE;
$db['gerais']['cachedir'] = '/var/www/html/allink/Clientes/propostas/database_cache/';
$db['gerais']['char_set'] = 'iso-8859-1';
$db['gerais']['dbcollat'] = 'latin1_general_ci';
$db['gerais']['swap_pre'] = '';
$db['gerais']['autoinit'] = TRUE;
$db['gerais']['stricton'] = FALSE;

$db['usuarios']['hostname'] = $hostname;
$db['usuarios']['username'] = $username;
$db['usuarios']['password'] = $password;
$db['usuarios']['database'] = 'USUARIOS';
$db['usuarios']['dbdriver'] = 'mysql';
$db['usuarios']['dbprefix'] = '';
$db['usuarios']['pconnect'] = TRUE;
$db['usuarios']['db_debug'] = TRUE;
$db['usuarios']['cache_on'] = FALSE;
$db['usuarios']['cachedir'] = '/var/www/html/allink/Clientes/propostas/database_cache/';
$db['usuarios']['char_set'] = 'iso-8859-1';
$db['usuarios']['dbcollat'] = 'latin1_general_ci';
$db['usuarios']['swap_pre'] = '';
$db['usuarios']['autoinit'] = TRUE;
$db['usuarios']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */
