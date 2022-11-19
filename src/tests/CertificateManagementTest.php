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
    public function testCreateANewCertificateSigningRequest()
    {
        $domain = '*.ggallohernandez.com';
        $validity_in_days = 365;
        $csr = '';
        
        $request = new CreateCertificateSigningRequest();
        $request->commonName = $domain;
        $request->organization = 'ggallohernandez';
        $request->organizationalUnit = 'IT';
        $request->city = 'Montevideo';
        $request->state = 'Montevideo';
        $request->country = 'UY';

        $domainVerifierService = $this->createStub(IDomainVerifier::class);
        $domainVerifierService->method('verify')
             ->willReturn(true);


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

        $domainVerifierService = $this->createStub(IDomainVerifier::class);
        $domainVerifierService->method('verify')
             ->willReturn(true);


        $certificateManager = new StepCaCertificateManager($domainVerifierService);

        $certificateService = new CertificateService($domainVerifierService, $certificateManager);

        $certificate = $certificateService->createCertificate($request);

        $this->assertEquals($request->commonName, $certificate->commonName);
        $this->assertNotEmpty($certificate->content);
    }
}