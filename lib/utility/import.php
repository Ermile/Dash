<?php
namespace lib\utility;

class import
{
    /**
    *   import data from csv to array
    */
    public static function csv($_file, $_length = 0, $_delimiter = ",", $_enclosure = '"', $_escape = "\\")
    {
        if(!is_string($_file))
        {
            return false;
        }

        $header = [];
        $rows   = [];

        if(($handle = fopen($_file, "r")) !== false)
        {
            while(($one_rows = fgetcsv($handle, $_length, $_delimiter, $_enclosure, $_escape)) !== false)
            {
                if(empty($header))
                {
                    $header = $one_rows;
                }
                else
                {
                    $rows[] = $one_rows;
                }
            }
            fclose($handle);
        }

        $result = [];

        foreach ($rows as $key => $value)
        {
            $result[]  = array_combine($header, $value);
        }

        return $result;
    }

}
?>