<?php
	/**
	  * Base class for "new" actions (as of 1.3)
	  */

	class WikkaAction
	{
		var $wakka;
		var $filename;

		function WikkaAction($wakka, $filename)
		{
			$this->wakka = $wakka;
			$this->filename = $filename;
		}

		static function getInfo()
		{
			return array(
				'author' => 'not defined',
				'email' => 'not defined',
				'date' => 'not defined',
				'name' => 'not defined',
				'desc' => 'not defined',
				'url' => 'not defined',
				'since' => 'not defined'
			);
		}

		function process($vars = null)
		{
			return "<em class='error'>Please define process() in your WikkaAction class!</em>";
		}

		function _isDisabled()
		{	
			if(file_exists(dirname($this->filename).DIRECTORY_SEPARATOR.'disabled'))
				return true;
			else
				return false;
		}	
	}
