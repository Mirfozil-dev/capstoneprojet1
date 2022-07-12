<?php
$data = [];
header('Content-Type: application/json; charset=utf-8');

function dir_size($directory)
{
    $size = 0;
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file)
    {
        $size += $file->getSize();
    }
    return $size;
}

function getFilesCount ($dir)
{
    $count = 0;

    foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
        $count += is_file($each) ? 1 : getFilesCount($each);
    }

    return $count;
}

$path = './src/';

$disk_total_space = disk_total_space("/");

$disk_free_space = disk_free_space("/");

$file = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
$files_count = getFilesCount($path);

$filesCountByDate = [];
$totalFileSizeUploadedByDate = [];

foreach (new DirectoryIterator($path) as $fileInfo) {
    if($fileInfo->isDot()) continue;
    $fileDate = date("d", $fileInfo->getMTime());
    $filesCountByDate[$fileDate]++;
    if ($fileInfo->isDir()) {
    	$totalFileSizeUploadedByDate[$fileDate] += dir_size($path.$fileInfo->getFilename());
    } else {
    	$totalFileSizeUploadedByDate[$fileDate] += $fileInfo->getSize();
    }
}
$data = [
    'total_space' => $disk_total_space,
    'free_space' => $disk_free_space,
    'files_count' => $files_count,
    'files_count_by_date' => $filesCountByDate,
    'total_uploaded_file_size' => $totalFileSizeUploadedByDate
];

echo json_encode($data);
?>