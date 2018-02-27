<?php

defined( 'NODEMON_RUNNING') || die( 'Access denied.' );

function create_request( $method, $params = array() ) {
    $request = array(
        'jsonrpc' => '1.0',
        'id' => 'request',
        'method' => $method,
        'params' => $params
    );

    return json_encode( $request );
}

function send_request( $request, $username='', $password='', $serverurl='' ) {
    global $nodeconfig;

    // use node config if no args given
    if( empty($username)) { $username = $nodeconfig['username']; }
    if( empty($password)) { $password = $nodeconfig['password']; }
    if( empty($serverurl)) { $serverurl = $nodeconfig['serverurl']; }

    $conn = curl_init();

    curl_setopt( $conn, CURLOPT_URL, $serverurl );
    curl_setopt( $conn, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $conn, CURLOPT_POST, true );
    curl_setopt( $conn, CURLOPT_POSTFIELDS, $request );
    curl_setopt( $conn, CURLOPT_HTTPHEADER, array('Content-Type: text/plain') );
    curl_setopt( $conn, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
    curl_setopt( $conn, CURLOPT_USERPWD, $username . ':' . $password );

    $response = curl_exec( $conn );
    curl_close( $conn );

    return json_decode( $response, true );
}

try {
    $getnetinfo = send_request(
        create_request( 'getnetworkinfo' )
    );

    $getnetinfofailed = false;
} catch (\Exception $e) {
    $getnetinfofailed = true;
    $getnetinfofailedreason = "Timed out: " . $e->getMessage();
}

try {
    $getpeerinfo = send_request(
        create_request( 'getpeerinfo' )
    );

    $getpeerinfofailed = false;
} catch (\Exception $e) {
    $getpeerinfofailed = true;
    $getpeerinfofailedreason = "Timed out: " . $e->getMessage();
}

try {
    $listbanned = send_request(
        create_request( 'listbanned' )
    );

    $listbannedfailed = false;
} catch (\Exception $e) {
    $listbannedfailed = true;
    $listbannedfailedreason = "Timed out: " . $e->getMessage();
}

try {
    $getbcinfo = send_request(
        create_request( 'getblockchaininfo' )
    );

    $getbcinfofailed = false;
} catch (\Exception $e) {
    $getbcinfofailed = true;
    $getbcinfofailedreason = "Timed out: " . $e->getMessage();
}

try {
    $getnettotals = send_request(
        create_request( 'getnettotals' )
    );

    $getnettotalsfailed = false;
} catch (\Exception $e) {
    $getnettotalsfailed = true;
    $getnettotalsfailedreason = "Timed out: " . $e->getMessage();
}

try {
    $getmpinfo = send_request(
        create_request( 'getmempoolinfo' )
    );

    $getmpinfofailed = false;
} catch (\Exception $e) {
    $getmpinfofailed = true;
    $getmpinfofailedreason = "Timed out: " . $e->getMessage();
}

if ( $getnetinfofailed != true ) {
    if ( $getnetinfo ['result']['version'] > 150000 ) {
        try {
            $uptime = send_request(
                create_request( 'uptime' )
            );

            $getuptimefailed = false;
        } catch (\Exception $e) {
            $getuptimefailed = true;
            $getuptimefailedreason = "Timed out: " . $e->getMessage();
        }
    }
}
?>
