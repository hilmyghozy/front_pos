<!-- Menu Modal -->
<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close w-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body" id="jenis-ticket1">
                        <table id="table_id" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($item as $produk)
                                <tr
                                onclick="openAdditionalMenu(this)"
                                data-dataid="{{$produk->id_item}}"
                                data-type="{{$produk->is_paket ? 'paket' : 'item'}}"
                                data-kategori="{{ $produk->id_kategori }}"
                                >
                                    <td>{{ $produk->nomor}}</td>
                                    <td>{{$produk->id_item}}</td>
                                    <td>{{$produk->nama_item}}</td>
                                    <td>Rp. {{number_format($produk->harga_jual,0,',','.')}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-auto" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>