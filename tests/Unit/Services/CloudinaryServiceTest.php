<?php

declare(strict_types=1);

use App\Services\CloudinaryService;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Asset\Image;
use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Mockery as m;
use Psr\Http\Message\UriInterface;
use Tests\TestCase;

uses(TestCase::class);

function cloudUploaResult(array $data = []): ApiResponse
{
    return m::mock(ApiResponse::class)
        ->shouldReceive('getArrayCopy')
        ->andReturn(array_merge([
            'public_id' => 'fluxa/test/x',
            'secure_url' => 'https://res.cloudinary.com/fluxa/test/x',
        ], $data))
        ->getMock();
}

function cloudServiceMock(?callable $upload = null): array
{
    $image = m::mock(Image::class);
    $url = m::mock(UriInterface::class);
    $url->shouldReceive('__toString')->andReturn('https://res.cloudinary.com/fluxa/x.jpg');
    $image->shouldReceive('toUrl')->andReturn($url);

    $uploadApi = m::mock(UploadApi::class);
    if ($upload) {
        $uploadApi->shouldReceive('upload')->andReturnUsing($upload);
    }

    $cloudinary = m::mock(Cloudinary::class);
    $cloudinary->shouldReceive('uploadApi')->andReturn($uploadApi);
    $cloudinary->shouldReceive('image')->andReturn($image);

    $service = (new ReflectionClass(CloudinaryService::class))->newInstanceWithoutConstructor();
    (new ReflectionProperty(CloudinaryService::class, 'cloudinary'))->setValue($service, $cloudinary);

    return [$service, $uploadApi];
}

describe('CloudinaryService', function () {
    describe('upload', function () {
        it('sube un archivo con folder y resource_type auto', function () {
            $captured = null;
            [$service] = cloudServiceMock(function ($file, $options) use (&$captured) {
                $captured = $options;

                return cloudUploaResult();
            });

            $result = $service->upload(UploadedFile::fake()->create('doc.pdf', 10), 'fluxa/pruebas');

            expect($captured)->toMatchArray(['folder' => 'fluxa/pruebas', 'resource_type' => 'auto'])
                ->and($result)->toHaveKey('public_id');
        });

        it('aplica public_id y overwrite cuando se pasa publicId', function () {
            $captured = null;
            [$service] = cloudServiceMock(function ($file, $options) use (&$captured) {
                $captured = $options;

                return cloudUploaResult();
            });

            $service->upload(UploadedFile::fake()->image('foto.jpg'), 'fluxa/x', 'user_1');

            expect($captured['public_id'])->toBe('user_1')
                ->and($captured['overwrite'])->toBeTrue();
        });
    });

    describe('uploadAvatar', function () {
        it('configura la carpeta de avatares y la transformación', function () {
            $captured = null;
            [$service] = cloudServiceMock(function ($file, $options) use (&$captured) {
                $captured = $options;

                return cloudUploaResult();
            });

            $service->uploadAvatar(UploadedFile::fake()->image('avatar.jpg'), '42');

            expect($captured['folder'])->toBe('fluxa/avatares')
                ->and($captured['public_id'])->toBe('user_42')
                ->and($captured['transformation'])->toBeArray();
        });
    });

    describe('uploadProjectMedia', function () {
        it('usa resource_type image para imágenes', function () {
            $captured = null;
            [$service] = cloudServiceMock(function ($file, $options) use (&$captured) {
                $captured = $options;

                return cloudUploaResult();
            });

            $service->uploadProjectMedia(UploadedFile::fake()->image('foto.jpg'), 0);

            expect($captured['resource_type'])->toBe('image');
        });

        it('usa resource_type video para videos', function () {
            $captured = null;
            [$service] = cloudServiceMock(function ($file, $options) use (&$captured) {
                $captured = $options;

                return cloudUploaResult();
            });

            $file = m::mock(UploadedFile::class)->makePartial();
            $file->shouldReceive('getMimeType')->andReturn('video/mp4');

            $service->uploadProjectMedia($file, 0);

            expect($captured['resource_type'])->toBe('video');
        });
    });

    describe('uploadFromUrl', function () {
        it('sube desde una URL externa', function () {
            $capturedUrl = null;
            [$service] = cloudServiceMock(function ($url, $options) use (&$capturedUrl) {
                $capturedUrl = $url;

                return cloudUploaResult();
            });

            $service->uploadFromUrl('https://ejemplo.com/gif.gif', 'fluxa/gifs');

            expect($capturedUrl)->toBe('https://ejemplo.com/gif.gif');
        });
    });

    describe('uploadMessageMedia', function () {
        it('aplica transformación para imágenes', function () {
            $captured = null;
            [$service] = cloudServiceMock(function ($file, $options) use (&$captured) {
                $captured = $options;

                return cloudUploaResult();
            });

            $service->uploadMessageMedia(UploadedFile::fake()->image('foto.jpg'), 'image');

            expect($captured['resource_type'])->toBe('image')
                ->and($captured['folder'])->toBe('fluxa/messages/images')
                ->and($captured['transformation'])->toBeArray();
        });

        it('usa resource_type auto para archivos no imagen', function () {
            $captured = null;
            [$service] = cloudServiceMock(function ($file, $options) use (&$captured) {
                $captured = $options;

                return cloudUploaResult();
            });

            $file = m::mock(UploadedFile::class)->makePartial();
            $file->shouldReceive('getMimeType')->andReturn('application/pdf');

            $service->uploadMessageMedia($file, 'file');

            expect($captured['resource_type'])->toBe('auto')
                ->and($captured)->not->toHaveKey('transformation');
        });
    });

    describe('getImageUrl', function () {
        it('genera la URL de una imagen por public_id', function () {
            [$service] = cloudServiceMock();

            expect($service->getImageUrl('fluxa/x'))->toBe('https://res.cloudinary.com/fluxa/x.jpg');
        });
    });

    describe('delete', function () {
        it('elimina el recurso y retorna true', function () {
            [$service, $uploadApi] = cloudServiceMock();
            $uploadApi->shouldReceive('destroy')->once()->andReturn(cloudUploaResult());

            expect($service->delete('fluxa/x', 'image'))->toBeTrue();
        });

        it('retorna false si la eliminación falla', function () {
            [$service, $uploadApi] = cloudServiceMock();
            $uploadApi->shouldReceive('destroy')->once()->andThrow(new Exception('boom'));

            expect($service->delete('fluxa/x'))->toBeFalse();
        });
    });
});
