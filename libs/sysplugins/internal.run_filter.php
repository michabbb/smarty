<?php

/**
* Smarty Internal Plugin Run Filter
* 
* Smarty run filter class
* 
* @package Smarty
* @subpackage PluginsInternal
* @author Uwe Tews 
*/

/**
* Class for filter processing
*/
class Smarty_Internal_Run_Filter extends Smarty_Internal_Base {
    /**
    * Run filters over content
    * 
    * The filters will be lazy loaded if required
    * class name format: Smarty_FilterType_FilterName
    * plugin filename format: filtertype.filtername.php
    * Smarty2 filter plugins could be used
    * 
    * @param string $type the type of filter ('pre','post' or 'output') which shall run
    * @param string $content the content which shall be processed by the filters
    * @return string the filtered content
    */
    public function execute($type, $content)
    {
        $output = $content; 
        // loop over the filters of specified type
        foreach ($this->smarty->autoload_filters[$type] as $name) {
            $plugin_name = "Smarty_{$type}filter_{$name}";

            if (class_exists($plugin_name, false)) {
                // existing filter class
                $output = call_user_func_array(array($plugin_name, 'execute'), array($output, $this->smarty));
            } elseif (function_exists($plugin_name)) {
                // existing filter function
                $output = call_user_func_array($plugin_name, array($output, $this->smarty));
            } elseif ($this->smarty->loadPlugin($plugin_name)) {
                // use class plugin if found
                if (class_exists($plugin_name, false)) {
                    // loaded class of filter plugin
                    $output = call_user_func_array(array($plugin_name, 'execute'), array($output, $this->smarty));
                } elseif (function_exists($plugin_name)) {
                    // use loaded Smarty2 style plugin
                    $output = call_user_func_array($plugin_name, array($output, $this->smarty));
                } 
            } else {
                // nothing found, throw exception
                throw new SmartyException("Unable to load filter {$plugin_name}");
            } 
        } 
        // return filtered output
        return $output;
    } 
} 

?>
