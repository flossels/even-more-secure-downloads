<?php
declare(strict_types = 1);
namespace Flossels\EvenMoreSecureDownloads\Security;

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

use Flossels\EvenMoreSecureDownloads\Domain\Repository\ClaimRepository;
use Flossels\EvenMoreSecureDownloads\Domain\Transfer\Token\RsaToken;
use Leuchtfeuer\SecureDownloads\Security\AbstractCheck;

class OneTimeCheck extends AbstractCheck
{
    /**
     * @var RsaToken
     */
    protected $token;

    public function hasAccess(): bool
    {
        $claimRepository = new ClaimRepository();

        if (!$claimRepository->isClaimed($this->token)) {
            $claimRepository->setClaimed($this->token);

            return true;
        }

        return false;
    }
}
