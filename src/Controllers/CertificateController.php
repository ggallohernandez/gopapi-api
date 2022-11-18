<?php declare(strict_types=1);

namespace App\Controllers;

use App\Services\CertificateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CertificateController
{
    protected CertificateService $certificateService;

    public function __construct(
        CertificateService $certificateService
    ) {
        $this->certificateService = $certificateService;
    }

    public function create(Request $request)
    {
        return new Response('Hello World!');
    }
}