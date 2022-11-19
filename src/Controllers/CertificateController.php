<?php declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\CreateCertificateRequest;
use App\DTOs\CreateCertificateSigningRequest;
use App\Services\CertificateService;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $domain = $request->get('domain');
        $validityInDays = $request->get('validityInDays');
        $csr = $request->get('csr');

        $command = new CreateCertificateRequest($domain, $validityInDays, $csr);

        $certificate = $this->certificateService->createCertificate($command);

        return new JsonResponse(json_encode($certificate), Response::HTTP_CREATED);
    }

    public function createCsr(Request $request)
    {
        $domain = $request->get('domain');
        $keySize = $request->get('keySize', 2048);
        $keyType = $request->get('keyType', 'RSA');

        $command = new CreateCertificateSigningRequest();

        $command->commonName = $domain;
        $command->keySize = $keySize;
        $command->keyType = $keyType;
        $command->organization = $request->get('organization', '');
        $command->organizationalUnit = $request->get('organizationalUnit', '');
        $command->country = $request->get('country', 'US');
        $command->state = $request->get('state', '');
        $command->city = $request->get('city', '');

        $csr = $this->certificateService->createCertificateSigningRequest($command);

        return new JsonResponse(json_encode($csr), Response::HTTP_CREATED);
    }
}