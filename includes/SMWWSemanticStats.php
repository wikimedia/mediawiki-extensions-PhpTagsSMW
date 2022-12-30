<?php
namespace PhpTagsObjects;

use PhpTags\GenericObject;
use PhpTags\HookException;
use SMW\ApplicationFactory;
use SMW\DataTypeRegistry;

/**
 * Class providing statistical info from SMW.
 *
 * @file SMWWSemanticStats.php
 * @ingroup PhpTagsSMW
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @license GPL-2.0-or-later
 */
class SMWWSemanticStats extends GenericObject {

	private static function getStatistics() {
		static $stats = null;
		if ( $stats === null ) {
			$store = ApplicationFactory::getInstance()->getStore();
			$stats = $store->getStatistics();
		}
		return $stats;
	}

/*
 * PhpTags object
 */

	public static function c_PROPERTY_VALUES() {
		$stats = self::getStatistics();
		return $stats['PROPUSES'];
	}

	public static function c_IMPROPER_VALUES() {
		$stats = self::getStatistics();
		if ( !isset( $stats['ERRORUSES'] ) ) {
			throw new HookException( "this statistic requires Semantic MediaWiki 2.2+ (" . SMW_VERSION . " installed)", HookException::EXCEPTION_NOTICE );
		}
		return $stats['ERRORUSES'];
	}

	public static function c_PROPERTIES_USED() {
		$stats = self::getStatistics();
		return $stats['USEDPROPS'];
	}

	public static function c_PROPERTIES_WITH_PAGES() {
		$stats = self::getStatistics();
		return $stats['OWNPAGE'];
	}

	public static function c_PROPERTIES_ASSIGNED_TYPES() {
		$stats = self::getStatistics();
		return $stats['DECLPROPS'];
	}

	public static function c_SUBOBJECTS() {
		$stats = self::getStatistics();
		return $stats['SUBOBJECTS'];
	}

	public static function c_QUERIES() {
		$stats = self::getStatistics();
		return $stats['QUERY'];
	}

	public static function c_CONCEPTS() {
		$stats = self::getStatistics();
		return $stats['CONCEPTS'];
	}

	public static function c_DATATYPES() {
		return count( DataTypeRegistry::getInstance()->getKnownTypeLabels() );
	}

}
