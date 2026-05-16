<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientManagementTest extends TestCase
{
    use RefreshDatabase; 

    public function test_tc01_admin_can_view_specific_patient_details()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        
        $patient = User::factory()->create([
            'role' => 'patient',
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com'
        ]);

        // Triggers AdminController@show mapped underneath your admin prefix
        $response = $this->actingAs($admin)
                         ->get("/admin/patients/{$patient->id}");

        $response->assertStatus(200); 
        $response->assertSee('Siti Aminah'); 
        $response->assertSee('siti@example.com'); 
    }

    public function test_tc02_admin_can_edit_patient_details()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        
        $patient = User::factory()->create([
            'role' => 'patient', 
            'name' => 'Old Name',
            'email' => 'old@example.com'
        ]);

        // Triggers AdminController@update matching your parameter definitions
        $response = $this->actingAs($admin)
                         ->put("/admin/patients/{$patient->id}", [
                             'name' => 'Ainin Sofiya',
                             'email' => 'updated@example.com',
                         ]);

        $response->assertRedirect();
        
        // Match the exact session flash string defined on line 113 of your AdminController!
        $response->assertSessionHas('success', 'Patient record updated in database.'); 

        // Reloads page context cleanly to check data visibility loop
        $this->get("/admin/patients/{$patient->id}")
             ->assertSee('Ainin Sofiya');
    }

    public function test_tc03_admin_can_search_patient_by_exact_name()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create a target patient we want to find
        $targetPatient = User::factory()->create([
            'role' => 'patient',
            'name' => 'Siti Aminah'
        ]);

        // Create a different patient who should NOT show up in an exact match look-up
        $otherPatient = User::factory()->create([
            'role' => 'patient',
            'name' => 'John Doe'
        ]);

        // Act: Hit the management listing page with the search query term
        $response = $this->actingAs($admin)
                         ->get("/admin/patients?search=Siti+Aminah");

        $response->assertStatus(200);
        
        // Assert: The system must display our searched patient but omit the other one
        $response->assertSee('Siti Aminah');
        $response->assertDontSee('John Doe');
    }
}