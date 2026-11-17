# ITMOSOFT Laravel Marketplace Messaging

Conversation and messaging primitives for marketplace Laravel applications. Supports polymorphic conversation subjects, participants, senders, read timestamps, and attachment metadata.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

```bash
composer require itmosoft/laravel-marketplace-messaging
php artisan vendor:publish --tag=marketplace-messaging-config
php artisan migrate
```

Laravel package discovery registers the service provider automatically.

## Create a conversation

```php
use ITMOSOFT\MarketplaceMessaging\Models\Conversation;

$conversation = Conversation::query()->create([
    'subject_type' => $order->getMorphClass(),
    'subject_id' => $order->getKey(),
    'type' => 'order',
    'title' => 'Discussion about order #123',
]);

$conversation->addParticipant($buyer, 'buyer');
$conversation->addParticipant($seller, 'seller');
```

The subject is optional, allowing general conversations. Use morph-map aliases through `getMorphClass()`.

## Send messages

```php
$message = $conversation->sendMessage($buyer, 'When will my order ship?');
```

Only active conversation participants can send messages through this method. This is a domain guard, not a complete authorization policy. The host application must authorize who may create conversations, add participants, read messages, edit/delete messages, and download attachments.

## Membership and read state

```php
$conversation->removeParticipant($seller);

$membership = $conversation->participants()
    ->where('participant_id', $buyer->getKey())
    ->first();

$membership->markRead();
```

Re-adding a previously removed participant reactivates the existing membership record. The membership's `last_read_at` is a per-participant read watermark, not per-message delivery/read receipts.

Add `ITMOSOFT\MarketplaceMessaging\Concerns\HasConversations` to a participant model to expose `conversationMemberships()`.

## Edit and delete messages

```php
$message->edit('Updated message text.');
$message->softDeleteMessage();
```

Soft deletion is implemented with a `deleted_at` timestamp and blanking the body; it does not use Laravel's `SoftDeletes` trait. Build retention, moderation, and audit rules in the host application.

## Attachment metadata

```php
$message->attachments()->create([
    'disk' => 'private',
    'path' => 'marketplace-messages/123/document.pdf',
    'original_name' => 'document.pdf',
    'mime_type' => 'application/pdf',
    'size' => 120000,
]);
```

The package stores attachment metadata and paths only. It does not upload, validate, virus-scan, encrypt, or authorize files. Store files privately and authorize downloads in your application.

## Important boundaries

This package does not provide real-time transport, WebSockets, email/push notifications, message search, per-message read receipts, moderation, spam controls, rate limiting, encryption-at-rest, end-to-end encryption, or immutable audit history. Integrate broadcasting and notifications separately. Avoid placing payment card data, credentials, or other secrets in message bodies or metadata.

## Testing

```bash
composer install
composer test
```

## License

MIT. See [LICENSE](LICENSE).
