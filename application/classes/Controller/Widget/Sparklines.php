<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Base class for all widgets displaying sparklines
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
abstract class Controller_Widget_Sparklines extends Controller_Widget_Raw
{

    public static $preferredSize  = array(4, 2);
    public static $availableSizes = array(
        array(4, 2), array(4, 4), array(4, 6)
    );
    protected static $extensible     = FALSE;

    /**
     * List of sparklines to display
     * @var array
     */
    protected $sparklines = array();

    /**
     * Renders the widget
     */
    protected function render()
    {
        if (!empty($this->sparklines)) {
            $this->content = '<table width="100%"><tbody>';
            $width         = ($this->getModelWidget()->width - 1) * Owaka::GRIDCELL_SIZE - 2 * Owaka::GRIDCELL_SPACE;

            $i      = 0;
            $script = '';
            foreach ($this->sparklines as $sparkline) {
                $this->content .= '<tr><td style="text-align: right; font-size: 60%;">' . $sparkline['title'] . '</td>';
                if (!empty($sparkline['data'])) {
                    $this->content .= '<td width="' . $width . '"><span id="sparkline_' . $this->getModelWidget()->id . '_' . $i . '" class="sparkline"></span></td>';
                    $this->content .= '<td style="text-align: left">';
                    $this->content .= $sparkline['data'][sizeof($sparkline['data']) - 1];
                    $this->content .= '</td>';
                    $script .= '$(\'#sparkline_' . $this->getModelWidget()->id . '_' . $i . '\').sparkline(' . json_encode($sparkline['data']) . ', {width: \'100%\'});';
                } else {
                    $this->content .= '<td width="' . $width . '">No data</td>';
                }

                $this->content .= '</tr>';
                $i++;
            }
            $this->content .= '</tbody></table>';
            $this->content .= '<script type="text/javascript">
$(document).ready(function() {' . $script . '});
</script>';
        }

        parent::render();
    }
}