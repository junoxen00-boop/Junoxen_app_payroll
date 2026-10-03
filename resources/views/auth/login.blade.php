<x-guest-layout>

<style>
    .junoxen-login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        background:
            radial-gradient(circle at 20% 20%, rgba(79,70,229,.18), transparent 30%),
            radial-gradient(circle at 80% 80%, rgba(16,185,129,.12), transparent 30%),
            #0b1220;
        overflow: hidden;
    }

    .junoxen-login-card {
        width: 100%;
        max-width: 1050px;
        min-height: 620px;
        display: grid;
        grid-template-columns: 1.05fr .95fr;
        background: rgba(255,255,255,.97);
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(0,0,0,.35);
        position: relative;
    }

    /* LEFT SIDE */

    .junoxen-animation-side {
        position: relative;
        overflow: hidden;
        background: linear-gradient(145deg, #172554, #312e81, #4f46e5);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        padding: 40px;
    }

    .logo-box {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.logo-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

    .junoxen-logo {
        position: absolute;
        top: 35px;
        left: 40px;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 5;
    }

    .junoxen-logo-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #4f46e5;
        font-size: 22px;
        font-weight: 800;
        box-shadow: 0 10px 25px rgba(0,0,0,.18);
    }

    .junoxen-logo-icon img {
    width: 70%;
    height: 70%;
    object-fit: contain;
}

    .junoxen-logo-text {
        font-size: 23px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .junoxen-subtitle {
        margin-top: 6px;
        font-size: 13px;
        opacity: .75;
    }

    .junoxen-heading {
        text-align: center;
        margin-top: 55px;
        z-index: 4;
    }

    .junoxen-heading h1 {
        font-size: 38px;
        font-weight: 800;
        margin: 0;
    }

    .junoxen-heading p {
        margin-top: 10px;
        opacity: .8;
        font-size: 15px;
    }

    /* Animated background circles */

    .bubble {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        animation: floatBubble 7s ease-in-out infinite;
    }

    .bubble.one {
        width: 180px;
        height: 180px;
        top: 10%;
        right: -60px;
    }

    .bubble.two {
        width: 120px;
        height: 120px;
        bottom: 8%;
        left: -40px;
        animation-delay: 1.5s;
    }

    .bubble.three {
        width: 70px;
        height: 70px;
        top: 50%;
        left: 15%;
        animation-delay: 3s;
    }

    @keyframes floatBubble {
        0%,100% {
            transform: translateY(0) scale(1);
        }

        50% {
            transform: translateY(-25px) scale(1.08);
        }
    }

    /* Animated employee */

    .employee-animation {
        position: relative;
        width: 300px;
        height: 270px;
        margin-top: 35px;
        z-index: 3;
    }

.character {
    position: absolute;
    left: -190px;
    bottom: 25px;
    width: 180px;
    height: 260px;
    z-index: 3;
    animation: characterEnter 1.6s ease-out forwards;
}

.login-character-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center bottom;
    display: block;
}
 

@keyframes characterEnter {
    0% {
        left: -190px;
    }

    25% {
        left: -120px;
    }

    50% {
        left: -50px;
    }

    75% {
        left: 20px;
    }

    100% {
        left: 70px;
    }
}

    .head {
        position: absolute;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #f3c39b;
        left: 21px;
        top: 0;
        box-shadow: inset -5px -4px 0 rgba(0,0,0,.08);
    }

    .hair {
        position: absolute;
        width: 52px;
        height: 22px;
        border-radius: 50% 50% 35% 35%;
        background: #252525;
        left: 19px;
        top: -4px;
        z-index: 2;
    }

    .body {
        position: absolute;
        width: 62px;
        height: 70px;
        border-radius: 18px 18px 10px 10px;
        background: white;
        left: 14px;
        top: 45px;
        box-shadow: 0 8px 0 rgba(0,0,0,.08);
    }

    .shirt {
        position: absolute;
        width: 30px;
        height: 70px;
        background: #4f46e5;
        left: 16px;
        top: 0;
        border-radius: 5px;
    }

    .arm {
        position: absolute;
        width: 15px;
        height: 58px;
        background: #f3c39b;
        border-radius: 12px;
        top: 52px;
    }

    .arm.left {
        left: 3px;
        transform: rotate(18deg);
    }

    .arm.right {
        right: 3px;
        transform: rotate(-25deg);
    }

    .leg {
        position: absolute;
        width: 19px;
        height: 58px;
        background: #1f2937;
        border-radius: 10px;
        top: 105px;
    }

    .leg.left {
        left: 27px;
        transform: rotate(7deg);
    }

    .leg.right {
        left: 48px;
        transform: rotate(-7deg);
    }

    .character .leg.left {
    animation: legLeftWalk 2.8s ease-in-out infinite;
    transform-origin: top center;
}

.character .leg.right {
    animation: legRightWalk 2.8s ease-in-out infinite;
    transform-origin: top center;
}

@keyframes legLeftWalk {

    0%, 100% {
        transform: rotate(7deg);
    }

    20% {
        transform: rotate(-12deg);
    }

    40% {
        transform: rotate(12deg);
    }

    55% {
        transform: rotate(-5deg);
    }

    65%, 85% {
        transform: rotate(7deg);
    }
}

@keyframes legRightWalk {

    0%, 100% {
        transform: rotate(-7deg);
    }

    20% {
        transform: rotate(12deg);
    }

    40% {
        transform: rotate(-12deg);
    }

    55% {
        transform: rotate(5deg);
    }

    65%, 85% {
        transform: rotate(-7deg);
    }
}

    .shoe {
        position: absolute;
        width: 30px;
        height: 12px;
        background: white;
        border-radius: 10px;
        top: 155px;
    }

    .shoe.left {
        left: 15px;
    }

    .shoe.right {
        left: 45px;
    }

    .character .arm.left {
    animation: armLeftWalk 2.8s ease-in-out infinite;
    transform-origin: top center;
}

.character .arm.right {
    animation: armRightWalk 2.8s ease-in-out infinite;
    transform-origin: top center;
}

@keyframes armLeftWalk {

    0%, 100% {
        transform: rotate(18deg);
    }

    20% {
        transform: rotate(-8deg);
    }

    40% {
        transform: rotate(20deg);
    }

    55% {
        transform: rotate(5deg);
    }

    65%, 85% {
        transform: rotate(35deg);
    }
}

@keyframes armRightWalk {

    0%, 100% {
        transform: rotate(-25deg);
    }

    20% {
        transform: rotate(-40deg);
    }

    40% {
        transform: rotate(-15deg);
    }

    55% {
        transform: rotate(-30deg);
    }

    65%, 85% {
        transform: rotate(-5deg);
    }
}

    /* Briefcase */

   .briefcase {
    position: absolute;
    width: 55px;
    height: 40px;
    background: #92400e;
    border-radius: 6px;
    left: 15px;
    bottom: 45px;
    transform: rotate(-8deg);
    animation: briefcaseMove 2.8s ease-in-out infinite;
    box-shadow: 0 7px 15px rgba(0,0,0,.25);
    transform-origin: bottom center;
}

    .briefcase:before {
        content: "";
        position: absolute;
        width: 20px;
        height: 8px;
       border: 4px solid #78350f;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        left: 14px;
        top: -10px;
    }

    .briefcase:after {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 55px;
    height: 13px;
    background: #a65314;
    border-radius: 6px 6px 2px 2px;
    transform-origin: bottom center;
    animation: briefcaseLid 2.8s ease-in-out infinite;
    z-index: 2;
}

 @keyframes briefcaseMove {
    0%, 30% {
        transform: rotate(-8deg) translate(0, 0);
    }

    40% {
        transform: rotate(-3deg) translate(3px, -2px);
    }

    50% {
        transform: rotate(3deg) translate(5px, -5px);
    }

    60% {
        transform: rotate(0deg) translate(2px, -2px);
    }

    70% {
        transform: rotate(-3deg) translate(0, 0);
    }

    100% {
        transform: rotate(-8deg) translate(0, 0);
    }
}

 @keyframes briefcaseLid {

    0%, 35% {
        transform: rotateX(0deg);
    }

    45% {
        transform: rotateX(-15deg);
    }

    55% {
        transform: rotateX(-35deg);
    }

    70% {
        transform: rotateX(-15deg);
    }

    80%, 100% {
        transform: rotateX(0deg);
    }
}

    .ground {
        position: absolute;
        bottom: 35px;
        left: 30px;
        width: 240px;
        height: 12px;
        border-radius: 50%;
        background: rgba(0,0,0,.18);
        filter: blur(3px);
    }

    /* RIGHT SIDE */

    .junoxen-form-side {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 55px;
        background: white;
    }

.login-form-container {
    width: 100%;
    max-width: 400px;
    opacity: 0;
}

.login-form-container.show-login {
    animation: formEnter 1s ease forwards;
}

    @keyframes formEnter {
        from {
            opacity: 0;
            transform: translateX(35px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .login-title {
        font-size: 34px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 8px;
    }

    .login-description {
        color: #6b7280;
        margin-bottom: 35px;
    }

    .login-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .login-input {
        width: 100%;
        height: 50px;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        padding: 0 15px;
        outline: none;
        transition: all .25s ease;
        background: #f9fafb;
    }

    .login-input:focus {
        background: white;
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79,70,229,.12);
        transform: translateY(-1px);
    }

    .password-wrapper {
    position: relative;
}

.password-wrapper .login-input {
    padding-right: 50px;
}

.password-toggle {
    position: absolute;
    top: 50%;
    right: 14px;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: #6b7280;
    cursor: pointer;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle:hover {
    color: #4f46e5;
}

.password-toggle:focus {
    outline: none;
}

    .login-button {
        width: 100%;
        height: 52px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: white;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition: all .25s ease;
        box-shadow: 0 10px 20px rgba(79,70,229,.25);
    }

    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 25px rgba(79,70,229,.35);
    }

    .login-button:active {
        transform: translateY(0);
    }

    .remember-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 18px;
        margin-bottom: 25px;
    }

    .remember-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #6b7280;
    }

    .login-footer {
        text-align: center;
        color: #9ca3af;
        font-size: 12px;
        margin-top: 28px;
    }

    @media (max-width: 850px) {

        .junoxen-login-card {
            grid-template-columns: 1fr;
            max-width: 520px;
        }

        .junoxen-animation-side {
            min-height: 330px;
            padding: 25px;
        }

        .junoxen-heading {
            margin-top: 45px;
        }

        .junoxen-heading h1 {
            font-size: 28px;
        }

        .employee-animation {
            transform: scale(.75);
            margin-top: 5px;
        }

        .junoxen-form-side {
            padding: 40px 25px;
        }
    }

    @media (max-width: 500px) {

        .junoxen-login-page {
            padding: 0;
        }

        .junoxen-login-card {
            min-height: 100vh;
            border-radius: 0;
        }

        .junoxen-animation-side {
            min-height: 300px;
        }

        .junoxen-logo {
            top: 22px;
            left: 22px;
        }

        .junoxen-form-side {
            padding: 35px 22px;
        }

        .login-title {
            font-size: 29px;
        }
    }
</style>


<div class="junoxen-login-page">

    <div class="junoxen-login-card">

        <!-- LEFT ANIMATED SECTION -->
        <div class="junoxen-animation-side">

            <div class="bubble one"></div>
            <div class="bubble two"></div>
            <div class="bubble three"></div>

            <div class="junoxen-logo">

                <div class="logo-box">
   <img src="{{ asset('images/logo.jpg') }}" alt="JUNOXEN Logo">
</div>

                <div>
                    <div class="junoxen-logo-text">
                        JUNOXEN
                    </div>

                    <div class="junoxen-subtitle">
                        Employee Management
                    </div>
                </div>

            </div>


            <div class="junoxen-heading">

                <h1>
                    Welcome Back
                </h1>

                <p>
                    Manage your work. Stay connected.
                </p>

            </div>


         <div class="employee-animation">

    <div class="ground"></div>

    <div class="character">
    <img
        id="loginCharacterFrame"
        src="{{ asset('images/login-character/char_01.png') }}"
        alt="Employee"
        class="login-character-image"
    >
</div>

</div>

        </div>


        <!-- LOGIN SECTION -->
        <div class="junoxen-form-side">

            <div class="login-form-container">

                <div class="login-title">
                    Sign In
                </div>

                <div class="login-description">
                    Enter your credentials to access your dashboard.
                </div>


                <!-- Session Status -->
                <x-auth-session-status
                    class="mb-4"
                    :status="session('status')"
                />


                <form method="POST" action="{{ route('login') }}">

                    @csrf


                    <!-- Email -->
                    <div>

                        <label
                            for="email"
                            class="login-label">

                            Email Address

                        </label>

                        <input
                            id="email"
                            class="login-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                        >

                        @error('email')

                            <div class="text-sm text-red-600 mt-2">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- Password -->
                    <div style="margin-top:20px;">

                        <label
                            for="password"
                            class="login-label">

                            Password

                        </label>

                     <div class="password-wrapper">
    <input
        id="password"
        class="login-input"
        type="password"
        name="password"
        required
        autocomplete="current-password"
    >

    <button
        type="button"
        class="password-toggle"
        id="password-toggle"
        aria-label="Show password"
        title="Show password"
    >
        <svg
            id="password-eye-icon"
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
    </button>
</div>
                        @error('password')

                            <div class="text-sm text-red-600 mt-2">
                                {{ $message }}
                            </div>

                        @enderror

                        @if (Route::has('password.request'))
    <div style="text-align:right; margin-top:8px;">
        <a href="{{ route('password.request') }}"
           style="font-size:14px; color:#4f46e5; text-decoration:none;">
            Forgot your password?
        </a>
    </div>
@endif

                    </div>


                    <!-- Remember -->
                    <div class="remember-row">

                        <label class="remember-label">

                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                style="accent-color:#4f46e5;"
                            >

                            <span>
                                Remember me
                            </span>

                        </label>

                    </div>


                    <!-- Login Button -->
                    <button
                        type="submit"
                        class="login-button">

                        Sign In

                    </button>


                </form>


                <div class="login-footer">
                    © {{ date('Y') }} JUNOXEN Employee Management
                </div>

            </div>

        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        const passwordEyeIcon = document.getElementById('password-eye-icon');

        if (!passwordInput || !passwordToggle || !passwordEyeIcon) {
            return;
        }

        passwordToggle.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';

            passwordToggle.setAttribute(
                'aria-label',
                isPassword ? 'Hide password' : 'Show password'
            );

            passwordToggle.setAttribute(
                'title',
                isPassword ? 'Hide password' : 'Show password'
            );

            if (isPassword) {
                passwordEyeIcon.innerHTML = `
                    <path d="M3 3l18 18"></path>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                    <path d="M9.9 4.2A10.9 10.9 0 0 1 12 4c7 0 10 8 10 8a17.5 17.5 0 0 1-3.1 4.3"></path>
                    <path d="M6.6 6.6C3.6 8.5 2 12 2 12s3.5 8 10 8a9.7 9.7 0 0 0 3.4-.6"></path>
                `;
            } else {
                passwordEyeIcon.innerHTML = `
                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            }
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const character = document.getElementById('loginCharacterFrame');
        const loginForm = document.querySelector('.login-form-container');

        if (!character) {
            return;
        }

        const frames = [
            '{{ asset('images/login-character/char_01.png') }}',
            '{{ asset('images/login-character/char_02.png') }}',
            '{{ asset('images/login-character/char_03.png') }}',
            '{{ asset('images/login-character/char_04.png') }}',
            '{{ asset('images/login-character/char_05.png') }}',
            '{{ asset('images/login-character/char_06.png') }}',
            '{{ asset('images/login-character/char_07.png') }}',
            '{{ asset('images/login-character/char_08.png') }}',
            '{{ asset('images/login-character/char_09.png') }}',
            '{{ asset('images/login-character/char_10.png') }}',
            '{{ asset('images/login-character/char_11.png') }}',
            '{{ asset('images/login-character/char_12.png') }}'
        ];

        // Preload all images
        frames.forEach(function (src) {
            const img = new Image();
            img.src = src;
        });

        /*
         * 1. WALKING
         * Keep the current working walking animation.
         */
        let currentFrame = 0;

        function playWalkingFrame() {
            character.src = frames[currentFrame];

            currentFrame++;

            if (currentFrame >= 4) {
                currentFrame = 0;
            }
        }

        playWalkingFrame();

        const walkingTimer = setInterval(playWalkingFrame, 180);

        /*
         * 2. STOP WALKING
         * char_05 = standing with briefcase
         */
        setTimeout(function () {
            clearInterval(walkingTimer);
            character.src = frames[4];
        }, 1600);

        /*
         * 3. OPEN / USE BRIEFCASE
         */
        setTimeout(function () {
            character.src = frames[5]; // char_06
        }, 2000);

        setTimeout(function () {
            character.src = frames[6]; // char_07
        }, 2400);

        setTimeout(function () {
            character.src = frames[7]; // char_08
        }, 2800);

        setTimeout(function () {
            character.src = frames[8]; // char_09
        }, 3200);

        /*
         * 4. SHOW LOGIN FORM
         */
        setTimeout(function () {
            if (loginForm) {
                loginForm.classList.add('show-login');
            }
        }, 3300);

        /*
         * 5. FINAL STANDING / GESTURE
         */
        setTimeout(function () {
            character.src = frames[9]; // char_10
        }, 3700);

        setTimeout(function () {
            character.src = frames[10]; // char_11
        }, 4100);

        /*
         * 6. FINAL POSE
         * char_12 stays visible.
         */
        setTimeout(function () {
            character.src = frames[11]; // char_12
        }, 4500);
    });
</script>

</x-guest-layout>