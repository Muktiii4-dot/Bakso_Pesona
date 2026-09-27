@extends('layouts.app')
@section('title', 'Bakso Pesona - Bakso Lezat, Rasa Berkesan')
@section('content')

    {{-- Hero --}}
    <section class="relative overflow-idden bg-[#7F1D1D] pt-32 text-white">

        <div class="mx-auto grid min-h-[720px] max-w-7xl items-center gap-12 px-6 py-20 lg:grid-cols-2 lg:px-8">

            {{-- Hero Content --}}
            <div>
                <span class="inline-flex rounded-full border border-[#E87532]/40 bg-[#E87532]/10 px-4 py-2 text-sm font-semibold text-[#E87532]">
                    Rasa Lokal, Pesona Istimewa
                </span>

                <h1 class="mt-6 max-w-3xl text-5xl font-black leading-tight tracking-tight sm:text-6xl lg:text-7xl">
                    Bakso Lezat, 
                    <span class="block text-[#E87532]">
                        Rasa Berkesan.
                    </span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-white/70">
                    Nikmati semangkuk bakso hangat dengan cita rasa
                    yang dibuat untuk menemani setiap momen.
                </p>

                <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                    <a 
                        href="#menu"
                        class="inline-flex items-center justify-center rounded-full bg-[#e87532] px-7 px-3.5 font-bold text-white transition hover:bg-[#B83B25]"
                    >
                        Lihat Menu
                    </a>

                    <a 
                        href="#about"
                        class="inline-flex items-center justify-center rounded-full border border-white/20 px-7 py-3.5 font-bold text-white transition hover:bg-white/10"
                    >
                        Tentang Kami
                    </a>
                </div>

            </div>

            {{-- Hero Visual --}}
            <div class="relative">

                <div class="mx-auto flex aspect-square max-w-lg items-center justify-center rounded-full bg-[#E87532]/20">

                    <div class="flex h-72 w-72 items-center justify-center rounded-full bg-[#B83B25] shadow-2xl sm:h-96 sm:w-96">

                        <div class="text-center">
                            <div class="text-7xl sm:text-8xl">
                                🍲
                            </div>
                            <p class="mt-4 text-sm font-bold uppercase tracking-[0.3em]">
                                Bakso Pesona
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {{-- About --}}
    <section id="about" class="bg-white py-24">

        <div class="mx-auto max-w-7xl px-6 lg:px-8">

            <div class="grid gap-12 lg:grid-cols-2 lg:items-center">

                <div>
                    <span class="text-sm font-bold uppercase tracking-[0.2em] text-[#B83B25]">Tentang Kami</span>
                    <h2 class="mt-4 text-4xl font-black tracking-tight text-[#7F1D1D] sm:text-5xl">
                        Lebih dari sekadar semangkuk bakso.
                    </h2>
                </div>

                <div>
                    <p class="leading-8 text-gray-600">
                        Bakso pesona hadir dengan semangat menghadirkan
                        makanan yang sederhana namun memiliki rasa ang
                        memorable. Setiap sajian dibuat dengan perhatian
                        terhadap rasa, kualitas, dan pengalaman pelanggan.
                    </p>
                </div>

            </div>

        </div>

    </section>

    {{-- Menu --}}
    <section id="menu" class="bg-orange-50 py-24">

        <div class="mx-auto max-w-7xl px-6 lg:px-8">

            <div class="text-center">

                <span class="text-sm font-bold uppercase tracking-[0.2em] text-[#B83B25]">Menu Favorit</span>
                <h2 class="mt-3 text-4xl font-black text-[#7F1D1D]">
                    Pilihan yang 
                </h2>
            </div>
        </div>
    </section>