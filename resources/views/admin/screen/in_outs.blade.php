@extends('admin.layout')

@section('main')
   <div class="row">
      <div class="col-md-12">
         <div class="box">
                <div class="box-header with-border">
                    <h2 class="box-title">Registro de Gastos</h2>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form action="{{ route('in_outs.save') }}" method="post" accept-charset="UTF-8" class="form-horizontal">
                    <input type="hidden" name="type" value="{{ \App\Models\AdminInOuts::DEBIT_TYPE }}">
                    @isset($inOut)
                        <input type="hidden" name="id" value="{{$inOut->id}}">
                    @endisset

                    <div class="box-body">
                        <div class="fields-group">

                            <div class="form-group col-sm-6   {{ $errors->has('amount') ? ' has-error' : '' }}">
                                <label for="amount" class="col-sm-4 col-form-label">Monto:</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                        <input type="number" step="0.01" id="amount" name="amount" value="{!! old()?old('amount'):$inOut['amount']??'' !!}" class="form-control" placeholder="" />
                                    </div>
                                        @if ($errors->has('amount'))
                                            <span class="help-block">
                                                {{ $errors->first('amount') }}
                                            </span>
                                        @endif
                                </div>
                            </div>

                            <div class="form-group col-sm-6   {{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-sm-4 col-form-label">Descripción</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                        <input type="text" id="description" name="description" value="{!! old()?old('description'):$inOut['description']??'' !!}" class="form-control" placeholder="" />
                                    </div>
                                        @if ($errors->has('description'))
                                            <span class="help-block">
                                                {{ $errors->first('description') }}
                                            </span>
                                        @endif
                                </div>
                            </div>



                            <div class="form-group col-sm-6   {{ $errors->has('category') ? ' has-error' : '' }}">
                                <label for="category" class="col-sm-4 col-form-label">Categoria</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                        <input type="text" id="category" name="category" value="{!! old()?old('category'):$inOut['category']??'' !!}" class="form-control" placeholder="" />
                                    </div>
                                        @if ($errors->has('category'))
                                            <span class="help-block">
                                                {{ $errors->first('category') }}
                                            </span>
                                        @endif
                                </div>
                            </div>

                            <div class="form-group col-sm-6   {{ $errors->has('date') ? ' has-error' : '' }}">
                                <label for="date" class="col-sm-4 col-form-label">Fecha</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                        <input type="date" id="date" data-date-format="YYYY-MM-DD" class="form-control"
                                            name="date"
                                            value="{{ (old('date', $inOut['date'] ?? ''))}}">
                                        </div>
                                    </div>
                                        @if ($errors->has('date'))
                                            <span class="help-block">
                                                {{ $errors->first('date') }}
                                            </span>
                                        @endif
                                </div>
                            </div>

                    <!-- /.box-body -->

                    <div class="box-footer">
                            @csrf
                        <div class="col-md-2">
                        </div>

                        <div class="col-md-8">
                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.submit') }}</button>
                            </div>

                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning">{{ trans('admin.reset') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- /.box-footer -->
                </form>                
        </div>
        <br>
        <br>

        <div class="box">
            <div class="box-header">
                <h3>Total: Q{{ sc_currency_format($total) }}</h3>
                {{-- <form action="{{route('in_outs.index')}}" method="GET">
                    <div class="d-flex text-right">
                        <label for="from_date">Desde: &nbsp;</label>
                        <input type="text" id="date_from" name="expires_at" value="{{ \Carbon\Carbon::now()->firstOfMonth()->format('d/m/Y') }}" 
                            class="inline form-control date_time" style="width: 100px;" placeholder="" />
                        &nbsp;&nbsp;
                        <label for="from_date">Hasta: &nbsp;</label>
                        <input type="text" id="date_to" name="expires_at" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('d/m/Y') }}" 
                            class="inline form-control date_time" style="width: 100px;" placeholder="" />
                    </div>
                </form> --}}
            </div>
            <div class="box-body">
                <table class="table">
                    <thead>                    
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Descripción</th>
                        <th>Categoria</th>
                        <th>Acciones</th>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{$item->date->format('Y-m-d') }}</td>
                                <td>Q{{ sc_currency_format($item->amount) }}</td>
                                <td>{{$item->description}}</td>
                                <td>{{$item->category}}</td>
                                <td>
                                <form class="inline" action="{{route('in_outs.index')}}" method="GET">
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                    <button type="submit" class="btn btn-sm btn-primary" >Editar</button>
                                </form>

                                <form class="inline" action="{{route('in_outs.delete')}}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                    <button type="submit" class="btn btn-sm btn-danger" >Borrar</button>
                                </form>

                                </td>
                            </tr>    
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')

@endpush

@push('scripts')



<script type="text/javascript">

$(document).ready(function() {
    $('.select2').select2()
    $('.table').DataTable({
        "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
            },
            order:[[0,'desc']]
    });
});

</script>

@endpush
