@extends('layouts.main')

@section('title', 'Editando' . $event->title)
{{--{{dd($event)}}--}}
@section('content')
    <div id="event-create-container" class="col-md-6 offset-md-3">
        <h1>editando: {{$event->title}}</h1>
        <form action="/events/update/{{$event->id}}" method="POST" enctype="multipart/form-data">
            {{--Diretiva necessária para enviar dados ao banco--}}
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="image">Imagem do evento:</label>
                <input type="file" id="image" name="image" class="from-control-file">
                <img src="/img/events/{{$event->image}}" alt="{{$event->title}}" class="img-preview">
            </div>
            <div class="form-group">
                <label for="title">Evento:</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Nome do evento"
                       value="{{$event->title}}">
            </div>
            <div class="form-group">
                <label for="date">Data do evento:</label>
                <input type="date" class="form-control" id="date" name="date"
                       value="{{ $event->date->format('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label for="title">Cidade:</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Local do evento"
                       value="{{$event->city}}">
            </div>
            <div class="form-group">
                <label for="title">O evento é privado?</label>
                <select name="private" id="private" class="form-control">
                    <option value="0">Não</option>
                    <option value="1" {{ $event->private == 1 ? "selected='selected'" : "" }}>Sim</option>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Descrição:</label>
                <textarea name="description" id="description" class="form-control"
                          placeholder="O que vai acontecer no evento?">{{$event->description}}</textarea>
            </div>
            <label for="title">Adicione itens de infraestrutura:</label>
            @foreach($itens as $item)
                @foreach($event->items as $checado )
                    @if($checado == $item)
                        @php
                        $checked = 'checked';
                        @endphp
                    @endif
                @endforeach
                <div class="form-group">
                    <div class="form-group">
                        <input type="checkbox" name="items[]" {{$checked}} value="{{$item}}">{{$item}}
                    </div>
                </div>
                @endforeach
                {{--            <div class="form-group">--}}
                {{--                <div class="form-group">--}}
                {{--                    <input type="checkbox" name="items[]" value="Palco">Palco--}}
                {{--                </div>--}}
                {{--            </div>--}}
                {{--            <div class="form-group">--}}
                {{--                <div class="form-group">--}}
                {{--                    <input type="checkbox" name="items[]" value="Mesa">Mesa--}}
                {{--                </div>--}}
                {{--            </div>--}}
                {{--            <div class="form-group">--}}
                {{--                <div class="form-group">--}}
                {{--                    <input type="checkbox" name="items[]" value="Open food">Open Food--}}
                {{--                </div>--}}
                {{--            </div>--}}
                {{--            <div class="form-group">--}}
                {{--                <div class="form-group">--}}
                {{--                    <input type="checkbox" name="items[]" value="Brindes">Brindes--}}
                {{--                </div>--}}
                {{--            </div>--}}
                </br>
                <input type="submit" class="btn btn-primary" value="Editar Evento">
        </form>
        {{--        {{dd($event)}}--}}
    </div>
@endsection
