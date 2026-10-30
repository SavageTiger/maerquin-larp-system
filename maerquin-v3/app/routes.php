<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use SvenHK\Maerquin\Controller\API\CharactersApiController;
use SvenHK\Maerquin\Controller\API\LinkableSkillsApiController;
use SvenHK\Maerquin\Controller\API\PlayerResetPasswordEmailController;
use SvenHK\Maerquin\Controller\API\SkillsApiController;
use SvenHK\Maerquin\Controller\CharactersController;
use SvenHK\Maerquin\Controller\EventsController;
use SvenHK\Maerquin\Controller\FormDeleteController;
use SvenHK\Maerquin\Controller\FormFieldsController;
use SvenHK\Maerquin\Controller\FormsController;
use SvenHK\Maerquin\Controller\FormSectionsController;
use SvenHK\Maerquin\Controller\Frontend\CharactersController as FrontendCharactersController;
use SvenHK\Maerquin\Controller\Frontend\PasswordEmailController;
use SvenHK\Maerquin\Controller\Frontend\RegisterController;
use SvenHK\Maerquin\Controller\Frontend\SubscriptionsController as FrontendSubscriptionsController;
use SvenHK\Maerquin\Controller\Frontend\VerifyEmailController;
use SvenHK\Maerquin\Controller\HomeController;
use SvenHK\Maerquin\Controller\LoginController;
use SvenHK\Maerquin\Controller\LogoutController;
use SvenHK\Maerquin\Controller\PlayerResetPasswordController;
use SvenHK\Maerquin\Controller\PlayersController;
use SvenHK\Maerquin\Controller\PrintCharacter;
use SvenHK\Maerquin\Controller\SubscriptionsExportController;
use SvenHK\Maerquin\Controller\RacesController;
use SvenHK\Maerquin\Controller\SkillsController;
use SvenHK\Maerquin\Controller\StatusController;
use SvenHK\Maerquin\Controller\SubscriptionsController;

return function (App $app): void {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', HomeController::class);
    $app->get('/home.html', HomeController::class);

    $app->get('/login.html', LoginController::class);
    $app->post('/login.html', LoginController::class);

    $app->get('/logout.html', LogoutController::class);

    $app->get('/register.html', RegisterController::class);
    $app->post('/register.html', RegisterController::class);
    $app->get('/verify-email/{hash}.html', VerifyEmailController::class);
    $app->get('/password-email.html', PasswordEmailController::class);
    $app->post('/password-email.html', PasswordEmailController::class);

    $app->get('/admin/characters.html', CharactersController::class);
    $app->get('/admin/characters/create.html', CharactersController::class);
    $app->get('/admin/characters/{characterId}.html', CharactersController::class);
    $app->post('/admin/characters/{characterId}.html', CharactersController::class);
    $app->get('/admin/characters/persisted/{characterId}.html', CharactersController::class);
    $app->get('/admin/characters/api', CharactersApiController::class);

    $app->post('/admin/character/print.pdf', PrintCharacter::class);

    $app->get('/admin/players.html', PlayersController::class);
    $app->get('/admin/player/create.html', PlayersController::class);
    $app->get('/admin/players/persisted/{playerId}.html', PlayersController::class);
    $app->get('/admin/players/{playerId}.html', PlayersController::class);
    $app->post('/admin/players/{playerId}.html', PlayersController::class);
    $app->post('/admin/players/{playerId}/reset_password_email/api', PlayerResetPasswordEmailController::class);
    $app->get('/reset-password/{hash}.html', PlayerResetPasswordController::class);
    $app->get('/reset-password/persisted/{hash}.html', PlayerResetPasswordController::class);
    $app->post('/reset-password/{hash}.html', PlayerResetPasswordController::class);

    $app->get('/admin/skills.html', SkillsController::class);
    $app->post('/admin/skills/{skillId}.html', SkillsController::class);
    $app->get('/admin/skills/{skillId}.html', SkillsController::class);
    $app->get('/admin/skills/api', SkillsApiController::class);
    $app->get('/admin/skills/persisted/{skillId}.html', SkillsController::class);
    $app->get('/admin/skills/linkable/api', LinkableSkillsApiController::class);

    $app->get('/admin/events.html', EventsController::class);
    $app->get('/admin/events/{eventId}.html', EventsController::class);
    $app->get('/admin/events/persisted/{eventId}.html', EventsController::class);
    $app->post('/admin/events/{eventId}.html', EventsController::class);

    $app->get('/admin/races.html', RacesController::class);
    $app->get('/admin/races/{raceId}.html', RacesController::class);
    $app->get('/admin/races/persisted/{raceId}.html', RacesController::class);
    $app->post('/admin/races/{raceId}.html', RacesController::class);

    $app->get('/admin/status.html', StatusController::class);
    $app->get('/admin/forms.html', FormsController::class);
    $app->get('/admin/form/create.html', FormsController::class);
    $app->get('/admin/forms/persisted/{formId}.html', FormsController::class);
    $app->get('/admin/forms/{formId}.html', FormsController::class);
    $app->post('/admin/forms/{formId}.html', FormsController::class);
    $app->post('/admin/forms/{formId}/delete', FormDeleteController::class);
    $app->post('/admin/forms/{formId}/sections', FormSectionsController::class);
    $app->get('/admin/forms/{formId}/sections/{sectionId}.html', FormSectionsController::class);
    $app->post('/admin/forms/{formId}/sections/{sectionId}/delete', FormSectionsController::class);
    $app->post('/admin/forms/{formId}/sections/{sectionId}/fields', FormFieldsController::class);

    $app->get('/admin/subscriptions.html', SubscriptionsController::class);
    $app->get('/admin/subscriptions/{eventId}.html', SubscriptionsController::class);
    $app->get('/admin/subscriptions/{eventId}/subscribers.csv', SubscriptionsExportController::class);
    $app->post('/admin/subscriptions/{eventId}/{subscriptionId}', SubscriptionsController::class);
    $app->get('/subscriptions.html', FrontendSubscriptionsController::class);
    $app->get('/subscriptions/{eventId}.html', FrontendSubscriptionsController::class);
    $app->post('/subscriptions/{eventId}.html', FrontendSubscriptionsController::class);
    $app->get('/characters.html', FrontendCharactersController::class);
    $app->get('/characters/{characterId}.html', FrontendCharactersController::class);
    $app->post('/character/print.sheet.pdf', PrintCharacter::class);
};
