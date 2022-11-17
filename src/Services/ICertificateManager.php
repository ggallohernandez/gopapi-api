<?php

namespace App\Services;

use App\DTOs\Certificate;
use App\DTOs\CreateCertificateRequest;

interface ICertificateManager
{
    public function createCertificate(CreateCertificateRequest $request): Certificate;
}