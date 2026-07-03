<?php

namespace Tests\Feature;

use App\Models\SupportCannedResponse;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@ex.com'],
            ['name' => 'ادمین', 'password' => 'password', 'role' => 'admin'],
        );
    }

    public function test_ticket_number_format(): void
    {
        $num = SupportTicket::generateTicketNumber();
        $this->assertMatchesRegularExpression('/^TKT-\d{4}-\d{4}$/', $num);
    }

    public function test_guest_can_create_ticket_with_attachment(): void
    {
        $res = $this->post(route('support.store'), [
            'subject'     => 'مشکل در ورود به حساب',
            'department'  => 'technical',
            'message'     => 'من نمی‌توانم وارد حساب کاربری خود شوم و این پیام خطا را می‌بینم.',
            'guest_name'  => 'مهمان تست',
            'guest_email' => 'guest@ex.com',
            'attachments' => [UploadedFile::fake()->image('shot.png')],
        ]);

        $ticket = SupportTicket::first();
        $this->assertNotNull($ticket);
        $res->assertRedirect(route('support.show', $ticket->ticket_number));

        $this->assertSame('open', $ticket->status);
        $this->assertSame('مهمان تست', $ticket->guest_name);
        $this->assertCount(1, $ticket->messages);

        $att = $ticket->messages->first()->attachments->first();
        $this->assertNotNull($att);
        $full = storage_path('app/' . $att->file_path);
        $this->assertFileExists($full);

        // پاک‌سازی فایل تست
        @unlink($full);
    }

    public function test_guest_store_validation_requires_email(): void
    {
        $this->post(route('support.store'), [
            'subject'    => 'x', // too short only for subject? subject max 255 fine, but message min 20 fails
            'department' => 'general',
            'message'    => 'کوتاه',
        ])->assertSessionHasErrors(['message', 'guest_name', 'guest_email']);
    }

    public function test_guest_can_track_ticket_by_number_and_email(): void
    {
        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-2607-0001',
            'guest_name' => 'گ', 'guest_email' => 'g@ex.com',
            'subject' => 'سوال', 'department' => 'general',
        ]);

        $this->post(route('support.track.result'), [
            'ticket_number' => 'TKT-2607-0001',
            'email' => 'g@ex.com',
        ])->assertRedirect(route('support.show', $ticket->ticket_number));

        // با ایمیل اشتباه پیدا نشود
        $this->post(route('support.track.result'), [
            'ticket_number' => 'TKT-2607-0001',
            'email' => 'wrong@ex.com',
        ])->assertSessionHas('error');
    }

    public function test_admin_reply_sets_first_response_and_status(): void
    {
        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-2607-0002',
            'guest_name' => 'گ', 'guest_email' => 'g@ex.com',
            'subject' => 'سوال', 'department' => 'general', 'status' => 'open',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.support.reply', $ticket->ticket_number), [
                'message' => 'سلام، در حال بررسی هستیم.',
            ])->assertRedirect();

        $ticket->refresh();
        $this->assertNotNull($ticket->first_response_at);
        $this->assertSame('waiting_user', $ticket->status);
    }

    public function test_internal_note_hidden_from_user_but_visible_to_admin(): void
    {
        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-2607-0003',
            'guest_name' => 'گ', 'guest_email' => 'g@ex.com',
            'subject' => 'سوال محرمانه', 'department' => 'general', 'status' => 'open',
        ]);

        $secret = 'یادداشت-داخلی-محرمانه-XYZ';
        $this->actingAs($this->admin())
            ->post(route('admin.support.reply', $ticket->ticket_number), [
                'message' => $secret,
                'is_internal' => '1',
            ]);

        // internal note must NOT change status / first_response
        $ticket->refresh();
        $this->assertNull($ticket->first_response_at);

        // admin sees it
        $this->actingAs($this->admin())
            ->get(route('admin.support.show', $ticket->ticket_number))
            ->assertOk()->assertSee($secret);

        // guest (who tracked) does NOT see it
        $this->withSession(['support_access' => [$ticket->ticket_number]])
            ->get(route('support.show', $ticket->ticket_number))
            ->assertOk()->assertDontSee($secret);
    }

    public function test_non_owner_cannot_view_ticket(): void
    {
        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-2607-0004',
            'guest_name' => 'گ', 'guest_email' => 'g@ex.com',
            'subject' => 'خصوصی', 'department' => 'general',
        ]);

        // no session access → forbidden
        $this->get(route('support.show', $ticket->ticket_number))->assertForbidden();
    }

    public function test_admin_status_change_creates_system_message(): void
    {
        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-2607-0005',
            'guest_name' => 'گ', 'guest_email' => 'g@ex.com',
            'subject' => 'x', 'department' => 'general', 'status' => 'open',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.support.status', $ticket->ticket_number), ['status' => 'resolved']);

        $ticket->refresh();
        $this->assertSame('resolved', $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertTrue($ticket->messages()->where('sender_type', 'system')->exists());
    }

    public function test_canned_response_use_count_increments_on_admin_reply(): void
    {
        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-2607-0006',
            'guest_name' => 'گ', 'guest_email' => 'g@ex.com',
            'subject' => 'x', 'department' => 'general', 'status' => 'open',
        ]);
        $admin = $this->admin();
        $canned = SupportCannedResponse::create([
            'title' => 'پاسخ استاندارد', 'content' => 'با تشکر از تماس شما.', 'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->post(route('admin.support.reply', $ticket->ticket_number), [
            'message' => 'با تشکر از تماس شما.',
            'canned_id' => $canned->id,
        ]);

        $this->assertSame(1, $canned->fresh()->use_count);
    }
}
