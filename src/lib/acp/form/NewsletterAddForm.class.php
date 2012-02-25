<?php
//wcf imports
require_once(WCF_DIR.'lib/acp/form/WysiwygCacheloaderForm.class.php');
require_once(WCF_DIR.'lib/data/message/newsletter/NewsletterEditor.class.php');
require_once(WCF_DIR.'lib/system/style/StyleManager.class.php');

/**
 * Shows the newsletter add form.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.form
 * @category Community Framework
 */
class NewsletterAddForm extends WysiwygCacheloaderForm {
    public $activeMenuItem = 'wcf.acp.menu.link.content.newslettersystem.writeNewsletter';
    public $templateName = 'newsletterAdd';
    public $action = 'add';
    public $enableSmilies = 0;
    public $showAttachments = false;
        
    /**
     * Contains the read date values.
     * @var array<int>
     */
    protected $dateValues = array('hour' => 0, 'day' => 0, 'month' => 0, 'year' => 0);
    
    /**
     * Contains the options to be chosen in the form.
     * @var array
     */
    protected $dateOptions = array('hour' => array(), 'day' => array(), 'month' => array(), 'year' => array());
    
    /**
     * Contains the result of adding or editing a newsletter.
     * @var string
     */
    protected $result = '';
    
    /**
     * If true, the newsletter was sended successfully.
     * @var boolean
     */
    protected $success = false;
    
    /**
     * @see CaptchaForm::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_GET['result'])) $this->result = StringUtil::trim($_GET['result']);
        if (isset($_GET['success'])) $this->success = true;
    }
    
    /**
     * @see AbstractForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['hour'])) $this->dateValues['hour'] = intval($_POST['hour']);
        if (isset($_POST['day'])) $this->dateValues['day'] = intval($_POST['day']);
        if (isset($_POST['month'])) $this->dateValues['month'] = intval($_POST['month']);
        if (isset($_POST['year'])) $this->dateValues['year'] = intval($_POST['year']);
    }
    
    /**
     * @see AbstractForm::readData()
     */
    public function readData() {
        parent::readData();
        
        for ($h = 0; $h <= 24; $h++) {
            $this->dateOptions['hour'][$h] = ($h < 10 ? '0'.  (string) $h: (string) $h);
        }
        for ($d = 1; $d <= 31; $d++) {
            $this->dateOptions['day'][$d] = ($d < 10 ? '0'. (string) $d: (string) $d);
        }
        for ($m = 1; $m <= 12; $m++) {
            $this->dateOptions['month'][$m] = ($m < 10 ? '0'. (string) $m: (string) $m);
        }
        for ($y = 2011; $y <= 2038; $y++) {
            $this->dateOptions['year'][$y] = (string) $y;
        }
    }
    
    /**
     * @see AbstractForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateDate();
    }
    
    /**
     * @see AbstractForm::save()
     */
    public function save() {
        parent::save();
        //create date
        $date = (string) $this->dateValues['year'].'-'.
            (string) $this->dateValues['month'].'-'.
            (string) $this->dateValues['day'].
            (MESSAGE_NEWSLETTERSYSTEM_GENERAL_HOURLYCRONJOB ? ' '.(string) $this->dateValues['hour'] : '');
        //convert date to timestamp
        $unixTime = strtotime($date);
        $newsletter = NewsletterEditor::create($unixTime,
                    $this->subject, $this->text, $this->enableSmilies,
                    $this->enableHtml, $this->enableBBCodes);
        
        $this->saved();
        
        //resetting cache
        $cacheName = 'newsletter-'.PACKAGE_ID;
        $cacheResource = array(
			'cache' => $cacheName,
			'file' => WCF_DIR.'cache/cache.'.$cacheName.'.php',
			'className' => 'CacheBuilderNewsletter',
			'classFile' => WCF_DIR.'lib/system/cache/CacheBuilderNewsletter.class.php',
			'minLifetime' => 0,
			'maxLifetime' => 0
		);
        WCF::getCache()->rebuild($cacheResource);
        HeaderUtil::redirect('index.php?form=NewsletterAdd&result=success&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
        exit;
    }
    
    /**
     * @see AbstractForm::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
            'hour' => $this->dateValues['hour'],
            'day' => $this->dateValues['day'],
            'month' => $this->dateValues['month'],
            'year' => $this->dateValues['year'],
            'action' => $this->action,
            'dateOptions' => $this->dateOptions,
            'result' => $this->result,
            'useACPAttachments' => false,
            'success' => $this->success
        ));
    }
    
    /**
     * @see MessageForm::show()
     */
    public function show() {
        if (!empty($this->activeMenuItem)) WCFACP::getMenu()->setActiveMenuItem($this->activeMenuItem);
		parent::show();
    }
    
    /**
     * Validates the subject.
     * @throws UserInputException
     */
    protected function validateSubject() {
        parent::validateSubject();
        if (strlen($this->subject) < 4) {
            throw new UserInputException('subject', 'tooShort');
        }
    }
    
    /**
     * Validates the text.
     * @throws UserInputException
     */
    protected function validateText() {
        if (empty($this->text)) {
            throw new UserInputException('text');
        }
    }
    
    /**
     * Validates the date.
     * @throws UserInputException
     */
    protected function validateDate() {
        if (!checkdate($this->dateValues['month'], $this->dateValues['day'], $this->dateValues['year'])) {
            throw new UserInputException('date', 'notValidated');
        }
    }
}
