<?php

function autoload($className) {
    $className = ltrim($className, '\\');
    $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    $namespace = '';

    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    if (is_file($fileName)) {
        require $fileName;
    }
}

spl_autoload_register('autoload');

$folders = array(
    'modules/*/*/*.php',
    'themes/*/*/*.php',
    'translations/*/*.php'
);
$variableKeys = array('_MODULE', '_LANG', '_LANGADM', '_ERRORS', '_FIELDS', '_LANGPDF', 'tabs');
$path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$englishPath = $path . 'english_reference' . DIRECTORY_SEPARATOR;
$englishPathLen = strlen($englishPath);
$langResult = array();
foreach ($folders AS $folder) {
    foreach (glob($englishPath . $folder) AS $file) {
        foreach ($variableKeys AS $variableKey) {
            $$variableKey = array();
        }
        $enStack = $twStack = array();
        include $file;
        foreach ($variableKeys AS $variableKey) {
            if (!empty($$variableKey)) {
                foreach ($$variableKey AS $key => $val) {
                    $enStack[$key] = $val;
                }
            }
        }

        $targetLangFile = $path . substr($file, $englishPathLen);
        $targetLangFile = str_replace('/en', '/tw', $targetLangFile);
        if (file_exists($targetLangFile)) {
            foreach ($variableKeys AS $variableKey) {
                $$variableKey = array();
            }
            include $targetLangFile;
            foreach ($variableKeys AS $variableKey) {
                if (!empty($$variableKey)) {
                    foreach ($$variableKey AS $key => $val) {
                        $twStack[$key] = $val;
                    }
                }
            }
        }

        foreach ($enStack AS $key => $val) {
            $langResult[$val] = '';
            if(isset($twStack[$key]) && ($val != $twStack[$key])) {
                $langResult[$val] = $twStack[$key];
            }
        }
    }
}

if (!empty($langResult)) {
    $translations = array();
    foreach($langResult AS $key => $val) {
        $translation = new Gettext\Translation(null, $key);
        $translation->setTranslation($val);
        $translations[] = $translation;
    }
    $entries = new Gettext\Entries($translations);
    Gettext\Generators\Po::generateFile($entries, dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tw.po');
}