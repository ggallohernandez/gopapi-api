<?php declare(strict_types=1);

namespace App\DTOs;

use JsonSerializable;

// Represents a Certificate Signing Request
class CreateCertificateSigningRequest extends CertificateSigningRequest implements JsonSerializable
{
    public function jsonSerialize()
    {
        $json =  ['subject' => ['commonName' => $this->commonName]];

        if ($this->organization !== '')
            $json['subject']['organization'] = $this->organization;
        
        if ($this->organizationalUnit !== '')
            $json['subject']['organizationalUnit'] = $this->organizationalUnit;
        
        if ($this->city !== '')
            $json['subject']['locality'] = $this->city;
        
        if ($this->state !== '')
            $json['subject']['province'] = $this->state;
        
        if ($this->country !== '')
            $json['subject']['country'] = $this->country;
        
        return $json;
    }
}