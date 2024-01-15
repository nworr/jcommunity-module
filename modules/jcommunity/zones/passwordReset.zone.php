<?php
/**
* @package      jcommunity
* @subpackage
* @author       Laurent Jouanneau <laurent@xulfr.org>
* @contributor
* @copyright    2007-2018 Laurent Jouanneau
* @link         http://jelix.org
* @licence      http://www.gnu.org/licenses/gpl.html GNU General Public Licence, see LICENCE file
*/


class passwordResetZone extends jZone {

   protected $_tplname = 'password_reset_form';


    protected function _prepareTpl()
    {
        $form = jForms::get('password_reset');
        if ($form == null) {
            $form = jForms::create('password_reset');

            if (\jAuth::getDriverParam('authenticateWith') == 'login-email') {
                $form->deactivate('pass_email');
            }
        }
        if ($form->isActivated('pass_email')) {
            $explanationLocale = 'jcommunity~password.form.text.html';
        } else {
            $explanationLocale = 'jcommunity~password.form.login-email.text.html';
            $form->getControl('pass_login')->label = jLocale::get('account.form.login_or_email');
        }
        $this->_tpl->assign('explanationlocale',$explanationLocale);
        $this->_tpl->assign('form',$form);
    }

}
