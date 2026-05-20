<?php

namespace Tests\Unit;

use App\Helpers\AddressManager;
use Tests\TestCase;

class AddressManagerTest extends TestCase
{
    public function test_address_requires_valid_phone_and_postal_code(): void
    {
        $result = AddressManager::validateAddress([
            'label' => 'Home',
            'recipient_name' => 'Jane Doe',
            'recipient_phone' => '81234567890',
            'address' => 'Jl. Mawar No. 10',
            'city' => 'Jakarta',
            'postal_code' => '12345',
        ]);

        $this->assertTrue($result['valid']);
        $this->assertSame([], $result['errors']);
    }

    public function test_phone_number_is_normalized_to_plus_62_format(): void
    {
        $this->assertSame('+6281234567890', AddressManager::normalizePhoneNumber('081234567890'));
        $this->assertSame('+6281234567890', AddressManager::normalizePhoneNumber('6281234567890'));
        $this->assertSame('81234567890', AddressManager::localPhoneNumber('+6281234567890'));
    }

    public function test_address_rejects_empty_text_fields_letters_in_phone_and_bad_postal_code(): void
    {
        $result = AddressManager::validateAddress([
            'label' => 'Home',
            'recipient_name' => '   ',
            'recipient_phone' => 'phone-08123',
            'address' => '',
            'city' => ' ',
            'postal_code' => '1234A',
        ]);

        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('recipient_name', $result['errors']);
        $this->assertArrayHasKey('recipient_phone', $result['errors']);
        $this->assertArrayHasKey('address', $result['errors']);
        $this->assertArrayHasKey('city', $result['errors']);
        $this->assertArrayHasKey('postal_code', $result['errors']);
    }
}
