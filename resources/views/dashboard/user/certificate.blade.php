<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Imunisasi - {{ $patient->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Roboto:wght@300;400;500&display=swap');
        
        body {
            background-color: #f3f4f6;
            font-family: 'Roboto', sans-serif;
        }
        
        #certificate-container {
            width: 297mm; /* A4 Landscape width */
            height: 210mm; /* A4 Landscape height */
            margin: 0 auto;
            background: white;
            position: relative;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .border-pattern {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 5px solid #1e40af; /* Blue-800 */
            z-index: 10;
        }

        .border-pattern::before {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 2px solid #93c5fd; /* Blue-300 */
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            opacity: 0.03;
            font-weight: bold;
            color: #1e40af;
            z-index: 0;
            white-space: nowrap;
        }

        .content-layer {
            position: relative;
            z-index: 20;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 60px;
            text-align: center;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #1e3a8a; /* Blue-900 */
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="fixed top-4 right-4 z-50 no-print flex gap-2">
        <a href="{{ route('user.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow transition">
            Kembali
        </a>
        <button onclick="downloadPDF()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download PDF
        </button>
    </div>

    <div id="certificate-container">
        <!-- Decorative Borders -->
        <div class="border-pattern"></div>
        
        <!-- Watermark -->
        <div class="watermark">IMUNISASI LENGKAP</div>

        <div class="content-layer">
            <!-- Header -->
            <div class="mb-8">
                <p class="text-sm tracking-widest text-gray-500 uppercase font-semibold mb-2">SERTIFIKAT BAGIAN BELAKANG</p>
                <h1 class="text-4xl font-bold mb-2 tracking-wide text-blue-900">SERTIFIKAT IMUNISASI DASAR LENGKAP</h1>
                <p class="text-xl text-blue-600 font-serif italic mt-2">PUSTU ILP SELENGEN</p>
            </div>

            <!-- Pre-content -->
            <div class="mb-8">
                <p class="text-gray-600 text-lg uppercase tracking-wide">Dahak, ________________________</p>
                <!-- Wait, image says 'DIBERIKAN KEPADA' in center. Logic adjustment -->
            </div>
            
            <div class="mb-10">
                <p class="text-gray-500 font-serif italic mb-6">Diberikan Kepada :</p>
                
                <div class="max-w-3xl mx-auto space-y-4 text-left pl-20">
                    <div class="grid grid-cols-3 gap-4 border-b border-gray-200 pb-2">
                        <span class="font-bold text-gray-700 col-span-1">NAMA</span>
                        <span class="col-span-2 text-xl font-serif text-blue-900">: {{ strtoupper($patient->name) }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 border-b border-gray-200 pb-2">
                        <span class="font-bold text-gray-700 col-span-1">TANGGAL LAHIR</span>
                        <span class="col-span-2 text-lg text-gray-800">: {{ \Carbon\Carbon::parse($patient->date_birth)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 border-b border-gray-200 pb-2">
                        <span class="font-bold text-gray-700 col-span-1">NAMA ORANG TUA</span>
                        <span class="col-span-2 text-lg text-gray-800">: {{ strtoupper($patient->mother_name) }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 border-b border-gray-200 pb-2">
                        <span class="font-bold text-gray-700 col-span-1">ALAMAT</span>
                        <span class="col-span-2 text-lg text-gray-800">: {{ strtoupper($patient->address) }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Statement -->
            <div class="mt-4 border-t-2 border-blue-100 pt-6 mx-20">
                <p class="text-lg font-bold text-gray-800 leading-relaxed uppercase">
                    TELAH MENYELESAIKAN IMUNISASI DASAR LENGKAP SESUAI DENGAN JADWAL IMUNISASI
                </p>
            </div>
            
            <!-- Signature Area (Optional, keeping simple as per image) -->
            <!-- Signature Area -->
            <div class="w-full flex justify-end mt-12 pr-4">
                <div class="text-center">
                    <p class="text-gray-600 mb-20">Selengen, {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold text-gray-800 border-t border-gray-400 pt-2 px-8 inline-block select-none">Petugas Imunisasi</p>
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

            // Choose to download or print
            // Using html2pdf to download
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
