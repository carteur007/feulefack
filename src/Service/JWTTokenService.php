<?php
namespace App\Service;


class JWTTokenService
{

    //On genere le token
    
    /**
     * Generation du JWTToken
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param int $validity
     * @return string $jwtToken
     */
    public function generateToken(array $header,array $payload,string $secret, int $validity =10800): string
    {
        if($validity > 0 ){  
            $dateNow = new \DateTimeImmutable();
            $expiration = $dateNow->getTimestamp() + $validity; 

            //Initialisation du payload
            $payload['iat'] = $dateNow->getTimestamp();
            $payload['expiration'] = $expiration;
        }

        //On encode en base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        //On nettoie les valeurs encodees (retrait des +, / et =)
        $base64Header = str_replace(['+', '/', '='],['-', '_', ''],$base64Header);
        $base64Payload = str_replace(['+', '/', '='],['-', '_', ''],$base64Payload);  

        //On genere la signature
        $secret = base64_decode($secret);

        $signature = hash_hmac('sha256',$base64Header.'.'.$base64Payload,$secret,true);
        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+', '/', '='],['-', '_', ''],$base64Signature);  

        //On genere le jwtoken
        $jwtoken = $base64Header.'.'.$base64Payload.'.'.$base64Signature;

        return $jwtoken;
    }
    
    // On valide le jwtToken

    public function isValid($jwtToken):bool
    {
        return ( preg_match(
                '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $jwtToken
        ) === 1);
    }

    // Onrecupere le header

    public function getHeader(string $jwtToken): array
    {
        // On decoupe le token
        $tab = explode('.',$jwtToken);
        // On decode le payload
        $header = json_decode(base64_decode($tab[0]), true);

        return $header;
    }
    // Onrecupere le payload

    public function getPayload(string $jwtToken): array
    {
        // On decoupe le token
        $tab = explode('.',$jwtToken);
        // On decode le payload
        $payload = json_decode(base64_decode($tab[1]), true);

        return $payload;
    }

    // On verifie si le token a expire

    public function isExpired(string $jwtToken): bool
    {
        // On recupere la date d'aujourd'huit
        $now = new \DateTimeImmutable();
        // On recupere le payload
        $payload = $this->getPayload($jwtToken);

        return $payload['expiration'] < $now->getTimesTamp();
    
    }

    // On verifie la signature du token

    public function checToken(string $jwtToken, string $secret): bool
    {
        // On recupere le header et le payload
        $header = $this->getHeader($jwtToken);
        $payload = $this->getPayload($jwtToken);
        // On regenere le token
        $jwtTokenGen = $this->generateToken($header,$payload,$secret,0);

        return $jwtToken === $jwtTokenGen;

    }

}





















