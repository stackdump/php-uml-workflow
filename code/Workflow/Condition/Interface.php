<?

/**
 * Cms_Workflow_Condition_Interface 
 * 
 * @package Cms
 * @subpackage Workflow
 */
interface Cms_Workflow_Condition_Interface
{
    /**
     * Evaluate - process the condition
     * 
     * @param Cms_Workflow_Context $context 
     * @access public
     * @return void
     */
    public function Evaluate(Cms_Workflow_Context &$context);

}
