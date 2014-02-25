<?php defined('SYSPATH') or die('No direct script access.');

/**
 * View renderer.
 *
 * @package    Layout
 * @category   Template
 * @author     Gian Carlo Val Ebao <gianebao@gmail.com>
 */
class Layout_View extends Kohana_View {
    
    public static function compress($html)
    {
        if (Kohana::$profiling === TRUE)
        {
            $benchmark = Profiler::start('Layout', __function__);
        }

        preg_match_all('!(<(?:code|pre).*>[^<]+</(?:code|pre)>)!',$html, $pre); //exclude pre or code tags

        $html = preg_replace('!<(?:code|pre).*>[^<]+</(?:code|pre)>!', '#pre#', $html); //removing all pre or code tags

        $html = preg_replace('#<!Ð[^\[].+Ð>#', '', $html); //removing HTML comments

        $html = preg_replace('/[\r\n\t]+/', ' ', $html); //remove new lines, spaces, tabs

        $html = preg_replace('/>[\s]+</', '><', $html); //remove new lines, spaces, tabs

        $html = preg_replace('/[\s]+/', ' ', $html); //remove new lines, spaces, tabs

        if(!empty($pre[0]))
        {
            $count = count($pre[0]);
            $i = 0;
            
            do
            {
                $html = preg_replace('!#pre#!', $pre[0][$i], $html, 1); //putting back pre|code tags
            } while (++$i < $count);
        }

        if (isset($benchmark))
        {
            Profiler::stop($benchmark);
        }

        return $html;
    }
    
    
    protected static function capture($kohana_view_filename, array $kohana_view_data)
    {
        // Import the view variables to local namespace
        extract($kohana_view_data, EXTR_SKIP);

        if (View::$_global_data)
        {
            // Import the global view variables to local namespace
            extract(View::$_global_data, EXTR_SKIP | EXTR_REFS);
        }

        // Capture the view output
        if (!in_array('View::compress', ob_list_handlers()))
        {
            ob_start('View::compress');
        }
        else
        {
            ob_start();
        }

        try
        {
            // Load the view within the current scope
            include $kohana_view_filename;
        }
        catch (Exception $e)
        {
            // Delete the output buffer
            ob_end_clean();

            // Re-throw the exception
            throw $e;
        }

        // Get the captured output and close the buffer
        return ob_get_clean();
    }
}