<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ID Card Preview - {{ $student->name }}</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #f8f9fa;
        }

        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .preview-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .preview-header h1 {
            color: #495057;
            margin-bottom: 10px;
        }

        .preview-header p {
            color: #6c757d;
            margin: 0;
        }

        .card-preview {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .id-card {
            width: 340px;
            height: 214px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 15px;
            color: white;
            position: relative;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        .id-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: rgba(255,255,255,0.2);
        }

        .card-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .university-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-bottom: 5px;
        }

        .university-name {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .card-title {
            font-size: 10px;
            margin: 0;
            opacity: 0.9;
        }

        .card-content {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .photo-section {
            flex-shrink: 0;
        }

        .student-photo {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 5px;
        }

        .info-section {
            flex-grow: 1;
            min-width: 0;
        }

        .student-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            word-wrap: break-word;
        }

        .info-row {
            margin-bottom: 4px;
            display: flex;
            align-items: center;
        }

        .info-label {
            font-size: 10px;
            opacity: 0.8;
            min-width: 50px;
        }

        .info-value {
            font-size: 11px;
            font-weight: 500;
        }

        .department {
            font-size: 9px;
            background: rgba(255,255,255,0.2);
            padding: 3px 8px;
            border-radius: 3px;
            display: inline-block;
            margin-top: 8px;
        }

        .card-number {
            position: absolute;
            bottom: 15px;
            left: 15px;
            font-size: 9px;
            opacity: 0.8;
        }

        .validity {
            position: absolute;
            bottom: 5px;
            left: 15px;
            font-size: 8px;
            opacity: 0.7;
        }

        .preview-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 24px;
            font-weight: bold;
            color: rgba(255,255,255,0.3);
            z-index: 10;
            pointer-events: none;
        }

        .info-panel {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-panel h3 {
            color: #495057;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-item label {
            font-weight: 600;
            color: #6c757d;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-item span {
            color: #495057;
            font-size: 14px;
        }

        .actions {
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #1e7e34;
        }

        @media print {
            body {
                background: white;
            }

            .preview-container {
                box-shadow: none;
                padding: 0;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1>ID Card Preview</h1>
            <p>Preview of {{ $student->name }}'s student ID card</p>
        </div>

        <div class="card-preview">
            <div class="id-card">
                <div class="preview-watermark">PREVIEW</div>

                <div class="card-header">
                    @if(file_exists($university_logo))
                        <img src="{{ $university_logo }}" alt="University Logo" class="university-logo">
                    @endif
                    <h2 class="university-name">LEXA UNIVERSITY</h2>
                    <p class="card-title">STUDENT IDENTIFICATION CARD</p>
                </div>

                <div class="card-content">
                    <div class="photo-section">
                        <img src="{{ $request->photo_url }}" alt="Student Photo" class="student-photo">
                    </div>

                    <div class="info-section">
                        <h3 class="student-name">{{ $student->name }}</h3>

                        <div class="info-row">
                            <span class="info-label">ID:</span>
                            <span class="info-value">{{ $card_number }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Matric:</span>
                            <span class="info-value">{{ $student->matric_no }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $student->email }}</span>
                        </div>

                        @if($student->phone)
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $student->phone }}</span>
                        </div>
                        @endif

                        <div class="department">{{ $student->department }}</div>
                    </div>
                </div>

                <div class="card-number">
                    Card #: {{ $card_number }}
                </div>

                <div class="validity">
                    Valid: {{ $generated_at->format('m/Y') }} - {{ $generated_at->addYears(4)->format('m/Y') }}
                </div>
            </div>
        </div>

        <div class="info-panel">
            <h3>Request Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Request Number</label>
                    <span>{{ $request->request_number }}</span>
                </div>
                <div class="info-item">
                    <label>Card Number</label>
                    <span>{{ $card_number }}</span>
                </div>
                <div class="info-item">
                    <label>Reason</label>
                    <span>{{ $request->reason_label }}</span>
                </div>
                <div class="info-item">
                    <label>Status</label>
                    <span>{{ ucfirst($request->status) }}</span>
                </div>
                <div class="info-item">
                    <label>Submitted</label>
                    <span>{{ $request->created_at->format('M d, Y') }}</span>
                </div>
                <div class="info-item">
                    <label>Generated</label>
                    <span>{{ $generated_at->format('M d, Y \a\t h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="actions">
            <button onclick="window.print()" class="btn btn-secondary">
                Print Preview
            </button>
            <a href="{{ route('admin.id-cards.show', $request) }}" class="btn btn-primary">
                Back to Request
            </a>
            <form method="POST" action="{{ route('admin.id-cards.generate', $request) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Generate and print this ID card?')">
                    Generate ID Card
                </button>
            </form>
        </div>
    </div>
</body>
</html>
