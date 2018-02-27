<?php

defined( 'NODEMON_RUNNING') || die( 'Access denied.' );

// See https://github.com/xblau/node-interface/wiki/Configuration
// for more info about these options.

$nodeconfig = array(
    'pagetitle' => 'Bitcoin Node Interface',
    'pagedesc' => '',
    'autorefresh' => 300,
    'serverurl' => 'http://127.0.0.1:8332/',
    'coinname' => "BTC",
    'projectname' => 'Bitcoin Core',
    'username' => 'rpcuser',
    'password' => 'rpcpass',
    'broadcast' => false,
);

?>
