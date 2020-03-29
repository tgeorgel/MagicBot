<?php

namespace App\Classes;

class Config
{
    private static $_instance = null;

    private static $path = __DIR__ . "/../../config";

    private $config = [];


    /**
     * Return Config instance
     */
    public static function getinstance(): Config
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    /**
     * Init the class
     */
    private function __construct()
    {
        $this->setConfig();
    }


    /**
     * Variable getter
     *
     * @param string $module_name Module name to get the config for
     * 
     * @return mixed
     * @access public
     * @since 2.0
     */
    public function __get(string $key)
    {
        return $this->config[$key] ?? false;
    }


    /**
     * Read all JSON files and initiate the config array
     *
     * @return void
     * @since 2.0
     */
    public function setConfig()
    {
        $files = $this->getConfigFiles();
        $cfg = [];

        if (!empty($files)) {
            foreach ($files as $file) {
                $module_name = substr($file, 0, strrpos($file, '.'));
                $cfg[$module_name] = $this->jsonToConfig($file);
            }
        }

        $this->config = $cfg;
    }


    /**
     * Read a single JSON file and get required fields informations
     *
     * @param string $file Json file name
     * @return array
     * @since 2.0
     */
    private function jsonToConfig(string $file)
    {
        return json_decode(file_get_contents(static::$path . '/' . $file));
    }


    /**
     * Retrieve JSON files
     *
     * @return array
     * @since 2.0
     */
    private function getConfigFiles(): array
    {
        $files = scandir(static::$path);

        if (!$files) {
            return [];
        }

        return array_filter($files, function ($file) {
            return strpos($file, '.json') !== false;
        }) ?? [];
    }
}
