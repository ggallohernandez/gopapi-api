<?php declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Services\IDnsRecordFetcher;
use App\Services\TxtDnsRecordVerifier;

class DomainVerificationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testVerifyDomain()
    {
        $domain = 'test.com';
        $txt_records = [TxtDnsRecordVerifier::TXT_RECORD_PREFIX.'=test'];

        $dnsRecordFetcher = $this->createStub(IDnsRecordFetcher::class);
        $dnsRecordFetcher->method('getTxtRecords')
             ->willReturn($txt_records);

        $dnsRecordVerifier = new TxtDnsRecordVerifier($dnsRecordFetcher);

        $this->assertTrue($dnsRecordVerifier->verify($domain));
    }
}