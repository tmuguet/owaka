<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Helper for views
 *
 * @package   Helpers
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
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
            if (isset($_entry['class'])) {
                $res .= ' class="' . $_entry['class'] . '"';
            }
            if (isset($_entry['id'])) {
                $res .= ' id="' . $_entry['id'] . '"';
            }
            if (isset($_entry['selected']) && $_entry['selected']) {
                $res .= ' style="font-weight: bold"';
            }
            $res .= '>' . $_entry['title'];
            if (isset($_entry['img'])) {
                $res .= ' <i class="icon-' . $_entry['img'] . '"></i>';
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