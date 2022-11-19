<?php declare(strict_types=1);

namespace App\Tests;

use App\DTOs\Certificate;
use PHPUnit\Framework\TestCase;

use App\DTOs\CreateCertificateRequest;
use App\Services\CertificateService;
use App\Services\ICertificateManager;
use App\Services\IDnsRecordFetcher;
use App\Services\IDomainVerifier;
use App\Services\TxtDnsRecordFetcher;
use App\Services\TxtDnsRecordVerifier;

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

        $dnsRecordFetcher = $this->createStub(IDnsRecordFetcher::class);
        $dnsRecordFetcher->method('getTxtRecords')
             ->willReturn([TxtDnsRecordVerifier::TXT_RECORD_PREFIX.'=test']);

        $domainVerifierService = new TxtDnsRecordVerifier($dnsRecordFetcher);

        $certificateManager = $this->createStub(ICertificateManager::class);
        $certificateManager->method('createCertificate')
             ->willReturn(new Certificate($domain, '', ''));

        $certificateService = new CertificateService($domainVerifierService, $certificateManager);

        $certificate = $certificateService->createCertificate($request);

        $this->assertEquals($domain, $certificate->getDomain());
    }
}