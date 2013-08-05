<?php

/**
 * Helper for views
 *
 * @package Helpers
 */
class Helper_View
{

    /**
     * Processes menu
     * 
     * @param array $menu Menu
     * 
     * @return string HTML
     */
    static public function processMenu(array $menu)
    {
        $res = '<ul>';
        foreach ($menu as $_entry) {
            $res .= '<li>';
            if (isset($_entry['href']) || isset($_entry['js'])) {
                $res .= '<a href="';
                if (isset($_entry['href'])) {
                    $res .= $_entry['href'];
                } else {
                    $res .= 'javascript:void(0)" onclick="' . $_entry['js'];
                }
                $res .= '" title="' . $_entry['title'] . '"';
            } else {
                $res .= '<span';
            }
            if (isset($_entry['id'])) {
                $res .= ' id="' . $_entry['id'] . '"';
            }
            if (isset($_entry['selected']) && $_entry['selected']) {
                $res .= ' style="font-weight: bold"';
            }
            $res .= '>' . $_entry['title'];
            if (isset($_entry['img'])) {
                $size = (isset($_entry['img-size']) ? $_entry['img-size'] : 32);
                $res .= ' <img src="img/' . $_entry['img'] . '.png" width="' . $size . '" alt="' . $_entry['alt'] . '"/>';
            }
            if (isset($_entry['href']) || isset($_entry['js'])) {
                $res .= '</a>';
            } else {
                $res .= '</span>';
            }

            if (isset($_entry['submenu'])) {
                $res .= self::processMenu($_entry['submenu']);
            }
            $res .= '</li>';
        }
        $res .= '</ul>';
        return $res;
    }
}