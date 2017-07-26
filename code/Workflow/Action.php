<?

/**
 * Cms_Workflow_Action  - this is the default action that will simply exec the code specified in the UML
 * 
 * @uses Cms_Workflow_Action_Interface
 * @package Cms
 * @subpackage Workflow
 */
class Cms_Workflow_Action implements Cms_Workflow_Action_Interface
{
    protected $script = null;
    protected $operation = null;
    protected $args = array();

    function __construct($meta)
    {

        if(empty($meta['script']))
        {
            throw new Cms_Workflow_Exception('Cannot construct an action without specifing script.');
        }

        foreach($meta as $key => $value)
        {
            $this->$key = $value;
        }

    }

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
            Cms_Workflow::addDebug('Executing Action '.$this->script);
            eval($this->script);
        }
   }

}
