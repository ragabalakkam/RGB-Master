<?php

namespace App\Helpers\Classes;

use Keensoen\CPanelApi\CPanel as CP;

class CPanel extends CP
{
    public function deleteSubdomain(string $subdomain, string $root_dir)
    {
        $master_url = getConfig('url');
        Dir::remove($root_dir);
        file_put_contents("$root_dir/public/index.php", "<?php header('Location: $master_url'); exit;");
    }

    // override already existing functions

    public function deleteDatabaseUser($username)
    {
        $module = "Mysql";
        $function = "delete_user";
        $parameters = array(
            'name'    => $username
        );
        return $this->call($module, $function, $parameters);
    }
}
