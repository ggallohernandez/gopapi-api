<?php declare(strict_types=1);

namespace App\Services;

use App\Services\IDnsRecordFetcher;

class TxtDnsRecordFetcher implements IDnsRecordFetcher
{
    public function getTxtRecords(string $domain): array
    {
        $sanitized_domain = filter_var($domain, FILTER_VALIDATE_DOMAIN);
        $txt_records = dns_get_record($sanitized_domain, DNS_TXT);

        /* Older implementation: In real life I'd delete this hole chunk of code. 
           In this case I'll leave it just to show up the thinking process.
           
        $regex = 'dig +short @8.8.8.8 '.$domain.' txt | sed -n "s/^\"'.self::TXT_RECORD_PREFIX.'=\(.*\)\"$/\1/p"';
        $process = proc_open($regex, [["pipe", "r"], ["pipe", "w"], ["file", "/tmp/dns_record_verifier.log", "a"]], $pipes);

        if (is_resource($process)) {
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $txt_records = explode(PHP_EOL, $output);   
            $return_value = proc_close($process);

            if ($return_value === 0) {
                return $txt_records;
            }
        }*/

        if (!$txt_records) {
            return [];
        }

        return $txt_records;
    }
}