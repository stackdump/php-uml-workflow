<?php
	    
/**
 * TestWorkflows 
 * 
 * @uses Cms
 * @uses _UnitTest_Case
 * @package Cms
 * @subpackage Tests
 */
class TestWorkflows extends Cms_UnitTest_Case 
{
    function skip()
    {
        echo 'Using: <a href="'.BASE_HREF.'/application/tests/data/test_statechart2.uml">test_statechart2.uml</a>';
    }

    function setup()
    {
        unset($_SESSION['workflow']);
    }

    /**
     * TestRfpWorkflow  - test the actual workflow being used on VMOL
     *
     * @todo we should be able to save the workflow config as php instead of parsing every time
     * update the workflow class to do this
     *
     * EXAMPLE:
     * $workflow_config =  new Cms_Workflow_Config(SITE_ROOT_CUSTOM.'/lib/Workflow/Rfp/config.uml');
     * var_export($workflow_config);die; -- should save this output to a file
     * 
     * @access public
     * @return void
     */
    function _TestRfpWorkflow()
    {

         $WF = Cms_Workflow_Factory::Workflow(TEST_PATH.'/data/test_statechart.uml');
         $this->AssertIsA($WF, 'Cms_Workflow', 'Did not get a workflow back from the factory method');

         //$count = 9;
         //$this->AssertTrue(count($WF->nodes)== 9, "Did not find nodes");
         //echo "<pre>"; echo count($WF->nodes). ' Total Nodes <hr>' ; print_r($WF); echo "</pre>";
    }


    /**
     * TestAutoWorkflow  - this is a sample workflow that should transition all the way to the end right after it loads
     * 
     * @todo finish testint the workflow object with serialization
     * @access public
     * @return void
     */
    function TestAutoWorkflow()
    {
         $WF = Cms_Workflow_Factory::Workflow(TEST_PATH.'/data/test_statechart2.uml');
       
         //$WF = unserialize(serialize($WF)); //simulate serializing this object

         $this->AssertIsA($WF, 'Cms_Workflow', 'Did not get a workflow back from the factory method');

         $WF->reset();
         //echo "<pre>"; echo count($WF->nodes). ' Total Nodes <hr>' ; print_r($WF); echo "</pre>";
         $WF->Init();


         $this->AssertTrue($WF->isComplete() , 'Workflow was not completed');

         $this->AssertTrue($WF->getContext()->foo_count == 3 , 'Loop in workflow did not seem to work.');

         //var_dump($WF->getContext()); //die;
         $this->AssertTrue(count($WF->stateHistory) == 10, 'Did not find 10 states in the stateHistory');

         //var_dump($GLOBALS[CMS_EVT_KEY]);

         //$count = 4;
         //$this->AssertEqual(count($WF->nodes), $count, "Did not find $count nodes");

         $this->assertTrue(count($_SESSION['workflow'][$WF->id]['stateHistory']) == 10, 'Session state history did not match class attribute');
        
    }

}
