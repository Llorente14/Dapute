<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NotFoundPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('role')->default('customer');
        });

        DB::table('users')->insert([
            ['id' => 'customer-123', 'role' => 'customer'],
        ]);
    }

    public function test_unknown_route_renders_branded_not_found_page(): void
    {
        $this->get('/missing-dapute-shelf')
            ->assertNotFound()
            ->assertSee('Page Not Found')
            ->assertSee('Back To Home')
            ->assertSee('href="/"', false)
            ->assertDontSee('Stack trace')
            ->assertDontSee('vendor\\laravel');
    }

    public function test_about_route_stays_not_found(): void
    {
        $this->get('/about')
            ->assertNotFound()
            ->assertSee('Page Not Found');
    }

    public function test_customer_admin_access_renders_not_found_page(): void
    {
        $this->actingAs($this->authUser('customer-123'))
            ->get('/admin/users')
            ->assertNotFound()
            ->assertSee('Page Not Found')
            ->assertSee('Back To Home');
    }

    private function authUser(string $id): User
    {
        $user = new User();
        $user->id = $id;
        $user->role = DB::table('users')->where('id', $id)->value('role');
        $user->exists = true;

        return $user;
    }
}
