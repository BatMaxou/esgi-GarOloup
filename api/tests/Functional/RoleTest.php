<?php

namespace App\Tests\Functional;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class RoleTest extends GarOloupApiTestCase
{
    public function test_everyone_can_access_role_list(): void
    {
        When::role()->list();
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_everyone_can_access_role(): void
    {
        $roleBuilder = ThereIs::aRole()->villager()->build();

        When::role()->get($roleBuilder->getEntity()->getId());
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_admin_can_update_role(): void
    {
        $roleBuilder = ThereIs::aRole()->villager()->build();
        $adminBuilder = ThereIs::anAdmin()->build();

        $response = When::asUser($adminBuilder)->role()->update($roleBuilder->getEntity()->getId(), [
            'name' => '***g ***',
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($response->get('[name]'), '***g ***');
    }

    public function test_admin_can_update_role_files(): void
    {
        $pictureBuilder = ThereIs::anUploadFile()
            ->withPath($this->getMockedAssetPath('pastel-blue-square.png'))
            ->withName('pastel-blue-square.png')->build()
        ;
        $roleBuilder = ThereIs::aRole()->villager()->withPicture(null)->build();
        $adminBuilder = ThereIs::anAdmin()->build();

        $response = When::asUser($adminBuilder)->role()->updateFiles($roleBuilder->getEntity()->getId(), [
            'picture' => $pictureBuilder->getEntity(),
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertNotNull($response->get('[picture]'));
    }

    public function test_user_cant_update_role(): void
    {
        $roleBuilder = ThereIs::aRole()->villager()->build();
        $userBuilder = ThereIs::anUser()->build();

        $response = When::asUser($userBuilder)->role()->update($roleBuilder->getEntity()->getId(), [
            'name' => '***g ***',
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_user_cant_update_role_files(): void
    {
        $pictureBuilder = ThereIs::anUploadFile()
            ->withPath($this->getMockedAssetPath('pastel-blue-square.png'))
            ->withName('pastel-blue-square.png')->build()
        ;
        $roleBuilder = ThereIs::aRole()->villager()->withPicture(null)->build();
        $userBuilder = ThereIs::anUser()->build();

        $response = When::asUser($userBuilder)->role()->updateFiles($roleBuilder->getEntity()->getId(), [
            'picture' => $pictureBuilder->getEntity(),
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_temp_user_cant_update_role(): void
    {
        $roleBuilder = ThereIs::aRole()->villager()->build();
        $tempUserBuilder = ThereIs::aTempUser()->build();

        $response = When::asTempUser($tempUserBuilder)->role()->update($roleBuilder->getEntity()->getId(), [
            'name' => '***g ***',
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_temp_user_cant_update_role_files(): void
    {
        $pictureBuilder = ThereIs::anUploadFile()
            ->withPath($this->getMockedAssetPath('pastel-blue-square.png'))
            ->withName('pastel-blue-square.png')->build()
        ;
        $roleBuilder = ThereIs::aRole()->villager()->withPicture(null)->build();
        $tempUserBuilder = ThereIs::aTempUser()->build();

        $response = When::asTempUser($tempUserBuilder)->role()->updateFiles($roleBuilder->getEntity()->getId(), [
            'picture' => $pictureBuilder->getEntity(),
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_anonymous_cant_update_role(): void
    {
        $roleBuilder = ThereIs::aRole()->villager()->build();
        $adminBuilder = ThereIs::anAdmin()->build();

        $response = When::role()->update($roleBuilder->getEntity()->getId(), [
            'name' => '***g ***',
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_anonymous_cant_update_role_files(): void
    {
        $pictureBuilder = ThereIs::anUploadFile()
            ->withPath($this->getMockedAssetPath('pastel-blue-square.png'))
            ->withName('pastel-blue-square.png')->build()
        ;
        $roleBuilder = ThereIs::aRole()->villager()->withPicture(null)->build();
        $adminBuilder = ThereIs::anAdmin()->build();

        $response = When::role()->updateFiles($roleBuilder->getEntity()->getId(), [
            'picture' => $pictureBuilder->getEntity(),
        ]);
        $this->assertResponseStatusCodeSame(401);
    }
}
