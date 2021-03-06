<?php
/**
* Form for adding mints to rulers
*
* @category   Pas
* @package    Pas_Form
* @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
* @license    GNU General Public License
*/
class AddMintToRulerForm extends Pas_Form
{
public function __construct($options = null) {

	parent::__construct($options);
	$this->setName('MintToRuler');

	$mint = new Zend_Form_Element_Select('mint_id');
	$mint->setLabel('Active mint: ')
	->setRequired(true)
	->addValidator('Int')
	->addFilters(array('StripTags','StringTrim','StringToLower'))
	->setAttribs(array('class'=> 'textInput'));

	$ruler_id = new Zend_Form_Element_Hidden('ruler_id');
	$ruler_id ->removeDecorator('label')
	->addValidator('Int')
	->addFilter('StringTrim');


	$hash = new Zend_Form_Element_Hash('csrf');
	$hash->setValue($this->_salt)->setTimeout(60);

	$submit = new Zend_Form_Element_Submit('submit');

	$this->addElements(array($mint,$ruler_id,$submit, $hash));

	$this->addDisplayGroup(array('mint_id'), 'details');

	$this->details->setLegend('Add an active Mint');

	$this->addDisplayGroup(array('submit'), 'buttons');

	parent::init();
	}

}