<?php

namespace App\Tests;

use App\DTOs\Certificate;
use PHPUnit\Framework\TestCase;

use App\DTOs\CreateCertificateRequest;
use App\Services\CertificateService;
use App\Services\ICertificateManager;
use App\Services\IDomainVerifyer;

class CertificateManagementTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateANewCertificate()
    {
        $domain = 'test.com';
        $validity_in_days = 365;
        $csr = '';
        
        $request = new CreateCertificateRequest($domain, $validity_in_days, $csr);

        $domainVerifyerService = $this->createStub(IDomainVerifyer::class);
        $domainVerifyerService->method('verify')
             ->willReturn(true);

        $certificateManager = $this->createStub(ICertificateManager::class);
        $certificateManager->method('createCertificate')
             ->willReturn(new Certificate($domain, '', ''));

        $certificateService = new CertificateService($domainVerifyerService, $certificateManager);

        $certificate = $certificateService->createCertificate($request);

        $this->assertEquals($domain, $certificate->getDomain());
    }
}