@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'origin-top-left left-0';
        break;
    case 'top':
        $alignmentClasses = 'origin-top';
        break;
    case 'right':
    default:
        $alignmentClasses = 'origin-top-right right-0';
        break;
}

if ($width === '48') {
    $width = 'w-48';
}
@endphp

<div class="relative" data-dropdown>
    <div data-dropdown-trigger>
        {{ $trigger }}
    </div>

    <div data-dropdown-menu
         class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }} hidden"
         style="display: none;">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
