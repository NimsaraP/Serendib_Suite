@props([
    'label',
    'name',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'value' => null,
    'autocomplete' => null,
])

@php
    $isPassword = ($type === 'password');
    $inputId = $name . '_' . Str::random(4);
    $hasError = $errors->has($name);
@endphp

<div class="flex flex-col gap-2 w-full" x-data="{ show: false }">
    <label for="{{ $inputId }}" class="text-sm font-medium text-[#262626] font-sans">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative w-full">
        <input 
            id="{{ $inputId }}"
            name="{{ $name }}"
            :type="{{ $isPassword ? "show ? 'text' : 'password'" : "'$type'" }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            {{ $attributes->merge([
                'class' => 'w-full h-14 px-4 bg-white rounded-lg border text-sm text-[#262626] placeholder-[#737373] transition-colors focus:outline-none focus:ring-2 focus:ring-[#262626] focus:border-[#262626] font-sans ' . 
                ($hasError ? 'border-red-500 focus:ring-red-500' : 'border-[#E6DED2]') .
                ($isPassword ? ' pr-12' : '')
            ]) }}
        />

        @if($isPassword)
            <button 
                type="button" 
                @click="show = !show" 
                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#737373] hover:text-[#262626] focus:outline-none p-1 transition-colors"
                title="Toggle password visibility"
                aria-label="Toggle password visibility"
            >
                {{-- Eye open icon --}}
                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{-- Eye closed icon --}}
                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
            </button>
        @endif
    </div>

    @error($name)
        <p class="text-xs text-red-600 font-medium font-sans mt-0.5">{{ $message }}</p>
    @enderror
</div>

