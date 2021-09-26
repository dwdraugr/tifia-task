<?php

namespace app\commands;

use app\service\referral\ComputingClient;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Throwable;
use yii\console\Controller;
use yii\console\ExitCode;
use DateTimeImmutable;

class ReferralController extends Controller
{
    private ?ComputingClient $computingClient;

    public function __construct($id, $module, $config = [], ComputingClient $computingClient = null)
    {
        parent::__construct($id, $module, $config);
        $this->computingClient = $computingClient;
    }

    /**
     * Get clients refferal tree
     * @param int $clientUid
     * @param int $padding - padding for output tree
     * @param bool $displayFullname - enable displaying fullname in output
     * @return int
     */
    public function actionGetTree(int $clientUid, int $padding = 3, bool $displayFullname = false): int
    {
        $users = $this->computingClient->getReferralsTree($clientUid);
        $output = '';

        foreach ($users as $user) {
            $output .= $user->partner_id === 0 ? '' : '|';
            $output .= str_repeat(' ', $user->level * $padding) . '├──' . $user->client_uid;
            $output .= ($displayFullname ? " ({$user->fullname})" : '') . PHP_EOL;
        }
        echo $output;

        return ExitCode::OK;
    }

    /**
     * Get summarize value of all network
     * @param int $clientUid
     * @param string $dateFrom
     * @param string $dateTo
     * @return int
     * @throws \Exception
     */
    public function actionGetNetworkVolume(
        int $clientUid,
        string $dateFrom,
        string $dateTo
    ): int
    {
        $summarizeValue = $this->computingClient->getReferralsSummarizeValue(
            $clientUid,
            new DateTimeImmutable($dateFrom),
            new DateTimeImmutable($dateTo)
        );
        echo $summarizeValue . PHP_EOL;

        return ExitCode::OK;
    }

    /**
     * Get profit for all network
     * @param int $clientUid
     * @param string $dateFrom
     * @param string $dateTo
     * @return int
     * @throws \Exception
     */
    public function actionGetNetworkProfit(
        int $clientUid,
        string $dateFrom,
        string $dateTo
    ): int
    {
        $profit = $this->computingClient->getReferralsProfit(
            $clientUid,
            new DateTimeImmutable($dateFrom),
            new DateTimeImmutable($dateTo)
        );
        echo $profit . PHP_EOL;

        return ExitCode::OK;
    }

    /**
     * Get count of referral members
     * @param int $clientUid
     * @param bool $isDepth
     * @return int
     */
    public function actionGetReferralsCount(int $clientUid, bool $isDepth = false): int
    {
        if ($isDepth) {
            $count = $this->computingClient->getAllReferralsCount($clientUid);
        } else {
            $count = $this->computingClient->getDirectReferralsCount($clientUid);
        }
        echo $count . PHP_EOL;

        return ExitCode::OK;
    }

    /**
     * Get depth of referral network
     * @param int $clientUid
     * @return int
     */
    public function actionGetNetworkDepth(int $clientUid): int
    {
        $depth = $this->computingClient->getReferralsDepth($clientUid);
        echo $depth . PHP_EOL;

        return ExitCode::OK;
    }
}
