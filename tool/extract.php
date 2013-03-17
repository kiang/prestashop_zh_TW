<?php
$locale = 'tw';
$enReference = 'english_reference';

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

$ao = Gettext\Extractors\Po::extract(dirname(__FILE__) . DIRECTORY_SEPARATOR . $locale . '.po');
$lang = Gettext\Generators\PhpArray::generate($ao);

$folders = array(
    'modules/*/*/*.php',
    'themes/*/*/*.php',
    'translations/*/*.php'
);
$variableKeys = array('_MODULE', '_LANG', '_LANGADM', '_ERRORS', '_FIELDS', '_LANGPDF', 'tabs');
$path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$englishPath = $path . $enReference . DIRECTORY_SEPARATOR;
$englishPathLen = strlen($englishPath);
foreach ($folders AS $folder) {
    foreach (glob($englishPath . $folder) AS $file) {
        foreach ($variableKeys AS $variableKey) {
            $$variableKey = array();
        }
        $enStack = array();
        include $file;
        $targetLang = '';
        foreach ($variableKeys AS $variableKey) {
            if (!empty($$variableKey)) {
                $targetLang .= "<?php\n\nglobal \${$variableKey};\n\${$variableKey} = array();\n";
                foreach ($$variableKey AS $key => $val) {
                    $val = str_replace('\\"', '"', $val);
                    if (!isset($lang['messages'][$val])) {
                        echo $val . "\n";
                        continue;
                    }
                    $lang['messages'][$val][1] = str_replace('\'', '\\\'', $lang['messages'][$val][1]);
                    $lang['messages'][$val][1] = str_replace('\\\\\'', '\\\'', $lang['messages'][$val][1]);
                    if(empty($lang['messages'][$val][1])) continue;
                    $targetLang .= "\${$variableKey}['{$key}'] = '{$lang['messages'][$val][1]}';\n";
                }
                if('tabs.php' === substr($file, -8)) {
                    $targetLang .= "return \$tabs;\n";
                }
                $targetLang .= '?>';
            }
        }

        $targetLangFile = $path . substr($file, $englishPathLen);
        $targetLangFile = str_replace('/en', '/' . $locale, $targetLangFile);
        $dir = dirname($targetLangFile);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!empty($targetLang)) {
            file_put_contents($targetLangFile, $targetLang);
        }
    }
}