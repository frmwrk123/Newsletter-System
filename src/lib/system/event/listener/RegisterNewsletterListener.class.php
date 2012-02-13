<?php
//wcf imports
require_once(WCF_DIR.'lib/data/mail/Mail.class.php');
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Handles the newsletter subscription during registration.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage system.event.listener
 * @category Community Framework
 */
class RegisterNewsletterListener implements EventListener {
    
    /**
     * Contains the subscriber database table.
     * @var string
     */
    protected $subscriberTable = 'newsletter_subscriber';
    
    /**
     * Contains the activation database table.
     * @var string
     */
    protected $activationTable = 'newsletter_activation';
    
    /**
     * Contains the template name.
     * @var string
     */
    protected $templateName = 'registerNewsletter';
    
    /**
     * If true, the user accepts the newsletter.
     * @var boolean
     */
    protected $acceptNewsletter = false;
    
    /**
     * If true, the user wants to get newsletters via email.
     * @var boolean
     */
    protected $acceptNewsletterAsEmail = false;
    
    /**
     * If true, the user wants to get newsletters via pm.
     * @var boolean
     */
    protected $acceptNewsletterAsPM = false;
    
    /**
     * @see EventListener::execute()
     */
    public function execute($eventObj, $className, $eventName) {
        if ($className != 'RegisterForm') return;
        $this->{$eventName}($eventObj);
    }
    
    /**
     * Read form parameters.
     *
     * @param object $eventObj
     */
    protected function readFormParameters($eventObj) {
        if (isset($_POST['acceptNewsletter'])) $this->acceptNewsletter = (bool) intval($_POST['acceptNewsletter']);
        if (isset($_POST['acceptNewsletterAsEmail'])) $this->acceptNewsletterAsEmail = (bool) intval($_POST['acceptNewsletterAsEmail']);
        if (isset($_POST['acceptNewsletterAsPM'])) $this->acceptNewsletterAsPM = (bool) intval($_POST['acceptNewsletterAsPM']);
    }
    
    /**
     * Validates the input.
     *
     * @param object $eventObj
     *
     * @throws UserInputException
     */
    protected function validate($eventObj) {
        if (!$this->acceptNewsletterAsEmail && !$this->acceptNewsletterAsPM && $this->acceptNewsletter) {
            throw new UserInputException('acceptNewsletterAsEmail', 'notChecked');
        }
    }
    
    /**
     * Saves the decision.
     *
     * @param object $eventObj
     */
    protected function saved($eventObj) {
        $editor = $eventObj->user->getEditor();
        $options = array(
            'acceptNewsletter' => $this->acceptNewsletter,
            'acceptNewsletterAsEmail' => $this->acceptNewsletterAsEmail,
            'acceptNewsletterAsPM' => $this->acceptNewsletterAsPM
        );
        $editor->update('', '', '', null, $options);
        $this->sendValidationEmail($eventObj);
    }
    
    /**
     * Assigns necessary variables.
     *
     * @param object $eventObj
     */
    protected function assignVariables($eventObj) {
        WCF::getTPL()->assign('acceptNewsletter', $this->acceptNewsletter);
        $content = WCF::getTPL()->fetch($this->templateName);
        WCF::getTPL()->append('additionalFields', $content);
    }
    
	/**
     * Sends a validation email.
     *
     * @param object $eventObj
     */
    protected function sendValidationEmail($eventObj) {
        //save activation token into database
        $token = StringUtil::getRandomID();
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->activationTable.'
        		(userID, token)
        			VALUES
        		('.intval($eventObj->user->userID).", '".
                escapeString($token)."')";
        WCF::getDB()->sendQuery($sql);
        
        $url = PAGE_URL.'/index.php?action=NewsletterActivate&id='.$eventObj->user->userID.'&t='.$token;
        
        $subject = WCF::getLanguage()->get('wcf.acp.newsletter.optin.subject');
        $content = WCF::getLanguage()->getDynamicVariable('wcf.acp.newsletter.optin.text', array(
            'username' => $eventObj->user->username,
            'url' => $url
        ));
        WCF::getTPL()->assign(array(
            'subject' => $subject,
            'content' => $content
        ));
        $output = WCF::getTPL()->fetch('validationEmail');
        $mail = new Mail($eventObj->user->email, $subject, $output, MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
        $mail->setContentType('text/html');
        $mail->send();
    }
}