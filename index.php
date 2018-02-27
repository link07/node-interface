<?php

$startscript = microtime( true );

define( 'NODEMON_RUNNING', true );

if( file_exists( 'config.php' ) ) {
    require 'config.php';
} else {
    die( 'Configuration file (config.php) not found.' );
}

require 'utils.php';

if( !isset( $formid ) ) {
    require 'requests.php';
    require 'forms.php';
}

if ( $getbcinfofailed != true ) {
    $pruned = $getbcinfo['result']['pruned'] ? 'true' : 'false';
}

if ( $getpeerinfofailed != true ) {
    $cpeers = count( $getpeerinfo['result'] );
} else {
    $cpeers = 0;
}

if ( $listbannedfailed != true ) { 
    $bpeers = count( $listbanned['result'] );
} else {
    $bpeers = 0;
}

$localservbits  = hexdec('0x'.$getnetinfo['result']['localservices']);
$localservnames = decode_services($localservbits);

?>
<!DOCTYPE html>
<title><?php echo $nodeconfig['pagetitle']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="shortcut icon" href="favicon.png">
<link rel="stylesheet" href="style.css">
<?php
if( !isset( $formid ) && $nodeconfig['autorefresh'] > 0 ) {
    printf( '<meta http-equiv="refresh" content="%s" >', $nodeconfig['autorefresh'] );
}
?>

<body>
    <h1><?php echo $nodeconfig['pagetitle']; ?></h1>

    <?php

    if( isset( $formresult ) ) {
        echo $formresult;
        echo '<br><br><a href="">Return to the main page</a>';
        exit;
    }

    if( isset($getnetinfo['error']['code']) ) {
        $getnetinfofailed = true;
        $getnetinfofailedreason = $getnetinfo['error']['message'];
    } elseif( !isset( $getnetinfo['result']['version'] ) ) {
        $getnetinfofailed = true;
        $getnetinfofailedreason = "Something went wrong (getnetworkinfo failed). Check your config and try again.";
    }
    
    // only add h4's if there is a desc
    if ( $nodeconfig['pagedesc'] ) {
        echo '<h4>' . $nodeconfig['pagedesc'] . '</h4>';
    }
    ?> 

    <a id="about"></a>
    <fieldset>
        <legend>ABOUT THIS NODE</legend>
        <?php
        if ( $getnetinfofailed == true ) {
?>
            <b>Get Net Info Failed!</b><br><br><code><?php echo $getnetinfofailedreason; ?></code>
<?php
        } else {
?>
            <b>Node version:</b> <code><?php echo $getnetinfo['result']['version'].' ('.$getnetinfo['result']['protocolversion'].')';?></code><br>
            <b>Subversion:</b> <code><?php echo $getnetinfo['result']['subversion']; ?></code><br>
            <b>Local services:</b> <code><?php printf("%s (0x%s)", $localservnames, dechex($localservbits)); ?></code><br>
            <b>Relay fee:</b> <code><?php echo $getnetinfo['result']['relayfee']; ?></code>
<?php
            if( isset( $uptime ) ) {
                printf( "<br><b>Uptime:</b> <code>%s</code>\n", seconds_to_time( $uptime['result'] ) );
           }
        }
        ?>
    </fieldset><br>

    <a id="blockchaininfo"></a>
    <fieldset>
        <legend>BLOCKCHAIN INFO</legend>
        <?php
        if ( $getbcinfofailed == true) {
            echo '<b>Get Blockchain Info Failed!</b><br><br><code>' . $getbcinfofailedreason . '</code>';
        } else {
            echo '<b>Chain:</b> <code>' . $getbcinfo['result']['chain'] . '</code><br>';
            echo '<b>Blocks:</b> <code>' . $getbcinfo['result']['blocks'] . '</code><br>';
            echo '<b>Headers:</b> <code>' . $getbcinfo['result']['headers'] . '</code><br>';
            echo '<b>Difficulty:</b> <code>' . $getbcinfo['result']['difficulty'] . '</code><br>';
            echo '<b>Median time:</b> <code>' . date('d/m/Y H:i:s', $getbcinfo['result']['mediantime'] ) . '</code><br>';
            
            if(isset($getbcinfo['result']['size_on_disk'])) {
                printf("<b>Size on disk:</b> <code>%s</code><br>\n", format_bytes($getbcinfo['result']['size_on_disk']));
            }

            echo '<b>Pruned:</b> <code>' . $pruned . '</code>';
        }
        ?>
    </fieldset><br>

    <a id="mempool"></a>
    <fieldset>
        <legend>TX MEMORY POOL INFO</legend>
        <?php
        if ( $getmpinfofailed == true ) {
            echo '<b>Get Mem Pool Info Failed!</b><br><br><code>' . $getmpinfofailedreason . '</code>'; 
        } else {
            echo '<b>Transactions:</b> <code>' . $getmpinfo['result']['size'] . '</code><br>';
            
            if( $getmpinfo['result']['size'] == 0 ) {
                $tmpsize = "empty";
            } else {
                $tmpsize = format_bytes( $getmpinfo['result']['bytes'] );
            }
            echo '<b>Size:</b> <code>' . $tmpsize . '</code><br>';
        }
        ?>
    </fieldset><br>

    <a id="netusage"></a>
    <fieldset>
        <legend>NETWORK USAGE</legend>
        <?php
        if ( $getnettotalsfailed == true ) {
            echo '<b>Get Network Usage Failed!</b><br><br><code>' . $getnettotalsfailedreason . '</code>';
        } else {
            echo '<b>Total received:</b> <code>' . format_bytes( $getnettotals['result']['totalbytesrecv'] ) . '</code><br>';
            echo '<b>Total sent:</b> <code>' . format_bytes( $getnettotals['result']['totalbytessent'] ) . '</code><br>';
        }
        ?>
    </fieldset><br>

    <?php echo $nodeconfig['broadcast'] ? '' : '<!--' ?>
    <a id="broadcast"></a>
    <fieldset>
        <legend>BROADCAST RAW TRANSACTION</legend>
        <form method="post">
            <input type="hidden" name="formid" value="broadcast">
            Raw transaction data:<br>
            <textarea name="transaction" rows="4" cols="50" required></textarea><br><br>
            <input type="submit" value="Broadcast">
        </form>
    </fieldset><br>
    <?php echo $nodeconfig['broadcast'] ? '' : '-->' ?>

    <a id="peers"></a>
    <fieldset>
        <legend><?php printf( 'CONNECTED PEERS (%s)', $cpeers ); ?></legend>
        <?php
        if ( $getpeerinfofailed == true) {
            echo '<b>Get Peers Failed!</b><br><br><code>' . $getpeerinfofailedreason . '</code>';
        } else {
            echo '<table>
                    <tr>
                        <th>addr</th>
                        <th>services</th>
                        <th>conntime</th>
                        <th>version</th>
                        <th>subver</th>
                        <th>inbound</th>
                        <th>banscore</th>
                    </tr>';

                $tinbound = 0; $toutbound = 0;

                foreach( $getpeerinfo['result'] as $peer ) {
                    $inbound = $peer['inbound'] ? 'true' : 'false';
                    $conntime = date('d/m/Y H:i:s', $peer['conntime'] );

                    $servbits = hexdec('0x' . $peer['services']);
                    $servnames = decode_services($servbits);

                    echo '<tr>';
                    printf( '<td>%s</td>', $peer['addr'] );
                    printf( '<td title="%s">0x%s</td>', $servnames, dechex($servbits) );
                    printf( '<td title="%s">%s</td>', $conntime, $peer['conntime'] );
                    printf( '<td>%s</td>', $peer['version'] );
                    printf( '<td>%s</td>', $peer['subver'] );
                    printf( '<td>%s</td>', $inbound );
                    printf( '<td>%s</td>', $peer['banscore'] );
                    echo '</tr>';

                    $peer['inbound'] ? $tinbound++ : $toutbound++;
                }

            echo '</table>';
            echo '<br><b>Total inbound/outbound:</b> <code>' . $tinbound . '/' . $toutbound . '</code>';
        }
        ?>
    </fieldset><br>

    <a id="banned"></a>
    <fieldset>
        <legend><?php printf( 'BANNED PEERS (%s)', $bpeers ); ?></legend>
        <?php
        if ( $getpeerinfofailed == true) {
            echo '<b>Get Peers Failed!</b><br><br><code>' . $getpeerinfofailedreason . '</code>';
        } else {
            echo '<table>
                    <tr>
                        <th>address</th>
                        <th>banned since</th>
                        <th>banned until</th>
                        <th>ban reason</th>
                    </tr>';
                    foreach( $listbanned['result'] as $peer ) {
                    $bansince = date('d/m/Y H:i:s', $peer['ban_created'] );
                    $banuntil = date('d/m/Y H:i:s', $peer['banned_until'] );

                    echo '<tr>';
                    printf( '<td>%s</td>', $peer['address'] );
                    printf( '<td title="%s">%s</td>', $bansince, $peer['ban_created'] );
                    printf( '<td title="%s">%s</td>', $banuntil, $peer['banned_until'] );
                    printf( '<td>%s</td>', $peer['ban_reason'] );
                    echo '</tr>';
                    }
            echo '</table>';
        }
        ?>
    </fieldset><br>

    <?php
    $endscript = microtime( true );
    $loadtime = $endscript - $startscript;
    ?>
    <div class="footer">
        Made by <a href="https://github.com/xblau">xBlau</a>, modified by <a href="https://github.com/link07">link07</a>.
        Powered by <?php echo $nodeconfig['projectname']; ?>. Generated in <?php echo number_format( $loadtime, 4 ) ?> seconds.
        Source code <a href="https://github.com/link07/node-interface">here</a>.
        <br><br>
    </div>
</body>
