@props([])

<div {{ $attributes->merge(['class' => 'mt-4 flex flex-wrap items-center justify-between gap-3']) }}>
    <div class="flex items-center gap-2">
        {{ $left }}
    </div>
    <div class="flex items-center gap-2">
        {{ $right }}
    </div>
</div>