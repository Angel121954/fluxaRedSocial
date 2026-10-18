<?php

declare(strict_types=1);

namespace App\Services\Cv;

use Spatie\Browsershot\Browsershot;

class CvPdfGenerator
{
    public function generate(string $html, array $margins = [0, 0, 0, 0]): string
    {
        return Browsershot::html($html)
            ->setNodeBinary(config('browsershot.node_binary', '/usr/bin/node'))
            ->setNpmBinary(config('browsershot.npm_binary', '/usr/bin/npm'))
            ->setNodeModulePath(config('browsershot.node_modules_path'))
            ->setChromePath(config('browsershot.chrome_path'))
            ->noSandbox()
            ->addChromiumArguments([
                'crash-dumps-dir' => '/tmp',
                'disable-dev-shm-usage' => true,
            ])
            ->timeout((int) config('browsershot.timeout', 120))
            ->waitUntilNetworkIdle()
            ->format('A4')
            ->margins($margins[0] ?? 0, $margins[1] ?? 0, $margins[2] ?? 0, $margins[3] ?? 0)
            ->pdf();
    }

    public function wrapHtml(string $content): string
    {
        return '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>* { margin:0; padding:0; box-sizing:border-box; } body { background:#ffffff; }</style>
</head>
<body>'.$content.'</body>
</html>';
    }
}
