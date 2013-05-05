<?php

class Controller_Widget_BaseSparklines extends Controller_Widget_BaseRaw
{

    protected $sparklines = array();

    protected function render()
    {
        if (!empty($this->sparklines)) {
            $this->content = '<table width="100%" style="margin-top: 30px"><tbody>';
            $width         = $this->getWidth() * 80 - 40;

            $i      = 0;
            $script = '';
            foreach ($this->sparklines as $sparkline) {
                $this->content .= '<tr><td style="text-align: right">' . $sparkline['title'] . '</td>';
                $this->content .= '<td width="' . $width . '"><span id="sparkline_' . $i . '" class="sparkline"></span></td>';
                $this->content .= '<td class="widget-detailed" style="text-align: left">' . $sparkline['data'][sizeof($sparkline['data'])
                        - 1] . '</td>';
                $this->content .= '</tr>';
                $script .= '$(\'#sparkline_' . $i . '\').sparkline(' . json_encode($sparkline['data']) . ', {width: \'100%\'});';
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