<?php 
class Pas_View_Helper_ChangesCoins extends Zend_View_Helper_Abstract
{
public function buildHtml($a)
	{
	$html = '';
	$html .= '<li><a href="';
	$html .= $this->view->url(array('module' => 'database', 'controller' => 'ajax', 'action' => 'coinaudit','id' => $a['editID']),NULL,true);
	$html .= '" rel="facebox" title="View all changes on this date">';
	$html .= $this->view->timeagoinwords($a['created']);
	$html .= '</a> ';
	$html .= $a['fullname'];
	$html .= ' edited this record.</li>';
	echo $html;
	}
	public function ChangesCoins($id) {
	$audit = new CoinsAudit();
	$auditdata = $audit->getChanges($id);
	if($auditdata) {
	echo '<h5>Coin data audit</h5>';
	echo '<ul id="related">';
	foreach($auditdata as $a) {
	$this->buildHtml($a);
	}
	echo '</ul>';
	}

}

}