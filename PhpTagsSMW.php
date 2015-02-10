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

const PHPTAGS_SMW_VERSION = '1.2.0';

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

/**
 * @codeCoverageIgnore
 */
$wgHooks['ParserFirstCallInit'][] = function() {
	if ( !defined( 'PHPTAGS_VERSION' ) ) {
	throw new MWException( "\n\nYou need to have the PhpTags extension installed in order to use the PhpTags SMW extension." );
	}
	$needVersion = '4.0.2';
	if ( version_compare( PHPTAGS_VERSION, $needVersion, '<' ) ) {
		throw new MWException( "\n\nThis version of the PhpTags SMW extension requires the PhpTags extension $needVersion or above.\n You have " . PHPTAGS_VERSION . ". Please update it." );
	}
	if ( PHPTAGS_HOOK_RELEASE != 6 ) {
		throw new MWException( "\n\nThis version of the PhpTags SMW extension is outdated and not compatible with current version of the PhpTags extension.\n Please update it." );
	}
	return true;
};

/**
 * @codeCoverageIgnore
 */
$wgHooks['PhpTagsRuntimeFirstInit'][] = function() {
	\PhpTags\Hooks::addJsonFile( __DIR__ . '/PhpTagsSMW.json' );
};

// Preparing classes for autoloading
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
