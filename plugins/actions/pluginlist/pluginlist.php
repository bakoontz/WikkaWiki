<?php
	/**
	  * Generate a list of plugins
	  */
	class WikkaAction_pluginlist extends WikkaAction
	{
		function WikkaAction_pluginlist($wakka)
		{
			parent::WikkaAction($wakka);
		}

		static function getInfo()
		{
			return array(
				'author' => 'Brian Koontz',
				'email' => 'brian@wikkawiki.org',
				'date' => '2009-06-18',
				'name' => 'Plugin List',
				'desc' => 'Display list of all available plugins',
				'url' => 'http://www.wikkawiki.org',
				'since' => '1.3'
			);
		}

		function process($vars = null)
		{
			$header_output = FALSE;
			if(is_array($vars))
			{
				if(isset($vars['type']) && $vars['type'] == 'action')
				{
					$paths = preg_split('/;|:|,/',$this->wakka->config['wikka_action_path']); 
					foreach($paths as $path)
					{
						$path = trim($path);
						$actions = glob($path.DIRECTORY_SEPARATOR."*");
						$action_info_array = array();
						foreach($actions as $action)
						{
							$action_class = "WikkaAction_".basename($action);
							$action_file = $action.DIRECTORY_SEPARATOR.basename($action).".php";
							if(FALSE===file_exists($action_file))
								continue;

							// TODO: Brute-force check for "new" action,
							// needs to be deprecated as soon as all
							// actions are converted
							$file = file_get_contents($action_file);
							if(0 == preg_match("/class $action_class/", $file))
								continue;

							include_once($action_file);

							// PHP 5 would eliminate this...
							eval("\$action_info = $action_class::getInfo();");
							if(is_array($action_info))
							{
								array_push($action_info_array, $action_info);
							}
						}
						include_once("pluginlist.html");
					}
				}
			}
		}
	}
?>
