<?php

class PhpTagsSMWInit {

	public static function initializeRuntime() {
		\PhpTags\Hooks::setObjects(
				array(
					'ExtArrays' => 'SMWExtArrays',
				)
			);
		return true;
	}

}
