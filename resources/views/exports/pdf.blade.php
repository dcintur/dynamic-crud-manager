<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $page->name }} Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>{{ $page->name }} - Data Export</h1>
    
    <table>
        <thead>
            <tr>
                @foreach($fields as $field)
                    <th>{{ $field->label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    @foreach($fields as $field)
                        <td>
                            @if(isset($item->data[$field->name]))
                                @switch($field->type)
                                    @case('file')
                                        File Link
                                        @break
                                    @case('checkbox')
                                        {{ $item->data[$field->name] ? 'Yes' : 'No' }}
                                        @break
                                    @default
                                        {{ $item->data[$field->name] }}
                                @endswitch
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
</body>
</html>