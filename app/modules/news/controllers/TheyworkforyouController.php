<?php
/** Controller for accessing they work for you based news
*
* @category   Pas
* @package    Pas_Controller
* @subpackage ActionAdmin
* @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
* @license    GNU General Public License
* @version    1.1
* @since      1/2/2012
*
*/
class News_TheyworkforyouController extends Pas_Controller_Action_Admin {

        /** The cache object
         *
         * @var type
         */
	protected $_cache = NULL;

        /** Initialise
         *
         */
	public function init() {
 	$this->_helper->_acl->allow(null);
 	$this->_helper->contextSwitch()->setAutoJsonSerialization(false);
	$this->_helper->contextSwitch()
            ->setAutoDisableLayout(true)
            ->addContext('kml',array('suffix' => 'kml'))
            ->addContext('rss',array('suffix' => 'rss'))
            ->addContext('atom',array('suffix' => 'atom'))
            ->addActionContext('finds', array('xml','json','kml','rss','atom'))
            ->addActionContext('members',array('xml','json'))
            ->addActionContext('constituencies',array('xml','json'))
            ->addActionContext('index',array('xml','json'))
             ->initContext();
	$this->_cache = Zend_Registry::get('cache');
	}


        /** Retrieve the page number
        *
        */
        public function getPage(){
        $page = $this->_getParam('page');
	if(!isset($page)){
		$start = 1;
	} else {
		$start = $page ;
	}
	return $start;
        }

        /** Get the index page and results for PAS search of twfy
        * @uses Pas_Twfy_Hansard
        */
	public function indexAction() {
	$term = $this->_getParam('term');
        $search = $term ? $term : 'portable antiquities scheme';
        $twfy = new Pas_Twfy_Hansard();
        $arts = $twfy->get($search, $this->getPage(), 20);
	$data = array();
	foreach($arts->rows as $row){
	$data[] = get_object_vars($row);
	}
	$pagination = array(
	'page'          => (int)$this->getPage(),
	'perpage'      => (int)$arts->info->results_per_page,
	'total_results' => (int)$arts->info->total_results
	);

	$paginator = Zend_Paginator::factory($pagination['total_results']);
	$paginator->setCurrentPageNumber($pagination['page'])
			->setItemCountPerPage($pagination['perpage'])
			->setCache($this->_cache);
	$this->view->data = $data;
	$this->view->paginator = $paginator;
	}

        /** Get data for a MP
         * @uses Pas_Twfy_Person
         * @throws Pas_Exception_Param
         */
	public function mpAction() {
	if($this->_getParam('id',false)) {
	$person =  new Pas_Twfy_Person();
	$unclean = $person->get($this->_getParam('id'));
	$clean = array();
	foreach($unclean as $object){
		$mp = array(); 
		$object = get_object_vars($object);
		foreach($object as $k => $v){
			
		$mp[$k] = utf8_encode($v);
		
		}
		$clean[] = $mp;
	}
	$this->view->data = $clean;	
	} else {
	throw new Pas_Exception_Param($this->_missingParameter);
	}
	}

        /** Get the finds within a consituency
         * @uses Pas_Twfy_Geometry
         * @throws Pas_Exception_Param
         */
	public function findsAction(){
	if($this->_getParam('constituency',false)){
	$geo = new Pas_Twfy_Geometry();
	
	$const = urldecode($this->_getParam('constituency'));
	$cons = $geo->get($const);
	
	$bbox = array(
            $cons->min_lat,
            $cons->min_lon,
            $cons->max_lat,
            $cons->max_lon);
	$search = new Pas_Solr_Handler('beowulf');
    $context = $this->_helper->contextSwitch->getCurrentContext();
	$fields = new Pas_Solr_FieldGeneratorFinds($context);
	
	$search->setFields($fields->getFields());
        $params = $this->_getAllParams();
        $params['bbox'] = implode(',',$bbox);
        $search->setFacets(array('objectType','county','broadperiod','institution','workflow'));
		$search->setParams($params);
        $search->execute();

        $this->view->facets = $search->_processFacets();
        $this->view->paginator = $search->_createPagination();
        $this->view->finds = $search->_processResults();
        $this->view->constituency = $const;
	} else {
	throw new Pas_Exception_Param($this->_missingParameter);
	}
 	}

        /** Get a list of constituencies
         * @uses Pas_Twfy_Constituencies
         */
	public function constituenciesAction() {
	$cons = new Pas_Twfy_Constituencies();
 	$data = $cons->get('2010-05-07');
 	$clean = array();
 	foreach($data as $dat){
 		foreach($dat as $k => $v){
 		$clean[] = array( 'name' => utf8_encode($v));
 		}
 	} 
	$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($clean));
	$paginator->setCurrentPageNumber($this->getPage())
			->setItemCountPerPage(40)
			->setCache($this->_cache);
	$this->view->data = $paginator;
	}

        /** get a list of members of parliament
         * @uses Pas_Twfy_Mps
         * @uses Zend_Paginator
         * @access public
         */
	public function membersAction() {
	$members = new Pas_Twfy_Mps();
	$data = $members->get();
	$clean = array();
	foreach($data as $d){
		$mp = array();
		foreach($d as $k => $v){
			$mp[$k] = utf8_encode($v);
		}
	$clean[] = (object)$mp;		
	}
	$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($clean));
	$paginator->setCurrentPageNumber((int)$this->getPage());
	$paginator->setItemCountPerPage(30)
    	      ->setPageRange(10)
    	      ->setCache($this->_cache);
   	$this->view->data = $paginator;
	}

}
