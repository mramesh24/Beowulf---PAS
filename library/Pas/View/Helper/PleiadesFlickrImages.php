<?php
/**
 *
 * @author dpett
 * @version 
 */

/**
 * PleiadesFlickrImages helper
 *
 * @uses viewHelper Pas_View_Helper
 */
class Pas_View_Helper_PleiadesFlickrImages extends Zend_View_Helper_Abstract {
	
	protected $_cache;
	
	protected $_api;
	
	protected $_flickr;
	
	protected $_config;
	
	public function __construct(){
	$this->_config = Zend_Registry::get('config');
	$this->_cache = Zend_Registry::get('cache');
	$this->_flickr = $this->_config->webservice->flickr;
	$this->_api	= new Pas_Yql_Flickr($this->_flickr);
	}
	
	/**
	 * 
	 */
	public function pleiadesFlickrImages($pleiadesID) {
		if(isset($pleiadesID)) {
		$photos = $this->_api->getMachineTagged('pleiades:depicts=' . $pleiadesID, 5);
		if($photos->photo){
		$html = '<div class="row-fluid"><h3>Photos linked to this Pleiades ID</h3>';
		$html .= $this->view->partialLoop('partials/flickr/favourite.phtml', $photos->photo);
		$html .= '</div>';
		return $html;
		}
		} else {
		return null;
		}
	}
	
}

