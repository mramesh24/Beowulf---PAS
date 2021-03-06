<?php
/** Controller for all the Scheme's reviews
* 
* @category   Pas
* @package    Pas_Controller
* @subpackage ActionAdmin
* @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
* @license    GNU General Public License
*/
class News_ReviewsController extends Pas_Controller_Action_Admin {
	
	/** Initialise the ACL and contexts
	*/ 
	public function init() {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->_helper->acl->allow('public',null);
	}
	
	/** Render index pages
	*/ 
	public function indexAction() {
	 	$content = new Content();
		$this->view->contents = $content->getContent('reviews',$this->_getParam('slug'));
    }
}