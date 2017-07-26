<?
/**
 * Cms_Workflow_Config_Interface  - Basic functions to get data needed to build a workflow
 * NOTE: that this Class is responsible for registering Cms_Event_Handlers for each transition
 *
 * @package Cms
 * @subpackage Workflow
 */
interface Cms_Workflow_Config_Interface
{
    /**
     * getId 
     * 
     * @access public
     * @return string - return id of workflow
     */
    public function getId();

    /**
     * getName 
     * 
     * @access public
     * @return string - return Name of workflow
     */
    public function getName();

    /**
     * getNodes 
     * 
     * @access public
     * @return array - array of Cms_Workflow_Node
     */
    public function getNodes();
}
