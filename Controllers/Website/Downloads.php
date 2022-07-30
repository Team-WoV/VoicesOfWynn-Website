<?php

namespace VoicesOfWynn\Controllers\Website;

use VoicesOfWynn\Models\Website\DownloadsManager;

class Downloads extends WebpageController
{
    
    /**
     * @inheritDoc
     */
    public function process(array $args): int
    {
        self::$data['base_title'] = 'Downloads';
        self::$data['base_description'] = 'Downloads of all versions of the mod are available here. Download the latest version, install it to your Minecraft client and enjoy voiced Wynncraft.';
        self::$data['base_keywords'] = 'Minecraft,Wynncraft,Mod,Voice,Downloads,Download';
    
		$downloadsManager = new DownloadsManager();
		self::$data['downloads_versions'] = $downloadsManager->listDownloads();

        self::$views[] = 'downloads';
        return true;
    }
}