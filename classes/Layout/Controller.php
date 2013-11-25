<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Layout templating.
 *
 * @package    Layout
 * @category   Template
 * @author     Gian Carlo Val Ebao <gianebao@gmail.com>
 */
class Layout_Controller extends Controller
{

    /**
     * @var  boolean  auto render template
     */
    public $auto_render = TRUE;
    
    /**
     * Path of the contents container folder relative to the view folder.
     */
    public $content_path = 'content';

    /**
     * template template file
     */
    public $template = 'template/:device/default';

    /**
     * Path of the contents container folder relative to the view folder.
     */
    public $subfolder = null;

    /**
     * header template file
     */
    public $header = 'template/:device/header';

    /**
     * footer template file
     */
    public $footer = 'template/:device/footer';

    /**
     * layout object
     */
    public $layout = null;

    /**
     * Loads the template [View] object.
     */
    public function before()
    {
        if (Kohana::$profiling === TRUE)
        {
            $benchmark = Profiler::start(get_class(), __function__);
        }
        
        parent::before();

        // Just in case the whole controller does not need rendiring.
        if ($this->auto_render !== TRUE)
        {
            if (isset($benchmark))
            {
                Profiler::stop($benchmark);
            }
        
            return true;
        }
        
        $controller = str_replace('_', '/', strtolower($this->request->controller()));
        $action = strtolower($this->request->action());
        
        $body = $this->content_path . DIRECTORY_SEPARATOR . $controller
            . (!empty($this->subfolder) ? DIRECTORY_SEPARATOR . $this->subfolder: '') . DIRECTORY_SEPARATOR . $action;
        
        // Create content layout depending on the controller and action.
        
        //Render the layout as ajax.
        if ($this->request->is_ajax())
        {
            if (isset($benchmark))
            {
                Profiler::stop($benchmark);
            }
            
            return $this->layout = new View(Layout_Parser::evaluate($body));
        }
        
        $this->layout = Layout::factory($this->template, $body);
        
        // the following can also be manually called in the action method. Layout_Parser is already done by set_header and set_footer
        if (!empty($this->header))
        {
            $this->layout->set_header($this->header);
        }

        if (!empty($this->footer))
        {
            $this->layout->set_footer($this->footer);
        }
        
        if (isset($benchmark))
        {
            Profiler::stop($benchmark);
        }
    }

    /**
     * Assigns the template [View] as the request response.
     */
    public function after()
    {
        if (Kohana::$profiling === TRUE)
        {
            $benchmark = Profiler::start(get_class(), __function__);
        }
        
        if ($this->auto_render === TRUE)
        {
            $this->response->body($this->layout);
        }

        parent::after();

        if (isset($benchmark))
        {
            Profiler::stop($benchmark);
        }
    }
}