<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
enum OrderStatus: string
{
    use EnumBehaviourTrait;

    case DRAFT = 'draft';
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case VERIFIED = 'verified';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
    case REVISED = 'revised';
    case INVITED = 'invited';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'draft',
            self::PENDING => 'pending',
            self::CONFIRMED => 'incoming',
            self::VERIFIED => 'verified',
            self::COMPLETED => 'shipped',
            self::REJECTED => 'rejected',
            self::CANCELED => 'canceled',
            self::REVISED => 'revised',
            self::INVITED => 'invited',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
            self::PENDING => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
            self::CONFIRMED => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            self::VERIFIED => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            self::COMPLETED => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::REJECTED => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            self::CANCELED => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
            self::REVISED => 'bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-900 dark:text-fuchsia-300',
            self::INVITED => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        };
    }

    public static function getArchiveOptions(): array
    {
        return array_filter(collect(self::cases())->map(
            function ($item) {
                if (!in_array($item, [OrderStatus::CONFIRMED, OrderStatus::VERIFIED, OrderStatus::COMPLETED, OrderStatus::INVITED])) {
                    return ['value' => $item->value, 'label' => ucfirst($item->value)];
                }

                return null;
            }
        )->toArray());
    }
}
