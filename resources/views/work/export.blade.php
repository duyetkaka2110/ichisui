<table>
    <thead>
        @if (isset($list) && $list)
        <tr>
            @foreach($list[0] as $k=>$v)
            <th>{{ $k}}</th>
            @endforeach
        </tr>
        @endif
    </thead>
    <tbody>
        @if (isset($list) && $list)
        @foreach ($list as $l)
        <tr>
            @foreach($l as $v)
            <td>{{ $v}}</td>
            @endforeach
        </tr>
        @endforeach
        @endif

    </tbody>
</table>