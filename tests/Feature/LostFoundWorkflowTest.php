<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LostFoundWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login_with_nim()
    {
        Category::create(['name' => 'Elektronik']);
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();

        $this->post(route('register.store'), [
            'name' => 'Ayu Putri',
            'nim' => '20260001',
            'study_program' => 'Informatika',
            'phone' => '081234567890',
            'email' => 'ayu@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ])->assertRedirect(route('beranda'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['nim' => '20260001', 'role' => 'user']);
        $this->get(route('lapor.create'))->assertOk()->assertSee('Elektronik');

        auth()->logout();

        $this->post(route('login.store'), [
            'identifier' => '20260001',
            'password' => 'password123',
        ])->assertRedirect(route('beranda'));

        $this->assertAuthenticated();
    }

    public function test_authenticated_user_can_submit_report_and_it_waits_for_review()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Elektronik']);

        $this->actingAs($user)->post(route('lapor.store'), [
            'nama_barang' => 'Dompet kulit cokelat',
            'deskripsi' => 'Ada goresan kecil di sisi kiri dan kartu perpustakaan di dalam.',
            'kategori' => $category->id,
            'lokasi' => 'Perpustakaan lantai dua',
            'tanggal' => now()->toDateString(),
            'foto' => UploadedFile::fake()->create('dompet.jpg', 10, 'image/jpeg'),
        ])->assertRedirect();

        $report = Report::firstOrFail();
        $this->assertDatabaseHas('reports', [
            'user_id' => $user->id,
            'item_name' => 'Dompet kulit cokelat',
            'status' => 'pending',
        ]);
        $this->get(route('barang.detail', $report))->assertOk();
        $this->assertDatabaseHas('report_histories', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('report_images', [
            'report_id' => $report->id,
            'is_primary' => true,
        ]);
        Storage::disk('public')->assertExists($report->images()->first()->image_path);
    }

    public function test_admin_can_approve_a_report_and_it_appears_on_the_public_listing()
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Elektronik']);
        $location = Location::create(['name' => 'Gedung A']);
        $report = Report::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'type' => 'found',
            'item_name' => 'Tablet biru',
            'description' => 'Tablet biru dengan casing transparan.',
            'event_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->get(route('barang.detail', $report))->assertOk();
        $this->actingAs($admin)->patch(route('admin.reports.update', $report), [
            'status' => 'approved',
            'note' => 'Informasi sudah ditinjau.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('report_histories', [
            'report_id' => $report->id,
            'user_id' => $admin->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'report',
        ]);

        $this->get(route('beranda'))->assertOk()->assertSee('Tablet biru');
    }

    public function test_authenticated_user_can_submit_claim_and_admin_approval_reserves_item()
    {
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $secondClaimant = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Elektronik']);
        $location = Location::create(['name' => 'Gedung A']);
        $report = Report::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'type' => 'found',
            'item_name' => 'Laptop hitam',
            'description' => 'Laptop dengan stiker di bagian penutup.',
            'event_date' => now()->toDateString(),
            'status' => 'approved',
        ]);

        $this->actingAs($claimant)->post(route('klaim.store', $report), [
            'ciri_khusus' => 'Ada stiker khusus berbentuk bintang di bagian bawah.',
            'whatsapp' => $claimant->phone,
        ])->assertRedirect(route('klaim.create', $report));
        $this->get(route('klaim.create', $report))->assertOk();

        $claim = Claim::firstOrFail();
        $this->actingAs($secondClaimant)->post(route('klaim.store', $report), [
            'ciri_khusus' => 'Ada goresan berbentuk garis di sisi kanan.',
            'whatsapp' => $secondClaimant->phone,
        ])->assertRedirect(route('klaim.create', $report));
        $otherClaim = Claim::where('user_id', $secondClaimant->id)->firstOrFail();

        $this->assertSame('pending', $claim->status);
        $this->assertDatabaseHas('claim_histories', ['claim_id' => $claim->id, 'status' => 'pending']);
        $this->assertDatabaseHas('notifications', ['user_id' => $owner->id, 'type' => 'claim']);

        $this->actingAs($admin)->get(route('admin.index'))->assertOk();
        $this->actingAs($claimant)->post(route('klaim.store', $report), [
            'ciri_khusus' => 'Klaim duplikat untuk barang yang sama.',
            'whatsapp' => $claimant->phone,
        ])->assertSessionHasErrors('ciri_khusus');

        $this->actingAs($admin)->get(route('notifications.index'))->assertOk();
        $this->patch(route('admin.claims.update', $claim), [
            'status' => 'approved',
            'note' => 'Bukti sesuai.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'claimed']);
        $this->assertDatabaseHas('claims', ['id' => $claim->id, 'status' => 'approved']);
        $this->assertDatabaseHas('claims', ['id' => $otherClaim->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('claim_histories', ['claim_id' => $otherClaim->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('report_histories', ['report_id' => $report->id, 'status' => 'claimed']);
        $ownerNotification = Notification::where('user_id', $owner->id)->firstOrFail();
        $this->actingAs($owner)->patch(route('notifications.read', $ownerNotification))
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->assertNotNull($ownerNotification->fresh()->read_at);

        $this->actingAs($admin)->patch(route('admin.claims.update', $claim), [
            'status' => 'completed',
        ])->assertRedirect();

        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'completed']);
        $this->assertDatabaseHas('claim_histories', ['claim_id' => $claim->id, 'status' => 'completed']);
    }

    public function test_guest_cannot_submit_report_and_user_cannot_access_admin_panel()
    {
        $this->get(route('lapor.create'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('admin.index'))
            ->assertForbidden();
    }

    public function test_users_cannot_mark_another_users_notification_as_read()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $owner->id,
            'title' => 'Laporan disetujui',
            'message' => 'Laporan sudah ditinjau.',
            'type' => 'report',
        ]);

        $this->actingAs($otherUser)
            ->patch(route('notifications.read', $notification))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }
}
