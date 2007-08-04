<?php
/**
* @package      jcommunity
* @subpackage
* @author       Laurent Jouanneau <laurent@xulfr.org>
* @contributor
* @copyright    2007 Laurent Jouanneau
* @link         http://jelix.org
* @licence      http://www.gnu.org/licenses/gpl.html GNU General Public Licence, see LICENCE file
*/

define('COMAUTH_STATUS_VALID',2);
define('COMAUTH_STATUS_MODIFIED',1);
define('COMAUTH_STATUS_NEW',0);
define('COMAUTH_STATUS_DEACTIVATED',-1);
define('COMAUTH_STATUS_DELETED',-2);

class registrationCtrl extends jController {
    /**
    * registration form
    */
    function index() {
        $rep = $this->getResponse('html');
        $rep->body->assignZone('MAIN','registration');
        return $rep;
    }

    /**
    * save new user and send an email for a confirmation, with
    * a key to activate the account
    */
    function save() {
        $form = jForms::fill('registration');
        if(!$form->check()){
            $rep= $this->getResponse("redirect");
            $rep->action="registration_index";
            return $rep;
        }

        $login = $form->getData('login');
        if(jAuth::getUser($login)){
            $form->setErrorOn('login',jLocale::get('register.form.login.exists'));
            $rep= $this->getResponse("redirect");
            $rep->action="registration_index";
            return $rep;
        }

        $pass = jAuth::getRandomPassword();
        $key = substr(md5($login.'-'.$pass),1,10);

        $user = jAuth::createUserObject($login,$pass);
        $user->email = $form->getData('email');
        $user->pseudo = $login;
        $user->status = COMAUTH_STATUS_NEW;
        $user->keyactivate = $key;
        jAuth::saveNewUser($user);

        $mail = new jMailer();
        $mail->From = 'webmaster@xulfr.org';
        $mail->FromName = 'Webmaster Xulfr';
        $mail->Sender = 'webmaster@xulfr.org';
        $mail->Subject = jLocale::get('register.mail.new.subject');

        $tpl = new jTpl();
        $tpl->assign(compact('login','pass','key'));
        $tpl->assign('server',$_SERVER['SERVER_NAME']);
        $mail->Body = $tpl->fetch('mail_registration', 'text');

        $mail->AddAddress($user->email);
        //$mail->SMTPDebug = true;
        $mail->Send();

        $rep= $this->getResponse("redirect");
        $rep->action="registration_infosent";
        return $rep;
    }

    /**
    * display the page which confirm that the user is registered
    * but his account is not activated yet
    */
    function infosent() {
        $rep = $this->getResponse('html');
        $rep->body->assignZone('MAIN','registrationsent');
        return $rep;
    }


    /**
    * form to enter the confirmation key
    * to activate the account
    */
    function confirmform() {
        $rep = $this->getResponse('html');
        $rep->body->assignZone('MAIN','registration_confirmation');
        return $rep;
    }

    /**
    * activate an account. the key should be given as a parameter
    */
    function confirm() {
        $form = jForms::fill('confirmation');
        if(!$form->check()){
            $rep= $this->getResponse("redirect");
            $rep->action="registration_confirmform";
            return $rep;
        }

        $login = $form->getData('login');
        $user = jAuth::getUser($login);
        if(!$user){
            $form->setErrorOn('login',jLocale::get('register.form.confirm.login.doesnt.exist'));
            $rep= $this->getResponse("redirect");
            $rep->action="registration_confirmform";
            return $rep;
        }

        if($user->status != COMAUTH_STATUS_NEW) {
            jForms::destroy('confirmation');
            $rep = $this->getResponse('html');
            $rep->body->assignZone('MAIN','registrationok', array('already'=>true));
            return $rep;
        }

        if($form->getData('key') == $user->keyactivate) {
            $user->status = COMAUTH_STATUS_VALID;
            jAuth::updateUser($user);
            $rep = $this->getResponse('redirect');
            $rep->action="registration_confirmok";
            return $rep;
        }
        else {
            $form->setErrorOn('key',jLocale::get('register.form.confirm.bad.key'));
            $rep= $this->getResponse("redirect");
            $rep->action="registration_confirmform";
            return $rep;
        }
    }

    /**
    * Page which confirm that the account is activated
    */
    function confirmok() {
        $rep = $this->getResponse('html');
        $rep->body->assignZone('MAIN','registrationok');
        return $rep;
    }
}
?>
