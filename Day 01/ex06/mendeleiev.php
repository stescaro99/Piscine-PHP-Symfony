<?php
function parseElement($line)
{
    $parts = explode(' = ', $line);
    if (count($parts) !== 2)
        return null;
    $name = trim($parts[0]);
    $attributes = $parts[1];
    preg_match('/position:(\d+)/', $attributes, $positionMatch);
    preg_match('/number:(\d+)/', $attributes, $numberMatch);
    preg_match('/small:\s*([A-Za-z]+)/', $attributes, $symbolMatch);
    preg_match('/molar:([\d.]+)/', $attributes, $molarMatch);
    preg_match('/electron:(.+)$/', $attributes, $electronMatch);
    return [
        'name' => $name,
        'position' => $positionMatch[1],
        'number' => $numberMatch[1],
        'symbol' => $symbolMatch[1],
        'molar' => $molarMatch[1],
        'electron' => trim($electronMatch[1])
    ];
}

function getRowFromAtomicNumber($number) 
{
    if ($number <= 2)
        return 1;
    if ($number <= 10)
        return 2;
    if ($number <= 18)
        return 3;
    if ($number <= 36)
        return 4;
    if ($number <= 54)
        return 5;
    if ($number <= 86)
        return 6;
    return 7;
}

$elements = [];
$lines = file('ex06.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line)
{
    $element = parseElement($line);
    if ($element)
    {
        $row = getRowFromAtomicNumber($element['number']);
        $elements[$row][$element['position']] = $element;
    }
}

$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Periodic Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 1500px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        table {
            border-collapse: collapse;
            margin: 0 auto;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        td {
            border: 2px solid #333;
            padding: 8px;
            width: 70px;
            height: 80px;
            vertical-align: top;
            position: relative;
            background: linear-gradient(135deg, #e3f2fd,rgb(185, 224, 255));
        }
        td.empty {
            border: none;
            background: none;
        }
        h4 {
            margin: 0 0 5px 0;
            font-size: 11px;
            font-weight: bold;
            color: #1565c0;
            text-align: center;
        }
        ul {
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 9px;
            line-height: 1.2;
        }
        li {
            margin: 1px 0;
            color: #333;
        }
        .atomic-number {
            font-weight: bold;
            color: #d32f2f;
        }
        .symbol {
            font-size: 20px;
            font-weight: bold;
            color:rgb(0, 0, 0);
            text-align: center;
            margin: 2px 0;
        }
        .molar {
            color: #388e3c;
        }
        .electron {
            color: #7b1fa2;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Periodic Table</h1>
        <table>
';

$maxRow = max(array_keys($elements));
for ($row = 1; $row <= $maxRow; $row++)
{
    $html .= "            <tr>\n";
    for ($col = 0; $col <= 17; $col++)
    {
        if (isset($elements[$row][$col]))
        {
            $element = $elements[$row][$col];
            $html .= '
                <td>
                    <ul>
                        <li class="atomic-number">' . $element['number'] . '</li>
                        <li class="symbol">' . htmlspecialchars($element['symbol']) . '</li>
                        <h4>' . htmlspecialchars($element['name']) . '</h4>
                        <li class="molar">' . $element['molar'] . '</li>
                        <li class="electron">' . htmlspecialchars($element['electron']) . ' electron' . ($element['electron'] !== '1' ? 's' : '') . '</li>
                    </ul>
                </td>';
        }
        else
            $html .= '<td class="empty"></td>';
    }
    $html .= "</tr>\n";
}
$html .= '
</table>
    </div>
</body>
</html>';

file_put_contents('mendeleiev.html', $html);
echo "Mendeleiev periodic table HTML file has been generated successfully!\n";
echo "Open 'mendeleiev.html' in your web browser to view the table.\n";

?>