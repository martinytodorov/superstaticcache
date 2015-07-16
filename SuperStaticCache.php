<?php
/***
 * Class SuperStaticCache
 * This class is a component that caches the whole php output into files based on the URL string.
 * Please use it only if you don't have any dynamic content like user login.
 */
class SuperStaticCache extends CApplicationComponent
{
    const SSC_DEFAULT_CACHE = '/runtime/superstaticcache/';

    public $sURL;
    public $duration;
    public $htmlContent;
    public $newFile;
    public $cacheHomepage;
    public $controllers = array(); //Should be in the following format -> ('controllerName')

    private $_fileName;

    public function init()
    {
        $this->_composeFileName();
    }

    public static function startCache()
    {
        //Check if actions are configured
        if(!self::checkActions())
            return false;

        $dirname = dirname(__FILE__);
        //Check if folder exists and create it
        if(!file_exists($dirname . "/.." . self::SSC_DEFAULT_CACHE))
        {
            mkdir($dirname . "/.." . self::SSC_DEFAULT_CACHE, 0777);
        }

        if(file_exists(Yii::app()->superStaticCache->_getFileName()) && !is_dir(Yii::app()->superStaticCache->_getFileName()))
        {
            //Open the file
            $f = Yii::app()->superStaticCache->_openFileToRead();

            //Check if the time has expired
            $timeLine = fgets($f);

            //Get the time without the duration
            $time = strtotime(str_replace(array("<!-- ", " -->"), "", $timeLine)) + Yii::app()->superStaticCache->duration;
            $timeNow = strtotime('now');

            //Compare the time to now and see if its expired
            if($time < $timeNow)
            {
                //Then we need to create a new file
                Yii::app()->superStaticCache->newFile = true;
                ob_start();
            }
            else
            {
                require Yii::app()->superStaticCache->_getFileName();
                Yii::app()->end();
            }
        }
        else
        {
            //Create the new cache file
            Yii::app()->superStaticCache->newFile = true;
            ob_start();
        }
    }


    public static function endCache()
    {
        if(Yii::app()->superStaticCache->newFile)
        {
            $htmlContent = "<!-- " . date('Y-m-d H:i:s') . " -->" . PHP_EOL . ob_get_contents();
            ob_end_clean();

            $f = Yii::app()->superStaticCache->_openFileToWrite();
            fwrite($f, $htmlContent);
            fclose($f);

            echo $htmlContent;
        }
    }


    private function _getFileName()
    {
        return $this->_fileName;
    }

    private function _composeFileName()
    {
        $this->_fileName = dirname(__FILE__) . "/.." . self::SSC_DEFAULT_CACHE . $_SERVER['HTTP_HOST'] . preg_replace('/[^A-Za-z0-9 _ .-]/', '', $_SERVER['REQUEST_URI']) . ".html";
    }

    private static function _getTimeAsNumber()
    {
        return intval(date('YmdHis'));
    }

    private static function _openFileToRead()
    {
        return fopen(Yii::app()->superStaticCache->_getFileName(), 'r');
    }

    private static function _openFileToWrite()
    {
        //This cleans up the file and we can refill it
        return fopen(Yii::app()->superStaticCache->_getFileName(), 'w+');
    }

    public static function checkActions()
    {
        if(empty(Yii::app()->superStaticCache->controllers))
            return true;

        $arr = Yii::app()->superStaticCache->controllers;

        //Cache the homepage
        if(Yii::app()->superStaticCache->cacheHomepage === true && $_SERVER['REQUEST_URI'] == '/')
            return true;
        else
        {
            foreach($arr as $v)
            {
                if(strpos($_SERVER['REQUEST_URI'], $v))
                    return true;
            }
        }

        return false;
    }
}