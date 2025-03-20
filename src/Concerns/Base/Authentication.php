<?php

namespace Hanafalah\ModuleIcd\Concerns\Base;

use Illuminate\Support\Facades\Http;

trait Authentication
{
    use HasEndPoint, HasRequest;
    protected string $__token_end_point = "https://icdaccessmanagement.who.int/connect/token";
    protected string $__scope = "icdapi_access";
    protected string $__grant_type = 'client_credentials';
    protected string $__client_id;
    protected string $__client_secret;
    protected string $__token;
    protected object $__auth;

    public function oauth(): object
    {
        $this->setAuth($this->post($this->__token_end_point, [
            'grant_type'    => $this->__grant_type,
            'client_id'     => $this->__client_id,
            'client_secret' => $this->__client_secret,
            'scope'         => $this->__scope,
        ]));
        return $this->__auth;
    }

    private function setAuth(mixed $result): self
    {
        if (!is_object($result)) $result = (object) $result;
        $this->__auth  = $result;
        $this->setToken($this->__auth->access_token);
        $this->setHeader();
        return $this;
    }

    public function getToken(): string
    {
        return $this->__token;
    }

    protected function setToken($token): self
    {
        $this->__token = $token;
        return $this;
    }

    public function getAuthorization()
    {
        $token = $this->getToken();
        switch ($this->__auth->token_type) {
            case 'Bearer':
                return 'Bearer ' . $token;
                break;
        }
    }

    protected function setAuthorization(): self
    {
        $authentication        = $this->getConfigAuthentication();
        if (isset($authentication['client_id']) && !isset($authentication['client_secret'])) {
            $this->__client_id     = $authentication['client_id'];
            $this->__client_secret = $authentication['client_secret'];
        }
        return $this;
    }
}
