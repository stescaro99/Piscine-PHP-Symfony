<?php
function array2hash($array): array
{
    $hash = array();
    
    foreach ($array as $item)
    {
        if (is_array($item) && count($item) >= 2)
        {
            $name = $item[0];
            $age = $item[1];
            $hash[$age] = $name;
        }
    }
    return $hash;
}
?>