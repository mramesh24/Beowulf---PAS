<?php
/**
 * @package OaiPmhRepository
 * @subpackage MetadataFormats
 * @author John Flatness, Yu-Hsun Lin
 * @copyright Copyright 2009 John Flatness, Yu-Hsun Lin
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once('Abstract.php');

/**
 * Class implmenting metadata output CDWA Lite.
 *
 * @link http://www.getty.edu/research/conducting_research/standards/cdwa/cdwalite.html
 * @package OaiPmhRepository
 * @subpackage Metadata Formats
 */
class Pas_OaiPmhRepository_Metadata_CdwaLite extends Pas_OaiPmhRepository_Metadata_Abstract
{
  /** OAI-PMH metadata prefix */
    const METADATA_PREFIX = 'cdwalite';    
    
    /** XML namespace for output format */
    const METADATA_NAMESPACE = 'http://www.getty.edu/CDWA/CDWALite';
    
    /** XML schema for output format */
    const METADATA_SCHEMA = 'http://www.getty.edu/CDWA/CDWALite/CDWALite-xsd-public-v1-1.xsd';
    
    protected $view;
		
	public function init()
	{
	$this->view = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->view;	
	}
	protected function _xmlEscape($string)
    {
        $enc = 'UTF-8';
        if ($this->view instanceof Zend_View_Interface
            && method_exists($this->view, 'getEncoding')
        ) {
            $enc = $this->view->getEncoding();
        }

        // TODO: remove check when minimum PHP version is >= 5.2.3
        if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
            // do not encode existing HTML entities
            return htmlspecialchars($string, ENT_QUOTES, $enc, false);
        } else {
            $string = preg_replace('/&(?!(?:#\d++|[a-z]++);)/ui', '&amp;', $string);
            $string = str_replace(array('<', '>', '\'', '"'), array('&lt;', '&gt;', '&#39;', '&quot;'), $string);
            return $string;
        }
    }
    
    /**
     * Appends CDWALite metadata. 
     *
     * Appends a metadata element, an child element with the required format,
     * and further children for each of the Dublin Core fields present in the
     * item.
     */
    public function appendMetadata() 
    {

    	
    	$metadataElement = $this->document->createElement('metadata');
        $this->parentElement->appendChild($metadataElement);   
        
        $cdwaliteWrap = $this->document->createElementNS(
            self::METADATA_NAMESPACE, 'cdwalite:cdwaliteWrap');
        $metadataElement->appendChild($cdwaliteWrap);

        /* Must manually specify XML schema uri per spec, but DOM won't include
         * a redundant xmlns:xsi attribute, so we just set the attribute
         */
        $cdwaliteWrap->setAttribute('xmlns:cdwalite', self::METADATA_NAMESPACE);
        $cdwaliteWrap->setAttribute('xmlns:xsi', self::XML_SCHEMA_NAMESPACE_URI);
        $cdwaliteWrap->setAttribute('xsi:schemaLocation', self::METADATA_NAMESPACE
            .' '.self::METADATA_SCHEMA);
            
        $cdwalite = $this->appendNewElement($cdwaliteWrap, 'cdwalite:cdwalite');
        /* Each of the 16 unqualified Dublin Core elements, in the order
         * specified by the oai_dc XML schema
         */
        /*$dcElementNames = array( 
                                 'publisher', 'contributor',
                                 'format', 'identifier', 'source', 'language',
                                 'relation', 'coverage', 'rights' );
                                 */
        /* ====================
         * DESCRIPTIVE METADATA
         * ====================
         */

        $descriptive = $this->appendNewElement($cdwalite, 'cdwalite:descriptiveMetadata');
        
        /* Type => objectWorkTypeWrap->objectWorkType 
         * Required.  Fill with 'Unknown' if omitted.
         */
        $types = array('Archaeological artefact record');
        $objectWorkTypeWrap = $this->appendNewElement($descriptive, 'cdwalite:objectWorkTypeWrap');
        if(count($types) == 0) $types[] = 'Unknown';
        foreach($types as $type)
        {
            $this->appendNewElement($objectWorkTypeWrap, 'cdwalite:objectWorkType', $type);
        }      
        
        /* Subject => classificationWrap->classification
         * Not required.
         */
        $subjects = array('Archaeology');
      
        $classificationWrap = $this->appendNewElement($descriptive, 'cdwalite:classificationWrap');
        foreach($subjects as $subject)
        {
            $this->appendNewElement($classificationWrap, 'cdwalite:classification', $subject);
        }
        
        
        /* Title => titleWrap->titleSet->title
         * Required.  Fill with 'Unknown' if omitted.
         */        
        $titles = array('A ' . ucfirst(strtolower($this->item['broadperiod'])) . ' ' 
        . ucfirst(strtolower($this->item['objecttype'])));
        $titleWrap = $this->appendNewElement($descriptive, 'cdwalite:titleWrap');
        if(count($titles) == 0) $titles[] = 'Unknown';
        foreach($titles as $title)
        {
            $titleSet = $this->appendNewElement($titleWrap, 'cdwalite:titleSet');
            $this->appendNewElement($titleSet, 'cdwalite:title', $title);
        }
        
        /* Creator => displayCreator
         * Required.  Fill with 'Unknown' if omitted.
         * Non-repeatable, implode for inclusion of many creators.
         */
        $creators = array($this->item['recorder']);
        foreach($creators as $creator) $creatorTexts[] = $creator;
        $creatorText = count($creators) >= 1 ? implode(',', $creatorTexts) : 'Unknown';
        $this->appendNewElement($descriptive, 'cdwalite:displayCreator', $creatorText);
        
        /* Creator => indexingCreatorWrap->indexingCreatorSet->nameCreatorSet->nameCreator
         * Required.  Fill with 'Unknown' if omitted.
         * Also include roleCreator, fill with 'Unknown', required.
         */
        $indexingCreatorWrap = $this->appendNewElement($descriptive, 'cdwalite:indexingCreatorWrap');
        if(count($creators) == 0) $creators[] = 'Unknown';       
        foreach($creators as $creator) 
        {
            $indexingCreatorSet = $this->appendNewElement($indexingCreatorWrap, 'cdwalite:indexingCreatorSet');
            $nameCreatorSet = $this->appendNewElement($indexingCreatorSet, 'cdwalite:nameCreatorSet');
            $this->appendNewElement($nameCreatorSet, 'cdwalite:nameCreator', $creator);
            $this->appendNewElement($indexingCreatorSet, 'cdwalite:roleCreator', 'Unknown');
        }
        
        /* displayMaterialsTech
         * Required.  No corresponding metadata, fill with 'not applicable'.
         */
        $materials = array($this->item['primaryMaterial'], $this->item['secondaryMaterial']);
        if(count($materials) == 0) {
        $materials[] = 'Unknown';	
        }        
        $indexingMaterialsTechWrap= $this->appendNewElement($descriptive, 'cdwalite:indexingMaterialsTechWrap');
        $displayMaterialsSet = $this->appendNewElement($indexingMaterialsTechWrap, 'cdwalite:indexingMaterialsTechSet');
        $termMaterialsTech = $this->appendNewElement($displayMaterialsSet, 'cdwalite:termMaterialsTech');
                    
    	foreach($materials as $material) 
        {
        	if(!is_null($material)){
            $this->appendNewElement($termMaterialsTech, 'cdwalite:termMaterialsTech', $material);
        	}
        }
        
        /* Date => displayCreationDate
         * Required. Fill with 'Unknown' if omitted.
         * Non-repeatable, include only first date.
         */
        $dates = array($this->item['numdate1'], $this->item['numdate2']);
        $dateText = count($dates) > 0 ? $dates[0] : 'Unknown';
        $this->appendNewElement($descriptive, 'cdwalite:displayCreationDate', $dateText);
        
        /* Date => indexingDatesWrap->indexingDatesSet
         * Map to both earliest and latest date
         * Required.  Fill with 'Unknown' if omitted.
         */
        $indexingDatesWrap = $this->appendNewElement($descriptive, 'cdwalite:indexingDatesWrap');   
       
            $indexingDatesSet = $this->appendNewElement($indexingDatesWrap, 'cdwalite:indexingDatesSet');
            $this->appendNewElement($indexingDatesSet, 'cdwalite:earliestDate', $dates['0']);
            $this->appendNewElement($indexingDatesSet, 'cdwalite:latestDate', $dates['1']);
        
        
        /* locationWrap->locationSet->locationName
         * Required. No corresponding metadata, fill with 'location unknown'.
         */
        $locationWrap = $this->appendNewElement($descriptive, 'cdwalite:locationWrap');
        $locationSet = $this->appendNewElement($locationWrap, 'cdwalite:locationSet');
        $this->appendNewElement($locationSet, 'cdwalite:locationName', 'location unknown');
        
        /* Description => descriptiveNoteWrap->descriptiveNoteSet->descriptiveNote
         * Not required.
         */
        $descriptions = array($this->item['description']);
        if(count($descriptions) > 0)
        {
            $descriptiveNoteWrap = $this->appendNewElement($descriptive, 'cdwalite:descriptiveNoteWrap');
            foreach($descriptions as $description)
            {
                $descriptiveNoteSet = $this->appendNewElement($descriptiveNoteWrap, 'cdwalite:descriptiveNoteSet');
                $this->appendNewElement($descriptiveNoteSet, 'cdwalite:descriptiveNote', $this->_xmlEscape($description));
            }
        }
        
        /* =======================
         * ADMINISTRATIVE METADATA
         * =======================
         */
         
        $administrative = $this->appendNewElement($cdwalite, 'cdwalite:administrativeMetadata');
        
        /* Rights => rightsWork
         * Not required.
         */
        $rights = array('Creative Commons BY-NC-SA');
        foreach($rights as $right)
        {
            $this->appendNewElement($administrative, 'cdwalite:rightsWork', $right);
        }
        
        /* id => recordWrap->recordID
         * 'item' => recordWrap-recordType
         * Required.
         */     
        $recordWrap = $this->appendNewElement($descriptive, 'cdwalite:recordWrap');
        $this->appendNewElement($recordWrap, 'cdwalite:recordID', $this->item['id']);
        $this->appendNewElement($recordWrap, 'cdwalite:recordType', 'item');
        $recordMetadataWrap = $this->appendNewElement($recordWrap, 'cdwalite:recordMetadataWrap');
        $recordInfoID = $this->appendNewElement($recordMetadataWrap, 'cdwalite:recordInfoID', Pas_OaiPmhRepository_OaiIdentifier::itemToOaiId($this->item['id']));
        $recordInfoID->setAttribute('type', 'oai');
        
        /* file link => resourceWrap->resourceSet->linkResource
         * Not required.
         */
       
    }
    
    /**
     * Returns the OAI-PMH metadata prefix for the output format.
     *
     * @return string Metadata prefix
     */
    public function getMetadataPrefix()
    {
        return self::METADATA_PREFIX;
    }
    
    /**
     * Returns the XML schema for the output format.
     *
     * @return string XML schema URI
     */
    public function getMetadataSchema()
    {
        return self::METADATA_SCHEMA;
    }
    
    /**
     * Returns the XML namespace for the output format.
     *
     * @return string XML namespace URI
     */
    public function getMetadataNamespace()
    {
        return self::METADATA_NAMESPACE;
    }
}