<?php

namespace App\Tests\Functional\User;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class ResetPasswordTest extends GarOloupApiTestCase
{
    public function test_user_cant_reset_password_with_invalid_token(): void
    {
        ThereIs::anUser()->withResetToken('__reset__')->withEmail('sliipman@garoloup.fr')->withPassword('azertyuiAZ123#')->build();

        When::user()->resetPassword('__invalid__', 'azertyuiAZ123###');
        $this->assertResponseStatusCodeSame(200);

        When::auth()->login('sliipman@garoloup.fr', 'azertyuiAZ123###');
        $this->assertResponseStatusCodeSame(401);

        When::auth()->login('sliipman@garoloup.fr', 'azertyuiAZ123#');
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_user_cant_reset_password_without_twelve_characters_password(): void
    {
        ThereIs::anUser()->withResetToken('__reset__')->withEmail('sliipman@garoloup.fr')->withPassword('azertyuiAZ123#')->build();

        When::user()->resetPassword('__reset__', 'azertAZ123#');
        $this->assertResponseStatusCodeSame(422);

        When::auth()->login('sliipman@garoloup.fr', 'azertyuiAZ123#');
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_user_cant_reset_password_without_one_lowercase_character_password(): void
    {
        ThereIs::anUser()->withResetToken('__reset__')->withEmail('sliipman@garoloup.fr')->withPassword('azertyuiAZ123#')->build();

        When::user()->resetPassword('__reset__', 'AZERTYUIAZ123#');
        $this->assertResponseStatusCodeSame(422);

        When::auth()->login('sliipman@garoloup.fr', 'azertyuiAZ123#');
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_user_cant_reset_password_without_one_uppercase_character_password(): void
    {
        ThereIs::anUser()->withResetToken('__reset__')->withEmail('sliipman@garoloup.fr')->withPassword('azertyuiAZ123#')->build();

        When::user()->resetPassword('__reset__', 'azertyuiaz123#');
        $this->assertResponseStatusCodeSame(422);

        When::auth()->login('sliipman@garoloup.fr', 'azertyuiAZ123#');
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_user_cant_reset_password_without_one_number_character_password(): void
    {
        ThereIs::anUser()->withResetToken('__reset__')->withEmail('sliipman@garoloup.fr')->withPassword('azertyuiAZ123#')->build();

        When::user()->resetPassword('__reset__', 'azertyuiAZ####');
        $this->assertResponseStatusCodeSame(422);

        When::auth()->login('sliipman@garoloup.fr', 'azertyuiAZ123#');
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_user_cant_reset_password_without_one_special_character_password(): void
    {
        ThereIs::anUser()->withResetToken('__reset__')->withEmail('sliipman@garoloup.fr')->withPassword('azertyuiAZ123#')->build();

        When::user()->resetPassword('__reset__', 'azertyuiAZ1234');
        $this->assertResponseStatusCodeSame(422);

        When::auth()->login('sliipman@garoloup.fr', 'azertyuiAZ123#');
        $this->assertResponseStatusCodeSame(200);
    }
}
