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
		return \PhpTags\Runtime::getParser()->mExtArrays;
	}

	public static function checkArguments( $object, $method, &$arguments, $expects = false ) {
		switch ( $method ) {
			case 'getarray':
			case 'unsetarray':
				$expects = array(
					\PhpTags\Hooks::TYPE_STRING,
					\PhpTags\Hooks::EXPECTS_EXACTLY_PARAMETERS => 1,
				);
				break;
			case 'getarrayvalue':
				$expects = array(
					\PhpTags\Hooks::TYPE_STRING,
					\PhpTags\Hooks::TYPE_SCALAR,
					\PhpTags\Hooks::EXPECTS_MINIMUM_PARAMETERS => 2,
					\PhpTags\Hooks::EXPECTS_MAXIMUM_PARAMETERS => 3,
				);
				break;
		}
		return parent::checkArguments( $object, $method, $arguments, $expects );
	}

	public static function s_getArray( $arrayId ) {
		return self::getExtArrays()->getArray( $arrayId );
	}

	public static function s_getArrayValue( $arrayId, $index, $default = null ) {
		return self::getExtArrays()->getArrayValue( $arrayId, $index, $default );
	}

	public static function s_unsetArray( $arrayId ) {
		return self::getExtArrays()->unsetArray( $arrayId );
	}

}

