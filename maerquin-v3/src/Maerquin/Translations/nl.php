<?php

declare(strict_types=1);

return [
    'character.warning.forbidden_skill' => 'De skill "%skill%" is niet toegestaan voor een character met ras "%race%".',
    'character.warning.mandatory_skill' => 'De skill "%skill%" is verplicht voor een character met ras "%race%".',
    'character.warning.missing_required_skill' => 'De skill "%skill%" vereist eerst "%requiredSkill%", maar die ontbreekt nog.',
    'character.warning.not_enough_points' => 'Het karakter heeft niet genoeg punten: %available% spendeerbaar, %required% vereist.',
    'character.warning.skill_bought_too_often' => 'De skill ‘%skill%’ is %amount%× aangeschaft; toegestaan maximum is %maximum%.',

    'registration.error.first_name_missing' => 'We missen nog je voornaam.',
    'registration.error.last_name_missing' => 'We missen nog je achternaam.',
    'registration.error.invalid_email' => 'Vul een geldig e-mailadres in.',
    'registration.error.password_too_short' => 'Het wachtwoord moet minimaal 8 tekens lang zijn.',
    'registration.error.passwords_not_matching' => 'De wachtwoorden komen niet overeen.',

    'login.error.user_not_found' => 'Gebruiker niet gevonden.',
    'login.error.password_incorrect' => 'Wachtwoord onjuist.',
    'login.error.email_not_verified' => 'Je e-mailadres is nog niet bevestigd. We hebben je een nieuwe bevestigingslink gestuurd.',

    'mail.subject.password_setup' => 'Maerquin wachtwoord instellen',
    'mail.subject.email_verification' => 'Maerquin account bevestigen',
];
