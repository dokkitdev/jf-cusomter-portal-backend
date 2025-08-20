<?php

namespace App\Modules\Notify\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

class LetterTemplateService
{
    public const GROUP_NAME_PRIVATE = 'private';
    public const GROUP_NAME_HOUSING_AUTHORITIES = 'housing_authorities';
    public const GROUP_NAME_CHL_OTHER_LETTERS = 'chl_other_letters';
    public const GROUP_NAME_CHL_GAS_LETTERS = 'chl_gas_letters';
    public const GROUP_NAME_CHL_ELECTRIC_LETTERS = 'chl_electric_letters';
    public const GROUP_NAME_APPOINTMENT_LETTERS = 'appointment_letters';

    public const LETTER_NAME_PRIVATE_ANNUAL_CONTRACTS = 'private__annual_contracts';
    public const LETTER_NAME_PRIVATE_DIRECT_DEBIT_CONTRACTS = 'private__direct_debit_contracts';
    public const LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_1 = 'housing_authorities__no_access_1';
    public const LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_2 = 'housing_authorities__no_access_2';
    public const LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_3 = 'housing_authorities__no_access_3';
    public const LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_1 = 'chl_other_letters__appointment_1';
    public const LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_2 = 'chl_other_letters__appointment_2';
    public const LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_3 = 'chl_other_letters__appointment_3';
    public const LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_1 = 'chl_gas_letters__appointment_1';
    public const LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_2 = 'chl_gas_letters__appointment_2';
    public const LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_3 = 'chl_gas_letters__appointment_3';
    public const LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_1 = 'chl_electric_letters__appointment_1';
    public const LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_2 = 'chl_electric_letters__appointment_2';
    public const LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_3 = 'chl_electric_letters__appointment_3';
    public const LETTER_NAME_APPOINTMENT_LETTERS_APPOINTMENT = 'appointment_letters__appointment';

    public const LETTER_NAMES = [
        self::LETTER_NAME_PRIVATE_ANNUAL_CONTRACTS,
        self::LETTER_NAME_PRIVATE_DIRECT_DEBIT_CONTRACTS,
        self::LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_1,
        self::LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_2,
        self::LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_3,
        self::LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_1,
        self::LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_2,
        self::LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_3,
        self::LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_1,
        self::LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_2,
        self::LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_3,
        self::LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_1,
        self::LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_2,
        self::LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_3,
        self::LETTER_NAME_APPOINTMENT_LETTERS_APPOINTMENT,
    ];

    public const GROUPED_LETTER_TEMPLATES = [
        self::GROUP_NAME_PRIVATE => [
            self::LETTER_NAME_PRIVATE_ANNUAL_CONTRACTS,
            self::LETTER_NAME_PRIVATE_DIRECT_DEBIT_CONTRACTS,
        ],
        self::GROUP_NAME_HOUSING_AUTHORITIES => [
            self::LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_1,
            self::LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_2,
            self::LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_3,
        ],
        self::GROUP_NAME_CHL_OTHER_LETTERS => [
            self::LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_1,
            self::LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_2,
            self::LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_3,
        ],
        self::GROUP_NAME_CHL_GAS_LETTERS => [
            self::LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_1,
            self::LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_2,
            self::LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_3,
        ],
        self::GROUP_NAME_CHL_ELECTRIC_LETTERS => [
            self::LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_1,
            self::LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_2,
            self::LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_3,
        ],
        self::GROUP_NAME_APPOINTMENT_LETTERS => [
            self::LETTER_NAME_APPOINTMENT_LETTERS_APPOINTMENT,
        ],
    ];

    protected Filesystem $storage;

    public function __construct()
    {
        $this->storage = Storage::disk('letter_templates');
    }

    public function exists(string $letterName): bool
    {
        return $this->storage->exists($letterName);
    }

    public function upload(string $letterName, File $file): void
    {
        $this->storage->putStream($letterName, fopen($file->getPathname(), 'r'));
    }

    public function getContent(string $letterName): string
    {
        return $this->storage->get($letterName);
    }
}
