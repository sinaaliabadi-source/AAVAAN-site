@props([
    'name' => 'field',
    'selected' => '',
    'required' => false,
    'id' => null,
    'placeholder' => 'انتخاب حوزه فعالیت...',
])

@php
    // آیا مقدار انتخاب‌شده در فهرست منبع واحد وجود دارد؟ (برای نمایش مقادیر قدیمی)
    $allFields = collect(config('art_fields'))->flatten();
    $isLegacy = filled($selected) && ! $allFields->contains($selected);
@endphp

<select name="{{ $name }}"
        @if($id) id="{{ $id }}" @endif
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-control art-fields-select']) }}>
    <option value="">{{ $placeholder }}</option>

    @if($isLegacy)
        {{-- مقدار قبلیِ خارج از فهرست فعلی تا از دست نرود --}}
        <option value="{{ $selected }}" selected>{{ $selected }}</option>
    @endif

    @foreach(config('art_fields') as $category => $fields)
        <optgroup label="{{ $category }}">
            @foreach($fields as $field)
                <option value="{{ $field }}" {{ (string) $selected === (string) $field ? 'selected' : '' }}>
                    {{ $field }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>
