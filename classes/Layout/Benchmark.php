<?php defined('SYSPATH') OR die('No direct access allowed.');

class Layout_Benchmark {
    
    public static function get ()
    {
        $arr = func_get_args();
        $fn = array_shift($arr);
        
        if (TRUE !== Kohana::$profiling)
        {
            return call_user_func_array($fn, $arr);
        }
        
        if (is_array($fn))
        {
            $cl_name = get_class($fn[0]);
            $fn_name = $fn[1];
        }
        elseif (0 === strpos($fn, 'self::'))
        {
            $fn_name = explode('::', $fn);
            $cl_name = get_class();
            $fn_name = $fn_name[1];
        }
        else
        {
            $fn_name = explode('::', $fn);
            $cl_name = $fn_name[0];
            $fn_name = $fn_name[1];
        }
        
        $benchmark = Profiler::start($cl_name, $fn_name);
        $response = call_user_func_array($fn, $arr);
        Profiler::stop($benchmark);
        
        return $response;
    }
}