<?php

namespace Tests\Feature;

use App\Enums\EmployeeStatus;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('products.index'))->assertRedirect(route('login'));
        $this->get(route('orders.index'))->assertRedirect(route('login'));
    }

    public function test_login_page_is_displayed(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Đăng nhập');
    }

    public function test_active_employee_can_login(): void
    {
        $employee = Employee::factory()->create(['username' => 'nhanvien01', 'password' => 'secret123']);

        $this->post(route('login'), ['username' => 'nhanvien01', 'password' => 'secret123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($employee);
    }

    public function test_password_is_stored_as_hash(): void
    {
        $employee = Employee::factory()->create(['password' => 'secret123']);

        $this->assertNotSame('secret123', $employee->getRawOriginal('password'));
    }

    public function test_wrong_password_does_not_log_in(): void
    {
        Employee::factory()->create(['username' => 'nhanvien01', 'password' => 'secret123']);

        $this->from(route('login'))
            ->post(route('login'), ['username' => 'nhanvien01', 'password' => 'wrong'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_unknown_username_does_not_log_in(): void
    {
        $this->post(route('login'), ['username' => 'khongtontai', 'password' => 'secret123'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_inactive_account_cannot_log_in(): void
    {
        Employee::factory()->inactive()->create(['username' => 'danghi', 'password' => 'secret123']);

        $this->post(route('login'), ['username' => 'danghi', 'password' => 'secret123'])
            ->assertSessionHasErrors(['username' => 'Tài khoản đã bị khóa. Vui lòng liên hệ quản lý.']);

        $this->assertGuest();
    }

    public function test_account_locked_while_logged_in_is_logged_out(): void
    {
        $employee = Employee::factory()->create();
        $this->actingAs($employee);

        $employee->update(['status' => EmployeeStatus::Inactive]);

        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_login_requires_username_and_password(): void
    {
        $this->post(route('login'), [])->assertSessionHasErrors(['username', 'password']);
    }

    public function test_employee_can_logout(): void
    {
        $this->actingAs(Employee::factory()->create())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
