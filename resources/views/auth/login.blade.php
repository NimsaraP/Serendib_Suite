<x-layouts.guest>
    <x-slot:title>Login</x-slot:title>
    <x-slot:portalSubtitle>Client & Photographer Portal</x-slot:portalSubtitle>

    <div class="w-full max-w-[1328px] mx-auto flex flex-col lg:flex-row items-stretch gap-8">
        {{-- Left: Visual Editorial Marketing Panel (Hidden on small screens) --}}
        <div class="hidden lg:flex lg:w-[46%] shrink-0">
            <x-auth.visual-panel 
                :image="asset('images/auth/auth_visual_login.jpg')"
                chipText="SerendibSuite"
                card1Title="Made for your moments"
                card1Subtitle="Discover your photographer."
                card2Title="Photography, thoughtfully booked"
                card2Subtitle="Find the right studio for you."
                statementLabel="SerendibSuite"
                statementQuote="Capture moments. Find the photographer who brings them to life."
            />
        </div>

        {{-- Right: Form Card Panel --}}
        <div class="w-full lg:w-[54%] bg-[#FFFDF8] border border-[#E7E1D7] rounded-3xl p-6 sm:p-10 lg:p-12 shadow-sm flex flex-col justify-between">
            <div class="flex flex-col gap-8">
                {{-- Header / Introduction --}}
                <div class="flex flex-col gap-3">
                    <span class="text-lg font-semibold tracking-tight text-[#262626]">SerendibSuite</span>
                    <h1 class="text-3xl sm:text-4xl font-medium text-[#262626] leading-tight font-display">
                        Welcome to SerendibSuite
                    </h1>
                    <p class="text-sm sm:text-base text-[#737373] leading-relaxed">
                        Discover photographers, explore studios, and book your perfect photography experience.
                    </p>
                </div>

                {{-- Status / General Error Alert --}}
                @if (session('status'))
                    <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Login Form --}}
                <form action="{{ url('/login') }}" method="POST" class="flex flex-col gap-5">
                    @csrf

                    <x-auth.form-input 
                        label="Email"
                        name="email"
                        type="email"
                        placeholder="Enter your email address"
                        :required="true"
                        autocomplete="email"
                        autofocus
                    />

                    <x-auth.form-input 
                        label="Password"
                        name="password"
                        type="password"
                        placeholder="Enter your password"
                        :required="true"
                        autocomplete="current-password"
                    />

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between text-sm pt-1">
                        <label class="inline-flex items-center gap-2.5 cursor-pointer text-[#737373] hover:text-[#262626] transition-colors select-none">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                id="remember" 
                                class="w-4 h-4 rounded border-[#E6DED2] text-[#262626] focus:ring-1 focus:ring-[#262626] cursor-pointer"
                                {{ old('remember') ? 'checked' : '' }}
                            />
                            <span>Remember me</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit" 
                        class="w-full h-14 bg-[#262626] hover:bg-[#1a1a1a] text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow active:scale-[0.99] flex items-center justify-center cursor-pointer mt-2"
                    >
                        Login
                    </button>
                </form>
            </div>

            {{-- Footer Divider & Alternate Registration Links --}}
            <div class="pt-8 mt-8 border-t border-[#E7E1D7] flex flex-col gap-4">
                <span class="text-sm font-medium text-[#737373]">
                    New here? Create an account.
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <a 
                        href="{{ route('register.photographer') }}"
                        class="w-full h-12 px-4 rounded-lg border border-[#E6DED2] hover:border-[#262626] bg-white text-[#262626] text-xs sm:text-sm font-semibold transition-all flex items-center justify-center text-center shadow-2xs hover:bg-[#FFFDFA]"
                    >
                        Register as Photographer
                    </a>

                    <a 
                        href="{{ route('register.client') }}"
                        class="w-full h-12 px-4 rounded-lg bg-[#FFE3CF] hover:bg-[#fedbc1] text-[#262626] text-xs sm:text-sm font-semibold transition-all flex items-center justify-center text-center shadow-2xs"
                    >
                        Register as Client
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>

