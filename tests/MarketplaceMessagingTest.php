<?php

namespace ITMOSOFT\MarketplaceMessaging\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ITMOSOFT\MarketplaceMessaging\Exceptions\InvalidConversationOperation;
use ITMOSOFT\MarketplaceMessaging\Models\Conversation;
use ITMOSOFT\MarketplaceMessaging\Tests\Fixtures\Order;
use ITMOSOFT\MarketplaceMessaging\Tests\Fixtures\User;

class MarketplaceMessagingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['test_users', 'test_orders'] as $table) {
            Schema::create($table, function (Blueprint $blueprint) {
                $blueprint->id();
                $blueprint->string('name');
            });
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    private function makeConversation(): array
    {
        $buyer = User::query()->create(['name' => 'Buyer']);
        $seller = User::query()->create(['name' => 'Seller']);
        $order = Order::query()->create(['name' => 'Order 1']);

        $conversation = Conversation::query()->create([
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'type' => 'order',
            'title' => 'Order discussion',
        ]);
        $conversation->addParticipant($buyer, 'buyer');
        $conversation->addParticipant($seller, 'seller');

        return [$conversation, $buyer, $seller, $order];
    }

    public function test_conversation_has_polymorphic_subject_and_participants(): void
    {
        [$conversation, $buyer, $seller, $order] = $this->makeConversation();

        $this->assertSame($order->id, $conversation->subject->id);
        $this->assertCount(2, $conversation->participants);
        $this->assertSame($buyer->id, $conversation->participants()->first()->participant->id);
        $this->assertSame($seller->id, $conversation->participants()->where('role', 'seller')->first()->participant->id);
    }

    public function test_active_participant_can_send_message(): void
    {
        [$conversation, $buyer] = $this->makeConversation();
        $message = $conversation->sendMessage($buyer, 'Hello seller.');

        $this->assertSame('Hello seller.', $message->body);
        $this->assertSame($buyer->id, $message->sender->id);
        $this->assertNotNull($conversation->fresh()->last_message_at);
    }

    public function test_non_participant_cannot_send_message(): void
    {
        [$conversation] = $this->makeConversation();
        $outsider = User::query()->create(['name' => 'Outsider']);

        $this->expectException(InvalidConversationOperation::class);
        $conversation->sendMessage($outsider, 'Unauthorized message.');
    }

    public function test_left_participant_cannot_send_message(): void
    {
        [$conversation, $buyer] = $this->makeConversation();
        $conversation->removeParticipant($buyer);

        $this->expectException(InvalidConversationOperation::class);
        $conversation->sendMessage($buyer, 'I left.');
    }

    public function test_membership_can_be_reactivated_without_duplicate_row(): void
    {
        [$conversation, $buyer] = $this->makeConversation();
        $conversation->removeParticipant($buyer);
        $conversation->addParticipant($buyer, 'buyer');

        $this->assertCount(1, $conversation->participants()->where('participant_id', $buyer->id)->get());
        $this->assertSame('active', $conversation->participants()->where('participant_id', $buyer->id)->first()->status);
    }

    public function test_participant_can_mark_conversation_read(): void
    {
        [$conversation, $buyer] = $this->makeConversation();
        $membership = $conversation->participants()->where('participant_id', $buyer->id)->first();
        $membership->markRead();

        $this->assertNotNull($membership->fresh()->last_read_at);
    }

    public function test_message_can_be_edited_and_soft_deleted(): void
    {
        [$conversation, $buyer] = $this->makeConversation();
        $message = $conversation->sendMessage($buyer, 'Original');
        $message->edit('Updated');

        $this->assertSame('Updated', $message->fresh()->body);
        $this->assertNotNull($message->fresh()->edited_at);

        $message->softDeleteMessage();
        $this->assertSame('', $message->fresh()->body);
        $this->assertNotNull($message->fresh()->deleted_at);
    }

    public function test_message_can_have_attachment_metadata(): void
    {
        [$conversation, $buyer] = $this->makeConversation();
        $message = $conversation->sendMessage($buyer, 'Please see attached.');
        $attachment = $message->attachments()->create([
            'disk' => 'private',
            'path' => 'messages/attachments/contract.pdf',
            'original_name' => 'contract.pdf',
            'mime_type' => 'application/pdf',
            'size' => 8192,
        ]);

        $this->assertSame('contract.pdf', $attachment->original_name);
        $this->assertSame(8192, $attachment->size);
        $this->assertCount(1, $message->attachments);
    }

    public function test_closed_conversation_rejects_messages_and_new_participants(): void
    {
        [$conversation, $buyer] = $this->makeConversation();
        $conversation->close();

        try {
            $conversation->sendMessage($buyer, 'Too late.');
            $this->fail('Expected closed conversation error.');
        } catch (InvalidConversationOperation) {
            $this->assertTrue(true);
        }

        $newUser = User::query()->create(['name' => 'New user']);
        $this->expectException(InvalidConversationOperation::class);
        $conversation->addParticipant($newUser);
    }
}
