
<div class="page-content container mx-4">

    <div class="row mt-4">
        <div class="col">
            <!-- .row -->

            <hr class="row brc-default-l1 mx-n1 mb-4" />
            <div class="mt-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Menu</th>
                                <th scope="col" class="text-center">Jumlah</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Total Harga</th>
                                <th scope="col">Thirdparty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembayaran as $key => $data)
                            <tr class="{{ $key > 0 && ($key+1)%2 == 0 ? 'table-secondary' : ''}}">
                                <th scope="row" class="align-baseline h-auto">
                                    <button type="button" class="btn-sm btn-red btn-trash w-auto" data-id="{{ $data->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </th>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                        @if(count($data->opsi_menu) > 0)
                                        <strong>{{ $data->nama_tiket }}</strong>
                                        @else
                                        {{ $data->nama_tiket }}
                                        @endif
                                    </p>
                                </td>
                                <td class="align-baseline h-auto text-center">
                                    <p class="my-2">
                                    <button type="button" class="btn btn-outline-info w-auto rounded-circle mr-3 btn-reduce-qty" onclick="reduceQty(this, {{ $data->id }})">
                                        <span aria-hidden="true">&minus;</span>
                                    </button>
                                    <span class="invoice-qty">{{ ($data->qty == 0) ? 0 : $data->qty }}</span>
                                    <button type="button" class="btn btn-outline-info w-auto rounded-circle ml-3 btn-add-qty" onclick="addQty(this, {{ $data->id }})">
                                        <span aria-hidden="true">&plus;</span>
                                    </button>
                                    </p>
                                </td>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                    Rp. <span><?= number_format(($data->harga == 0) ? 0 : $data->harga, 0, ",", ".") ?></span>
                                    </p>
                                </td>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                    Rp. <span><?= number_format(($data->total == 0) ? 0 : $data->total, 0, ",", ".") ?></span>
                                    </p>
                                </td>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                    Rp. <span><?= number_format(($data->subthirdparty == 0) ? 0 : $data->subthirdparty, 0, ",", ".") ?></span>
                                    </p>
                                </td>
                            </tr>
                            @foreach ($data->additional_menu as $additional_menu)
                            <tr class="{{ $key > 0 && ($key+1)%2 == 0 ? 'table-secondary' : ''}}">
                                <th scope="row" class="align-baseline h-auto">
                                    
                                </th>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                        <span class="badge badge-info">+ {{ $additional_menu->text }}</span>
                                    </p>
                                </td>
                                <td class="align-baseline h-auto">
                                </td>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                    Rp. <span><?= number_format(($additional_menu->harga == 0) ? 0 : $additional_menu->harga, 0, ",", ".") ?></span>
                                    </p>
                                </td>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                    Rp. <span><?= number_format(($additional_menu->harga == 0) ? 0 : $additional_menu->harga * $data->qty, 0, ",", ".") ?></span>
                                    </p>
                                </td>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                    Rp. <span><?= number_format(($additional_menu->harga == 0) ? 0 : $additional_menu->harga * $data->qty, 0, ",", ".") ?></span>
                                    </p>
                                </td>
                            </tr>
                            @endforeach

                            @foreach ($data->opsi_menu as $_key => $opsi_menu)
                            <tr class="{{ $_key > 0 && ($_key+1)%2 == 0 ? 'table-secondary' : ''}}">
                                <th scope="row" class="align-baseline h-auto">
                                    
                                </th>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                    {{ $opsi_menu->nama_item }}
                                    </p>
                                </td>
                                <td class="align-baseline h-auto">
                                    
                                </td>
                                <td class="align-baseline h-auto">
                                    
                                </td>
                                <td class="align-baseline h-auto">
                                    
                                </td>
                                <td class="align-baseline h-auto">
                                    
                                </td>
                            </tr>
                            @if(isset($opsi_menu->additional_menu))
                            @foreach ($opsi_menu->additional_menu as $additional_menu)
                            <tr class="{{ $_key > 0 && ($_key+1)%2 == 0 ? 'table-secondary' : ''}}">
                                <th scope="row" class="align-baseline h-auto">
                                    
                                </th>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                        <span class="badge badge-info">+ {{ $additional_menu->text }}</span>
                                    </p>
                                </td>
                                <td class="align-baseline h-auto">
                                    
                                </td>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                    Rp. <span><?= number_format(($additional_menu->harga == 0) ? 0 : $additional_menu->harga, 0, ",", ".") ?></span>
                                    </p>
                                </td>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                    Rp. <span><?= number_format(($additional_menu->harga == 0) ? 0 : $additional_menu->harga * $data->qty, 0, ",", ".") ?></span>
                                    </p>
                                </td>
                                <td class="align-baseline h-auto">
                                    <p class="my-2">
                                    Rp. <span><?= number_format(($additional_menu->harga == 0) ? 0 : $additional_menu->harga * $data->qty, 0, ",", ".") ?></span>
                                    </p>
                                </td>
                            </tr>
                            @endforeach
                            @endif

                            @endforeach

                            @endforeach
                            <tr class="table-info">
                                <th scope="row" colspan="3" class="align-baseline h-auto">
                                   
                                </th>
                                <th class="align-baseline h-auto">
                                    <p class="my-2 font-weight-bold">
                                        Subtotal
                                    </p>
                                </th>
                                <th class="align-baseline h-auto">
                                    <p class="my-2 font-weight-bold">
                                    Rp. <span><?= number_format(($sub_total == 0) ? 0 : $sub_total, 0, ",", ".") ?></span>
                                    </p>
                                </th>
                                <th class="align-baseline h-auto">
                                    <p class="my-2 font-weight-bold">
                                    Rp. <span><?= number_format(($sub_total_thirdparty == 0) ? 0 : $sub_total_thirdparty, 0, ",", ".") ?></span>
                                    </p>
                                </th>
                            </tr>
                            <tr class="table-info">
                                <th scope="row" colspan="3" class="align-baseline h-auto">
                                   
                                </th>
                                <th class="align-baseline h-auto">
                                    <p class="my-2 font-weight-bold">
                                        Pajak
                                    </p>
                                </th>
                                <th class="align-baseline h-auto">
                                    <p class="my-2 font-weight-bold">
                                    Rp. <span><?= number_format(($pajak == 0) ? 0 : $pajak, 0, ",", ".") ?></span>
                                    </p>
                                </th>
                                <th class="align-baseline h-auto">
                                    <p class="my-2 font-weight-bold">
                                    Rp. <span><?= number_format(($pajak_thirdparty == 0) ? 0 : $pajak_thirdparty, 0, ",", ".") ?></span>
                                    </p>
                                </th>
                            </tr>
                            <tr class="table-info">
                                <th scope="row" colspan="3" class="align-baseline h-auto">
                                   
                                </th>
                                <th class="align-baseline h-auto">
                                    <p class="my-2 font-weight-bold">
                                        Total
                                    </p>
                                </th>
                                <th class="align-baseline h-auto">
                                    <p class="my-2 font-weight-bold">
                                    Rp. <span><?= number_format(($total == 0) ? 0 : $total, 0, ",", ".") ?></span>
                                    </p>
                                </th>
                                <th class="align-baseline h-auto">
                                    <p class="my-2 font-weight-bold">
                                    Rp. <span><?= number_format(($thirdparty == 0) ? 0 : $thirdparty, 0, ",", ".") ?></span>
                                    </p>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
