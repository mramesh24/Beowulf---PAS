<?php
/**
 *
 * @author dpett
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * PreviousFind helper
 *
 * @uses viewHelper Pas_View_Helper
 */
class Pas_View_Helper_PreviousFind extends Zend_View_Helper_Abstract {
	
	protected $_cache;

	protected $_solrConfig;
	
	protected $_solr;
	
	protected $_config;
	
	protected $_query;
	
	public function __construct(){
		$this->_cache = Zend_Registry::get('cache');
		$this->_config = Zend_Registry::get('config');
		$this->_solrConfig = array('adapteroptions' => $this->_config->solr->master->toArray());
		$this->_solr = new Solarium_Client($this->_solrConfig);
	}
	
	protected function _getRole(){
	$user = new Pas_User_Details();
	return $user->getPerson()->role;
	}
	
	protected $_allowed =  array('fa','flos','admin','treasure');
	
	/**
	 * 
	 */
	public function previousFind($findID) {
		return $this->getSolrData($findID);
	}
	
	public function getSolrData($findID){
		$key = md5('previousfind' . $findID . $this->_getRole());
		if (!($this->_cache->test($key))) {
		$query = 'id:[* TO ' . $findID  . ']';
		$select = array(
    	'query'         => $query,
    	'filterquery' => array(),
    	);
		$select['fields'] = array('id', 'old_findID', 'objecttype', 'broadperiod');
    	$select['sort'] = array('id' => 'desc');
    	$select['start'] = 1;
    	$select['rows'] = 1;
    	$this->_query = $this->_solr->createSelect($select);
		if(!in_array($this->_getRole(), $this->_allowed) || is_null($this->_getRole()) ) {
	    $this->_query->createFilterQuery('workflow')->setQuery('workflow:[3 TO 4]');
		}
	    
		$this->_resultset = $this->_solr->select($this->_query);
//		Zend_Debug::dump($this->_resultset);
		$results = $this->_processResults($this->_resultset);
		$this->_cache->save($results);
		} else {
		$results = $this->_cache->load($key);
		}
		if($results){
		return $this->view->partial('partials/database/previous.phtml', $results['0']);
		} else {
			return false;
		}
		
	}
	
	public function _processResults(){
    $data = array();
    foreach($this->_resultset as $doc){
	$fields = array();
	foreach($doc as $key => $value){
            $fields[$key] = $value;
            }
    	$data[] = $fields;
    }
    $processor = new Pas_Solr_SensitiveFields();
    $clean = $processor->cleanData($data, $this->_getRole(), $this->_core);
    return $clean;
    }
}

