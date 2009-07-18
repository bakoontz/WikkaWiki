<?php
	/**
	  * Generate a list of plugins
	  */
	class WikkaAction_pluginlist extends WikkaAction
	{
		function WikkaAction_pluginlist($wakka)
		{
			parent::WikkaAction($wakka, __FILE__);
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
			if(!$this->wakka->IsAdmin() || $this->_isDisabled()) 
				return;

			// Action invoked?
			if(null != $this->wakka->GetSafeVar('install', 'post'))
			{
				$plugin_url =
				$this->wakka->htmlspecialchars_ent($this->wakka->GetSafeVar('plugin_url', 'post'));
				if(!isset($plugin_url))
					return;
				// TODO: This requires libcurl to be installed. 
				// TODO: Error handling...
				// Download to tmpdir
				$tmpdir = ini_get('session.save_path');
				$ch = curl_init($plugin_url);
				$output_file = $tmpdir.DIRECTORY_SEPARATOR.basename($plugin_url);
				$fp = fopen($output_file, "w");
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
				$data = curl_exec($ch);
				fwrite($fp, $data);
				curl_close($ch);
				fclose($fp);

				// Install!
				// TODO: Overwrites existing files, need to prompt
				// user before doing this.  Maybe a version check as
				// well?
				// TODO: Hardcoded paths need to be placed elsewhere
				// TODO: Make this portable across platforms
				// TODO: It would be nice to have a library function
				// that returns physical install directory
				// TODO: Sanity check before deleting plugin archive?
				if(file_exists($output_file))
				{
					$file_parts = explode('.', $output_file);
					switch(end($file_parts))
					{
						case "tgz":
						case "tar": 
							system("tar -xzf $output_file -C ".dirname(__FILE__).DIRECTORY_SEPARATOR."..");
							break;
						case "zip":
							// TODO
							break;
					}
				}
				else 
				{
					return;
				}
			}
			else
			{
				// TODO: Might be better to have checkboxes to allow for
				// multiple selection...
				foreach($_POST as $key=>$val)
				{
					$parts = explode('_', $key);
					if($parts[0] == 'enable')
					{
						// TODO: Danger! Need to untaint this safely
						unlink($parts[1].DIRECTORY_SEPARATOR.'disabled');
						break;
					}
					else if($parts[0] == 'disable')
					{
						// TODO: Danger! Need to untaint this safely
						touch($parts[1].DIRECTORY_SEPARATOR.'disabled');
						break;
					}
				}
			}

			// Set row colors
			// TODO: Probably need to move this to a stylesheet
			$enabled_row_color = $this->_getColor('F','1');			
			$disabled_row_color = $this->_getColor('D','1');

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
								if(file_exists($action.DIRECTORY_SEPARATOR."disabled"))
								{
									$action_info['row_color'] = $disabled_row_color;
									$action_info['status'] = "disabled";
								}
								else
								{
									$action_info['row_color'] = $enabled_row_color;
									$action_info['status'] = "enabled";
								}
								$action_info['action_path'] = $action;
								array_push($action_info_array, $action_info);
							}
						}
						include_once("pluginlist.html");
					}
				}
			}
		}
		
		// TODO: Proabably should move this to a stylesheet
		function _getColor($idx1, $idx2)
		{
			//color scheme array (ported from {{since}})
			$c = array(
					// cyan
					'A' => array('#699', '#BFFFFF', '#303030', '#A0E0E0', '#90B0B0'),
					// yellow
					'B' => array('#996', '#FFFFBF', '#303030', '#E0E0A0', '#B0B090'),
					// magenta
					'C' => array('#969', '#FFBFFF', '#303030', '#E0A0E0', '#B090B0'),
					// red
					'D' => array('#966', '#FFBFBF', '#303030', '#E0A0A0', '#B09090'),
					// blue
					'E' => array('#669', '#BFBFFF', '#303030', '#A0A0E0', '#9090B0'),
					// green
					'F' => array('#696', '#BFFFBF', '#303030', '#A0E0A0', '#90B090')
			);
			return $c[$idx1][$idx2];
		}
	}
?>
