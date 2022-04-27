<div class="form-group">
    <label for="from">From</label>
    <div class="d-flex justify-content-between">
        <input type="hidden" id="id_item_edit_order" value="<?= $id ?>">
        <input type="hidden" id="id_pos_belum_bayar" value="<?= $item_revisi->id_pos_belum_bayar ?>">
        <input type="text" value="<?= $item_revisi->nama_item ?>" class="form-control col-8" readonly>
        <input type="text" value="<?= $item_revisi->qty ?>" class="form-control col-3" readonly>
    </div>
    <div class="form-row mt-2">
        <div class="col-12">
            @foreach($item_revisi->additional_menu as $item)
            <div class="form-check form-check-inline mb-2">
                <button type="button" class="btn btn-outline-primary w-auto" disabled>
                    {{ $item->text }} <span class="badge badge-info">+ Rp. {{ $item->harga }}</span>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    <div class="form-row">
        @foreach($item_revisi->opsi_menu as $data)
        <div class="form-group col-md-6 col-sm-12">
            <button type="button" class="btn btn-outline-primary my-2"
                >{{ $data->nama_item }}</button>
            <div class="form-group row mb-2">
                @if(isset($data->additional_menu))
                <div class="col-sm-12">
                    @foreach($data->additional_menu as $item)
                    <div class="form-check form-check-inline mb-1">
                        <button type="button" class="btn btn-outline-primary w-auto" disabled>
                            {{ $item->text }} <span class="badge badge-info">+ Rp. {{ $item->harga }}</span>
                        </button>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
<div class="form-group">
    <label for="from">To</label>
    <div class="d-flex justify-content-between">
        <select id="sel_order_revisi" class="form-control col-8">
            <option></option>
            <?php foreach ($item_product as $key => $value) { ?>
                <option
                    value="<?= $value->id_item ?>"
                    data-dataid="{{$value->id_item}}"
                    data-type="{{$value->is_paket ? 'paket' : 'item'}}"
                    data-kategori="{{ $value->id_kategori }}"
                    data-text="{{ $value->nama_item }}"
                    <?= $value->nama_item == $item_revisi->nama_item_lama ? 'selected' : null ?>
                >
                    <?= $value->nama_item ?> @<?= $value->harga_jual ?>
                </option>
            <?php } ?>
        </select>
        <input type="number" min="1" value="1" id="qty_order_revisi" class="form-control col-3">
    </div>
    <div class="form-row mt-2">
        <div class="col-12" id="sel_order_revisi_additional_option">
            
        </div>
    </div>
</div>