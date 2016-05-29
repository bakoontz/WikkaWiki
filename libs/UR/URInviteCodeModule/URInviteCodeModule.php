<?php
	/**
	 * Require invitation code (password) for successful registration
	 *
	 * This UR (user registration) verification module requires the
	 * user to enter an invitation code (password) for a successful
	 * registration.
	 *
	 * To implement, set the following in wikka.config.php:
	 *     'UR_validation_modules' => 'URInviteCodeModule'
	 *     'UR_InviteCode_password' => 'some_invitation_code'
	 * (If combined with other modules, separate each module with a
	 * comma.)
	 *
	 * @see libs/userregistration.class.php
	 * @see URAuth, URAuthTmpl
	 *
	 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
	 * @filesource
	 *
	 * @author {@link http://wikkawiki.org/BrianKoontz Brian Koontz} 
	 *
	 * @copyright Copyright 2007 {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
	 */

	include_once('libs/userregistration.class.php');

	class URInviteCodeModule extends URAuthTmpl
	{

		var $inviteCode;

		function URInviteCodeModule(&$wakka)
		{
			$this->URAuthTmpl($wakka);	
			if(isset($wakka->config['UR_InviteCode_password']))
			{
				$this->inviteCode = $wakka->config['UR_InviteCode_password'];
			}
		}

		function URAuthTmplDisplay()
		{
			if(!isset($this->inviteCode))
			{
				echo '<em class="error">Error in URInviteCode module</em>';
				return;
			}
			echo "<label>Enter invitation code:</label>\n<input type='text' name='UR_inviteCode'/><br/>";
		}

		function URAuthTmplVerify()
		{
			if(!isset($this->inviteCode))
			{
				return false;
			}
			if($this->wakka->GetSafeVar('UR_inviteCode', 'post') === $this->inviteCode)
			{
				return true;
			}
			return false;
		}
	}
?>
