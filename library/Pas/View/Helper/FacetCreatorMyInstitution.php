<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/** This view helper takes the array of facets and their counts and produces
 * an html rendering of these with links for the search.
 * @category Pas
 * @package Pas_View
 * @subpackage Helper
 * @version 1
 * @since 30/1/2012
 * @copyright Daniel Pett
 * @author Daniel Pett
 * @license GNU
 * @uses Pas_Exception_BadJuJu
 * @uses Zend_View_Helper_Url
 * @uses Zend_Controller_Front
 */
class Pas_View_Helper_FacetCreatorMyInstitution extends Zend_View_Helper_Abstract {

    /** Create the facets boxes for rendering
     * @access public
     * @param array $facets
     * @return string
     * @throws Pas_Exception_BadJuJu
     */

    public function facetCreatorMyInstitution(array $facets){
        if(is_array($facets)){
        $html = '<h3>Search facets</h3>';
        foreach($facets as $facetName => $facet){
            $html .= $this->_processFacet($facet, $facetName);
        }
        return $html;
        } else {
            throw new Pas_Exception_BadJuJu('The facets sent are not an array');
        }
    }

    /** Process the facet array and name
     * @access public
     * @param array $facet
     * @param string $facetName
     * @return string
     * @throws Pas_Exception_BadJuJu
     * @uses Zend_Controller_Front
     * @uses Zend_View_Helper_Url
     */
    protected function _processFacet(array $facet, $facetName){
        if(is_array($facet)){
        	if(count($facet)){
        $html = '<div id="facet-' . $facetName .'">';
        $html .= '<h4>' . $this->_prettyName($facetName) . '</h4>';
        $html .= '<ul class="navpills nav-stacked nav">';

        if($facetName !== 'workflow'){
            $facets = array_slice($facet,0,10);
        } else {
        	$facets = $facet;
        }
        foreach($facets as $key => $value){
        $request = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		if(isset($request['page'])){
            unset($request['page']);
        }
        $request[$facetName] = $key;
		
        $url = $this->view->url($request,'default',true);
        
        $html .= '<li>';
        
        if($facetName !== 'workflow'){
        $html .= '<a href="' . $url . '" title="Facet query for ' . $this->view->facetContentSection($key);
        $html .= '">';
        $html .= $key . ' ('. number_format($value) .')';
        } else {
        $html .=  '<a href="' . $url . '" title="Facet query for ' . $this->_workflow($key);
        $html .= '">';
        $html .= $this->_workflow($key) . ' ('. number_format($value) .')';
        }

        $html .= '</a>';
        $html .= '</li>';
        }

        $html .= '</ul>';
        $request = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        if(isset($request['page'])){
            unset($request['page']);
        }
		if(count($facet) > 10){
			$request['controller'] = 'ajax';
			$request['action'] = 'myinstitutionfacet';
			$request['facetType'] = $facetName;
			$html .= '<a class="btn btn-small overlay" href="' . $this->view->url(($request),'default',true) . '" rel="facebox">All ' . $this->_prettyName($facetName) . ' options <i class="icon-plus"></i></a>';
		}
        $facet = $request[$facetName];
        if(isset($facet)){
            unset($request[$facetName]);
            $html .= '<p><i class="icon-remove-sign"></i> <a href="' . $this->view->url(($request),'default',true)
                    . '" title="Clear the facet">Clear this facet</a></p>';
        }

        $html .= '</div>';
        return $html;
        	}
        } else {
            throw new Pas_Exception_BadJuJu('The facet is not an array');
        }
    }

    /** Create a pretty name for the facet
     * @access public
     * @param string $name
     * @return string
     */
    protected function _prettyName($name){
        switch($name){
            case 'objectType':
                $clean = 'Object type';
                break;
            case 'objecttype':
                $clean = 'Object type';
                break;
            case 'broadperiod':
                $clean = 'Broad period';
                break;
            case 'county':
                $clean = 'County of origin';
                break;
            case 'denominationName':
            	$clean = 'Denomination';
            	break;
            case 'mintName':
            	$clean = 'Mint';
            	break;
            case 'rulerName':
            	$clean = 'Ruler/issuer';
            	break;
            case 'licenseAcronym':
            	$clean = 'License applicable';
            	break;	
            case 'materialTerm':
            	$clean = 'Material';
            	break;
            case 'creator':
            	$clean = 'Created by user';
            	break;
            default:
                $clean = ucfirst($name);
                break;
        }
        return $clean;
    }

    protected function _workflow($key){
        switch($key){
            case '1':
                $type = 'Quarantine';
                break;
            case '2':
                $type = 'Review';
                break;
            case '3':
                $type = 'Published';
                break;
            case '4':
                $type = 'Validation';
                break;
            default:
                $type = 'Unset workflow';
                break;
            }
            return $type;
        }

}
