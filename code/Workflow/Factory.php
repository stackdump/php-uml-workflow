<?


/**
 * Cms_Workflow_Factory - Builds workflow instance from a config
 *  NOTE: that currently this is just an unused layer between the actual constructor and the Config
 *  we may want to move validation out of each of constructor functions to this class 
 * 
 * @package Cms
 * @subpackage Workflow
 */
class Cms_Workflow_Factory 
{
    
    /**
     * Cms_Workflow  - accepts a stateflow defined in UML and constructs a workflow instance
     * @todo add config caching here using var_export we don't want to have to parse the UML every time
     * 
     * @param mixed $config_file xml definition of the workflow
     * @static
     * @access public
     * @return void
     */
    static function Workflow($config_file)
    {
        $wf_config = new Cms_Workflow_Config($config_file);

        return new Cms_Workflow($wf_config);
    }

    static function Node($meta)
    {
        return  new Cms_Workflow_Node($meta);
    }

    static function Transition($meta)
    {
        return new Cms_Workflow_Transition($meta);
    }

    static function Signal($meta)
    {
        return new Cms_Workflow_Signal($meta);
    }

    static function Condition($meta)
    {
        return new Cms_Workflow_Condition($meta);
    }

    static function Action($meta)
    {
        return new Cms_Workflow_Action($meta);
    }

    static function Effect($meta)
    {
        return new Cms_Workflow_Effect($meta);
    }

}
