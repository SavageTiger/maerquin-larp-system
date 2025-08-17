<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Pdf;

use Mpdf\Config\ConfigVariables;
use Mpdf\Mpdf;

class PdfGeneratorFactory
{
    public static function create(): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();

        $fontDirs = $defaultConfig['fontDir'];

        $mpdf = new Mpdf([
            'margin_top' => 4,
            'margin_left' => 4,
            'margin_right' => 4,
            'margin_bottiom' => 4,
            'tempDir' => sys_get_temp_dir(),
            'fontDir' => array_merge($fontDirs, [
                __DIR__ . '/Fonts',
            ]),
            'default_font' => 'Verdana',
            'default_font_size' => 10,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetBasePath(__DIR__ . '/../../../public/assets');

        return $mpdf;
    }
}
