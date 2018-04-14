<?php
namespace PhpTagsObjects;

use \PhpTagsSMW\InputConverter;

/**
 * Class with static methods for affecting how semantic data is handled
 * for the page currently being parsed by MediaWiki.
 *
 * These methods can for example be called through a parser function in
 * some extension. Uses include:
 * - Adding semantic property values to the page being parsed.
 *
 * @file SMWWSemanticData.php
 * @ingroup PhpTagsSMW
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @license GPL-2.0-or-later
 */
class SMWWSemanticData extends \PhpTags\GenericObject {

	/**
	 * Return a ParserData instance which can be used to access the
	 * SemanticData container used by SMW for the current page being
	 * parsed.
	 *
	 * @return \SMW\ParserData
	 */
	public static function getParserData() {
		$applicationFactory = \SMW\ApplicationFactory::getInstance();
		$parser = \PhpTags\Renderer::getParser();
		$parserData = $applicationFactory->newParserData(
				$parser->getTitle(), $parser->getOutput() );
		return $parserData;
	}

/*
 * PhpTags object
 */

	public static function s_addValue( $property, $value ) {
		$parserData = self::getParserData();
		$converter = new InputConverter( $parserData->getTitle() );
		$diProperty = $converter->makeDIProperty( $property );
		$valueString = $converter->makeValueString( $value );
		if ( $valueString === '' ) {
			return; // Don't store an empty value
		}
		$dataValue = $converter->makeDataValue( $diProperty, $valueString );
		$parserData->addDataValue( $dataValue );
		$parserData->pushSemanticDataToParserOutput();
	}

	public static function s_addValues( $valueAssignments ) {
		$parserData = self::getParserData();
		$converter = new InputConverter( $parserData->getTitle() );
		$valueAssignments = $converter->makeValueAssignmentArray( $valueAssignments );
		foreach ( $valueAssignments as $property => $values ) {
			$diProperty = $converter->makeDIProperty( $property );
			foreach ( $values as $valueString ) {
				$dataValue = $converter->makeDataValue( $diProperty, $valueString );
				$parserData->addDataValue( $dataValue );
			}
		}
		$parserData->pushSemanticDataToParserOutput();
	}

	public static function s_addSubobject( $valueAssignments, $id = '', $linkbackProperty = '' ) {
		$parserData = self::getParserData();
		$converter = new InputConverter( $parserData->getTitle() );
		$valueAssignments = $converter->makeValueAssignmentArray( $valueAssignments, $linkbackProperty );
		$subobject = $converter->makeSubobject( $valueAssignments, $id );
		if ( $subobject->getSemanticData()->isEmpty() ) {
			return; // Don't store an empty subobject
		}
		$parserData->getSemanticData()->addSubobject( $subobject );
		$parserData->pushSemanticDataToParserOutput();
	}

}
