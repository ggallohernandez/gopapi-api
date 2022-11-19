<?php declare(strict_types=1);

namespace App\Services;

use App\DTOs\Certificate;
use App\DTOs\CreateCertificateRequest;

class StepCaCertificateManager implements ICertificateManager
{
    protected IDomainVerifier $domainVerifier;

    public function __construct(IDomainVerifier $domainVerifier)
    {
        $this->domainVerifier = $domainVerifier;
    }

    public function createCertificate(CreateCertificateRequest $request): Certificate
    {
        // todo: create certificate
        return new Certificate($request->getDomain(), 'cert', 'pk');
    }
}