<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use DateTimeImmutable;
use DOMDocument;
use DOMNode;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Form\FormResolver;
use SvenHK\Maerquin\Pdf\PdfGeneratorFactory;

class PrintCharacter extends Action
{
    public function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $formResolver = FormResolver::createFromRequest($this->request);

        $amountOfCharacters = (int)$formResolver->getValue('characterCount', 'html');
        $characterOffset = 0;

        $pdfGenerator = PdfGeneratorFactory::create();

        while ($amountOfCharacters > 0) {
            ++$characterOffset;

            $htmlDocument = new DOMDocument('1.0', 'UTF-8');
            $htmlDocument->loadHTML(
                base64_decode($formResolver->getValue('body' . $characterOffset, 'html')),
            );

            $pdfGenerator->WriteHTML($view->fetch(
                'Pdf/character.html.twig',
                [
                    'characterTable' => $this->getTableContent($htmlDocument, 'characterTable'),
                    'backgroundTable' => $this->getTableContent($htmlDocument, 'backgroundTable'),
                    'skillsTable' => $this->getTableContent($htmlDocument, 'skillsTable'),
                    'eventsTable' => $this->getTableContent($htmlDocument, 'eventsTable'),
                    'notesTable' => $this->getTableContent($htmlDocument, 'notesTable'),
                    'now' => new DateTimeImmutable()->format('d-m-Y'),
                ],
            ));

            if ($amountOfCharacters !== 1) {
                $pdfGenerator->WriteHTML('<pagebreak/>');
            }

            --$amountOfCharacters;
        }

        $pdfBinary = $pdfGenerator->OutputBinaryData();

        $this->response->getBody()->write($pdfBinary);

        return $this->response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="' . uniqid() . '.pdf"')
            ->withStatus(200);
    }

    private function getTableContent(DOMDocument $htmlDocument, string $tableName): string
    {
        $tables = $htmlDocument->getElementsByTagName('table');

        $content = '';

        /** @var DOMNode $table */
        foreach ($tables as $table) {
            $tableId = $table->attributes->getNamedItem('id')?->nodeValue;
            $tableCaption = $table->attributes->getNamedItem('data-caption')?->nodeValue;

            if (str_starts_with($tableId, $tableName) === true) {
                if ($tableCaption !== null) {
                    $content .= '<h2 class="caption">' . $tableCaption . '</h2>';
                }

                $content .= $htmlDocument->saveHTML($table);
            }
        }

        return $content;
    }
}
