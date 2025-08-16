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
    public function __construct(private PdfGeneratorFactory $pdfFactory)
    {
    }

    public function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $formResolver = FormResolver::createFromRequest($this->request);

        $htmlDocument = new DOMDocument('1.0', 'UTF-8');
        $htmlDocument->loadHTML(base64_decode($formResolver->getValue('body', 'html')));

        $pdfGenerator = PdfGeneratorFactory::create();
        $pdfGenerator->WriteHTML($view->fetch(
            'pdf/character.html.twig',
            [
                'characterTable' => $this->getTableContent($htmlDocument, 'characterTable'),
                'backgroundTable' => $this->getTableContent($htmlDocument, 'backgroundTable'),
                'now' => new DateTimeImmutable()->format('d-m-Y'),
            ],
        ));

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

        /** @var DOMNode $table */
        foreach ($tables as $table) {
            if ($table->attributes->getNamedItem('id')?->nodeValue === $tableName) {
                return str_replace('<table', '<table colspan="10"', $htmlDocument->saveHTML($table));
            }
        }

        return '';
    }
}
