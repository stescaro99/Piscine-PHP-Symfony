<?php
function array2hash_sorted($array): array
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
    arsort(array: $hash);
    return $hash;
}
?>