# Release instructions

## Repository

- Repository: `ITMOSOFT/laravel-marketplace-messaging`
- Composer package: `itmosoft/laravel-marketplace-messaging`
- Description: `Marketplace conversation and messaging primitives for Laravel applications, with polymorphic participants, messages, read receipts, and attachment metadata.`

## Validate locally

```bash
composer validate --strict
composer install
composer test
```

Do not tag a release until local PHPUnit tests pass and GitHub Actions is green.

## User-run Git commands

Run these from the existing, already-connected repository. These commands are for you to execute; no repository changes are performed by the assistant.

```bash
git add .
git commit -m "feat: add marketplace messaging"
git push origin main
```

After tests and CI pass:

```bash
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

## GitHub Release

Title: `v1.0.0 - Marketplace Messaging`

Description:

```markdown
## Features
- Polymorphic conversations and optional subject linking.
- Polymorphic participants with roles and membership status.
- Participant-guarded message sending.
- Conversation close and reopen.
- Participant read watermark.
- Message edit and soft-delete operations.
- Attachment metadata with storage path references.

## Requirements
- PHP 8.3+
- Laravel 12 or 13

## Installation
composer require itmosoft/laravel-marketplace-messaging
php artisan migrate

## Notes
This package does not provide real-time transport, notifications, message search, per-message read receipts, moderation, file upload/security, or end-to-end encryption.
```

## Packagist

1. Sign in to Packagist.
2. Submit `https://github.com/ITMOSOFT/laravel-marketplace-messaging`.
3. Confirm the package name is `itmosoft/laravel-marketplace-messaging`.
4. Configure the GitHub service hook if prompted.
5. Verify Packagist detects the `v1.0.0` tag.
