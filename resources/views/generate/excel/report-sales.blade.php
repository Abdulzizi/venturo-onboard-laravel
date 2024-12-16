<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Excel Template</title>
</head>

<body>

    <table>
        <thead>
            <tr>
                <td style="width: 170px; vertical-align: middle;" rowspan="2" id="menu">Menu</td>
                <td colspan="{{ count($dates) }}" class="text-center" id="periode">Periode</td>
                <td style="width: 70px; vertical-align: middle;" rowspan="2" id="total">Total</td>
            </tr>
            <tr>
                @foreach ($dates as $date)
                    <td>
                        {{ date('d', strtotime($date)) }}
                    </td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $category)
                <tr class="category">
                    <td colspan="{{ count($dates) + 1 }}">
                        {{ $category['category_name'] }}
                    </td>
                    <td class="nominal">
                        Rp {{ number_format($category['category_total']) }}
                    </td>
                </tr>
                @foreach ($category['products'] as $product)
                    <tr>
                        <td>
                            {{ $product['product_name'] }}
                        </td>
                        @foreach ($product['transactions'] as $sale)
                            <td class="nominal">
                                Rp {{ number_format($sale['total_sales']) }}
                            </td>
                        @endforeach
                        <td class="nominal">
                            Rp {{ number_format($product['transactions_total']) }}
                        </td>
                    </tr>
                @endforeach
                <tr class="category">
                    <td>Grand Total</td>
                    @foreach ($total_per_date as $total)
                        <td class="nominal">
                            Rp {{ number_format($total) }}
                        </td>
                    @endforeach
                    <td class="nominal">
                        Rp {{ number_format($grand_total) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
