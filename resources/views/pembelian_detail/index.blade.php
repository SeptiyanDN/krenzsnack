@extends('layouts.master')
@section('title')
Transaksi Pembelian
@endsection
@push('css')
<style>
    .tampil-bayar {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .tampil-terbilang {
        padding: 10px;
        background: #f0f0f0;
        font-style: italic;
    }

    .table-pembelian tbody tr:last-child {
        display: none;
    }

    @media(max-width: 768px) {
        .tampil-bayar {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
    }
</style>

@endpush

@section('content')
<div class="ibox float-e-margins">
    <div class="ibox-title">
        <table>
            <tr>
                <td>Supplier</td>
                <td>: {{ $supplier->nama }}</td>
            </tr>
            <tr>
                <td>Telepon</td>
                <td>: {{ $supplier->telepon }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: {{ $supplier->alamat }}</td>
            </tr>
        </table>
    </div>
    <div class="ibox-content">
        <form class="form-produk">
            @csrf
            <div class="form-group">
                <div class="input-group col-md-6" onclick="tampilProduk()">
                    <input type="hidden" name="id_produk" id="id_produk">
                    <input type="hidden" name="id_pembelian" id="id_pembelian" value="{{ $id_pembelian }}">
                    <input type="text" class="form-control" name="kode_produk" id="kode_produk"
                        placeholder="Pilih Produk...">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-chevron-right"></i></button>
                    </span>
                </div>
            </div>
        </form>

        <table class="table table-striped table-bordered table-hover table-pembelian">
            <thead>
                <th width="2%">No</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Harga</th>
                <th width="5%">Jumlah</th>
                <th>Subtotal</th>
                <th width="15%"><i class="fa fa-cog"></i></th>
            </thead>
        </table>

        <div class="row">
            <div class="col-lg-8">
                <div class="tampil-bayar bg-primary"></div>
                <div class="tampil-terbilang"></div>
            </div>
            <div class="col-lg-4">
                <form action="{{ route('pembelian.store') }}" class="form-pembelian" method="post">
                    @csrf
                    <input type="hidden" name="id_pembelian" value="{{ $id_pembelian }}">
                    <input type="hidden" name="total" id="total">
                    <input type="hidden" name="total_item" id="total_item">
                    <input type="hidden" name="bayar" id="bayar">

                    <div class="form-group row">
                        <label for="totalrp" class="col-lg-2 control-label">Total</label>
                        <div class="col-lg-8">
                            <input type="text" id="totalrp" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="diskon" class="col-lg-2 control-label">Diskon</label>
                        <div class="col-lg-8">
                            <input type="number" name="diskon" id="diskon" class="form-control" value="{{ $diskon }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="bayar" class="col-lg-2 control-label">Bayar</label>
                        <div class="col-lg-8">
                            <input type="text" id="bayarrp" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="ibox-footer m-sm">
            <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-simpan"><i
                    class="fa fa-floppy-o"></i> Simpan Transaksi</button>
        </div>
    </div>
</div>

@includeIf('pembelian_detail.produk')
@endsection

@push('scripts')
<script>
    let table, table2;

    $(function () {
        table = $('.table-pembelian').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('pembelian_detail.data', $id_pembelian) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_beli'},
                {data: 'jumlah'},
                {data: 'subtotal'},
                {data: 'action', searchable: false, sortable: false},
            ],
            dom: 'brt',
            bSort: false,
            paginate: false
        })
        .on('draw.dt', function (){
            loadForm($('#diskon').val());
        });
    table2 = $('.table-produk').DataTable();

    $(document).on('input', '.quantity', function(){
        let id = $(this).data('id');
        let jumlah= parseInt($(this).val());

        if (jumlah < 1) {
            $(this).val(1);
            alert('Jumlah tidak boleh kurang dari 1');
            return;
        }
        if (jumlah > 100000) {
            $(this).val(99999);
            alert('Jumlah tidak boleh lebih dari 100000');
            return;
        }

        $.post(`{{ url('/pembelian_detail') }}/${id}`, {
            '_token': $('[name=csrf-token]').attr('content'),
            '_method': 'put',
            'jumlah': jumlah
        })
            .done(response => {
                $(this).on('mouseout', function () {
                table.ajax.reload(() => loadForm($('#diskon').val()));
                })
            })
            .fail(errors => {
                
            })
        });

        $(document).on('input', '#diskon', function() {
            if ($(this).val()=="") {
                $(this).val(0).select();
            }
            loadForm($(this).val());
        });

        $('.btn-simpan').on('click', function() {
            $('.form-pembelian').submit();
        });
    });
    function tampilProduk(){
        $('#modal-produk').modal('show');
    }
    function hideProduk(){
        $('#modal-produk').modal('hide');
    }
    function tambahProduk(){
        $.post('{{ route('pembelian_detail.store') }}', $('.form-produk').serialize())
        .done(response => {
            $('#kode_produk').focus();
            table.ajax.reload(() => loadForm($('#diskon').val()));
        })
        .fail(errors => {
            alert('error');
            return;
        })
    }

    function pilihProduk(id, kode){
        $('#id_produk').val(id);
        $('#kode_produk').val(kode);
        hideProduk();
        tambahProduk();
    }

    function deleteData(url){
        swal({
            title: "Peringatan!",
            text: "Apakah anda yakin?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, Hapus",
            closeOnConfirm: false
        },
        function () {
        $.post(url, {
        '_token': $('[name=csrf-token]').attr('content'),
        '_method': 'delete'
        })
        .done((response) => {
        swal("Deleted!", "Data berhasil terhapus", "success");
        table.ajax.reload();
        });
        });
    }

    function loadForm(diskon = 0){
        $('#total').val($('.total').text());
        $('#total_item').val($('.total_item').text());

        $.get(`{{ url('/pembelian_detail/loadform') }}/${diskon}/${$('.total').text()}`)
            .done(response => {
                $('#totalrp').val('Rp '+response.totalrp);
                $('#bayarrp').val('Rp '+response.bayarrp);
                $('#bayar').val(response.bayar);
                $('.tampil-bayar').text('Rp '+ response.bayarrp);
                $('.tampil-terbilang').text(response.terbilang);
            })
            .fail(errors => {
                alert('error');
                return;
            })
    }


    
</script>
@endpush