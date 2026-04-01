<?php
declare(strict_types=1);

namespace App\Services\EveOnline\ESI;

use App\Services\EveOnline\EveOnlineBaseService;

final class RolesService extends EveOnlineBaseService
{
    public function __construct()
    {}

    public function getRoles(string $accessToken): array
    {
        $response = $this->client($accessToken);
    }
}

?>
