<?php


    /**
     * Cosmetic tweaking aside, here's where the stations are configured.
     * For each line, provide an array containing stop names to be displayed.
     * 
     * Use the text strings corresponding with your desired stop(s) based on
     * how they appear in these files:
     * 
     *    http://developer.mbta.com/lib/rthr/red.json
     *    http://developer.mbta.com/lib/rthr/blue.json
     *    http://developer.mbta.com/lib/rthr/orange.json
     * 
     */
    $data =
        array(
        "red"     => array( "Kendall/MIT", "Charles/MGH" ),
        "orange"  => array( "Downtown Crossing" ),
        "blue"    => array( "Government Center" )
    );


?>

<style type="text/css">

    tr.station td {
        font-size: 28px;
        line-height: 45px;
        background: black;
        background-image: none;
        border-radius: 0;
    }


    .station-red td {
        border-top: 1px solid #cb0027;
        border-bottom: 1px solid #98001d;
        color: #e5002c;
    }


    .station-orange td {
        border-top: 1px solid #ff7e00;
        border-bottom: 1px solid #cc6500;
        color: orange;
    }

    .station-blue td {
        border-top: 1px solid #007fd6;
        border-bottom: 1px solid #0061a3;
        color: #4da1ff;
    }

    .trip { font-size: 13px; height: 45px; }
    .trip .train { font-size: 22px; }
    .trip .time { font-size: 20px; text-align: center; }
    .trip .time.arr { color: #c3ae00;}
</style>
<?php

templateTable( 'open' );
foreach ( $data as $color => $stops ) {

    $cells = parseLine( $color, $stops );
    foreach ( $cells as $destination=>$trains ) {
        templateDestination( $color, $destination, $trains );
    }


}
templateTable( 'close' );

function templateDestination( $color, $station, $destinations ) {

    templateStation( $color, $station );
    foreach ( $destinations as $train=>$distances ) {
        echo "<tr class=\"trip\">";
        echo "<td></td>";
        echo "<td class=\"train\">$train</td>";

        // Next train
        if ( isset( $distances[0] ) ) {
            $display = gmdate( "i", $distances[0] );
            if ( $display < 10 ) { $display = substr( $display, -1, 1 ); }
            if ( $display < 1 ) { $display = 'ARR'; }
            else { $display .= "m"; }
            echo "<td class=\"" . ( $display=='ARR'?'arr':'' ) . " time\">" . $display . "</td>";
        }
        else {
            echo "<td class=\"time time-empty\">&mdash;</td>";
        }

        // Following train (@TODO Not DRY, I know!)
        if ( isset( $distances[1] ) ) {
            $display = gmdate( "i", $distances[1] );
            if ( $display < 10 ) { $display = substr( $display, -1, 1 ); }
            if ( $display < 1 ) { $display = 'ARR'; }
            else { $display .= "m"; }
            echo "<td class=\"" . ( $display=='ARR'?'arr':'' ) . " time\">" . $display . "</td>";
        }
        else {
            echo "<td class=\"time time-empty\">&mdash;</td>";
        }

        echo "</tr>";
    }

}

function templateTable( $tag = 'open' ) {

    if ( $tag == 'open' ) {
        echo "<table>";
    } else {
        echo "</table>";
    }

}

function templateStation( $color = null, $name = '' ) {

    echo "<tr class=\"station station-$color\">";
    echo "<td style=\"width: 30px;\">";
    echo "<img src=\"$color.svg\"  style=\"width: 30px;\">";
    echo "</td>";
    echo "<td class=\"station-name\" style=\"width: 240px\">";
    echo "$name";
    echo "</td>";
    echo "<td colspan=\"2\">";
    echo "</tr>";
}


/**
 * Query the MBTA site for a specific line and return an array of results
 * based on how far away trains are.
 *
 * @param string  $color A supported line color, currently: red, orange, blue
 * @param array   $stops An array with relevant station stops you care about.
 *
 * @access public
 *
 * @return array Associative array per Stop and train servicing it, similar to:
 * <code>
 * Array
 * (
 *    [Downtown Crossing] => Array
 *         (
 *             [Forest Hills] => Array
 *                 (
 *                     [0] => 33
 *                     [1] => 340
 *                     [2] => 599
 *                     [3] => 965
 *                     [4] => 1107
 *                 )
 *
 *             [Oak Grove] => Array
 *                 (
 *                     [0] => 366
 *                     [1] => 821
 *                 )
 *
 *         )
 *
 * )
 * </code> 
 * 
 */
function parseLine( $color, $stops ) {

    if ( !in_array( $color, array( 'red', 'orange', 'blue' ) ) ) {
        die( "Error" );
    }

    $csv = "http://developer.mbta.com/lib/rthr/$color.json";
    $file = file_get_contents( $csv );

    if ( $file === false ) {
        die( "Error" );
    }

    $file = json_decode( $file );
    $trips = $file->TripList->Trips;
    $result = array();

    foreach ( $trips as $trip ) {

        foreach ( $trip->Predictions as $pID=>$pKeys ) {
            if ( in_array( $pKeys->Stop, $stops ) ) {
                $result[$pKeys->Stop][$trip->Destination][] = $pKeys->Seconds;
                sort( $result[$pKeys->Stop][$trip->Destination] );
            }
        }

    }

    return $result;
}
