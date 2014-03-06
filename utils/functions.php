<?php
global $mainConfig;
global $globals;

$mainConfig = parse_ini_file('config/main.ini', true);
$globals = parse_ini_file('config/globals.ini', true);

function _var_extract($name, $array, $default) {
    $exploded_name = explode('.', $name);

    foreach ($exploded_name as $val) {
        if (is_array($array) && isset($array[$val])) {
            $array = $array[$val];
        } else{
            if ($default != NULL){ return $default; }
            else die("Empty Variable: $name");
        } 
    }
    
    return $array;

}

function globals($name, $default = null) {
    global $globals;
    return _var_extract($name, $globals, $default);
}

function conf($name, $default = null) {
    global $mainConfig;
    return _var_extract($name, $mainConfig, $default);
}

function checkFiles($inputDir) {
    $files = scandir($inputDir);
    $tmp = [];

    foreach ($files as $file) {
        if (is_file("$inputDir/$file") && $file[0] != '_' && $file[0] != '.') {
            array_push($tmp, preg_replace('/\.\w+?$/', '', $file));
        }
    }

    return $tmp;
}

function checkLang ($langDir, $outputDir) {
    $languages = scandir($langDir);
    $tmp = [];

    foreach ($languages as $lang) {
        if($lang[0] != '.') {
            $lang = preg_replace('/\.\w+?$/', '', $lang);
            if (!is_dir("$outputDir/$lang")) mkdir("$outputDir/$lang");
            $tmp[$lang] = "$langDir/$lang.lng";
        }
    }

    return $tmp;
    
}


function dataReader ($galleryDir, $galleryConfig = null) {
    
    $nextLevel = ['sub' => [], 'files' => []];
    $nextLevel['config'] = ($galleryConfig == null) ? conf('defaultGalleryConfig') : $galleryConfig;

    $config = conf('defaults.galleryConfigFile');

    $directories = scandir($galleryDir);

    if (array_search($config, $directories)) {
        $nextLevel['config'] =  array_merge($nextLevel['config'], parse_ini_file("$galleryDir/$config", true));
    }

    $directories = array_diff($directories, preg_grep('/^(\..*|config.ini)/', $directories));

    foreach ($directories as $dir) {
        if( is_dir("$galleryDir/$dir") ){ 
            $nextLevel['sub'][$dir] = dataReader("$galleryDir/$dir", $nextLevel['config']);
        }
        else $nextLevel['files'][$dir] = preg_replace('/^\d+\_/', '', $dir);
    }    

    if (sizeof($nextLevel['sub']) == 0) unset($nextLevel['sub']);
    if (sizeof($nextLevel['files']) == 0) unset($nextLevel['files']);
    
    if (isset($nextLevel['sub']) || isset($nextLevel['files'])) return $nextLevel;

    return null;
}