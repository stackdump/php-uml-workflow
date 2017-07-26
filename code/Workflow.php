<?

/**
 * Cms_Workflow - workflow object, contains all nodes and transitions for a workflow
 * 
 * @package Cms
 * @subpackage Workflow
 */
class Cms_Workflow 
{
    private $id; //unique id of this workflow
    private $name; 
    private $nodes; //array of Cms_Workflow_Node

    private $stateHistory; //nested array of state_id's and names

    private $beginState; //state id of first node
    private $endState; //state id of last node

    private $trigger; // Cms_Event Object - latest trigger that was fired

    /**
     * __construct Build a workflow from a workflow config object
     *  NOTE: currently the event handlers are registered by the Config object
     *  this is vital to the operation of the workflow as transitions are _ONLY_ trigged by Cms_Events
     *
     * During the call to getNodes() all the transistion events are registered
     *
     * @todo eventually transitions should be registered on wakeup as well
     * 
     * @param Cms_Workflow_Config $cfg 
     * @access protected
     * @return void
     */
    function __construct(Cms_Workflow_Config $cfg)
    {

        $this->id = $cfg->getId();
        $this->name = $cfg->getName();

        if (! $this->id || !$this->name)
        {
            throw new Cms_Workflow_Exception('Workflow name and id are required.');
        }
        
        /**
         */
        foreach($cfg->getNodes() as $node)
        {
            $this->addNode($node);
        }

        //bind stateHistory to the session var
        $this->stateHistory = &$_SESSION['workflow'][$this->id]['stateHistory'];
    }

    /**
     * __wakeup - *NOT COMPLETE* rebuild refs after the workflow is unserialized
     *
     * @todo update this method to  re-register all of the transition events as event handlers
     * @access protected
     * @return void
     */
    function __wakeup()
    {
        $this->stateHistory = &$_SESSION['workflow'][$this->id]['stateHistory'];
    }

    /**
     * __get all class attributes are readonly
     * 
     */
    function __get($name)
    {
        return $this->$name;
    }
    function __set($name, $value)
    {
        throw new Cms_Workflow_Exception(__CLASS__.' data is read only - Tried to set:'.$name);
    }

    /**
     * addNode - add a new node to the workflow
     * 
     * @param Cms_Workflow_Node $node 
     * @access public
     * @return void
     */
    function addNode(Cms_Workflow_Node $node)
    {
        if(! count($node->incoming) && ! count($node->outgoing)) 
        {
            throw new Cms_Workflow_Exception('Cannot add a node with no transitions');
        }

        //set the workflow reference in the node
        $node->setWorkflow($this);

        $this->nodes[$node->id] = $node; 

        if($node->isFirstNode)
        {
            $this->beginState = $node->id;
        }

        if($node->isLastNode)
        {
            $this->endState = $node->id;
        }

    }

    /**
     * _setState push the next state onto the history stack
     *  The last inserted value is the current state.
     * 
     * @param mixed $state_id 
     * @access private
     * @return void
     */
    private function _setState($state_id)
    {
        self::addDebug('State Change '.$this->getCurrentState().' -> '.$state_id);
        $new = $this->getNode($state_id);
        $this->stateHistory[] = (array($new->id, $new->name));
    }

    /**
     * setState change the workflow's currently active state
     *  NOTE: this should really never be called manuall, but triggered by a transition 
     *
     * @param mixed $state_id 
     * @param mixed $transitionId 
     * @access public
     * @return void
     */
    function setState($state_id, Cms_Workflow_Transition $transitionObj)
    {
        $currentNode = $this->getCurrentNode();

        /**
         * enforce internal transitions
         */
        if ($currentNode->id  == $state_id && ! $transitionObj->internal == true) 
        {
            throw new Cms_Workflow_Exception("Cannot call setState on an already active state");
        }
        
        /**
         * execute the transition effect 
         */
         $transitionObj->effect();

         /**
          *  internal transitions cause an entry action or change state
          */
         if ($transitionObj->internal == true)  return;

        /**
         *  Set the new workflow state
         */
        $this->_setState($state_id);

        /**
         * execute the entry action on the new current node.
         */
        $this->getCurrentNode()->entry();

    }

    /**
     * getNode returns the node belonging to the node/state id
     * 
     * @param mixed $state_id 
     * @access public
     * @return Cms_Workflow_Node
     */
    function getNode($state_id)
    {
        if (empty($state_id)) return;

        if( ! is_a($this->nodes[$state_id],  'Cms_Workflow_Node'))
        {
            throw new Cms_Workflow_Exception('Could not find node by id:'.$state_id);
        }
        
        return $this->nodes[$state_id];
    }

    /**
     * setTrigger - set the current Cms_Event Trigger being handled by a transition
     * 
     * @access public
     * @return void
     */
    function setTrigger(Cms_Event $event)
    {
        $this->trigger = $event;
    }

    /**
     * getCurrentState return the current workflow state
     * 
     * @access public
     * @return string state_id
     */
    function getCurrentState()
    {
        $this->stateHistory = &$_SESSION['workflow'][$this->id]['stateHistory'];

        if (! is_array($this->stateHistory) || ! count($this->stateHistory))
        {
            return null;
        }

        list($id, $name) = end($this->stateHistory);

        return $id;
    }

    /**
     * getCurrentNode return node for current state
     * 
     * @access public
     * @return Cms_Workflow_Node
     */
    function getCurrentNode()
    {
        return $this->getNode($this->getCurrentState());
    }

    /**
     * getContext 
     * 
     * @access public
     * @return Cms_Workflow_Context
     */
    function getContext()
    {
        return new Cms_Workflow_Context($this->id, $this->trigger);
    }

    /**
     * Init - Called just after workflow is loaded from the config.
     * NOTE: that this raises an event: CMS_WORKFLOW_INIT
     * 
     * @access public
     * @return void
     */
    function Init()
    {
        self::addDebug('Workflow Init');

        if(! $this->getCurrentState()) 
        {
            $this->reset();
        }

        $this->getCurrentNode()->doActivity();

        /**
         *  For simple workflows most transitions will listen for this event
         */
        Cms_Event_Manager::Raise('CMS_WORKFLOW_INIT'); 
    }

    /**
     * isComplete 
     * 
     * @access public
     * @return boolean true if the workflow is on the last state
     */
    function isComplete()
    {
        return ($this->endState == $this->getCurrentState());
    }

    /**
     * addDebug wrapper method for the global SiteEngine debug 
     * 
     * @param mixed $message 
     * @param mixed $level must be defined as an SE_ error level
     * @static
     * @access public
     * @return void
     */
    static function addDebug($message, $level=Zend_Log::DEBUG)
    {
        
        Zend_Registry::getInstance()
            ->logger->log(__CLASS__.':'.$message, $level);
    }

    /**
     * reset  start from the beginning & drop workflow history
     * 
     * @access public
     * @return void
     */
    function reset()
    {
        self::AddDebug('Reset');
        unset($_SESSION['workflow'][$this->id]);//clear all workflow vars

        $_SESSION['workflow'][$this->id]['stateHistory']= array(); //create empty history

        $this->_setState($this->beginState);//set first state
    }

}
