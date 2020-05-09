<?php
declare(strict_types = 1);
namespace Flossels\EvenMoreSecureDownloads\Domain\Transfer\Token;

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
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Claim;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use Leuchtfeuer\SecureDownloads\Domain\Transfer\ExtensionConfiguration;
use Leuchtfeuer\SecureDownloads\Domain\Transfer\Token\AbstractToken;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\EnvironmentService;

class RsaToken extends AbstractToken
{
    const PRIVATE_KEY_FILE = 'EXT:secure_downloads_example/Resources/Private/Keys/private.key';

    const PUBLIC_KEY_FILE = 'EXT:secure_downloads_example/Resources/Private/Keys/public.key';

    const CLAIMS = ['user', 'groups', 'file', 'page'];

    protected $extensionConfiguration;

    public function __construct()
    {
        parent::__construct();

        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }

    public function encode(?array $payload = null): string
    {
        $builder = new Builder();
        $builder->issuedBy($this->getIssuer());
        $builder->permittedFor($this->getPermittedFor());
        $builder->issuedAt($this->getIat());
        $builder->canOnlyBeUsedAfter($this->getIat());
        $builder->expiresAt($this->getExp());

        foreach (self::CLAIMS as $claim) {
            $getter = 'get' . ucfirst($claim);
            $builder->withClaim($claim, $this->$getter());
        }

        $signer = new Sha256();
        $key = new Key('file://' . GeneralUtility::getFileAbsFileName(self::PRIVATE_KEY_FILE));
        (new ClaimRepository())->addClaim($this);

        return (string)$builder->getToken($signer, $key);
    }

    public function getHash(): string
    {
        return md5(parent::getHash() . $this->getExp());
    }

    public function log(array $parameters = []): void
    {
        // TODO: Implement log() method.
    }

    public function decode(string $jsonWebToken): void
    {
        if (empty($jsonWebToken)) {
            throw new \Exception('Token is empty.', 1588852881);
        }

        $parsedToken = (new Parser())->parse($jsonWebToken);

        if (!$parsedToken->validate($this->getValidationData())) {
            throw new \Exception('Could not validate data.', 1588852940);
        }

        $signer = new Sha256();
        $key = new Key('file://' . GeneralUtility::getFileAbsFileName(self::PUBLIC_KEY_FILE));

        if (!$parsedToken->verify($signer, $key)) {
            throw new \Exception('Could not verify data.', 1588852970);
        }

        foreach ($parsedToken->getClaims() ?? [] as $claim) {
            /** @var $value Claim */
            if (property_exists(__CLASS__, $claim->getName())) {
                $property = $claim->getName();
                $this->$property = $claim->getValue();
            }
        }
    }

    protected function getIssuer(): string
    {
        $environmentService = GeneralUtility::makeInstance(EnvironmentService::class);

        if ($environmentService->isEnvironmentInFrontendMode()) {
            try {
                $pageId = (int)$GLOBALS['TSFE']->id;
                $base = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($pageId)->getBase();

                if ($base->getScheme() !== null) {
                    $issuer = sprintf('%s://%s', $base->getScheme(), $base->getHost());
                } else {
                    // Base of site configuration might be "/" so we have to retrieve the domain from the ENV
                    $issuer = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
                }
            } catch (SiteNotFoundException $exception) {
                $issuer = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
            }
        } elseif ($environmentService->isEnvironmentInBackendMode()) {
            $issuer = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
        }

        return $issuer ?? '';
    }

    protected function getPermittedFor(): string
    {
        return $this->extensionConfiguration->getDocumentRootPath() . $this->extensionConfiguration->getLinkPrefix();
    }

    protected function getValidationData(): ValidationData
    {
        $validationData = new ValidationData();
        $validationData->setIssuer($this->getIssuer());
        $validationData->setAudience($this->getPermittedFor());

        return $validationData;
    }
}
