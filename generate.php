<?php
# To Remove
echo "<style>body{margin: 0; padding: 0} body > div  {margin: 1em;}</style>";
# To Remove

include 'utils/functions.php';

# Some usefull variables
$outputDir = conf('folders.output');
$defaultLayout = conf("defaults.layout");

# Data Reader
$data = galleryReader(conf('folders.data'));

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
            'data' => $data
            ];


    # Render all the files for the current Language
    foreach ($files as $file) {

        # Set file-specific options
        $options['pageKey'] = $file;
        
        if (preg_match('/^\s*\{\#(.*)\#\}\s*/s', file_get_contents("content/$file.twig"), $myoptions)) {
            $myopt = parse_ini_string($myoptions[1]);
            if (isset($myopt['data'])) {
                foreach ($myopt['data'] as $item) {
                    $my2opt = explode(':', $item);    
                    if(isset($my2opt[1])) {
                        echo "pagination: " . $my2opt[1];
                    }
                    $options['data'] = $data['sub'][$my2opt[0]];
                }
            }
        }
        
        # Set the layout TODO: Use the actual file options to load a layout
        $layout = isset($myopt['layout']) ? $myopt['layout'] : $defaultLayout;


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
