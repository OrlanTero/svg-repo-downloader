<?php

function DownloadZIP($name, $file_includes)
{
    $zip = new ZipArchive;

    if ($zip->open($name, ZipArchive::CREATE) === TRUE) {

        foreach ($file_includes as $file) {
            $zip->addFile($file);
        }

        $zip->close();
    }

    return $zip;
}

function DelTree($dir)
{

    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {

        (is_dir("$dir/$file")) ? DelTree("$dir/$file") : unlink("$dir/$file");

    }

    return rmdir($dir);

}

$title = $_POST['title'];
$urls = json_decode($_POST['urls'], true);
$path = './output/' . $title;
$zipName = './output/' . $title . '.zip';
$success = 0;
$failed = 0;
$success_files = [];

foreach ($urls as $url) {

    if (!file_exists($path)) {
        mkdir($path);
    }

    try {
        $file_name = basename($url);
        $ch = curl_init($url);
        $save_file_loc = $path . '/' . $file_name;

        $fp = fopen($save_file_loc, 'wb');

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        array_push($success_files, $save_file_loc);

        $success++;
    } catch (Exception $e) {
        $failed++;
    }
}


$zip = DownloadZIP($zipName, $success_files);

try {

    DelTree($path);

} catch (Exception $e) {
}

$data = [
    "title" => $title,
    "success" => $success,
    "failed" => $failed,
    "path" => $path,
    "zip" => $zip,
    "zipPath" => $zipName
];

echo json_encode($data);