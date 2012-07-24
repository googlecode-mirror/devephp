<?php
// +----------------------------------------------------------------------
// | DevePHP [ EASIER EFFICIENT SECURE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://devephp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yearnfar <yearnfar@gmail.com>
// +----------------------------------------------------------------------


 /**
 +------------------------------------------------------------------------------
 * 系统调试类
 +------------------------------------------------------------------------------
 * @category   DevePHP
 * @package  DevePHP
 * @subpackage  Util
 * @author    yearnfar <yearnfar@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Debug extends Deve
{//类定义开始

    static private $marker =  array();
    /**
     +----------------------------------------------------------
     * 标记调试位
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name  要标记的位置名称
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static public function mark($name)
    {
        self::$marker['time'][$name]  =  microtime(TRUE);
        if(MEMORY_LIMIT_ON) {
            self::$marker['mem'][$name] = memory_get_usage();
            self::$marker['peak'][$name] = function_exists('memory_get_peak_usage')?memory_get_peak_usage(): self::$marker['mem'][$name];
        }
    }

    /**
     +----------------------------------------------------------
     * 区间使用时间查看
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $start  开始标记的名称
     * @param string $end  结束标记的名称
     * @param integer $decimals  时间的小数位
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     */
    static public function useTime($start,$end,$decimals = 6)
    {
        if ( ! isset(self::$marker['time'][$start]))
            return '';
        if ( ! isset(self::$marker['time'][$end]))
            self::$marker['time'][$end] = microtime(TRUE);
        return number_format(self::$marker['time'][$end] - self::$marker['time'][$start], $decimals);
    }

    /**
     +----------------------------------------------------------
     * 区间使用内存查看
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $start  开始标记的名称
     * @param string $end  结束标记的名称
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     */
    static public function useMemory($start,$end)
    {
        if(!MEMORY_LIMIT_ON)
            return '';
        if ( ! isset(self::$marker['mem'][$start]))
            return '';
        if ( ! isset(self::$marker['mem'][$end]))
            self::$marker['mem'][$end] = memory_get_usage();
        return number_format((self::$marker['mem'][$end] - self::$marker['mem'][$start])/1024);
    }

    /**
     +----------------------------------------------------------
     * 区间使用内存峰值查看
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $start  开始标记的名称
     * @param string $end  结束标记的名称
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     */
    static function getMemPeak($start,$end) {
        if(!MEMORY_LIMIT_ON)
            return '';
        if ( ! isset(self::$marker['peak'][$start]))
            return '';
        if ( ! isset(self::$marker['peak'][$end]))
            self::$marker['peak'][$end] = function_exists('memory_get_peak_usage')?memory_get_peak_usage(): memory_get_usage();
        return number_format(max(self::$marker['peak'][$start],self::$marker['peak'][$end])/1024);
    }
}//类定义结束
?>
