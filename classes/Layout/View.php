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

        $html = preg_replace('#<!�[^\[].+�>#', '', $html); //removing HTML comments

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
    
    public function render($file = NULL)
    {
        $output = parent::render($file);
        
        return View::compress($output);
    }
}