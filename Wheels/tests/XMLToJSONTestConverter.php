<?php

function xmlTest2json($file) {
    $xmlTests = simplexml_load_file($file);

    $section = str_replace("\"","\\\"",($xmlTests->attributes()['section']));
    $section = $section ? "\t\"section\" : \"" . $section . "\",\n" : '';
    $tests = "\t\"tests\" : ";
    $json =
    "{\n" .
    $section .
    $tests .
    "[\n";

    $firstGroup = true;
    foreach($xmlTests->group as $testGroup) {
        if (!$firstGroup) {
            $json .= ",\n";
        }
        $firstGroup = false;
        $desc = (string)$testGroup->attributes()['desc'];

        $section = str_replace("\"","\\\"",($testGroup->attributes()['section']));
        $section = $section ? "\t\"section\"  : \"" . $section . "\",\n" : '';

        $desc  = str_replace("\"","\\\"", $desc);
        $desc = "\t\"desc\"    : \"{$desc}\",\n";
        $group = "\t\"group\"   : ";
        $jGroup = "{\n" .
                  $section .
                  $desc .
                  $group .
            "[\n";

        $firstTest = true;
        foreach($testGroup->test as $test) {
            if (!$firstTest) {
                $jGroup .= ",\n";
            }
            $firstTest = false;
            $input = str_replace(array("\n","\""),array("\r\n","\\\""),$test->input);
            $expected = str_replace(array("\n","\""),array("\r\n","\\\""),$test->expected);

            $section = str_replace("\"","\\\"",$test->attributes()['section']);

            $section = $section ? "\t\"section\"   : \"" . $section . "\",\n" : '';
            $input = "\t\"input\"    : \"{$input}\",\n";
            $expected = "\t\"expected\" : \"{$expected}\"\n";
            $jTest ="{\n" .
                    $section .
                    $input .
                    $expected .
                    "}";

            $jGroup .= preg_replace('~^~m',"\t\t",$jTest);
        }
        $jGroup .= "   \n\t]\n" .
            "}";

        $json .= preg_replace('~^~m',"\t\t",$jGroup);
    }


    $json .=
    "   \n\t]\n" .
    "}";
    return $json;
}



function convertDir($dir) {
    $jsonDir = realpath($dir) . '/json';
    if (!file_exists($jsonDir))
        mkdir($jsonDir);
    $xmlFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath($dir)));
    foreach ($xmlFiles as $xmlFile => $iter)
    {
        preg_match('~(?:.*/)?' . $dir . '\\\\(.+)\.xml~',$xmlFile,$subPath);
        if (is_file($xmlFile) && $subPath) {
            $json = xmlTest2json($xmlFile);

            $jsonSubDir = $jsonDir . '\\' . $subPath[1];
            if (!file_exists($jsonSubDir))
                mkdir($jsonSubDir,0777, true);
            file_put_contents($jsonSubDir . '\\' . basename($xmlFile,'.xml') .'.json',$json);
        }
    }
}

convertDir('resources');
