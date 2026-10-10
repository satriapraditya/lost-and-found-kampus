<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Location;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function makeReport(User $owner, array $attributes = []): Report
    {
        return Report::create($attributes + [
            'user_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['name' => 'Elektronik'])->id,
            'location_id' => Location::firstOrCreate(['name' => 'Perpustakaan'])->id,
            'type' => 'found',
            'item_name' => 'Tablet biru',
            'description' => 'Tablet biru dengan casing transparan.',
            'event_date' => now()->toDateString(),
            'status' => 'pending',
        ]);
    }

    public function test_login_sends_admin_to_dashboard_and_user_to_home()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $this->post(route('login.store'), ['identifier' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.index'));

        auth()->logout();

        $this->post(route('login.store'), ['identifier' => $user->nim, 'password' => 'password'])
            ->assertRedirect(route('beranda'));
    }

    public function test_admin_tab_rejects_non_admin_accounts()
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login.store'), ['identifier' => $user->email, 'password' => 'password', 'role' => 'admin'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('identifier');

        $this->assertGuest();
    }

    public function test_admin_login_url_points_to_login_page()
    {
        $this->get('/admin/login')->assertRedirect('/login');
    }

    public function test_admin_can_open_every_admin_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $found = $this->makeReport($owner, ['status' => 'claimed']);
        $lost = $this->makeReport($owner, ['type' => 'lost', 'item_name' => 'Dompet hitam']);
        $claim = Claim::create([
            'report_id' => $found->id,
            'user_id' => User::factory()->create()->id,
            'claim_description' => 'Tablet saya, ada stiker kucing.',
            'proof_description' => 'Foto lama tablet.',
            'status' => 'approved',
        ]);

        $this->actingAs($admin);

        foreach ([
            route('admin.index'),
            route('admin.queue'),
            route('admin.statistics'),
            route('admin.lost.index'),
            route('admin.lost.show', $lost),
            route('admin.found.index', ['status' => 'claimed']),
            route('admin.found.show', $found),
            route('admin.claims.index'),
            route('admin.claims.show', $claim),
            route('admin.admins.index'),
            route('admin.admins.create'),
            route('admin.users.index'),
        ] as $url) {
            $this->get($url)->assertOk();
        }

        // Laporan hilang tidak bisa dibuka lewat alamat barang temuan
        $this->get(route('admin.found.show', $lost))->assertNotFound();
    }

    public function test_admin_pages_are_closed_to_guests_and_regular_users()
    {
        $this->get(route('admin.lost.index'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('admin.lost.index'))
            ->assertForbidden();
    }

    public function test_admin_can_mark_an_approved_report_as_completed()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $report = $this->makeReport(User::factory()->create(), ['type' => 'lost', 'status' => 'approved']);

        $this->actingAs($admin)
            ->patch(route('admin.reports.complete', $report))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'completed']);
        $this->assertDatabaseHas('report_histories', ['report_id' => $report->id, 'user_id' => $admin->id, 'status' => 'completed']);
    }

    public function test_pending_report_cannot_be_marked_completed()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $report = $this->makeReport(User::factory()->create());

        $this->actingAs($admin)
            ->patch(route('admin.reports.complete', $report))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'pending']);
    }
}
