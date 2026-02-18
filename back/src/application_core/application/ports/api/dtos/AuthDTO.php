<?php
namespace CurioMap\src\application_core\application\ports\api\dtos;

class AuthDTO {
    public string $accessToken;
    public string $refreshToken;

    public function __construct(string $accessToken, string $refreshToken) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }
}
