<?php
namespace PhpTagsObjects;

use PhpTags\HookException;

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
	private static function getExtArrays() {
		if ( true !== class_exists( 'ExtArrays', false ) ) {
			throw new HookException( wfMessage( 'phptagssmw-ext-arrays-not-installed' )->inContentLanguage()->text(), HookException::EXCEPTION_FATAL );
		}
		return \ExtArrays::get( \PhpTags\Renderer::getParser() );
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
			$extArrays->mArrays = [];
			return true;
		}
		$retrun = true;
		$args = func_get_args();
		foreach ( $args as $arrayId ) {
			$retrun &= $extArrays->unsetArray( $arrayId );
		}
		return $retrun;
	}

}
