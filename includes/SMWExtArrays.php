<?php
namespace PhpTagsObjects;

/**
 * Description of SMWextArray
 *
 * @author pastakhov
 */
class SMWExtArrays extends \PhpTags\GenericObject {

	/**
	 *
	 * @return \ExtArrays
	 * @throws \PhpTags\HookException
	 */
	private static function getExtArrays () {
		if ( true !== class_exists( 'ExtArrays', false ) ) {
			throw new \PhpTags\HookException( \PhpTags\HookException::EXCEPTION_FATAL, wfMessage( 'phptagssmw-ext-arrays-not-installed' )->text() );
		}
		return \ExtArrays::get( \PhpTags\Runtime::$parser );
	}

	public static function s_getArray( $arrayId ) {
		return self::getExtArrays()->getArray( $arrayId );
	}

	public static function s_getArrayValue( $arrayId, $index, $default = null ) {
		return self::getExtArrays()->getArrayValue( $arrayId, $index, $default );
	}

	public static function s_unsetArray() {
		$extArrays = self::getExtArrays();
		if ( func_num_args() === 0 ) {
			$extArrays->mArrays = array();
			return true;
		}
		$retrun = true;
		$args = func_get_args();
		foreach( $args as $arrayId ) {
			$retrun &= $extArrays->unsetArray( $arrayId );
		}
		return $retrun;
	}

}
