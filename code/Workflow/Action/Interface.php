<?

/**
 * Cms_Workflow_Action_Interface - represents and operation defined in the workflow
 *  Currently this is simplified to merely eval'ing php script
 *  
 * @package Cms
 * @subpackage Workflow
 */
interface Cms_Workflow_Action_Interface
{
    /**
     * Execute 
     * 
     * @access public
     * @return void
     */
    function Execute(Cms_Workflow_Context &$context);
}
