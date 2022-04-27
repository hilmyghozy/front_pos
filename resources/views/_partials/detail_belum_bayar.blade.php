<tr>
    <th colspan="2" class="text-left">Kode</th>
    <td class="text-right" id="order_no_invoice"><?= $kode_temp ?></td>
</tr>
<tr>
    <th colspan="2" class="text-left">Type Order</th>
    <td class="text-right"><?= $type_or ?> - <?= $pembayaran->keterangan_order ?></td>
</tr>

@foreach ($item_bayar->data as $key => $value)
<tr>
    <th class="text-left">
        <?= $value->nama_item ?>
        @foreach ($value->additional_menu as $additional_menu)
        <p class="m-0"><span>&nbsp;&nbsp; + {{ $additional_menu->text}}</span></p>
        @endforeach
    </th>
    <td class="text-center">
        @if(count($value->opsi_menu) == 0)
        <?= $value->qty ?>
        @endif
    </td>
    <td class="text-right">
        <?= number_format(($value->harga * $value->qty), 0, ',', '.') ?>
        <br>
        @foreach ($value->additional_menu as $additional_menu)
        <p class="m-0"><?= number_format(($additional_menu->harga * $value->qty), 0, ',', '.') ?></p>
        @endforeach
    </td>
</tr>
@foreach($value->opsi_menu as $opsi_menu)
<tr>
    <th class="text-left">
        - <?= $opsi_menu->nama_item ?>
        @if(isset($opsi_menu->additional_menu))
        @foreach ($opsi_menu->additional_menu as $additional_menu)
        <p class="m-0"><span>&nbsp;&nbsp;&nbsp; + {{ $additional_menu->text}}</span></p>
        @endforeach
        @endif
    </th>
    <td class="text-center">
        <?= $opsi_menu->qty ?>
    </td>
    <td class="text-right">
        
        <br>
        @if(isset($opsi_menu->additional_menu))
        @foreach ($opsi_menu->additional_menu as $additional_menu)
        <p class="m-0"><?= number_format(($additional_menu->harga * $opsi_menu->qty), 0, ',', '.') ?></p>
        @endforeach
        @endif
    </td>
</tr>
@endforeach
@endforeach

<tr>
    <th colspan="2" class="text-left">Subtotal</th>
    <td class="text-right" id="sub_total_order"><?= number_format($item_bayar->sub_total, 0, ',', '.') ?></td>
</tr>
<tr>
    <th colspan="2" class="text-left">Pajak</th>
    <td class="text-right" id="pajak_order"><?= number_format($item_bayar->pajak, 0, ',', '.') ?></td>
</tr>
<tr>
    <th class="text-left">Total</th>
    <td class="text-left"><?= $item_bayar->qty ?></td>
    <td class="text-right" id="total_order"><?= number_format($item_bayar->total, 0, ',', '.') ?></td>
</tr>