@extends('layouts.master')
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
    #sortable li { margin: 0 4px 4px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 15px; border-radius: 5px; }
    #sortable li span { margin-left: -1em; margin-right: 1em; }
    .ui-state-highlight { height: 1.5em; line-height: 1.2em; }
</style>
@endpush

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                @if ($errors->has('error'))
                <div class="alert alert-custom alert-danger" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon-warning"></i>
                    </div>
                    <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{{ $errors->first('keterangan') }}</div>
                </div>
                @endif
                
                <form class="form" method="POST" action="{{ route('admin.kategori.submit') }}" id="frmSort">
                    @csrf
                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-header">
                            <h3 class="card-title">Sorting Kategori</h3>
                        </div>
                        @csrf
                        <div class="card-body">
                            <ul id="sortable">
                                @foreach ($kategori as $row)
                                <li class="ui-state-default" id="{{ $row->id }}">
                                    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>{{ $row->name }}
                                </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="position" id="position" />
                        </div>
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col col-md-9 text-left">
                                    <button type="submit" class="btn btn-primary mr-2" id="btnSubmit">Simpan</button>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('admin.kategori.index') }}" class="btn btn-danger">Batal</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

@push('script')
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    $(function() {
        var $sortable = $("#sortable").sortable({
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                var $data = $(this).sortable('toArray');
                $("#position").val(JSON.stringify($data));
            }
        });
        $sortable.disableSelection();
        $("#position").val(JSON.stringify($sortable.sortable("toArray")));
        $("#frmExample").submit(function(e) {
            e.preventDefault();
        });
    });
</script>
@endpush

@endsection
