<?php declare(strict_types=1);

namespace App\Services;

use App\DTOs\Certificate;
use App\DTOs\CertificateSigningRequest;
use App\DTOs\CreateCertificateRequest;
use App\DTOs\CreateCertificateSigningRequest;

interface ICertificateManager
{
    public function createCertificate(CreateCertificateRequest $request): Certificate;
    public function createCertificateSigningRequest(CreateCertificateSigningRequest $request): CertificateSigningRequest;
}