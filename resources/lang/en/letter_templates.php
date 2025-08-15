<?php

use App\Services\LetterTemplateService;

return [
    'groups' => [
        LetterTemplateService::GROUP_NAME_PRIVATE => 'Private',
        LetterTemplateService::GROUP_NAME_HOUSING_AUTHORITIES => 'Housing Authorities',
        LetterTemplateService::GROUP_NAME_CHL_OTHER_LETTERS => 'CHL Other Letters',
        LetterTemplateService::GROUP_NAME_CHL_GAS_LETTERS => 'CHL Gas Letters',
        LetterTemplateService::GROUP_NAME_CHL_ELECTRIC_LETTERS => 'CHL Electric Letters',
        LetterTemplateService::GROUP_NAME_APPOINTMENT_LETTERS => 'Appointment letter',
    ],
    'letter_names' => [
        LetterTemplateService::LETTER_NAME_PRIVATE_ANNUAL_CONTRACTS => 'Annual Contracts',
        LetterTemplateService::LETTER_NAME_PRIVATE_DIRECT_DEBIT_CONTRACTS => 'Direct Debit Contracts',
        LetterTemplateService::LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_1 => 'Letter No Access 1 (Letter)',
        LetterTemplateService::LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_2 => 'Letter No Access 2 (Letter)',
        LetterTemplateService::LETTER_NAME_HOUSING_AUTHORITIES_NO_ACCESS_3 => 'Letter No Access 3 (Letter)',
        LetterTemplateService::LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_1 => 'Appointment Letter 1 (Other)',
        LetterTemplateService::LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_2 => 'Appointment Letter 2 (Other)',
        LetterTemplateService::LETTER_NAME_CHL_OTHER_LETTERS_APPOINTMENT_3 => 'Appointment Letter 3 (Other)',
        LetterTemplateService::LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_1 => 'Appointment Letter 1 (Gas)',
        LetterTemplateService::LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_2 => 'Appointment Letter 2 (Gas)',
        LetterTemplateService::LETTER_NAME_CHL_GAS_LETTERS_APPOINTMENT_3 => 'Appointment Letter 3 (Gas)',
        LetterTemplateService::LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_1 => 'Appointment Letter 1 (Electric)',
        LetterTemplateService::LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_2 => 'Appointment Letter 2 (Electric)',
        LetterTemplateService::LETTER_NAME_CHL_ELECTRIC_LETTERS_APPOINTMENT_3 => 'Appointment Letter 3 (Electric)',
        LetterTemplateService::LETTER_NAME_APPOINTMENT_LETTERS_APPOINTMENT => 'Letter',
    ],
];