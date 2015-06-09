<?php
/**
 * Created by PhpStorm.
 * User: michele
 * Date: 03/06/15
 * Time: 16.47
 */

 class FileReader {

     /**
      * Simple yaml file reader, returns an array with the yaml file keys
      * @param $fileToRead
      * @return array
      */

     static function ReadFile($fileToRead)
    {
        $yamlStringArray = yaml_parse_file($fileToRead);

        return $yamlStringArray;
    }

}