<?

/**
 * Cms_Workflow_Signal - a simple wrapper for an eventName corresponding to a Cms_Event
 *
 * @package Cms
 * @subpackage Workflow
 */
class Cms_Workflow_Signal
{
    /**
     * __construct  
     * 
     * @param mixed $meta Must contain a 'name' key that corresponds to a Cms_Event
     * @access protected
     * @return void
     */
    function __construct($meta)
    {

        if(empty($meta['name']))
        {
            
            echo '<pre>'; print_r($meta); echo '</pre>';
            throw new Cms_Workflow_Exception('Cannot construct signal without name');
        }

        foreach($meta as $key => $value)
        {
            $this->$key = $value;
        }

    }

    /**
     * getEventName
     * 
     * @access public
     * @return string
     */
    function getEventName()
    {
        return $this->name;
    }


}
