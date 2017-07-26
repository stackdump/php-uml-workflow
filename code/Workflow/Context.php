<?

/**
 * Cms_Workflow_Context  - a wrapper for working with session data for a workflow instance
 * 
 * @package Cms
 * @subpackage Workflow
 */
class Cms_Workflow_Context
{
    private $meta; //context workflow-specific data
    private $event; //Cms_Event instance - latest trigger to invoke a transition or action

    private $workflowId; //id of workflow

    function __construct($workflow_id, Cms_Event &$eventObj=null)
    {
        $this->event = $eventObj;
        $this->workflowId = $workflow_id;
        $this->meta = &$_SESSION['workflow'][$this->workflowId]['context_meta'];
    }

    function __wakeup($workflow_id)
    {
        $this->data = &$_SESSION['workflow'][$this->workflowId]['context_meta'];
    }

    function __get($name)
    {
        if(property_exists($this, $name))
        {
            return $this->$name;
        }

        if (isset($this->meta[$name])) 
        {
            return $this->meta[$name];
        }
        return null;
    }

    function __set($name, $value)
    {
        if(property_exists($this, $name))
        {
            throw new Cms_Workflow_Exception (__CLASS__.'::'.$name.' is read-only');
        }
        $this->meta[$name] = $value;
    }
}
