@props([
    'image',
    'chipText' => 'SerendibSuite',
    'card1Title',
    'card1Subtitle',
    'card2Title',
    'card2Subtitle',
    'statementLabel' => 'SerendibSuite',
    'statementQuote',
])

<div class="relative w-full h-full min-h-[620px] rounded-[32px] overflow-hidden flex flex-col justify-between p-8 bg-[#F5F1E8] shadow-sm">
    {{-- Background Image with Subtle Warm Vignette --}}
    <img 
        src="{{ $image }}" 
        alt="SerendibSuite editorial preview" 
        class="absolute inset-0 w-full h-full object-cover object-center"
    />
    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent pointer-events-none"></div>

    {{-- Top Row: Brand Chip and Floating Tagline Card --}}
    <div class="relative z-10 flex flex-col gap-4 items-start">
        <div class="inline-flex items-center px-3.5 py-2 rounded-full bg-white/85 backdrop-blur-md border border-white/60 shadow-sm">
            <span class="text-xs font-semibold tracking-wide text-[#262626] font-sans">{{ $chipText }}</span>
        </div>

        @if(isset($card1Title) && isset($card1Subtitle))
            <div class="max-w-[260px] p-4 rounded-2xl bg-white/85 backdrop-blur-md border border-white/60 shadow-md flex flex-col gap-1.5 transition-transform hover:-translate-y-0.5">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#737373] font-sans">{{ $card1Title }}</span>
                <span class="text-sm font-semibold text-[#262626] leading-snug font-sans">{{ $card1Subtitle }}</span>
            </div>
        @endif
    </div>

    {{-- Middle Floating Card (Offset Right) --}}
    @if(isset($card2Title) && isset($card2Subtitle))
        <div class="relative z-10 self-end max-w-[260px] p-4 rounded-2xl bg-white/85 backdrop-blur-md border border-white/60 shadow-md flex flex-col gap-1.5 transition-transform hover:-translate-y-0.5 my-auto">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-[#737373] font-sans">{{ $card2Title }}</span>
            <span class="text-sm font-semibold text-[#262626] leading-snug font-sans">{{ $card2Subtitle }}</span>
        </div>
    @endif

    {{-- Bottom Hero Statement Card --}}
    @if(isset($statementQuote))
        <div class="relative z-10 max-w-[420px] p-6 rounded-[20px] bg-white/90 backdrop-blur-md border border-white/70 shadow-lg flex flex-col gap-2">
            <span class="text-xs font-semibold tracking-wider uppercase text-[#737373] font-sans">{{ $statementLabel }}</span>
            <p class="text-xl sm:text-2xl font-medium text-[#262626] leading-snug font-display">
                {{ $statementQuote }}
            </p>
        </div>
    @endif
</div>

