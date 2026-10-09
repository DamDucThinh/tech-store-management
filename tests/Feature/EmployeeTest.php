<?php

namespace Tests\Feature;

use App\Enums\EmployeeRole;
use App\Enums\EmployeeStatus;
use App\Models\Employee;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    private Employee $manager;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->manager = Employee::factory()->manager()->withProfile(['name' => 'Nguyễn Văn Quản'])->create();
        $this->actingAs($this->manager);
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'username' => 'hang.le',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => EmployeeRole::Staff->value,
            'status' => EmployeeStatus::Active->value,
            'name' => 'Lê Minh Hàng',
            'email' => 'hang@example.com',
            'phone' => '0987654321',
            'address' => '8 Nguyễn Trãi, Hà Nội',
        ], $overrides);
    }

    public function test_sales_staff_cannot_access_employee_management(): void
    {
        $this->actingAs(Employee::factory()->create());

        $this->get(route('employees.index'))->assertForbidden();
        $this->post(route('employees.store'), $this->validData())->assertForbidden();
    }

    public function test_manager_can_see_employee_list(): void
    {
        Employee::factory()->withProfile(['name' => 'Trần Thị Bán'])->create();

        $this->get(route('employees.index'))->assertOk()->assertSee('Trần Thị Bán');
    }

    public function test_manager_can_create_account_with_profile_and_avatar(): void
    {
        $this->post(route('employees.store'), $this->validData(['avatar' => UploadedFile::fake()->image('hang.png', 200, 200)]))
            ->assertRedirect(route('employees.index'));

        $employee = Employee::where('username', 'hang.le')->sole();
        $this->assertTrue(Hash::check('secret123', $employee->password));
        $this->assertSame(EmployeeRole::Staff, $employee->role);
        $this->assertSame(EmployeeStatus::Active, $employee->status);
        $this->assertSame('Lê Minh Hàng', $employee->profile->name);
        $this->assertSame('0987654321', $employee->profile->phone);
        Storage::disk('public')->assertExists($employee->profile->avatar);
    }

    public function test_employee_validation(): void
    {
        Employee::factory()->create(['username' => 'hang.le']);

        $this->post(route('employees.store'), $this->validData())->assertSessionHasErrors('username');
        $this->post(route('employees.store'), $this->validData(['username' => 'co dau cách']))->assertSessionHasErrors('username');
        $this->post(route('employees.store'), $this->validData(['username' => 'moi', 'password_confirmation' => 'khac']))->assertSessionHasErrors('password');
        $this->post(route('employees.store'), $this->validData(['username' => 'moi', 'name' => '']))->assertSessionHasErrors('name');
        $this->post(route('employees.store'), $this->validData(['username' => 'moi', 'role' => 'admin']))->assertSessionHasErrors('role');
        $this->post(route('employees.store'), $this->validData(['username' => 'moi', 'avatar' => UploadedFile::fake()->create('cv.pdf', 50, 'application/pdf')]))->assertSessionHasErrors('avatar');
    }

    public function test_blank_password_on_update_keeps_old_password(): void
    {
        $employee = Employee::factory()->withProfile()->create(['username' => 'hang.le', 'password' => 'old-password']);

        $this->put(route('employees.update', $employee), $this->validData(['password' => '', 'password_confirmation' => '', 'name' => 'Tên mới']))
            ->assertSessionHasNoErrors();

        $employee->refresh();
        $this->assertSame('Tên mới', $employee->profile->name);
        $this->assertTrue(Hash::check('old-password', $employee->password));
    }

    public function test_manager_can_lock_account_and_replace_avatar(): void
    {
        $oldAvatar = UploadedFile::fake()->image('old.png')->store('avatars', 'public');
        $employee = Employee::factory()->withProfile(['avatar' => $oldAvatar])->create(['username' => 'hang.le']);

        $this->put(route('employees.update', $employee), $this->validData([
            'password' => '',
            'password_confirmation' => '',
            'status' => EmployeeStatus::Inactive->value,
            'avatar' => UploadedFile::fake()->image('new.png'),
        ]))->assertSessionHasNoErrors();

        $employee->refresh();
        $this->assertSame(EmployeeStatus::Inactive, $employee->status);
        Storage::disk('public')->assertMissing($oldAvatar);
        Storage::disk('public')->assertExists($employee->profile->avatar);
    }

    public function test_manager_cannot_change_own_role_or_lock_self(): void
    {
        $this->put(route('employees.update', $this->manager), $this->validData([
            'username' => $this->manager->username,
            'password' => '',
            'password_confirmation' => '',
            'role' => EmployeeRole::Staff->value,
            'status' => EmployeeStatus::Inactive->value,
        ]))->assertSessionHasNoErrors();

        $this->manager->refresh();
        $this->assertTrue($this->manager->isManager());
        $this->assertTrue($this->manager->isActive());
    }

    public function test_manager_cannot_delete_self(): void
    {
        $this->delete(route('employees.destroy', $this->manager))->assertSessionHas('error');

        $this->assertModelExists($this->manager);
    }

    public function test_employee_with_orders_cannot_be_deleted(): void
    {
        $employee = Employee::factory()->withProfile()->create();
        Order::factory()->for($employee)->create();

        $this->delete(route('employees.destroy', $employee))->assertSessionHas('error');

        $this->assertModelExists($employee);
    }

    public function test_employee_without_orders_is_deleted_with_profile_and_avatar(): void
    {
        $avatar = UploadedFile::fake()->image('a.png')->store('avatars', 'public');
        $employee = Employee::factory()->withProfile(['avatar' => $avatar])->create();

        $this->delete(route('employees.destroy', $employee))->assertRedirect(route('employees.index'));

        $this->assertModelMissing($employee);
        $this->assertDatabaseMissing('employee_profiles', ['employee_id' => $employee->id]);
        Storage::disk('public')->assertMissing($avatar);
    }
}
