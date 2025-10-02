<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student ID Card - {{ $student->name }}</title>
    <style>
        @page {
            margin: 0;
            size: 85.6mm 53.98mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: white;
            color: #000;
            width: 85.6mm;
            height: 53.98mm;
            overflow: hidden;
            position: relative;
        }

        /* Single ID Card Container */
        .id-card {
            width: 85.6mm;
            height: 53.98mm;
            position: relative;
            background: white;
            border: 1pt solid #1a365d;
            border-radius: 3mm;
            overflow: hidden;
            display: block;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1a365d 0%, #2d5a87 100%);
            height: 12mm;
            width: 100%;
            display: flex;
            align-items: center;
            padding: 2mm;
            color: white;
            border-radius: 2mm 2mm 0 0;
        }

        .logo {
            width: 8mm;
            height: 8mm;
            background: white;
            border-radius: 50%;
            margin-right: 2mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .university-info {
            flex: 1;
        }

        .university-name {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }

        .card-type {
            font-size: 6pt;
            opacity: 0.9;
        }

        /* Content Area */
        .content {
            display: flex;
            padding: 3mm;
            gap: 3mm;
            height: calc(100% - 12mm);
        }

        /* Photo */
        .photo {
            width: 20mm;
            height: 25mm;
            border: 1pt solid #ddd;
            border-radius: 2mm;
            overflow: hidden;
            flex-shrink: 0;
        }

        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Student Info */
        .info {
            flex: 1;
            padding-left: 2mm;
        }

        .student-name {
            font-size: 12pt;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 2mm;
            text-transform: uppercase;
        }

        .details {
            font-size: 8pt;
            line-height: 1.4;
            margin-bottom: 1mm;
        }

        .details strong {
            color: #666;
            font-size: 7pt;
        }

        /* QR Code */
        .qr-code {
            position: absolute;
            top: 15mm;
            right: 3mm;
            width: 15mm;
            height: 15mm;
            border: 1pt solid #ddd;
            border-radius: 2mm;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .qr-placeholder {
            font-size: 6pt;
            color: #999;
            text-align: center;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 2mm;
            left: 3mm;
            right: 20mm;
            font-size: 6pt;
            color: #666;
            display: flex;
            justify-content: space-between;
        }

    </style>
</head>
<body>
    <div class="id-card">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                @if(file_exists(public_path('logo.png')))
                    <img src="{{ public_path('logo.png') }}" alt="Logo" style="width: 6mm; height: 6mm;">
                @endif
            </div>
            <div class="university-info">
                <div class="university-name">LEXA UNIVERSITY</div>
                <div class="card-type">STUDENT ID CARD</div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="photo">
                <img src="{{ $request->photo_url }}" alt="Student Photo">
            </div>
            
            <div class="info">
                <div class="student-name">{{ $student->name }}</div>
                
                <div class="details">
                    <strong>ID:</strong> {{ $card_number ?? $request->card_number }}<br>
                    <strong>Matric:</strong> {{ $student->matric_no }}<br>
                    <strong>Dept:</strong> {{ $student->department }}<br>
                    <strong>Email:</strong> {{ $student->email }}<br>
                    @if($student->phone)
                    <strong>Phone:</strong> {{ $student->phone }}<br>
                    @endif
                </div>
            </div>
        </div>

        <!-- QR Code -->
        @if(!isset($is_preview) || !$is_preview)
        <div class="qr-code">
            @if(isset($qr_code_path) && $qr_code_path && file_exists($qr_code_path))
                <img src="{{ $qr_code_path }}" alt="QR Code">
            @else
                <div class="qr-placeholder">QR<br>CODE</div>
            @endif
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <span><strong>{{ $card_number ?? $request->card_number }}</strong></span>
            <span>{{ $generated_at->format('m/Y') }} - {{ $generated_at->addYears(4)->format('m/Y') }}</span>
        </div>

        @if(isset($is_preview) && $is_preview)
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 12pt; font-weight: bold; color: rgba(26, 54, 93, 0.1); letter-spacing: 2pt; z-index: 100; pointer-events: none;">PREVIEW</div>
        @endif
    </div>
</body>
</html>
