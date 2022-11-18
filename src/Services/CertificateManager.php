<?php declare(strict_types=1);

namespace App\Services;

use App\DTOs\Certificate;
use App\DTOs\CreateCertificateRequest;

class CertificateManager implements ICertificateManager
{
    protected IDomainVerifier $domainVerifier;

    public function __construct(IDomainVerifier $domainVerifier)
    {
        $this->domainVerifier = $domainVerifier;
    }

    public function createCertificate(CreateCertificateRequest $request): Certificate
    {
        if (!$this->domainVerifier->verify($request->domain)) {
            throw new \Exception('Domain is not verified');
        }

        // todo: create certificate
        return new Certificate($request->getDomain(), 'cert', 'pk');
    }
}