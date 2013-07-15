<?php

class Helper_View
{

    static public function treatMenu(array $menu)
    {
        $res = '<ul>';
        foreach ($menu as $_entry) {
            $res .= '<li><a href="' . $_entry['href'] . '" title="' . $_entry['title'] . '"';
            if (isset($_entry['selected']) && $_entry['selected']) {
                $res .= ' style="font-weight: bold"';
            }
            $res .= '>' . $_entry['title'];
            if (isset($_entry['img'])) {
                $size = (isset($_entry['img-size']) ? $_entry['img-size'] : 32);
                $res .= ' <img src="img/' . $_entry['img'] . '.png" width="' . $size . '" alt="' . $_entry['alt'] . '"/>';
            }
            $res .= '</a>';

            if (isset($_entry['submenu'])) {
                $res .= self::treatMenu($_entry['submenu']);
            }
            $res .= '</li>';
        }
        $res .= '</ul>';
        return $res;
    }
}