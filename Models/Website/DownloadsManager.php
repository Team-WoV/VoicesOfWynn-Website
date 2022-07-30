<?php

namespace VoicesOfWynn\Models\Website;

use VoicesOfWynn\Models\Db;

class DownloadsManager
{
    private const ROOT_DOWNLOADS_DIRECTORY = 'files/mod';
    private const FILE_NAME_FORMATS = 'VoicesOfWynn-MC{mcVersion}-v{version}.jar';

    /**
     * Lists all downloads, newest to oldest
     * @return ModDownload[] Array of ModDownloads objects
     * @throws \Exception
     */
    public function listDownloads(): array
    {
        $db = new Db('Website/DbInfo.ini');
        $result = $db->fetchQuery('SELECT * FROM download ORDER BY release_date DESC', array(), true);

        if ($result === false) {
            return array();
        }

        $downloads = array();
        foreach ($result as $downloadData) {
            $downloads[] = new ModDownload($downloadData);
        }

        return $downloads;
    }

    /**
     * Sends the mod file to the client to download and increases the count of downloads for that file
     * It also sets all headers for the file download request
     * @param int $downloadId
     * @return bool false, if the download is not found, nothing otherwise (script is terminated with exit();
     * @throws \Exception
     */
    public function downloadFile(int $downloadId): bool
    {
        $db = new Db('Website/DbInfo.ini');
        $result = $db->fetchQuery('SELECT filename,mc_version,version,size FROM download WHERE download_id = ?', array($downloadId));
        if ($result === false) {
            return false;
        }

        $filePath = self::ROOT_DOWNLOADS_DIRECTORY.'/'.$result['filename'];
        $fileName = str_replace('{mcVersion}', $result['mc_version'],
            str_replace('{version}', $result['version'], self::FILE_NAME_FORMATS)
        );
        $fileSize = $result['size'];

        header('Content-Description: File Transfer');
        header('Content-Type: application/java-archive');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.$fileSize);
        readfile($filePath);

        return $db->executeQuery('UPDATE download SET downloaded_times = downloaded_times + 1 WHERE download_id = ?', array($downloadId));
    }

    public function createDownload(string $type, string $mcVersion, string $version, string $changelog, string $path)
    {
        //TODO
    }
}
