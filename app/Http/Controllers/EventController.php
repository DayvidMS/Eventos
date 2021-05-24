<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\User;


class EventController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * retorna a busca da view principal e busca todos os eventos existentes
     */
    public function index(){
        $search = request('search');
        //condição para buscar um evento especifico ou trazer todos os eventos
        if($search){
            $events = Event::where([
               ['title','like','%'.$search.'%']
            ])->get();
        }else{
        $events = Event::all();
        }
        $users = User::all();

        return view('welcome',['events' => $events,'search' => $search,'users' => $users]);
    }

    /**
     * Retorna a página de formulario de criação de eventos
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(){
        return view('events.create');
    }

    /**
     * Salva os dados do formulario de criação de eventos no banco
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request){
        //instancia o objeto da model Event
        $event = new Event;

        //popula os dados
        $event->title = $request->title;
        $event->city = $request->city;
        $event->description = $request->description;
        $event->private = $request->private;
        $event->items = $request->items;
        $event->date = $request->date;

        //Trata o upload da Imagem recebida do formulario
        if ($request->hasFile('image') && $request->file('image')->isValid()){
            $requestImage = $request->image;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName().strtotime("now")) . "."  . $extension;

            $requestImage->move(public_path('img/events'), $imageName);

            $event->image = $imageName;
        }
        //Pega o Usuario logado para salvar no banco da fk user_id
        $user = auth()->user();
        $event->user_id = $user->id;

        //salva os dados no banco
        $event->save();

        return redirect('/')->with('msg','Evento cadastrado com sucesso');
    }

    /**
     * faz a busca de um evento selecionado na tela principal e retorna os dados do evento
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id){
        $event = Event::findOrFail($id);
        $user = auth()->user();
        $hasUserjoined = false;

        if ($user){
            $userEvents = $user->eventsAsParticipant->toArray();
            foreach ($userEvents as $userEvent){
                if ($userEvent['id'] == $id){
                    $hasUserjoined = true;
                }
            }
        }

        //busca do usuario logado na tabela user.
        $eventOwner = User::where('id',$event->user_id)->first()->toArray();

        return view('events.show', ['event' => $event,'eventOwner' => $eventOwner,'hasUserJoined' => $hasUserjoined]);
    }

    /**
     *Vai para a view que mostra a tabela com todas os eventos marcados do usuario logado
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function dashboard(){
        //coloca na variavel user o usuario logado no momento
        $user = auth()->user();
        //chama a função criada de hasToMany na model Event para buscar os eventos do usuario e verifica os eventos que o usuario criou
        $events = $user->events;
        //Verifica os eventos que o usuario está participando chamando a função criada na model
        $eventsAsParticipant = $user->eventsAsParticipant;
        //retorna a view com o eventos do usuario
        return view('events.dashboard',['events' => $events, 'eventsasparticipant' => $eventsAsParticipant]);
    }

    /**
     * Deleta um evento
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id){


        //deleta o dado atravez do id passado
        Event::findOrFail($id)->delete();

        return redirect('/dashboard')->with('msg','Evento excluido com Sucesso');
    }

    /**
     * Abre a tela com o formulario preenchido com os dados do evento selecionado para edição
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id){
        $user = auth()->user();
        //busca o id do front
        $event = Event::findOrFail($id);
        //Condição de medida de segurança para edições maliciosas não ocorrerem, passando o id do evento na url
        if ($user->id != $event->user_id){
            return redirect('/dashboard');
        }

        $itens = ['Cadeiras','Palco','Mesa','Open food','brindes'];
        return view('events.edit',['event' => $event, 'itens' => $itens]);
    }

    /**
     * Faz o update quando o botão editar  e feito
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request){

        $data = $request->all();

        //Trata o upload da Imagem recebida do formulario de edição
        if ($request->hasFile('image') && $request->file('image')->isValid()){
            $requestImage = $request->image;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName().strtotime("now")) . "."  . $extension;

            $requestImage->move(public_path('img/events'), $imageName);

            $data['image'] = $imageName;
        }

        //faz o update dos dados recebido no banco
       Event::findOrFail($request->id)->update($data);

       return redirect('/dashboard')->with('msg','Evento editado com sucesso!');
    }

    /**
     * Relaciona um participante a um evento que confirmou a presença
     * @param $id
     */
    public function joinEvent($id){
        //resgata o usuario logado
        $user = auth()->user();

        //Salva o Id do evento no id do usuario
        $user->eventsAsParticipant()->attach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua presença está confirmada no evento' . $event->title);
    }
    /**
     * Função para o usuario sair de um Evento
     */
    public function leaveEvent($id){
        //resgata o usuario logado
        $user = auth()->user();

        //desfaz a ligação
        $user->eventsAsParticipant()->detach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Você cancelou sua presença do evento' . $event->title);

    }
}

