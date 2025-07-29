<?php
function search_by_states( $string ): array
{
    $states = [
    'Oregon'=> 'OR',
    'Alabama'=> 'AL',
    'New Jersey'=> 'NJ',
    'Colorado'=> 'CO',
    ];
    $capitals = [
    'OR'=> 'Salem',
    'AL'=> 'Montgomery',
    'NJ'=> 'trenton',
    'KS'=> 'Topeka',
    ];

    $array = explode(', ', $string );
    $len = count( $array );
    $ret = array();
    for ( $i = 0; $i < $len; $i++)
    {
        if (in_array($array[$i], $capitals) && in_array(array_search($array[$i], $capitals), $states))
            $ret[$i] = $array[$i] . " is the capital of " . array_search(array_search($array[$i], $capitals), $states);
        else if(array_key_exists($array[$i], $states) && array_key_exists($states[$array[$i]], $capitals))
            $ret[$i] = $capitals[$states[$array[$i]]] . " is the capital of " . $array[$i];
        else
            $ret[$i] = $array[$i] . " is neither a capital nor a state.";
    }
    return $ret;
}
?>
