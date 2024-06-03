<table border="1" style="margin: 0 auto; margin-top: 5%">
    <tr">
        <th>Key</th>
        <th>Value</th>
    </tr>
    @foreach($data as $key => $value)
    <tr>
        <td>{{ $key }}</td>
        <td>{{ is_array($value) ? implode(", ", $value) : $value }}</td>
    </tr>
    @endforeach
</table>