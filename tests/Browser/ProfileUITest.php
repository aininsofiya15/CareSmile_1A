<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProfileUITest extends DuskTestCase
{
    /** @test for TC-06 */
    public function user_sees_error_when_passwords_do_not_match()
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $this->browse(function (Browser $browser) use ($patient) {
            $browser->loginAs($patient)
                    ->visit('/patient/profile')
                    
                    // 1. Type in the passwords
                    ->type('current_password', 'OldPassword123!')
                    ->type('password', 'NewPassword123!')
                    ->type('password_confirmation', 'TypoPassword123!')
                    
                    // 2. Click the button
                    ->press('Update Password')
                    
                    // 3. Assert the exact error message appears on screen
                    ->assertSee('The password field confirmation does not match.');
        });
    }
}