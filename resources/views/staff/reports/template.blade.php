<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #853953; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #f8f8f8; color: #853953; padding: 10px; text-align: left; border: 1px solid #eee; }
        td { padding: 8px; border: 1px solid #eee; }
        .footer { margin-top: 20px; font-size: 10px; color: #999; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>DreamHome Management System | Generated: {{ $generated_at }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @if($data->isNotEmpty())
                    @foreach(array_keys((array)$data->first()) as $column)
                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach((array)$row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">End of Report</div>
</body>
</html>