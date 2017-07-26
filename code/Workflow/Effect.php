<?

/**
 * Cms_Workflow_Effect  - represents operations that can happen during a transition
 * The main difference here is that the trigger event is made available in the execute function
 * 
 * @uses Cms
 * @uses _Workflow_Action
 * @package Cms
 * @subpackage Workflow
 */
class Cms_Workflow_Effect extends Cms_Workflow_Action
{
    /**
     * Execute for executing  user defined actions
     * 
     * @access public
     * @return void
     */
    function Execute(Cms_Workflow_Context &$context)
    {
        if (! empty($this->script))
        {
            Cms_Workflow::addDebug('Executing Effect '.$this->script);
            eval($this->script);
        }
    }

}
