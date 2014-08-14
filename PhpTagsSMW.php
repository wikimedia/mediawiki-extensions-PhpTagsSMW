<?php
/**
 * Main entry point for the PhpTags SMW extension.
 *
 * @link https://www.mediawiki.org/wiki/Extension:PhpTags_SMW Documentation
 * @defgroup PhpTags
 * @ingroup Extensions
 * @author Pavel Astakhov <pastakhov@yandex.ru>
 * @licence GNU General Public Licence 2.0 or later
 */

// Check to see if we are being called as an extension or directly
if ( !defined('MEDIAWIKI') ) {
	die( 'This file is an extension to MediaWiki and thus not a valid entry point.' );
}

if ( !defined( 'PHPTAGS_VERSION' ) ) {
	die( 'ERROR: The <a href="https://www.mediawiki.org/wiki/Extension:PhpTags">extension PhpTags</a> must be installed for the extension PhpTags SMW to run!' );
}

$needVersion = '3.4.0';
if ( version_compare( PHPTAGS_VERSION, $needVersion, '<' ) ) {
	die(
		'<b>Error:</b> This version of extension PhpTags SMW needs <a href="https://www.mediawiki.org/wiki/Extension:PhpTags">PhpTags</a> ' . $needVersion . ' or later.
		You are currently using version ' . PHPTAGS_VERSION . '.<br />'
	);
}

if ( PHPTAGS_HOOK_RELEASE != 5 ) {
	die (
			'<b>Error:</b> This version of extension PhpTags SMW is not compatible to current version of the PhpTags extension.'
	);
}

define( 'PHPTAGS_SMW_VERSION' , '1.1.0' );

// Register this extension on Special:Version
$wgExtensionCredits['phptagssmw'][] = array(
	'path'				=> __FILE__,
	'name'				=> 'PhpTags SMW',
	'version'			=> PHPTAGS_SMW_VERSION,
	'url'				=> 'https://www.mediawiki.org/wiki/Extension:PhpTags_SMW',
	'author'			=> '[https://www.mediawiki.org/wiki/User:Pastakhov Pavel Astakhov]',
	'descriptionmsg'	=> 'phptagssmw-desc'
);

// Allow translations for this extension
$wgMessagesDirs['PhpTagsSMW'] =				__DIR__ . '/i18n';
$wgExtensionMessagesFiles['PhpTagsSMW'] =	__DIR__ . '/PhpTagsSMW.i18n.php';

// Specify the function that will initialize the parser function.
/**
 * @codeCoverageIgnore
 */
$wgHooks['PhpTagsRuntimeFirstInit'][] = 'PhpTagsSMWInit::initializeRuntime';

// Preparing classes for autoloading
$wgAutoloadClasses['PhpTagsSMWInit']	= __DIR__ . '/PhpTagsSMW.init.php';

$wgAutoloadClasses['PhpTagsObjects\\SMWExtArrays']	= __DIR__ . '/includes/SMWExtArrays.php';
$wgAutoloadClasses['PhpTagsObjects\\SMWExtSQI']		= __DIR__ . '/includes/SMWExtSQI.php';

/**
 * Add files to phpunit test
 * @codeCoverageIgnore
 */
//$wgHooks['UnitTestsList'][] = function ( &$files ) {
//	$testDir = __DIR__ . '/tests/phpunit';
//	$files = array_merge( $files, glob( "$testDir/*Test.php" ) );
//	return true;
//};
//
//$wgParserTestFiles[] = __DIR__ . '/tests/parser/PhpTagsSMWTests.txt';
