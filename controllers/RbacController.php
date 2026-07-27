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

        // ==========================
        // Roles
        // ==========================
        $admin = $auth->createRole('admin');
        $moderator = $auth->createRole('moderator');
        $blogger = $auth->createRole('blogger');

        $auth->add($admin);
        $auth->add($moderator);
        $auth->add($blogger);

        // ==========================
        // Permissions
        // ==========================

        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create Posts';
        $auth->add($createPost);

        $updateOwnPost = $auth->createPermission('updateOwnPost');
        $updateOwnPost->description = 'Update Own Posts';
        $auth->add($updateOwnPost);

        $deleteOwnPost = $auth->createPermission('deleteOwnPost');
        $deleteOwnPost->description = 'Delete Own Posts';
        $auth->add($deleteOwnPost);

        $updateAnyPost = $auth->createPermission('updateAnyPost');
        $updateAnyPost->description = 'Update Any Post';
        $auth->add($updateAnyPost);

        $deleteAnyPost = $auth->createPermission('deleteAnyPost');
        $deleteAnyPost->description = 'Delete Any Post';
        $auth->add($deleteAnyPost);

        $approvePost = $auth->createPermission('approvePost');
        $approvePost->description = 'Approve Blog Posts';
        $auth->add($approvePost);

        $rejectPost = $auth->createPermission('rejectPost');
        $rejectPost->description = 'Reject Blog Posts';
        $auth->add($rejectPost);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Manage Users';
        $auth->add($manageUsers);

        // ==========================
        // Blogger Permissions
        // ==========================
        $auth->addChild($blogger, $createPost);
        $auth->addChild($blogger, $updateOwnPost);
        $auth->addChild($blogger, $deleteOwnPost);

        // ==========================
        // Moderator Permissions
        // ==========================
        $auth->addChild($moderator, $approvePost);
        $auth->addChild($moderator, $rejectPost);
        $auth->addChild($moderator, $updateAnyPost);
        $auth->addChild($moderator, $deleteAnyPost);

        // ==========================
        // Admin Permissions
        // ==========================
        $auth->addChild($admin, $manageUsers);

        // Admin inherits everything
        $auth->addChild($admin, $moderator);
        $auth->addChild($admin, $blogger);

        // ==========================
        // Assign Roles
        // ==========================
        $auth->assign($admin, 1);
        $auth->assign($moderator, 2);
        $auth->assign($blogger, 3);
        $auth->assign($blogger, 4);

        echo "RBAC initialized successfully.\n";
    }
}