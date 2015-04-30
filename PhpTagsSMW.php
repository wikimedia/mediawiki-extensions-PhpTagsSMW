<?php
/**
 * Main entry point for the PhpTags SMW extension.
 *
 * @link https://www.mediawiki.org/wiki/Extension:PhpTags_SMW Documentation
 * @file PhpTagsSMW.php
 * @defgroup PhpTags
 * @ingroup Extensions
 * @author Pavel Astakhov <pastakhov@yandex.ru>
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @licence GNU General Public Licence 2.0 or later
 */

// Check to see if we are being called as an extension or directly
if ( !defined('MEDIAWIKI') ) {
	die( 'This file is an extension to MediaWiki and thus not a valid entry point.' );
}

const PHPTAGS_SMW_VERSION = '1.5.0';

// Register this extension on Special:Version
$wgExtensionCredits['phptags'][] = array(
	'path'           => __FILE__,
	'name'           => 'PhpTags SMW',
	'version'        => PHPTAGS_SMW_VERSION,
	'url'            => 'https://www.mediawiki.org/wiki/Extension:PhpTags_SMW',
	'author'         => array( '[https://www.mediawiki.org/wiki/User:Pastakhov Pavel Astakhov]', '[https://www.mediawiki.org/wiki/User:JoelKP Joel K. Pettersson]' ),
	'descriptionmsg' => 'phptagssmw-desc',
	'license-name'   => 'GPL-2.0+',
);

// Register message files
$wgMessagesDirs['PhpTagsSMW'] = __DIR__ . '/i18n';

// Register hooks

/**
 * @codeCoverageIgnore
 */
$wgHooks['ParserFirstCallInit'][] = function() {
	if ( !defined( 'PHPTAGS_VERSION' ) ) {
	throw new MWException( "\n\nYou need to have the PhpTags extension installed in order to use the PhpTags SMW extension." );
	}
	$needVersion = '5.0.0';
	if ( version_compare( PHPTAGS_VERSION, $needVersion, '<' ) ) {
		throw new MWException( "\n\nThis version of the PhpTags SMW extension requires the PhpTags extension $needVersion or above.\n You have " . PHPTAGS_VERSION . ". Please update it." );
	}
	if ( PHPTAGS_HOOK_RELEASE != 7 ) {
		throw new MWException( "\n\nThis version of the PhpTags SMW extension is outdated and not compatible with current version of the PhpTags extension.\n Please update it." );
	}
	return true;
};

/**
 * @codeCoverageIgnore
 */
$wgHooks['PhpTagsRuntimeFirstInit'][] = 'PhpTagsSMWHooks::onPhpTagsRuntimeFirstInit';

// Register classes for autoloading
$wgAutoloadClasses['PhpTagsSMWHooks'] = __DIR__ . '/PhpTagsSMW.hooks.php';
$wgAutoloadClasses['PhpTagsSMW\\InputConverter'] = __DIR__ . '/includes/InputConverter.php';

$wgAutoloadClasses['PhpTagsObjects\\SMWExtArrays'] =
	__DIR__ . '/includes/SMWExtArrays.php';
$wgAutoloadClasses['PhpTagsObjects\\SMWExtSQI'] =
	__DIR__ . '/includes/SMWExtSQI.php';
$wgAutoloadClasses['PhpTagsObjects\\SMWWSemanticData'] =
	__DIR__ . '/includes/SMWWSemanticData.php';
$wgAutoloadClasses['PhpTagsObjects\\SMWWSemanticProperty'] =
	__DIR__ . '/includes/SMWWSemanticProperty.php';
$wgAutoloadClasses['PhpTagsObjects\\SMWWSemanticStats'] =
	__DIR__ . '/includes/SMWWSemanticStats.php';

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
