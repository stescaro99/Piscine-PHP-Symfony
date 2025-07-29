<?php

$filename = 'ex01.txt';

if (file_exists($filename))
{
    $content = file_get_contents($filename);
    $content = trim($content);
    $values = explode(',', $content);
    $length = count($values);
    for ($i = 0; $i < $length; $i++)
    {
        echo $values[$i];
        if ($i < $length - 1)
            echo "\n";
    }
}
else
    echo "Errore: il file $filename non esiste.";
?>

