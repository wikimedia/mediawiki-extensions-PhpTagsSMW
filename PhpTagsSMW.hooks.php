<?php

/**
 * Contains the implementations of hooks used by PhpTags SMW.
 *
 * @file PhpTagsSMW.hooks.php
 * @ingroup PhpTagsSMW
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @licence GNU General Public Licence 2.0 or later
 */
final class PhpTagsSMWHooks {

	/**
	 * Called when the PhpTags runtime initializes for the first time.
	 *
	 * Used for initialization of things that are only used when
	 * PhpTags is.
	 */
	public static function onPhpTagsRuntimeFirstInit() {
		\PhpTags\Hooks::addJsonFile( __DIR__ . '/PhpTagsSMW.non-smw.json' );
		if ( defined( 'SMW_VERSION' ) ) {
			global $wgCacheEpoch;

			\PhpTags\Hooks::addJsonFile( __DIR__ . '/PhpTagsSMW.json' );
			\PhpTags\Hooks::addCallbackConstantValues( 'PhpTagsSMWHooks::getSmwNsConstants', $wgCacheEpoch );
		}
		return true;
	}

	/**
	 * When SMW is installed, called to add the SMW_NS_* constants.
	 * The Semantic Forms SF_NS_* constants are also defined here.
	 * Also check for and add other known semantic namespaces.
	 *
	 * @return int[] Array of '*_NS_*' => *_NS_* entries
	 */
	public static function getSmwNsConstants() {
		global $smwgNamespaceIndex;

		// SMW + Semantic Forms namespace indices
		$nsConstants = \SMW\NamespaceManager::buildNamespaceIndex( $smwgNamespaceIndex );
		// Semantic Drilldown namespace indices
		if ( defined( 'SD_NS_FILTER' ) ) {
			$nsConstants['SD_NS_FILTER'] = SD_NS_FILTER;
			$nsConstants['SD_NS_FILTER_TALK'] = SD_NS_FILTER_TALK;
		}

		return $nsConstants;
	}

}
