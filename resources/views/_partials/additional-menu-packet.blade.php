<div id="packetAdditionalOption">
    <form class="additional-menu-form">
        <div class="form-row">
            @foreach($n_opsi_menu as $key => $lists)
            <div class="col-md-12 mb-3">
                <div class="accordion" id="accordionExample_{{ $lists[0]->id_kategori }}">
                    <div class="card">
                        <div class="card-header" id="headingOne_{{ $lists[0]->id_kategori }}">
                            <h4 class="mb-0">
                                <a href="javascript:;" type="button" data-toggle="collapse" data-target="#collapseOne_{{ $lists[0]->id_kategori }}" aria-expanded="true" aria-controls="collapseOne_{{ $lists[0]->id_kategori }}">
                                    {{ $key . ' (' . $lists[0]->jumlah . ')'}}
                                </a>
                            </h4>
                        </div>
                        <table class="table">
                            {{-- <thead>
                                <tr>
                                    <th scope="col" colspan="2">Menu Dipilih</th>
                                </tr>
                            </thead> --}}
                            <tbody class="daftar_menu_terpilih_{{ $lists[0]->id_kategori }}" data-jumlah="{{ $lists[0]->jumlah }}" data-kategori="{{ $key }}">
                                <tr class="tr_daftar_menu_terpilih_{{ $lists[0]->id_kategori }}">
                                    <td colspan="2">Belum ada menu yang dipilih</td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="collapseOne_{{ $lists[0]->id_kategori }}" class="collapse" aria-labelledby="headingOne_{{ $lists[0]->id_kategori }}" data-parent="#accordionExample_{{ $lists[0]->id_kategori }}">
                            <div class="card-body">
                                <table class="table table_opsi_menu">
                                    <thead>
                                        <tr>
                                            <th scope="col">Item</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">
                                        @foreach($lists as $_key => $menu)
                                        <tr
                                            onclick="openAdditionalMenuPaket(this)"
                                            data-menuid="{{ $menu->id_item }}_{{ $_key }}"
                                            data-dataid="{{$menu->id_item}}"
                                            data-type="item"
                                            data-text="{{ $menu->nama_item }}"
                                            data-kategori="{{ $lists[0]->id_kategori }}"
                                            data-menu_type="{{ $menu->menu_type }}"
                                            data-selected_menu_type="0"
                                            data-jumlah_menu="{{ $menu->jumlah }}"
                                        >
                                            <td>{{ $menu->nama_item }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            {{-- @foreach($opsi_menu as $data)
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
            @endforeach --}}
        </div>
    </form>
</div>