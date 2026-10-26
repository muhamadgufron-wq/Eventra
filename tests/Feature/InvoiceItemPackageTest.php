<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceItemPackageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->owner()->create());
    }

    public function test_invoice_item_keeps_package_on_create(): void
    {
        $package = $this->createPackage();
        $client = $this->createClient($package);

        $response = $this->postJson('/api/invoices', $this->invoicePayload($client, $package))
            ->assertCreated();

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $response->json('data.id'),
            'package_id' => $package->id,
        ]);
    }

    public function test_invoice_item_keeps_package_on_update(): void
    {
        $package = $this->createPackage();
        $client = $this->createClient($package);

        $invoiceId = $this->postJson('/api/invoices', $this->invoicePayload($client, $package))
            ->assertCreated()
            ->json('data.id');

        $this->putJson("/api/invoices/{$invoiceId}", $this->invoicePayload($client, $package))
            ->assertOk();

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoiceId,
            'package_id' => $package->id,
        ]);
    }

    private function createPackage(): Package
    {
        return Package::create([
            'name' => 'Paket Luxury',
            'price' => 90000000,
            'is_active' => true,
        ]);
    }

    private function createClient(Package $package): Client
    {
        return Client::create([
            'bride_name' => 'Test Bride',
            'groom_name' => 'Test Groom',
            'package_id' => $package->id,
        ]);
    }

    private function invoicePayload(Client $client, Package $package): array
    {
        return [
            'client_id' => $client->id,
            'invoice_date' => now()->toDateString(),
            'items' => [
                [
                    'package_id' => $package->id,
                    'description' => $package->name,
                    'quantity' => 1,
                    'unit_price' => 90000000,
                ],
            ],
        ];
    }
}
