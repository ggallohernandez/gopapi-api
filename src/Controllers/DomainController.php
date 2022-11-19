<?php declare(strict_types=1);

namespace App\Controllers;

use App\Services\IDomainVerifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DomainController
{
    protected IDomainVerifier $domainVerifier;

    public function __construct(IDomainVerifier $domainVerifier)
    {
        $this->domainVerifier = $domainVerifier;
    }

    public function verifyDomain(Request $request): Response
    {
        $verified = $this->domainVerifier->verify($request->get('domain'));

        return new Response($verified ? 'Verified' : 'Not verified', $verified ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}