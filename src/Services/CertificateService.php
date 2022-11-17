<?php

namespace App\Services;

use App\DTOs\CreateCertificateRequest;

class CertificateService
{
    protected IDomainVerifyer $domainVerifyer;
    protected ICertificateManager $certificateManager;

    public function __construct(IDomainVerifyer $domainVerifyer, ICertificateManager $certificateManager)
    {
        $this->domainVerifyer = $domainVerifyer;
        $this->certificateManager = $certificateManager;
    }

    public function createCertificate(CreateCertificateRequest $request)
    {
        $domain = $request->getDomain();

        if (!$this->domainVerifyer->verify($domain)) {
            throw new \Exception('Domain is not verified');
        }

        $certificate = $this->certificateManager->createCertificate($request);

        return $certificate;
    }
}