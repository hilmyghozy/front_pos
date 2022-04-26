<div id="nonPacketAdditionalOption">
    <div class="form-group">
        <form class="additional-menu-form">
            <div class="row">
                <div class="col-12">
                    @if(isset($item_types) && count($item_types) > 0)
                    <h5 class="modal-title mb-1 mt-2">Type</h5>
                    @foreach($item_types as $type)
                    <button type="button" id="item_type_{{ $type->id_type }}" data-text="{{ $type->nama_type }}" data-harga="{{ $type->harga }}" data-id="{{ $type->id_type }}" class="btn btn-outline-primary w-auto btn-item-type">
                        {{ $type->nama_type }} 
                        @if(!$paket)
                        <span class="badge badge-info"> Rp. {{ $type->harga }}</span>
                        @endif
                        <span class="item-check"></span>
                    </button>
                    @endforeach
                    @endif
                </div>
                <div class="col-12">
                    @if(isset($item_sizes) && count($item_sizes) > 0)
                    <h5 class="modal-title mb-1 mt-2">Size</h5>
                    @foreach($item_sizes as $size)
                    <button type="button" class="btn btn-outline-primary w-auto btn-item-size" id="item_size_{{ $size->id_size }}" data-text="{{ $size->nama_size }}" data-harga="{{ $size->harga }}" data-id="{{ $size->id_size }}">
                        {{ $size->nama_size }} <span class="badge badge-info"> Rp. {{ $size->harga }}</span> <span class="item-check"></span>
                    </button>
                    @endforeach
                    @endif
                </div>
                <div class="col-12">
                    <h5 class="modal-title mb-1 mt-2">Additional</h5>
                    @foreach($additional_menu as $item)
                    <div class="form-check form-check-inline mb-2">
                        <input class="form-check-input additional_menu_item" id="item-{{$item->id}}" name="additional_menu" type="checkbox" value="{{ $item->id }}" data-text="{{ $item->nama_additional_menu }}" data-harga="{{ $item->harga }}">
                        <label class="form-check-label" for="item-{{$item->id}}">{{ $item->nama_additional_menu }} <span class="badge badge-info"> + Rp. {{ $item->harga }}</span></label>
                    </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</div>