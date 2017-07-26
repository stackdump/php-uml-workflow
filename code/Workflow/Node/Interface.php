<?
/**
 * Cms_Workflow_Node_Interface 
 * 
 * @package Cms
 * @subpackage Workflow
 */
interface Cms_Workflow_Node_Interface
{

    /**
     * addOutTransition add outgoing transition
     * 
     * @param Cms_Workflow_Transition $transition 
     * @access public
     * @return void
     */
    public function addOutTransition(Cms_Workflow_Transition &$transition);
    /**
     * addInTransition add incoming transition
     * 
     * @param Cms_Workflow_Transition $transition 
     * @access public
     * @return void
     */
    public function addInTransition(Cms_Workflow_Transition &$transition);

    /**
     * setDoActivity set activity action
     * 
     * @param Cms_Workflow_Action $action 
     * @access public
     * @return void
     */
    public function setDoActivity(Cms_Workflow_Action &$action);
    /**
     * setEntry set entry action
     * 
     * @param Cms_Workflow_Action $action 
     * @access public
     * @return void
     */
    public function setEntry(Cms_Workflow_Action &$action);
    /**
     * setWorkflow bind to a workflow
     * 
     * @param Cms_Workflow $wf 
     * @access public
     * @return void
     */
    public function setWorkflow(Cms_Workflow &$wf);

    /**
     * isCurrentState report if this is the current workflow state
     * 
     * @access public
     * @return boolean
     */
    public function isCurrentState();

    /**
     * entry  execute the entry action
     * 
     * @access public
     * @return void
     */
    public function entry();
    /**
     * doActivity execute the doActivity action
     * 
     * @access public
     * @return void
     */
    public function doActivity();

}
