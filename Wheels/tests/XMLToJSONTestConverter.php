<?php

function escapeJSONString($str) {
   return  str_replace(array("\\","\"","\r\n","\n","\t"),array("\\\\","\\\"","\\r\\n","\\n","\\t"),$str);
}
function xmlTest2json($file) {
    $xmlTests = simplexml_load_file($file);

    $indent = "    ";
    $section = escapeJSONString($xmlTests->attributes()['section']);
    $section = $section ? "{$indent}\"section\" : \"" . $section . "\",\r\n" : '';
    $tests = "{$indent}\"tests\" : ";
    $json =
    "{\r\n" .
    $section .
    $tests .
    "[\r\n";

    $firstGroup = true;
    foreach($xmlTests->group as $testGroup) {
        if (!$firstGroup) {
            $json .= ",\r\n";
        }
        $firstGroup = false;
        $desc = (string)$testGroup->attributes()['desc'];

        $section = escapeJSONString($testGroup->attributes()['section']);
        $section = $section ? "{$indent}\"section\"  : \"" . $section . "\",\r\n" : '';

        if ($desc) {
            $desc  = escapeJSONString($desc);
            $desc = "{$indent}\"desc\"    : \"{$desc}\",\r\n";
        }
        $group = "{$indent}\"group\"   : ";
        $jGroup = "{\r\n" .
                  $section .
                  $desc .
                  $group .
            "[\r\n";

        $firstTest = true;
        foreach($testGroup->test as $test) {
            if (!$firstTest) {
                $jGroup .= ",\r\n";
            }
            $firstTest = false;
            $input = escapeJSONString($test->input);
            $expected = escapeJSONString($test->expected);

            $section = escapeJSONString($test->attributes()['section']);

            $section = $section ? "{$indent}\"section\"   : \"" . $section . "\",\r\n" : '';
            $input = "{$indent}\"input\"    : \"{$input}\",\r\n";
            $expected = "{$indent}\"expected\" : \"{$expected}\"\r\n";
            $jTest ="{\r\n" .
                    $section .
                    $input .
                    $expected .
                    "}";

            $jGroup .= preg_replace('~^~m',"{$indent}{$indent}",$jTest);
        }
        $jGroup .= "{$indent}\r\n{$indent}]\r\n" .
            "}";

        $json .= preg_replace('~^~m',"{$indent}{$indent}",$jGroup);
    }


    $json .=
    "{$indent}\r\n{$indent}]\r\n" .
    "}";
    return $json;
}



function convertDir($dir) {
    $jsonDir = realpath($dir) . '/json';
    if (!file_exists($jsonDir))
        mkdir($jsonDir);
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath($dir)));
    foreach ($files as $file => $iter)
    {
        preg_match('~(?:.*/)?' . $dir . '\\\\(.+)\\\\.+\.(xml|json)~',$file,$matches);
        $subPath = $matches[1];
        $ext =  $matches[2];
        if (is_file($file)) {
            if ($ext == 'xml') {
                $json = xmlTest2json($file);

                $jsonSubDir = $jsonDir . '\\' . $subPath;
                if (!file_exists($jsonSubDir))
                    mkdir($jsonSubDir,0777, true);
                file_put_contents($jsonSubDir . '\\' . basename($file,'.xml') .'.json',$json);
            }
            else if ($ext == 'json') {
                copy($file,$jsonSubDir . '\\' . basename($file));
            };
        }
    }
}

convertDir('resources');
