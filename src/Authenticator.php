<?php

namespace Peldax\NetteInit;

use Nette\Security as NS;

final class Authenticator extends \Peldax\NetteInit\Model\UserModel implements NS\IAuthenticator
{
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
        $row = $this->getTable()
            ->where('username', $username)
            ->where('active', 1)
            ->fetch();

        if (!$row)
        {
            throw new NS\AuthenticationException('User not found.');
        }

        if (!NS\Passwords::verify($password, $row->password))
        {
            throw new NS\AuthenticationException('Invalid password.');
        }

        $data = $row->toArray();
        unset($data['password']);
        return new \Nette\Security\Identity($row->id, $row->role, $data);
    }
}
