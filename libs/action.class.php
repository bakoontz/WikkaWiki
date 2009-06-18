<?php
	/**
	  * Base class for "new" actions (as of 1.3)
	  */

	class WikkaAction
	{
		var $wakka;

		function WikkaAction($wakka)
		{
			$this->wakka = $wakka;
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
	}
