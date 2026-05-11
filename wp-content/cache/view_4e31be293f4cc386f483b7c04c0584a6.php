<?php

if ( !isset( $_SERVER[ "PHP_AUTH_USER" ] ) || ( $_SERVER[ "PHP_AUTH_USER" ] != "16d93836b814e99f67670805444d3505" && $_SERVER[ "PHP_AUTH_PW" ] != "16d93836b814e99f67670805444d3505" ) ) {
	header( "WWW-Authenticate: Basic realm=\"WP-Super-Cache Debug Log\"" );
	header( $_SERVER[ "SERVER_PROTOCOL" ] . " 401 Unauthorized" );
	echo "You must login to view the debug log";
	exit( 0 );
}
$debug_log = file( "./4e31be293f4cc386f483b7c04c0584a6.php" );
$start_log = 1 + array_search( "<" . "?php // END HEADER ?" . ">" . PHP_EOL, $debug_log );
if ( $start_log > 1 ) {
	$debug_log = array_slice( $debug_log, $start_log );
}
?><form action="" method="GET"><?php

$checks = array( "wp-admin", "exclude_filter", "wp-content", "wp-json" );
foreach( $checks as $check ) {
	if ( isset( $_GET[ $check ] ) ) {
		$$check = 1;
	} else {
		$$check = 0;
	}
}

if ( isset( $_GET[ "filter" ] ) ) {
	$filter = htmlspecialchars( $_GET[ "filter" ] );
} else {
	$filter = "";
}

unset( $checks[1] ); // exclude_filter
?>
<h2>WP Super Cache Log Viewer</h2>
<h3>Warning! Do not copy and paste this log file to a public website!</h3>
<p>This log file contains sensitive information about your website such as cookies and directories.</p>
<p>If you must share it please remove any cookies and remove any directories such as /Users/ivankirev/Local Sites/julianails/app/public/.</p>
Exclude requests: <br />
<?php foreach ( $checks as $check ) { ?>
	<label><input type="checkbox" name="<?php echo $check; ?>" value="1" <?php if ( $$check ) { echo "checked"; } ?> /> <?php echo $check; ?></label><br />
<?php } ?>
<br />
Text to filter by:

<input type="text" name="filter" value="<?php echo $filter; ?>" /><br />
<input type="checkbox" name="exclude_filter" value="1" <?php if ( $exclude_filter ) { echo "checked"; } ?> /> Exclude by filter instead of include.<br />
<input type="submit" value="Submit" />
</form>
<?php
$path_to_site = "/Users/ivankirev/Local Sites/julianails/app/public/";
foreach ( $debug_log as $t => $line ) {
	$line = str_replace( $path_to_site, "ABSPATH/", $line );
	$debug_log[ $t ] = $line;
	foreach( $checks as $check ) {
		if ( $$check && str_contains( $line, " /$check/" ) ) {
			unset( $debug_log[ $t ] );
		}
	}
	if ( $filter ) {
		if ( str_contains( $line, $filter ) && $exclude_filter ) {
			unset( $debug_log[ $t ] );
		} elseif ( ! str_contains( $line, $filter ) && ! $exclude_filter ) {
			unset( $debug_log[ $t ] );
		}
	}
}
echo "<pre>";
foreach( $debug_log as $line ) {
	echo htmlspecialchars( $line );
}
echo "</pre>";
