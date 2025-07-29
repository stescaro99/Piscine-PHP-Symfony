<?php
include './TemplateEngine.php';
$TemplateEngine = new TemplateEngine();
$TemplateEngine->createFile("output.html", "book_description.html", [
    "nom" => "Harry Potter and the Philosopher's Stone",
    "auteur" => "J.K. Rowling",
    "description" => "Un orfano con la cicatrice sconfigge un tizio balbuziente con il turbante (non è un talebano).",
    "prix" => "Puoi scaricarlo illegalmente e leggerlo gratis o prendere una copia cartacea, che inquina pure di più, e pagare 20",
]);
if (!file_exists("output.html"))
    throw new Exception("Failed to create output file.");
unset($TemplateEngine);
echo "File created successfully.";
?>
