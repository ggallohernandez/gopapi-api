<?php declare(strict_types=1);

namespace App\Services;

use App\DTOs\Certificate;
use App\DTOs\CreateCertificateRequest;

class CertificateService
{
    protected IDomainVerifier $domainVerifier;
    protected ICertificateManager $certificateManager;

    public function __construct(IDomainVerifier $domainVerifier, ICertificateManager $certificateManager)
    {
        $this->domainVerifier = $domainVerifier;
        $this->certificateManager = $certificateManager;
    }

    public function createCertificate(CreateCertificateRequest $request): Certificate
    {
        $domain = $request->getDomain();

        if (!$this->domainVerifier->verify($domain)) {
            throw new \Exception('Domain is not verified');
        }

        $certificate = $this->certificateManager->createCertificate($request);

        return $certificate;
    }
}