<div class="mz-banner-spice pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
    <style>
        .mz-banner-spice .float-a,
        .mz-banner-spice .float-b,
        .mz-banner-spice .float-c,
        .mz-banner-spice .float-d,
        .mz-banner-spice .pulse,
        .mz-banner-spice .drift {
            transform-box: fill-box;
            transform-origin: center;
            will-change: transform;
        }
        .mz-banner-spice .float-a { animation: mz-float-a 8.5s ease-in-out infinite; }
        .mz-banner-spice .float-b { animation: mz-float-b 10s ease-in-out infinite; }
        .mz-banner-spice .float-c { animation: mz-float-c 12s ease-in-out infinite; }
        .mz-banner-spice .float-d { animation: mz-float-d 9s ease-in-out infinite 0.8s; }
        .mz-banner-spice .pulse { animation: mz-pulse 4.5s ease-in-out infinite; }
        .mz-banner-spice .drift { animation: mz-drift 14s linear infinite; }
        @keyframes mz-float-a {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-12px, -18px) rotate(-7deg); }
        }
        @keyframes mz-float-b {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(14px, -12px) rotate(8deg); }
        }
        @keyframes mz-float-c {
            0%, 100% { transform: translate(0, 0) rotate(-18deg); }
            50% { transform: translate(-8px, 14px) rotate(-28deg); }
        }
        @keyframes mz-float-d {
            0%, 100% { transform: translate(0, 0) rotate(24deg); }
            50% { transform: translate(10px, -16px) rotate(32deg); }
        }
        @keyframes mz-pulse {
            0%, 100% { transform: translateY(0) scale(1); opacity: 0.4; }
            50% { transform: translateY(10px) scale(1.08); opacity: 0.7; }
        }
        @keyframes mz-drift {
            0% { transform: translate(0, 0); opacity: 0.15; }
            40% { opacity: 0.45; }
            100% { transform: translate(-40px, -80px); opacity: 0; }
        }
        @media (prefers-reduced-motion: reduce) {
            .mz-banner-spice .float-a,
            .mz-banner-spice .float-b,
            .mz-banner-spice .float-c,
            .mz-banner-spice .float-d,
            .mz-banner-spice .pulse,
            .mz-banner-spice .drift {
                animation: none !important;
            }
        }
    </style>

    <svg class="absolute inset-0 h-full w-full" viewBox="0 0 1200 640" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMaxYMid slice">
        <defs>
            <symbol id="mz-chili-up" viewBox="0 0 90 130">
                <path d="M34 26C18 34 10 58 16 86c5 24 22 40 34 42 2.4.4 4-2.2 3-4.4-8-18-6-42 2-62 10-14 8-28-6-34-5-2-11-3-15-1.6Z" fill="#E25A32"/>
                <path d="M49 30c8 8 12 28 8 52-3 18-12 34-22 44 14-4 28-22 32-42 5-24-2-46-18-54Z" fill="#F0B429"/>
                <path d="M40 24c-2-12 8-22 18-18 3 1.2 2.6 5.2-.4 6-6 1.6-10 6-12 12-1.2 3.4-5.4 2.8-5.6 0Z" fill="#2A5C38"/>
            </symbol>
            <symbol id="mz-chili-curve" viewBox="0 0 130 90">
                <path d="M18 38c-8 14-2 36 22 44 28 10 62-2 78-22 12-16 6-32-14-28-16 3-30 16-46 14-12-1-18-10-24-16-4-4-10-2-16 8Z" fill="#D4532A"/>
                <path d="M92 34c14 4 22 18 14 30-10 16-36 26-58 20 22 2 46-8 56-22 8-12 2-24-12-28Z" fill="#E89A3A"/>
            </symbol>
            <symbol id="mz-mango" viewBox="0 0 110 110">
                <path d="M48 18c28 2 48 28 42 56-6 28-34 42-56 32C12 94 8 58 22 36 30 24 38 17 48 18Z" fill="#CA9636"/>
                <path d="M62 22c16 8 28 26 24 46-4 16-16 28-30 32 18-6 32-24 36-42 4-18-6-32-20-38-4-2-8 0-10 2Z" fill="#E8C15A"/>
                <path d="M58 8c18 2 32 16 28 32-2 6-10 4-12-1-4-12-14-18-26-20-6-1-4-12 10-11Z" fill="#2A5C38"/>
            </symbol>
            <symbol id="mz-drop" viewBox="0 0 60 80">
                <path d="M30 6c16 24 22 40 14 56-8 16-28 16-36 0C0 46 10 28 30 6Z" fill="#2A5C38"/>
                <path d="M24 28c6-8 12-6 10 6-1 8-8 14-14 12-2-8 0-12 4-18Z" fill="#4A7A55" opacity="0.7"/>
            </symbol>
        </defs>

        <path d="M1180 -40c-220 40-360 180-340 360 20 190 200 300 380 240" stroke="#1F4A2C" stroke-width="28" opacity="0.35" fill="none" stroke-linecap="round"/>
        <path d="M1220 80c-160 30-260 140-240 260 18 130 150 210 300 170" stroke="#CA9636" stroke-width="10" opacity="0.2" fill="none" stroke-linecap="round"/>

        <g class="float-a">
            <use href="#mz-chili-up" x="780" y="90" width="150" height="216" opacity="0.55"/>
        </g>
        <g class="float-b">
            <use href="#mz-chili-curve" x="930" y="280" width="200" height="138" opacity="0.5"/>
        </g>
        <g class="float-b" style="animation-delay: -3s;">
            <use href="#mz-mango" x="980" y="60" width="150" height="150" opacity="0.48"/>
        </g>
        <g class="pulse">
            <use href="#mz-drop" x="890" y="230" width="70" height="94"/>
        </g>
        <g class="float-d">
            <use href="#mz-chili-up" x="1120" y="360" width="90" height="130" opacity="0.28"/>
        </g>
        <g class="float-c">
            <use href="#mz-mango" x="740" y="400" width="80" height="80" opacity="0.22"/>
        </g>
        <g class="float-a" style="animation-delay: -5s; animation-duration: 11s;">
            <use href="#mz-chili-curve" x="40" y="470" width="110" height="76" opacity="0.2"/>
        </g>
        <g class="pulse" style="animation-delay: -1.6s;">
            <use href="#mz-drop" x="160" y="40" width="36" height="48" opacity="0.18"/>
        </g>

        <circle class="drift" cx="860" cy="520" r="3.2" fill="#CA9636" style="animation-delay: 0s;"/>
        <circle class="drift" cx="1020" cy="500" r="2.4" fill="#E25A32" style="animation-delay: -2s; animation-duration: 11s;"/>
        <circle class="drift" cx="940" cy="560" r="2" fill="#F4F1EA" style="animation-delay: -4s; animation-duration: 16s;"/>
        <circle class="drift" cx="1100" cy="480" r="2.8" fill="#CA9636" style="animation-delay: -6s; animation-duration: 13s;"/>
        <circle class="drift" cx="800" cy="540" r="1.8" fill="#E25A32" style="animation-delay: -1s; animation-duration: 15s;"/>
        <circle class="drift" cx="1180" cy="420" r="2.2" fill="#E8C15A" style="animation-delay: -8s; animation-duration: 12s;"/>
        <circle class="drift" cx="200" cy="580" r="2" fill="#CA9636" style="animation-delay: -3.5s; animation-duration: 18s;"/>
    </svg>
</div>
