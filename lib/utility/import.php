<?php
namespace dash\utility;

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
        $data   = [];

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
                    $data[] = $one_rows;
                }
            }
            fclose($handle);
        }

        $result = [];

        foreach ($data as $key => $value)
        {
            if(count($value) === count($header))
            {
                $result[]  = array_combine($header, $value);
            }
        }
        return $result;
    }
}
?>