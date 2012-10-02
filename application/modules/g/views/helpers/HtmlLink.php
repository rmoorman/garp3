<?php
/**
 * G_View_Helper_HtmlLink
 * Generate HTML links (<a href="#">...</a>)
 * @author Harmen Janssen | grrr.nl
 * @modifiedby $LastChangedBy: $
 * @version $Revision: $
 * @package Garp
 * @subpackage Helper
 * @lastmodified $Date: $
 */
class G_View_Helper_HtmlLink extends Zend_View_Helper_HtmlElement {
	/**
	 * Return a HTML <a> tag
	 * @param String $url The url to link to (e.g. <a href="$url">)
	 * @param String $label The link label (e.g. <a href="$url">$label</a>)
	 * @param Array $attributes More attributes
	 * @param Boolean $escape Wether to escape attributes and label
	 * @return String
	 */
	public function htmlLink($url, $label, array $attributes = array(), $escape = true) {
		$urlAttribs = parse_url($url);
		if (empty($urlAttribs['scheme'])) {
			$url = $this->view->baseUrl($url);
		}
		$attributes['href'] = $url;
		$label = $escape ? $this->view->escape($label) : $label;
		$html = '<a'.$this->_htmlAttribs($attributes).'>'.$label.'</a>';
		return $html;		
	}
}