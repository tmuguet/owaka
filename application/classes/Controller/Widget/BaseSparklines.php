<?php

/**
 * Base class for all widgets displaying sparklines
 */
abstract class Controller_Widget_BaseSparklines extends Controller_Widget_BaseRaw
{

    /**
     * List of sparklines to display
     * @var array
     */
    protected $sparklines = array();

    /**
     * Gets the preferred size (width, height)
     * @return int[]
     */
    static public function getPreferredSize()
    {
        return array(4, 2);
    }

    /**
     * Gets the expected parameters
     * @return int[][]
     */
    static public function getOptimizedSizes()
    {
        return array(
            array(4, 2), array(4, 4), array(4, 6)
        );
    }

    /**
     * Renders the widget
     */
    protected function render()
    {
        if (!empty($this->sparklines)) {
            $this->content = '<table width="100%" style="margin-top: 30px"><tbody>';
            $width         = $this->getWidth() * Owaka::GRIDCELL_SIZE - 2 * Owaka::GRIDCELL_SPACE;

            $i      = 0;
            $script = '';
            foreach ($this->sparklines as $sparkline) {
                $this->content .= '<tr><td style="text-align: right">' . $sparkline['title'] . '</td>';
                $this->content .= '<td width="' . $width . '"><span id="sparkline_' . $this->getModelWidget()->id . '_' . $i . '" class="sparkline"></span></td>';
                $this->content .= '<td class="widget-detailed" style="text-align: left">';
                $this->content .= $sparkline['data'][sizeof($sparkline['data']) - 1];
                $this->content .= '</td>';
                $this->content .= '</tr>';
                $script .= '$(\'#sparkline_' . $this->getModelWidget()->id . '_' . $i . '\').sparkline(' . json_encode($sparkline['data']) . ', {width: \'100%\'});';
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