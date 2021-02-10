<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shipping Label</title>
    <style>
    table.blueTable {
        font-family: Arial, Helvetica, sans-serif;
        background-color: #fff;
        width: 100%;
        text-align: left;
        border-collapse: collapse;
    }

    table.blueTable td,
    table.blueTable th {
        border: 1px solid #aaaaaa;
        padding: 3px 2px;
    }

    table.blueTable tbody td {
        font-size: 13px;
    }

    table.blueTable tfoot td {
        font-size: 13px;
    }

    table.blueTable tfoot .links {
        text-align: right;
    }

    table.blueTable tfoot .links a {
        display: inline-block;
        background: #1c6ea4;
        color: #ffffff;
        padding: 2px 8px;
        border-radius: 5px;
    }
    </style>
</head>

<body>
    <table class="blueTable">
        <tbody>
            <tr>
                <td>
                    <p>
                        From: 0097333389000 <br />
                        Shalooh General Trading <br />
                        Office 21 Building 101W, Road 11 <br />
                        Block 711 Tubli <br />
                        Manama <br />
                        Bahrain
                    </p>
                </td>
                <td>
                    <p>SHIP DATE: {{ date('Y-m-d') }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>
                        &nbsp;To:
                        {{ $data['phone'] }}<br />{{ $data['first_name'].' '.$data['last_name'] }}<br />{{ $data['shipping_address1'] }}<br />{{$data['shipping_address2']}}&nbsp;<br />{{$data['city']}}&nbsp;<br />{{ $data['country']}}
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p style="text-align: center;">
                        <br />
                        <br />
                        <barcode code="{{ $data['task_id'] }}" type="C128A" />
                        <br />
                        <br />
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>