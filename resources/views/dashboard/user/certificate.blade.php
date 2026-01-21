<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Imunisasi - {{ $patient->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fredoka+One&family=Courgette&family=Poppins:wght@400;500;600;700&display=swap');
        
        body {
            background-color: #f3f4f6;
            font-family: 'Poppins', sans-serif;
        }
        
        #certificate-container {
            width: 297mm;
            height: 210mm;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            background-image: url('/images/certificate_background.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .certificate-title {
            font-family: 'Fredoka One', cursive;
            background: linear-gradient(180deg, #ec4899 0%, #db2777 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 3px 3px 0px rgba(0,0,0,0.1);
            letter-spacing: 8px;
        }

        .subtitle-blue {
            font-family: 'Fredoka One', cursive;
            color: #2563eb;
            text-shadow: 2px 2px 0px rgba(0,0,0,0.1);
        }

        .subtitle-pink {
            font-family: 'Fredoka One', cursive;
            color: #ec4899;
            text-shadow: 2px 2px 0px rgba(0,0,0,0.1);
        }

        .given-to {
            font-family: 'Courgette', cursive;
            color: #1e40af;
        }

        .info-label {
            font-family: 'Poppins', sans-serif;
            color: #1e3a8a;
            font-weight: 600;
        }

        .info-dots {
            border-bottom: 2px dotted #1e3a8a;
            flex-grow: 1;
            margin: 0 10px;
        }

        .info-value {
            font-family: 'Poppins', sans-serif;
            color: #1e3a8a;
            font-weight: 500;
        }

        .statement-text {
            font-family: 'Poppins', sans-serif;
            color: #1e3a8a;
            font-weight: 700;
        }

        .ribbon {
            width: 60px;
            height: 80px;
            position: relative;
        }

        .ribbon-circle {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 50%;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        .ribbon-center {
            width: 25px;
            height: 25px;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .ribbon-tail {
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-top: 30px solid #2563eb;
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
        }

        .signature-area {
            font-family: 'Poppins', sans-serif;
        }

        /* Decorative elements */
        .star {
            position: absolute;
            color: white;
            opacity: 0.8;
        }

        .corner-decoration {
            position: absolute;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
            border-radius: 50%;
        }

        .certificate-number {
            font-family: 'Poppins', sans-serif;
            color: #dc2626;
            font-weight: 700;
            font-size: 14px;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="fixed top-4 right-4 z-50 no-print flex gap-2">
        <a href="{{ route('user.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow transition">
            Kembali
        </a>
        <button onclick="downloadPDF()" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download PDF
        </button>
    </div>

    <div id="certificate-container">
        <!-- Decorative Stars -->
        <div class="star" style="top: 80px; right: 60px; font-size: 24px;">✦</div>
        <div class="star" style="top: 120px; right: 40px; font-size: 16px;">✦</div>
        <div class="star" style="top: 160px; right: 70px; font-size: 20px;">✦</div>
        <div class="star" style="bottom: 100px; right: 50px; font-size: 18px;">✦</div>
        
        <!-- Corner decorations -->
        <div class="corner-decoration" style="top: -100px; right: -100px;"></div>
        <div class="corner-decoration" style="bottom: -100px; left: -100px;"></div>

        <!-- Main Content -->
        <div class="absolute inset-0 z-10 flex flex-col items-center justify-center" style="padding: 20px 60px;">
            
            <!-- Header Title -->
            <div class="text-center mb-4">
                <h1 class="certificate-title text-5xl mb-2">SERTIFIKAT</h1>
                <h2 class="subtitle-blue text-2xl tracking-wider mb-1">IMUNISASI DASAR LENGKAP</h2>
                <h3 class="subtitle-pink text-xl tracking-widest">PUSTU ILP SELENGEN</h3>
            </div>

            <!-- Certificate Number -->
            <div class="text-center mb-3">
                <p class="certificate-number">No. {{ $certificateNumber }}</p>
            </div>

            <!-- Given To Section -->
            <div class="text-center mb-4">
                <p class="given-to text-2xl italic">Diberikan kepada :</p>
            </div>

            <!-- Information Fields -->
            <div class="w-full max-w-xl mx-auto space-y-3 mb-6">
                <div class="flex items-end">
                    <span class="info-label w-36">Nama</span>
                    <span class="info-value text-lg" style="min-width: 250px;">: {{ strtoupper($patient->name) }}</span>
                </div>
                <div class="flex items-end">
                    <span class="info-label w-36">Tanggal lahir</span>
                    <span class="info-value text-lg" style="min-width: 250px;">: {{ \Carbon\Carbon::parse($patient->date_birth)->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex items-end">
                    <span class="info-label w-36">Nama ibu</span>
                    <span class="info-value text-lg" style="min-width: 250px;">: {{ strtoupper($patient->mother_name) }}</span>
                </div>
            </div>

            <!-- Statement -->
            <div class="text-center mb-2">
                <p class="statement-text text-lg leading-relaxed">
                    Telah Menyelesaikan Imunisasi Dasar Lengkap Sesuai Dengan
                </p>
                <p class="statement-text text-lg">
                    Jadwal Imunisasi
                </p>
            </div>

            <!-- Signature Area -->
            <div class="flex justify-center items-end w-full gap-64 mt-2">
                <!-- Left Signature -->
                <div class="signature-area text-center">
                    <p class="text-sm font-semibold text-blue-900 mb-2">Kepala UPT BLUD Puskesmas</p>
                    <p class="text-sm font-semibold text-blue-900 mb-1">Kayangan</p>
                    <div class="relative flex items-end justify-center" style="margin-bottom: -20px;">
                        <img src="{{ asset('images/signature_sabri.png') }}" alt="Tanda Tangan" class="h-20 object-contain">
                    </div>
                    <p class="font-bold text-blue-900 relative z-10">SABRI, SKM</p>
                </div>

                <!-- Center Medal/Ribbon -->
                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/ribbon_medal.png') }}" alt="Medal" class="h-32 object-contain">
                </div>

                <!-- Right Signature -->
                <div class="signature-area text-center">
                    <p class="text-sm font-semibold text-blue-900 mb-1">Petugas Jurim Pustu ILP</p>
                    <p class="text-sm font-semibold text-blue-900 mb-1">Selengen</p>
                    <div class="relative flex items-end justify-center" style="margin-bottom: -25px;">
                        <img src="{{ asset('images/signature_endang.png') }}" alt="Stempel dan Tanda Tangan" class="h-28 object-contain">
                    </div>
                    <p class="font-bold text-blue-900 relative z-10">Endang Junaela, S.ST.,NS</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('certificate-container');
            const opt = {
                margin:       0,
                filename:     'Sertifikat-Imunisasi-{{ Str::slug($patient->name) }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
