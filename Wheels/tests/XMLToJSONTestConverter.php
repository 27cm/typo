<?php

function escapeJSONString($str)
{
    return str_replace(array("\\", "\"", "\r\n", "\n", "\t"), array("\\\\", "\\\"", "\\r\\n", "\\r\\n", "\\t"), $str);
}

function xmlTest2json($file)
{
    $xmlTests = simplexml_load_file($file);

    $indent = "    ";
    $lineSep = "\r\n";
    $section = escapeJSONString($xmlTests->attributes()['section']);
    $section = $section ? "{$indent}\"section\" : \"" . $section . "\",'{$lineSep}" : '';
    $tests = "{$indent}\"tests\" : ";
    $json
        = "{{$lineSep}" .
        $section .
        $tests .
        "[{$lineSep}";

    $firstGroup = true;
    foreach ($xmlTests->group as $testGroup) {
        if (!$firstGroup) {
            $json .= ",{$lineSep}";
        }
        $firstGroup = false;
        $desc = (string)$testGroup->attributes()['desc'];

        $section = escapeJSONString($testGroup->attributes()['section']);
        $section = $section ? "{$indent}\"section\"  : \"" . $section . "\",{$lineSep}" : '';

        if ($desc) {
            $desc = escapeJSONString($desc);
            $desc = "{$indent}\"desc\"    : \"{$desc}\",{$lineSep}";
        }
        $group = "{$indent}\"group\"   : ";
        $jGroup = "{{$lineSep}" .
            $section .
            $desc .
            $group .
            "[{$lineSep}";

        $firstTest = true;
        foreach ($testGroup->test as $test) {
            if (!$firstTest) {
                $jGroup .= ",{$lineSep}";
            }
            $firstTest = false;
            $input = escapeJSONString($test->input);
            $expected = escapeJSONString($test->expected);

            $section = escapeJSONString($test->attributes()['section']);

            $section = $section ? "{$indent}\"section\"   : \"" . $section . "\",{$lineSep}" : '';
            $input = "{$indent}\"input\"    : \"{$input}\",{$lineSep}";
            $expected = "{$indent}\"expected\" : \"{$expected}\"{$lineSep}";
            $jTest = "{{$lineSep}" .
                $section .
                $input .
                $expected .
                "}";

            $jGroup .= preg_replace('~^~m', "{$indent}{$indent}", $jTest);
        }
        $jGroup .= "{$indent}{$lineSep}{$indent}]{$lineSep}" .
            "}";

        $json .= preg_replace('~^~m', "{$indent}{$indent}", $jGroup);
    }


    $json
        .= "{$indent}{$lineSep}{$indent}]{$lineSep}" .
        "}";
    return $json;
}


function convertDir($dir)
{
    $jsonDir = realpath($dir) . '/json';
    if (!file_exists($jsonDir)) {
        mkdir($jsonDir);
    }
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath($dir)));
    foreach ($files as $file => $iter) {
        preg_match('~(?:.*/)?' . $dir . '\\\\(.+)\\\\.+\.(xml|json)~', $file, $matches);
        $subPath = $matches[1];
        $ext = $matches[2];
        if (is_file($file)) {
            if ($ext == 'xml') {
                $json = xmlTest2json($file);

                $jsonSubDir = $jsonDir . '\\' . $subPath;
                if (!file_exists($jsonSubDir)) {
                    mkdir($jsonSubDir, 0777, true);
                }
                file_put_contents($jsonSubDir . '\\' . basename($file, '.xml') . '.json', $json);
            } else {
                if ($ext == 'json') {
                    copy($file, $jsonSubDir . '\\' . basename($file));
                }
            };
        }
    }
}

convertDir('resources');
