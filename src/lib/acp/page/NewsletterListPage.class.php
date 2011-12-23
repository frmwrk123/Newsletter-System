<?php
//wcf imports
require_once(WCF_DIR.'lib/page/SortablePage.class.php');

/**
 * Shows a list of all newsletters.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.page
 * @category Community Framework
 */
class NewsletterListPage extends SortablePage {
    public $neededPermissions = 'admin.content.newsletterSystem.canSeeNewsletterOverview';
    public $templateName = 'newsletterList';
    public $defaultSortField = MESSAGE_NEWSLETTERSYSTEM_GENERAL_SORTFIELD_NEWSLETTER;
    public $defaultSortOrder = MESSAGE_NEWSLETTERSYSTEM_GENERAL_SORTORDER_NEWSLETTER;
    public $itemsPerPage = MESSAGE_NEWSLETTERSYSTEM_GENERAL_ITEMS;
    
    /**
     * Contains the result of deleting a newsletter.
     * @var string
     */
    protected $result = '';
    
    /**
     * Contains the newsletter list.
     * @var array
     */
    protected $newsletterList = array();
    
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['result'])) $this->result = StringUtil::trim($_REQUEST['result']);
    }
    
	/**
     * @see SortablePage::readData()
     */
    public function readData() {
        $this->readNewsletters();
        parent::readData();
    }
    
	/**
     * @see SortablePage::validateSortField()
     */
    public function validateSortField() {
        parent::validateSortField();
        $allowedSortFields = array(
            'newsletterID',
            'username',
            'deliveryTime',
            'subject'
        );
        $inArray = false;
        //Checks whether the sort field is allowed or not.
        foreach ($allowedSortFields as $field) {
            if ($this->sortField != $field) continue;
            $inArray = true;
        }
        if (!$inArray) {
            $this->sortField = $this->defaultSortField;
        }
    }
    
    /**
     * @see SortablePage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
            'newsletters' => $this->newsletterList,
            'result' => $this->result
        ));
    }
    
    /**
     * @see MultipleLinkPage::countItems()
     */
    public function countItems() {
        parent::countItems();
        return count($this->newsletterList);
    }
    
    /**
     * @see AbstractPage::show()
     */
    public function show() {
        // enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.content.newsletterSystem.newsletterList');
		
		parent::show();
    }
    
    /**
     * Reads the newsletters.
     */
    protected function readNewsletters() {
        //add cache resource
        $cacheName = 'newsletter-list-'.PACKAGE_ID;
        WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNewsletter.class.php');
        
        //get options
        $this->newsletterList = WCF::getCache()->get($cacheName, 'newsletter');
    }
}
