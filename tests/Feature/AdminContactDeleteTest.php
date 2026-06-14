<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 管理者は特定の問い合わせを削除できる()
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create();

        $this->assertDatabaseHas('contacts', ['id' => $contact->id]);

        $response = $this->actingAs($user)
            ->delete('/admin/contacts/'.$contact->id);

        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
