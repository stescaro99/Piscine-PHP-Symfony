<?php

function capital_city_from($state): string
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
    if (!array_key_exists($state, $states) || !array_key_exists($states[$state], $capitals))
        return "Unknown\n";
    return $capitals[$states[$state]] . "\n";
}

?>
