<?php

namespace App\Tests\Functional\User;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class RegisterTest extends GarOloupApiTestCase
{
    public function test_user_cant_register_with_invalid_email(): void
    {
        When::user()->register('__invalid__', 'Sliipman', 'azertyuiAZ123#');
        $this->assertResponseStatusCodeSame(422);
    }

    public function test_user_cant_register_without_twelve_characters_password(): void
    {
        When::user()->register('sliipman@garoloup.fr', 'Sliipman', 'azertAZ123#');
        $this->assertResponseStatusCodeSame(422);
    }

    public function test_user_cant_register_without_one_lowercase_character_password(): void
    {
        When::user()->register('sliipman@garoloup.fr', 'Sliipman', 'AZERTYUIAZ123#');
        $this->assertResponseStatusCodeSame(422);
    }

    public function test_user_cant_register_without_one_uppercase_character_password(): void
    {
        When::user()->register('sliipman@garoloup.fr', 'Sliipman', 'azertyuiaz123#');
        $this->assertResponseStatusCodeSame(422);
    }

    public function test_user_cant_register_without_one_number_character_password(): void
    {
        When::user()->register('sliipman@garoloup.fr', 'Sliipman', 'azertyuiAZ####');
        $this->assertResponseStatusCodeSame(422);
    }

    public function test_user_cant_register_without_one_special_character_password(): void
    {
        When::user()->register('sliipman@garoloup.fr', 'Sliipman', 'azertyuiAZ1234');
        $this->assertResponseStatusCodeSame(422);
    }

    public function test_user_cant_register_with_existing_email(): void
    {
        ThereIs::anUser()->withEmail('sliipman@garoloup.fr')->build();

        When::user()->register('sliipman@garoloup.fr', 'Sliipman', 'azertyuiAZ123#');
        $this->assertResponseStatusCodeSame(409);
    }

    public function test_user_can_register_with_valid_credentials(): void
    {
        $response = When::user()->register('sliipman@garoloup.fr', 'Sliipman', 'azertyuiAZ123#');
        $this->assertResponseStatusCodeSame(201);

        $this->assertTrue($response->get('[success]'));
    }
}
