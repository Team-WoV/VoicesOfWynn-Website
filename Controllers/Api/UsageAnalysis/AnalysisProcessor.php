<?php

namespace VoicesOfWynn\Controllers\Api\UsageAnalysis;

use DateTime;
use VoicesOfWynn\Controllers\Api\ApiController;
use VoicesOfWynn\Models\Api\UsageAnalysis\BootupLogger;
use VoicesOfWynn\Models\Api\UsageAnalysis\PingAggregator;

class AnalysisProcessor extends ApiController
{

    public function process(array $args): int
    {
        if (!isset($_REQUEST['apiKey'])) {
            return 401;
        }

        switch ($args[0]) {
            case 'aggregate':
                return $this->aggregate();
            default:
                return 400;
        }
    }

    private function aggregate(): int
    {
        /*if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            return 405;
        }*/
        if ($_REQUEST['apiKey'] !== self::AGGREGATE_API_KEY) {
            return 401;
        }
        $logger = new PingAggregator();
        return $logger->aggregateDay(
            new DateTime('@'.(time() - (
                max(BootupLogger::MINIMUM_DELAY_BETWEEN_PINGS_BY_IP, BootupLogger::MINIMUM_DELAY_BETWEEN_PINGS_BY_UUID)
            )))
        );
    }
}
