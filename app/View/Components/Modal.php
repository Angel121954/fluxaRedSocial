<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public string $id;
    public ?string $title;
    public ?string $subtitle;
    public ?string $maxWidth;
    public bool $hideHeader;
    public bool $hideClose;

    public function __construct(
        string $id,
        ?string $title = null,
        ?string $subtitle = null,
        ?string $maxWidth = null,
        bool $hideHeader = false,
        bool $hideClose = false,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->maxWidth = $maxWidth;
        $this->hideHeader = $hideHeader;
        $this->hideClose = $hideClose;
    }

    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
