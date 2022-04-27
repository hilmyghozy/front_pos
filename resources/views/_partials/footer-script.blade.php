
<div class="modal fade" id="modalPaketDetail" tabindex="-1" role="dialog" aria-labelledby="modalPaketDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-2">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-8 text-center">
              <h5 class="mt-2">Detail Paket</h5>
            </div>
            <div class="col-sm-2">
              {{-- <button type="button" class="btn-style btn-primary">Validasi</button> --}}
            </div>
          </div>
          <hr>
        </div>
      </div>
      <div class="modal-body">
        <div class="table-responsive mt-3" id="paket-detail-data">
          
        </div>
        <div class="container-fluid mt-3">

        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="voucherModal" tabindex="-1" role="dialog" aria-labelledby="voucherModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Masukan Kode Voucher</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 mb-2">
              <input type="text" class="form-control form-control-sm" aria-label="">
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-primary">Validasi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalNote" tabindex="-1" role="dialog" aria-labelledby="modalNoteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Note Order</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <textarea class="form-control col-md-12 mb-4" rows="4" placeholder="Note" id="textarea_note" style="height: 120px"></textarea>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-primary" onclick="open_modal_order()">Next</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalOrder" tabindex="-1" role="dialog" aria-labelledby="modalOrderLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Type Order</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 mb-5 row pr-0">
              <div class="col-md-4">
                <button class="btn-style btn-green" onclick="type_or(1)">Third <br> Party</button>
              </div>
              <div class="col-md-4">
                <button class="btn-style btn-green" onclick="type_or(2)">Take <br> Away</button>
              </div>
              <div class="col-md-4">
                <button class="btn-style btn-green" onclick="type_or(3)">Dine <br> In</button>
              </div>
              <input type="hidden" id="type_order">
              <input type="hidden" id="keterangan_order">
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-primary" id="next_type">Next</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalThird" tabindex="-1" role="dialog" aria-labelledby="modalThirdLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Third Party</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 mb-5 row pr-0">
              <div class="col-md-4">
                <button class="btn-style btn-green" onclick="set_3rd(1)" id="third_1">Go<br>Food</button>
              </div>
              <div class="col-md-4">
                <button class="btn-style btn-green" onclick="set_3rd(2)" id="third_2">Grab<br>Food</button>
              </div>
              <div class="col-md-4">
                <button class="btn-style btn-green" onclick="set_3rd(3)" id="third_3">Shopee<br>Food</button>
              </div>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-primary" id="btn_validasi_third">Validasi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalDine" tabindex="-1" role="dialog" aria-labelledby="modalDineLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Choose Table</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 mb-5 row pr-0" id="daftar_meja">
              {{-- <div class="col-md-4">
                <button class="btn-style btn-green" onclick="set_meja(1)">Meja 1</button>
              </div> --}}
            </div>
            <div class="col-sm-6">
              <input type="hidden" id="meja_choose">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-primary" id="validasi_dine_in">Validasi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalOrderPay" tabindex="-1" role="dialog" aria-labelledby="modalOrderPayLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-2">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-8 text-center">
              <h5 class="mt-2">Pembayaran Order</h5>
            </div>
            <div class="col-sm-2">
              {{-- <button type="button" class="btn-style btn-primary">Validasi</button> --}}
            </div>
          </div>
          <hr>
        </div>
      </div>
      <div class="modal-body">
        <div class="table-responsive mt-3">
          <table class="table table-striped table-md">
            <tr>
              <th>Kode</th>
              <th>Tipe Order</th>
              <th>Keterangan</th>
              <th>Waiters</th>
              <th>Total Bayar</th>
              <th class="text-center">#</th>
            </tr>
            <tbody id="body_belum_bayar">

            </tbody>
          </table>
        </div>
        <div class="container-fluid mt-3">

        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalOrderDetail" tabindex="-1" role="dialog" aria-labelledby="modalOrderDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Order Detail</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 mb-5 pr-0">
              <div class="table-responsive mt-3">
                <table class="table table-striped table-md" id="body_modalOrderDetail">

                </table>
              </div>
            </div>
            <div class="col-sm-6">
              <input type="hidden" id="meja_choose">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            {{-- <div class="col-sm-4">
              <button type="button" class="btn-style btn-info" onclick="print_resi()">Resi</button>
            </div> --}}
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-primary" id="validasi_modalOrderDetail">Validasi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="pembayaranModal" tabindex="-1" role="dialog" aria-labelledby="pembayaranModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-4">
              <button type="button" class="btn-style btn-red">Batal</button>
            </div>
            <div class="col-sm-4 text-center">
              <h5 class="mt-2">Metode Pembayaran</h5>
            </div>
            <div class="col-sm-4">
              <button type="button" class="btn-style btn-info btn-print-resi" onclick="print_resi(this)">Resi</button>
            </div>
          </div>
          <hr>
        </div>
      </div>
      <div class="modal-body">

        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">

            </div>
            <div class="col-md-12 metode-pembayaran">
              @foreach ($metode as $tipe)
              <button type="button" class="btn-style btn-pay" data-dataid="{{$tipe->id_payment}}" value="{{$tipe->nama_payment}}" data-value="{{$tipe->is_split}}" data-tipe="{{$tipe->tipe_payment}}"> {{$tipe->nama_payment}}
              </button>
              @endforeach
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <p class="lead mt-3">Voucher</p>
            </div>
            <div class="col-md-12 daftar-voucher">
              @foreach ($voucher as $tipe)
              <button type="button" class="btn-style btn-pay btn-add-diskon" data-dataid="{{$tipe->id_voucher}}" value="{{$tipe->nama_voucher}}" data-value="{{$tipe->nominal}}" data-tipe="{{$tipe->tipe_voucher}}"> {{$tipe->nama_voucher}} </button>
              @endforeach
            </div>
          </div>
        </div>
        <input type="hidden" id="kode-order-temp">
        <input type="hidden" id="id-payment-method">
        <input type="hidden" id="nama-payment-method">
        <input type="hidden" id="is-split-method">
        <input type="hidden" id="tipe-payment-method">
        <input type="hidden" id="split-bill-clicked" value=0>
        <div class="table-responsive mt-3">
          <table class="table table-striped table-md">
            <tr>
              <th>Subtotal Penjualan</th>
              <th>Pajak Penjualan</th>
              <th>Diskon Penjualan</th>
              <th>Total Penjualan</th>
              <th>Pembayaran</th>
              <th>Kembalian</th>
            </tr>
            <tr>
              <td class="subtotal_penjualan">11.800.000</td>
              <td class="pajak_penjualan">11.800.000</td>
              <td class="diskon">
                -
              </td>
              <td class="total-harga"></td>
              <td id="metodePembayaran">
                <div class="badge badge-success"></div> <b></b> <b class="plus-split hidden">+</b>
              </td>
              <td class="kembalian" id="bar_kembalian"></td>
            </tr>

          </table>
        </div>
        <div class="container-fluid mt-3">
          <div class="row">
            <div class="col-sm-10">
              <div class="row">
                <div class="col-sm-5">
                  <div class="row">
                    <div class="col-sm-12 text-right mb-1">
                      <button class="split-bill btn btn-success w-25 p-0 hidden">Split Bill</button>
                    </div>
                  </div>
                  <table class="table-pembayaran" id="text-bayar">
                    <tr>
                      <td class="bayar">
                        <h6>Bayar</h6>
                      </td>
                      <td><input type="text" id="bayar-input" class="form-control form-control-sm" aria-label="" name="input-payment" min="0" oninput="input_tunai()"></td>
                    </tr>
                  </table>
                  <table class="table-pembayaran hidden" id="text-nokartu">
                    <tr>
                      <td class="bayar">
                        <h6>No Kartu</h6>
                      </td>
                      <td><input type="text" class="form-control form-control-sm" aria-label="" name="input-payment" id="kartu-input"></td>
                    </tr>
                  </table>
                  <table class="table-pembayaran hidden" id="split-bill">
                    <tr>
                      <td class="bayar">
                        <h6>Bayar</h6>
                      </td>
                      <td><input type="text" class="form-control form-control-sm" aria-label="Amount (to the nearest dollar)" oninput="splitUangKembali()"></td>
                    </tr>
                  </table>
                  <table class="table-pembayaran hidden" id="split-bill-tunai">
                    <tr>
                      <td class="bayar">
                        <h6>Bayar Tunai</h6>
                      </td>
                      <td><input type="text" class="form-control form-control-sm" aria-label="Amount (to the nearest dollar)" value="0" oninput="splitUangKembali()"></td>
                    </tr>
                  </table>
                  <table class="table-pembayaran" id="kembalian-bill">
                    <tr>
                      {{-- <td class="nama-barang" id="kembalian-tunai"><b><h5>Kembalian</h5></b></td> --}}
                      <td class="nama-barang" id="kembalian-tunai"><b>
                          <h5>Kembalian</h5>
                        </b></td>
                      <td class="harga"><b>Rp <span class="kembalian"></span></b></td>
                    </tr>
                  </table>
                </div>
                <div class="col-sm-7">
                  <div class="row">
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green">1</button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green">2</button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green">3</button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green">4</button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green">5</button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green">6</button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green">7</button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green">8</button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green">9</button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-red"><i class="fas fa-trash-alt"></i></button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green">0</button>
                    </div>
                    <div class="col-sm-4">
                      <button type="button" class="btn-style btn-green btn-back"><i class="fas fa-arrow-left"></i></button>
                    </div>
                    <div class="col-sm-12">
                      <button type="button" class="btn-style btn-primary" id="btn-validasi-pembayaran">Validasi</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-2">
              <button type="button" class="btn-style btn-yel btn-nominal">{{number_format('10000', 0, '', '.')}}</button>
              <button type="button" class="btn-style btn-yel btn-nominal">{{number_format('20000', 0, '', '.')}}</button>
              <button type="button" class="btn-style btn-yel btn-nominal">{{number_format('50000', 0, '', '.')}}</button>
              <button type="button" class="btn-style btn-yel btn-nominal">{{number_format('100000', 0, '', '.')}}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalOrderEdit" tabindex="-1" role="dialog" aria-labelledby="modalOrderEditLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Order Edit
                <button class="btn btn-primary btn-sm col-1 float-right" onclick="open_AddItem()"><i class="fas fa-plus"></i></button>
              </h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 mb-5 pr-0">
              <div class="table-responsive mt-3">
                <table class="table table-striped table-md" id="body_modalOrderEdit">

                </table>
              </div>
            </div>
            <div class="col-sm-6">
              <input type="hidden" id="meja_choose">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-primary" id="validasi_modalOrderEdit">Validasi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalOrderEditItem" tabindex="-1" role="dialog" aria-labelledby="modalOrderEditItemLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Edit Item</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 mb-5 pr-0">
              <div class="table-responsive mt-3">
                <table class="table table-striped table-md" id="body_modalOrderEditItem">

                </table>
              </div>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-primary" id="validasi_modalOrderDetailEditItem">Validasi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalOrderAddItem" tabindex="-1" role="dialog" aria-labelledby="modalOrderAddItemLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Add Item</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 mb-5 pr-0">
              <div class="table-responsive mt-3">
                <table class="table table-striped table-md" id="body_modalOrderAddItem">

                </table>
              </div>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-primary" id="validasi_modalOrderDetailAddItem">Validasi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalPaket" tabindex="-1" role="dialog" aria-labelledby="modalPaketLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Masukan Jumlah Paket</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 mb-4">
              <input type="number" class="form-control form-control-sm" aria-label="" id="jumlah_paket" oninput="jum_diskon_input()" min="1">
            </div>

            <div id="input_diskon" class="p-0 mr-0">
            </div>

            <div class="col-md-12 mt-3 d-flex">
              <h5 class="col-2">Rp. </h5>
              <h5 id="total_diskon_paket" class="col-10"></h5>
            </div>

            <div class="col-sm-6 mt-2">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-6 mt-2">
              <button type="button" class="btn-style btn-primary" id="validasi_modalPaket">Validasi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalNoteEdit" tabindex="-1" role="dialog" aria-labelledby="modalNoteEditLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12 text-center">
              <h5 class="mt-2">Note Order Edit</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <input type="hidden" id="textarea_note_edit_id">
            <textarea class="form-control col-md-12 mb-4" rows="4" placeholder="Note" id="textarea_note_edit" style="height: 120px"></textarea>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-red" data-dismiss="modal" aria-label="Close">Batal</button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn-style btn-primary" onclick="edit_note()">Next</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@include('_partials.menumodal')
@include('_partials.additional')


<!-- General JS Scripts -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="js/stisla.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<!-- JS Libraies -->

<!-- Template JS File -->
<script src="js/scripts.js"></script>
<script src="js/custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
  $('.sidebar-menu li').click(function() {
    alert($(this).val());

    $('.sidebar-menu li').removeClass("active");
    $(this).addClass("active");
  });

  $('.metode-pembayaran button').click(function() {
    // alert($(this).val());
    var value_button = $(this).val();
    // var html_button = $(this).html();
    // alert(html_button);
    if (value_button == "Tunai") {
      $('#text-nokartu').addClass("hidden");
      $('#text-bayar').removeClass("hidden");
      $('#kembalian-tunai b h5').html("Kembalian");
      $('.split-bill').addClass("hidden");
      $('#split-bill').addClass("hidden");
      $('#split-bill-tunai').addClass("hidden");
      $('#kembalian-bill').removeClass("hidden");
    } else {
      $('#text-nokartu').removeClass("hidden");
      $('#text-bayar').addClass("hidden");
      $('#split-bill').removeClass("hidden");
      $('.split-bill').removeClass("hidden");
      $('#split-bill-tunai').addClass("hidden");
      $('#kembalian-bill').addClass("hidden");
    }



    $('#metodePembayaran div').text(value_button);


  });

  $("#bayar-input")
    .keyup(function() {
      var value = $(this).val();

      var total_harga = $(".total-harga").html().split(".").join("");
      var input_bayar = value;

      var kembalian = input_bayar - total_harga;
      var output_format = parseInt(kembalian).toLocaleString();

    })
    .keyup();




  // $("#bayar-input").bind("input", function() {
  //   var bayar_input = $("#bayar-input").val();
  //   $('#metodePembayaran').append("<b>" + bayar_input + "</b>");
  // });
</script>


<script>
</script>