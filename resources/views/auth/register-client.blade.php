<x-layouts.guest>
    <x-slot:title>Client Registration</x-slot:title>
    <x-slot:portalSubtitle>Client & Photographer Portal</x-slot:portalSubtitle>

    <div class="w-full max-w-[1328px] mx-auto flex flex-col lg:flex-row items-stretch gap-8">
        {{-- Left: Visual Editorial Marketing Panel --}}
        <div class="hidden lg:flex lg:w-[46%] shrink-0">
            <x-auth.visual-panel 
                :image="asset('images/auth/auth_visual_client.jpg')"
                chipText="SerendibSuite"
                card1Title="Celebrate your story"
                card1Subtitle="Find a photographer who gets you."
                card2Title="From moments to memories"
                card2Subtitle="Discover your perfect photography experience."
                statementLabel="SerendibSuite"
                statementQuote="Your moments deserve to be remembered beautifully."
            />
        </div>

        {{-- Right: Registration Form Panel --}}
        <div class="w-full lg:w-[54%] bg-[#FFFDF8] border border-[#E7E1D7] rounded-3xl p-6 sm:p-10 lg:p-12 shadow-sm flex flex-col justify-between">
            <div class="flex flex-col gap-6">
                {{-- Header / Introduction --}}
                <div class="flex flex-col gap-3">
                    <span class="text-lg font-semibold tracking-tight text-[#262626]">SerendibSuite</span>
                    <h1 class="text-3xl sm:text-4xl font-medium text-[#262626] leading-tight font-display">
                        Create your client account
                    </h1>
                    <p class="text-sm sm:text-base text-[#737373] leading-relaxed">
                        Discover talented photographers and find the perfect package for your special moments.
                    </p>
                </div>

                {{-- Form errors alert --}}
                @if ($errors->any())
                    <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Registration Form --}}
                <form action="{{ url('/register/client') }}" method="POST" class="flex flex-col gap-4">
                    @csrf

                    {{-- Name Fields (First Name & Last Name) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-auth.form-input 
                            label="First Name"
                            name="first_name"
                            placeholder="Enter your first name"
                            :required="true"
                            autocomplete="given-name"
                            autofocus
                        />

                        <x-auth.form-input 
                            label="Last Name"
                            name="last_name"
                            placeholder="Enter your last name"
                            :required="true"
                            autocomplete="family-name"
                        />
                    </div>

                    {{-- Email --}}
                    <x-auth.form-input 
                        label="Email"
                        name="email"
                        type="email"
                        placeholder="Enter your email address"
                        :required="true"
                        autocomplete="email"
                    />

                    {{-- Phone --}}
                    <x-auth.form-input 
                        label="Phone"
                        name="phone"
                        type="tel"
                        placeholder="Enter your phone number"
                        autocomplete="tel"
                    />

                    {{-- Passwords --}}
                    <x-auth.form-input 
                        label="Password"
                        name="password"
                        type="password"
                        placeholder="Enter your password"
                        :required="true"
                        autocomplete="new-password"
                    />

                    <x-auth.form-input 
                        label="Confirm Password"
                        name="password_confirmation"
                        type="password"
                        placeholder="Confirm your password"
                        :required="true"
                        autocomplete="new-password"
                    />

                    {{-- Submit Button --}}
                    <button 
                        type="submit" 
                        class="w-full h-14 bg-[#F5864E] hover:bg-[#e0753d] text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow active:scale-[0.99] flex items-center justify-center cursor-pointer mt-2"
                    >
                        Create Client Account
                    </button>
                </form>
            </div>

            {{-- Footer Divider & Login Link --}}
            <div class="pt-6 mt-6 border-t border-[#E7E1D7] flex items-center justify-between text-sm">
                <span class="text-[#737373]">
                    Already have an account?
                </span>
                <a href="{{ route('login') }}" class="font-semibold text-[#262626] hover:text-[#F5864E] transition-colors">
                    Login
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>

