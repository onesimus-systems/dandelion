<?php
/**
 * Display something vaguely comprehensible in the event of a totally unrecoverable error.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * This method was taken from the MediaWiki project.s
 */

/**
 * Display something vaguely comprehensible in the event of a totally unrecoverable error.
 * Does not assume access to *anything*; no globals, no autoloader, no database.
 * Safe for PHP4.
 *
 * Calling this function kills execution immediately.
 *
 * @param string $type Which entry point we are protecting. One of:
 *   - index.php
 *   - load.php
 *   - api.php
 *   - cli
 *
 * @note Since we can't rely on anything, the minimum PHP versions and Dandelion current
 * version are hardcoded here
 */
function PHPVersionError( $type ) {
    $dVersion = '5.0.0';
    $minimumVersionPHP = '5.3.7';

    $phpVersion = phpversion();
    $protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
    $message = "Dandelion {$dVersion} requires at least PHP version {$minimumVersionPHP}, you are using PHP {$phpVersion}.";

    if ( $type == 'cli' ) {
        $finalOutput = "You are using PHP version {$phpVersion} but Dandelion {$dVersion} needs PHP {$minimumVersionPHP} or higher. ABORTING.\n" .
            "Check if you have a newer php executable with a different name, such as php5.\n";
    } elseif ( $type == 'site' ) {
        $pathinfo = pathinfo( $_SERVER['SCRIPT_NAME'] );
        /*$encLogo = htmlspecialchars(
            str_replace( '//', '/', $pathinfo['dirname'] . '/' ) .
            'skins/common/images/mediawiki.png'
        );*/

        header( "$protocol 500 MediaWiki configuration Error" );
        header( 'Content-type: text/html; charset=UTF-8' );
        // Don't cache error pages!  They cause no end of trouble...
        header( 'Cache-control: none' );
        header( 'Pragma: no-cache' );

        $finalOutput = <<<HTML
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="UTF-8" />
		<title>Dandelion {$dVersion}</title>
		<style media='screen'>
			body {
				color: #000;
				background-color: #fff;
				font-family: sans-serif;
				padding: 2em;
				text-align: center;
			}
			p, img, h1 {
				text-align: left;
				margin: 0.5em 0;
			}
			h1 {
				font-size: 120%;
			}
		</style>
	</head>
	<body>
		<!--<img src="{$encLogo}" alt='The MediaWiki logo' />-->
		<h1>Dandelion {$dVersion} internal error</h1>
		<div class='error'>
		<p>
			{$message}
		</p>
		<p>
			Please consider <a href="http://www.php.net/downloads.php">upgrading your copy of PHP</a>.
			PHP versions less than 5.3.0 are no longer supported by the PHP Group and will not receive
			security or bugfix updates.
		</p>
		</div>
	</body>
</html>
HTML;
        // Handle everything that's not index.php
    } else {
        // So nothing thinks this is JS or CSS
        $finalOutput = ( $type == 'load.php' ) ? "/* $message */" : $message;
        header( "$protocol 500 Dandelions configuration Error" );
    }
    echo "$finalOutput\n";
    die( 1 );
}
