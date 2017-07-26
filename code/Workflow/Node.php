<?

/**
 * Cms_Workflow_Node 
 * 
 * @package Cms
 * @subpackage Workflow
 */
class Cms_Workflow_Node implements Cms_Workflow_Node_Interface
{
    private $id; // id of the node
    private $name; // friendly name for the node

    private $workflow; // reference to workflow object

    private $isFirstNode;
    private $isLastNode; 

    private $outgoing; //Transistions
    private $incoming; //Transistions -- currenty not used for anything

    private $entry; //Action triggered on setState()
    private $doActivity; //Action triggered on INIT

    public $method_name; //needed to serve as an event handler
    public $class_name; //needed to serve as an event handler

    /**
     * __construct 
     * 
     * @access protected
     * @return void
     */
    function __construct($meta)
    {
        if(empty($meta['name']) || empty($meta['id']))
        {
            throw new Cms_Workflow_Exception('Cannot construct a node without name and id');
        }

        foreach($meta as $key => $value)
        {
            $this->$key = $value;
        }

        $this->incoming= array();
        $this->outgoing= array();
    }

    /**
     * __get - all node data is read-only
     * 
     * @param mixed $name 
     * @access protected
     * @return void
     */
    function __get($name)
    {
        return $this->$name;
    }

    function __set($name, $value)
    {
        throw new Cms_Workflow_Exception( __CLASS__.' data is read only. tried to set: '.$name);
    }

    /**
     * _addTransition private method to add in or out transistion
     * NOTE: that currenlty incomming transitions are not used in the workflow
     * 
     * @param Cms_Workflow_Transition $transition 
     * @access private
     * @return void
     */
    private function _addTransition(Cms_Workflow_Transition &$transition)
    {
        $transition->setNode($this);

        if (preg_match('/incoming/', $transition->type))
        {
            return $this->incoming[$transition->id] = $transition;
        }
        else
        {
            return $this->outgoing[$transition->id] = $transition;
        }

        /*
        if(! is_a($transition->condition, 'Cms_Workflow_Condition'))
        {
            throw new Cms_Workflow_Exception('Cannot add a transition that does not contain a condition');
        }
        */


    }

    /**
     * addOutTransition add outgoing transition
     * 
     * @param Cms_Workflow_Transition $transition 
     * @access public
     * @return void
     */
    public function addOutTransition(Cms_Workflow_Transition &$transition)
    {
        return $this->_addTransition($transition);
    }

    /**
     * addInTransition add incoming transition
     * 
     * @param Cms_Workflow_Transition $transition 
     * @access public
     * @return void
     */
    public function addInTransition(Cms_Workflow_Transition &$transition)
    {
        return $this->_addTransition($transition);
    }

    public function getOutTransition($transitionId)
    {
        if(isset($this->outgoing[$transitionId]))
        {
            return $this->outgoing[$transitionId];
        }
        return null;
    }

    public function setDoActivity(Cms_Workflow_Action &$action)
    {
        $this->doActivity = &$action;
    }

    public function setEntry(Cms_Workflow_Action &$action)
    {
        $this->entry = &$action;
    }

    /**
     * setWorkflow - set reference to the workflow container
     * 
     * @param Cms_Workflow $wf 
     * @access public
     * @return void
     */
    public function setWorkflow(Cms_Workflow &$wf)
    {
        $this->workflow = &$wf;
    }

    private function _doAction($action_name)
    {
        if(is_a($this->$action_name, 'Cms_Workflow_Action'))
        {
            $this->$action_name->Execute($this->workflow->getContext());
        }
    }

    /**
     * entry - executed after a transition
     * 
     * @access public
     * @return void
     */
    public function entry()
    {
        $this->_doAction(__FUNCTION__);
    }

    /**
     * doActivity - execute on WO
     * 
     * @access public
     * @return void
     */
    public function doActivity()
    {
        $this->_doAction(__FUNCTION__);
    }

    public function isCurrentState()
    {
       return  $this->workflow->getCurrentState() == $this->id;
    }
}
