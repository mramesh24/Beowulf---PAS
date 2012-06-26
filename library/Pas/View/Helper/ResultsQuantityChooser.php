<?php
/**
 *
 * @author dpett
 * @version 
 */

/**
 * ResultsQuantityChooser helper
 *
 * @uses viewHelper Pas_View_Helper
 */
class Pas_View_Helper_ResultsQuantityChooser extends Zend_View_Helper_Abstract{
	
	protected $_quantities = array(10, 20, 40, 100);	
	/**
	 * 
	 */
	public function resultsQuantityChooser($results) {
		if($results){
			$urls = array();
			$request = Zend_Controller_Front::getInstance()->getRequest()->getParams();
			
			foreach($this->_quantities as $quantity){
				$request['show'] = $quantity;
				$urls[$quantity] = $this->view->url($request, 'default', true);
			}	
			return $this->_buildHtml($urls);
		} else {
		return null;
		}
	}
	
	protected function _buildHtml($urls){
		$html = '<p>Records per page: ';
		foreach($urls as $k => $v){
			$html .= '<a href="' . $v . '" title="show ' . $k . ' records">' . $k . '</a> ';
		}
		$html .= '</p>';
		return $html;
	} 
	
	

}

