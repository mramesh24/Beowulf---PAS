<?php
/** Controller for displaying information about coins
* 
* @category   Pas
* @package    Pas_Controller
* @subpackage ActionAdmin
* @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
* @license    GNU General Public License
*/
class Database_CoinsController extends Pas_Controller_Action_Admin {

    protected $_coins;
    /** Setup the contexts by action and the ACL.
    */	
    public function init() {	
    $this->_helper->_acl->allow('member',array(
    'add', 'edit', 'delete',
    'coinref', 'editcoinref', 'deletecoinref'));
    $this->_helper->_acl->allow('flos',null);
    $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    $this->_coins = new Coins();
    }
    
    const REDIRECT = '/database/artefacts/';
    /** Redirect as no direct access to the coins index page
    */
    public function indexAction() {
    $this->_flashMessenger->addMessage('There is not a root action for coins');
    $this->_redirect(Zend_Controller_Request_Http::getServer('referer'));
    }
    /** Add a coin's data
    */
    public function addAction() {
    if( ($this->_getParam('broadperiod',false)) && ($this->_getParam('findID',false) )){
    $exist = $this->_coins->checkCoinData($this->_getParam('findID'));
    $broadperiod = (string)$this->_getParam('broadperiod');
   
    $form = $this->_helper->coinFormLoader($broadperiod);	
        $this->view->form = $form;
    $last = $this->_getParam('copy');
    if($last == 'last') {
    $this->_flashMessenger->addMessage('Your last record data has been cloned');
    $coindata = $this->_coins->getLastRecord($this->getIdentityForForms());
    foreach($coindata as $coindataflat){
    $form->populate($coindataflat);
    $formLoader = $this->_helper->coinFormLoaderOptions($broadperiod, $coindataflat);
    
    }
    }

    if ($this->_request->isPost()) {
    $formData = $this->_request->getPost();
    if ($form->isValid($formData)) {
    $insertData  = $form->getValues();
    $insertData['findID'] = (string) $this->_getParam('findID');
    $insertData['secuid'] = (string) $this->secuid();
    $this->_coins->insert($insertData);
    $solr = new Pas_Solr_Updater();
    $solr->add($this->_getParam('returnID'));
    $this->_flashMessenger->addMessage('Coin data saved for this record.');
    $this->_redirect(self::REDIRECT.'record/id/' . $this->_getParam('returnID'));
    } 
    else
    {
    $form->populate($formData);
    //Add menu data here
    }
    }
    } else {
            throw new Pas_Exception_Param($this->_missingParameter);
    }
    }

    /** Edit coin data
     * @throws Pas_Exception_Param
    */	
    public function editAction() {
    if($this->_getParam('id',false)){
    $finds = new Finds();
    $this->view->finds = $finds->getFindNumbersEtc($this->_getParam('returnID'));
    $broadperiod = (string)$this->_getParam('broadperiod');
    $this->view->jQuery()->addJavascriptFile($this->view->baseUrl() 
    . '/js/JQuery/jQueryLinkedSelect.js',
    $type='text/javascript'); 
    $this->view->jQuery()->addJavascriptFile($this->view->baseUrl() 
    . '/js/JQuery/coinslinkedselect.js',
    $type='text/javascript'); 
    switch ($broadperiod) {
        case 'ROMAN':
            $form = new RomanCoinForm();
            $form->details->setLegend('Edit Roman numismatic data');
            $form->submit->setLabel('Save Roman data');
            $this->view->headTitle('Edit a Roman coin\'s details');
            $this->view->jQuery()->addJavascriptFile($this->view->baseUrl() 
            . '/js/JQuery/coinslinkedinit.js',$type='text/javascript');
        break;
        case 'IRON AGE':
            $form = new IronAgeCoinForm();
            $form->details->setLegend('Edit Iron Age numismatic data');
            $form->submit->setLabel('Save Iron Age data');
            $this->view->headTitle('Edit an Iron Age coin\'s details');
            $this->view->jQuery()->addJavascriptFile($this->view->baseUrl()
            . '/js/JQuery/iacoinslinkedinit.js',$type='text/javascript');
            $this->view->jQuery()->addJavascriptFile($this->view->baseUrl() 
            . '/js/JQuery/jquery.autocomplete.pack.js',$type='text/javascript');
            $this->view->jQuery()->addJavascriptFile($this->view->baseUrl() 
            . '/js/JQuery/autocompleteinit.js',$type='text/javascript'); 
            $this->view->headLink()->appendStylesheet($this->view->baseUrl() 
            . '/css/autocomplete.css');
        break;
        case 'EARLY MEDIEVAL':
            $form = new EarlyMedievalCoinForm();
            $form->details->setLegend('Edit Early Medieval numismatic data');
            $form->submit->setLabel('Save Early Medieval data');
            $this->view->headTitle('Edit an Early Medieval coin\'s details');
            $this->view->jQuery()->addJavascriptFile($this->view->baseUrl() 
            . '/js/JQuery/coinslinkedinitearlymededit.js',$type='text/javascript');
        break; 
        case 'MEDIEVAL':
            $form = new MedievalCoinForm();
            $form->details->setLegend('Edit Medieval numismatic data');
            $form->submit->setLabel('Save Medieval data');
            $this->view->headTitle('Edit a Medieval coin\'s details');
            $this->view->jQuery()->addJavascriptFile($this->view->baseUrl() 
            . '/js/JQuery/coinslinkedinitmededit.js',$type='text/javascript');
        break; 
        case 'POST MEDIEVAL':
            $form = new PostMedievalCoinForm();
            $form->details->setLegend('Edit Post Medieval numismatic data');
            $form->submit->setLabel('Save Post Medieval data');
            $this->view->headTitle('Edit a Post Medieval coin\'s details');
            $this->view->jQuery()->addJavascriptFile($this->view->baseUrl() 
            . '/js/JQuery/coinslinkedinitpostmededit.js',$type='text/javascript');
        break; 
        case 'BYZANTINE':
            $form = new ByzantineCoinForm();
            $form->details->setLegend('Edit Byzantine numismatic data');
            $form->submit->setLabel('Save Byzantine data');
            $this->view->headTitle('Edit a Byzantine coin\'s details');
        break; 
        case 'GREEK AND ROMAN PROVINCIAL':
            $form = new GreekAndRomanCoinForm();
            $form->details->setLegend('Edit Greek & Roman numismatic data');
            $form->submit->setLabel('Save Greek & Roman data');
            $this->view->headTitle('Edit a Greek & Roman provincial coin\'s details');
        break; 
        default:
            throw new Exception('You cannot have a coin for that period.');
        break;
    }		
    $this->view->form = $form;
    if($this->getRequest()->isPost() && $form->isValid($_POST)) 	 {
    if ($form->isValid($form->getValues())) {
    $updateData = $form->getValues();

    $oldData = $this->_coins->fetchRow('id=' . $this->_getParam('id'))->toArray();
    $where =  $this->_coins->getAdapter()->quoteInto('id = ?', 
            $this->_getParam('id'));
    $update = $this->_coins->update($updateData, $where);
    $solr = new Pas_Solr_Updater();
    $solr->add($this->_getParam('returnID'));

    $this->_helper->audit($updateData, $oldData, 'CoinsAudit', 
            $this->_getParam('id'), $this->_getParam('returnID'));

    $this->_flashMessenger->addMessage('Numismatic details updated.');
    $this->_redirect(self::REDIRECT . 'record/id/' . $this->_getParam('returnID'));
    } else {
    $this->_flashMessenger->addMessage('Please check your form for errors');

    $form->populate($form->getValues());
    }
    } else {
    // find id is expected in $params['id']
    $id = (int)$this->_getParam('id', 0);
    if ($id > 0) {
    $coin = $this->_coins->getCoinToEdit($id);
    $form->populate($coin['0']);
    switch ($broadperiod) {
    case 'IRON AGE':
    if(isset($coin['0']['denomination'])) {
    $geographies= new Geography();
    $geography_options = $geographies->getIronAgeGeographyMenu($coin['0']['denomination']);
    $form->geographyID->addMultiOptions(array(NULL => 'Choose geographic region', 'Available regions' => $geography_options));
    }
    break;
    case 'ROMAN':
    if(isset($coin['0']['ruler'])) {
        $reverses = new Revtypes();
        $reverse_options = $reverses->getRevTypesForm($coin['0']['ruler']);
        if($reverse_options)
        {
        $form->revtypeID->addMultiOptions(array(NULL => 'Choose reverse type', 
            'Available reverses' => $reverse_options));
        } else {
        $form->revtypeID->addMultiOptions(array(NULL => 'No options available'));
        }
        } else {
        $form->revtypeID->addMultiOptions(array(NULL => 'No options available'));
        }
        if(isset($coin['0']['ruler']) && ($coin['0']['ruler'] == 242)){
        $moneyers = new Moneyers();
        $moneyer_options = $moneyers->getRepublicMoneyers();
        $form->moneyer->addMultiOptions(array(NULL => 'Choose a moneyer',
        'Available moneyers' => $moneyer_options));
        } else {
        $form->moneyer->addMultiOptions(array(NULL => 'No options available'));
        //$form->moneyer->disabled=true;
        }	
        break;

        case 'EARLY MEDIEVAL':
        if(isset($coin['0']['ruler'])) {
        $types = new MedievalTypes();
        $type_options = $types->getMedievalTypeToRulerMenu($coin['0']['ruler']);
        $form->typeID->addMultiOptions(array(NULL => 'Choose Early Medieval type',
            'Available types' => $type_options));
        $form->mint_id->clearMultiOptions();
        $form->denomination->clearMultiOptions();
        $mints = new Mints();
        $mints_options = $mints->getEarlyMedMintRulerPairs($coin['0']['ruler']);
        $form->mint_id->addMultiOptions(array(NULL => 'Choose a valid mint',
            'Available mints' => $mints_options));
        $denoms = new Denominations();
        $denom_options = $denoms->getEarlyMedRulerToDenominationPairs($coin['0']['ruler']);
        $form->denomination->addMultiOptions(array(NULL => 'Choose a valid denomination', 'Available denominations' => $denom_options));
        }
        break;

            case 'MEDIEVAL':
            if(isset($coin['0']['ruler'])) {
            $form->mint_id->clearMultiOptions();
            $form->denomination->clearMultiOptions();
            $types = new MedievalTypes();
            $type_options = $types->getMedievalTypeToRulerMenu($coin['0']['ruler']);
            $form->typeID->addMultiOptions(array(NULL => 'Choose Medieval type', 'Available types' => $type_options));
            $mints = new Mints();
            $mints_options = $mints->getEarlyMedMintRulerPairs($coin['0']['ruler']);
            $form->mint_id->addMultiOptions(array(NULL => 'Choose a valid mint', 'Available mints' => $mints_options));
            $denoms = new Denominations();
            $denom_options = $denoms->getEarlyMedRulerToDenominationPairs($coin['0']['ruler']);
            $form->denomination->addMultiOptions(array(NULL => 'Choose a valid denomination', 'Available denominations' => $denom_options));
            }
            break;
            case 'POST MEDIEVAL':
            if(isset($coin['0']['ruler'])) {
            $form->mint_id->clearMultiOptions();
            $form->denomination->clearMultiOptions();
            $types = new MedievalTypes();
            $type_options = $types->getMedievalTypeToRulerMenu($coin['0']['ruler']);
            $form->typeID->addMultiOptions(array(NULL => 'Choose Post Medieval type', 'Available types' => $type_options));
            $denoms = new Denominations();
            $denom_options = $denoms->getEarlyMedRulerToDenominationPairs($coin['0']['ruler']);
            $form->denomination->addMultiOptions(array(NULL => 'Choose a valid denomination', 'Available denominations' => $denom_options));
            $mints = new Mints();
            $mints_options = $mints->getEarlyMedMintRulerPairs($coin['0']['ruler']);
            $form->mint_id->addMultiOptions(array(NULL => 'Choose a valid mint', 'Available mints' => $mints_options));
            }
            break;	
    }
    }
    }
    } else {
            throw new Pas_Exception_Param($this->_missingParameter);
    }
    }
    /** Delete coin data via primary key
    */
    public function deleteAction() {
    if($this->_getParam('id',false)){
    if ($this->_request->isPost()) {
    $id = (int)$this->_request->getPost('id');
    $returnID = (int)$this->_request->getPost('returnID');
    $del = $this->_request->getPost('del');
    if ($del == 'Yes' && $id > 0) {
    $where = 'id = ' . $id;
    $this->_coins->delete($where);
    $this->_flashMessenger->addMessage('Numismatic data deleted!');
    $this->_redirect(self::REDIRECT . 'record/id/' . $returnID);
    }
    } else {
    $id = (int)$this->_request->getParam('id');
    if ($id > 0) {
    $this->view->coins = $this->_coins->getFindToCoinDelete($id);
    }
    }
    } else {
            throw new Pas_Exception_Param($this->_missingParameter);
    }
    }

    /** Link coin reference to object
    */
    public function coinrefAction() {
    $params = $this->_getAllParams();
    if(!isset($params['returnID']) && !isset($params['findID'])) {
    throw new Pas_Exception_Param('Find ID and return ID missing');
    }
    if(!isset($params['returnID'])) {
    throw new Pas_Exception_Param('The return ID parameter is missing.');
    }
    if(!isset($params['findID'])) {
    throw new Pas_Exception_Param('The find ID parameter is missing.');
    }
    $form = new ReferenceCoinForm();
    $form->submit->setLabel('Add reference');
    $this->view->form = $form;
    if ($this->_request->isPost()) {
    $formData = $this->_request->getPost();
    if ($form->isValid($formData)) {
    $coins = new CoinXClass();
    $secuid = $this->secuid();
    $insertData = array(
                    'findID' => (string)$this->_getParam('findID'),
                    'classID' => $form->getValue('classID'),
                    'vol_no' => $form->getValue('vol_no'),
                    'reference' => $form->getValue('reference'),
                    'created' => $this->getTimeForForms(), 
                    'createdBy' => $this->getIdentityForForms()
                    );
    foreach ($insertData as $key => $value) {
  if (is_null($value) || $value=="") {
    unset($insertData[$key]);
  }
}
    $coins->insert($insertData);
    $this->_flashMessenger->addMessage('Coin reference data saved for this record.');
    $this->_redirect(self::REDIRECT.'record/id/' . $this->_getParam('returnID'));
    } else {
    $form->populate($formData);
    }
    }
    }

    /** Edit a coin reference to object
    */	
    public function editcoinrefAction()	{
    $form = new ReferenceCoinForm();
    $form->submit->setLabel('Edit reference');
    $this->view->form = $form;
    if ($this->_request->isPost()) {
    $formData = $this->_request->getPost();
    if ($form->isValid($formData)) {
    $coins = new CoinXClass();
    $updateData = array(
    'findID' => (string)$this->_getParam('findID'),
    'classID' => $form->getValue('classID'),
    'vol_no' => $form->getValue('vol_no'),
    'reference' => $form->getValue('reference'),
    'updated' => $this->getTimeForForms(),
    'updatedBy' => $this->getIdentityForForms()
    );
    foreach ($updateData as $key => $value) {
if (is_null($value) || $value=="") {
unset($updateData[$key]);
}
}
    $where = array();
    $where[] = $coins->getAdapter()->quoteInto('id = ?', $this->_getParam('id'));
    $coins->update($updateData,$where);
    $this->_flashMessenger->addMessage('Coin reference information updated!');
    $this->_redirect(self::REDIRECT . 'record/id/' . $this->_getParam('returnID'));
    }  else {
    $form->populate($formData);
    }
    } else {
    $id = (int)$this->_request->getParam('id', 0);
    if ($id > 0) {
    $coins = new CoinXClass();
    $coins = $coins->fetchRow('id=' . $id);
    $form->populate($coins->toArray());
    }
    }
    }
    /** Delete a coin reference to object
    */
    public function deletecoinrefAction() {
    $returnID = $this->_getParam('returnID');
    $this->view->returnID = $returnID;
    if ($this->_request->isPost()) {
    $id = (int)$this->_request->getPost('id');
    $del = $this->_request->getPost('del');
    if ($del == 'Yes' && $id > 0) {
    $coins = new CoinXClass();
    $where = $coins->getAdapter()->quoteInto('id = ?', $id);
    $this->_flashMessenger->addMessage('Record deleted!');
    $coins->delete($where);	
    $this->_redirect(self::REDIRECT . 'record/id/' . $returnID);
    }
    $this->_flashMessenger->addMessage('No changes made!');
    $this->_redirect('database/artefacts/record/id/' . $returnID);
    } else {
    $id = (int)$this->_request->getParam('id');
    if ($id > 0) {
    $coins = new CoinXClass();
    $this->view->coin = $coins->fetchRow('id=' . $id);
    }
    }
    }

}