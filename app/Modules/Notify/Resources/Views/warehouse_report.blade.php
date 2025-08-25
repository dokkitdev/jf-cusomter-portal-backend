<img src="{{ config('notify.root_dir') . '/Resources/Assets/logo.png' }}" alt="logo" style="width: 220px; height: 103px; opacity: .7;">

@foreach($reportData as $reportOneDate)
    <p style="margin-bottom: 20px;">
        Blue Flame Heating Solutions <br>
        {{ $type }} Warehouse Report for <b>{{ $reportOneDate['date']->format('l, jS F Y') }}</b>
    </p>
    @foreach($reportOneDate['report_jobs'] as $reportJob)
        <h3>Job #{{ $reportJob['id'] }}</h3>
        <div>
            Site: {{ $reportJob['site_name'] }} <br>
            Engineer/s: {{ $reportJob['engineer_name'] }}
        </div>
        <table style="width: 100%; margin-top: 30px; width: 100%; border-collapse: collapse;">
            <thead>
            <tr style="background: rgb(206, 223, 242); border: 1px solid black;">
                <th style="border: 1px solid black; padding: 5px; text-align: left;" width="20%">Part No.</th>
                <th style="border: 1px solid black; max-width: 300px; padding: 5px; text-align: left;" width="35%">Stock
                    Name
                </th>
                <th style="border: 1px solid black; padding: 5px; text-align: left;" width="15%">Stored</th>
                <th style="border: 1px solid black; padding: 5px;" width="10%">Required</th>
                <th style="border: 1px solid black; padding: 5px;" width="10%">Assigned</th>
                <th style="border: 1px solid black; padding: 5px;" width="10%">Needed</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($reportJob['parts'] as $index => $part)
                <tr style="background: {{ $index % 2 != 0 ? 'rgb(233, 237, 247);' : 'white;' }}; border: 1px solid black;">
                    <td style="border: 1px solid black; padding: 5px; text-align: left;">{{ $part['part_no'] }}</td>
                    <td style="max-width: 300px; border: 1px solid black; padding: 5px; text-align: left;">{{ $part['stock_name'] }}</td>
                    <td style="border: 1px solid black; padding: 5px; text-align: left;">{{ $part['storage_location'] }}</td>
                    <td style="border: 1px solid black; padding: 5px; text-align: center;">{{ $part['required'] }}</td>
                    <td style="border: 1px solid black; padding: 5px; text-align: center;">{{ $part['assigned'] }}</td>
                    <td style="border: 1px solid black; padding: 5px; text-align: center;">{{ $part['needed'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach
    <p style="page-break-after: always"></p>
    <p><br></p>
@endforeach
