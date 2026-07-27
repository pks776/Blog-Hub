<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // Remove existing RBAC data
        $auth->removeAll();

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';

        $moderator = $auth->createRole('moderator');
        $moderator->description = 'Moderator';

        $blogger = $auth->createRole('blogger');
        $blogger->description = 'Blogger';

        $auth->add($admin);
        $auth->add($moderator);
        $auth->add($blogger);

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [
            'createPost',
            'updateOwnPost',
            'deleteOwnPost',

            'updateAnyPost',
            'deleteAnyPost',

            'publishPost',

            'manageUsers',
            'manageCategories',
            'manageComments',
        ];

        foreach ($permissions as $name) {

            $permission = $auth->createPermission($name);
            $permission->description = ucwords(str_replace('Post', ' Post', $name));

            $auth->add($permission);
        }

        /*
        |--------------------------------------------------------------------------
        | Blogger Permissions
        |--------------------------------------------------------------------------
        */

        $auth->addChild($blogger, $auth->getPermission('createPost'));
        $auth->addChild($blogger, $auth->getPermission('updateOwnPost'));
        $auth->addChild($blogger, $auth->getPermission('deleteOwnPost'));

        /*
        |--------------------------------------------------------------------------
        | Moderator Permissions
        |--------------------------------------------------------------------------
        */

        $auth->addChild($moderator, $auth->getPermission('publishPost'));
        $auth->addChild($moderator, $auth->getPermission('updateAnyPost'));
        $auth->addChild($moderator, $auth->getPermission('deleteAnyPost'));
        $auth->addChild($moderator, $auth->getPermission('manageComments'));

        /*
        |--------------------------------------------------------------------------
        | Admin Permissions
        |--------------------------------------------------------------------------
        */

        // Admin inherits Moderator + Blogger permissions
        $auth->addChild($admin, $moderator);
        $auth->addChild($admin, $blogger);

        $auth->addChild($admin, $auth->getPermission('manageUsers'));
        $auth->addChild($admin, $auth->getPermission('manageCategories'));

        /*
        |--------------------------------------------------------------------------
        | Assign Roles
        |--------------------------------------------------------------------------
        */

        // User ID 1 -> Admin
        $auth->assign($admin, 1);

        // User ID 2 -> Moderator
        $auth->assign($moderator, 2);

        // User ID 3 -> Blogger
        $auth->assign($blogger, 3);

        // User ID 4 -> Blogger
        $auth->assign($blogger, 4);

        echo "=====================================\n";
        echo "RBAC initialized successfully.\n";
        echo "=====================================\n";
        echo "Admin      -> User ID 1\n";
        echo "Moderator  -> User ID 2\n";
        echo "Blogger    -> User ID 3\n";
        echo "Blogger    -> User ID 4\n";
        echo "=====================================\n";
    }
}