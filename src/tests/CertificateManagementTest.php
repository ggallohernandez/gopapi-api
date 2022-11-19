<?php declare(strict_types=1);

namespace App\Tests;

use App\DTOs\Certificate;
use PHPUnit\Framework\TestCase;

use App\DTOs\CreateCertificateRequest;
use App\DTOs\CreateCertificateSigningRequest;
use App\Services\CertificateService;
use App\Services\ICertificateManager;
use App\Services\IDnsRecordFetcher;
use App\Services\IDomainVerifier;
use App\Services\StepCaCertificateManager;
use App\Services\TxtDnsRecordFetcher;
use App\Services\TxtDnsRecordVerifier;

class CertificateManagementTest extends TestCase
{
    protected CreateCertificateSigningRequest $createCertificateSigningRequest;
    protected IDomainVerifier $domainVerifierService;

    protected function setUp(): void
    {
        $this->createCertificateSigningRequest = new CreateCertificateSigningRequest();
        $this->createCertificateSigningRequest->commonName = '*.ggallohernandez.com';
        $this->createCertificateSigningRequest->organization = 'ggallohernandez';
        $this->createCertificateSigningRequest->organizationalUnit = 'IT';
        $this->createCertificateSigningRequest->city = 'Montevideo';
        $this->createCertificateSigningRequest->state = 'Montevideo';
        $this->createCertificateSigningRequest->country = 'UY';

        $this->domainVerifierService = $this->createStub(IDomainVerifier::class);
        $this->domainVerifierService->method('verify')->willReturn(true);
    }

    public function testCreateANewCertificateSigningRequest()
    {
        $request = $this->createCertificateSigningRequest;
        $domainVerifierService = $this->domainVerifierService;

        $certificateManager = new StepCaCertificateManager($domainVerifierService);

        $certificateService = new CertificateService($domainVerifierService, $certificateManager);

        $csr = $certificateService->createCertificateSigningRequest($request);

        $this->assertEquals($request->commonName, $csr->commonName);
        $this->assertEquals($request->organization, $csr->organization);
        $this->assertEquals($request->organizationalUnit, $csr->organizationalUnit);
        $this->assertEquals($request->city, $csr->city);
        $this->assertEquals($request->state, $csr->state);
        $this->assertEquals($request->country, $csr->country);
        $this->assertNotEmpty($csr->content);
        $this->assertNotEmpty($csr->privateKey);
    }

    public function testCreateANewCertificate()
    {
        $domain = '*.ggallohernandez.com';
        $validity_in_days = 365;
        $csr = '';
        
        $request = new CreateCertificateRequest($domain, $validity_in_days, $csr);

        $domainVerifierService = $this->domainVerifierService;

        $certificateManager = new StepCaCertificateManager($domainVerifierService);

        $certificateService = new CertificateService($domainVerifierService, $certificateManager);

        $certificate = $certificateService->createCertificate($request);

        $this->assertEquals($request->commonName, $certificate->commonName);
        $this->assertNotEmpty($certificate->content);
    }

    public function testCreateANewCertificateFromCSR()
    {
        $csrRequest = $this->createCertificateSigningRequest;
        $domainVerifierService = $this->domainVerifierService;

        $certificateManager = new StepCaCertificateManager($domainVerifierService);

        $certificateService = new CertificateService($domainVerifierService, $certificateManager);

        $csr = $certificateService->createCertificateSigningRequest($csrRequest);

        $validity_in_days = 365;
        
        $request = new CreateCertificateRequest($csr->commonName, $validity_in_days, $csr->content);

        $certificate = $certificateService->createCertificate($request);

        $this->assertEquals($request->commonName, $certificate->commonName);
        $this->assertNotEmpty($certificate->content);
    }
}