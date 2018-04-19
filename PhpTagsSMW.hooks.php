<?php

/**
 * Contains the implementations of hooks used by PhpTags SMW.
 *
 * @file PhpTagsSMW.hooks.php
 * @ingroup PhpTagsSMW
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @license GPL-2.0-or-later
 */
final class PhpTagsSMWHooks {

	/**
	 * Check on version compatibility
	 * @return bool
	 */
	public static function onParserFirstCallInit() {
		$extRegistry = ExtensionRegistry::getInstance();
		$phpTagsLoaded = $extRegistry->isLoaded( 'PhpTags' );
		// if ( !$extRegistry->isLoaded( 'PhpTags' ) ) { use PHPTAGS_VERSION for backward compatibility
		if ( !( $phpTagsLoaded || defined( 'PHPTAGS_VERSION' ) ) ) {
			throw new MWException( "\n\nYou need to have the PhpTags extension installed in order to use the PhpTags SMW extension." );
		}
		if ( $phpTagsLoaded ) {
			$neededVersion = '5.8.0';
			$phpTagsVersion = $extRegistry->getAllThings()['PhpTags']['version'];
			if ( version_compare( $phpTagsVersion, $neededVersion, '<' ) ) {
				throw new MWException( "\n\nThis version of the PhpTags SMW extension requires the PhpTags extension $neededVersion or above.\n You have $phpTagsVersion. Please update it." );
			}
		}
		if ( !$phpTagsLoaded || PHPTAGS_HOOK_RELEASE != 8 ) {
			throw new MWException( "\n\nThis version of the PhpTags SMW extension is outdated and not compatible with current version of the PhpTags extension.\n Please update it." );
		}
		return true;
	}

	/**
	 * Called when the PhpTags runtime initializes for the first time.
	 *
	 * Used for initialization of things that are only used when
	 * PhpTags is.
	 */
	public static function onPhpTagsRuntimeFirstInit() {
		$version = ExtensionRegistry::getInstance()->getAllThings()['PhpTags SMW']['version'];
		\PhpTags\Hooks::addJsonFile( __DIR__ . '/PhpTagsSMW.non-smw.json', $version );
		if ( defined( 'SMW_VERSION' ) ) {
			\PhpTags\Hooks::addJsonFile( __DIR__ . '/PhpTagsSMW.json', $version . SMW_VERSION );
		}
		return true;
	}

}
