<?php
declare(strict_types = 1);
namespace Flossels\EvenMoreSecureDownloads\Domain\Repository;

/***
 *
 * This file is part of the "Even More Secure Downloads" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Florian Wessels <mail@flossels.de>
 *
 ***/

use Flossels\EvenMoreSecureDownloads\Domain\Transfer\Token\RsaToken;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClaimRepository
{
    const TABLE_NAME = 'tx_evenmoresecuredownloads_claims';

    public function addClaim(RsaToken $token): void
    {
        $this->getQueryBuilder()
            ->insert(self::TABLE_NAME)
            ->values([
                'hash' => $token->getHash(),
                'expires' => $token->getExp(),
                'claimed' => 0,
                'user' => 0,
            ])->execute();
    }

    public function setClaimed(RsaToken $token): void
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->update(self::TABLE_NAME)
            ->set('claimed', time())
            ->set('user', $token->getUser())
            ->where($queryBuilder->expr()->eq('hash', $queryBuilder->createNamedParameter($token->getHash())))
            ->execute();
    }

    public function isClaimed(RsaToken $token): bool
    {
        $queryBuilder = $this->getQueryBuilder();
        $claimed = $queryBuilder
            ->select('claimed')
            ->from(self::TABLE_NAME)
            ->where($queryBuilder->expr()->eq('hash', $queryBuilder->createNamedParameter($token->getHash())))
            ->execute()
            ->fetchColumn();

        return (int)$claimed > 0;
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::TABLE_NAME);
    }
}
