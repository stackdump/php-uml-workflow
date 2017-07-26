<?

/**
 * Cms_Workflow_Condition  - serves as a guard clause in a workflow transition
 *  This keeps a transition from happening if it evaluates to false
 * 
 * @uses Cms
 * @uses _Event_Handler
 * @package Cms
 * @subpackage Workflow
 */
class Cms_Workflow_Condition implements Cms_Workflow_Condition_Interface
{
    function __construct($args)
    {
        $this->args = $args;
    }
   
    /**
     * evaluate  - evaluate the guard rule for the workflow
     *
     * @param mixed $args 
     * @access protected
     * @return void
     */
    public function Evaluate(Cms_Workflow_Context &$context)
    {
        /**
         * Allow php script to specify the result
         */
        //this is the node from the XMI that contains the php script 
        if ( isset($this->args['script'])) 
        {
            $return = false;
            $code = ( '$return = ('.$this->args['script'].') ;');

            Cms_Workflow::addDebug('Evaluating condition:'.$code);

            eval($code);

            return $return;
        }
        else
        {
            throw new Cms_Workflow_Exception('Failed to evaluate Condition');
        }

    }
}
