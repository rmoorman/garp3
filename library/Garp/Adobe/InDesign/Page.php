<?php
/**
 * Garp_Adobe_InDesign_Page
 * Wrapper around various InDesign related functionality.
 *
 * @package Garp_Adobe_InDesign
 * @author  David Spreekmeester <david@grrr.nl>
 */
class Garp_Adobe_InDesign_Page extends Garp_Adobe_InDesign_SpreadNode {

    /**
     * @var int The page number within a document. One-based.
     */
    public $index;

    /**
     * @var string The color label of this page.
     */
    public $colorLabel;

    /**
     * @param SimpleXMLElement  $spreadConfig The <Spread> node of an InDesign Spread configuration.
     * @param SimpleXMLElement  $pageConfig   The <Page> node within the Spread configuration.
     */
    public function __construct(SimpleXMLElement $spreadConfig, $pageConfig) {
        parent::__construct($spreadConfig, $pageConfig);

        $this->index = $this->_getPageIndex();
        $this->colorLabel = $this->_getColorLabel();
    }


    protected function _getPageIndex() {
        $descriptorNodes = $this->_nodeConfig->Properties->Descriptor->children();

        foreach ($descriptorNodes as $descriptorNode) {
            if ((string)$descriptorNode->attributes()->type === "long") {
                return (int)$descriptorNode;
            }
        }
    }


    protected function _getColorLabel() {
        return (string)$this->_nodeConfig->Properties->PageColor;
    }
}
