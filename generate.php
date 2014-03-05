<?php
# To Remove
echo "<style>body{margin: 0; padding: 0} body > div  {margin: 1em;}</style>";
# To Remove

include 'utils/functions.php';

# Some usefull variables
$outputDir = conf('folders.output');
$defaultLayout = conf("layouts.default");

# Create output folder
if (!is_dir($outputDir)) { mkdir($outputDir); }

# Gallery Reader
$galleries = galleryReader(conf('folders.gallery'));

# Load Twig
$twig = include('utils/twigLoader.php');

# Get Content Files
$files = checkFiles(conf('folders.input'));

# Get availables Languages
$languages = checkLang(conf('folders.lang'), $outputDir);

# Set Generic Options
$options = [
            'pages' => $files,
            'globals' => $globals,
            'galleries' => $galleries
            ];


    # Render all the files for the current Language
    foreach ($files as $file) {
        # Set file-specific options
        $options['pageKey'] = $file;
        
        if (preg_match('/^\s*\{\#(.*)\#\}\s*/s', file_get_contents("content/$file.twig"), $myoptions)) {
            $myopt = parse_ini_string($myoptions[1]);
            var_dump($myopt);
        }
        
        # Set the layout
        $layout = conf("layouts.$file", $defaultLayout);


        # Loop over the languages
        foreach ($languages as $lang => $langFile) {

            $options['newName'] = "$outputDir/$lang/$file.html";
            $options['lang'] = $lang;
            $options['i18n'] = parse_ini_file("$langFile", true);
            
            # Render the content
            $data = $twig->render("layouts/$layout", $options);
        
            echo "<h1 style='color: white; background: #000; padding: 5px 10px'>$lang/$file</h1><div>$data</div>";

            file_put_contents("$options[newName]", $data);
        }
    
}
