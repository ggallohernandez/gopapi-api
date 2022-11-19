<?php declare(strict_types=1);

namespace App\Services;


class TxtDnsRecordVerifier implements IDomainVerifier
{
    public const TXT_RECORD_PREFIX = 'gopapi-site-verification';

    protected IDnsRecordFetcher $dnsRecordFetcher;

    public function __construct(IDnsRecordFetcher $dnsRecordFetcher)
    {
        $this->dnsRecordFetcher = $dnsRecordFetcher;
    }

    public function verify(string $domain): bool
    {
        $txt_records = $this->dnsRecordFetcher->getTxtRecords($domain);

        if (empty($txt_records)) {
            return false;
        }

        foreach ($txt_records as $txt_record) {
            // This is a basic check to see if the TXT record is present
            if (strpos($txt_record, self::TXT_RECORD_PREFIX) !== false) {
                return true;
            }
        }

        return false;
    }
}
