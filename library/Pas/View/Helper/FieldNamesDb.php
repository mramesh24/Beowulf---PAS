<?php

class Pas_View_Helper_FieldNamesDb extends Zend_View_Helper_Abstract {
	
	protected $_fields = array(
		'geographyID'				=> 'Iron Age geographical region',
		'geography_qualifier'		=> 'Geographical qualifier',
		'greekstateID' 				=> 'Greek state',
		'ruler_id' 					=> 'Primary ruler',
		'ruler2_id'					=> 'Secondary ruler',
		'ruler_qualifier'			=> 'primary ruler qualifier',
		'ruler2_qualifier' 			=> 'Secondary ruler qualifier',
		'tribe_qualifier'			=> 'Ascribed culture',
		'denomination_qualifier'	=> 'Denomination qualifier',
		'mint_id'					=> 'Mint',
		'mint_qualifier'			=> 'Mint qualifier',
		'categoryID'				=> 'Medieval category',
		'typeID'					=> 'Medieval type',
		'status_qualifier'			=> 'Status qualifier',
		'obverse_description'		=> 'Obverse description',
		'obverse_inscription'		=> 'Obverse inscription',
		'initial_mark'				=> 'Initial mark',
		'reverse_description'		=> 'Reverse description',
		'reverse_inscription'		=> 'Reverse inscription',
		'reverse_mintmark'			=> 'Reverse mintmark',
		'revtypeID'					=> 'Reverse type',
		'revTypeID_qualifier'		=> 'Reverse type qualifier',
		'degree_of_wear'			=> 'Degree of wear',
		'die_axis_measurement'		=> 'Die axis',
		'die_axis_certainty'		=> 'Die axis certainty',
		'cciNumber'					=> 'CCI number',
		'allen_type'				=> 'Allen type',
		'mack_type'					=> 'Mack type',
		'bmc_type'					=> 'BMC type',
		'rudd_type'					=> 'Ancient British Coinage type',
		'va_type'					=> 'Van Arsdell type',
		'numChiab'					=> 'CHIAB number',
		'reeceID'					=> 'Reece period',
	'finderID'			=> 'Finder name',
		'smr_ref' 			=> 'SMR reference',
		'other_ref' 		=> 'Other reference',
		'datefound1qual' 	=> 'First date found qualifier',
		'datefound1'		=> 'First date found',
		'datefound2'		=> 'Second date found',
		'datefound2qual' 	=> 'Second date found qualifier',
		'culture'			=> 'Ascribed culture',
		'discmethod'		=> 'Discovery method',
		'disccircum'		=> 'Discovery circumstances',
		'objecttype'		=> 'Object type',
		'objecttypecert'	=> 'Object type certainty',
		'subclass'			=> 'Sub-classification',
		'objdate1cert'		=> 'Object period certainty from',
		'objdate2cert'		=> 'Object period certainty to',
		'objdate1period'	=> 'Object period from',
		'objdate2period'	=> 'Object period to',
		'objdate1subperiod'	=> 'Object sub-period from',
		'objdate2subperiod'	=> 'Object sub-period to',
		'numdate1qual'		=> 'Date from qualifier',
		'numdate2qual'		=> 'Date to qualifier',
		'numdate1'			=> 'Date from',
		'numdate2'			=> 'Date to',
		'material1'			=> 'Primary material',
		'material2'			=> 'Secondary material',
		'manmethod'			=> 'Manufacture method',
		'decmethod'			=> 'Decoration method',
		'surftreat'			=> 'Surface treatment',
		'decstyle'			=> 'Decoration style',
		'reuse_period'		=> 'Period of reuse',
		'curr_loc'			=> 'Current location',
		'recorderID'		=> 'Recorder',
		'identifier1ID'		=> 'Primary identifier',
		'identifier2ID'		=> 'Secondary identifier',
		'musaccno'			=> 'Museum accession number',
		'subs_action'		=> 'Subsequent action',
		'findofnote'		=> 'Find of note',
		'findofnotereason'	=> 'Find of note reasoning',
		'treasureID'		=> 'Treasure ID number',
		'gridref'			=> 'Grid reference',
		'gridrefsrc'		=> 'Grid reference source',
		'gridrefcert' 		=> 'Grid reference certainty',
		'knownas' 			=> 'Known as',
		'disccircum'		=> 'Discovery circumstances',
		'landusevalue'		=> 'Land use value',
		'landusecode'	 	=> 'Land use code',
		'depthdiscovery'	=> 'Depth of discovery',
		'Highsensitivity'	=> 'High sensitivity',
	);
	
	public function fieldNamesDb($field){
		
		if(array_key_exists($field,$this->_fields)){
			$clean = $this->_fields[$field];
		} else {
			$clean = $field;
		}
		return ucfirst($clean);
	}
	
}