<?php declare(strict_types=1);

namespace App\Services;

use App\DTOs\Certificate;
use App\DTOs\CertificateSigningRequest;
use App\DTOs\CreateCertificateRequest;
use App\DTOs\CreateCertificateSigningRequest;

class StepCaCertificateManager implements ICertificateManager
{
    protected IDomainVerifier $domainVerifier;

    public function __construct(IDomainVerifier $domainVerifier)
    {
        $this->domainVerifier = $domainVerifier;
    }

    public function createCertificate(CreateCertificateRequest $request): Certificate
    {
        $commonName = filter_var($request->commonName, FILTER_VALIDATE_DOMAIN);
        $keySize = filter_var($request->keySize, FILTER_VALIDATE_INT);
        $keyType = filter_var($request->keyType, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(RSA|EC|OKP)$/']]);

        $token = $this->getToken($commonName);

        // write to a temporary file
        $inputFile = tempnam(sys_get_temp_dir(), 'cert_input_');
        $outputFile = tempnam(sys_get_temp_dir(), 'cert_');
        $keyFile = tempnam(sys_get_temp_dir(), 'cert_pkey_');

        if (!empty($request->csr)) {
            file_put_contents($inputFile, $request->csr);

            $cmd = "step ca sign {$inputFile} {$outputFile} --force --token {$token}";
        } else {
            $cmd = "step ca certificate {$commonName} {$outputFile} {$keyFile} --kty {$keyType} --size {$keySize} --provisioner mariano@smallstep.com --force --token {$token}";
        }

        $process = proc_open($cmd, [["pipe", "r"], ["pipe", "w"], ["file", "/tmp/create_certificate.log", "a"]], $pipes);

        $output = "";

        if (is_resource($process)) {
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $return_value = proc_close($process);

            if ($return_value === 0) {
                $cert = new Certificate();
                $cert->commonName = $commonName;
                $cert->content = file_get_contents($outputFile);
                $cert->privateKey = file_get_contents($keyFile);

                return $cert;
            }
        }

        throw new \Exception("Error creating CSR: {$output}");
    }

    public function createCertificateSigningRequest(CreateCertificateSigningRequest $request): CertificateSigningRequest
    {
        $commonName = filter_var($request->commonName, FILTER_VALIDATE_DOMAIN);
        $keySize = filter_var($request->keySize, FILTER_VALIDATE_INT);
        $keyType = filter_var($request->keyType, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(RSA|EC|OKP)$/']]);

        // write to a temporary file
        $inputFile = tempnam(sys_get_temp_dir(), 'csr_input_');
        $outputFile = tempnam(sys_get_temp_dir(), 'csr_');
        $keyFile = tempnam(sys_get_temp_dir(), 'csr_pkey_');

        $json = json_encode($request);

        file_put_contents($inputFile, $json);

        $cmd = "step certificate create --csr --force --kty {$keyType} --size {$keySize} --template {$inputFile} {$commonName} {$outputFile} {$keyFile}";

        $process = proc_open($cmd, [["pipe", "r"], ["pipe", "w"], ["file", "/tmp/create_csr.log", "a"]], $pipes);

        $output = "";

        if (is_resource($process)) {
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $return_value = proc_close($process);

            if ($return_value === 0) {
                $csr = new CertificateSigningRequest();
                $csr->commonName = $commonName;
                $csr->organization = $request->organization;
                $csr->organizationalUnit = $request->organizationalUnit;
                $csr->city = $request->city;
                $csr->state = $request->state;
                $csr->country = $request->country;
                $csr->keySize = $keySize;
                $csr->keyType = $keyType;

                $csr->content = file_get_contents($outputFile);
                $csr->privateKey = file_get_contents($keyFile);

                return $csr;
            }
        }

        throw new \Exception("Error creating CSR: {$output}");
    }

    protected function getToken($commonName): string
    {
        $cmd = "step ca token {$commonName}";

        $process = proc_open($cmd, [["pipe", "r"], ["pipe", "w"], ["file", "/tmp/create_csr.log", "a"]], $pipes);

        $output = "";

        if (is_resource($process)) {
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $return_value = proc_close($process);

            if ($return_value === 0) {
                return $output;
            }
        }

        throw new \Exception("Error requesting CA Token: {$output}");
    }
}