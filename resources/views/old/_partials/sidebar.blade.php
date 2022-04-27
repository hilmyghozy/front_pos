<div class="sidebar-brand">
  <a href="#">Pembayaran</a>
</div>
<hr class="mb-1">
<ul class="sidebar-menu" id="tab-sidemenu" style="height: 400px">
  
  {{-- <li value="2">
    <table class="table-pembayaran">
      <tr>
        <td class="total-pesan"><div class="jumlah-tiket"><span>99</span></div></td>
        <td class="nama-barang"><b>Normal Tiket</b></td>
        <td class="harga"><b>Rp <span>11.990.000</span></b></td>
      </tr>
      <tr>
        <td class="total-pesan"></td>
        <td class="nama-barang">Catatan</td>
        <td class="harga"> </td>
      </tr>
    </table>
    <hr align="right">
  </li>
  <li value="3">
    <table class="table-pembayaran">
      <tr>
        <td class="total-pesan"><div class="jumlah-tiket"><span>9</span></div></td>
        <td class="nama-barang"><b>Shopee Weekends</b></td>
        <td class="harga"><b>Rp <span>100.250.000</span></b></td>
      </tr>
      <tr>
        <td class="total-pesan"></td>
        <td class="nama-barang">Shopee</td>
        <td class="harga"> </td>
      </tr>
      <tr>
        <td class="total-pesan"></td>
        <td class="nama-barang">Weekend</td>
        <td class="harga"> </td>
      </tr>
      <tr>
        <td class="total-pesan"></td>
        <td class="nama-barang">Diskon 10%</td>
        <td class="harga">Rp <span>- 100.000 </td>
      </tr>
    </table>
    <hr align="right">
  </li> --}}
</ul>
<hr class="mt-1 mb-1">
<div class="container">
  <div class="row">
    <div class="col-sm-12 text-right">
      {{-- <button type="button" class="btn-style btn-yellow" data-toggle="modal" data-target="#voucherModal"><i class="fas fa-cut"></i></button>
      <button type="button" class="btn-style btn-yellow"><i class="fas fa-plus-circle"></i></button> --}}
    </div>
  </div>
</div>

<div class="button-bottom">
  <div class="type-btn">
    <div class="container">
      <div class="row">
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-1">1</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-2">2</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-3">3</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-4">4</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-5">5</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-6">6</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-7">7</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-8">8</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-9">9</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-red" id="btn-trash"><i class="fas fa-trash-alt"></i></button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-0">0</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn-style btn-green" id="btn-next"><i class="fas fa-arrow-left"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div class="payment">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <table class="table-pembayaran">
            <tr id="bagan-subtotal">
              <td class="nama-barang" id="subtotal">Subtotal</td>
              <td class="harga">Rp <span id = "n_subtotal">0</span></td>
            </tr>
          </table>
        </div>
        <div class="col-sm-12">
          <table class="table-pembayaran">
            <tr id="bagan-thirdparty">
              <td class="nama-barang" id="thirdparty">Thirdparty</td>
              <td class="harga">Rp <span id = "n_thirdparty">0</span></td>
            </tr>
          </table>
        </div>
        <div class="col-sm-12">
          <table class="table-pembayaran">
            <tr id="bagan-diskon">
              <td class="nama-barang" id="nama-diskon"><i class="fas fa-cut"></i> {{session('nama_diskon')==null ? 'Diskon' : 'Diskon'.session('nama_diskon')}} &nbsp; &nbsp; &nbsp; 
                @if (session('id_diskon'))
                  <i class="fas fa-times-circle" id="hapus-diskon"></i>
                @endif
              </td>
              <td class="harga" id="total-diskon">Rp <span>- {{ session('jumlah_diskon') == null ? '0' : number_format(session('jumlah_diskon'),0,",",".") }}</span></td>
            </tr>
          </table>
        </div>
        <div class="col-sm-12">
          <table class="table-pembayaran">
            <tr>
              <td class="nama-barang"><b><h5>TOTAL</h5></b></td>
              <td class="harga" ><b>Rp <span id = "total-all-harga">0</span></b></td>
            </tr>
          </table>
        </div>
        <div class="col-sm-7 d-none">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#pembayaranModal">Pembayaran</button>
        </div>
        <div class="col-sm-12">
          <button type="button" class="btn btn-warning" onclick="open_modal_note()">Order <i class="fas fa-shopping-cart"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>


{{-- <script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" type="text/javascript"></script>
 --}}

