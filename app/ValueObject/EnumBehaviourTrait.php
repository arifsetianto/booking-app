<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
trait EnumBehaviourTrait
{
    /**
     * @param $enumerator
     * @return bool
     */
    public function is($enumerator): bool
    {
        return $this === $enumerator || $this->value === $enumerator;
    }

    /**
     * @return string|int
     */
    public function getValue(): string|int
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public static function getValues(): array
    {
        return collect(self::cases())->map(function ($item) {
            return $item->value;
        })->toArray();
    }

}
