<div id="packetAdditionalOption">
    <form class="additional-menu-form">
        <div class="form-row">
            @foreach($opsi_menu as $data)
            @for ($i = 0; $i < $data->jumlah; $i++)
            <div class="form-group col-md-6 col-sm-12">
                <button type="button" id="opsi_menu_{{ $data->id_item }}_{{ $i }}" data-value="{{ $data->id_item }}" class="btn btn-outline-primary my-2 opsi_menu_text"
                    onclick="openAdditionalMenuPaket(this)"
                    data-menuid="{{ $data->id_item }}_{{ $i }}"
                    data-dataid="{{$data->id_item}}"
                    data-type="item"
                    data-text="{{ $data->nama_item }}"
                    data-kategori="{{ $data->additional_menu[0]->id_kategori }}"
                    data-menu_type="{{ $data->menu_type }}"
                    data-selected_menu_type="0"
                    >{{ $data->nama_item }}</button>
                <div class="form-group row mb-2">
                    <div class="col-sm-12 additional_menu_list">
                    </div>
                </div>
            </div>
            @endfor
            @endforeach
        </div>
    </form>
</div>