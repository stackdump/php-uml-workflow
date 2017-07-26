<?

/**
 * Cms_Workflow_Transition 
 * 
 * @uses Cms
 * @uses _Event_Handler
 * @package Cms
 * @subpackage Workflow
 */
class Cms_Workflow_Transition extends Cms_Event_Handler
{
    private $id;
    private $name;
    private $target; //target state

    private $node; // reference to workflow_node object

    private $trigger; //KLUDGE the UML diagram diferentiates between a trigger and a signal but we do not

    private $condition; //instance of Cms_Workflow_Transition

    private $signal; //incomming or outgoing

    private $effect; //Cms_Workflow_Action executed during a transition

    private $internal; //boolean indicates that this is an internal transition

    public $method_name; //needed to serve as an event handler
    public $class_name; //needed to serve as an event handler

    public $type; //incomming or outgoing


    function __construct($meta)
    {

        if(empty($meta['id']))
        {
            throw new Cms_Workflow_Exception('Cannot construct new transition without id');
        }

        foreach($meta as $key => $value)
        {
            $this->$key = $value;
        }

    }

    /**
     * setWorkflowSignal - add a signal event that this transition is listening for
     * 
     * @param Cms_Workflow_Signal $signal 
     * @access public
     * @return void
     */
    function setWorkflowSignal(Cms_Workflow_Signal $signal)
    {

        $this->signal = &$signal;

        /**
         *  Don't register an EventHander for incoming transitions
         */
        if (preg_match('/incoming/', $this->type)) return;

        /**
         * Register as an event handler
         */
        $this->method_name = 'Execute';
        $this->class_name = __CLASS__;

        /**
         * Register the trigger event 
         */
        Cms_Event_Manager::RegisterHandler($this->signal->getEventName(), $this);
    }

    function setCondition(Cms_Workflow_Condition $condition)
    {
        $this->condition = $condition;
    }

    function setNode(Cms_Workflow_Node &$node)
    {
        $this->node = &$node;
    }

    function setEffect(Cms_Workflow_Action &$action)
    {
        $this->effect = &$action;
    }

    /**
     * _doAction - execute the action if it is set
     * 
     * @param mixed $action_name 
     * @access private
     * @return void
     */
    private function _doAction($action_name)
    {
        if(is_object($this->$action_name))
        {
            $this->$action_name->Execute($this->getWorkflow()->getContext());
        }
    }

    /**
     * effect - execute an action that happens during a transition
     * 
     * @access public
     * @return void
     */
    public function effect()
    {
        $this->_doAction(__FUNCTION__);
    }

    function getWorkflow()
    {
        return $this->node->workflow;
    }

    /**
     * Execute - this function recieves an event from the Cms_Event_Manager
     * 
     * @param Cms_Event $e 
     * @access public
     * @return string  Returns the target state if this is a valid transition
     */
    function Execute(Cms_Event $e)
    {
        $this->getWorkflow()->setTrigger($e);

        if (! $this->node->isCurrentState() )
        {
            Cms_Workflow::addDebug('Skipped transition:'.$this->name.', this node is not the currentState');

            return; // This transition should not be fired yet
        }

        if(true == $this->condition->Evaluate($this->getWorkflow()->getContext()))
        {
            //change set the new workflow state
            $this->getWorkflow()->setState($this->target, $this);

            return $this->target; //condition passed return target state_id
        }

        Cms_Workflow::addDebug('Skipped transition:'.$this->name.', condition not met');
    }

    /**
     * __get  - transition data is readonly
     */
    function __get($name)
    {
        return $this->$name;
    }

    function __set($name, $value)
    {
        throw new Cms_Workflow_Exception(__CLASS__.' data is read only - Tried to set:'.$name.' = '.$value);
    }
}
